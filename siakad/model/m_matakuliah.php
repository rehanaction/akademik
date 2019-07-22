<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	//$conn->debug=true;
	class mMatakuliah extends mModel {
		const schema = 'akademik';
		const table = 'ak_matakuliah';
		const order = 'thnkurikulum desc,kodemk';
		const key = 'thnkurikulum,kodemk';
		const label = 'mata kuliah';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select m.*, j.namajenis,(case when m.isaktif='1' then '".self::getCheckImages()."' end) as isaktif,
					(case when m.isagama='1' then '".self::getCheckImages()."' end) as isagama from ".static::table()." m
					left join ".static::table('lv_jenismk')." j on m.kodejenis = j.kodejenis";
			
			return $sql;
		}
		function getCheckImages(){
			return "<div align=center><img src=images/check.png></div>";
		}
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data = array();
			$data['kodejenis'] = 'm.kodejenis';
			
			return $data;
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select m.*, p.idpegawai::text||' - '||akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as dosenpengampu
					from ".static::table()." m
					left join sdm.ms_pegawai p on m.nipdosenpengampu = p.idpegawai::character varying
					where ".static::getCondition($key,'','m');
			
			return $sql;
		}
		
		// mendapatkan tipe kuliah
		function getTipeKuliah($conn,$key) {
			$sql = "select tipekuliah from ".static::table()." where ".static::getCondition($key);
			
			return $conn->GetOne($sql);
		}
		
		// jenis mata kuliah
		function jenisMataKuliah($conn) {
			$sql = "select kodejenis, namajenis from ".static::table('lv_jenismk')." order by urutan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// tipe kuliah
		function tipeKuliah() {
			$data = array('A' => 'Kuliah', 'P' => 'Praktikum', 'D'=>'Kuliah dan praktikum','K' => 'KKN', 'T' => 'TA/Skripsi');
			
			return $data;
		}
		
		// nilai min
		function nAngkaKurikulum($conn,$kurikulum) {
			$sql = "select nangkasn from ".static::table('ak_skalanilai')."
					where thnkurikulum = '$kurikulum' order by nangkasn";
			
			return Query::arrQuery($conn,$sql);
		}/*
		function getPrasyarat( $tahun, $kodemk){
			
		}*/
		function findMatkul($conn,$str,$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			
			$sql = "select $key, $col as label from ".static::table()."
					where namamk like '%$str%' or kodemk like '%str%' order by ".static::order;
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}
	}
?>
