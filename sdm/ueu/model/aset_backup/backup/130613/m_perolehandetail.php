<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPerolehanDetail extends mModel {
		const schema = 'aset';
		const table = 'as_perolehandetail';
		const order = 'iddetperolehan';
		const key = 'iddetperolehan';
		const label = 'detail perolehan';
		
		function dataQuery($key){
		    $sql = "select d.*,b.namabarang,d.idbarang+' - '+b.namabarang as barang,b.idsatuan 
		        from ".static::table()." d 
		        left join ".static::schema.".ms_barang b on b.idbarang = d.idbarang 
		        where ".static::getCondition($key);
			return $sql;
		}
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select d.*,b.namabarang 
		        from ".static::table()." d left join ".static::schema.".ms_barang b on b.idbarang = d.idbarang 
		        where idperolehan = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function setTotal($conn, $key){
		    $ok = $conn->Execute("update ".self::table()." set total = qty*harga where iddetperolehan = '$key'");
		    
		    $err = false;
		    $msg = '';
		    if(!$ok){
		        $err = true;
		        $msg = 'Set total gagal';
		    }
		    
		    return array($err, $msg);
		}

		function setTotalByIDP($conn, $key){
		    $ok = $conn->Execute("update ".self::table()." set total = qty*harga where idperolehan = '$key'");
		    
		    $err = false;
		    $msg = '';
		    if(!$ok){
		        $err = true;
		        $msg = 'Set total gagal';
		    }
		    
		    return array($err, $msg);
		}

		function getSeriByID($conn, $key){
		    $sql = "select s.idseri,s.noseri,s.idbarang,b.namabarang,s.merk,s.spesifikasi,s.idlokasi,p.namalengkap,l.namalokasi 
		        from ".self::table('as_seri')." s 
		        left join ".self::table('ms_barang')." b on b.idbarang = s.idbarang 
		        left join ".self::table('ms_lokasi')." l on l.idlokasi = s.idlokasi 
		        left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
		        where s.iddetperolehan = '$key'";
		    
		    return $conn->GetArray($sql);
		}
		
		function setDataSeri($conn,$seri,$idlokasi,$idpegawai){
		    $err = true;
		    $msg = 'Set seri gagal';
		    
		    if(count($seri) > 0){
		        foreach($seri as $idseri){
		            $sql = "update ".static::schema.".as_seri set idlokasi = '$idlokasi',idpegawai = '$idpegawai' 
		                where idseri in ('".implode($seri,"','")."')";
		        }
		        
		        $ok = $conn->Execute($sql);
		        if($ok) {
		            $err = false;
		            $msg = 'Set seri berhasil';
		        }
		    }
		    
		    return array($err, $msg);
		}

		function getStatusTrans(){
		    return array('' => '', 'I' => 'Inventarisasi');
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
            $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'readonly' => true);
            $a_input[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'readonly' => true);
            $a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'maxlength' => 45, 'size' => 30);
            $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
            $a_input[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true);
            $a_input[] = array('kolom' => 'idlokasi', 'type' => 'H');
            $a_input[] = array('kolom' => 'pegawai', 'label' => 'Pemakai', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true);
            $a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');
	        
	        return $a_input;
        }
	}
?>
