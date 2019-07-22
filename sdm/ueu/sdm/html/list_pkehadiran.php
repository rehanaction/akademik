<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_edit = $a_auth['canupdate'];
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel esensial	
	if(SDM::isPegawai())
		$r_self = 1;
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
		
	// properti halaman
	$p_title = 'Daftar Rekap Presensi';
	$p_tbwidth = 900;
	$p_aktivitas = 'ABSENSI';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPresensi;
	$p_key = 'periode';
	$p_dbtable = 'pe_presensi';
		
	// variabel request
	$r_tahun = Modul::setRequest($_POST['periode'],'PERIODEPRESENSI');
	if(empty($r_tahun))
		$r_tahun = date('Y');
	
	// combo
	$data = $p_model::getTahunPresensi($conn,$r_key);
	$l_tahun = uCombo::combo($data,$r_tahun,'periode','onchange="goSubmit()"',false);
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode', 'align' => 'center', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'hadir', 'label' => 'Hadir', 'align' => 'center', 'width' => '75px');
	$a_kolom[] = array('kolom' => 'sakit', 'label' => 'Sakit', 'align' => 'center', 'width' => '75px');
	$a_kolom[] = array('kolom' => 'izin', 'label' => 'Izin', 'align' => 'center', 'width' => '75px');
	$a_kolom[] = array('kolom' => 'alpa', 'label' => 'Alpa', 'align' => 'center', 'width' => '75px');
	$a_kolom[] = array('kolom' => 'cuti', 'label' => 'Cuti', 'align' => 'center', 'width' => '75px');
	$a_kolom[] = array('kolom' => 'dinas', 'label' => 'Dinas', 'align' => 'center', 'width' => '75px');
	$a_kolom[] = array('kolom' => 'tugasbelajar', 'label' => 'Tugas Belajar', 'align' => 'center', 'width' => '120px');
	$a_kolom[] = array('kolom' => 'terlambat', 'label' => 'Terlambat', 'align' => 'center', 'width' => '100px');
	$a_kolom[] = array('kolom' => 'pulangduluan', 'label' => 'Pulang Awal', 'align' => 'center', 'width' => '100px');
	$a_kolom[] = array('kolom' => 'hadirlibur', 'label' => 'Hadir Libur', 'align' => 'center', 'width' => '100px');

	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = 'periode';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_tahun)) $a_filter[] = $p_model::getListFilter('periode',$r_tahun);
	
	$sql = $p_model::listQueryRekapPresensi($r_key);
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_tahun);
	
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
	<script type="text/javascript" src="scripts/forpagerx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?php
					if(!(empty($a_filtercombo) and empty($r_page))) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<?	/************************/
									/* COMBO FILTER HALAMAN */
									/************************/
									
									if(!empty($a_filtercombo)) {
								?>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
								<?	}
									
									/**********************/
									/* COMBO FILTER KOLOM */
									/**********************/
									
									if(!empty($r_page)) {
								?>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>
											<td align="right"><input type="button" value="Refresh" class="ControlStyle" onClick="goRefresh()"></td>
										</tr>
									</table>
								</td>
							<?	} ?>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?php
					}
				?>
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
						<?	
							foreach($a_kolom as $datakolom) {								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?></th>
						<?	} ?>
						<th width="50">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row, $p_key);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								if($a_kolom[$j]['kolom'] == 'periode')
									$rowcc = Date::indoMonth((int) substr($rowcc,4,2),true).' '.substr($rowcc,0,4);
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/magnify.png" onclick="showDetail(this)" style="cursor:pointer">
							<img id="<?= $t_key?>" title="Cetak Presensi" src="images/small-print.png" onclick="goPrint(this)" style="cursor:pointer">
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
				<?php require_once('inc_listnavajax.php'); ?>
				<? } ?>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var xtdid = "contents";
var sent = "key=<?= $r_key; ?>";

$(document).ready(function() {	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function showDetail(periode){
	win = window.open("<?= Route::navAddress('pop_detailpresensi').'&key='.$r_key.'&periode='?>"+periode.id,"popup_detailpresensi","width=950,height=800,scrollbars=1");
	win.focus();
}

function goPrint(elem){
	var thn = elem.id.substring(0,4);
	var bln = elem.id.substring(4);
	window.open("<?= Route::navAddress('rep_kehadiranpeg') ?>"+"&format=doc&idpegawai=<?= $r_key?>&tahun="+thn+"&bulan="+bln,"_blank");
}
</script>
</body>
</html>

