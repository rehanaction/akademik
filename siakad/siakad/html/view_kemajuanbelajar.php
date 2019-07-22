<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(Akademik::isMhs())
	{
		$r_key = Modul::getUserName();
		$display="none";
	}
	else if(Akademik::isDosen()){
		$display="none";
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	}
	else
	{
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
		$display="block";
	}
	// properti halaman
	$p_title = 'Kemajuan Belajar Mahasiswa';
	$p_tbwidth = "100%";
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_data = mKRS::getDataPerSemester($conn,$r_key,$a_infomhs['periodedaftar'],false,true);
	$a_index = mPerwalian::getDataMahasiswa($conn,$r_key);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/perwalian.js"></script>
	<style>
		.container_box {
			float:left;
			width:100%;
			height:230px;
			border:1px solid #CCC;
			margin:9px 9px;
		}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<?php require_once('inc_headermahasiswa.php') ?>
			<center>
			<div class="filterTable" style="width:<?= $p_tbwidth ?>px;">
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" align="center">
					<tr valign="top">
						<td width="100" align="center" rowspan="4"><?= uForm::getImageMahasiswa($conn,$r_key,false,'width="90"') ?></td>
						<td><span style="font-size:18px"><?= $a_infomhs['nim'] ?> - <?= $a_infomhs['nama'] ?></span></td>
					</tr>
					<tr valign="top">
						<td><span style="font-size:18px"><?= $a_infomhs['jenjang'] ?> - <?= $a_infomhs['jurusan'] ?></span></td>
					</tr>
					<tr valign="top">
						<td><span style="font-size:16px">Angkatan: <?= substr($a_infomhs['periodemasuk'],0,4) ?>, Semester: <?= $a_infomhs['semestermhs'] ?></span></td>
					</tr>
					<tr valign="top">
						<td><span style="font-size:16px">IPK: <?= $a_infomhs['ipk'] ?>, SKS Lulus: <?= $a_infomhs['skslulus'] ?></span></td>
					</tr>
				</table>
			</div>
			<br>
			<div style="width:400px; font-size:15px; font-weight:bold; border:solid 1px #CCC; padding:5px; background:#D8FEEC">
		 - Klik grafik untuk detail -
			</div>
			<div id="container_bar" style="width:100%;height:200px">
			
			</div>
		 
			<br>
			
			<div style="width:<?= $p_tbwidth ?>px;">
			<div id="container_ipk" class="container_box"></div>
			<div id="container_ips" class="container_box"></div>
			<div style="clear:both"></div>
			<div id="container_sks" class="container_box"></div>
			<div id="container_nh" class="container_box"></div>
			</div>
			</center>
			
			<div id="div_dark" class="Darken" style="display:none"></div>
			<div id="div_light" class="Lighten" align="center" style="display:none">
			<div id="div_content" style="background-color:white;width:600px;height:450px;padding:0 11px 11px 11px;overflow:auto">
			<div>
				<table border="0" cellspacing="10" class="nowidth">
					<tr>
						<td class="TDButton" onclick="goClose()"><img src="images/off.png"> Tutup</td>
					</tr>
				</table>
			
<?php
	// untuk grafik
	$a_semester = array();
	$a_skssemester = array();
	$a_ipksemester = array();
	$a_ipssemester = array();
	$a_nhuruf = array();
	$a_nhangka = array();
	$a_datanhuruf = array();
	
	$p_semmkmax = 0;
	
	foreach($a_data as $t_semester => $t_data) {
?>
<div id="div_semester_<?= $t_semester ?>" style="display:none;margin-bottom:20px">
<header>
	<div class="inner">
		<div class="left title">
			<img id="img_workflow" width="24px" src="images/aktivitas/KULIAH.png" onerror="loadDefaultActImg(this)">
			<h1>Matakuliah yang <?= $t_semester == '' ? 'Belum Diambil' : 'Diambil di Semester '.$t_semester ?></h1>
		</div>
	</div>
</header>
<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
	<tr>
		<th>No.</th>
		<th>Kode</th>
		<th>Nama Matakuliah</th>
		<th>SKS</th>
		<th>N.H</th>
		<th>N.A</th>
	</tr>
<?php
		$i = 0;
		$t_tsks = 0;
		$t_tsksips = 0;
		$t_tbobot = 0;
		foreach($t_data as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			
			$t_sks = (int)$row['sks'];
			$t_tsks += $t_sks;
			
			if(empty($row['nilaimasuk'])) {
				$t_nh = '&nbsp;';
				$t_bobot = '&nbsp;';
			}
			else {
				$t_tsksips += $t_sks;
				
				$t_bobot = $t_sks * (float)$row['nangka'];
				$t_tbobot += $t_bobot;
				
				$t_nh = trim($row['nhuruf']);
				$a_nhangka[$t_nh] = (float)$row['nangka'];
				$a_nhuruf[$t_nh]++;
				
				// data per huruf
				$a_datanhuruf[$t_nh][] = $row;
				
				if(empty($t_bobot))
					$t_nh = '<span style="color:red">'.$t_nh.'</span>';
			}
			
			if($row['semmk'] > $p_semmkmax)
				$p_semmkmax = $row['semmk'];
?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td><?= $i ?>.</td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $t_sks ?></td>
		<td align="center"><?= $t_nh ?></td>
		<td align="center"><?= $t_semester == '' ? '&nbsp;' : $t_bobot ?></td>
	</tr>
<?php
		}
		
		if($t_tsksips == 0)
			$t_ips = 0;
		else
			$t_ips = number_format(round($t_tbobot/$t_tsksips,2),2);
?>
	<tr>
		<th colspan="3">J U M L A H</th>
		<th><?= $t_tsks ?></th>
		<th>&nbsp;</th>
		<th><?= $t_semester == '' ? '&nbsp;' : $t_tbobot ?></th>
	</tr>
<?php
		if($t_semester != '') {
?>
	<tr>
		<th colspan="3">S K S / I P S</th>
		<th><?= $t_tsksips ?></th>
		<th colspan="2"><?= empty($t_tsksips) ? 0 : $t_ips ?></th>
	</tr>
<?php
		}
?>
</table>
</div>
<?php
		// untuk grafik
		if(!empty($t_semester)) {
			$a_semester[] = $t_semester;
			$a_skssemester[] = $t_tsks;
			$a_ipssemester[] = $t_ips;
			$a_ipksemester[] = (float)$a_index[$t_semester]['ipk'];
			
			$p_semmhsmax = $t_semester;
		}
	}
	
	// atur semester
	if($p_semmhsmax >= $p_semmkmax)
		$p_semmkmax = $p_semmhsmax + (empty($a_data['']) ? 0 : 1);
	
	foreach($a_datanhuruf as $t_nh => $t_data) {
		$t_nhid = str_replace('+','P',$t_nh);
?>
<div id="div_huruf_<?= $t_nhid ?>" style="display:none;margin-bottom:20px">
<header>
	<div class="inner">
		<div class="left title">
			<img id="img_workflow" width="24px" src="images/aktivitas/KULIAH.png" onerror="loadDefaultActImg(this)">
			<h1>Matakuliah dengan Nilai Huruf <?= $t_nh ?></h1>
		</div>
	</div>
</header>
<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
	<tr>
		<th>No.</th>
		<th>Kode</th>
		<th>Nama Matakuliah</th>
		<th>SKS</th>
		<th>N.H</th>
		<th>N.A</th>
	</tr>
<?php
		$i = 0;
		$t_tsks = 0;
		foreach($t_data as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			
			$t_sks = (int)$row['sks'];
			$t_bobot = $t_sks * (float)$row['nangka'];
			
			$t_tsks += $t_sks;
			$t_tbobot += $t_bobot;
?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td><?= $i ?>.</td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $t_sks ?></td>
		<td align="center"><?= $t_nh ?></td>
		<td align="center"><?= $t_bobot ?></td>
	</tr>
<?php
		}
?>
	<tr>
		<th colspan="3">J U M L A H</th>
		<th><?= $t_tsks ?></th>
		<th>&nbsp;</th>
		<th><?= $t_tbobot ?></th>
	</tr>
</table>
</div>
<?php
	}
	
	// menghitungan persentase nilai huruf
	arsort($a_nhangka);
	
	$t_jumlahnilai = 0;
	foreach($a_nhuruf as $t_nhuruf => $t_jumlah)
		$t_jumlahnilai += $t_jumlah;
	
	$a_nhurufpie = array();
	foreach($a_nhangka as $t_nhuruf => $t_nangka) {
		$t_jumlah = $a_nhuruf[$t_nhuruf];
		$a_nhurufpie[] = "'$t_nhuruf', ".round(($t_jumlah*100)/$t_jumlahnilai,2);
	}
?>
			
			</div>
			<form name="pageform" id="pageform" method="post">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
	
$(function () {
	var chart_bar, chart_sks, chart_ipk, chart_ips, chart_nh;
	
	$(document).ready(function() {
		chart_bar = new Highcharts.Chart({
			chart: {
				renderTo: 'container_bar',
				type: 'bar'
			},
			title: {
				text: 'Perkuliahan Mahasiswa'
			},
			xAxis: {
				categories: ['']
			},
			yAxis: {
				min: 0,
				max: <?= $p_semmkmax ?>,
				minTickInterval: 1,
				title: {
					text: 'Semester'
				}
			},
			tooltip: {
                formatter: function() {
                    return this.series.name;
                }
            },
			legend: {
				backgroundColor: '#FFFFFF',
				reversed: true
			},
			plotOptions: {
				series: {
					cursor: 'pointer',
					point: {
					   events: {
							click: function() {
								goShow('semcek',this.series.name);
							}
					   }
					},
					stacking: 'normal'
				}
			},
			colors: [
				'#2F7ED8', 
				'#F28F43', 
				'#8BBC21'
			],
			series: [{
				name: 'Belum ditempuh',
				data: [<?= $p_semmkmax-$p_semmhsmax ?>]
			}, {
				name: 'Sedang ditempuh',
				data: [1]
			}, {
				name: 'Sudah diselesaikan',
				data: [<?= $p_semmhsmax-1 ?>]
			}]
		});
		
		chart_ipk = new Highcharts.Chart({
            chart: {
                renderTo: 'container_ipk',
                type: 'line'
            },
            title: {
                text: 'IPK Mahasiswa',
                x: -20 //center
            },
            xAxis: {
				title: {
                    text: 'Semester'
                },
                categories: ['<?= implode("', '",$a_semester) ?>']
            },
            yAxis: {
				min: 0,
                title: {
                    text: 'IPK'
                }
            },
			tooltip: {
                formatter: function() {
                    return '<strong>' + this.series.name + ': </strong>' + this.y;
                }
            },
			plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    }
                },
				series: {
					cursor: 'pointer',
					point: {
					   events: {
							click: function() {
								goShow('semester',this.category);
							}
					   }
				   },
				   marker: {
					   lineWidth: 1
				   }
				}
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'IPK',
                data: [<?= implode(', ',$a_ipksemester) ?>]
            }]
        });
		
		chart_ips = new Highcharts.Chart({
            chart: {
                renderTo: 'container_ips',
                type: 'line'
            },
            title: {
                text: 'IPS Mahasiswa',
                x: -20 //center
            },
            xAxis: {
				title: {
                    text: 'Semester'
                },
                categories: ['<?= implode("', '",$a_semester) ?>']
            },
            yAxis: {
				min: 0,
                title: {
                    text: 'IPS'
                }
            },
			tooltip: {
                formatter: function() {
                    return '<strong>' + this.series.name + ': </strong>' + this.y;
                }
            },
			plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    }
                },
				series: {
					cursor: 'pointer',
					point: {
					   events: {
							click: function() {
								goShow('semester',this.category);
							}
					   }
				   },
				   marker: {
					   lineWidth: 1
				   }
				}
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'IPS',
                data: [<?= implode(', ',$a_ipssemester) ?>]
            }]
        });
		
		chart_sks = new Highcharts.Chart({
            chart: {
                renderTo: 'container_sks',
                type: 'line'
            },
            title: {
                text: 'SKS Mahasiswa',
                x: -20 //center
            },
            xAxis: {
				title: {
                    text: 'Semester'
                },
                categories: ['<?= implode("', '",$a_semester) ?>']
            },
            yAxis: {
				min: 0,
                title: {
                    text: 'SKS'
                }
            },
			tooltip: {
                formatter: function() {
                    return '<strong>' + this.series.name + ': </strong>' + this.y;
                }
            },
			plotOptions: {
                line: {
                    dataLabels: {
                        enabled: true
                    }
                },
				series: {
					cursor: 'pointer',
					point: {
					   events: {
							click: function() {
								goShow('semester',this.category);
							}
					   }
				   },
				   marker: {
					   lineWidth: 1
				   }
				}
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'SKS',
                data: [<?= implode(', ',$a_skssemester) ?>]
            }]
        });
		
		chart_nh = new Highcharts.Chart({
            chart: {
                renderTo: 'container_nh',
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
                },
				series: {
					cursor: 'pointer',
					point: {
					   events: {
							click: function() {
								goShow('huruf',this.name.replace(/\+/,'P'));
							}
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
});

function goShow(jenis,idx) {
	// atur posisi
	var padtop = $(window).height()/4;
	$("#div_light").css("padding-top",padtop);
	
	if(jenis == "semcek") {
		if(idx == 'Sudah diselesaikan')
			$("[id^=div_semester_]:lt(<?= $p_semmhsmax-1 ?>)").show();
		else if(idx == 'Sedang ditempuh')
			$("[id^=div_semester_]:eq(<?= $p_semmhsmax-1 ?>)").show();
		else
			$("#div_semester_").show();
	}
	else
		$("#div_"+jenis+"_"+idx).show();
	
	$("#div_dark").show();
	$("#div_light").show();
}

function goClose() {
	$("#div_light").hide();
	$("#div_dark").hide();
	
	$("[id^='div_semester_']").hide();
	$("[id^='div_huruf_']").hide();
}

</script>
</body>
</html>