<?php
$db = new mysqli('localhost', 'root', 'QyPdsATN8eIu99gcTb81vS', 'gaig_users');

if (mysqli_connect_errno()) {
    echo 'Error: Could not connect to the database..';
    exit;
}

$ip = $_SERVER['REMOTE_ADDR'];
$host = gethostbyaddr($_SERVER['REMOTE_ADDR']);
$reqpath = $_SERVER['REQUEST_URI'];
$browseragent = $_SERVER['HTTP_USER_AGENT'];
$date = date("Y-m-d");
$time = date("H:i:s");
$sql = "INSERT INTO gaig_users.email_tracking (id,ip,host,
  	browser_agent,req_path,access_date,access_time) VALUES 
  	(null,'$ip','$host','$browseragent','$reqpath','$date',
  	'$time');";
$result = $db->query($sql);

$result->free();
$db->close();

header( 'Content-type: image/jpg' );
?>