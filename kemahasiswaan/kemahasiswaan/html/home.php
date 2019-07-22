<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// cek apakah sudah login
	if(!Modul::isAuthenticated()){//echo "b".die();
		Route::redirect($conf['menu_path']);}
//echo "Debug Mode"; die;
	//echo "a".die();
	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));

		Modul::changeRole($r_role,$r_unit);
	}
	//echo "a".die();
	// include
	require_once(Route::getModelPath('berita'));

	// properti halaman
	$p_title = 'Selamat Datang di SIM Akademik';

	// mendapatkan data
	$a_pengumuman = mBerita::getListPengumuman($conn);
	$a_berita = mBerita::getListBerita($conn);

	$u_role=Modul::getRole();
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
			<?	foreach($a_pengumuman as $row) { ?>
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
			<?	foreach($a_pengumuman as $row) { ?>
				<tr>
					<td><u class="ULink" id="u_pengumuman" onclick="javascript:goDetail('<?= $row['idberita'] ?>')"><?= $row['judulberita'] ?></u></td>
					<td align="right"><?= CStr::formatDateDiff($row['waktuvalid']) ?></td>
				</tr>
			<?	} ?>
			</table>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/BERITA.png" onerror="loadDefaultActImg(this)"> Berita
			</div>
			<?	foreach($a_berita as $row) { ?>
			<div id="div_berita" style="height:240px;display:none">
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
					<td class="SideSubTitle" colspan="2">Daftar Berita</td>
				</tr>
			<?	foreach($a_berita as $row) { ?>
				<tr>
					<td><u class="ULink" id="u_berita" onclick="javascript:goDetail('<?= $row['idberita'] ?>')"><?= $row['judulberita'] ?></u></td>
					<td align="right"><?= CStr::formatDateDiff($row['waktuvalid']) ?></td>
				</tr>
			<?	} ?>
			</table>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/FORUM.png" onerror="loadDefaultActImg(this)"> Diskusi
			</div>
			<table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideSubTitle" colspan="2">Diksusi Terbaru</td>
				</tr>
			<?	foreach($a_diskusi as $row) {
					list($t_waktuposting,$t_user) = explode('|',$row['max']);
			?>
				<tr>
					<td><u id="<?= $row['idforum'] ?>" class="ULink" onclick="javascript:goDiskusi(this)"><?= $row['judulforum'] ?></u></td>
					<td align="right"><?= $t_user ?>, <?= CStr::formatDateDiff($t_waktuposting) ?></td>
				</tr>
			<?	} ?>
			</table>
		</div>

		<?	if($p_role == 'M') { ?>
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
				<!--tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/ABSENSI.png" onerror="loadDefaultActImg(this)">
						&nbsp; <span class="SideSubTitle">Status:</span> <?= $a_infomhs['namastatus'] ?>,
						<span class="SideSubTitle">Semester:</span> <?= $a_infomhs['semestermhs'] ?>,
						<span class="SideSubTitle">IPK:</span> <?= $a_infomhs['ipk'] ?>,
						<span class="SideSubTitle">SKS Lulus:</span> <?= $a_infomhs['skslulus'] ?>,
						<span class="SideSubTitle">IPS Lalu:</span> <?= $a_infomhs['ipslalu'] ?>
					</td>
				</tr-->
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/BARU.png" onerror="loadDefaultActImg(this)">
						&nbsp; <u class="ULink" onclick="goInbox()"><?= $n_pesan ?> pesan</u> belum dibaca,
						<u class="ULink" onclick="goTugas()"><?= $n_tugas ?> tugas</u> belum dikumpulkan
					</td>
				</tr>
				<tr class="Break">
					<td></td>
				</tr>
				<tr>
					<td colspan="3" style="border-bottom:1px solid #647287">
						<img width="16px" src="images/aktivitas/JADWAL2.png" onerror="loadDefaultActImg(this)">
						&nbsp; <span class="SideSubTitle">Jadwal Kuliah</span>
					</td>
				</tr>
				<?	if(empty($a_jadwal)) { ?>
				<tr>
					<td colspan="3">(Tidak ada jadwal)</td>
				</tr>
				<?	}
					foreach($a_jadwal as $t_jadwal) {
						foreach($t_jadwal as $row) { ?>
				<tr>
					<td width="80"><?= $row['jam'] ?></td>
					<td><?= $row['mk'] ?></td>
					<td><?= $row['koderuang'] ?></td>
				</tr>
				<?		}
					} ?>
			</table>
			<br>
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DOCUMENT.png" onerror="loadDefaultActImg(this)"> USER GUIDE
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td>
						<u class="ULink" onclick="goView('download&type=userguide&id=M')"><img width="16px" src="images/aktivitas/DOWNLOAD_FILE.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Mahasiswa </span></u> <br />
					</td>
				</tr>
			</table>
			<br>
			<!--div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/GRAFIK.png" onerror="loadDefaultActImg(this)"> Kemajuan Belajar
			</div>
			<div id="container_ipk" style="height:200px"></div>
			<br>
			<div id="container_ips" style="height:200px"></div>
			<br>
			<div id="container_sks" style="height:200px"></div-->
		</div>
		<?	} else  { ?>
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
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/BARU.png" onerror="loadDefaultActImg(this)">
						&nbsp; <u class="ULink" onclick="goInbox()"><?= $n_pesan ?> pesan</u> belum dibaca,
						<u class="ULink" onclick="goBerita()"><?= $n_berita ?> berita</u> belum divalidasi
					</td>
				</tr>
			</table>
		</div>
		<div class="SideItem" style="width:30%;float:right;">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DOCUMENT.png" onerror="loadDefaultActImg(this)"> USER GUIDE
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td>
						<u class="ULink" onclick="goView('download&type=userguide&id=<?=$u_role?>')"><img width="16px" src="images/aktivitas/DOWNLOAD_FILE.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download </span></u> <br />
						<?php /*if($u_role=='PR'){ ?>
						<u class="ULink" onclick="goView('download&type=guide&id=guide_tuprodi')"><img width="16px" src="images/aktivitas/DOWNLOAD_FILE.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Dosen </span></u> <br />
						<?php } */?>
					</td>
				</tr>
			</table>
		</div>
		<?	} ?>

		<div style="clear:both"></div>
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
