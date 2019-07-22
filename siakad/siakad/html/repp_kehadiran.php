<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('kuliah'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	//$r_tglujian = Modul::setRequest($_POST['tglujian'],'TGLUJIAN');
	$r_periode=$r_tahun.$r_semester;
	$r_tglujian=date('Y-m-d');
		
	// properti halaman
	$p_title = 'Laporan Absensi Dosen dan Mahasiswa';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$a_input = array();
	$a_input[] = array('label' => 'Pengelola', 'input' => uCombo::unit($conn,$r_unit,'unit','',false));
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Basis', 'input' => uCombo::kelas($conn,$r_sistemkuliah,'sistemkuliah','',false));
	$a_input[] = array('label' => 'Jenis Kuliah','type' => 'S', 'option' => mKuliah::jenisKuliah($conn),'nameid' => 'jeniskuliah');
	$a_starttgl= array('kolom' => 'starttgl', 'type' => 'D');
	$a_input[] = array('label' => 'Tgl Awal Kuliah', 'input' => uForm::getInput($a_starttgl,$r_tglujian));
	$a_endtgl= array('kolom' => 'endtgl', 'type' => 'D');
	$a_input[] = array('label' => 'Tgl Akhir Kuliah', 'input' => uForm::getInput($a_endtgl,$r_tglujian));
	$a_input[] = array('kolom' => 'username', 'label' => 'Dosen/Mahasiswa', 'type' => 'X', 'text' => 't_username','param'=>'strpost:"f=acdosen"');
	
	$a_laporan = array();
	$a_laporan['rep_kehadirandosen'] = 'Absensi Dosen';
	$a_laporan['rep_kehadiranmhs'] = 'Absensi Mahasiswa';
	
	
	require_once($conf['view_dir'].'inc_repp.php');
?>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>
<script src="scripts/jquery-1.7.1.min.js" type="text/javascript" charset="utf-8"></script>	
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	$("#t_username").xautox({strpost: "f=acuser", targetid: "username"});
});

</script>
