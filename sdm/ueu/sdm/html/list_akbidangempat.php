<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_edit = $a_auth['canupdate'];
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('angkakredit'));
	require_once(Route::getUIPath('combo'));
	
	// variabel esensial
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
		
	// properti halaman
	$p_title = 'Daftar Bidang IV (Penunjang)';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mAngkaKredit;
	$p_key = 'nobidangiv';
	$p_dbtable = 'ak_bidang4';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai', 'type' => 'D', 'align' => 'center', 'width' => '100px');
	$a_kolom[] = array('kolom' => 'namaperiodeakad', 'label' => 'Periode', 'filter' => "substring(r.thnakademik,1,4)+'/'+substring(r.thnakademik,5,4)+' '+case when semester = '01' then 'Ganjil' else 'Genap' end");
	$a_kolom[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan', 'filter' => 'r.namakegiatan');
	$a_kolom[] = array('kolom' => 'kegiatan', 'label' => 'Indeks Akreditasi', 'filter' => "m.kodekegiatan+' - '+m.namakegiatan");
	$a_kolom[] = array('kolom' => 'nilaikredit', 'label' => 'Nilai Kredit', 'align' => 'center', 'filter' => 'r.nilaikredit');
	$a_kolom[] = array('kolom' => 'statusvalidasiak', 'label' => 'Status', 'filter' => "case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' else 'Belum' end");
	$a_kolom[] = array('kolom' => 'isvalid', 'type' => 'H');

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$a_key = $r_key.'|'.$r_subkey;
		$where = 'idpegawai,nobidangiv';
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_key,$p_dbtable,$where,'','filebidangempat');
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'tglmulai desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryBidang4($r_key);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	require_once(Route::getViewPath('inc_listajax'));
?>
