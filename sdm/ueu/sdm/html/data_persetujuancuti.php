<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_update = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('cuti'));
	require_once(Route::getModelPath('email'));
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pengajuan Cuti';
	$p_tbwidth = 600;
	$p_aktivitas = 'DATA';
	$p_dbtable = 'pe_rwtcuti';
	$p_key = 'nourutcuti';
	$p_listpage = Route::getListPage();
	
	$p_model = mCuti;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nourutsurat', 'label' => 'No. Surat', 'readonly' => true);
	$a_input[] = array('kolom' => 'nik', 'label' => 'NIP', 'readonly' => true);
	$a_input[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglp', 'type' => 'H');
	$a_input[] = array('kolom' => 'jeniscuti', 'label' => 'Jenis Cuti', 'readonly' => true);
	$a_input[] = array('kolom' => 'alasancuti', 'label' => 'Alasan Cuti', 'readonly' => true);
	$a_input[] = array('kolom' => 'alamatselamacuti', 'label' => 'Alamat Selama Cuti', 'readonly' => true);
	$a_input[] = array('kolom' => 'telpselamacuti', 'label' => 'Telp Selama Cuti', 'readonly' => true);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'statususulan', 'label' => 'Status', 'type' => 'R', 'option' => $p_model::statusSetujuiCuti());
	$a_input[] = array('kolom' => 'tgldisetujui', 'label' => 'Tgl. Persetujuan', 'type' => 'D', 'default' => date('Y-m-d'));
	
	if(Modul::getRole() == 'A' or Modul::getRole() == 'admhrm')
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		//mendapatkan no surat, bila sudah divalidasi
		if($record['isvalid'] == 'Y'){
			$nosurat = $p_model::getNoSuratCuti($conn,$r_subkey,$record['tglp']);
			if($nosurat != 'null')
				$record['nosurat'] = $nosurat;
		}
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$p_dbtable,$p_key);
		
		//simpan ke presensi
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = mPresensi::saveFromCuti($conn,$r_key);
		
		//email ke pegawai
		if(!$p_posterr)
			mEmail::confirmCuti($conn,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	
	$sql = $p_model::getDataEditPersetujuanCuti($r_key);
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
	}
	
	//cuti detail
	if(!empty($r_key)){
		$a_cd = $p_model::getCutiDetail($conn,$r_key);
		$ket = $p_model::getKetCuti($conn,$r_key);
		$ambil = $p_model::getAmbilCuti($conn,$ket['idpegawai'],$ket['idjeniscuti'],$ket['tglpengajuan']);
		$sisa = $p_model::getSisaCuti($conn,$ket['idpegawai'],$ket['idjeniscuti'],$ket['tglpengajuan']);
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
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
						<?= Page::getDataTR($row,'nourutsurat') ?>
						<?= Page::getDataTR($row,'nik') ?>
						<?= Page::getDataTR($row,'namalengkap') ?>
						<?= Page::getDataTR($row,'tglpengajuan') ?>
						<?= Page::getDataTR($row,'jeniscuti') ?>
						<?= Page::getDataTR($row,'alasancuti') ?>
						<?= Page::getDataTR($row,'alamatselamacuti') ?>
						<?= Page::getDataTR($row,'telpselamacuti') ?>
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'statususulan') ?>
						<?= Page::getDataTR($row,'tgldisetujui') ?>
						<?= Page::getDataTR($row,'isvalid') ?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Keterangan Cuti</td>
							<td class="RightColumnBG">
								Anda sudah mengambil cuti <?= $ambil.' hari'?><?= $sisa != '' ? ', sisa '.$sisa.' hari' : ''?> untuk periode ini
								<?= Page::getDataInput($row,'tglp') ?>
							</td>
						</tr>
					</table>
					</div>
				</center>
				
				<br>				
				<center>	
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Detail Cuti</h1>
							</div>
						</div>
					</header>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
						<th>Tgl. Mulai</th>
						<th>Tgl. Selesai</th>
						<th>Lama Cuti</th>					
						<?
							$i = 0;$detail=0;
							if(count($a_cd) > 0){
								foreach($a_cd as $rowd){
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;$detail++;
						?>
						<tr valign="top" class="<?= $rowstyle ?>">
							<td align="center"><?= CStr::formatDateInd($rowd['tglmulai'],false);?></td>
							<td align="center"><?= CStr::formatDateInd($rowd['tglselesai'],false);?></td>
							<td align="center"><?= $rowd['lamacuti'].' hari';?></td>
						</tr>
						<?
								}
							}
							if($i == 0) {
						?>
						<tr>
							<td colspan="4" align="center">Data kosong</td>
						</tr>
						<?	}
						?>				
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
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
