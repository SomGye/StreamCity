<?php require_once './includes/secure_conn.php';
session_start();
//Check for admin email in session, else boot back to index
$email_admin = false;
if (isset($_SESSION['email'])){
    //Check email for admin string:
    if (strcmp('superduperuser01@hotmail.com', $_SESSION['email']) == 0){
        $email_admin = true;
    }
}

//Check for success/fail:
$success = false;
if (isset($_SESSION['admin_message'])){
    $admin_message = $_SESSION['admin_message'];
    if (strcmp($admin_message, 'success') == 0){
        $success = true;
    }
    else {
        $success = false;
    }
}

//Check for select result or rowcount (insert/update/delete):
$select = false;
if (isset($_SESSION['select_movies']) || isset($_SESSION['select_customers'])){
    $select = true; //flag for display format later!
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
?>
    <!-- Team Super 7 -->
    <br>
    <section>
        <h2><b>   Admin Menu...</b></h2>
    </section>
        <fieldset class="adminfield">
            <legend>Output Message</legend>
            <!-- Output success/fail message-->
            <h3>Output was a <?php if ($success){echo 'success!';} else {echo 'failure.';}?></h3>
            <!-- Show results (rowcount affected, or items if select) -->
            <?php
            if ($select){
                //Show select results in table:
                echo '<table>';
                if (isset($_SESSION['select_movies'])) {
                    $select_result = $_SESSION['select_movies'];
                    echo '<tr>';
                    echo '<th>M_ID</th>';
                    echo '<th>title</th>';
                    echo '<th>genre</th>';
                    echo '<th>runtime_minutes</th>';
                    echo '<th>release_date</th>';
                    echo '<th>acquire_date</th>';
                    echo '<th>avg_rating</th>';
                    echo '<th>mpaa_rating</th>';
                    echo '</tr>';

                }
                elseif (isset($_SESSION['select_customers'])) {
                    $select_result = $_SESSION['select_customers'];
                    echo '<tr>';
                    echo '<th>C_ID</th>';
                    echo '<th>S_ID</th>';
                    echo '<th>name</th>';
                    echo '<th>email</th>';
                    echo '<th>subscribe_begin</th>';
                    echo '<th>subscribe_end</th>';
                    echo '</tr>';
                }
                
                foreach ($select_result as $srow){
                    //Echo results
                    echo '<tr>';
                    if (isset($_SESSION['select_movies'])) {
                        echo '<td>' . $srow['M_ID'] . '</td>';
                        echo '<td>' . $srow['title'] . '</td>';
                        echo '<td>' . $srow['genre'] . '</td>';
                        echo '<td>' . $srow['runtime_minutes'] . '</td>';
                        echo '<td>' . $srow['release_date'] . '</td>';
                        echo '<td>' . $srow['acquire_date'] . '</td>';
                        echo '<td>' . $srow['avg_rating'] . '</td>';
                        echo '<td>' . $srow['mpaa_rating'] . '</td>';
                    }
                    elseif (isset($_SESSION['select_customers'])) {
                        echo '<td>' . $srow['C_ID'] . '</td>';
                        echo '<td>' . $srow['S_ID'] . '</td>';
                        echo '<td>' . $srow['name'] . '</td>';
                        echo '<td>' . $srow['email'] . '</td>';
                        echo '<td>' . $srow['subscribe_begin'] . '</td>';
                        echo '<td>' . $srow['subscribe_end'] . '</td>';
                    }
                    echo '</tr>';
                }
                echo '</table>';
                unset($_SESSION['select_movies']); //manually clear!
                unset($_SESSION['select_customers']);
            } //end if select
            //Show # of rows affected/returned:
            else {
                echo '<h4>';
                if (isset($_SESSION['insert_movies'])){
                    echo $_SESSION['insert_movies'];
                    echo ' movies were inserted.';
                    unset($_SESSION['insert_movies']);//manually clear!
                }
                elseif (isset($_SESSION['insert_customers'])){
                    echo $_SESSION['insert_customers'];
                    echo ' customers were inserted.';
                    unset($_SESSION['insert_customers']);//manually clear!
                }
                elseif (isset($_SESSION['update_movies'])){
                    echo $_SESSION['update_movies'];
                    echo ' movies were updated.';
                    unset($_SESSION['update_movies']);//manually clear!
                }
                elseif (isset($_SESSION['update_customers'])){
                    echo $_SESSION['update_customers'];
                    echo ' customers were updated.';
                    unset($_SESSION['update_customers']);//manually clear!
                }
                elseif (isset($_SESSION['refill_customers'])){
                    echo $_SESSION['refill_customers'];
                    echo " customer's subscriptions were refilled (30-days).";
                    unset($_SESSION['refill_customers']);//manually clear!
                }
                elseif (isset($_SESSION['delete_movies'])){
                    echo $_SESSION['delete_movies'];
                    echo ' movies were deleted.';
                    unset($_SESSION['delete_movies']);//manually clear!
                }
                elseif (isset($_SESSION['delete_customers'])){
                    echo $_SESSION['delete_customers'];
                    echo ' customers were deleted.';
                    unset($_SESSION['delete_customers']);//manually clear!
                }
                echo '</h4>';
            }
            ?>
        </fieldset><br>
    <br><br>
<?php include './includes/footer.php'; ?>