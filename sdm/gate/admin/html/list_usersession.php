<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('usersession'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_role = Modul::setRequest($_POST['role'],'ROLE');
	
	// combo
	$l_role = uCombo::role($conn,$r_role,'role','onchange="goSubmit()"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 't_userid', 'label' => 'User');
	$a_kolom[] = array('kolom' => 't_username', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 't_logintime', 'label' => 'Login', 'type' => 'DT');
	$a_kolom[] = array('kolom' => 't_logouttime', 'label' => 'Logout', 'type' => 'DT');
	$a_kolom[] = array('kolom' => 't_osname', 'label' => 'Keterangan');
	
	// properti halaman
	$p_title = 'Daftar User Login';
	$p_tbwidth = 800;
	$p_aktivitas = 'USER';
	
	$p_model = mUserSession;
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_role)) $a_filter[] = $p_model::getListFilter('role',$r_role);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Role', 'combo' => $l_role);
	
	require_once(Route::getViewPath('inc_list'));
?>