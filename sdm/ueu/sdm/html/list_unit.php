<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//koneksi dengan mutu
	//$connmutu = Query::connect('mutu');
	//if($_SERVER['REMOTE_ADDR'] == "36.85.91.184" or $_SERVER['REMOTE_ADDR'] == "66.96.234.212") //ip public sevima
	//	$connmutu->debug=true;
	
	// include
	require_once(Route::getModelPath('mastkepegawaian'));
	require_once(Route::getModelPath('integrasi'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar Unit Kerja';
	$p_tbwidth = 800;
	$p_aktivitas = 'UNIT';
	$p_key = 'idunit';
	$p_dbtable = 'ms_unit';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mMastKepegawaian;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Kode','align' => 'center', 'filter' => 'u.kodeunit');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit Kerja', 'filter' => 'u.namaunit');
	$a_kolom[] = array('kolom' => 'namaparentunit', 'label' => 'Parent Unit Kerja', 'filter' => 'pu.namaunit');
	
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_key = CStr::removeSpecial($_POST['key']);
	if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();		
		
		$p_posterr = $p_model::deleteUnit($conn,$r_key);
		if($p_posterr)
			$p_postmsg = 'Penghapusan Unit Kerja gagal';
		/*
		if(!$p_posterr){
			$p_posterr = $p_model::deleteInfoLain($connmutu,'mutu','ms_unit',$p_key,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Mutu gagal';
		}

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);

		if(!$p_posterr){
			$p_posterr = Query::qDelete($connmutu,'mutu.ms_unit',$p_model::getCondition($r_key,$p_key));
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Mutu gagal';
		}
			
		//delete juga integrasi sim gate dan aset
		if(!$p_posterr){
			$p_posterr = mIntegrasi::deleteUnit($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Integrasi gagal';
		}
			
		//delete juga integrasi mutu
		if(!$p_posterr){
			$p_posterr = mIntegrasi::deleteUnitLain($connmutu,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penghapusan Unit Kerja Mutu gagal';
		}
		
		$ok = Query::isOK($p_posterr);
		if($ok){
			//mengganti right
			Modul::changeRight($conn,Modul::getUnit(),Modul::getRole(),Modul::getUserID());
			
			$conn->CommitTrans($ok);
		}else
			$conn->RollbackTrans();
			*/
	}
	else if($r_act == 'up' and $c_edit) {
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::moveUp($p_key,$r_key,$p_dbtable);

		if(!$p_posterr){
			list($p_posterr,$p_postmsg) = $p_model::moveUpLain($connmutu,$p_key,$r_key,'mutu','ms_unit');
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Kerja Mutu gagal';
		}

		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveUnit($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Kerja Integrasi gagal';
		}

		if(!$p_posterr){
			list($p_posterr,$p_postmsg) = mIntegrasi::saveUnitLain($connmutu,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Kerja Mutu gagal';
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);			
	}
	else if($r_act == 'down' and $c_edit) {
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::moveDown($p_key,$r_key,$p_dbtable);

		if(!$p_posterr){
			list($p_posterr,$p_postmsg) = $p_model::moveDownLain($connmutu,$p_key,$r_key,'mutu','ms_unit');
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Kerja Mutu gagal';
		}
		
		if(!$p_posterr){
			$p_posterr = mIntegrasi::saveUnit($conn,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Kerja Integrasi gagal';
		}

		if(!$p_posterr){
			list($p_posterr,$p_postmsg) = mIntegrasi::saveUnitLain($connmutu,$r_key);
			if($p_posterr)
				$p_postmsg = 'Penyimpanan Unit Kerja Mutu gagal';
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'infoleft';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listUnit();
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql,$p_dbtable);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
		
	// menentukan yang termasuk id first last
	$a_first = array();
	$a_last = array();
	foreach($a_data as $row) {
		$a_last[$row['parentunit']] = $row['idunit'];
		if(empty($a_first[$row['parentunit']]))
			$a_first[$row['parentunit']] = $row['idunit'];
	}
	
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
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
							<div class="right">
								<img title="Cetak unit kerja" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
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
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="120">Aksi</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,$p_key);
										
							// cek first last
							if(in_array($t_key,$a_first))
								$t_isfirst = true;
							else
								$t_isfirst = false;
							
							if(in_array($t_key,$a_last))
								$t_islast = true;
							else
								$t_islast = false;
							
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
						<?		if($c_insert and $row['level'] < 6) { ?>
							<img id="<?= $t_key ?>" title="Tambah Sub Unit" src="images/child.png" onclick="goChild(this)" style="cursor:pointer">
						<?		}
								if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
								if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
							<img class="ImgRight" src="images/separator.png">
						<?	if($c_edit) {
								if($t_islast) { ?>
							<img class="ImgRight ImgDisabled" src="images/down.png">
							<?	} else { ?>
							<img id="<?= $t_key ?>" class="ImgAct" src="images/down.png" style="cursor:pointer" title="Turunkan item" onclick="goDownItem(this)">
							<?	}
								if($t_isfirst) { ?>
							<img class="ImgRight ImgDisabled" src="images/up.png">
							<?	} else { ?>
							<img id="<?= $t_key ?>" class="ImgAct" src="images/up.png" style="cursor:pointer" title="Naikkan item" onclick="goUpItem(this)">
							<?	} ?>
						<?	} ?>
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
				<input type="hidden" name="pkey" id="pkey">
			</form>
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

function goChild(elem){
	location.href = detailpage + "&pkey=" + elem.id;
}

function goPrint(){
	window.open("<?= Route::navAddress('rep_unit') ?>&format=html","_blank");
}

function goUpItem(elem) {
	document.getElementById("act").value = "up";
	document.getElementById("key").value = elem.id;
	goSubmit();
}

function goDownItem(elem) {
	document.getElementById("act").value = "down";
	document.getElementById("key").value = elem.id;
	goSubmit();
}
</script>
</body>
</html>
