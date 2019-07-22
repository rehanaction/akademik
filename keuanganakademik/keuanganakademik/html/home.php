<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::redirect($conf['menu_path']);
	
	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));
		
		Modul::changeRole($r_role,$r_unit);
	}
	
	
	
	// include
	//require_once(Route::getModelPath('berita'));
	
	// properti halaman
	$p_title = 'Selamat Datang di SIM Keuangan Akademik';
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" style="float:left;width:60%;">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/BERITA.png" onerror="loadDefaultActImg(this)"> Pengumuman
			</div>
			<?
			if($a_pengumuman)	
			foreach($a_pengumuman as $row) { ?>
			<div id="div_pengumuman" style="height:240px;display:none">
				<img src="<?= Route::navAddress('img_datathumb&type='.mBerita::uptype.'&id='.$row['idberita']) ?>">
				<div class="Break"></div>
				<div class="SideTitle"><?= $row['judulberita'] ?></div>
				<div class="Break"></div>
				<div class="NewsContent"><?= CStr::cBrief($row['isi']) ?></div>
				<div class="Break"></div>
				<u class="ULink" onclick="javascript:goDetail('<?= $row['idberita'] ?>')">Selengkapnya...</u>
			</div>
			<?	} ?>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideSubTitle" colspan="2">Daftar Pengumuman</td>
				</tr>
			<?	if($a_pengumuman)
			foreach($a_pengumuman as $row) { ?>
				<tr>
					<td><u class="ULink" id="u_pengumuman" onclick="javascript:goDetail('<?= $row['idberita'] ?>')"><?= $row['judulberita'] ?></u></td>
					<td align="right"><?= CStr::formatDateDiff($row['waktuvalid']) ?></td>
				</tr>
			<?	} ?>
			</table>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/BERITA.png" onerror="loadDefaultActImg(this)"> Tagihan</div>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideSubTitle" colspan="2">Daftar Tagihan</td>
				</tr>
			<?
			if($a_berita)		
			foreach($a_berita as $row) { ?>
				<tr>
					<td><u class="ULink" id="u_berita" onclick="javascript:goDetail('<?= $row['idberita'] ?>')"><?= $row['judulberita'] ?></u></td>
					<td align="right"><?= CStr::formatDateDiff($row['waktuvalid']) ?></td>
				</tr>
			<?	} ?>
			</table>
		</div>
		<div class="SideItem" style="width:30%;float:right;">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)"> <?= CStr::formatDateTimeInd(date('Y-m-d'),false,true) ?>
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="3" style="border-bottom:1px solid #647287">
						<img width="16px" src="images/aktivitas/ADMIN.png" onerror="loadDefaultActImg(this)">
						&nbsp; <?= Modul::getUserName() ?> - <?= Modul::getUserDesc() ?>
					</td>
				</tr>
				<tr class="Break">
					<td></td>
				</tr>
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/HISTORI.png" onerror="loadDefaultActImg(this)">
						&nbsp; <span class="SideSubTitle">Login Terakhir:</span> <?= CStr::formatDateTimeInd(Modul::getLastLogin(),false,true) ?>
					</td>
				</tr>
			</table>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
	
var idxberita = -1;
var idxpengumuman = -1;

$(document).ready(function() {
	chooseBerita(0);
	choosePengumuman(0);
	
	$("[id='u_berita'] ").mouseover(function() {
		var idx = $("[id='u_berita']").index(this);
		chooseBerita(idx);
	});
	$("[id='u_pengumuman'] ").mouseover(function() {
		var idx = $("[id='u_pengumuman']").index(this);
		choosePengumuman(idx);
	});
	
	<?	if($p_role == 'M') { ?>
	var chart_ipk, chart_ips, chart_sks;
	
    $(document).ready(function() {
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
                categories: [<?= implode(',',$a_semester) ?>]
            },
            yAxis: {
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
                }
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'IPK',
                data: [<?= implode(',',$a_ipk) ?>]
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
                categories: [<?= implode(',',$a_semester) ?>]
            },
            yAxis: {
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
                }
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'IPS',
                data: [<?= implode(',',$a_ips) ?>]
            }]
        });
		
		chart_sks = new Highcharts.Chart({
            chart: {
                renderTo: 'container_sks',
                type: 'line'
            },
            title: {
                text: 'Pengambilan SKS',
                x: -20 //center
            },
            xAxis: {
				title: {
                    text: 'Semester'
                },
                categories: [<?= implode(',',$a_semester) ?>]
            },
            yAxis: {
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
                }
            },
			legend: {
                enabled: false
            },
            series: [{
                name: 'SKS',
                data: [<?= implode(',',$a_sks) ?>]
            }]
        });
	});
	
	<?	} ?>
});

function chooseBerita(idx) {
	if(idx != idxberita) {
		idxberita = idx;
		
		$("[id='div_berita']").hide();
		$("[id='div_berita']:eq("+idx+")").show(); // fadeIn(); // efeknya jelek
	}
}

function choosePengumuman(idx) {
	if(idx != idxpengumuman) {
		idxpengumuman = idx;
		
		$("[id='div_pengumuman']").hide();
		$("[id='div_pengumuman']:eq("+idx+")").show(); // fadeIn(); // efeknya jelek
	}
}

function goDetail(id) {
	goOpen('view_berita&key='+id);
}

function goInbox() {
	goOpen('list_inbox');
}

function goTugas() {
	goOpen('list_tugas');
}

function goBerita() {
	goOpen('list_berita');
}

function goDiskusi(elem) {
	goOpen('list_subdiskusikelas&key='+elem.id);
}

</script>

</body>
</html>
