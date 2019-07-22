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
$idsmu='';
if(isset($_POST['repkey'])){
	$key=explode('|',$_POST['repkey']);
	$periode=$key[0];
	$jalur=$key[1];
	$idsmu=$key[2];
}
$tahap = mCombo::getTahapUjian();

//model
$p_model='mPendaftar';
$p_title ='LAPORAN DATA PENDAFTAR YANG TELAH DAFTAR ULANG';
$p_tbwidth = 840;

 
$list_prodi=mCombo::jurusan($conn);
$pendaftar=$p_model::getReportPendaftar($conn, $periode,$jalur,'',$jurusan,'-1',$idsmu);
$p_namafile = 'daftarulang_'.$periode.'_'.$jalur.'_'.$jurusan;
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
			<div style=" width:<?=$p_tbwidth ?>px;" >
			
			<table width="<?=$p_tbwidth ?>px">
			<tr>
			<td align="center" width="100"><img src="images/logo.jpg" height="70"></td>
			<td align="left">
				   
				<span style="font-size: 1;">KEMENTRIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</span><br>
								<span style="font-size: 1;">UNIVERSITAS ESA UNGGUL</span><br>
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
	</center> 
	<br>
	<table width="100%" border=1 cellspacing=0 cellpadding="4">
		<thead>
			<tr align="center">
				<td ><strong>No</strong></td>
				<td ><strong>No Pendaftar</strong></td>
				<td ><strong>NIM</strong></td>
				<td ><strong>Nama</strong></td>
				<td ><strong>Jenis kelamin</strong></td>
				<td ><strong>Tempat / Tgl Lahir</strong></td>
				<td ><strong>Periode Daftar</strong></td>
				<td ><strong>Sistem Kuliah</strong></td>
				<td ><strong>Asal Sekolah</strong></td>
				<td ><strong>Jurusan</strong></td>
				<td ><strong>Tgl. Daf. Ulang</strong></td>
				<td ><strong>Ukuran Almamater</strong></td>
				
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
				<td><?=$data['nimpendaftar']?></td>
				<td><?=$data['namalengkap'] ?></td>
				<td><?=$data['sex']=='L'?'Laki - Laki':'Perempuan' ?></td>
				<td><?=$data['namakota']?> / <?= !empty($data['tgllahir'])?date('d-m-Y',strtotime($data['tgllahir'])):''?></td>
				<td><?=$data['periodedaftar'] ?></td>
				<td><?=$data['namasistem'] ?></td>
				<td><?=$data['namasmu'] ?></td>
				<td><?=$list_prodi[$data['pilihanditerima']] ?></td>
				<td><?=Date::indoDate($data['tgldaftarulang'])?></td>
				<td align="center"><?=$data['ukuranalmamater'] ?></td>
			</tr>
		</tbody>
			<?php } ?>
	</table>
	</div>
	</center>
	</body>
</html>
