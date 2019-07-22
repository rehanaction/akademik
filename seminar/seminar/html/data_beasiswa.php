<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if (isset ($_GET['key']))
	$r_key = CStr::removeSpecial($_GET['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Beasiswa';
	$p_tbwidth = 500;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	
	$p_model = mBeasiswa;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodesumber', 'label' => 'Sumber', 'type' => 'S', 'option' => mSumberBeasiswa::getArray($conn));
	$a_input[] = array('kolom' => 'namabeasiswa', 'label' => 'Jenis', 'size' => 30, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'kodekategori', 'label' => 'Kategori', 'type' => 'S', 'option' => array('' => '') + mBeasiswa::getArrayKategori($conn));
	$a_input[] = array('kolom' => 'semesterawal', 'label' => 'Periode Awal', 'type' => 'S', 'option' => mCombo::semester(), 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'tahunawal', 'type' => 'S', 'option' => mCombo::tahun(), 'request' => 'TAHUN');
	$a_input[] = array('kolom' => 'semesterakhir', 'label' => 'Periode Akhir', 'type' => 'S', 'option' => mCombo::semester(), 'empty' => true);
	$a_input[] = array('kolom' => 'tahunakhir', 'type' => 'S', 'option' => mCombo::tahun(), 'empty' => true);
	$a_input[] = array('kolom' => 'tglawal', 'label' => 'Tanggal Awal', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglakhir', 'label' => 'Tanggal Akhir', 'type' => 'D');
	$a_input[] = array('kolom' => 'jumlahpenerima', 'label' => 'Jumlah Penerima', 'type' => 'NP', 'size' => 3, 'maxlength' => 3);
	$a_input[] = array('kolom' => 'jumlahperperiode', 'label' => 'Jumlah per Periode', 'type' => 'N', 'size' => 10, 'maxlength' => 10);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 5, 'cols' => 40);
	$a_input[] = array('kolom' => 'persyaratan', 'label' => 'Persyaratan', 'type' => 'A', 'rows' => 5, 'cols' => 40);
	
	// mengambil data pelengkap
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'nim', 'label' => 'NIM');
	$t_detail[] = array('kolom' => 'nama', 'label' => 'Nama Mahasiswa');
	
	$a_detail['penerima'] = array('key' => $p_model::getDetailInfo('penerima','key'), 'data' => $t_detail);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if($record['tahunawal'] == 'null' or $record['semesterawal'] == 'null')
			$record['periodeawal'] = 'null';
		else
			$record['periodeawal'] = $record['tahunawal'].$record['semesterawal'];
		
		if($record['tahunakhir'] == 'null' or $record['semesterakhir'] == 'null')
			$record['periodeakhir'] = 'null';
		else
			$record['periodeakhir'] = $record['tahunakhir'].$record['semesterakhir'];
		
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
		
		$record = array('idbeasiswa' => $r_key);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$r_detail.'_'.CStr::cEmChg($t_detail['nameid'],$t_detail['kolom'])];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}
		
		list($p_posterr,$p_postmsg) = $p_model::cekPenerima($conn,$record['nim'],$r_key);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	if(!empty($r_key)) {
		$rowd = array();
		$rowd += $p_model::getPenerima($conn,$r_key,'penerima',$post);
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
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/*****************/
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
						
						$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'kodesumber') ?>
						<?= Page::getDataTR($row,'namabeasiswa') ?>
						<?= Page::getDataTR($row,'kodekategori') ?>
						<?= Page::getDataTR($row,'semesterawal,tahunawal') ?>
						<?= Page::getDataTR($row,'semesterakhir,tahunakhir') ?>
						<?= Page::getDataTR($row,'tglawal') ?>
						<?= Page::getDataTR($row,'tglakhir') ?>
						<?= Page::getDataTR($row,'jumlahpenerima') ?>
						<?= Page::getDataTR($row,'jumlahperperiode') ?>
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'persyaratan') ?>
					</table>
					<? if(!empty($r_key)) { ?>
					<br>
					<?	/**********/
						/* DETAIL */
						/**********/
						
						$t_field = 'penerima';
						$t_colspan = count($a_detail[$t_field]['data'])+2;
						$t_dkey = $a_detail[$t_field]['key'];
						
						if(!is_array($t_dkey))
							$t_dkey = explode(',',$t_dkey);
					?>
					<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td colspan="<?= $t_colspan ?>" class="DataBG">Penerima Beasiswa</td>
						</tr>
						<tr>
							<th align="center" class="HeaderBG" width="30">No</th>
						<?	foreach($a_detail[$t_field]['data'] as $datakolom) { ?>
							<th align="center" class="HeaderBG"><?= $datakolom['label'] ?></th>
						<?	} ?>
							<th align="center" class="HeaderBG" width="30" id="edit" style="display:none">Aksi</th>
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
						<tr valign="top" class="<?= $rowstyle ?>">
							<td><?= $i ?></td>
						<?		foreach($a_detail[$t_field]['data'] as $datakolom) { ?>
							<td<?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?= uForm::getLabel($datakolom,$rowdd[$datakolom['kolom']]) ?></td>
						<?		} ?>
							<td id="edit" align="center" style="display:none">
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
								<?= UI::createTextBox('mahasiswa','','ControlStyle',65,65) ?>
								<input type="hidden" id="nim" name="penerima_nim">
							</td>
							<td align="center">
								<img title="Tambah Data" src="images/disk.png" onclick="goInsertDetail('<?= $t_field ?>')" style="cursor:pointer">
							</td>
						</tr>
					</table>
					<? } ?>
					</div>
				</center>
				
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
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
});

</script>
</body>
</html>