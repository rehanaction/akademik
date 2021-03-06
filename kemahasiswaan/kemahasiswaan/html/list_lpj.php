<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('lpj'));
	require_once(Route::getUIPath('combo'));

	// combo
	$a_periode = array('' => '-- Semua Periode --') + mCombo::periode($conn);
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE',$a_periode);
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');

	$r_tanggal = Modul::setRequest($_POST['tanggal'],'LPJ.TANGGAL');
	$r_sdtanggal = Modul::setRequest($_POST['sdtanggal'],'LPJ.SDTANGGAL');

	// properti halaman
	$p_title = 'Daftar Pertanggungjawaban';
	$p_tbwidth = 900;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();

	$p_model = mLpj;

	// struktur view
	$a_periode = mCombo::periode($conn);

	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'j.nosurat', 'label' => 'Nomor Surat');
	$a_kolom[] = array('kolom' => 'namaorganisasi', 'label' => 'Nama Organisasi');
	$a_kolom[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan');
	$a_kolom[] = array('kolom' => 'nrp', 'label' => 'NIM Pelapor');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama Pelapor');
	$a_kolom[] = array('kolom' => 'tgllpj', 'label' => 'Tanggal', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'j.status', 'label' => 'Valid');

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
	$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
	$a_datafilter = Page::getFilter($a_kolom);

	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('j.periode',$r_periode);
	if(!empty($r_tanggal)) $a_filter[] = $p_model::getListFilter('fromtanggal',$r_tanggal);
	if(!empty($r_sdtanggal)) $a_filter[] = $p_model::getListFilter('sdtanggal',$r_sdtanggal);

	//filter sesuai unit organisasi
	$a_filter[] = $p_model::getListFilter('unit',Modul::getUnit());

	// $a_data = $p_model::getListLpj($conn,$a_filter);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$p_model::getSQLListLpj());

	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);

	// membuat filter
	$x_tanggal = UI::createTextBox('tanggal',$r_tanggal,'ControlStyle',10,8);
	$x_tanggal .= ' <img src="images/cal.png" id="tanggal_trg" style="cursor:pointer" title="Pilih tanggal mulai">';
	$x_tanggal .= ' &nbsp;s.d.&nbsp; '.UI::createTextBox('sdtanggal',$r_sdtanggal,'ControlStyle',10,8);
	$x_tanggal .= ' <img src="images/cal.png" id="sdtanggal_trg" style="cursor:pointer" title="Pilih tanggal selesai">';
	$x_tanggal .= ' &nbsp;<input type="button" value="Pilih" onclick="goSubmit()">';

	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Tanggal', 'combo' => $x_tanggal);

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
		<link href="style/calendar.css" rel="stylesheet" type="text/css">
		<script type="text/javascript" src="scripts/forpager.js"></script>
		<script type="text/javascript" src="scripts/calendar.js"></script>
		<script type="text/javascript" src="scripts/calendar-id.js"></script>
		<script type="text/javascript" src="scripts/calendar-setup.js"></script>
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

								<td><?=$row['nosurat']?></td>
								<td><?=$row['namaorganisasi']?></td>
								<td><?=$row['namakegiatan']?></td>
								<td><?=$row['nrp']?></td>
								<td><?=$row['nama']?></td>
								<td><?=$row['tgllpj']?></td>
								<td><?=($row['status']<>0)?'<div align="center">
										<img src="images/check.png">
										</div>':'' ?>
								</td>
								<?
									if($c_edit) { ?>
								<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
								<?	}
									if($c_delete) { ?>
								<td align="center">
									<?php if($row['status'] <> -1) { ?>
									<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
									<?php } ?>
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

			Calendar.setup({
				inputField	: "tanggal",
				ifFormat	: "%d-%m-%Y",
				button		: "tanggal_trg",
				align		: "Br",
				singleClick	: true
			});

			Calendar.setup({
				inputField	: "sdtanggal",
				ifFormat	: "%d-%m-%Y",
				button		: "sdtanggal_trg",
				align		: "Br",
				singleClick	: true
			});
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
