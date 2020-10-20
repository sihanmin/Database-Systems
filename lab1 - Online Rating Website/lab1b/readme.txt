Our team use github to share code and files.
We evenly distributed the workload and helps each other’s out.
The included file: 
     |
     +- team.txt
     |
     +- create.sql : create table as specified with constraint we designed. We introduce every table with primary key and we make constraints that make real senses like the year for movie should be after the year for the first movie. 
     |
     +- load.sql: file to load data into our table.
     |
     +- queries.sql: qeeries specified by the spec.
     |
     +- query.php: php interface in which will return the result of php query. We did not handle error as specified by spec. 
     |
     +- violate.sql: sql query that will make error with one or more constraints. Note: our query may conflict with one or more constraints. After all, the file covers all the conflicts that can happen with the constraints. 