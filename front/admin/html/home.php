<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
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
	require_once(Route::getModelPath('usersession'));
	
	// properti halaman
	$p_title = 'Selamat Datang di Administrasi SIM';
	
	// mendapatkan data
	$a_data = mUserSession::getHomeGraphData($conn);
?>
<!DOCTYPE html>
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
	<?php require_once('inc_header.php') ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
				<div id="container_login" style="width:900px;height:300px"></div>
				<br>
				<div style="width:900px">
					<div id="container_waktu" style="float:left;width:440px;height:350px;padding-right:10px"></div>
					<div id="container_browser" style="float:left;width:440px;height:350px;padding-left:10px"></div>
				</div>
			</center>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript">
	
$(function () {
    var chart_login, chart_waktu, chart_browser;
	
    $(document).ready(function() {
        chart_login = new Highcharts.Chart({
            chart: {
                renderTo: 'container_login',
                type: 'area'
            },
            title: {
                text: 'Grafik user login 2 minggu terakhir'
            },
            xAxis: {
                type: 'datetime',
				dateTimeLabelFormats: {
					day: '%e %b %Y'   
				}
            },
            yAxis: {
                title: {
                    text: 'Jumlah user login'
                }
            },
			legend: {
                enabled: false
            },
            series: [{
				name: 'User Login',
				data: [<?= $a_data['login'] ?>],
				pointStart: Date.UTC(<?= $a_data['mulai'] ?>),
				pointInterval: 24 * 3600 * 1000
			}]
        });
		
		chart_waktu = new Highcharts.Chart({
            chart: {
                renderTo: 'container_waktu',
                plotBackgroundColor: null,
                plotBorderWidth: null,
                plotShadow: false
            },
            title: {
                text: 'Perbandingan Waktu Login (WIB)'
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
                name: 'Perbandingan Waktu Login',
                data: [<?= $a_data['waktu'] ?>]
            }]
        });
		
        var colors = Highcharts.getOptions().colors,
            categories = [<?= $a_data['web']['browser'] ?>],
            name = 'Merk browser',
            data = [
			<?	$n = count($a_data['web']['detail']);
				for($i=0;$i<$n;$i++) {
					$t_data = $a_data['web']['detail'][$i];
			?>
				{
					y: <?= $t_data['y'] ?>,
                    color: colors[<?= $i ?>],
                    drilldown: {
                        name: 'Versi <?= $t_data['browser'] ?>',
                        categories: [<?= $t_data['kategori'] ?>],
                        data: [<?= $t_data['data'] ?>],
                        color: colors[<?= $i ?>]
					}
				}<?= ($i < $n-1) ? ',' : ''  ?>
			<?	} ?>
			];
		
        // Build the data arrays
        var browserData = [];
        var versionsData = [];
        for (var i = 0; i < data.length; i++) {
            // add browser data
            browserData.push({
                name: categories[i],
                y: data[i].y,
                color: data[i].color
            });
			
            // add version data
            for (var j = 0; j < data[i].drilldown.data.length; j++) {
                var brightness = 0.2 - (j / data[i].drilldown.data.length) / 5 ;
                versionsData.push({
                    name: data[i].drilldown.categories[j],
                    y: data[i].drilldown.data[j],
                    color: Highcharts.Color(data[i].color).brighten(brightness).get()
                });
            }
        }
    
        // Create the chart
        chart_browser = new Highcharts.Chart({
            chart: {
                renderTo: 'container_browser',
                type: 'pie'
            },
            title: {
                text: 'Perbandingan Penggunaan Browser'
            },
            yAxis: {
                title: {
                    text: 'Penggunaan browser'
                }
            },
            plotOptions: {
                pie: {
                    shadow: false
                }
            },
            tooltip: {
        	    valueSuffix: '%'
            },
            series: [{
                name: 'Browser',
                data: browserData,
                size: '60%',
                dataLabels: {
                    formatter: function() {
                        return this.y > 5 ? this.point.name : null;
                    },
                    color: 'white',
                    distance: -30
                }
            }, {
                name: 'Versi',
                data: versionsData,
                innerSize: '60%',
                dataLabels: {
					distance: 5,
                    formatter: function() {
                        // display only if larger than 1
                        return this.y > 1 ? '<b>'+ this.point.name +':</b> '+ this.y +'%'  : null;
                    }
                }
            }]
        });
    });
    
});
	
</script>

</body>
</html>