<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKonversi extends mModel {
		const schema = 'aset';
		const table = 'ms_konversi';
		const order = 'idkonversi';
		const key = 'idkonversi';
		const label = 'Konversi Satuan';
		
		function getDataByIDbarang($conn,$idbarang){
		    $data = array();
		    $rs = $conn->Execute("select idtujuan,nilai from ".static::table()." where idbarang = '$idbarang'");
		    while($row = $rs->FetchRow()){
		        $data[$row['idtujuan']] = $row['nilai'];
		    }
		    
		    return $data;
		}
		
		function getNilaiKonv($conn,$idbarang,$idtujuan){
            return (float)$conn->GetOne("select nilai from ".static::table()." where idbarang = '$idbarang' and idtujuan = '$idtujuan'");
		}

	}
?>
