<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('alumni'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	// $r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	// $l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Data Alumni';
	$p_tbwidth = 750;
	$p_aktivitas = 'BIODATA';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mAlumni;
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'nim', 'label' => 'N I M', 'width' => 15, 'align' => 'center');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama', 'width' => 35, 'align' => 'left');
	//$a_kolom[] = array('kolom' => 'sex', 'label' => 'L/P', 'width' => 30, 'align' => 'center');
	$a_kolom[] = array('kolom' => 'substring(periodelulus,1,4)', 'label' => 'Thn.Lulus', 'alias' => 'thnlulus', 'width' => 20, 'align' => 'center');
	//$a_kolom[] = array('kolom' => 'semestermhs', 'label' => 'Lama', 'width' => 30, 'align' => 'right');
	$a_kolom[] = array('kolom' => 'noijasah', 'label' => 'No.Ijasah', 'width' => 110, 'align' => 'right');
	//$a_kolom[] = array('kolom' => 'notranskrip', 'label' => 'No.Transkrip', 'width' => 150, 'align' => 'right');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	// if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	$a_filter[] = $p_model::getListFilter('unit',Modul::getUnit());
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	// $a_filtercombo = array();
	// $a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	
	// filter tree
	$a_filtertree = array();
	$a_filtertree['tahunlulus'] = array('label' => 'Tahun Lulus', 'data' => $p_model::tahunLulus($conn));
	$a_filtertree['unit'] = array('label' => 'Prodi', 'data' => mCombo::unitTree($conn,true));
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
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if(!empty($r_page)) { ?>
							<div class="right">
								<?php require_once('inc_listnavtop.php'); ?>
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
						<?	} ?>
						<th width="30">Link</th>
						<?	if($c_edit) { ?>
						<th width="30">Aksi</th>
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
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
							<img id="<?= $t_key ?>" title="Halaman Alumni" src="images/link.png" onclick="goPop('popMenu',this,event)" style="cursor:pointer">
						</td>
						<?	if($c_edit) { ?>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
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
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="npm" id="npm">
				<?	if(!empty($a_filtertree)) { ?>
				</div>
				<?	} ?>
			</form>
		</div>
	</div>
</div>
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_transkrips1') ?>')">Transkrip-S1</td>
    </tr>    
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('npm','<?= Route::navAddress('view_transkrips2') ?>')">Transkrip-S2</td>
    </tr>
</table>
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

</script>
</body>
</html>
