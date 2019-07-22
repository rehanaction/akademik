<?php
	require_once('init.php');
	
	function payment($input) {//sleep(80); //exit();
		// koneksi, karena parameter default sesuai wsdl
		global $conn;
		
		// input log
		mH2H::insertInputLog($conn,'payment',$input);
		
		// response, status diset di method
		$jumlahbayar = (float)$input['paymentAmount'];
		
		$return = array(
					'nim'=>'-1',
					'notoken'=>'-1',
					'billCode'=>'-1',
					'billName'=>'-1',
					'transactionID'=>'-1',
					'paymentAmount'=>'-1',
					'billRemain'=>'-1'
				);
		
		// pengecekan koneksi database
		$err = mH2H::isDatabaseError($conn);
		if($err !== false)
			return mH2H::getResponse($return,$err);
		
		// inisialisasi log
		$jenistagihan = $input['billCode'];
		$periode = mH2H::getPeriodeSekarang($conn,$jenistagihan);
		
		$log = mH2H::initLog($input,'pay',$jenistagihan,$periode);
		
		// pengecekan buka
		$cek = mH2H::isPaymentOpen($conn,$jenistagihan);
		if(empty($cek))
			return mH2H::getResponse($return,'ERROR_NO_PAY',$conn,$log);
		
		// cek input
		$nim = $input['nim'];
		$transid = $input['transactionID'];
		
		$refno = $transid; // disamakan
		$log['refno'] = $refno;
		
		// pengecekan formulir
		if($jenistagihan == H2H_JENISFORMULIR) {
			// mengambil tarif
			$pilihan = $input['nim'];
			
			list($data,$err,$msg) = mTagihan::getListInquiryFormulir($conn,$pilihan,true);
			if($data === false)
				return mH2H::getResponse($return,$err,$conn,$log,$msg);
			
			// cek pembayaran
			$tarif = (float)$data[0]['billAmount'];
			
			if($jumlahbayar < $tarif)
				return mH2H::getResponse($return,'BILL_AMOUNT_UNDER',$conn,$log,'bill under - '.$jumlah);
			else if($jumlahbayar > $tarif)
				return mH2H::getResponse($return,'BILL_AMOUNT_OVER',$conn,$log,'bill over - '.$jumlah);
			
			// pembayaran
			$notoken = mTagihan::getNoToken($conn);
			
			$record = array();
			$record['kodeformulir'] = $pilihan;
			$record['idtariffrm'] = $data[0]['billID'];
			$record['tglbayar'] = date('Y-m-d H:i:s');
			$record['jumlahbayar'] = $jumlahbayar;
			$record['notoken'] = $notoken;
			$record['ish2h'] = 1;
			$record['refno'] = $refno;
			$record['idcurrency'] = H2H_CURRENCY;
			$record['periodebayar'] = $periode;
			$record['terminalid'] = $input['terminalID'];
			$record['companycode'] = $input['companyCode'];
			$record['trxdatetime'] = $input['trxDateTime'];
			$record['transmissiontime'] = $input['transmissionDateTime'];
			$record['flagbatal'] = 0;
			
			// cek rekon
			if(!empty($input['rekon'])) {
				$record['flagrekon'] = 1;
				$record['trekontime'] = date('Y-m-d H:i:s');
			}
			
			$ok = mTagihan::insertPembayaranFormulir($conn,$record);
			
			$msg = $conn->ErrorMsg();
		}
		else {
			// pengecekan detail tagihan
			$billdet = $input['billDetails'];
			if(empty($billdet)) {
				// return mH2H::getResponse($return,'ERROR_NO_DATA',$conn,$log,'no bill');
				
				// inquiry ulang
				$billdet = mTagihan::getListInquiry($conn,$nim,$jenistagihan,$periode);
			}
			
			// cek pembayaran
			list($err,$msg) = mTagihan::isPaymentError($conn,$billdet,$jumlahbayar);
			if($err !== false)
				return mH2H::getResponse($return,$err,$conn,$log,$msg);
			
			// pengaturan pembayaran
			$deposit = array();
			$tagihan = array();
			foreach($billdet as $cek) {
				$id = $cek['billID'];
				$name = $cek['billName'];
				$jumlah = (float)$cek['billAmount'];
				
				if($name == 'Deposit' or $name == 'Voucher') {
					if($name == 'Deposit')
						$id = (int)substr($id,3);
					else
						$id = mTagihan::getIDDepositFromNoVoucher($conn,$id);
					
					$deposit[$id] = -1*$jumlah; // sisa deposit
				}
				else
					$tagihan[$id] = $jumlah;
			}
			
			$totalbayar = 0;
			$detbayar = array();
			foreach($tagihan as $idt => $jumlaht) {
				$totalbayar += $jumlaht;
				$sisatagihan = $jumlaht;
				foreach($deposit as $idd => $jumlahd) {
					if($jumlahd <= 0)
						continue;
					
					if($jumlahd >= $sisatagihan) {
						$detbayar[$idd][$idt] = $jumlaht;
						$deposit[$idd] -= $jumlaht;
						
						$sisatagihan = 0;
						break;
					}
					else {
						$detbayar[$idd][$idt] = $jumlahd;
						$deposit[$idd] = 0;
						
						$sisatagihan -= $jumlahd;
					}
				}
				
				if($sisatagihan > 0)
					$detbayar[0][$idt] = $sisatagihan;
			}
			
			// pembayaran
			$notoken = '-1'; // tidak pakai token
			
			$conn->BeginTrans();
			
			$nokuitansi = mTagihan::getNoKuitansi($conn,date('Ym'));
			
			$record = array();
			$record['tglbayar'] = date('Y-m-d H:i:s');
			$record['jumlahbayar'] = $jumlahbayar;
			$record['ish2h'] = 1;
			$record['refno'] = $refno;
			$record['idcurrency'] = H2H_CURRENCY;
			$record['periodebayar'] = $periode;
			$record['companycode'] = $input['companyCode'];
			$record['terminalid'] = $input['terminalID'];
			$record['trxdatetime'] = $input['trxDateTime'];
			$record['transmissiontime'] = $input['transmissionDateTime'];
			$record['flagbatal'] = 0;
			$record['nim'] = $nim;
			$record['jumlahuang'] = $totalbayar;
			$record['jenistagihan'] = $jenistagihan;
			$record['nokuitansi'] = $nokuitansi;
			
			$ok = mTagihan::insertPembayaran($conn,$record);
			
			// cek rekon
			if(!empty($input['rekon'])) {
				$record['flagrekon'] = 1;
				$record['trekontime'] = date('Y-m-d H:i:s');
			}
			
			// detail pembayaran
			if($ok) {
				$record = array();
				$record['idpembayaran'] = $ok;
				
				foreach($detbayar as $id => $detbayarid) {
					if($id == 0)
						unset($record['iddeposit']);
					else
						$record['iddeposit'] = $id;
					
					foreach($detbayarid as $idt => $nominal) {
						$record['idtagihan'] = $idt;
						$record['nominalbayar'] = $nominal;
						
						$ok = mTagihan::insertPembayaranDetail($conn,$record);
						
						if(!$ok)
							break 2;
					}
				}
			}
			
			$msg = $conn->ErrorMsg();
			
			$conn->CommitTrans($ok);
		}
		
		if(!$ok) {
			if(empty($msg))
				$msg = 'error';
			else
				$msg = substr($msg,0,H2H_LOGKETLENGTH);
			
			return mH2H::getResponse($return,'ERROR_DB',$conn,$log,$msg);
		}
		
		// asumsi $jenistagihan == Kode
		$namakelompok = mTagihan::getNamaJenisTagihanFromKode($conn,$jenistagihan);
		
		// final response, status diset di method
		$return = array(
					'nim'=>$nim,
					'notoken'=>$notoken,
					'billCode'=>$jenistagihan,
					'billName'=>$namakelompok,
					'transactionID'=>$transid,
					'paymentAmount'=>$jumlahbayar,
					'billRemain'=>0 // selalu tepat
				);
		
		return mH2H::getResponse($return,'SUCCESS',$conn,$log);
	}
?>