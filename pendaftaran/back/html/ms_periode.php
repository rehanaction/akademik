<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	/*
	//hapus setelah diintegrasikan
	$c_insert = true;
	$c_edit = true;
	$c_delete = true;
	*/
	// include
	require_once(Route::getModelPath('periode'));
	require_once(Route::getUIPath('combo'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'periodedaftar', 'label' => 'Periode Pendaftaran', 'size' => 5, 'maxlength' => 5, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'Aktif ?','type'=>'S', 'option'=>array(-1=>'Aktif',0=>'Tidak'));
	
	// properti halaman
	$p_title = 'Daftar Periode';
	$p_tbwidth = 300;
	$p_aktivitas = 'SPMB';
	
	$p_model = mPeriode;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		if (!empty($_POST['i_isaktif']))
			$cekaktif = $p_model::cekaktif($conn);

		if ($cekaktif)
			list($p_posterr,$p_postmsg) = array(true, 'Hanya diperbolehkan 1 periode yang aktif<br> Penyimpanan Gagal');
		else
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		if (!empty($_POST['u_isaktif']))
			$cekaktif = $p_model::cekaktif($conn);
			
		if ($cekaktif)
			list($p_posterr,$p_postmsg) = array(true, 'Hanya diperbolehkan 1 periode yang aktif<br>Penyimpanan Gagal');
		else
			list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
