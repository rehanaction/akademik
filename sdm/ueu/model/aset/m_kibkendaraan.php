<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKIBKendaraan extends mModel {
		const schema = 'aset';
		const table = 'as_kibkendaraan';
		const order = 'idseri';
		const key = 'idseri';
		const label = 'KIB Kendaraan';
		
		//List KIB Kendaraan
		function listQuery(){
			$sql = "select k.idseri, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, k.nostnk, k.merk, k.tipe,
				k.tahunrakit, k.bahanbakar, k.cc, k.warna, b.idbarang1, b.namabarang, k.nopol, k.idsopir, 
				p.namalengkap as sopir,k.tglstnk, k.kmpakai
				from ".self::table()." k 
				left join ".static::schema.".as_seri s on s.idseri = k.idseri 
				left join ".static::schema.".ms_barang1 b on b.idbarang1 = s.idbarang1
				left join sdm.v_biodatapegawai p on p.idpegawai = k.idsopir ";

			return $sql;	
		}

		function dataQuery($key){
			$sql = "select k.nostnk, k.merk, k.tipe, k.namapemilik, k.alamatpemilik, k.norangka, k.nobpkb, k.nomesin,
				k.tahunrakit, k.tahunbuat, k.bahanbakar, k.cc, k.warna, k.nopol, k.idsopir, p.namalengkap as sopir,k.tglstnk, k.kmpakai
				from ".self::table()." k 
				left join ".static::schema.".as_seri s on s.idseri = k.idseri 
				left join ".static::schema.".ms_barang1 b on b.idbarang1 = s.idbarang1
				left join sdm.v_biodatapegawai p on p.idpegawai = k.idsopir
				where k.".static::getCondition($key);
		
			return $sql;
		}
		
		function isExist($conn,$idseri){
		    return (int)$conn->GetOne("select 1 from ".self::table()." where idseri = '$idseri'");
		}
		
		function getInputAttr($p=''){
			$a_input = array();
			$a_input[] = array('kolom' => 'namapemilik', 'label' => 'Nama Pemilik', 'maxlength' => 45, 'size' => 20);
			$a_input[] = array('kolom' => 'alamatpemilik', 'label' => 'Alamat Pemilik', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 100);
			$a_input[] = array('kolom' => 'sopir', 'label' => 'Nama Sopir', 'size' => 25);
			$a_input[] = array('kolom' => 'idsopir', 'type' => 'H');
			$a_input[] = array('kolom' => 'nopol', 'label' => 'No. Polisi', 'maxlength' => 11, 'size' => 10);
			$a_input[] = array('kolom' => 'nostnk', 'label' => 'No. STNK', 'maxlength' => 11, 'size' => 10);
		    $a_input[] = array('kolom' => 'tglstnk', 'label' => 'Tgl. STNK', 'type' => 'D', 'default' => $now);
			$a_input[] = array('kolom' => 'nobpkb', 'label' => 'No. BPKB', 'maxlength' => 32, 'size' => 25);
			$a_input[] = array('kolom' => 'norangka', 'label' => 'No. Rangka', 'maxlength' => 45, 'size' => 20);
			$a_input[] = array('kolom' => 'nomesin', 'label' => 'No. Mesin', 'maxlength' => 45, 'size' => 20);
			//$a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'maxlength' => 20, 'size' => 15);
			$a_input[] = array('kolom' => 'tipe', 'label' => 'Tipe', 'maxlength' => 45, 'size' => 20);
			$a_input[] = array('kolom' => 'kmpakai', 'label' => 'Pemakaian (KM)', 'type' => 'N', 'maxlength' => 20, 'size' => 14);
			$a_input[] = array('kolom' => 'bahanbakar', 'label' => 'Bahan Bakar', 'maxlength' => 20, 'size' => 15);
			$a_input[] = array('kolom' => 'cc', 'label' => 'CC', 'maxlength' => 4, 'size' => 5);
			$a_input[] = array('kolom' => 'warna', 'label' => 'Warna', 'maxlength' => 20, 'size' => 10);
			$a_input[] = array('kolom' => 'tahunbuat', 'label' => 'Th. Buat', 'maxlength' => 4, 'size' => 4);
			$a_input[] = array('kolom' => 'tahunrakit', 'label' => 'Th. Rakit', 'maxlength' => 4, 'size' => 4);

			
			return $a_input;
		}
	}
?>

