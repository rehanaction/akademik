<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPeriodeTahap extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_periodetahap';
		const sequence = 'mw_periodetahap_idperiodetahap_seq';
		const order = 'idperiodetahap';
		const key = 'idperiodetahap';
		const label = 'Periode Tahap Kegiatan';
		
		function listQuery() {
			$sql = "select pt.*, namatahap 
					from ".static::table()." pt 
					join ".static::table('mw_tahap')." t using (idtahap) ";
			
			return $sql;

		}
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}

		// get list list periodetahap
		function getListPeriodeTahap($conn,$filter) {
			$sql = "select pt.*, 
					       namatahap 
					from   kemahasiswaan.mw_periodetahap pt 
					       join kemahasiswaan.mw_tahap t using (idtahap) 
					where  ( Lower(( kodejenjang ) :: varchar) like '%".$filter."%' 
					          or Lower(( namatahap ) :: varchar) like '%".$filter."%'  
					          or Lower(( pt.semsbawah ) :: varchar) like '%".$filter."%'  
					          or Lower(( pt.semsatas ) :: varchar) like '%".$filter."%'  ) 
					order  by idperiodetahap ";
			
			return $conn->GetArray($sql);
		}
	}
?>
