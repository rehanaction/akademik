<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	require_once(Route::getModelPath('laporan'));
	/*
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::redirect($conf['menu_path']);
	*/
	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));
		
		Modul::changeRole($r_role,$r_unit);
	}
	$a_data = mLaporan::InquiryPendaftarPeriode($conn);
	
	// properti halaman
	$p_title = 'Selamat Datang di SIM Penerimaan Mahasiswa Baru';
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<script src="scripts/chartjs/Chart.bundle.js"></script>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style type="text/css">
            .container {
                width: 50%;
                margin: 15px auto;
            }
        </style>
</head>
<body>
<?/*
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
						<img width="16px" src="images/aktivitas/BIODATA.png" onerror="loadDefaultActImg(this)">
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
						<img width="16px" src="images/aktivitas/ABSENSI.png" onerror="loadDefaultActImg(this)">
						&nbsp; <span class="SideSubTitle">Status:</span> <?= $a_infomhs['namastatus'] ?>,
						<span class="SideSubTitle">Semester:</span> <?= $a_infomhs['semestermhs'] ?>,
						<span class="SideSubTitle">IPK:</span> <?= $a_infomhs['ipk'] ?>,
						<span class="SideSubTitle">SKS Lulus:</span> <?= $a_infomhs['skslulus'] ?>,
						<span class="SideSubTitle">IPS Lalu:</span> <?= $a_infomhs['ipslalu'] ?>
					</td>
				</tr>
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
						<img width="16px" src="images/aktivitas/JADWAL.png" onerror="loadDefaultActImg(this)">
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
				<img width="24px" src="images/aktivitas/GRAFIK.png" onerror="loadDefaultActImg(this)"> Kemajuan Belajar
			</div>
			<div id="container_ipk" style="height:200px"></div>
			<br>
			<div id="container_ips" style="height:200px"></div>
			<br>
			<div id="container_sks" style="height:200px"></div>
		</div>
		<?	} else if($p_role == 'A') { ?>
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
		<?	} ?>
	</div>
</div>
*/?>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
	<div class="SideItem" id="SideItem">
			<div class="ViewTitle">Chart Penerimaan Mahasiswa Baru</div>
			<br>
			<div class="container">
            <canvas id="myChart" width="100" height="100"></canvas>
        </div>
        <script>
            var ctx = document.getElementById("myChart");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php foreach($a_data as $val){ echo '"' . pendaftaran::getNamaPeriode($val['periodedaftar']) . '",'; }?> ],
                    datasets: [{
                            label: 'Jumlah Pendaftar Nim ',
                            data: [<?php foreach($a_data as $val2){echo $val2['bayar'].','; } ?>],
                            backgroundColor: [
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        },
                        {
                            label: 'Jumlah Pendaftar',
                            data: [<?php foreach($a_data as $val2){echo $val2['pendaftar'].','; } ?>],
                            backgroundColor: [
                                'rgba(255, 246, 143, 1)',
                                'rgba(255, 246, 143, 1)',
                                'rgba(255, 246, 143, 1)',
                                'rgba(75, 192, 192, 0.2)',
                                'rgba(153, 102, 255, 0.2)',
                                'rgba(255, 159, 64, 0.2)'
                            ],
                            borderColor: [
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(75, 192, 192, 1)',
                                'rgba(153, 102, 255, 1)',
                                'rgba(255, 159, 64, 1)'
                            ],
                            borderWidth: 1
                        }
                        
                        ]
                },
                options: {
                    scales: {
                        yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                    }
                }
            });
        </script>
			<div class="container">
            <canvas id="myChart2" width="100" height="100"></canvas>
        </div>
        <script>
            var ctx = document.getElementById("myChart2");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php foreach($a_data as $val){ echo '"' . pendaftaran::getNamaPeriode($val['periodedaftar']) . '",'; }?> ],
                    datasets: [{
                            label: 'Jumlah Pendaftar Nim ',
                            data: [<?php foreach($a_data as $val2){echo round((($val2['bayar']/$val2['pendaftar'])*100)).','; } ?>],
                            backgroundColor: [
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)'
                               
                            ],
                            borderColor: [
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                
                            ],
                            borderWidth: 1
                        }
                      
                        
                        ]
                },
                options: {
                    scales: {
                        yAxes: [{
                                ticks: {
                                    beginAtZero: true
                                }
                            }]
                    }
                }
            });
        </script>
		</div>
		<div class="SideItem" id="SideItem">
			<div class="ViewTitle">Selamat Datang di SIM Penerimaan Mahasiswa Baru</div>
			<br>
			<table class="filterTable" width="100%">
				<tr>
					<td>
						Assalamualaikum Wr. Wb.<br><br>
						Selamat Datang di Sistem Informasi Manajemen Pendaftaran Mahasiswa Baru STIE INABA. Di sini Anda dapat melihat dan mengelola semua data yang berhubungan dengan pendaftaran mahasiswa baru selama Anda menjadi mahasiswa STIE INABA.<br><br>	
						Silakan memanfaatkan semua fasilitas yang disedikan oleh sistem ini dengan semestinya. Jika Anda menemui kesalahan entry data, masukan nama maupun error pada program, Anda dapat menyampaikan keluhan tersebut kepada pihak Biro Teknologi & Informasi.<br><br>
						Wassalamualaikum Wr. Wb. 
					</td>
				</tr>
			</table>
			
		</div>

	</div>

</div>
</body>
</html>
