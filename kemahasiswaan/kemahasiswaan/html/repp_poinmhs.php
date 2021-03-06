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
	$p_title = 'Cetak Rekap Poin Mahasiswa';
	$p_tbwidth = 550;
	$p_aktivitas = 'LAPORAN';

	$a_input = array();
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	$a_input[] = array('label' => 'Unit', 'nameid' => 'jurusan', 'type' => 'S', 'option' => mCombo::unit($conn), 'default' => $r_unit);
	$a_input[] = array('label' => 'Mahasiswa', 'nameid' => 'nim', 'type'=>'X');

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
	// autocomplete
	$("#nimlabel").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
});

</script>
