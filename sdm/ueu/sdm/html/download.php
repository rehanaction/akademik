<? 
	$auto = $_REQUEST['_auto'];
	
	$r_id = CStr::removeSpecial($_REQUEST['id']);
	$_ocd = base64_decode($_REQUEST['_ocd']);
	
	if ($_POST['disauto'])
		$auto =0;
		
	$r_aksi = $_POST['act'];
	if ($r_aksi == 'download'){
		require_once(Route::getModelPath('model'));
		
		$uploads_dir = $conf['docuploads_dir'];
			
		if ($_ocd == 'fileijazah'){
			$filename = mModel::getFileDown($conn,'pe_rwtpendidikan','sdm',$_ocd,"nourutrpen=$r_id");
		}else if ($_ocd == 'filetranskrip'){
			$filename = mModel::getFileDown($conn,'pe_rwtpendidikan','sdm',$_ocd,"nourutrpen=$r_id");
		}else if ($_ocd == 'filepangkat'){
			$filename = mModel::getFileDown($conn,'pe_rwtpangkat','sdm',$_ocd,"nourutrp=$r_id");
		}else if ($_ocd == 'filefungsional'){
			$filename = mModel::getFileDown($conn,'pe_rwtfungsional','sdm',$_ocd,"nourutjf=$r_id");
		}else if ($_ocd == 'filestruktural'){
			$filename = mModel::getFileDown($conn,'pe_rwtstruktural','sdm',$_ocd,"nourutjs=$r_id");
		}else if ($_ocd == 'filemutasi'){
			$filename = mModel::getFileDown($conn,'pe_rwtmutasi','sdm',$_ocd,"nourutmutasi=$r_id");
		}else if ($_ocd == 'filehubungankerja'){
			$filename = mModel::getFileDown($conn,'pe_rwthubungankerja','sdm',$_ocd,"nourutrwthub=$r_id");
		}else if ($_ocd == 'fileaktif'){
			$filename = mModel::getFileDown($conn,'pe_rwtaktif','sdm',$_ocd,"nourut=$r_id");
		}else if ($_ocd == 'filepensiun'){
			$filename = mModel::getFileDown($conn,'pe_pensiun','sdm',$_ocd,"idpegawai=$r_id");
		}else if ($_ocd == 'filestudilanjut'){
			$filename = mModel::getFileDown($conn,'pe_tugasbelajar','sdm',$_ocd,"nouruttugas=$r_id");
		}else if ($_ocd == 'fileorganisasi'){
			$filename = mModel::getFileDown($conn,'pe_organisasi','sdm',$_ocd,"nourutpo=$r_id");
		}else if ($_ocd == 'filepiagam'){
			$filename = mModel::getFileDown($conn,'pe_piagam','sdm',$_ocd,"nourutpiagam=$r_id");
		}else if ($_ocd == 'filesertifikasi'){
			$filename = mModel::getFileDown($conn,'pe_sertifikasi','sdm',$_ocd,"idsertifikasi=$r_id");
		}else if ($_ocd == 'filepenelitian'){
			$filename = mModel::getFileDown($conn,'pe_penelitian','sdm',$_ocd,"idpenelitian=$r_id");
		}else if ($_ocd == 'fileproposal'){
			$filename = mModel::getFileDown($conn,'pe_penelitian','sdm',$_ocd,"idpenelitian=$r_id");
		}else if ($_ocd == 'filepublikasi'){
			$filename = mModel::getFileDown($conn,'pe_penelitian','sdm',$_ocd,"idpenelitian=$r_id");
		}else if ($_ocd == 'filepkm'){
			$filename = mModel::getFileDown($conn,'pe_pkm','sdm',$_ocd,"idpkm=$r_id");
		}else if ($_ocd == 'filepenghargaan'){
			$filename = mModel::getFileDown($conn,'pe_penghargaan','sdm',$_ocd,"nourutpenghargaan=$r_id");
		}else if ($_ocd == 'filekedinasan'){
			$filename = mModel::getFileDown($conn,'pe_rwtdinas','sdm',$_ocd,"nodinas=$r_id");
		}else if ($_ocd == 'filevalidasidinas'){
			$filename = mModel::getFileDown($conn,'pe_rwtdinas','sdm',$_ocd,"nodinas=$r_id");
		}else if ($_ocd == 'filebidangsatua'){
			$filename = mModel::getFileDown($conn,'ak_bidang1a','sdm',$_ocd,"nobidangia=$r_id");
		}else if ($_ocd == 'filebidangsatub'){
			$filename = mModel::getFileDown($conn,'ak_bidang1b','sdm',$_ocd,"nobidangib=$r_id");
		}else if ($_ocd == 'filebidangdua'){
			$filename = mModel::getFileDown($conn,'ak_bidang2','sdm',$_ocd,"nobidangii=$r_id");
		}else if ($_ocd == 'filebidangtiga'){
			$filename = mModel::getFileDown($conn,'ak_bidang3','sdm',$_ocd,"nobidangiii=$r_id");
		}else if ($_ocd == 'filebidangempat'){
			$filename = mModel::getFileDown($conn,'ak_bidang4','sdm',$_ocd,"nobidangiv=$r_id");
		}else if ($_ocd == 'filelamaran'){
			$filename = mModel::getFileDown($conn,'re_calon','sdm',$_ocd,"nopendaftar='$r_id'");
		}else if ($_ocd == 'filecv'){
			$filename = mModel::getFileDown($conn,'re_calon','sdm',$_ocd,"nopendaftar='$r_id'");
		}else if ($_ocd == 'fileijazahpelamar'){
			$filename = mModel::getFileDown($conn,'re_pendpelamar','sdm',$_ocd,"nopendpelamar='$r_id'");
		}else if ($_ocd == 'filetranskrippelamar'){
			$filename = mModel::getFileDown($conn,'re_pendpelamar','sdm',$_ocd,"nopendpelamar='$r_id'");
		}else if ($_ocd == 'ug'){
			if ($r_id == 'admin'){
				$filename = 'Quick Guide Administrator SIM HRM.pdf';
				$r_id = 'QG-A';
			}else if ($r_id == 'admingaji'){
				$filename = 'Quick Guide Admin Payroll SIM HRM.pdf';
				$r_id = 'QG-GA';
			}else if ($r_id == 'portal'){
				$filename = 'User Guide Portal Rekrutmen.pdf';
				$r_id = 'QG-PR';
			}else{
				$filename = 'Quick Guide User Pegawai.pdf';
				$r_id = 'QG-P';
			}
				
			$uploads_dir = $conf['uploads_dir'].'m4nu_al';
			$_ocd = '';
		}else if ($_ocd == 'template'){
			if ($r_id == 'presensi'){
				$filename = 'absensi.xls';
				$r_id = 'absensi';
			}
				
			$uploads_dir = $conf['uploads_dir'].'template';
			$_ocd = '';
		}
		else
			exit();
				
		$fullPath = $uploads_dir.$_ocd.'/'.$r_id;
		
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
				<p align="center">Jika ada masalah dalam download coba manual <a href="#" onClick="goDownload()" title="Download">disini</a></p>
				<br><br>
				<? }else{ ?>
				<p align="center" id="auto">Proses download akan berjalan dalam <font color="red"><span class="countdown"></span></font> lagi.</p>
				<p align="center" id="manual" style="display:none">Jika ada masalah dalam download coba manual <a href="#" onClick="goDownload()" title="Download">disini</a></p>
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

function goDownload(){
	document.getElementById('act').value="download";
	document.getElementById('disauto').value="1";
	document.getElementById('formdown').submit();
}
</script>
</html>