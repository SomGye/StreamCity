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

//Grab Movie ID string
$current_M_ID = "";
if (isset($_GET['current_M_ID'])){
    $current_M_ID = $_GET['current_M_ID'];
    $_SESSION['current_M_ID'] = $current_M_ID;
}
else {echo 'Error: No movie ID sent from previous page!';}
require './includes/header.php';

//Check watches submission
if (isset($_GET['watchbtn'])){
    //Go to watches.php with ?url
    include './includes/footer.php';
    $url = 'watches.php?current_M_ID=' . $current_M_ID;
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}
?>
    <!-- Team Super 7 -->
    <br>
    <section>
        <h2><b>   Currently Selected Movie...</b></h2>
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

        //Grab movie info for MATCHING M_ID
        try{
            $infosql = $conn->prepare("SELECT title, genre, runtime_minutes, release_date, avg_rating, mpaa_rating FROM f_movies, f_media WHERE f_movies.M_ID = :current_M_ID");
            $infosql->bindValue(':current_M_ID', $current_M_ID);
            $infosql->execute();
            $infores = $infosql->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab "simple_date" version of release date from stored func
        try{
            $datesql = $conn->prepare("SELECT simple_date(f_movies.release_date) AS 'release_date' FROM f_movies WHERE f_movies.M_ID = :current_M_ID");
            $datesql->bindValue(':current_M_ID', $current_M_ID);
            $datesql->execute();
            $dateres = $datesql->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Grab "overall_rating" of avg_rating from stored func
        try{
            $ratesql = $conn->prepare("SELECT overall_rating(f_movies.avg_rating) AS 'overall_rating' FROM f_movies WHERE f_movies.M_ID = :current_M_ID");
            $ratesql->bindValue(':current_M_ID', $current_M_ID);
            $ratesql->execute();
            $rateres = $ratesql->fetchAll();
        }
        catch (PDOException $e) {
            echo $e->getMessage();
            exit();
        }

        //Get movie info from results:
        foreach ($mediares as $row) {
            $filename = $row['filename'];
            $movietitle = $infores[0]['title']; //was [$mediaid]['title']
            $moviegenre = $infores[0]['genre'];
            $movieruntime = $infores[0]['runtime_minutes'];
            $moviereleasedate = $dateres[0]['release_date'];
            $movieavg_rating = $infores[0]['avg_rating'];
            $movieoverall_rating = $rateres[0]['overall_rating'];
            $moviempaa_rating = $infores[0]['mpaa_rating'];
        }
        ?>
        <!-- Show movie poster img, as 400x600-->
        <div class="infoleft">
            <img src = "../media/<?php echo $filename;?>" <?php echo ' alt="' . $movietitle . '"';?> width="400" height="600">
            <br>
            <h2><b><em>Rated: <?php echo $moviempaa_rating;?></em></b></h2>
        </div>
        <!-- Show movie info: Title, Genre, Runtime Minutes, Release Date,
        Average Rating, MPAA Rating-->
        <div class="inforight">
            <h2><b>Title: <?php echo $movietitle;?></b></h2>
            <h2>
                Genre: <?php echo $moviegenre;?><br>
                Runtime: <?php
                $runtimeint = (int)$movieruntime;
                $runtimehr = floor($runtimeint/60);
                $runtimemin = $runtimeint%60;
                echo $runtimehr . ' hours and ' . $runtimemin . ' minutes';
                ?><br>
                Release Date: <?php echo $moviereleasedate;?><br>
                Average Rating: <?php
                if (isset($movieavg_rating)){
                    echo $movieavg_rating . ' (' . $movieoverall_rating . ')';
                }
                else {
                    echo 'Not yet rated...';
                }
                ?>
            </h2><br><br>
            <form method="get" action="movie_info.php">
                <input type="submit" name="watchbtn" value="Watch This Movie!">
            </form>
        </div>
    </fieldset>
    <?php include './includes/footer.php'; ?>