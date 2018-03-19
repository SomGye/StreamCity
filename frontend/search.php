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
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Grab search string
$searchtext = "";
if (isset($_POST['searchsubmit'])){
    //Search box text, need to trim/filter
    if (!empty(trim(filter_input(INPUT_POST, 'searchtext', FILTER_SANITIZE_STRING))))
        $searchtext = trim(filter_input(INPUT_POST, 'searchtext', FILTER_SANITIZE_STRING));
    //Set session var for searchtext...
    $_SESSION['searchtext'] = $searchtext;
}

$current_M_ID = 1; //temp
$M_IDs = array(); //temp
$titles = array();
require './includes/header.php'; ?>
    <!-- Team Super 7 -->
    <h5> Welcome to StreamCity! </h5>
    <br>
    <section>
        <h2><b>   Search Results (by Title)...</b></h2><br>
    </section>
    <div class="moviediv">
        <?php
        //Grab imgs
        try{
            $sql = 'SELECT MEDIA_ID, filename FROM f_media';
            $result = $conn->query($sql);
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
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab movie titles THAT MATCH SEARCH STRING
        try{
            $searchsql = $conn->prepare("SELECT f_movies.title, f_media.MEDIA_ID, f_media.filename FROM f_movies, f_media WHERE (f_movies.M_ID = f_media.M_ID) AND (title LIKE CONCAT('%', :searchtext, '%'))");
            $searchsql->bindValue(':searchtext', $searchtext);
            $searchsql->execute();
            $searchres = $searchsql->fetchAll();
            $numRows = $searchsql->rowCount();
            echo '<h4><em> We found ' . $numRows . ' title(s) that matched your search.</em></h4>';
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        // Grab M_ID's from result and store in array
        foreach ($searchres as $srow) {
            $current_M_ID = (int)$srow['MEDIA_ID']; //not M_ID!
            $M_IDs[] = $current_M_ID;
        }
        unset($srow);

        // Loop thru Media result to get info
        asort($M_IDs);
        foreach ($result as $row) { ?>
            <?php
            $searchid = $row['MEDIA_ID'];
            $mediaid = (int)$row['MEDIA_ID'];
            if (!in_array($searchid, $M_IDs)){
                continue; //skip if not in current!
            }
            $filename = $row['filename'];
            $movietitle = $res2[$mediaid-1]['title']; //note the -1 offset!
            $titles[] = $movietitle; ?>
            <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
        <?php } //end loop
        unset($row);
        unset($searchid);

        //Show movie titles
        echo '<br>';
        echo '<p>';
        foreach ($titles as $t) {
            echo '  ' . $t . '  --  ';
        }
        echo '</p>';
        $current_M_ID = 1;
        $M_IDs = array();
        $titles = array(); ?>
    </div>
    <br><br>
    <section>
        <h2><b>   Search Results (by Genre)...</b></h2><br>
    </section>
    <div class="moviediv">
        <?php
        //Grab imgs
        try{
            $sql = 'SELECT MEDIA_ID, filename FROM f_media';
            $result = $conn->query($sql);
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
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab movie genres THAT MATCH SEARCH STRING
        try{
            $searchsql = $conn->prepare("SELECT f_movies.title, f_media.MEDIA_ID, f_media.filename FROM f_movies, f_media WHERE (f_movies.M_ID = f_media.M_ID) AND (genre LIKE CONCAT('%', :searchtext, '%'))");
            $searchsql->bindValue(':searchtext', $searchtext);
            $searchsql->execute();
            $searchres = $searchsql->fetchAll();
            $numRows = $searchsql->rowCount();
            echo '<h4><em> We found ' . $numRows . ' title(s) that matched your search.</em></h4>';
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        // Grab M_ID's from result and store in array
        foreach ($searchres as $srow) {
            $current_M_ID = (int)$srow['MEDIA_ID']; //not M_ID!
            $M_IDs[] = $current_M_ID;
        }
        unset($srow);

        // Loop thru Media result to get info
        asort($M_IDs);
        foreach ($result as $row) { ?>
            <?php
            $searchid = $row['MEDIA_ID'];
            $mediaid = (int)$row['MEDIA_ID'];
            if (!in_array($searchid, $M_IDs)){
                continue; //skip if not in current!
            }
            $filename = $row['filename'];
            $movietitle = $res2[$mediaid-1]['title']; //note the -1 offset!
            $titles[] = $movietitle; ?>
            <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
        <?php } //end loop
        unset($row);
        unset($searchid);

        //Show movie titles
        echo '<br>';
        echo '<p>';
        foreach ($titles as $t) {
            echo '  ' . $t . '  --  ';
        }
        echo '</p>';
        $current_M_ID = 1;
        $M_IDs = array();
        $titles = array(); ?>
    </div>
    <br><br>
    <section>
        <h2><b>   Search Results (by MPAA Rating)...</b></h2><br>
    </section>
    <div class="moviediv">
        <?php
        //Grab imgs
        try{
            $sql = 'SELECT MEDIA_ID, filename FROM f_media';
            $result = $conn->query($sql);
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
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab movie MPAA ratings THAT MATCH SEARCH STRING
        try{
            $searchsql = $conn->prepare("SELECT f_movies.title, f_media.MEDIA_ID, f_media.filename FROM f_movies, f_media WHERE f_movies.M_ID = f_media.M_ID AND mpaa_rating = :searchtext");
            $searchsql->bindValue(':searchtext', $searchtext);
            $searchsql->execute();
            $searchres = $searchsql->fetchAll();
            $numRows = $searchsql->rowCount();
            echo '<h4><em> We found ' . $numRows . ' title(s) that matched your search.</em></h4>';
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        // Grab M_ID's from result and store in array
        foreach ($searchres as $srow) {
            $current_M_ID = (int)$srow['MEDIA_ID']; //not M_ID!
            $M_IDs[] = $current_M_ID;
        }
        unset($srow);

        // Loop thru Media result to get info
        asort($M_IDs);
        foreach ($result as $row) { ?>
            <?php
            $searchid = $row['MEDIA_ID'];
            $mediaid = (int)$row['MEDIA_ID'];
            if (!in_array($searchid, $M_IDs)){
                continue; //skip if not in current!
            }
            $filename = $row['filename'];
            $movietitle = $res2[$mediaid-1]['title']; //note the -1 offset!
            $titles[] = $movietitle; ?>
            <a href="movie_info.php?current_M_ID=<?php echo $searchid;?>"><img class="searchimg" src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"'; echo ' title="' . $movietitle . '"';?> width="125" height="160"></a>&nbsp;&nbsp;
        <?php } //end loop
        unset($row);
        unset($searchid);

        //Show movie titles
        echo '<br>';
        echo '<p>';
        foreach ($titles as $t) {
            echo '  ' . $t . '  --  ';
        }
        echo '</p>';
        $current_M_ID = 1;
        $M_IDs = array();
        $titles = array(); ?>
    </div>
    <br>
<?php include './includes/footer.php'; ?>