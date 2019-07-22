<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$p_title="Daftar Laporan";
	$p_aktivitas='LAPORAN';
	
	$r_access=array();
	$r_access[] = array('label' => 'Kelas (Sebaran)', 'val'=>'kelas', 'report'=>'repp_kelas');
	$r_access[] = array('label' => 'Mahasiswa', 'val'=>'mahasiswa', 'report'=>'repp_mahasiswa');
	
	$r_baru=array();
	$r_baru[] = array('label' => 'IPK Prodi', 'val'=>'ipkprodi', 'report'=>'repp_ipkprodi');
	$r_baru[] = array('label' => 'IPK Angkatan', 'val'=>'ipkangkatan', 'report'=>'repp_ipkangkatan');
	$r_baru[] = array('label' => 'IPK Jalur per prodi & Semester', 'val'=>'ipkjalurperpdori', 'report'=>'repp_jalurpenerimaan');
	$r_baru[] = array('label' => 'IPK Jalur per Semester', 'val'=>'ipkjalurpersemester', 'report'=>'repp_jalurpenerimaansem');
	$r_baru[] = array('label' => 'Mhs Berhak Ujian Skripsi', 'val'=>'mhsskripsi' , 'report'=>'repp_jmlmhsskripsi');
	$r_baru[] = array('label' => 'IPK Tertinggi', 'val'=>'ipktinggi', 'report'=>'repp_ipktertinggi');
	$r_baru[] = array('label' => 'IPK Rata-rata', 'val'=>'ipkrata', 'report'=>'repp_ipkrata2');
	$r_baru[] = array('label' => 'Status Mahasiswa', 'val'=>'statusmhs', 'report'=>'repp_statusmhs');
	$r_baru[] = array('label' => 'Ajar Dosen Per Prodi', 'val'=>'ajardosenperprodi', 'report'=>'repp_kuliahprodi');
	$r_baru[] = array('label' => 'Ajar Dosen Per Prodi & Semester', 'val'=>'ajardosenpersmt', 'report'=>'repp_kuliahsem');
	$r_baru[] = array('label' => 'Lama Study', 'val'=>'lamastudy', 'report'=>'repp_lamastudy');
	$r_baru[] = array('label' => 'IPK Lulusan', 'val'=>'lamastudy', 'report'=>'repp_ipklulusan');
	$r_baru[] = array('label' => 'IPK Lulusan per prodi', 'val'=>'ipklulusanperprody', 'report'=>'repp_ipklulusanprodi');
	$r_baru[] = array('label' => 'Mhs Belum KRS', 'val'=>'mhsbelumkrs', 'report'=>'repp_krsmhs');
	$r_baru[] = array('label' => 'Mhs Program Lebih', 'val'=>'mhsprogramlebih', 'report'=>'repp_programmtkul');
	$r_baru[] = array('label' => 'Jumlah mahasiswa per status', 'val'=>'jmlmhsperstatus', 'report'=>'repp_statusmhs5thn');
	$r_baru[] = array('label' => 'Rasio Dosen Per Prodi', 'val'=>'rasiodosenprodi', 'report'=>'repp_rasiodosprodi');
	$r_baru[] = array('label' => 'Rasio Dosen Per Fakultas', 'val'=>'rasiodosenfakultas', 'report'=>'repp_rasiodosfakultas');
	$r_baru[] = array('label' => 'Rasio Dosen Per Institut', 'val'=>'rasiodoseninstitut', 'report'=>'repp_rasiodosenmhs');
	
	$r_terbaru=array();
	$r_terbaru[] = array('label' => 'Nilai KKN', 'val'=>'nilaikkn', 'report'=>'repp_nilaikkn');
	$r_terbaru[] = array('label' => 'Beasiswa Mahasiswa', 'val'=>'beasiswamahasiswa', 'report'=>'repp_beasiswa');
	$r_terbaru[] = array('label' => 'Prestasi Mahasiswa', 'val'=>'prestasimahasiswa', 'report'=>'repp_prestasimhs');
	$r_terbaru[] = array('label' => 'Jurnal Perwalian', 'val'=>'jurnalperwalian', 'report'=>'repp_jumalperwalian');
	$r_terbaru[] = array('label' => 'Daftar Yudisium atau Wisuda', 'val'=>'daftaryudisium', 'report'=>'repp_daftaryudisium');
	$r_terbaru[] = array('label' => 'Distribusi dosen wali', 'val'=>'distribusidosenwali', 'report'=>'repp_distribusidosenwali');
	$r_terbaru[] = array('label' => 'Kapasitas Kelas', 'val'=>'kapasitaskelas', 'report'=>'repp_kapasitaskelas');
	$r_terbaru[] = array('label' => 'Karya Ilmiah Dosen', 'val'=>'karyailmiahdosen', 'report'=>'repp_karyailmiahdos');
	$r_terbaru[] = array('label' => 'Jadwal Skripsi', 'val'=>'jadwalskripsi', 'report'=>'repp_jadwalskripsi');
	$r_terbaru[] = array('label' => 'Nilai Ujian Skripsi', 'val'=>'nilaiujianskripsi', 'report'=>'repp_nilaiujiskripsi');
	$r_terbaru[] = array('label' => 'Perwalian', 'val'=>'perwalian', 'report'=>'repp_perwalian');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link rel="stylesheet" href="style/jquery-ui.css" />
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style>
	.menu{cursor:pointer;}
	.menu:hover {cursor:pointer; color:red}
	
	</style>
	<script>
	function openDiv(elem) {
		var val=elem.id;
		document.getElementById(val).style.display="block";
	}
	
	function hideDiv(elem) {
		var val=elem.id;
		document.getElementById(val).style.display="none";
	}
	
	</script>
</head>
<body>
<div id="main_content">
<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			  
			<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
				<span> 
					<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"><?= $p_title ?>
				</span>
			</div>
			 
			<div style="float:left; width:219px; margin-top:10px;border:1px solid #CCC">
				<form name="pageform" id="pageform" method="post">
						<header style="width:220px">
							<div class="inner">
								<div class="left title">
									<img id="img_workflow" width="24px" src="images/aktivitas/CARI.png" onerror="loadDefaultActImg(this)"> <h1>Laporan</h1>
								</div>
							</div>
						</header>
				 
				<div id="accordion" class="inner">
					<h3>Access</h3>
					<div>
						<?
							foreach ($r_access as $row){ 
								  $id=$row['val']; ?> 
										<span class="menu" 
											  onmouseover ="openDiv(<?=$id;?>)" 
											  onmouseout ="hideDiv(<?=$row['val']?>)" 
											  onClick="showPage('laporan','<?= Route::navAddress($row['report']) ?>')" >
									<?=$row['label'] ?>
								</span>
								<br> 
						<?	
							}
						?>
					</div>
					<h3>Baru</h3>
					<div>
						<?
							foreach ($r_baru as $row){
								  $id=$row['val']; ?> 
								<span class="menu" 
												onmouseover ="openDiv(<?=$id;?>)" 
												onmouseout ="hideDiv(<?=$row['val']?>)" 
												onClick="showPage('laporan','<?= Route::navAddress($row['report']) ?>')" >
									<?=$row['label'] ?>
								</span>
								<br> 
						<?	
							}
						?>
					</div>
					<h3>Terbaru</h3>
					<div>
						<?
							foreach ($r_terbaru as $row){
								  $id=$row['val']; ?>
								 
								<span class="menu" 
												onmouseover ="openDiv(<?=$id;?>)" 
												onmouseout ="hideDiv(<?=$row['val']?>)" 
												onClick="showPage('laporan','<?= Route::navAddress($row['report']) ?>')" >
									<?=$row['label'] ?>
								</span>
								<br>
								 
						<?	
							}
						?>
					</div>
				</div>
				</form>
				
			</div>
			<center>
			<div style="width:720px; float:left; padding:10px; margin-top:50px">
				<?
				foreach ($r_access as $row){
					$id=$row['val'];
					?>
					<div id="<?= $id?>" style="display:none">
						Preview Laporan <?= $row['label']?>
					</div>
				<? }  
				foreach ($r_baru as $row){
					$id=$row['val'];
					?>
					<div id="<?= $id?>" style="display:none">
						Preview Laporan <?= $row['label']?>
					</div>
				<? }  
				foreach ($r_terbaru as $row){
					$id=$row['val'];
					?>
					<div id="<?= $id?>" style="display:none">
						Preview Laporan <?= $row['label']?>
					</div>
				<? } ?>
			</div>
			</center>
		</div>
	</div>
</div>
	<script src="scripts/jquery-1.9.1.js"></script>
	<script src="scripts/jquery-ui.js"></script>
	<script>
	$(function() {
		$( "#accordion" ).accordion({
		heightStyle: "content"
		});
	});
	</script>
</body>
</html>