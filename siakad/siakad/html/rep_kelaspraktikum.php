<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('kuliah'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_basis = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Jadwal Praktikum';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mkelasPraktikum;
	
	
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);

	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_basis)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_basis);
	
	$a_data = $p_model::getReportKelas($conn,$a_kolom,$r_sort,$a_filter);
	
	$a_sistemkuliah = mCombo::sistemkuliah($conn);
	$a_unit = mUnit::getComboUnit($conn);
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
		.tb_head { border-bottom: 1px solid black }
		.tb_head td { font-size: 10px }
		.tb_head .mark { font-size: 11px }
		.div_head { font-size: 16px; text-decoration: underline }
		.div_subhead { font-size: 14px; margin-bottom: 5px }
		.div_head, .div_subhead { font-weight: bold }
		
		.tb_data { border: 1px solid black; border-collapse: collapse }
		.tb_data th, .tb_data td { border: 1px solid black; font-family: "Times New Roman"; padding: 1px }
		.tb_data th { background-color: #CFC; font-size: 11px }
		.tb_data td { font-size: 10px }
		.tb_data .noborder th { border-left: none; border-right: none }
		
		.tb_subfoot, .tb_foot { font-family: "Times New Roman" }
		.tb_subfoot { font-size: 11px; border-top: 1px solid black }
		.tb_foot { font-size: 10px; font-weight: bold; margin-top: 10px }
		.tb_foot .mark { font-size: 11px; font-weight: normal }
		.tb_foot .pad { padding-left: 30px }
	</style>
</head>
<body>
<div align="center">
	<?php echo $p_title?><br>
	<?=Akademik::getNamaPeriode($r_periode)?>
	<br><br>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<thead>
		<tr>
			<th>Pengelola</th>
			<th>Kode</th>
			<th>Nama MK</th>
			<th>Sesi</th>
			<th>Kelompok</th>
			<th>Basis</th>
			<th>Jenis</th>
			<th>Jadwal</th>
			<th>Pengajar</th>
			<th>Koordinator</th>
			<th>Ruang</th>
			<th>Peserta</th>
			<th>Kap.</th>
		</tr>
		</thead>
		<tbody>
		<?	
			$i = 0;
			foreach($a_data as $row) {	
			$i++;
			$r_key = $p_model::getKeyRow($row);
			$dosen = $p_model::getDosenPengajar($conn,$r_key);
		?>
		<tr>
			<td><?= $a_unit[$row['kodeunit']] ?></td>
			<td><?= $row['kodemk'] ?></td>
			<td><?= $row['namamk'] ?></td>
			<td><?= $row['kelasmk'] ?></td>
			<td><?= $row['kelompok'] ?></td>
			<td><?= $a_sistemkuliah[$row['sistemkuliah']] ?></td>
			<td>Teori</td>
			<td><?=Date::indoDay($row['nohari'])?>, <?=CStr::formatJam($row['jammulai'])?>-<?=CStr::formatJam($row['jamselesai'])?></td>
			<td>
			<?php
				foreach($dosen as $rowd)
					echo $rowd['nipdosen'].' - '.$rowd['nama'].'<br>';
			?>
			</td>
			<td>
			<?php
				foreach($dosen as $rowd){
					if(!empty($rowd['ispjmk']))
						echo $rowd['nipdosen'].' - '.$rowd['nama'].'<br>';
				}
			?>
			</td>
			<td><?= $row['koderuang'] ?></td>
			<td><?= $row['peserta'] ?></td>
			<td><?= $row['kapasitas'] ?></td>
			
		</tr>
		
		<?	}
			if($i == 0) {
		?>
		<tr>
			<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
		</tr>
		<?	} ?>
		</tbody>
	</table>				
</div>
</body>
</html>
