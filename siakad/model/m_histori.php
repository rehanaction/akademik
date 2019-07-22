<?php
	// model histori
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHistori extends mModel {
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select h.*, k.namahistori from ".static::table()." h
					join akademik.lv_kodehistori k on h.kodehistori = k.kodehistori";
			
			return $sql;
		}
	}
	
	class mHistoriMahasiswa extends mHistori {
		const schema = 'akademik';
		const table = 'hi_histmahasiswa';
		const order = 'idhistmahasiswa desc';
		const key = 'idhistmahasiswa';
		const label = 'histori perubahan mahasiswa';
	}
	
	class mHistoriMataKuliah extends mHistori {
		const schema = 'akademik';
		const table = 'hi_histmatakuliah';
		const order = 'idhistmatakuliah desc';
		const key = 'idhistmatakuliah';
		const label = 'histori perubahan mata kuliah';
	}
	
	class mHistoriNilai extends mHistori {
		const schema = 'akademik';
		const table = 'hi_histnilai';
		const order = 'idhistnilai desc';
		const key = 'idhistnilai';
		const label = 'histori perubahan nilai';
	}
	
	class mHistoriTA extends mHistori {
		const schema = 'akademik';
		const table = 'hi_histta';
		const order = 'idhistta desc';
		const key = 'idhistta';
		const label = 'histori perubahan tugas akhir';
	}
?>