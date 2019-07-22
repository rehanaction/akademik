<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('supplier'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_jenissupplier = Modul::setRequest($_POST['jenissupplier'],'JENISSUPPLIER');
	
	// combo
	$l_jenissupplier = uCombo::jenissupplier($conn,$r_jenissupplier,'jenissupplier','style="width:160px" onchange="goSubmit()"',true);
	
	// properti halaman
	$p_title = 'Daftar Supplier';
	$p_tbwidth = 800;
	$p_aktivitas = 'Supplier';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mSupplier;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idsupplier', 'label' => 'ID.', 'width' => '40', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'namasupplier', 'label' => 'Nama Supplier');
	$a_kolom[] = array('kolom' => 'jenissupplier', 'label' => 'Jenis Supplier', 'width' => '175', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'namacp', 'label' => 'Contact Person', 'width' => '150');
	$a_kolom[] = array('label' => 'Contacts', 'width' => '90');
	
	$p_colnum = count($a_kolom)+1;
	
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
	if(!empty($r_jenissupplier)) $a_filter[] = $p_model::getListFilter('jenissupplier',$r_jenissupplier);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Jenis Supplier', 'combo' => $l_jenissupplier);
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
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
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
						<?	if($c_edit or $c_delete) { ?>
						<th width="50">Aksi</th>
						<?	} ?>
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
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td>
							<? if(empty($row['alamat'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/house.png" title="<?= $row['alamat'] ?>">
							<? } if(empty($row['notlp'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/telp.png" title="<?= $row['notlp'] ?>">
							<? } if(empty($row['nohp'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/hp.png" title="<?= $row['nohp'] ?>">
							<? } if(empty($row['email'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/mail.png" title="<?= $row['email'] ?>">
							<? } ?>
						</td>
						<?	if($c_edit or $c_delete) { ?>
						<td align="center">
						<?		if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
								if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
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
							Halaman <?= $r_page ?>
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
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle contact
	$("[id='imgcontact']").balloon();
});
</script>
</body>
</html>
