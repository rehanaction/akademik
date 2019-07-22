<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporanmhs'));
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_angkatan = (int)$_REQUEST['angkatan'];
	$r_format = $_REQUEST['format'];
	
	if(Akademik::isMhs())
		$r_npm = Modul::getUserName();
	else
		$r_npm = CStr::removeSpecial($_REQUEST['nim']); 
		$r_nmr = CStr::removeSpecial($_REQUEST['nmrsuket']); 
		$r_isi = CStr::removeSpecial($_REQUEST['isisuket']);
	
	$r_periode = Akademik::getPeriode();
	
	// properti halaman
	$p_title = 'Cetak Surat Keterangan';
	$p_tbwidth = 720;
	
	if(empty($r_npm)) {
		$p_namafile = 'Transkrip'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getTranskripSementaraUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'Surat Keterangan_'.$r_npm;
		$a_data = mLaporanMhs::getResumeSementara($conn,$r_periode,$r_npm);
	}
	
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
		.div_head { font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px }
		
		.tb_cont td { padding: 0; vertical-align: top }
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; font-size: 8px; padding: 1px }
		.tb_data th { background-color: #FFF }
		.tb_data .mark { font-family: "Arial Narrow","Arial" }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-weight: normal }
	</style>
</head>
<body>
<div align="center">

<?php
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
     
	//include('inc_headerlap.php');
?>

<!--<div div_head"></div>-->
<br/><br/>
<table width="720"> 
	<tr>
    <td align="center"><font face="verdana"><strong>SURAT KETERANGAN</strong></td>
	</tr>
	<tr height="0">
    <td align="center"><font face="verdana">Nomor : <?=$r_nmr; ?></font></td> <br>
	</tr>
  <tr valign="top">
    <td align="center"></td>
  </tr>
  <tr height="40">
    <td align="center"></td>
  </tr>
  <tr>
    <td align="left">Yang bertanda tangan di bawah ini menerangkan bahwa :</td>
  </tr>
</table><br>
<table width="720">
	<tr valign="top">
		<td width="120">N a m a</font></td>
		<td align="center" width="10">:</td>
		<td width="250"><?= strtoupper($row['nama']) ?></td>
  		</tr>
	<tr valign="top">
		<td>N I M</td>
		<td align="center">:</td>
		<td><?= $row['nim'] ?></td>
	</tr>
	<tr valign="top">
		<td>Tempat & Tanggal Lahir</td>
		<td align="center">:</td>
		<td><?= ucwords($row['tmplahir']). ", ". CStr::formatDateInd($row['tgllahir']) ?></td>
	</tr>
  	<tr>
		<td>Jurusan</td>
		<td align="center" width="5">:</td>
		<td><?= strtoupper($row['programpend']) ?> - <?= strtoupper($row['namaunit']) ?></td>
	</tr>
  	<tr>
		<td>Tahun Masuk</td>
		<td align="center" width="5">:</td>
		<td><?= strtoupper($row['tahunmasuk']) ?>  </td>
	</tr>
  	<tr>
		<td>SKS diambil</td>
		<td align="center" width="5">:</td>
		<td><?= strtoupper($row['skslulus']) ?>  </td>
	</tr>
  	<tr>
		<td>Indeks Prestasi (IPK)</td>
		<td align="center" width="5">:</td>
		<td><?= strtoupper($row['ipk']) ?>  </td>
	</tr>
	<tr valign="top">
		<td>Semester Terakhir</td>
		<td align="center">:</td>
		<td ><?= Akademik::getNamaPeriodeLong($row['semesterakhir'])?></td>
	</tr>
	<tr valign="top">
		<td>Alamat </td>
		<td align="center">:</td>
		<td style="padding-bottom:10px;"><?= strtoupper($row['alamat'])?></td>
	</tr>
	<tr valign="top">
		<td>Keterangan </td>
		<td align="center">:</td>
    	<td align="left"><?= $r_isi;?></td>
	</tr>
</table>
<div style="height:5px"></div>

<table width="720">          
  <tr valign="top">
		<td align="left">Demikian surat keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya. </td>
  </tr>
</table>

<div style="height:5px"></div>
<table width="<?= $p_tbwidth ?>">
	<tr height="55">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td width="450">&nbsp;</td>
		<td align="left" class="mark">Bandung, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td align="left" ></td>
	</tr>
	<tr height="55">
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
    	<td align="left">Yusnandi, S.Kom.</td>
	</tr>
	<tr>
		<td>&nbsp;</td>
    	<td align="left">Ka. Biro Administrasi Akademik</td>
	</tr>
</table>

<table width="720">
	<tr valign="top">
		<td width="120">Tembusan:<br/>1. Kepala Program Studi <?= ucwords($row['namaunit']) ?> <br/>2. Arsip</td>
  	</tr>
</table>
<table>
	<tr valign="top">
		<td style="width:920px">&nbsp;</td>
  	</tr>
</table>
<?php 
} 
?>
</div>
</body>
</html>
