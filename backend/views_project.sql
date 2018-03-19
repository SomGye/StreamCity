#VIEWS
DROP VIEW IF EXISTS f_customers_subs;
DROP VIEW IF EXISTS f_movies_common;
DROP VIEW IF EXISTS f_servers_customers;

#1 customers (name, email, subscribe_end)
CREATE VIEW f_customers_subs AS SELECT name, email, simple_date(subscribe_end) AS Subscription_Ends FROM f_customers ORDER BY name;
# SELECT * FROM f_customers_subs;

#2 movies (title, genre, release_date, avg_rating)
CREATE VIEW f_movies_common AS SELECT title, genre, runtime_minutes,release_date, avg_rating, mpaa_rating from f_movies ORDER BY title;
# SELECT * FROM f_movies_common;

#3 customers by server (name, location)
CREATE VIEW f_servers_customers AS SELECT f_servers.location, f_customers.name from f_servers, f_customers
WHERE f_customers.S_ID = f_servers.S_ID ORDER BY location;
# SELECT * FROM f_servers_customers;

#4 ratings, showing customer name, movie name, and 'overall_rating' in readable form
CREATE VIEW f_ratings_common AS SELECT f_customers.name, f_movies.title, overall_rating(f_ratings.rating) FROM f_ratings, f_customers, f_movies
  WHERE f_ratings.C_ID = f_customers.C_ID AND f_ratings.M_ID = f_movies.M_ID;
# SELECT * FROM f_ratings_common;
