<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mPa;
			
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeformsubyektif', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namaperiode', 'label' => 'Periode Bobot');
	$a_kolom[] = array('kolom' => 'namaform', 'label' => 'Form');
	$a_kolom[] = array('kolom' => 'namapajenis', 'label' => 'Jenis Penilai');
	
	// properti halaman
	$p_title = 'Daftar Form Penilaian';
	$p_tbwidth = 700;
	$p_aktivitas = 'NILAI';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = "pa_formsubyektif";
	$p_key = "kodeformsubyektif";
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'kodeformsubyektif';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$r_periode = CStr::removeSpecial($_POST['periode']);
	if (empty($r_periode)) $r_periode = $p_model::getLastPeriodeBobot($conn);
	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodebobot',$r_periode);
	
	$sql = $p_model::listQueryForm();
					
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode Bobot', 'combo' => UI::createSelect('periode',mPa::getCPeriodeBobot($conn), $r_periode, 'ControlStyle',$c_edit,'onChange="goSubmit()"'));
	
	require_once(Route::getViewPath('inc_list'));
?>