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

$zipname = time().".zip";
$zip = new ZipArchive;

if($zip->open($zipname, ZipArchive::CREATE)!== TRUE){
	echo " Sorry ZIP creation failed at this time";
}
foreach ($files_arr as $file) {
	$zip->addFile($file);
}
$zip->close();

$file_url = PATH.$zipname;

header('Content-type: application/zip');
header('Content-Disposition: attachment; filename="'.basename($zipname).'"');
header("Content-length: " . filesize($zipname));
header("Pragma: no-cache");
header("Expires: 0");

ob_clean();
flush();

readfile($file_url);
unlink($zipname)

?>