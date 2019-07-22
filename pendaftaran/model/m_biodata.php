<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once($conf['model_dir'].'m_model.php');
	
	class mBiodata extends mModel {
		// agama
		function agama($conn) {
			$sql = "select kodeagama, namaagama from akademik.lv_agama order by kodeagama";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getAgama($conn,$idagama) {
			$agama = $conn->GetOne("select namaagama from akademik.lv_agama where kodeagama='$idagama'");
			
			return $agama;
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
		
		// pendidikan
		function pendidikan($conn) {
			$sql = "select kodependidikan, namapendidikan from lv_pendidikan order by kodependidikan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// status nikah
		function statusNikah($conn) {
			$sql = "select statusnikah, namastatus from akademik.lv_statusnikah order by statusnikah";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// tingkat keahlian
		function tingkatKeahlian() {
			$data = array('1' => 'Tidak Bisa', '2' => 'Pasif', '3' => 'Aktif', '4' => 'Mahir');
			
			return $data;
		}
		
		//pekerjaan
		function getPekerjaan($conn,$kodepekerjaan){
			$pekerjaan = $conn->GetOne("select namapekerjaan from akademik.lv_pekerjaan where kodepekerjaan='$kodepekerjaan'");
			return $pekerjaan;
		}
		
		//pendapatan
		function getPendapatan($conn,$kodependapatan){
			$pendapatan = $conn->GetOne("select namapendapatan from akademik.lv_pendapatan where kodependapatan='$kodependapatan'");
			return $pendapatan;
		}
	}
?>