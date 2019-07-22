<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
        // hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pendaftar';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPendaftar;
	
        // hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
                
	// struktur view
        $r_act = $_POST['act'];
        
		$a_input = array();	
        $a_input[] = array('kolom' => 'nopendaftar', 'label' => 'No.Pendaftar', 'maxlength' => 12, 'size' => 15, 'readonly' => true,'notnull' => true);
        
        $a_input[] = array('kolom' => 'gelardepan', 'label' => 'Nama Pendaftar', 'maxlength' => 50, 'size' => 5, 'notnull' => true,'add'=>'placeholder="Gelar Depan"','readonly' => true);
		$a_input[] = array('kolom' => 'nama', 'label' => '', 'maxlength' => 50, 'size' => 30, 'notnull' => true,'add'=>'placeholder="Nama Lengkap"','readonly' => true);
        $a_input[] = array('kolom' => 'gelarbelakang', 'label' => '', 'maxlength' => 50, 'size' => 5, 'notnull' => true,'add'=>'placeholder="Gelar Belakang"','readonly' => true);

        $a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur Penerimaan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::jalur($conn),'empty'=>true,'readonly' => true);
		$a_input[] = array('kolom' => 'periodedaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'notnull' => true, 'option' => mCombo::periode($conn),'empty'=>true,'readonly' => true);
		$a_input[] = array('kolom' => 'idgelombang', 'label' => 'Gelombang', 'type' => 'S', 'notnull' => true, 'option' => mCombo::gelombang($conn),'empty'=>true,'readonly' => true);
		$a_input[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem Kuliah', 'type' => 'S','notnull' => true, 'option' => mCombo::sistemKuliah($conn),'empty'=>true,'readonly' => true);

		$a_input[] = array('kolom' => 'potonganbeasiswa', 'label' => 'Potongan Beasiswa', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly' => true);
		$a_input[] = array('kolom' => 'potonganregistrasi', 'label' => 'Potongan Registrasi', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly' => true);
		$a_input[] = array('kolom' => 'potongansemesterpendek', 'label' => 'Potongan Semester Pendek', 'maxlength' => 12, 'size' => 10, 'type'=>'N','readonly' => true);
		
		$a_input[] = array('kolom' => 'keteranganpotonganbeasiswa', 'label' => 'Keterangan Potongan Beasiswa', 'maxlength' => 200, 'type'=>'A','readonly' => true);
		$a_input[] = array('kolom' => 'keteranganpotonganregistrasi', 'label' => 'Keterangan Potongan Registrasi', 'maxlength' => 200, 'type'=>'A','readonly' => true);
		$a_input[] = array('kolom' => 'keteranganpotongansemesterpendek', 'label' => 'Keterangan Potongan Semester Pendek', 'maxlength' => 200, 'type'=>'A','readonly' => true);
		$a_input[] = array('kolom' => 'isvalidbeasiswa', 'label' => 'Validasi Potongan Beasiswa', 'type'=>'R', 'option'=>array(0=>'Tolak', -1=>'Setujui'));
		$a_input[] = array('kolom' => 'isvalidregistrasi', 'label' => 'Validasi Potongan Registrasi', 'type'=>'R', 'option'=>array(0=>'Tolak', -1=>'Setujui'));
		$a_input[] = array('kolom' => 'isvalidsemesterpendek', 'label' => 'Validasi Potongan Semester Pendek', 'type'=>'R', 'option'=>array(0=>'Tolak', -1=>'Setujui'));
		
        //ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
			
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
				
		if(!$p_posterr) unset($post);
	}
        
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
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
        <script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<script type="text/javascript" src="scripts/common.js"></script>
        <script type="text/javascript" src="scripts/foredit.js"></script>
        <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		#table_evaluasi { border-collapse:collapse }
		#table_evaluasi .td_ev { border:1px solid #666 }
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
        <script type="text/javascript">
$(".subnav").hover(function() {
    $(this.parentNode).addClass("borderbottom");
}, function() {
    $(this.parentNode).removeClass("borderbottom");
});

</script>
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
					<?	$a_required = array('nopendaftar','nama', 'tokenpendaftaran','jurusansmaasal','thnlulussmaasal'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nopendaftar') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nopendaftar') ?></td>
						</tr>
						<?= Page::getDataTR($row,'gelardepan,nama,gelarbelakang') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">
								Info Pendaftaran<br>
								<p style="width:50px; font-size: 8px;">Data harus sesuai dengan<br> jalur yang dibuka</p>
							</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td><?= Page::getDataLabel($row,'periodedaftar') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'periodedaftar') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'jalurpenerimaan') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'jalurpenerimaan') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'idgelombang') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'idgelombang') ?></td>
									</tr>
									<tr>
										<td><?= Page::getDataLabel($row,'sistemkuliah') ?></td>
										<td>:</td>
										<td><?= Page::getDataInput($row,'sistemkuliah') ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr><td><br></td></tr>
						<?= Page::getDataTR($row,'potonganbeasiswa') ?>
						<?= Page::getDataTR($row,'keteranganpotonganbeasiswa') ?>
						<?= Page::getDataTR($row,'isvalidbeasiswa') ?>
						<tr><td><br></td></tr>						
						<?= Page::getDataTR($row,'potonganregistrasi') ?>
						<?= Page::getDataTR($row,'keteranganpotonganregistrasi') ?>
						<?= Page::getDataTR($row,'isvalidregistrasi') ?>
						<tr><td><br></td></tr>
						<?= Page::getDataTR($row,'potongansemesterpendek') ?>
						<?= Page::getDataTR($row,'keteranganpotongansemesterpendek') ?>
						<?= Page::getDataTR($row,'isvalidsemesterpendek') ?>
						
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
<?php $conn->debug=false;?>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var ajax = "<?= Route::navAddress("ajax") ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
function numberOnly(evt) {
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false
    }
    return true
} 
</script>
</body>
</html>
