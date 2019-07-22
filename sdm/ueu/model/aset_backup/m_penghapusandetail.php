<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPenghapusanDetail extends mModel {
		const schema = 'aset';
		const table = 'as_penghapusandetail';
		const order = 'iddetpenghapusan';
		const key = 'iddetpenghapusan';
		const label = 'Detail Penghapusan';

        function getRowByIDP($conn, $idparent){
		    $sql = "select d.iddetpenghapusan,s.noseri,d.idseri,d.nilaipenghapusan,
		        s.idbarang+' - '+b.namabarang as barang,s.merk,s.spesifikasi,s.tglperolehan,
		        s.idlokasi,d.nilaipenghapusan,p.namalengkap as pemakai
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
				left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
		        where d.idpenghapusan = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function getHeaderByIDP($conn, $idparent){
			$sql = " select p.idpenghapusan, u.namaunit, u.kodeunit, s.idlokasi, l.namalokasi 
				from ".static::table()." p 
				left join ".static::schema.".as_penghapusan pd on pd.idpenghapusan=p.idpenghapusan 
				left join ".static::schema.".as_seri s on s.idseri=p.idseri 
				left join ".static::schema.".ms_unit u on u.idunit=pd.idunit 
				left join ".static::schema.".ms_lokasi l on l.idlokasi=s.idlokasi 
				where p.idpenghapusan = '$idparent'";
			$a_data = $conn->GetRow($sql);
			return $a_data;
		}
		
		function dataQuery($key){
		    $sql = "select d.iddetpenghapusan,d.idpenghapusan,d.idseri,d.nilaipenghapusan,d.catatan,
		        s.idbarang,b.namabarang,s.merk,s.spesifikasi,s.tglperolehan,s.idlokasi,l.namalokasi,
		        right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		        s.idbarang+' - '+b.namabarang as barang 
		        from ".static::table()." d 
		        left join ".static::schema.".as_seri s on s.idseri = d.idseri 
		        left join ".static::schema.".ms_barang b on b.idbarang = s.idbarang
				left join ".static::schema.".ms_lokasi l on l.idlokasi = s.idlokasi 
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
	        $a_input[] = array('kolom' => 'nilaipenghapusan', 'label' => 'Nilai Hapus', 'type' => 'N,2', 'maxlength' => 20, 'size' => 10, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $p['isro']);

	        return $a_input;
        }
		
		function getSeri($conn,$mdata,$pkey){
		    $sql = "select s.idseri,s.noseri,s.merk,s.spesifikasi,s.tglperolehan,
		        s.idlokasi,s.idbarang+' - '+b.namabarang as barang, pd.nilaipenghapusan
		        from ".static::schema.".as_seri s join ".static::schema.".ms_barang b on b.idbarang = s.idbarang 
				left join ".static::schema.".as_penghapusandetail pd on pd.idseri = s.idseri
		        where s.idunit = '{$mdata['idunit']}' and s.idlokasi = '{$mdata['idlokasi']}' 
		        and s.idseri not in (select idseri from aset.as_penghapusandetail where idpenghapusan = '$pkey') 
		        order by b.namabarang";
		    return $conn->GetArray($sql);
        }
		
		function updatehapus($conn,$pkey,$record){
		    $ok = $conn->Execute("update aset.as_penghapusandetail set nilaipenghapusan = {$record['nilaipenghapusan']} where iddetpenghapusan = '$pkey'");
		    if($ok){
		        $err = false;
		        $msg = 'Proses Update Nilai Penghapusan Berhasil';
            }else{
		        $err = true;
		        $msg = 'Proses Update Nilai Penghapusan Gagal';
            }

	        return array($err,$msg);
		}
	}
?>
