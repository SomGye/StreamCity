## RECENTS
# Show basic info
SELECT name, title as Recent_Title
  FROM f_customers, f_movies, f_recents
  WHERE f_recents.C_ID = f_customers.C_ID AND f_recents.M_ID = f_movies.M_ID
  ORDER BY name;

#Show recents, per customer matched by email
SELECT email, f_recents.M_ID, title as Recent_Title
  FROM f_customers, f_movies, f_recents
  WHERE f_recents.C_ID = f_customers.C_ID AND f_recents.M_ID = f_movies.M_ID
  ORDER BY email;

# Show recents per SELECTED CUSTOMER by email
SET @testemail = 'IM_READY@aol.com';
SELECT email, f_recents.M_ID, title
  FROM f_customers, f_movies, f_recents
  WHERE f_recents.C_ID = f_customers.C_ID AND f_recents.M_ID = f_movies.M_ID AND email = @testemail
  ORDER BY title;