<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKemahasiswaan {
		const schema = 'akademik';
		
		function getJumlahPoinPrestasi($conn,$nim){
			$sql = "select coalesce(poinprestasi,'0') as poinprestasi from kemahasiswaan.v_poinprestasimhs where nim = '$nim'";
			return $conn->getOne($sql);
			
		}
		function getJumlahPoinKegiatan($conn,$nim){
			$sql = "select coalesce(poinkegiatan,'0') as poinkegiatan from kemahasiswaan.v_poinkegiatanmhs where nim = '$nim'";
			return $conn->getOne($sql);
			
		}
		function getPointMahasiswa($conn,$nim){
			
		$poinprestasi = static::getJumlahPoinPrestasi($conn,$nim);
		$poinkegiatan = static::getJumlahPoinKegiatan($conn,$nim);
		
		$data = $poinprestasi + $poinkegiatan;
		return $data;	
		}
		
		
	}
?>
