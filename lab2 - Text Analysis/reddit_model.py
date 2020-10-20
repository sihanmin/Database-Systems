from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from cleantext import sanitize

# IMPORT OTHER MODULES HERE
def select(ngrams):
	return [ngrams[1], ngrams[2], ngrams[3]]

def main(context):
	"""Main function takes a Spark SQL context."""
	"""Task1"""
	#commentsDF = sqlContext.read.json("comments-minimal.json.bz2")
	#submissionsDF = sqlContext.read.json("submissions.json.bz2")
	#commentsDF.write.parquet("comments.parquet")
	#submissionsDF.write.parquet("submissions.parquet")
	labeledDF = sqlContext.read.format("csv").options(header='true', inferschema='true').load("labeled_data.csv")
	commentsDF = sqlContext.read.parquet("comments.parquet")
	submissionsDF = sqlContext.read.parquet("submissions.parquet")

	"""Task2"""
	#data = labeled_data.join(comments, comments("id")===labeled_data("Input_id"), "inner").select("id","body","labeldem","labelgop","labeldjt")
	commentsDF.createOrReplaceTempView("comments")
	labeledDF.createOrReplaceTempView("labeled_data")
	dataDF = sqlContext.sql("SELECT id, body, labeldem, labelgop, labeldjt FROM comments INNER JOIN labeled_data ON comments.id = labeled_data.Input_id")

	"""Task4"""
	dataDF.createOrReplaceTempView("data")
	sqlContext.udf.register("sanitize_udf", sanitize)
	dataDF = sqlContext.sql("SELECT *, sanitize_udf(body) AS ngrams FROM data")

	"""Task5"""
	dataDF.createOrReplaceTempView("data")
	sqlContext.udf.register("select_udf", select)
	data = sqlContext.sql("SELECT id, body, labeldem, labelgop, labeldjt, select_udf(ngrams) AS selected_ngrams FROM data")
	
	data.show(20, False)

if __name__ == "__main__":
	conf = SparkConf().setAppName("CS143 Project 2B")
	conf = conf.setMaster("local[*]")
	sc   = SparkContext(conf=conf)
	sqlContext = SQLContext(sc)
	sc.addPyFile("cleantext.py")
	main(sqlContext)
