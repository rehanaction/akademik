<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('historyupload'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Data Riwayat Upload Pembayaran';
	$p_tbwidth = 600;
	$p_aktivitas = 'SUBMIT';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = true; // empty($a_authlist) ? false : true;
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	// ambil daftar transaksi
	$a_data = mHistoryUpload::getListTransaksi($conn,$r_key);
	
	// mengambil waktu
	$a_key = str_split($r_key,2);
	$r_time = $a_key[0].$a_key[1].'-'.$a_key[2].'-'.$a_key[3].' '.$a_key[4].':'.$a_key[5].':'.$a_key[6];
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<?php if($c_readlist) { ?>
				<table border="0" cellspacing="10" align="center">
				<tr>
					<td id="be_list" class="TDButton" onclick="goList()">
						<img src="images/list.png"> Daftar
					</td>
				</tr>
				</table>
				<?php } ?>
				<div class="filterTable">
					<table width="100%" cellpadding="4" cellspacing="0" align="center">
						<tr>
							<td style="width:100px"><strong>Waktu Upload :</strong></td>
							<td><?php echo CStr::formatDateTimeInd($r_time,true,true) ?></td>
						</tr>
					</table>
				</div>
				<br />
				<div class="filterTable">
					<table width="100%" height="35px" cellpadding="4" cellspacing="4" align="center">
						<tr>
							<td style="width:35px;border:1px solid #AAA" class="GreenBG">&nbsp;</td>
							<td>Pembayaran Lunas</td>
							<td style="width:35px;border:1px solid #AAA" class="BlueBG">&nbsp;</td>
							<td>Pembayaran Menggunakan Deposit</td>
							<td style="width:35px;border:1px solid #AAA" class="YellowBG">&nbsp;</td>
							<td>Pembayaran Tidak Lunas</td>
							<td style="width:35px;border:1px solid #AAA" class="RedBG">&nbsp;</td>
							<td>Pembayaran Gagal</td>
						</tr>
					</table>
				</div>
			</center>
			<div class="Break"></div>
			<center>
				<header>
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
						</div>
					</div>
				</header>
			</center>
			<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<tr>
					<th>No</th>
					<th>NIM</th>
					<th>Nama</th>
					<th>Jml Bayar</th>
					<th>Tgl Bayar</th>
					<th>Bank</th>
					<th>Keterangan</th>
					<th>No</th>
					<th>Status</th>
				</tr>
				<?php
					$i = 0;
					foreach($a_data as $row) {
						if(substr($row['status'],0,2) == 'OK') {
							if(substr($row['status'],0,11) == 'OK - Kurang')
								$class = 'YellowBG';
							else if(substr($row['status'],0,10) == 'OK - Pakai')
								$class = 'BlueBG';
							else
								$class = 'GreenBG';
						}
						else
							$class = 'RedBG';
				?>
				<tr valign="top" class="<?php echo $class ?>">
					<td><?php echo ++$i ?></td>
					<td style="text-align:center"><?php echo $row['nim'] ?></td>
					<td><?php echo $row['nama'] ?></td>
					<td style="text-align:right"><?php echo CStr::formatNumber($row['jumlahbayar']) ?></td>
					<td style="text-align:center"><?php echo CStr::formatDateTimeInd($row['tglbayar'],false) ?></td>
					<td><?php echo $row['bank'] ?></td>
					<td><?php echo $row['keterangan'] ?></td>
					<td><?php echo $row['notrans'] ?></td>
					<td><?php echo $row['status'] ?></td>
				</tr>
				<?php } ?>
			</table>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goUpload(){
	document.getElementById("act").value = "upload";
	goSubmit();
}

</script>
</body>
</html>
