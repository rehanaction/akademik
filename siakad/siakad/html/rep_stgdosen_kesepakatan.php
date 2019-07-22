<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
	
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_nomor = CStr::removeSpecial($_REQUEST['nomor']);
	// $r_bulanbayar = CStr::removeSpecial($_REQUEST['bulanbayar']);
	// $r_tahunbayar = CStr::removeSpecial($_REQUEST['tahunbayar']);
	$r_nip = CStr::removeSpecial($_REQUEST['nipdosen']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	if(Akademik::isDosen())
		$r_nip = Modul::getUserName();

	require_once(Route::getModelPath('unit'));
 	require_once(Route::getMOdelPath('mengajar'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('periode'));
	
	// properti halaman
	$p_title = 'Surat Tugas Dosen Mengajar';
	$p_tbwidth = 700;
	
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
	$dsnTariff=mPegawai::getTarifDosen($conn_sdm,$r_nip);
 	$tariff = array("Regular"=>0, "Sabtu/Malam"=>0, "Minggu"=>0, "Online"=>0);
	foreach ( $dsnTariff as $row ){
			if ( $row["namajnsrate"]=="Regular" ){
				$tariff["Regular"]=$row["nominal"];
			}
			if ( $row["namajnsrate"]=="Sabtu/Malam" ){
				$tariff["Sabtu/Malam"]=$row["nominal"];
			}
			if ( $row["namajnsrate"]=="Minggu" ){
				$tariff["Minggu"]=$row["nominal"];
			}
			if ( $row["namajnsrate"]=="Online" ){
				$tariff["Online"]=$row["nominal"];
			}	
	}

//  echo '<br/><br/>$mtkuliah: ';
//  print_r($mtkuliah);

	// header
	Page::setHeaderFormat($r_format,$p_namafile);

	// properti halaman
	$p_title = 'SURAT KESEDIAAN DOSEN MENGAJAR';
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
<div class="div_head">SURAT KESEPAKATAN MENGAJAR DOSEN TIDAK TETAP</div>
<div class="div_head"><?= Akademik::getNamaPeriode($periode); ?></div>
<br>
<table class="tb_data" width="720px">
	<tr height="14">
		<td colspan="3">Yang bertandatangan dibawah ini :</td>
	</tr>
	<tr height="14">
		<td style="width:120px">Nama</td><td>:</td><td><?= $namadekan; ?></td>
	</tr>
	<tr height="14">
		<td>Jabatan</td><td>:</td><td>Dekan/Kepala</td>
	</tr>
	<tr height="14">
		<td>Fakultas/Unit</td><td>:</td><td><?= $fakultas; ?></td>
	</tr>
	<tr height="14">
		<td colspan="3">Dalam hal ini bertindak mewakili Universitas Esa Unggul dan selanjutnya disebut sebagai PIHAK PERTAMA<br/><br/></td>
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
		<td colspan="3">Dalam hal ini bertindak mewakili dirinya sendiri dan selanjutnya disebut sebagai PIHAK KEDUA</td>
	</tr>
</table>
<br>
<table class="tb_data" width="720px">
	<tr height="14">
		<td colspan="2">PIHAK PERTAMA dan PIHAK KEDUA melakukan kesepakatan, sebagai berikut:</td>
	</tr>
	<tr height="14">
		<td valign="top">1.</td><td align="left">PIHAK KEDUA sepakat untuk mengajar pada <?= Akademik::getNamaPeriode($periode); ?> (periode tanggal <?= CStr::formatDateInd($arr_periode['tglawal'])?> sampai dengan <?= CStr::formatDateInd($arr_periode['tglakhir'])?>) di <?= $fakultas; ?> untuk mata kuliah berikut :
		<br/>
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
				<td colspan="2">Dengan ketentuan:</td>
			</tr>
			<tr height="14">
				<td valign="top">1.1</td><td align="left">Menyusun Rencana Pembelajaran Semester (RPS) yang ditetapkan oleh Fakultas/ Jurusan sebelum perkuliahan dimulai dalam bentuk yang telah ditetapkan oleh Universitas;<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">1.2</td><td align="left">Membuat bahan kuliah/praktikum berbasis konsep untuk 14 kali pertemuan dan di upload pada web pembelajaran Universitas Esa Unggul yaitu untuk bahan presentasi dan modul kuliah (teori) di http://ddp.esaunggul.ac.id/ dan modul kuliah praktikum di Repository Perpustakaan Universitas Esa Unggul http://digilib.esaunggul.ac.id/;<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">1.3</td><td align="left">Membuat materi e-learning berupa : Modul, PPT, Audio Video, weblink, forum, quiz (multiple choice), tugas, dan memberikan feedback kepada mahasiswa dalam bentuk nilai dari tugas;*)<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">1.4</td><td align="left">Menghadiri kegiatan perkuliahan/praktikum tepat waktu sesuai dengan jadwal yang telah ditetapkan;<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">1.5</td><td align="left">Memotivasi mahasiswa untuk mempelajari bahan kuliah dan bahan-bahan lain yang mendukung;<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">1.6</td><td align="left">Melakukan evaluasi keberhasilan mahasiswa secara objektif sesuai dengan peraturan yang telah dikomunikasikan dan disepakati bersama mahasiswa pada awal semester;<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">1.7</td><td align="left">Memberikan hasil evaluasi keberhasilan (nilai Mata Kuliah) mahasiswa paling lambat 2 minggu setelah ujian akhir semester berlangsung.<br/><br/></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height="14">
		<td valign="top">2.</td><td align="left">PIHAK PERTAMA sepakat untuk memberikan kompensasi berupa:
		<br/>
		<table class="tb_data" width="720px">
			<tr height="14">
				<td valign="top">2.1</td><td align="left">Honor mengajar per sks sebagai berikut :
				<br/><br/>
				<table class="tb_data" width="400px">
					<tr height="14">
						<td align="center" style="border:1px solid black;">Kelas Reguler</td>
						<td align="center" style="border:1px solid black;">Kelas Malam/Sabtu</td>
						<td align="center" style="border:1px solid black;">Kelas Minggu</td>
						<?php /* td align="center" style="border:1px solid black;">Kelas Online</td */ ?>
					</tr>
					<tr height="14">
						<td style="border:1px solid black;text-align:right;padding-right:10px;"><?= number_format($tariff["Regular"]); ?>,-</td>
						<td style="border:1px solid black;text-align:right;padding-right:10px;"><?= number_format($tariff["Sabtu/Malam"]); ?>,-</td>
						<td style="border:1px solid black;text-align:right;padding-right:10px;"><?= number_format($tariff["Minggu"]); ?>,-</td>
						<?php /* td style="border:1px solid black;text-align:right;padding-right:10px;"><?= number_format($tariff["Online"]); ?>,-</td */ ?>
					</tr>
				</table>
				<br/>
				</td>
			</tr>
			<tr height="14">
				<td valign="top">2.2</td><td align="left">Honor membuat soal ujian Rp. 30.000 per ujian;<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">2.3</td><td align="left">Honor memeriksa hasil UTS/UAS :<br/>- Reguler Rp. 3.500/mahasiswa<br/>- Pararel Rp. 4.500/mahasiswa<br/></td>
			</tr>
			<tr height="14">
				<td valign="top">2.4</td><td align="left">Honor membuat RPS, bahan ajar baru (bukan modifikasi sebagian) berupa Bahan Presentasi, Modul, Buku Ajar dsb. untuk perkuliahan selama 1 semester, berdasarkan pengajuan dari Lembaga Pengembangan Pembelajaran (LPP).<br/></td>
			</tr>
		</table>
		</td>
	</tr>
	<tr height="14">
		<td colspan="2"><br/><br/>Demikian surat kesediaan mengajar ini dibuat tanpa paksaan dari pihak manapun.</td>
	</tr>
</table>
<br>
<?php
$date = new DateTime($arr_periode['tglawal']);
$date->modify('-4 day');
?>
<table class="tb_foot" width="720px">
	<tr>
		<td colspan="3" align="left" class="mark">Jakarta, <?= CStr::formatDateInd($date->format('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td align="left">PIHAK PERTAMA,</td><td style="width:75px"></td><td>PIHAK KEDUA,</td>
	</tr>
	<tr height="70">
		<td>&nbsp;</td><td>&nbsp;</td><td align="left">Materai 6000,-</td>
	</tr>
	<tr>
		<td align="left"><u><?= $namadekan ?></u></td><td></td><td><u><?= $arr_pegawai["namalengkap"] ?></u></td>
	</tr>
	<tr>
		<td align="left" class="mark">NIP. <?= $nipdekan ?></td><td></td><td class="mark"><?= $arr_pegawai["nodosen"]." / ".$arr_pegawai["nip"] ?></td>
	</tr>
</table>
<br/>
<table class="tb_foot" width="720px">
	<tr>
		<td align="left" class="mark">*) Khusus untuk perkuliahan online</td>
	</tr>
</table>

</body>
</html>
