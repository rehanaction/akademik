<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	//$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	$_SESSION['SERI'] = 'list_dirrekap';
	
	// include
	require_once(Route::getModelPath('dirrekap'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_lokasi = Modul::setRequest($_POST['lokasi'], 'LOKASI');
	$r_pemakai = Modul::setRequest($_POST['pemakai'], 'PEMAKAI');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:270px;"',false);
	$l_lokasi = uCombo::lokasi($conn,$r_lokasi,'lokasi','onchange="goSubmit()" style="width:270px;"',true,$r_unit);
	$l_pemakai = uCombo::pemakai($conn,$r_pemakai,'pemakai','onchange="goSubmit()" style="width:270px;"',true,$r_unit);

	// properti halaman
	$p_title = 'Rekap Inventaris Ruang';
	$p_tbwidth = 900;
	$p_aktivitas = 'Inventaris Ruang';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mDIRRekap;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idbarang1', 'label' => 'ID Barang', 'width' => 100, 'align' => 'center', 'nosearch' => true);
//	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit', 'width' => 60, 'align' => 'center', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'namabarang', 'label' => 'Nama Barang');
	$a_kolom[] = array('kolom' => 'idlokasi', 'label' => 'ID. Lokasi', 'width' => 60, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Pemakai', 'width' => 200, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'total', 'label' => 'Total', 'width' => 75, 'align' => 'right', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'b', 'label' => 'B', 'width' => 40, 'align' => 'right', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'rb', 'label' => 'RB', 'width' => 40, 'align' => 'right', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'rr', 'label' => 'RR', 'width' => 40, 'align' => 'right', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'tb', 'label' => 'TB', 'width' => 40, 'align' => 'right', 'type' => 'N');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
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
	if(!empty($r_lokasi)) $a_filter[] = $p_model::getListFilter('lokasi',$r_lokasi);
	if(!empty($r_pemakai)) $a_filter[] = $p_model::getListFilter('pemakai',$r_pemakai);	

	$a_kondisi = mCombo::kondisi($conn);
	$a_statusbarang = mCombo::status($conn);
	
	$sql = $p_model::listRekap();
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	

	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Lokasi', 'combo' => $l_lokasi);
	$a_filtercombo[] = array('label' => 'Pemakai', 'combo' => $l_pemakai);


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
							<?	if($c_insert or $p_printpage) { ?>
							<div class="right">
							<?	if($p_printpage) { ?>
								<img title="Cetak <?= $p_model::label ?>" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							<?	}
								if($c_insert) { ?>
								<div class="addButton" onClick="goNew()">+</div>
							<?	} ?>
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
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="50">Aksi</th>
						<?	} ?>	
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,$p_key);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								
								$pad = '';
								if($j==1)//utk padding nama unit
									$pad = 'style="padding-left:'.(($row['level']*15)+4).'px"';
						?>
						<td<?= $t_align.' '.$pad ?>><?= $rowcc ?></td>
						<?	}
							if($c_edit or $c_delete) { ?>
						<td align="right" style="padding-right:12px">
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
				<input type="hidden" name="pkey" id="pkey">
				<? if(!empty($p_printpage)) { ?>
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				<? } ?>
			</form>
			<br /><br />
			<center>
			<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
				<table cellspacing="0" cellpadding="0" align="center" width="890">
					<tr>
						<td colspan="2"><strong>Keterangan Kondisi Barang :</strong></td>
					</tr>
					<? foreach ($a_kondisi as $kode => $label){?>
					<tr>
						<td width="20"><?= $kode; ?></td>
						<td>: <?= $label; ?></td>
					</tr>
					<? } ?>
				</table>
			</div>
			</center>
		</div>
	</div>
</div>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' ?>;
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

<? if($p_printpage) { ?>
function goPrint() {
	showPage('null','<?= Route::navAddress($p_printpage) ?>');
}
<? } ?>

function goChild(elem){
	location.href = detailpage + "&pkey=" + elem.id;
}
</script>
</body>
</html>
