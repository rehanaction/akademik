<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('monitoring'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	$r_ctahun = $r_tahun-1;
	$l_csemester = uCombo::semester($r_semester,false,'csemester','',false);
	$l_ctahun = uCombo::tahun($r_ctahun,true,'ctahun','',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Monitoring Pengisian Nilai';
	$p_tbwidth = 1000;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mMonitoring;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'thnkurikulum', 'label' => 'Kur.');
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'kelasmk', 'label' => 'Kelas');
	$a_kolom[] = array('kolom' => 'sks', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'dinilai', 'label' => 'Nilai'); 
	$a_kolom[] = array('kolom' => 'dinilai', 'label' => 'Prosentase');
	$a_kolom[] = array('kolom' => 'namapengajar', 'label' => 'Pengajar');
	//$a_kolom[] = array('kolom' => 'koderuang', 'label' => 'Ruang');
	//$a_kolom[] = array('kolom' => 'jumlahpeserta', 'label' => 'Peserta');
	//$a_kolom[] = array('kolom' => 'dayatampung', 'label' => 'Kap.');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	//$p_pagenum = ceil($p_rownum/$r_row);
	
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
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {	
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
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
				<?	}?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if(!empty($r_page) or $c_insert) { ?>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	}
									if($c_insert) { ?>
								<div class="addButton" style="float:left;margin-left:10px" onClick="goNew()">+</div>
								<?	} ?>
							</div>
							<?	}
								if($c_edit) { ?>
							<div class="right">
								<div class="addButton" style="padding:0 0 3px 7px;margin-right:15px" title="Tampilkan/sembunyikan impor data" onClick="toggleImpor()">
									<img src="images/outbox.png" style="float:none;padding:0px 0px 2px;margin-right:6px">
								</div>
							</div>
							<?	} ?>
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
							if($c_edit) { ?>
						<th width="30">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							$t_key = $p_model::getKeyRow($row);
							
							if($row['total']>0){
								$prosentase=round(($row['dinilai']/$row['total'])*100,2);
							}else{
								$prosentase=0;
							}
							
							// cek mengulang
							if($row['total']>0 && $prosentase < 100)
								$rowstyle = 'YellowBG';
							else if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><font size=1><?= $row['thnkurikulum'] ?></font></td>
						<td><font size=1><?= $row['kodemk']?></font></td>
						<td><?= $row['namamk'] ?></td>
						<td align="center"><?= $row['kelasmk'] ?></td>
						<td align="center"><?= $row['sks'] ?></td>
						<td align="center"><?= $row['dinilai']."/".$row['total'] ?></td>
						<td align="center"><?= $prosentase?>%</td>
						<td><font size=1><?= $row['namapengajar'] ?></font></td>
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
							
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				
				
				
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
	
	/* $("#xls").change(function() {
		goUpXLS();
	}); */
});

function goChooseXLS() {
	$("#xls").click();
}

function goDownXLS() {
	document.getElementById("act").value = "downxls";
	goSubmit();
}

function goUpXLS() {
	var upload = confirm("Apakah anda yakin akan mengupdate data dari format excel?");
	if(upload) {
		document.getElementById("act").value = "upxls";
		goSubmit();
	}
}

function goSalin() {
	var fsemester = $("#csemester option:selected").text();
	var ftahun = $("#ctahun option:selected").text();
	
	var salin = confirm("Apakah anda yakin akan menyalin data "+fsemester+" "+ftahun+"?");
	if(salin) {
		document.getElementById("act").value = "copy";
		goSubmit();
	}
}

function toggleImpor() {
	$("#div_impor").toggle();
}

</script>
</body>
</html>
