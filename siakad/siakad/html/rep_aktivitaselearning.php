<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	Modul::getFileAuth();
    //print_r($_POST);
    //die();
	// include
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('unit'));
	$startdate = str_replace('/', '-', $_POST['starttgl']);
    $startdate = date("Y-m-d", strtotime($startdate));


    $enddate = str_replace('/', '-', $_POST['endtgl']);
    $enddate = date("Y-m-d", strtotime($enddate));
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(empty($r_key)) {
		$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
		$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	}
	else
		list($r_kurikulum,$r_kodemk,$r_kodeunit,$r_periode,$r_kelasmk) = explode('|',$r_key);
	
	$r_format = $_REQUEST['format'];
	
	// pengecekan unit
	$r_kodeunit = mUnit::passUnit($conn,$r_kodeunit);
	
	// properti halaman
	$p_title = 'Aktivitas E-Learning Kuliah';
	$p_tbwidth = 700;
	$p_maxrow = 46;
	$p_maxday = 16;
	$p_namafile = 'aktivitaselearning_'.$r_kodeunit.'_'.$r_periode;
	$a_data =  mLaporan::getReportActByDate($conn_moodle,$r_kodeunit,$r_periode,$startdate,$enddate);
    // header
	Page::setHeaderFormat($r_format,$p_namafile);
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
<center>
<table class="tab_header" width="<?= $p_tbwidth ?>">
		<thead>
			<tr>
				<td width="70" align="center">
					<img src="images/logo.jpg" width="65">
				</td>
				<td valign="middle" align="center">
					<div class="div_header">LAPORAN Aktivitas E-Learning <?= Akademik::getNamaPeriode($_POST['tahun'].$_POST['semester']) ?></div>
					<div class="div_header"><b>STIE INABA </b></div>
                    <div class="div_header"><b>Periode Bulan <?=$_POST['starttgl']?> s/d <?= $_POST['endtgl'] ?> </b></div>
					
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
                <th>Jumlah Quiz</th>
                <th>Jumlah Tugas</th>
            </tr>
            <?php
                foreach($a_data as $row) { 
                    $unit = explode("|",$row['kodeunit']);
            ?>

                <tr>
                    <td><?= mUnit::getNamaParentUnit($conn,$unit[0]) ?></td>
                    <td><?= Akademik::getNamaUnit($conn,$unit[0])?></td>
                    <td><?= $row['namakelas'] ?></td>
                    <td><?= $row['namadosen'] ?></td>
                    <td><?= $row['quiz'] ?></td>
                    <td><?= $row['tugas'] ?></td>


                </tr>
                <?php } ?>
        </table>
        </div>
	<div style="page-break-after:always"></div>

</body>
</html>





</center>