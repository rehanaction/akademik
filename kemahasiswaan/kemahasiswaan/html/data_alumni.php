<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('alumni'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$p_model = mAlumni;
	
	// properti halaman
	$p_title = 'Data Alumni';
	$p_tbwidth = 600;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	$c_readlist = empty($a_authlist) ? false : true;
	
	// struktur view
	$a_input = array();
	
	//$a_propinsi = mCombo::propinsi($conn);
	//$a_kota = mCombo::kota($conn);
	
	$a_input[] = array('kolom' => 'nim', 'label' => 'N I M', 'readonly' => true);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Alumni', 'readonly' => true);
	$a_input[] = array('kolom' => 'kodefakultas', 'label' => 'Fakultas', 'type' => 'S', 'option' => mCombo::fakultas($conn), 'add' => 'onchange="loadJurusan()"', 'readonly' => true);
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type' => 'S', 'option' => mCombo::jurusan($conn), 'readonly' => true);
	$a_input[] = array('kolom' => 'thnlulus', 'label' => 'Tahun Lulus', 'readonly' => true);
	$a_input[] = array('kolom' => 'semestermhs', 'label' => 'Lama Studi', 'readonly' => true);
	$a_input[] = array('kolom' => 'noijasah', 'label' => 'No. Ijasah', 'readonly' => true);
	$a_input[] = array('kolom' => 'notranskrip', 'label' => 'No. Transkrip', 'readonly' => true);
	
	$a_input[] = array('kolom' => 'pekerjaan', 'label' => 'Pekerjaan', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'namaperusahaan', 'label' => 'Perusahaan', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'jenisinstansi', 'label' => 'Jenis Instansi', 'type' => 'S', 'option' => $p_model::jenisInstansi(), 'empty' => true);
	$a_input[] = array('kolom' => 'alamatperusahaan', 'label' => 'Alamat Perusahaan', 'maxlength' => 60, 'size' => 50);
	$a_input[] = array('kolom' => 'kodepropinsiperusahaan', 'label' => 'Propinsi', 'type' => 'S', 'option' => $a_propinsi, 'add' => 'onchange="loadKotaPerusahaan()"', 'empty' => '-- Pilih Propinsi --');
	$a_input[] = array('kolom' => 'kodekotaperusahaan', 'label' => 'Kota', 'type' => 'S', 'option' => $a_kota, 'empty' => true, 'empty' => '-- Pilih Kota --');
	$a_input[] = array('kolom' => 'telpperusahaan', 'label' => 'Telp Perusahaan', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'jabatan', 'label' => 'Jabatan', 'maxlength' => 30, 'size' => 30);
	
	// mengambil data riwayat
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'tglpenghargaan', 'label' => 'Tanggal', 'type' => 'D');
	$t_detail[] = array('kolom' => 'namapenghargaan', 'label' => 'Nama Penghargaan', 'size' => 55, 'maxlength' => 255);
	
	$a_detail['penghargaan'] = array('key' => $p_model::getDetailInfo('penghargaan','key'), 'data' => $t_detail);
	
	// ada aksi
	$r_act = $_POST['act'];	
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$rowd = array();
	$rowd += $p_model::getPenghargaan($conn,$r_key,'penghargaan',$post);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					require_once('inc_databutton.php');
					
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
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
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nim') ?></td>
							<td class="RightColumnBG"><?= Page::getDataInput($row,'nim') ?></td>
							<td align="right" valign="top" rowspan="8">
								<?= uForm::getImageMahasiswa($conn,$r_key,false) ?>
							</td>
						</tr>
						<?= Page::getDataTR($row,'nama') ?>
						<?= Page::getDataTR($row,'kodefakultas') ?>
						<?= Page::getDataTR($row,'kodeunit') ?>
						<?= Page::getDataTR($row,'thnlulus') ?>
						<?= Page::getDataTR($row,'semestermhs') ?>
						<?= Page::getDataTR($row,'noijasah') ?>
						<?= Page::getDataTR($row,'notranskrip') ?>
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Pekerjaan</a></li>
						<li><a id="tablink" href="javascript:void(0)">Penghargaan</a></li>
					</ul>
				
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<?= Page::getDataTR($row,'pekerjaan') ?>
						<?= Page::getDataTR($row,'namaperusahaan') ?>
						<?= Page::getDataTR($row,'jenisinstansi') ?>
						<?= Page::getDataTR($row,'alamatperusahaan') ?>
						<?= Page::getDataTR($row,'kodepropinsiperusahaan') ?>
						<?= Page::getDataTR($row,'kodekotaperusahaan') ?>
						<?= Page::getDataTR($row,'telpperusahaan') ?>
						<?= Page::getDataTR($row,'jabatan') ?>
					</table>
					</div>
					
					<div id="items">
					<?= Page::getDetailTable($rowd,$a_detail,'penghargaan','Penghargaan',true,false) ?>
					</div>
				</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
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
	
	loadKotaPerusahaan();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

// ajax ganti fakultas
function loadJurusan() {
	var param = new Array();
	param[0] = $("#kodefakultas").val();
	param[1] = "<?= $r_kodeunit ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optjurusan", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodeunit").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

// ajax ganti kota
function loadKotaPerusahaan() {
	var param = new Array();
	param[0] = $("#kodepropinsiperusahaan").val();
	param[1] = "<?= $r_kodekotaperusahaan ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotaperusahaan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

</script>
</body>
</html>
