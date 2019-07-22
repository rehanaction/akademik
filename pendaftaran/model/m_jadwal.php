<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once ($conf['helpers_dir'].'query.class.php');
	
	class mJadwal{
            function getJadwal($jalur){
                global $conn;
				$sql = "select * from pendaftaran.pd_gelombangdaftar where jalurpenerimaan='$jalur' and isaktif  = 't'";
				return $conn->SelectLimit($sql);   
			}
			 function getJadwal_info($jalur){
                global $conn;
				$sql = "select * from pendaftaran.pd_gelombangdaftar where jalurpenerimaan='$jalur'	and isaktif='t' and isopen='t' ";
				return $conn->SelectLimit($sql);   
			}
        }
?>
