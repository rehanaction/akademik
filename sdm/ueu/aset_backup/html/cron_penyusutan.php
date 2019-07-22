<?php
    // cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//ini_set('max_execution_time',10000);
	// hak akses
	//$a_auth = Modul::getFileAuth();

    $conn->debug = false;
	$periode = date('Ym');
	
	$sql = "update aset.as_histdepresiasi set isaktif = 1 where periode = '$periode'";
	$rs = $conn->Execute($sql);

    $sql = "update s set nilaiaset = d.nilaiaset 
        from aset.as_seri s 
        join aset.as_histdepresiasi d on d.idseri = s.idseri 
        where d.periode = '$periode'";
	$rs = $conn->Execute($sql);

?>

