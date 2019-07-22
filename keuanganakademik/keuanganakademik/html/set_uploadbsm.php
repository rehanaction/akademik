<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('pembayaran'));
	require_once(Route::getModelPath('historyupload'));
	
	// properti halaman
	$p_title = 'Hasil Upload Pembayaran';
	$p_aktivitas = 'SUBMIT';
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'upload') {
		// ambil phpexcel
		include('../includes/phpexcel/PHPExcel/IOFactory.php');
		
		$xls = PHPExcel_IOFactory::load($_FILES['excel']['tmp_name']);
		$data = $xls->getActiveSheet()->toArray(null,true,true,true);
		
		// untuk waktu upload
		$now = date('Y-m-d H:i:s');
		
		$i = 0;
		$err = 0;
		$errnum = 0;
		$a_result = array();
		foreach($data as $rowd) {
			$i++;
			
			// skip pertama karena header
			if($i == 1)
				continue;
			
			// masukkan pembayaran, transaksi per pembayaran
			$conn->BeginTrans();
			
			// di-trim
			$row = array();
			// foreach($rowd as $r => $col)
			for($r=0;$r<=6;$r++)
				$row[$r] = trim($rowd[$r]);
			
			// tidak ada nim, continue, eh break
			if(empty($row[0]))
				break; // continue;
			
			// no trans set integer
			$row[6] = (int)$row[6];
			
			list($err,$msg) = mPembayaran::bayarBSM($conn,$row[0],$row[2],$row[3],$row[6]); // nim, jumlah, tanggal, no trans
			if($err)
				$errnum++;
			
			// tulis hasil
			$row[] = ($err ? 'GAGAL' : 'OK').(empty($msg) ? '' : ' - '.$msg);
			
			// simpan log
			if($conn->ErrorNo() == 0) {
				$record = array();
				$record['nim'] = CStr::cStrNull($row[0]);
				$record['jumlahbayar'] = CStr::cStrNull($row[2]);
				$record['tglbayar'] = CStr::cStrNull($row[3]);
				$record['bank'] = CStr::cStrNull($row[4]);
				$record['keterangan'] = CStr::cStrNull($row[5]);
				$record['iserror'] = ($err ? 1 : 0);
				$record['notrans'] = CStr::cStrNull($row[6]);
				$record['status'] = CStr::cStrNull($row[7]);
				$record['uploadtime'] = $now;
				
				$err = mHistoryUpload::insertRecord($conn,$record);
			}
			
			$conn->CommitTrans($err ? false : true);
			
			$a_result[] = $row;
		}
		
		@unlink($_FILES['excel']['tmp_name']);
		
		$p_posterr = (empty($errnum) ? false : true);
		if($p_posterr)
			$p_postmsg = "Ada <strong>$errnum</strong> pembayaran yang gagal dimasukkan";
		else
			$p_postmsg = "Upload Data Pembayaran Berhasil";
	}
	else if($r_act == 'test') {
		// ambil phpexcel
		include('../includes/phpexcel/PHPExcel/IOFactory.php');
		
		$xls = PHPExcel_IOFactory::load("/home/www/esademo/doc/upload_pembayaran.xls");
		$data = $xls->getActiveSheet()->toArray(null,true,true,true);
		
		// untuk waktu upload
		$now = date('Y-m-d H:i:s');
		
		$i = 0;
		$err = 0;
		$errnum = 0;
		$a_result = array();
		foreach($data as $rowd) {
			$i++;
			
			// skip pertama karena header
			if($i == 1)
				continue;
			
			echo '<h1>'.($i-1).'</h1>';
			
			// masukkan pembayaran, transaksi per pembayaran
			$conn->BeginTrans();
			
			// di-trim
			$row = array();
			// foreach($rowd as $r => $col)
			for($r=0;$r<=6;$r++)
				$row[$r] = trim($rowd[$r]);
			
			// tidak ada nim, continue, eh break
			if(empty($row[0]))
				break; // continue;
			
			// no trans set integer
			$row[6] = (int)$row[6];
			
			list($err,$msg) = mPembayaran::bayarBSM($conn,$row[0],$row[2],$row[3],$row[6]); // nim, jumlah, tanggal, no trans
			if($err)
				$errnum++;
			
			// tulis hasil
			$row[] = ($err ? 'GAGAL' : 'OK').(empty($msg) ? '' : ' - '.$msg);
			
			// simpan log
			if($conn->ErrorNo() == 0) {
				$record = array();
				$record['nim'] = CStr::cStrNull($row[0]);
				$record['jumlahbayar'] = CStr::cStrNull($row[2]);
				$record['tglbayar'] = CStr::cStrNull($row[3]);
				$record['bank'] = CStr::cStrNull($row[4]);
				$record['keterangan'] = CStr::cStrNull($row[5]);
				$record['iserror'] = ($err ? 1 : 0);
				$record['notrans'] = CStr::cStrNull($row[6]);
				$record['status'] = CStr::cStrNull($row[7]);
				$record['uploadtime'] = $now;
				
				$err = mHistoryUpload::insertRecord($conn,$record);
			}
			
			$conn->CommitTrans(false); // $err ? false : true);
			
			$a_result[] = $row;
		}
		
		$p_posterr = (empty($errnum) ? false : true);
		if($p_posterr)
			$p_postmsg = "Ada <strong>$errnum</strong> pembayaran yang gagal dimasukkan";
		else
			$p_postmsg = "Testing Upload Data Pembayaran Berhasil";
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
					<div class="filterTable" style="float:left;width:65%">
						<table width="100%" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top">
									<table cellspacing="0" cellpadding="4">
                                        <tr>
											<td><strong>Upload Excel Pembayaran</strong></td>
                                        </tr>
										<tr>
											<td>Pilih file excel berisi pembayaran mahasiswa yang diperoleh dari bank pada isian di bawah ini kemudian klik Upload</td>
                                        </tr>
										<tr>
											<td>
												<input type="file" name="excel" size="50">
												<input type="button" value="Upload" onClick="goUpload()">
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
					<div class="filterTable" style="float:right">
						<table width="100%" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top">
									<table cellspacing="0" cellpadding="4">
                                        <tr>
											<td><strong>Template Excel</strong></td>
                                        </tr>
										<tr>
											<td>Pastikan file yang diupload sesuai dengan template ini</td>
                                        </tr>
										<tr>
											<td style="padding:8px 4px"><a href="template/upload_pembayaran.xls">Download Template</a></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
					<div style="clear:both"></div>
				</center>
				<?php if(!empty($p_postmsg)) { ?>
				<br />
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>">
					<?= $p_postmsg ?>
				</div>
				</center>
				<?php } ?>
				<?php if(!empty($a_result)) { ?>
				<br />
				<center>
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
						foreach($a_result as $row) {
							if(substr($row[7],0,2) == 'OK') {
								if(substr($row[7],0,11) == 'OK - Kurang')
									$class = 'YellowBG';
								else if(substr($row[7],0,10) == 'OK - Pakai')
									$class = 'BlueBG';
								else
									$class = 'GreenBG';
							}
							else
								$class = 'RedBG';
					?>
					<tr valign="top" class="<?php echo $class ?>">
						<td><?php echo ++$i ?></td>
						<td style="text-align:center"><?php echo $row[0] ?></td>
						<td><?php echo $row[1] ?></td>
						<td style="text-align:right"><?php echo CStr::formatNumber($row[2]) ?></td>
						<td style="text-align:center"><?php echo CStr::formatDateTimeInd($row[3],false) ?></td>
						<td><?php echo $row[4] ?></td>
						<td><?php echo $row[5] ?></td>
						<td><?php echo $row[6] ?></td>
						<td><?php echo $row[7] ?></td>
					</tr>
					<?php } ?>
				</table>
				<?php } ?>
				<input type="hidden" name="act" id="act">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

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
