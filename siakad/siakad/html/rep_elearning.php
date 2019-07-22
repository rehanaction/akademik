<?php 
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=false;
    // hak akses
   
    $r_periode = $_POST['tahun'].$_POST['semester'];
    Modul::getFileAuth();
   
    require_once(Route::getModelPath('laporan'));
    require_once(Route::getModelPath('unit'));
    if($_POST['jenis']=='quiz'){
        $a_data = mLaporan::getQuiz($conn_moodle,$r_periode,$_POST['unit']);
       
    }elseif($_POST['jenis']=='tugas'){
		$a_data = mLaporan::getTugas($conn_moodle,$r_periode,$_POST['unit']);
	}elseif($_POST['jenis']=='video'){
		$a_data = mLaporan::getVideo($conn_moodle,$r_periode,$_POST['unit']);
	}


//print_r($a_data);
  
$p_title = 'Laporan E-Learning';
$p_tbwidth = 700;
$r_format = $_REQUEST['format'];



?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12pt }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		.div_head { text-decoration: underline }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 2px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
	</style>
</head>
<body>
<table class="tab_header" width="<?= $p_tbwidth ?>">
		<thead>
			<tr>
				<td width="70" align="center">
					<img src="images/logo.jpg" width="65">
				</td>
				<td valign="middle" align="center">
					<div class="div_header">LAPORAN <?= strtoupper($_POST['jenis'])?> E-Learning <?= Akademik::getNamaPeriode($_POST['tahun'].$_POST['semester']) ?></div>
					<div class="div_header"><b>STIE INABA </b></div>
					
				</td>
			</tr>
			</thead>
		</table>
        <table class="tb_data" width="<?= $p_tbwidth ?>">
            <tr>
                <th>Fakultas</th>
                <th>Prodi</th>
                <th>Matakuliah</th>
                <th>Nama Dosen</th>
                <th>Jumlah</th>
                <th>%</th>
            </tr>

            <?php foreach($a_data as $row) { 
                    $unit = explode("|",$row['kodeunit']);
                ?>
                    
                <tr>
                    <td><?= mUnit::getNamaParentUnit($conn,$unit[0]) ?></td>
                    <td><?= Akademik::getNamaUnit($conn,$unit[0])?></td>
                    <td><?= $row['namakelas'] ?></td>
                    <td><?= $row['namadosen'] ?></td>
                    <td><?= $row['jumlah'] ?></td>
                    <td><?= (float)($row['jumlah']/11)*100 ?>%</td>


                </tr>
            <?php } ?>
        </table>
        </div>
	<div style="page-break-after:always"></div>

</body>
</html>