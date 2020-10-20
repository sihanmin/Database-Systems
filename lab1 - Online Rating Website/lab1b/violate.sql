INSERT INTO Movie (id, year)
VALUES (1, 1600);
-- This movie has no title.
-- Its prodection year is before film is even invented.

INSERT INTO Movie (id, title)
VALUES (272, "whatever movie");
-- This movie has no production year.
-- The movie id is not unique.

INSERT INTO Actor (id, last)
VALUES (1, "whatever last");
-- This actor has no first name, sex, or date of birth.

INSERT INTO Actor (id, first)
VALUES (1, "whatever first");
-- This actor has no last name, sex, or date of birth.
-- The actor id is not unique.

INSERT INTO Director (id, last)
VALUES (2, "whatever last");
-- This director has no first name, or date of birth.

INSERT INTO Director (id, first)
VALUES (37146, "whatever first");
-- This director has no last name, or date of birth.
-- The director id is not unique.

INSERT INTO MovieGenre (mid)
VALUES (23456);
-- This movie id does not exist in the Movie table.
-- This insertion has no genre.

INSERT INTO MovieDirector (mid, did)
VALUES (23456, 91234);
-- This movie id does not exist in the Movie table.
-- This director id does not exist in the Director table.

INSERT INTO MovieDirector (mid, aid)
VALUES (23456, 777779);
-- This movie id does not exist in the Movie table.
-- This actor id does not exist in the Actor table.

INSERT INTO Review (name, time, mid)
VALUES ("whatever user", CURRENT_TIMESTAMP()+10000, 23456);
-- The review time is shown to be in the future.
-- This movie id does not exist in the Movie table.
-- This review has no rating.

INSERT INTO Review (rating)
VALUES (-100);
-- This review has no name, time, or mid.
-- This review has negative rating.