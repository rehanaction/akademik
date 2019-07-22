<?php
	// model supplier
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSupplier extends mModel {
		const schema = 'aset';
		const table = 'ms_supplier';
		const order = 'namasupplier';
		const key = 'idsupplier';
		const label = 'Supplier';
		
	    //list supplier
	    function listQuery() {
		    $sql = "select idsupplier,namasupplier,jenissupplier,alamat,notlp,nohp,email,namacp,siup,npwp,isblacklist
				    from ".self::table()." s 
				    left join ".static::schema.".ms_jenissupplier j on j.idjenissupplier = s.idjenissupplier";
		
		    return $sql;
	    }
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'jenissupplier': 
				    return "s.idjenissupplier = '$key'";
			    break;
			}
		}
		
		function getNamaSupplier($conn,$key){
		    return empty($key) ? '' : $conn->GetOne("select namasupplier from ".self::table()." where idsupplier = '$key'");
		}
		
		/**************************************** INTEGRASI SIM KEUANGAN KEU.MS_REKANAN -> ASET.MS_SUPPLIER *******************************************/
		
		function saveSyncKeu($conn,$connkeu,$r_key){
			// Select data Supplier dari MsSQL
			$record = $conn->GetRow("select * from aset.ms_supplier where idsupplier = '$r_key'");
			
			$reckeu = array();
			$reckeu['idsuplier'] = $record['idsupplier'];
			$reckeu['namarekanan'] = $record['namasupplier'];
			$reckeu['alamatrekanan'] = $record['alamat'];
			$reckeu['telprekanan'] = $record['notlp'];
			$reckeu['hprekanan'] = $record['nohp'];
			$reckeu['kodepos'] = $record['kodepos'];
			$reckeu['fax'] = $record['nofax'];
			$reckeu['email'] = $record['email'];
			$reckeu['namacp'] = $record['namacp'];
			$reckeu['npwprekanan'] = $record['npwp'];
			$reckeu['siup'] = $record['siup'];
						
			$isExist = $connkeu->GetRow("select 1 from keu.ms_rekanan where idsuplier = ".$r_key." ");
			if(!$isExist){
				$err = Query::recInsert($connkeu,$reckeu,'keu.ms_rekanan');
			}else{
				$err = Query::recUpdate($connkeu,$reckeu,'keu.ms_rekanan'," idsuplier = ".$r_key." ");
			}
			
			return $err;
		}
		
		function getKey($conn){
			$sql = $conn->GetOne("select top 1 idsupplier from aset.ms_supplier order by idsupplier desc");
			
			return $sql;
		}
		
		function deleteSyncKeu($conn,$connkeu,$r_key){
			$err = Query::qDelete($connkeu,'keu.ms_rekanan'," idsuplier = ".$r_key." ");
			
			return $err;
			
		}
		
		/**************************************** END INTEGRASI SIM KEUANGAN KEU.MS_REKANAN -> ASET.MS_SUPPLIER *******************************************/
	}
?>
