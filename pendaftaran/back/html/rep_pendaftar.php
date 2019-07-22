<?
// cek akses halaman
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
        
require_once($conf['model_dir'].'m_pendaftar.php'); 
require_once($conf['model_dir'].'m_smu.php');
require_once($conf['model_dir'].'m_combo.php');
//parameter
$periode    = CStr::removeSpecial($_REQUEST['periode']);
$jalur      = CStr::removeSpecial($_REQUEST['jalur']);
$pilihan  = CStr::removeSpecial($_REQUEST['pilihan1']);
$r_format = CStr::removeSpecial($_REQUEST['format']);
$r_daftarulang = CStr::removeSpecial($_REQUEST['isdaftarulang']);
$r_lulusujian = CStr::removeSpecial($_REQUEST['lulusujian']);
$idsmu='';
if(isset($_POST['repkey'])){
	$key=explode('|',$_POST['repkey']);
	$periode=$key[0];
	$jalur=$key[1];
	$idsmu=$key[2];
}
//model
$p_model='mPendaftar';
$p_title ='LAPORAN DATA PENDAFTAR';
$p_tbwidth = 1300;
$rs_sma = mSmu::getSmu();
	$list_smu = array();
	$list_alamatsmu = array();
	$list_tlpsmu = array();
	while($row = $rs_sma->FetchRow()){
		$list_smu[$row['idsmu']] = $row['namasmu'];
		$list_alamatsmu[$row['idsmu']] = $row['alamatsmu'];
		$list_tlpsmu[$row['idsmu']] = $row['telpsmu'];
	}
$list_prodi=mCombo::jurusan($conn);

$mkotalahir = mCombo::getKota($conn);
$mkota = mCombo::getKota($conn);

$mprop = mCombo::propinsi($conn);
$pendaftar=$p_model::getReportPendaftar($conn, $periode,$jalur,$pilihan,'',$r_daftarulang,$idsmu,$r_lulusujian);

$p_namafile='pendaftar_'.$periode.'_'.$jalur.'_'.$pilihan;
Page::setHeaderFormat($r_format,$p_namafile);
?>
<!DOCTYPE html>
<html>
	<head>
			<title><?=$p_title?></title>   
			<link rel="icon" type="image/x-icon" href="image/favicon.png">
			<link href="style/style.css" rel="stylesheet" type="text/css">
			
	</head>
	<body style="background:white;" onLoad="window.print();">
  
			<center>
			<div style=" border-color: #c5c5c5; width:<?=$p_tbwidth ?>px;" >
			
			<table width="<?=$p_tbwidth ?>px">
			<tr>
			<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
			<td align="left">
				   
				<span style="font-size: 1;"><b>STIE INABA</b></span><br>
				<span style="font-size: 1;">Jalan Soekarno Hatta No. 448 Kota Bandung, Jawa Barat 40266</span><br>
				<span style="font-size: 1;">Telp: (022) 7563919</span><br>
				<span style="font-size: 1;">Website: www.inaba.ac.id<br>
				<span style="font-size: 1;">Email: humas.marketing@inaba.ac.id</span>
			</td>
		</tr>
	</table>
	<hr>
	<center>
	 <strong>
		 <?=$p_title?>
		  <?=!empty($jalur)?'PROGRAM '.strtoupper($jalur):''?><br>
		  <?=!empty($pilihan)?strtoupper($list_prodi[$pilihan]):''?>
		  <br>
		<?=!empty($periode)?'PERIODE '.$periode.'/'.($periode+1):''?>
			
	</strong>     
	</center> 
	<br>
	<table width="100%" border=1 cellspacing=0 cellpadding="4" style="border-collapse:collapse">
		<thead>
			<tr align="center">
				<td><strong>No</strong></td>
				<td><strong>No Pendaftar</strong></td>
				
				<td><strong>Nama</strong></td>
				<td><strong>Jenis kelamin</strong></td>
				<td><strong>Tempat / Tgl Lahir</strong></td>
				<td><strong>No TLP/HP</strong></td>
				<td><strong>Alamat</strong></td>
				<td><strong>Asal SMU</strong></td>
				<td><strong>Periode Daftar</strong></td>
				<td><strong>Jalur Seleksi/Gel.</strong></td>
				<td><strong>Sistem Kuliah</strong></td>
				<td><strong>Tgl. Daftar</strong></td>
				<td><strong>Prodi Pilihan</strong></td>
				<td><strong>Status Kelulusan Asal</strong></td>
			</tr>
		</thead>
		<tbody>
			<?
		   
			$no=0;
			while($data = $pendaftar->FetchRow()){
					$no++;
					
					
			?>
			<tr>
				<td><?=$no;?></td>
				<td><?=$data['nopendaftar']?></td>
				
				<td><?=$data['namalengkap'] ?></td>
				<td><?=$data['sex']=='L'?'Laki - Laki':'Perempuan' ?></td>
				<td><?= $mkotalahir[$data['kodekotalahir']]?> / <?= !empty($data['tgllahir'])?Date::indoDate($data['tgllahir']):''?></td>
				<td><?=$data['telp'] ?><?=!empty($data['telp2'])?', '.$data['telp2']:'' ?>, <?=$data['hp'] ?> <?=!empty($data['hp2'])?', '.$data['hp2']:'' ?></td>
				<td>
					<span>Jln. <?= $data['jalan'] ?> RT. <?= $data['rt'] ?> RW. <?= $data['rw'] ?> Kelurahan <?= $data['kel'] ?></span>
					<span>Kecamatan <?= $data['kec'] ?>, <?= $mkota[$data['kodekota']] ?>, <?= $mprop[$data['kodepropinsi']] ?></span>
				</td>
				<td><?=$list_smu[$data['asalsmu']] ?></td>
				<td><?=$data['periodedaftar'] ?></td>
				<td><?=$data['jalurpenerimaan'].'/'.$data['idgelombang'] ?></td>
				<td><?=$data['namasistem']; ?></td>
				<td><?=Date::indoDate($data['tglregistrasi']) ?></td>
				<td><?=$list_prodi[$data['pilihanditerima']] ?></td>
				<td><?=$data['isdaftarulang']==-1?'Sudah':'Belum' ?></td>
				
				
			</tr>
		</tbody>
			<?php } ?>
	</table>
	</div>
	</center>
	</body>
</html>
