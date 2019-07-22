<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulanbayar = Modul::setRequest($_POST['bulanbayar'],'BULANBAYAR');
	$r_tahunbayar = Modul::setRequest($_POST['tahunbayar'],'TAHUN');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	$r_periode=$r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Laporan Slip Honor Dosen Pembimbing Akademik';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	//$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit);
	$a_input[] = array('label' => 'Periode Pembayaran', 'input' => uCombo::bulan($r_bulanbayar,'bulanbayar','',false).' '.uCombo::tahun($r_tahunbayar,true,'tahunbayar','',false));
	if(!Akademik::isDosen())
	$a_input[] = array('kolom' => 'nipasisten', 'label' => 'Asisten', 'type' => 'X', 'text' => 'asisten','notnull'=>true);
	
	$a_laporan = array();
	$a_laporan['rep_rekapsliphonorasisten'] = 'Rekap';
	$a_laporan['rep_sliphonorasisten'] = 'Detail';
	
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
	$("#asisten").xautox({strpost: "f=acpegawaipenunjang", targetid: "nipasisten"});
});
//@overide
function goReport(page) {
	var form = document.getElementById("pageform");
	
	if(typeof(page) == "undefined")
		form.action = reportpage;
	else
		form.action = getPage(page);
	
	form.target = "_blank";
	if($("#nip").val()=='')
		alert('Masukkan Asisten');
	else
		goSubmit();
	
	form.action = "";
	form.target = "";
}
</script>
