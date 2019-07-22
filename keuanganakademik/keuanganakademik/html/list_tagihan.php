<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	//$conn->debug = true;
	//var_dump(modul::getBasis());
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	//var_dump($_SESSION);
	$p_detailpage = "data_tagihan";
	// include
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarif'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$arr_flag = mCombo::arrFlagtagihan();
	$arr_unit = mCombo::unit($conn);
	
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT',$arr_unit);
	$r_jenistagihan = Modul::setRequest($_POST['jenistagihan'],'JENISTAGIHAN');
	$r_isedit = Modul::setRequest($_POST['isedit'],'ISEDIT');
	$r_bulantahun = Modul::setRequest($_POST['bulantahun'],'BULANTAHUN');
	$r_sistem = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
	$l_jenistagihan = uCombo::jenistagihan($conn,$r_jenistagihan,'jenistagihan','onchange="goSubmit()"',true);
	$l_unit = uCombo::combo($arr_unit,$r_unit,'kodeunit','onchange="goSubmit()"',false);
	$l_isedit = uCombo::isedit($r_isedit,'isedit','onchange="goSubmit()"',true);
	$l_sistem = uCombo::sistemkuliah($conn,$r_sistem,'sistemkuliah','onchange="goSubmit()"',true);	
        
	// properti halaman
	$p_title = 'Daftar Tagihan';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	$sisa = $a_kolom['nominaltagihan']-$a_kolom['nominalbayar'];
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No','width'=>'1%');
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM / No Pendaftar','align'=>'center');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Jurusan');
	$a_kolom[] = array('kolom' => 'namasistem', 'label' => 'Basis');
	$a_kolom[] = array('kolom' => 'idtagihan', 'label' => 'ID Tagihan','align'=>'center');
	$a_kolom[] = array('kolom' => 'jenistagihan', 'label' => 'Jenis Tagihan','align'=>'center');
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode','align'=>'center');
	$a_kolom[] = array('kolom' => 'tgltagihan', 'label' => 'Tgl Tagihan','type'=>'D');
	// $a_kolom[] = array('kolom' => 'bulantahun', 'label' => 'Bulan Tahun','align'=>'center');
	$a_kolom[] = array('kolom' => 'nominaltagihan', 'label' => 'Tagihan','type'=>'N','align'=>'right');
	$a_kolom[] = array('kolom' => 'nominalbayar', 'label' => 'Bayar','type'=>'N','align'=>'right');
	$a_kolom[] = array('kolom' => 'nominalbayar-nominaltagihan ', 'label' => 'Sisa','type'=>'N','align'=>'right');
	// $a_kolom[] = array('kolom' => 'denda', 'label' => 'Denda','type'=>'N','align'=>'right');
	$a_kolom[] = array('kolom' => 'flaglunas', 'label' => 'Status Lunas','align'=>'center');
	
	
	$p_model = mTagihan;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
		// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unitdesc',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_jalur)) $a_filter[] = $p_model::getListFilter('jalurpenerimaan',$r_jalur);
	if(!empty($r_jenistagihan)) $a_filter[] = $p_model::getListFilter('jenistagihan',$r_jenistagihan);
	if(!empty($r_isedit)) $a_filter[] = $p_model::getListFilter('isedit',$r_isedit);
	if(!empty($r_sistem)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_sistem);
	
	//filter by basis dan kampus
	//if(empty($r_sistem))
	$a_filter[] = $p_model::getListFilter('basiskampus');
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Kelas Mahasiswa', 'combo' => $l_sistem);
	$a_filtercombo[] = array('label' => 'Status Tagihan', 'combo' =>'');
	$a_filtercombo[] = array('label' => 'Jenis Tagihan', 'combo' => $l_jenistagihan);
	$a_filtercombo[] = array('label' => 'Status Edit', 'combo' => $l_isedit);
	
	$r_act = $_POST["act"];
	if($r_act=="print"){

	}


	
	require_once($conf['view_dir'].'v_list_tagihan.php');
?>
