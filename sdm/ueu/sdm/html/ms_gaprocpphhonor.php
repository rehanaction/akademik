<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('honor'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mHonor;
	$p_dbtable = 'ms_procpphhonor';
	$p_key = 'idhubkerja,isnpwp';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idhubkerja', 'label' => 'Hubungan Kerja', 'type' => 'S', 'option' => $p_model::getCHubKerja($conn), 'notnull' => true);
	$a_kolom[] = array('kolom' => 'isnpwp', 'label' => 'NPWP?', 'type' => 'S', 'option' => $p_model::getCNPWP($conn),  'align' => 'center','notnull' => true);
	$a_kolom[] = array('kolom' => 'prosentase', 'label' => '% PPh.', 'maxlength' => 3, 'size' => 3, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'isAktif?', 'type' => 'S', 'option' => SDM::getValid($conn), 'align' => 'center', 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Prosentase PPh Honor Mengajar';
	$p_tbwidth = 550;
	$p_aktivitas = 'ANGGARAN';
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {	
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST,$p_dbtable,$r_key,$p_key);
		if(empty($p_posterr))			
			list($p_posterr,$p_postmsg) = $p_model::setPPHHonor($conn,'',$_POST['i_idhubkerja'],$_POST['i_isnpwp']);
			
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'update' and $c_edit) {
		$conn->BeginTrans();
		
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key,$p_dbtable,$p_key);
		if(empty($p_posterr))			
			list($p_posterr,$p_postmsg) = $p_model::setPPHHonor($conn,'',$_POST['u_idhubkerja'],$_POST['u_isnpwp']);
			
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
		
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'idhubkerja';
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter,'',$p_dbtable);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>