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

    $c_viewdashboard = false;

	$r_role = Modul::getRole();
	if(in_array($r_role, array('A','admaset','karou','karm','rektor','sky'))){
	    $c_viewdashboard = true;
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
    }
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
		<div class="SideItem" id="SideItem">
			<div class="ViewTitle">Selamat Datang di SIM Aset Universitas Esa Unggul</div>
        </div>
        <? if(count($a_data) > 0){ ?>
		<div class="SideItem" id="SideItem">
			<div class="LeftRibbon">
				<img width="24px" src="images/aktivitas/DASHBOARD.png" onerror="loadDefaultActImg(this)"> Dashboard
			</div>

		    <div class="chart" id="chart_jmlasetperiode" style="float:left;width:600px;height:250px;padding-right:10px;padding-top:10px;"></div>
		    <div class="chart" id="chart_jmljenisperolehan" style="float:left;width:350px;height:250px;padding-right:10px;padding-top:10px;"></div>
		    <div class="chart" id="chart_jmlasetkelompok" style="float:left;width:500px;height:250px;padding-right:10px;padding-top:10px;"></div>
		</div>
        <? } ?>
	    <div class="SideItem" style="width:60%">
		    <div class="LeftRibbon">
			    <img width="24px" src="images/aktivitas/NEWS.png" onerror="loadDefaultActImg(this)"> Pengumuman
		    </div>
		    <table class="filterTable" width="100%">
			    <tr>
				    <td>
					    Assalamualaikum Wr. Wb.
					    <br><br>
					    Selamat Datang di Sistem Informasi Manajemen Aset Universitas Esa Unggul. 
					    Dengan adanya sistem ini diharapkan dapat mempermudah bapak/ibu sekalian dalam mengelola aset secara cepat, tepat, dan akurat
					    <br><br>
					    Wassalamualaikum Wr. Wb. 
				    </td>
			    </tr>
		    </table>
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

	</div>
</div>
<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<? if(count($a_data) > 0){ ?>
<script type="text/javascript">
$(function () {
    var chart_jmlasetperiode, chart_jmlasetkelompok, chart_jmljenisperolehan;
	
    $(document).ready(function() {
        chart_jmlasetperiode = new Highcharts.Chart({
            chart: {
                renderTo: 'chart_jmlasetperiode',
                type: 'area'
            },
            title: {
                text: 'Jumlah aset per periode'
            },
            xAxis: {
                categories: ['<?= implode($a_data["brgperiode"][0],"','") ?>']
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
				    data: [<?= implode($a_data["brgperiode"][1],",") ?>]
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
                name: 'Total per kelompok',
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
</script>

<? } ?>

</body>
</html>
