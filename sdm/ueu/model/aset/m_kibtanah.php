<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKIBTanah extends mModel {
		const schema = 'aset';
		const table = 'as_kibtanah';
		const order = 'idseri';
		const key = 'idseri';
		const label = 'KIB Tanah';
		
		//List KIB Tanah
		function listQuery(){
			$sql = "select t.idseri, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, t.nosertifikat, t.luas, t.alamat, 
						t.noakte, t.noskpt, t.statushukum, b.idbarang1, b.namabarang
						from ".self::table()." t left join ".static::schema.".as_seri s on s.idseri = t.idseri left join ".static::schema.".ms_barang1 b on b.idbarang1=s.idbarang1";
				
			return $sql;	
		}
		
		function isExist($conn,$idseri){
		    return (int)$conn->GetOne("select 1 from ".self::table()." where idseri = '$idseri'");
		}
		
		function statusHukum(){
			return array('1' => 'Hak Guna Usaha', '2' => 'Hak Milik', '3' => 'Hak Guna Bangunan', '4' => 'Hak Pakai', '5' => 'Hak Sewa Untuk Bangunan', '6' => 'Hak Membuka Tanah', '7' => 'Hak Sewa Tanah Pertanian');
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'nosertifikat', 'label' => 'No. Sertfikat', 'maxlength' => 45, 'size' => 30);
	        $a_input[] = array('kolom' => 'noakte', 'label' => 'No. Akte', 'maxlength' => 45, 'size' => 30);
	        $a_input[] = array('kolom' => 'luas', 'label' => 'Luas', 'type' => 'N,2','size' => 6);
	        $a_input[] = array('kolom' => 'noskpt', 'label' => 'No. SKPT', 'maxlength' => 45, 'size' => 30);
	        $a_input[] = array('kolom' => 'statushukum', 'label' => 'Status Hukum', 'type' => 'S', 'option' => mKIBTanah::statusHukum(), 'add' => 'style="width:200px;"');
	        $a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'type' => 'A','rows' => 3, 'cols' => 30, 'maxlength' => 100);
	        $a_input[] = array('kolom' => 'namapemilik', 'label' => 'Nama Pemilik', 'maxlength' => 45, 'size' => 20);
	        $a_input[] = array('kolom' => 'alamatpemilik', 'label' => 'Alamat Pemilik', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 100);	        

	        return $a_input;
        }		

	}
?>
