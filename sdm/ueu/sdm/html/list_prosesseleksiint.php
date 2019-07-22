<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('rekrutmen'));	
		
	$r_key = CStr::removeSpecial($_POST['key']);
	
	// properti halaman
	$p_title = 'Proses Seleksi Penerimaan Pegawai';
	$p_tbwidth = 800;
	$p_aktivitas = 'STRUKTUR';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 're_kandidat';
	$where = 'idpegawai';
	
	$p_model = mRekrutmen;
	
	// mendapatkan data
	$a_infore = array();
	$a_infore = mRekrutmen::getInformasiRE($conn,$r_key);
	
	//$r_proses = CStr::removeSpecial($_POST['idproses']);
	$r_step = CStr::removeSpecial($_POST['step']);
	if (empty($r_step))
		$r_step = 1;
	
	//if (empty($r_proses))
	$r_proses = $p_model::getMekanisme($conn, $r_step, $r_key);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'simpan' and $c_edit) {	
		$a_id = $_POST['id'];
		$where = 'idrekrutmen,idpegawai';
		
		foreach($a_id as $id){
			$p_key = '';
			$record = array();
			$record['issetuju'] = CStr::cStrNull($_POST['lolos_'.$id]);
			$record['tglproses'] = CStr::formatDate($_POST['tglproses_'.$id]);
			$record['keterangan'] = CStr::cStrNull($_POST['keterangan_'.$id]);
			$p_key = $r_key.'|'.$id;
			
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$p_key,true,$p_dbtable,$where);
		}
	}
	
	$a_data = $p_model::getKandidat($conn,$r_key);
	
	$a_jenisrekrutmen = $p_model::jenisRekrutmen();
	
	$a_lolos = $p_model::statusLulus();
				
	if ($r_step == 1){
		$class_1 = "active-step";
	}
	else if ($r_step == 2){
		$class_1 = "completed-step";
		$class_2 = "active-step";
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/wizard.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
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
							<td width="120"><strong>Unit</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td width="40%"><?= $a_infore['namaunit']; ?></td>
							<td width="120"><strong>Jumlah Dibutuhkan</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_infore['jmldibutuhkan']; ?></td>
						</tr>
						<tr valign="top">
							<td><strong>Tgl. Rekrutmen</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= CStr::formatDateInd($a_infore['tglrekrutmen']); ?></td>
							<td><strong>Tgl. Mulai Kerja</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= CStr::formatDateInd($a_infore['tglterakhir']); ?></td>
						</tr>						
						<tr valign="top">
							<td><strong>Posisi</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_infore['posisikaryawan']; ?></td>
							<td><strong>Uraian Tugas</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_infore['tugaskaryawan']; ?></td>
						</tr>					
						<tr valign="top">
							<td><strong>Jenis Rekrutmen</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td colspan="4"><?= $a_jenisrekrutmen[$a_infore['jenisrekrutmen']]; ?></td>
						</tr>
					</tbody>
				</table>
				<br />
				<? if ($c_edit) {?>
				<table>
					<tr><td><input type="button" name="simpan" value="Simpan Proses" class="ControlStyle" style="cursor:pointer" onClick="goSave()"></td></tr>
				</table>
				<? } ?>
				<br />
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td colspan="9" align="center">Peserta Seleksi</td>
					</tr>
					<tr>
						<th><?= $a_infore['jenisrekrutmen'] == 'B' ? 'NO Pendaftar' : 'N I P';?></th>
						<th>Nama Kandidat</th>
						<th>Pendidikan</th>
						<th width="60px">Lolos</th>
						<th width="120px">Tgl. Proses</th>
						<th>Keterangan</th>
					</tr>
				<?php
						$i = 0;
						if (count($a_data) >0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							$idkandidat = $row['idpegawai'];
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $idkandidat;?><input type="hidden" name="id[]" value="<?= $idkandidat; ?>" /></td>
						<td><?= $row['namalengkap'] ?></td>
						<td><?= $row['namapendidikan'] ?></td>
						<td><?= UI::createRadio('lolos_'.$idkandidat,$a_lolos,$row['issetuju'],$c_edit); ?></td>
						<td><?= UI::createTextBox('tglproses_'.$idkandidat,CStr::formatDate($row['tglproses']),'ControlStyle',10,10,$c_edit) ?>
							<? if ($c_edit) {?>
							<img src="images/cal.png" id="tglproses_<?= $idkandidat; ?>_trg" style="cursor:pointer;" title="Pilih Proses">
							<script type="text/javascript">
							Calendar.setup({
								inputField     :    "tglproses_<?= $idkandidat; ?>",
								ifFormat       :    "%d-%m-%Y",
								button         :    "tglproses_<?= $idkandidat; ?>_trg",
								align          :    "Br",
								singleClick    :    true
							});
							</script>
							<? } ?>
						</td>
						<td><?= UI::createTextArea('keterangan_'.$idkandidat,$row['keterangan'],'ControlStyle',3,30,$c_edit) ?></td>
					</tr>
				<?php
						}}else{
				?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td colspan="12" align="center">Data tidak ditemukan</td>
					</tr>
				<? } ?>
				</table>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
				<input type="hidden" name="step" id="step" value="<?= $r_step; ?>">
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
	
function goSave() {
	document.getElementById("act").value = 'simpan';	
	goSubmit();
}




</script>
</body>
</html>