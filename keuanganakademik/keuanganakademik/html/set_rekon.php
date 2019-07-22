<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('bank'));
	
	// variabel request
	$a_bank = mBank::arrQuery($conn);
	$r_bank = Modul::setRequest($_POST['bank'],'BANK',$a_bank);
	
	if(empty($r_tanggal)) {
		$r_tanggal = $_POST['tanggal'];
		if(empty($r_tanggal))
			$r_tanggal = date('d-m-Y');
	}
	
	// properti halaman
	$p_title = 'Rekonsiliasi Bank';
	$p_tbwidth = '100%';
	$p_aktivitas = 'SPP';
	
	// ambil data
	$row = mBank::getData($conn,$r_bank);
	
	// ambil file
	$dir = mBank::rekondir.$row['dirrekon'];
	$rekon = mBank::getRekonStep();
	
	list($d,$m,$y) = explode('-',$r_tanggal);
	$filename = $r_bank.'_'.$y.$m.$d;
	
	// cek rekon
	$a_exist = array();
	foreach($rekon as $row) {
		$file = $dir.'/'.$row[0].'/'.$filename.'.'.$row[1];
		if(file_exists($file))
			$a_exist[$row[0]] = true;
		else
			$a_exist[$row[0]] = false;
	}
	
	$c_spx = false;
	$c_rcn = false;
	if($c_edit) {
		// if(!$a_exist['rcn'])
			$c_spx = true;
		if($a_exist['spx'] /* and !$a_exist['rcn'] */)
			$c_rcn = true;
	}
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'spx' and $c_spx) {
		// format tanggal
		$r_ftanggal = $y.$m.$d;
		
		// panggil lewat http
		$ch = curl_init();
		
		curl_setopt($ch,CURLOPT_URL,mBank::h2hurl.'spx.php?tgl='.$r_ftanggal.'&bank='.$r_bank);
		curl_setopt($ch,CURLOPT_HEADER,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		curl_exec($ch);
		curl_close($ch);
		
		$a_flash = array();
		$a_flash['r_tanggal'] = $r_tanggal;
		$a_flash['p_posterr'] = false;
		$a_flash['p_postmsg'] = 'Pembuatan SPX berhasil';
		
		Route::setFlashData($a_flash);
	}
	else if($r_act == 'rcn' and $c_rcn) {
		// format tanggal
		$r_ftanggal = $y.$m.$d;
		
		// panggil lewat http
		$ch = curl_init();
		
		curl_setopt($ch,CURLOPT_URL,mBank::h2hurl.'rcn.php?tgl='.$r_ftanggal.'&bank='.$r_bank);
		curl_setopt($ch,CURLOPT_HEADER,false);
		curl_setopt($ch,CURLOPT_RETURNTRANSFER,true);
		
		curl_exec($ch);
		curl_close($ch);
		
		$a_flash = array();
		$a_flash['r_tanggal'] = $r_tanggal;
		$a_flash['p_posterr'] = false;
		$a_flash['p_postmsg'] = 'Pembuatan RCN berhasil';
		
		Route::setFlashData($a_flash);
	}
	
	// combo
	$l_bank = UI::createSelect('bank',$a_bank,$r_bank,'ControlStyle');
	$x_tanggal = UI::createTextBox('tanggal_text',$r_tanggal,'ControlStyle',10,10);
	$x_tanggal .= '&nbsp; <img src="images/cal.png" id="tanggal_trg" style="cursor:pointer" title="Pilih tanggal rekonsiliasi">';
	$x_tanggal .= '<input type="hidden" name="tanggal" id="tanggal" value="'.$r_tanggal.'">';
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Bank', 'combo' => $l_bank);
	$a_filtercombo[] = array('label' => 'Tanggal', 'combo' => $x_tanggal);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
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
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:220px;float:left">
						<table width="100%" cellspacing="0" cellpadding="4">
							<? foreach($a_filtercombo as $t_filter) { ?>
							<tr>		
								<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
								<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
							</tr>
							<? } ?>
							<tr>
								<td colspan="2">
									<input type="button" value="Pilih" onclick="goPilih()">
									<? if($c_edit) { ?>
									<input type="button" value="Buat SPX"<? echo $c_spx ? ' onclick="goSPX()"'.($a_exist['spx'] ? ' class="button-warning"' : '') : ' disabled' ?>>
									<input type="button" value="Buat RCN"<? echo $c_rcn ? ' onclick="goRCN()"'.($a_exist['rcn'] ? ' class="button-warning"' : '')  : ' disabled' ?>>
									<? } ?>
								</td>
							</tr>
						</table>
					</div>
					<div class="filterTable" style="width:720px;float:right">
						<table width="100%" cellspacing="0" cellpadding="4">
							<tr>
								<td style="padding:2px 4px">
									<ol style="margin:0;padding-left:14px">
										<li style="padding:2px 0"><strong>TRANS</strong> adalah file transaksi harian yang dikirimkan oleh bank.</li>
										<li style="padding:2px 0"><strong>SPX</strong> adalah file suspect yang dibuat oleh sistem berdasarkan perbandingan data pembayaran dengan file TRANS.</li>
										<li style="padding:2px 0"><strong>RCN</strong> adalah file hasil rekonsiliasi file SPX.</li>
										<? if($c_edit) { ?>
										<li style="padding:2px 0">Untuk membuat file SPX atau RCN, pilih Bank dan Tanggal terlebih dahulu dengan mengeklik tombol Pilih.</li>
										<? } ?>
									</ol>
								</td>
							</tr>
						</table>
					</div>
					<div style="clear:both"></div>
				</center>
				<br>
				<?	}
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<?	foreach($rekon as $row) {
						$file = $dir.'/'.$row[0].'/'.$filename.'.'.$row[1];
				?>
					<tr>
						<th><?= strtoupper($row[0]) ?></th>
					</tr>
					<tr valign="top">
						<?php if(file_exists($file)) { ?>
						<td>
							<div style="max-height:250px;overflow-y:auto">
							<?= nl2br(file_get_contents($file)) ?>
							</div>
						</td>
						<?php } else { ?>
						<td>
							<span style="color:red">File <?php echo strtoupper($row[0]) ?> belum dibuat</span>
						</td>
						<?php } ?>
					</tr>
				<?	} ?>
				</table>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

Calendar.setup({
	inputField     :    "tanggal_text",
	ifFormat       :    "%d-%m-%Y",
	button         :    "tanggal_trg",
	align          :    "Br",
	singleClick    :    true
});

function goPilih() {
	$("#tanggal").val($("#tanggal_text").val());
	goSubmit();
}

<?php if($c_edit) { ?>
function goSPX() {
	var go;
	<?php if($a_exist['spx']) { ?>
	go = confirm("Apakah anda yakin akan membuat ulang SPX? Aksi ini hanya dilakukan bila ada revisi TRANS");
	<?php } else { ?>
	go = true;
	<?php } ?>
	
	if(go) {
		$("#act").val("spx");
		goSubmit();
	}
}

function goRCN() {
	var go;
	<?php if($a_exist['rcn']) { ?>
	go = confirm("Apakah anda yakin akan membuat ulang RCN? Aksi ini hanya dilakukan bila ada revisi SPX");
	<?php } else { ?>
	go = true;
	<?php } ?>
	
	if(go) {
		$("#act").val("rcn");
		goSubmit();
	}
}
<?php } ?>

</script>
</body>
</html>