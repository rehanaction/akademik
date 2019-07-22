<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('laporankelas'));
	require_once(Route::getModelPath('combo'));
	
	// variabel request
	$r_tglawalkuliah = CStr::removeSpecial(CStr::formatDate($_REQUEST['tglawalkuliah']));
	$r_tglakhirkuliah = CStr::removeSpecial(CStr::formatDate($_REQUEST['tglakhirkuliah']));
	$r_periode = (int)$_REQUEST['tahun'].(int)$_REQUEST['semester'];
	$r_format = $_REQUEST['format'];
	
	
	// properti halaman
	$p_title = 'REKAPITULASI KEHADIRAN DOSEN '.Akademik::getNamaPeriode($r_periode);
	$p_title.= '<br>periode tanggal '.CStr::formatDateInd($r_tglawalkuliah).' - '.CStr::formatDateInd($r_tglakhirkuliah);
	$p_tbwidth = 750;
	$p_maxrow = 46;
	$p_maxday = 16;
	$p_namafile = 'realisasi_'.$r_periode;
	
	$a_fakultas=mCombo::fakultas($conn);
	
	/*$rs =  mLaporanKelas::getRekapKuliah($conn,$r_periode,$r_tglawalkuliah,$r_tglakhirkuliah);
	$a_data=array();
	$a_perkuliahan=array();
	while($row = $rs->FetchRow()) {
		$kodefakultas=$row['kodeunitparent'];
		$a_data[$kodefakultas][$row['perkuliahanke']]+=$row['jumlah'];
		$a_perkuliahan[$row['perkuliahanke']]=$row['perkuliahanke'];
	}*/
	
	$a_data =  mLaporanKelas::rekapralisasifak($conn,$r_periode,$r_tglawalkuliah,$r_tglakhirkuliah);
	
	$a_perkuliahan = array();
	foreach($a_data as $a_datafakultas){
		foreach($a_datafakultas as $perkuliahan=>$jmlkelas)
			$a_perkuliahan[$perkuliahan] = $perkuliahan;
	}
	sort($a_perkuliahan);
	Page::setHeaderFormat($r_format,$p_namafile);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<style>
		.tab_header { border-bottom: 1px solid black; margin-bottom: 5px }
		.div_headeritem { float: left }
		.div_preheader, .div_header { font-family: "Times New Roman" }
		.div_preheader { font-size: 10px; font-weight: bold }
		.div_header { font-size: 12px }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-size: 10px; padding: 2px }
		.tb_data th { background-color: #CFC; font-family: Arial; font-weight: bold }
		.tb_data td { font-family: Tahoma, Arial }
		
		.tb_foot { font-family: "Times New Roman"; font-size: 10px }
	</style>
</head>
<body>
<div align="center">
<?php if(!empty($a_data)) include('inc_headerlap.php'); ?>

	<div class="div_head"><?=strtoupper($p_title)?></div>
	<br>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<thead>
		<tr>
			<th  rowspan="3" width="15">No</th>
			<th  rowspan="3">Fakultas</th>
			<th  colspan="<?=count($a_perkuliahan)?>">Realisasi Pertemuan</th>
			<th  rowspan="2" width="50">TOTAL</th>
		</tr>
		<tr>
			<?php foreach($a_perkuliahan as $perkuliahan){ ?>
			<th><?=$perkuliahan?></th>
			<?php }?>
		</tr>
		</thead>
		<tbody>
		<?php $i=1;foreach($a_fakultas as $kodefakultas=>$namafakultas){ ?>
			<tr>
				<td align="center"><?=$i++?></td>
				<td align="left"><?=$kodefakultas?> <?=$namafakultas?></td>
				<?php 
				if(empty($a_perkuliahan))
					echo '<td>&nbsp;</td>';
					
				$right=0;
				foreach($a_perkuliahan as $perkuliahan){ 
				?>
				<td align="center">
					<?php
					if(isset($a_data[$kodefakultas][$perkuliahan])){
						$right+=$a_data[$kodefakultas][$perkuliahan];
						echo $a_data[$kodefakultas][$perkuliahan];
					}else
						echo 0;
					?>
				</td>
				<?php }?>
				<td align="center"><?=$right?></td>
			</tr>
		<?php } ?>
		</tbody>
	</table>
	
</div>
</body>
</html>
