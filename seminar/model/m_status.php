<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once ($conf['helpers_dir'].'query.class.php');
	
	class mStatus{
            
	    const schema = '';
	    const table = '';
            const order = '';
	    const key = '';
            const label = '';
            
            function getSyarat($conn, $nopendaftar){
                $sql="SELECT * FROM pendaftaran.pd_syaratpendaftar sp
                INNER JOIN pendaftaran.pd_pendaftar p
                ON sp.jalurpenerimaan=p.jalurpenerimaan
                AND sp.nopendaftar=p.nopendaftar
                INNER JOIN pendaftaran.lv_syaratperjalur spj
                ON spj.idsyaratjalur=sp.idsyaratjalur
                WHERE p.nopendaftar='$nopendaftar'";
                
                return $conn->SelectLimit($sql);
            }
            function getStatusSyarat($conn, $nopendaftar, $idsyarat){
                $sql="SELECT * FROM pendaftaran.pd_syaratpendaftar WHERE nopendaftar='$nopendaftar' AND idsyaratjalur='$idsyarat'";
                $ok=$conn->SelectLimit($sql)->RecordCount();
                
                if($ok==0) return false;
                else return true;
            }
            function getJalurInfo($conn, $nopendaftaran){
                $sql="SELECT sp.* FROM pendaftaran.pd_gelombangdaftar sp
                INNER JOIN pendaftaran.pd_pendaftar p
                ON sp.jalurpenerimaan=p.jalurpenerimaan
                AND sp.periodedaftar=p.periodedaftar
                AND sp.idgelombang=p.idgelombang
                WHERE p.nopendaftar='$nopendaftaran'";
                
                return $conn->SelectLimit($sql);
            }
            function statusLulus($conn, $nopendaftar){
                $sql="SELECT p.lulusujian, u.namaunit FROM pendaftaran.pd_pendaftar p
                INNER JOIN gate.ms_unit u ON
                p.pilihanditerima=u.kodeunit
                WHERE p.nopendaftar='$nopendaftar'";
                $ok=$conn->SelectLimit($sql);
                $ok=$ok->RecordCount();
                
                if ($ok==0) return false;
                elseif ($ok!=0) return $conn->SelectLimit($sql)->FetchRow();
            }
            function getDU($conn){
                $sql="SELECT * FROM pendaftaran.lv_syaratdaftarulang";
                return $conn->SelectLimit($sql);
            }
            function getStatusDU($conn, $nopendaftar, $idsyarat){
                $sql="SELECT * FROM pendaftaran.pd_syaratdaftarulang WHERE nopendaftar='$nopendaftar' AND kodesyarat='$idsyarat'";
                $ok=$conn->SelectLimit($sql)->RecordCount();
                
                if($ok==0) return false;
                else return true;
            }
	    //--------------------------------------------------------
	    
	    
        }
?>
