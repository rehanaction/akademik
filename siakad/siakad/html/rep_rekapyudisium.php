<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_jurusan = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_periode2 = CStr::removeSpecial($_REQUEST['periode2']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('laporan'));
	
	
	// properti halaman
	$p_title = 'Laporan Rekap Statistik Alumni';
	$p_tbwidth = 800;
	$p_namafile = 'rekap_statistik_alumni_periode'.$r_periode;
	
	$rs = mLaporan::rekapAlumni($conn,$r_jurusan,$r_periode,$r_periode2);
	$arr_jml = array();
	while($row = $rs->FetchRow()){
		$arr_jml[$row['idyudisium']][$row['kodeunit']][$row['sex']] = $row['nim'];
	}
	$rs_leftright = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit='$r_jurusan'");
	$rs_unit = $conn->Execute("select kodeunit, namaunit, level from gate.ms_unit where infoleft >= '".$rs_leftright['infoleft']."' and inforight <= '".$rs_leftright['inforight']."' and isakad=-1 order by infoleft ");
	
	$jml_periode=$conn->Execute("select * from akademik.ak_periodeyudisium where idyudisium between $r_periode and $r_periode2");
	$arr_periode = array();
	while($row = $jml_periode->FetchRow()){
		$arr_periode[] = $row['idyudisium'];
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
		.tb_data th { background-color: #CFC; font-size: 11px }
		.tb_data td { font-size: 20px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 15px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
<?php
	
		include('inc_headerlap.php');
?>
<div class="div_head">LAPORAN REKAP STATISTIK ALUMNI</div>
</br>
<table  border ="0" width="<?= $p_tbwidth ?>">
	<?if($r_jurusan != '110000'){?>
		<tr valign ="top">
			<td width="75">Prodi</td>
			<td align="center" width="10">:</td>
			<td width="310" class="mark"><?= mUnit::getNamaUnit($conn,$r_jurusan) ?></td>
		</tr>
	<?}?>
	<tr valign ="top">
		<td width="70">Periode Wisuda</td>
		<td align="center" width="10">:</td>	
		<td width="310" class="mark"><?= $r_periode.' - '.$r_periode2;?></td>	
	</tr>
</table>
<br>
<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">

<tr bgcolor = "grey">
	<th rowspan=3 style="color:#FFFFFF">No</th>
	<th rowspan=3 style="color:#FFFFFF">Nama Unit</th>
	<th colspan=<?= count($arr_periode)*2?> style="color:#FFFFFF">Periode Wisuda</th>
	<th width="50" rowspan=3 style="color:#FFFFFF">Jumlah</th>
</tr>
<tr bgcolor = "grey">
	<? foreach($arr_periode as $idx => $periode){?>
		<th colspan=2 style="color:#FFFFFF"><?= $periode?></th>
	<?}?>
</tr>
<tr bgcolor = "grey">
	<? foreach($arr_periode as $idx => $periode){?>
		<th width="30" style="color:#FFFFFF">L</th>
		<th width="30" style="color:#FFFFFF">P</th>
	<?}?>
</tr>
<? 
	$i=0;
	while ($row = $rs_unit->FetchRow()){
	$i++;
	?>
	<tr>
		<td><?=$i;?></td>
		<td><?=$row['namaunit']?></td>
		<? $jml_total = 0; 
		foreach($arr_periode as $idx => $periode){?>
			<td align="center"><?= count($arr_jml[$periode][$row['kodeunit']]['L'])?></td>
			<td align="center"><?= count($arr_jml[$periode][$row['kodeunit']]['P'])?></td>
		<?
			$jml_total = $jml_total+count($arr_jml[$periode][$row['kodeunit']]['L'])+count($arr_jml[$periode][$row['kodeunit']]['P']);
		}?>
		<td align="center"><?= $jml_total?></td>
	</tr>
	<? }?>
</table>
	<table>
	<tr>
		<td width="650">&nbsp;</td>
		<td class="mark"><?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
    </table>
 </body></html>