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
	require_once(Route::getModelPath('lpj'));
	require_once(Route::getModelPath('proposal'));
	require_once(Route::getModelPath('mahasiswa'));	
	require_once(Route::getModelPath('pegawai'));	
	require_once(Route::getModelPath('organisasi'));	
	require_once(Route::getModelPath('periode'));	
	require_once(Route::getModelPath('program'));	
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
	$p_title = 'Data Pertanggungjawaban';
	$p_tbwidth = 500;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	
	$p_model = mLpj;
	$a_unit = mUnit::getComboUnit($conn);
	$a_organisasi = mOrganisasi::getArray($conn);
	$a_periode = mPeriode::getArray($conn);
	$a_proposal = mProposal::getArray($conn);

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeorganisasi', 'label' => 'Nama Organisasi','type'=>'S', 'option' => $a_organisasi);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode','type'=>'S', 'option' => $a_periode);
	$a_input[] = array('kolom' => 'idproposal', 'label' => 'Kegiatan','type'=>'S', 'option' => $a_proposal);
	$a_input[] = array('kolom' => 'tgllpj', 'label' => 'Tanggal LPJ','type'=>'D');
	$a_input[] = array('kolom' => 'jmltanggungjawab', 'label' => 'Jumlah Pertanggungjawaban','type'=>'N');
	$a_input[] = array('kolom' => 'jmlpemakaianormawa', 'label' => 'Jumlah Pemakaian','type'=>'N');
	$a_input[] = array('kolom' => 'jmlsisa', 'label' => 'Jumlah Sisa','type'=>'N');
	$a_input[] = array('kolom' => 'jmlpermintaan', 'label' => 'Jumlah permintaan','type'=>'N');
	$a_input[] = array('kolom' => 'filelpj', 'label' => 'File Pertanggungjawaban', 'type' => 'U', 'uptype' => $p_model::uptype, 'size' => 40);
	$a_input[] = array('kolom' => 'nrp', 'label' => 'Pelapor LPJ');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'maxlength' => 100);
	$a_input[] = array('kolom' => 'tempatkegiatan', 'label' => 'Tempat Kegiatan');
	$a_input[] = array('kolom' => 'nosurat', 'label' => 'Nomor Surat');
	$a_input[] = array('kolom' => 'status', 'label' => 'Valid', 'type' => 'C', 'option' => array('-1' => ''));

	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($_REQUEST['status']))
			$record['status'] = 0;
		
		if(empty($r_key)){
			$record['tglmasuk'] = date('Y-m-d');
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}else if($r_act == 'deletefile' and $c_edit){
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'filelpj');

	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$r_pelapor = Page::getDataValue($row,'nrp');
	if(!empty($r_pelapor))
		$r_namapelapor = $r_pelapor.' - '.mMahasiswa::getNama($conn,trim($r_pelapor),false);
		
	$r_keterangan = Page::getDataValue($row,'keterangan');
	
	//valid tidak bisa dihapus
	$valid = Page::getDataValue($row,'status');
	if(!empty($valid))
		$c_delete = false;
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
						
						$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<?= Page::getDataTR($row,'nosurat') ?>
							<?= Page::getDataTR($row,'kodeorganisasi') ?>
							<?= Page::getDataTR($row,'periode') ?>					
							<?= Page::getDataTR($row,'idproposal') ?>
							<?= Page::getDataTR($row,'tgllpj') ?>
							<?= Page::getDataTR($row,'jmltanggungjawab') ?>
							<?= Page::getDataTR($row,'jmlpemakaianormawa') ?>
							<?= Page::getDataTR($row,'jmlsisa') ?>
							<?= Page::getDataTR($row,'filelpj') ?>
							<tr>
								<td class="LeftColumnBG">Pelapor</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namapelapor,
										UI::createTextBox('pelapor',$r_pelapor,'ControlStyle',30,30)) ?>
									<input type="hidden" name="nrp" id="nrp" value="<?=$r_pelapor?>">
								</td>
							</tr>		
							<tr>
								<td class="LeftColumnBG">Keterangan</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_keterangan,
										UI::createTextArea('keterangan',$r_keterangan,'ControlStyle',2,30)) ?>
								</td>
							</tr>
							<?= Page::getDataTR($row,'status')  ?>
						</table>

						<span id="ngawur">
							
						</span>
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
	$("#pelapor").xautox({strpost: "f=acmahasiswa", targetid: "nrp"});

	// penguranagan jumlah
	$("#jmlpemakaianormawa").on('keypress', function (event) {
		var masuk = parseInt($('#jmltanggungjawab').val());
    	var keluar = parseInt($('#jmlpemakaianormawa').val());
		var sisa = masuk - keluar;
		
		$('#jmlsisa').val(sisa);
	});

});

</script>
</body>
</html>
