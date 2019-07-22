<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = true; // $a_auth['canupdate'];
	 $c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('smu'));
	require_once(Route::getUIPath('combo'));
	
	$r_kota = Modul::setRequest($_POST['kota'],'KOTA');
	
	$l_kota = uCombo::kota($conn,$r_kota,'','kota','onchange="goSubmit()"',true,$r_propinsi);
	
	// properti halaman
	$p_title = 'Data SMU';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';
	
	$p_model = mSmu;
	$kota=$p_model::getKota($conn);
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namasmu', 'label' => 'SMU', 'size' => 20, 'maxlength' => 20, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'alamatsmu', 'label' => 'Alamat', 'size' => 2, 'maxlength' => 9, 'notnull' => true, 'type'=>'A');
	$a_kolom[] = array('kolom' => 'telpsmu', 'label' => 'Telp', 'size' => 15, 'maxlength' => 13, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'kodekota', 'label' => 'Kota', 'notnull' => true, 'type'=>'S', 'option' => $kota,'readonly'=>true);
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	if(!empty($r_kota)) $a_filter[] = $p_model::getListFilter('kota',$r_kota);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Kota', 'combo' => $l_kota);
	require_once($conf['view_dir'].'inc_list.php');
?>
