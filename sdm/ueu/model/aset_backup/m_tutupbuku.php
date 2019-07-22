<?php
	// model gedung
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTutupBuku extends mModel {
		const schema = 'aset';
		const table = 'as_tutupbuku';
		const order = 'idtutupbuku';
		const key = 'idtutupbuku';
		const label = 'tutup buku';

		function getListTutup($conn){
		    $a_periode = array();
		    $rs = $conn->Execute("select periode from ".self::table()." order by periode");
		    while($row = $rs->FetchRow()){
		        $a_periode[$row['periode']] = $row['periode'];
		    }
		    return $a_periode;
		}
		
		function getMaxPeriode($conn){
		    return $conn->GetOne("select max(periode) from aset.as_tutupbuku");
		}
		
		function isExist($conn, $periode){
		    return $conn->GetOne("select 1 from aset.as_tutupbuku where periode = '$periode'");
		}

		function isTutupBuku($conn, $periode){
		    return (int)$conn->GetOne("select 1 from aset.as_tutupbuku where periode = '$periode'");
		}

		function getMaxTutupBuku($conn){
		    return (int)$conn->GetOne("select max(periode) from aset.as_tutupbuku");
		}

		function getListPeriode($maxperiode=''){
		    $data = array();
		    
		    $tahun = (int)date('Y');
		    $bulan = (int)date('m');
		    if($maxperiode != ''){
		        $tahun = (int)substr($maxperiode,0,4);
		        $bulan = (int)substr($maxperiode,4,2);
		    }
		        
		    for($i=$bulan-7; $i<=$bulan+5; $i++){
		        if($i <= 0)
		            $data[] = ($tahun-1).str_pad($i+12, 2, '0', STR_PAD_LEFT);
	            else if($i > 12)
		            $data[] = ($tahun+1).str_pad($i-12, 2, '0', STR_PAD_LEFT);
	            else
		            $data[] = $tahun.str_pad($i, 2, '0', STR_PAD_LEFT);
	        }
		        
	        return $data;
		}
	}
?>
