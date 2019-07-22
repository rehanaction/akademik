<?php
	// model pendaftar (terpakai)
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once ($conf['helpers_dir'].'query.class.php');
	
	class mKeuangan{
            function getDetailtoken($conn, $token){
                global $conn;
				$sql = "select t.*, p.notoken from h2h.ke_pembayaranfrm p
						join h2h.ke_Tariffrm t using (idtariffrm) where p.notoken = '$token'";
				return $conn->getRow($sql);
			}
			
			function getListKelompokTagihan($conn) {
				$sql = "select kodekelompok, namakelompok
						from h2h.lv_kelompoktagihan
						order by kodekelompok";
				
				return Query::arrQuery($conn,$sql);
			}
        }
?>
