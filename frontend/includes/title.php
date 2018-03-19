<?php
    //Author: Maxwell Crawford
	$title = 'StreamCity';
	$title = $title . ' - ' . basename($_SERVER['SCRIPT_FILENAME'], '.php');
	$title = str_replace('_', ' ', $title);
	if(strpos($title,'index') !== false){
		$title = 'StreamCity - Home';
		}
	$title = ucwords($title);