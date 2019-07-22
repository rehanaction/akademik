<?php
	// cek akses halaman
	defined('__VALID_ENTRANCE') or die('Akses terbatas');
	
	// ambil data notifikasi
	$data = Pay::getPaymentNotification();
	
	if($data !== false) {
		// include
		require_once(Route::getModelPath('tagihanva'));
		
		// mulai transaksi
		$conn->BeginTrans();
		
		// update status lunas
		$record = array();
		$record['status'] = 'L';
		
		$err = mTagihanVA::updateRecord($conn,$record,$data['trx_id']);
		
		// masukkan pembayaran
		if(empty($err))
			$err = mTagihanVA::bayarVA($conn,$data['trx_id'],$data);
		
		// selesai transaksi
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
	}
	
	if($ok)
		$response = array('status' => Pay::ERROR_OK);
	else
		$response = array('status' => Pay::ERROR_INTERNAL);
	
	ob_clean();
	
	echo json_encode($response);
?>