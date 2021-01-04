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

$file_num = count($files_arr);

foreach ($files_arr as $file_name){
	
	$input = array();
	$headers_array = array();
	$architecture_array = array();
	$entity_array = array();
	$output_array = array();

	$line_num = 1;
	$i = 0;
	
	$file = fopen($file_name, "r") or exit("Unable to open file!");
	$newfilename = $file_name."temp.txt";
	$newfile = fopen($newfilename, "w") or exit("Unable to create file!");

	$input = file($file_name, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

	foreach($input as $line){
		$output = trim(preg_replace('/\t+/', '', $line));
		$output = preg_replace( '/\r|\n/', '', $output);
		if(strlen($output) == 0){
			continue;
		}
		if(strpos($output, '--') !== FALSE){
			$temparr = explode('--', $output);
			$output = $temparr[0];
		}
		if((strpos($output, ';') !== FALSE)&&(substr_count($output, ';') > 1)){
			
			$temparr = explode(';', $output);
			foreach($temparr as $ta){
				if($ta != ""){
					fwrite($newfile, $ta.";".PHP_EOL);
				}
			}
			continue;
		}
		fwrite($newfile, $output.PHP_EOL);
	}
	echo "Cleaning code comments and empty lines...<br>";
	
	$temp_file_name = explode(".", $file_name);
	$corfilename = $temp_file_name[0]."_restructured.vhd";
	$newfile = fopen($corfilename, "w") or exit("Unable to create file!");
	$input = file($newfilename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	//$lword = array("then", "else");
	$caseflag = 0;
	$lwflag = 0;
	
	foreach($input as $line){
		$output = $line;
		
		
		if(strpos($output, "else") !== FALSE){
				//if(substr($output, -4) != $lw){
			$temparr = explode("else", $output);

			if(count($temparr)>2){
				for($i=0; $i<count($temparr); $i++){
					if(substr($temparr[$i], -1) == ';'){
						fwrite($newfile, $temparr[$i].PHP_EOL);
						$lwflag = 1;
					}else{
						fwrite($newfile, $temparr[$i]."else".PHP_EOL);
						$lwflag = 1;
					}
					
				}
			}else if($temparr[1] != ""){
				break;
			}
		}
		
		
		if(strpos($output, 'case') !== FALSE){$caseflag = 1;}
		if(($caseflag == 1)&&(strpos($output, 'when') !== FALSE)&&(strpos($output, ';') !== FALSE)){
			$temparr = explode('=>', $output);
			foreach($temparr as $ta){
				if(strpos($ta, 'when') !== FALSE){
					fwrite($newfile, $ta."=>".PHP_EOL);
				}else{
					fwrite($newfile, $ta.PHP_EOL);
				}
			}
			continue;
		}
		if(strpos($output, 'end case') !== FALSE){$caseflag = 0;}
		if($lwflag == 0){
			fwrite($newfile, $output.PHP_EOL);
		}else{
			$lwflag = 0;
		}
		
	}
	
	fclose($newfile);
	//fclose($file);
	unlink($newfilename);
	$input = file($corfilename, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
	
	//echo '<pre>',print_r($input),'</pre>';
	
	foreach($input as $line){
		
		if((strpos($line, 'library') !== FALSE)||(strpos($line, 'use') !== FALSE)){
			$headers_array[$i] = $line;
			$i++;
		}

		if((strpos($line, 'entity') !== FALSE)&&(strpos($line, 'is') !== FALSE)){
			
			entity_func($input,$line_num,$i,$line,0);
		}
		
		if((strpos($line, 'architecture') !== FALSE)&&(strpos($line, 'of') !== FALSE)&&(strpos($line, 'is') !== FALSE)){
			
			architecture_func($input,$line_num,$i,$line);
		}
		$line_num++;
	}
	
	$k = 0;
	$sep_comment = str_repeat("-", 80);
	foreach($headers_array as $ha){
		$output_array[$k++] = $ha;
	}
	$output_array[$k++] = $sep_comment;
	if(!empty($entity_array)){
		for($i=0; $i<count($entity_array); $i++){
			if((is_string($entity_array[$i]))&&($i % 3 == 0)){
				$output_array[$k++] = "entity".$entity_array[$i]." is";
				$end = $i;
			}else if((is_array($entity_array[$i]))&&(!empty($entity_array[$i]))&&($i % 3 == 1)){
				$output_array[$k++] = "generic(";
				for($j=0; $j<count($entity_array[$i])-1; $j++){
					$output_array[$k++] = "\t".$entity_array[$i][$j].";";
				}
				$output_array[$k++] = "\t".$entity_array[$i][$j++];
				$output_array[$k++] = ");";
			}else if((is_array($entity_array[$i]))&&(!empty($entity_array[$i]))&&($i % 3 == 2)){
				$output_array[$k++] = "port(";
				for($j=0; $j<count($entity_array[$i])-1; $j++){
					$output_array[$k++] = "\t".$entity_array[$i][$j].";";
				}
				$output_array[$k++] = "\t".$entity_array[$i][$j++];
				$output_array[$k++] = ");";
				$output_array[$k++] = "end".$entity_array[$end].";";
			}

		}
		
	}
	$output_array[$k++] = $sep_comment;
	if(!empty($architecture_array)){
		for($i=0; $i<count($architecture_array); $i++){
			if((is_string($architecture_array[$i]))&&($i % 5 == 0)){
				$output_array[$k++] = "architecture".$architecture_array[$i]." is";
				$end = $i;
			}else if((is_array($architecture_array[$i]))&&(!empty($architecture_array[$i]))&&($i % 5 == 1)){
				for($j=0; $j<count($architecture_array[$i]); $j++){
					$output_array[$k++] = "\t".$architecture_array[$i][$j];
				}
				$output_array[$k++] = "begin";
			}else if((is_string($architecture_array[$i]))&&($i % 5 == 2)){
				$output_array[$k++] = "\t".$architecture_array[$i];
			}else if((is_array($architecture_array[$i]))&&(!empty($architecture_array[$i]))&&($i % 5 == 3)){
				for($j=0; $j<count($architecture_array[$i]); $j++){
					$output_array[$k++] = "\t\t".$architecture_array[$i][$j];
				}
			}else if((is_array($architecture_array[$i]))&&(!empty($architecture_array[$i]))&&($i % 5 == 4)){
				for($j=0; $j<count($architecture_array[$i]); $j++){
					$output_array[$k++] = "\t".$architecture_array[$i][$j];
				}
				$archnametmp = explode(" ", $architecture_array[$end]);
				$output_array[$k++] = "end ".$archnametmp[1].";";
			}
		}
	}
	$output_array[$k++] = $sep_comment;
	echo '<pre>',print_r($output_array),'</pre>';
	$newfile = fopen($corfilename, "w") or exit("Unable to create file!");
	foreach($output_array as $oa){
		fwrite($newfile, $oa.PHP_EOL);
	}
	
	unset($headers_array);
	unset($entity_array);
	unset($architecture_array);
	unset($output_array);
	unset($input);
	fclose($newfile);
	//unlink($corfilename);
	//echo '<pre>',print_r($entity_array),'</pre>';
	//echo '<pre>',print_r($architecture_array),'</pre>';
	
	$db_host = DBHOST;
	$db_name = DBNAME;
	$db_user = DBUSER;
	$db_pass = DBPASS;
	
	$file_name_for_db = (get_string_between($corfilename, $path, ".vhd")).".vhd";
		
	$sql = "INSERT INTO files(filename, dir, username)VALUES('{$file_name_for_db}', '{$corfilename}', '{$username}');";
	try{
		$conn = new PDO("mysql:host=$db_host;dbname=$db_name", "$db_user", "$db_pass");
		$conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		#echo "connected successfully";
		$conn->exec($sql);
			
	}catch(PDOException $e){
		echo "Connection failed: ".$e->getMessage();
	}
	
	//echo "<br><a href='.$newfile.'>Click here to download</a>&nbsp;&nbsp;&nbsp;";
	echo "<br><a href='javascript:window.location.reload(true)'>Reload Page</a><br>";
}

function architecture_func($input,$line_num,$i,$line){

	$process_array = array();
	$process_var_array = array();
	$signal_array = array();
	$dupl = array();
	global $architecture_array;
	//global $entity_array;
	//$i = 0;
	$whenflag = 0;
	$tabcount = 0;
	$process_line = "";
	$architecture_line = "";

	
	$architectures_name = rtrim(get_string_between($line, 'architecture', 'of'));
	$architecture_line = rtrim(get_string_between($line, 'architecture', 'is'));
	array_push($architecture_array, $architecture_line);
	echo "Architecture ".$architectures_name." found!<br>";
	$i = 0;
	while(strpos($input[$line_num], 'begin')=== FALSE){//architectures begin //signals and components
		if((strpos($input[$line_num], 'component')!== FALSE)&&(strpos($input[$line_num], 'end')=== FALSE)){
			$temparr = explode(' ', $input[$line_num]);
			$component_name = $temparr[1];
			echo "Component ".$component_name." found!<br>";
			$signal_array[$i] = $input[$line_num];
			$line_num++;
			$i++;
			while(strpos($input[$line_num], 'end component')=== FALSE){
				$signal_array[$i] = "\t".$input[$line_num];
				$line_num++;
				$i++;
			}
			//entity_func($input,$line_num,$i,$input[$line_num],1);
		}
		$signal_array[$i] = $input[$line_num];
		$line_num++;
		$i++;
	}
	$line_num++;
	$i = 0;//$count = 0;
	if(strpos($input[$line_num], 'process')!== FALSE){
		$process_line = $input[$line_num];
		echo $process_line." found!";
		$line_num++;
		while(strpos($input[$line_num], 'begin')=== FALSE){//processess begin //variables
			$process_var_array[$i] = $input[$line_num];
			$line_num++;
			$i++;
		}
	}
	
	$j = 0;
	while(strpos($input[$line_num], 'end'.$architectures_name)=== FALSE){
		
		if(((strpos($input[$line_num], 'if')!== FALSE)&&(strpos($input[$line_num], 'elsif')=== FALSE)&&(strpos($input[$line_num], 'then')!== FALSE))||
		((strpos($input[$line_num], 'for')!== FALSE)&&(strpos($input[$line_num], 'loop')!== FALSE))||
		((strpos($input[$line_num], 'case')!== FALSE)&&(strpos($input[$line_num], 'is')!== FALSE))){
			
			$process_array[$j] = $input[$line_num];
			$line_num++;
			$j++;
			$tabcount++;

			while($tabcount > 0){
				$tab = str_repeat("\t", $tabcount);
				$elsetab = str_repeat("\t", $tabcount-1);
				if((strpos($input[$line_num], 'elsif') !== FALSE)||(strpos($input[$line_num], 'else') !== FALSE)||(strpos($input[$line_num], 'when') !== FALSE)){
					if((strpos($input[$line_num], 'when') !== FALSE)&&(strpos($input[$line_num], 'else') === FALSE)){
						$process_array[$j] = "\t".$elsetab.$input[$line_num];
						$whenflag = 1;
						$line_num++;
						$j++;
					}else{
						$process_array[$j] = $elsetab.$input[$line_num];
						$line_num++;
						$j++;
					}
				}
				if((((strpos($input[$line_num], 'if')!== FALSE)&&(strpos($input[$line_num], 'elsif')=== FALSE)&&(strpos($input[$line_num], 'then')!== FALSE))||
				((strpos($input[$line_num], 'for')!== FALSE)&&(strpos($input[$line_num], 'loop')!== FALSE))||
				((strpos($input[$line_num], 'case')!== FALSE)&&(strpos($input[$line_num], 'is')!== FALSE)))){
					$tabcount++;
					$process_array[$j] = $tab.$input[$line_num];
					$line_num++;
					$j++;
					continue;
				}
				if($whenflag == 1){
					$process_array[$j] = "\t".$tab.$input[$line_num];
				}else{
					$process_array[$j] = $tab.$input[$line_num];
				}
				$line_num++;
				$j++;
				
				if((strpos($input[$line_num], 'end if;')!== FALSE)||(strpos($input[$line_num], 'end loop;')!== FALSE)||(strpos($input[$line_num], 'end case;')!== FALSE)){
					$whenflag = 0;
					if($tabcount>0){
						$tabcount--;
					}
					$process_array[$j] = $input[$line_num];	
				}

			}

		}else if((strpos($input[$line_num], 'when')!== FALSE)&&(strpos($input[$line_num], 'else')!== FALSE)){
			
			$temparr = explode("<=", $input[$line_num]);
			$process_array[$j] = $temparr[0]." <=".$temparr[1];
			$line_num++;
			$j++;
			$spacecount = strlen($temparr[0])+3;
			$space = str_repeat(" ", $spacecount);
		
			while((strpos($input[$line_num], 'when') !== FALSE)&&(strpos($input[$line_num], 'else') !== FALSE)){
				$input[$line_num] = ltrim($input[$line_num]);
				$process_array[$j] = $space.$input[$line_num];
				$line_num++;
				$j++;
			}
			if(strpos($input[$line_num], ';') !== FALSE){
				$input[$line_num] = ltrim($input[$line_num]);
				$process_array[$j] = $space.$input[$line_num];
				$j++;
				array_push($dupl, $j);
			}
			
		}else if((strpos($input[$line_num], 'with')!== FALSE)&&(strpos($input[$line_num], 'select')!== FALSE)){
			if(strpos($input[$line_num], '<=')!== FALSE){
				$space = "\t";
				$process_array[$j] = $input[$line_num];
				$line_num++;
				$j++;
			}else{
				$process_array[$j] = $input[$line_num];
				$line_num++;
				$j++;
				$input[$line_num] = ltrim($input[$line_num]);
				$temparr = explode("<=", $input[$line_num]);
				$process_array[$j] = $temparr[0]." <=".$temparr[1];
				$line_num++;
				$j++;
				$spacecount = strlen($temparr[0])+3;
				$space = str_repeat(" ", $spacecount);
			}
			
			while(strpos($input[$line_num], ',') !== FALSE){
				$process_array[$j] = $space.$input[$line_num];
				$line_num++;
				$j++;
			}
			if(strpos($input[$line_num], ';') !== FALSE){
				$process_array[$j] = $space.$input[$line_num];
				$j++;
				array_push($dupl, $j);
			}
			
		}
		$process_array[$j] = $input[$line_num];
		$j++;
		$line_num++;
		
	}

	if(!empty($dupl)){
		foreach($dupl as $d){
			unset($process_array[$d]);
		}
		$process_array = array_values($process_array);
	}

	if($process_line != ""){
		$i = 1;
		while(strpos($process_array[$i], 'end process') === FALSE){
			$process_array[$i] = "\t".$process_array[$i];
			$i++;
		}
	}
	
	//echo $process_line."<br>";
	//echo '<pre>',print_r($process_array),'</pre>';
	array_push($architecture_array, $signal_array, $process_line, $process_var_array, $process_array);
	unset($dupl);
	unset($signal_array);
	unset($process_var_array);
	unset($process_array);
}

function entity_func($input,$line_num,$i,$line,$fl){
	global $entity_array;
	$port_input = array();
	$generic_input = array();
	$genericflag = 0;
	$portflag = 0;
	if($fl == 0){
		$entitys_name = rtrim(get_string_between($line, 'entity', 'is'));
		array_push($entity_array, $entitys_name); 
	}else{
		$entitys_name = "component";
	}

	echo "Entity ".$entitys_name." found!<br>";
	while (strpos($input[$line_num], $entitys_name)=== FALSE){

		$countpar = 0;
		if(strpos($input[$line_num], 'generic') !== FALSE){
			echo "Generic block found...<br>";
			$genericflag = 1;
			$temp_generic_array[$i] = $input[$line_num];
			$countpar += substr_count($input[$line_num], '(');
			$countpar += substr_count($input[$line_num], ')');
			do{
				if($countpar%2 == 0){break;}
				$line_num++;
				$i++;
				$temp_generic_array[$i] = $input[$line_num];
				$countpar += substr_count($input[$line_num], '(');
				$countpar += substr_count($input[$line_num], ')');
				
			}while($countpar%2 != 0);
			
		}
		
		$countpar = 0;
		if(strpos($input[$line_num], 'port') !== FALSE){
			echo "Port block found...<br>";
			$portflag = 1;
			$temp_port_array[$i] = $input[$line_num];
			$countpar += substr_count($input[$line_num], '(');
			$countpar += substr_count($input[$line_num], ')');
			do{
				if($countpar%2 == 0){break;}
				$line_num++;
				$i++;
				$temp_port_array[$i] = $input[$line_num];
				$countpar += substr_count($input[$line_num], '(');
				$countpar += substr_count($input[$line_num], ')');
				
			}while($countpar%2 != 0);
			
		}
		
		$line_num++;			
	}
	if($genericflag == 1){
		$i = 0;
		foreach($temp_generic_array as $tga){
			if(strpos($tga, ':') === FALSE){
				unset($temp_generic_array[$i]);
			}
			$i++;
		}	
		$temp_generic_array = array_values($temp_generic_array);
		
		if(strpos($temp_generic_array[0], 'generic(') !== FALSE){
			$temp_generic_array[0] = substr($temp_generic_array[0], 8);
		}else if(strpos($temp_generic_array[0], 'generic (') !== FALSE){
			$temp_generic_array[0] = substr($temp_generic_array[0], 9);
		}
		if(strpos($temp_generic_array[count($temp_generic_array)-1], ');') !== FALSE){
			$temp_generic_array[count($temp_generic_array)-1] = substr($temp_generic_array[count($temp_generic_array)-1], 0, -2);
		}
		$i = 0;
		$j = 0;
		$k = 0;
		foreach($temp_generic_array as $tga){
			$temp_generic_array[$i] = explode(';', $tga);
			$i++;
		}

		for($i=0; $i<count($temp_generic_array); $i++){
			foreach($temp_generic_array[$i] as $tga){
				if(($tga != '')&&($tga != ' ')){
					$temp_generic_array_2[$k] = $tga;
					$k++;
				}
			}
		}
		$i = 0;
		$k = 0;
		foreach($temp_generic_array_2 as $tga){
			$temp_generic_array_2[$i] = explode(':', $tga, 2);
			$i++;
		}
		for($i=0; $i<count($temp_generic_array_2); $i++){
			if(strpos($temp_generic_array_2[$i][1], ';') !== FALSE){
				$temp_generic_array_2[$i][1] = substr($temp_generic_array_2[$i][1], 0, -1);
			}
		}

		for($i=0; $i<count($temp_generic_array_2); $i++){
			$temp_generic_array_2[$i][0] = explode(',', $temp_generic_array_2[$i][0]);
		}
		
		$spacecount = 0;
		for($i=0; $i<count($temp_generic_array_2); $i++){
			foreach($temp_generic_array_2[$i][0] as $tga){
				if($spacecount <= strlen($tga)){
					$spacecount = strlen($tga);
				}
			}
		}
		
		for($i=0; $i<count($temp_generic_array_2); $i++){
			foreach($temp_generic_array_2[$i][0] as $tga){
				
				$space = str_repeat(" ", $spacecount-strlen($tga));
				$temp_generic_array_2[$i][1] = ltrim($temp_generic_array_2[$i][1]);
				$generic_input[$k] = $tga.$space." :".$temp_generic_array_2[$i][1];
				$k++;
			}
		}
	}
	
	if($portflag == 1){
	    /*
	    //something weird
		$i = 0;
		foreach($temp_port_array as $tpa){
			if(strpos($tpa, ':') === FALSE){
				unset($temp_port_array[$i]);
			}
			$i++;
		}
		*/
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
		
		$spacecount = 0;
		for($i=0; $i<count($temp_port_array_2); $i++){
			foreach($temp_port_array_2[$i][0] as $tpa){
				if($spacecount <= strlen($tpa)){
					$spacecount = strlen($tpa);
				}
			}
		}
		
		for($i=0; $i<count($temp_port_array_2); $i++){
			foreach($temp_port_array_2[$i][0] as $tpa){
				
				$space = str_repeat(" ", $spacecount-strlen($tpa));
				$temp_port_array_2[$i][1] = ltrim($temp_port_array_2[$i][1]);
				$port_input[$k] = $tpa.$space." :".$temp_port_array_2[$i][1];
				$k++;
			}
		}
	}
	
	array_push($entity_array, $generic_input, $port_input);
	unset($generic_input);
	unset($port_input);
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