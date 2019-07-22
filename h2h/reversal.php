<?php
	require_once('init.php');
	
	function reversal($input) {
		// koneksi, karena parameter default sesuai wsdl
		global $conn;
		
		// input log
		mH2H::insertInputLog($conn,'reversal',$input);
		
		// response, status diset di method
		$return = array(
					'billRemain'=>'-1'
				);
		
		// pengecekan koneksi database
		$err = mH2H::isDatabaseError($conn);
		if($err !== false)
			return mH2H::getResponse($return,$err);
		
		// inisialisasi log
		$jenistagihan = $input['billCode'];
		$periode = mH2H::getPeriodeSekarang($conn,$jenistagihan);
		
		$log = mH2H::initLog($input,'rev',$jenistagihan,$periode);
		
		// pengecekan buka
		$cek = mH2H::isReversalOpen($conn,$jenistagihan);
		if(empty($cek))
			return mH2H::getResponse($return,'ERROR_NO_REV',$conn,$log);
		
		// cek pembayaran
		$refno = $input['transactionID'];
		if($jenistagihan == H2H_JENISFORMULIR)
			$data = mTagihan::getPembayaranFormulirFromRefNo($conn,$refno);
		else
			$data = mTagihan::getPembayaranFromRefNo($conn,$refno);
		
		if(empty($data))
			return mH2H::getResponse($return,'ERROR_NO_DATA',$conn,$log,'pembayaran not exists');
		if(!empty($data['flagbatal']))
			return mH2H::getResponse($return,'ERROR_REV_DONE',$conn,$log,'rev already done');
		if((float)$input['paymentAmount'] != (float)$data['jumlahbayar'])
			return mH2H::getResponse($return,'ERROR_REV_AMOUNT_DIFF',$conn,$log,'rev amount different');
		
		// cek waktu reversal
		if(empty($input['rekon'])) {
			$maxtime = mH2H::getReversalTime($conn);
			if(!empty($maxtime)) {
				list($tgl,$waktu) = explode(' ',$data['tglbayar']); // bukan trxdatetime atau input:origTrxDateTime
				list($y,$m,$d) = explode('-',$tgl);
				list($h,$i,$s) = explode(':',$waktu);
				
				$timepay = mktime((int)$h,(int)$i,(int)$s,(int)$m,(int)$d,(int)$y);
				
				/* list($tgl,$waktu) = explode(' ',$input['trxDateTime']);
				list($y,$m,$d) = explode('-',$tgl);
				list($h,$i,$s) = explode(':',$waktu);
				
				$timerev = mktime((int)$h,(int)$i,(int)$s,(int)$m,(int)$d,(int)$y); */
				$timerev = time();
				
				// bila hari senin boleh mereversal sampai hari jumat sebelumnya
				if(date('N',$timepay) == 5 and date('N',$timerev) == 1)
					$maxtime += 172800; // tambah 2 hari
				
				if($timerev-$timepay > $maxtime)
					return mH2H::getResponse($return,'ERROR_REV_EXPIRED',$conn,$log,'reversal expired');
			}
		}
		
		// reversal
		$log['refno'] = $input['transactionID']; // disamakan
		
		$record = array();
		$record['flagbatal'] = 1;
		
		// cek rekon
		if(!empty($input['rekon'])) {
			$record['flagrekon'] = 1;
			$record['trekontime'] = date('Y-m-d H:i:s');
		}
		
		if($jenistagihan == H2H_JENISFORMULIR)
			$ok = mTagihan::updatePembayaranFormulir($conn,$record,$data['idpembayaranfrm']);
		else
			$ok = mTagihan::updatePembayaran($conn,$record,$data['idpembayaran']);
		
		if(!$ok) {
			$msg = $conn->ErrorMsg();
			if(empty($msg))
				$msg = 'error';
			else
				$msg = substr($msg,0,H2H_LOGKETLENGTH);
			
			return mH2H::getResponse($return,'ERROR_DB',$conn,$log,$msg);
		}
		
		// final response, status diset di method
		$return = array(
					'billRemain'=>0 // selalu tepat
				);
		
		return mH2H::getResponse($return,'SUCCESS',$conn,$log);
	}
?>