<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_kodeunit = CStr::removeSpecial($_REQUEST['unit']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_semester = CStr::removeSpecial($_REQUEST['semester']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	if(is_array($_REQUEST['kode'])){
		if(count($_REQUEST['kode'])>0)
			$r_kode = implode("','",$_REQUEST['kode']);
	}else
		$r_kode = CStr::removeSpecial($_REQUEST['kode']);
	
	require_once(Route::getModelPath('angkakredit'));
	
	// definisi variable halaman	
	$p_tbwidth = 1000;
	$p_col = 8;
	$p_file = 'akbidang1a_'.$r_kodeunit.'_periode_'.$r_tahun.'_'.$r_semester;
	$p_model = 'mAngkaKredit';
	$p_window = 'Daftar Angka Kredit Bidang III';
	
	switch($r_format) {
		case "doc" :
			header("Content-Type: application/msword");
			header('Content-Disposition: attachment; filename="'.$p_namafile.'.doc"');
			break;
		case "xls" :
			header("Content-Type: application/msexcel");
			header('Content-Disposition: attachment; filename="'.$p_namafile.'.xls"');
			break;
		default : header("Content-Type: text/html");
	}
	
	$a_data = $p_model::getListBidang3($conn,$r_kode,($r_tahun.$r_semester));
	$rs = $a_data['list'];
	$a_det = $a_data['detail'];
	$a_col = $a_data['colspan'];
	
	$p_title = 'DAFTAR PERNYATAAN<br>MELAKSANAKAN KEGIATAN PENGABDIAN PADA MASYARAKAT';
?>
<html>
<head>
	<title><?= $p_window; ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<style>
		table { border-collapse:collapse }
		div,td,th {
			font-family:Verdana, Arial, Helvetica, sans-serif;
			font-size:12px;
		}
		td,th { border:1px solic black }
	</style>
</head>
<body>
<div align="center">
	<?
		while($row = $rs->FetchRow()){
			$atasan = $p_model::getAtasanPegawai($conn,$row['idpegawai']);
	?>
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth; ?>" cellpadding="2" border="0" style="border-collapse:collapse;">
			<tr>
				<td colspan="3">Yang bertanda tangan di bawah ini :</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td width="200px">Nama</td>
				<td width="12px">:</td>
				<td><?= $atasan['namalengkap']; ?></td>
			</tr>
			<tr>
				<td>NPP</td>
				<td>:</td>
				<td><?= $atasan['npp']; ?></td>
			</tr>
			<tr>
				<td>Pangkat/ Golongan Ruang</td>
				<td>:</td>
				<td><?= $atasan['namagolongan']; ?></td>
			</tr>
			<tr>
				<td>Jabatan Fungsional</td>
				<td>:</td>
				<td><?= $atasan['jabatanfungsional']; ?></td>
			</tr>
			<tr>
				<td>Unit Kerja</td>
				<td>:</td>
				<td><?= $atasan['namaunit']; ?></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">Menyatakan bahwa :</td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td>Nama</td>
				<td>:</td>
				<td><?= $row['namalengkap'] ?></td>
			</tr>
			<tr>
				<td>NPP</td>
				<td>:</td>
				<td><?= $row['npp']; ?></td>
			</tr>
			<tr>
				<td>Pangkat/ Golongan Ruang</td>
				<td>:</td>
				<td><?= $row['namagolongan'];?></td>
			</tr>
			<tr>
				<td>Jabatan Fungsional</td>
				<td>:</td>
				<td><?= $row['jabatanfungsional']; ?></td>
			</tr>
			<tr>
				<td>Unit Kerja</td>
				<td>:</td>
				<td><?= $row['namaunit'].' - '.$row['parentunit']; ?></td>
			</tr>
			<tr>
				<td colspan="3">&nbsp;</td>
			</tr>
			<tr>
				<td colspan="3">Telah melakukan kegiatan karya sebagai berikut :</td>
			</tr>
			<tr height="20">
				<td colspan="3"></td>
			</tr>
		</table>	
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr bgcolor = "gray">
				<th align="center" width="5%"><b  style = "color:#FFFFFF">No</b></th>
				<th align="center" width="30%"><b  style = "color:#FFFFFF">Kegiatan Pengabdian Pada Masyarakat</b></th>
				<th align="center" width="15%"><b  style = "color:#FFFFFF">Bentuk</b></th>
				<th align="center" width="15%%"><b  style = "color:#FFFFFF">Tempat/ Instansi</b></th>
				<th align="center" width="8%"><b  style = "color:#FFFFFF">Tanggal</b></th>
				<th align="center" width="7%"><b  style = "color:#FFFFFF">Jumlah Angka Kredit</b></th>
				<th align="center" width="15%%"><b  style = "color:#FFFFFF">Keterangan</b></th>
				<th align="center" width="5%"><b  style = "color:#FFFFFF">Lamp.</b></th>
			</tr>
			<?
				$i=0;$no=1;$kredit=0;$temp='';
				foreach($a_det[$row['idpegawai']] as $key => $val){
					$i++;
					if($temp != $val['indexakreditasiparent'])
						$nodet = 1;
					else
						$nodet++;
					
					$kredit += $val['nilaikredit'];
			?>
			<tr>
				<?if($temp != $val['indexakreditasiparent']){?>
				<td align="center" valign="top" rowspan="<?= $a_col[$row['idpegawai']][$val['indexakreditasiparent']]?>"><?= $no<10 ? '0'.$no : $no?></td>
				<?}?>
				<td>
					<?
					if($temp != $val['indexakreditasiparent']){
						echo $val['indexakreditasiparent'].'<br><br>';
						$valign = 'bottom';
					}
					else
						$valign = 'middle';
					echo '<table width="100%">';
					echo '	<tr>';
					echo '		<td valign="top">'.$nodet.'. </td>';
					echo '		<td>'.$val['indexakrediasi'].'</td>';
					echo '	</tr>';
					echo '</table>'; 
					?>
				</td>
				<td valign="<?= $valign;?>" align="center"><?= $val['bentuk']; ?></td>
				<td valign="<?= $valign;?>" align="center"><?= $val['tempatkegiatan']; ?></td>
				<td valign="<?= $valign;?>" align="center"><?= CStr::formatDate($val['tglmulai']); ?></td>
				<td valign="<?= $valign;?>" align="center"><?= $val['nilaikredit']; ?></td>
				<td valign="<?= $valign;?>"><?= $val['keterangan']; ?></td>
				<td valign="<?= $valign;?>" align="center"><?= $i; ?></td>
			</tr>
			<?
					if($temp != $val['indexakreditasiparent'])
						$no++;
					$temp = $val['indexakreditasiparent'];
				}
			?>
			<tr style="font-weight:bold">
				<td colspan="5" align="center">JUMLAH</td>
				<td align="right"><?= number_format($kredit,2)?></td>
				<td colspan="2">&nbsp;</td>
			</tr>
		</table>
		<br>
		<table width="<?= $p_tbwidth; ?>">
			<tr>
				<td width="70%" valign="top">Demikian pernyataan ini dibuat untuk dapat digunakan sebagai mestinya.</td>
				<td>Surabaya, <?= CStr::formatDateInd(date('Y-m-d'))?><br>Kepala <?= $atasan['namaunit']?><br><br><br><br><br><br>
					<?= empty($atasan['namalengkap']) ? ' - ' : $atasan['namalengkap']; ?>
				</td>
			</tr>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
		<br>
		<br>
		<br>
		<?} ?>
</div>
</body>
</html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>
