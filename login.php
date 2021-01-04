<?php
include('constants.php');
ob_start();
session_start();

if(isset($_POST['submit'])){
	
	$db_host = DBHOST;
	$db_name = DBNAME;
	$db_user = DBUSER;
	$db_pass = DBPASS;
	
	$username = $_POST['username'];
	$pass = $_POST['pass'];

    $pass = md5($pass);
	$sql = "SELECT * FROM users WHERE username = '{$username}' and pass = '{$pass}'";
	try{
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		#echo "connected successfully";
		$stmt = $conn->prepare($sql);
		$stmt->execute();
		$result = $stmt->fetchAll(PDO::FETCH_ASSOC);
		
		if(empty($result)){
			echo "<h4 style='color: red'>Incorrect username or password. Try again!</h4>";
		}else{
			#print_r($result);
			$_SESSION['message'] = "You are now logged in!";
			$_SESSION['username'] = $username;
			echo $_SESSION['username']." ".$_SESSION['message'];
			header("location:index.php");
		}
	}catch(PDOException $e){
		echo "Connection failed: ".$e->getMessage();
	}
}
?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="icon" href="xoricon.png">
		<link rel="stylesheet" type="text/css" href="reglog.css">
		<title>Login Page</title>
	</head>
	<body>
		
		<div class="user">
			<header class="user__header">
				<img src="logocircle.png" height="160" width="160">
				<h2 class="user__title">Login</h2>
			</header>
			<form class="form" method="POST" enctype="multipart/form-data">

				<input class="form__input" placeholder="Username" type="text" name="username" required><br>			
				<input class="form__input" placeholder="Password" type="password" name="pass" required><br>				
				<input class="btn" type="submit" name="submit" value="GO"><br>
				<a href="guest.php">Enter as Guest user!</a><br>
				<a href="register.php">Create account!</a><br>

			</form>
		</div>
	</body>
</html>