<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('seminar'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getModelPath('loggenerate'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Tagihan Seminar';
	$p_tbwidth = 800;
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mSeminar;
	
	// struktur view
	$a_periode = mCombo::periode($conn);
	
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'Nomor');
	$a_kolom[] = array('kolom' => 'namaseminar', 'label' => 'Nama Seminar');
	
	// ada aksi
	$r_act = $_POST['act'];
	// ada aksi
	$r_act = $_POST['act'] ;
	$r_key = $_POST['key'];
	if (($r_act == 'generate' or $r_act == 'void' or $r_act == 'voidna') and $c_edit) {
		// filter data
		$a_filter = array();
		$a_filter['idseminar'] = $r_key;
		
		$conn->BeginTrans();
		
		if ($r_act == 'generate')
			list($err,$msg,$jml) = mTagihan::generateTagihan($conn,$a_filter,$r_periode,$a_jenis);
		else if ($r_act == 'void')
			list($err,$msg,$jml) = mTagihan::voidTagihan($conn,$a_filter,$r_periode,$a_jenis);
		else if ($r_act == 'voidna')
			list($err,$msg,$jml) = mTagihan::voidTagihanNA($conn,$a_filter,$r_periode,$a_jenis);
		
		// buat log
		if(!$err) {
			$record = array();
			$record['periodetagihan'] = $r_periode;
			$record['bulantahun'] = date('Ym');
			$record['kodeunit'] = CStr::cStrNull($r_key);
			$record['jml'] = (int)$jml;
			$record['isgen'] = ($r_act == 'void' ? 'V' : 'G');
			$record['ismahasiswa'] = 1;
			
			$err = mLoggenerate::insertRecord($conn,$record);
			if($err)
				$msg = 'Log generate tagihan gagal';
		}
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
		
		$p_posterr = $err;
		$p_postmsg = $msg.' ('.$jml.' data mahasiswa)';
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
	
	$r_periode 	= Modul::setRequest($_POST['periode'],'PERIODE');
	//$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true); SALAH, perhatikan fungsi masing-masing parameter
	$l_periode 	= uCombo::periode($conn,$r_periode,'','periode','onchange="goSubmit()"');


	if(!empty($r_periode)) $a_filter[] 	= $p_model::getListFilter('periode',$r_periode); //tambahan agar filter jalan
	
	
	// mendapatkan data
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$a_datatagihan = mTagihan::getListTagihanGenerate($conn,$r_periode);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);

	$p_colnum = count($a_kolom)+2;
	
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();

	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
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
		<?php if ($p_mhspage) require_once('inc_headermahasiswa.php');?>
			<form name="pageform" id="pageform" method="post">
				<?	
					
					/**************/
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
					<header style="width:<?= $p_tbwidth ?>px;display:table">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>							
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
						<th>Jumlah tagihan</th>
						<?	if($c_edit) { ?>
						<th width="30">Generate</th>
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
						<?	} ?>
						<td><?= CStr::formatNumber($a_datatagihan[$t_key]) ?></td>
						<?php	if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Generate" src="images/disk.png" onclick="goGenerate(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Kosongkan Data" src="images/delete.png" onclick="goVoid(this)" style="cursor:pointer"></td>
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
							
							if($c_edit) 
								$n_kolom++;
							
							if($c_delete) 
								$n_kolom++;
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
						<td colspan="<?= $p_colnum +1 ?>" align="right" class="FootBG">
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


function goGenerate(elem) {
	var key;
	if(typeof(elem) == "undefined")
		key = "";
	else
		key = elem.id;
	
	var txt = confirm("Apakah anda yakin akan melakukan Generate Tagihan?");
	if(txt) {
		document.getElementById("act").value = "generate";
		document.getElementById("key").value = key;
		goSubmit();
	}
}

function goVoid(elem) {
	var key;
	if(typeof(elem) == "undefined")
		key = "";
	else
		key = elem.id;
	
	var txt = confirm("Apakah anda yakin akan melakukan Hapus Tagihan?");
	if(txt) {
		document.getElementById("act").value = "void";
		document.getElementById("key").value = key;
		goSubmit();
	}
}
</script>
</body>
</html>

