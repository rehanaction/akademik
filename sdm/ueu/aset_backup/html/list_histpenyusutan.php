<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = false;
	
	$_SESSION['PEROLEHAN'] = 'list_histpenyusutan';
	
	// include
	require_once(Route::getModelPath('histdepresiasi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan = Modul::setRequest($_POST['bulan'],'BULAN');
	
	
	$year = date("Y"); 
	$mon = date("m"); 

	if(empty($r_tahun)) $r_tahun = $year;
	if(empty($r_bulan)) $r_bulan = $mon;
	//echo '--'.$r_unit;

	// combo
	$lr = Modul::getLeftRight();
	if($lr['LEFT'] == '1')
    	$l_unit = uCombo::unitAuto($conn,$r_unit,'unit','onchange="goSubmit()"');
	else
    	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px;"',false);
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','onchange="goSubmit()"',false);
	$l_bulan = uCombo::bulan($conn,$r_bulan,'bulan','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'History Penyusutan';
	$p_tbwidth = 975;
	$p_aktivitas = 'JENIS PENYUSUTAN';
	//$p_detailpage = Route::getDetailPage();
	$p_detailpage = Route::navAddress('data_perolehan');
	
	$p_model = mHistDepresiasi;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'idhistdepresiasi', 'label' => 'ID.', 'width' => 30, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode', 'width' => 100, 'align' => 'center', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'noseri', 'label' => 'No. Seri' ,'width' => 50, 'align' => 'center');
	$a_kolom[] = array('kolom' => 'barang', 'label' => 'Barang');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit' ,'width' => 175, 'nosearch' => true);
	//$a_kolom[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti' ,'width' => 100);
	//$a_kolom[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D' ,'width' => 70);
	$a_kolom[] = array('kolom' => 'nilaiawal', 'label' => 'Harga Perolehan' ,'width' => 90, 'align' => 'right', 'type' => 'N,2', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'nilaisusut', 'label' => 'Nilai Susut' ,'width' => 90, 'align' => 'right', 'type' => 'N,2', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'nilaiaset', 'label' => 'Nilai Aset' ,'width' => 90, 'align' => 'right','type' => 'N,2', 'nosearch' => true);
	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'refresh'){
		Modul::refreshList();
	}else if($r_act == 'proses'){
	    echo "proses";
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);

	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_tahun) or !empty($r_bulan)) $a_filter[] = $p_model::getListFilter('periode',$r_tahun.$r_bulan);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
 	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_bulan.'&nbsp;&nbsp;&nbsp;'.$l_tahun);
	
	$a_bulan = mCombo::bulan();
	
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
							<?/*
							<div class="right">
								<div class="addButton" onClick="goProcess()">Proses</div>
							</div>
							*/?>
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
							//if($c_edit or $c_delete) { ?>
						<th width="40">Aksi</th>
						<?//	} ?>
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
						
						<?	$perolehan = '';
							/*echo "<pre>";
							print_r($a_data);
							echo "</pre>";*/
							foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
									
								if($a_kolom[$j]['kolom'] == 'periode'){
								    $rowcc = $a_bulan[substr($rowcc,4,2)].'&nbsp;'.substr($rowcc,0,4);
								}
								
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
						<?		if($c_edit) { ?>
							<img id="<?= $row['idperolehanheader'] ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}	?>
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
		</div>
	</div>
</div>

<?/*
<span id="edit" style="display:none">
	<img id="imgpeg_c" src="images/green.gif">
	<img id="imgpeg_u" src="images/red.gif" style="display:none">
</span>
*/?>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
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
	
	
	$("#namaunit").xautox({strpost: "f=acxunit", targetid: "unit", imgchkid: "imgunit", imgavail: true});
	//$("#unit").xautox({strpost: "f=acxunit", targetid: "idunit"});
	//$("#unit").bind()
});

<? if($p_printpage) { ?>
function goPrint() {
	showPage('null','<?= Route::navAddress($p_printpage) ?>');
}
<? } ?>

function goChild(elem){
	location.href = detailpage + "&pkey=" + elem.id;
}

function goProcess(){
    $('#act').val('proses');
    goSubmit();
}

</script>
</body>
</html>
