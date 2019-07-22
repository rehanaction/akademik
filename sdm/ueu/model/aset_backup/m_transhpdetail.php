<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTransHPDetail extends mModel {
		const schema = 'aset';
		const table = 'as_transhpdetail';
		const order = 'iddettranshp';
		const key = 'iddettranshp';
		const label = 'detail transaksi habis pakai';
		
		function insertCRecord($conn,$a_detail,$record,$r_keydet){
    		self::setDataSave($record);
            self::setDataKonv($conn, $record);
            
		    return parent::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}
		
		function updateCRecord($conn,$a_detail,$record,$r_keydet){
    		self::setDataSave($record);
            self::setDataKonv($conn, $record);
            
		    return parent::updateCRecord($conn,$a_detail,$record,$r_keydet);
		}
/*		
		function delete($conn,$r_keydet){
		    return parent::delete($conn,$r_keydet);
		}
*/
		function getRowByIDP($conn, $idparent){
		    $sql = "select iddettranshp,d.idbarang,b.namabarang,d.idsatuan,qty,harga,total,idcoa,d.catatan,
		        konvidsatuan,konvqty,konvharga,konvtotal,qtyaju,s.jmlstock 
		        from ".static::table()." d 
		        left join ".static::schema.".ms_barang b on b.idbarang = d.idbarang 
		        left join ".static::schema.".as_stockhp s on s.idbarang = b.idbarang 
		        where d.idtranshp = '$idparent'";
		    return $conn->GetArray($sql);
		}
		
		function dataQuery($key){
		    $sql = "select iddettranshp,d.idbarang,b.namabarang,d.idsatuan,qty,harga,total,idcoa,d.catatan,
		        konvidsatuan,konvqty,konvharga,konvtotal,qtyaju,
		        d.idbarang+' - '+b.namabarang as barang
		        from ".static::table()." d 
		        left join ".static::schema.".ms_barang b on b.idbarang = d.idbarang 
		        where ".static::getCondition($key);
	        return $sql;
		}
		
		function getInputAttr($p=''){
	        $a_input = array();
	        $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idbarang', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'qtyaju', 'label' => 'Jml. Diajukan', 'type' => 'N,2', 'size' => 10, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'qty', 'label' => 'Jml. Disetujui', 'type' => 'N,2', 'size' => 10, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'idsatuan', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'harga', 'label' => 'Harga Satuan', 'type' => 'N,2', 'size' => 10, 'notnull' => true, 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $p['isro']);
	        //$a_input[] = array('kolom' => 'satuan', 'label' => 'Satuan', 'class' => 'ControlAuto', 'size' => 40, 'notnull' => true, 'readonly' => $p['isro']);
	        /*
	        $a_input[] = array('kolom' => 'total', 'type' => 'H', 'readonly' => $p['isro']);
            //konversion
	        $a_input[] = array('kolom' => 'konvidsatuan', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'konvqty', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'konvharga', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'konvtotal', 'type' => 'H', 'readonly' => $p['isro']);
	        $a_input[] = array('kolom' => 'konvnilai', 'type' => 'H', 'readonly' => $p['isro']);
            */
	        return $a_input;
        }
        
        function getMDetData($conn, $key){
            return $conn->GetRow("select idbarang from ".static::table()." where iddettranshp = '$key'");
        }
        
		function setDataSave(&$record){
		    if($record['tok'] == 'T')
		        $record['total'] = (float)$record['qty']*(float)$record['harga'];
		}
		
		function setDataKonv($conn,&$record){
		    require_once(Route::getModelPath('barang'));
		    $r_defsatuan = mBarang::getSatuanByID($conn,$record['idbarang']);

		    $record['idsatuan'] = $r_defsatuan; //konversi ditutup sementara
		    
            //konversi
		    $record['konvidsatuan'] = $record['idsatuan'];
		    $record['konvqty'] = $record['qty'];
		    $record['konvnilai'] = 1;
		    if($record['tok'] == 'T'){
		        $record['konvharga'] = $record['harga'];
		        $record['konvtotal'] = $record['total'];
            }
		    
		    if($r_defsatuan != $record['idsatuan']){
		        require_once(Route::getModelPath('konversi'));
		        $r_nilai = mKonversi::getNilaiKonv($conn,$record['idbarang'],$record['idsatuan']);

		        $record['konvidsatuan'] = $r_defsatuan;
		        $record['konvqty'] = (float)$record['qty']/$r_nilai;
		        $record['konvnilai'] = $r_nilai;
    		    if($record['tok'] == 'T'){
		            $record['konvharga'] = (float)$record['harga']/$r_nilai;
		            $record['konvtotal'] = (float)$record['konvqty']*(float)$record['konvharga'];
	            }
		    }
		}
		
		function setSaldoAvg($conn,$idbarang,$verifytime,$idtranshp=0){
            $ok = true;
            //ambil saldo sebelumnya
/*
		    $sql = "select top 1 d.iddettranshp,d.saldoqty,d.saldototal,d.avgprice 
		            from aset.as_transhpdetail d join aset.as_transhp t on d.idtranshp = t.idtranshp 
		            where t.isverify = '1' and d.qty > 0 and (t.tgltransaksi < '$tgltransaksi' 
		            or (t.tgltransaksi = '$tgltransaksi' and t.idtranshp < '$idtranshp')) 
		            and d.idbarang = '$idbarang' 
		            order by t.tgltransaksi desc,d.idtranshp desc";
*/
		    $sql = "select top 1 d.iddettranshp,d.saldoqty,d.saldototal,d.avgprice 
		            from aset.as_transhpdetail d join aset.as_transhp t on d.idtranshp = t.idtranshp 
		            where t.isverify = '1' and d.qty > 0 and t.verifytime < '$verifytime' and d.idbarang = '$idbarang' 
		            order by t.verifytime desc";
            $tprev = $conn->GetRow($sql);
            
            //init saldo awal
            $p_saldoqty = (float)$tprev['saldoqty'];
            $p_saldototal = (float)$tprev['saldototal'];
            $p_avgprice = (float)$tprev['avgprice'];
                                    
            //transaksi - transaksi setelahnya
		    $sql = "select d.iddettranshp,t.tok,t.idjenistranshp,d.qty,d.konvqty,d.konvharga,d.konvnilai,d.konvidsatuan 
		            from aset.as_transhpdetail d join aset.as_transhp t on d.idtranshp = t.idtranshp 
		            where t.isverify = '1' and d.qty > 0 and t.verifytime >= '$verifytime' and d.idbarang = '$idbarang' 
		            order by t.verifytime";
            $rs = $conn->Execute($sql);
            //echo '-->'.$rs->RowCount();

		    $sql = '';
            while($row = $rs->FetchRow()){
                $idsatuan = $row['konvidsatuan'];
                
                if($row['tok'] == 'T' and $row['idjenistranshp'] != '209'){
                    $p_avgprice = $row['konvharga'];
                }
                
		        $saldo = self::hitungSaldo($row['tok'],
		                                $p_saldoqty,
		                                $p_saldototal,
		                                $row['konvqty'],
		                                $p_avgprice);

                $p_saldoqty = (float)$saldo['saldoqty'];
                $p_saldototal = (float)$saldo['saldototal'];
                $p_avgprice = (float)$saldo['avgprice'];

		        $sql .= "update aset.as_transhpdetail set 
		                saldoqty = $p_saldoqty,
		                saldototal = $p_saldototal,
		                avgprice = $p_avgprice";
                if($row['tok'] == 'K' or $row['idjenistranshp'] == '209'){
                    $sql .= ",";
                    $sql .= "harga = $p_avgprice*{$row['konvnilai']},
                            total = $p_avgprice*{$row['qty']},
                            konvharga = $p_avgprice,
                            konvtotal = $p_avgprice*{$row['konvqty']} ";
                }
                $sql .= "where iddettranshp = '{$row['iddettranshp']}';\n";
            }
            
            //return $sql;
            if(!empty($sql)){
                $ok = $conn->Execute($sql);

                if($ok){
                    require_once(Route::getModelPath('stockhp'));
                    list($p_posterr,$p_postmsg) = mStockHP::setStock($conn, $idbarang, $p_saldoqty, $p_saldototal, $idsatuan);

                    if($p_posterr) $ok = true;
                }
            }
            
            return $ok;
		}
		
		function hitungSaldo($tok,$psaldoqty,$psaldototal,$qty,$harga){
		    $ntotal = (float)($qty*$harga);

		    if($tok == 'T'){
		        $saldoqty = $psaldoqty+$qty;
		        $saldototal = ($saldoqty == 0) ? 0 : $psaldototal+$ntotal;
		        $avgprice = ($saldoqty == 0) ? 0 : (float)$saldototal/(float)$saldoqty;
		    }else{
		        $saldoqty = $psaldoqty-$qty;
		        $saldototal = ($saldoqty == 0) ? 0 : $psaldototal-$ntotal;
		        $avgprice = ($saldoqty == 0) ? 0 : $harga;
		    }
		    
		    return array('saldoqty' => $saldoqty,'saldototal' => $saldototal,'avgprice' => $avgprice);
		}
		
		
	}
?>
