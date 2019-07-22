<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_lokasi = Modul::setRequest($_POST['lokasi'],'LOKASI');

	// properti halaman
	$p_title = 'Laporan Rekap Daftar Barang';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$i_check = '<br><input type="checkbox" name="showchild" id="showchild" value="1" checked="checked" class="ControlStyle" />';
	$i_check .= '&nbsp;&nbsp;<i>Beserta unit didalamnya</i>';
	
	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:300px"',false).$i_check);
	$a_input[] = array('label' => 'Lokasi/Ruang', 'input' => uCombo::lokasi($conn,$r_lokasi,'lokasi','style="width:300px"',true));
	//$a_input[] = array('label' => 'Kondisi', 'input' => uCombo::kondisi($conn,$r_kondisi,'kondisi','',false));

	require_once($conf['view_dir'].'inc_repp.php');
?>
