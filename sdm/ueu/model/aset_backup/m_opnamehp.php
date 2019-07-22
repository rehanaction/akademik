<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mOpnameHP extends mModel {
		const schema = 'aset';
		const table = 'as_opnamehp';
		const order = 'idopnamehp desc';
		const key = 'idopnamehp';
		const label = 'opname habis pakai';
		
        // mendapatkan kueri list
		function listQuery() {
			$sql = "select idopnamehp,namaunit,tglpembukuan,tglopname,nobukti,catatan,status 
					from ".self::table()." o 
					left join ".static::schema.".ms_unit u on u.idunit = o.idunit";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				break;
			}
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select status from ".self::table()." where idopnamehp = '$key'");
		}
		
		function prosesOpname($conn,$key){
		    $r_now = date('Y-m-d');
			$ok = true;
		    
	        $sql = "select o.tglopname,d.iddetopnamehp,d.idbarang,d.qtyawal,d.qtyakhir,d.idsatuan 
	            from aset.as_opnamehpdetail d 
				join aset.as_opnamehp o on o.idopnamehp = d.idopnamehp 
	            where d.idopnamehp = '$key'";
	        $rs = $conn->Execute($sql);
	        while($row = $rs->FetchRow()){
	            $r_tglopname = $row['tglopname'];
	            $tok = '';

	            if($row['qtyawal'] < $row['qtyakhir'])
    	            $tok = 'T';
	            else if($row['qtyawal'] > $row['qtyakhir'])
    	            $tok = 'K';
	            
	            $a_stok[$tok][$row['iddetopnamehp']]['idbarang'] = $row['idbarang'];
	            $a_stok[$tok][$row['iddetopnamehp']]['idsatuan'] = $row['idsatuan'];
	            $a_stok[$tok][$row['iddetopnamehp']]['qty'] = abs((float)$row['qtyakhir']-(float)$row['qtyawal']);
	        }
	        
	        //jika ada jumlah lebih besar dari stock
	        if(count($a_stok['T']) > 0){
	            $ok = self::setSaldoOpname($conn,'T',$key,$a_stok,$r_tglopname);
	        }
	        
	        //jika ada jumlah lebih kecil dari stock
	        if(count($a_stok['K']) > 0 and $ok){
	            $ok = self::setSaldoOpname($conn,'K',$key,$a_stok,$r_tglopname);
	        }
	        
	        return $ok;

		} // end function proses opname

        function setSaldoOpname($conn,$tok,$idopnamehp,$a_stok,$tglopname){
		    require_once(Route::getModelPath('transhpdetail'));

		    $user = Modul::getUserDesc();
	        $time = date('Y-m-d H:i:s');

            $sql = "insert into aset.as_transhp (tok,idjenistranshp,idunit,tgltransaksi,tglpembukuan,isverify,verifyuser,verifytime,idopnamehp) values 
                ('$tok',209,'63','$tglopname','$tglopname',1,'$user','$time',$idopnamehp)";
            $ok = $conn->Execute($sql);
            $r_idtranshp = $conn->Insert_ID();
			
            if($ok){
                foreach($a_stok[$tok] as $val){
                    $sql = "insert into aset.as_transhpdetail (idtranshp,idbarang,idsatuan,qty,konvidsatuan,konvqty,konvnilai) values 
                        ($r_idtranshp,'{$val['idbarang']}','{$val['idsatuan']}',{$val['qty']},'{$val['idsatuan']}',{$val['qty']},1)";
                    $ok = $conn->Execute($sql);
                    
                    if($ok){ //hitung saldo avg
                        $ok = mTransHPDetail::setSaldoAvg($conn,$val['idbarang'],$time,$r_idtranshp);
                    }
                }
            }
            
            return $ok;
        }
        
	}
?>
