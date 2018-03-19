<?php require_once './includes/secure_conn.php';
require_once('../pdo_config.php');
session_start();
//Check that session has email, else quit to home
if (isset($_SESSION['email'])){
    $current_email = $_SESSION['email'];
}
else{
    //Redirect to home
    $url = 'index.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">'; //$url defined in secure_conn.php
    exit();
}

$current_M_ID = 1; //temp
$M_IDs = array(); //temp
$current_subend = null; //temp
require './includes/header.php'; ?>
    <!-- Team Super 7 -->
    <h5> Welcome to StreamCity! </h5>
    <br>
    <!-- Show subscription quick status -->
    <section style="color: lightgray; text-align: right; margin-right: 10%;">
        <?php
        echo '<h3>';
        //Grab subscription_end for current customer (using simple_date stored func)
        try{
            $substmt = $conn->prepare("SELECT simple_date(f_customers.subscribe_end) AS subend FROM f_customers WHERE email = :current_email");
            $substmt->bindValue(':current_email', $current_email);
            $substmt->execute();
            $subres = $substmt->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        foreach ($subres as $subrow) {
            $current_subend = $subrow['subend'];
        }
        unset($subrow);
        echo 'Subscription Ends: ' . $current_subend;
        echo '</h3>';

        //Show link to 'refill' page (resubscribe)
        echo '<h3><a href="resubscribe.php">Go here to refill subscription!</a></h3>';
        ?>
    </section>
    <br>
    <section>
        <h2><b>   Favorites...</b></h2><br>
    </section>
    <div class="moviediv">
        <?php
        //Grab imgs
        try{
            $sql = 'SELECT MEDIA_ID, filename FROM f_media';
            $result = $conn->query($sql);
            $numRows = $result->rowCount();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab movie titles
        try{
            $sql2 = 'SELECT title FROM f_movies, f_media WHERE f_movies.M_ID = f_media.M_ID';
            $result2 = $conn->query($sql2);
            $res2 = $result2->fetchAll();
            $numRows2 = $result->rowCount();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab favorites, per email
        try{
            $favstmt = $conn->prepare("SELECT email, f_favorites.M_ID, title FROM f_customers, f_movies, f_favorites WHERE f_favorites.C_ID = f_customers.C_ID AND f_favorites.M_ID = f_movies.M_ID AND email = :current_email ORDER BY title");
            $favstmt->bindValue(':current_email', $current_email);
            $favstmt->execute();
            $favresult = $favstmt->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //grab M_ID's from $favresult and store in array
        foreach ($favresult as $frow) {
            $current_M_ID = (int)$frow['M_ID'];
            $M_IDs[] = $current_M_ID;
        }
        unset($frow);

        //loop thru Media result to get info
        asort($M_IDs);
        foreach ($result as $row) { ?>
            <?php
            $searchid = $row['MEDIA_ID'];
            $mediaid = (int)$row['MEDIA_ID'];
            if (!in_array($searchid, $M_IDs)){
                continue; //skip if not in current favorites!
            }
            $filename = $row['filename'];
            $movietitle = $res2[$mediaid-1]['title'];?>
            <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
        <?php } //end loop
        unset($row);
        unset($searchid);
        $current_M_ID = 1;
        $M_IDs = array(); ?>
    </div>
    <br><br>
    <section>
        <h2><b>   Recents...</b></h2><br>
    </section>
    <div class="moviediv">
        <?php
        //Grab recent, per email
        try{
            $recstmt = $conn->prepare("SELECT email, f_recents.M_ID, title FROM f_customers, f_movies, f_recents WHERE f_recents.C_ID = f_customers.C_ID AND f_recents.M_ID = f_movies.M_ID AND email = :current_email ORDER BY title");
            $recstmt->bindValue(':current_email', $current_email);
            $recstmt->execute();
            $recresult = $recstmt->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //grab M_ID's from $recresult and store in array
        foreach ($recresult as $rrow) {
            $current_M_ID = (int)$rrow['M_ID'];
            $M_IDs[] = $current_M_ID;
        }
        unset($rrow);

        //Grab imgs
        try{
            $sql = 'SELECT MEDIA_ID, filename FROM f_media';
            $result = $conn->query($sql);
            $numRows = $result->rowCount();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //loop thru Media result to get info
        asort($M_IDs);
        foreach ($result as $row) { ?>
            <?php
            $searchid = $row['MEDIA_ID'];
            $mediaid = (int)$row['MEDIA_ID'];
            if (!in_array($searchid, $M_IDs)){
                continue; //skip if not in current!
            }
            $filename = $row['filename'];
            $movietitle = $res2[$mediaid-1]['title'];?>
            <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
        <?php } //end loop
        unset($row);
        unset($searchid);
        $current_M_ID = 1;
        $M_IDs = array(); ?>
    </div>
    <br><br>
    <section>
        <h2><b>   Highest Rated Overall...</b></h2><br>
    </section>
    <div class="moviediv">
        <?php
        //Grab highest rated (top 5)
        try{
            $highsql = 'SELECT M_ID, title FROM f_movies ORDER BY avg_rating DESC LIMIT 5';
            $highstmt = $conn->query($highsql);
            $highresult = $highstmt->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //grab M_ID's from $highresult and store in array
        foreach ($highresult as $hrow) {
            $current_M_ID = (int)$hrow['M_ID'];
            $M_IDs[] = $current_M_ID;
        }
        unset($hrow);

        //Grab imgs
        try{
            $sql = 'SELECT MEDIA_ID, filename FROM f_media';
            $result = $conn->query($sql);
            $numRows = $result->rowCount();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //loop thru Media result to get info
        asort($M_IDs);
        foreach ($result as $row) { ?>
            <?php
            $searchid = $row['MEDIA_ID'];
            $mediaid = (int)$row['MEDIA_ID'];
            if (!in_array($searchid, $M_IDs)){
                continue; //skip if not in current!
            }
            $filename = $row['filename'];
            $movietitle = $res2[$mediaid-1]['title'];?>
            <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
        <?php } //end loop
        unset($row);
        unset($searchid);
        $current_M_ID = 1;
        $M_IDs = array(); ?>
    </div>
    <br><br>
    <!-- Start of genres -> grab list of genres, then dynamically create divs per found genre -->
    <?php
        //Grab list of genres
        try{
            $genresql = 'SELECT DISTINCT genre FROM f_movies ORDER BY genre ASC';
            $genrestmt = $conn->query($genresql);
            $genreresult = $genrestmt->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Generate sections/div per genre
        foreach ($genreresult as $genre){
            $current_genre = $genre['genre'];
            echo '<section><h2><b>   ' . $current_genre . '...</b></h2><br></section>';
            echo '<div class="moviediv">'; //start of div

            //Grab all movies of current genre
            try{
                $genremoviestmt = $conn->prepare("SELECT M_ID, title FROM f_movies WHERE genre = :genre");
                $genremoviestmt->bindValue(':genre', $current_genre);
                $genremoviestmt->execute();
                $genremovieres = $genremoviestmt->fetchAll();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                exit();
            }

            //grab M_ID's and store in array
            foreach ($genremovieres as $row) {
                $current_M_ID = (int)$row['M_ID'];
                $M_IDs[] = $current_M_ID;
            }
            unset($row);

            //Grab imgs
            try{
                $sql = 'SELECT MEDIA_ID, filename FROM f_media';
                $result = $conn->query($sql);
                $numRows = $result->rowCount();
            }
            catch (PDOException $e) {
                echo $e->getMessage();
                exit();
            }

            //loop thru Media result to get info
            asort($M_IDs);
            foreach ($result as $row) { ?>
                <?php
                $searchid = $row['MEDIA_ID'];
                $mediaid = (int)$row['MEDIA_ID'];
                if (!in_array($searchid, $M_IDs)){
                    continue; //skip if not in search!
                }
                $filename = $row['filename'];
                $movietitle = $res2[$mediaid-1]['title'];?>
                <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
            <?php } //end loop
            unset($row);
            unset($searchid);
            $current_M_ID = 1;
            $M_IDs = array();

            echo '</div><br><br>'; //end div
        } //end genre loop!
    ?>
    <br><br>
    <!-- Start of MPAA ratings -> grab list of ratings, then dynamically create divs per found rating group -->
    <?php
    //Grab list of MPAA ratings
    try{
        $mpaasql = 'SELECT DISTINCT mpaa_rating FROM f_movies ORDER BY mpaa_rating DESC';
        $mpaastmt = $conn->query($mpaasql);
        $mpaares = $mpaastmt->fetchAll();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }

    //Generate sections/div per genre
    foreach ($mpaares as $mpaa){
        $current_mpaa = $mpaa['mpaa_rating'];
        echo '<section><h2><b>   Rated: ' . $current_mpaa . '...</b></h2><br></section>';
        echo '<div class="moviediv">'; //start of div

        //Grab all movies of current rating group
        try{
            $mpaamoviestmt = $conn->prepare("SELECT M_ID, title FROM f_movies WHERE mpaa_rating = :mpaa_rating");
            $mpaamoviestmt->bindValue(':mpaa_rating', $current_mpaa);
            $mpaamoviestmt->execute();
            $mpaamovieres = $mpaamoviestmt->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //grab M_ID's and store in array
        foreach ($mpaamovieres as $row) {
            $current_M_ID = (int)$row['M_ID'];
            $M_IDs[] = $current_M_ID;
        }
        unset($row);

        //Grab imgs
        try{
            $sql = 'SELECT MEDIA_ID, filename FROM f_media';
            $result = $conn->query($sql);
            $numRows = $result->rowCount();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //loop thru Media result to get info
        asort($M_IDs);
        foreach ($result as $row) { ?>
            <?php
            $searchid = $row['MEDIA_ID'];
            $mediaid = (int)$row['MEDIA_ID'];
            if (!in_array($searchid, $M_IDs)){
                continue; //skip if not in search!
            }
            $filename = $row['filename'];
            $movietitle = $res2[$mediaid-1]['title'];?>
            <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
        <?php } //end loop
        unset($row);
        unset($searchid);
        $current_M_ID = 1;
        $M_IDs = array();

        echo '</div><br><br>'; //end div
    } //end MPAA loop!
    ?>
    <br><br><br>
<?php include './includes/footer.php'; ?>