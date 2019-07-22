<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Daftar Rekap Presensi';
	$p_tbwidth = 1000;
	$p_aktivitas = 'TIME';
	$p_key = 'periode,idpegawai';
	$p_dbtable = 'pe_presensi';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPresensi;
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_month = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_month = empty($r_month) ? (int)date('m') : $r_month;
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_tahun = empty($r_tahun) ? date('Y') : $r_tahun;
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$l_bulan = uCombo::bulan($r_month,true,'bulan','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama', 'filter' => 'sdm.f_namalengkap(gelardepan,namatengah,namadepan,namabelakang,gelarbelakang)');
	$a_kolom[] = array('kolom' => 'hadir', 'label' => 'Hadir', 'width' => '50px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'sakit', 'label' => 'Sakit', 'width' => '50px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'izin', 'label' => 'Izin', 'width' => '50px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'alpa', 'label' => 'Alpa', 'width' => '50px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'cuti', 'label' => 'Cuti', 'width' => '50px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'dinas', 'label' => 'Dinas', 'width' => '50px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'terlambat', 'label' => 'Terlambat', 'width' => '70px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'pulangduluan', 'label' => 'Pulang Awal', 'width' => '80px', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'hadirlibur', 'label' => 'Hadir Libur', 'width' => '80px', 'align' => 'center');
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'nip desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_month)) $a_filter[] = $p_model::getListFilter('blnpresensi',str_pad($r_month, 2, "0", STR_PAD_LEFT));
	if(!empty($r_tahun)) $a_filter[] = $p_model::getListFilter('periode',$r_tahun);
	
	$sql = $p_model::listRekapPresensi();
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
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
	<div id="wrapper" style="width:<?= $p_tbwidth+50 ?>px;">
		<div class="SideItem" id="SideItem" style="width:<?= $p_tbwidth+15 ?>px;">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td width="50" style="white-space:nowrap"><strong>Unit </strong></td>
											<td><strong> : </strong><?= $l_unit ?></td>		
										</tr>
										<tr>		
											<td style="white-space:nowrap"><strong>Periode </strong></td>
											<td><strong> : </strong><?= $l_bulan.' '.$l_tahun ?></td>		
										</tr>
									</table>
								</td>
								<?
									/**********************/
									/* COMBO FILTER KOLOM */
									/**********************/
									
									if(!empty($r_page)) {
								?>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>
											<td width="40" style="white-space:nowrap"><strong>Cari :</strong></td>
											<td width="50"><?= uCombo::listColumn($a_kolom,'',(Modul::setRequest($_POST['cfilter'],'CFILTER_'.Route::thisPage()))) ?></td>
											<td width="210"><input name="tfilter" id="tfilter" class="ControlStyle" size="25" onkeydown="etrFilterCombo(event)" type="text"></td>
											<td width="40"><input type="button" value="Filter" class="ControlStyle" onClick="goFilterCombo()"></td>
											<td><input type="button" value="Refresh" class="ControlStyle" onClick="goRefresh()"></td>
										</tr>
									</table>
									<?	/********************/
										/* INFORMASI FILTER */
										/********************/
										
										if(!empty($a_datafilter)) { ?>
									<table cellpadding="4" cellspacing="0" class="LiteHeaderBG">
									<?	$i = 0;
										foreach($a_datafilter as $t_idx => $t_data) { ?>
										<tr>
											<td width="30" style="white-space:nowrap"><?= $t_data['label'] ?></td>
											<td align="center" width="5">:</td>
											<td><?= $t_data['str'] ?></td>
											<td valign="top" align="right"><u title="Hapus Filter" id="remfilter" style="color:#3300FF;cursor:pointer;text-decoration:none" onclick="goRemoveFilter(<?= $i++ ?>)">x</u></td>
										</tr>
									<?	} ?>
									</table>
									<?	} ?>
								</td>
							<?	} ?>
							</tr>
						</table>
					</div>
				</center>
				<br>
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
						<th width="30">Aksi</th>
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
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/magnify.png" onclick="showDetail(this)" style="cursor:pointer">
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

function showDetail(pkey){
	pkey = pkey.id;
	pkey = pkey.split('|');
	win = window.open("<?= Route::navAddress('pop_detailpresensi')?>&periode="+pkey[0]+"&key="+pkey[1],"popup_detailpresensi","width=950,height=800,scrollbars=1");
	win.focus();
}

function goSimpan(){
	document.getElementById("act").value = "save";
	goSubmit();	
}
</script>
</body>
</html>
