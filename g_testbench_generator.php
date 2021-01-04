<?php
include('constants.php');
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
/*
$path = 'C:/xampp/htdocs/myproject/test/';
$files_arr = array();
$filename = "newtest.txt";

$filepath = $path.$filename;
array_push($files_arr,$filepath);
*/

foreach ($files_arr as $file_name){
	
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
	
	echo "<br>";
	if($entitys_name != ""){
		echo "Entity :".$entitys_name."</br>";
	}else{
		echo "Entity block not found! Fix your code and try again!<br>";
		break;
	}
	if(empty($temp_port_array)){
		echo "Port block not found! Fix your code and try again!<br>";
		break;
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
	$temp_file_name = explode(".", $file_name);
	$testbenchfilename = $temp_file_name[0]."_g_test_bench.vhd";	

	$tbfile = fopen($testbenchfilename, "w") or exit("Unable to create file!");
	$output = array();
	$k = 0;
	
	for($i=0; $i<count($port_input); $i++){
		$portmap_input[$i] = strtok($port_input[$i], ":");
		if((stripos($portmap_input[$i], 'clock') !== FALSE)||(stripos($portmap_input[$i], 'clk') !== FALSE)){
			$clock_input = $portmap_input[$i];
		}
		if((stripos($portmap_input[$i], 'reset') !== FALSE)||(stripos($portmap_input[$i], 'rst') !== FALSE)){
			$reset_input = $portmap_input[$i];
		}
	}
	
	if($clock_input != ""){
		echo "Clock :".$clock_input."</br>";
	}else{
		echo "Could not guess clock!<br>";
	}
	if($reset_input != ""){
		echo "Reset :".$reset_input."</br>";
	}else{
		echo "Could not guess reset!<br>";
	}
	
	
	$output[$k++] = "library ieee;".PHP_EOL."use ieee.std_logic_1164.all;".PHP_EOL;
	$output[$k++] = "entirty tb_".$entitys_name." is".PHP_EOL."end tb_".$entitys_name.";".PHP_EOL;
	$output[$k++] = "architecture tb of tb_".$entitys_name." is".PHP_EOL;
	$output[$k++] = "component ".$entitys_name;
	$output[$k++] = "\tport (";
	for($i=0; $i<count($port_input); $i++){
		$k++;
		$output[$k] = "\t\t".$port_input[$i].";";
	}
	$output[$k++] = "\t\t".$port_input[count($port_input)-1];
	$output[$k++] = "\t);";
	$output[$k++] = "end component;".PHP_EOL;
	for($i=0; $i<count($port_input); $i++){
		$output[$k] = "\tsignal ".$port_input[$i].";";
		$k++;
	}
	$output[$k++] = PHP_EOL."\tconstant TbPeriod : time := 100 ns; -- EDIT Put right period here";
	$output[$k++] = "\tsignal TbClock : std_logic := '0';";
	$output[$k++] = "\tsignal TbSimEnded : std_logic := '0';".PHP_EOL;
	$output[$k++] = "begin".PHP_EOL;
	$output[$k++] = "\t dut : ".$entitys_name;
	$output[$k++] = "\t port map ( ";
	for($i=0; $i<count($port_input); $i++){
		$k++;
		$output[$k] = "\t\t".$portmap_input[$i]."=>".$portmap_input[$i].",";
	}
	$output[$k++] = "\t\t".$portmap_input[count($port_input)-1]."=>".$portmap_input[count($port_input)-1];
	$output[$k++] = "\t);".PHP_EOL;
	if($clock_input != ""){
		$output[$k++] = "\t-- Clock generation".PHP_EOL."\tTbClock <= not TbClock after TbPeriod/2 when TbSimEnded /= '1' else '0';".PHP_EOL;
		$output[$k++] = "\t-- EDIT: Check that Clock is really your main clock signal";
		$output[$k++] = "\t".$clock_input." <= TbClock;".PHP_EOL;
	}
	$output[$k++] = "\t stimulus : process";
	$output[$k++] = "\t begin";
	$output[$k++] = "\t\t-- EDIT Adapt initialization as needed";
	for($i=0; $i<count($port_input); $i++){
		if(($portmap_input[$i] != $clock_input)&&($portmap_input[$i] != $reset_input)){
			if(strpos($port_input[$i], '(') !== FALSE){
				$output[$k] = "\t\t".$portmap_input[$i]." <= (others => '0');";
			}else{
				$output[$k] = "\t\t".$portmap_input[$i]." <= '0';";
			}
			$k++;
		}
	}
	if($reset_input != ""){
		$output[$k++] = PHP_EOL."\t\t-- Reset generation";
		$output[$k++] = "\t\t-- EDIT: Check that Reset is really your reset signal";
		$output[$k++] = "\t\t".$reset_input." <= '1';";
		$output[$k++] = "\t\twait for 100 ns;";
		$output[$k++] = "\t\t".$reset_input." <= '0';";
		$output[$k++] = "\t\twait for 100 ns;".PHP_EOL;
	}
	$output[$k++] = "\t\t-- EDIT Add stimulus here".PHP_EOL."\t\twait for 100 * TbPeriod;".PHP_EOL;
	$output[$k++] = "\t\t-- Stop the clock and hence terminate the simulation";
	$output[$k++] = "\t\tTbSimEnded <= '1';";
	$output[$k++] = "\t\twait;";
	$output[$k++] = "\tend process;".PHP_EOL;
	$output[$k++] = "end tb;".PHP_EOL;
	$output[$k++] = "-- Configuration block below is required by some simulators. Usually no need to edit.".PHP_EOL;
	$output[$k++] = "configuration cfg_tb_".$entitys_name." of tb_".$entitys_name." is";
	$output[$k++] = "\tfor tb".PHP_EOL."\tend for;";
	$output[$k++] = "end cfg_tb_".$entitys_name.";";
	
	foreach($output as $op){
		//file_put_contents($testbenchfilename, $op, FILE_APPEND);
		fwrite($tbfile, $op.PHP_EOL);
	}
	unlink($newfilename);
	unset($port_input);
	unset($portmap_input);
	unset($generic_input);
	
	echo '<pre>',print_r($output),'</pre>';
	//echo '<pre>',print_r($portmap_input),'</pre>';
	//echo '<pre>',print_r($temp_port_array),'</pre>';
	//echo '<pre>',print_r($port_input),'</pre>';

	$db_host = DBHOST;
	$db_name = DBNAME;
	$db_user = DBUSER;
	$db_pass = DBPASS;

	$file_name_for_db = (get_string_between($testbenchfilename, $path, ".vhd")).".vhd";
		
	$sql = "INSERT INTO files(filename, dir, username)VALUES('{$file_name_for_db}', '{$testbenchfilename}', '{$username}');";
	try{
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		#echo "connected successfully";
		$conn->exec($sql);
		
	}catch(PDOException $e){
		echo "Connection failed: ".$e->getMessage();
	}
	echo "Processing finished successfully...<br>";
	
	//echo "<br><a href='.$newfile.'>Click here to download</a>&nbsp;&nbsp;&nbsp;";
	echo "<br><a href='javascript:window.location.reload(true)'>Reload Page</a><br>";
	
	fclose($file);

}

function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}


?>