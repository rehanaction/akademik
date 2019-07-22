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
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Informasi Prodi';
	$p_tbwidth = 640;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mUnit;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Unit', 'type' => 'S', 'option' => mCombo::unit($conn,false));
	$a_input[] = array('kolom' => 'nama_program_studi', 'label' => 'Nama Prodi', 'size' => 40, 'maxlength' => 40);
	$a_input[] = array('kolom' => 'kode_jenjang_studi', 'label' => 'Jenjang', 'type' => 'S', 'option' => mCombo::programPendidikan($conn), 'empty' => true);
	$a_input[] = array('kolom' => 'sks_lulus', 'label' => 'SKS Lulus', 'type' => 'NP', 'size' => 3, 'maxlength' => 3);
	$a_input[] = array('kolom' => 'kodenim', 'label' => 'Kode NIM (Prodi)', 'size' => 4, 'maxlength' => 4);
	$a_input[] = array('kolom' => 'epskodeprodi', 'label' => 'Kode Prodi (EPSBED)', 'size' => 10, 'maxlength' => 10, 'infoedit' => 'Kode Prodi Untuk Laporan Epsbed');
	$a_input[] = array('kolom' => 'gelar', 'label' => 'Gelar (Singkat)', 'size' => 5, 'maxlength' => 10);
	$a_input[] = array('kolom' => 'deskgelar', 'label' => 'Gelar (Panjang)', 'size' => 50, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'tgl_berdiri', 'label' => 'Tanggal Berdiri', 'type' => 'D');
	
	//fachruddin 
	$a_input[] = array('kolom' => 'no_sk_dikti', 'label' => 'No SK Dikti', 'size' => 40, 'maxlength' => 40);
	$a_input[] = array('kolom' => 'tgl_sk_dikti', 'label' => 'Tanggal SK Dikti', 'type' => 'D');
	$a_input[] = array('kolom' => 'tgl_akhir_sk_dikti', 'label' => 'Tanggal Akhir SK Dikti', 'type' => 'D');

	$a_input[] = array('kolom' => 'no_sk_ban', 'label' => 'No SK BAN PT', 'size' => 40, 'maxlength' => 40);
	$a_input[] = array('kolom' => 'tgl_sk_ban', 'label' => 'Tanggal SK BAN PT', 'type' => 'D');
	$a_input[] = array('kolom' => 'tgl_akhir_sk_ban', 'label' => 'Tanggal Akhir SK BAN PT', 'type' => 'D');
	//combobox
	$a_input[] = array('kolom' => 'kode_akreditasi', 'label' => 'Akreditasi', 'type' => 'S', 'option' => mCombo::akreditasi($conn,false) );
	$a_input[] = array('kolom' => 'frekuensi_kurikulum', 'label' => 'Frekuensi Kurikulum','type' => 'S', 'option' => mCombo::frekkurikulum($conn,false) );
	$a_input[] = array('kolom' => 'pelaksanaan_kurikulum', 'label' => 'Pelaksanaan Kurikulum','type' => 'S','option' => mCombo::pelkurikulum($conn,false) );
	//----------
	$a_input[] = array('kolom' => 'nidn', 'label' => 'NIDN', 'size' => 10, 'maxlength' => 10);
	$a_input[] = array('kolom' => 'hp_ketua', 'label' => 'No Hp Ketua Prodi', 'size' => 20, 'maxlength' => 20);
	$a_input[] = array('kolom' => 'telepon_kantor', 'label' => 'NoTelp Kantor', 'size' => 20, 'maxlength' => 20);
	$a_input[] = array('kolom' => 'fax', 'label' => 'FAX', 'size' => 20, 'maxlength' => 20);
	$a_input[] = array('kolom' => 'nama_operator', 'label' => 'Nama Operator', 'size' => 40, 'maxlength' => 40);
	$a_input[] = array('kolom' => 'hp_operator', 'label' => 'No Hp Operator', 'size' => 20, 'maxlength' => 20);
	$a_input[] = array('kolom' => 'batasip', 'label' => 'Batas IPS', 'type'=>'N,2','size' => 3, 'maxlength' => 4); 
	$a_input[] = array('kolom' => 'prosentasecmax', 'label' => 'Prosentase Cuti', 'size' => 5, 'maxlength' => 5);
	$a_input[] = array('kolom' => 'toeflmin', 'label' => 'Nilai Min Toefl', 'type'=>'N', 'size' => 5, 'maxlength' => 3);
	$a_input[] = array('kolom' => 'cutimax', 'label' => 'Cuti', 'type'=>'N', 'size' => 2, 'maxlength' => 2);
	$a_input[] = array('kolom' => 'email', 'label' => 'Email', 'size' => 50, 'maxlength' => 100);
	
 
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['nama_program_studi'] = mUnit::getNamaUnit($conn,$record['kodeunit']);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecordProdi($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecordProdi($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteProdi($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$sql = $p_model::dataQueryProdi($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$sql);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
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
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<?	$a_required = array('nama_program_studi'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'kodeunit') ?> 
						<?= Page::getDataTR($row,'kode_jenjang_studi') ?>
						<?= Page::getDataTR($row,'kodenim') ?>
						<?= Page::getDataTR($row,'epskodeprodi') ?>
						<?= Page::getDataTR($row,'gelar') ?>
						<?= Page::getDataTR($row,'deskgelar') ?>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Akademik</a></li>
						<li><a id="tablink" href="javascript:void(0)">Profil</a></li>
					</ul>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'sks_lulus') ?>
						<?= Page::getDataTR($row,'no_sk_dikti') ?>
						<?= Page::getDataTR($row,'tgl_sk_dikti') ?>
						<?= Page::getDataTR($row,'tgl_akhir_sk_dikti') ?>
						<?= Page::getDataTR($row,'no_sk_ban') ?>
						<?= Page::getDataTR($row,'tgl_sk_ban') ?>
						<?= Page::getDataTR($row,'tgl_akhir_sk_ban') ?>
						<?= Page::getDataTR($row,'batasip') ?>
						<?= Page::getDataTR($row,'prosentasecmax') ?>
						<?= Page::getDataTR($row,'toeflmin') ?>
						<?= Page::getDataTR($row,'cutimax') ?>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'tgl_berdiri') ?>
						<?= Page::getDataTR($row,'kode_akreditasi') ?> 
						<?= Page::getDataTR($row,'frekuensi_kurikulum') ?> 
						<?= Page::getDataTR($row,'pelaksanaan_kurikulum') ?>
						<?= Page::getDataTR($row,'nidn') ?> 
						<?= Page::getDataTR($row,'hp_ketua') ?> 
						<?= Page::getDataTR($row,'telepon_kantor') ?> 
						<?= Page::getDataTR($row,'fax') ?> 
						<?= Page::getDataTR($row,'nama_operator') ?>
						<?= Page::getDataTR($row,'hp_operator') ?> 
						<?= Page::getDataTR($row,'email') ?> 
						
					</table>
					</div>
				</div>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
