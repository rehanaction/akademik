<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('honorpenguji'));
	
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_jenispenguji = Modul::setRequest($_POST['jenispenguji'],'JENISPENGUJI');
	$r_bulanbayar = Modul::setRequest($_POST['bulanbayar'],'BULANBAYAR');
	$r_tahunbayar = Modul::setRequest($_POST['tahunbayar'],'TAHUNBAYAR');
	
	$r_periode=$r_tahun.$r_semester;
	$r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	// properti halaman
	$p_title = 'Laporan Detail Honor Dosen Penguji';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit,'add'=>'onchange="goSubmit()"');
	$a_input[] = array('label' => 'jenis Penguji', 'nameid' => 'jenispenguji', 'type' => 'S','option'=>mHonorPenguji::getJenisSidang(),'add'=>'onchange="goSubmit()"','default'=>$r_jenispenguji);
	$a_input[] = array('label' => 'Periode Pembayaran', 'input' => uCombo::bulan($r_bulanbayar,'bulanbayar','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahunbayar,true,'tahunbayar','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Nomor Pengajuan', 'nameid' => 'nopengajuan', 'type' => 'S', 'option' => mHonorPenguji::listNopengajuan($conn,$r_periode,$r_unit,$r_periodegaji,false,$r_jenispenguji));
	$a_input[] = array('nameid' => 'nipdosen', 'label' => 'Dosen Pengajar', 'type' => 'X', 'text' => 'nip','param'=>'acdosen');
	
	
	
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
	$("#nip").xautox({strpost: "f=acdosen", targetid: "nipdosen"});
});

</script>
