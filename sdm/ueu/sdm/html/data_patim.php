<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));	
	require_once(Route::getUIPath('combo'));
		
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Pengaturan Tim Penilaian Subjektif';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	$p_detailpage = Route::getDetailPage();
	$p_listpage = Route::getListPage();
	$p_dbtable = 'pa_timsubyektif';
	$p_key = 'idtim';
	
	$p_model = mPa;
				
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'simpan' and $c_edit) {
		list($r_periode, $r_idpegawai)  = explode("|", $r_key);
		$record = array();
		$record['idpegawai'] = $r_idpegawai;
		$record['kodeperiode'] = $r_periode;
		$record['idpenilai'] = CStr::cStrNull($_POST['idpenilai']);
		$record['kodepajenis'] = CStr::cStrNull($_POST['kodepajenis']);
		$record['kodeformsubyektif'] = $p_model::getKodeFormSubj($conn, $r_periode, $record['kodepajenis']);
			
		list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
		
		if (!$p_posterr){
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {		
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$p_key);
	}
	else if($r_act == 'set' and $c_edit) {		
		$record = array();
		$record['kodekategori'] = CStr::cStrNull($_POST['kodekategori']);
		$where = 'kodeperiode,idpegawai';
		mPa::updateRecord($conn, $record, $r_key, false, 'pa_nilaiakhir',$where);
	}
	
	$a_info = $p_model::getInfoPenilaian($conn,$r_key);
	
	$a_data = array();
	$a_data = $p_model::getListTimPenilai($conn, $r_key);
	
	$a_required = array('pegawai');
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
		.bottomline td{
			border-bottom:1px solid #eaeaea;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<div class="DTitle" style="width:<?= $p_tbwidth ?>px;">
					<table border="0" cellspacing="0" align="left">
						<tr>
							<td valign="bottom" align="left" width="200">
								<table border="0" cellspacing="0" align="left">
									<tr>
										<td id="be_list" class="TDButton" onClick="location.href='<?= Route::navAddress($p_listpage); ?>'">
											<img src="images/list.png"> Daftar
										</td>
									</tr>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</center>	
			<br />
			<br />
			<br />
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title.' '.$a_info['keterangan'] ?>
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
							<td width="120"><strong>Nama</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td width="40%"><?= $a_info['namalengkap']?></td>
							<td width="150"><strong>Periode</strong></td>
							<td align="center" width="10"><strong>:</strong></td>
							<td><?= $a_info['namaperiode']?></td>
						</tr>	
						<tr>
							<td><strong>Unit Kerja</strong></td>
							<td><strong>:</strong></td>
							<td><?= $a_info['namaunit']; ?></td>
							<td><strong>Bobot Nilai Subjektif</strong></td>
							<td><strong>:</strong></td>
							<td><?= UI::createSelect('kodekategori',$p_model::getCKategori($conn),$a_info['kodekategori'],'ControlStyle',$c_edit,'',true); ?>&nbsp;
							<? if ($c_edit) { ?><input type="button" name="bkategori" id="bkategori" value="Set Kategori" onClick="goSet()" class="ControlStyle" /><? } ?></td>
						</tr>
					</tbody>
				</table>
				<br />
				<? if ($c_edit) {?>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas; ?>.png" onerror="loadDefaultActImg(this)"> <h1>Entry Tim Penilai</h1>
						</div>
					</div>
				</header>
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
				<table width="<?= $p_tbwidth-22; ?>" cellspacing="0" cellpadding="4" class="bottomline">
					<tbody>
						<? if (empty($a_info['kodekategori'])) {?>
						<tr>
							<td colspan="3" align="center">Kategori Penilaian Subjektif belum ditentukan. <br />Set Kategori terlebih dahulu untuk bisa menentukan Tim Penilai</td>
						</tr>
						<? }else{ ?>
						<tr>
							<td width="100">Penilai :</td>
							<td width="250" align="left"><?= UI::createTextBox('penilai',$r_namapenilai,'ControlStyle',60,60,$c_edit)?>
								<input type="hidden" name="idpenilai" id="idpenilai" value="<?= $r_idenilai; ?>" />
								<img id="imgnik_c" src="images/green.gif"><img id="imgnik_u" src="images/red.gif" style="display:none">
							</td>
							<td width="200">
								<?= UI::createSelect('kodepajenis',$p_model::getCJenisPenilaiKategori($conn, $a_info['kodekategori']), $r_jenis, 'ControlStyle', $c_edit); ?>
							</td>
						</tr>
						<tr>
							<td colspan="3" align="center"><input type="button" name="bsave" id="bsave" value="Tambah Tim" onClick="goSave()" /></td>
						</tr>
						<? } ?>
					</tbody>
				</table>
				</div> 
				<br />
				<? } ?>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/PERSON.png" onerror="loadDefaultActImg(this)"> <h1>Daftar Tim Penilai</h1>
						</div>
					</div>
				</header>
				<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<br />
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle">
						<tr>
							<th width="50">No</th>
							<th width="100">NIP</th>
							<th>Nama Penilai</th>
							<th>Jenis Penilai</th>
							<th width="50">Aksi</th>
						</tr>
						<?php
							$i = 0;
							if (count($a_data) >0){
								foreach($a_data as $row) {
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
						?>
						<tr valign="top" class="<?= $rowstyle ?>">
							<td align="right"><?= $i ?></td>
							<td align="center"><?= $row['nik']; ?></td>
							<td align="left"><?= $row['namapenilai'] ?></td>	
							<td align="left"><?= $row['namapajenis'] ?></td>	
							<td align="center">
								<? if ($row['kodepajenis'] != 'D' and $c_delete) { ?>
								<img src="images/delete.png" onClick="goRemove('<?= $row['idtim']; ?>')" style="cursor:pointer" />
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

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
		
	$("input[name='penilai']").xautox({strpost: "f=acnamapegawai", targetid: "idpenilai", imgchkid: "imgnik", imgavail: true});
});
	

function goSave(){		
	var required = "<?= @implode(',',$a_required) ?>";
	var pass = true;
	
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}
	
	if(pass) {
		var set = confirm("Anda yakin untuk menambahkan tim penilai " + $("#penilai").val() + " ini sebagai "+ $("#kodepajenis option:selected").text() + " ?");
		if (set){		
			document.getElementById("act").value = 'simpan';	
			goSubmit();	
		}
	}
}

function goRemove(key){
	var hapus = confirm("Anda yakin untuk menghapus tim penilai ini ?");
	if (hapus){	
		document.getElementById("subkey").value = key;
		document.getElementById("act").value = 'delete';	
		goSubmit();
	}
}

function goSet(){
	var set = confirm("Anda yakin set kategori tim penilai ke kategori "+ $("#kodekategori option:selected").text() + " ?");
	if (set){
		document.getElementById("act").value = 'set';
		goSubmit();
	}
}

</script>
</body>
</html>