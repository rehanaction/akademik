<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('pengajuanbeasiswa'));
	require_once(Route::getModelPath('jenisbeasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// combo
	$a_jenis = array('' => '-- Semua Jenis --') + mJenisbeasiswa::getArray($conn);
	$r_jenis = Modul::setRequest($_POST['jenis'],'JENISBEASISWA',$a_jenis);
	$l_jenis = UI::createSelect('jenis',$a_jenis,$r_jenis,'ControlStyle',true,'onchange="goSubmit()"');
	
	if(!Akademik::isMhs()) {
		$r_tfilteripkb = Modul::setRequest($_POST['tfilteripkb'],'AJUBEA.IPKBAWAH');
		$r_tfilteripka = Modul::setRequest($_POST['tfilteripka'],'AJUBEA.IPKATAS');
	}

	// properti halaman
	$p_title = 'Data Pengajuan Beasiswa';
	$p_tbwidth = 900;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	
	$p_model = mPengajuanBeasiswa;

	// struktur view
	$a_periode = mCombo::periode($conn);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	/* $r_tfilteruna = $_POST['tfilteruna'];
	$r_tfilterunb = $_POST['tfilterunb']; */
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'm.nim', 'label' => 'NIM');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'namajenisbeasiswa', 'label' => 'Jenis Beasiswa');
	$a_kolom[] = array('kolom' => 'namabeasiswa', 'label' => 'Beasiswa');
	$a_kolom[] = array('kolom' => 'm.ipk', 'label' => 'IPK');
	// $a_kolom[] = array('kolom' => 'penghasilanortu', 'label' => 'Penghasilan');
	$a_kolom[] = array('kolom' => 'namastatus', 'label' => 'Status');
	$a_kolom[] = array('kolom' => 'namatahap', 'label' => 'Tahap');
	$a_kolom[] = array('kolom' => 'isditerima', 'label' => 'Lulus', 'type' => 'C');
	$a_kolom[] = array('kolom' => 'tglterima', 'label' => 'Ditetapkan', 'type' => 'D');

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);

		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	/* else if($r_act == 'refresh') {
		Modul::refreshList();
		unset($r_tfilteripka,$r_tfilteripkb);
	} */

	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);

	// mendapatkan data
	if(Akademik::isMhs()) {
		$a_filter[] = $p_model::getListFilter('nim',Modul::getUserName());
	}
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_jenis)) $a_filter[] = $p_model::getListFilter('b.idjenisbeasiswa',$r_jenis);
	
	if(!Akademik::isMhs()) {
		if(!empty($r_tfilteripkb)) $a_filter[] = $p_model::getListFilter('ipkbawah',$r_tfilteripkb);
		if(!empty($r_tfilteripka)) $a_filter[] = $p_model::getListFilter('ipkatas',$r_tfilteripka);
		/* if(!empty($r_tfilteripkb) and !empty($r_tfilteripka))
			$a_filter[] = $p_model::getListFilter('ipk',$r_tfilteripkb.'|'.$r_tfilteripka); */
	}
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	$a_filtercombo[] = array('label' => 'Jenis Beasiswa', 'combo' => $l_jenis);
	
	if(!Akademik::isMhs()) {
		$x_ipk = UI::createTextBox('tfilteripkb',$r_tfilteripkb,'ControlStyle',4,4);
		$x_ipk .= ' &nbsp;s.d.&nbsp; '.UI::createTextBox('tfilteripka',$r_tfilteripka,'ControlStyle',4,4);
		$x_ipk .= ' &nbsp;<input type="button" value="Filter" onclick="goSubmit()">';
		
		$a_filtercombo[] = array('label' => 'IPK', 'combo' => $x_ipk);
	}

	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);

?>
<?php

	$p_colnum = count($a_kolom)+2;

	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
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
		<?php if ($p_mhspage) require_once('inc_headermahasiswa.php');?>
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfiltertree.php');?>
				<?	if(!empty($a_filtertree)) { ?>
				<div style="float:left;width:760px">
				<?	}

					/**************/
					/* JUDUL LIST */
					/**************/

					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					if($p_headermhs) { ?>
				<center>
				<?php require_once('inc_headermhs.php') ?>
				</center>
				<br>
				<?	} 
				/* if(!Akademik::isMhs()){
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
					<br>
				</center>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td width="40" style="white-space:nowrap"><strong>Cari :</strong></td>
								<td style="display:none"><?= uCombo::listColumn($a_kolom,'',$a_addcolfilter) ?></td>
								<?php // <td width="210"><input name="tfilter" id="tfilter" class="ControlStyle" size="25" onkeydown="etrFilterCombo(event)" type="text"></td> ?>
								<?php // <td width="40"><input type="button" value="Filter" class="ControlStyle" onClick="goFilterCombo()"></td> ?>
								<td width="260"><input name="tfilter" id="tfilter" class="ControlStyle" size="40" onkeydown="etrFilterAll(event)" type="text" value="<?= $r_filterstr ?>"></td>
								<td><input type="button" value="Cari" class="ControlStyle" onClick="goFilterAll()"></td>
								<td><input type="button" value="Refresh" class="ControlStyle" onClick="goRefresh()"></td>
							</tr>
						</table>
						<br>
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td width="40" style="white-space:nowrap"><strong>Batas bawah IPK :</strong></td>
								<td width="40">
									<input name="tfilteripkb" id="tfilteripkb" class="ControlStyle" size="5" onkeydown="etrFilterAll(event)" type="text" value="<?= $r_tfilteripkb ?>">
								</td>
								<td width="40" style="white-space:nowrap"><strong>Batas atas IPK :</strong></td>
								<td width="40">
									<input name="tfilteripka" id="tfilteripka" class="ControlStyle" size="5" onkeydown="etrFilterAll(event)" type="text" value="<?= $r_tfilteripka ?>">
								</td>
								<td> <input type="button" value="Filter IPK" class="ControlStyle" onClick="goFilterAll()"> <input type="button" value="Refresh" class="ControlStyle" onClick="goRefresh()"></td>
							</tr>
						</table>
					</div>
					<br>
				</center>
				<?php } */ ?>
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
								if($p_printpage) { ?>
							<div class="right">
								<img title="Cetak <?= $p_model::label ?>" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
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
						<th width="30">Detail</th>
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
				<?	if(!empty($p_printpage) or !empty($p_mhspage)) { ?>
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				<?	}
					if(!empty($a_filtertree)) { ?>
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

<? if($p_printpage) { ?>
function goPrint() {
	showPage('null','<?= Route::navAddress($p_printpage) ?>');
}
<? } ?>
<? if($a_salin) { ?>
function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}
<? } ?>
</script>
</body>
</html>
