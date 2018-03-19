## MOVIES
# AVERAGE Rating - High to Low
SELECT title, avg_rating
  FROM f_movies
  ORDER BY avg_rating DESC;

# TOP MOVIE
SELECT title, avg_rating
  FROM f_movies
  ORDER BY avg_rating DESC LIMIT 1;

# TOP 5 MOVIES
SELECT title, avg_rating
  FROM f_movies
  ORDER BY avg_rating DESC LIMIT 5;

# MPAA Rating (Safe->Mature)
# This works since G < PG < PG-13 < R in SQL
SELECT title, mpaa_rating
  FROM f_movies
  ORDER BY mpaa_rating;

# Release Date (Age, New to Old)
SELECT title, release_date
  FROM f_movies
  ORDER BY release_date DESC;

# NEWEST MOVIE
SELECT title, release_date
  FROM f_movies
  ORDER BY release_date DESC LIMIT 1;

# OLDEST MOVIE
SELECT title, release_date
  FROM f_movies
  ORDER BY release_date ASC LIMIT 1;

# TEST - show distinct genres
SELECT DISTINCT genre
  FROM f_movies
  ORDER BY genre ASC;

# TEST - show movie by given genre
SET @testgenre = 'Action';
SET @testgenre = 'Comedy';
SELECT M_ID, title FROM f_movies WHERE genre = @testgenre;

# 'Search' by Title
# By default, search by including every movie that includes substring of search
# NOTE: both of these stmts must be run, and @searchtitle will be specified in front-end later
SET @searchtitle = 'Star'; #to be replaced by what user specifies...
# TEST
SET @searchtitle = '';
SELECT title, genre, release_date, avg_rating, mpaa_rating
  FROM f_movies
  WHERE title LIKE CONCAT("%", @searchtitle, "%");

## CUSTOMERS
# Subscription ending soonest...
SELECT name, email, subscribe_end
  FROM f_customers
  ORDER BY subscribe_end ASC;

# Show Info By Location...
SELECT name, email, location
  FROM f_customers, f_servers
  WHERE f_customers.S_ID = f_servers.S_ID
  ORDER BY location;

# Count customers by Location
SELECT location, count(*) as Count
  FROM f_customers, f_servers
  WHERE f_customers.S_ID = f_servers.S_ID
  GROUP BY location;

## RATINGS
# By Location...
SELECT location, rating
  FROM f_servers, f_ratings, f_customers
  WHERE f_ratings.C_ID = f_customers.C_ID AND f_customers.S_ID = f_servers.S_ID;

## WATCHES
# By Location... (who is currently watching now)
SELECT location, name
  FROM f_servers, f_watches, f_customers
  WHERE f_watches.C_ID = f_customers.C_ID AND f_customers.S_ID = f_servers.S_ID;

## RECENTS
# Show basic info
SELECT name, title as Recent_Title
  FROM f_customers, f_movies, f_recents
  WHERE f_recents.C_ID = f_customers.C_ID AND f_recents.M_ID = f_movies.M_ID
  ORDER BY name;

## FAVORITES
# Show customer name->favorite info
SELECT name, title as Favorite_Title
  FROM f_customers, f_movies, f_favorites
  WHERE f_favorites.C_ID = f_customers.C_ID AND f_favorites.M_ID = f_movies.M_ID
  ORDER BY name;