<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getModelPath('jenisbeasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// combo
	$a_periode = array('' => '-- Semua Periode --') + mCombo::periode($conn);
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE',$a_periode);
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');
	
	$a_penerima = array('' => '-- Semua Jenis Penerima --') + mCombo::jenispenerima();
	$r_penerima = Modul::setRequest($_POST['jenispenerima'],'JENISPENERIMA',$a_penerima);
	$l_penerima = UI::createSelect('jenispenerima',$a_penerima,$r_penerima,'ControlStyle',true,'onchange="goSubmit()"');
	
	// properti halaman
	$p_title = 'Data Beasiswa';
	$p_tbwidth = 800;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();

	$p_model = mBeasiswa;

	// variabel request
	$r_kodesumberbeasiswa = CStr::removeSpecial($_POST['kodesumberbeasiswa']);
	$r_jenis = CStr::removeSpecial($_POST['jenis']);

	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namasumberbeasiswa', 'label' => 'Sumber');
	$a_kolom[] = array('kolom' => 'namajenisbeasiswa', 'label' => 'Jenis');
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option' => $a_periode);
	$a_kolom[] = array('kolom' => 'namabeasiswa', 'label' => 'Nama Beasiswa');
	// $a_kolom[] = array('kolom' => 'jumlah', 'label' => 'Penerima', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'jumlahpenerima', 'label' => 'Penerima', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'jumlahbeasiswa', 'label' => 'Kuota', 'type' => 'N');

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
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);

	if(!empty($r_periode))
		$a_filter[] = $p_model::getListFilter('b.periode',$r_periode);
	if(!empty($r_penerima))
		$a_filter[] = $p_model::getListFilter('b.pesertabeasiswa',$r_penerima);
	if(!empty($r_kodesumberbeasiswa))
		$a_filter[] = $p_model::getListFilter('b.kodesumberbeasiswa',$r_kodesumberbeasiswa);
	if(!empty($r_jenis))
		$a_filter[] = $p_model::getListFilter('b.idjenisbeasiswa',$r_jenis);

	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jenis Penerima', 'combo' => $l_penerima);
	$a_filtercombo[] = array('label' => 'Sumber Beasiswa', 'combo' => uCombo::sumberbeasiswa($conn,$r_kodesumberbeasiswa,'kodesumberbeasiswa','onchange="goSubmit()"'));
	$a_filtercombo[] = array('label' => 'Jenis Beasiswa', 'combo' => uCombo::jenisbeasiswa($conn,$r_jenis,'jenis','onchange="goSubmit()"'));

	// mendapatkan data
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);

	require_once(Route::getViewPath('inc_list'));
?>
