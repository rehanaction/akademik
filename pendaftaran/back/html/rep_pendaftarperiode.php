<?php 
require_once(Route::getModelPath('laporan'));

$a_data = mLaporan::InquiryPendaftarPeriode($conn);

print_r($a_data);

?>
<html>
    <head>
        <title>Belajarphp.net - ChartJS</title>
        <script src="scripts/chartjs/Chart.bundle.js"></script>
        <style type="text/css">
            .container {
                width: 50%;
                margin: 15px auto;
            }
        </style>
    </head>
    <body>
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
    </body>
</html>