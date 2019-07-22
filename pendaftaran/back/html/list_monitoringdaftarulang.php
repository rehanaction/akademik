<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = false;//$a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = false;//$a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_kodeunit = Modul::setRequest($_POST['kodeunit'],'KODEUNIT');
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'','periode','onchange="goSubmit()"',true);
	$l_kodeunit = uCombo::unit($conn,$r_kodeunit,'','kodeunit','onchange="goSubmit()"',true);
	
	// properti halaman
	$p_title = 'Data Pendaftar (daftar ulang)';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPendaftar;
	
	$list_unit = mUnit::listJurusan($conn);
	$list_sistemkuliah = mCombo::sistemKuliah($conn);
	// struktur view
	$a_kolom = array();
	
	$a_kolom[] = array('kolom' => 'nopendaftar', 'label' => 'No Pendaftar');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'hp', 'label' => 'Nomor HP');
	$a_kolom[] = array('kolom' => 'email', 'label' => 'E-Mail');
	$a_kolom[] = array('kolom' => 'pilihan1', 'label' => 'Pilihan 1', 'type'=>'S', 'option'=>$list_unit);
	$a_kolom[] = array('kolom' => 'pilihanditerima', 'label' => 'Jurusan', 'type'=>'S', 'option'=>$list_unit);
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis', 'type'=>'S', 'option'=>$list_sistemkuliah);
	$a_kolom[] = array('kolom' => 'isfollowup', 'label' => 'Follow Up','type'=>'S', 'option'=>array('-1'=>'<img src="images/check.png">', '0' =>''), 'align'=>'center');
	//$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan');
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_kodeunit)) $a_filter[] = $p_model::getListFilter('pilihan1',$r_kodeunit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	$a_filter[] = $p_model::getListFilter('isdaftarulang',-1);
	
	//$sql = $p_model::getSQLTagihan($conn,$r_periode,$r_kodeunit);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	 $a_filtercombo = array();
	 $a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	 $a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_kodeunit);
	
	
	require_once(Route::getViewPath('inc_list'));
?>
