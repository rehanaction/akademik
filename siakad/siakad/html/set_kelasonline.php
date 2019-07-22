<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('honordosen'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'List Kelas Online';
	$p_tbwidth = 800;
	$p_aktivitas = 'ABSENSI';
	
	
	
	$p_model = mKuliah;
	
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_basis = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	$r_periode=$r_tahun.$r_semester;

	
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'u.kodeunit', 'label' => 'Prodi');
	$a_kolom[] = array('kolom' => 'mk.semmk', 'label' => 'Sem.');
	$a_kolom[] = array('kolom' => 'namadepan', 'label' => 'Dosen');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama Mata Kuliah');
	$a_kolom[] = array('kolom' => 'kelasmk','label' => 'Sesi');
	$a_kolom[] = array('kolom' => 'perkuliahanke', 'label' => 'Perkuliahan Ke');
	$a_kolom[] = array('kolom' => 'tglkuliahrealisasi', 'label' => 'Tanggal', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'jeniskuliah', 'label' => 'Aktifitas','type' => 'S', 'option' => mKuliah::jenisKuliah($conn));
	
	
	
	
	$f = 3;
	
	// properti halaman tambahan
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	 if($r_act == 'genPertemuan' and $c_delete) {
		$r_status = $_POST['status'];
		list($p_posterr,$p_postmsg) = $p_model::genPertemuan($conn,$r_unit,$r_periode,$r_basis,$r_status);
	}
	
	
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	$a_filter[]=" isonline=-1";
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_basis)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_basis); 
	
	
	
	$a_data = $p_model::getKuliahOnline($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" align="center">
		<div class="SideItem" id="SideItem">
			
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
			
				<?	if(!empty($p_postmsg)) { ?>
			 
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				 
				<div class="Break"></div>
				<?	} ?>
				<br>
					<header style="width:<?= $p_tbwidth-200 ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Set Pertemuan Online</h1>
							</div>
							
						</div>
					</header>
				<table width="<?= $p_tbwidth-200 ?>" cellpadding="6" cellspacing="0" class="GridStyle">
					<tr>		
						<td width="150"> &nbsp; <strong>Pilih Periode Akademik</strong></td>
						<td>
							<?=uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false).' '.uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);?>
							
						</td>
					</tr>
					<tr>		
						<td width="100"> &nbsp; <strong>Pilih Prodi </strong></td>
						<td>
							<?=uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);?>
							
						</td>
					</tr>
					<tr>		
						<td width="100"> &nbsp; <strong>Pilih Basis </strong></td>
						<td>
							<?=uCombo::sistemkuliah($conn,$r_basis,'sistemkuliah','onchange="goSubmit()"',true);?>
						</td>
					</tr>
					
					<tr>		
						<td width="100">&nbsp;</td>
						<td>
							
							<input type="button" value="Generate Pertemuan Online" onclick="genPertemuan('seton')">
							<input type="button" value="Batalkan Generate Pertemuan" onclick="genPertemuan('setoff')">
						</td>
					</tr>
				</table><br>
				<?php require_once('inc_listfilter.php'); ?>
			
				<div class="DivError" style="display:none"></div>
				<div class="DivSuccess" style="display:none"></div>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	} ?>
							</div>
						</div>
					</header>
				 
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
							
							foreach($a_kolom as $i => $datakolom) {
								if(empty($datakolom['label']))
									continue;
								
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						
						
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) { 
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$j = $f;
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['namaunit'] ?></td>
						<td><?= $row['semmk'] ?></td>
						<td><?= $row['nipdosenrealisasi'] ?>-<?= $row['namadosen'] ?></td>
						<td><?= $row['kodemk'] ?> - <?= $row['namamk'] ?></td> 
						<td><?= $row['kelasmk'] ?></td>
						<td><?= $row['perkuliahanke'] ?></td> 
						<td><?= $row['tglkuliahrealisasi'] ?></td> 
						<td><?= $row['jeniskuliah'] ?></td> 
						
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
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				
				<input type="hidden" name="status" id="status">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
			
		</div>
	</div>
</div>
<script type="text/javascript">
<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>	



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


function genPertemuan(status){
	document.getElementById("act").value = "genPertemuan";
	document.getElementById("status").value = status;
	goSubmit();
}

</script>
</body>
</html>