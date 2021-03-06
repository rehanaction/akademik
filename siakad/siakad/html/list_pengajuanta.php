<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_ratakan = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('skripsi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	// $r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	//jika yg login mhs
	if(Modul::getRole()=='M')
		$r_tahun = $conn->GetOne("select substring(periodemasuk,1,4) as periodemasuk from akademik.ms_mahasiswa where nim='".Modul::getUserName()."'");
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false,true);
	// $l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun_angkatan($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	// tambahan
	$r_periode = $r_tahun;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No');
	$a_kolom[] = array('kolom' => 'm.nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'topikta', 'label' => 'Topik Skripsi');
	$a_kolom[] = array('kolom' => 'judulta', 'label' => 'Judul Skripsi');
	$a_kolom[] = array('kolom' => 'statuspengajuanta', 'label' => 'Status');
	$a_kolom[] = array('kolom' => 'pemb1', 'label' => 'Dosen Pembimbing');
	$a_kolom[] = array('kolom' => 'pemb2', 'label' => 'Dosen Pembimbing 2');
	
	// properti halaman
	$p_title = 'Data Pengajuan Skripsi';
	$p_tbwidth = 900;
	$p_aktivitas = 'ABSENSI';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mSkripsi;
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
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode) and $r_periode!='*') $a_filter[] = $p_model::getListFilter('periodemasuk',$r_periode);
	
	if(Modul::getRole() == 'M')
		$a_filter[] = $p_model::getListFilter('nim_skripsi',Modul::getUserName());
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	if(Modul::getRole() == 'A' or Modul::getRole() == 'KTA')
		$a_filtercombo[] = array('label' => 'Angkatan', 'combo' => $l_tahun);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?	if($p_headermhs) { ?>
				<center>
				<?php require_once('inc_headermhs.php') ?>
				</center>
				<br>
				<?	} ?>
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
							<?	if(!empty($r_page)) { ?>
							<div class="right">
								<?php require_once('inc_listnavtop.php'); ?>
								<div class="addButton" style="float:left;margin-left:10px" onClick="goNew()">+</div>
							</div>
							<?	}?>
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
						<?	}
							if($c_ratakan) { ?>
						<th width="50">Detail</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
							switch($row['statuspengajuanta']){
								case "P" : $status="pengajuan"; $color="#EDF252"; break;
								case "S" : $status="Disetujui"; $color="#A2ED58"; break;
								case "T" : $status="ditolak";  $color="#F5563D"; break;
							}
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						
						<td><?= $i ?>.</td>
						<td><?= $row['nim'] ?></td>
						<td><?= $row['nama'] ?></td>
						<td><?= $row['topikta'] ?></td>
						<td><?= $row['judulta'] ?></td>
						<td style="background:<?=$color?>"><?= $row['statuspengajuanta'] ?></td>
						<td><?= $row['pemb1'] ?></td>
						<td><?= $row['pemb2'] ?></td>
						<?	
							if($c_ratakan) { ?>
						<td align="center">
							<img id="<?= $t_key ?>" style="cursor:pointer" onclick="goDetail(this)" src="images/edit.png" title="Tampilkan Detail">
						</td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				<div style="clear:both"></div>
				<div align="center">
					<? require_once('inc_legendstatusta.php')?>
				</div>
				<br>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goRatakan(elem) {
	// location.href = "<?= Route::navAddress('set_pesertakelas') ?>&key=" + elem.id;
	
	gParam = elem.id;
	showPage('key','<?= Route::navAddress('set_bagiruang') ?>');
}

</script>
</body>
</html>
