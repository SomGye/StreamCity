# STORED PROCEDURES
# 1) Get titles of Top-Rated (by avg_rating), Newest, and Oldest (by release_date) movies
DROP PROCEDURE IF EXISTS get_overall_info;
DELIMITER //
CREATE PROCEDURE get_overall_info()
  LANGUAGE SQL
  COMMENT 'Gets Top-Rated/Newest/Oldest Movie Titles and shows as a record'
  BEGIN
    DECLARE toptitle, newesttitle, oldesttitle VARCHAR(200);
    # TOP MOVIE
    SET toptitle = (SELECT title FROM (SELECT title, avg_rating
      FROM f_movies
      ORDER BY avg_rating DESC LIMIT 1) AS temp1);

    # NEWEST MOVIE
    SET newesttitle = (SELECT title FROM (SELECT title, release_date
      FROM f_movies
      ORDER BY release_date DESC LIMIT 1) AS temp2);

    # OLDEST MOVIE
    SET oldesttitle = (SELECT title FROM (SELECT title, release_date
      FROM f_movies
      ORDER BY release_date ASC LIMIT 1) AS temp3);

    SELECT toptitle, newesttitle, oldesttitle;
  END//
DELIMITER ;

# 2) Check if M_ID has been 'favorited' already by given C_ID, if so return 'INVALID', if not 'VALID'
DROP PROCEDURE IF EXISTS check_favorite_validity;
DELIMITER //
CREATE PROCEDURE check_favorite_validity(IN current_C_ID INT, IN current_M_ID INT, OUT validity VARCHAR(10))
  LANGUAGE SQL
  COMMENT 'Return INVALID if C_ID already favorited this M_ID, else VALID'
  BEGIN
    DECLARE numstr VARCHAR(3);
    DECLARE numint INT;

    SELECT COUNT(*) AS NUM INTO numstr FROM f_favorites WHERE C_ID = current_C_ID AND M_ID = current_M_ID;
    SET numint = CAST(numstr AS UNSIGNED);

    IF numint > 0 THEN
      SET validity = 'INVALID';
    ELSE
      SET validity = 'VALID';
    END IF;
  END//
DELIMITER ;

# 3) Check if M_ID has been rated already by given C_ID, if so return 'INVALID', if not 'VALID'
DROP PROCEDURE IF EXISTS check_rating_validity;
DELIMITER //
CREATE PROCEDURE check_rating_validity(IN current_C_ID INT, IN current_M_ID INT, OUT validity VARCHAR(10))
  LANGUAGE SQL
  COMMENT 'Return INVALID if C_ID already rated this M_ID, else VALID'
  BEGIN
    DECLARE numstr VARCHAR(3);
    DECLARE numint INT;

    SELECT COUNT(*) AS NUM INTO numstr FROM f_ratings WHERE C_ID = current_C_ID AND M_ID = current_M_ID;
    SET numint = CAST(numstr AS UNSIGNED);

    IF numint > 0 THEN
      SET validity = 'INVALID';
    ELSE
      SET validity = 'VALID';
    END IF;
  END//
DELIMITER ;

# 4) Check if new entry would be a 'duplicate' in f_watches,
# if so, delete entry already in watches before insert
DROP PROCEDURE IF EXISTS check_and_insert_f_watches;
DELIMITER //
CREATE PROCEDURE check_and_insert_f_watches(IN current_C_ID INT, IN current_M_ID INT, IN current_rating TINYINT)
  LANGUAGE SQL
  COMMENT 'Check if dupe in f_watches, delete, then insert'
  BEGIN
    DECLARE matchingids INT;

    SELECT EXISTS(SELECT * FROM f_watches WHERE C_ID = current_C_ID AND M_ID = current_M_ID) INTO matchingids;
    IF matchingids > 0 THEN
      DELETE FROM f_watches WHERE C_ID = current_C_ID AND M_ID = current_M_ID;
    END IF;

    INSERT INTO f_watches(C_ID, M_ID, rating) VALUES(current_C_ID, current_M_ID, current_rating);
  END //
DELIMITER ;

# 5) Check if current customer (C_ID) has less than 10 days left in subscription,
# and return message
DROP PROCEDURE IF EXISTS check_if_low_sub;
DELIMITER //
CREATE PROCEDURE check_if_low_sub(IN current_C_ID INT, OUT substatus VARCHAR(4))
  LANGUAGE SQL
  COMMENT 'Returns NONE if <1, LOW if <10, else OKAY'
  BEGIN
    DECLARE daysleft INT;

    SELECT DATEDIFF(subscribe_end, CURDATE()) FROM f_customers WHERE C_ID = current_C_ID INTO daysleft;
    IF daysleft < 1 THEN
      SET substatus = 'NONE';
    ELSEIF daysleft < 10 THEN
      SET substatus = 'LOW';
    ELSE
      SET substatus = 'OKAY';
    END IF;
  END //
DELIMITER ;

# STORED FUNCTIONS
# 1) Get rating # and output string for display in frontend/view
DROP FUNCTION IF EXISTS overall_rating;
CREATE FUNCTION overall_rating(p_rating DECIMAL)
  RETURNS VARCHAR(10)
  DETERMINISTIC
BEGIN
  DECLARE rating_str VARCHAR(10);

  IF p_rating > 4 THEN
    SET rating_str = 'Excellent!';
  ELSEIF p_rating > 3 THEN
    SET rating_str = 'Great';
  ELSEIF p_rating > 2 THEN
    SET rating_str = 'Decent';
  ELSEIF p_rating > 1 THEN
    SET rating_str = 'Not Great';
  ELSE
    SET rating_str = 'Bad!';
  END IF;

  RETURN rating_str;
END;

# 2) Get 'human readable format' for customer subscription dates (%M %D, %Y)
DROP FUNCTION IF EXISTS simple_date;
CREATE FUNCTION simple_date(p_date DATETIME)
  RETURNS VARCHAR(30)
  DETERMINISTIC
BEGIN
  DECLARE new_date VARCHAR(30);
  SELECT FROM_UNIXTIME(UNIX_TIMESTAMP(p_date), '%M %D, %Y') INTO new_date;
  RETURN new_date;
END;