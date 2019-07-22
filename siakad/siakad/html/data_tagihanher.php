<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	/* $c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete']; */
	
	// include
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// koneksi database
	$connh = Query::connect('h2h');
	$connh->debug = $conn->debug;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Tagihan Formulir';
	$p_tbwidth = 600;
	$p_aktivitas = 'KULIAH';
	$p_listpage = Route::getListPage();
	
	$p_model = mTagihan;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// cek data
	if(!empty($r_key)) {
		$g_lunas = $p_model::getLunasHer($connh,$r_key);
		
		if($g_lunas) {
			$c_edit = false;
			$c_delete = false;
		}
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'semester', 'label' => 'Periode', 'type' => 'S', 'option' => mCombo::semester(), 'request' => 'SEMESTER');
	$a_input[] = array('kolom' => 'tahun', 'type' => 'S', 'option' => mCombo::tahun(), 'request' => 'TAHUN');
	$a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'maxlength' => 9, 'size' => 10);
	$a_input[] = array('kolom' => 'semester', 'label' => 'Smt', 'type' => 'N', 'maxlength' => 2, 'size' => 2);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'S', 'option' => $p_model::status($connh));
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Mulai', 'type' => 'D');
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Selesai', 'type' => 'D');
	$a_input[] = array('kolom' => 'billamount', 'label' => 'Jumlah', 'type' => 'N', 'maxlength' => 19, 'size' => 20);
	$a_input[] = array('kolom' => 'lunas', 'label' => 'Lunas', 'type' => 'R', 'option' => array('' => 'Belum Lunas', '1' => 'Lunas'));
	
	// ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['billcode'] = $p_model::billher;
		
		if(empty($r_key))
			$err = $p_model::insertCRecordFrm($connh,$a_input,$record);
		else
			$err = $p_model::updateCRecordFrm($connh,$a_input,$record,$r_key);
		
		if(!$err)
			$r_key = $record['billcode'].'|'.$record['periode'].'|'.$record['nim'];
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Penyimpanan '.strtolower($p_title).' '.($err ? 'gagal' : 'berhasil');
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		$err = $p_model::deleteFrm($connh,$r_key);
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Penghapusan '.strtolower($p_title).' '.($err ? 'gagal' : 'berhasil');
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEditHer($connh,$a_input,$r_key,$post);
	
	$r_mahasiswa = Page::getDataValue($row,'nim');
	if(!empty($r_mahasiswa))
		$r_namamahasiswa = $r_mahasiswa.' - '.$p_model::getNamaMahasiswa($connh,$r_mahasiswa);
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<?	$a_required = array('billcode','nim'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'billcode') ?>
						<?= Page::getDataTR($row,'semester,tahun') ?>
						<tr>
							<td class="LeftColumnBG">Mahasiswa</td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namamahasiswa,
									UI::createTextBox('mahasiswa',$r_namamahasiswa,'ControlStyle',60,60)."\n".
									'<input type="hidden" id="nim" name="nim" value="'.$r_mahasiswa.'">') ?>
							</td>
						</tr>
						<?= Page::getDataTR($row,'semester') ?>
						<?= Page::getDataTR($row,'status') ?>
						<?= Page::getDataTR($row,'tglmulai') ?>
						<?= Page::getDataTR($row,'tglselesai') ?>
						<?= Page::getDataTR($row,'billamount') ?>
						<?= Page::getDataTR($row,'lunas') ?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
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
	
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
});

</script>
</body>
</html>