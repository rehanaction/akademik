<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('fasilitas'));
	require_once(Route::getUIPath('combo'));

	// variabel request
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');

	// properti halaman
	$p_title = 'Data Fasilitas';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mFasilitas;
	// struktur view
	$a_kolom = array();
	
	//combo
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',false);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit); 
	
	
	$a_kolom[] = array('kolom' => 'u.namaunit', 'label' => 'Nama'); 
	$a_kolom[] = array('kolom' => 'luastanah', 'label' => 'Luas Tanah');
	$a_kolom[] = array('kolom' => 'luasrkuliah', 'label' => 'Luas Ruang Kuliah');	
	$a_kolom[] = array('kolom' => 'luasrlab', 'label' => 'Luas Ruang Lab');  
	$a_kolom[] = array('kolom' => 'luasrasramamhs', 'label' => 'Luas Arama Mahasiswa');
	$a_kolom[] = array('kolom' => 'luasraula', 'label' => 'Luas Aula');
	$a_kolom[] = array('kolom' => 'luasrkomputer', 'label' => 'Luas Ruang Komputer'); 
	$a_kolom[] = array('kolom' => 'luasrperpus', 'label' => 'Luas Perpustakaan');
	//$a_kolom[] = array('kolom' => 'u.kodeunit', 'label' => 'Kode'); 
	//$a_kolom[] = array('kolom' => 'luaskebun', 'label' => 'Luas Kebun');
	//$a_kolom[] = array('kolom' => 'luasradministrasi', 'label' => 'Luas Ruang Administrasi'); 
	//$a_kolom[] = array('kolom' => 'jumlahrperpus', 'label' => 'Jumlah Luas Ruang Perpustakaan');
	//$a_kolom[] = array('kolom' => 'jumlahjuduldigunakan', 'label' => 'Jumlah Judul Digunakan');
	//$a_kolom[] = array('kolom' => 'judulpustaka', 'label' => 'Judul Pustaka');
	//$a_kolom[] = array('kolom' => 'jumlahjudul_pustaka', 'label' => 'Jumlah Judul Pustaka');
	//$a_kolom[] = array('kolom' => 'jumlahrlab', 'label' => 'Jumlah Ruang Lab');
	//$a_kolom[] = array('kolom' => 'luasrdosen', 'label' => 'Jumlah Ruang Dosen'); 
	//$a_kolom[] = array('kolom' => 'jumlahrkomputer', 'label' => 'Jumlah Ruang Komputer');
	//$a_kolom[] = array('kolom' => 'luasrekskulmhs', 'label' => 'Luas Ruang Ekskul Mahasiswa');
	//$a_kolom[] = array('kolom' => 'jumlahrekskulmhs', 'label' => 'Jumlah Luas Ruang Ekskul Mahasiswa');
	//$a_kolom[] = array('kolom' => 'luasrseminar', 'label' => 'Luas Ruang Seminar');  
	//$a_kolom[] = array('kolom' => 'luasperumahan', 'label' => 'Luas Perumahan');
	
	// ada aksi
	$r_act = $_POST['act'];
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
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('kodeunit',$r_unit);
	//print_r($a_filter);  
	//$sql = $p_model::listQueryProdi();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	require_once(Route::getViewPath('inc_list'));
?>

