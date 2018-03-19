#General deletes
DELETE FROM f_recents;
DELETE FROM f_favorites;
DELETE FROM f_ratings;
DELETE FROM f_watches;
DELETE FROM f_servers;
DELETE FROM f_customers;
DELETE FROM f_movies;

#General full truncates
#Does not trigger, but does reset auto-increment
TRUNCATE TABLE f_recents;
TRUNCATE TABLE f_favorites;
TRUNCATE TABLE f_ratings;
TRUNCATE TABLE f_watches;
TRUNCATE TABLE f_servers;
TRUNCATE TABLE f_customers;
TRUNCATE TABLE f_movies;

#Delete scenarios...
#1 Delete movies which have an avg_rating < 2.5 (below average!)
DELETE FROM f_movies WHERE avg_rating < 2.5;

#2 Delete customers which have had an expired subscription for > 30days
DELETE FROM f_customers WHERE timestampdiff(DAY, subscribe_end, now()) > 30;

#3 Delete from favorites a specific movie by C_ID
SET @testMID = 12;
DELETE FROM f_favorites WHERE C_ID = @testCID AND M_ID = @testMID;

#4 Delete from watches the current C_ID/M_ID pair
SET @testMID = 4;
DELETE FROM f_watches WHERE C_ID = @testCID AND M_ID = @testMID;