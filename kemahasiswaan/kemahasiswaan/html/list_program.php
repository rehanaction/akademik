<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('program'));
	require_once(Route::getModelPath('organisasi'));
	require_once(Route::getUIPath('combo'));
	
	// combo
	$a_periode = array('' => '-- Semua Periode --') + mCombo::periode($conn);
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE',$a_periode);
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');
	
	$a_organisasi = array('' => '-- Semua Organisasi --') + mOrganisasi::getArray($conn);
	$r_organisasi = Modul::setRequest($_POST['organisasi'],'ORGANISASI',$a_organisasi);
	$l_organisasi = UI::createSelect('organisasi',$a_organisasi,$r_organisasi,'ControlStyle',true,'onchange="goSubmit()"');
	
	// properti halaman
	$p_title = 'Daftar Kegiatan';
	$p_tbwidth = 800;
	$p_aktivitas = 'SPP';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mKegiatan;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namaorganisasi', 'label' => 'Nama Organisasi');
	$a_kolom[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan');
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode');
	$a_kolom[] = array('kolom' => 'ketupel', 'label' => 'Ketua Pelaksana');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}else if($r_act == 'downxls') {
		$a_header = array('KODEORGANISASI','NAMAKEGIATAN','PERIODE');		
		$a_huruf = CStr::arrayHuruf();
		
		// menghilangkan segala echo
		ob_clean();
		
		header("Content-Type: application/msexcel");
		header('Content-Disposition: attachment; filename="template_program.xls"');
		
		// pakai phpexcel
		require_once($conf['includes_dir'].'phpexcel/PHPExcel.php');
		
		$xls = new PHPExcel();
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		
		// header
		$r = 1;
		foreach($a_header as $i => $t_header)
			$sheet->setCellValue($a_huruf[$i].$r,$t_header);
		
		// paskan ukuran
		$n = count($a_header);
		for($i=0;$i<$n;$i++)
			$sheet->getColumnDimension($a_huruf[$i])->setAutoSize(true);
		
		$xlsfile = PHPExcel_IOFactory::createWriter($xls,'Excel5');
		$xlsfile->save('php://output');
		
		exit;
	}
	else if($r_act == 'upxls' and $c_edit) {
		$r_file = $_FILES['xls']['tmp_name'];
		
		// pakai excel reader
		require_once($conf['includes_dir'].'phpexcel/excel_reader2.php');
		$xls = new Spreadsheet_Excel_Reader($r_file);
		
		$cells = $xls->sheets[0]['cells'];
		$numrow = count($cells);

		// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
		if(empty($numrow)) {
			if(($handle = fopen($r_file, 'r')) !== false) {
				while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
					$numrow++;
					foreach($data as $k => $v)
						$cells[$numrow][$k+1] = $v;
				}
				fclose($handle);
			}
		}
		
		// baris pertama adalah header
		$conn->BeginTrans();
		
		
		$ok = true;
		for($r=2;$r<=$numrow;$r++) {
			$data = $cells[$r];
			
			$record = array();
			foreach($cells[1] as $k => $v) {
				$v = strtolower($v);
				$record[$v] = CStr::cStrNull($data[$k]);
			}

			list($p_posterr,$p_postmsg) = $p_model::saveRecord($conn,$record,true);
			if($p_posterr) {
				$ok = false;
				break;
			}
		}
		
		if($ok)
			$p_postmsg = 'Impor data dari format excel berhasil';
		
		$conn->CommitTrans($ok);
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
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_organisasi)) $a_filter[] = $p_model::getListFilter('kodeorganisasi',$r_organisasi);
	
	// mendapatkan data
	// $a_data = $p_model::getListKegiatan($conn,$r_filterstr,$a_filter);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$p_model::getSQLListKegiatan());
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Nama Organisasi', 'combo' => $l_organisasi);
	
	//require_once(Route::getViewPath('inc_list'));
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
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
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
				<?	} 
					//if($c_edit) { ?>
				<center id="div_impor" >
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="2">Impor Data dari Format Excel</td>
						<? //<td align="center">Salin Data Periode Lain</td> ?>
					</tr>
					<tr class="NoHover NoGrid">		
						<td width="55"> &nbsp; <strong>Upload </strong></td>
						<td>
							<strong> : </strong> <input type="file" name="xls" id="xls" size="50" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpXLS()"> &nbsp; &nbsp; &nbsp;
							<u class="ULink" onclick="goDownXLS()">Download Template Excel...</u>
						</td>
						<?/*
						<td>
							<strong>Periode : </strong> <?= $l_csemester ?> <?= $l_ctahun ?>
							<input type="button" value="Salin" onclick="goSalin()">
						</td>
						*/?>
					</tr>
				</table>
				<br>
				</center>
				<?	//} ?>
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
</script>
</body>
</html>
