<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	
	$r_nama = Akademik::getNamaMahasiswa($conn,$r_key);
	
	// properti halaman
	$p_title = 'Jadwal Perkuliahan';
	$p_tbwidth = "100%";
	$p_aktivitas = 'JADWAL';
	
	$p_model = mKRS;
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_dataminggu = $p_model::getDataJadwalMingguan($conn,$r_key,$a_infomhs);
	$a_datahari = $p_model::getDataJadwalHarian($conn,$r_key,$a_infomhs);
//$conn->debug=true;
	$a_dataujian=$p_model::getDataJadwalUjian($conn,$r_key,$a_infomhs);
	// array terjemah
	$a_jeniskuliah = mKuliah::jenisKuliah($conn);
	$a_statuskuliah = mKuliah::statusKuliah();
	//print_r($a_dataujian);
	$a_online=array('0'=>'Tatap Muka','-1'=>'Online');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center>
			<?php require_once('inc_headermhs.php') ?>
			</center>
			<br>
			
			<center>
				<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
					<span>
						<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
						&nbsp;Jadwal Perkuliahan Mingguan 
					</span>
				</div>
			</center>
			
			<br>
			<? /*
			<?php
				$n = count($a_dataminggu);
				
				for($i=0;$i<$n;$i++) {
				
					$row = $a_dataminggu[$i];
					$jadwalkey=$row['periode'].'|'.$row['kodeunit'].'|'.$row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kelasmk'];
					
					if($row['nohari'] != $t_nohari) {
						$j = 0;
						$t_nohari = $row['nohari'];
			?>
			<center>
				
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<tr>
					<th colspan="7" class="SubHeaderBG"><?= $row['namahari'] ?></th>
				</tr>
				<tr>
					<th width="60">Mulai</th>
					<th width="60">Selesai</th>
					<th width="90">Kode MK</th>
					<th>Mata Kuliah</th>
					<th width="40">Sesi</th>
					<th width="80">Ruang</th>
					<th>Detail</th>
				</tr>
			<?php
					}
					
					if ($j % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $j++;
			?>
				<tr valign="top" class="<?= $rowstyle ?>">
					<td align="center"><?= CStr::formatJam($row['jammulai']) ?></td>
					<td align="center"><?= CStr::formatJam($row['jamselesai']) ?></td>
					<td align="center"><?= $row['kodemk'] ?></td>
					<td><?= $row['namamk'] ?></td>
					<td align="center"><?= $row['kelasmk'] ?></td>
					<td align="center"><?= $row['koderuang'] ?></td>
					<td align="center">
					<? if ( true ) { ?>
					<img src="images/link.png" id="<?= $jadwalkey ?>" onClick="goOpenpage(this)" style="cursor:pointer" title="Detail Jadwal">
					<? } ?>
				</tr>
			<?php
					if($i < $n)
						$rown = $a_dataminggu[$i+1];
					
					if($i >= $n or $rown['nohari'] != $t_nohari) {
			?>
			</table>
			</center>
			<br>
			<?php
					}
				}
			?>
			
			<br>
			*/?>
			
			<?php
					foreach($a_dataminggu as $t_nohari => $t_kelas) {
						$i = 0;
			?>

			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
				<tr>
					<th colspan="9" class="SubHeaderBG"><?= Date::indoDay($t_nohari) ?></th>
				</tr>
				<tr>
					
					<th width="70">Jam</th>
					<th align="left">Mata Kuliah</th>
					<th width="40">Kelas</th>
					<th width="80">Ruang</th>
					<th width="80">Status Kuliah</th>
					<th width="30">Detail</th>
					
				</tr>

			<?php
						foreach($t_kelas as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							//$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
							$jadwalkey=$row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['periode'].'|'.$row['kelasmk'].'|K|1';
							// pewarnaan
							
			?>
				<tr class="<?= $rowstyle ?> <?= $t_class ?>">
					<td align="center"><?= CStr::formatJam($row['jammulai']) ?>-<?= CStr::formatJam($row['jamselesai']) ?></td>
					<td><?= $row['kodemk'] ?> - <?= $row['namamk'] ?></td>
					<td align="center"><?= $row['kelasmk'] ?></td>
					<td align="center"><?= $row['koderuang'] ?></td>
					<td align="center"><?= $row['isonline'] ?></td>
					<td align="center">
					<img src="images/link.png" id="<?= $jadwalkey ?>" onClick="goOpenpage(this)" style="cursor:pointer" title="Detail Jadwal">
					
				</tr>
			<?php
						}
			?>
			</table>
			<br>
			<?php
					}
					
			?>
			

	
			
			<center>
				<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
					<span>
						<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
						&nbsp;Jadwal Perkuliahan Harian
					</span>
				</div>
			</center>
			<br>
			<?php 
				$n = count($a_datahari);
				for($i=0;$i<$n;$i++) {
					$row = $a_datahari[$i];
					
					if($row['tglkuliahrealisasi'] != $t_tanggal) {
						$j = 0;
						$t_tanggal = $row['tglkuliahrealisasi'];
			?>
			<center>
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<tr>
					<th colspan="9" class="SubHeaderBG"><?= $row['namahari'] ?>, <?= CStr::formatDateInd($row['tglkuliahrealisasi']) ?></th>
				</tr>
				<tr>
					<th width="30">Pert.</th>
					<th width="80">Jam</th>
					<th align="left">Mata Kuliah</th>
					<th>Pengajar</th>
					<th width="50">Jenis</th>
					<th width="50">Perkuliahan</th>
					<th>Topik</th>
					<th width="60">Ruang</th>
				</tr>
			<?php
					}
					
					if ($j % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $j++;
					if($row['tglkuliah']==$row['tglkuliahrealisasi'] or $row['waktumulai']==$row['waktumulairealisasi'] or $row['waktuselesai']==$row['waktuselesairealisasi'] or $row['koderuang']==$row['koderuangrealisasi'])
						$rowstyle = "GrayBG";
					else
						$rowstyle = "RedBG";
					/*switch ($row['statusperkuliahan']) {
						case 'J' :
							$rowstyle = "GreenBG";
							break;
						case 'S' :
							$rowstyle = "GrayBG";
							break;
						case 'B' :
							$rowstyle = "RedBG";
							break;
					}*/
			?>
				<tr valign="top" class="<?= $rowstyle ?>">
					<td align="center"><?= $row['perkuliahanke'] ?></td>
					<td align="center"><?= CStr::formatJam($row['waktumulairealisasi']) ?> - <?= CStr::formatJam($row['waktuselesairealisasi']) ?></td>
					<td><?= $row['kodemk'] ?> - <?= $row['namamk'] ?></td>
					<td><?= $row['namadosen'] ?></td>
					<td align="center"><?= $a_jeniskuliah[$row['jeniskuliah']] ?></td>
					<td align="center"><?= $row['isonline'] ?></td>
					<td><?= $row['topikkuliah'] ?></td>
					<td align="center"><?= $row['koderuangrealisasi'] ?></td>
				</tr>
			<?php
					if($i < $n)
						$rown = $a_datahari[$i+1];
					
					if($i >= $n or $rown['tglkuliahrealisasi'] != $t_tanggal) {
			?>
			</table>
			</center>
			<br>
			<?php
					}
				}
			?>
			
			<!-- Jadwal Ujian -->
			
			<center>
				<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
					<span>
						<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
						&nbsp;Jadwal UTS/UAS
					</span>
					
				</div>
				<br>
				<div style="width:<?= $p_tbwidth ?>px;" align="left">*)Pastikan tagihan yang berhubungan dengan ujian telah lunas</div>
			</center>
			<br>
			<?php 
				$n = count($a_dataujian);
				for($x=0;$x<$n;$x++) {
					$row = $a_dataujian[$x];
					
					if($row['tglujian'] != $t_tanggalujian) {
						$j = 0;
						$t_tanggalujian = $row['tglujian'];
			?>
			<center>
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
				<tr>
					<th colspan="8" class="SubHeaderBG"><?= Date::IndoDay(date('N',strtotime($row['tglujian']))) ?>, <?= CStr::formatDateInd($row['tglujian']) ?></th>
				</tr>
				<tr>
					<th width="50">Hari</th>
					<th width="150">Jam</th>
					<th align="left">Mata Kuliah</th>
					<th width="40">Kelas</th>
					<th width="40">Jenis Ujian</th>
					<th width="60">Ruang</th>
				</tr>
			<?php
					}
					
				if ($j % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $j++;
					
			?>
				<tr valign="top" class="<?=$rowstyle?>">

					<td align="center"><?= Date::IndoDay(date('N',strtotime($row['tglujian']))) ?></td>
					<td align="center"><?= CStr::formatJam($row['waktumulai']) ?> - <?= CStr::formatJam($row['waktuselesai']) ?></td>
					<td><?= $row['kodemk'] ?> - <?= $row['namamk'] ?></td>
					<td align="center"><?= $row['kelasmk'] ?></td>
					<td align="center"><?= $row['jenis_ujian'] ?></td>
					<td><center><?= $row['koderuang'] ?></center></td>
					
				</tr>
			<?php
					if($x < $n)
						$rown = $a_dataujian[$x+1];
					
					if($x >= $n or $rown['tglujian'] != $t_tanggalujian) {
			?>
			</table>
			
			</center>
			<br>
			<?php
					}
				}
			?>
			
		</div>
	</div>
	
</div>

<form name="pageform" id="pageform" method="post">
	<input type="hidden" id="key" name="key">
</form>

</body>
</html>
<script>
	function goOpenpage(elem){
		document.getElementById("pageform").action = "<?= Route::navAddress('detail_jadwalblock')?>";
		document.getElementById("key").value = elem.id;
		document.getElementById("pageform").target="_blank";
		goSubmit();
	
		}
</script>
