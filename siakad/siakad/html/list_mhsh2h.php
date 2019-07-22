<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// koneksi database
	$connh = Query::connect('h2h');
	$connh->debug = $conn->debug;
	
	// properti halaman
	$p_title = 'Daftar Mahasiswa H2H';
	$p_tbwidth = 700;
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mMahasiswaH2H;
	
	// struktur view
	$a_kolom = array();
	
	$a_kolom[] = array('kolom' => 'nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'kelamin', 'label' => 'L/P');
	$a_kolom[] = array('kolom' => 'm.fakultas', 'label' => 'Fakultas');
	$a_kolom[] = array('kolom' => 'namajurusan', 'label' => 'Prodi');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'sync' and $c_edit) {
		$err = $p_model::syncMahasiswa($conn,$connh);
		
		$p_posterr = $err;
		$p_postmsg = 'Sinkronisasi mahasiswa H2H '.($err ? 'gagal' : 'berhasil');
	}
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
	$a_data = $p_model::getPagerData($connh,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// filter tree
	$a_filtertree = array();
	$a_filtertree['m.jurusan'] = array('label' => 'Unit', 'data' => $p_model::unit($connh));
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
<?	if(!empty($a_filtertree)) { ?>
	<link href="style/jquery.treeview.css" rel="stylesheet" type="text/css">
<?	} ?>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<?php if ($p_mhspage) require_once('inc_headermahasiswa.php'); ?>
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfiltertree.php') ?>
				<?	if(!empty($a_filtertree)) { ?>
				<div style="float:left;width:760px">
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
					<header style="width:<?= $p_tbwidth ?>px;display:table">
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
								<div class="addButton" style="padding:0 0 3px 7px;margin-right:15px" title="Sinkronisasi data mahasiswa" onClick="goSync()">
									<img src="images/copy.png" style="float:none;padding:0px 0px 2px;margin-right:6px">
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
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								
								if(!empty($a_total) and $a_total['index'] == $j)
									$t_total += $rowcc;
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	}
							if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						else if(!empty($a_total)) {
							$n_kolom = count($a_kolom);
							if($c_edit) $n_kolom++;
							if($c_delete) $n_kolom++;
					?>
					<tr>
						<th colspan="<?= $a_total['index'] ?>"><?= $a_total['label'] ?></th>
						<th><?= $t_total ?></th>
						<th colspan="<?= $n_kolom-$a_total['index']+1 ?>">&nbsp;</th>
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
				<?	if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<?	} ?>
				
				<?	if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<?	if(!empty($a_filtertree)) { ?>
				</div>
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<?	if(!empty($a_filtertree)) { ?>
<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="scripts/jquery.treeview.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.js"></script>
<?	} ?>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var cookiename = '<?= $i_page ?>.accordion';

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	<?	if(!empty($a_filtertree)) { ?>
	initFilterTree();
	<?	} ?>
});

<? if($c_edit) { ?>
function goSync() {
	document.getElementById("act").value = "sync";
	goSubmit();
}
<? } ?>

</script>
</body>
</html>