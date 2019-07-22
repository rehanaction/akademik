<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kalender'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array(); 
	$a_kolom[] = array('kolom' => 'tglmulai', 'label' => 'Tanggal Mulai', 'type'=>'D');
	$a_kolom[] = array('kolom' => 'tglselesai', 'label' => 'Tanggal Selesai', 'type'=>'D');
	$a_kolom[] = array('kolom' => 'kodekegiatan', 'label' => 'Kegiatan', 'size' => 5, 'maxlength' => 5, 'type'=>'S', 'option'=>mCombo::kegiatan());
	$a_kolom[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'size' => 50, 'maxlength' => 100, 'notnull' => true);

	// variabel request 
	$r_tahun=Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan=Modul::setRequest($_POST['bulan'],'BULAN');
	
	// combo
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$l_bulan = uCombo::bulan($r_bulan,'bulan','onchange="goSubmit()"',false);
	
	// membuat filter
	$a_filtercombo = array(); 
	/* $a_filtercombo[] = array('label' => 'Bulan', 'combo' => $l_bulan);
	$a_filtercombo[] = array('label' => 'Tahun', 'combo' => $l_tahun); */
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_bulan.' '.$l_tahun);

	// properti halaman
	$p_title = 'Kalender Kegiatan';
	$p_tbwidth = 800;
	$p_aktivitas = 'JADWAL';
	
	$p_model = mKalender;
	$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);

	
	// mendapatkan data ex
	$r_periode=$r_tahun.str_pad($r_bulan, 2, '0', STR_PAD_LEFT);
	
	$r_sort = Page::setSort($_POST['sort']);
	
	$a_filter[] = $p_model::getListFilter('periode',$r_periode); 	
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort, $a_filter);
 
	
	require_once($conf['view_dir'].'inc_ms.php');
?>