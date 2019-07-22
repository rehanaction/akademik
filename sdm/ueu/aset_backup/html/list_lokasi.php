<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('lokasi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_jenislokasi = Modul::setRequest($_POST['jenislokasi'],'JENISLOKASI');
	$r_gedung = Modul::setRequest($_POST['gedung'],'GEDUNG');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_lantai = Modul::setRequest($_POST['lantai'],'LANTAI');

	// combo
	$lr = Modul::getLeftRight();
	if($lr['LEFT'] == '1')
    	$l_unit = uCombo::unitAuto($conn,$r_unit,'unit','onchange="goSubmit()"');
	else
    	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:250px;"',true);
	$l_jenislokasi = uCombo::jenislokasi($conn,$r_jenislokasi,'jenislokasi','onchange="goSubmit()" style="width:200px;"',true);
	$l_gedung = uCombo::gedung($conn,$r_gedung,'gedung','onchange="goSubmit()" style="width:200px;"',true);
	$l_lantai = uCombo::lantai($conn,$r_lantai,'lantai','onchange="goSubmit()" style="width:125px;"',true);

	// properti halaman
	$p_title = 'Daftar Lokasi';
	$p_tbwidth = 900;
	$p_aktivitas = 'Lokasi';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mLokasi;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idlokasi', 'label' => 'ID Lokasi', 'width' => 60);
	$a_kolom[] = array('kolom' => 'namalokasi', 'label' => 'Nama Lokasi');
	$a_kolom[] = array('kolom' => 'unit', 'label' => 'Unit', 'width' => 200);
	$a_kolom[] = array('kolom' => 'jenislokasi', 'label' => 'Jenis Lokasi', 'width' => 120,'nosearch' => true);
	$a_kolom[] = array('kolom' => 'namagedung', 'label' => 'Gedung', 'width' => 75,'nosearch' => true);
	$a_kolom[] = array('kolom' => 'lantai', 'label' => 'Lantai', 'width' => 40, 'align' => 'center', 'type' => 'N');
	//$a_kolom[] = array('kolom' => 'kapasitas', 'label' => 'Kapasitas (Orang)', 'width' => 50, 'align' => 'right', 'type' => 'N');
	
	$p_colnum = count($a_kolom)+1;
	
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
	if(!empty($r_jenislokasi)) $a_filter[] = $p_model::getListFilter('jenislokasi',$r_jenislokasi);
	if(!empty($r_gedung)) $a_filter[] = $p_model::getListFilter('gedung',$r_gedung);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_lantai)) $a_filter[] = $p_model::getListFilter('lantai',$r_lantai);

	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jenis Lokasi', 'combo' => $l_jenislokasi);
	$a_filtercombo[] = array('label' => 'Gedung', 'combo' => $l_gedung);
	$a_filtercombo[] = array('label' => 'Lantai', 'combo' => $l_lantai);

	
	require_once(Route::getViewPath('inc_list'));
?>
