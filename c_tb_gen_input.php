<?php 
	include('constants.php');
	session_start();
	if(!isset($_SESSION['username'])){header("Location:login.php");}else{ $username = $_SESSION['username'];}
	$path = PATH.'uploads/'.$username.'/';
?>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="icon" href="xoricon.png">
		<link rel="stylesheet" type="text/css" href="reglog.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js"></script> 
		<script src="http://malsup.github.com/jquery.form.js"></script>
		<title>Testbench Values</title>
	</head>
	<body>
	
		<img class="logo__main" src="logocircle.png" height="90" width="90">
		<h2 class="user__name">&nbsp;&nbsp;Hello <?php echo $username; ?> !</h2>
		<!--<a href="logout.php">Log out</a>-->
		<br><br><br><br><br><br>
		<?php 
			$port_input = array();
			$port_input = $_SESSION['port_input'];
			$t_port_input = $port_input;
			$file_name = $_SESSION['file_name'];
			$entitys_name = $_SESSION['entitys_name'];
			$out_count = 0;
			for($i=0; $i<count($t_port_input); $i++){
				if((strpos($t_port_input[$i], ':out')!== FALSE)||(strpos($t_port_input[$i], ': out')!== FALSE)){
					$out_count++;
				}
				$portmap_input[$i] = strtok($port_input[$i], ":");
				if((stripos($portmap_input[$i], 'clock') !== FALSE)||(stripos($portmap_input[$i], 'clk') !== FALSE)){
					$clock_input = $portmap_input[$i];
					unset($port_input[$i]);
				}
				if((stripos($portmap_input[$i], 'reset') !== FALSE)||(stripos($portmap_input[$i], 'rst') !== FALSE)){
					$reset_input = $portmap_input[$i];
					unset($port_input[$i]);
				}
			}
			echo $out_count."<br>";
			$cols = count($port_input);
			$i = 0;
			foreach($port_input as $pa){ 
				$port_arr_t[$i] = strtok(preg_replace('/\s+/', '', $pa), ':'); //input id
				//$port_arr_n[$i] = $port_arr_t[$i].'[]';                      //input name
				$i++;
			}
		?>
		<form method="POST" enctype="multipart/form-data">
			<b>Wait period (in ns):</b> &nbsp <input type="number" name="wait" min="1" max="500" value="20"><br>
			<b>Error report:</b> &nbsp <input type="text" name="report" value="Test failed"><br><br>
			<table id="tblTest" cellpadding="0" cellspacing="0" border="1">
			<thead>
				<tr>
					<?php foreach($port_input as $pa){ echo "<th>".$pa."</th>"; } ?>
					<th><input type="button" onclick="Add()" value="   Add    " /></th>
				</tr>
			</thead>
			<tbody>
			</tbody>
			<tfoot>
				<tr>
					<!--<td></td>-->
				</tr>
			</tfoot>
			</table>
			<br><input type="submit" name="Submit" onclick="Sendfunc()">
		</form>
	
		<script>
		
			var i = 0;
			function Add(){
				
				<?php 
					$str = 'AddRow(';
					for($i=0; $i<sizeof($port_arr_t)-1; $i++){
						$str .= '$("#'.$port_arr_t[$i].'"),';
					}
					$str .= '$("#'.end($port_arr_t).'"));';
					echo $str;
					$str = '';
					//for($i=0; $i<sizeof($port_arr_t); $i++){
					//	$str = '$("#'.$port_arr_t[$i].'").val("");';
					//	echo $str;
					//}
				?>
				
			};
			
			<?php 
				$alpha = range('a','z');
				echo "function AddRow(";
				for($i=0; $i<sizeof($port_input); $i++){
					echo $alpha[$i].",";
				}
				$i++;
				echo $alpha[$i]."){";

				echo "var tBody = $('#tblTest > TBODY')[0];";
				echo "row = tBody.insertRow(-1);";
				
				$str = "";
				for($i=0; $i<sizeof($port_input); $i++){
					
					//$str .= "var cell = $(row.insertCell(-1)); cell.html(".$alpha[$i].");";
					$str .= "var cell = $(row.insertCell(-1)); var inp = $('<input />'); inp.attr('required', 'required'); inp.attr('type', 'text'); inp.attr('name', i++); cell.append(inp);";
				}
				echo $str;

				echo "cell = $(row.insertCell(-1));";
				echo "var btnRemove = $('<input />');";
				echo "btnRemove.attr('type', 'button');";
				echo "btnRemove.attr('onclick', 'Remove(this);');";
				echo "btnRemove.val('Remove');";
				echo "cell.append(btnRemove);";
				echo "};";
			?>
			
			function Remove(button) {
				//Determine the reference of the Row using the Button.
				var row = $(button).closest("TR");
				var name = $("TD", row).eq(0).html();
				if (confirm("Do you want to delete this test?")) {
	 
					//Get the reference of the Table.
					var table = $("#tblTest")[0];
	 
					//Delete the Table row using it's Index.
					table.deleteRow(row[0].rowIndex);
				}
			};
			
		</script>
		
		<?php
		error_reporting(0);
		$i = 0;
		$c_out = 0;
		$userinput = array();
		foreach($port_input as $pa){ 
			$userinput[$i][0] = $pa;
			$i++;
			if((strpos($pa, ':out') !== false)||(strpos($pa, ': out') !== false)){
				$c_out++;
			}
		}
		$i = 0;
		
		if(isset($_POST["Submit"])){
			$period = $_POST['wait'];
			$report = $_POST['report'];
			while(isset($_POST[$i])){
				$userinput[$i % $cols][$i / $cols +1] = $_POST[$i];
				$i++;
					
			}
			$lines = $i / $cols;
			
			//print_r($userinput);
			//echo $c_out;
			$flag2 = 0;
			$i = 0;
			$j = 1;
			//print_r($userinput);
			$assertarr = array();
			while($j <= $lines){
				$flag = 0;
				
				foreach($userinput as $ui){
					
					if((strpos($ui[0], ':in') !== false)||(strpos($ui[0], ': in') !== false)){
						$in_n = explode(':',$ui[0],2);
						
						$assertarr[$i] .= "\t\t".$in_n[0]." <= '".$ui[$j]."';".PHP_EOL;
						
					}else if((strpos($ui[0], ':out') !== false)||(strpos($ui[0], ': out') !== false)){
						$out_n = explode(':',$ui[0],2);
						
						//$assertarr[$i] .= PHP_EOL."wait for period;".PHP_EOL
						if(($out_count <= 1)&&($flag == 0)&&($flag2 == 0)){
							$assertarr[$i] .= "\t\twait for period;".PHP_EOL."\t\tassert(".$out_n[0]." = '".$ui[$j]."')".PHP_EOL;
						}else{
							$l = 0;
							$flag2 = 1;
							if($flag == 0){
								$flag = 1;
								$assertarr[$i] .= "\t\twait for period;".PHP_EOL."\t\tassert(";
								
							}
							if($flag == 1){
								$flag = 2;
								$assertarr[$i] .= $out_n[0]." = '".$ui[$j]."' and ";
								$l++;
								$out_count--;
							}else{
								$assertarr[$i] .= $out_n[0]." = '".$ui[$j]."')".PHP_EOL;
							}
						}
					}
					
				}
				$assertarr[$i] .= "\t\treport '".$report."' severity error;".PHP_EOL;
				//echo $assertarr[$i]."<br>";
				$j++; 
				$i++;
			}
			
			$temp_file_name = explode(".", $file_name);
			$testbenchfilename = $temp_file_name[0]."_c_test_bench.vhd";	

			$tbfile = fopen($testbenchfilename, "w") or exit("Unable to create file!");
			$output = array();
			$k = 0;

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
			for($i=0; $i<count($t_port_input); $i++){
				$k++;
				$output[$k] = "\t\t".$t_port_input[$i].";";
			}
			$output[$k++] = "\t\t".$t_port_input[count($t_port_input)-1];
			$output[$k++] = "\t);";
			$output[$k++] = "end component;".PHP_EOL;
			for($i=0; $i<count($t_port_input); $i++){
				$output[$k] = "\tsignal ".$t_port_input[$i].";";
				$k++;
			}
			$output[$k++] = PHP_EOL."\tconstant TbPeriod : time := ".$period." ns; -- EDIT Put right period here";
			$output[$k++] = "\tsignal TbClock : std_logic := '0';";
			$output[$k++] = "\tsignal TbSimEnded : std_logic := '0';".PHP_EOL;
			$output[$k++] = "begin".PHP_EOL;
			$output[$k++] = "\t dut : ".$entitys_name;
			$output[$k++] = "\t port map ( ";
			for($i=0; $i<count($t_port_input); $i++){
				$k++;
				$output[$k] = "\t\t".$portmap_input[$i]."=>".$portmap_input[$i].",";
			}
			$output[$k++] = "\t\t".$portmap_input[count($t_port_input)-1]."=>".$portmap_input[count($t_port_input)-1];
			$output[$k++] = "\t);".PHP_EOL;
			if($clock_input != ""){
				$output[$k++] = "\t-- Clock generation".PHP_EOL."\tTbClock <= not TbClock after TbPeriod/2 when TbSimEnded /= '1' else '0';".PHP_EOL;
				$output[$k++] = "\t-- EDIT: Check that Clock is really your main clock signal";
				$output[$k++] = "\t".$clock_input." <= TbClock;".PHP_EOL;
			}
			$output[$k++] = "\t stimulus : process";
			$output[$k++] = "\t begin";
			$output[$k++] = "\t\t-- EDIT Adapt initialization as needed";
			for($i=0; $i<count($t_port_input); $i++){
				if(($portmap_input[$i] != $clock_input)&&($portmap_input[$i] != $reset_input)){
					if(strpos($t_port_input[$i], '(') !== FALSE){
						$output[$k] = "\t\t".$portmap_input[$i]." <= (others => '0');";
					}else{
						$output[$k] = "\t\t".$portmap_input[$i]." <= '0';";
					}
					$k++;
				}
			}
			$output[$k++] = PHP_EOL;
			if($reset_input != ""){
				$output[$k++] = PHP_EOL."\t\t-- Reset generation";
				$output[$k++] = "\t\t-- EDIT: Check that Reset is really your reset signal";
				$output[$k++] = "\t\t".$reset_input." <= '1';";
				$output[$k++] = "\t\twait for 100 ns;";
				$output[$k++] = "\t\t".$reset_input." <= '0';";
				$output[$k++] = "\t\twait for 100 ns;".PHP_EOL;
			}
			//$output[$k++] = "\t\t-- EDIT Add stimulus here"
			foreach($assertarr as $asar){
				$output[$k++] = $asar;
			}
			$output[$k++] = PHP_EOL."\t\twait for 100 * TbPeriod;".PHP_EOL;
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
			
			echo '<pre>',print_r($output),'</pre>';
			echo "Processing finished successfully...<br>";
			
			$db_host = DBHOST;
			$db_name = DBNAME;
			$db_user = DBUSER;
			$db_pass = DBPASS;
			
			//echo "<br><a href='.$newfile.'>Click here to download</a>&nbsp;&nbsp;&nbsp;";
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
	</body>
	
</html>