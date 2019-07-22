<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	//$c_editpass = $c_edit;
	
	// include
	require_once(Route::getModelPath('gelombangdaftar'));
	require_once($conf['helpers_dir'].'date.class.php');
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode 	= Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur 	= Modul::setRequest($_POST['jalur'],'JALUR');
	$r_gelombang = Modul::setRequest($_POST['gelombang'],'GELOMBANG');

	
	
	//combo
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode','onchange="goSubmit()"');
	$l_jalur 	= uCombo::jalur($conn,$r_jalur,'','jalur','onchange="goSubmit()"');
	$l_gelombang 	= uCombo::gelombang($conn,$r_gelombang,'','gelombang','onchange="goSubmit()"');


	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'periodedaftar', 'label' => 'Periode');
	$a_kolom[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur');
	$a_kolom[] = array('kolom' => 'idgelombang', 'label' => 'Gelombang');
	$a_kolom[] = array('kolom' => 'isaktif', 'label' => 'Aktif', 'type' => 'C', 'option' => array('t' => ''));
	$a_kolom[] = array('kolom' => 'isopen', 'label' => 'Status', 'type' => 'C', 'option' => array('t' => ''));
	$a_kolom[] = array('label' => 'Keterangan');
	
	// properti halaman
	$p_title = 'Daftar Jalur Penerimaan';
	$p_tbwidth = 800;
	$p_aktivitas = 'SPMB';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mGelombangDaftar;
	$p_colnum = count($a_kolom)+2;
	
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
	if(!empty($r_periode)) $a_filter[] 	= $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_jalur)) $a_filter[] 	= $p_model::getListFilter('jalur',$r_jalur);
	if(!empty($r_gelombang)) $a_filter[] 	= $p_model::getListFilter('gelombang',$r_gelombang);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();

	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jalur', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
	
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
				<?php  require_once('inc_listfilter.php'); ?>
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
						<th width="50">Edit</th>
						<th width="50">Hapus</th>
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
					<tr valign="top" align="center" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td>Gelombang <?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td>
							<? if(empty($row['tglawaldaftar']) || empty($row['tglakhirdaftar'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/calenderadd.png" title="<?= Date::indoDate($row['tglawaldaftar']) ?> s/d <?= Date::indoDate($row['tglakhirdaftar']) ?>">
							<? } if(empty($row['tglujian'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/calender.png" title="<?= Date::indoDate($row['tglujian']) ?>">
							<? } if(empty($row['tglpengumuman'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/calenderinfo.png" title="<?= Date::indoDate($row['tglpengumuman']) ?>">
							<? } if(empty($row['tglawalregistrasi']) || empty($row['tglakhirregistrasi'])) { ?>
							&nbsp;&nbsp;&nbsp;&nbsp;
							<? } else { ?>
							<img id="imgcontact" src="images/calendernotice.png" title="<?= Date::indoDate($row['tglawalregistrasi']) ?> s/d <?= Date::indoDate($row['tglakhirregistrasi']) ?>">
							<? } ?>
						</td>
						
						<?	if($c_edit or $c_delete) { ?>
						<td align="center">
						<?		if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
						?>
						</td>
						<td align="center">
						<?		if($c_delete) { ?>
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
					
				<input type="hidden" name="npm" id="npm">
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
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle contact
	$("[id='imgcontact']").balloon();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function showKRS() {
	gParam = $("#nimkrs").val();
	
	if(gParam == "") {
		alert("Mohon isi KRS u/ NIM terlebih dahulu");
		$("#nimkrs").focus();
	}
	else
		showPage('npm','<?= Route::navAddress('set_krs') ?>');
}

</script>
</body>
</html>
