<?php
include('constants.php');
ob_start();
session_start();
if(!isset($_SESSION['username'])){header("Location:login.php");}else{ $username = $_SESSION['username'];}
$path = PATH.'uploads/'.$username.'/';

$files_arr = array();
if(isset($_POST['files'])){
	foreach($_POST['files'] as $filename) {
		$filepath = $path.$filename;
		array_push($files_arr,$filepath);
	}
}

#$files_arr = array("file1.txt","file2.txt","file3.txt");
$files_with_unused_signals = array();
$file_num = count($files_arr);

foreach ($files_arr as $file_name){
	
	$file = fopen($file_name, "r") or exit("Unable to open file!");

	$delimiter = array(" ","=>","**",":=","/=",">=","<=","<>","&","'","(",")","*","+",",","-",".","/",":",";","<","=",">","|","[","]");
	$signal_array = array();
	$line_num = 0;
	
    while(!feof($file)) {
        $line = fgets($file);
		#echo $line,"<br>" ;
		$line_num ++;
		
		if (strpos($line, 'signal') !== FALSE){
			$signal_name = trim(get_string_between($line, "signal", ":"));
			$signal_name = explode(",",$signal_name);
			foreach ($signal_name as $signal_name_sameline){
				$signal_array[] = array('name' => $signal_name_sameline, 'line_num' => $line_num );
			}
        }
		if (strpos($line, 'begin') !== FALSE){
			while(!feof($file)) {
				$newline = fgets($file);
				#echo $newline,"<br>" ;
				$line_num ++;
				$replace = str_replace($delimiter, $delimiter[0], $newline);
				$explode = explode($delimiter[0], $replace);
				
				foreach($explode as $ex){
					$token_array[] = $ex;
				}
			}
		}
		
    }
    
	$token_array = array_flip($token_array);
	$filtered = array_filter( $signal_array, function( $el) use( $token_array) {
    return isset( $token_array[ $el['name'] ]);
	});
	

	error_reporting(0);
	
	$unused_signals = array_diff_assoc($signal_array,$filtered);
	//print_r($filtered);
	if ($unused_signals != NULL){
		array_push($files_with_unused_signals,$file_name);
		echo "<br>Unused signals found on file:", $file_name, "<br>";
		print_r($unused_signals);
		echo "<br>";
		echo "<br><a href='javascript:window.location.reload(true)'>Reload Page</a><br>";
	} else {
		echo "<br>There are no Unused signals in your code on file:", $file_name, "!<br>";
		echo "<br><a href='javascript:window.location.reload(true)'>Reload Page</a><br>";
	}	
	fclose($file);
	
}
	
/*	
if ($files_with_unused_signals != NULL){
	foreach ($files_with_unused_signals as $file_name){
		$line_num = 0;
		$flag = 0; $flag2 = 1;
		$file = fopen($file_name, "r") or exit("Unable to open file!");
		$newfile = (substr($file_name, 0, -4))."_code_rem.vhd";
		$cor_file = fopen($newfile, "w") or exit("Unable to open file!");
		#chmod($file,777);
		while (!feof($file)){		
			$line = fgets($file);
			$line_num ++;

			
			foreach ($unused_signals as $un){
				if($line_num == $un['line_num']){
					
					if ((strpos($line, ',') !== FALSE) && (strpos((strrchr($line, $un['name'])),',') === FALSE)) {
						if(strpos($line, ','.$un['name']) !== FALSE){
							$templine = str_replace(','.$un['name'],"",$line);
						}else if(strpos($line, $un['name'].',') !== FALSE){
							$templine = str_replace($un['name'].',',"",$line);
						}
						fwrite($cor_file, $templine);
						$flag = 1;
					}else if (strpos($line, ',') !== FALSE){
						fwrite($cor_file,(str_replace($un['name'].',',"",$line)));
						$flag = 1;
					} else {
						
						fwrite($cor_file,(str_replace($line,"",$line)));
						$flag = 1;
					}
					
				}
				
			}
			
			if(($flag == 1)&&($flag2 == 1)){
				foreach ($filtered as $f){
					fwrite($cor_file,"\tsignal ".$f['name'].": std_logic;");
				}
				$flag2 = 0;
			}
			if ($flag == 0){
				fwrite($cor_file, $line);
			} else {
				$flag = 0;
			}

		}
		fclose($file,$cor_file);
		
		$db_host = DBHOST;
		$db_name = DBNAME;
		$db_user = DBUSER;
		$db_pass = DBPASS;
		
		$file_name_for_db = (get_string_between($cor_file, $path, ".vhd")).".vhd";

		$sql = "INSERT INTO files(filename, dir, username)VALUES('{$file_name_for_db}', '{$cor_file}', '{$username}');";
		try{
			$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
			$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			#echo "connected successfully";
			$conn->exec($sql);
			
		}catch(PDOException $e){
			echo "Connection failed: ".$e->getMessage();
		}
		//echo "<br><a href='.$newfile.'>Click here to download</a>&nbsp;&nbsp;&nbsp;";
		//echo "<br><a href='javascript:window.location.reload(true)'>Reload Page</a><br>";
	}
}
*/
function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

?>
