<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	//$conn->debug = true;	
	//$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	$_SESSION['SERI'] = 'list_dir';
	$p_printpage = true;
	
	// include
	require_once(Route::getModelPath('seri'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_idunitn = $_SESSION[SITE_ID]['VAR']['UNIT'];
    $r_unit = Modul::setRequest($_POST['unit'],'UNIT');
    $r_idunito = $_SESSION[SITE_ID]['VAR']['UNIT'];
    if($r_idunito != $r_idunitn){
        $_POST['lokasi'] = '';
        $_POST['pemakai'] = '';
    }
	
	//$r_unit = Modul::setRequest($_POST['unit'], 'UNIT');
	$r_lokasi = Modul::setRequest($_POST['lokasi'], 'LOKASI');
	$r_pemakai = Modul::setRequest($_POST['pemakai'], 'PEMAKAI');
	
	// combo
	$lr = Modul::getLeftRight();
	if($lr['LEFT'] == '1')
    	$l_unit = uCombo::unitAuto($conn,$r_unit,'unit','onchange="goSubmit()"');
	else
    	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:270px;"',false);
	$l_lokasi = uCombo::lokasibrg($conn,$r_lokasi,'lokasi','onchange="goSubmit()" style="width:270px;"',true,$r_unit);
	$l_pemakai = uCombo::pemakai($conn,$r_pemakai,'pemakai','onchange="goSubmit()" style="width:270px;"',true,$r_unit);

	// properti halaman
	$p_title = 'Data Inventaris Ruang';
	$p_tbwidth = 950;
	$p_aktivitas = 'Inventaris Ruang';
	$p_detailpage = Route::navAddress('data_seri');
	
	$p_model = mSeri;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'width' => 50, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'unit', 'label' => 'Unit', 'width' => 125, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'pegawai', 'label' => 'Pemakai', 'width' => 175, 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'width' => 50, 'align' => 'center', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'barang', 'label' => 'Nama Barang');
    $a_kolom[] = array('kolom' => 'merk', 'label' => 'Merk', 'width' => 100, 'nosearch' => true);
//    $a_kolom[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'width' => 60, 'align' => 'center', 'nosearch' => true);
	$a_kolom[] = array('kolom' => 'tglperolehan', 'label' => 'Tgl. Perolehan', 'type' => 'D', 'width' => 75, 'align' => 'center');
    $a_kolom[] = array('kolom' => 'idkondisi', 'label' => 'Kondisi', 'width' => 40, 'align' => 'center', 'nosearch' => true);
    $a_kolom[] = array('kolom' => 'idstatus', 'label' => 'Status', 'width' => 40, 'align' => 'center', 'nosearch' => true);
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
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
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_lokasi)) $a_filter[] = $p_model::getListFilter('lokasi',$r_lokasi);
	if(!empty($r_pemakai)) $a_filter[] = $p_model::getListFilter('pemakai',$r_pemakai);
	
	$a_kondisi = mCombo::kondisi($conn);
	$a_statusbarang = mCombo::status($conn);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	

	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Lokasi', 'combo' => $l_lokasi);
	$a_filtercombo[] = array('label' => 'Pemakai', 'combo' => $l_pemakai);


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
							<?	if($c_insert or $p_printpage) { ?>
							<div class="right">
							<?	if($p_printpage) { ?>
								<img title="Cetak Kartu Inventaris Ruang" width="24px" src="images/print.png" style="cursor:pointer" onclick="goCetakKIR()">
								<img title="Cetak Label" width="24px" src="images/barcode.png" style="cursor:pointer" onclick="goCetakLabel()">
							<?	}
								if($c_insert) { ?>
								<div class="addButton" onClick="goNew()">+</div>
							<?	} ?>
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
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="35">Aksi</th>
						<?	} ?>
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
								
								$pad = '';
								if($j==1)//utk padding nama unit
									$pad = 'style="padding-left:'.(($row['level']*15)+4).'px"';
						?>
						<td<?= $t_align.' '.$pad ?>><?= $rowcc ?></td>
						<?	}
							if($c_edit or $c_delete) { ?>
						<td align="right" style="padding-right:12px">
						<?		if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
								if($c_delete) { ?>
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
				<input type="hidden" name="pkey" id="pkey">
				<input type="hidden" name="from" id="from" value="dir">
				<input type="hidden" name="startno" id="startno">
				
				<? if(!empty($p_printpage)) { ?>
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				<? } ?>
			</form>
			<br /><br />
			<center>
				<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
					<table cellspacing="0" cellpadding="0" align="center" width="890">
						<tr>
							<td>
								<table cellspacing="0" cellpadding="0" align="left">
									<tr><td colspan="2"><strong>Keterangan Kondisi Barang :</strong></td></tr>
									<? foreach ( $a_kondisi as $kode => $label){?>
									<tr><td width="20"><?= $kode; ?></td><td>: <?= $label; ?></td></tr>
									<? } ?>
								</table>
							</td>
							<td>
								<table cellspacing="0" cellpadding="0" align="left">	
									<tr><td colspan="2"><strong>Keterangan Status Barang :</strong></td></tr>
									<? foreach ( $a_statusbarang as $kode => $label){?>
									<tr><td width="20"><?= $kode; ?></td><td>: <?= $label; ?></td></tr>
									<? } ?>
								</table>
							</td>
						</tr>
					</table>
				</div>
			</center>
		</div>
	</div>
</div>

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
	
	<?  if($lr['LEFT'] == '1') { ?>
	$("#namaunit").xautox({strpost: "f=acxunit", targetid: "unit", imgchkid: "imgunit", imgavail: true});
	<?  } ?>
});

<? if($p_printpage) { ?>
function goPrint() {
	showPage('null','<?= Route::navAddress($p_printpage) ?>');
}
<? } ?>

function goChild(elem){
	location.href = detailpage + "&pkey=" + elem.id;
}

function goCetakKIR() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_kir") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

function goCetakLabel() {
    var startno = prompt("Print label dimulai label ke ?","1");
    if(startno != null && startno != ""){
	    $('#startno').val(startno);
	 
	    $('#pageform').attr('action','<?= Route::navAddress("set_label") ?>');
	    $('#pageform').attr('target','_blank');
	    goSubmit();
	    $('#pageform').attr('action','');
	    $('#pageform').attr('target','');
    }
}

</script>
</body>
</html>
