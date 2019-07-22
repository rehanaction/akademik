<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('transkrip'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isMhs())
	{
	$r_key = Modul::getUserName();
	$display="none";
	}	
	else if(Akademik::isDosen())
	{
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
		$display="none";
	}	
	else
	{
		$display="block";	
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	}
	// properti halaman
	$p_title = 'Daftar Nilai Mahasiswa';
	$p_tbwidth = 700;
	$p_aktivitas = 'NILAI';
	$p_headermhs = true;
	$p_printpage = 'rep_daftarnilai';
	$p_mhspage = true;
	
	$p_model = mTranskrip;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No.');
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama Matakuliah');
	$a_kolom[] = array('kolom' => 'sks', 'label' => 'SKS', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'nhuruf', 'label' => 'Nilai');
	$a_kolom[] = array('kolom' => '(nangka*sks)', 'alias' => 'nk', 'label' => 'N.K.');
        $a_kolom[] = array('kolom' => 'periode', 'label' => 'Smt');
	
	$p_colnum = count($a_kolom)+1;
	
	// mendapatkan data
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	//print_r($a_data);
	// total
	$a_total = array('index' => 3, 'label' => 'Jumlah SKS');
	
	
	// membuat filter
	require_once(Route::getViewPath('inc_list'));
?>
