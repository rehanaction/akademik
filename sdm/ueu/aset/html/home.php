<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// cek apakah sudah login
	if(!Modul::isAuthenticated())
		Route::redirect($conf['menu_path']);
	
	// include
	require_once(Route::getModelPath('pengumuman'));

	// ada aksi ganti role
	$r_aksi = $_REQUEST['act'];
	if($r_aksi == 'chgrole') {
		list($r_role,$r_unit) = explode(':',CStr::removeSpecial($_REQUEST['key']));
		
		Modul::changeRole($r_role,$r_unit);
	}

	$p_model = mPengumuman;
	
	$a_pengumuman = array();
	$a_pengumuman = $p_model::getPengumuman($conn);

    $c_viewdashboard = false;

	$r_role = Modul::getRole();
	if(in_array($r_role, array('A','admaset','karou','karm','rektor','sky'))){
	    $c_viewdashboard = true;
	}
	
	if($r_role == 'karm' or $r_role == 'admaset'){
		$c_notif = true;
		$c_notifhp = true;
	}

	if($r_role == 'prm'){
		$c_notifprm = true;
	}

	if($r_role == 'kadu'){
		$c_notifkadu = true;
	}
	
	if($r_role == 'kaproc'){
		$c_notifkaproc = true;
	}
	if($r_role == 'A' or $r_role == 'admaset' or $r_role == 'kadu' or $r_role == 'kakeu' or $r_role == 'kaproc' or $r_role == 'karm' or $r_role == 'karou'){
		$c_download = true;
	}
	// properti halaman
	$p_title = 'Selamat Datang di SIM Aset';
	
	// include
	require_once(Route::getModelPath('laporan'));
	if($c_viewdashboard){
        $a_data = array();
	    $a_data['brgperiode'] = mLaporan::getJmlAsetPeriode($conn);
	    $a_data['brgkelompok'] = mLaporan::getJmlAsetKelompok($conn);
	    $a_data['jmljenisperolehan'] = mLaporan::getJmlJenisPerolehan($conn);
	    $a_data['jmlperolehan'] = mLaporan::getJmlPerolehan($conn);
	    $a_data['jmlpermintaanhp'] = mLaporan::getJmlPermintaanHP($conn);
	    $a_data['jmlrawat'] = mLaporan::getUnverifiedRawat($conn);
	    $a_data['jmlpinjam'] = mLaporan::getUnverifiedPinjam($conn);
	    $a_data['jmlmutasi'] = mLaporan::getUnverifiedMutasi($conn);
	    $a_data['jmlhapus'] = mLaporan::getUnverifiedHapus($conn);
	    $a_data['jmlunrawat'] = mLaporan::getUnprocessedRawat($conn);
	    $a_data['jmlunpinjam'] = mLaporan::getUnprocessedPinjam($conn);
	    $a_data['jmlunmutasi'] = mLaporan::getUnprocessedMutasi($conn);
	    $a_data['jmlunhapus'] = mLaporan::getUnprocessedHapus($conn);
    }

    if($c_notifprm){
        $a_data['jmlpermintaanhp'] = mLaporan::getJmlPermintaanHP($conn);
    }

    if($c_notifkadu){
		$a_data['jmlunrawat'] = mLaporan::getUnprocessedRawat($conn);
	    $a_data['jmlunpinjam'] = mLaporan::getUnprocessedPinjam($conn);
	    $a_data['jmlunmutasi'] = mLaporan::getUnprocessedMutasi($conn);
	    $a_data['jmlunhapus'] = mLaporan::getUnprocessedHapus($conn);
    }
	
	if($c_notifkaproc)
		$a_data['jmlperolehan'] = mLaporan::getJmlPerolehan($conn);
	

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<div class="ViewTitle">Selamat Datang di SIM Aset Universitas Esa Unggul</div>
        </div>
        
        <div style="width:60%; margin:0; padding:0; float:left;">
        <? if($c_viewdashboard) { ?>
		<div class="SideItem" id="SideItem" style="width:100%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DASHBOARD.png" onerror="loadDefaultActImg(this)"> Dashboard
			</div>

		    <div class="chart" id="chart_jmlasetperiode" style="float:left;width:600px;height:250px;padding-right:10px;padding-top:10px;"></div>
		    <div class="chart" id="chart_jmljenisperolehan" style="float:left;width:600px;height:200px;padding-right:10px;padding-top:10px;"></div>
		    <div class="chart" id="chart_jmlasetkelompok" style="float:left;width:600px;height:200px;padding-right:10px;padding-top:10px;"></div>
		</div>
        <? } ?>
        	<div class="SideItem" style="width:100%">
		    <div class="LeftRibbon">
			    <img width="24px" src="images/aktivitas/NEWS.png" onerror="loadDefaultActImg(this)"> Pengumuman
		    </div>
		    <table class="NewsList" width="100%" cellspacing="0" cellpadding="0">
				<tr>
					<td class="SideTitle" colspan="2">Daftar Pengumuman<div class="Break"></div><div class="Break"></div></td>
				</tr>
				<?	
				if (count($a_pengumuman) > 0) {
					foreach($a_pengumuman as $row) { ?>
				<tr>
					<td align="right" colspan="2">Posting : <?= CStr::formatDateInd($row['tglmulai']) ?></td>
				</tr>
				<tr style="border-bottom:1px solid black">
					<td colspan="2">
					<div class="Break"></div>
					<div class="SideSubTitle"><?= $row['judulpengumuman'] ?></div>
					<div class="Break"></div>
					<div class="NewsContent"><?= CStr::cBrief($row['isipengumuman']) ?></div>
					<div class="Break"></div>
					<u class="ULink" onclick="javascript:openDetail('<?= $row['idpengumuman'] ?>')">Selengkapnya...</u>
					<div class="Break"></div><div class="Break"></div>
					</td>
				</tr>
				<?	}} ?>
			</table>
			<br>
	    </div>
        
        </div>
        
		<div class="SideItem" style="float:right;width:30%">
		    <div class="LeftRibbon">
			    <img width="24px" src="images/aktivitas/INFO.png" onerror="loadDefaultActImg(this)"> Informasi
		    </div>
		    <table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="3">
						<img width="16px" src="images/aktivitas/BIODATA.png" onerror="loadDefaultActImg(this)">
						&nbsp; <?= Modul::getUserName() ?> - <?= Modul::getUserDesc() ?>,
						<br><span class="SideSubTitle">Login : </span> <?= CStr::formatDateTimeInd(Modul::getLastLogin(),false,true) ?>
					</td>
				</tr>
			</table>
		</div>

	    <div class="SideItem" style="float:right;width:30%">
		    <div class="LeftRibbon">
			    <img width="24px" src="images/aktivitas/INFO.png" onerror="loadDefaultActImg(this)"> Notifikasi
		    </div>
		    <table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td colspan="3">
						<? if($c_notif) { ?>
							<? if(!empty($a_data['jmlrawat'])) { ?>
								<br><span class="SideTitle blink">Perawatan Belum Verifikasi : <?= $a_data['jmlrawat'] ?> </span>
							<? } ?>
							<? if(!empty($a_data['jmlpinjam'])) { ?>
								<br><span class="SideTitle blink">Peminjaman Belum Verifikasi : <?= $a_data['jmlpinjam'] ?> </span>
							<? } ?>
							<? if(!empty($a_data['jmlmutasi'])) { ?>
								<br><span class="SideTitle blink">Mutasi Belum Verifikasi : <?= $a_data['jmlmutasi'] ?> </span>
							<? } ?>
							<? if(!empty($a_data['jmlhapus'])) { ?>
								<br><span class="SideTitle blink">Penghapusan Belum Verifikasi : <?= $a_data['jmlhapus'] ?> </span>
							<? } ?>
						<br><span class="SideTitle blink">Perolehan Belum Verifikasi : <?= $a_data['jmlperolehan'] ?> </span>
						<? } ?>
						<? if($c_notifkadu) { ?>
							<? if(!empty($a_data['jmlunrawat'])) { ?>
							<br><span class="SideTitle blink">Perawatan Belum Proses : <?= $a_data['jmlunrawat'] ?></span>
							<? } ?>
							<? if(!empty($a_data['jmlunpinjam'])) { ?>
							<br><span class="SideTitle blink">Peminjaman Belum Proses : <?= $a_data['jmlunpinjam'] ?></span>
							<? } ?>
							<? if(!empty($a_data['jmlunmutasi'])) { ?>
							<br><span class="SideTitle blink">Mutasi Belum Proses : <?= $a_data['jmlunmutasi'] ?></span>
							<? } ?>
							<? if(!empty($a_data['jmlunhapus'])) { ?>
							<br><span class="SideTitle blink">Penghapusan Belum Proses : <?= $a_data['jmlunhapus'] ?></span>
							<? } ?>
						<? } ?>
						<? if($c_notifkaproc) { ?>
							<? if(!empty($a_data['jmlperolehan'])) { ?>
							<br><span class="SideTitle blink">Perolehan Belum Verifikasi : <?= $a_data['jmlperolehan'] ?> </span>
							<? } ?>
						<? } ?>
						<? if($c_notifhp or $c_notifprm) { ?>
						<br><span class="SideTitle blink">Permintaan Habis Pakai (ATK) : <?= $a_data['jmlpermintaanhp'] ?> </span>
						<? } ?>
					</td>
				</tr>
			</table>
		</div>
		
		<div class="SideItem" style="float:right;width:30%">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DOCUMENT.png" onerror="loadDefaultActImg(this)"> USER GUIDE
			</div>
			<table width="100%" cellpadding="4" cellspacing="0">
				<tr>
					<td>
						<?if($c_download){?>
						<u class="ULink" onclick="javascript:goDownloadUG('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','admin')" target="_blank"><img width="16px" src="images/aktivitas/DOWNLOAD_FILE.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download Transaksi Aset (Admin) </span></u> <br />
						<?}?>
						<u class="ULink" onclick="javascript:goDownloadUG('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','unit')"><img width="16px" src="images/aktivitas/DOWNLOAD_FILE.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download Transaksi Aset (Pengajuan) </span></u>
                        <br><u class="ULink" onclick="javascript:goDownloadUG('<?= Route::navAddress('download&_auto=1&_ocd=').base64_encode('ug'); ?>','peg')"><img width="16px" src="images/aktivitas/DOWNLOAD_FILE.png" onerror="loadDefaultActImg(this)"> <span class="SideSubTitle"> Download Transaksi Habis Pakai </span></u>
					</td>
				</tr>
			</table>
		</div>
		
	    

	</div>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<? if(count($a_data) > 0){ ?>
<script type="text/javascript">
	var detform = "<?= Route::navAddress('pop_pengumuman') ?>";
	
$(function () {
    var chart_jmlasetperiode, chart_jmlasetkelompok, chart_jmljenisperolehan;
	
    $(document).ready(function() {
        chart_jmlasetperiode = new Highcharts.Chart({
            chart: {
                renderTo: 'chart_jmlasetperiode',
                type: 'column'
            },
            title: {
                text: 'Jumlah aset per periode'
            },
            xAxis: {
                categories: ['<?= implode($a_data["brgperiode"][0],"','") ?>'],
				labels: {
                    rotation: -25,
                    align: 'center',
                    style: {
                        fontSize: '10px',
                        fontFamily: 'Verdana, sans-serif'
                    }
                }
            },
            yAxis: {
                title: {
                    text: 'Jumlah aset'
                }
            },
			legend: {
                enabled: false
            },
            series: [{
				    name: 'Jumlah',
				    data: [<?= implode($a_data["brgperiode"][1],",") ?>],
					dataLabels: {
						enabled: true,
						rotation: -70,
						color: '#FFFF00',
						align: 'center',
						x: 4,
						y: 30,
						style: {
							fontSize: '13px',
							fontFamily: 'Verdana, sans-serif',
							textShadow: '0 0 3px black',
							fontWeight: 'bold'
						}
					}
		    }]
        });
        
        chart_jmlasetkelompok = new Highcharts.Chart({
            chart: {
                renderTo: 'chart_jmlasetkelompok',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Persentase per kelompok barang'
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
						distance: 5,
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Persentase per kelompok',
                data: [<? 
                        $i = 0;
                        foreach($a_data['brgkelompok'][0] as $brg => $val){
                            $data = '';
                            $jml = (int)$a_data['brgkelompok'][1][$brg];
                            
                            if($i > 0)
                                $data .= ',';
                            $data .= "['$val',$jml]";

                            if($jml > 0){
                                echo $data;
                                $i++;
                            }
                        } 
                    ?>]
            }]
        });

        chart_jmljenisperolehan = new Highcharts.Chart({
            chart: {
                renderTo: 'chart_jmljenisperolehan',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Total per jenis perolehan'
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
						distance: 5,
                        formatter: function() {
                            return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
                        }
                    }
                }
            },
            series: [{
                type: 'pie',
                name: 'Total per jenis perolehan',
                data: [<? 
                        $i = 0;
                        foreach($a_data['jmljenisperolehan'][0] as $id => $jns){
                            $data = '';
                            $jml = (int)$a_data['jmljenisperolehan'][1][$id];
                            
                            if($i > 0)
                                $data .= ',';
                            $data .= "['$jns',$jml]";

                            echo $data;
                            $i++;
                        } 
                    ?>]
            }]
        });
        
    });
    
});

function openDetail(pkey){
	$.ajax({
		url: detform,
		type: "POST",
		data: {key : pkey},
		success: function(data){
			$.facybox(data);
		}
	});
}

/**
 * Grid theme for Highcharts JS
 * @author Torstein HÃ¸nsi
 */

Highcharts.theme = {
	colors: ['#058DC7', '#50B432', '#ED561B', '#DDDF00', '#24CBE5', '#64E572', '#FF9655', '#FFF263', '#6AF9C4'],
	chart: {
		backgroundColor: {
			linearGradient: { x1: 0, y1: 0, x2: 1, y2: 1 },
			stops: [
				[0, 'rgb(255, 255, 255)'],
				[1, 'rgb(240, 240, 255)']
			]
		},
		borderWidth: 2,
		plotBackgroundColor: 'rgba(255, 255, 255, .9)',
		plotShadow: true,
		plotBorderWidth: 1
	},
	title: {
		style: {
			color: '#000',
			font: 'bold 16px "Trebuchet MS", Verdana, sans-serif'
		}
	},
	subtitle: {
		style: {
			color: '#666666',
			font: 'bold 12px "Trebuchet MS", Verdana, sans-serif'
		}
	},
	xAxis: {
		gridLineWidth: 1,
		lineColor: '#000',
		tickColor: '#000',
		labels: {
			style: {
				color: '#000',
				font: '11px Trebuchet MS, Verdana, sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Trebuchet MS, Verdana, sans-serif'

			}
		}
	},
	yAxis: {
		minorTickInterval: 'auto',
		lineColor: '#000',
		lineWidth: 1,
		tickWidth: 1,
		tickColor: '#000',
		labels: {
			style: {
				color: '#000',
				font: '11px Trebuchet MS, Verdana, sans-serif'
			}
		},
		title: {
			style: {
				color: '#333',
				fontWeight: 'bold',
				fontSize: '12px',
				fontFamily: 'Trebuchet MS, Verdana, sans-serif'
			}
		}
	},
	legend: {
		itemStyle: {
			font: '9pt Trebuchet MS, Verdana, sans-serif',
			color: 'black'

		},
		itemHoverStyle: {
			color: '#039'
		},
		itemHiddenStyle: {
			color: 'gray'
		}
	},
	labels: {
		style: {
			color: '#99b'
		}
	},

	navigation: {
		buttonOptions: {
			theme: {
				stroke: '#CCCCCC'
			}
		}
	}
};

// Apply the theme
var highchartsOptions = Highcharts.setOptions(Highcharts.theme);

</script>

<? } ?>

</body>
</html>
