<?
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
require_once($conf['model_dir'].'m_pendaftar.php'); 
require_once($conf['model_dir'].'m_smu.php');
require_once($conf['model_dir'].'m_combo.php');
//parameter
$periode    = CStr::removeSpecial($_REQUEST['periode']);
$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
$jurusan  = CStr::removeSpecial($_REQUEST['jurusan']);
 $r_format = CStr::removeSpecial($_REQUEST['format']);
$list_prodi=mCombo::jurusan($conn);
//model
$p_model='mPendaftar';
$p_title ='LAPORAN STATISTIK DATA PENERIMAAN MAHASISWA BARU';
$p_tbwidth = 700;

$level=$p_model::getLevelUnit($conn,$jurusan);
if($level==2){
	$unit=mCombo::jurusan($conn,$jurusan);
	
	foreach($unit as $key=>$value)
		$arr_unit[]=$key;
	$in_unit=implode("','",$arr_unit);
	
	$in_jurusan="'".$in_unit."'";
	//print_r($kodeunit);
}else{
	$arr_unit[0]=$jurusan;
	$in_jurusan="'".$jurusan."'";
}

$pendaftar=$p_model::getStatistikProdi($conn, $periode,$jalur,$in_jurusan);
/*
while($data = $pendaftar->FetchRow()){
	if((in_array($data['pilihan1'],$arr_unit) or in_array($data['pilihan2'],$arr_unit) or in_array($data['pilihan3'],$arr_unit)) and $data['sex']=='L')
		$daftarl++;
	if((in_array($data['pilihan1'],$arr_unit) or in_array($data['pilihan2'],$arr_unit) or in_array($data['pilihan3'],$arr_unit)) and $data['sex']=='P')
		$daftarp++;
	if(in_array($data['pilihanditerima'],$arr_unit) and $data['sex']=='L')
		$diterimal++;
	if(in_array($data['pilihanditerima'],$arr_unit) and $data['sex']=='P')
		$diterimap++;
	if(in_array($data['pilihanditerima'],$arr_unit) and $data['isdaftarulang']==-1 and $data['sex']=='L')
		$regl++;
	if(in_array($data['pilihanditerima'],$arr_unit) and $data['isdaftarulang']==-1 and $data['sex']=='P')
		$regp++;
}
*/
$row=array();
while($data = $pendaftar->FetchRow()){
	foreach($arr_unit as $kodeunit){
		if($data['pilihan1']==$kodeunit){
			if($data['sex']=='L')
				$row[$kodeunit]['daftarl']++;
			if($data['sex']=='P')
				$row[$kodeunit]['daftarp']++;
			}	
			if($data['pilihanditerima']==$kodeunit and $data['sex']=='L')
				$row[$kodeunit]['diterimal']++;
			if($data['pilihanditerima']==$kodeunit and $data['sex']=='P')
				$row[$kodeunit]['diterimap']++;
			if($data['pilihanditerima']==$kodeunit and $data['isdaftarulang']==-1 and $data['sex']=='L')
				$row[$kodeunit]['regl']++;
			if($data['pilihanditerima']==$kodeunit and $data['isdaftarulang']==-1 and $data['sex']=='P')
				$row[$kodeunit]['regp']++;
	}
}

$p_namafile='statistik_'.$periode.'_'.$jalur.'_'.$jurusan;
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
				   
				<span style="font-size: 1;">KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL SURABAYA</span><br>
								<span style="font-size: 1;">Jalan Arjuna Utara No.9, Kebon jeruk-Jakarta Barat 11510</span><br>
								<span style="font-size: 1;">021-5674223 (hunting) 021-5682510 (direct) Fax: 021-5674248 Website:www.esaunggul.ac.id, email:info@esaunggul.ac.id</span>

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
	<br><br>
	<table width="100%" border=1 cellspacing=0 cellpadding="4">
		<thead>
			<tr align="center">
				<td rowspan="2"><strong>No</strong></td>
				<td rowspan="2"><strong>Jurusan</strong></td>
				<td colspan="2"><strong>peserta</strong></td>
				<td colspan="2"><strong>Diterima</strong></td>
				<td colspan="2"><strong>Registrasi</strong></td>
			</tr>
			<tr align="center">
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
				foreach($arr_unit as $kodeunit){
					$data=$row[$kodeunit];
					$no++;
			?>
			
			<tr align="center">
				<td><?=$no?></td>
				<td align="left"><?=$list_prodi[$kodeunit];?></td>
				<td><?=isset($data['daftarl'])?$data['daftarl']:'0';?></td>
				<td><?=isset($data['daftarp'])?$data['daftarp']:'0';?></td>
				<td><?=isset($data['diterimal'])?$data['diterimal']:'0';?></td>
				<td><?=isset($data['diterimap'])?$data['diterimap']:'0';?></td>
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
