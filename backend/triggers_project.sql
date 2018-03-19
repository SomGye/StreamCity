##TRIGGERS
DROP TRIGGER IF EXISTS avg_rating_insert_trigger;
DROP TRIGGER IF EXISTS avg_rating_update_trigger;
DROP TRIGGER IF EXISTS f_recents_insert_trigger;
DROP TRIGGER IF EXISTS f_ratings_insert_trigger;
DROP TRIGGER IF EXISTS f_watches_to_recents_and_ratings_trigger;
DROP TRIGGER IF EXISTS f_movies_insert_to_f_media_trigger;

#1 avg_rating -> when f_ratings is updated, update f_movies.avg_rating with new avg_rating
DELIMITER //
CREATE TRIGGER avg_rating_insert_trigger
AFTER INSERT
  ON f_ratings FOR EACH ROW
BEGIN
UPDATE f_movies m JOIN
  (SELECT M_ID, avg(rating) as avg_score
    FROM f_ratings r
    GROUP BY M_ID) r
  ON m.M_ID = r.M_ID
  SET m.avg_rating = r.avg_score;
END; //
DELIMITER ;

#1b
DELIMITER //
CREATE TRIGGER avg_rating_update_trigger
AFTER UPDATE
  ON f_ratings FOR EACH ROW
BEGIN
UPDATE f_movies m JOIN
  (SELECT M_ID, avg(rating) as avg_score
    FROM f_ratings r
    GROUP BY M_ID) r
  ON m.M_ID = r.M_ID
  SET m.avg_rating = r.avg_score;
END; //
DELIMITER ;

#2 watches to recents/ratings -> when new 'f_watches' entry is inserted, need to insert a record with its (C_ID, M_ID) pair
# Prevent redundancy by checking new vs existing IDs in recents/ratings
DELIMITER //
CREATE TRIGGER f_watches_to_recents_and_ratings_trigger
AFTER INSERT
  ON f_watches FOR EACH ROW
BEGIN
  DECLARE matchingids INT;

  SELECT EXISTS(SELECT * FROM f_recents WHERE C_ID = NEW.C_ID AND M_ID = NEW.M_ID) INTO matchingids;
  IF matchingids < 1 THEN
    INSERT INTO f_recents(C_ID, M_ID) VALUES (NEW.C_ID, NEW.M_ID);
  END IF;

  SELECT EXISTS(SELECT * FROM f_ratings WHERE C_ID = NEW.C_ID AND M_ID = NEW.M_ID) INTO matchingids;
  IF matchingids > 0 THEN
    UPDATE f_ratings
    SET rating = NEW.rating
    WHERE C_ID = NEW.C_ID AND M_ID = NEW.M_ID;
  ELSEIF NEW.rating IS NOT NULL THEN
    INSERT INTO f_ratings(C_ID, M_ID, rating) VALUES (NEW.C_ID, NEW.M_ID, NEW.rating);
  END IF;
END; //
DELIMITER ;

#4 media -> when movie is inserted, automatically add matching media entry for img
DELIMITER //
CREATE TRIGGER f_movies_insert_to_f_media_trigger
  AFTER INSERT
  ON f_movies FOR EACH ROW
  BEGIN
    DECLARE newID INT;
    DECLARE newIDstr CHAR(255);
    DECLARE newFilename VARCHAR(255);
    SET @newID = NEW.M_ID;
    SET @newIDstr = CONVERT(@newID, CHAR(250));
    SET @newFilename = CONCAT(@newIDstr, '.jpg');
    INSERT INTO f_media VALUES(NULL, @newID, @newFilename, 'jpg');
  END; //
DELIMITER ;