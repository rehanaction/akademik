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
	require_once(Route::getModelPath('organisasi'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('pegawai'));
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
	$p_title = 'Data Organisasi BEM / UKM';
	$p_tbwidth = 500;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();

	$p_model = mOrganisasi;
	$a_unit = mUnit::getComboUnit($conn);

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeorganisasi', 'label' => 'Kode Organisasi','notnull'=>true);
	$a_input[] = array('kolom' => 'namaorganisasi', 'label' => 'Nama Organisasi');
	$a_input[] = array('kolom' => 'kodeunit', 'label' => 'Unit', 'type'=>'S', 'option' => $a_unit);
	$a_input[] = array('kolom' => 'nippegawai', 'label' => 'Pembina');
	$a_input[] = array('kolom' => 'alamatorganisasi', 'label' => 'Alamat');
	$a_input[] = array('kolom' => 'telporganisasi', 'label' => 'Telp Organisasi');
	//$a_input[] = array('kolom' => 'kodeanggaran', 'label' => 'Kode Anggaran');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan','type'=>'');
	$a_input[] = array('kolom' => 'nimketua', 'label' => 'Ketua');
	$a_input[] = array('kolom' => 'telpketua', 'label' => 'Telp Ketua');
	$a_input[] = array('kolom' => 'nimwakil', 'label' => 'Wakil');
	$a_input[] = array('kolom' => 'telpwakil', 'label' => 'Telp Wakil Ketua');
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

	$r_ketua = Page::getDataValue($row,'nimketua');
	$r_wakil = Page::getDataValue($row,'nimwakil');
	$r_pembina = Page::getDataValue($row,'nippegawai');
	if(!empty($r_ketua))
		$r_namaketua = $r_ketua.' - '.mMahasiswa::getNama($conn,trim($r_ketua),false);
	if(!empty($r_wakil))
		$r_namawakil = $r_wakil.' - '.mMahasiswa::getNama($conn,trim($r_wakil),false);
	if(!empty($r_pembina))
		$r_namapembina = $r_pembina.' - '.mPegawai::getNamaPegawai($conn,trim($r_pembina));

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

						$a_required = array('kodeorganisasi');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<?= Page::getDataTR($row,'kodeorganisasi') ?>
							<?= Page::getDataTR($row,'namaorganisasi') ?>
							<?= Page::getDataTR($row,'kodeunit') ?>
							<tr>
								<td class="LeftColumnBG">Pembina</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namapembina,
										UI::createTextBox('pembina',$r_pembina,'ControlStyle',30,30)) ?>
									<input type="hidden" name="nippegawai" id="nippegawai" value="<?=$r_pembina?>">
								</td>
							</tr>
							<?= Page::getDataTR($row,'alamatorganisasi') ?>
							<?= Page::getDataTR($row,'telporganisasi') ?>
							<? /* Page::getDataTR($row,'kodeanggaran') */ ?>
							<tr>
								<td class="LeftColumnBG">Keterangan</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_keterangan,
										UI::createTextArea('keterangan',$r_keterangan,'ControlStyle',2,30)) ?>
								</td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Ketua</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namaketua,
										UI::createTextBox('ketua',$r_namaketua,'ControlStyle',30,30)) ?>
									<input type="hidden" name="nimketua" id="nimketua" value="<?=$r_ketua?>">
								</td>
							</tr>
							<?= Page::getDataTR($row,'telpketua') ?>
							<tr>
								<td class="LeftColumnBG">Wakil Ketua</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namawakil,
										UI::createTextBox('wakil',$r_namawakil,'ControlStyle',30,30)) ?>
									<input type="hidden" name="nimwakil" id="nimwakil" value="<?=$r_wakil?>">
								</td>
							</tr>
							<?= Page::getDataTR($row,'telpwakil') ?>
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
	$("#ketua").xautox({strpost: "f=acmahasiswa", targetid: "nimketua"});
	$("#wakil").xautox({strpost: "f=acmahasiswa", targetid: "nimwakil"});
	$("#pembina").xautox({strpost: "f=acpegawai", targetid: "nippegawai"});
});

</script>
</body>
</html>
