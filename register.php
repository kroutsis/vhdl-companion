<?php 
include('constants.php');
ob_start();
session_start(); 
		
if(isset($_POST['register'])){

	$db_host = DBHOST;
	$db_name = DBNAME;
	$db_user = DBUSER;
	$db_pass = DBPASS;
	
	$username = $_POST['username'];
	$email = $_POST['email'];
	$pass = $_POST['pass'];
	$pass2 = $_POST['pass2'];
	
	if($pass == $pass2){
		$pass = md5($pass); #encrypt password
		#$query = "SELECT * FROM users WHERE username = '{$username}';";
		$sql = "INSERT INTO users(username, email, pass)VALUES('{$username}', '{$email}', '{$pass}');";
		try{
			$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			#echo "connected successfully";
			$conn->exec($sql);
					
			$_SESSION['message'] = "You are now logged in!";
			$_SESSION['username'] = $username;
			
			header("location:index.php");
	
		}catch(PDOException $e){
			echo "<br>Connection failed: ".$e->getMessage();
		}
	}else{
		$_SESSION['message'] = "<h4 style='color: red'>The two passwords do not match.Try again!</h4>";
		echo $_SESSION['message'];
	}
}
?>

<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="icon" href="xoricon.png">
		<link rel="stylesheet" type="text/css" href="reglog.css">
		<title>Register Page</title>
	</head>
	<body>
		<div class="user">
			<header class="user__header">
				<img src="logocircle.png" height="160" width="160">
				<h2 class="user__title">Register</h2>
			</header>
			<form class="form" method="POST" enctype="multipart/form-data">
				
				<input class="form__input" placeholder="Username" type="text" name="username" required><br>
				<input class="form__input" placeholder="Email" type="email" name="email" required><br>
				<input class="form__input" placeholder="Password" type="password" name="pass" required><br>
				<input class="form__input" placeholder="Re-type Password" type="password" name="pass2" required><br>
				<input class="btn" type="submit" name="register" value="Register"><br>
				Already have account? <a href="login.php">Login</a>


			</form>
			
		</div>
		
	</body>
</html>