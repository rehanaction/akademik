<?php
	// model konsultasi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKonsultasi extends mModel {
		const schema = 'akademik';
		const table = 'ak_konsultasi';
		const order = 'tglkonsultasi desc';
		const key = 'idkonsultasi';
		const label = 'konsultasi';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'nim': return "nim = '$key'";
				case 'nip': return "nip = '$key'";
			}
		}
		
		// menemukan data mhs wali, untuk autocomplete
		function findMhsWali($conn,$str,$nip='',$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
						
			$sql = "select distinct $key as key, $col as label from akademik.ms_mahasiswa m
					left join ".static::table()." k on k.nim = m.nim
					where lower($col::varchar) like '%$str%'";
			if(!empty($nip))
				$sql .= " and (m.nipdosenwali = '$nip' or k.nip = '$nip')";
			$sql .= " order by $key";
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row['key'];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}
		
		// menemukan data dosen wali, untuk autocomplete
		function findDosenWali($conn,$str,$nim='',$col='',$key='') {
	 
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
						
			$sql = "select distinct $key as key, $col as label from sdm.ms_pegawai p
					left join akademik.ms_mahasiswa m on m.nipdosenwali = p.idpegawai::text
					left join ".static::table()." k on k.nip = p.idpegawai::text
					where lower($col::varchar) like '%$str%'";
			if(!empty($nim))
				$sql .= " and (m.nim = '$nim' or k.nim = '$nim')";
			$sql .= " order by $key";
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row['key'];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}
		
		function getJenisKonsultasi(){
			return array('R'=>'KRS','K'=>'Kuliah','T'=>'UTS','A'=>'UAS');
		}
		
		
		function getCountKonsul($conn,$nip,$nim,$periode){
			$sql = "select count(idkonsultasi)
					from akademik.ak_konsultasi
					where nim  = '$nim' and nip = '$nip' and periode = '$periode'";

			return $conn->GetOne($sql);
		}
	}
?>
