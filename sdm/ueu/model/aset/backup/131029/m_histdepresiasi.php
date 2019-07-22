<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHistDepresiasi extends mModel {
		const schema = 'aset';
		const table = 'as_histdepresiasi';
		const order = 'idhistdepresiasi desc';
		const key = 'idhistdepresiasi';
		const label = 'Depresiasi';
		
		function listQuery(){
			$sql = "select idhistdepresiasi,d.periode, b.namabarang as barang, 
			    right('000000' + cast(s.noseri as varchar(6)), 6) noseri, u.namaunit,s.idlokasi, 
                jp.jenispenyusutan, d.nilaiawal, d.nilaisusut, d.nilaiaset, u.infoleft, u.inforight, u.kodeunit, p.tglbukti, p.nobukti, d.idperolehan
			    from ".self::table()." d 
                left join ".static::schema.".as_seri s on s.idseri = d.idseri 
                left join ".static::schema.".ms_barang1 b on b.idbarang1 = s.idbarang1
                left join ".static::schema.".ms_jenispenyusutan jp on jp.idjenispenyusutan = d.idjenispenyusutan
                left join ".static::schema.".ms_unit u on u.idunit = s.idunit 
                left join aset.as_perolehandetail pd on pd.iddetperolehan = s.iddetperolehan 
                left join aset.as_perolehan p on p.idperolehan = pd.idperolehan";
			return $sql;							   
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					$row = mUnit::getData($conn,$key);
					
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
                break;
				case 'periode': 
					return "d.periode = '$key'"; 
				break;														
				case 'isaktif': 
					return "d.isaktif = '$key'"; 
				break;														
			}
		}
		
		function proses($conn, $idperolehan){
		    $sql = "";
		}
		
		function setPenyusutan($conn, $idperolehan){
		    $debug = $conn->debug;
		    //$conn->debug = false;

		    $nperiode = date('Ym');
		    
		    $ok = $conn->Execute("delete from aset.as_histdepresiasi where idperolehan = '$idperolehan'");
		    
		    if($ok){
		        $sql = "select p.nobukti,p.tglbukti,p.idbarang1,p.harga,b.idjenispenyusutan,j.tarifsusut,j.lifetime,j.nilaisisa,s.idseri 
		            from aset.as_perolehan p 
		            join aset.as_perolehandetail d on d.idperolehan = p.idperolehan 
		            join aset.as_seri s on s.iddetperolehan = d.iddetperolehan 
		            join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 
		            join aset.ms_jenispenyusutan j on j.idjenispenyusutan = b.idjenispenyusutan 
		            where p.idperolehan = '$idperolehan' and p.tglbukti is not null";
		        $rs = $conn->Execute($sql);
		        while($row = $rs->FetchRow()){
		            $lifetime = (int)$row['lifetime'];
	                $nilaiaset = (float)$row['harga'];
                    $tarif = (float)$row['tarifsusut']/100;

                    $tahun = (int)substr($row['tglbukti'],0,4);
                    $bulan = (int)substr($row['tglbukti'],5,2);
                    $fbulan = $bulan;
                    $max = $lifetime - $bulan;
                    $nilaiakhir = 0;

                    $i = 1;
                    $sqli = '';
                    do{
	                    $nilaisusut = (float)(($nilaiaset*$tarif)/12);
                        do{
                            /*--------------------------------------------------*/
                            $periode = $tahun.str_pad($bulan, 2, '0', STR_PAD_LEFT);
		                    $nilaiaset -= $nilaisusut;

		                    $isaktif = '0';
		                    if($periode <= $nperiode){ 
		                        $isaktif = '1';
		                        $nilaiakhir = $nilaiaset;
	                        }

		                    $sqli .= "insert into aset.as_histdepresiasi (periode,idseri,idjenispenyusutan,nilaiawal,nilaisusut,nilaiaset,idperolehan,isaktif) values 
		                        ('$periode','{$row['idseri']}','{$row['idjenispenyusutan']}','{$row['harga']}','$nilaisusut','$nilaiaset','$idperolehan','$isaktif');";
                            /*--------------------------------------------------*/

                            $bulan++;
                            if($i == $lifetime and $bulan == $fbulan)
                                break;
                        }while($bulan <= 12);

                        $bulan = 1;
                        $tahun++;
                        $i++;

                    }while($i <= $lifetime);

                    //residu
                    $atahun = $tahun-1;
                    if($fbulan == 1)
                        $atahun = $tahun;
                    
                    $periode = $atahun.str_pad($fbulan, 2, '0', STR_PAD_LEFT);
                    $sqli .= "insert into aset.as_histdepresiasi (periode,idseri,idjenispenyusutan,nilaiawal,nilaisusut,nilaiaset,idperolehan) values 
                        ('$periode','{$row['idseri']}','{$row['idjenispenyusutan']}','{$row['harga']}','$nilaiaset','1','$idperolehan');";

                    $ok = $conn->Execute($sqli);
                    if($ok)
                        $ok = $conn->Execute("update aset.as_seri set nilaiaset = '$nilaiakhir' where idseri = '{$row['idseri']}'");
                    if(!$ok) break;
                    
        		    $conn->debug = $debug;
		        } //end while
	        } //end ok hapus
		    
		    return $ok;
		}
		
	}
?>
