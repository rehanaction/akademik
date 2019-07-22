<?php
	require_once('init.php');
	
	function inquiry($input) {//sleep(80);
		// koneksi, karena parameter default sesuai wsdl
		global $conn;
		
		// input log
		mH2H::insertInputLog($conn,'inquiry',$input);
		
		// response, status diset di method
		$return = array(
					'billCode'=>'-1',
					'nim'=>'-1',
					'nama'=>'-1',
					'jurusan'=>'-1',
					'gelombang'=>'-1',
					'jalur'=>'-1',
					'keterangan'=>'-1',
					'numBill'=>0,
					'billDetails'=>false
				);
		
		// pengecekan koneksi database
		$err = mH2H::isDatabaseError($conn);
		if($err !== false)
			return mH2H::getResponse($return,$err);
		
		// inisialisasi log
		$kodetagihan = $input['typeInq'];
		$jenistagihan = mTagihan::getJenisTagihanFromKode($conn,$kodetagihan);
		$periode = mH2H::getPeriodeSekarang($conn,$jenistagihan);
		
		$log = mH2H::initLog($input,'inq',$jenistagihan,$periode);
		
		// pengecekan buka
		$cek = mH2H::isInquiryOpen($conn,$jenistagihan);
		if(empty($cek))
			return mH2H::getResponse($return,'ERROR_NO_INQ',$conn,$log);
		
		// cek input
		$nim = $input['nim'];
		
		// hardcoded timeout
		if($nim == '123')
			sleep(100);
		
		// pengecekan formulir
		if($jenistagihan == H2H_JENISFORMULIR) {
			// pengecekan tarif
			$pilihan = $nim;
			$info = mTagihan::getInfoTarifFormulir($conn,$pilihan);
			
			// inquiry
			list($data,$err,$msg) = mTagihan::getListInquiryFormulir($conn,$pilihan,true);
			if($data === false)
				return mH2H::getResponse($return,$err,$conn,$log,$msg);
			
			// tambahan keterangan response
			$info['nama'] = '-1';
			$info['namaunit'] = '-1';
		}
		else {
			// harus ada nim
			if(empty($nim))
				return mH2H::getResponse($return,'ERROR_NO_NIM',$conn,$log,'no nim');
			
			// pengecekan mahasiswa
			$info = mAkademik::getInfoMahasiswa($conn,$nim);
			if(empty($info))
				return mH2H::getResponse($return,'ERROR_NO_NIM',$conn,$log,'nim not exist');
			
			// inquiry
			list($data,$err,$msg) = mTagihan::getListInquiry($conn,$nim,$jenistagihan,$periode,true);
			if($data === false)
				return mH2H::getResponse($return,$err,$conn,$log,$msg);
			
			// tambahan keterangan response
			$info['keterangan'] = '-1';
		}
		
		// final response, status diset di method
		$return = array(
					'billCode'=>$jenistagihan,
					'nim'=>$nim,
					'nama'=>$info['nama'],
					'jurusan'=>$info['namaunit'],
					'gelombang'=>$info['gelombang'],
					'jalur'=>$info['jalurpenerimaan'],
					'keterangan'=>$info['keterangan'],
					'numBill'=>count($data),
					'billDetails'=>$data
				);
		
		return mH2H::getResponse($return,'SUCCESS',$conn,$log);
	}
?>