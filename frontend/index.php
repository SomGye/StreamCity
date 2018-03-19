<?php require_once './includes/reg_conn.php';
//Manually clear session
session_start();
$_SESSION=array();
session_destroy();
require './includes/header.php'; ?>
    <!-- Team Super 7 -->
	<h5> Welcome to StreamCity! </h5>
	<br>
	<section style="text-align: center; color: white; text-shadow: 0 1px 2px black;">
		<h3><em> Please sign in or create an account to view our selection!</em><br><br>
		We have movies you actually want, such as... <br>
		Bad Boys, Alien, Star Wars, Pitch Perfect, and <b>MINIONS</b>!</h3>
	</section>
	<?php include './includes/footer.php'; ?>