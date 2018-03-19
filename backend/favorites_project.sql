## FAVORITES
# Show ALL customer names->favorite movie
SELECT name, title as Favorite_Title
  FROM f_customers, f_movies, f_favorites
  WHERE f_favorites.C_ID = f_customers.C_ID AND f_favorites.M_ID = f_movies.M_ID
  ORDER BY name;

# Show favorites, per customer matched by email
SELECT email, f_favorites.M_ID, title
FROM f_customers, f_movies, f_favorites
WHERE f_favorites.C_ID = f_customers.C_ID AND f_favorites.M_ID = f_movies.M_ID
ORDER BY email;

# Show favorite per SELECTED CUSTOMER by email
SET @testemail = 'IM_READY@aol.com';
SELECT email, f_favorites.M_ID, title
FROM f_customers, f_movies, f_favorites
WHERE f_favorites.C_ID = f_customers.C_ID AND f_favorites.M_ID = f_movies.M_ID AND email = @testemail
ORDER BY title;