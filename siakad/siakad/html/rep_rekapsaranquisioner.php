<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('monitoring'));
	require_once(Route::getModelPath('combo'));
	
	// properti halaman
	$p_title = 'Rekap Saran Quisioner';
	$p_tbwidth = 700;
	$p_namafile = "";
	$r_format = $_REQUEST['format'];
    if(empty($r_format))
  	$r_format='xls';

	$r_key = CStr::removeSpecial($_REQUEST['key']);

	if(empty($r_key))
		Route::navigate($p_listpage);
	
	$key = explode('|',$r_key);

	$a_infokelas = mKelas::getDataSingkat($conn,$r_key,true,$key[5]);

	$a_data = mMonitoring::saranQuisioner($conn,$r_key);
	
	$p_namafile = 'Saran-'.$a_infokelas['namamk']."-".$a_infokelas['pengajar']."- Kelas (".$key[4].")";
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
		.tb_foot_ttd td { padding-right:80px }
		
	</style>
</head>
<body>
<div align="center">

<div align="left" style="width:<?= $p_tbwidth ?>px"><b><?=$p_title?></b></div>
<table width="<?= $p_tbwidth ?>" class="tb_head">
	<tr>
		<td width="100">Nama Mata Kuliah</td>
		<td>:</td>
		<td><?= $a_infokelas['namamk'] ?></td>
	</tr>
	<tr>
		<td>Kode MK / Kelas</td>
		<td>:</td>
		<td><?= $a_infokelas['kodemk'] ?> / <?= $a_infokelas['kelasmk'] ?></td>
	</tr>
	<tr>
		<td>Jadwal / Ruang</td>
		<td>:</td>
		<td><?= $a_infokelas['jadwal'] ?> / <?= $a_infokelas['koderuang'] ?></td>
	</tr>
	<tr>
		<td>Dosen</td>
		<td>:</td>
		<td><?= $a_infokelas['pengajar'] ?></td>
	</tr>
</table>

	

<br>
<table width="<?= $p_tbwidth ?>" class="tb_data">
<thead>
<tr bgcolor= 'green'>
    <th>No</th>
    
	<th>Saran</th>
</tr>
</thead> 
<tbody>
	<?php  
		$p_namafile = $a_infokelas['pengajar'];
		$no = 0 ;
		foreach ($a_data as $row => $rows) { ?>
		<tr>
			<td><center><?= ++$no  ?></center></td>
			<?php /* <td><?= $rows['nim']  ?></td>
			<td><?= $rows['nama']  ?></td> */ ?>
			<td><?= $rows['saran']  ?></td>
		</tr>	
	<?php		
		}
	?>
	
</tbody> 
	</table>
 </body>
 </html>
