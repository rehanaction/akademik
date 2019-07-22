<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Pengumuman Nilai';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	$p_listpage = 'list_nilai';
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);
	
	// mendapatkan data
	$a_data = mKelas::getDataPeserta($conn,$r_key);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<div style="float:left; width:15%">
			<? require_once('inc_sidemenudosen.php');?>
			</div>
		<div style="float:left; width:50%">
			<form name="pageform" id="pageform" method="post">
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<?	if(isset($p_posterr)) { ?>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				<?	} else { ?>
				<div style="width:<?= $p_tbwidth ?>px;font-size:14px">
					<strong><?= $p_postmsg ?></strong>
				</div>
				<?	} ?>
				<div class="Break"></div>
				</center>
				<?	} ?>
				<center>
				<table width="<?= $p_tbwidth ?>">
					<tr valign="top">
						<td width="450">
					<center>
						<header>
							<div class="inner">
								<div class="left title">
									<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
									<h1><?= $p_title ?></h1>
								</div>
							</div>
						</header>
					</center>
					<?	/*************/
						/* LIST DATA */
						/*************/
					?>
					<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
						<?	/**********/
							/* HEADER */
							/**********/
						?>
						<tr>
							<th width="25">No.</th>
							<th width="80">NIM</th>
							<th>Nama</th>
							<th width="40">NA</th>
							<th width="30">NH</th>
							<th width="40">NK</th>
							<th width="30">Lulus</th>
						</tr>
						<?	/********/
							/* ITEM */
							/********/
							
							$i = 0;
							foreach($a_data as $row) {
								if ($i % 2) $rowstyle = 'NormalBG'; else $rowstyle = 'AlternateBG';
								
								$t_nangka = (float)$row['nangka'];
								$t_tnangka += $t_nangka;
								
								// untuk diagram
								$a_nhangka[$row['nhuruf']] = (float)$row['nangka'];
								$a_nhuruf[$row['nhuruf']]++;
						?>
						<tr valign="top" class="<?= $rowstyle ?>">
							<td><?= ++$i ?>.</td>
							<td align="center"><?= $row['nim'] ?></td>
							<td><?= $row['nama'] ?></td>
							<td align="center"><?= $t_nnumerik ?></td>
							<td align="center"><?= $row['nhuruf'] ?></td>
							<td align="center"><?= CStr::formatNumber($t_nangka,2) ?></td>
							<td align="center"><?= empty($row['lulus']) ? '' : '<img src="images/check.png">' ?></td>
						</tr>
						<?	}
							if($i == 0) {
						?>
						<tr>
							<td colspan="7" align="center">Data kosong</td>
						</tr>
						<?	}
							else { ?>
						<tr>
							<th colspan="5"><div style="text-align:right;padding-right:10px">IP Kelas</div></th>
							<th align="right"><?= CStr::formatNumber($t_tnangka/$i,2) ?></th>
							<th>&nbsp;</th>
						</tr>
						<?	} ?>
					</table>
				</div>
						</td>
						<td align="center">
				<?php
					/***********/
					/* DIAGRAM */
					/***********/
					
					arsort($a_nhangka);
					
					$a_nhurufcat = array();
					$a_nhurufbar = array();
					$a_nhurufpie = array();
					
					foreach($a_nhangka as $t_nhuruf => $t_nangka) {
						$t_jumlah = $a_nhuruf[$t_nhuruf];
						
						$a_nhurufcat[] = $t_nhuruf;
						$a_nhurufbar[] = $t_jumlah;
						$a_nhurufpie[] = "'$t_nhuruf', ".round(($t_jumlah*100)/$i,2);
					}
				?>
					<div id="container_bar" style="width:250px;height:200px"></div>
					<br>
					<div id="container_pie" style="width:250px;height:200px"></div>
						</td>
					</tr>
				</table>
				</center>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">

			</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	var chart_bar, chart_pie;
	
	chart_bar = new Highcharts.Chart({
		chart: {
			renderTo: 'container_bar',
			type: 'column'
		},
		title: {
			text: 'Perbandingan Nilai',
			x: -20 //center
		},
		xAxis: {
			title: {
				text: 'Nilai'
			},
			categories: ['<?= implode("', '",$a_nhurufcat) ?>']
		},
		yAxis: {
			title: {
				text: 'Jumlah Mahasiswa'
			}
		},
		tooltip: {
			formatter: function() {
				return '<strong>' + this.x + ': </strong>' + this.y + ' mahasiswa';
			}
		},
		plotOptions: {
			column: {
				dataLabels: {
					enabled: true
				}
			}
		},
		legend: {
			enabled: false
		},
		series: [{
			name: 'Perbandingan Nilai',
			data: [<?= implode(', ',$a_nhurufbar) ?>]
		}]
	});
	
	chart_pie = new Highcharts.Chart({
		chart: {
			renderTo: 'container_pie',
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: {
			text: 'Perbandingan Nilai'
		},
		tooltip: {
			pointFormat: '<strong>{point.percentage}%</strong>',
			percentageDecimals: 2
		},
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Perbandingan Nilai',
			data: [
				[<?= implode('],[',$a_nhurufpie) ?>]
			]
		}]
	});
});

</script>
</body>
</html>