# 'Search' by Title
# By default, search by including every movie that includes substring of search
# NOTE: both of these stmts must be run, and @searchtitle will be specified in front-end later
SET @searchtitle = 'Star'; #to be replaced by what user specifies...
SET @searchtitle = '';
SELECT title, genre, release_date, avg_rating, mpaa_rating
  FROM f_movies
  WHERE title LIKE CONCAT("%", @searchtitle, "%");

# 'Search' by Genre
SET @searchgenre = 'Com';
SET @searchgenre = 'Ac';
SELECT title, genre, release_date, avg_rating, mpaa_rating
  FROM f_movies
  WHERE genre LIKE CONCAT("%", @searchgenre, "%");

# 'Search' by MPAA
SET @searchmpaa = 'G';
SELECT title, genre, release_date, avg_rating, mpaa_rating
  FROM f_movies
  WHERE mpaa_rating = @searchmpaa;