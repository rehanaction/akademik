<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('matakuliah'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_fakultas = Modul::setRequest($_POST['fakultas'],'FAKULTAS');
	
	// properti halaman
	$p_title = 'Mencetak Surat Keterangan';
	$p_tbwidth = 800;
	$p_aktivitas = 'LAPORAN';
	

	$option = array('1' => 'Surat Keterangan', '2' => 'Surat Pengunduran Diri');
	$a_input = array();
	//$a_input[] = array('label' => 'Prodi', 'nameid' => 'jurusan', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit);
	//$a_input[] = array('label' => 'Periode Semester', 'input' => uCombo::semester($r_semester,true,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Jenis Surat', 'nameid' => 'jnssurat', 'type' => 'S', 'option' =>$option, 'add'=>' onchange="loadBProdiFakultas()"' );
	$a_input[] = array('label' => 'Mahasiswa', 'nameid' => 'nim', 'type' => 'X', 'text' =>'mahasiswa','param'=>'acmahasiswa','add'=>' size="30" onchange="loadBProdiFakultas()"');
	$a_input[] = array('label' => 'Nomor', 'nameid' => 'nmrsuket', 'type' => 'A', 'cols' => 60, 'rows' => 1, 'default' => '/STIEINABA/BAA/KET/'.date('m').'/'.date('Y'));	
	$a_input[] = array('label' => 'Isi', 'nameid' => 'isisuket', 'type' => 'A', 'cols' => 60, 'rows' => 10 );
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
