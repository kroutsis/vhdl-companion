<?php
session_start();
if(isset($_SESSION['username']) && ($_SESSION['username'] == "guest")){
	include('constants.php');
	$db_host = DBHOST;
	$db_name = DBNAME;
	$db_user = DBUSER;
	$db_pass = DBPASS;
	
	$username = "guest";
	$path = PATH.'uploads/'.$username.'/';
	
	$files = array_diff(scandir($path), array('.', '..'));
	foreach($files as $file_name){
		if(($file_name != "and_gate.vhd")&&($file_name != "comb_ckt_with_unused_signals.vhd")&&($file_name != "or_gate_bad_code_sructure.vhd")){
			unlink($path.$file_name);
		}
	}
	/*
	$sql = "DELETE FROM users WHERE username = '{$username}'";
	try{
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		#echo "connected successfully";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
	}catch(PDOException $e){
		echo "Update database failed: ".$e->getMessage();
	}
	*/
}

session_unset();
session_destroy();
header("location:login.php");

exit();

?>
