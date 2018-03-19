#Update avg_rating in f_movies by matching M_ID in f_ratings
UPDATE f_movies m JOIN
  (SELECT M_ID, avg(rating) as avg_score
    FROM f_ratings r
    GROUP BY M_ID) r
  ON m.M_ID = r.M_ID
  SET m.avg_rating = r.avg_score;