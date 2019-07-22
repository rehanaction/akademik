<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	//ini_set('display_errors', 1);
	//error_reporting(E_ALL);
	// include
//	$conn->debug=-true;
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('kuliah'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_tglkuliah 	= Modul::setRequest($_POST['tglkuliah'],'TGLKULIAH');
	if(!empty($r_tglkuliah))
		$r_tglkuliah=date('Y-m-d',strtotime($r_tglkuliah));
	else
		$r_tglkuliah=date('Y-m-d');
	$r_nohari = date('N',strtotime($record['tglkuliahrealisasi']));
	
	
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Monitoring Kegiatan Perkuliahan';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mKuliah;
	
	
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = 'namamk,kelasmk,jeniskuliah';
	$a_filter = Page::setFilter($_POST['filter']);

	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_tglkuliah)) $a_filter[] = $p_model::getListFilter('tglkuliah',$r_tglkuliah);
	
	$a_data = $p_model::getReportListPerkuliahan($conn,$a_kolom,$r_sort,$a_filter);
	
	$a_jenispertemuan=$p_model::jenisKuliah($conn);
	$status=array('0'=>'Tatap Muka','-1'=>'Online');
	$statusKuliah=$p_model::statusKuliah();
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
	Jadwal Perkuliahan<br>
	<?=Date::indoDay($r_nohari)?> <?=CStr::formatDateInd($r_tglkuliah)?>
	<br><br>
	<table class="tb_data" width="<?= $p_tbwidth ?>">
		<thead>
		<tr>
			<th>No</th>
			<th>Kode MK</th>
			<th>Nama Matakuliah</th>
			<th>Sesi</th>
			<th>Jenis</th>
			<th>Dosen Pengajar</th>
			<th>Waktu</th>
			<th>Ruang</th>
			<th>Pertemuan</th>
			<th>Status</th>
		</tr>
		</thead>
		<tbody>
		<?	
			$i = 0;
			foreach($a_data as $row) {	
			$i++;
		?>
		<tr>
			<td align="center"><?=$i?></td>
			<td><?= $row['kodemk'] ?></td>
			<td><?= $row['namamk'] ?></td>
			<td><?= $row['kelasmk'] ?></td>
			<td><b><?= $a_jenispertemuan[$row['jeniskuliah']] ?></b></td>
			<td><?= ($row['namadosenperencanaan']=='')?$row['namadosenperencanaan']:$row['namadosenrealisasi'] ?></td>
			<td><?= $row['wakturealisasi'] ?></td>
			<td><?= $row['koderuang'] ?></td>
			<td><?= $status[$row['isonline']] ?></td>
			<td><?= $statusKuliah[$row['statusperkuliahan']] ?></td>
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
