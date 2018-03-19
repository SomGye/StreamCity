# TOP 5 MOVIES
SELECT title, avg_rating
  FROM f_movies
  ORDER BY avg_rating DESC LIMIT 5;

#For frontend
SELECT M_ID, title
  FROM f_movies
  ORDER BY avg_rating DESC LIMIT 5;