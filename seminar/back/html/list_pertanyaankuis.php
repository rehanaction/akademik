<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pertanyaankuisseminar'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Data Soal Kuisioner';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	$p_model = mPertanyaanKuisSeminar;
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"');
	
	if(empty($r_periode)) {
		$a_idseminar = array();
		$r_idseminar = null;
	}
	else {
		$a_idseminar = mCombo::seminar($conn,$r_periode);
		$r_idseminar = Modul::setRequest($_POST['idseminar'],'IDSEMINAR');
		
		if(empty($a_idseminar[$r_idseminar]))
			$r_idseminar = null;
	}
	$l_idseminar = UI::createSelect('idseminar',$a_idseminar,$r_idseminar,'ControlStyle',true,'onchange="goSubmit()" style="max-width:350px"',true,(empty($r_periode) ? '-- Pilih Periode terlebih dahulu --' : '-- Pilih Seminar --'));
	
	// variabel untuk salin
	if($c_edit) {
		if(empty($r_idseminar)) {
			$a_periodeke = array();
			$r_periodeke = null;
		}
		else {
			$a_periodeke = mCombo::periode($conn);
			$r_periodeke = Modul::setRequest($_POST['periodeke'],'PERIODECOPY');
			
			if(empty($a_periodeke[$r_periodeke]))
				$r_periodeke = null;
		}
		$l_periodeke = UI::createSelect('periodeke',$a_periodeke,$r_periodeke,'ControlStyle',true,'onchange="goSubmit()"',true,(empty($r_idseminar) ? '-- Pilih Seminar terlebih dahulu --' : '-- Pilih Periode --'));
		
		if(empty($r_periodeke)) {
			$a_idseminarke = array();
			$r_idseminarke = null;
		}
		else {
			$a_idseminarke = mCombo::seminar($conn,$r_periodeke);
			$r_idseminarke = Modul::setRequest($_POST['idseminarke'],'IDSEMINARCOPY');
			
			if(empty($a_idseminarke[$r_idseminarke]))
				$r_idseminarke = null;
		}
		$l_idseminarke = UI::createSelect('idseminarke',$a_idseminarke,$r_idseminarke,'ControlStyle',true,'onchange="goSubmit()" style="max-width:230px"',true,(empty($r_periodeke) ? '-- Pilih Periode disamping --' : '-- Pilih Seminar --'));
		
		if(!empty($r_idseminarke))
			$x_tombol = '&nbsp;<input type="button" value="Salin" class="ControlStyle" onClick="goSalin()">';
	}
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namaseminar', 'label' => 'Seminar');
	$a_kolom[] = array('kolom' => 'nomor', 'label' => 'Nomor');
	$a_kolom[] = array('kolom' => 'pertanyaan', 'label' => 'Pertanyaan');
	$a_kolom[] = array('kolom' => 'namakategori', 'label' => 'Kategori Jawaban');
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'Aktif?','option'=>array('1'=>'Aktif','0'=>'Tidak Aktif'));
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'copy' and $c_edit and !empty($r_idseminarke)) {
		list($p_posterr,$p_postmsg) = $p_model::copy($conn,$r_idseminar,$r_idseminarke);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_idseminar)) $a_filter[] = $p_model::getListFilter('idseminar',$r_idseminar);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Seminar', 'combo' => $l_idseminar);
	if($c_edit) {
		$a_salin = true; // hanya untuk mengaktifkan function js goSalin()
		$a_filtercombo[] = array('label' => 'Salin Ke', 'combo' => $l_periodeke.' '.$l_idseminarke.' '.$x_tombol);
	}
	
	require_once(Route::getViewPath('inc_list'));
?>
