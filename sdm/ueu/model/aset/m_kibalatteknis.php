<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKIBAlatTeknis extends mModel {
		const schema = 'aset';
		const table = 'as_kibalatteknis';
		const order = 'idseri';
		const key = 'idseri';
		const label = 'KIB Alat Teknis';
		
		//List KIB Kendaraan
		function listQuery(){
			$sql = "select k.idseri, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, k.norangka, k.nomesin,
						k.tahunbuat, k.negaraasal, k.namapabrik, b.idbarang1, b.namabarang
						from ".self::table()." k left join ".static::schema.".as_seri s on s.idseri=k.idseri left join ".static::schema.".ms_barang1 b on b.idbarang1=s.idbarang1";
				
			return $sql;	
		}
		
		function isExist($conn,$idseri){
		    return (int)$conn->GetOne("select 1 from ".self::table()." where idseri = '$idseri'");
		}
		
		function getInputAttr($p=''){
			$a_input = array();
			$a_input[] = array('kolom' => 'norangka', 'label' => 'No. Rangka', 'maxlength' => 45, 'size' => 20);
			$a_input[] = array('kolom' => 'nomesin', 'label' => 'No. Mesin', 'maxlength' => 45, 'size' => 20);
			$a_input[] = array('kolom' => 'tipealat', 'label' => 'Tipe Alat', 'maxlength' => 45, 'size' => 20);
			$a_input[] = array('kolom' => 'tahunbuat', 'label' => 'Th. Buat', 'maxlength' => 4, 'size' => 4);
			$a_input[] = array('kolom' => 'negaraasal', 'label' => 'Negara Asal', 'maxlength' => 45, 'size' => 25);
			$a_input[] = array('kolom' => 'namapabrik', 'label' => 'Nama Pabrik', 'maxlength' => 45, 'size' => 25);
			
			
			return $a_input;
		}
	}
?>
