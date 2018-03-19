<?php session_start();
include 'title.php';?>
<!DOCTYPE html>
<html lang="en">
<head>
	<!--Author: Team Super 7! -->
	<title><?php if(isset($title)) {echo "$title";} ?></title>
	<meta charset="utf-8">
	<link rel="stylesheet" type="text/css" href="styles/main.css">

	<!-- Favicon/logo code, courtesy of realfavicongenerator.net -->
    <link rel="apple-touch-icon" sizes="76x76" href="./images/apple-touch-icon.png">
    <link rel="icon" type="image/png" sizes="32x32" href="./imagesfavicon-32x32.png">
    <link rel="icon" type="image/png" sizes="16x16" href="./images/favicon-16x16.png">
    <link rel="manifest" href="./images/manifest.json">
    <link rel="mask-icon" href="./images/safari-pinned-tab.svg" color="#5bbad5">
    <meta name="theme-color" content="#ffffff">
</head>
<body>
	<header>
		<span class="banner_h">
            <img class="banner_img" src="images/StreamCity_banner.png" alt="Banner" title="StreamCity Banner" />
            <img class="sm_logo" src="images/favicon-32x32.png" alt="StreamCity!" title="StreamCity!"/>
		</span>
		<?php $currentPage = basename($_SERVER['SCRIPT_FILENAME']);?>
		<?php if (isset($_SESSION['usertoken'])){$currname = $_SESSION['usertoken']; $logouttext = 'Logout: ' . $currname;} elseif (($currentPage == 'admin_menu.php') || ($currentPage == 'admin_message.php')){$currname = 'Leave Admin';} else{$currname = 'Sign In/Register';}?>
        <!-- Grab subscription status if applicable -->
        <?php $current_sub = 'OKAY';?>
        <?php if (isset($_SESSION['substatus'])){$current_sub = $_SESSION['substatus'];}?>
        <!-- NOTE: sign_in.php uses secure_conn.php to ensure HTTPS;-->
        <!--Also, style of Sign In btn dynamically changes with session var-->
        <!--Lastly, if already signed in, logout and go to home -->
        <!-- Search form code start -->
        <?php if (($currentPage == 'index_signedin.php') || ($currentPage == 'search.php') || ($currentPage == 'movie_info.php')){echo '<form method="POST" action="search.php">';} ?>
		<nav>
			<ul>
                <!-- Home button: If user: go to signed-in home; If admin: go to admin_menu; If NONE sub: go to resubscribe; else: go home-->
			  <li><a <?php if (strcmp($current_sub, 'NONE') == 0){echo 'href="resubscribe.php"';} elseif (isset($_SESSION['usertoken'])){echo 'href="index_signedin.php"';} elseif (($currentPage == 'admin_menu.php') || ($currentPage == 'admin_message.php')){echo 'href="admin_menu.php"';} else{echo 'href="index.php"';}?><?php if (($currentPage == 'index.php') || ($currentPage == 'index_signedin.php')) {echo ' id="here"';} ?>><?php if(($currentPage == 'admin_menu.php') || ($currentPage == 'admin_message.php')){echo 'Admin Menu';} else{echo 'Home';}?></a></li>
              <!-- Check for being on index_signedin/search/movie_info -> show search bar and btn in compact form -->
              <?php if (($currentPage == 'index_signedin.php') || ($currentPage == 'search.php') || ($currentPage == 'movie_info.php'))
              {
                  echo '<li><input type="text" class="searchtext" name="searchtext" id="searchtext" placeholder="Search here..."></li>';
                  echo '&nbsp&nbsp&nbsp&nbsp&nbsp&nbsp';
                  echo '<li><input type="submit" class="searchbtn" name="searchsubmit" value="Search!"></li>';
              } ?>
                <!--Code for login/logout and displaying current user on nav btn -->
                <li style="float:right;<?php if (isset($_SESSION['usertoken'])){echo '; font-size: 18px; width: 15%;';}?>"><a class="active" <?php if(isset($_SESSION['usertoken'])){echo 'href="index.php"';} elseif (($currentPage == 'admin_menu.php') || ($currentPage == 'admin_message.php')){echo 'href="index.php"';} else{echo 'href="sign_in.php"';}?><?php if ($currentPage == 'sign_in.php') {echo 'id="here"';} ?>><?php if(isset($_SESSION['usertoken'])){echo "$logouttext";} else{echo "$currname";}?></a></li>
			</ul>
		</nav>
        <!-- Search form code END -->
        <?php if (($currentPage == 'index_signedin.php') || ($currentPage == 'search.php') || ($currentPage == 'movie_info.php')){echo '</form>';} ?>
        <h2>StreamCity! -- <em>Quick -n- Easy Movie Streaming.</em></h2>
	</header>