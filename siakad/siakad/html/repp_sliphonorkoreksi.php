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
	$p_title = 'Laporan Slip Honor Koreksi Hasil Ujian';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','',false).' '.uCombo::tahun($r_tahun,true,'tahun','',false));
	//$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit);
	$a_input[] = array('label' => 'Periode Pembayaran', 'input' => uCombo::bulan($r_bulanbayar,'bulanbayar','',false).' '.uCombo::tahun($r_tahunbayar,true,'tahunbayar','',false));
	if(!Akademik::isDosen())
	$a_input[] = array('nameid' => 'nipdosen', 'label' => 'Dosen Pengajar', 'type' => 'X', 'text' => 'nip','param'=>'acdosen','notnull'=>true);
	
	$a_laporan = array();
	$a_laporan['rep_rekapsliphonorkoreksi'] = 'Rekap';
	$a_laporan['rep_sliphonorkoreksi'] = 'Detail';
	
	require_once($conf['view_dir'].'inc_repp.php');
?>

<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";


//@overide
function goReport(page) {
	var form = document.getElementById("pageform");
	
	if(typeof(page) == "undefined")
		form.action = reportpage;
	else
		form.action = getPage(page);
	
	form.target = "_blank";
	if($("#nip").val()=='')
		alert('Masukkan Dosen Pengajar');
	else
		goSubmit();
	
	form.action = "";
	form.target = "";
}
</script>
