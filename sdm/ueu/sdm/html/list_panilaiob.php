<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit');
	$a_kolom[] = array('kolom' => 'nilaiob1', 'label' => 'Nilai', 'type' => 'N,2');
	
	// properti halaman
	$p_title = 'Daftar Penilaian Objektif (OB-1)';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 'pa_kinerjaob1unit';
	
	$p_model = mPa;
	$p_colnum = count($a_kolom)+2;
	
	$r_periode = CStr::removeSpecial($_POST['periode']);
	$r_idpenilai = CStr::removeSpecial($_POST['idpenilai']);
	$r_penilai = CStr::removeSpecial($_POST['penilai']);
	if (empty($r_periode)) $r_periode = $p_model::getLastPeriode($conn);
	
	// ada aksi
	$r_act = CStr::removeSpecial($_POST['act']);
	if($r_act == 'simpan' and $c_edit) {
		$conn->StartTrans();
		$a_id = $_POST['unit'];
		if (count($a_id) > 0){
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_periode, $p_dbtable,"kodeperiode");
			foreach($a_id as $r_id){
				$record = array();
				$record['kodeperiode'] = $r_periode;
				$record['idunit'] = $r_id;
				$record['nilaiob1'] = CStr::cStrNull(CStr::cStrDec($_POST['nilai_'.$r_id]));
				if ($record['nilaiob1'] != 'null')
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record, true, $p_dbtable);
			}
		}
		$conn->CompleteTrans();
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'nik';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
		
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodetim',$r_periode);
	
	$a_data = array();
	$a_data = $p_model::getNilaiUnitOB($conn, $r_periode);
	
			
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => UI::createSelect('periode',mPa::getCPeriode($conn), $r_periode, 'ControlStyle',$c_edit,'onChange="goSubmit()"'));
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
					<br />
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<? if ($c_edit) { ?>
							<div class="right">
								<div class="TDButton" onclick="goSave()" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;">
								<img style="position:relative;left:-60px;top:-7px;" src="images/disk.png">
								<span style="position:relative;left:15px">Simpan</span>
								</div>
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
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'idtim');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?>
						<input type="hidden" name="unit[]" id="unit[]" value="<?= $row['idunit']?>" />
						</td>
						<td style="padding-left:<?= $row['level']*10; ?>px"><?= $rowc[$j++] ?></td>
						<td><?= UI::createTextBox('nilai_'.$row['idunit'],CStr::formatNumber($row['nilaiob1'],2),'ControlStyle',5,5,$c_edit,'onKeyDown="return onlyNumber(event,this,true,true)"')?></td>
					</tr>
					<?	}}
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
						<td colspan="<?= $p_colnum ?>" align="center" class="FootBG">
							<? if ($c_edit) {?>
							<div class="center" style="width:90px">
								<div class="TDButton" onclick="goSave()" >
								<img src="images/disk.png">
								Simpan
								</div>
							</div>
							<? } ?>
						</td>
					</tr>
					<?	} ?>
				</table>
				
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

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSave(){
	var simpan = confirm("Apakah anda yakin untuk menyimpan nilai objektif ini?");
	if (simpan){
		document.getElementById("act").value = "simpan";
		goSubmit();
	}
}

</script>
</body>
</html>
