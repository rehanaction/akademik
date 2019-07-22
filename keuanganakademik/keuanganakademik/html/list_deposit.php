<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('deposit'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
        
	// properti halaman
	$p_title = 'Daftar Deposit Mahasiswa';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	$p_detailpage = 'data_deposit';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No','width'=>'1%');
	$a_kolom[] = array('kolom' => 'd.nim', 'label' => 'NIM ','align'=>'center');
	$a_kolom[] = array('kolom' => 'm.nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'd.tgldeposit', 'label' => 'Tgl Deposit','align'=>'center','type'=>'D');
	$a_kolom[] = array('kolom' => 'd.periode', 'label' => 'Periode','align'=>'center');
	$a_kolom[] = array('kolom' => 'd.nominaldeposit', 'label' => 'Nominal','type'=>'N','align'=>'right');
	$a_kolom[] = array('kolom' => 'd.nominalpakai', 'label' => 'Dipakai','type'=>'N','align'=>'right');
	$a_kolom[] = array('kolom' => 'd.status', 'label' => 'Status','align'=>'center');
	$a_kolom[] = array('kolom' => 'd.tglexpired', 'label' => 'Tgl Expired','align'=>'center');
	$a_kolom[] = array('kolom' => 'd.keterangan', 'label' => 'Keterangan');
	
	$p_model = mDeposit;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
		// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	
	require_once($conf['view_dir'].'v_list_tagihan.php');
?>
