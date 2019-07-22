<?php
	require_once('init.php');
	
	$tgl = $_REQUEST['tgl'];
	$bank = $_REQUEST['bank'];
	
	// jika kosong, ambil sekarang
	$hari = 86400;
	if(empty($tgl))
		$tgl = date('Ymd',time()-$hari);
	
	// cek senin
	$time = mktime(0,0,0,substr($tgl,4,2),substr($tgl,6,2),substr($tgl,0,4));
	
	// bila hari senin membuat transaksi dari hari jumat sebelumnya
	if(date('N',$time) == 1) {
		$atgl = array();
		$atgl[] = date('Ymd',$time-(2*$hari));
		$atgl[] = date('Ymd',$time-$hari);
		$atgl[] = $tgl;
	}
	else
		$atgl = array($tgl);
	
	// mengambil bank
	$rows = mH2H::getListBank($conn,$bank);
	foreach($rows as $row) {
		$bankcode = $row['bankcode'];
		
		foreach($atgl as $tgl) {
			// mengambil transaksi
			$trans = mH2H::getStrTrans($conn,$bankcode,$tgl); // format $tgl = Ymd
			
			// tulis file
			$file = $conf['rekon_dir'].$row['dirrekon'].'/trans/'.$bankcode.'_'.$tgl.'.txt';
			
			@unlink($file);
			file_put_contents($file,$trans);
		}
	}
?>