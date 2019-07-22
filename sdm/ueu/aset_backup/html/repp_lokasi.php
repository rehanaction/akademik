<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_cabang = Modul::setRequest($_POST['cabang'],'CABANG');
	$r_gedung = Modul::setRequest($_POST['gedung'],'GEDUNG');
	$r_jenisruang = Modul::setRequest($_POST['jenisruang'],'JENISRUANG');
	
	// properti halaman
	$p_title = 'Laporan Daftar Lokasi';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$i_check = '<br><input type="checkbox" name="showchild" id="showchild" value="1" checked="checked" class="ControlStyle" />';
	$i_check .= '&nbsp;&nbsp;<i>Beserta unit didalamnya</i>';

	$i_gedung = uCombo::combo(array(''=>'-- Pilih cabang dahulu --'),$r_gedung,'gedung','style="width:250px"',false,'');
	$i_jenis = uCombo::combo(mCombo::jenislokasi($conn),$r_jenisruang,'jenisruang','style="width:250px"',true,'jenis ruang');

	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:300px"',false).$i_check);
	$a_input[] = array('label' => 'Cabang', 'input' => uCombo::cabang($conn,$r_cabang,'cabang','style="width:250px"',true));
	$a_input[] = array('label' => 'Gedung', 'input' => $i_gedung);
	$a_input[] = array('label' => 'Jenis Ruang', 'input' => $i_jenis);

	require_once($conf['view_dir'].'inc_repp.php');
?>

