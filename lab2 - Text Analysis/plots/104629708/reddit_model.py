from __future__ import print_function
from pyspark import SparkConf, SparkContext
from pyspark.sql import SQLContext
from cleantext import sanitize
from pyspark.ml.feature import CountVectorizer
from pyspark.sql.functions import split, col, udf
from pyspark.sql.types import *
from pyspark.ml.classification import LogisticRegression
from pyspark.ml.tuning import CrossValidator, ParamGridBuilder, CrossValidatorModel
from pyspark.ml.evaluation import BinaryClassificationEvaluator
import pandas
import re

states = ['Alabama', 'Alaska', 'Arizona', 'Arkansas', 'California', 'Colorado', 'Connecticut', 'Delaware', 'District of Columbia', 'Florida', 'Georgia', 'Hawaii', 'Idaho', 'Illinois', 'Indiana', 'Iowa', 'Kansas', 'Kentucky', 'Louisiana', 'Maine', 'Maryland', 'Massachusetts', 'Michigan', 'Minnesota', 'Mississippi', 'Missouri', 'Montana', 'Nebraska', 'Nevada', 'New Hampshire', 'New Jersey', 'New Mexico', 'New York', 'North Carolina', 'North Dakota', 'Ohio', 'Oklahoma', 'Oregon', 'Pennsylvania', 'Rhode Island', 'South Carolina', 'South Dakota', 'Tennessee', 'Texas', 'Utah', 'Vermont', 'Virginia', 'Washington', 'West Virginia', 'Wisconsin', 'Wyoming']

# IMPORT OTHER MODULES HERE
def select(ngrams):
	return ngrams[1] + " " + ngrams[2] + " " + ngrams[3]

def in_state(state):
	if state in states:
		return True
	return False

def vectorize(ngrams):
	unigram = ngrams[1]
	bigram = ngrams[2]
	trigram = ngrams[3]
	unigram_vector = unigram.split(" ")
	bigram_vector = bigram.split(" ")
	trigram_vector = trigram.split(" ")
	ret = unigram_vector + bigram_vector + trigram_vector
	return ret

def check_pos(label):
	if label == 1:
		return 1
	return 0

def check_neg(label):
	if label == -1:
		return 1
	return 0
	
def link_id(id):
	res = re.sub('t3_', '', id)
	return res

def remove(string):
        if(string.find('/s') == -1 or string.find('&gt') != 0):
            return True
        return False


def main(context):
	"""Main function takes a Spark SQL context."""
	"""Task1"""
	commentsDF = sqlContext.read.json("comments-minimal.json.bz2")
	submissionsDF = sqlContext.read.json("submissions.json.bz2")
	# commentsDF.write.parquet("comments.parquet")
	# submissionsDF.write.parquet("submissions.parquet")
	# labeledDF = sqlContext.read.format("csv").options(header='true', inferschema='true').load("labeled_data.csv")
	# commentsDF = sqlContext.read.parquet("comments.parquet")
	# submissionsDF = sqlContext.read.parquet("submissions.parquet")

	"""Task2"""
	commentsDF.createOrReplaceTempView("comments")
	labeledDF.createOrReplaceTempView("labeled_data")
	dataDF = sqlContext.sql("SELECT id, body, labeldem, labelgop, labeldjt FROM comments INNER JOIN labeled_data ON comments.id = labeled_data.Input_id") #.sample(withReplacement=False, fraction=0.1, seed=None)
	'''drop the temp view to save memory (RAM)'''

	"""Task4"""
	dataDF.createOrReplaceTempView("data")
	sqlContext.udf.register("sanitize_udf", sanitize)
	dataDF = sqlContext.sql("SELECT *, sanitize_udf(body) AS ngrams FROM data")

	"""Task5"""
	dataDF.createOrReplaceTempView("data")
	sqlContext.udf.register("select_udf", select)
	data = sqlContext.sql("SELECT id, body, labeldem, labelgop, labeldjt, select_udf(ngrams) AS selected_ngrams FROM data")
	#data = sqlContext.sql("SELECT id, body, labeldem, labelgop, labeldjt, vectorize_udf(ngrams) AS vectorized_ngrams FROM data")

	"""Task6A"""
	vectorized_data = data.withColumn("selected_ngrams", split(col("selected_ngrams"), " ").cast(ArrayType(StringType())))
	cv = CountVectorizer(inputCol="selected_ngrams", outputCol="features", minDF=5.0, binary=True)
	cv_model = cv.fit(vectorized_data)
	vectorized = cv_model.transform(vectorized_data)
	#vectorized.show(1, truncate=False)

	"""Task6B"""
	vectorized.createOrReplaceTempView("Vectorized")
	sqlContext.udf.register("check_pos", check_pos)
	sqlContext.udf.register("check_neg", check_neg)
	pos_label = sqlContext.sql("SELECT features,IF(labeldjt = 1, 1, 0) AS label FROM Vectorized")
	neg_label = sqlContext.sql("SELECT features,IF(labeldjt = -1, 1, 0) AS label FROM Vectorized")
	
    
	"""Task7"""
	# Bunch of imports (may need more)
	# Initialize two logistic regression models.
	# Replace labelCol with the column containing the label, and featuresCol with the column containing the features.
	poslr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
	neglr = LogisticRegression(labelCol="label", featuresCol="features", maxIter=10)
	# This is a binary classifier so we need an evaluator that knows how to deal with binary classifiers.
	posEvaluator = BinaryClassificationEvaluator()
	negEvaluator = BinaryClassificationEvaluator()
	# There are a few parameters associated with logistic regression. We do not know what they are a priori.
	# We do a grid search to find the best parameters. We can replace [1.0] with a list of values to try.
	# We will assume the parameter is 1.0. Grid search takes forever.
	posParamGrid = ParamGridBuilder().addGrid(poslr.regParam, [1.0]).build()
	negParamGrid = ParamGridBuilder().addGrid(neglr.regParam, [1.0]).build()
	# We initialize a 5 fold cross-validation pipeline.
	posCrossval = CrossValidator(
    estimator=poslr,
    evaluator=posEvaluator,
    estimatorParamMaps=posParamGrid,
    numFolds=5)
	negCrossval = CrossValidator(
    estimator=neglr,
    evaluator=negEvaluator,
    estimatorParamMaps=negParamGrid,
    numFolds=5)
	# Although crossvalidation creates its own train/test sets for
	# tuning, we still need a labeled test set, because it is not
	# accessible from the crossvalidator (argh!)
	# Split the data 20/80

	posTrain, posTest = pos_label.randomSplit([0.8, 0.2])
	negTrain, negTest = neg_label.randomSplit([0.8, 0.2])
	# Train the models
	print("Training positive classifier...")
	posModel = posCrossval.fit(posTrain)
	print("Training negative classifier...")
	negModel = negCrossval.fit(negTrain)

	# Once we train the models, we don't want to do it again. We can save the models and load them again later.
	posModel.save("./www/pos.model")
	negModel.save("./www/neg.model")
	
	

	"""task 8"""
	comments_result = context.sql('SELECT id, link_id, body, created_utc, author_flair_text, score FROM comments')
	comments_result.createOrReplaceTempView("comments_result")
	#tmp = comments_result.toPandas()
	#tmp.to_csv("./comments_result.csv", index=False, sep=' ')
	submissionsDF.createOrReplaceTempView("submissions_view")
	submissions_title = context.sql("SELECT id, title, score FROM submissions_view ORDER BY id")	
	submissions_title.createOrReplaceTempView("submissions_result")
	#submissions_result.show()
	#tmp = submissions_result.toPandas()
	#tmp.to_csv("./submissions_result.csv", index=False, sep=' ')
	
	context.udf.register("remove", remove)
	context.udf.register("link_id", link_id)
	commands = "SELECT c.id, c.body, c.created_utc, c.author_flair_text, s.title, c.score AS comment_score, s.score AS submission_score FROM comments_result c INNER JOIN submissions_result s ON link_id(c.link_id) = s.id WHERE remove(c.body) = TRUE"
	task8_result =context.sql(commands)
	#unseen_data = task8_result.withColumn("body", split(col("body"), " ").cast(ArrayType(StringType())))
	unseen_data = task8_result.sample(withReplacement=False, fraction=0.2, seed=None)

	# # """task 9"""
	unseen_data.createOrReplaceTempView("Unseen_data")
	sqlContext.udf.register("sanitize_udf", sanitize)
	unseen_data = sqlContext.sql("SELECT *, sanitize_udf(body) AS ngrams FROM Unseen_data")
	unseen_data.createOrReplaceTempView("Unseen_data2")
	sqlContext.udf.register("select_udf", select)
	temp_data = sqlContext.sql("SELECT *, select_udf(ngrams) AS selected_ngrams FROM Unseen_data2")
	
	temp_data = temp_data.withColumn("selected_ngrams", split(col("selected_ngrams"), " ").cast(ArrayType(StringType())))
	# # cv = CountVectorizer(inputCol="selected_ngrams", outputCol="features", minDF=5.0, binary=True)
	# cv_model = cv.fit(temp_data)
	unseen_data = cv_model.transform(temp_data)

	unseen_data = unseen_data.drop("ngrams")
	unseen_data = unseen_data.drop("selected_ngrams")
	unseen_data.show(20, False)
	unseen_data.write.parquet("unseen_data.parquet")



	'''SEGMENTATION'''
	posModel = CrossValidatorModel.load("./www/pos.model")
	negModel = CrossValidatorModel.load("./www/neg.model")
	unseen_data = sqlContext.read.parquet("unseen_data.parquet")
	# unseen_data.show(20, False)
	print('transform test data')
	posResult = posModel.transform(unseen_data)
	posResult.printSchema()
	posResult.show(1, False)

	negResult = negModel.transform(unseen_data)
	posResult.show(20, truncate=False)

	def convert_pos(value):
 		return 1 if value[1] > 0.2 else 0

	def convert_neg(value):
		return 1 if value[1] > 0.25 else 0

	convert_pos_udf = udf(convert_pos, StringType())
	convert_neg_udf = udf(convert_neg, StringType())

	posResult = posResult.withColumn("pos", convert_pos_udf(col('probability')))
	negResult = negResult.withColumn("neg", convert_neg_udf(col('probability')))

	posResult.show(5, False)
	posResult.write.parquet("posResult.parquet")
	negResult.show(5, False)
	negResult.write.parquet("negResult.parquet")

	
	# '''task10'''
	
	posResult = sqlContext.read.parquet("posResult.parquet")
	negResult = sqlContext.read.parquet("negResult.parquet")
	posResult.createOrReplaceTempView("posResult")
	negResult.createOrReplaceTempView("negResult")

	#1
	total_prob_pos = sqlContext.sql('SELECT AVG(pos) AS Positive FROM posResult')
	total_prob_neg = sqlContext.sql('SELECT AVG(neg) AS Negative FROM negResult')
	tmp = total_prob_pos .toPandas()
	tmp.to_csv('./all_sub_pos.csv')
	tmp = total_prob_neg.toPandas()
	tmp.to_csv('./all_sub_neg.csv')

	#2
	date_prob_pos = sqlContext.sql("SELECT AVG(pos) AS Positive, date(from_unixtime(created_utc)) AS date FROM posResult GROUP BY date ORDER BY date")
	date_prob_neg = sqlContext.sql("SELECT AVG(neg) AS Negative, date(from_unixtime(created_utc)) AS date FROM negResult GROUP BY date ORDER BY date")
	date_prob_pos.createOrReplaceTempView("date_prob_pos")
	date_prob_neg.createOrReplaceTempView("date_prob_neg")
	date_prob = sqlContext.sql("SELECT p.date, Positive, Negative FROM date_prob_pos p INNER JOIN date_prob_neg n ON p.date = n.date")
	tmp = date_prob_pos.toPandas()
	tmp.to_csv('./time_data_pos.csv')
	tmp = date_prob_neg.toPandas()
	tmp.to_csv('./time_data_neg.csv')
	tmp = date_prob.toPandas()
	tmp.to_csv('./time_data.csv')

	#3
	context.udf.register("in_state", in_state)
	state_prob_pos = sqlContext.sql("SELECT AVG(pos) AS Positive, author_flair_text AS state FROM posResult WHERE author_flair_text is not null and in_state(author_flair_text) = True GROUP BY state")
	state_prob_neg = sqlContext.sql("SELECT AVG(neg) AS Negative, author_flair_text AS state FROM negResult WHERE author_flair_text is not null and in_state(author_flair_text) = True GROUP BY state")
	state_prob_pos.createOrReplaceTempView("state_prob_pos")
	state_prob_neg.createOrReplaceTempView("state_prob_neg")
	state_prob = sqlContext.sql("SELECT p.state, Positive, Negative FROM state_prob_pos p INNER JOIN state_prob_neg n ON p.state = n.state")
	tmp = state_prob_pos.toPandas()
	tmp.to_csv('./state_sub_pos.csv')
	tmp = state_prob_neg .toPandas()
	tmp.to_csv('./state_sub_neg.csv')
	tmp = state_prob.toPandas()
	tmp.to_csv('./state_prob.csv')

	#comment_score
	comment_prob_pos = sqlContext.sql('SELECT AVG(pos) AS Positive, comment_score FROM posResult GROUP BY comment_score')
	comment_prob_neg = sqlContext.sql('SELECT AVG(neg) AS Negative, comment_score FROM negResult GROUP BY comment_score')
	comment_prob_pos.createOrReplaceTempView("comment_prob_pos")
	comment_prob_neg.createOrReplaceTempView("comment_prob_neg")
	comment_prob = sqlContext.sql("SELECT p.comment_score, Positive, Negative FROM comment_prob_pos p INNER JOIN comment_prob_neg n ON p.comment_score = n.comment_score")
	tmp = comment_prob_pos.toPandas()
	tmp.to_csv('./comment_prob_pos.csv')
	tmp = comment_prob_neg.toPandas()
	tmp.to_csv('./comment_prob_neg.csv')
	tmp = comment_prob.toPandas()
	tmp.to_csv('./comment_prob.csv')

	#submission_score
	submission_prob_pos = sqlContext.sql('SELECT AVG(pos) AS Positive, submission_score FROM posResult GROUP BY submission_score')
	submission_prob_neg = sqlContext.sql('SELECT AVG(neg) AS Negative, submission_score FROM negResult GROUP BY submission_score')
	submission_prob_pos.createOrReplaceTempView("submission_prob_pos")
	submission_prob_neg.createOrReplaceTempView("submission_prob_neg")
	submission_prob = sqlContext.sql("SELECT p.submission_score, Positive, Negative FROM submission_prob_pos p INNER JOIN submission_prob_neg n ON p.submission_score = n.submission_score")
	tmp = submission_prob_pos.toPandas()
	tmp.to_csv('./submission_prob_pos.csv')
	tmp = submission_prob_neg.toPandas()
	tmp.to_csv('./submission_prob_neg.csv')
	tmp = submission_prob.toPandas()
	tmp.to_csv('./submission_prob.csv')

	#top-tens
	top_ten_pos = sqlContext.sql('SELECT AVG(pos) AS pos_percentage, title FROM posResult GROUP BY title ORDER BY pos_percentage DESC LIMIT 10')
	tmp = top_ten_pos.toPandas()
	tmp.to_csv('./top_ten_pos.csv')
	top_ten_neg = sqlContext.sql('SELECT AVG(neg) AS neg_percentage, title FROM negResult GROUP BY title ORDER BY neg_percentage DESC LIMIT 10')
	tmp = top_ten_neg.toPandas()
	tmp.to_csv('./top_ten_neg.csv')

	#daily number of comments


	date_prob_pos = sqlContext.sql("SELECT COUNT(pos) AS Positive, date(from_unixtime(created_utc)) AS date FROM posResult GROUP BY date ORDER BY date")
	date_prob_neg = sqlContext.sql("SELECT COUNT(neg) AS Negative, date(from_unixtime(created_utc)) AS date FROM negResult GROUP BY date ORDER BY date")
	date_prob_pos.createOrReplaceTempView("date_prob_pos")
	date_prob_neg.createOrReplaceTempView("date_prob_neg")
	date_prob = sqlContext.sql("SELECT p.date, Positive, Negative FROM date_prob_pos p INNER JOIN date_prob_neg n ON p.date = n.date")
	tmp = date_prob_pos.toPandas()
	tmp.to_csv('./count_pos.csv')
	tmp = date_prob_neg.toPandas()
	tmp.to_csv('./count_neg.csv')
	tmp = date_prob.toPandas()
	tmp.to_csv('./count.csv')


if __name__ == "__main__":
	conf = SparkConf().setAppName("CS143 Project 2B")
	conf = conf.setMaster("local[*]")
	sc   = SparkContext(conf=conf)
	sqlContext = SQLContext(sc)
	sc.addPyFile("cleantext.py")
	main(sqlContext)
