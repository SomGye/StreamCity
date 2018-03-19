<?php require_once './includes/secure_conn.php';
require_once('../pdo_config.php');
session_start();
//Check for admin email in session, else boot back to index
$email_admin = false;
if (isset($_SESSION['email'])){
    //Check email for admin string:
    if (strcmp('superduperuser01@hotmail.com', $_SESSION['email']) == 0){
        $email_admin = true;
    }
}
require './includes/header.php';

//If not admin:
if (!$email_admin){
    include './includes/footer.php';
    //Redirect to home
    $url = 'index.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for selectbtn1 (movies)
if (isset($_POST['selectbtn1'])){
    //Perform select actions!
    try{
        $selectsql1 = 'SELECT * FROM f_movies';
        $selectresult1 = $conn->query($selectsql1);
        $numRows = $selectresult1->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }
    
    //Store results in array and pass to session
    $select_result = $selectresult1->fetchAll();
    $_SESSION['select_movies'] = $select_result;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for selectbtn2 (customers)
if (isset($_POST['selectbtn2'])){
    //Perform select actions!
    try{
        $selectsql2 = 'SELECT * FROM f_customers';
        $selectresult2 = $conn->query($selectsql2);
        $numRows = $selectresult2->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }
    
    //Store results in array and pass to session
    $select_result = $selectresult2->fetchAll();
    $_SESSION['select_customers'] = $select_result;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for insertbtn1 (movies)
if (isset($_POST['insertbtn1'])) {
    //Grab and store vars
    $current_title = filter_input(INPUT_POST, minserttext1);
    $current_genre = filter_input(INPUT_POST, minserttext2);
    $current_runtime = filter_input(INPUT_POST, minserttext3);
    $current_release = filter_input(INPUT_POST, minserttext4);
    $current_mpaa = filter_input(INPUT_POST, minserttext5);

    //Perform insert actions!
    try{
        $insmoviestmt = $conn->prepare("INSERT INTO f_movies(title, genre, runtime_minutes, release_date, acquire_date, mpaa_rating) VALUES(:current_title, :current_genre, :current_runtime, :current_release, CURDATE(), :current_mpaa)");
        $insmoviestmt->bindValue(':current_title', $current_title);
        $insmoviestmt->bindValue(':current_genre', $current_genre);
        $insmoviestmt->bindValue(':current_runtime', $current_runtime);
        $insmoviestmt->bindValue(':current_release', $current_release);
        $insmoviestmt->bindValue(':current_mpaa', $current_mpaa);
        $insmoviestmt->execute();
        $insmoviecount = $insmoviestmt->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }

    //Store affected rows and pass to session
    $_SESSION['insert_movies'] = $insmoviecount;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for insertbtn2 (customers)
if (isset($_POST['insertbtn2'])) {
    //Grab and store vars
    $current_server = filter_input(INPUT_POST, cinserttext2);
    $current_name = filter_input(INPUT_POST, cinserttext3);
    $current_email = filter_input(INPUT_POST, cinserttext4);
    //NOTE: subscribe_begin = CURDATE(); subscribe_end = ADDDATE(CURDATE(),30);

    //Perform insert actions!
    try{
        $inscuststmt = $conn->prepare("INSERT INTO f_customers(S_ID, name, email, subscribe_begin, subscribe_end) VALUES(:current_server, :current_name, :current_email, CURDATE(), ADDDATE(CURDATE(), 30))");
        $inscuststmt->bindValue(':current_server', $current_server);
        $inscuststmt->bindValue(':current_name', $current_name);
        $inscuststmt->bindValue(':current_email', $current_email);
        $inscuststmt->execute();
        $inscustcount = $inscuststmt->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }

    //Store affected rows and pass to session
    $_SESSION['insert_customers'] = $inscustcount;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for updatebtn1 (movies)
//NOTE: only use ONE field at a time...
// if title/genre/runtime/avg_rating/mpaa_rating -> text -> mupdatetext1
// if release_date/acquire_date -> date -> mupdatetext1d
if (isset($_POST['updatebtn1'])){
    //Grab field to edit...
    $current_field = $_POST['mupdatefield'];

    //If not date, use mupdatetext1
    if ((strcmp($current_field, 'release_date') != 0) AND
        (strcmp($current_field, 'acquire_date') != 0)){
        //Grab text from text field
        $current_value = filter_input(INPUT_POST, 'mupdatetext1', FILTER_SANITIZE_STRING);
    } //end if for mupdatetext1
    else {
        //Use date field
        $current_value = $_POST['mupdatetext1d'];
    } //end if for mupdatetext1d

    //Grab M_ID to edit...
    $current_M_ID = filter_input(INPUT_POST, 'mupdatetext2');

    //Perform update actions!
    try{
        if (strcmp($current_field, 'title') == 0){
            $updatesql = $conn->prepare("UPDATE f_movies SET title = :current_value WHERE M_ID = :current_M_ID");
        }
        elseif (strcmp($current_field, 'genre') == 0){
            $updatesql = $conn->prepare("UPDATE f_movies SET genre = :current_value WHERE M_ID = :current_M_ID");
        }
        elseif (strcmp($current_field, 'runtime_minutes') == 0){
            $updatesql = $conn->prepare("UPDATE f_movies SET runtime_minutes = :current_value WHERE M_ID = :current_M_ID");
        }
        elseif (strcmp($current_field, 'avg_rating') == 0){
            $updatesql = $conn->prepare("UPDATE f_movies SET avg_rating = :current_value WHERE M_ID = :current_M_ID");
        }
        elseif (strcmp($current_field, 'mpaa_rating') == 0){
            $updatesql = $conn->prepare("UPDATE f_movies SET mpaa_rating = :current_value WHERE M_ID = :current_M_ID");
        }
        elseif (strcmp($current_field, 'release_date') == 0){
            $updatesql = $conn->prepare("UPDATE f_movies SET release_date = :current_value WHERE M_ID = :current_M_ID");
        }
        elseif (strcmp($current_field, 'acquire_date') == 0){
            $updatesql = $conn->prepare("UPDATE f_movies SET acquire_date = :current_value WHERE M_ID = :current_M_ID");
        }
        $updatesql->bindValue(':current_value', $current_value);
        $updatesql->bindValue(':current_M_ID', $current_M_ID);
        $updatesql->execute();
        $updatecount = $updatesql->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }

    //Store affected rows and pass to session
    $_SESSION['update_movies'] = $updatecount;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for updatebtn2 (customers)
//NOTE: only use ONE field at a time...
// if S_ID -> select -> cupdatetext1
// if name/email -> text -> cupdatetext2
if (isset($_POST['updatebtn2'])){
    //Grab field to edit...
    $current_field = $_POST['cupdatefield'];

    //If not S_ID, use cupdatetext2
    if (strcmp($current_field, 'S_ID') != 0){
        //Grab text/email from text field
        $current_value = filter_input(INPUT_POST, 'cupdatetext2', FILTER_DEFAULT);
    } //end if for mupdatetext1
    else {
        //Use select field
        $current_value = $_POST['cupdatetext1'];
    } //end if for mupdatetext1d

    //Grab C_ID to edit...
    $current_C_ID = filter_input(INPUT_POST, 'cupdatetext3');

    //Perform update actions!
    try{
        if (strcmp($current_field, 'S_ID') == 0){
            $updatesql = $conn->prepare("UPDATE f_customers SET S_ID = :current_value WHERE C_ID = :current_C_ID");
        }
        elseif (strcmp($current_field, 'name') == 0){
            $updatesql = $conn->prepare("UPDATE f_customers SET name = :current_value WHERE C_ID = :current_C_ID");
        }
        elseif (strcmp($current_field, 'email') == 0){
            $updatesql = $conn->prepare("UPDATE f_customers SET email = :current_value WHERE C_ID = :current_C_ID");
        }
        $updatesql->bindValue(':current_value', $current_value);
        $updatesql->bindValue(':current_C_ID', $current_C_ID);
        $updatesql->execute();
        $updatecount = $updatesql->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }

    //Store affected rows and pass to session
    $_SESSION['update_customers'] = $updatecount;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for refill sub (customers)
// -- Refill sub for matching C_ID (subscribe_end = ADDDATE(CURDATE(), 30))
if (isset($_POST['crefillbtn'])){
    //Grab current C_ID
    $current_C_ID = filter_input(INPUT_POST, 'crefillsub', FILTER_SANITIZE_STRING);

    //Perform simple query to update customer sub:
    try{
        $refillsql = $conn->prepare("UPDATE f_customers SET subscribe_begin = CURDATE(), subscribe_end = ADDDATE(CURDATE(), 30) WHERE C_ID = :current_C_ID");
        $refillsql->bindValue(':current_C_ID', $current_C_ID);
        $refillsql->execute();
        $refillcount = $refillsql->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }

    //Store affected rows and pass to session
    $_SESSION['refill_customers'] = $refillcount;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for deletebtn1 (movies)
if (isset($_POST['deletebtn1'])){
    //Grab M_ID from field (mdeletetext1)
    $current_M_ID = filter_input(INPUT_POST, 'mdeletetext1', FILTER_SANITIZE_STRING);
    //Perform delete actions!
    try{
        $delmoviestmt = $conn->prepare("DELETE FROM f_movies WHERE M_ID = :current_M_ID");
        $delmoviestmt->bindValue(':current_M_ID', $current_M_ID);
        $delmoviestmt->execute();
        $delmoviecount = $delmoviestmt->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }

    //Store affected rows and pass to session
    $_SESSION['delete_movies'] = $delmoviecount;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Check for deletebtn2 (customers)
if (isset($_POST['deletebtn2'])){
    //Grab C_ID from field (cdeletetext1)
    $current_C_ID = filter_input(INPUT_POST, 'cdeletetext1', FILTER_SANITIZE_STRING);
    //Perform delete actions!
    try{
        $delcuststmt = $conn->prepare("DELETE FROM f_customers WHERE C_ID = :current_C_ID");
        $delcuststmt->bindValue(':current_C_ID', $current_C_ID);
        $delcuststmt->execute();
        $delcustcount = $delcuststmt->rowCount();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: redirect
        unset($_SESSION['admin_message']); //manually unset
        include './includes/footer.php';
        $url = 'admin_message.php';
        echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
        exit();
    }

    //Store affected rows and pass to session
    $_SESSION['delete_customers'] = $delcustcount;

    //Set success
    $_SESSION['admin_message'] = 'success';

    include './includes/footer.php';
    //Redirect to admin_message
    $url = 'admin_message.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}
?>
    <!-- Team Super 7 -->
    <br>
    <section>
        <h2><b>   Admin Menu...</b></h2>
    </section>
    <form class="adminform" method="post" action="admin_menu.php">
        <fieldset class="adminfield">
            <legend>MOVIES</legend>
            <!-- Select movies -->
            <h3><em>Select all Movies...</em></h3>
            <label for="selectbtn1">Select Movies</label>
            &nbsp;<input type="submit" name="selectbtn1" value="Select"><br>
            <!-- Insert movie info... NOTE: M_ID, avg_rating are left NULL, acquire_date = CURDATE()-->
            <br><h3><em>Insert a new Movie...</em></h3>
            <label for="minserttext1">Title:</label>&nbsp;
            <input type="text" name="minserttext1" id="minserttext1">
            <label for="minserttext2">Genre:</label>&nbsp;
            <input type="text" name="minserttext2" id="minserttext2">
            <label for="minserttext3">Runtime Minutes:</label>&nbsp;
            <input type="text" name="minserttext3" id="minserttext3"><br>
            <label for="minserttext4">Release Date:</label>&nbsp;
            <input type="date" name="minserttext4" id="minserttext4">
            <label for="minserttext5">MPAA Rating:</label>&nbsp;
            <input type="text" name="minserttext5" id="minserttext5">
            &nbsp;<input type="submit" name="insertbtn1" value="Insert"><br>
            <!-- Update movie info...-->
            <br><h3><em>Update an existing Movie...</em></h3>
            <label for="mupdatefield">Field to Edit:</label>&nbsp;
            <select name="mupdatefield" id="mupdatefield">
                <option value="title" selected>Title</option>
                <option value="genre">Genre</option>
                <option value="runtime_minutes">Runtime Minutes</option>
                <option value="release_date">Release Date</option>
                <option value="acquire_date">Acquire Date</option>
                <option value="avg_rating">Avg. Rating</option>
                <option value="mpaa_rating">MPAA Rating</option>
            </select>
            <label for="mupdatetext1">Set Field Equal To:</label>&nbsp;
            (Text)<input type="text" name="mupdatetext1" id="mupdatetext1">&nbsp;<label for="mupdatetext1d">(Date) </label>&nbsp;<input type="date" name="mupdatetext1d" id="mupdatetext1d"><br>
            <label for="mupdatetext2">Where M_ID is Equal To:</label>&nbsp;
            <input type="text" name="mupdatetext2" id="mupdatetext2">
            &nbsp;<input type="submit" name="updatebtn1" value="Update"><br>
            <!-- Delete movie  -->
            <br><h3><em>Delete an existing Movie...</em></h3>
            <label for="mdeletetext1">Delete Movie Where M_ID is Equal To:</label>&nbsp;
            <input type="text" name="mdeletetext1" id="mdeletetext1">
            &nbsp;<input type="submit" name="deletebtn1" value="Delete">
        </fieldset><br>
        <fieldset class="adminfield">
            <legend>CUSTOMERS</legend>
            <!-- Select customers -->
            <h3><em>Select all Customers...</em></h3>
            <label for="selectbtn2">Select Customers</label>
            &nbsp;<input type="submit" name="selectbtn2" value="Select"><br>
            <!-- Insert customer info... NOTE: C_ID will be NULL, and subs will have separate refill btn. -->
            <br><h3><em>Insert a new Customer...</em></h3>
            <label for="cinserttext2">Server Location:</label>&nbsp;
            <select name="cinserttext2" id="cinserttext2">
                <option value="1" selected>United States - CA</option>
                <option value="2">United States - NY</option>
                <option value="3">Canada</option>
                <option value="4">Mexico</option>
                <option value="5">United Kingdom - Great Britain</option>
            </select>
            <label for="cinserttext3">Name: </label>&nbsp;
            <input type="text" name="cinserttext3" id="cinserttext3">
            <label for="cinserttext4">Email: </label>&nbsp;
            <input type="text" name="cinserttext4" id="cinserttext4">
            &nbsp;<input type="submit" name="insertbtn2" value="Insert"><br>
            <!-- Update customer info -->
            <br><h3><em>Update an existing Customer...</em></h3>
            <label for="cupdatefield">Field to Edit:</label>&nbsp;
            <select name="cupdatefield" id="cupdatefield">
                <option value="S_ID" selected>Server</option>
                <option value="name">Name</option>
                <option value="email">Email</option>
            </select>
            <label for="cupdatetext1">Set Field Equal To:</label>&nbsp;
            <select name="cupdatetext1" id="cupdatetext1">
                <option value="1" selected>United States - CA</option>
                <option value="2">United States - NY</option>
                <option value="3">Canada</option>
                <option value="4">Mexico</option>
                <option value="5">United Kingdom - Great Britain</option>
            </select>&nbsp;
            <label for="cupdatetext2">(Name/Email)</label>&nbsp;<input type="text" name="cupdatetext2" id="cupdatetext2"><br>
            <label for="cupdatetext3">Where C_ID is Equal To:</label>&nbsp;
            <input type="text" name="cupdatetext3" id="cupdatetext3">
            &nbsp;<input type="submit" name="updatebtn2" value="Update"><br>
            <!-- Refill customer sub. by additional 30 days;
             subscribe_begin=CURDATE(), subscribe_end=ADDDATE(CURDATE(), 30)-->
            <label for="crefillsub">Refill Subscription (30 day) Where C_ID is:</label>&nbsp;
            <input type="text" name="crefillsub" id="crefillsub">&nbsp;
            <input type="submit" name="crefillbtn" value="Refill">
            <!-- Delete customer -->
            <br><h3><em>Delete an existing Customer...</em></h3>
            <label for="cdeletetext1">Delete Customer Where C_ID is Equal To:</label>&nbsp;
            <input type="text" name="cdeletetext1" id="cdeletetext1">
            &nbsp;<input type="submit" name="deletebtn2" value="Delete">
        </fieldset><br>
    </form><br><br>
    <?php include './includes/footer.php'; ?>