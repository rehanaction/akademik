<?php
	// model surat permintaan barang
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSpb extends mModel {
		const schema = 'prcm';
		const table = 'pr_spb';
		const order = 'idspb desc';
		const key = 'idspb';
		const label = 'surat permintaan barang';
		
		/*function updateCRecord($conn,$a_input,$record,$r_key,$r_isverify=''){
		    $conn->BeginTrans();

		    list($p_posterr,$p_postmsg) = parent::updateCRecord($conn,$a_input,$record,$r_key);
		    
		    if(!$p_posterr){
		        if(isset($record['isverify']) and $record['isverify'] != $r_isverify){
		            if($record['isverify'] == '1'){
		                require_once(Route::getModelPath('transhpdetail'));
		                
		                $sql = "select d.idbarang1,t.tgltransaksi from aset.as_transhp t 
		                    join aset.as_transhpdetail d on d.idtranshp = t.idtranshp 
		                    where t.idtranshp = '$r_key'";
	                    $rs = $conn->Execute($sql);
	                    while($row = $rs->FetchRow()){
	                         $ok = mTransHPDetail::setSaldoAvg($conn,$row['idbarang1'],$row['tgltransaksi'],$r_key);
	                         if(!$ok){ 
	                            $p_posterr = true;
	                            $p_postmsg = 'Set saldo gagal !';
	                            break;
                            }
	                    }
                    }
		        }
	        }

            if($p_posterr)
                $conn->RollbackTrans();
            else
                $conn->CommitTrans();

		    return array($p_posterr,$p_postmsg);
		}*/
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idspb,tglspb,u.kodeunit+' - '+u.namaunit as unit,isok1,p.idpegawai,pg.nik, pg.namalengkap as pegawai,status, isverify
				from ".self::schema.".pr_spb p
				left join aset.ms_unit u on u.idunit = p.idunit 
				left join sdm.v_biodatapegawai pg on pg.idpegawai = p.idpegawai";
			
			return $sql;
		}

		function dataQuery($key){
		    $sql = "select idspb,tglspb,p.idunit,u.kodeunit+' - '+u.namaunit as unit,isok1,p.idpegawai,status,
                pg.nik+' - '+pg.namalengkap as pegawai, insertuser, inserttime, catatan, isok1user, isok1time, memo1, nospb,
				isverify, verifynote, verifyuser, verifytime
                from ".self::schema.".pr_spb p
				left join aset.ms_unit u on u.idunit = p.idunit
                left join sdm.v_biodatapegawai pg on pg.idpegawai = p.idpegawai 
				where ".static::getCondition($key);
				
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key,$tahun='',$bulan='') {
			switch($col) {
				case 'periode': 
					return "datepart(year,tglspb) = '$tahun' and datepart(month,tglspb) = '$bulan' "; 
				break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				break;
			}
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select isok1,isverify, idunit,tglspb,idpegawai from ".self::schema.".pr_spb where idspb = '$key'");
		}

		function getPOExist($conn, $key){
			return $conn->GetRow(" select count(*) as ispo from ".self::schema.".pr_po where idspb = '$key' ");
		}

		function setCreatePO($conn, $record, $r_key){
			$a_mdata = self::getMData($conn, $r_key);
			$now = date('Y-m-d');
			$ok = $conn->Execute($sql);

			if(!empty($a_mdata['isok1'])){
				if($ok){
		        $sql = "insert into prcm.pr_po (tglpo,status,idspb,insertuser,inserttime,t_insertuser,t_insertip,t_inserttime) 
		            select '$now','D',$r_key,insertuser,inserttime,t_insertuser,t_insertip,t_inserttime
		            from prcm.pr_spb s
		            join prcm.pr_spbdetail sd on sd.idspb = s.idspb 
		            where s.idspb = '$r_key'";

					$ok = $conn->Execute($sql);
				    $r_po = $conn->Insert_ID();
				}



				if($ok){
				$sql = "insert into prcm.pr_podetail (idpo,iddetspb,idbarang1,qtypo)
					select $r_po,iddetspb,idbarang1,qtysetuju
					from prcm.pr_spb s
					join prcm.pr_spbdetail sd on sd.idspb = s.idspb
					where s.idspb = '$r_key'";

					$conn->Execute($sql);
				}

			}

		}

	}
?>
