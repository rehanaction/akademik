<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
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
	$r_tahun = CStr::removeSpecial($_POST['tahun']);
	
	$p_model = mPresensi;
	
	if (empty($r_tahun))
		$r_tahun = $p_model::getLastTahunShift($conn);
	
	// properti halaman
	$p_title = 'Pengaturan Jadwal Shift Pegawai';
	$p_tbwidth = 700;
	$p_aktivitas = 'TIME';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 'pe_rwtharikerja';
	$p_key = 'idpegawai,kodekelkerja,tglberlaku';
	
	$a_info = $p_model::infoShift($conn,$r_key);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'simpan' and $c_edit) {		
		$a_idpegawai = $_POST['idpegawai'];
		foreach($a_idpegawai as $inc => $idpegawai){
			$record = array();
			$record['idpegawai'] = $idpegawai;
			$record['tglberlaku'] = CStr::formatDate($_POST['tglberlaku']);
			$record['kodekelkerja'] = $r_key;
			$record['isaktif'] = 'Y';
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
		}
	}
	else if($r_act == 'simpanaktif' and $c_edit) {		
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		$is = CStr::cAlphaNum($_POST['check'.$r_subkey]);
		if(!empty($is))
			$record['isaktif'] = 'Y';
		else
			$record['isaktif'] = 'null';
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,false,$p_dbtable,$p_key);
	}
	else if($r_act == 'delete' and $c_delete) {		
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$p_key);
	}
	
	$a_jamhadir = $p_model::getCJamHadir($conn);
	$a_group = $p_model::listRJadwalGroup($conn, $r_key, $r_tahun);
	
	$a_data = $p_model::listRJadwalDate($conn, $r_key, $r_tahun);
	
	$a_tahunpresensi = $p_model::getCTahunShift($conn);
	$c_tahunpresensi = UI::createSelect('tahun',$a_tahunpresensi,$r_tahun,'ControlStyle',$c_edit,'onChange="goSubmit()"');
		
	$a_required = array('tglberlaku');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/wizard.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		.labeloff {
			background-color: red;
			border-radius: 3px 3px 3px 3px;
			color: #FFFFFF;
			display: block;
			float: left;
			font-size: 11.05px;
			font-weight: bold;
			margin: 0 2px 2px 0;
			padding: 2px 4px 3px;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		}
	</style>
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
							&nbsp;<?= $p_title  ?>
						</span>
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
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas; ?>.png" onerror="loadDefaultActImg(this)"> <h1>Nama Shift : <?= $a_info['keterangan']?></h1>
						</div>
					</div>
				</header>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tbody>
						<tr>
							<th>Senin</th><th>Selasa</th><th>Rabu</th><th>Kamis</th><th>Jumat</th><th>Sabtu</th><th>Minggu</th>
						</tr>
						<tr>
							<td align="center"><?= (empty($a_info['senin'])) ? '<div class="labeloff">OFF</div>' : $a_jamhadir[$a_info['senin']]; ?></td>
							<td align="center"><?= (empty($a_info['selasa'])) ? '<div class="labeloff">OFF</div>' : $a_jamhadir[$a_info['selasa']]; ?></td>
							<td align="center"><?= (empty($a_info['rabu'])) ? '<div class="labeloff">OFF</div>' : $a_jamhadir[$a_info['rabu']]; ?></td>
							<td align="center"><?= (empty($a_info['kamis'])) ? '<div class="labeloff">OFF</div>' : $a_jamhadir[$a_info['kamis']]; ?></td>
							<td align="center"><?= (empty($a_info['jumat'])) ? '<div class="labeloff">OFF</div>' : $a_jamhadir[$a_info['jumat']]; ?></td>
							<td align="center"><?= (empty($a_info['sabtu'])) ? '<div class="labeloff">OFF</div>' : $a_jamhadir[$a_info['sabtu']]; ?></td>
							<td align="center"><?= (empty($a_info['minggu'])) ? '<div class="labeloff">OFF</div>' : $a_jamhadir[$a_info['minggu']]; ?></td>
						</tr>
					</tbody>
				</table>
				<br />
				<? if ($c_edit) {?>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas; ?>.png" onerror="loadDefaultActImg(this)"> <h1>Entry Jadwal</h1>
						</div>
					</div>
				</header>
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
				<table width="<?= $p_tbwidth-22; ?>" cellspacing="0" cellpadding="4">
					<tbody>
						<tr>
							<td width="200px"><b>Tanggal Berlaku Shift * </b></td>
							<td><b>:</b></td>
							<td>
								<?= UI::createTextBox('tglberlaku','','ControlStyle',10,10,$c_edit); ?>
								<? if ($c_edit) {?>
								<img src="images/cal.png" id="tglberlaku_trg" style="cursor:pointer;" title="Pilih Tanggal Awal">
								<script type="text/javascript">
								Calendar.setup({
									inputField     :    "tglberlaku",
									ifFormat       :    "%d-%m-%Y",
									button         :    "tglberlaku_trg",
									align          :    "Br",
									singleClick    :    true
								});
								</script>
								<? } ?>
							</td>
						</tr>
						<tr>
							<td valign="top"><b>Pegawai</b></td>
							<td valign="top"><b>:</b></td>
							<td>
								<table width="100%" cellpadding="2" cellspacing="0">
									<tr>
										<td style="border:1px solid white">
											<?= UI::createTextBox('pegawai[]','','ControlStyle',60,60,$c_edit)?>
											<input type="hidden" name="idpegawai[]" id="idpegawai"/>
											<img id="imgnik_c" src="images/green.gif"><img id="imgnik_u" src="images/red.gif" style="display:none">&nbsp;
										</td>
									</tr>
									<tr id="tr_tambah">
										<td colspan="2">&nbsp;</td>
									</tr>
									<tr>
										<td>
											<input type="button" name="badd" id="badd" value="Tambah Pegawai" onClick="goAdd()" />&nbsp;
											<input type="button" name="bsave" id="bsave" value="Simpan Jadwal" onClick="goSave()" />
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</tbody>
				</table>
				</div>
				<? } ?>
				<br />
				
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/PERSON.png" onerror="loadDefaultActImg(this)"> <h1>Jadwal Kehadiran Tahun <?= $c_tahunpresensi; ?></h1>
						</div>
					</div>
				</header>
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
				<? if (count($a_group) > 0){ 
					foreach($a_group as $col){

				?>
					<div class="ViewTitle" style="width:<?= $p_tbwidth-22 ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;Tanggal Berlaku Shift: <?= CStr::formatDateInd($col['tglberlaku']); ?>
						</span>
					</div>					
					<br />
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle">
						<tr>
							<th width="50">No</th>
							<th width="100">NIP</th>
							<th>Nama</th>
							<th width="50">Aktif?</th>
							<th width="50">Aksi</th>
						</tr>
					<?php
							$i = 0;
							if (count($a_data[str_replace('-','',$col['tglberlaku'])]) >0){
							foreach($a_data[str_replace('-','',$col['tglberlaku'])] as $row) {

								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
						<tr valign="top" class="<?= $rowstyle ?>">
							<td align="right"><?= $i ?></td>
							<td align="center"><?= $row['nip']; ?></td>
							<td align="left"><?= $row['namalengkap'] ?></td>
							<?
								$checked='';
								if($row['isaktif']=='Y')
									$checked='checked';
							?>
							<? if($c_edit) { ?>
							<td align="center">
								<input type="checkbox" id="check" name="check<?= $row['idpegawai'].'|'.$row['kodekelkerja'].'|'.$row['tglberlaku']; ?>" title="Cek untuk validasi per item" <?= $checked?>>
							</td>
							<?	} ?>
							<td align="center">
								<? if ($c_edit) {?>
								<img src="images/disk.png" onClick="goSaveAktif('<?= $row['idpegawai'].'|'.$row['kodekelkerja'].'|'.$row['tglberlaku']; ?>')" style="cursor:pointer" />
								<? } ?>
								<? if ($c_delete) {?>
								<img src="images/delete.png" onClick="goRemove('<?= $row['idpegawai'].'|'.$row['kodekelkerja'].'|'.$row['tglberlaku']; ?>')" style="cursor:pointer" />
								<? } ?>
							</td>
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
				<? }} ?>
				</div>
				<br />
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key; ?>">
				<input type="hidden" name="subkey" id="subkey">
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<? if (!empty($r_key)) { ?>
<table style="display:none">
	<tbody id="tb_template">
	<tr><td style="border:1px solid white">
		<?= UI::createTextBox('pegawai[]','','ControlStyle',60,60) ?>
		<input type="hidden" name="idpegawai[]" id="idpegawai">
		<img id="imgnik_c" src="images/green.gif" style="display:none"><img id="imgnik_u" src="images/red.gif">&nbsp;
	</td></tr>
	</tbody>
</table>
<? } ?>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
		
	$("[id='imgday']").balloon();
	$("input[name='pegawai[]']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
});
	
function goAdd(){	
	var newtr = $($("#tb_template").html()).insertBefore("#tr_tambah");
	
	newtr.find("input[name='pegawai[]']").xautox({strpost: "f=acnamapegawai", targetid: "idpegawai", imgchkid: "imgnik", imgavail: true});
}

function goSave(){		
	var required = "<?= @implode(',',$a_required) ?>";
	var pass = true;
	
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		var set = confirm("Anda yakin untuk menyimpan jadwal ini ?");
		if (set){		
			document.getElementById("act").value = 'simpan';	
			goSubmit();	
		}
	}
}

function goRemove(key){
	var hapus = confirm("Anda yakin untuk menghapus jadwal ini ?");
	if (hapus){	
		document.getElementById("subkey").value = key;
		document.getElementById("act").value = 'delete';	
		goSubmit();
	}
}

function goSaveAktif(key){
	document.getElementById("subkey").value = key;
	document.getElementById("act").value = 'simpanaktif';	
	goSubmit();
}

</script>
</body>
</html>