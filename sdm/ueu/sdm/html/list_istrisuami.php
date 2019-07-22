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
	require_once(Route::getUIPath('form'));	
	
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
	$p_title = 'Daftar Istri/Suami';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mRiwayat;
	$p_key = 'nourutist';
	$p_dbtable = 'pe_istrisuami';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'namapasangan', 'label' => 'Nama Istri/Suami');
	$a_kolom[] = array('kolom' => 'jnskelamin', 'label' => 'Jenis Kelamin', 'filter' => "case when r.jeniskelamin = 'P' then 'Perempuan' when r.jeniskelamin = 'L' then 'Laki-laki' else '' end");
	$a_kolom[] = array('kolom' => 'tgllahir', 'label' => 'Tgl Lahir', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'stspasangan', 'label' => 'Status','filter' => "case when r.statuspasangan = 'W' then 'Wafat' when r.statuspasangan = 'H' then 'Hidup' else '' end");
	$a_kolom[] = array('kolom' => 'isvalid', 'type' => 'H');

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$a_key = $r_key.'|'.$r_subkey;
		$where = 'idpegawai,nourutist';
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_key,$p_dbtable,$where);
		if(!$p_posterr){
			$dirfoto = 'fotopasangan';
			$p_foto = uForm::getPathImageFoto($conn,$r_subkey,$dirfoto);
			@unlink($p_foto);
		}
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'nourutist';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQueryRiwayatIST($r_key);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
			
	//cek apakah tidak bisa insert bila sudah punya istri
	$ispunyaistrisuami = $p_model::cekPunyaIstriSuami($conn,$r_key);
	if($ispunyaistrisuami)
		$c_insert = false;
		
	require_once(Route::getViewPath('inc_listajax'));
?>
