<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenistagihan extends mModel {
		const schema = 'h2h';
		const table = 'lv_jenistagihan';
		const order = 'jenistagihan';
		const key = 'jenistagihan';
		const label = 'jenistagihan';
		
	// mendapatkan array data
		function getArray($conn,$frekuensitagihan='',$sks='', $nofrm = false, $isd3smu = '') {
			$sql = "select * from ".static::table()." where 1=1 ";
			if (!$nofrm)
				$sql.= " AND jenistagihan<>'FRM'";
			if($frekuensitagihan)
				if(is_array($frekuensitagihan))
					$sql .= " and frekuensitagihan in ('".implode("','",$frekuensitagihan)."')";
				else
					$sql .= " and frekuensitagihan = '".$frekuensitagihan."'";
			if($sks <> '' and $sks <> '0')
				$sql .= " and issks = '".$sks."'";
				
			if (!empty ($isd3smu)){
				if ($isd3smu==1)
						$sql.=" and isd3  =  '-1'";
					else
						$sql.=" and issmu  =  '-1'";					
					
			}
				
			$sql .= " order by ".static::order;
		
			return $conn->GetArray($sql);
		}
		
		function getArrJenisTagihan($conn,$jenis,$sistem){
			
			// $arr_jenis = self::getArray($conn,array('A','B','S','T'),'',false,$jenis,$sistem);
			$arr_jenis = self::getArray($conn,'','',false,$jenis,$sistem);
			foreach($arr_jenis as $i => $v){
				$arr_jenistagihan[$v['jenistagihan']] = $v['jenistagihan'].' - '.$v['namajenistagihan'];
				}
		
			return $arr_jenistagihan;
			}

		function getArrayTagRutin($conn,$frekuensitagihan='',$sks=''){
			$sql = "select * from ".static::table()." where 1=1 AND jenistagihan<>'FRM' and jenistagihan<>'UKT'";
			if($frekuensitagihan)
			    if(is_array($frekuensitagihan))
				$sql .= " and frekuensitagihan in ('".implode("','",$frekuensitagihan)."')";
			    else
				$sql .= " and frekuensitagihan ='".$frekuensitagihan."'";
			if($sks <>'' and $sks<>'0')
			    $sql .= " and issks = '".$sks."'";

			$sql .= " order by ".static::order;
			return $conn->GetArray($sql);
		    
		}
		
		// mendapatkan array data
		function getDatacombo($conn) {
			$sql = "select * from ".static::table()." where 1=1";
			$sql .= " order by ".static::order;
		
			$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
					
						$data[$row['jenistagihan']] = $row['jenistagihan'].' - '.$row['namajenistagihan'];
					}
					
				return $data;
		}
		
		// mendapatkan kode tagihan
		function getKodeTagihan($conn,$jenis) {
			$sql = "select kodetagihan from ".static::table()." where jenistagihan = ".Query::escape($jenis);
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan kode kelompok
		function getKodeKelompok($conn,$jenis) {
			$sql = "select kodekelompok from ".static::table()." where jenistagihan = ".Query::escape($jenis);
			
			return $conn->GetOne($sql);
		}
	}
?>
