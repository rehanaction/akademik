<?php
	// model perolehan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPerolehanHeader extends mModel {
		const schema = 'aset';
		const table = 'as_perolehanheader';
		const order = 'idperolehanheader desc';
		const key = 'idperolehanheader';
		const label = 'Perolehan';
		
		function delete($conn, $r_key){
	        $conn->BeginTrans();
	        
            //seri
            $in = "select d.iddetperolehan from aset.as_perolehandetail d join aset.as_perolehan p on p.idperolehan = d.idperolehan 
                where p.idperolehanheader = '$r_key' group by d.iddetperolehan";
		    $err = Query::qDelete($conn,'aset.as_seri',"iddetperolehan in ($in)");

            //inventarisasi
            if(!$err)
    			$err = Query::qDelete($conn,'aset.as_perolehandetail',"idperolehan in (select idperolehan from aset.as_perolehan where idperolehanheader = '$r_key')");

            //detail
            if(!$err)
    			$err = Query::qDelete($conn,'aset.as_perolehan',"idperolehanheader = '$r_key'");

	        if(!$err)
    		    list($err,$msg) = parent::delete($conn,$r_key);
		    
	        if($err)
                $conn->RollbackTrans();
            else
	            $conn->CommitTrans();
            
	        return array($err,$msg);		    
		}
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idperolehanheader,namaunit,tglperolehan,jenisperolehan,nobukti,
		        u.kodeunit+' - '+u.namaunit as unit,tglpo,nopo,tglbukti,nobukti,s.namasupplier,p.listbarang 
				from ".self::table()." p 
				left join ".static::schema.".ms_unit u on u.idunit = p.idunit 
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
			$sql = "select idperolehanheader,p.idunit,u.namaunit as unit,idjenisperolehan,tglperolehan,tglpembukuan,nobukti,tglbukti,nopo,tglpo,nospk,tglspk,
		        idsumberdana,p.catatan,status,p.idsupplier,s.namasupplier,insertuser,inserttime 
				from ".self::table()." p 
				left join ".static::schema.".ms_supplier s on s.idsupplier = p.idsupplier 
				left join ".static::schema.".ms_unit u on u.idunit = p.idunit 
				where ".static::getCondition($key);
            
            return $sql;
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select idunit,nobukti,tglbukti,tglperolehan,idjenisperolehan from ".self::table()." where idperolehanheader = '$key'");
		}
		
		function getNVerified($conn, $key){
		    return (int)$conn->GetOne("select count(*) from aset.as_perolehan where idperolehanheader = '$key' and isverify = 1");
		}
		
		function updatePerolehan($conn, $key){
	        $sql = "update p set
                        idunit = h.idunit,
                        idjenisperolehan = h.idjenisperolehan,
                        tglperolehan = h.tglperolehan,
                        tglpembukuan = h.tglpembukuan,
                        idsumberdana = h.idsumberdana,
                        idsupplier = h.idsupplier,
                        nosk = h.nosk,
                        tglsk = h.tglsk,
                        nobukti = h.nobukti,
                        tglbukti = h.tglbukti,
                        nopo = h.nopo,
                        tglpo = h.tglpo,
                        total = p.qty*p.harga,
                        catatan = h.catatan 
                    from aset.as_perolehan p 
                    join aset.as_perolehanheader h on h.idperolehanheader = p.idperolehanheader 
                    where h.idperolehanheader = '$key'";
            return $conn->Execute($sql);
		}
		
		function setSusut($conn, $key){
		    $ok = true;
		    $sql = "select idperolehan from aset.as_perolehan where idperolehanheader = '$key' and isverify = 1";
		    $row = $conn->GetArray($sql);
		    if(count($row) > 0){
		        require_once(Route::getModelPath('histdepresiasi'));
		        
		        foreach($row as $val){
		            $ok = mHistDepresiasi::setPenyusutan($conn, $val['idperolehan']);
		            if(!$ok) break;
	            }
		    }
		    
		    return $ok;
		}
		
		function setListBarang($conn, $key){
		    $data = array();

		    $sql = "select b.namabarang from aset.as_perolehan p join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 where p.idperolehanheader = '$key'";
		    $rs = $conn->Execute($sql);
		    while($row = $rs->FetchRow()){
		        $data[] = $row['namabarang'];
		    }		    

		    $listbarang = implode($data,'<br>');
		    
		    return $conn->Execute("update aset.as_perolehanheader set listbarang = '$listbarang' where idperolehanheader = '$key'");
		}
				
	}
?>
