<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_coa = Modul::setRequest($_POST['coa'],'COA');
	
	$year = date("Y"); 
	$mon = date("m"); 

	if(empty($r_tahun)) $r_tahun = $year;
	if(empty($r_bulan)) $r_bulan = $mon;
	
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','style="width:75px"',false);
	$l_bulan = uCombo::bulan($conn,$r_bulan,'bulan','style="width:90px"',false);
	
	// properti halaman
	$p_title = 'Laporan Penyusutan Aset';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$i_check = '<br><input type="checkbox" name="showchild" id="showchild" value="1" checked="checked" class="ControlStyle" />';
	$i_check .= '&nbsp;&nbsp;<i>Beserta unit didalamnya</i>';
	
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:350px"',false).$i_check);
	$a_input[] = array('label' => 'Periode' , 'input' => $l_bulan.'&nbsp;&nbsp;&nbsp;'.$l_tahun);
	$a_input[] = array('label' => 'Kode COA', 'input' => uCombo::coa($conn,$r_coa,'coa','style="width:300px"'));

	require_once($conf['view_dir'].'inc_repp.php');
?>
