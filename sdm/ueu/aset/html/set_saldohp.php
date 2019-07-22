<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;	
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_permintaanhp');
	
	// include
	require_once(Route::getModelPath('transhp'));
	require_once(Route::getModelPath('transhpdetail'));
	
	
	$p_model = mTransHP;
	$p_modeldet = mTransHPDetail;
	
	//$rs = $conn->Execute("select * from aset.as_stockhp order by idbarang1");
	//while($row = $rs->FetchRow()){
    //    $p_modeldet::setSaldoAvg($conn,$row['idbarang1'],'2013-09-01');
    //}
    $row = array('1010301010000009','1010301001000008','1010304004000332','1010304004000333','1010304004000334','1010304004000335');
    
    foreach($row as $idbarang1){
        //echo $idbarang1.'<br>';
        $p_modeldet::setSaldoAvg($conn,$idbarang1,'2013-09-01');
    }
    
?>
