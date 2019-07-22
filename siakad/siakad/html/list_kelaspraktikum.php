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
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('detailkelas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_basis = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	
	// combo
	$l_basis = uCombo::sistemkuliah($conn,$r_basis,'sistemkuliah','onchange="goSubmit()"',true);
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	$r_ctahun = $r_tahun-1;
	$l_csemester = uCombo::semester($r_semester,false,'csemester','',false);
	$l_ctahun = uCombo::tahun($r_ctahun,true,'ctahun','',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Data Kelas Praktikum';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	$p_printpage='rep_kelaspraktikum';
	$p_model = mKelasPraktikum;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'k.thnkurikulum', 'label' => 'Kur.');
	$a_kolom[] = array('kolom' => 'k.kodemk', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'k.namamk', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'k.kelasmk', 'label' => 'Sesi');
	$a_kolom[] = array('kolom' => 'p.kelompok', 'label' => 'Kelompok');
	$a_kolom[] = array('kolom' => 'jadwal', 'label' => 'Jadwal');
	$a_kolom[] = array('kolom' => 'k.koderuang', 'label' => 'Ruang');
	$a_kolom[] = array('kolom' => 'p.peserta', 'label' => 'Peserta');
	$a_kolom[] = array('kolom' => 'p.kapasitas', 'label' => 'Kap.');
	
	$p_colnum = count($a_kolom)+3;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_basis)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_basis);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
 
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi Pengelola', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	$a_filtercombo[] = array('label' => 'Basis', 'combo' => $l_basis);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style>
	.judullaporan{ display:none;font-size:14pt;margin-bottom:10px}	
	@media print{
		.WorkHeader, .sidemenu, .tombol, .filterTable, .inner, .FootBG, .infotime, .action, .pagination{ display:none;}	
		.SideItem{border:none;background:#fff}
		.judullaporan{display:block;}
	}
	</style>
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
							<?	if(!empty($r_page) or $c_insert) { ?>
							<div class="right">
								<div class="addButton"  style="float:left;padding-top:0" title="Cetak Data Kelas" onclick="goPrint()">
									<img width="22" src="images/print.png" style="margin:0 0 3px 0;padding:0">
								</div>
								
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	}
									if($c_insert) { ?>
								<div class="addButton" style="float:left;margin-left:10px" onClick="goNew()">+</div>
								<?	} ?>
								
							</div>
							<?	}?>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<center class="judullaporan">
					Jadwal Praktikum<br>
					<?=Akademik::getNamaPeriode($r_periode)?>
				</center>
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
						<th width="30">Gen.</th>
						<?php if($c_edit) { ?>
						<th width="30" class="action">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30" class="action">Hapus</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
							// cek mengulang
							if(!empty($row['mengulang']))
								$rowstyle = 'YellowBG';
							else if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?=$row['thnkurikulum']?></td>
						<td><?=$row['kodemk']?></td>
						<td><?=$row['namamk']?></td>
						<td><?=$row['kelasmk']?></td>
						<td><?=$row['kelompok']?></td>
						<td><?=Date::indoDay($row['nohari'])?>, <?=CStr::formatJam($row['jammulai'])?>-<?=CStr::formatJam($row['jamselesai'])?></td>
						<td><?=$row['koderuang']?></td>
						<td><?=$row['peserta']?></td>
						<td><?=$row['kapasitas']?></td>
						<td align="center"><?=(!empty($row['tglawalkuliah']))?'<img src="images/check.png">':''?></td>
						<?	if($c_edit) { ?>
						<td align="center" class="action"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center" class="action"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
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
				<br><br>
				
				
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
function goPrint() {
	$('#act').val('');
	goOpen('<?= $p_printpage ?>');
}
</script>
</body>
</html>
