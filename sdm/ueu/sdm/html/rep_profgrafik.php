<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_POST['unit']);
	$r_periode = CStr::removeSpecial($_POST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('profile'));
	
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
		$connsia->debug=true;
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_col = 8;
	$p_file = 'grafikprofildosen'.$r_unit;
	$p_model = 'mProfile';
	$p_window = 'Grafik Profil Dosen Tetap dan Tidak Tetap';
	
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
	
	$namaperiode = $p_model::getNamaPeriode($connsia,$r_periode);
    $a_data = $p_model::getLapGrafik($conn,$r_periode,$r_unit);
	
	$a_count = $a_data['count'];
	$a_counthb = $a_data['counthb'];
	
	$a_pendidikan = $a_data['pendidikan'];
	$a_fungsional = $a_data['fungsional'];
	$a_jenispegawai = $a_data['jenispegawai'];
	$a_cpendidikan = $a_count['pendidikan'];
	$a_cfungsional = $a_count['fungsional'];
	$a_cjenispegawai = $a_count['jenispegawai'];
	$a_cjenispegawaihb = $a_counthb['jenispegawai'];
	
	$a_grappend = array();
	foreach($a_pendidikan as $inc => $namapendidikan){
		$value = 0;
		if (!empty($a_cpendidikan[$inc]))
			$value = $a_cpendidikan[$inc];
		$a_graphpend[] = "{pend: '$namapendidikan',value:".$value."}";
	}
	$graphpend = implode(', ', $a_graphpend);
	
	$a_graphfung = array();
	foreach($a_fungsional as $inc => $jabatan){
		$value = 0;
		if (!empty($a_cfungsional[$inc]))
			$value = $a_cfungsional[$inc];
		$a_graphfung[] = "{jabf: '$jabatan',value:".$value."}";
	}
	$graphfung = implode(', ', $a_graphfung);
	
	$a_graphjenis = array();
	foreach($a_jenispegawai as $inc => $jenispegawai){
		$value = 0;
		if (!empty($a_cjenispegawai[$inc]))
			$value = $a_cjenispegawai[$inc];
		$a_graphjenis[] = "{jenispeg: '$jenispegawai',value:".$value."}";
	}
	$graphjenis = implode(', ', $a_graphjenis);
	
	$a_graphjenishb = array();
	foreach($a_jenispegawai as $inc => $jenispegawai){
		$value = 0;
		if (!empty($a_cjenispegawaihb[$inc]))
			$value = $a_cjenispegawaihb[$inc];
		$a_graphjenishb[] = "{jenispeghb: '$jenispegawai',value:".$value."}";
	}
	$graphjenishb = implode(', ', $a_graphjenishb);
	
	$p_title = 'Grafik Profil Dosen Tetap Dan Tidak Tetap <br />
				Unit '.$a_data['unit'].'<br />
				'.$namaperiode;
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
<div align="center">
		<? require_once($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="2" align="center"><strong>Pendidikan</strong></td>
			</tr>
			<tr>
				<td valign="top" width="300px">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="3">Pendidikan</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Jumlah</td>
							<td>%</td>
						</tr>
						<? if (count($a_pendidikan) >0){
							foreach($a_pendidikan as $incp => $namapendidikan){
						?>
						<tr>
							<td><?= $namapendidikan; ?></td>
							<td align="right"><?= empty($a_cpendidikan[$incp]) ? 0 : $a_cpendidikan[$incp]; ?></td>
							<td>&nbsp;</td>
						</tr>
						<? }} ?>
					</table>
					<br />
				</td>
				<td><div id="chartpenddiv" style="width: 100%; height: 300px;"></div></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><strong>Kepangkatan Akademik</strong></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="3">Kepangkatan Akademik</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Jumlah</td>
							<td>%</td>
						</tr>
						<? if (count($a_fungsional) >0){
							foreach($a_fungsional as $incf => $jabatan){
						?>
						<tr>
							<td><?= $jabatan; ?></td>
							<td align="right"><?= empty($a_cfungsional[$incf]) ? 0 : $a_cfungsional[$incf]; ?></td>
							<td>&nbsp;</td>
						</tr>
						<? }} ?>
					</table>
					<br />
				</td>
				<td><div id="chartjabdiv" style="width: 100%; height: 400px;"></div></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><strong>Jenis Pegawai</strong></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="3">Jenis Pegawai</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Jumlah</td>
							<td>%</td>
						</tr>
						<? if (count($a_jenispegawai) >0){
							foreach($a_jenispegawai as $incj => $namajenispegawai){
						?>
						<tr>
							<td><?= $namajenispegawai; ?></td>
							<td align="right"><?= empty($a_cjenispegawai[$incj]) ? 0 : $a_cjenispegawai[$incj]; ?></td>
							<td>&nbsp;</td>
						</tr>
						<? }} ?>
					</table>
					<br />
				</td>
				<td><div id="chartstatusdiv" style="width: 700px; height: 400px;"></div></td>
			</tr>
			<tr>
				<td colspan="2" align="center"><strong>Jenis Pegawai Homebase</strong></td>
			</tr>
			<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="4" cellspacing="0">
						<tr>
							<td colspan="3">Jenis Pegawai</td>
						</tr>
						<tr>
							<td>&nbsp;</td>
							<td>Jumlah</td>
							<td>%</td>
						</tr>
						<? if (count($a_jenispegawai) >0){
							foreach($a_jenispegawai as $incj => $namajenispegawai){
						?>
						<tr>
							<td><?= $namajenispegawai; ?></td>
							<td align="right"><?= empty($a_cjenispegawaihb[$incj]) ? 0 : $a_cjenispegawaihb[$incj]; ?></td>
							<td>&nbsp;</td>
						</tr>
						<? }} ?>
					</table>
					<br />
				</td>
				<td><div id="chartstatusdivhome" style="width: 700px; height: 400px;"></div></td>
			</tr>
		</table>
		
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
<script type="text/javascript" src="scripts/amcharts/amcharts.js"></script>
      
        <script type="text/javascript">
            var chart;
            var legend;

            var chartDataPend = [<?= $graphpend; ?>];

            AmCharts.ready(function () {
                // PIE CHART
                chart = new AmCharts.AmPieChart();
                chart.dataProvider = chartDataPend;
                chart.titleField = "pend";
                chart.valueField = "value";
				chart.labelText = "[[percents]]%";
                chart.outlineColor = "#FFFFFF";
                chart.outlineAlpha = 0.8;
                chart.outlineThickness = 2;
                // this makes the chart 3D
                chart.depth3D = 15;
                chart.angle = 20;
				chart.labelRadius = -10;
				
				legend = new AmCharts.AmLegend();
                legend.align = "left";
                legend.markerType = "circle";
				legend.labelText = "[[title]]";
				legend.switchType = "v";
                chart.addLegend(legend);
                // WRITE
                chart.write("chartpenddiv");
            });
			
			var chartDataJabF = [<?= $graphfung; ?>];
			
			AmCharts.ready(function () {
                // PIE CHART
                chart = new AmCharts.AmPieChart();
                chart.dataProvider = chartDataJabF;
                chart.titleField = "jabf";
                chart.valueField = "value";
				chart.labelText = "[[percents]]%";
                chart.outlineColor = "#FFFFFF";
                chart.outlineAlpha = 0.8;
                chart.outlineThickness = 2;
                // this makes the chart 3D
                chart.depth3D = 15;
                chart.angle = 20;
				chart.labelRadius = -20;
								
				legend = new AmCharts.AmLegend();
                legend.align = "left";
                legend.markerType = "circle";
				legend.labelText = "[[title]]";
				legend.switchType = "v";
                chart.addLegend(legend);
                // WRITE
                chart.write("chartjabdiv");
            });
						
			var chartDataJP = [<?= $graphjenis; ?>];
			
			AmCharts.ready(function () {
                // PIE CHART
                chart = new AmCharts.AmPieChart();
                chart.dataProvider = chartDataJP;
                chart.titleField = "jenispeg";
                chart.valueField = "value";
				chart.labelText = "[[percents]]%";
                chart.outlineColor = "#FFFFFF";
                chart.outlineAlpha = 0.8;
                chart.outlineThickness = 2;
                // this makes the chart 3D
                chart.depth3D = 15;
                chart.angle = 20;
				chart.labelRadius = -20;
				
				legend = new AmCharts.AmLegend();
                legend.align = "left";
                legend.markerType = "circle";
				legend.labelText = "[[title]]";
				legend.switchType = "v";
                chart.addLegend(legend);
                // WRITE
                chart.write("chartstatusdiv");
            });
			
			var chartDataJPHB = [<?= $graphjenishb; ?>];
			
			AmCharts.ready(function () {
                // PIE CHART
                chart = new AmCharts.AmPieChart();
                chart.dataProvider = chartDataJPHB;
                chart.titleField = "jenispeghb";
                chart.valueField = "value";
				chart.labelText = "[[percents]]%";
                chart.outlineColor = "#FFFFFF";
                chart.outlineAlpha = 0.8;
                chart.outlineThickness = 2;
                // this makes the chart 3D
                chart.depth3D = 15;
                chart.angle = 20;
				chart.labelRadius = -20;
				
				legend = new AmCharts.AmLegend();
                legend.align = "left";
                legend.markerType = "circle";
				legend.labelText = "[[title]]";
				legend.switchType = "v";
                chart.addLegend(legend);
                // WRITE
                chart.write("chartstatusdivhome");
            });
        </script>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
