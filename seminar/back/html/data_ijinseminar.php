<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('ijinseminar'));;
	require_once(Route::getModelPath('seminar'));;
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('mahasiswa'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);

	if (isset ($_GET['key']))
	$r_key = CStr::removeSpecial($_GET['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Seminar';
	$p_tbwidth = 600;
	$p_listpage = Route::getListPage();
	
	$p_model = mIjinSeminar;
	$p_uptype = $p_model::uptype;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;

	$a_periode = mCombo::periode($conn);
	$a_pengaju = array('M' =>'Mahasiswa' ,'P' =>'Pegawai');

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option' => $a_periode, 'empty' => true);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 5, 'cols' => 40);
	$a_input[] = array('kolom' => 'typepengajuijin', 'label' => 'Pengaju Seminar', 'type' => 'S', 'option' => $a_pengaju, 'empty' => '-- Pilih Pengaju --');
	$a_input[] = array('kolom' => 'nimpengajuijinseminar', 'label' => 'NIM Pengaju');
	$a_input[] = array('kolom' => 'nippengajuijinseminar', 'label' => 'NIP Pengaju');
	$a_input[] = array('kolom' => 'fileijinseminar', 'label' => 'File Referensi', 'type' => 'U', 'uptype' => $p_uptype, 'size' => 40);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'C', 'option' => array('1' => ''),'readonly'=>$p_limited);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		//print_r(implode(",",$_POST['semester']));die();
		if(empty($_POST['isvalid']))
			$record['isvalid'] = 0;
			
		$file_types=$_FILES['fileijinseminar']['type'];
		$file_name=$_FILES['fileijinseminar']['name'];
				
		//print_r($_FILES['fileijinseminar']['name']);die();
		
		if(empty($_FILES['fileijinseminar']['error'])){
			$upload=move_uploaded_file($_FILES['fileijinseminar']['tmp_name'],'uploads/ijinseminar/'.$file_name);
		}
		if(empty($r_key)){
			$record['fileijinseminar']=$file_name;
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			$r_key = $p_model::getLastValue($conn);
		}else{
			$record['fileijinseminar']=$file_name;
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,"",$record,$r_key);	
		}
			

		//$tipe=array('image/jpeg','image/jpg','image/gif','image/png');
		//$ext=array('image/jpg'=>'jpg','image/jpeg'=>'jpeg','image/gif'=>'gif','image/png'=>'png');
		
		//var_dump($_FILES);exit;
			/*
		if(!$p_posterr){
			if(empty($_FILES['fileijinseminar']['error'])) {
				$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,150);
				
				switch($err) {
					case -1:
					case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
					case -3: $msg = 'foto tidak bisa disimpan'; break;
					default: $msg = false;
				}
				if($msg !== false)
					$msg = 'Upload gagal, '.$msg;
			}
			else
				$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
		}
		*/
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'file'.$p_uptype);
	
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	$r_mahasiswa = Page::getDataValue($row,'nim');
	$r_pegawai = Page::getDataValue($row,'nip');
	
	if(!empty($r_mahasiswa))
		$r_namamahasiswa = $r_mahasiswa.' - '.$p_model::getNamaMahasiswa($conn,$r_mahasiswa);	


	if(!empty($r_pegawai))
		$r_namapegawai = $r_pegawai.' - '.$p_model::getNamaPegawai($conn,$r_pegawai);	
	
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
			<form name="pageform" id="pageform" method="post"  enctype="multipart/form-data">
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

					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'periode') ?>
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'typepengajuijin') ?>
						<tr id="mhs">
							<td class="LeftColumnBG">NIM Mahasiswa</td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namamahasiswa,
									UI::createTextBox('nim_pengajuijinseminar',$r_mahasiswa,'ControlStyle',30,30)) ?>
								<input type="hidden" name="nimpengajuijinseminar" id="nimpengajuijinseminar">
							</td>
						</tr>
						<tr id="pgw">
							<td class="LeftColumnBG">NIP Pegawai</td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namapegawai,
									UI::createTextBox('nip_pengajuijinseminar',$r_namapegawai,'ControlStyle',30,30)) ?>
								<input type="hidden" name="nippengajuijinseminar" id="nippengajuijinseminar" value="<?=$r_pegawai?>">
							</td>	
						</tr>	
						</div>						
						<?= Page::getDataTR($row,'fileijinseminar') ?>	
						<?= Page::getDataTR($row,'isvalid') ?>
						
					</table>
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

<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
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
	$("#nim_pengajuijinseminar").xautox({strpost: "f=acmahasiswa", targetid: "nimpengajuijinseminar"});
	$("#nip_pengajuijinseminar").xautox({strpost: "f=acpegawai", targetid: "nippengajuijinseminar"});
});

$(function() {
	// typepengaju : 1 - Mhs , 2 - Pegawai
	$("#pgw").hide();
	$("#mhs").hide();	
	
	
	$("#typepengajuijin").change(function(a) {
		var value = $("#typepengajuijin").val();
		if (value != 'M') {
			//document.getElementById("nimpengajuseminar").disabled = true; 
			$("#pgw").show();
			$("#mhs").hide();
			//$('#pgw').attr('readonly', true);
		} else {			
			$("#mhs").show();
			$("#pgw").hide();

		}
		//window.alert();
	});
	
	$("#typepeserta").change(function(a) {
		
		var value = $("#typepeserta").val();
		if (value != 'M') {
			//document.getElementById("nimpengajuseminar").disabled = true; 
			$("#sem").hide();
		} else  
			$("#sem").show();
	});
	

});

	function hideSem(){
		var type = '<?=$type?>';
		
		if(type!='M')
			$('#sem').hide();
		if(type=='M')
			$('#sem').show();
			
	}
	
	
	$(function() {
        $.mask.definitions['~'] = "[+-]";
		$("#i_jammulai").mask("99:99");
		$("#i_jammulai").mask("99:99");

    });

</script>
</body>
</html>
