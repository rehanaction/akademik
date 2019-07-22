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
	require_once(Route::getModelPath('riwayat'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel esensial
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
		
	// properti halaman
	$p_title = 'Daftar Riwayat Jabatan Struktural';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mRiwayat;
	$p_key = 'nourutjs';
	$p_dbtable = 'pe_rwtstruktural';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'tmtmulai', 'label' => 'Tgl. Mulai', 'type' => 'D', 'align' => 'center', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'tmtselesai', 'label' => 'Tgl. Selesai', 'type' => 'D', 'align' => 'center', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'jenispejabat', 'label' => 'Jenis Pejabat', 'filter' => 'p.jenispejabat');
	$a_kolom[] = array('kolom' => 'jabatanstruktural', 'label' => 'Nama Jabatan', 'filter' => 'j.jabatanstruktural');

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$a_key = $r_key.'|'.$r_subkey;
		$where = 'idpegawai,nourutjs';
		
		$p_posterr = mIntegrasi::deletePejabatRole($conn,$r_key,$r_subkey);
		if($p_posterr)
			$p_postmsg = 'Penghapusan Role Pejabat ke Gate gagal';
		
		//update kepala unitnya
		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveKepalaUnit($conn,$connmutu,$r_subkey);
			if($p_posterr)
				$p_postmsg = 'Pengubahan Kepala Unit gagal';
		}

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_key,$p_dbtable,$where,'','filestruktural');
					
		$ok = Query::isOK($p_posterr);
		if($ok)
			$conn->CommitTrans($ok);
		else
			$conn->RollbackTrans();
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'tmtmulai desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryRiwayatStruktural($r_key);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	require_once(Route::getViewPath('inc_listajax'));
?>
