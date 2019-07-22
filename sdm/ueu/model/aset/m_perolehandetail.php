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
		    $sql = "select iddetperolehan,idperolehan,qty,d.idlokasi,l.namalokasi,d.idlokasi+' - '+l.namalokasi as lokasi,
		        d.idpegawai,p.namalengkap,p.nip+' - '+p.namalengkap as pegawai 
		        from ".static::table()." d 
		        left join ".static::schema.".ms_lokasi l on l.idlokasi = d.idlokasi 
		        left join sdm.v_biodatapegawai p on p.idpegawai = d.idpegawai 
		        where ".static::getCondition($key);
			return $sql;
		}
		
		function getRowByIDP($conn, $idparent){
		    $sql = "select iddetperolehan,idperolehan,qty,d.idlokasi,l.namalokasi,d.idlokasi+' - '+l.namalokasi as lokasi,
		        d.idpegawai,p.namalengkap,p.nip+' - '+p.namalengkap as pegawai 
		        from ".static::table()." d 
		        left join ".static::schema.".ms_lokasi l on l.idlokasi = d.idlokasi 
		        left join sdm.v_biodatapegawai p on p.idpegawai = d.idpegawai 
		        where idperolehan = '$idparent'";
		    return $conn->GetArray($sql);
		}

		function getSeriByID($conn, $key){
		    $sql = "select s.idseri,s.noseri,s.idbarang1,b.namabarang,s.merk,s.spesifikasi,s.idlokasi,p.namalengkap,l.namalokasi 
		        from ".self::table('as_seri')." s 
		        left join ".self::table('ms_barang1')." b on b.idbarang1 = s.idbarang1 
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
		
		function getSumQty($conn,$idparent){
		    return (int)$conn->GetOne("select sum(qty) from ".static::table()." where idperolehan = '$idparent'");
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
            $a_input[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
            $a_input[] = array('kolom' => 'idlokasi', 'type' => 'H', 'notnull' => true, 'readonly' => $p['isro']);
            
        	//$a_input[] = array('kolom' => 'idlokasi', 'label' => 'Lokasi', 'type' => 'S', 'option' => $p['lokasi'], 'add' => 'style="width:350px"', 'notnull' => true, 'readonly' => $p['isro']);
            $a_input[] = array('kolom' => 'pegawai', 'label' => 'Pemakai', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
            $a_input[] = array('kolom' => 'idpegawai', 'type' => 'H', 'notnull' => true, 'readonly' => $p['isro']);
            $a_input[] = array('kolom' => 'qty', 'label' => 'Jumlah', 'type' => 'N', 'size' => 6, 'maxlength' => 6, 'notnull' => true, 'readonly' => $p['isro']);
	        
	        return $a_input;
        }
		
		function getCheckMutasi($conn, $idparent) {
			return (int)$conn->GetOne("select count(md.idseri) as mutasi
					from aset.as_perolehandetail pd
					join aset.as_seri s on s.iddetperolehan = pd.iddetperolehan
					join aset.as_mutasidetail md on md.idseri = s.idseri
					where pd.iddetperolehan = '$idparent'");
		}
	}
?>
