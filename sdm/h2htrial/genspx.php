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
			// mengambil file trans
			$file = $conf['rekon_dir'].$row['dirrekon'].'/trans/'.$bankcode.'_'.$tgl.'.txt';
			$trans = file_get_contents($file);
			$atrans = explode(PHP_EOL,$trans);
			
			// tidak pakai baris akhir (checksum)
			array_pop($atrans);
			
			$total = 0;
			$spx = array();
			foreach($atrans as $line) {
				$line = trim($line);
				$rowl = explode('|',$line);
				
				// dirandom, 0: dibiarkan, 1: direversal
				$rnd = mt_rand(0,1);
				if($rnd == 1) {
					$spx[] = 'R|'.$line;
					$total += $rowl[7];
					
					// dirandom, 0: dibiarkan, 1: dipayment
					$rnd = mt_rand(0,1);
					if($rnd == 1) {
						// diubah beberapa
						$rowl[5] = Helper::randomString(20);
						if(!empty($rowl[6]))
							$rowl[6] = mTagihan::getNoToken($conn);
						
						$line = implode('|',$rowl);
						$spx[] = 'P|'.$line;
						$total += $rowl[7];
					}
				}
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