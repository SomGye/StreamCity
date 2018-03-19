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

//Grab needed sess vars
if (isset($_SESSION)){
    $current_M_ID = $_SESSION['current_M_ID'];
}
require './includes/header.php';

$current_rating = 3; //default to Decent rating
$current_C_ID = 0; //not set
$errors = array(); //for checking duplicates/faves
$msgs = array(); //for checking additional msgs for labels...

//Grab C_ID by MATCHING email
try{
    $cidsql = $conn->prepare("SELECT C_ID FROM f_customers WHERE email = :current_email");
    $cidsql->bindValue(':current_email', $current_email);
    $cidsql->execute();
    $cidres = $cidsql->fetchAll();
}
catch (PDOException $e) {
    echo $e->getMessage();
    exit();
}

foreach ($cidres as $crow){
    $current_C_ID = $crow['C_ID'];
}
unset($crow); //manually clear pointer

//Check submission of rating...
if (isset($_GET['ratebtn'])){
    //Check actual rating from radio button group
    if (isset($_GET['rating'])){
        $current_rating = filter_input(INPUT_GET, 'rating');
    }

    //Call 'check_and_insert_f_watches' stored proc to decide if insert or delete/insert
    try{
        $watchprocsql = $conn->prepare("CALL check_and_insert_f_watches(:current_C_ID, :current_M_ID, :current_rating)");
        $watchprocsql->bindValue(':current_C_ID', $current_C_ID);
        $watchprocsql->bindValue(':current_M_ID', $current_M_ID);
        $watchprocsql->bindValue(':current_rating', $current_rating);
        $watchprocsql->execute();
        $watchprocres = $watchprocsql->fetchAll();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }

    //Insertion/update success! -> go back to index_signedin.php
    echo '<br><br><br><p style="text-align: center"><em>Going back home in 2 seconds...</em></p>';
    include './includes/footer.php';
    $url = 'index_signedin.php';
    echo '<meta http-equiv="refresh" content="2;url='. $url .'">';
    exit();
} //end ratebtn check

//Check submission of favoriting...
if (isset($_GET['favbtn'])){
    //Perform stored proc. to check if current C_ID CAN fave this M_ID...
    try{
        $favprocsql = $conn->prepare("CALL check_favorite_validity(:current_C_ID, :current_M_ID, @fav_validity_value)");
        $favprocsql->bindValue(':current_C_ID', $current_C_ID);
        $favprocsql->bindValue(':current_M_ID', $current_M_ID);
        $favprocsql->execute();
        $favprocres = $favprocsql->fetchAll();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }
    //Get result of favorite stored proc. from basic SELECT
    $favprocvalidity = false;
    try{
        $favselsql = "SELECT @fav_validity_value AS 'validity'";
        foreach($conn->query($favselsql) as $frow){
            if (strcmp($frow['validity'], 'VALID') == 0){
                $favprocvalidity = true; //flag ability to fave this!
            }
        }
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }
    //Only insert new favorite if valid!
    if ($favprocvalidity){
        try{
            $favinssql = $conn->prepare("INSERT INTO f_favorites VALUES(:current_C_ID, :current_M_ID)");
            $favinssql->bindValue(':current_C_ID', $current_C_ID);
            $favinssql->bindValue(':current_M_ID', $current_M_ID);
            $favinssql->execute();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }
        $msgs[] = 'favorites';
    }
    else {
        $errors[] = 'favorites';
    }
} //end favbtn check

//Check submission of unfavoriting...
if (isset($_GET['unfavbtn'])){
    //Perform deletion of C_ID/M_ID pair from table...
    try{
        $favdelsql = $conn->prepare("DELETE FROM f_favorites WHERE C_ID = :current_C_ID AND M_ID = :current_M_ID");
        $favdelsql->bindValue(':current_C_ID', $current_C_ID);
        $favdelsql->bindValue(':current_M_ID', $current_M_ID);
        $favdelsql->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }
    $msgs[] = 'unfavorites';
}
?>
    <!-- Team Super 7 -->
    <br>
    <section>
        <h2><b>   Currently Watching...</b></h2>
    </section>
    <fieldset class="moviefield">
        <legend>Your Movie</legend>
        <?php
        //Grab imgs
        try{
            $mediasql = $conn->prepare("SELECT MEDIA_ID, filename FROM f_media WHERE MEDIA_ID = :current_M_ID");
            $mediasql->bindValue(':current_M_ID', $current_M_ID);
            $mediasql->execute();
            $mediares = $mediasql->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab movie title for MATCHING M_ID
        try{
            $infosql = $conn->prepare("SELECT title FROM f_movies, f_media WHERE f_movies.M_ID = :current_M_ID");
            $infosql->bindValue(':current_M_ID', $current_M_ID);
            $infosql->execute();
            $infores = $infosql->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Get movie info from results:
        foreach ($mediares as $row) {
            $filename = $row['filename'];
            $movietitle = $infores[0]['title'];
        }
        ?>
        <!-- Show movie poster img, as 400x600-->
        <div class="infoleft">
            <img src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"';?> width="400" height="600">
            <h2><b><?php echo $movietitle;?></b></h2>
        </div>
        <!-- Show rating form -->
        <div class="watchesform">
            <form method="get" action="watches.php">
                <label for="rating">Rate this movie:</label>
                <input type="radio" name="rating" value="1"> Bad!&nbsp;&nbsp;
                <input type="radio" name="rating" value="2"> Not Great&nbsp;&nbsp;
                <input type="radio" name="rating" value="3" checked> Decent&nbsp;&nbsp;
                <input type="radio" name="rating" value="4"> Great&nbsp;&nbsp;
                <input type="radio" name="rating" value="5"> Excellent!&nbsp;&nbsp;<br>
                <!-- Button to RATE a movie -->
                <input type="submit" name="ratebtn" value="Rate and Close Movie"><br>
                <!-- Button to FAVORITE a movie -->
                <label for="favbtn">--
                <?php if($errors && in_array('favorites', $errors)) { ?>
                    <span class="warning"> Current movie already in Favorites!</span>
                <?php }
                if($msgs && in_array('favorites', $msgs)) { ?>
                    <span>Successfully added to favorites!</span>
                <?php }
                ?></label><br>
                <input type="submit" name="favbtn" value="Favorite This!"><br>
                <!-- Button to UNFAVORITE a movie -->
                <label for="unfavbtn">--
                <?php if($msgs && in_array('unfavorites', $msgs)) { ?>
                    <span>Successfully removed from favorites.</span>
                <?php }
                ?></label><br>
                <input type="submit" name="unfavbtn" value="Un-favorite This ;(">
            </form>
        </div>
    </fieldset>
<?php include './includes/footer.php'; ?>