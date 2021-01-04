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

//$files_arr = array("file1.txt","file2.txt","file3.txt");
$file_num = count($files_arr);
if($file_num <= 1){
	echo "You need to check 2 or more files for Structure Visualization!<br>";
}
echo "<br>Combinational Logic Codes' Structure :<br>";
$line_num = 0;
$num = 0;
	
foreach ($files_arr as $file_name){
	#$en_ar = array();
	#$n = 0;
	$co_ar = array();
	$m = 0;
	$file = fopen($file_name, "r") or exit("Unable to open file!");
	
	while(!feof($file)) {

		$line = fgets($file);
		#echo $line,"<br>" ;
		$line_num ++;
		
		if ((strpos($line, 'entity') !== FALSE)&&(trim(get_string_between($line, "entity", "is")))){
			#echo $file_name;
			$entity_name = trim(get_string_between($line, "entity", "is"));
			#echo $entity_name;
			#echo "<br>";
			#$en_ar[$n] = $entity_name;
			#$n++;

		}
		
		if ((strpos($line, 'component') !== FALSE)&&(trim(get_string_between($line, "component", "is")))){
			#echo $file_name;
			$component_name = trim(get_string_between($line, "component", "is"));
			#echo $component_name;
			#echo "<br>";
			$co_ar[$m] = $component_name;
			$m++;
		}
		error_reporting(0);
		$tree_arr[$num] = array ('file_name' => $file_name, 'entity_name' => $entity_name, 'component_name' => $co_ar);
	}
	
	fclose($file);
	$num ++;
}
#print_r($tree_arr);
#echo "<br>";

$x = 0;
$y = 0;
$father_node = array();
$child_node = array();

for($num = 0; $num<$file_num; $num++ ){
	if($tree_arr[$num]['component_name'] != NULL){
		$father_node[$x]=$num;
		$x++;
	} else {
		$child_node[$y]=$num;
		$y++;
	}
}

#echo "<br>";
#print_r($father_node);
#print_r($child_node);

if(count($father_node)>1){
	$root = $father_node[0];
	for($i = 1; $i <= count($father_node); $i++){
		foreach($tree_arr[$father_node[$i]]['component_name'] as $cn){
			if($tree_arr[$root]['entity_name'] == $cn){
				$root = $father_node[$i];
			}
		}
	}
}else if(count($father_node) == 1){
	$root = $father_node[0];
}else{
	echo "ERROR! Father node not found!";
}

echo "<link rel='stylesheet' type='text/css' href='tree.css'>";
$root_node[0] = $root;
$x = 0;

echo "<div class='tree'>";

echo "<ul id='tree'>";
while($x<count($father_node)){
	echo "<li><a href='#'>",$tree_arr[$root]['entity_name'],"</a>";
	echo "<ul>";
	foreach($tree_arr[$root]['component_name'] as $co_name){
		foreach($father_node as $fn){
			if($co_name == $tree_arr[$fn]['entity_name']){
				$root = $fn;
			}
		}
		foreach($child_node as $cn){
			if($co_name == $tree_arr[$cn]['entity_name']){
				echo "<li><a href='#'>",$tree_arr[$cn]['entity_name'],"</a></li>";
			}
		}
	}
	$x++;
	echo "</il>";
}
echo "</ul>";
echo "</div>";
echo "<br><a href='javascript:window.location.reload(true)'>Reload Page</a><br>";

function get_string_between($string, $start, $end){
	$string = " ".$string;
	$ini = strpos($string,$start);
	if ($ini == 0) return "";
	$ini += strlen($start);   
	$len = strpos($string,$end,$ini) - $ini;
	return substr($string,$ini,$len);
}


?>