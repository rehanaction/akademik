<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPinjamDetail extends mModel {
		const schema = 'aset';
		const table = 'as_pinjamdetail';
		const order = 'iddetpinjam';
		const key = 'iddetpinjam';
		const label = 'Detail Peminjaman';
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select d.iddetpinjam,noseri,d.idseri,s.merk,s.spesifikasi,s.tglperolehan,
		        case when dd.inserttime < '2016-06-01' then s.idbarang+' - '+b.namabarang else s.idbarang1+' - '+bb.namabarang END AS barang, 
		        idlokasi,p.namalengkap as pemakai,dd.tglpinjam,dd.tglkembali
		        from ".static::table()." d
			 join ".static::schema.".as_pinjam dd on dd.idpinjam = d.idpinjam 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang
		        left join ".static::schema.".ms_barang1 bb on bb.idbarang1 = s.idbarang1 
		        left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
		        where d.idpinjam = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function getHeaderByIDP($conn, $idparent){
			$sql = "select u1.kodeunit as kodeunitasal, u1.namaunit as unitasal, pg1.namalengkap as pemilik, 
				pg1.alamat as alamatpemilik, pg1.nip as nippemilik, u2.kodeunit as kodeunitpinjam, u2.namaunit as unitpeminjam, 
				pg.namalengkap as namapeminjam, pg.alamat as alamatpinjam, pg.nip as nippinjam 
				from aset.as_pinjam p 
				join aset.as_pinjamdetail pd on pd.idpinjam = p.idpinjam 
				left join aset.as_seri s on s.idseri = pd.idseri 
				left join aset.ms_unit u1 on u1.idunit = p.idunitasal 
				left join aset.ms_unit u2 on u2.idunit = p.idunitpeminjam 
				left join sdm.v_biodatapegawai pg on pg.idpegawai = p.idpeminjam 
				left join sdm.v_biodatapegawai pg1 on pg1.idpegawai = s.idpegawai 
				where p.idpinjam = '$idparent' ";
			$a_data = $conn->GetRow($sql);
			return $a_data;
		}
		
		function dataQuery($key){
		    $sql = "select d.iddetpinjam,d.idpinjam,d.idseri,d.catatan,
		        s.idbarang1,b.namabarang,s.merk,s.spesifikasi, 
		        right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        s.idbarang1+' - '+b.namabarang as labelbarang
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang1 b on b.idbarang1 = s.idbarang1 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'labelbarang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idseri', 'type' => 'H');
	        $a_input[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'readonly' => true);
	        $a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'readonly' => true);
	        $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'readonly' => true);
	        $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $p['isro']);

	        return $a_input;
        }
	}
?>
