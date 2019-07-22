<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('honortransportpenguji'));
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
	$p_title = 'Laporan Detail Honor Transport Dosen Penguji';
	$p_tbwidth = 500;
	$p_aktivitas = 'LAPORAN';
	

	$a_input = array();
	
	$a_input[] = array('label' => 'Periode', 'input' => uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Prodi', 'nameid' => 'unit', 'type' => 'S', 'option' => mCombo::unit($conn,false), 'default' => $r_unit,'add'=>'onchange="goSubmit()"');
	$a_input[] = array('label' => 'jenis Penguji', 'nameid' => 'jenispenguji', 'type' => 'S','option'=>mHonorPenguji::getJenisSidang(),'add'=>'onchange="goSubmit()"','default'=>$r_jenispenguji);
	$a_input[] = array('label' => 'Periode Pembayaran', 'input' => uCombo::bulan($r_bulanbayar,'bulanbayar','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahunbayar,true,'tahunbayar','onchange="goSubmit()"',false));
	$a_input[] = array('label' => 'Nomor Pengajuan', 'nameid' => 'nopengajuan', 'type' => 'S', 'option' => mHonorTransportPenguji::listNopengajuan($conn,$r_periode,$r_unit,$r_periodegaji,false,$r_jenispenguji));
	$a_input[] = array('label' => 'Basis', 'input' => uCombo::kelas($conn,$r_sistemkuliah,'sistemkuliah','',false));
	$a_input[] = array('nameid' => 'nipdosen', 'label' => 'Dosen Pengajar', 'type' => 'X', 'text' => 'nip','param'=>'acdosen','notnull'=>true);
	
	
	require_once($conf['view_dir'].'inc_repp.php');
?>

<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";



</script>
