<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('setonline'));
	//require_once(Route::getModelPath('periode'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	//variable salin
	$r_semestersalin = Modul::setRequest($_POST['semestersalin'],'SEMESTER');
	$r_tahunsalin = Modul::setRequest($_POST['tahunsalin'],'TAHUN');
	$r_periode = $r_tahun.$r_semester;
	$r_periodesalin = $r_tahunsalin.$r_semestersalin;
	
	// combo filter
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	//combo salin
	$l_semestersalin = uCombo::semester($r_semestersalin,false,'semestersalin','',false);
	$l_tahunsalin = uCombo::tahun($r_tahunsalin,true,'tahunsalin','',false);
	
	$p_model = mSetOnline;
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Prodi', 'type' => 'S', 'option' => mCombo::jurusan($conn));
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option' => mCombo::periode($conn,false));
	$a_kolom[] = array('kolom' => 'semmk', 'label' => 'Semester Mata Kuliah', 'size' => 1, 'maxlength' => 2, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'pertemuan', 'label' => 'List Pertemuan','type' => 'A', 'rows' => 3, 'cols' => 25, 'maxlength' => 100,'add'=>'placeholder="pisahkan dengan tanda koma (,) contoh : 5,7,9"');	
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'Status', 'type' => 'S', 'option' => array('-1'=>'aktif','0'=>'Tidak Aktif'));

	
	
	// properti halaman
	$p_title = 'Setting Aturan Pertemuan Online';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';
	
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
	else if($r_act == 'copy' and $c_edit) {
		list($p_posterr,$p_postmsg) = $p_model::copy($conn,$r_periode,$r_periodesalin);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Program Studi', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	
	//untuk salin aturab
	$a_salin=array('title'=>'Salin Data Aturan','label'=>'Salin Ke Periode','tujuan'=>$l_semestersalin.' '.$l_tahunsalin);
	require_once($conf['view_dir'].'inc_ms.php');
?>

	

