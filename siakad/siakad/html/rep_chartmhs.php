<?php
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
//$conn->debug= true;
// hak akses
Modul::getFileAuth();
// include
require_once(Route::getModelPath('laporan'));

$p_model = mLaporan;
$a_data = $p_model::getChartStatusMhs($conn);

$data_chart = array();
$detail_chart = array();
$periode = '';
foreach($a_data as $value){
    if($value['periode']==$periode)
    {
        $detail_chart[$periode][$value['statusmhs']]=$value['jumlah'];
        //$data_chart[$periode]=$detail_chart[$value['statusmhs']];
    }else{
        $detail_chart[$value['periode']][$value['statusmhs']]=$value['jumlah'];
        //$detail_chart[$value['statusmhs']]=$value['jumlah'];
        //$data_chart[$value['periode']]=$detail_chart[$value['statusmhs']];
    }
    $periode = $value['periode'];
   
}
$p_title = 'Transkrip Akademik';
$p_tbwidth = 900;
//print_r($detail_chart);

?>
<html>
    <head>
        <title>Chart Status Mahasiswa</title>
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
    <div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
	<div class="SideItem" id="SideItem">
			<div class="ViewTitle">Chart Status Mahasiswa</div>
			<br>
			<div class="container">
            <canvas id="myChart" width="100" height="100"></canvas>
        </div>
        <script>
            var ctx = document.getElementById("myChart");
            var myChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: [<?php foreach($detail_chart as $key => $val){ echo '"' .Akademik::getNamaPeriode($key) . '",'; }?> ],
                    datasets: [{
                            label: 'Mahasiswa Aktif ',
                            data: [<?php foreach($detail_chart as $key => $val2){ echo $val2['Aktif'].',';}?>],
                            backgroundColor: [
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)',
                                'rgba(200, 247, 197, 1)'
                            ],
                            borderColor: [
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)',
                                'rgba(99, 132, 0, 1)'
                            ],
                            borderWidth: 1
                        },
                        {
                            label: 'Mahasiswa Cuti',
                            data: [<?php foreach($detail_chart as $key => $val2){ echo $val2['Cuti'].',';}?>],
                            backgroundColor: [
                                'rgba(255, 246, 143, 1)',
                                'rgba(255, 246, 143, 1)',
                                'rgba(255, 246, 143, 1)',
                                'rgba(255, 246, 143, 1)',
                                'rgba(255, 246, 143, 1)',
                                'rgba(255, 246, 143, 1)'
                            ],
                            borderColor: [
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1
                        },
                        {
                            label: 'Mahasiswa Non-Aktif',
                            data: [<?php foreach($detail_chart as $key => $val2){ echo $val2['Non-Aktif'].',';}?>],
                            backgroundColor: [
                                'rgba(246, 36, 89, 1)',
                                'rgba(246, 36, 89, 1)',
                                'rgba(246, 36, 89, 1)',
                                'rgba(246, 36, 89, 1)',
                                'rgba(246, 36, 89, 1)',
                                'rgba(246, 36, 89, 1)'
                            ],
                            borderColor: [
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)',
                                'rgba(255, 206, 86, 1)'
                            ],
                            borderWidth: 1
                        },
                        {
                            label: 'Mahasiswa Lulus',
                            data: [<?php foreach($detail_chart as $key => $val2){ echo $val2['Lulus'].',';}?>],
                            backgroundColor: [
                                'rgba(0, 181, 204, 1)',
                                'rgba(0, 181, 204, 1)',
                                'rgba(0, 181, 204, 1)',
                                'rgba(0, 181, 204, 1)',
                                'rgba(0, 181, 204, 1)',
                                'rgba(0, 181, 204, 1)'
                            ],
                            borderColor: [
                                'rgba(197, 239, 247, 1)',
                                'rgba(197, 239, 247, 1)',
                                'rgba(197, 239, 247, 1)',
                                'rgba(197, 239, 247, 1)',
                                'rgba(197, 239, 247, 1)',
                                'rgba(197, 239, 247, 1)'
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
    </body>
</html>