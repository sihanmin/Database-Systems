
-- the names of all the actors in the movie 'Die Another Day'.
-- Use concat the correct the form of output
-- Use inner join on 3 tables to query amoung multipple relationships 
SELECT CONCAT(Actor.first," ",Actor.last) AS name
	FROM MovieActor
	INNER JOIN Movie ON MovieActor.mid = Movie.id and Movie.title = "Die Another Day"
	INNER JOIN Actor ON MovieActor.aid = Actor.id;



-- Give me the count of all the actors who acted in multiple movies.
-- First create all the actor with count greater than 1 and then count the subquery 
SELECT COUNT(*) AS frequency
FROM  
    (SELECT COUNT(MovieActor.mid) 
	 FROM MovieActor 
	 GROUP BY MovieActor.aid
	 HAVING COUNT(MovieActor.mid) > 1
	) mysubquery
;
-- Find the name of 5 movie with highest actor number
SELECT m.title AS name,COUNT(*) AS actor_num
FROM MovieActor ma
INNER JOIN Movie m ON ma.mid = m.id
GROUP BY m.id
ORDER BY COUNT(*) DESC
LIMIT 5;
