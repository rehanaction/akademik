<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftarseminar'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(empty($r_key))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pendaftar Pegawai';
	$p_tbwidth = 600;
	$p_listpage = Route::getListPage();
	
	$p_model = mPemdaftarSeminar;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;

	$a_propinsi = mCombo::propinsi($conn);
	$a_kota = mCombo::getKota($conn);
	$a_kerja = array('0' => 'Tidak', '1' => 'Ya');
	
	// struktur view
	$a_input = array();
	if(!empty($r_key)) {
		$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'Nomor Pendaftaran');
		$a_input[] = array('kolom' => 'nip', 'label' => 'ID Pegawai', 'readonly'=>true);
		$a_input[] = array('kolom' => 'nama', 'label' => 'Nama');
		$a_input[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => mMahasiswa::jenisKelamin());
		$a_input[] = array('kolom' => 'noktp', 'label' => 'No KTP');
		$a_input[] = array('kolom' => 'telp', 'label' => 'Telp');
		$a_input[] = array('kolom' => 'hp', 'label' => 'HP');
		$a_input[] = array('kolom' => 'email', 'label' => 'Mail');
		$a_input[] = array('kolom' => 'tmplahir', 'label' => 'Tmp & Tgl Lahir', 'maxlength' => 15, 'size' => 15, 'notnull' => true);
		$a_input[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D', 'notnull' => true);
		$a_input[] = array('kolom' => 'kodepropinsi', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKota()"', 'empty' => '-- Pilih Propinsi --','notnull'=>true);
		$a_input[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => '-- Pilih Kota --', 'notnull' => true);
		$a_input[] = array('kolom' => 'kodepos', 'label' => 'Kode Pos', 'maxlength' => 5, 'type' => 'P');
		$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'maxlength' => 100,'notnull' => true);
		$a_input[] = array('kolom' => 'iskerja', 'label' => 'Bekerja', 'type' => 'S', 'option' => $a_kerja, 'empty' => true);
		$a_input[] = array('kolom' => 'jabatan', 'label' => 'Jabatan');
		$a_input[] = array('kolom' => 'namaperusahaan', 'label' => 'Nama Perusahaan', 'size' => 30, 'maxlength' => 50);
	}
	else
		$a_input[] = array('kolom' => 'nip', 'label' => 'ID Pegawai');

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		// ambil data pegawai
		if(empty($r_key))
			$record = $p_model::getRecordPegawai($conn,$record);
		
		if(empty($record['nopendaftar']))
			$record['nopendaftar'] = $p_model::getNoPendaftarBaru($conn,$record);

		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		// if(!$p_posterr) unset($post);
		
		// flash karena ada perubahan $a_input
		if(empty($p_posterr)) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
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
							<?php
								foreach($a_input as $v) {
									if($v['kolom'] == 'jabatan' or $v['kolom'] == 'namaperusahaan') {
							?>
							<tr id="<?php echo $v['kolom'] ?>">
								<td class="LeftColumnBG"><?php echo $v['label'] ?></td>
								<td class="RightColumnBG">
									<?= Page::getDataInput($row,$v['kolom']) ?>
								</td>
							</tr>
							<?php
									}
									else
										echo Page::getDataTR($row,$v['kolom']);
								}
							?>
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

	loadKota();
	
	// autocomplete
	$("#nimpengajuseminar").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
	$("#nippengajuseminar").xautox({strpost: "f=acpegawai", targetid: "nip"});


	//show hide nama perusaahaan dan jabatan
	if ($("#iskerja").val() != 1) {
		$("#jabatan").hide();
		$("#namaperusahaan").hide();
	}
	
	$("#iskerja").change(function(a) {
		var value = $("#iskerja").val();
		
		if (value != 1) {
			//document.getElementById("nimpengajuseminar").disabled = true; 
			$("#jabatan").hide();
			$("#namaperusahaan").hide();
			//$('#pgw').attr('readonly', true);
		} else {
			$("#jabatan").show();
			$("#namaperusahaan").show();

		}
		//window.alert();
	});
	
});

// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekota").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

</script>
</body>
</html>
