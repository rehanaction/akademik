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
	$p_title = 'Daftar Riwayat Pendidikan';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mRiwayat;
	$p_key = 'nourutrpen';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Jenjang Pendidkan');
	$a_kolom[] = array('kolom' => 'namainstitusipend', 'label' => 'Nama Institusi','filter' => 'case when r.kodept is not null then t.namapt else namainstitusi end');
	$a_kolom[] = array('kolom' => 'noijazah', 'label' => 'No Ijazah');
	$a_kolom[] = array('kolom' => 'tglijazah', 'label' => 'Tgl Ijazah', 'type' => 'D', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'isvalid', 'type' => 'H');

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$a_key = $r_key.'|'.$r_subkey;
		$where = 'idpegawai,nourutrpen';
		
		//pendidikan terakhir sebelum disimpan
		$pendakhirbs = $p_model::getPendidikanAkhir($conn,$r_key);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_key,'pe_rwtpendidikan',$where,'','fileijazah');
		
		if(!$p_posterr){			
			//pendidikan terakhir setelah dihapus
			$pendakhirss = $p_model::getPendidikanAkhir($conn,$r_key);
			
			//bila ada perubah pendidikan terakhir, maka unvalid dulu tarif honor
			if($pendakhirss != $pendakhirbs){
				$p_posterr = mGaji::unValidRateHonor($conn,$r_key);
				if($p_posterr)
					$p_postmsg = 'Penyimpanan Unvalid honor gagal';
			}
			
			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = mGaji::setRateHonor($conn,$r_key);
		}
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	if (empty($r_sort))
		$r_sort = 'idpendidikan';
	
	$sql = $p_model::listQueryRiwayatPendidikan($r_key);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	require_once(Route::getViewPath('inc_listajax'));
?>
