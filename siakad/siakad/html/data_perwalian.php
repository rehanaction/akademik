<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Status Semester Mahasiswa';
	$p_tbwidth = 600;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPerwalian;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	$c_readlist = empty($a_authlist) ? false : true;
	
	list($r_npm) = explode('|',$r_key);
	
	if(empty($r_key))
		Route::navigate('list_mahasiswa');
	else if($c_readlist)
		$p_listpage .= ('&npm='.$r_npm);
	//print_r($p_model::infoMhs($conn,$r_npm));die();
	// struktur view
	$a_mhs = array($r_npm => Akademik::getNamaMahasiswa($conn,$r_npm).' ('.$r_npm.')'); // optimasi :D
	
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'Mahasiswa', 'type' => 'S', 'option' => $a_mhs, 'readonly' => true);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'S', 'option' => mCombo::periode($conn,false), 'readonly' => true);
	$a_input[] = array('kolom' => 'statusmhs', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::statusMhs($conn));
	$a_input[] = array('kolom' => 'alasancuti', 'label' => 'Alasan', 'maxlength' => 50, 'size' => 50);
	$a_input[] = array('kolom' => 'tglsk', 'label' => 'Tanggal SK', 'type' => 'D');
	$a_input[] = array('kolom' => 'nosk', 'label' => 'Nomor SK', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'maxlength' => 50, 'size' => 50);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$infoProgpend=$p_model::infoProgpend($conn,$r_npm);
		$infoMhs=$p_model::infoMhs($conn,$r_npm);
		if($record['statusmhs']=='C' and $infoMhs['jum_cuti']>=$infoProgpend['maxcuti']){
			$p_posterr=true;
			$p_postmsg="Maaf Batas Maximal Cuti ".$infoProgpend['maxcuti']." Kali";
		}else{
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			if(!$p_posterr) unset($post);
		}
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
