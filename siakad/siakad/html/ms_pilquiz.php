<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pilquiz'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_periode=$r_tahun.$r_semester;
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'pilihan', 'label' => 'Urutan', 'size' => 1, 'maxlength' => 1, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'point', 'label' => 'Skor', 'size' => 2, 'maxlength' => 2, 'notnull' => true,'type'=>'N');
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Keterangan Pilihan', 'size' => 50, 'maxlength' => 100);
	
	// properti halaman
	$p_title = 'Data Pilihan Soal Quisioner';
	$p_tbwidth = 610;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mPilQuiz;
	$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		$a_kolom[] = array('kolom' => 'periode', 'value' => $r_periode);
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		// buang lagi unit
		array_pop($a_kolom);
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
	$a_sort = Page::setSort($_POST['sort']);
	
	//ambil data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	$a_data = $p_model::getListData($conn,$a_kolom,$a_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
