<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	//$conn->debug = true;
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//koneksi dengan akademik
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
		$connsia->debug=true;
	
	// include
	require_once(Route::getModelPath('honor'));
	require_once(Route::getUIPath('combo'));
	
	
	$p_model = mHonor;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Sistem Kuliah','type' => 'S', 'option' => $p_model::getSistemKuliah($connsia));
	$a_kolom[] = array('kolom' => 'nohari', 'label' => 'Hari','type' => 'S', 'option' => $p_model::getHari());
	$a_kolom[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis Kuliah','type' => 'S', 'option' => $p_model::getJnsPerkuliahan($connsia));
	$a_kolom[] = array('kolom' => 'isonline', 'label' => 'Online?', 'type' => 'R', 'option' => $p_model::getIsOnline());	
	$a_kolom[] = array('kolom' => 'kodejnsrate', 'label' => 'Jenis Rate','type' => 'S', 'option' => $p_model::getRateJenisRate($conn));
	
	// properti halaman
	$p_title = 'Setting Tarif Ajar';
	$p_tbwidth = 700;
	$p_aktivitas = 'SETTING';
	$p_dbtable = 'ms_mapratehonor';
	$p_key = 'sistemkuliah,nohari,jeniskuliah,isonline';
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST,$p_dbtable);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'sistemkuliah';
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,'','',$p_dbtable);
	
	require_once($conf['view_dir'].'inc_ms.php');
?>
