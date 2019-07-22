<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug= true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit			= Modul::setRequest($_POST['unit'],'UNIT');
	$r_angkatan		= Modul::setRequest($_POST['angkatan'],'ANGKATAN');
	$r_jalur		= Modul::setRequest($_POST['jalur'],'JALUR');
	$r_gelombang	= Modul::setRequest($_POST['gelombang'],'GELOMBANG');
	
	// combo
	$l_unit			= uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"');
	$l_angkatan		= uCombo::tahun_angkatan($r_angkatan,true,'angkatan','onchange="goSubmit()"');
	$l_jalur		= uCombo::jalur($conn,$r_jalur,'jalur','onchange="goSubmit()"');
	$l_gelombang	= uCombo::gelombang($conn,$r_gelombang,'gelombang','onchange="goSubmit()"');
        
	// properti halaman
	$p_title = 'Beasiswa Mahasiswa';
	$p_tbwidth = '100%';
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No', 'width' => '1%');
	$a_kolom[] = array('kolom' => 'm.nim', 'label' => 'NIM', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'm.nama', 'label' => 'Nama Mahasiswa');
	$a_kolom[] = array('kolom' => 'u.namaunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'angkatan', 'label' => 'Angkatan', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'm.potongan', 'label' => 'Beasiswa', 'align' => 'right', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'm.potsmtawal', 'label' => 'Smt Awal', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'm.potsmtakhir', 'label' => 'Smt Akhir', 'align' => 'center');
	
	$p_model = mBeasiswa;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[]			= $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_angkatan)) $a_filter[]		= $p_model::getListFilter('angkatan',$r_angkatan);
	if(!empty($r_jalur)) $a_filter[]		= $p_model::getListFilter('jalur',$r_jalur);
	if(!empty($r_gelombang)) $a_filter[]	= $p_model::getListFilter('gelombang',$r_gelombang);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Angkatan', 'combo' => $l_angkatan);
	$a_filtercombo[] = array('label' => 'Jalur', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
	
	require_once($conf['view_dir'].'v_list_tagihan.php');
?>
