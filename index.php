<?php 
include('constants.php');
session_start();
if(!isset($_SESSION['username'])){header("Location:login.php");}else{ $username = $_SESSION['username'];}
?>
<!doctype html>
<html>
	<head>
		<meta charset="utf-8">
		<link rel="icon" href="xoricon.png">
		<link rel="stylesheet" type="text/css" href="reglog.css">
		<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
		<script src="https://ajax.aspnetcdn.com/ajax/jQuery/jquery-3.3.1.min.js"></script>
		<title>VHDL Companion</title>
	</head>
	<body>
		<div class="container">
		
		<div class="top">	
			<img class="logo__main" src="logocircle.png" height="130" width="130" onClick="location.reload();" >
			<div class="outer">
			
			<span class="user__name" id="datetime" ></span>
			<h2 class="user__name">Hello <?php echo $username; ?> !</h2>
			
			<div class="user__in">
				<a href="logout.php">Log out</a><br>
				<a href="upload.php" target="popup" onclick="window.open('upload.php','popup','width=600,height=400'); return false;">Upload Files</a>
			</div>
			</div>
			<ul class="menu">
				<!-- <input type="submit" name="correct" value="Correct"><br> -->
				<li><input type="button" id="vhdlwiki" onclick="window.open('https://en.wikipedia.org/wiki/VHDL');" value="VHDL Wiki"/></li>
				<li><input type="button" id="vhdltutorials" onclick="showDiv()" value="Tutorials & Examples"/></li>
				<li><input type="button" id="vhdlvideo" onclick="showDiv1()" value="Video Tutorials"/></li>
				<!--<input type="button" id="vhdllessons" onclick="showDiv2()" value="VHDL Lessons"/>-->
				<li><input type="button" id="vhdlbooks" onclick="showDiv2()" value="VHDL Books"/></li>
				<li><input type="button" id="vhdlsimulators" onclick="showDiv3()" value="Implementations & Simulators"/></li>
				<li><input type="button" id="vhdlabout" onclick="showDiv4()" value="About"/></li>
			</ul>
			<div id="tutorials" style="display:none">
			<br>VHDL Tutorials Examples and Courses<br>
			<ul>
				<li><a href="http://esd.cs.ucr.edu/labs/tutorial/">VHDL Tutorial: Learn by Example</a></li><br>
				<li><a href="https://www.seas.upenn.edu/~ese171/vhdl/vhdl_primer.html">VHDL Tutorial: University of Pennsylvania</a></li><br>
				<li><a href="https://www.nandland.com/vhdl/tutorials/tutorial-introduction-to-vhdl-for-beginners.html">NANDLAND - Introduction to VHDL</a></li><br>
				<li><a href="https://www.intel.com/content/www/us/en/programmable/support/training/course/ohdl1110.html">Intel Online Course : VHDL Basics</a></li><br>
				<li><a href="https://www.udemy.com/topic/vhdl/">Udemy Online Course : VHDL</a></li><br>
			</ul>
			</div>
			<div id="simulators" style="display:none">
			<br>Open Source Implementations and Simulators<br>
			<ul>
				<li><a href="http://ghdl.free.fr/index.html" target="_blank">GHDL</a></li><br>
				<li><a href="https://github.com/nickg/nvc" target="_blank">NVC</a></li><br>
				<li><a href="http://freehdl.seul.org/" target="_blank">Free HDL</a></li><br>
			</ul>
			Commercial Implementations and Simulators<br>
			<ul>
				<li><a href="https://www.aldec.com/en/products/fpga_simulation/active-hdl" target="_blank">Active-HDL</a></li><br>
				<li><a href="https://www.cadence.com/content/cadence-www/global/en_US/home/tools/system-design-and-verification/simulation-and-testbench-verification/incisive-enterprise-simulator.html" target="_blank">Incisive Enterprise Simulator</a></li><br>
				<li><a href="https://www.mentor.com/products/fpga/model/" target="_blank">ModelSim</a></li><br>
			</ul>
			</div>
			<div id="books" style="display:none">
			<br>Free books online (pdf)<br>
			<ul>
				<li><a href="https://www.csee.umbc.edu/portal/help/VHDL/VHDL-Handbook.pdf" target="_blank">VHDL handbook</a></li><br>
				<li><a href="https://www.ics.uci.edu/~alexv/154/VHDL-Cookbook.pdf" target="_blank">The VHDL Cookbook</a></li><br>
				<li><a href="http://freerangefactory.org/pdf/df344hdh4h8kjfh3500ft2/free_range_vhdl.pdf" target="_blank">Free range VHDL</a></li><br>
			</ul>
			Books to buy (Amazon)<br>
			<ul>
				<li><a href="https://www.amazon.com/Designers-Guide-Edition-Systems-Silicon/dp/0120887851" target="_blank">The Designer's Guide to VHDL, Third Edition</a></li><br>
				<li><a href="https://www.amazon.com/Circuit-Design-Simulation-VHDL-Press/dp/0262014335" target="_blank">Circuit Design and Simulation with VHDL</a></li><br>
				<li><a href="https://www.amazon.com/Vhdl-Example-Blaine-Readler/dp/0983497354" target="_blank">Vhdl By Example</a></li><br>
				<li><a href="https://www.amazon.com/VHDL-Basics-Programming-Gaganpreet-Kaur-ebook/dp/B00BIZS378" target="_blank">VHDL: Basics to Programming</a></li><br>
				<li><a href="https://www.amazon.com/VHDL-Engineers-Kenneth-L-Short/dp/0131424785" target="_blank">VHDL for Engineers</a></li><br>
				<li><a href="https://www.amazon.com/VHDL-Example-Fundamentals-Digital-Design/dp/0982497059" target="_blank">VHDL By Example: Fundamentals of Digital Design</a></li><br>
				<li><a href="https://www.amazon.com/Circuit-Design-VHDL-Volnei-Pedroni/dp/0262162245" target="_blank">Circuit Design with VHDL</a></li><br>
				<li><a href="https://www.amazon.com/Digital-Design-Using-VHDL-Approach/dp/1107098866" target="_blank">Digital Design Using VHDL: A Systems Approach</a></li><br>
			</ul>	
			</div>
			<div id="about" style="display:none">
			<h2>Σχολή Θετικών Επιστημών Τμήμα Πληροφορικής</h2>
			<h2>ΠΤΥΧΙΑΚΗ ΕΡΓΑΣΙΑ</h2>
			<h3>Δημιουργία διαδραστικής διαδικτυακής εφαρμογής τροποποίησης, επεξεργασίας και αποθήκευσης κωδίκων VHDL</h3>
				Το αντικείμενο της παρούσας πτυχιακής εργασίας είναι η δημιουργία διαδραστικής διαδικτυακής εφαρμογής για την τροποποίηση,
				την επεξεργασία και την αποθήκευση κωδίκων VHDL.Το όνομα της εφαρμογής - Ιστοσελίδας είναι VHDL Companion και αναφέρεται σε
				χρήστες που θέλουν να ξεκινήσουν να μαθαίνουν VHDL αλλά και σε χρήστες πιο έμπειρους με την γλώσσα οι οποίοι θέλουν να αποθηκεύσουν,
				να επεξεργαστούν και να τροποποιήσουν τους κωδικές τους με γρήγορο, εύκολο και δυναμικό τρόπο. Αν και αναφέρεται κυρίως σε 
				προγραμματιστές  η σχεδίαση της εφαρμογής έγινε με τέτοιο τρόπο ώστε να είναι κατανοητή και ευκολόχρηστη από όλους. Για την 
				υλοποίηση της εφαρμογής χρησιμοποιήθηκαν γλώσσα σήμανσης υπερκειμένου HTML, γλώσσα φύλλου ύφους CSS, καθώς και γλώσσες 
				προγραμματισμού PHP και Javascript. Τέλος χρησιμοποιήθηκε ένα σύστημα διαχείρισης σχεσιακών βάσεων δεδομένων MySQL για 
				την αποθήκευση και την ανάκτηση πληροφοριών για τους χρήστες και τα αρχεία τους από μια βάση δεδομένων.
			<h3>Κωνσταντίνος Ρούτσης - 2113122</h3><br>
			<h2>University of Thessaly School of Sciences Department of Informatics</h2>
			<h3>Interactive web application for modifying, editing and storing VHDL codes</h3>
				The object of this dissertation is to create an interactive web application for modification,processing and storing VHDL codes.
				The name of the application - Website is VHDL Companion and refers to users who want to start learning VHDL but also more 
				experienced users with the language they want to save, edit and modify their codes in a fast, easy and dynamic way.
				Although it refers mainly to developers the application was designed in such a way that it is understandable and easy to use by everyone.
				The application is written and implement with PHP and Javascript (HTML markup language, CSS style sheet language also used).
				Finally a MySQL relational database management system was used for storing and retrieving information about users and their files from a database.
			<h3>Konstantinos Routsis - krunknownmaniak95@gmail.com</h3>
			</div>
			<div id="videos" style="display:none"><br>
				<iframe width="420" height="345" src="https://www.youtube.com/embed/zm-RA6BsYmc" frameborder="0" allowfullscreen></iframe>
				<iframe width="420" height="345" src="https://www.youtube.com/embed/BDq8-QDXmek" frameborder="0" allowfullscreen></iframe>
				<iframe width="420" height="345" src="https://www.youtube.com/embed/h4ZXge1BE80" frameborder="0" allowfullscreen></iframe>
				<iframe width="420" height="345" src="https://www.youtube.com/embed/vXF0yDeQ-Ms" frameborder="0" allowfullscreen></iframe>
			</div><br>
		</div>
		
		<script>
		var dt = new Date();
		document.getElementById("datetime").innerHTML = dt.toLocaleDateString();
		
		$('textarea#txtar').focus(function () {
			$(this).animate({ height: "40em" }, 500);
		});
		
		function replyclk(clicked_id){
			var t = clicked_id;

			//alert(clicked_id);
			//$('.save_btn').click(function() {
			
			var comment = $.trim($('.'+t).val());
			alert (comment);
			$.ajax({
				url: "code_save.php",
				type: "post",
				data: {value: comment},
				
				success: function(data) {
					$('.responce').append(data);
				}
			});
			//});
		}
		
		function showDiv1() {
			//document.getElementById('videos').style.display = "block";
			var z = document.getElementById("videos");
			if (z.style.display === "none") {
				z.style.display = "block";
			} else {
				z.style.display = "none";
			}
			
		}
		function showDiv2() {
			var z = document.getElementById("books");
			if (z.style.display === "none") {
				z.style.display = "block";
			} else {
				z.style.display = "none";
			}
		}
		function showDiv3() {
			var z = document.getElementById("simulators");
			if (z.style.display === "none") {
				z.style.display = "block";
			} else {
				z.style.display = "none";
			}
		}
		function showDiv4() {
			var z = document.getElementById("about");
			if (z.style.display === "none") {
				z.style.display = "block";
			} else {
				z.style.display = "none";
			}
		}
		function showDiv() {
			var z = document.getElementById("tutorials");
			if (z.style.display === "none") {
				z.style.display = "block";
			} else {
				z.style.display = "none";
			}
		}
		</script>
		
		<form method="POST" enctype="multipart/form-data">
		<br>
		<div class="options" style="margin-left:40px; width:100%">
			<input type="button" id="dead_code_removal" value="Dead Code Removal" />
			<input type="button" id="code_structure_visualization" value="Code Structure Visualization" />
			<input type="button" id="code_restructuring" value="Code Restructuring" />
			<input type="button" id="g_testbench_generator" value="General Testbench Generator" />
			<input type="button" id="c_testbench_generator" value="Custom Testbench Generator" />
		</div>
		<div class="responce" style="margin-left: 200px;"></div>
		<div class="feedback" style="width: 100%; display: table;"><br>
		<div class= "table" style="display: table-row">
		
			<div class="checklist" style="width: 190px; display: table-cell;">
			<?php

			$path = PATH.'uploads/'.$username.'/';
			
			$dir_handle = @opendir($path) or die("<br><h2>Upload VHDL Files to continue...</h2>");
			echo "<h2>Your files</h2>";
			while ($file = readdir($dir_handle)) {
				if($file == "." || $file == ".." ) {
				  continue; 
				}
				// Prints a checkbox after each file.
				// The checkboxes are received by the form handler as an array called $files[]
				// holding the filenames of the files .
				$tmpname = mb_strimwidth($file, 0, 20, "...");
				echo "<label title='$file'><input type='checkbox' title='$file' class='files' id='chkbx' name='files[]' value='$file'/> $tmpname <label><br>";
			}
			?>
			<br>
			<input type="button" id="download" value="Download"/>
			<input type="button" id="delete" value="Delete"/>
			</div>

		</form>
		
		<form method="POST" enctype="multipart/form-data">
		<!--<input type="submit" id="reset" action="main.php" value="Reset"/>-->
		</form>	
			<ol>
			<?php
			
			$i = 0;
			$dir_handle = @opendir($path) or die("Unable to open $path");
			while ($file = readdir($dir_handle)) {
				if($file == "." || $file == ".." ) {
				  continue; 
				}
				$i++;
				
				$file_content[$i] = implode(" ",showfile($path.$file));
				$classcontainer[$i] = 'cc_'.$i;
				$txtclass[$i] = 'sb_'.$i;
				$savebtn[$i] = 'sb_'.$i;
				//echo "$classname";

				echo "<div class='classcontainer[$i]' style='position: relative;'>";
				echo "<textarea rows='15' cols='50' style='width: 75%; height: 100%; box-sizing: border-box;' spellcheck='false' id='txtar' class='$txtclass[$i]' name='files'> $file_content[$i] </textarea>";
				echo "<button type='button' class='save_btn' name='btn' style='display:none; position: absolute; bottom: 5px; left: 0px;' id='$savebtn[$i]' onClick='replyclk(this.id)'>Save Changes</button>";
				echo "</div>";

			}

			function showfile($file){
				$line_num = 0;
				$file = fopen($file, "r") or exit("Unable to open file!");
		
				while(!feof($file)) {
					$line[$line_num] = fgets($file);
					$line_num ++;
				}
				return $line;
				fclose($file);
			}
			if(empty($txtclass)){
				echo "<br><h2>Upload VHDL Files to continue...</h2>";
			}else{
			$j = 0;
				foreach($txtclass as $tc){
					$j++;
					echo "<script> $('.$tc').on('keyup',function(){ $('#$savebtn[$j]').show();}); </script>";
					
				}
			}
			//for($k=1; $k<=$j; $k++){
			//	echo "<script> var scrdata =";
			//	echo json_encode($file_content[$k]);
			//	echo ";</script>";
			//	echo "<script> $('#$savebtn[$k]').click(function(){ $.ajax({ type: 'post', url: 'code_save.php', data: {value: scrdata} , success:(function( data ) { $('.responce').append(data); }) }); });</script>";
			//}
			?>
			</ol>
		</div>
		</div>
		</div>

		<script>
		
		$('#code_restructuring').prop("disabled", true);
		$('#code_restructuring').click(function() {
			$.ajax({
				url: "code_restructuring.php",
				type: "post",
				data: $('.files:checked').serialize(),
				
				success: function(data) {
					$('.responce').append(data);
					$('.files').prop('checked', false);
					$('#download').prop("disabled", true);
					$('#delete').prop("disabled", true);
					$('#code_structure_visualization').prop("disabled", true);
					$('#dead_code_removal').prop("disabled", true);
					$('#code_restructuring').prop("disabled", true);
					$('#g_testbench_generator').prop("disabled", true);
					$('#c_testbench_generator').prop("disabled", true);
					
				}
			});
		});
		
		$('#g_testbench_generator').prop("disabled", true);
		$('#g_testbench_generator').click(function() {
			$.ajax({
				url: "g_testbench_generator.php",
				type: "post",
				data: $('.files:checked').serialize(),
				
				success: function(data) {
					$('.responce').append(data);
					$('.files').prop('checked', false);
					$('#download').prop("disabled", true);
					$('#delete').prop("disabled", true);
					$('#code_structure_visualization').prop("disabled", true);
					$('#dead_code_removal').prop("disabled", true);
					$('#code_restructuring').prop("disabled", true);
					$('#g_testbench_generator').prop("disabled", true);
					$('#c_testbench_generator').prop("disabled", true);
					
				}
			});
		});
		
		$('#dead_code_removal').prop("disabled", true);
		$('#dead_code_removal').click(function() {
			$.ajax({
				url: "dead_code_removal.php",
				type: "post",
				data: $('.files:checked').serialize(),
				
				success: function(data) {
					$('.responce').append(data);
					$('.files').prop('checked', false);
					$('#download').prop("disabled", true);
					$('#delete').prop("disabled", true);
					$('#code_structure_visualization').prop("disabled", true);
					$('#dead_code_removal').prop("disabled", true);
					$('#code_restructuring').prop("disabled", true);
					$('g_#testbench_generator').prop("disabled", true);
					$('#c_testbench_generator').prop("disabled", true);
				}
			});
		});
		
		$('#code_structure_visualization').prop("disabled", true);
		$('#code_structure_visualization').click(function() {
			if($(".files:checked").length > 1){
				$('#code_structure_visualization').prop("disabled", false);
			$.ajax({
				url: "code_structure_visualization.php",
				type: "post",
				data: $('.files:checked').serialize(),
				
				success: function(data) {
					$('.responce').append(data);
					$('.files').prop('checked', false);
					$('#download').prop("disabled", true);
					$('#delete').prop("disabled", true);
					$('#code_structure_visualization').prop("disabled", true);
					$('#dead_code_removal').prop("disabled", true);
					$('#code_restructuring').prop("disabled", true);
					$('#g_testbench_generator').prop("disabled", true);
					$('#c_testbench_generator').prop("disabled", true);
				}
			});
			}else{
				$('#code_structure_visualization').prop("disabled", true);
			}
		});
		
		$('#c_testbench_generator').prop("disabled", true);
		$('#c_testbench_generator').click(function() {
			if($(".files:checked").length == 1){
				$('#c_testbench_generator').prop("disabled", false);
			$.ajax({
				url: "c_testbench_generator.php",
				type: "post",
				data: $('.files:checked').serialize(),
				
				success: function(data) {
					$('.responce').append(data);
					$('.files').prop('checked', false);
					$('#download').prop("disabled", true);
					$('#delete').prop("disabled", true);
					$('#code_structure_visualization').prop("disabled", true);
					$('#dead_code_removal').prop("disabled", true);
					$('#code_restructuring').prop("disabled", true);
					$('#g_testbench_generator').prop("disabled", true);
					$('#c_testbench_generator').prop("disabled", true);
				}
			});
			}else{
				$('#c_testbench_generator').prop("disabled", true);
			}
		});
		
		$('#download').prop("disabled", true);
		$('#download').click(function() {
			$.ajax({
				url: "download.php",
				type: "post",
				data: $('.files:checked').serialize(),
				
				success: function(data) {
					//$('#feedback').replaceWith(data);
					window.open("download.php")
				}
			});
		});
		
		$('#delete').prop("disabled", true);
		$('#delete').click(function() {
			var c = confirm("You want to delete these file(s)?");
			if (c == true){
				$.ajax({
					url: "delete.php",
					type: "post",
					data: $('.files:checked').serialize(),
					
					success: function(data) {
						location.reload();
					}
				});
			}else{
				location.reload();
			}
		});


		$('input:checkbox').click(function() {
			if ($(this).is(':checked')) {
				
				$('#download').prop("disabled", false);
				$('#delete').prop("disabled", false);
				$('#dead_code_removal').prop("disabled", false);
				$('#code_restructuring').prop("disabled", false);
				$('#g_testbench_generator').prop("disabled", false);
				$('#c_testbench_generator').prop("disabled", false);
				
				if ($('.files').filter(':checked').length > 1){
					$('#code_structure_visualization').prop("disabled", false);
					$('#c_testbench_generator').prop("disabled", true);
				}
				if ($('.files').filter(':checked').length == 1){
					$('#c_testbench_generator').prop("disabled", false);
				}
			} else {
				if ($('.files').filter(':checked').length < 1){
					$('#download').attr('disabled',true);
					$('#delete').attr('disabled',true);
					$('#code_structure_visualization').attr("disabled", true);
					$('#dead_code_removal').attr("disabled", true);
					$('#code_restructuring').attr("disabled", true);
					$('#g_testbench_generator').attr("disabled", true);
					$('#c_testbench_generator').attr("disabled", true);
				}
				if ($('.files').filter(':checked').length < 2){
					$('#code_structure_visualization').attr("disabled", true);
				}
				if ($('.files').filter(':checked').length == 1){
					$('#c_testbench_generator').attr("disabled", false);
				}
			}
		});
	
		</script>
		
	</body>
</html>