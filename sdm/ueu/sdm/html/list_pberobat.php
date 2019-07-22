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
	require_once(Route::getModelPath('pekerjaan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel esensial
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	$p_model = mPekerjaan;
	
	//cek pengobatan
	$cekpengobatan = $p_model::cekPurna($conn,$r_key);
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_delete = $a_auth['candelete'];
		
		if ($cekpengobatan == 0){
			$c_insert = false;
			list($p_posterr,$p_postmsg) = array(true,'Masa pengajuan berobat anda sudah habis');
		}		
	}
	
	// properti halaman
	$p_title = 'Daftar Pengajuan Berobat';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	$p_key = 'nourutberobat';
	$p_dbtable = 'pe_rwtberobat';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'align' => 'center', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'nosurat', 'label' => 'No. Surat');
	$a_kolom[] = array('kolom' => 'statusob', 'label' => 'Pengobatan Bagi','filter' => "case when r.status = 'K' then 'Karyawan/ti' when r.status = 'I' then 'Istri/Suami' when r.status = 'A' then 'Anak' else '' end");
	$a_kolom[] = array('kolom' => 'keluhan', 'label' => 'Keluhan');
	$a_kolom[] = array('kolom' => 'isvalid', 'type' => 'H');

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		$a_key = $r_key.'|'.$r_subkey;
		$where = 'idpegawai,nourutberobat';
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$where,'','filerujuk');
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'tglpengajuan desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryBerobat($r_key);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	require_once(Route::getViewPath('inc_listajax'));
?>
