<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
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
	$p_title = 'Transkrip Alumni';
	$p_tbwidth = 870;
	
	if(empty($r_npm)) {
		$p_namafile = 'transkrip_'.$r_periode.'_'.$r_kodeunit.'_'.$r_angkatan;
		$a_data = mLaporanMhs::getTranskripUnit($conn,$r_periode,$r_kodeunit,$r_angkatan);
	}
	else {
		$p_namafile = 'transkrip_'.$r_npm;
		$a_data = mLaporanMhs::getTranskripLulus($conn,$r_periode,$r_npm);
	}
	$prodi_mhs = mLaporanMhs::getProdi($conn,$r_npm);
	
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
		 /*.div_head { font-family:'Arial Narrow','Arial'; font-size:27px;}*/
		.tb_head td{ font-family: "Arial Narrow","Arial"; font-size: 14px; }
		.tb_head td { font-size: 10px }
		.div_head { font-size:14px; font-weight:bold; text-align:center; text-decoration:underline; margin-top:50px; width:750px; }
		
		.tb_cont td { padding: 0; vertical-align: top; font-size: 14px }
		.tb_data { border: 0px solid black; border-collapse: collapse }  
		
		.tb_data th, .tb_data td { border: 0px solid black; font-family: "Arial Narrow","Arial"; font-size: 14px; padding: 1px }
		.tb_data th { background-color: #FFF }
		.tb_data .mark { font-family: "Arial Narrow","Arial" }
		
		.tb_foot { font-family: "Arial Narrow","Arial"; font-size: 14px; font-weight: bold; margin-top: 10px }
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
	/*	if(!empty($row['keterangan'])) {
?>
<div class="div_headertext"><?= strtoupper($row['keterangan']) ?></div>
<?php
		} */
?>
<div style="height:40px"></div>
<div style="font-family:'Arial Narrow','Arial'; font-size:25px;">TRANSKRIP AKADEMIK</div>
<!-- div class="div_subhead">(Lampiran Ijazah Program <?= $prodi_mhs['jenjang'] ?> / <?= $prodi_mhs['program'].'-'.$prodi_mhs['bidang'] ?>)</div -->
<br>
<table class="tb_cont" width="<?= $p_tbwidth ?>">
	<tr>
  
<?php
		$j = 0;                  /*j=nomor urut*/
		$xyz=array();
		for($s=0;$s<2;$s++) {    /**/
?>
		<td width="49%">
<table class="tb_head" width="100%">
<?			if($s == 0) { ?>
	<tr valign="top">
		<td style="width:140px">N I M</td>
		<td align="center" width="10">:</td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= $row['nim'] ?></td>
	</tr>
	<tr valign="top">
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;">Nama</td>
		<td align="center">:</td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= ucwords(strtolower($row['nama'])) ?></td>
	</tr>
	<tr valign="top">
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;">Tempat & Tanggal Lahir</td>
		<td align="center">:</td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= ucwords(strtolower($row['tmplahir'])).", ". CStr::formatDateInd($row['tgllahir']) ?></td>
	</tr>
<?			} else { ?>
	<tr valign="top">
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;width:90px">Program Studi</td>
		<td align="center" width="10">:</td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= $prodi_mhs['jenjang'] ." / ". $prodi_mhs['program'] ?></td>
	</tr>
	<!--<tr valign="top">
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;">Fakultas</td>
		<td align="center">:</td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"></td>
	</tr>-->
	<tr valign="top">
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;">Peminatan</td>
		<td align="center">:</td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= $prodi_mhs['bidang'] ?></td>
	</tr>
	<tr valign="top">
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;">&nbsp;</td>
		<td align="center">&nbsp;</td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;">&nbsp;</td>
	</tr>
<?			} ?>
</table>
<?php

// require_once("/home/emanshp/Debug.php");
// Zend_Debug::dump($row,"aa",true);

?>
<div style="height:15px"></div>
<table class="tb_data" width="100%">
<?php if ( !empty($row['transkrip'][$s]) ){ ?>

	<tr>
		<th align="left" width="50" style="padding-bottom:10px;font-family:'Arial Narrow','Arial';font-size:17px;">Kode</th>
		<th align="left" style="padding-bottom:10px;font-family:'Arial Narrow','Arial';font-size:17px;">Nama Mata Kuliah</th>
		<th align="center" width="50" style="padding-bottom:10px;font-family:'Arial Narrow','Arial';font-size:17px;">SKS</th>
		<th align="center" width="25" style="padding-bottom:10px;font-family:'Arial Narrow','Arial';font-size:17px;">Nilai</th>
	</tr>
<?php } ?>
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
					if ( empty($rowt['kodemk']) ){
?>
	
<?php	
					}else{
?>
	<tr height="16">
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= $rowt['kodemk'] ?></td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= $rowt['namamk'] ?></td>
		<td align="center" style="font-family:'Arial Narrow','Arial';font-size:15px;"><?= $rowt['sks'] ?></td>
		<td style="font-family:'Arial Narrow','Arial';font-size:15px;">&nbsp;&nbsp;&nbsp;&nbsp;<?= $rowt['nhuruf'] ?></td>
	</tr>  	
  
<?php
					}
  /*emanshp -> baris 159-165 merupakan hitungan lain yg tidak membedakan nilai pindahan atau pengambilan*/
   
 // $tsks += $rowt['sks'];
//  $tmutu += $rowt['sks']*$rowt['nangka'] ; 
//  $ipk = round($tmutu/$tsks,2); 
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
				$xyz[$s]=$i;
			}
?>
</table>
<?php
$rowww=strlen($row['judulta']) / 70;
if( ( $s==0 && ($xyz[0]+$rowww)<39 || ($s==1 && ($xyz[0]+$rowww)>=39) ) ){
//	if(empty($t_tsks))
//		$t_ipk = 0;
//	else
//		$t_ipk = round($t_tnsks/$t_tsks,2);

	// $predikat = mLaporanMhs::getPredikatByIPK($conn,$row["programpend"],$row["thnkurikulum"],$row["ipk"]);
?>
<div style="height:20px"></div>
<table class="tb_data" width="100%">
<tr valign="top">
	<td align="left" style="font-family:'Arial Narrow','Arial';font-size:15px;">Judul <?= $prodi_mhs['jenjang']=="S1"? 'Skripsi' : 'Tesis'; ?> :<br><br>
		<div class="ta"><?= $row['judulta'] ?></div>
	</td>
</tr>
</table>
<div style="height:20px"></div>
<table class="tb_data" width="100%" style='font-family: "Arial Narrow","Arial"; font-size: 15px;'> 
	<tr>
		<td style="width:120px">Lulus Tanggal</td><td>:</td><td><?= CStr::formatDateInd($row['tgllulus']) ?></td>
	</tr>
	<tr>
		<td>Jumlah SKS Diperoleh</td><td>:</td><td><?= $row["skslulus"] ?></td>
	</tr>
	<tr>
		<td>Indeks Prestasi</td><td>:</td><td><?= $row["ipk"] ?></td>
	</tr>
	<tr>
		<td>Predikat Kelulusan</td><td>:</td><td><?= $row["namapredikat"] ?></td>
	</tr>
<?php
if ( $xyz[0]+4+$rowww <=35 ){
for($i=$xyz[0]+4+$rowww;$i<=35;$i++){
?>
	<tr>
		<td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td>
	</tr>
<?php
}
}
?>
</table>
<?php
}
?>
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
	<td width="50%">&nbsp;</td>
	
	<td width="20%">&nbsp;</td>
	
	
</table>

<?php
	}
?>


<table width="<?= $p_tbwidth ?>" style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'>
	<tr>
		<td></td>
		<td></td>
		<td style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'>Bandung, <?= CStr::formatDateInd($row['tgllulus']) ?></td>
	</tr>
	<tr>
		<td valign="top" style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'>Ketua STIE INABA,</td>
		<td align="center" rowspan="6">
			<table>
				<tr>
					<td style="border: 1px solid black;"><br/>&nbsp;<br/>&nbsp;Foto&nbsp;3x4&nbsp;<br/>&nbsp;<br/>&nbsp;</td>
				</tr>
			</table>
		</td>
		<td valign="top" style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'>Ketua Program Studi,</td>
	</tr>
	<tr>
		<td valign="bottom">&nbsp;</td>
		<td valign="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td valign="bottom">&nbsp;</td>
		<td valign="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td valign="bottom">&nbsp;</td>
		<td valign="bottom">&nbsp;</td>
	</tr>
	<tr>
		<td valign="bottom" style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'>Dr. YOYO SUDARYO,S.E.,M.M.,Ak,CA</td>
		<td valign="bottom" style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'><?= $row['ketua'] ?></td>
	</tr>
	<tr>
		<td valign="top" style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'>NIDN. 409126902</td>
		<td valign="top" style='font-family: "Arial Narrow","Arial"; font-size: 15px; font-weight: bold;font-weight: normal'>NIDN. <?= $row['nidnketua'] ?></td>
	</tr>
	
</table>


</div>
</body>
</html>
