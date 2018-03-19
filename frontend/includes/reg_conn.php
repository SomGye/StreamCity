<?php //revert to normal HTTP connection
$https = filter_input(INPUT_SERVER, 'HTTPS');
if ($https)
{
	$host = filter_input(INPUT_SERVER, 'HTTP_HOST');
	$uri = filter_input(INPUT_SERVER, 'REQUEST_URI');
	$url = 'http://' . $host . $uri;
	header("Location: " . $url);
	exit();
}?>