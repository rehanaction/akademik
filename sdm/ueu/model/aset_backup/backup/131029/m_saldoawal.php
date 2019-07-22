<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSaldoAwal extends mModel {
		const schema = 'aset';
		const table = 'aaa_saldoawal';
		const order = 'idsaldoawal';
		const key = 'idsaldoawal';
		const label = 'saldo awal';
		
		function dataQuery($key){
		    $sql = "select s.*,b.idbarang+' - '+b.namabarang as barang,u.namaunit as unit,
                l.idlokasi as lokasi,p.namalengkap as pegawai,r.noseri 
                from aset.aaa_saldoawal s 
                left join aset.ms_barang b on b.idbarang = s.idbarang 
                left join aset.ms_unit u on u.idunit = s.idunit 
                left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
                left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
                left join aset.as_seri r on r.idsaldoawal = s.idsaldoawal 
                where s.idsaldoawal = '$key'"; //.static::getCondition($key);
		    
		    return $sql;
		}
		
		function insertCRecord($conn,$kolom,$rec,&$key){
		    $err = true;
		    $conn->BeginTrans();

		    $r_key = 1+(int)$conn->GetOne("select max(idsaldoawal) from aset.aaa_saldoawal");
		    $rec['idsaldoawal'] = $r_key;
		    
		    list($err,$msg) = parent::insertCRecord($conn,$kolom,$rec,$key);
		    
		    if(!$err){
		        //perolehan
		        $recp = array();
		        $recp['idunit'] = $rec['idunit'];
		        $recp['idjenisperolehan'] = '100';
		        $recp['tglperolehan'] = '2012-12-31';
		        $recp['tglpembukuan'] = '2012-12-31';
		        $recp['isverify'] = '1';
		        $recp['idbarang'] = $rec['idbarang'];
		        $recp['qty'] = '1';
		        $recp['harga'] = '1';
		        $recp['total'] = '1';
		        $recp['idkondisi'] = $rec['idkondisi'];
		        $recp['merk'] = $rec['merk'];
		        $recp['spesifikasi'] = $rec['spesifikasi'];
		        $recp['idsaldoawal'] = $r_key;
	            
		        //$err = parent::insertRecord($conn,$recp);
	            $err = Query::recInsert($conn,$recp,'aset.as_perolehan');
                $idp = $conn->Insert_ID();

                //detail
                if(!$err){
		            $recd = array();
		            $recd['idperolehan'] = $idp;
		            $recd['idlokasi'] = $rec['idlokasi'];
		            $recd['idpegawai'] = $rec['idpegawai'];
		            $recd['qty'] = '1';
		            $recd['idsaldoawal'] = $r_key;
	                
		            //$err = parent::insertRecord($conn,$recd);
		            $err = Query::recInsert($conn,$recd,'aset.as_perolehandetail');
                    $idd = $conn->Insert_ID();
                }
                
                //seri
                if(!$err){
		            $maxSeri = (int)$conn->GetOne("select max(noseri) from aset.as_seri where idbarang = '{$rec['idbarang']}'");
	                $noseri = $maxSeri+1;

		            $recs = array();
		            $recs['idbarang'] = $rec['idbarang'];
		            $recs['noseri'] = $noseri;
		            $recs['iddetperolehan'] = $idd;
		            $recs['tglperolehan'] = '2012-12-31';
		            $recs['idunit'] = $rec['idunit'];
		            $recs['idlokasi'] = $rec['idlokasi'];
		            $recs['idpegawai'] = $rec['idpegawai'];
                    $recs['idkondisi'] = $rec['idkondisi'];
		            $recs['merk'] = $rec['merk'];
		            $recs['spesifikasi'] = $rec['spesifikasi'];
		            $recs['nilaiawal'] = '1';
		            $recs['nilaiaset'] = '1';
		            $recs['idstatus'] = 'A';
		            $recs['idsaldoawal'] = $r_key;
	                
		            //$err = parent::insertRecord($conn,$recs);
		            $err = Query::recInsert($conn,$recs,'aset.as_seri');
                }
		    }   
		    
		    //$err = true;
		    if($err){ 
                $conn->RollbackTrans();
				$err = true;
				$msg = 'gagal';
	        }else{ 
		        $conn->CommitTrans();
				$err = false;
				$msg = 'berhasil';
            }
            
		    return array($err,$msg,$r_key);
		}
		
		function updateCRecord($conn,$kolom,$rec,&$key){
		    $err = true;
		    $conn->BeginTrans();
		    
		    list($err,$msg) = parent::updateCRecord($conn,$kolom,$rec,$key);
		    
		    if(!$err){
		        //perolehan
		        $recp = array();
		        $recp['idunit'] = $rec['idunit'];
		        $recp['idbarang'] = $rec['idbarang'];
		        $recp['idkondisi'] = $rec['idkondisi'];
		        $recp['merk'] = $rec['merk'];
		        $recp['spesifikasi'] = $rec['spesifikasi'];
	            
		        //$err = parent::updateRecord($conn,$recp,array('idsaldoawal'=>$key));
    			$err = Query::recUpdate($conn,$recp,'aset.as_perolehan',"idsaldoawal = '$key'");

                //detail
                if(!$err){
		            $recd = array();
		            $recd['idlokasi'] = $rec['idlokasi'];
		            $recd['idpegawai'] = $rec['idpegawai'];
	                
		            //$err = parent::updateRecord($conn,$recd,array('idsaldoawal'=>$key));
        			$err = Query::recUpdate($conn,$recd,'aset.as_perolehandetail',"idsaldoawal = '$key'");
                }
                
                //seri
                if(!$err){
		            $recs = array();
		            $recs['idunit'] = $rec['idunit'];
		            $recs['idlokasi'] = $rec['idlokasi'];
		            $recs['idpegawai'] = $rec['idpegawai'];
                    $recs['idkondisi'] = $rec['idkondisi'];
		            $recs['merk'] = $rec['merk'];
		            $recs['spesifikasi'] = $rec['spesifikasi'];

                    $idbarango = $conn->GetOne("select idbarang from aset.as_seri where idsaldoawal = '$key'");
                    if($idbarango != $rec['idbarang']){
		                $maxSeri = (int)$conn->GetOne("select max(noseri) from aset.as_seri where idbarang = '{$rec['idbarang']}'");
	                    $noseri = $maxSeri+1;
    
    		            $recs['idbarang'] = $rec['idbarang'];
    		            $recs['noseri'] = $noseri;
                    }
		                
		            //$err = parent::updateRecord($conn,$recs,array('idsaldoawal'=>$key));
        			$err = Query::recUpdate($conn,$recs,'aset.as_seri',"idsaldoawal = '$key'");
                }
		    }   
		    
		    //$err = true;
		    if($err){ 
                $conn->RollbackTrans();
				$err = true;
				$msg = 'gagal';
	        }else{ 
		        $conn->CommitTrans();
				$err = false;
				$msg = 'berhasil';
            }
            
		    return array($err,$msg);
		}

		function delete($conn,&$key){
		    $err = true;
		    $conn->BeginTrans();
		    
            //seri
			$err = Query::qDelete($conn,'aset.as_seri',"idsaldoawal = '$key'");

            //detail
            if(!$err)
    			$err = Query::qDelete($conn,'aset.as_perolehandetail',"idsaldoawal = '$key'");

	        //perolehan
            if(!$err)
    			$err = Query::qDelete($conn,'aset.as_perolehan',"idsaldoawal = '$key'");

		    if(!$err)
    		    list($err,$msg) = parent::delete($conn,$key);
		    
//		    $err = true;
		    if($err){ 
                $conn->RollbackTrans();
				$err = true;
				$msg = 'gagal';
	        }else{ 
		        $conn->CommitTrans();
				$err = false;
				$msg = 'berhasil';
            }
            
		    return array($err,$msg);
		}

	}
?>
