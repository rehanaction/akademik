<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	//$c_editpass = $c_edit;
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once($conf['helpers_dir'].'date.class.php');
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode 	= Modul::setRequest($_POST['periodedaftar'],'PERIODE DAFTAR');

	
	
	//combo
	$l_periode = uCombo::periodeDaftar($conn,$r_periode,'periodedaftar','onchange="goSubmit()"',true);
	
	// properti halaman
	$p_title = 'Daftar Tarif Formulir Jalur Penerimaan';
	$p_tbwidth = 800;
	$p_aktivitas = 'Master';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mAkademik;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$data = mAkademik::getGelombangdaftar($conn,$r_periode);

	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Tahun Pendaftaran', 'combo' => $l_periode);
	
		require_once($conf['view_dir'].'v_list_tariffrm.php');
?>
