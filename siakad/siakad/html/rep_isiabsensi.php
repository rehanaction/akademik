<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('absensikuliah'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Laporan Absensi Kuliah';
	$p_tbwidth = 900;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_absensi';
	$p_printpage = 'rep_absensi';
	
	$p_model = mAbsensiKuliah;
	
	// variabel request
	$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);
	$a_data = mKelas::getDataPeserta($conn,$r_key);
	$a_kuliah = mKuliah::getListPerKelas($conn,$r_key,true);
	
	$p_kulnum = count($a_kuliah);
	$p_colnum = 3 + $p_kulnum;
	
	
	
	// mendapatkan data
	$a_absen = $p_model::getListPerKelas($conn,$r_key);
	
	$p_namafile = 'absensi_'.$r_kodeunit.'_'.$r_periode;
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
		.div_header { font-size: 12pt }
		.div_headertext { font-size: 9px; font-style: italic }
		
		.tb_head td, .div_head, .div_subhead { font-family: "Times New Roman" }
		.tb_head td, .div_head { font-size: 12px }
		.div_subhead { font-size: 11px; margin-bottom: 5px }
		.div_head { text-decoration: underline }
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
		<?php include('inc_headerlap.php') ?>
		<center>
			<?php include('inc_repheaderkelas.php') ?>
		</center>
		<br>	
				<table width="<?= $p_tbwidth ?>" class="tb_data">
					<?	/**********/
						/* HEADER */
						/**********/
						
						$t_rowspan = 3;
					?>
					<thead>
					<tr>
						<th rowspan="<?= $t_rowspan ?>" width="25">No.</th>
						<th rowspan="<?= $t_rowspan ?>">NAMA MAHASISWA</th>
						<th rowspan="<?= $t_rowspan ?>" width="80">NIM</th>
						<? if(!empty($p_kulnum)) { ?>
						<th colspan="<?= $p_kulnum ?>" style="white-space:nowrap">Tatap Muka / Tanggal</th>
						<? } ?>
					</tr>
					<tr>
						<?	foreach($a_kuliah as $t_kuliah) { ?>
						<th width="25"><?= $t_kuliah['perkuliahanke'] ?></th>
						<?	} ?>
					</tr>
					<tr>
						<?	foreach($a_kuliah as $t_kuliah) { ?>
						<th>
							<? /* <sup><?= (int)substr($t_kuliah['ftglkuliah'],-2) ?></sup>&frasl;<sub><?= (int)substr($t_kuliah['ftglkuliah'],-4,2) ?></sub> */ ?>
							<?= substr($t_kuliah['ftglkuliah'],-2) ?><br><?= Date::indoMonth(substr($t_kuliah['ftglkuliah'],-4,2),false) ?>
						</th>
						<?	} ?>
					</tr>
					</thead>
					<tbody>
					<?	
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = trim($row['nim']);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $i ?>.</td>
						<td><?= STRTOUPPER($row['nama']) ?></td>
						<td align="center"><?= $row['nim'] ?></td>
						<?	foreach($a_kuliah as $t_kuliah) {
								if(empty($a_absen[$t_kuliah['perkuliahanke']][$t_kuliah['tglkuliah']][$t_key][$t_kuliah['jeniskuliah']]))
                {
									$t_selected = 'A';                
                  
								}else{
									//$t_selected = $a_absen[$t_kuliah['perkuliahanke']][$t_key][$t_kuliah['jeniskuliah']];
                  //$t_selected = 'H';  
                  //$t_selected = $a_absen([$t_kuliah['perkuliahanke']][$t_kuliah['tglkuliah']][$t_key][$t_kuliah['jeniskuliah']]);
                  
								  $t_selected = $a_absen[$t_kuliah['perkuliahanke']][$t_kuliah['tglkuliah']][$t_key][$t_kuliah['jeniskuliah']];
									
								}
						?>
						<td align="center">
						
							<?=$t_selected?>
					
						</td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}?>
					</tbody>
				</table>
		</div>
	</div>


</body>
</html>