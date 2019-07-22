<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMastDinas extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list
		function listQueryRate() {
			$sql = "select t.idrate,t.idtarifrate,rateperjalanan,jabatanstruktural,jnsrate,tarifrate from ".static::table('ms_tarifperjalanan')." t 
					left join ".static::schema()."lv_rateperjalanan j on j.idrate=t.idrate
					left join ".static::schema()."ms_struktural s on s.idjstruktural=t.idjstruktural";
			
			return $sql;
		}
				
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				case 'jenis':
										
					return "jnsrate='$key'";
			}
		}
		
		function getCTipeRate($conn){
			$sql = "select idrate,rateperjalanan from ".static::table('lv_rateperjalanan')." where isaktif='Y' and ismanual='T'";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function aManual(){
			return array("Y" => "Manual", "T" => "Bukan Manual");
		}
		
		function jenisRate(){
			return array("DK" => "Dalam Kota", "LK" => "Luar Kota", "LN" => "Luar Negeri");
		}
	}
?>
