<?php
include('constants.php');
session_start();
if(!isset($_SESSION['username'])){header("Location:login.php");}else{ $username = $_SESSION['username'];}
$path = PATH.'uploads/'.$username.'/';

$files_arr = array();
if(isset($_POST['files'])){
	if($username == "guest"){
		foreach($_POST['files'] as $filename) {
			if(($filename != "and_gate.vhd")&&($filename != "comb_ckt_with_unused_signals.vhd")&&($filename != "or_gate_bad_code_sructure.vhd")){
				$filepath = $path.$filename;
				array_push($files_arr,$filepath);
			}
		}
	}else{
		foreach($_POST['files'] as $filename) {
			$filepath = $path.$filename;
			array_push($files_arr,$filepath);
		}
	}
}	

foreach ($files_arr as $file_name){
	if(!unlink($file_name)){
		echo "Error while deleting your file";
	}else{
		$db_host = DBHOST;
		$db_name = DBNAME;
		$db_user = DBUSER;
		$db_pass = DBPASS;
		
		$sql = "DELETE FROM files WHERE dir = '{$file_name}' AND username = '{$username}';";
		try{
			$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			$conn->exec($sql);
			$_SESSION['message'] = "You have deleted your files!";
			#header('Location: '.$_SERVER['PHP_SELF']);
			#header("location:main.php");
		}catch(PDOException $e){
			echo "Update database failed: ".$e->getMessage();
		}
	}
}
?>