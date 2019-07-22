<?php
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
$r_name=$_GET['nopendaftar'];
$r_jalur=$_GET['jalur'];
$r_gel=$_GET['gelombang'];
$r_periode=$_GET['periode'];
$_SESSION['FOTO']['NAMAFILE'] = $r_name;
?>

	<table width="260">
	<tr>
		<td>
		<div align="center" id="upload_results" style="background-color:#eee;"></div>
		</td>
	</tr>
	<tr><td valign=top>
	<!-- First, include the JPEGCam JavaScript Library -->
	<script type="text/javascript" src="scripts/webcame.js"></script>
	<script type="text/javascript" src="scripts/jquery.js"></script>
	
	<!-- Configure a few settings -->
	<script language="JavaScript">
		webcam.set_api_url('index.php?page=captured&key=<?= $r_name ?>&jalur=<?= $r_jalur?>&gel=<?= $r_gel?>&periode=<?= $r_periode?>');
		// webcam.set_api_url('captured.php?key=<?= $r_name ?>&code=<?= $r_code ?>&jalur=<?= $r_jalur?>&gel=<?= $r_gel?>&period=<?= $r_periode?>');
		// webcam.set_api_url('captured.php&key=<?= $r_name ?>&code=<?= $r_code ?>');
		webcam.set_quality( 90 ); // JPEG quality (1 - 100)
		webcam.set_shutter_sound( false ); // play shutter click sound
	</script>
	
	<!-- Next, write the movie to the page at 320x240 -->
	<script language="JavaScript">
		document.write( webcam.get_html(220, 260) );
		// document.write( webcam.get_html(200, 150) );
	</script>
	
	<!-- Some buttons for controlling things -->
	<br/><form><br>
	<div align="left">
		<!--<input type=button value="Configure..." onClick="webcam.configure()">
		&nbsp;&nbsp;-->
		<input type=button value="Capture" onClick="webcam.freeze()">
		&nbsp;
		<input type=button value="Simpan" onClick="do_upload()">
		&nbsp;
		<input type=button value=" Reset " onClick="webcam.reset()">
		<input type="hidden" name="act" id="act">
	</form>
	</div>
	<!-- Code to handle the server response (see test.php) -->
	<script language="JavaScript">
		webcam.set_hook( 'onComplete', 'my_completion_handler' );
		
		function do_upload() {
			// upload to server
			//document.getElementById('upload_results').innerHTML = '<h1>Uploading...</h1>';
			document.getElementById('act').value="captured";
			webcam.upload();
		}
		
		function my_completion_handler(msg) {
			// extract URL out of PHP output
			if (msg.match(/(http\:\/\/\S+)/)) {
				//var image_url = RegExp.$1;
				var image_url=msg.split(">");
				// show JPEG image in page
				document.getElementById('upload_results').innerHTML = 
					' ';
				
				//alert(image_url[1]);
				// reset camera for another shot
				//webcam.reset();
				//window.opener.document.getElementById("imgfoto").waitload({mode: "unload"});
				window.opener.document.getElementById("imgfoto").src=image_url[1]+'?'+<?= mt_rand(1000,9999) ?>;
				//window.opener.location.reload();
				window.close();
			}
			else alert("PHP Error: " + msg);
		}
	</script>
	
	
	</tr>

	</table>

