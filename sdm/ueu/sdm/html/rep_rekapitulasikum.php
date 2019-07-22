<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_POST['unit']);
	$r_tahun = CStr::removeSpecial($_POST['tahun']);
	$r_sem = CStr::removeSpecial($_POST['sem']);
	$r_tahun = empty($r_tahun) ? date('Y') : $r_tahun;
	$r_format = CStr::removeSpecial($_REQUEST['format']);

	require_once(Route::getModelPath('angkakredit'));
	
	// definisi variable halaman	
	$p_tbwidth = 1100;
	$p_col = 13;
	$p_file = 'rekapkum';
	$p_model = 'mAngkaKredit';
	$p_window = 'Rekapitulasi KUM';
	
	// header
	switch($r_format) {
		case 'doc';
			header("Content-Type: application/msword");
			header('Content-Disposition: attachment; filename="'.$p_file.'.doc"');
			break;
		case 'xls' :
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="'.$p_file.'.xls"');
			break;
		default : header("Content-Type: text/html");
	}
	
	$semkredit1a = $p_model::getRWTKredit($conn,'ak_bidang1a',$r_unit,$r_tahun,$r_sem);
	$semkredit1b = $p_model::getRWTKredit($conn,'ak_bidang1b',$r_unit,$r_tahun,$r_sem);
	$semkredit2 = $p_model::getRWTKredit($conn,'ak_bidang2',$r_unit,$r_tahun,$r_sem);
	$semkredit3 = $p_model::getRWTKredit($conn,'ak_bidang3',$r_unit,$r_tahun,$r_sem);
	$semkredit4 = $p_model::getRWTKredit($conn,'ak_bidang4',$r_unit,$r_tahun,$r_sem);
	
    $data = $p_model::repRekapitulasiKUM($conn,$r_unit);
	$a_pegawai = $data['a_pegawai'];
	$namaunit = $data['namaunit'];
	$a_idunit = $data['a_idunit'];
	$a_unit = $data['a_unit'];

	$a_sem = $p_model::PeriodeSemester();
	
	if(!empty($a_idunit))
		$a_idunit = array_unique($a_idunit);
	
?>
<html>
<head>
<title><?= $p_window; ?></title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<style>
table { border-collapse:collapse }
div,td,th {
font-family:Verdana, Arial, Helvetica, sans-serif;
font-size:10px;
}
td,th { border:1px solic black }
td{font-weight:normal;}
</style>
</head>
<body>
<div align="center">
		<strong>
		<br />
		<table width="<?= $p_tbwidth ?>" cellpadding="1" cellspacing="0">
			<tr>
				<td colspan="2" ><strong>REKAPITULASI KUM</strong></td>
			</tr>
			<tr>
				<td colspan="2" ><strong><?= strtoupper($namaunit)?></strong></td>
			</tr>
			<tr>
				<td colspan="2" ><strong>Tahun Ajaran <?= $r_tahun?>/<?= $r_tahun+1?> <?= !empty($r_sem) ? $a_sem[$r_sem] : ''?></strong></td>
			</tr>
		</table>
		<br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr>
				<th bgcolor="#CCCCCC" rowspan="5"><strong>No</strong></th>
				<th width="180px" bgcolor="#CCCCCC" rowspan="5"><strong>Nama Dosen</strong></th>
				<th bgcolor="#CCCCCC" rowspan="5"><strong>KD</strong></th>
				<th bgcolor="#CCCCCC" rowspan="5"><strong>NIDN</strong></th>
				<th bgcolor="#CCCCCC" colspan="2"><strong>Jab. Akademik</strong></th>
				<th bgcolor="#CCCCCC" colspan="6"><strong><?= $r_tahun.(!empty($r_sem) ? ' '.$a_sem[$r_sem] : '')?></strong></th>
			</tr>
			<tr>
				<th bgcolor="#CCCCCC" rowspan="4"><strong>Jab.</strong></th>
				<th bgcolor="#CCCCCC" rowspan="4"><strong>KUM</strong></th>
				<th bgcolor="#CCCCCC" colspan="5"><strong>Pengembangan KUM</strong></th>
				<th bgcolor="#CCCCCC" rowspan="4"><strong>Total KUM</strong></th>
			</tr>
			<tr>
				<th bgcolor="#CCCCCC" colspan="5"><strong>Tridarma Perguruan Tinggi</strong></th>
			</tr>
			<tr>
				<th bgcolor="#CCCCCC" colspan="2"><strong>Pendidikan & Pengajaran</strong></th>
				<th bgcolor="#CCCCCC"><strong>Penelitian</strong></th>
				<th bgcolor="#CCCCCC"><strong>Abdimas</strong></th>
				<th bgcolor="#CCCCCC"><strong>Penunjang</strong></th>
			</tr>
			<tr>
				<th bgcolor="#CCCCCC" ><strong>IA</strong></th>
				<th bgcolor="#CCCCCC" ><strong>IB</strong></th>
				<th bgcolor="#CCCCCC" ><strong>Bidang II</strong></th>
				<th bgcolor="#CCCCCC" ><strong>Bidang III</strong></th>
				<th bgcolor="#CCCCCC" ><strong>Bidang IV</strong></th>
			</tr>
			
		<?
		$total = 0;
		if(!empty($a_idunit)){
			$totalpeg = 0;
			foreach($a_idunit as $idunit){
			$totalpeg += count($a_pegawai[$idunit]);
			?>
			<tr>
				<td colspan="18"><strong><?= strtoupper($a_unit[$idunit])?></strong></td>
			<tr>
			<?if(!empty($a_pegawai[$idunit])){
				$no = 1;
				$subtotbid1a = 0;
				$subtotbid1b = 0;
				$subtotbid2 = 0;
				$subtotbid3 = 0;
				$subtotbid4 = 0;
				$subtot = 0;
				foreach($a_pegawai[$idunit] as $pegawai){?>
				<tr>
					<td align="center"><?= $no++;?></td>
					<td><?= $pegawai['namalengkap']?></td>
					<td><?= $pegawai['nodosen']?></td>
					<td><?= $pegawai['nidn']?></td>
					<td align="center"><?= $pegawai['kodefungsional']?></td>
					<td align="center"><?= $pegawai['angkakredit']?></td>
					<td align="center">
					<?
						$nkredit1a = 0;
						if(!empty($semkredit1a[$pegawai['idpegawai']]))
							$nkredit1a = array_sum($semkredit1a[$pegawai['idpegawai']]);
						echo $nkredit1a;
					?>
					</td>
					<td align="center">
					<?
						$nkredit1b = 0;
						if(!empty($semkredit1b[$pegawai['idpegawai']]))
							$nkredit1b = array_sum($semkredit1b[$pegawai['idpegawai']]);
						echo $nkredit1b;
					?>
					</td>
					<td align="center">
					<?
						$nkredit2 = 0;
						if(!empty($semkredit2[$pegawai['idpegawai']]))
							$nkredit2 = array_sum($semkredit2[$pegawai['idpegawai']]);
						echo $nkredit2;
					?>
					</td>
					<td align="center">
					<?
						$nkredit3 = 0;
						if(!empty($semkredit3[$pegawai['idpegawai']]))
							$nkredit3 = array_sum($semkredit3[$pegawai['idpegawai']]);
						echo $nkredit3;
					?>
					</td>
					<td align="center">
					<?
						$nkredit4 = 0;
						if(!empty($semkredit4[$pegawai['idpegawai']]))
							$nkredit4 = array_sum($semkredit4[$pegawai['idpegawai']]);
						echo $nkredit4;
					?>
					</td>
					<? $totsem = $nkredit1a + $nkredit1b + $nkredit2 + $nkredit3 + $nkredit4;?>
					<td align="center">
					<?= $totsem?>
					</td>
				<tr>
			<?
					$subtotbid1a += $nkredit1a;
					$subtotbid1b += $nkredit1b;
					$subtotbid2 += $nkredit2;
					$subtotbid3 += $nkredit3;
					$subtotbid4 += $nkredit4;
					$subtot += $totsem;
				}
			?>
				<tr>
					<td align="right" colspan="6"><strong>Total <?= $a_unit[$idunit]?></strong></td>
					<td align="center"><strong><?= $subtotbid1a?></strong></td>
					<td align="center"><strong><?= $subtotbid1b?></strong></td>
					<td align="center"><strong><?= $subtotbid2?></strong></td>
					<td align="center"><strong><?= $subtotbid3?></strong></td>
					<td align="center"><strong><?= $subtotbid4?></strong></td>
					<td align="center"><strong><?= $subtot?></strong></td>
				<tr>
				<tr>
					<td align="center"><strong><?= $no-1?></strong></td>
					<td colspan="9"></td>
					<td><strong>Rata-rata</strong></td>
					<td align="center"><strong><?= round($subtot/($no-1),2) ?></strong></td>
				</tr>
			<?}
				$total += $subtot;
			}
			?>
			<tr>
				<td colspan="11" align="right"><strong>Total <?= $namaunit?></strong></td>
				<td align="center"><strong><?= round($total,2) ?></strong></td>
			<tr>
			<tr>
				<td colspan="11" align="right"><strong>Rata-rata</strong></td>
				<td align="center"><strong><?= round($total/$totalpeg,2)?></strong></td>
			<tr>
		<?}?>
		</table>
			<br />
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>