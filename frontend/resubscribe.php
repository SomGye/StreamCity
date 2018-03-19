<?php require_once './includes/secure_conn.php';
require_once ('../pdo_config.php'); // Connect to the db.
session_start();
//Check for email in session, else boot back to index
if (!isset($_SESSION['email'])){
    include './includes/footer.php';
    //Redirect to home
    $url = 'index.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

//Grab C_ID, else boot to index
if (isset($_SESSION['email'])){
    $current_email = $_SESSION['email'];
    //Grab C_ID by email
    try{
        $getcidsql = $conn->prepare("SELECT C_ID FROM f_customers WHERE email = :current_email");
        $getcidsql->bindValue(':current_email', $current_email);
        $getcidsql->execute();
        $getcidres = $getcidsql->fetchAll();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        exit();
    }

    //Get resulting C_ID
    foreach($getcidres as $crow){
        $current_C_ID = $crow['C_ID'];
    }
}
else {
    include './includes/footer.php';
    //Redirect to home
    $url = 'index.php';
    echo '<meta http-equiv="refresh" content="0;url='. $url .'">';
    exit();
}

require './includes/header.php';

//Check for refillbtn submission...
if (isset($_POST['refillbtn'])){
    //Perform simple query to update customer sub:
    try{
        $refillsql = $conn->prepare("UPDATE f_customers SET subscribe_begin = CURDATE(), subscribe_end = ADDDATE(CURDATE(), 30) WHERE C_ID = :current_C_ID");
        $refillsql->bindValue(':current_C_ID', $current_C_ID);
        $refillsql->execute();
    }
    catch (PDOException $e) {
        echo $e->getMessage();
        //Failure: set message
        $message = 'Unfortunately, an error occurred.';
    }

    //Set success and redirect
    unset($_SESSION['substatus']);
    if (!isset($message)){
        $message = 'Subscription successfully refilled!';
    }
    echo '<br><br><br><h3 style="text-align: center;">' . $message . '<br>Redirecting to home in 2 seconds...</h3>';
    unset($message);
    include './includes/footer.php';
    //Redirect to home (index_signedin)
    $url = 'index_signedin.php';
    echo '<meta http-equiv="refresh" content="2;url='. $url .'">';
    exit();
}
?>
    <!-- Team Super 7 -->
    <br>
    <section>
        <h2><b>   Resubscribe...</b></h2>
    </section>
    <br><br>
    <form method="post" action="resubscribe.php">
        <fieldset style="text-align: center;">
            <legend>'Buy' more subscription time!</legend>
            <br><br>
            <label for="refillbtn">Refill your subscription!</label><br>
            <input type="submit" name="refillbtn" value="Refill">
        </fieldset><br>
    </form>
    <br><br>
<?php include './includes/footer.php'; ?>