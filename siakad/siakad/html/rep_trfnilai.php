<?php
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

$a_auth = Modul::getFileAuth();
	// include
    require_once(Route::getModelPath('mahasiswa'));
    require_once(Route::getModelPath('unit'));
    require_once(Route::getModelPath('transkrip'));
    require_once(Route::getModelPath('kurikulum'));
    require_once(Route::getModelPath('skalanilai'));
    require_once(Route::getModelPath('konversinilai'));
    
    //require_once(Route::getUIPath('combo'));
    $r_kurikulum=Akademik::getKurikulum();
	$a_infomhs = mMahasiswa::getDataSingkat_konversi($conn,$_REQUEST['key']);
	$a_konversi = mKonversiNilai::getHasilKonversi($conn,$_REQUEST['key']);
 ?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style type="text/css">
		table {
    border-collapse: collapse;
}
	</style>
</head>
<body style="background: none !important; font-size: 10px;" onload="window.print()">

<?php include('inc_headerlap.php'); ?>
<br>
    <table width="100%" cellspacing="0" cellpadding="4" >
		<tr>		
			<td width="50" style="white-space:nowrap"><strong>Mahasiswa</strong></td>
			<td><strong> : </strong><?= $a_infomhs['nama'] ?> (<?= $a_infomhs['nim'] ?>)</td>
			<td>Jurusan</td>
			<td><strong> : </strong><?= $a_infomhs['jurusan'] ?></td>		
		</tr>
		
		<tr>		
			<td style="white-space:nowrap"><strong>Jurusan Asal</strong></td>
			<td><strong> : </strong><?= $a_infomhs['ptjurusan'] ?></td>		
		</tr>
	</table>
	<br>

<table width="100%" cellpadding="4" cellspacing="0" style="border: 1px solid black">
	<thead>
		<tr> 
			<td colspan="5" style="border: 1px solid black">Nilai Asal</td>
			<td colspan="3" style="border: 1px solid black">Konversi dengan Mata Kuliah Kur. <?=$r_kurikulum?></td>
		
		</tr>
		<tr>
			<td style="border: 1px solid black"><center>No.</center></td>
			<td style="border: 1px solid black"><center>Kode MK</center></td>
			<td style="border: 1px solid black">Nama MK</td>
			<td style="border: 1px solid black"><center>SKS</center></td>
			<td style="border: 1px solid black"><center>Nilai</center></td>
			<td style="border: 1px solid black">Mata Kuliah Konversi</td>
			<td style="border: 1px solid black"><center>SKS</center></td>
			<td style="border: 1px solid black"><center>Nilai Konversi</center></td>
		
		</tr>
	</thead>
	<tbody>
	   <?php $no=0;foreach($a_konversi as $row){ $no++;?>
		<tr>
			<td style="border: 1px solid black"><center><?=$no?></center></td>
			<td style="border: 1px solid black"><center><?=$row['kodemklama']?></center></td>
			<td style="border: 1px solid black"><?=$row['namamklama']?></td>
			<td style="border: 1px solid black"><center><?=$row['skslama']?></center></td>
			<td style="border: 1px solid black">
				<center>
					<?php 
						if ($row['nangkalama'] == "4") {
							echo "A";
						}elseif($row['nangkalama'] == "3"){
							echo "B";
						}elseif ($row['nangkalama'] == "2") {
							echo "C";
						}elseif ($row['nangkalama'] == "1") {
							echo "B";
						}elseif ($row['nangkalama'] == "0") {
							echo "E";
						}
					?>
					
						
				</center>
			</td>
			<td style="border: 1px solid black"><?=$row['kodemkbaru']?>-<?=$row['namamkbaru']?></td>
			<td style="border: 1px solid black"><center><?=$row['sksbaru']?></center></td>
			<td style="border: 1px solid black"><center><?=$row['nhurufbaru']?></center></td>
		
		</tr>
    	<?php } ?>
        </tbody>
</table>
<br>
	&nbsp;
<table width="100%">
	<tr>
		<td width="450">&nbsp;</td>
		<td class="mark">Bandung, <?= CStr::formatDateInd(date('Y-m-d')) ?></td>
	</tr>
	<tr>
		<td>Wakil Ketua Bidang Akademik</td>
		<td>Ketua Program Studi <?= $a_infomhs['jurusan'] ?></td>
	</tr>
	<tr>
		<td>&nbsp;</td>
		<td>&nbsp;</td>
	</tr>
	<tr height="45">
		<td colspan="2">&nbsp;</td>
	</tr>
	<tr>
		<td>Drs. RIYANDI NUR SUMAWIDJAYA , M.M</td>
		<td><?= $a_infomhs['ketua'] ?> </td>
	</tr>
	<tr>
		<td>NIDN. 0413106201</td>
		<td>NIDN. <?= $a_infomhs['nidnketua'] ?></td>
	</tr>
	
</table>
</div>
</body>
</html>