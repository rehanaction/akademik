<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('saldo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
	// $r_jenistagihan = Modul::setRequest($_POST['jenistagihan'],'JENISTAGIHAN');
	$r_sistem = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',false);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
	// $l_jenistagihan = uCombo::jenistagihan($conn,$r_jenistagihan,'jenistagihan','onchange="goSubmit()"',true);
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',true);
	$l_sistem = uCombo::sistemkuliah($conn,$r_sistem,'sistemkuliah','onchange="goSubmit()"',true);
	
	// properti halaman
	$p_title = 'Saldo Keuangan Mahasiswa '.Akademik::getNamaPeriode();
	$p_aktivitas = 'SPP';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label'=>'No', 'width'=>'3%');
	$a_kolom[] = array('kolom' => 't.nim', 'label'=>'NIM', 'width'=>'9%', 'align'=>'center');
	$a_kolom[] = array('kolom' => 'm.nama', 'label'=>'Nama');
	$a_kolom[] = array('kolom' => 'u.namaunit', 'label'=>'Jurusan', 'width'=>'15%');
	$a_kolom[] = array('kolom' => 'm.statusmhs', 'label'=>'Status');
	$a_kolom[] = array('kolom' => 't.totaltagihan', 'label'=>'Tagihan', 'width'=>'8%', 'align'=>'right', 'type'=>'N');
	$a_kolom[] = array('kolom' => 't.totaldenda', 'label'=>'Denda', 'width'=>'8%', 'align'=>'right', 'type'=>'N');
	$a_kolom[] = array('kolom' => 't.totalhutang', 'label'=>'Hutang', 'width'=>'8%', 'align'=>'right', 'type'=>'N');
	$a_kolom[] = array('kolom' => 't.totaldeposit', 'label'=>'Vochr/Dpsit', 'width'=>'8%', 'align'=>'right', 'type'=>'N');
	$a_kolom[] = array('kolom' => 't.saldo', 'label'=>'Saldo', 'width'=>'8%', 'align'=>'right', 'type'=>'N');
	$a_kolom[] = array('kolom' => 't.totalbayar', 'label'=>'Bayar', 'width'=>'8%', 'align'=>'right', 'type'=>'N');
	
	$p_model = mSaldo;
	$p_colnum = count($a_kolom);
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('kodeunit',$r_unit);
	// if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode); // periode pakai session saja
	if(!empty($r_jalur)) $a_filter[] = $p_model::getListFilter('jalurpenerimaan',$r_jalur);
	// if(!empty($r_jenistagihan)) $a_filter[] = $p_model::getListFilter('jenistagihan',$r_jenistagihan);
	if(!empty($r_sistem)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_sistem);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	// $a_filtercombo[] = array('label' => 'Jenis Tagihan', 'combo' => $l_jenistagihan);
	$a_filtercombo[] = array('label' => 'Kelas Mahasiswa', 'combo' => $l_sistem);
	
	require_once($conf['view_dir'].'inc_list.php');
?>