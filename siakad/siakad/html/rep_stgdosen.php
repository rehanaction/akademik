<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
	
	// include
// 	require_once(Route::getModelPath('laporanmhs'));
// 	
// 	// variabel request
// 	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
// 	$r_angkatan = (int)$_REQUEST['angkatan'];
// 	$r_format = $_REQUEST['format'];
// 	
// 	if(Akademik::isMhs())
// 		$r_npm = Modul::getUserName();
// 	else
// 		$r_npm = CStr::removeSpecial($_REQUEST['npm']);
// 	
// 	$r_periode = Akademik::getPeriode();


//------------------------------------------------------------------

	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_nomor = CStr::removeSpecial($_REQUEST['nomor']);
	// $r_bulanbayar = CStr::removeSpecial($_REQUEST['bulanbayar']);
	// $r_tahunbayar = CStr::removeSpecial($_REQUEST['tahunbayar']);
	$r_nip = CStr::removeSpecial($_REQUEST['nipdosen']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	// $r_periode=$r_tahun.$r_semester;
	// $r_periodegaji=$r_tahunbayar.str_pad($r_bulanbayar,2,'0',STR_PAD_LEFT);
	
	if(Akademik::isDosen())
		$r_nip = Modul::getUserName();

// echo '<br/>$r_nip: '.$r_nip;
	require_once(Route::getModelPath('unit'));
// 	require_once(Route::getMOdelPath('laporan'));
// 	require_once(Route::getMOdelPath('sistemkuliah'));
 	require_once(Route::getMOdelPath('mengajar'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('periode'));
	
//	$namaunit=mUnit::getNamaUnit($conn,$r_kodeunit);
//	$fakultas=mUnit::getNamaParentUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Surat Tugas Dosen Mengajar';
	$p_tbwidth = 700;
	// $p_namafile = 'rekap_ajar'.$r_kodeunit;
	
	// $a_sistemkuliah=mSistemkuliah::getArray($conn);
	
	
	// $a_data = mLaporan::getRekapMengajar($conn,$r_kodeunit,$r_periode,$r_periodegaji,'','',$r_nip);
	 
// 	 $arr_data=array();
// 	
// 	 foreach($a_data as $row){
// 		
// 		$idx=$row['kodeunit'].'-'.$row['basis'];
// 		$arr_data[$idx]['namaunit']=$row['jurusan'];
// 		$arr_data[$idx]['basis']=$row['basis'];
// 		$arr_data[$idx]['nopengajuan']=$row['nopengajuan'];
// 		$arr_data[$idx]['nopembayaran']=$row['nopembayaran'];
// 		if($row['tugasmengajar']=='0')
// 			$arr_data[$idx]['gaji'][]=$row['honordosen'];
// 		else
// 			$arr_data[$idx]['gaji'][]=0;
// 	 }
// 	 $a_honor=array();
	 
// 	 foreach($arr_data as $idx=>$row){
// 		 $a_honor[$idx]=array(
// 					'namaunit'=>$row['namaunit'],
// 					'basis'=>$row['basis'],
// 					'nopengajuan'=>$row['nopengajuan'],
// 					'nopembayaran'=>$row['nopembayaran'],
// 					'honor'=>array_sum($row['gaji']));
// 	 }
// 	ksort($a_honor);
	
	//ambil data dari SD
	$arr_pegawai=current(mPegawai::getDataDosen($conn_sdm,$r_nip));
// $arr_pegawai=current($arr_pegawai);
// error_reporting(E_ALL);
// 
// echo '$arr_pegawai: ';
// print_r($arr_pegawai);

// echo '<br/><br/>$_REQUEST: ';
// print_r($_REQUEST);

	$periode=$r_tahun.$r_semester;
// echo '<br/><br/>$periode: '.$periode;
// echo '<br/><br/>kodeunit: '.$r_kodeunit; 

	$arr_periode=mPeriode::getDetailPeriode($conn,$periode, $r_kodeunit);
// 	$arr_pegawai=$arr_pegawai[$periode];

// echo '<br/><br/>$arr_periode: ';
// print_r($arr_periode);

	$mtkuliah=mMengajar::getSuratTugasMengajar($conn, $r_nip, $periode, $r_kodeunit);
	$currentrow=current($mtkuliah);
	$fakultas=$currentrow['fakultas'];
	$nipdekan=$currentrow['nipdekan'];
	$namadekan=$currentrow['namadekan'];
//  echo '<br/><br/>$mtkuliah: ';
//  print_r($mtkuliah);

	// header
	Page::setHeaderFormat($r_format,$p_namafile);

//-------------------------------------------------------------------

	// properti halaman
	$p_title = 'Surat Tugas Dosen Mengajar';
	$p_tbwidth = 720;
	
// 	if(empty($r_npm)) {
// 		$p_namafile = 'stgdosen_'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
// 		$a_data = mLaporanMhs::getTranskripUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
// 	}
// 	else {
// 		$p_namafile = 'stgdosen_'.$r_npm;
// 		$a_data = mLaporanMhs::getTranskrip($conn,$r_periode,$r_npm);
// 	}
	
	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head { font-family: "Times New Roman" }
		.tb_head td { font-size: 10px }
		.div_head { font-size: 14px; font-weight: bold; margin-bottom: 5px }
		
		.tb_cont td { padding: 0; vertical-align: top }
		.tb_data { border: 0px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border:0px solid black; font-family: "Times New Roman"; font-size: 11px; padding:1px }
		.td2 { border:1px solid black; font-family: "Times New Roman"; font-size: 11px; padding:1px }
		.tb_data th { background-color: #CFC }
		.tb_data .mark { font-family: "Arial Narrow","Arial" }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-weight: normal }
	
		.pad { padding-left: 100px }
	</style>
</head>
<body>
	
<div align="center">
<?php
include('inc_headerlap.php');
?>

<br>
<div class="div_head">SURAT TUGAS</div>
<div class="div_head">No. <?= $r_nomor; ?></div>
<br>
<table class="tb_data" width="720px">
	<tr height="14">
		<td colspan="3">Berdasarkan Surat Kesepakatan Mengajar Dosen (terlampir), maka DEKAN/Kepala <?= $fakultas; ?>, memberikan tugas kepada :</td>
	</tr>
	<tr height="14">
		<td style="width:120px">Nama</td><td>:</td><td><?= $arr_pegawai["namalengkap"] ?></td>
	</tr>
	<tr height="14">
		<td>No. Dosen / NIP </td><td>:</td><td><?= $arr_pegawai["nodosen"]." / ".$arr_pegawai["nip"] ?></td>
	</tr>
	<tr height="14">
		<td>NIDN / NIDK</td><td>:</td><td><?= $arr_pegawai["nidn"] ?></td>
	</tr>
	<tr height="14">
		<td>Jabatan Akademik</td><td>:</td><td><?= $arr_pegawai["jabatanfungsional"] ?></td>
	</tr>
	<tr height="14">
		<td>Alamat</td><td>:</td><td><?= $arr_pegawai["alamatktp"] ?></td>
	</tr>
	<tr height="14">
		<td colspan="3">Untuk mengajar pada <?= Akademik::getNamaPeriode($periode) ?> yang berlangsung dari tanggal <?= CStr::formatDateInd($arr_periode['tglawal'])?> sampai dengan tanggal <?= CStr::formatDateInd($arr_periode['tglakhir'])?> untuk mata kuliah:</td>
	</tr>
</table>
<br>
<table style='font-family:"Times New Roman";font-size:11px;border-collapse:collapse;'>
	<tr>
		<th style="border:1px solid black;width:30px;">No.</th>
		<th style="border:1px solid black;width:350px">MATA KULIAH</th>
		<th style="border:1px solid black;width:70px">SKS</th>
		<th style="border:1px solid black;width:70px">SEKSI</th>
		<th style="border:1px solid black;width:150px">BASIS</th>
	</tr>
<?php
$i=1;
foreach ($mtkuliah as $mt)
{
?>
	<tr>
		<td style="border:1px solid black;text-align:right;padding-right:10px"><?= $i; ?></td>
		<td style="border:1px solid black;text-align:left;padding-left:10px"><?= $mt["matakuliah"]; ?></td>
		<td style="border:1px solid black;text-align:center;"><?= $mt["sks"]; ?></td>
		<td style="border:1px solid black;text-align:center;"><?= $mt["seksi"]; ?></td>
		<td style="border:1px solid black;text-align:left;padding-left:10px"><?= $mt["basis"]; ?></td>
	</tr>
<?php
$i++;
}
?>
</table>
<br>
<table class="tb_data" width="720px">
	<tr height="14">
		<td colspan="2">Dengan Hak dan Tanggung Jawab sebagai berikut :</td>
	</tr>
	<tr height="14">
		<td valign="top">1.</td><td align="left">Menyusun Rencana Pembelajaran Semester (RPS) yang ditetapkan oleh Fakultas/Program Studi sebelum perkuliahan dimulai dalam bentuk yang telah ditetapkan oleh Universitas;<br/></td>
	</tr>
	<tr height="14">
		<td valign="top">2.</td><td align="left">Membuat bahan kuliah/praktikum berbasis konsep untuk 14 kali pertemuan dan di upload pada web pembelajaran Universitas Esa Unggul yaitu untuk bahan presentasi dan modul kuliah (teori) di http://ddp.esaunggul.ac.id/ dan modul kuliah praktikum di Repository Perpustakaan Universitas Esa Unggul http://digilib.esaunggul.ac.id/;<br/></td>
	</tr>
	<tr height="14">
		<td valign="top">3.</td><td align="left">Menghadiri dan memberikan bahan kuliah/praktek pada kegiatan perkuliahan/praktikum tepat waktu sesuai dengan jadwal yang telah ditetapkan;<br/></td>
	</tr>
	<tr height="14">
		<td valign="top">4.</td><td align="left">Memotivasi mahasiswa untuk mempelajari bahan kuliah yang diberikan dan bahan lain dari perpustakaan;<br/></td>
	</tr>
	<tr height="14">
		<td valign="top">5.</td><td align="left">Melakukan evaluasi keberhasilan mahasiswa secara objektif melalui pemberian tugas-tugas sesuai dengan kebutuhan mata kuliah yang diajarkan dan ujian tengah/akhir semester;<br/></td>
	</tr>
	<tr height="14">
		<td valign="top">6.</td><td align="left">Menerima honor sesuai dengan ketentuan yang berlaku di Yayasan Pendidikan Kemala Bangsa/Universitas Esa Unggul berdasarkan persyaratan dan kualifikasi yang dimiliki;<br/></td>
	</tr>
	<tr height="14">
		<td colspan="2">Demikian Surat Tugas ini diberikan untuk dilaksanakan sebaik-baiknya.</td>
	</tr>
</table>
<br>
<?php
$date = new DateTime($arr_periode['tglawal']);
$date->modify('-4 day');
?>
<table class="tb_foot" width="720px">
	<tr>
		<td align="left" class="mark">Jakarta, <?= CStr::formatDateInd($date->format('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td align="left">Dekan/Kepala <?= $fakultas?> </td>
	</tr>
	<tr height="35">
		<td>&nbsp;</td><td>&nbsp;</td>
	</tr>
	<tr>
		<td align="left"><u><?= $namadekan ?></u></td>
	</tr>
	<tr>
		<td align="left" class="mark">NIP. <?= $nipdekan ?></td>
	</tr>
</table>

</body>
</html>
