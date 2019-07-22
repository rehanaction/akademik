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
			// mengambil file suspect
			$file = $conf['rekon_dir'].$row['dirrekon'].'/spx/'.$bankcode.'_'.$tgl.'.spx';
			$spx = trim(file_get_contents($file));
			$aspx = explode(PHP_EOL,$spx);
			
			// tidak pakai baris akhir (checksum)
			$cs = array_pop($aspx);
			
			$rcn = array();
			foreach($aspx as $line) {
				$line = trim($line);
				$rowl = explode('|',$line);
				
				// proses rekon
				$act = $rowl[0]; // P: Payment, R: Reversal
				
				// membuat tanggal
				$tanggal = $rowl[1];
				$adt = str_split($tanggal,2);
				$dt = $adt[0].$adt[1].'-'.$adt[2].'-'.$adt[3].' '.$adt[4].':'.$adt[5].':'.$adt[6];
				
				// jenis tagihan
				$kodetagihan = $rowl[4];
				$jenistagihan = mTagihan::getJenisTagihanFromKode($conn,$kodetagihan);
				
				// data lain
				$bank = $rowl[2];
				$nim = $rowl[3];
				$refno = $rowl[6];
				$jumlah = (float)$rowl[8];
				
				$ok = true;
				if($act == 'P') {
					// cek apakah ada refno
					if($jenistagihan == H2H_JENISFORMULIR)
						$rowb = mTagihan::getPembayaranFormulirFromRefNo($conn,$refno);
					else
						$rowb = mTagihan::getPembayaranFromRefNo($conn,$refno);
					
					if(empty($rowb['refno'])) {
						$periode = mH2H::getPeriodeSekarang($conn,$jenistagihan);
						
						if($jenistagihan == H2H_JENISFORMULIR)
							$data = mTagihan::getListInquiryFormulir($conn,$nim);
						else
							$data = mTagihan::getListInquiry($conn,$nim,$jenistagihan,$periode);
						
						require_once('payment.php');
						
						$input = array(
							'nim'=>$nim,
							'billCode'=>$jenistagihan,
							'transactionID'=>$refno,
							'paymentAmount'=>$jumlah,
							'companyCode'=>$bank,
							'channelID'=>'RECON',
							'terminalID'=>'1',
							'trxDateTime'=>$dt,
							'transmissionDateTime'=>$dt,
							'numBill'=>count($data),
							'billDetails'=>$data,
							'rekon'=>true
						);
						
						$ret = payment($input);
					
						// ambil pesan error
						switch($ret['status']['errorCode']) {
							case '00': $retc = 0; break;
							case '09': $retc = 2; break;
							case '11': $retc = 4; break;
							case '12': $retc = 5; break;
							case '04':
							default: $retc = 1; // dibuat default saja
						}
					}
					else {
						// membatalkan pembatalan payment
						if($jenistagihan == H2H_JENISFORMULIR)
							$retc = mTagihan::bayarFormulirFromRefNo($conn,$refno,$jumlah);
						else
							$retc = mTagihan::bayarFromRefNo($conn,$refno,$jumlah);
					}
				}
				else if($act == 'R') {
					// membuat input
					require_once('reversal.php');
					
					$input = array(
						'billCode'=>$jenistagihan,
						'nim'=>$nim,
						'notoken'=>$rowl[7],
						'transactionID'=>$refno,
						'paymentAmount'=>$jumlah,
						'companyCode'=>$bank,
						'channelID'=>'RECON',
						'terminalID'=>'1',
						'trxDateTime'=>date('Y-m-d H:i:s'),
						'transmissionDateTime'=>date('Y-m-d H:i:s'),
						'currency'=>H2H_CURRENCY,
						'origTrxDateTime'=>$dt,
						'origTransmissionDateTime'=>$dt,
						'rekon'=>true
					);
					
					$ret = reversal($input);
					
					// ambil pesan error
					switch($ret['status']['errorCode']) {
						case '00': $retc = 0; break;
						case '15': $retc = 2; break;
						case '04':
						default: $retc = 1; // dibuat default saja
					}
				}
				
				$rcn[] = $retc.'|'.$line;
			}
			
			// tambahkan row checksum dari yang sudah di-pop
			$rcn[] = $cs;
			$rcn = implode(PHP_EOL,$rcn);
			
			echo nl2br($rcn);
			
			// tulis file
			$file = $conf['rekon_dir'].$row['dirrekon'].'/rcn/'.$bankcode.'_'.$tgl.'.rcn';
			
			@unlink($file);
			file_put_contents($file,$rcn);
		}
	}
?>