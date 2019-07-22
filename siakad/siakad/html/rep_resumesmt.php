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
		$r_npm = CStr::removeSpecial($_REQUEST['npm']);
	
	$r_periode = Akademik::getPeriode();
	
	// properti halaman
	$p_title = 'Kemajuan Belajar/Daftar Prestasi';
	$p_tbwidth = 720;
	
	if(empty($r_npm)) {
		$p_namafile = 'transkrip_sementara_'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getTranskripSementaraUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'transkrip_sementara_'.$r_npm;
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
		.div_preheader, .div_header { font-family: "Arial Narrow" }
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head { font-family: "Arial Narrow" }
		.tb_head td { font-size: 10px }
		.div_head { font-size: 14px; font-weight: bold; margin-bottom: 5px }
		
		.tb_cont td { padding: 0; vertical-align: top }
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Arial Narrow"; font-size: 8px; padding: 1px }
		.tb_data th { background-color: #FFF }
		.tb_data .mark { font-family: "Arial Narrow","Arial" }
		
		.tb_foot { font-family: "Arial Narrow"; font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot2 { font-family: "Arial Narrow"; font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-weight: normal }
	</style>
</head>
<body>
<div align="center">
<?php 
	$tsks = 0;
	$tbobot = 0;
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];
    //$mutu = $row['sks'] * $row['nangka'];
    //$tmutu += $mutu ;
		//$tsks += $row['sks'];
    //$ipk = round($tmutu/$tsks,2);
    
		include('inc_headerlap.php');
?>
<div class="div_head">RESUME NILAI</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr valign="top">
		<td width="80"><strong>N a m a</strong></td>
		<td align="center" width="10">:</td>
		<td width="250"><strong><?= STRTOUPPER($row['nama']) ?></strong></td>
		<td width="80"><strong>Program Studi</td>
		<td align="center" width="10">:</td>
		<td><?= STRTOUPPER($row['namaunit']) ?> <!--(<?= $row['kodeunit'] ?>)</td>-->
	</tr>
	<tr valign="top">
		<td><strong>N I M</strong></td>
		<td align="center">:</td>
		<td><?= $row['nim'] ?></td>
		<td><strong>Jenjang</strong></td>
		<td align="center">:</td>
		<td><?= $row['programpend'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Tmp, Tgl Lahir</strong></td>
		<td align="center">:</td>
		<td colspan="4"><?= $row['tmplahir'] ?>, <?= CStr::formatDateInd($row['tgllahir']) ?></td>
	</tr>
</table>
<div style="height:5px"></div>
<table class="tb_cont" width="<?= $p_tbwidth ?>">
	<tr>
<?php
	$j = 0;
	for($s=0;$s<2;$s++) {
?>
		<td width="49%">
<table class="tb_data" width="100%">
	<tr>
		<th width="20">No</th> 
		<th width="30">Kode</th>
		<th>Nama Mata Kuliah</th>
		<th width="25">Nilai</th>
		<th width="25">SKS</th>
		<th width="30">Nk</th> 
		<th width="25">SEM</th>
	</tr>
  
<?php
		$n = count($row['transkrip'][$s]);
		for($i=0;$i<$n;$i++) { 
        
			$rowt = $row['transkrip'][$s][$i];
			
			if(is_array($rowt)) 
{
?>
  
	<tr height="14">
		<td align="center"><?= ++$j ?></td>
		<td align="center"><?= $rowt['kodemk'] ?></td>
		<td class="mark"><?= $rowt['namamk'] ?></td>
		<td align="center"><?= $rowt['nhuruf'] ?></td>
		<td align="center"><?= $rowt['sks'] ?></td>
		<td align="center"><?= $rowt['sks']*$rowt['nangka'] ?></td> 
		<td align="center"><?= $rowt['periode'] ?></td>
	
  
  <?php 
  $tsks += $rowt['sks'];
  $tmutu += $rowt['sks']*$rowt['nangka'] ; 
  $ipk = round($tmutu/$tsks,2); 
  ?>
  
  </tr>
  
<?php
			}
			else {
?>



	<!--<tr height="14">-->
	<!--	<th align="center" colspan="6"><?= $rowt ?></td>-->
	<!--</tr>-->
  
<?php
			}
		}
?>
</table>
		</td>
<?php
		if($s == 0) {
?>
		<td width="2%">&nbsp;</td>
<?php
		}
	}
?>
	</tr>
</table>
<table class="tb_foot2" width="<?= $p_tbwidth ?>"> 
  <tr>
   <td align="left" width="70">Total SKS</td>
   <td width="10">:</td>
   <td><?= $tsks ?></td>  
  <tr>
   <td align="left">IPK</td>
   <td>:</td>
   <td><?= $ipk ?></td>
  </tr>

  </table>
<div style="height:5px"></div>


  <table class="tb_foot" width="<?= $p_tbwidth ?>"> 
  <tr>
  	<td>&nbsp;</td>
  	<td>&nbsp;</td>
  </tr>
	<tr>
		<td width="450">&nbsp;</td>
		<td class="mark">Bandung, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>Ketua Program Studi <?= $row['namaunit'] ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td></td>
	</tr>
	<tr height="45">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td><?= $row['pdakad'] ?></td>
		<td><?= $row['ketua'] ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>NIDN. <?= $row['nidnketua'] ?></td>
	</tr>

</table>
<?php
	}
?>
</div>
</body>
</html>
