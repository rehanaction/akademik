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
	$r_golongan = Modul::setRequest($_POST['golongan'],'GOLONGAN');
	
	// combo
	$a_tarif = $p_model::getCPeriodeTarif($conn);
	$l_periode = uCombo::combo($a_tarif,$r_periode,'periodetarif','onchange="goSubmit()"',false);
	
	$a_gol = $p_model::getCPangkat($conn);
	$l_golongan = uCombo::combo($a_gol,$r_golongan,'golongan','onchange="goSubmit()"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'golongan', 'label' => 'Pangkat','align' => 'center');
	$a_kolom[] = array('kolom' => 'masakerja', 'label' => 'Masa Kerja (Tahun)','align' => 'center');
	$a_kolom[] = array('kolom' => 'tarifgapok', 'label' => 'Tarif','type' => 'N','align' => 'right');
	
	// properti halaman
	$p_title = 'Daftar Gaji Pokok';
	$p_tbwidth = 750;
	$p_aktivitas = 'ANGGARAN';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = "ms_tarifgapok";
	$p_key = "periodetarif,idpangkat,masakerja";
	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'deleteall' and $c_delete) {
		$r_periode = CStr::removeSpecial($_POST['periodetarif']);
		$where = "periodetarif";
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_periode,$p_dbtable,$where);
	}
	else if($r_act == 'salin' and $c_edit) {
		
		$conn->BeginTrans();
		$keyPeriode = $_POST['speriodetarif'];
		$prosentase = $_POST['sprosentase']/100;
		list($p_posterr,$p_postmsg) = $p_model::saveSalinGapok($conn,$r_periode,$keyPeriode,$prosentase);
			
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'masakerja';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodetarif',$r_periode);
	if(!empty($r_golongan)) $a_filter[] = $p_model::getListFilter('golongan',$r_golongan);
	
	$sql = $p_model::listQueryGapok();
					
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode Tarif', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Pangkat', 'combo' => $l_golongan);
	
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
		
	$a_salinperiode = $p_model::periodeTarifSalin($conn,$r_periode);
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
								<td width="80px"><?= UI::createSelect('speriodetarif',$a_salinperiode,'', 'ControlStyle',$c_edit,'',true) ?>
								</td>
								<td width="50px" style="white-space:nowrap"><strong>Prosentase peningkatan :</strong></td>
								<td width="30px"><?= UI::createTextBox('sprosentase','','ControlStyle','3','3',$c_edit) ?></td>
								<td>
									<input type="button" value="Salin" id="be_hitung" class="ControlStyle" onClick="goHitung()">&nbsp;&nbsp;
									<input type="button" value="Hapus" id="be_hapus" class="ControlStyle" onClick="goHapusAll()">
								</td>
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
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
							<div class="right">
								<img title="Cetak master gaji pokok periode ini" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrintGapok()">
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

function goPrintGapok(){
	var key = '<?= $r_periode?>';
	window.open("<?= Route::navAddress('rep_gagapok') ?>"+"&periodetarif="+key+"&format=html","_blank");
}

function goHitung(){
	var comboPerTarif = document.getElementById("speriodetarif");
    var valPerTarif = comboPerTarif.options[comboPerTarif.selectedIndex].text

	var textProsentasi = document.getElementById("sprosentase").value;
	
	if(textProsentasi==''){
		textProsentasi='0';
	}
	
	var retval = confirm("Apakah anda yakin akan menyalin tarif gaji pokok dari periode tarif '" + valPerTarif + "' dengan penambahan prosentase " + textProsentasi + "% ?");
	
	if(retval) {
		document.getElementById("act").value = "salin";
		goSubmit();
	}
}

function goHapusAll(){
	var combotarif = document.getElementById("periodetarif");
    var valPerTarif = combotarif.options[combotarif.selectedIndex].text
    
	var retval = confirm("Apakah anda yakin akan menghapus tarif gaji pokok '" + valPerTarif + "'");
	
	if(retval) {
		document.getElementById("act").value = "deleteall";
		goSubmit();
	}
}
</script>
</body>
</html>
