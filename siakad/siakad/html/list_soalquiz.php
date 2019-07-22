<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('soalquiz'));
	require_once(Route::getUIPath('combo'));
	// variabel request
	// $r_kurikulum = Modul::setRequest($_POST['kurikulum'],'KURIKULUM');
	
	// combo
	// $l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Data Soal Quisioner';
	$p_tbwidth = 750;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mSoalQuiz;
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_semestersalin = Modul::setRequest($_POST['semestersalin'],'SEMESTER');
	$r_tahunsalin = Modul::setRequest($_POST['tahunsalin'],'TAHUN');
	$r_periode=$r_tahun.$r_semester;
	$r_idjenissoal=Modul::setRequest($_POST['idjenissoal'],'IDJENISSOAL');;
	// combo
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_semestersalin = uCombo::semester($r_semestersalin,false,'semestersalin','',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$l_tahunsalin = uCombo::tahun($r_tahunsalin,true,'tahunsalin','',false);
	$l_idjenissoal = uCombo::jenisQuiz($conn,$r_idjenissoal,'idjenissoal','onchange="goSubmit()"',true);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'soal', 'label' => 'Soal');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status Soal');
	$a_kolom[] = array('kolom' => 'namajenissoal', 'label' => 'Jenis Soal');
	
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}else if($r_act == 'copy' and $c_insert) {
		$r_tujuan=$_POST['tahunsalin'].$_POST['semestersalin'];
		list($p_posterr,$p_postmsg) = $p_model::copy($conn,$r_periode,$r_tujuan);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	// if(!empty($r_kurikulum)) $a_filter[] = $p_model::getListFilter('thnkurikulum',$r_kurikulum);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_idjenissoal)) $a_filter[] = $p_model::getListFilter('idjenissoal',$r_idjenissoal);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	$a_filtercombo[] = array('label' => 'Jenis Soal', 'combo' => $l_idjenissoal);
	
	//untuk salin soal
	$a_salin=array('title'=>'Salin Data Soal Quisioner','label'=>'Salin Ke Periode','tujuan'=>$l_semestersalin.' '.$l_tahunsalin);
	require_once(Route::getViewPath('inc_list'));
?>
