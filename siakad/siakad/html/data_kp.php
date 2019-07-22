<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kerjapraktek'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mKerjaPraktek;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(Akademik::isMhs()) {
		$r_key = '';
		$r_npm = Modul::getUserName();
	}
	else
		$r_npm = CStr::removeSpecial($_REQUEST['npm']);
	
	if(empty($r_key) and !empty($r_npm))
		$r_key = $p_model::getKPMahasiswa($conn,$r_npm);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Kuliah Kerja Nyata (KKN)';
	$p_tbwidth = 700;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert) {
		if(!empty($r_npm)) {
			$p_posterr = true;
			$p_fatalerr = true;
			$p_postmsg = 'Data KKN <strong>'.$r_npm.'</strong> belum dimasukkan';
		}
		else
			Route::navigate($p_listpage);
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'judulkp', 'label' => 'Judul', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'judulkpen', 'label' => 'Judul (EN)', 'maxlength' => 50, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'periode'); // agar masuk $row
	$a_input[] = array('kolom' => 'semester', 'label' => 'Periode', 'type' => 'S', 'option' => mCombo::semester(), 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'tahun', 'type' => 'S', 'option' => mCombo::tahun(), 'request' => 'TAHUN');
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl Mulai', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl Selesai', 'type' => 'D');
	$a_input[] = array('kolom' => 'namaperusahaan', 'label' => 'Lokasi', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'alamatperusahaan', 'label' => 'Alamat', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'contactpersonkp', 'label' => 'Pelatih', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'nippembimbingkp');
	$a_input[] = array('kolom' => 'nippengujikp');
	$a_input[] = array('kolom' => 'statuskp', 'label' => 'Status', 'type' => 'S', 'option' => $p_model::status());
	
	// mengambil data pelengkap
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'nim', 'label' => 'NIM', 'readonly' => true);
	$t_detail[] = array('kolom' => 'nama', 'label' => 'Nama Mahasiswa', 'readonly' => true);
	$t_detail[] = array('kolom' => 'nilaiperusahaan', 'label' => 'Pelatihan', 'align' => 'center', 'type' => 'N,2', 'size' => 3, 'maxlength' => 5, 'add' => 'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"');
	$t_detail[] = array('kolom' => 'nilaipembimbing', 'label' => 'Proses', 'align' => 'center', 'type' => 'N,2', 'size' => 3, 'maxlength' => 5, 'add' => 'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"');
	$t_detail[] = array('kolom' => 'nilaipenguji', 'label' => 'Laporan', 'align' => 'center', 'type' => 'N,2', 'size' => 3, 'maxlength' => 5, 'add' => 'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"');
	$t_detail[] = array('kolom' => 'nnumerik', 'label' => 'NA', 'align' => 'center', 'type' => 'N,2', 'size' => 3, 'maxlength' => 5, 'class' => 'ControlRead', 'add' => 'readonly');
	$t_detail[] = array('kolom' => 'nhuruf', 'label' => 'NH', 'align' => 'center', 'readonly' => true);
	
	$a_detail['peserta'] = array('key' => $p_model::getDetailInfo('peserta','key'), 'data' => $t_detail);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['periode'] = $record['tahun'].$record['semester'];
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		$record = array('idkp' => $r_key);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$r_detail.'_'.CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}
		
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
		if(!$p_posterr and $r_detail == 'peserta') {
			$r_subkey = $p_model::getKeyRow($record,$a_detail[$r_detail]['key']);
			
			list($p_posterr,$p_postmsg) = $p_model::saveUnsurNilaiMhs($conn,$r_subkey);
		}
		
		$ok = ($p_posterr ? false : true);
		
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'updatedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			if(empty($t_detail['readonly'])) {
				$t_value = $_POST[CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
				$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
			}
		}
		
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail,$r_subkey);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::saveUnsurNilaiMhs($conn,$r_subkey);
		
		$ok = ($p_posterr ? false : true);
		
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
		
		$ok = ($p_posterr ? false : true);
		
		$conn->CommitTrans($ok);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$r_pembimbing = Page::getDataValue($row,'nippembimbingkp');
	$r_penguji = Page::getDataValue($row,'nippengujikp');
	$r_periode = Page::getDataValue($row,'periode');
	
	if(!empty($r_pembimbing))
		$r_namapembimbing = $r_pembimbing.' - '.Akademik::getNamaPegawai($conn,$r_pembimbing);
	if(!empty($r_penguji))
		$r_namapenguji = $r_penguji.' - '.Akademik::getNamaPegawai($conn,$r_penguji);
	
	if(!empty($r_key)) {
		$rowd = array();
		$rowd += $p_model::getPeserta($conn,$r_key,'peserta',$post);
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
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
				<?	/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databutton.php');
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
						
						$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
						}
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'judulkp') ?>
						<?= Page::getDataTR($row,'judulkpen') ?>
						<?= Page::getDataTR($row,'semester,tahun') ?>
						<?= Page::getDataTR($row,'tglmulai') ?>
						<?= Page::getDataTR($row,'tglselesai') ?>
						<?= Page::getDataTR($row,'namaperusahaan') ?>
						<?= Page::getDataTR($row,'alamatperusahaan') ?>
						<?= Page::getDataTR($row,'contactpersonkp') ?>
						<tr>
							<td class="LeftColumnBG">DPL</td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namapembimbing,
									UI::createTextBox('pembimbing',$r_namapembimbing,'ControlStyle',65,65)."\n".
									'<input type="hidden" id="nippembimbingkp" name="nippembimbingkp" value="'.$r_pembimbing.'">') ?>
							</td>
						</tr>
						<? /* <tr>
							<td class="LeftColumnBG">Dosen Penguji</td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namapenguji,
									UI::createTextBox('penguji',$r_namapenguji,'ControlStyle',65,65)."\n".
									'<input type="hidden" id="nippengujikp" name="nippengujikp" value="'.$r_penguji.'">') ?>
							</td>
						</tr> */ ?>
						<?= Page::getDataTR($row,'statuskp') ?>
					</table>
					<? if(!empty($r_key)) { ?>
					<br>
					<?	/**********/
						/* DETAIL */
						/**********/
						
						$t_field = 'peserta';
						$t_colspan = count($a_detail[$t_field]['data'])+2;
						$t_dkey = $a_detail[$t_field]['key'];
						
						if(!is_array($t_dkey))
							$t_dkey = explode(',',$t_dkey);
							$t_data = $a_detail[$t_field]['data'];
					?>
					<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td colspan="<?= $t_colspan ?>" class="DataBG">Peserta dan Nilai KKN</td>
						</tr>
						<tr>
							<th align="center" class="HeaderBG" width="30">No</th>
							<th align="center" class="HeaderBG" width="70"><?= $t_data[0]['label'] ?></th>
							<th align="center" class="HeaderBG"><?= $t_data[1]['label'] ?></th>
							<th align="center" class="HeaderBG" width="50"><?= $t_data[2]['label'] ?></th>
							<th align="center" class="HeaderBG" width="50"><?= $t_data[3]['label'] ?></th>
							<th align="center" class="HeaderBG" width="50"><?= $t_data[4]['label'] ?></th>
							<th align="center" class="HeaderBG" width="50"><?= $t_data[5]['label'] ?></th>
							<th align="center" class="HeaderBG" width="50"><?= $t_data[6]['label'] ?></th>
							<th align="center" class="HeaderBG" width="50" id="edit" style="display:none">Aksi</th>
						</tr>
						<?	$i = 0;
							if(!empty($rowd[$t_field])) {
								foreach($rowd[$t_field] as $rowdd) {
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
									
									$t_keyrow = array();
									foreach($t_dkey as $t_key)
										$t_keyrow[] = $rowdd[trim($t_key)];
									
									$t_key = implode('|',$t_keyrow);
						?>
						<tr valign="top" class="<?= $rowstyle ?>" id="tr_detail">
							<td><?= $i ?></td>
						<?		foreach($a_detail[$t_field]['data'] as $datakolom) { ?>
							<td<?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>>
								<?= Page::getDataInputWrap(uForm::getLabel($datakolom,$rowdd[$datakolom['kolom']]),uForm::getInput($datakolom,$rowdd[$datakolom['kolom']])) ?>
							</td>
						<?		} ?>
							<td id="edit" align="center" style="display:none">
								<img id="<?= $t_key ?>" title="Update Data" src="images/disk.png" onclick="goUpdateDetail('<?= $t_field ?>',this)" style="cursor:pointer">
								<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('<?= $t_field ?>',this)" style="cursor:pointer">
							</td>
						</tr>
						<?		}
							}
							if($i == 0) { ?>
						<tr>
							<td align="center" colspan="<?= $t_colspan ?>">Data kosong</td>
						</tr>
						<?	} ?>
						<tr valign="top" class="LeftColumnBG" id="edit" style="display:none">
							<td>*</td>
							<td colspan="2">
								<?= UI::createTextBox('mahasiswa','','ControlStyle',50,40) ?>
								<input type="hidden" id="nim" name="peserta_nim">
							</td>
							<td align="center"><?= UI::createTextBox('peserta_nilaiperusahaan','','ControlStyle',5,3,true,'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"') ?></td>
							<td align="center"><?= UI::createTextBox('peserta_nilaipembimbing','','ControlStyle',5,3,true,'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"') ?></td>
							<td align="center"><?= UI::createTextBox('peserta_nilaipenguji','','ControlStyle',5,3,true,'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"') ?></td>
							<td align="center"><?= UI::createTextBox('peserta_nnumerik','','ControlRead',5,3,true,'readonly') ?></td>
							<td align="center">&nbsp;</td>
							<td align="center">
								<img title="Tambah Data" src="images/disk.png" onclick="goInsertDetail('<?= $t_field ?>')" style="cursor:pointer">
							</td>
						</tr>
					</table>
					<? } ?>
					</div>
				</center>
				
				<input type="hidden" id="periode" value="<?= $r_periode ?>">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	// autocomplete
	$("#pembimbing").xautox({strpost: "f=acpegawai", targetid: "nippembimbingkp"});
	$("#penguji").xautox({strpost: "f=acpegawai", targetid: "nippengujikp"});
	$("#mahasiswa").xautox({strpost: "f=acmahasiswakp", targetid: "nim", postid: "periode"});
});

</script>
</body>
</html>