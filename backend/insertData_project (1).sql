SET FOREIGN_KEY_CHECKS = 0;

# TRUNCATE to reset the PK auto-increments and quickly wipe!
TRUNCATE TABLE f_recents;
TRUNCATE TABLE f_favorites;
TRUNCATE TABLE f_ratings;
TRUNCATE TABLE f_watches;
TRUNCATE TABLE f_servers;
TRUNCATE TABLE f_customers;
TRUNCATE TABLE f_media;
TRUNCATE TABLE f_movies;

# MOVIES
# By default, insert current date as acquire_date...
INSERT INTO f_movies values(NULL, 'Groundhog Day', 'Comedy', '102', '19930212', CURDATE(), NULL, 'PG');
INSERT INTO f_movies values(NULL, 'Men In Black', 'Comedy', '98', '19970702', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Independence Day', 'Comedy', '153', '19960703', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Bad Boys', 'Action', '119',  '19950407', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'Edge of Tomorrow', 'Action', '113', '20140528', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Alien', 'Horror', '117', '19790525', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'Star Wars - Episode IV - A New Hope','Science Fiction', '125', '19770525', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'Friday Night Lights', 'Drama','118','20041008', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Man On Fire', 'Action','146', '20040423', CURDATE(), NULL, 'R');	
INSERT INTO f_movies values(NULL, 'The Notebook', 'Romance','123', '20040625', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Friday The 13th', 'Horror','97', '20090213', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'The Texas Chainsaw Massacre', 'Horror', '98','20031017', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'Horrible Bosses', 'Comedy','98', '20110708', CURDATE(), NULL, 'R');	
INSERT INTO f_movies values(NULL, 'Pitch Perfect', 'Musical','112', '20121005', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Pitch Perfect 2', 'Musical','115', '20150515', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Chicago', 'Musical','113', '20030124', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Horrible Bosses 2', 'Comedy','108','20141126', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'The Heartbreak Kid', 'Romance','116', '20071005', CURDATE(), NULL, 'R');	
INSERT INTO f_movies values(NULL, 'Forrest Gump', 'Drama','142', '19940706', CURDATE(), NULL, 'PG-13');	
INSERT INTO f_movies values(NULL, 'Minions', 'Animation','91','20150710', CURDATE(), NULL, 'PG');
INSERT INTO f_movies values(NULL, 'Monsters, Inc.', 'Animation','92', '20011102', CURDATE(), NULL, 'G');	
INSERT INTO f_movies values(NULL, 'The Nightmare before Christmas', 'Animation','76', '19931029', CURDATE(), NULL, 'PG');
INSERT INTO f_movies values(NULL, 'Pulp Fiction', 'Crime','154','19941014', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'The Town', 'Crime', '125', '20100917', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'The Godfather', 'Crime', '175','19720324', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'The Big Short', 'Biography','130','20151223', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'The Fundementals of Caring', 'Drama','97','20160624', CURDATE(), NULL, 'TV-MA');
INSERT INTO f_movies values(NULL, 'Mona Lisa Smile', 'Drama','117', '20031219', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, '13 Going on 30', 'Romance','98','20040423', CURDATE(), NULL, 'PG-13');
INSERT INTO f_movies values(NULL, 'Blood Diamond', 'Action','143', '20061208' ,CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'Django Unchained', 'Western','165','20121225', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'Finding Nemo', 'Animation','100', '2003052003', CURDATE(), NULL, 'G');
INSERT INTO f_movies values(NULL, 'The Reaping', 'Horror','99', '20070405', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'The Conjuring', 'Horror','112', '20130719', CURDATE(), NULL, 'R');
INSERT INTO f_movies values(NULL, 'Bridesmaids', 'Comedy','125', '20110513', CURDATE(), NULL, 'R');

# MEDIA
# This will hold movie poster art, and link to movies by M_ID
# -- trigger
#TEST for media trigger
# INSERT INTO f_movies values(NULL, 'TEST MOVIE', 'TEST GENRE','60', '20171024', CURDATE(), NULL, 'G');

# SERVERS
INSERT INTO f_servers values(NULL, 'United States - CA');
INSERT INTO f_servers values(NULL, 'United States - NY');
INSERT INTO f_servers values(NULL, 'Canada');
INSERT INTO f_servers values(NULL, 'Mexico');
INSERT INTO f_servers values(NULL, 'United Kingdom - Great Britain');

# CUSTOMERS
# By default, added customers have 30-day sub. window...
INSERT INTO f_customers values(NULL, 1, 'Peter Cushing', 'cushpete@gmail.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 1, 'Spongebob Squarepants', 'IM_READY@aol.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 2, 'Bill Murray', 'thebigbill@gmail.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 3, 'Avril Lavigne', 'yeahohyeah@outlook.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 4, 'Ricardo Montalban', 'sassm4sta@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 2, 'Sandy Sandburg', 'sandyburg@gmail.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 1, 'Timothy Turner', 'fairlyoddparents@aol.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 2, 'Jennifer Biggins', 'JB123@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 2, 'April Waters', 'april92@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 2, 'Zeeko Zaki', 'thezeek@gmail.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 3, 'Colleen Kennedy', 'yascolls101@aol.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 3, 'Jenna Moody', 'Moodytoody@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 3, 'Jackson Lewis', 'JayLew2@aol.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 2, 'Rick James', 'ricky82@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 5, 'Rick Grimes', 'TWD@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 5, 'Beverlie Beasley', 'BevB2015@outlook.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 1, 'Jane Doe', 'itsjaney9@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 4, 'John Smith', 'johnnyboy2017@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 4, 'Mo Evers', 'mommamo1234@gmail.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 4, 'Chevy Samuel', 'chevy_s211@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 2, 'Alexander Higgins', 'AH989@yahoo.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 1, 'Robin Hood', 'robinh427@yaol.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
INSERT INTO f_customers values(NULL, 5, 'Geoff Jefferys', 'jefferys65@outlook.com', CURDATE(),
                               ADDDATE(CURDATE(), 30));
# WATCHES
INSERT INTO f_watches values(2, 4, NULL); #not rated yet example.
INSERT INTO f_watches values(1, 1, 5);
INSERT INTO f_watches values(3, 1, 4);
INSERT INTO f_watches values(4, 7, 5);
INSERT INTO f_watches values(5, 7, 3);

# RATINGS
INSERT INTO f_ratings values(1, 1, 5);
INSERT INTO f_ratings values(3, 1, 4);
INSERT INTO f_ratings values(4, 7, 5);
INSERT INTO f_ratings values(5, 7, 3);

# FAVORITES
INSERT INTO f_favorites values(2, 1);
INSERT INTO f_favorites values(3, 1);
INSERT INTO f_favorites values(5, 4);
#new
INSERT INTO f_favorites values(2, 30);
INSERT INTO f_favorites values(2, 31);

# RECENTS
# -- trigger

SET FOREIGN_KEY_CHECKS = 1;