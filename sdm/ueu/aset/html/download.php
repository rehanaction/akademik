<? 
	$auto = $_REQUEST['_auto'];
	
	$r_id = CStr::removeSpecial($_REQUEST['id']);
	$_ocd = base64_decode($_REQUEST['_ocd']);
	
	if ($_POST['disauto'])
		$auto =0;
		
	$r_aksi = $_POST['act'];
	if ($r_aksi == 'download'){
		require_once(Route::getModelPath('model'));
		echo $_ocd;
		$uploads_dir = $conf['docupload_dir'];
			echo $uploads_dir;
		if ($_ocd == 'ug'){
			echo $r_id;
			if ($r_id == 'admin'){
				$filename = 'UG-A.pdf';
				$r_id = 'UG-A';
			}else if ($r_id == 'unit'){
                $filename = 'UG-B.pdf';
                $r_id = 'UG-B';
            }else {
				$filename = 'QG-HP.pdf';
				$r_id = 'QG-HP';
			}
				
			$uploads_dir = $conf['upload_dir'].'manu_al';
			echo $uploads_dir;
			$_ocd = '';
		}
		else
			exit();
				
		$fullPath = $uploads_dir.$_ocd.'/'.$r_id;
		echo $fullPath;
		if (is_readable($fullPath)){
			$handle = fopen($fullPath,'rb');
			$contents = fread($handle,filesize($fullPath));
			fclose($handle);
			
			$finfo = finfo_open(FILEINFO_MIME_TYPE);
			$ext = finfo_file($finfo,$fullPath);
			finfo_close($finfo);
			
			ob_clean();
			
			header("Content-Type: $ext");
			header('Content-Disposition: attachment; filename="'.$filename.'"');
			
			echo $contents;
		}else
			$msg = 'Maaf, File tidak ditemukan';
	}
?>
<!DOCTYPE html>
<html>
<head>
	<title>Download</title>
	<meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<style>
		body{
			background:#F1F1F1;
			font-family:Helvetica,Trebuchet MS,Verdana;
			font-size:14px;
			color:#484848;
		}
		.wrap {
			background:#fff;
			width: 502px;
			height: 302px;
			margin: 10% 35%;
			border-radius: 5px;
			-webkit-box-shadow: 0px 1px 10px rgba(0,0,0,0.5);
			-moz-box-shadow: 0px 1px 10px rgba(0,0,0,0.5);
			box-shadow: 0px 1px 10px rgba(0,0,0,0.5);
		}

		.wrap .outside {
			width: 480px;
			position: absolute;
			height: 280px;
			margin: 10px;
			border-radius: 10px;
			border: 2px dashed #aaa;
			box-shadow: 0 0 0 1px #f5f5f5;
		}

		.wrap .inside {
			width: 460px;
			position: absolute;
			top: 0px;
			left: 0px;
			height: 260px;
			border-radius: 10px;
			box-shadow: 0 0 0 1px #f5f5f5;
			padding:10px;
		}

		p.title {
			margin-top: 30px;
			font-size: 50px;
			font-family: Georgia;
			color: #A00000;
			text-shadow: 1px 1px 2px #fff;
			text-align: center;
		}
		
		.icon{
			position:relative;
			bottom:110px;
			right:20px;
		}
	</style>
</head>
<body>
	<div class="wrap">
		<div class="outside">
			<div class="inside">
				<p class="title">Download</p>
				<? if (!empty($msg)) {?>
				<p align="center"><?= $msg;?></p>
				<p align="center">Jika ada masalah dalam download coba manual <a href="#" onClick="goDownloadUG()" title="Download">disini</a></p>
				<br><br>
				<? }else{ ?>
				<p align="center" id="auto">Proses download akan berjalan dalam <font color="red"><span class="countdown"></span></font> lagi.</p>
				<p align="center" id="manual" style="display:none">Jika ada masalah dalam download coba manual <a href="#" onClick="goDownloadUG()" title="Download">disini</a></p>
				<? } ?>
			</div>
			<div class="icon">
				<img src="images/download_page.png" alt="download-icon" height="140" />
			</div>
		</div>
	</div>
	<form name="frmdown" id="formdown" method="post">
		<input type="hidden" name="act" id="act">
		<input type="hidden" name="disauto" id="disauto">
	</form>
</body>
<script type="text/javascript">
<? if ($auto) { ?>
$(function(){
  var count = 4;
  countdown = setInterval(function(){
    $(".countdown").html(count + " detik!");
    if (count == 0) {
		document.getElementById('act').value="download";
		document.getElementById('disauto').value="1";
		document.getElementById('formdown').submit();
		clearInterval(countdown);
		$("#auto").hide();
		$("#manual").show();
    }else
		count--;
  }, 1000);
});
<? } ?>

function goDownloadUG(){
	document.getElementById('act').value="download";
	document.getElementById('disauto').value="1";
	document.getElementById('formdown').submit();
}
</script>
</html>