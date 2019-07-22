<?php

    // cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_statusmhs = CStr::removeSpecial($_REQUEST['statusmhs']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	$r_periode=$r_tahun.$r_semester;
	
	require_once(Route::getModelPath('laporanmhs'));
	require_once(Route::getModelPath('combo'));
	$conn->debug=true;
	// definisi variable halaman
	$a_statusmhs=mCombo::statusMhs($conn);
	$p_title = "REKAPITULASI<br>MAHASISWA ".strtoupper($a_statusmhs[$r_statusmhs])." KULIAH";
	$p_tbwidth = 900;
	$p_namafile = 'jml_mhs_berdasarkan_status'.$r_kodeunit;
	
     $data = mLaporanMhs::getRekapMhsStatus($conn,$r_periode,$r_statusmhs,$r_unit);
     $a_sistem=mCombo::sistemKuliah($conn);
     print_r($a_sistem);
    
     $a_angkatan=array();
     $a_data=array();
     $old=0;
     foreach($data as $row){
		
		 $a_angkatan[$row['angkatan']]=$row['angkatan'];
		 $a_data[$row['fak'].'-'.$row['jur']][$row['angkatan']][$row['sistem']]=$row; 
		 $a_unit[$row['fak'].'-'.$row['jur']]=$row;
	 }
	ksort($a_angkatan);
	 // header
	Page::setHeaderFormat($r_format,$p_namafile);
?>



<html>

<head>
	<title><?=$p_title?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head { font-family: "Times New Roman" }
		.tb_head td { font-size: 10px }
		.div_head { font-size: 14px; font-weight: bold; text-decoration: underline; margin-bottom: 5px }
		
		.tb_cont td { padding: 0; vertical-align: top }
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; font-size: 10px; padding: 2px }
		.tb_data th { background-color: #CFC }
		.tb_data .mark { font-family: "Arial Narrow","Arial" }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-weight: normal }
	</style>
</head>
<body>
	<div align="center">
		<b>
		<?=$p_title?><br>
		s/d T.A <?=Akademik::getNamaPeriode($r_periode,true)?>
		(tertanggal <?=Cstr::formatDateInd(date('Y-m-d'))?>) 
		</b>
		<br><br>
		<table class="tb_data" width="100%">
			<tr>
				<th width="40" rowspan="2">Kode P.S</th>
				<th width="200" rowspan="2">Program Studi</th>
				<?php foreach($a_angkatan as $angkatan){ ?>
				<th colspan="<?=count($a_sistem)?>"><?=$angkatan?> </th>
				<?php } ?>
				<th colspan="<?=count($a_sistem)?>">Jumlah</th>
				<th rowspan="2">TOTAL</th>
			</tr>
			<tr>
				<?php 
				foreach($a_angkatan as $angkatan){ 
					foreach($a_sistem as $sistem){
						echo '<th>'.$sistem.'</th>';
					} 
				} 
				foreach($a_sistem as $sistem){
					echo '<th>'.$sistem.'</th>';
				} 
				?>
			</tr>
			<?php 
			$bottom_sjum=array();
			$bottom_jum=0;
			foreach($a_unit as $key_unit=>$unit){
			?>
			<tr>
				<td align="center"><?=$unit['jur']?></td>
				<td><?=$unit['jurusan']?></td>
				<?php
				
				$right_sjum=array();
				$right_jum=0;
				foreach($a_angkatan as $angkatan){ 
					foreach($a_sistem as $sistem){
						if($a_data[$key_unit][$angkatan][$sistem])
							$jumlah=$a_data[$key_unit][$angkatan][$sistem]['jumlah'];
						else
							$jumlah=0;
						echo '<td align="right">'.$jumlah.'</td>';
						$right_sjum[$sistem]+=$jumlah;
						$bottom_sjum[$angkatan.'-'.$sistem]+=$jumlah;
						
						$right_jum+=$jumlah;
					} 
				}
				foreach($a_sistem as $sistem){
					$bottom_sjum['jumlah-'.$sistem]+=$right_sjum[$sistem];
					echo '<td align="right">'.$right_sjum[$sistem].'</td>';
				} 
				?>
				<td><?=$right_jum?></td>
			</tr>	
			<?php 
			$bottom_jum+=$right_jum;
			
			} ?>
			<tr>
				<td colspan="2" align="center">TOTAL</td>
			<?php foreach($bottom_sjum as $vbottom_jum){ ?>
				<td align="right"><?=$vbottom_jum?></td>
			<?php } ?>
			<td><?=$bottom_jum?></td>
			</tr>
		</table><br>
		<div align="left" class="div_header">
			Data Tanggal <?=Cstr::formatDateInd(date('Y-m-d'))?> (setelah Registrasi <?=Akademik::getNamaPeriode($r_periode,true)?>)
		</div>
	</div>
</body>
</html>



