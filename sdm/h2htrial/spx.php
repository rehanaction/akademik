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
			$ttrans = explode(PHP_EOL,$trans);
			
			// mengambil file transaksi
			$file = $conf['rekon_dir'].$row['dirrekon'].'/trans/'.$bankcode.'_'.$tgl.'.txt';
			$trans = trim(file_get_contents($file));
			$atrans = explode(PHP_EOL,$trans);
			
			// tidak pakai baris akhir (checksum)
			array_pop($ttrans);
			array_pop($atrans);
			
			// proses file transaksi
			$total = 0;
			$spx = array();
			foreach($atrans as $line) {
				$found = false;
				foreach($ttrans as $i => $cek) {
					// ambil kolom tertentu
					$rowl = explode('|',$line);
					$rowc = explode('|',$cek);
					
					// tanggal tanpa waktu
					$rowl[0] = substr($rowl[0],0,8);
					$rowc[0] = substr($rowc[0],0,8);
					
					// tanpa periode
					unset($rowl[4],$rowc[4]);
					
					// non formulir tanpa token
					if($rowl[3] != H2H_KODEFORMULIR)
						unset($rowl[6],$rowc[6]);
					
					// gabungkan
					$cline = implode('|',$rowl);
					$ccek = implode('|',$rowc);
					
					// bandingkan
					if($cline == $ccek) {
						$found = true;
						unset($ttrans[$i]);
						break;
					}
				}
				
				if(empty($found)) {
					$total += $rowl[7];
					
					$spx[] = 'P|'.$line;
				}
			}
			
			// proses transaksi
			foreach($ttrans as $line) {
				$rowl = explode('|',$line);
				$total += $rowl[7];
				
				$spx[] = 'R|'.$line;
			}
			
			// tambahkan row checksum dari yang sudah di-pop
			$spx[] = '0|'.$tgl.'000000|'.count($spx).'|0|0|0|0|0|0|'.$total;
			$spx = implode(PHP_EOL,$spx);
			
			echo nl2br($spx);
			
			// tulis file
			$file = $conf['rekon_dir'].$row['dirrekon'].'/spx/'.$bankcode.'_'.$tgl.'.spx';
			
			@unlink($file);
			file_put_contents($file,$spx);
		}
	}
?>