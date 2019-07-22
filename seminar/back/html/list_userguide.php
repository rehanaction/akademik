<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// $conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = true; 
	$a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('userguide'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'User Guide';
	$p_tbwidth = 600;
	
	$p_model = mUserGuide;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idguide', 'label' => 'ID UG');
	$a_kolom[] = array('kolom' => 'namaguide', 'label' => 'Nama User Guide');
	$a_kolom[] = array('kolom' => 'isfront', 'label' => 'Front', 'type' => 'C', 'option' => array(1=> 'Aktif'));
	$a_kolom[] = array('kolom' => 'fileguide', 'label' => 'File User Guide', 'type' => 'U', 'uptype' => 'ug', 'size' => 40,'maxsize'=>'10','arrtype'=>array('png','doc','pdf','ppt','xls','xlsx','docx','rar','zip'), 'readonly' => true);
	// $a_kolom[] = array('kolom' => 'up.namaunit', 'alias' => 'namaparent', 'label' => 'Induk');
	// $a_kolom[] = array('kolom' => 'kodeurutan', 'label' => 'Kode Urutan');
	//$a_kolom[] = array('kolom' => 'level', 'label' => 'Level', 'type' => 'S', 'option' => $p_model::namaLevel()); // type S nggak baik buat sort dan filter
	// $a_kolom[] = array('kolom' => 'p.nama', 'label' => 'Ketua');
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	

	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();

	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	require_once($conf['view_dir'].'inc_list.php');
?>
