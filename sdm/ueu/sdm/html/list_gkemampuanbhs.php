<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_edit = $a_auth['canupdate'];
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('pengembangan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel esensial
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
		
	// properti halaman
	$p_title = 'Daftar Kemampuan Bahasa';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPengembangan;
	$p_key = 'nourutkbhs';
	$p_dbtable = 'pe_kemampuanbhs';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'tahunkemampuan', 'label' => 'Tahun', 'align' => 'center', 'width' => '100px');
	$a_kolom[] = array('kolom' => 'kemampuandengar', 'label' => 'Kemampuan Mendengar', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'kemampuanbicara', 'label' => 'Kemampuan Bicara', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'kemampuantulisan', 'label' => 'Kemampuan Menulis', 'align' => 'center');

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$a_key = $r_key.'|'.$r_subkey;
		$where = 'idpegawai,nourutkbhs';
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_key,$p_dbtable,$where);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'tahunkemampuan desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryKemampuanBhs($r_key);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	require_once(Route::getViewPath('inc_listajax'));
?>
