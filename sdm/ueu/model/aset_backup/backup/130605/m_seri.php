<?php
	// model seri
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSeri extends mModel {
		const schema = 'aset';
		const table = 'as_seri';
		const order = 'idseri';
		const key = 'idseri';
		const label = 'Seri Barang';
		
		//list seri
		function listQuery() {
			$sql = "select s.idseri, s.idbarang,b.namabarang as namabarang, l.namalokasi as namalokasi, u.namaunit as namaunit, 
					right('000000' + cast(s.noseri as varchar(6)), 6) noseri, s.idkondisi, s.idstatus, s.merk, 
					s.spesifikasi, s.tglperolehan, s.idlokasi, s.idunit
					from ".self::table()." s
					left join ".static::schema.".ms_barang b on b.idbarang=s.idbarang
					left join ".static::schema.".ms_lokasi l on l.idlokasi=s.idlokasi
					left join ".static::schema.".ms_unit u on u.idunit=s.idunit";
		
			return $sql;
		}
		
		//list rekap inventaris ruang
		function listRekap(){
			$sql = "select * from ".static::schema.".vi_rekapseri";
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
		//function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					//if($default) {
						return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];	
					//}
					
			}
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'kibtanah':
					$info['table'] = 'as_kibtanah';
					$info['key'] = 'idseri';
					$info['label'] = 'kib tanah';
				break;
				case 'kibgedung':
					$info['table'] = 'as_kibbangunan';
					$info['key'] = 'idseri';
					$info['label'] = 'kib bangunan';
				break;
				case 'kibkendaraan':
					$info['table'] = 'as_kibkendaraan';
					$info['key'] = 'idseri';
					$info['label'] = 'kib kendaraan';
				break;
				case 'kibalatteknis':
					$info['table'] = 'as_kibkendaraan';
					$info['key'] = 'idseri';
					$info['label'] = 'kib kendaraan';
				break;
				case 'depresiasi':
					$info['table'] = 'as_histdepresiasi';
					$info['key'] = 'idhistdepresiasi';
					$info['label'] = 'history depresiasi';
				break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// KIB Tanah
		function getKIBTanah($conn,$key,$label='',$post='') {
			$sql = "select idseri, luas, noakte, nosertifikat, noskpt, statushukum, alamat, namapemilik, alamatpemilik
					from ".static::table('as_kibtanah')." 
					where idseri = '$key' order by idseri desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// KIB Gedung
		function getKIBGedung($conn,$key,$label='',$post='') {
			$sql = "select idseri, noimb, tglimb, nopersil, luas, jmllantai, alamat, namapemilik, alamatpemilik
					from ".static::table('as_kibbangunan')." 
					where idseri = '$key' order by idseri desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// KIB Kendaraan
		function getKIBKendaraan($conn,$key,$label='',$post='') {
			$sql = "select idseri, norangka, nomesin, nobpkb, nostnk, merk, tipe, tahunbuat, tahunrakit, bahanbakar, cc, warna, namapemilik, alamatpemilik
					from ".static::table('as_kibkendaraan')." 
					where idseri = '$key' order by idseri desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// KIB Kendaraan
		function getKIBAlatTeknis($conn,$key,$label='',$post='') {
			$sql = "select idseri, norangka, nomesin, nobpkb, nostnk, merk, tipe, tahunbuat, tahunrakit, bahanbakar, cc, warna, namapemilik, alamatpemilik
					from ".static::table('as_kibkendaraan')." 
					where idseri = '$key' order by idseri desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// History Depresiasi
		function getHistDepresiasi($conn,$key,$label='',$post='') {
			$sql = "select idseri, periode, idjenispenyusutan, nilaisusut, nilaiaset
					from ".static::table('as_histdepresiasi')." 
					where idseri = '$key' order by idseri desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
	    function getIDBarang($conn, $idseri){
	        return $conn->GetOne("select idbarang from ".self::table()." where idseri = '$idseri'");
	    }
	    
	    function getIDHistDepresiasi($conn, $idseri){
	        return $conn->GetOne("select idhistdepresiasi from ".self::table()." where idseri = '$idseri'");
	    }
		
	}
?>
