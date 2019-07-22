<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;
	ini_set('max_execution_time', 100000);
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('presensi'));	
	require_once(Route::getModelPath('pegawai'));	
	require_once(Route::getUIPath('combo'));
		
	$r_key = CStr::removeSpecial($_POST['key']);
	
	// properti halaman
	$p_title = 'Proses Upload Data Presensi';
	$p_tbwidth = 900;
	$p_aktivitas = 'TIME';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 'pe_presensidet';
	$p_key = 'tglpresensi,idpegawai';
	
	$p_model = mPresensi;	
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'upload' and $c_edit) {	
		$r_file = $_FILES['upxls']['tmp_name'];
		
		// pakai excel 
		require_once($conf['includes_dir'].'PHPExcel/PHPExcel.php');
		
		$xls = PHPExcel_IOFactory::load($r_file);
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		$maxRow = $sheet->getHighestRow();
		$maxCol = $sheet->getHighestColumn();
		
		$headingsArray = $sheet->rangeToArray('A1:'.$maxCol.'1',null, true, true, true);
		$headingsArray = $headingsArray[1];
		$a_header = $sheet->rangeToArray('A'.'1:'.$maxCol.'1',null, true, true, true);
		
		$r = -1; $a_data = array();
		for ($row = 2; $row <= $maxRow; ++$row) {
			$a_val = array();
			$empty=0;
			foreach($a_header as $inc => $a_col){
				foreach($a_col as $col => $value){
					if ($col == 'A' or $col == 'B' or $col == 'C' or $col == 'D'){
						/*if($col == 'A' or $col == 'E')
							$val = $sheet->getCell($col.$row)->getValue();
						else if ($col == 'B' or $col == 'C'){
							$val = $sheet->getCell($col.$row)->getCalculatedValue();
							$val = PHPExcel_Style_NumberFormat::toFormattedString($val, "dd/mm/yyyy");
							$val = CStr::formatDate($val,'-','/');
						}else if ($col == 'D'){
							$val = $sheet->getCell($col.$row)->getCalculatedValue();
							$val = PHPExcel_Style_NumberFormat::toFormattedString($val, "H:i:s");						
						}else*/
							$val = $sheet->getCell($col.$row)->getValue();

						$a_val[$col] = $val;
					}
				}
			}
			$a_data[] = $a_val;
		}
		list($p_posterr,$p_postmsg) = array(false,"Mohon diperiksa kebenaran data upload sebelum simpan presensi");	
	}
	else if($r_act == 'simpan' and $c_edit) {
		$a_idpegawai = mPegawai::getAIDPegawai($conn);
		
		//$a_presensidate = array();
		//$a_presensidate = $p_model::getPresensiDate($conn, date("Y-m-d"));
		
		$a_number = $_POST['no'];
		if(count($a_number)>0){
			foreach($a_number as $inc => $number){
				$akun = $_POST['akun_'.$number];
				
				$record = array();
				$record['idpegawai'] = $a_idpegawai[$akun];
				$record['tglpresensi'] = CStr::formatDate($_POST['tglpresensi_'.$akun.'_'.$number]);
				$record['tglpemasukan'] = CStr::formatDate($_POST['tglentry_'.$akun.'_'.$number]);
				$record['jamdatang'] = CStr::cStrNull(str_replace(':','',$_POST['jamdatang_'.$akun.'_'.$number]));
				$record['jampulang'] = CStr::cStrNull(str_replace(':','',$_POST['jampulang_'.$akun.'_'.$number]));
							
				$colkey = 'tglpresensi,idpegawai';
				$key = $record['tglpresensi'].'|'.$record['idpegawai'];
				list($p_posterr,$p_postmsg) = $p_model::saveUploadPresensi($conn,$record,$key,true,'pe_presensiupload',$colkey);
			}
		}
	}
	$a_nama = $p_model::getNamaLengkap($conn);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/wizard.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title ?>
						</span>
					</div>
				</center>
				<br>
				<center>
				<?php require_once('inc_header.php') ?>
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
				<table cellspacing="0" cellpadding="4" width="<?= $p_tbwidth ?>" border="0">
					<tbody>
						<tr valign="top">
							<td><strong>File Excel</strong></td>
							<td><input class="ControlStyle" type="file" name="upxls" id="upxls" />&nbsp
							<input type="button" name="bset" id="bset" onClick="goUpload()" class="ControlStyle" value="Upload" />&nbsp;
							<input type="button" name="bdownload" id="bdownload" onClick="javascript:goDownload('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('template'); ?>','presensi')" target="_blank" class="ControlStyle" value="Download Template" />&nbsp;
							<input type="button" name="btransfer" id="btransfer" onClick="goSave()" class="ControlStyle" value="Simpan Presensi" />
							</td>
						</tr>	
					</tbody>
				</table>
				<br />
				
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/PERSON.png" onerror="loadDefaultActImg(this)"> <h1>Daftar Kehadiran</h1>
						</div>
					</div>
				</header>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr>
						<th width="50">No</th>
						<th>No. Akun</th>
						<th>Nama Pegawai</th>
						<th>Tgl. Presensi</th>
						<th>Tgl. Entry</th>
						<th>Jam Masuk</th>
						<th>Jam Pulang</th>
					</tr>
				<?php
						$i = 0;
						if (count($a_data) >0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $i ?><input type="hidden" name="no[]" value="<?= $i; ?>" /></td>
						<td align="center"><?= $row['A']; ?><input type="hidden" name="akun_<?= $i; ?>" value="<?= $row['A']; ?>" /></td>
						<td><?= $a_nama[$row['A']]; ?></td>
						<td align="center"><?= CStr::formatDateInd(CStr::formatDate($row['B']),false) ?><input type="hidden" name="tglpresensi_<?= $row['A'].'_'.$i; ?>" value="<?= $row['B']; ?>" /></td>	
						<td align="center"><?= CStr::formatDateInd(CStr::formatDate(date('d-m-Y')),false) ?><input type="hidden" name="tglentry_<?= $row['A'].'_'.$i; ?>" value="<?= date('Y-m-d'); ?>" /></td>	
						<td align="center"><?= $row['C'] ?><input type="hidden" name="jamdatang_<?= $row['A'].'_'.$i; ?>" value="<?= $row['C']; ?>" /></td>	
						<td align="center"><?= $row['D'] ?><input type="hidden" name="jampulang_<?= $row['A'].'_'.$i; ?>" value="<?= $row['D']; ?>" /></td>		
					</tr>
				<?php
						}}else{
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td colspan="12" align="center">Data tidak ditemukan</td>
					</tr>
				<? } ?>
				</table>
				<br />
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goUpload(){
	var file;
	file=$("#upxls").val();
	if(file==''){
		doHighlight(document.getElementById("upxls"));
		alert("File upload harus diisi");
		err=true;	
	}else{
		var set = confirm("Anda yakin untuk melakukan upload file ini ?");
		if (set){
			document.getElementById("act").value = 'upload';	
			goSubmit();
		}
	}
}

function goSave(){
	var set = confirm("Anda yakin untuk menyimpan kehadiran ini ?");
	if (set){
		document.getElementById("act").value = 'simpan';	
		goSubmit();	
	}
}

</script>
</body>
</html>