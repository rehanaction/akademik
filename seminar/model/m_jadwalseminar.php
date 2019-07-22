<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJadwalSeminar extends mModel {
		const schema = 'seminar';
		const table = 'sm_jadwalseminar';
		const order = 'idjadwalseminar';
		const key = 'idjadwalseminar';
		const label = 'Detail Jadwal Seminar';

		function getDataJadwal($conn,$tgl='',$ruang=''){
			$sql = "select * from ".static::table() ." where tgljadwal = '$tgl' and koderuang = '$ruang' ";	

			$data = $conn->GetArray($sql);
			return $data;
		}
	}
?>
