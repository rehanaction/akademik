<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTarif extends mModel {
		const schema = 'h2h';
		const table = 'ke_tarif';
		const order = 'idtarif';
		const key = 'idtarif';
		const label = 'idtarif';
		
	// mendapatkan array data
		function getArraytarif($conn,$periode='',$jalur='',$kodeunit = '',$jenistagihan = '',$sistemkuliah='') {
			$sql = "select * from ".static::table()." where (1=1)";
			
			if($periode <> '')
				$sql .= " and periodetarif = '$periode'";
		//	if($jalur<>'')
				$sql .= " and jalurpenerimaan = '$jalur'";
			if($kodeunit <> '')
				$sql .= " and kodeunit = '$kodeunit'";
			if($jenistagihan <> '')
				$sql .= " and jenistagihan = '".$jenistagihan."'";
			if($sistemkuliah <> '')
				$sql .= " and sistemkuliah = '".$sistemkuliah."'";
		
			return $conn->GetArray($sql);
		}
		
	// row tarif
	function getRowtarif($conn,$periode='',$jalur='',$kodeunit = '',$jenistagihan = '',$sistemkuliah='') {
			$sql = "select * from ".static::table()." where (1=1)";
			
			if($periode <> '')
				$sql .= " and periodetarif = '$periode'";
		//	if($jalur<>'')
				$sql .= " and jalurpenerimaan = '$jalur'";
			if($kodeunit <> '')
				$sql .= " and kodeunit = '$kodeunit'";
			if($jenistagihan <> '')
				$sql .= " and jenistagihan = '".$jenistagihan."'";
			if($sistemkuliah <> '')
				$sql .= " and sistemkuliah = '".$sistemkuliah."'";
		
			return $conn->getRow($sql);
		}
	//get id tarif 
		function getIdtarif($conn,$data){
			$sql = " select idtarif from ".static::table()." where (1=1)";
			foreach($data as $i => $val)
				$sql .= " and ".$i." = '".$val."'";
			
			return $conn->GetOne($sql);
			
			}
	// delete
	function delete($conn,$data){
			$sql = " delete from ".static::table()." where (1=1)";
			foreach($data as $i => $val)
				$sql .= " and ".$i." = '".$val."'";
			
			 $conn->Execute($sql);
			return $err->errorNo;
			}
	//get tarif wisuda		
	function getTarifwisuda($conn,$periode){
			$sql = "select * from ".static::table()." where (1=1)";
			if($periode <> '')
				$sql .= " and periodetarif = '$periode'";
			return $conn->GetArray($sql);
		}
		
	}
?>