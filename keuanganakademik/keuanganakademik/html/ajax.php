<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// empty buffer
	ob_clean();
	
	$conn->debug = false;
	
	// require tambahan
	require_once(Route::getUIPath('combo'));
	
	// variabel reuqest
	$f = $_REQUEST['f'];
	$q = $_REQUEST['q'];
	$u = $_REQUEST['u'];
	$j = $_REQUEST['j'];
	
	// filtering
	if(is_array($q)) {
		for($i=0;$i<count($q);$i++)
			$q[$i] = CStr::removeSpecial($q[$i]);
	}
	else
		$q = CStr::removeSpecial($q);
	
	// function
	if($f == 'datamhs') {
		require_once(Route::getModelPath('akademik'));
		if($q[1]=='nim')
			$a_data = mAkademik::getDatamhs($conn,$q[0]);
		else
			$a_data = mAkademik::getDatapendaftar($conn,$q[0]);
			
		
		echo json_encode($a_data);
	}
	else if($f=='acmhspendaftar'){
 
		require_once(Route::getModelPath('akademik'));
		//require_once(Route::getModelPath('pendaftar'));
		if ($j=='mahasiswa')
		$a_data = mAkademik::findMahasiswa($conn,$q,"nim||' - '||nama",'nim',$u);
		else if ($j=='pendaftar')
		$a_data = mAkademik::findPendaftar($conn,$q,"nopendaftar||' - '||nama",'nopendaftar',$u);
		
		echo json_encode($a_data);
	}
	else if($f == 'acmhspendaftarunit') {
		require_once(Route::getModelPath('akademik'));
		
		$a_data = mAkademik::findMhsPendaftarUnit($conn,$q,"nim||' - '||nama",'nim');
		
		echo json_encode($a_data);
	}
	else if($f == 'validasi') {
		require_once(Route::getModelPath('pendaftar'));
		$record=array();
		$record['isvalid']=-1;
		list($p_posterr,$p_postmsg) = mPendaftar::updateCRecord($conn,$kolom,$record,$q[0]);
			echo $p_posterr.'|'.$p_postmsg;
	}
	else if($f == 'bukaValidasi') {
		require_once(Route::getModelPath('pendaftar'));
		$record=array();
		$record['isvalid']=0;
		list($p_posterr,$p_postmsg) = mPendaftar::updateCRecord($conn,$kolom,$record,$q[0]);
			echo $p_posterr.'|'.$p_postmsg;
	}
	else if ($f =='optjenistagihan'){
			require_once(Route::getModelPath('jenistagihan'));
			if ($q[0]=='-1')
				$jenis = 1;
			else
				$jenis = 2;
			
			$sistem = $q[1];
				
			$a_jenistagihan = mJenistagihan::getArrJenisTagihan($conn,$jenis,$sistem);
			
			echo UI::createOption($a_jenistagihan,$t_jenistagihan);
		
		}	
?>
