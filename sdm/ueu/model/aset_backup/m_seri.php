<?php
	// model seri
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSeri extends mModel {
		const schema = 'aset';
		const table = 'as_seri';
		const order = 's.idbarang';
		const key = 'idseri';
		const label = 'Seri Barang';
		
		//list seri //Aset::formatNoSeri(5)=000005
		function listQuery() {
			$sql = "select s.idseri, s.idbarang+' - '+b.namabarang as barang, u.namaunit as unit,s.merk,s.spesifikasi,
			    s.idlokasi as lokasi, right('000000' + cast(s.noseri as varchar(6)), 6) noseri, 
			    s.idkondisi, s.idstatus, s.tglperolehan, p.namalengkap as pegawai
				from ".self::table()." s
				left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang
				left join ".static::schema.".ms_lokasi l on l.idlokasi = s.idlokasi
				left join ".static::schema.".ms_unit u on u.idunit = s.idunit
				left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai ";
	
			return $sql;
		}
		
		function dataQuery($key){
			$sql = "select s.*, b.namabarang, 
				l.namalokasi, u.namaunit, p.namalengkap, s.tglperolehan,
			    s.idbarang+' - '+b.namabarang as barang, 
			    p.nip+' - '+p.namalengkap as pegawai,
			    u.kodeunit+' - '+u.namaunit as unit,
			    s.idlokasi+' - '+l.namalokasi as lokasi, s.kmpakai
				from ".self::table()." s
				left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
				left join ".static::schema.".ms_lokasi l on l.idlokasi = s.idlokasi 
				left join ".static::schema.".ms_unit u on u.idunit = s.idunit 
				left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
				where ".static::getCondition($key);
		
			return $sql;
		}
		
		function getSeriByP($conn, $idperolehan){
			$sql = "select s.idseri,s.noseri,s.idbarang+' - '+b.namabarang as barang,u.namaunit,s.merk,s.spesifikasi,
			    s.idlokasi,s.idkondisi,s.idstatus,s.tglperolehan,p.namalengkap
				from ".self::table()." s
				left join ".static::schema.".as_perolehandetail d on d.iddetperolehan = s.iddetperolehan
				left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang
				left join ".static::schema.".ms_unit u on u.idunit = s.idunit
				left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
				where d.idperolehan = '$idperolehan'";
	
			return $conn->Execute($sql);
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select idbarang,noseri from ".self::table()." where idseri = '$key'");
		}
		
		//list rekap inventaris ruang
		function listRekap(){
			$sql = "select * from ".static::schema.".vi_rekapseri";
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'lokasi': 
				    return "s.idlokasi = '$key'";
			    break;
				case 'pemakai': 
				    return "s.idpegawai = '$key'";
			    break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				break;
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
					$info['label'] = 'kib alat teknis';
				break;
				case 'depresiasi':
					$info['table'] = 'as_histdepresiasi';
					$info['key'] = 'idhistdepresiasi';
					$info['label'] = 'history depresiasi';
				break;
				case 'perawatan':
					$info['table'] = 'as_rawatdetail';
					$info['key'] = 'idrawat';
					$info['label'] = 'history perawatan';
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
					where isaktif = 1 and idseri = '$key' order by idseri desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// History Perawatan
/*		function getHistPerawatan($conn,$key,$label='',$post='') {
			$sql = "select jr.jenisrawat, r.tglrawat, r.catatan
				   from ".static::table('as_rawatdetail')." rd
 				   join ".static::table('as_rawat')." r on r.idrawat=rd.idrawat
			       left join ".static::table('as_seri')." s on s.idseri=rd.idseri
			       left join ".static::table('ms_jenisrawat')." jr on jr.idjenisrawat=r.idjenisrawat
				   where rd.idseri = '$key' order by r.tglpengajuan desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
*/
		function getPerawatan($conn, $key) {
		    $sql = "select r.tglrawat,j.jenisrawat,d.keluhan,s.namasupplier,d.biaya 
		        from ".static::table('as_rawat')." r 
		        left join ".static::table('as_rawatdetail')." d on d.idrawat = r.idrawat 
		        left join ".static::table('ms_supplier')." s on s.idsupplier = r.idsupplier 
		        left join ".static::table('ms_jenisrawat')." j on j.idjenisrawat = d.idjenisrawat 
		        where d.idseri = '$key' order by r.tglrawat";
	        return $conn->Execute($sql);
        }

	    function getIDBarang($conn, $idseri){
	        return $conn->GetOne("select idbarang from ".self::table()." where idseri = '$idseri'");
	    }
	    
	    function getIDHistDepresiasi($conn, $idseri){
	        return $conn->GetOne("select idhistdepresiasi from ".static::table('as_histdepresiasi')." where idseri = '$idseri'");
	    }
	    
		function pemakai($conn,$idunit=''){
			$sql = "select s.idpegawai, p.namalengkap 
			    from aset.as_seri s left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
			    where s.idpegawai is not null ";
		    if($idunit != '')
		        $sql .= "and s.idunit = '$idunit' ";
			$sql .= "group by s.idpegawai,p.namalengkap order by p.namalengkap";
	        
	        return Query::arrQuery($conn, $sql);
		}

	}
?>
