<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
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
		$p_namafile = 'transkrip_'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getTranskripUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'transkrip_'.$r_npm;
		$a_data = mLaporanMhs::getTranskripLulus($conn,$r_periode,$r_npm);
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
		.tab_header { border-bottom: 0px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head { font-family: "Times New Roman" }
		.tb_head td { font-size: 10px }
		.div_head { font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px }
		
		.tb_cont td { padding: 0; vertical-align: top }
		.tb_data { border: 0px solid black; border-collapse: collapse }  
		
		.tb_data th, .tb_data td { border: 0px solid black; font-family: "Times New Roman"; font-size: 9px; padding: 1px }
		.tb_data th { background-color: #FFF }
		.tb_data .mark { font-family: "Arial Narrow","Arial" }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-weight: normal }
	
		.pad { padding-left: 100px }
	</style>
</head>
<body>
	
<div align="center">
<?php
	$m = count($a_data);
	for($c=0;$c<$m;$c++) {
		$row = $a_data[$c];

    //include('inc_headerlap.php'); /*header berlogo, di-off karena dicetak di kertas pre-printed*/
?>


<?php
		if(!empty($row['keterangan'])) {
?>
<div class="div_headertext"><?= strtoupper($row['keterangan']) ?></div>
<?php
		}
?>
<br> <br> <br>
<div class="div_head">TRANSKRIP HASIL STUDI</div>
<div class="div_subhead">(Lampiran Ijazah Program <?= $row['namaprogram'] ?> / <?= $row['programpend'][0].'-'.$row['programpend'][1] ?>)</div>
<br>
<table class="tb_cont" width="<?= $p_tbwidth ?>">
	<tr>
  
<?php
		$j = 0;                  /*j=nomor urut*/
		for($s=0;$s<2;$s++) {    /**/
?>
		<td width="49%">
<table class="tb_head" width="100%">
<?			if($s == 0) { ?>
	<tr valign="top">
		<td width="35"><strong>N I M</strong></td>
		<td align="center" width="10">:</td>
		<td><?= $row['nim'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Nama</strong></td>
		<td align="center">:</td>
		<td><strong><?= $row['nama'] ?></strong></td>
	</tr>
<?			} else { ?>
	<tr valign="top">
		<!--<td width="95"><strong>No Ijazah Institut</td>--> 
		<td width="95"><strong></td>
		<!--<td align="center" width="10">:</td>-->
		<td><?= $row['noijasah'] ?></td>
	</tr>
	<tr valign="top">
		<!--<td><strong>No Ijazah Fakultas</strong></td>--> 
		<td width="95"><strong></td>
		<!--<td align="center">:</td>-->
		<td><?= $row['notranskrip'] ?></td>
	</tr>
<?			} ?>
</table>
<div style="height:5px"></div>
<table class="tb_data" width="100%">
	<tr>
		<th width="20">No</th>
		<th width="50">Kode</th>
		<th>Nama Mata Kuliah</th>
		<th width="25">Nilai</th>
		<th width="15">SKS</th>
	</tr>
  <tr height="16"></tr>
<?php
			$t_tsks = 0;
			$t_tnsks = 0;
			$n = count($row['transkrip'][$s]);
      
			for($i=0;$i<$n;$i++) {                           /*i=0*/
				$rowt = $row['transkrip'][$s][$i];             /*kolom kiri dan kolom kanan - emanshp*/
				
				if(is_array($rowt)) {
					$t_nsks = $rowt['sks']*$rowt['nangka'];
					$t_tsks += $rowt['sks'];
					$t_tnsks += $t_nsks;
?>
	<tr height="16">
		<td align="center"><?= ++$j ?></td>
		<td align="center"><?= $rowt['kodemk'] ?></td>
		<td class="mark"><?= $rowt['namamk'] ?></td>
		<td align="center"><?= $rowt['nhuruf'] ?></td>
		<td align="center"><?= $rowt['sks'] ?></td>
  </tr>  	
  
  <?php
  /*emanshp -> baris 159-165 merupakan hitungan lain yg tidak membedakan nilai pindahan atau pengambilan*/
   
  $tsks += $rowt['sks'];
  $tmutu += $rowt['sks']*$rowt['nangka'] ; 
  $ipk = round($tmutu/$tsks,2); 
  ?>
  
  
<?php
				}
				else {
?>
	<tr height="16">
		<th align="center" colspan="6"><?= $rowt ?></td>
	</tr>
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
<div style="height:5px"></div>
<table class="tb_cont" width="<?= $p_tbwidth ?>">
	<td width="49%">
<table class="tb_box" width="100%">
  <tr></tr>       
  <tr></tr>
  <tr></tr>
	<tr valign="top">
		<td align="center">
			<strong>Judul Skripsi :</strong><br>
			<div class="ta"><?= $row['judulta'] ?></div>
		</td>
	</tr>
</table>
<br>
<table class="tb_foot" width="100%">
	<tr>
		<td>&nbsp;</td>
	</tr>
	<tr>
		<!--<td align="center">Pembantu Dekan Bidang Akademik,</td>-->
	</tr>
	<tr height="35">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="center"><u><?= $row['pdakad'] ?></u></td>
	</tr>
	<tr>
		<!--<td align="center" class="mark">NIP. <?= $row['nippdakad'] ?></td>-->
	</tr>
</table>
	</td>
	
	<td width="2%">&nbsp;</td>
	
	<td width="49%">
<?php
		if(empty($t_tsks))
			$t_ipk = 0;
		else
			$t_ipk = round($t_tnsks/$t_tsks,2);
?>
<table class="tb_box" width="100%"> 
  <tr></tr>
  <tr></tr>
  <tr></tr>   
  <br>
	<tr>
		<!--<td>Jumlah SKS : <?= $row['skslulus'] // $t_tsks ?></td>--> 
		<td>Jumlah SKS : <?= $tsks ?></td>
		<!--<td>Jumlah SKS x N : <?= $t_tnsks ?></td>-->
	</tr>
	<tr>
		<td>IPK : <?= $ipk ?></td>
		<!--<td>Predikat : <?= $row['namapredikat'] ?></td>-->
	</tr>
	<tr>
		<td colspan="2" align="center" class="nobox">
<table>
	<tr>
		<!--<td rowspan="2" valign="middle">Keterangan : IPK =</td>-->
		<!--<td style="border-bottom:1px solid black">&Sigma; SKS x N</td>-->
	</tr>
	<tr>
		<!--<td>&Sigma; SKS</td>-->
	</tr>
</table>
		</td>
	</tr>
</table>
<br>
<table class="tb_box" width="100%">
	<tr>
		<!--<td align="center" class="mark">Jakarta, <?= CStr::formatDateInd($row['tgltranskrip']) ?></td>-->
	</tr>
	<tr>
		<td align="left"><strong> Kepala Biro Administrasi Akademik </strong></td>
	</tr>
	<tr height="35">
		<td>&nbsp;</td>
	</tr>
	<tr>
		<td align="left"><u>Andri Mauludi, SE.</u></td>
	</tr>
	<tr>
		<!--<td align="center" class="mark">NIP. <?= $row['nipdekan'] ?></td>-->
	</tr>
</table>
	</td>
</table>
<?php
	}
?>
</div>
</body>
</html>
