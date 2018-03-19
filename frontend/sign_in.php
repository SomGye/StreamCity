<?php require_once './includes/secure_conn.php';
session_start();
require './includes/header.php';
    //Author: Maxwell Crawford
	$missing = array(); //for new users
	$errors = array();
	$missing_old = array(); //for old/returning users
	$errors_old = array();

	//Submission check for NEW users:
	if (isset($_POST['newuser'])) {
		//Names, need to trim
		if (empty(trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING))))
			$missing[] = 'name';
		else $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));

		//Email, need to trim and check filter return value (if false)
		if (empty(trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL))))
			$missing[] = 'email';
		if (filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL) == false) 
			$errors[] = 'email';
		else $email = trim(filter_input(INPUT_POST, 'email', FILTER_VALIDATE_EMAIL));

		//Server location
        if (empty(trim(filter_input(INPUT_POST, 'serverloc', FILTER_DEFAULT))))
            $missing[] = 'serverloc';
        else $serverloc = trim(filter_input(INPUT_POST, 'serverloc', FILTER_DEFAULT));

		//No errors/missing
		if ((empty($missing)) && (empty($errors)))
		{
            // Check if email is taken/unique:
            //Set vars for validity flags:
            $email_valid = true;
            //Check DB for user and password
            try {
                require_once ('../pdo_config.php'); // Connect to the db.

                //Select stmt to get user table
                $sql = "SELECT email FROM f_customers";
                foreach($conn->query($sql) as $row){
                    //NOTE: If email is found -> not unique/already taken!
                    if (strcmp($row['email'], $email) == 0){
                        $email_valid = false;
                    }
                }
            } catch (PDOException $e) {
                echo $e->getMessage(); //for development only
                /*for deployment :
                echo '<main><h2>We are sorry but we were unable to process your<br> request at this time.</h2></main>'; */
            }

            //If invalid, add to errors list
            if ($email_valid == false)
                $errors[] = 'email';

			//Insert data into DB if new user AND email isn't taken!
            if ($email_valid == true)
            {
                //Display confirmation form:
                ?><main>
                    <h3>Thank you for registering!</h3>
                    <h4>We have received the following information:</h4>
                    <p>Name: <?php echo "$name"; ?></p>
                    <p>Email: <?php echo $email; ?></p>
                    <p style="text-align: center"><em>Page will be redirected in 2 seconds...</em></p>
                </main>
            <?php
              try {
                    //Prepared stmt:
                   $stmt = $conn->prepare("INSERT INTO f_customers (S_ID, name, email, subscribe_begin, subscribe_end)
                            VALUES (:S_ID, :name, :email, CURDATE(), ADDDATE(CURDATE(), 30))"); //sub dates are builtin
                   $stmt->bindValue(':S_ID', $serverloc);
                   $stmt->bindValue(':name', $name);
                   $stmt->bindValue(':email', $email);
                   $stmt->execute();
                } catch (PDOException $e) {
                    echo $e->getMessage(); //for development only
                    /*for deployment :
                    echo '<main><h2>We are sorry but we were unable to process your<br> request at this time.</h2></main>'; */
                } //end DB try

                //All done
                $usertokenarr = explode("@", $email, 2);
                $usertoken = $usertokenarr[0];
                $_SESSION['usertoken'] = $usertoken;
                $_SESSION['email'] = $email;
                include './includes/footer.php';
				//Redirect to home
                $url = 'index_signedin.php';
                echo '<meta http-equiv="refresh" content="2;url='. $url .'">';
                exit();
            }
            
		} //end error/missing check
	} //end submission check (new user)

    //Submission check for OLD/returning users:
    if (isset($_POST['olduser'])) {
        //Email, need to trim
        if (empty(trim(filter_input(INPUT_POST, 'email2', FILTER_VALIDATE_EMAIL))))
            $missing_old[] = 'email2';
        if (filter_input(INPUT_POST, 'email2', FILTER_VALIDATE_EMAIL) == false)
            $errors_old[] = 'email2';
        else $email_old = trim(filter_input(INPUT_POST, 'email2', FILTER_VALIDATE_EMAIL));

        //No errors/missing
        if (empty($missing_old)) {
            //Set vars for validity flags:
            $email_valid = false;
            $email_admin = false;
            $sub_valid = false;

            //Check email for admin string:
            if (strcmp('superduperuser01@hotmail.com', $email_old) == 0){
                $email_valid = true; //set all flags!
                $email_admin = true;
                $sub_valid = true;
            }
            //Check DB for user and password
            if ($email_valid == false){
                try {
                    require_once ('../pdo_config.php'); // Connect to the db.

                    //Select stmt to get user table
                    $sql = "SELECT email FROM f_customers";
                    foreach($conn->query($sql) as $row){
                        //NOTE: Need to match username AND password on same row!!
                        if (strcmp($row['email'], $email_old) == 0){
                            $email_valid = true;
                        }
                    }
                } catch (PDOException $e) {
                    echo $e->getMessage(); //for development only
                    /*for deployment :
                    echo '<main><h2>We are sorry but we were unable to process your<br> request at this time.</h2></main>'; */
                }
            } //end try

            //If invalid, add to errors list
            if ($email_valid == false)
                $errors_old[] = 'email2';

            //Display confirmation form for signin IF SUCCESSFUL:
            // if not valid -> load orig form with error msgs...
            if ($email_admin == true) {
                ?>
                <main>
                    <h4>You've unlocked the admin menu!</h4><br><br>
                    <p style="text-align: center"><em>Page will be redirected in 2 seconds...</em></p>
                </main>
                <?php
                //All done
                $_SESSION['email'] = $email_old;
                include './includes/footer.php';
                //Redirect to ADMIN MENU
                $url = 'admin_menu.php';
                echo '<meta http-equiv="refresh" content="2;url='. $url .'">'; //$url defined in secure_conn.php
                exit();
            }

            if ($email_valid == true){
                //Grab C_ID by email
                try{
                    $getcidsql = $conn->prepare("SELECT C_ID FROM f_customers WHERE email = :current_email");
                    $getcidsql->bindValue(':current_email', $email_old);
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

                //Check subscription time left!
                try{
                    $checksubsql = $conn->prepare("CALL check_if_low_sub(:current_C_ID, @teststatus)");
                    $checksubsql->bindValue(':current_C_ID', $current_C_ID);
                    $checksubsql->execute();
                }
                catch (PDOException $e) {
                    echo $e->getMessage();
                    exit();
                }

                //Get result of sub stored proc. from basic SELECT
                try{
                    $subselsql = "SELECT @teststatus AS 'substatus'";
                    foreach($conn->query($subselsql) as $srow){
                        if (strcmp($srow['substatus'], 'OKAY') == 0){
                            $sub_valid = true; //flag sub status
                            $sub_message = 'OKAY';
                        }
                        elseif (strcmp($srow['substatus'], 'LOW') == 0){
                            $sub_valid = true; //flag sub status
                            $sub_message = 'LOW';
                        }
                        elseif (strcmp($srow['substatus'], 'NONE') == 0){
                            $sub_valid = false; //flag sub status
                            $sub_message = 'NONE';
                        }
                    }
                }
                catch (PDOException $e) {
                    echo $e->getMessage();
                    exit();
                }

            } //end sub check

            //If subscription is still valid, sign in and go home!
            if (($email_valid == true) AND ($sub_valid == true)) {
                ?>
                <main>
                <h3>Successfully signed in!</h3>
                <h4>You are signed in as:</h4>
                <p>Email: <?php echo "$email_old"; ?></p>
                <p>Subscription Status: <?php echo "$sub_message"?></p><br>
                <p style="text-align: center"><em>Page will be redirected in 2 seconds...</em></p>
                </main>
                <?php
                //All done
                $usertokenarr = explode("@", $email_old, 2);
                $usertoken = $usertokenarr[0];
                $_SESSION['usertoken'] = $usertoken;
                $_SESSION['email'] = $email_old;
                include './includes/footer.php';
				//Redirect to home
                $url = 'index_signedin.php';
                echo '<meta http-equiv="refresh" content="2;url='. $url .'">';
                exit();
            } //end login error check

            //If subscription is NONE
            elseif (($email_valid == true) AND ($sub_valid != true)) {
                ?>
                <main>
                <br>
                <p>Email: <?php echo "$email_old"; ?></p>
                <p>Subscription Status: <?php echo "$sub_message"?></p><br>
                <p style="text-align: center"><em>Redirecting to subscription refill page in 2 seconds...</em></p>
                </main>
                <?php
                //All done
                $usertokenarr = explode("@", $email_old, 2);
                $usertoken = $usertokenarr[0];
                $_SESSION['usertoken'] = $usertoken;
                $_SESSION['email'] = $email_old;
                $_SESSION['substatus'] = 'NONE'; //pass to resubscribe
                include './includes/footer.php';
				//Redirect to resubscribe (then later to home)
                $url = 'resubscribe.php';
                echo '<meta http-equiv="refresh" content="2;url='. $url .'">';
                exit();
            } //end NONE status check
        } //end error/missing check2
    } //end submission check (old user)
?>

<main>
    <br><br>
    <!-- ORIGINAL FORM 1: REGISTRATION/NEW USER-->
    <fieldset>
        <legend><h4>Please register or sign in to continue</h4></legend>
        <div class="formleft">
            <form method="POST" action="sign_in.php">
                <h4>Registration (New Users):</h4>
                <!-- First and last name-->
                <p>
                <label for="name">Full Name:
                <?php if ($missing && in_array('name', $missing)) { ?>
                    <br><span class="warning"> Please enter your full name</span>
                <?php } ?></label><br>
                <input type="text" name="name" id="name"
                <?php if (isset($name)) {
                    echo 'value="' . htmlspecialchars($name) . '"';
                } ?>
                >
                </p>
                <!-- Email-->
                <p>
                <label for="email">Email:
                <?php if ($errors && in_array('email', $errors)) { ?>
                    <br><span class="warning"> Please enter a valid or unique email</span>
                <?php }
                elseif ($missing && in_array('email', $missing)) { ?>
                    <br><span class="warning"> Please enter an email</span>
                <?php } ?></label><br>
                <input type="text" name="email" id="email"
                <?php if (isset($email)) {
                    echo 'value="' . htmlspecialchars($email) . '"';
                } ?>
                >
                </p>
                <!-- Server Location/Region -->
                <p>
                    <label for="serverloc">Region:
                    <?php if ($missing && in_array('serverloc', $missing)) { ?>
                        <br><span class="warning"> Please enter your Region/Location</span>
                    <?php } ?></label><br>
                    <select name="serverloc" id="serverloc" required
                    <?php if (isset($serverloc)) {
                        echo 'value="' . $serverloc . '"';
                    } ?>
                    >
                        <option value="">Please select your Region...</option>
                        <option value="1">United States - CA</option>
                        <option value="2">United States - NY</option>
                        <option value="3">Canada</option>
                        <option value="4">Mexico</option>
                        <option value="5">United Kingdom - Great Britain</option>
                    </select>
                </p>
                <!-- Form submission btn for REGISTER-->
                <br>
                <input type="submit" name="newuser" value="Register">
            </form>
        </div>
    <!-- ORIGINAL FORM 2: SIGN-IN/OLD USER-->
        <div class="formright">
            <form method="POST" action="sign_in.php">
                <h4>Sign In (Returning Users):</h4>
                <!-- Email -->
                <p>
                    <label for="email2">Current Email:
                        <?php if ($missing_old && in_array('email2', $missing_old)) { ?>
                            <br><span class="warning"> Please enter an email</span>
                        <?php } ?>
                        <?php if ($errors_old && in_array('email2', $errors_old)) { ?>
                            <br><span class="warning"> Email is not valid!</span>
                        <?php } ?></label><br>
                    <input type="text" name="email2" id="email2"
                        <?php if (isset($email_old)) {
                            echo 'value="' . htmlspecialchars($email_old) . '"';
                        } ?>
                    >
                </p>
                <!-- Form submission btn for SIGNIN-->
                <br>
                <input type="submit" name="olduser" value="Sign In">
            </form>
        </div>
    </fieldset>
</main>
<?php include './includes/footer.php'; ?>