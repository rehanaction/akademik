<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
		
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('imporpendaftar'));
	require_once(Route::getModelPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalur'],'JALUR');
	
	// combo periode
	$a_periode = mCombo::periode($conn);
	if(empty($r_periode))
		$r_periode = key($a_periode);
	
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');
	
	// combo jalur
	$a_jalur = mCombo::jalur($conn);
	if(empty($r_jalur))
		$r_jalur = key($a_jalur);
	
	$l_jalur = UI::createSelect('jalur',$a_jalur,$r_jalur,'ControlStyle',true,'onchange="goSubmit()"');
	
	// properti halaman
	$p_title = 'Impor Pendaftar';
	$p_tbwidth = 700;
	$p_aktivitas = 'DAFTAR';
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'upxls' and $c_edit) {	
		//print_r($_FILES['xls']);die();
		if(empty($_FILES['xls']['name'])){
			$p_posterr=true;
			$p_postmsg = 'Mohon Pilih File';
		}else if($_FILES['xls']['type']!='application/xls' and $_FILES['xls']['type']!='application/vnd.ms-excel'){
			$p_posterr=true;
			$p_postmsg = 'Data Harus format xls, agar lebih pasti Download template yang disediakan';
		}else{
			$r_file = $_FILES['xls']['tmp_name'];
			$import = mImporPendaftar::uploadFile($conn,$r_periode,$r_jalur,$r_file);
			if($import)
				$p_postmsg = 'Impor data dari format excel berhasil';
			else{
				$p_posterr=true;
				$p_postmsg = 'Impor data dari format excel gagal';
			}
		}
	}
	
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="4" cellspacing="0" align="center">
							<tr>
								<td width="50" style="white-space:nowrap"><strong>Periode </strong></td>
								<td><strong> : </strong><?= $l_periode ?></td>		
							</tr>
							<tr>		
								<td style="white-space:nowrap"><strong>Jalur </strong></td>
								<td><strong> : </strong><?= $l_jalur ?></td>		
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
				
				<div class="Break"></div>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="3">Impor Data Pendaftar dari Format Excel</td>
					</tr>
					<tr class="NoHover NoGrid">		
						<td width="50"> &nbsp; <strong>Upload </strong></td>
						<td width="15" align="center"><strong>:</strong></td>
						<td>
							<input type="file" name="xls" id="xls" size="30" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpXLS()">
							<u class="ULink" onclick="goDownXLS()">Download Template Excel...</u>
						</td>		
					</tr>
					
				</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

function goUpXLS() {
	var upload = confirm("Apakah anda yakin akan mengupload data dari format excel?");
	if(upload) {
		document.getElementById("act").value = "upxls";
		goSubmit();
	}
}

function goDownXLS() {
	location.href = '<?=$conf['download_dir']?>template_pendaftar.xls';
}

</script>

</body>
</html>
