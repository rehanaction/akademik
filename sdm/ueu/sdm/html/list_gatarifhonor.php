<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mGaji;
			
	// variabel request
	$r_periode = Modul::setRequest($_POST['periodetarif'],'PERIODETARIF');
	$r_honor = Modul::setRequest(!empty($_POST['shonor']) ? $_POST['shonor'] : $_POST['honor'],'HONOR');
	$honor = $p_model::infoHonor($conn,$r_honor);
	
	// combo
	$a_tarif = $p_model::getCPeriodeTarif($conn);
	$l_periode = uCombo::combo($a_tarif,$r_periode,'periodetarif','onchange="goChange()"',false);
	
	$a_honor = $p_model::getCHonorTarif($conn);
	$l_honor = uCombo::combo($a_honor,$r_honor,'honor','onchange="goChange()"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'namavariabel', 'label' => $honor['info'], 'filter' => $honor['filter']);	
	$a_kolom[] = array('kolom' => 'nominal', 'label' => 'Jumlah Tarif','type' => 'N','align' => 'right', 'width' => '250px');
	
	// properti halaman
	$p_title = 'Daftar Tarif '.$honor['namahonor'];
	$p_tbwidth = 850;
	$p_aktivitas = 'ANGGARAN';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = "ga_tarifhonor";
	$p_key = "notarifhonor";
	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'salin' and $c_edit) {
		$conn->BeginTrans();
		
		$f_keyPeriode = $_POST['speriodetarif'];
		$f_keyHonor = $_POST['shonor'];
		$prosentase = $_POST['sprosentase']/100;
		
		list($p_posterr,$p_postmsg) = $p_model::saveSalinTarifHonor($conn,$r_periode,$r_honor,$f_keyPeriode,$f_keyHonor,$prosentase);
			
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	
	if (empty($r_sort)){
		$r_sort = 'variabel1';
		if($r_honor == 'H0001')		
			$r_sort = 'infoleft';
	}
	
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodetarif',$r_periode);
	if(!empty($r_honor)) $a_filter[] = $p_model::getListFilter('jnshonor',$r_honor);
	
	$sql = $p_model::listQueryTarifHonor($r_honor);
					
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode Tarif', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Honor', 'combo' => $l_honor);
	
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
		
	$a_salinperiode = $p_model::getCSalinPeriodeTarif($conn,$r_periode);
	$a_salinhonor = $p_model::getCSalinHonor($conn,$r_honor);
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
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table cellpadding="5" cellspacing="0" width="<?= $p_tbwidth-12 ?>px";>
							<tr>
								<td width="95px" style="white-space:nowrap"><strong>Salin dari Periode Tarif:</strong></td>
								<td><?= UI::createSelect('speriodetarif',$a_salinperiode,'', 'ControlStyle',$c_edit,'',true) ?>
								</td>
								<td width="50px" style="white-space:nowrap"><strong>Honor :</strong></td>
								<td><?= UI::createSelect('shonor',$a_salinhonor,'', 'ControlStyle',$c_edit,'',true) ?></td>
								<td width="50px" style="white-space:nowrap"><strong>Proc. Kenaikan (%) :</strong></td>
								<td width="30px"><?= UI::createTextBox('sprosentase','','ControlStyle','3','3',$c_edit) ?></td>
								<td><input type="button" value="Salin Honor" id="be_copy" class="ControlStyle" onClick="goCopy()"></td>
							</tr>
						</table>
					</div>
					<br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goInsert()">+</div>
							</div>
							<div class="right">
								<img title="Cetak master tarif honor" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrintTarifHonor()">
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
						<th width="50">Aksi</th>
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
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
						</td>
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
							Record : <?= uCombo::listRowNum($r_row,'onchange="goLimit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= Page::getTheLastPage();?>
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
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goInsert(){
	document.getElementById("pageform").action = detailpage;
	goSubmit();
	document.getElementById("pageform").action = '';
}

function goChange(){
	document.getElementById("page").value='1';
	goSubmit();
}

function goCopy(){
	var comboPerTarif = document.getElementById("speriodetarif");
    var valPerTarif = comboPerTarif.options[comboPerTarif.selectedIndex].text

	var comboHonor = document.getElementById("shonor");
    var valHonor = comboHonor.options[comboHonor.selectedIndex].text
	
	var retval = confirm("Apakah anda yakin akan menyalin tarif honor dari periode tarif '" + valPerTarif + "' dan honor '" + valHonor + "' ?");
	if(retval) {
		document.getElementById("act").value = "salin";
		goSubmit();
	}
}
function goPrintTarifHonor(){
	var kperiode = '<?= $r_periode?>';
	var khonor = '<?= $r_honor?>';
	window.open("<?= Route::navAddress('rep_gatarifhonor') ?>"+"&periodetarif="+kperiode+"&honor="+khonor+"&format=html","_blank");
}
</script>
</body>
</html>
