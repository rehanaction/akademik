<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	list($kodeperiodepa,$idpenilai,$idpegawai) = explode('|',$r_key);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	if(empty($r_key)){
		$kodeperiodepa = CStr::removeSpecial($_REQUEST['periode']);
		$idpenilai = CStr::removeSpecial($_REQUEST['penilai']);
	}
	
	require_once(Route::getModelPath('pa'));
	require_once(Route::getModelPath('pegawai'));
	
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_file = 'hasilpenilaian_'.$kodeperiodepa;
	$p_model = 'mPa';
	$p_window = 'Penilaian Kinerja';
	
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
	
	//mendapatkan hasil penilaian
	$a_info = array();
	$a_info = $p_model::repHasilPenilaian($conn,$kodeperiodepa,$idpenilai,$idpegawai);
	
	$rs = $a_info['list'];
	$jmlskala = $a_info['jmlskala'];
	$kodeperiodebobot = $a_info['kodeperiodebobot'];
	
	//soal
	$a_data = array();
	$a_data = $p_model::listSoalPenilaian($conn, $kodeperiodebobot);
	$a_skalanilai = $p_model::getSkalaPenilaian($conn, $kodeperiodebobot);
	
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
<? 
	while($row = $rs->FetchRow()){
		$p_title = 'Penilaian SDM <br />';
		$p_title .= 'Tanggal Penilaian '.cstr::formatDateInd($row['tglpenilaian']);		
?>
	<div align="center" style="page-break-after:always">
		<? include($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<?$p_title ="";?>
		<table width="<?= $p_tbwidth ?>" style="border:1px solid" cellpadding="4" cellspacing="0">
			<tr bgcolor = "gray" style="border-bottom: 1px solid black;">
				<td colspan="2" style="border-right: 1px solid black"><strong style=" color:#FFFFFF">Yang dinilai</strong></td>
				<td colspan="2"><strong style=" color:#FFFFFF">Yang menilai</strong></td>
			</tr>
			<tr>
				<td width="15%"><strong>Nama</strong></td>
				<td width="35%" style="border-right: 1px solid black"><strong>:</strong> <?= $row['namadinilai']?></td>
				<td width="15%"><strong>Nama</strong></td>
				<td width="35%"><strong>:</strong> <?= $row['namapenilai']?> </td>
			</tr>
			<tr>
				<td><strong>Jabatan</strong></td>
				<td style="border-right: 1px solid black"><strong>:</strong> <?= $row['jabatandinilai']?></td>
				<td><strong>Jabatan</strong></td>
				<td><strong>:</strong> <?= $row['jabatanpenilai']?> </td>
			</tr>
			<tr>
				<td><strong>Unit</strong></td>
				<td style="border-right: 1px solid black"><strong>:</strong> <?= $row['unitdinilai']?></td>
				<td><strong>Unit</strong></td>
				<td><strong>:</strong> <?= $row['unitpenilai']?></td>
			</tr>
		</table>
		<br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse 1px">
				<tr bgcolor = "gray">
					<th><b  style = "color:#FFFFFF">No</b></th>
					<th><b  style = "color:#FFFFFF">Aspek Penilaian</b></th>
					<? for ($i = 1; $i <= $jmlskala; $i++){
						echo '<th width="50px"><b style="color:#FFFFFF"> '.$i.' </b></th>';
					}?>
				</tr>
				<? if (count($a_data) > 0){
						$i = 0;
						foreach($a_data[$row['idpegawai']] as $data){
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				?>
				<tr id="s<?= $data['urutan'] ?>" class="<?= $rowstyle ?>">
						<td align="center"><?= $data['urutan']; ?></td>
						<td><?= $data['namasoal']; ?></td>
						<? for ($x = 1; $x <= $jmlskala; $x++){?>
						<td align="center">
							<?  
								if ($data['nilai']==$x){?>
									<img src="<?= $conf['image_dir']?>check.png">
							<?}?>
						</td>
						<?} ?>
				</tr>
				<? }?>
				<tr>
					<td colspan="2" align="center"><strong>Total Nilai</strong></td>
					<td colspan="<?= $jmlskala?>" align="center"><strong><?= $row['nilaiakhir'] ?></strong></td>
				</tr>
				<?}else{ ?>
				<tr>
					<td colspan="<?= $jmlskala+2?>" align="center">Data Kosong</td>
				</tr>
				<? } ?>
			</tbody>
		</table>
		<br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0" style="border-collapse:collapse 1px">
			<tr bgcolor = "gray">
			<?if (count($a_skalanilai) > 0){
					foreach($a_skalanilai as $skalanilai){	
						echo '	<th><b  style = "color:#FFFFFF"> '.$skalanilai['nilaihuruf'].' </th>';
			}}?>
			</tr>
			<tr>
			<?if (count($a_skalanilai) > 0){
					foreach($a_skalanilai as $skalanilai){	
						echo '	<td align="center">'.$skalanilai['nilaibawah'].' - '.$skalanilai['nilaiatas'].'</td>';
			}}?>
			</tr>
			<tr>
			<?if (count($a_skalanilai) > 0){
					foreach($a_skalanilai as $skalanilai){	
			?>
				<td align="center">
				<? if($row['nilaiakhir'] >= $skalanilai['nilaibawah'] and $row['nilaiakhir'] <= $skalanilai['nilaiatas'])
					echo '&nbsp;<img src="'.$conf['image_dir'].'check.png">&nbsp;';
				?>	
				</td>
			<?}}?>
			</tr>
		</table>
	<? include($conf['view_dir'].'inc_footerrep.php'); ?>
	</div>
<?}?>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>