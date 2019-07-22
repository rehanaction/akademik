<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// variabel request
	$r_unit = CStr::removeSpecial($_POST['unit']);
	$r_periode = CStr::removeSpecial($_POST['periode']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('profile'));
	
	$connsia = Query::connect('akad');
	if($_SERVER['REMOTE_ADDR'] == "36.85.91.184") //ip public sevima
		$connsia->debug=true;
		
	// definisi variable halaman	
	$p_tbwidth = 900;
	$p_col = 8;
	$p_file = 'profiledosen_'.$r_unit;
	$p_model = 'mProfile';
	$p_window = 'Profil Dosen Tetap Dan Tidak Tetap';
	
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
	
	$namaperiode = $p_model::getNamaPeriode($connsia,$r_periode);
    $a_data = $p_model::getLapProfileDosen($conn,$r_periode,$r_unit);
	
	$rs = $a_data['list'];
	
	$p_title = 'Profil Dosen Tetap Dan Tidak Tetap <br />
				Unit '.$a_data['unit'].'<br />
				Semester / Tahun Akademik '.$namaperiode;
?>
<html>
<head>
<title><?= $p_window; ?></title>
<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
<link rel="icon" type="image/x-icon" href="images/favicon.png">
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
		<? require_once($conf['view_dir'].'inc_headerrep.php'); ?>
		<strong><font size="4" style="font-family:Times New Roman"><?= $p_title ?></font></strong>
		<br><br>
		<table width="<?= $p_tbwidth ?>" border="1" cellpadding="4" cellspacing="0">
			<tr bgcolor = "gray">
				<th><b style = "color:#FFFFFF">No</b></th>
				<th><b style = "color:#FFFFFF">KD</b></th>
				<th><b style = "color:#FFFFFF">Nama</b></th>
				<th><b style = "color:#FFFFFF">PEND.</b></th>
				<th><b style = "color:#FFFFFF">JAB. AKD.</b></th>
				<th><b style = "color:#FFFFFF">LBP</b></th>
				<th><b style = "color:#FFFFFF">KA</b></th>
			</tr>
			<? $i=1;$count=0;
				if (count($rs) > 0){
					$total_irpd = 0;
					$total_irkd = 0;
					foreach ($rs as $row) {
						$total_irpd += $row['irpd'];
						$total_irkd += $row['irkd'];
						$count++;
			?>
			<tr>
				<td><?= $i++; ?></td>
				<td><?= $row['nodosen']; ?></td>
				<td><?= $row['namalengkap']; ?></td>
				<td><?= $row['namapendidikan']; ?></td>
				<td><?= $row['jabatanfungsional']; ?></td>
				<td align="right"><?= $row['irpd']; ?></td>
				<td align="right"><?= $row['irkd']; ?></td>
			</tr>
			<? } ?>
			<tr bgcolor = "gray">
				<td colspan="5" align="right"><b style = "color:#FFFFFF"><strong>JUMLAH</strong></td>
				<td style = "color:#FFFFFF" align="right"><strong><?= $total_irpd; ?></strong></td>
				<td style = "color:#FFFFFF" align="right"><strong><?= $total_irkd; ?></strong></td>
			</tr>
			<?	}else{	?>
			<tr>
				<td colspan="<?= $p_col; ?>" align="center">Data tidak ditemukan</td>
			</tr>
			<? } ?>
		</table>
		<? if (count($rs) > 0){ ?>
		<br />
		<table width="<?= $p_tbwidth ?>" border="0" cellpadding="4" cellspacing="0">
			<tr>
				<td colspan="2">Indeks Rata-2 Pendidikan Dosen</td>
				<td colspan="2">Indeks Rata-2 Kepangkatan Dosen</td>
			</tr>
			<tr>
				<td>IRPD  :</td>
				<td>(Jml. Nilai Total) : (Jml. Dosen)</td>
				<td>IRPD  :</td>
				<td>(Jml. Nilai Total) : (Jml. Dosen)</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>(<?= $total_irpd; ?>) : (<?= $count; ?>)</td>
				<td>&nbsp;</td>
				<td>(<?= $total_irkd; ?>) : (<?= $count; ?>)</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td>(<?= $total_irpd/$count; ?>)</td>
				<td>&nbsp;</td>
				<td>(<?= $total_irkd/$count; ?>)</td>
			</tr>
			<tr>
				<td colspan="4">&nbsp;</td>
			</tr>
			<tr>
				<td><strong>ILBD  :</strong></td>
				<td colspan="3">{75% * (IRPD)} + {25% * (IRBK)}</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3">{75% * <?= $total_irpd/$count; ?>} + {25% * <?= $total_irkd/$count; ?>}</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
				<td colspan="3"><?= (0.75* $total_irpd/$count) + (0.25 * $total_irkd/$count); ?></td>
			</tr>
		</table>
		<? } ?>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>