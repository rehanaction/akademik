<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTransHP extends mModel {
		const schema = 'aset';
		const table = 'as_transhp';
		const order = 'idtranshp desc';
		const key = 'idtranshp';
		const label = 'transaksi habis pakai';
		
		function updateCRecord($conn,$a_input,$record,$r_key,$r_isverify=''){
		    $conn->BeginTrans();

		    list($p_posterr,$p_postmsg) = parent::updateCRecord($conn,$a_input,$record,$r_key);
		    
		    if(!$p_posterr){
		        if(isset($record['isverify']) and $record['isverify'] != $r_isverify){
		            if($record['isverify'] == '1'){
		                require_once(Route::getModelPath('transhpdetail'));
		                
		                $sql = "select d.idbarang1,t.tgltransaksi,t.verifytime from aset.as_transhp t 
		                    join aset.as_transhpdetail d on d.idtranshp = t.idtranshp 
		                    where t.idtranshp = '$r_key'";
	                    $rs = $conn->Execute($sql);
	                    while($row = $rs->FetchRow()){
	                         $ok = mTransHPDetail::setSaldoAvg($conn,$row['idbarang1'],$row['verifytime'],$r_key);
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
		}
		
		function proses($conn, $key){
		    $ok = $conn->Execute("update aset.as_transhp set status = 'P' where idtranshp = '$key'");
		    if($ok){
		        self::mailPermintaan($conn, $key);
		        
		        $err = false;
		        $msg = 'Proses permintaan berhasil';
            }else{
		        $err = true;
		        $msg = 'Proses permintaan gagal';
            }

	        return array($err,$msg);
		}
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idtranshp,tglpembukuan,u.kodeunit+' - '+u.namaunit as unit,jenistranshp,nobukti,namasupplier,tglpengajuan,
			    status,isverify,tgltransaksi,uj.kodeunit+' - '+uj.namaunit as unitaju, p.namalengkap, t.nopo, t.tglpo
				from ".self::table()." t 
				left join ".static::schema.".ms_unit u on u.idunit = t.idunit 
				left join ".static::schema.".ms_unit uj on uj.idunit = t.idunitaju 
				left join ".static::schema.".ms_jenistranshp j on j.idjenistranshp = t.idjenistranshp 
				left join ".static::schema.".ms_supplier s on s.idsupplier = t.idsupplier 
				left join sdm.v_biodatapegawai p on p.idpegawai = t.idpegawai";
			
			return $sql;
		}

		function dataQuery($key){
		    $sql = "select idtranshp,idjenistranshp,t.idunit,tglpengajuan,tgltransaksi,status,t.idsupplier, s.namasupplier, tglpembukuan, 
                isverify,verifynote,insertuser,inserttime,verifyuser,verifytime,t.catatan,idunitaju,nobukti,tglbukti,nospk,tglspk,nopo,tglpo,
                p.namalengkap as pegawai 
                from ".self::table()." t 
                left join aset.ms_supplier s on s.idsupplier = t.idsupplier 
                left join sdm.v_biodatapegawai p on p.idpegawai = t.idpegawai 
				where ".static::getCondition($key);
				
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key,$tahun='',$bulan='') {
			switch($col) {
				case 'tok': 
				    return "t.tok = '$key' and t.idjenistranshp != 209 ";
			    break;
				case 'periode_perolehan': 
					return "datepart(year,tgltransaksi) = '$tahun' and datepart(month,tgltransaksi) = '$bulan' "; 
				break;
				case 'periode_permintaan': 
					return "datepart(year,tglpengajuan) = '$tahun' and datepart(month,tglpengajuan) = '$bulan' "; 
				break;
				case 'unit':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				break;
				case 'unitaju':
					global $conn, $conf;
					require_once('m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "uj.infoleft >= ".(int)$row['infoleft']." and uj.inforight <= ".(int)$row['inforight'];
				break;
			}
		}
		
		function getMData($conn, $key){
		    return $conn->GetRow("select isverify,idunit,tgltransaksi,idjenistranshp,tok,idunitaju,tglpengajuan,insertuserid,status from ".self::table()." where idtranshp = '$key'");
		}
		
		function mailPermintaan($conn, $key){
		    global $conf;

		    require_once(Route::getModelPath('transhpdetail'));
		    $p_modeldet = mTransHPDetail;
		    
		    $a_mdata = self::getMData($conn, $key);
		    
		    if(!empty($a_mdata['insertuserid']))
    		    $tujuan = $conn->GetOne("select email from sdm.ms_pegawai where idpegawai = '{$a_mdata['insertuserid']}'");
		    //$tujuan = 'awal@sevima.com';
            if(!empty($tujuan)){
		        require_once($conf['includes_dir'].'phpmailer/class.phpmailer.php');
		        $det = $p_modeldet::getRowByIDP($conn, $key);
		        
		        $p_tbwidth = 1000;
		        //isi email
		        $isi ='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td><font size="+2"><strong>'.$conf['univ_name'].'</strong></font></td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td>'.$conf['univ_address'].'</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td>Telp. '.$conf['univ_telp'].', Fax : '.$conf['univ_fax'].'</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td></td>';
			    $isi.='	 <td>'.$conf['univ_email'].'</td>';
			    $isi.=' </tr>';
			    $isi.='</table>';
			
			    $isi.='<hr style="width:"'.$p_tbwidth.'" align="left"><br>';
			    $isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">Permohonan Bapak/Ibu atas barang persediaan/ATK pada tanggal <b>'.Cstr::formatDateInd($a_mdata['tglpengajuan']).'</b> sudah dapat diambil di bagian rumah tangga.</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2"> ';
			    $isi.='     <table border="1" style="border-collapse:collapse;" width="700">';
			    $isi.='         <tr>';
			    $isi.='             <td align="center" width="30">No.</td>';
			    $isi.='             <td align="center">Barang</td>';
			    $isi.='             <td align="center" width="100">Jml. Diajukan</td>';
			    $isi.='             <td align="center" width="100">Jml. Disetujui</td>';
			    $isi.='             <td align="center" width="80">Satuan</td>';
			    $isi.='         </tr>';
				
				    if(count($det)>0){
				        $i = 0;
					    foreach($det as $id => $val){
					        $i++;
						    $isi.=' <tr>';
						    $isi.='  <td>'.$i.'</td>';
						    $isi.='  <td>'.$val['namabarang'].'</td>';
						    $isi.='  <td align="right">'.CStr::formatNumber($val['qtyaju'],2).'</td>';
						    $isi.='  <td align="right">'.CStr::formatNumber($val['qty'],2).'</td>';
						    $isi.='  <td>'.$val['idsatuan'].'</td>';
						    $isi.=' </tr>';
					    }
				    }
				
			    $isi.='     </table>';
			    $isi.='  </td>';
			    $isi.=' </tr>';	
			    $isi.=' <tr>';
			    $isi.='	 <td colspan="2">&nbsp;</td>';
			    $isi.=' </tr>';
			    //$isi.=' <tr>';
			    //$isi.='	 <td colspan="2"><b>Sudah dapat diambil di bagian rumah tangga</b></td>';
			    //$isi.=' </tr>';
			    $isi.='</table><br>';
			
			    $isi.='<table cellpadding="0" cellspacing="0" width="'.$p_tbwidth.'">';
			    $isi.='	<tr>';
			    $isi.='	 <td>';
			    $isi.='	    <a href="http://simueu.esaunggul.ac.id">http://simueu.esaunggul.ac.id</a>';
			    $isi.='	 </td>';
			    $isi.='</tr>';
			    $isi.='</table>';
			
			    //echo $isi;
			    
			    $subject = 'Permintaan barang persediaan/atk tgl. '.Cstr::formatDateInd($a_mdata['tglpengajuan']);
			
		        $mail = new PHPMailer;
		        $mail->IsSMTP();  // send via SMTP
		        try {
			        $mail->IsHTML(true);
			        //$mail->SMTPAuth = true;
			        $mail->Host     = $conf['smtp_host']; // SMTP servers
			        $mail->Username = $conf['smtp_user'];
			        $mail->Password = $conf['smtp_pass'];
			        $mail->ClearAddresses();
			        $mail->AddAddress($tujuan);
			        //$mail->AddAddress('awal@sevima.com');
			        $mail->From = $conf['smtp_email'];
			        $mail->FromName = $conf['smtp_admin'];
			        $mail->Subject = $subject;
			        $mail->Body = $isi;			
			        $mail->Send();
			
			        return true;
		        } catch (phpmailerException $e) {
			        return false;
		        } catch (Exception $e) {
			        return false;
		        }
		        
                //-------------------untuk ngecek imel--------------------------
/*
		        try {
			        $mail->IsHTML(true);
			        //$mail->SMTPAuth = true;
			        $mail->Host     = $conf['smtp_host']; // SMTP servers
			        $mail->Username = $conf['smtp_user'];
			        $mail->Password = $conf['smtp_pass'];
			        $mail->ClearAddresses();
			        $mail->AddAddress('awal_yahud@yahoo.co.id');
			        $mail->From = $conf['smtp_email'];
			        $mail->FromName = $conf['smtp_admin'];
			        $mail->Subject = $subject;
			        $mail->Body = $isi;			
			        $mail->Send();
			
			        return true;
		        } catch (phpmailerException $e) {
			        return false;
		        } catch (Exception $e) {
			        return false;
		        }
*/
                //--------------------------------------------------------------
			    
		    }
		    
		    
		}
		
	}
?>
