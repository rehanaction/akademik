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
			$sql = "select idperolehan,namaunit,tglpembukuan,jenisperolehan,nobukti,isverify 
					from ".self::table()." p 
					left join ".static::schema.".ms_unit u on u.idunit = p.idunit
					left join ".static::schema.".ms_jenisperolehan j on j.idjenisperolehan = p.idjenisperolehan";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'jenisperolehan': 
				    return "p.idjenisperolehan = '$key'";
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
			$sql = "select idperolehan,p.idunit,namaunit,tglpembukuan,p.idjenisperolehan,j.jenisperolehan,nobukti,tglbukti,nopo,tglpo,nospk,tglspk,
		        p.idsumberdana,p.idsupplier,s.namasupplier,p.catatan,total,status,isverify 
				from ".self::table()." p 
				left join ".static::schema.".ms_unit u on u.idunit = p.idunit 
				left join ".static::schema.".ms_supplier s on s.idsupplier = p.idsupplier 
				left join ".static::schema.".ms_jenisperolehan j on j.idjenisperolehan = p.idjenisperolehan 
				where ".static::getCondition($key);
            
            return $sql;
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select isverify from ".self::table()." where idperolehan = '$key'");
		}
		
		function getChildID($conn, $key){
		    return $conn->GetOne("select iddetperolehan from ".self::table('as_perolehandetail')." where idperolehan = '$key'");
		}
		
		function setTotal($conn, $key){
		    $sql = "update ".self::table()." 
		        set total = (select sum(qty*harga) from ".self::table('as_perolehandetail')." where idperolehan = '$key') 
		        where idperolehan = '$key'";
		    $ok = $conn->Execute($sql);
		    
		    $err = false;
		    $msg = '';
		    if(!$ok){
		        $err = true;
		        $msg = 'Set total gagal';
		    }
		    
		    return array($err, $msg);
		}
		
		function doVerified($conn,$key){
		    require_once(Route::getModelPath('perolehandetail'));
		    
		    $ok = false;
		    $conn->BeginTrans();
		    
		    $row = self::getData($conn, $key);
		    $det = mPerolehanDetail::getRowByIDP($conn, $key);
		    if(count($det) > 0){
		        foreach($det as $val){
		            $val['tglperolehan'] = $row['tglperolehan'];
		            $val['idunit'] = $row['idunit'];
		            //$val['idlokasi'] = $row['idlokasi'];
		            //$val['idpegawai'] = $row['idpegawai'];
		            
		            $ok = self::genSeri($conn,$val);
		            if(!$ok) break;
		        }
		    }
		    
		    if($ok)
		        $ok = $conn->Execute("update ".self::table()." set isverify = 1 where idperolehan = '$key'");
            		    
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
		    $maxSeri = 1+(int)$conn->GetOne("select max(noseri) from ".static::schema.".as_seri where idbarang1 = '{$row['idbarang1']}'");

		    //insert yang perlu saja supaya tidak berat
		    $jml = (int)$row['qty'];
		    $sql = '';
		    for($i=$maxSeri;$i<=$jml;$i++){
		        $sql .= "insert into ".static::schema.".as_seri (idbarang1,noseri,iddetperolehan,idunit) 
		            values ('{$row['idbarang1']}',$i,'{$row['iddetperolehan']}','{$row['idunit']}');";
		    }
		    $ok = $conn->Execute($sql);
		    
		    //update kemudian
		    if($ok){
		        $sql = "update ".static::schema.".as_seri set 
		                    merk = '{$row['merk']}',
		                    spesifikasi = '{$row['spesifikasi']}',
		                    catatan = '{$row['catatan']}',
		                    nilaiawal = {$row['harga']},
		                    tglperolehan = '{$row['tglperolehan']}'
		                    where iddetperolehan = '{$row['iddetperolehan']}';";
                $ok = $conn->Execute($sql);
		    }
		    
		    return $ok;
		}
				
	}
?>
