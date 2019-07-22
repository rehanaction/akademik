<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('program'));
	require_once(Route::getModelPath('mahasiswa'));	
	require_once(Route::getModelPath('pegawai'));	
	require_once(Route::getModelPath('organisasi'));	
	require_once(Route::getModelPath('periode'));	
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
	$p_title = 'Data Program';
	$p_tbwidth = 600;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	
	$p_model = mKegiatan;
	$a_unit = mUnit::getComboUnit($conn);
	$a_organisasi = mOrganisasi::getArray($conn);
	$a_periode = mPeriode::getArray($conn);
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeorganisasi', 'label' => 'Nama Organisasi','type'=>'S', 'option' => $a_organisasi);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode','type'=>'S', 'option' => $a_periode);
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tanggal Mulai','type'=>'D');
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tanggal Selesai','type'=>'D');
	$a_input[] = array('kolom' => 'nimketupel', 'label' => 'Ketua Pelaksana', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'narasumber', 'label' => 'Narasumber', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'kompetensins', 'label' => 'Kompetensi', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'jabatanns', 'label' => 'Jabatan', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'alamatns', 'label' => 'Alamat', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'telpns', 'label' => 'Telp', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'kontak1', 'label' => 'Contact Person 1', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'kontak2', 'label' => 'Contact Person 2', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'biayapeserta', 'label' => 'Biaya Peserta', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'danaorganisasi', 'label' => 'Dana Organisasi', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'subsidiuniv', 'label' => 'Subsidi Univ', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'subsidifakultas', 'label' => 'Subsidi Fakultas', 'size' => 50, 'maxlength' => 50);
	//$a_input[] = array('kolom' => 'subsidijurusan', 'label' => 'Subsidi Jurusan', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'danasponsor', 'label' => 'Dana Sponsor', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'tema', 'label' => 'Tema', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 5, 'cols' => 40);
	//$a_input[] = array('kolom' => 'isfree', 'label' => 'Bebas', 'type' => 'C', 'option' => array('-1' => ''));
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($_REQUEST['isfree']))
			$record['isfree'] = 0;
		
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
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$r_ketua = Page::getDataValue($row,'nimketupel');
	if(!empty($r_ketua))
		$r_namaketua = $r_ketua.' - '.mMahasiswa::getNama($conn,trim($r_ketua),false);
		
	$r_keterangan = Page::getDataValue($row,'keterangan');
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
							<?= Page::getDataTR($row,'kodeorganisasi') ?>
							<?= Page::getDataTR($row,'periode') ?>
							<?= Page::getDataTR($row,'namakegiatan') ?>
							<tr>
								<td class="LeftColumnBG">Ketua Pelaksana</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namaketua,
										UI::createTextBox('ketua',$r_namaketua,'ControlStyle',30,42)) ?>
									<input type="hidden" name="nimketupel" id="nimketupel" value="<?=$r_ketua?>">
								</td>
							</tr>							
							<?= Page::getDataTR($row,'tglmulai') ?>
							<?= Page::getDataTR($row,'tglselesai') ?>
							<?= Page::getDataTR($row,'narasumber') ?>
							<?= Page::getDataTR($row,'kompetensins') ?>
							<?= Page::getDataTR($row,'jabatanns') ?>
							<?= Page::getDataTR($row,'alamatns') ?>
							<?= Page::getDataTR($row,'telpns') ?>
							<?= Page::getDataTR($row,'email') ?>
							<?= Page::getDataTR($row,'kontak1') ?>
							<?= Page::getDataTR($row,'kontak2') ?>
							<?= Page::getDataTR($row,'biayapeserta') ?>
							<?= Page::getDataTR($row,'danaorganisasi') ?>
							<?= Page::getDataTR($row,'subsidiuniv') ?>
							<?= Page::getDataTR($row,'subsidifakultas') ?>
							<?= Page::getDataTR($row,'danasponsor') ?>
							<?= Page::getDataTR($row,'tema') ?>
							<?= Page::getDataTR($row,'keterangan') ?>
							<? /* Page::getDataTR($row,'isfree') */ ?>
						</table>
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
	$("#ketua").xautox({strpost: "f=acmahasiswa", targetid: "nimketupel"});
});

</script>
</body>
</html>
