<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Isi Nilai';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	
	$p_model = mKelas;
	
	// struktur view
	$a_kolom = array();
	// /$a_kolom[] = array('kolom' => ':no', 'label' => 'No');
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Prodi');
	
	$a_kolom[] = array('kolom' => 'thnkurikulum', 'label' => 'Thn Kur.');
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Kode Matkul');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama Matkul');
	$a_kolom[] = array('kolom' => 'kelasmk', 'label' => 'Kelas');
	$a_kolom[] = array('kolom' => 'sks', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'namapengajar', 'label' => 'Dosen Pengajar');
	$a_kolom[] = array('label' => 'Nilai TUGAS<br>(Jumlah Mahasiswa)');
	$a_kolom[] = array('label' => 'Nilai UTS<br>(Jumlah Mahasiswa)');
	$a_kolom[] = array('label' => 'Nilai UAS<br>(Jumlah Mahasiswa)');
	$a_kolom[] = array('kolom' => 'kuncinilai', 'label' => 'Kunci?');
	
	$p_colnum = count($a_kolom)+2;
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	


	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
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
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfilter.php'); ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						<th width="30">Link</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
					?>
					<?php 
						$a_tugas = $p_model::getJumlahNilaiTugas($conn, $row['thnkurikulum'], $row['kodemk'], $row['kelasmk'], $row['kodeunit'], $r_periode);
						$a_uts = $p_model::getJumlahNilaiUTS($conn, $row['thnkurikulum'], $row['kodemk'], $row['kelasmk'], $row['kodeunit'], $r_periode);
						$a_uas = $p_model::getJumlahNilaiUAS($conn, $row['thnkurikulum'], $row['kodemk'], $row['kelasmk'], $row['kodeunit'], $r_periode);
						 
					?>
					<?php foreach($a_tugas as $jumlahtugas){ ?>
						<?php foreach($a_uts as $jumlahuts){ ?>
							<?php foreach($a_uas as $jumlahuas){ ?>
								<tr valign="top" class="<?= $rowstyle ?>">
									
									<td><?= $row['kodeunit'] ?></td>
									<td><?= $row['thnkurikulum'] ?></td>
									<td><?= $row['kodemk'] ?></td>
									<td><?= $row['namamk'] ?></td>
									<td align="center"><?= $row['kelasmk'] ?></td>
									<td><?= $row['sks'] ?></td>
									<td><?= $row['namapengajar'] ?></td>
									<td align="center"><?= $jumlahtugas['jumlahmahasiswa'] ?></td>
									<td align="center"><?= $jumlahuts['jumlahmahasiswa'] ?></td>
									<td align="center"><?= $jumlahuas['jumlahmahasiswa'] ?></td>
									<td align="center"><?= empty($row['kuncinilai']) ? '' : '<img src="images/check.png">' ?></td>
									<td><img id="<?= Akademik::base64url_encode($t_key) ?>" title="Halaman Kuliah" src="images/link.png" onclick="goNilai(this)" style="cursor:pointer"></td>
								</tr>
							<?php } ?>
						<?php } ?>
					<?php } ?>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('set_nilai') ?>')">Isi Nilai</td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('view_nilai') ?>')">Pengumuman Nilai</td>
    </tr>
	<tr>
		<td><hr style="border:none;border-top:1px solid white;padding:0"></td>
	</tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('rep_pftgsuts') ?>')">PF-Tugas-UTS</td>
    </tr>
</table>
</div>

<script type="text/javascript">

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
});

function goNilai(elem) {
	// location.href = "<?= Route::navAddress('set_pesertakelas') ?>&key=" + elem.id;
	
	gParam = elem.id;
	showPage('key','<?= Route::navAddress('set_nilai') ?>');
}
</script>
</body>
</html>