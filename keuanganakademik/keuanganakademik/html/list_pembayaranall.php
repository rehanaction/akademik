<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	
	$p_printpagedet = "rep_payment";
	
	$p_detailpage = "data_pembayaranall";
	// include
	require_once(Route::getModelPath('pembayaran'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periodebayar'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
	
	$arr_flag = mCombo::arrFlagtagihan();
	// combo
	//$l_periode = uCombo::periode($conn,$r_periode,'periodebayar','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',true);
	
	$r_sistem = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	$l_sistem = uCombo::sistemkuliah($conn,$r_sistem,'sistemkuliah','onchange="goSubmit()"',true);
	
	// properti halaman
	$p_title = 'Daftar Pembayaran';
	$p_tbwidth = '960';
	$p_aktivitas = 'Master';
	
	// struktur view
	$a_kolom = array();
	//$a_kolom[] = array('kolom' => 'idpembayaran', 'label' => 'ID Bayar', 'width'=>'6%','align'=>'center');
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM','align' => 'center');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Jurusan');
	$a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur');
	$a_kolom[] = array('kolom' => 'tglbayar', 'label' => 'Tgl Bayar', 'type' => 'D', 'width' => '75');
	$a_kolom[] = array('kolom' => 'p.jumlahbayar', 'label' => 'Jumlah', 'align' => 'right', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'nip', 'label' => 'Petugas');
	$a_kolom[] = array('kolom' => 'refno', 'label' => 'No. Ref.', 'align'=>'center');
	//$a_kolom[] = array('kolom' => 'periodebayar', 'label' => 'Periode','align'=>'center');
	$a_kolom[] = array('kolom' => 'ish2h', 'label' => 'H2H?', 'type' => 'C', 'option' => array('1' => ''), 'align' => 'center');
	$a_kolom[] = array('kolom' => 'flagbatal', 'label' => 'Batal?', 'type' => 'C', 'option' => array('1' => ''), 'align' => 'center');
	
	$p_model = mPembayaran;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'batal' and $c_edit) {
		$r_key = $_POST['key'];
		
		$p_posterr = mPembayaran::getReversal($conn,$r_key);
		
		if ($p_posterr)
			$p_postmsg = 'Pembatalan Pembayaran Gagal';
		else
			$p_postmsg = 'Pembatalan Pembayaran Berhasil';
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	//if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('kodeunit',$r_unit);
	if(!empty($r_jalur)) $a_filter[] = $p_model::getListFilter('jalurpenerimaan',$r_jalur);
	if(!empty($r_sistem)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_sistem);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	//$a_filtercombo[] = array('label' => 'Periode', 'kolom' => 'periodebayar','combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Kelas Mahasiswa', 'combo' => $l_sistem);
	
	require_once(Route::getViewPath('v_list_pembayaranall'));
?>
