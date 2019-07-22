<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPegawai extends mModel {
		const schema = 'sdm';
		const table = 'ms_pegawai';
		const order = 'idpegawai';
		const key = 'idpegawai';
		const label = 'Pegawai';
		
		function getNamaPegawai($conn, $key){
		    return $conn->GetOne("select namalengkap from sdm.v_biodatapegawai where idpegawai = '$key'");
		}
	}
?>
