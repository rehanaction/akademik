<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('pengalaman'));
	require_once(Route::getModelPath('jenispelanggaran'));
	require_once(Route::getModelPath('sanksipelanggaran'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('periode'));
	require_once(Route::getModelPath('strukturkegiatan'));
	require_once(Route::getModelPath('poinkegiatan'));
	require_once(Route::getModelPath('poinmhs'));
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
	$p_title = 'Data Aktivitas';
	$p_tbwidth = 600;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();

	$p_model = mPengalaman;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;
	$c_validasi = $a_auth['canother']['V'];

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'Mahasiswa');
	$a_input[] = array('kolom' => 'periode', 'label' => 'periode', 'type' => 'S', 'option' => mPeriode::getArray($conn));
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tanggal Pengajuan','type' => 'D','add'=>'onchange="setHari1(this.value)"');
	$a_input[] = array('kolom' => 'tglkegiatan', 'label' => 'Tanggal Kegiatan','type' => 'D','add'=>'onchange="setHari1(this.value)"');
	$a_input[] = array('kolom' => 'tglkegiatanakhir', 'label' => 'Akhir Tanggal Kegiatan','type' => 'D');
	$a_input[] = array('kolom' => 'jenisaktivitas', 'label' => 'Jenis Aktivitas', 'type' => 'S', 'option' => array('E'=>'External','I'=>'Internal'));
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Aktivitas', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'namakegiatanen', 'label' => 'Nama Aktivitas (EN)', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'penyelenggara', 'label' => 'Penyelenggara', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'kodekategori', 'label' => 'Kategori', 'type' => 'S', 'option' => $p_model::getListKategori());
	$a_input[] = array('kolom' => 'parentkegiatan', 'label' => 'Peran Kegiatan', 'type' => 'S', 'option' => array(''=>'')+mStrukturKegiatan::getArrayLevel($conn,'1'),'add'=>'onChange="loadChildKegiatan()"');
	$a_input[] = array('kolom' => 'kodekegiatan', 'label' => 'Skala Kegiatan', 'type' => 'S', 'empty' => true);
	$a_input[] = array('kolom' => 'filepengalaman', 'label' => 'File Pengalaman <br> (jpg,word,pdf)', 'type' => 'U', 'uptype' => 'pengalaman', 'size' => 40);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'C', 'option' => array('-1' => ''));
	$a_input[] = array('kolom' => 'tglvalidasi', 'label' => 'Tanggal Validasi','type' => 'D','add'=>'onchange="setHari1(this.value)"', 'readonly' => !$c_validasi);
	$a_input[] = array('kolom' => 'istampil', 'label' => 'Tampil di SKPI', 'type' => 'C', 'option' => array('-1' => ''));
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 5, 'cols' => 45);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($_REQUEST['isvalid']))
			$record['isvalid'] = 0;

		$inputpoin = false;
		if( $_POST['isvalid'] != $_POST['valid'] or ((!empty($_POST['isvalid'])) and empty($r_key)))
			$inputpoin = true;
		
		$record['istampil'] = (int)$_POST['istampil'];

		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);

		//input poin
		if(!$p_posterr and $inputpoin) {
			//check poin mhs
			$i_poinmhs = mPoinmhs::getData($conn,$record['nim']);
			$jenjang = mMahasiswa::getJenjangByNim($conn,$record['nim']);
			//cari poin prestasi
			$poin = mPoinkegiatan::getPoin($conn,$record['periode'].'|'.$record['kodekegiatan'].'|'.$jenjang);
			if(!empty($poin)){
				$recordp = array();
				$recordp['nim'] = $record['nim'];
				if(!empty($_POST['isvalid']) and empty($_POST['valid']))
					$recordp['poinpengalaman'] = $i_poinmhs['poinpengalaman']+$poin;
				else if( empty($_POST['isvalid']))
					$recordp['poinpengalaman'] = $i_poinmhs['poinpengalaman']-$poin;

				if(empty($i_poinmhs)){
					list($p_posterr,$p_postmsg) = mPoinmhs::insertRecord($conn,$recordp);
				}else{
					list($p_posterr,$p_postmsg) = mPoinmhs::updateRecord($conn,$recordp,$record['nim']);
				}
			}else{
				list($p_posterr,$p_postmsg) = array(true,'Setting poin aktivitas belum dilakukan');
			}
		}
		if(!$p_posterr)
			unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);
	}else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'file'.'pengalaman');

	$a_kegiatan = array(''=>'')+mStrukturKegiatan::getArrayLevel($conn,'1');

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	if(Akademik::isMhs())
		$r_mahasiswa = Modul::getUserName();
	else {
		$r_mahasiswa = Page::getDataValue($row,'nim');
	}
	
	if(!empty($r_mahasiswa))
		$r_namamahasiswa = $r_mahasiswa.' - '.$p_model::getNamaMahasiswa($conn,$r_mahasiswa);

	$r_kegiatan = Page::getDataValue($row,'kodekegiatan');
	$v_kegiatan = mStrukturKegiatan::getData($conn,$r_kegiatan);
	$r_parentkegiatan = mStrukturKegiatan::getParent($conn,$r_kegiatan);
	$a_parentkegiatan = mStrukturKegiatan::getData($conn,$r_parentkegiatan);
	
	$poin_datavalid = Page::getDataValue($row,'isvalid');
	$tglkegiatan = Page::getDataValue($row,'tglkegiatan');
	$tglkegiatanakhir = Page::getDataValue($row,'tglkegiatanakhir');
	
	if(!empty($poin_datavalid)){
		$jenjang = mMahasiswa::getJenjangByNim($conn,$r_mahasiswa);
		$poinpreiode = Page::getDataValue($row,'periode');
		$poinkodekegiatan = Page::getDataValue($row,'kodekegiatan');

		//cari poin prestasi
		if(!empty($poinkodekegiatan))
			$poinvalid = mPoinkegiatan::getPoin($conn,$poinpreiode.'|'.$poinkodekegiatan.'|'.$jenjang);
		else
			$poinvalid = 0;
		
		$c_delete = false;
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
							<?php
								if(Akademik::isMhs())
								{
							?>
									<tr>
										<td class="LeftColumnBG">Mahasiswa</td>
										<td class="RightColumnBG"><?=$r_namamahasiswa?></td>
									</tr>
									<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">
							<?php
							}else{
								?>

								<tr>
									<td class="LeftColumnBG">Mahasiswa</td>
									<td class="RightColumnBG">
										<?= Page::getDataInputWrap($r_namamahasiswa,
											UI::createTextBox('mahasiswa',$r_namamahasiswa,'ControlStyle',null,50)) ?>
										<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">
									</td>
								</tr>
							<?php } ?>
							<?= Page::getDataTR($row,'periode') ?>
							<?= Page::getDataTR($row,'tglpengajuan') ?>
							<?// Page::getDataTR($row,'tglkegiatan,tglkegiatanakhir') ?>
							<tr>
								<td class="LeftColumnBG">Tgl Kegiatan</td>
								<td class="RightColumnBG">
									<span id="show"><?=date::indoDate($tglkegiatan).' s/d '.date::indoDate($tglkegiatanakhir)?></span>
									<span id="edit" style="display:none"><?=Page::getDataInput($row,'tglkegiatan') ?> s/d <?=Page::getDataInput($row,'tglkegiatanakhir') ?></span>
								</td>
							</tr>

							<?= Page::getDataTR($row,'jenisaktivitas') ?>
							<?= Page::getDataTR($row,'namakegiatan') ?>
							<?= Page::getDataTR($row,'namakegiatanen') ?>
							<?= Page::getDataTR($row,'penyelenggara') ?>
							<?= Page::getDataTR($row,'kodekategori') ?>
							<tr>
								<td class="LeftColumnBG">Kegiatan</td>
								<td class="RightColumnBG">
									<span id="show"><?=$a_parentkegiatan['namakegiatan']?></span>
									<span id="edit" style="display:none"><?=UI::createSelect('parentkegiatan',$a_kegiatan,$r_parentkegiatan,'ControlStyle',true,'onclick="loadChildKegiatan()"'); ?></span>
								</td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Sub Kegiatan</td>
								<td class="RightColumnBG">
									<span id="show"><?=$v_kegiatan['namakegiatan']?></span>
									<span id="edit" style="display:none"><?=Page::getDataInput($row,'kodekegiatan') ?></span>
								</td>
							</tr>
							<?php
							if(!empty($poin_datavalid)){
							?>
							<tr>
								<td class="LeftColumnBG">Poin Kegiatan</td>
								<td class="RightColumnBG">
									<span id="show"><?=$poinvalid?></span>
									<span id="edit" style="display:none"><?=$poinvalid?></span>
								</td>
							</tr>
							<?php
							}
							?>
							<?= Page::getDataTR($row,'filepengalaman') ?>
							<? 
							if(!Akademik::isMhs()){
								echo Page::getDataTR($row,'isvalid') ;
							}else{
							?>
							<tr>
								<td class="LeftColumnBG">Valid</td>
								<td class="RightColumnBG">
									<span id="show"><?=(!empty($poin_datavalid)?'<img src="images/check.png">':'' )?></span>
									<span id="edit" style="display:none"><?=(!empty($poin_datavalid)?'<img src="images/check.png">':'' )?></span>
								</td>
							</tr>
							<?	
							}
							?>
							<?= Page::getDataTR($row,'tglvalidasi') ?>
							<?= Page::getDataTR($row,'istampil') ?>
							<?= Page::getDataTR($row,'keterangan') ?>
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
	loadChildKegiatan();
	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
});


// ajax ganti kegiatan
function loadChildKegiatan() {
	var param = new Array();
	param[0] = $("#parentkegiatan").val();
	param[1] = "<?= $r_kegiatan ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "getkegiatanchild", q: param }
				});

	jqxhr.done(function(data) {
		$("#kodekegiatan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

</script>
</body>
</html>
