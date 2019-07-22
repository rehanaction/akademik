<?php
	require_once('init.php');
	
	function inquiry($input) {
		// koneksi, karena parameter default sesuai wsdl
		
		global $conn;
		
		// input log
		mH2H::insertInputLog($conn,'inquiry',$input);
		//print_r($input);
        //die();
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
		// 
		
		$err = mH2H::isDatabaseError($conn);
		
		
		if($err !== false)
			return mH2H::getResponse($return,$err);
		
		
		// inisialisasi log
		$kodetagihan = $input['typeInq'];
		//disable untuk memampilkan semua tagihan
		//$jenistagihan = mTagihan::getJenisTagihanFromKode($conn,$kodetagihan);
		$jenistagihan='03';
	
       // print_r($jenistagihan);
        //die();
		$periode = mH2H::getPeriodeSekarang($conn,$jenistagihan);
	
		$log = mH2H::initLog($input,'inq',$jenistagihan,$periode);
		
		
		// pengecekan buka
		$cek = mH2H::isInquiryOpen($conn,$jenistagihan);
		
		if(empty($cek))
			return mH2H::getResponse($return,'ERROR_NO_INQ',$conn,$log);
		// cek input
		$nim = $input['nim'];
		
		$bank = $input['companyCode'];
		if($bank == BANK_BTNS) {
			$basis = $nim[0];
			$nim = substr($nim,1);
		}
		
		// pengecekan formulir
	
		if($kodetagihan == H2H_JENISFORMULIR) {
        
			// pengecekan tarif
			$pilihan = $nim;
			$info = mTagihan::getInfoTarifFormulir($conn,$pilihan);
        

			// cek basis
			if(!empty($basis) and $info['basis'] != $basis)
				return mH2H::getResponse($return,'ERROR_NO_NIM',$conn,$log,'different base');
			
			// inquiry
			list($data,$err,$msg) = mTagihan::getListInquiry($conn,$nim,$periode,true);
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
			
			// cek basis
			if(!empty($basis) and $info['basis'] != $basis)
				return mH2H::getResponse($return,'ERROR_NO_NIM',$conn,$log,'different base');
			
			// inquiry
			list($data,$err,$msg) = mTagihan::getListInquiry($conn,$nim,$periode,true);
			if($data===false){
            	$return = array(
					'billCode'=>$jenistagihan,
					'nim'=>$nim,
					'nama'=>$info['nama'],
					'jurusan'=>$info['namaunit'],
					'gelombang'=>$info['gelombang'],
					'jalur'=>$info['jalurpenerimaan'],
					'keterangan'=>$info['keterangan'],
					'numBill'=>count($data),
        			'BillType'=>$input['jenisTagihan'],
					'billDetails'=>$data
				);
				return mH2H::getResponse($return,$err,$conn,$log,$msg);
             
            }
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
        			'BillType'=>$input['jenisTagihan'],
					'billDetails'=>$data
				);

	return mH2H::getResponse($return,'SUCCESS',$conn,$log);
	}
?>