<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJawabanKuisPeserta extends mModel {
		const schema = 'seminar';
		const table = 'ms_jawabankuispeserta';
		const order = 'idjawabankuispeserta';
		const key = 'idjawabankuispeserta';
		const label = 'Jawaban Peserta';
	function deleteJawaban($conn,$nopeserta,$idseminar){
		$conn->Execute("delete from ".static::table()." where nopeserta  = '$nopeserta' 
						and idpertanyaankuisseminar 
						in (select idpertanyaankuisseminar from ".static::table('ms_pertanyaankuisseminar')." 
								where idseminar = '$idseminar')");
		if ($conn->ErrorNo() <> '0')
			return true;
		else
			return false;
	}
		
	}
?>
