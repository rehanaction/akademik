<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPerencanaanKuliah extends mModel {
		const schema = 'akademik';
		const table = 'ak_perencanaankuliah';
		const order = 'perkuliahanke,tglkuliah';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,tglkuliah,perkuliahanke';
		const label = 'Perancanaan perkuliahan';
		const uptype = 'jurnal';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'kelas':
					require_once(Route::getModelPath('kelas'));
					
					return mKelas::getCondition($key);
			}
		}
		
		// jenis kuliah
		function jenisKuliah($conn) {
			$sql="select * from akademik.lv_jeniskuliah ";
			$rs=$conn->Execute($sql);
			
			while ($row = $rs->FetchRow()){
				$data[$row['idjeniskuliah']] = $row['namajeniskuliah'];
			} 
			 
			return $data;
		}
		function getdata_jurnalperencanaan($conn,$r_key){
		$sql="select perkuliahanke, perkuliahanke||' - '||CASE WHEN statusperkuliahan='S' THEN 'Selesai' WHEN statusperkuliahan='J' THEN 'Terjadwal' WHEN statusperkuliahan='B' THEN 'BATAL' END as namaperkuliahan
			from ".static::table()." where ". static::getCondition($r_key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk')." order by perkuliahanke";
		
			
		$rs=$conn->Execute($sql);
		
			$a_data=array();
			while ($row = $rs->FetchRow()){
				$a_data[$row['perkuliahanke']]=$row['namaperkuliahan'];
			}
			return $a_data;
		
		}
		
		// status kuliah
		function statusKuliah() {
			$data = array('J' => 'Terjadwal','S' => 'Selesai');
			
			return $data;
		}
	}
?>
