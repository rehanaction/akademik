<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// properti halaman
	$p_title = 'Laporan Rekap Kondisi Barang';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$i_check = '<br><input type="checkbox" name="showchild" id="showchild" value="1" checked="checked" class="ControlStyle" />';
	$i_check .= '&nbsp;&nbsp;<i>Beserta unit didalamnya</i>';

    $i_cabang = UI::createSelect('cabang',mCombo::cabang($conn),$r_cabang,'ControlStyle',true,'style="width:250px"',true,'-- Semua cabang --');
	$i_gedung = uCombo::combo(array(''=>'-- Pilih cabang dahulu --'),$r_gedung,'gedung','style="width:250px"',false,'');
	//$i_lantai = uCombo::combo(mCombo::lantai(),$r_lantai,'lantai','style="width:250px"',true,'lantai');
    $i_lantai = UI::createSelect('lantai',mCombo::lantai(),$r_lantai,'ControlStyle',true,'style="width:250px"',true,'-- Semua lantai --');

	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:350px"',false).$i_check);
	$a_input[] = array('label' => 'Cabang', 'input' => $i_cabang);
	$a_input[] = array('label' => 'Gedung', 'input' => $i_gedung);
	$a_input[] = array('label' => 'Lantai', 'input' => $i_lantai);
	$a_input[] = array('label' => 'Lokasi/Ruang', 'input' => uCombo::lokasi($conn,$r_lokasi,'lokasi','style="width:350px"',true));

	require_once($conf['view_dir'].'inc_repp.php');
?>
