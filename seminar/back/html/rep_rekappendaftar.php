<?
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	$a_auth = Modul::getFileAuth();


	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getModelPath('combo'));
        
	//parameter
	$periode    = CStr::removeSpecial($_REQUEST['periode']);
	$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
	$jurusan  = CStr::removeSpecial($_REQUEST['jurusan']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	$list_prodi=mCombo::jurusan($conn);
	
	$p_model=mPendaftar;
	$p_title ='LAPORAN REKAPITULASI DATA PENDAFTAR';
	$p_tbwidth = 700;


$pendaftar=$p_model::getStatistikProdi($conn,$periode,$jalur,$jurusan);

$row=array();
while($data = $pendaftar->FetchRow()){
	foreach($list_prodi as $kodeunit => $val){
		if($data['pilihan1']==$kodeunit){
			if($data['sex']=='L')
				$row[$kodeunit]['daftarl']++;
			if($data['sex']=='P')
				$row[$kodeunit]['daftarp']++;	
		}
		if($data['pilihanditerima']==$kodeunit and $data['isdaftarulang']==-1 and $data['sex']=='L')
			$row[$kodeunit]['regl']++;
		if($data['pilihanditerima']==$kodeunit and $data['isdaftarulang']==-1 and $data['sex']=='P')
			$row[$kodeunit]['regp']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulustpa']==-1 and $data['sex']=='L')
			$row[$kodeunit]['lulustpal']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulustpa']==-1 and $data['sex']=='P')
			$row[$kodeunit]['lulustpap']++;
		if($data['pilihanditerima']==$kodeunit and $data['luluswawancara']==-1 and $data['sex']=='L')
			$row[$kodeunit]['luluswawancaral']++;
		if($data['pilihanditerima']==$kodeunit and $data['luluswawancara']==-1 and $data['sex']=='P')
			$row[$kodeunit]['luluswawancarap']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulusteskesehatan']==-1 and $data['sex']=='L')
			$row[$kodeunit]['lulusteskesehatanl']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulusteskesehatan']==-1 and $data['sex']=='P')
			$row[$kodeunit]['lulusteskesehatanp']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulusnilairaport']==-1 and $data['sex']=='L')
			$row[$kodeunit]['lulusnilairaportl']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulusnilairaport']==-1 and $data['sex']=='P')
			$row[$kodeunit]['lulusnilairaportp']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulustespelajaran']==-1 and $data['sex']=='L')
			$row[$kodeunit]['lulustespelajaranl']++;
		if($data['pilihanditerima']==$kodeunit and $data['lulustespelajaran']==-1 and $data['sex']=='P')
			$row[$kodeunit]['lulustespelajaranp']++;
		if($data['pilihanditerima']==$kodeunit and $data['luluskompetensi']==-1 and $data['sex']=='L')
			$row[$kodeunit]['luluskompetensil']++;
		if($data['pilihanditerima']==$kodeunit and $data['luluskompetensi']==-1 and $data['sex']=='P')
			$row[$kodeunit]['luluskompetensip']++;
	}
}

$p_namafile='rekap_'.$periode.'_'.$jalur.'_'.$jurusan;
Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
<html>
	<head>
			<title><?=$p_title?></title>   
			<link rel="icon" type="image/x-icon" href="image/favicon.png">
			<link href="style/style.css" rel="stylesheet" type="text/css">
			
	</head>
	<body style="background:white" onLoad="window.print();">
  
			<center>
			<div style="border: solid; border-width: thin; border-color: #c5c5c5; width:<?=$p_tbwidth ?>px;" >
			
	<table width="<?=$p_tbwidth ?>px">
		<tr>
			<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
			<td align="left">				   
				<span>KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
				<span>UNIVERSITAS ESA UNGGUL JAKARTA</span><br>
				<span>Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
				<span>021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>
			</td>
		</tr>
	</table>
	<hr>
	<center>
	 <strong>
		 <?=$p_title?><br>
		  <?=!empty($jalur)?'PROGRAM '.strtoupper($jalur):''?><br>
		  <?=!empty($jurusan)?strtoupper($list_prodi[$jurusan]):''?>
		  <br>
		<?=!empty($periode)?'PERIODE '.$periode.'/'.($periode+1):''?>
	</strong>
	</center> 
	<br>
	<table width="100%" border=1 cellspacing=0 cellpadding="4" style="border-collapse:collapse">
		<thead>
			<tr align="center">
				<th rowspan="2"><strong>No</strong></th>
				<th rowspan="2"><strong>Jurusan</strong></th>
				<th colspan="2"><strong>Pendaftar</strong></th>
				<th colspan="2"><strong>Lulus TPA</strong></th>
				<th colspan="2"><strong>Lulus Wawancara</strong></th>
				<th colspan="2"><strong>Lulus Tes Kesehatan</strong></th>
				<th colspan="2"><strong>Lulus Nilai Raport</strong></th>
				<th colspan="2"><strong>Lulus tes Bidang Mata Pelajaran</strong></th>
				<th colspan="2"><strong>Lulus Uji Kompetensi</strong></th>
				<th colspan="2"><strong>Registrasi</strong></th>
			</tr>
			<tr align="center">
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
			</tr>
		</thead>
		<tbody>
			<?
		   $no=0;
				foreach($list_prodi as $kodeunit => $val){
					$data=$row[$kodeunit];
					$no++;
			?>
			<tr align="center">
				<td><?=$no?></td>
				<td align="left"><?=$list_prodi[$kodeunit];?></td>
				<td><?=isset($data['daftarl'])?$data['daftarl']:'0';?></td>
				<td><?=isset($data['daftarp'])?$data['daftarp']:'0';?></td>
				<td><?=isset($data['lulustpal'])?$data['lulustpal']:'0';?></td>
				<td><?=isset($data['lulustpal'])?$data['lulustpal']:'0';?></td>
				<td><?=isset($data['luluswawancaral'])?$data['luluswawancaral']:'0';?></td>
				<td><?=isset($data['luluswawancarap'])?$data['luluswawancarap']:'0';?></td>
				<td><?=isset($data['lulusteskesehatanl'])?$data['lulusteskesehatanl']:'0';?></td>
				<td><?=isset($data['lulusteskesehatanp'])?$data['lulusteskesehatanp']:'0';?></td>
				<td><?=isset($data['lulusnilairaportl'])?$data['lulusnilairaportl']:'0';?></td>
				<td><?=isset($data['lulusnilairaportp'])?$data['lulusnilairaportp']:'0';?></td>
				<td><?=isset($data['lulustespelajaranl'])?$data['lulustespelajaranl']:'0';?></td>
				<td><?=isset($data['lulustespelajaranp'])?$data['lulustespelajaranp']:'0';?></td>
				<td><?=isset($data['luluskompetensil'])?$data['luluskompetensil']:'0';?></td>
				<td><?=isset($data['luluskompetensip'])?$data['luluskompetensip']:'0';?></td>
				<td><?=isset($data['regl'])?$data['regl']:'0';?></td>
				<td><?=isset($data['regp'])?$data['regp']:'0';?></td>
			</tr>
		</tbody>
			<?php } ?>
	</table>
	</div>
	</center>
	</body>
</html>
