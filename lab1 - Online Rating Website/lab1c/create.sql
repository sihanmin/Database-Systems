
CREATE TABLE Movie (
	id INT PRIMARY KEY,
		-- Every movie has a unique identification number.
	title VARCHAR(100) NOT NULL,
		-- Every movie must have a title.
	year INT NOT NULL,
		-- Every movie must have a production year.
	rating VARCHAR(10),
	company VARCHAR(50),
	CHECK (year > 1800 AND year < 3000)
		-- Every movie should be produced after film technology invented
) ENGINE = INNODB;

CREATE TABLE Actor (
	id INT PRIMARY KEY,
		-- Every actor has a unique identification number.
	last VARCHAR(20) NOT NULL,
		-- Every actor must have a last name.
	first VARCHAR(20) NOT NULL,
		-- Every actor must have a first name.
	sex VARCHAR(6) NOT NULL,
		-- Every actor must have a sex.
	dob DATE NOT NULL,
		-- Every actor must have a date of birth.
	dod DATE
) ENGINE = INNODB;

CREATE TABLE Director (
	id INT PRIMARY KEY,
		-- Every director has a unique identification number.
	last VARCHAR(20) NOT NULL,
		-- Every director must have a last name.
	first VARCHAR(20) NOT NULL,
		-- Every director must have a first name.
	dob DATE NOT NULL,
		-- Every director must have a date of birth.
	dod DATE
) ENGINE = INNODB;

CREATE TABLE MovieGenre (
	mid INT,
	genre VARCHAR(20) NOT NULL,
		-- Every row should associate a movie with a genre.
	Foreign KEY (mid) REFERENCES Movie(id)
		-- Every movie with a genre should be a valid movie.
) ENGINE = INNODB;

CREATE TABLE MovieDirector (
	mid INT,
	did INT,
	Foreign KEY (mid) REFERENCES Movie(id),
		-- Every movie with a director should be a valid movie.
	Foreign KEY (did) REFERENCES Director(id)
		-- Every director of a movie should be a valid director.
) ENGINE = INNODB;

CREATE TABLE MovieActor (
	mid INT,
	aid INT,
	role VARCHAR(50),
	Foreign KEY (mid) REFERENCES Movie(id),
		-- Every movie with a actor should be a valid movie.
	Foreign KEY (aid) REFERENCES Actor(id)
		-- Every actor in a movie should be a valid actor.
) ENGINE = INNODB;

CREATE TABLE Review (
	name VARCHAR(20),
	time TIMESTAMP,
	mid INT,
	rating INT NOT NULL,
		-- Every review should at least have a rating.
	comment VARCHAR(500),
	PRIMARY KEY(name, time),
	Foreign KEY (mid) REFERENCES Movie(id),
		-- Every movie with a reveiw should be a valid movie.
	CHECK (rating >= 0),
		-- No rating should be negative.
	CHECK (time <= CURRENT_TIMESTAMP())
		-- No review should be created in the future.
) ENGINE = INNODB;

CREATE TABLE MaxPersonID (
	id INT
) ENGINE = INNODB;

CREATE TABLE MaxMovieID (
	id INT
) ENGINE = INNODB;




