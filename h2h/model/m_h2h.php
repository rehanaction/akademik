<?php
	class mH2H {
		// database
		function isDatabaseError($conn) {
			if(!$conn)
				return 'ERROR_DB';
			else if(!$conn->isConnected())
				return 'ERROR_DB_CONN';
			else
				return false;
		}
		
		// bank
		function getListBank($conn,$kodebank=null) {
			$sql = "select * from h2h.ms_bank";
			
			if(!empty($kodebank)) {
				$sql .= " where bankcode = ?";
				
				return $conn->GetArray($sql,array($kodebank));
			}
			else
				return $conn->GetArray($sql);
		}
		
		// rekon
		function getStrTrans($conn,$kodebank,$ymd) {
			$sql = "select * from
					((select to_char(b.transmissiontime,'YYYYMMDDHH24MISS') as tglbayar, b.companycode, b.kodebayar, b.jenistagihan, b.periodebayar, b.refno, '0' as notoken, b.jumlahbayar, b.idpembayaran, b.nim
					from h2h.ke_pembayaran b
					where to_char(b.tglbayar,'YYYYMMDD') = ? and b.companycode = ? and b.ish2h = '1' and b.flagbatal = '0')
					union all
					(select to_char(b.transmissiontime,'YYYYMMDDHH24MISS'), b.companycode, b.kodebayar, '".H2H_KODEFORMULIR."', b.periodebayar, b.refno, b.notoken, b.jumlahbayar, b.idpembayaranfrm, b.kodeformulir
					from h2h.ke_pembayaranfrm b
					where to_char(b.tglbayar,'YYYYMMDD') = ? and b.companycode = ? and b.ish2h = '1' and b.flagbatal = '0')) x
					order by x.tglbayar";
			$rs = $conn->Execute($sql,array($ymd,$kodebank,$ymd,$kodebank));
			
			$total = 0;
			$str = array();
			while($row = $rs->FetchRow()) {
				// ambil periode
				if($row['jenistagihan'] == H2H_KODEFORMULIR)
					$row['periodebayar'] = mTagihan::getPeriodeBayarFormulir($conn,$row['nim']);
				else
					$row['periodebayar'] = mTagihan::getPeriodeBayarTagihan($conn,$row['idpembayaran']);
				
				unset($row['idpembayaran'],$row['nim']);
				
				$str[] = implode('|',$row);
				$total += (float)$row['jumlahbayar'];
			}
			
			// tambahkan row checksum
			$str[] = $ymd.'000000|'.count($str).'|0|0|0|0|0|'.$total;
			
			return implode(PHP_EOL,$str);
		}
		
		// setting
		function getPeriodeSekarang($conn,$jenistagihan) {
			$sql = "select periodesekarang from h2h.ke_settingdetail where jenistagihan = ?";
			
			return $conn->GetOne($sql,array($jenistagihan));
		}
		
		function getReversalTime($conn) {
			$sql = "select reversal_time from h2h.ke_setting where idsetting = 1";
			
			return $conn->GetOne($sql);
		}
		
		function isInquiryOpen($conn,$jenistagihan) {
			$sql = "select allow_inquiry from h2h.ke_settingdetail where jenistagihan = ?";
			$cek = $conn->GetOne($sql,array($jenistagihan));
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		function isPaymentOpen($conn,$jenistagihan) {
			$sql = "select allow_payment from h2h.ke_settingdetail where jenistagihan = ?";
			$cek = $conn->GetOne($sql,array($jenistagihan));
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		function isReversalOpen($conn,$jenistagihan) {
			$sql = "select allow_reversal from h2h.ke_settingdetail where jenistagihan = ?";
			$cek = $conn->GetOne($sql,array($jenistagihan));
			
			if(empty($cek))
				return false;
			else
				return true;
		}
		
		// status
		function getStatusResponse($kode) {
			$statusResponse = array(
				'SUCCESS' => array ('isError'=>false,
								 'errorCode'=>'00',
								 'statusDescription'=>'Success'),
				'ERROR_UNKNOWN' => array ('isError'=>true,
								 'errorCode'=>'01',
								 'statusDescription'=>'Unknown error'),
				'ERROR_DB' => array ('isError'=>true,
								 'errorCode'=>'02',
								 'statusDescription'=>'Database error'),
				'ERROR_DB_CONN' => array ('isError'=>true,
								 'errorCode'=>'03',
								 'statusDescription'=>'Database connection error'),
				'ERROR_NO_DATA' => array ('isError'=>true,
								 'errorCode'=>'04',
								 'statusDescription'=>'Bill is not available'),
				'ERROR_NO_INQ' => array ('isError'=>true,
								 'errorCode'=>'05',
								 'statusDescription'=>'Inquiry is not allowed at the moment'),
				'ERROR_NO_PAY' => array ('isError'=>true,
								 'errorCode'=>'06',
								 'statusDescription'=>'Payment is not allowed at the moment'),
				'ERROR_NO_REV' => array ('isError'=>true,
								 'errorCode'=>'07',
								 'statusDescription'=>'Reversal is not allowed at the moment'),
				'ERROR_BILL_PENDING' => array ('isError'=>true,
								 'errorCode'=>'08',
								 'statusDescription'=>'Bill is in pending at the moment'),
				'ERROR_BILL_PAID' => array ('isError'=>true,
								 'errorCode'=>'09',
								 'statusDescription'=>'Bill is already paid'),
				'ERROR_BILL_NOT_PAID' => array ('isError'=>true,
								 'errorCode'=>'10',
								 'statusDescription'=>'Bill is not paid yet'),
				'ERROR_AMOUNT_UNDER' => array ('isError'=>true,
								 'errorCode'=>'11',
								 'statusDescription'=>'Amount is under paid'),
				'ERROR_AMOUNT_OVER' => array ('isError'=>true,
								 'errorCode'=>'12',
								 'statusDescription'=>'Amount is over paid'),
				'ERROR_REV_EXPIRED' => array ('isError'=>true,
								 'errorCode'=>'13',
								 'statusDescription'=>'Reversal time is expired'),
				'ERROR_REV_AMOUNT_DIFF' => array ('isError'=>true,
								 'errorCode'=>'14',
								 'statusDescription'=>'Reversal amount is different'),
				'ERROR_REV_DONE' => array ('isError'=>true,
								 'errorCode'=>'15',
								 'statusDescription'=>'Reversal is already done'),
				'ERROR_TRX_PERIODE' => array ('isError'=>true,
								 'errorCode'=>'16',
								 'statusDescription'=>'Transactions retrieval frequency is limited'),
				'ERROR_NO_NIM' => array ('isError'=>true,
								 'errorCode'=>'17',
								 'statusDescription'=>'Student is not exists')
			);
			
			$status = $statusResponse[$kode];
			if(empty($status))
				$status = $statusResponse['ERROR_UNKNOWN'];
			
			return $status;
		}
		
		function getStatusDefault() {
			return self::getStatusResponse('ERROR_UNKNOWN');
		}
		
		function getResponse($return,$kode,$conn=null,$log=null,$err=null) {
			$return['status'] = self::getStatusResponse($kode);
			
			// sekalian masukkan log
			if(isset($conn) and isset($log)) {
				if(isset($err))
					$log['ket'] = $err;
				else if($return['status']['isError'])
					$log['ket'] = $return['status']['statusDescription'];
				
				self::insertLog($conn,$log);
				
				// log input juga
				global $input;
				
				$trace = debug_backtrace();
				
				self::insertInputLog($conn,$trace[1]['function'],$return,0);
			}
			
			return $return;
		}
		
		// action log
		function initLog($input,$typesvc,$jenistagihan,$periode) {
			$log = array();
			$log['jenistagihan'] = $jenistagihan;
			$log['nim'] = (empty($input['nim']) ? $input['pilihan'] : $input['nim']);
			$log['ish2h'] = '1';
			$log['typeinq'] = $typesvc;
			$log['periode'] = $periode;
			$log['companycode'] = $input['companyCode'];
			$log['channelid'] = $input['channelID'];
			
			return $log;
		}
		
		function insertLog($conn,$record) {
			$record = Helper::addLog($record);
			
			return $conn->AutoExecute('h2h.translog',$record,'INSERT');
		}
		
		function insertInputLog($conn,$method,$input,$isinput=1) {
        	
			$record = array();
			$record['method'] = $method;
			$record['isinput'] = $isinput;
			$record['input'] = json_encode($input);
			$record = Helper::addLog($record);
			
			return $conn->AutoExecute('h2h.inputtranslog',$record,'INSERT');
		}
	}
?>