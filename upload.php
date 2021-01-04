<?php 
include('constants.php');
ob_start();
session_start();
if(!isset($_SESSION['username'])){header("Location:login.php");}else{ $username = $_SESSION['username'];}
	
if((isset($_FILES['files']))&&(isset($_POST['upload']))){
	
	$myfile = $_FILES['files'];
	$filecount = count($myfile["name"]);
	$uploaded = array();
	$allowed = array('txt','vhd','vhdl');
	
	for($i=0; $i<$filecount; $i++){
		
		$name = $myfile['name'][$i];
		$type = $myfile['type'][$i];
		$size = $myfile['size'][$i];
		$tmp_name = $myfile['tmp_name'][$i];
		$error = $myfile['error'][$i];
		
		$format = explode('.',$name);
		$format = strtolower(end($format));
		
		if(in_array($format, $allowed)){
			if($error === 0){
				if($size <= 2097152){
					
					#$newname = $name.uniqid('',FALSE).".".$format;
					if(!file_exists('uploads/'.$username.'/')){
						mkdir('uploads/'.$username.'/', 0777, TRUE);
					}
					$destination = PATH.'uploads/'.$username.'/'.$name;
					
					if(move_uploaded_file($tmp_name,$destination)){
						
						$db_host = DBHOST;
						$db_name = DBNAME;
						$db_user = DBUSER;
						$db_pass = DBPASS;
						
						$uploaded[$i] = $destination;
						$sql = "INSERT INTO files(filename, dir, username)VALUES('{$name}', '{$destination}', '{$username}');";
						try{
							$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
							$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
							#echo "connected successfully";
							$conn->exec($sql);
							$_SESSION['message'] = "You have uploaded your files!";
							
							#header("location:index.php");
							echo "<script>opener.location.reload();window.close();</script>";
							
						}catch(PDOException $e){
							echo "Connection failed: ".$e->getMessage();
						}
						echo "Upload of {$name} succed!<br>";
					}else{
						echo "Upload failed! File {$name} failed to upload!<br>";
					}
				}else{
					echo "Upload failed! File {$name} is too big!<br>";
				}
			}else{
				echo "Upload failed! There was an error uploading {$name} file!<br>";
			}
		}else{
			echo "Upload failed! File {$name} has an incompatible type!<br>";
		}
	}
	if(!empty($uploaded)){
		print_r($uploaded);
	}
}
?>
	
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="icon" href="xoricon.png">
		<link rel="stylesheet" type="text/css" href="reglog.css">
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7/jquery.js"></script> 
		<script src="http://malsup.github.com/jquery.form.js"></script>
		<title>File upload</title>
	</head>
	<body>

		<img class="logo__main" src="logocircle.png" height="90" width="90">
		<h2 class="user__name">&nbsp;&nbsp;Hello <?php echo $username; ?> !</h2>
		<!--<a href="logout.php">Log out</a>-->
		<br><br><br><br><br><br>
		<form method="POST" enctype="multipart/form-data" id="certform">
			<input type="file" name="files[]" required multiple>
			<input type="submit" name="upload" value="Upload" onclick="closeSelf();"><br>
		</form>
		<!--<a href="main.php">< Back</a>-->
		
		<script>
		    
			$(document).ready(function () {
				$('#certform').ajaxForm(function () {
					window.opener.location.reload(false);
					window.close();
				});
			});
			
		</script>
		
	</body>
</html>