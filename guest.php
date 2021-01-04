<?php
include('constants.php');
ob_start();
session_start();

$db_host = DBHOST;
$db_name = DBNAME;
$db_user = DBUSER;
$db_pass = DBPASS;

$username = "guest";
$pass = "guest";

$pass = md5($pass);
$sql = "SELECT * FROM users WHERE username = '{$username}' and pass = '{$pass}'";
try{
	$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
	$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	#echo "connected successfully";
	$stmt = $conn->prepare($sql);
	$stmt->execute();
	$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	

	#print_r($result);
	$_SESSION['message'] = "You are now logged in!";
	$_SESSION['username'] = $username;
	echo $_SESSION['username']." ".$_SESSION['message'];
	header("location:index.php");
}catch(PDOException $e){
	echo "Connection failed: ".$e->getMessage();
}

?>