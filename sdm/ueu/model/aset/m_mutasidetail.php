<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMutasiDetail extends mModel {
		const schema = 'aset';
		const table = 'as_mutasidetail';
		const order = 'iddetmutasi';
		const key = 'iddetmutasi';
		const label = 'Mutasi Seri Barang';
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select d.iddetmutasi,s.noseri,d.idseri,s.merk,s.spesifikasi,s.tglperolehan,p.namalengkap as pemakai,
		        s.idlokasi,k.kondisi,
		        case when m.inserttime < '2016-06-01' then s.idbarang+' - '+b.namabarang else s.idbarang1+' - '+bb.namabarang END AS barang
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri
		        left join ".static::schema.".as_mutasi m on m.idmutasi = d.idmutasi 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang
		        left join ".static::schema.".ms_barang1 bb on bb.idbarang1 = s.idbarang1 
				left join ".static::schema.".ms_kondisi k on k.idkondisi = s.idkondisi
		        left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
		        where d.idmutasi = '$idparent' order by b.namabarang";
		    return $conn->GetArray($sql);
		}
		
		function getHeaderByIDP($conn, $idparent) {
			$sql = "select u.kodeunit as kdunitasal, u.namaunit as unitasal, m.idlokasiasal, l.namalokasi as lokasiasal, 
				u2.kodeunit as kdunittujuan, u2.namaunit as unittujuan, m.idlokasitujuan, l2.namalokasi as lokasitujuan, 
				p.nip as niptujuan, p.namalengkap as namapegawai, p2.nip as nipasal, p2.namalengkap as pegawaiasal 
				from aset.as_mutasi m 
				left join ".static::schema.".ms_unit u on u.idunit = m.idunitasal 
				left join ".static::schema.".ms_lokasi l on l.idlokasi = m.idlokasiasal 
				left join ".static::schema.".ms_unit u2 on u2.idunit = m.idunittujuan 
				left join ".static::schema.".ms_lokasi l2 on l2.idlokasi = m.idlokasitujuan 
				left join sdm.v_biodatapegawai p on p.idpegawai = m.idpegawaitujuan 
				left join sdm.v_biodatapegawai p2 on p2.idpegawai = m.idpegawaiasal 
				where m.idmutasi = '$idparent'";
			$a_data = $conn->GetRow($sql);
			return $a_data;
		}
		
		function dataQuery($key){
		    $sql = "select d.iddetmutasi,d.idmutasi,d.idseri,d.catatan,
		        s.idbarang1,b.namabarang,s.merk,s.spesifikasi, s.tglperolehan,
		        right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        s.idbarang1+' - '+b.namabarang as barang 
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang1 b on b.idbarang1 = s.idbarang1 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idseri', 'type' => 'H');
	        $a_input[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'readonly' => true);
	        $a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'readonly' => true);
	        $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'readonly' => true);
	        $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $p['isro']);

	        return $a_input;
        }
        
        function getSeri($conn,$mdata,$pkey){
		    $sql = "select s.idseri,s.noseri,s.merk,s.spesifikasi,s.tglperolehan,p.namalengkap as pemakai,
		        s.idlokasi,s.idbarang1+' - '+b.namabarang as barang 
		        from ".static::schema.".as_seri s join ".static::schema.".ms_barang1 b on b.idbarang1 = s.idbarang1 
		        left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
		        where s.idunit = '{$mdata['idunitasal']}' and s.idlokasi = '{$mdata['idlokasiasal']}' and s.idpegawai = '{$mdata['idpegawaiasal']}' 
		        and s.idseri not in (select idseri from aset.as_mutasidetail where idmutasi = '$pkey') 
		        order by b.namabarang";
		    return $conn->GetArray($sql);
        }
	}
?>
