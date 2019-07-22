<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	//$conn->debug=true;
	// variabel request
	$r_jurusan = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_nip = CStr::removeSpecial($_REQUEST['nipdosen']);
	$r_frsdisetujui = CStr::removeSpecial($_REQUEST['frsdisetujui']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	$r_periode = $r_tahun.$r_semester;
	
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	
	// properti halaman
	$p_title = 'Laporan Data Mahasiswa yang Sudah KRS';
	$p_tbwidth = 800;
	$p_namafile = 'mahasiswa_krs'.$r_kodeunit;
	
	 $a_data = mLaporan::getSdhkrs($conn,$r_jurusan,$r_periode,$r_frsdisetujui,$r_nip);
	 $r_namajurusan = mUnit::getNamaUnit($conn,$r_jurusan);
	
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
		.div_preheader { font-size: 15px; font-weight: bold }
		.div_header { font-size: 15px }
		.div_headertext { font-size: 12px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head { border-bottom: 1px solid black }
		.tb_head td { font-size: 10px }
		.tb_head .mark { font-size: 11px }
		.div_head { font-size: 16px; text-decoration: underline }
		.div_subhead { font-size: 14px; margin-bottom: 5px }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 1px }
		.tb_data th { background-color: #CFC; font-size: 13px }
		.tb_data td { font-size: 13px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 15px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body onload="window.print()">
<div align="center">
<?php
	
		include('inc_headerlap.php');
?>
<div class="div_head">JUMLAH MAHASISWA WALI</div>
</br>
<?php foreach($a_data as $nipdosen=>$row){ ?>
 <table  border ="0" width="<?= $p_tbwidth ?>">
 <tr valign ="top">
	 <td width = "100">Periode Semester</td>
	 <td align ="center" width ="10">:</td>
	 <td width ="310" class="mark">
	 <? echo Akademik::getNamaPeriode($r_periode) ?></td>
   </tr>
   <tr>
	<td>Unit</td>
	<td>:</td>
	<td><?=$r_namajurusan?></td>
   </tr>
    <tr>
	<td>Nama Dosen</td>
	<td>:</td>
	<td><?=$row[0]['nipdosenwali']?> - <?=$row[0]['dosenwali']?></td>
   </tr>
   <tr>
	<td>Jumlah Mahasiswa</td>
	<td>:</td>
	<td><?=count($row)?></td>
   </tr>
   </table>
<br>
<table width="<?= $p_tbwidth ?>" class="tb_data">
	<tr >
		<th >No</th>
		<th >NPM</th>
		<th >Nama</th>
		<?php /* <th >IPS Lalu</th> */ ?>
		<th >Status</th>
		<th >SKS </th>
		<?php /* <th >Validator </th> */ ?>
	</tr>
	<? 
	$i=0;
	$ipskurang3=0;
	$ipslebih3=0;
	foreach($row as $rowm){
		if(!empty($row['waktukrs'])){
			$date=explode(' ',$rowm['waktukrs']);
			$tgl=Date::indoDate($date[0],false);
			$waktu=$tgl.' '.$date[1];
		}else
			$waktu='';
			
		if($rowm['ipslalu']<3)
			$ipskurang3++;
		else
			$ipslebih3++;
	$i++;
	?>

	<tr>
	<td><?=$i;?></td>
	<td><?=$rowm['nim']?></td>
	<td><?=$rowm['nama']?></td>
	<?php /* <td align="center"><?=$rowm['ipslalu']?></td> */ ?>
	<td><?=$rowm['status']?></td>
	<td align="center"><?= $rowm['sks']?></td>
	<?php /* <td><?=$rowm['t_updateuser']?></td> */ ?>
	</tr>
	<? }?>
	</table><br>
	<?php /*
	<div class="div_headertext" align="left" style="width:<?php echo $p_tbwidth?>px">
		Keterangan : 
		<ul>
			<li>IPS < 3.00 sebanyak <strong><?php echo $ipskurang3?> Mahasiswa.</strong></li>
			<li> IPS >=3.00 sebanyak <strong><?php echo $ipslebih3?> Mahasiswa.</strong></li>
		</ul>
	</div>
	*/ ?>
	<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td width="450">&nbsp;</td>
		<td class="mark">Bandung, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td width="">&nbsp;</td>
		<td class="mark">Ketua,</td>
	</tr>
	<tr>
		<td width="">&nbsp;</td>
		<td class="mark">&nbsp;</td>
	</tr>
	<tr>
		<td width="">&nbsp;</td>
		<td class="mark"></td>
	</tr>
	<tr>
		<td width="">&nbsp;</td>
		<td class="mark"></td>
	</tr>
	<tr>
		<td width="350">&nbsp;</td>
		<td class="mark"><b>Dr. YOYO SUDARYO, S.E., M.M., Ak., C.A.</b></td>
	</tr>
	<tr>
		<td width="">&nbsp;</td>
		<td class="mark"><b>NIDN. 409126902</b></td>
	</tr>
    </table>
    
    <div style="page-break-after:always"></div>
 <?php } ?>  
	</div>
 </body></html>
