<?php
include('constants.php');
session_start();
if(!isset($_SESSION['username'])){header("Location:login.php");}else{ $username = $_SESSION['username'];}
$path = PATH.'uploads/'.$username.'/';
error_reporting(0);
$files_arr = array();
if(isset($_POST['files'])){
	foreach($_POST['files'] as $filename) {
		$filepath = $path.$filename;
		array_push($files_arr,$filepath);
	}
}
$file_name = $files_arr[0];
$port_input = array();
$portmap_input = array();
$generic_input = array();

$entitys_name = "";
$clock_input = "";
$reset_input = "";

$line_num = 0;
$i = 0;
$j = 0;
echo "Processing...<br>";
$file = fopen($file_name, "r") or exit("Unable to open file!");
$newfilename = $file_name."temp.txt";
$newfile = fopen($newfilename, "w") or exit("Unable to create file!");

$input = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

foreach($input as $line){
	$output = trim(preg_replace('/\t+/', '', $line));
	$output = preg_replace( "/\r|\n/", "", $output);
	if(strlen($output) == 0){
		continue;
	}
	if(strpos($output, '--') !== FALSE){
		$temparr = explode('--', $output);
		$output = $temparr[0];
	}
	fwrite($newfile, $output.PHP_EOL);
}
$input = file($newfilename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
echo "Cleaning code comments...<br>";

foreach($input as $line){
	
	if((strpos($line, 'entity') !== FALSE)&&(strpos($line, 'is') !== FALSE)){
		echo "Entity block found...<br>";
		$entitys_name = rtrim(get_string_between($line, 'entity', 'is'));//entitys name
		$line_num++;
		
		while(strpos($input[$line_num], $entitys_name)=== FALSE){
			//if((strpos($input[$line_num], 'generic') !== FALSE)){
			//	$generic_input[$j] = $input[$line_num];
			//	$line_num++;
			//	$j++;
			//}
			$countpar = 0;
			if(strpos($input[$line_num], 'port') !== FALSE){
				echo "Port block found...<br>";
				$temp_port_array[$i] = $input[$line_num];
				$countpar += substr_count($input[$line_num], '(');
				$countpar += substr_count($input[$line_num], ')');

				if($countpar%2 == 0){break;}
				do{
					$line_num++;
					$i++;
					$temp_port_array[$i] = $input[$line_num];
					$countpar += substr_count($input[$line_num], '(');
					$countpar += substr_count($input[$line_num], ')');
						
				}while($countpar%2 != 0);
				
			}
			$line_num++;			
		}
	}
	$line_num++;
	
}
fclose($newfile);
unlink($newfilename);

	echo "<br>";
	if($entitys_name != ""){
		echo "Entity :".$entitys_name."</br>";
	}else{
		echo "Entity block not found! Fix your code and try again!<br>";
		
	}
	if(empty($temp_port_array)){
		echo "Port block not found! Fix your code and try again!<br>";
		
	}
	
	$i = 0;
	foreach($temp_port_array as $tpa){
		if(strpos($tpa, ':') === FALSE){
			unset($temp_port_array[$i]);
		}
		$i++;
	}	
	$temp_port_array = array_values($temp_port_array);
	
	if(strpos($temp_port_array[0], 'port(') !== FALSE){
		$temp_port_array[0] = substr($temp_port_array[0], 5);
	}else if(strpos($temp_port_array[0], 'port (') !== FALSE){
		$temp_port_array[0] = substr($temp_port_array[0], 6);
	}
	if(strpos($temp_port_array[count($temp_port_array)-1], ');') !== FALSE){
		$temp_port_array[count($temp_port_array)-1] = substr($temp_port_array[count($temp_port_array)-1], 0, -2);
	}
	$i = 0;
	$j = 0;
	$k = 0;
	foreach($temp_port_array as $tpa){
		$temp_port_array[$i] = explode(';', $tpa);
		$i++;
	}

	for($i=0; $i<count($temp_port_array); $i++){
		foreach($temp_port_array[$i] as $tpa){
			if(($tpa != '')&&($tpa != ' ')){
				$temp_port_array_2[$k] = $tpa;
				$k++;
			}
		}
	}

	$i = 0;
	$k = 0;
	foreach($temp_port_array_2 as $tpa){
		$temp_port_array_2[$i] = explode(':', $tpa);
		$i++;
	}
	for($i=0; $i<count($temp_port_array_2); $i++){
		if(strpos($temp_port_array_2[$i][1], ';') !== FALSE){
			$temp_port_array_2[$i][1] = substr($temp_port_array_2[$i][1], 0, -1);
		}
	}
	for($i=0; $i<count($temp_port_array_2); $i++){
		$temp_port_array_2[$i][0] = explode(',', $temp_port_array_2[$i][0]);
	}
	for($i=0; $i<count($temp_port_array_2); $i++){
		foreach($temp_port_array_2[$i][0] as $tpa){
			$port_input[$k] = $tpa." :".$temp_port_array_2[$i][1];
			$k++;
		}
	}
	
for($i=0; $i<count($temp_port_array_2); $i++){
	echo $port_input[$i]."<br>";
}
$_SESSION['port_input'] = $port_input;
$_SESSION['file_name'] = $file_name;
$_SESSION['entitys_name'] = $entitys_name;
unlink($newfilename);
echo "<script>window.open('c_tb_gen_input.php','popup','width=800,height=400')</script>";



function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}

?>