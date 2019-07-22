<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mRawatDetail extends mModel {
		const schema = 'aset';
		const table = 'as_rawatdetail';
		const order = 'iddetrawat';
		const key = 'iddetrawat';
		const label = 'detail perawatan';
		
        function getRowByIDP($conn, $idparent){
		    $sql = "select d.iddetrawat,noseri,d.idseri,d.keluhan,d.biaya,s.idbarang,s.merk,s.spesifikasi,
		        b.namabarang, s.idbarang+' - '+b.namabarang as barang,p.namalengkap as pegawai,s.tglperolehan,s.tglgaransi,j.jenisrawat
		        from ".static::table()." d 
		        left join ".static::schema.".ms_jenisrawat j on j.idjenisrawat = d.idjenisrawat 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
		        left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
		        where d.idrawat = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function getHeaderByIDP($conn, $idparent){
			$sql = " select u.kodeunit, u.namaunit, r.idlokasi, l.namalokasi, r.insertuser
 			   from aset.as_rawat r
			   join aset.as_rawatdetail rd on rd.idrawat = r.idrawat
			   left join aset.ms_unit u on u.idunit = r.idunit 
			   left join aset.ms_lokasi l on l.idlokasi = r.idlokasi
			  where r.idrawat = '$idparent' ";
			$a_data =  $conn->GetRow($sql);
			return $a_data;
		}
		
		function dataQuery($key){
		    $sql = "select d.iddetrawat,d.idrawat,d.idseri,d.keluhan,d.biaya,d.idjenisrawat,j.jenisrawat,d.keluhan,
		        s.idbarang,b.namabarang,s.merk,s.spesifikasi,s.tglperolehan,s.tglgaransi, 
		        right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        s.idbarang+' - '+b.namabarang as barang 
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
		        left join ".static::schema.".ms_jenisrawat j on j.idjenisrawat = d.idjenisrawat 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'readonly' => true);
	        $a_input[] = array('kolom' => 'idseri', 'type' => 'H');
	        $a_input[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'size' => 6, 'maxlength' => 6, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'readonly' => true);
	        $a_input[] = array('kolom' => 'tglperolehan', 'label' => 'Tgl. Perolehan', 'type' => 'D', 'readonly' => true);
            $a_input[] = array('kolom' => 'tglgaransi', 'label' => 'Tgl. Garansi', 'type' => 'D', 'readonly' => true);
	        $a_input[] = array('kolom' => 'keluhan', 'label' => 'Keluhan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $p['isro']);
    	    $a_input[] = array('kolom' => 'idjenisrawat', 'label' => 'Jenis Perawatan', 'type' => 'S', 'option' => $p['a_jenisrawat'], 'add' => 'style="width:250px"', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'biaya', 'label' => 'Biaya', 'type' => 'N,2', 'maxlength' => 20, 'size' => 10);
/*
            $a_input[] = array('kolom' => 'tglrawat', 'label' => 'Tgl. Perawatan', 'type' => 'D', 'readonly' => $p['isro']);
            $a_input[] = array('kolom' => 'tglkembali', 'label' => 'Tgl. Kembali', 'type' => 'D', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idsupplier', 'type' => 'H');
	        $a_input[] = array('kolom' => 'biaya', 'label' => 'Biaya', 'type' => 'N,2', 'maxlength' => 20, 'size' => 10, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $p['isro']);
*/
	        return $a_input;
        }		
	}
?>
