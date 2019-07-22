<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('transfernilai'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(isset($_POST['mhstransfer']))
		$r_status=$_POST['mhstransfer'];
	else
		$r_status='1';
		
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false,true);
	$l_status=uCombo::listStatusTransfer('mhstransfer',$r_status,'onchange="goSubmit()"');
	
	// properti halaman
	$p_title = 'Transfer Nilai Mahasiswa';
	$p_tbwidth = 850;
	$p_aktivitas = 'NILAI';
	if($r_status=='1')
		$p_detailpage = 'set_transfernilai';
	else
		$p_detailpage = 'set_transfernilaiex';
	
	$p_model = mTransferNilai;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'sex', 'label' => 'L/P');
	$a_kolom[] = array('kolom' => 'm.kodeunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'ptjurusan', 'label' => 'Prodi.Lama');
	$a_kolom[] = array('kolom' => 'nim', 'alias' => 'nimlama', 'label' => 'NIM Lama');
	$a_kolom[] = array('kolom' => 'semestermhs', 'label' => 'Sem.');
	$a_kolom[] = array('kolom' => 'statusmhs', 'label' => 'Status');
	$a_kolom[] = array('kolom' => $p_model::schema.'.f_namaperiode(periodemasuk)', 'alias' => 'namaperiode', 'label' => 'Periode Daftar');
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_status)) $a_filter[] = $p_model::getListFilter('mhstransfer',$r_status);
	
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Status Transfer', 'combo' => $l_status);
	
	//$a_filtercombo[] = array('label' => 't', 'combo' => $l_unit);
	
	require_once(Route::getViewPath('inc_list'));
?>
