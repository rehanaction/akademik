<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mBiodata extends mModel {
		const schema = "sdm";
		
		// agama
		function agama($conn) {
			$sql = "select idagama, namaagama from ".static::schema.".lv_agama order by namaagama";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// golongan darah
		function golonganDarah() {
			$data = array('A' => 'A', 'B' => 'B', 'AB' => 'AB', 'O' => 'O');
			
			return $data;
		}
		
		// jenis kelamin
		function jenisKelamin() {
			$data = array('L' => 'Laki-Laki', 'P' => 'Perempuan');
			
			return $data;
		}
		
		// status nikah
		function statusNikah() {
			$data = array('S' => 'Single', 'N' => 'Menikah', 'D'=>'Duda', 'J'=>'Janda');
			
			return $data;
		}
		
		// status pasangan
		function statusPasangan() {
			$data = array('H' => 'Hidup', 'W' => 'Wafat');
			
			return $data;
		}
		
		// pendidikan
		function pendidikan($conn) {
			$sql = "select idpendidikan, namapendidikan from ".static::schema()."lv_jenjangpendidikan order by urutan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// warga negara
		function warganegara($conn) {
			$sql = "select idkewarganegaraan, namakewarganegaraan from ".static::schema.".ms_warganegara order by namakewarganegaraan desc";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// propinsi
		function propinsi($conn, $r_propinsi='') {
			$sql = "select idpropinsi, namapropinsi from ".static::schema()."lv_propinsi";
			if (!empty($r_propinsi))
				$sql .= " where idpropinsi = '$r_propinsi'";
				
			$sql .= " order by namapropinsi";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// kota/kabupaten
		function kabupaten($conn,$r_propinsi='') {
			$sql = "select idkabupaten, upper(namakabupaten) from ".static::schema()."lv_kabupaten";
			if (!empty($r_propinsi))
				$sql .= " where substring(idkabupaten,1,2) = '$r_propinsi'";
			
			$sql .= " order by namakabupaten";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// kecamatan
		function kecamatan($conn, $r_kabupaten='') {
			$sql = "select idkecamatan, upper(namakecamatan) from ".static::schema()."lv_kecamatan";
			if (!empty($r_kabupaten))
				$sql .= " where substring(idkecamatan,1,4) = '$r_kabupaten'";
			
			$sql .= " order by namakecamatan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// kota/kabupaten
		function kelurahan($conn, $r_kecamatan='') {
			$sql = "select idkelurahan, upper(namakelurahan) from ".static::schema()."lv_kelurahan";
			if (!empty($r_kecamatan))
				$sql .= " where substring(idkelurahan,1,6) = '$r_kecamatan'";
			
			$sql .= " order by namakelurahan";
			
			return Query::arrQuery($conn,$sql);
		}
	}
?>