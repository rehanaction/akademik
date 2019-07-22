<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKIBBangunan extends mModel {
		const schema = 'aset';
		const table = 'as_kibbangunan';
		const order = 'idseri';
		const key = 'idseri';
		const label = 'KIB Bangunan';
		
		//List KIB Bangunan
		function listQuery(){
			$sql = "select a.idseri, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, a.noimb, a.tglimb, 
						a.nopersil, a.luas, a.jmllantai, a.alamat, b.idbarang, b.namabarang
						from ".self::table()." a left join ".static::schema.".as_seri s on s.idseri=a.idseri left join ".static::schema.".ms_barang b on b.idbarang=s.idbarang";
				
			return $sql;	
		}
		
		function isExist($conn,$idseri){
		    return (int)$conn->GetOne("select 1 from ".self::table()." where idseri = '$idseri'");
		}
		
		//$now = date('Y-m-d');
		
		function getInputAttr($p=''){
			$a_input = array();
			$a_input[] = array('kolom' => 'noimb', 'label' => 'No. IMB', 'maxlength' => 30, 'size' => 20);
			$a_input[] = array('kolom' => 'tglimb', 'label' => 'Tgl. IMB', 'type' => 'D');
			$a_input[] = array('kolom' => 'nopersil', 'label' => 'No. Persil', 'maxlength' => 30, 'size' => 20);
			$a_input[] = array('kolom' => 'luas', 'label' => 'Luas', 'type' => 'N,2', 'size' => 8);
			$a_input[] = array('kolom' => 'jmllantai', 'label' => 'Jml. Lantai', 'type' => 'N', 'maxlength' => 3, 'size' => 5);
			$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 100);
			$a_input[] = array('kolom' => 'namapemilik', 'label' => 'Nama Pemilik', 'maxlength' => 45, 'size' => 20);
			$a_input[] = array('kolom' => 'alamatpemilik', 'label' => 'Alamat Pemilik', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 100);
	        
	        return $a_input;
        }		
		
	}
?>
