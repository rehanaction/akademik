<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_saldoawal');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$r_role = Modul::getRole();
	if($r_role == 'A')
	    $conn->debug = true;
	    
	// include
	require_once(Route::getModelPath('saldoawal'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Saldo Awal';
	$p_tbwidth = 500;
	$p_aktivitas = 'saldo awal';
	$p_listpage = Route::getListPage();
	
	$p_model = mSaldoAwal;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$isro = false;
	$a_input = array();
	$a_input[] = array('kolom' => 'idsaldoawal', 'label' => 'ID. Saldo Awal','default' => 'Otomatis', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idlokasi', 'type' => 'H');
	$a_input[] = array('kolom' => 'unit', 'label' => 'Unit', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idunit', 'type' => 'H');
	$a_input[] = array('kolom' => 'pegawai', 'label' => 'Pemakai', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');
	$a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idbarang', 'type' => 'H');
	//$a_input[] = array('kolom' => 'harga', 'label' => 'Harga', 'type' => 'N,2', 'maxlength' => 20, 'size' => 25);
	$a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'type' => 'S', 'option' => mCombo::merk($conn,false), 'add' => 'style="width:150px"', 'empty' => true);
	$a_input[] = array('kolom' => 'idkondisi', 'label' => 'Kondisi', 'type' => 'S', 'option' => mCombo::kondisi($conn,false), 'add' => 'style="width:150px"', 'empty' => true);
	$a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg,$r_key) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
                        <?= Page::getDataTR($row,'idsaldoawal') ?>
	                    <tr>
		                    <td class="LeftColumnBG"><?= Page::getDataLabel($row,'lokasi') ?></td>
		                    <td class="RightColumnBG">
		                        <?= Page::getDataInput($row,'lokasi') ?>
		                        <?= Page::getDataInput($row,'idlokasi') ?>
		                    </td>
	                    </tr>                        
	                    <tr>
		                    <td class="LeftColumnBG"><?= Page::getDataLabel($row,'unit') ?></td>
		                    <td class="RightColumnBG">
		                        <?= Page::getDataInput($row,'unit') ?>
		                        <?= Page::getDataInput($row,'idunit') ?>
		                    </td>
	                    </tr>                        
	                    <tr>
		                    <td class="LeftColumnBG"><?= Page::getDataLabel($row,'pegawai') ?></td>
		                    <td class="RightColumnBG">
		                        <?= Page::getDataInput($row,'pegawai') ?>
		                        <?= Page::getDataInput($row,'idpegawai') ?>
		                    </td>
	                    </tr>                        
	                    <tr>
		                    <td class="LeftColumnBG"><?= Page::getDataLabel($row,'barang') ?></td>
		                    <td class="RightColumnBG">
		                        <?= Page::getDataInput($row,'barang') ?>
		                        <?= Page::getDataInput($row,'idbarang') ?>
		                    </td>
	                    </tr>                        
                        <?//= Page::getDataTR($row,'harga') ?>
                        <?= Page::getDataTR($row,'merk') ?>
                        <?= Page::getDataTR($row,'idkondisi') ?>
                        <?= Page::getDataTR($row,'spesifikasi') ?>
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

	// autocomplete
	$("#lokasi").xautox({strpost: "f=acxlokasi", targetid: "idlokasi"});
	$("#unit").xautox({strpost: "f=acxunit", targetid: "idunit"});
	$("#pegawai").xautox({strpost: "f=acxpegawai", targetid: "idpegawai"});
	$("#barang").xautox({strpost: "f=acxbaranginv", targetid: "idbarang"});

});

</script>
</body>
</html>
