<?
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
require_once($conf['model_dir'].'m_pendaftar.php'); 
require_once($conf['model_dir'].'m_unit.php'); 
require_once($conf['model_dir'].'m_smu.php');
require_once($conf['model_dir'].'m_combo.php');
//parameter
$periode    = CStr::removeSpecial($_REQUEST['periode']);
$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
$jurusan  = CStr::removeSpecial($_REQUEST['jurusan']);
 $r_format = CStr::removeSpecial($_REQUEST['format']);
$list_prodi=mUnit::listJurusan($conn,$jurusan);
//model
$p_model='mPendaftar';
$p_title ='LAPORAN STATISTIK DATA PENERIMAAN MAHASISWA BARU';
$p_tbwidth = 700;
foreach($list_prodi as $key=>$value)
	$arr_unit[]=$key;
		
$pendaftar=$p_model::getStatistikProdi($conn, $periode,$jalur,$jurusan);
$row=array();
while($data = $pendaftar->FetchRow()){
	foreach($arr_unit as $kodeunit){
		if($data['pilihan1']==$kodeunit and $data['sex']=='L')
			$row[$kodeunit]['pil1l']++;
		if($data['pilihan1']==$kodeunit and $data['sex']=='P')
			$row[$kodeunit]['pil1p']++;
		if($data['pilihan2']==$kodeunit and $data['sex']=='L')
			$row[$kodeunit]['pil2l']++;
		if($data['pilihan2']==$kodeunit and $data['sex']=='P')
			$row[$kodeunit]['pil2p']++;
		if($data['pilihan3']==$kodeunit and $data['sex']=='L')
			$row[$kodeunit]['pil3l']++;
		if($data['pilihan3']==$kodeunit and $data['sex']=='P')
			$row[$kodeunit]['pil3p']++;	
			
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
				   
				
								<span style="font-size: 1;"><b>STIE INABA</b></span><br>
								<span style="font-size: 1;">Jl. Soekarno Hatta No.448, Batununggal, Bandung Kidul, Kota Bandung, Jawa Barat 40266</span><br>
								<span style="font-size: 1;">(022) 7563919<br>Website:inaba.ac.id, email:info@inaba.ac.id</span>

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
				<td colspan="2"><strong>Pilihan 1</strong></td>
				<td colspan="2"><strong>Pilihan 2</strong></td>
				<td colspan="2"><strong>Pilihan 3</strong></td>
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
				<td><b>L</b></td>
				<td><b>P</b></td>
				<td><b>L</b></td>
				<td><b>P</b></td>
			</tr>
		</thead>
		<tbody>
			<?
				$no=0;
				$pil1l=0;$pil1p=0;$pil2l=0;$pil2p=0;$pil3l=0;$pil3p=0;$diterimal=0;$diterimap=0;$regl=0;$regp=0;
				foreach($arr_unit as $kodeunit){
					$data=$row[$kodeunit];
					$no++;
					isset($data['pil1l'])?$pil1l+=$data['pil1l']:$pil1l=$pil1l;
					isset($data['pil1p'])?$pil1p+=$data['pil1p']:$pil1p=$pil1p;
					isset($data['pil2l'])?$pil2l+=$data['pil2l']:$pil2l=$pil2l;
					isset($data['pil2p'])?$pil2p+=$data['pil2p']:$pil2p=$pil2p;
					isset($data['pil3l'])?$pil3l+=$data['pil3l']:$pil3l=$pil3l;
					isset($data['pil3p'])?$pil3p+=$data['pil3p']:$pil3p=$pil3p;
					isset($data['diterimal'])?$diterimal+=$data['diterimal']:$diterimal=$diterimal;
					isset($data['diterimap'])?$diterimap+=$data['diterimap']:$diterimap=$diterimap;
					isset($data['regl'])?$regl+=$data['regl']:$regl=$regl;
					isset($data['regp'])?$regp+=$data['regp']:$regp=$regp;
			?>
			
			<tr align="center">
				<td><?=$no?></td>
				<td align="left"><?=$list_prodi[$kodeunit];?></td>
				<td><?=isset($data['pil1l'])?$data['pil1l']:'0';?></td>
				<td><?=isset($data['pil1p'])?$data['pil1p']:'0';?></td>
				<td><?=isset($data['pil2l'])?$data['pil2l']:'0';?></td>
				<td><?=isset($data['pil2p'])?$data['pil2p']:'0';?></td>
				<td><?=isset($data['pil3l'])?$data['pil3l']:'0';?></td>
				<td><?=isset($data['pil3p'])?$data['pil3p']:'0';?></td>
				<td><?=isset($data['diterimal'])?$data['diterimal']:'0';?></td>
				<td><?=isset($data['diterimap'])?$data['diterimap']:'0';?></td>
				<td><?=isset($data['regl'])?$data['regl']:'0';?></td>
				<td><?=isset($data['regp'])?$data['regp']:'0';?></td>
			</tr>
			<?php } ?>
			<tr>
				<th colspan="2">Jumlah</th>
				<th><?=$pil1l?></th>
				<th><?=$pil1p?></th>
				<th><?=$pil2l?></th>
				<th><?=$pil2p?></th>
				<th><?=$pil3l?></th>
				<th><?=$pil3p?></th>
				<th><?=$diterimal?></th>
				<th><?=$diterimap?></th>
				<th><?=$regl?></th>
				<th><?=$regp?></th>
				
			</tr>
			</tbody>
	</table>
	</div>
	</center>
	</body>
</html>
