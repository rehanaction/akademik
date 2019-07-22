<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_POST['unit']);
	$r_tglmulai = CStr::formatDate($_POST['tglmulai']);
	$r_tglselesai = CStr::formatDate($_POST['tglselesai']);
	$r_hubkerja = $_POST['hubkerja'];
	$r_format = CStr::removeSpecial($_POST['format']);
	
	require_once(Route::getModelPath('presensi'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 13;
	$p_file = 'laporanstatushadir_'.$r_kodeunit;
	$p_model = 'mPresensi';
	$p_window = 'Laporan Statistik Kehadiran Kerja';
	
	// header
	switch($r_format) {
		case 'doc';
			header("Content-Type: application/msword");
			header('Content-Disposition: attachment; filename="'.$p_file.'.doc"');
			break;
		case 'xls' :
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="'.$p_file.'.xls"');
			break;
		default : header("Content-Type: text/html");
	}
	
	if(empty($r_hubkerja))
		$sqlhubkerja = '';
	else if(count($r_hubkerja) == 1) {
		if(is_array($r_hubkerja)) $r_hubkerja = $r_hubkerja[0];
		$sqlhubkerja = "and idhubkerja = '".CStr::cAlphaNum($r_hubkerja)."' ";
	}
	else {
		for($i=0;$i<count($r_hubkerja);$i++)
			$r_hubkerja[$i] = CStr::cAlphaNum($r_hubkerja[$i]);
		$i_hubkerja = implode("','",$r_hubkerja);
		$sqlhubkerja = "and idhubkerja in ('$i_hubkerja') ";
	}
	
    $a_data = $p_model::getLapStatusHadir($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$sqlhubkerja);
	
	$rs = $a_data['list'];
	$a_statusabsen = $a_data['statusabsen'];
	$a_jamreal = $a_data['jamnyata'];
	$jamnormal = $a_data['jamnormal'];
	
	$a_graphstatus = array();
	foreach($rs as $row){
		$value = 0;
		if (!empty($a_cpendidikan[$inc]))
			$value = $a_cpendidikan[$inc];
		$a_graphstatus[] = "{pend: '$namapendidikan',value:".$value."}";
	}
	$a_graphstatus = implode(', ', $a_graphstatus);
		
	$p_title = 'Laporan Kehadiran Kerja <br />
				Unit '.$a_data['namaunit'].'<br />
				Periode '.CStr::formatDateInd($r_tglmulai).' s/d '.CStr::formatDateInd($r_tglselesai);
?>

<html>
<head>
<title><?= $p_window; ?></title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<link rel="icon" type="image/x-icon" href="images/favicon.png">
<style>
table { border-collapse:collapse }
div,td,th {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:12px;
}
td,th { border:1px solic black }
</style>
</head>
<body>
<div align="center" style="page-break-after:always">
	<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">			
			<tr bgcolor = "gray">
				<th rowspan = "2"><b style = "color:#FFFFFF">No</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">NIP</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">Nama</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">Mangkir</b></th>
				<th colspan = "2"><b style = "color:#FFFFFF">Jumlah Jam Kerja</b></th>
				<th colspan = "4"><b style = "color:#FFFFFF">Status</b></th>
				<th rowspan = "2"><b style = "color:#FFFFFF">Ket</b></th>
			</tr>
			<tr bgcolor ="gray">				
				<th><b style = "color:#FFFFFF">Nyata</b></th>							
				<th><b style = "color:#FFFFFF">Dinas + Cuti</b></th>						
				<th><b style = "color:#FFFFFF">Izin</b></th>
				<th><b style = "color:#FFFFFF">Sakit</b></th>							
				<th><b style = "color:#FFFFFF">Dinas</b></th>
				<th><b style = "color:#FFFFFF">Cuti</b></th>	
			</tr>
			<?php 
				if (count($rs) > 0){
			$i=0;
			$jmlmangkir=$jamnyata=$jamdinas=$jmlijin=$jmlsakit=$jmldinas=$jmlcuti=0;
			$a_graphstatus = array();
			foreach($rs as $row) { 
				$i++;
					$jmlmangkir += $a_statusabsen['A'][$row['idpegawai']];
					$jamnyata += round($a_jamreal['H'][$row['idpegawai']]/60,2);
					$jamdinas += round($a_jamreal['D'][$row['idpegawai']]/60,2);
					$jmlijin += $a_statusabsen['I'][$row['idpegawai']];
					$jmlsakit += $a_statusabsen['S'][$row['idpegawai']];
					$jmldinas += $a_statusabsen['D'][$row['idpegawai']];
					$jmlcuti += $a_statusabsen['C'][$row['idpegawai']];
					
					$a_graphstatus[] = '{status: "'.$row[nik].'",
										valuereal: '.round($a_jamreal['H'][$row['idpegawai']]/60,2).',									
										colorreal: "#0D8ECF",
										valuedinas: '.round(($a_jamreal['D'][$row['idpegawai']])/60,2).',									
										colordinas: "#FF6600"}';
			?>
			<tr>
				<td><?= $i; ?></td>
				<td><?= $row['nik']; ?></td>
				<td><?= $row['namalengkap']; ?></td>
				<td align="right"><?= $a_statusabsen['A'][$row['idpegawai']]; ?></td>
				<td align="right"><?= round($a_jamreal['H'][$row['idpegawai']]/60,2); ?></td>
				<td align="right"><?= round(($a_jamreal['D'][$row['idpegawai']])/60,2); //round(($a_jamreal['D'][$row['idpegawai']]+$a_jamreal['H'][$row['idpegawai']])/60,2); ?></td>
				<td align="right"><?= $a_statusabsen['I'][$row['idpegawai']]; ?></td>
				<td align="right"><?= $a_statusabsen['S'][$row['idpegawai']]; ?></td>
				<td align="right"><?= $a_statusabsen['D'][$row['idpegawai']]; ?></td>
				<td align="right"><?= $a_statusabsen['C'][$row['idpegawai']]; ?></td>
				<td align="right"></td>
			</tr>
			<?php } 
				$graphstatus = implode(', ', $a_graphstatus);
				//$jamdinas += $jamnyata;
			?>
			<tr>
				<td colspan="3" align="right"><strong>Jumlah</strong></td>
				<td align="right"><?= $jmlmangkir; ?></td>
				<td align="right"><?= $jamnyata; ?></td>
				<td align="right"><?= $jamdinas; ?></td>
				<td align="right"><?= $jmlijin; ?></td>
				<td align="right"><?= $jmlsakit; ?></td>
				<td align="right"><?= $jmldinas; ?></td>
				<td align="right"><?= $jmlcuti; ?></td>
				<td align="right"></td>
			</tr>
			<tr>
				<td colspan="3" align="right"><strong>Rata-rata per Unit</strong></td>
				<td align="right"><?= round($jmlmangkir/$i,2); ?></td>
				<td align="right"><?= round($jamnyata/$i,2); ?></td>
				<td align="right"><?= round($jamdinas/$i,2); ?></td>
				<td align="right"><?= round($jmlijin/$i,2); ?></td>
				<td align="right"><?= round($jmlsakit/$i,2); ?></td>
				<td align="right"><?= round($jmldinas/$i,2); ?></td>
				<td align="right"><?= round($jmlcuti/$i,2); ?></td>
				<td align="right"></td>
			</tr>
			<? }else{ ?>		
			<tr>
				<td align="center" colspan="10">Data tidak ditemukan</td>
			</tr>
			<? } ?>
		</table>
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">			
			<tr>
				<td><?= 'Periode '.CStr::formatDateInd($r_tglmulai).' s/d '.CStr::formatDateInd($r_tglselesai); ?></td>
			</tr>		
			<tr>
				<td>Dengan jam kerja normal <?= $jamnormal; ?> jam</td>
			</tr>
		</table>
		<br />
		<div id="chartdiv" style="width: <?= $p_tbwidth ?>px; height: 400px;border:1px solid black;"></div>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
<script type="text/javascript" src="scripts/amcharts/amcharts.js"></script>
<script type="text/javascript">
<? if (count($rs) > 0){ ?>
    var chart;
    var legend;
	
	var chartData = [<?= $graphstatus; ?>];


            AmCharts.ready(function () {
                // SERIAL CHART
                chart = new AmCharts.AmSerialChart();
                chart.dataProvider = chartData;
                chart.categoryField = "status";
                chart.startDuration = 1;

                // AXES
                // category
               /* var categoryAxis = chart.categoryAxis;
                categoryAxis.labelRotation = 90;
                categoryAxis.gridPosition = "start";*/
				
				  // category
               var categoryAxis = chart.categoryAxis;
                categoryAxis.labelRotation = 45; // this line makes category values to be rotated
                categoryAxis.gridAlpha = 0;
                categoryAxis.fillAlpha = 1;
                categoryAxis.fillColor = "#FAFAFA";
                categoryAxis.gridPosition = "start";

                // value
                // in case you don't want to change default settings of value axis,
                // you don't need to create it, as one value axis is created automatically.

                // GRAPH
                var graph1 = new AmCharts.AmGraph();
                graph1.valueField = "valuereal";
                graph1.balloonText = "Nyata : [[value]]";
                graph1.colorField = "colorreal";
                graph1.type = "column";
                graph1.lineAlpha = 0;
                graph1.fillAlphas = 1;
                chart.addGraph(graph1);
				
				var graph2 = new AmCharts.AmGraph();
                graph2.valueField = "valuedinas";
                graph2.balloonText = "Dinas + Cuti : [[value]]";
                graph2.colorField = "colordinas";
                graph2.type = "column";
                graph2.lineAlpha = 0;
                graph2.fillAlphas = 1;
                chart.addGraph(graph2);

                chart.write("chartdiv");
            });
	<? } ?>
 </script>
 </html>
 
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
 
