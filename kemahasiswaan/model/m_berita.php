<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBerita extends mModel {
		const schema = 'elearning';
		const table = 'el_berita';
		const sequence = 'el_berita_idberita_seq';
		const order = 'idberita desc';
		const key = 'idberita';
		const label = 'berita';
		const uptype = 'berita';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select f.*, case when f.validator is null then 0 else -1 end as valid,
					uc.userdesc as namacreator, uv.userdesc as namavalidator
					from ".static::table()." f
					left join gate.sc_user uc on f.creator = uc.username
					left join gate.sc_user uv on f.validator = uv.username";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'valid': return "f.validator is ".(empty($key) ? 'null' : 'not null')." or f.creator = '".Modul::getUserName()."'";
			}
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select f.*, case when f.validator is null then 0 else -1 end as valid,
					uc.userdesc as namacreator, uv.userdesc as namavalidator
					from ".static::table()." f
					left join gate.sc_user uc on f.creator = uc.username
					left join gate.sc_user uv on f.validator = uv.username
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan jumlah berita belum divalidasi
		function getNumInvalid($conn) {
			$sql = "select count(*) from ".static::table()." where validator is null";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan data list sederhana
		function getList($conn,$row=25) {
			$sql = "select * from ".static::table()."
					where validator is not null order by waktuvalid desc";
			$rs = $conn->SelectLimit($sql,$row);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		// mendapatkan berita
		function getListBerita($conn,$row=5) {
			$sql = "select * from ".static::table()." where jenis = 'B'
					and validator is not null order by waktuvalid desc";
			$rs = $conn->SelectLimit($sql,$row);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		// mendapatkan pengumuman
		function getListPengumuman($conn,$row=5) {
			$sql = "select * from ".static::table()." where jenis = 'P'
					and validator is not null order by waktuvalid desc";
			$rs = $conn->SelectLimit($sql,$row);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		// jenis berita
		function jenisBerita($pengumuman=true) {
			$data = array('B' => 'Berita');
			if($pengumuman)
				$data['P'] = 'Pengumuman';
			
			return $data;
		}
	}
?>