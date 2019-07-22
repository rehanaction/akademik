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
	$p_file = 'rekapprofiledosen_'.$r_unit;
	$p_model = 'mProfile';
	$p_window = 'Rekapitulasi Indeks Latar Belakang Dosen (ILBD)';
	
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
	
    $a_data = $p_model::getLapRekapILBD($conn,$r_periode,$r_unit);
	$a_periode = $p_model::getCPeriodeAkademik($connsia,$r_periode);
	
	$a_fakultas = $a_data['fakultas'];
	$a_prodi = $a_data['prodi'];
	$a_dekanat = $a_data['dekanat'];
	$a_nilai = $a_data['list'];
	$a_nilaifak = $a_data['listfak'];
	$a_count = $a_data['count'];
	$a_countfak = $a_data['countfak'];
	
	//perhitungan ilbd
	$a_ilbd = array();
	if (count($a_fakultas['idunit']) > 0){
		foreach ($a_fakultas['idunit'] as $inc => $idunit) {
			if(count($a_periode)>0){
				foreach($a_periode as $pkey => $namaperiode){
					if (!empty($a_nilaifak['irpd'][$pkey][$idunit]) or ($a_nilaifak['irkd'][$pkey][$idunit]))
						$a_ilbdfak[$pkey][$idunit] = round((0.75 * ($a_nilaifak['irpd'][$pkey][$idunit]/$a_countfak[$pkey][$idunit])) + (0.25 * ($a_nilaifak['irkd'][$pkey][$idunit]/$a_countfak[$pkey][$idunit])),2);
					else
						$a_ilbdfak[$pkey][$idunit] = 0;
				}
			}
			
			if (count($a_prodi['idunit'][$idunit]) > 0){ 
				foreach($a_prodi['idunit'][$idunit] as $incp => $idprodi){
					$rowspan[$idunit]++;
					if(count($a_periode)>0){
						foreach($a_periode as $pkey => $namaperiode){
							if (!empty($a_nilai['irpd'][$pkey][$idprodi]) or ($a_nilai['irkd'][$pkey][$idprodi]))
								$a_ilbd[$pkey][$idprodi] = round((0.75 * ($a_nilai['irpd'][$pkey][$idprodi]/$a_count[$pkey][$idprodi])) + (0.25 * ($a_nilai['irkd'][$pkey][$idprodi]/$a_count[$pkey][$idprodi])),2);
							else
								$a_ilbd[$pkey][$idprodi] = 0;
						}
					}
					
					if (count($a_dekanat['idunit'][$idprodi]) > 0){ 
						foreach($a_dekanat['idunit'][$idprodi] as $incd => $iddekanat){
							$rowspan[$idunit]++;
							if(count($a_periode)>0){
								foreach($a_periode as $pkey => $namaperiode){
									if (!empty($a_nilai['irpd'][$pkey][$iddekanat]) or ($a_nilai['irkd'][$pkey][$iddekanat]))
										$a_ilbddek[$pkey][$iddekanat] = round((0.75 * ($a_nilai['irpd'][$pkey][$iddekanat]/$a_count[$pkey][$iddekanat])) + (0.25 * ($a_nilai['irkd'][$pkey][$iddekanat]/$a_count[$pkey][$iddekanat])),2);
									else
										$a_ilbddek[$pkey][$iddekanat] = 0;
								}
							}
						}
					}
				}
			}
		}
	}
		
	$p_title = 'Rekapitulasi <br />
				Indeks Latar Belakang Dosen (ILBD) <br />
				Tahun Akademik '.$r_periode;
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
				<th style = "color:#FFFFFF">No</th>
				<th style = "color:#FFFFFF">FAKULTAS</th>				
				<? 
					if(count($a_periode)>0){
						foreach($a_periode as $pkey => $namaperiode){
				?>
				<th style = "color:#FFFFFF">ILBD <br /><?= $namaperiode?></th>
				<? } }?>
			</tr>
			<? $i=1;
				if (count($a_fakultas['idunit']) > 0){
					foreach ($a_fakultas['idunit'] as $inc => $idunit) {
			?>
			<tr>
				<td rowspan="<?= $rowspan[$idunit]+1; ?>" align="right" valign="top"><?= $i++; ?>.</td>
				<td><strong><?= $a_fakultas['namaunit'][$inc]; ?></strong></td>
				<? 
					if(count($a_periode)>0){
						foreach($a_periode as $pkey => $namaperiode){
				?>
				<td align="right"><strong><?= $a_ilbdfak[$pkey][$idunit] != 0 ? $a_ilbdfak[$pkey][$idunit] : '-'; ?><strong></td>
				<?}}?>
			</tr>
			<? if (count($a_prodi['idunit'][$idunit]) > 0){ 
					foreach($a_prodi['idunit'][$idunit] as $incp => $idprodi){
			?>
			<tr>
				<td style="border-top:1px solid white;"><?= $a_prodi['namaunit'][$idunit][$incp]; ?></td>
				<? 
					if(count($a_periode)>0){
						foreach($a_periode as $pkey => $namaperiode){
				?>
				<td style="border-top:1px solid white;" align="right"><?= $a_ilbd[$pkey][$idprodi] != 0 ? $a_ilbd[$pkey][$idprodi] : '-'; ?></td>
				<?}}?>
			</tr>
			<? if (count($a_dekanat['idunit'][$idprodi]) > 0){ 
					foreach($a_dekanat['idunit'][$idprodi] as $incd => $iddekanat){
			?>
			<tr>
				<td style="border-top:1px solid white;"><?= $a_dekanat['namaunit'][$idprodi][$incd]; ?></td>
				<? 
					if(count($a_periode)>0){
						foreach($a_periode as $pkey => $namaperiode){
				?>
				<td style="border-top:1px solid white;" align="right"><?= $a_ilbddek[$pkey][$iddekanat] != 0 ? $a_ilbddek[$pkey][$iddekanat] : '-'; ?></td>
				<?}}?>
			</tr>
			<? }}}}}
			}else{	?>
			<tr>
				<td colspan="<?= $p_col; ?>" align="center">Data tidak ditemukan</td>
			</tr>
			<? } ?>
		</table>
<? require_once($conf['view_dir'].'inc_footerrep.php'); ?>
</div>
</body>
 </html>
 <?	// cetak ke pdf
	if($r_format == 'pdf')
		Page::saveWkPDF($p_file.'.pdf');
?>