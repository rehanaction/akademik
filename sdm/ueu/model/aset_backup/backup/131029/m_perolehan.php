<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPerolehan extends mModel {
		const schema = 'aset';
		const table = 'as_perolehan';
		const order = 'idperolehan desc';
		const key = 'idperolehan';
		const label = 'Perolehan';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idperolehan,namaunit,tglperolehan,jenisperolehan,nobukti,isverify,
			        u.kodeunit+' - '+u.namaunit as unit, p.tglpo, p.nopo, p.idbarang+' - '+b.namabarang as barang, s.namasupplier, p.qty, p.total
					from ".self::table()." p 
					left join ".static::schema.".ms_unit u on u.idunit = p.idunit
					left join ".static::schema.".ms_barang b on b.idbarang = p.idbarang
					left join ".static::schema.".ms_supplier s on s.idsupplier = p.idsupplier
					left join ".static::schema.".ms_jenisperolehan j on j.idjenisperolehan = p.idjenisperolehan";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key,$tahun='',$bulan='') {
			switch($col) {
				case 'periode': 
					return "datepart(year,p.tglperolehan) = '$tahun' and datepart(month,p.tglperolehan) = '$bulan' "; 
				break;														
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
				break;
			}
		}
		
		function dataQuery($key) {
			$sql = "select idperolehan,p.idunit,idjenisperolehan,tglperolehan,tglpembukuan,nobukti,tglbukti,nopo,tglpo,nospk,tglspk,
		        idsumberdana,p.catatan,status,isverify,verifyuser,verifytime,idcoa,p.idsupplier,s.namasupplier,insertuser,inserttime,
		        p.idbarang,b.namabarang,b.idsatuan,p.idbarang+' - '+b.namabarang as barang,
		        qty,harga,total,iddasarharga,idkondisi,idcoa,thnprod,merk,ukuran,spesifikasi,tglgaransi,kmgaransi, u.namaunit as unit
				from ".self::table()." p 
				left join ".static::schema.".ms_supplier s on s.idsupplier = p.idsupplier 
				left join ".static::schema.".ms_barang b on b.idbarang = p.idbarang 
				left join ".static::schema.".ms_unit u on u.idunit = p.idunit 
				where ".static::getCondition($key);
            
            return $sql;
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select idunit,isverify,qty,nobukti,tglbukti,idbarang,harga,tglperolehan from ".self::table()." where idperolehan = '$key'");
		}
		
		function getChildID($conn, $key){
		    return $conn->GetOne("select iddetperolehan from ".self::table('as_perolehandetail')." where idperolehan = '$key'");
		}
		
		function setTotal($conn, $key){
		    $sql = "update ".self::table()." set total = qty*harga where idperolehan = '$key'";
		    $ok = $conn->Execute($sql);
		    
		    $err = false;
		    $msg = '';
		    if(!$ok){
		        $err = true;
		        $msg = 'Set total gagal';
		    }
		    
		    return array($err, $msg);
		}
		
		function updateHarga($conn,$harga,$key){
		    $sql = "update aset.as_seri set nilaiawal = $harga 
		        where iddetperolehan in (select iddetperolehan from aset.as_perolehandetail where idperolehan = '$key')";
		    $ok = $conn->Execute($sql);
		    
		    $err = false;
		    $msg = '';
		    if(!$ok){
		        $err = true;
		        $msg = 'Gagal merubah harga, silahkan ulangi lagi !';
		    }
		    
		    return array($err, $msg);
		}
		
		function doVerified($conn,$record,$key){
		    require_once(Route::getModelPath('perolehandetail'));
		    
		    $ok = false;
		    $conn->BeginTrans();
		    
		    $row = self::getData($conn, $key);
		    $det = mPerolehanDetail::getRowByIDP($conn, $key);
		    if(count($det) > 0){
		        foreach($det as $val){
		            $val['tglperolehan'] = $row['tglperolehan'];
		            $val['idunit'] = $row['idunit'];
		            $val['idbarang'] = $row['idbarang'];
		            $val['merk'] = $row['merk'];
		            $val['spesifikasi'] = $row['spesifikasi'];
		            $val['harga'] = $row['harga'];
		            $val['idkondisi'] = $row['idkondisi'];
		            $val['tglgaransi'] = $row['tglgaransi'];
		            $val['kmgaransi'] = $row['kmgaransi'];
		            
		            $ok = self::genSeri($conn,$val);
		            if(!$ok) break;
		        }
		    }
		    
		    if($ok){
		        $sql = "update ".self::table()." set 
		            isverify = 1,
		            verifyuser = '{$record['verifyuser']}',
		            verifytime = '{$record['verifytime']}' 
		            where idperolehan = '$key'";
		        $ok = $conn->Execute($sql);
        	}
        	
		    if($ok){ 
		        $conn->CommitTrans();
				$err = false;
				$msg = 'Verifikasi data berhasil';
	        }else{ 
                $conn->RollbackTrans();
				$err = true;
				$msg = 'Verifikasi data gagal';
            }
            
            return array($err, $msg);
		}
		
		
		function genSeri($conn,$row){
		    $maxSeri = (int)$conn->GetOne("select max(noseri) from ".static::schema.".as_seri where idbarang = '{$row['idbarang']}'");

		    //insert yang perlu saja supaya tidak berat
		    $jml = (int)$row['qty'];
		    $sql = '';
		    
		    for($i=1;$i<=$jml;$i++){
		        $noseri = $maxSeri+$i;
		        $sql .= "insert into ".static::schema.".as_seri (idbarang,noseri,iddetperolehan,idunit,idlokasi,idpegawai) 
		            values ('{$row['idbarang']}',$noseri,'{$row['iddetperolehan']}','{$row['idunit']}','{$row['idlokasi']}','{$row['idpegawai']}');";
		    }
		    $ok = $conn->Execute($sql);
		    
		    //update kemudian
		    if($ok){
		        $tglperolehan = empty($row['tglperolehan']) ? 'null' : "'{$row['tglperolehan']}'";
		        $tglgaransi = empty($row['tglgaransi']) ? 'null' : "'{$row['tglgaransi']}'";
		        $kmgaransi = empty($row['kmgaransi']) ? 'null' : "'{$row['kmgaransi']}'";
		        $sql = "update ".static::schema.".as_seri set 
		                    merk = '{$row['merk']}',
		                    spesifikasi = '{$row['spesifikasi']}',
		                    nilaiawal = {$row['harga']},
		                    nilaiaset = {$row['harga']},
		                    tglperolehan = $tglperolehan,
		                    idkondisi = '{$row['idkondisi']}',
		                    tglgaransi = $tglgaransi,
		                    kmgaransi = $kmgaransi
		                    where iddetperolehan = '{$row['iddetperolehan']}';";
                $ok = $conn->Execute($sql);
		    }

		    return $ok;
		}
		
		function delete($conn, $r_key){
		    $a_mdata = self::getMData($conn, $r_key);
		    
	        $conn->BeginTrans();

		    if($a_mdata['isverify'] == '1'){
			    $err = Query::qDelete($conn,'aset.as_seri',"iddetperolehan in (select iddetperolehan from aset.as_perolehandetail where idperolehan = '$r_key')");
		    }

            //detail
            if(!$err)
    			$err = Query::qDelete($conn,'aset.as_perolehandetail',"idperolehan = '$r_key'");

	        if(!$err)
    		    list($err,$msg) = parent::delete($conn,$r_key);
		    
//            $err = true;
	        if($err)
                $conn->RollbackTrans();
            else
	            $conn->CommitTrans();
            
	        return array($err,$msg);		    
		}
				
	}
?>
