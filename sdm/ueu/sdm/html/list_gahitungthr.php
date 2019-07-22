<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mGaji;
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEGAJITHR');
	$r_setting = Modul::setRequest($_POST['setting'],'SETTINGTHR');
	$r_bayar = Modul::setRequest($_POST['bayar'],'BAYARGAJI');
	if(empty($r_setting))
		$r_setting = 'O';
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'reset' and $c_insert) {
		Modul::resetSession('HUBKERJATHR');
	}
	
	if(count($_POST['hubkerja'])>0 and $r_act != 'reset')
		$i_hubkerja = implode(',',$_POST['hubkerja']);
	$a_hubkerja = Modul::setRequest($i_hubkerja,'HUBKERJATHR');
	if(!empty($a_hubkerja))
		$r_hubkerja = explode(',',$a_hubkerja);
		
	//periode aktif
	$r_periodenow = $p_model::getLastPeriodeGajiTHR($conn);
	if(empty($r_periode))
		$r_periode = $r_periodenow;
	
	//data referensi periode thr
	$dref = $p_model::getDataPeriodeGajiTHR($conn,$r_periode);
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$a_periode = $p_model::getCPeriodeGajiTHR($conn);
	$l_periode = UI::createSelect('periode',$a_periode,$r_periode,'ControlStyle',true,'onchange="goSubmit()"');
	
	$a_hubkerja = $p_model::getCHubkerja($conn);
	$a_setting = $p_model::getCSettingTHR();
	$l_setting = UI::createSelect('setting',$a_setting,$r_setting,'ControlStyle',true);
	$a_bayar = $p_model::getCBayar();
	$l_bayar = UI::createSelect('bayar',$a_bayar,$r_bayar,'ControlStyle',true,'onchange="goSubmit()"');
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namapegawai', 'label' => 'Nama Pegawai','filter'=>'sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit');
	$a_kolom[] = array('kolom' => 'namajenispegawai', 'label' => 'Jenis Pegawai');
	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Pendidikan');
	$a_kolom[] = array('kolom' => 'mkgaji', 'label' => 'Masa Kerja','filter' => "substring(gh.masakerja,1,2)+' tahun ' + substring(gh.masakerja,3,2)+' bulan'");
	$a_kolom[] = array('kolom' => 'pph', 'label' => 'PPh21','type' => 'N','align' => 'right');
	
	// properti halaman
	$p_title = 'Daftar Gaji THR';
	$p_tbwidth = 1000;
	$p_aktivitas = 'ANGGARAN';
	$p_detailpage = 'data_gaslipgaji';
	$p_dbtable = "ga_gajipeg";
	$p_key = "periodegaji,idpegawai";
	
	$p_colnum = count($a_kolom)+2;
		
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	if (empty($r_sort))
		$r_sort = 'namapegawai';
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_bayar)) $a_filter[] = $p_model::getListFilter('bayar',$r_bayar);
		
	//cek apakah sudah dihitung atau disimpan
	$isExist = $p_model::isDihitungTHR($conn,$r_periode);

	$sql = $p_model::listQueryHitungTHR($r_periode,$dref['refperiodegaji'],$r_hubkerja,$isExist);
	$a_sql = $p_model::getListQuery($r_sort,$a_filter,$sql,$table,$r_row);
	
	if($r_act == 'save' and $c_edit) {			
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::saveGajiTHR($conn,$r_periode,$_POST);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'hitung' and $c_insert) {		
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::hitGajiTHR($conn,$r_periode,$a_sql,$dref['refperiodegaji']);
		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
		}
	}
	else if($r_act == 'hitpajak' and $c_insert) {		
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::hitPajak($conn,$r_periode,$a_sql);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'bayar' and $c_insert) {
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = $p_model::bayarGaji($conn,$r_periode,$a_sql);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
		
	$a_data = array();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode Gaji', 'combo' => $l_periode);
	
	//periode aktif
	if($r_periode != $r_periodenow){
		$p_posterr = true;
		$p_postmsg = 'Periode gaji tidak aktif';
		$c_insert = false;
	}
	
	//cek apakah sudah dilakukan penarikan data
	$istarik = $p_model::isTarikData($conn,$r_unit,$dref['refperiodegaji']);
	if(!$istarik){
		$p_posterr = true;
		$p_postmsg = 'Silahkan setting THR berdasarkan jenis pegawainya';
		$c_insert = false;
	}
	
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
	<div id="wrapper" style="width:<?= $p_tbwidth+50 ?>px;">
		<div class="SideItem" id="SideItem" style="width:<?= $p_tbwidth+15 ?>px;">
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
											<td width="90" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
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
										foreach($a_datafilter as $t_idx => $t_data) {?>
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
							<tr>
								<td>
									<table cellpadding="4" cellspacing="0">	
										<tr>
											<td width="120"><strong>Hubungan Kerja :</strong></td>
											<td>
											<? if (count($a_hubkerja)>0){
												foreach($a_hubkerja as $keys=>$vals){ 
											?>
													<input type="checkbox" id="hubkerja<?= $keys; ?>" name="hubkerja[]" value="<?= $keys; ?>" <?= in_array($keys,$r_hubkerja) ? 'checked' : '';?>><label for="hubkerja<?= $keys; ?>"><?= $vals; ?></label> <br />											
											<? }} ?>	
											</td>
										</tr>
									</table>
								</td>
								<td>
									<table cellpadding="4" cellspacing="0">	
										<tr>						
											<td width="120"><strong>Cara Hitung :</strong></td>
											<td>
												<?= $l_setting ?>&nbsp;&nbsp;&nbsp;
												<input type="button" value="Setting" class="ControlStyle" onClick="goSubmit()">
												<input type="button" value="Reset" class="ControlStyle" onClick="goReset()">
											</td>
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td colspan="2">
									<table cellpadding="3" cellspacing="0">	
										<tr>						
											<td width="120"><strong>Status :</strong></td>
											<td><?= $l_bayar ?></td>
										</tr>
									</table>
								</td>
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
							<?	if($c_insert) { ?>
							<?if($r_setting == 'O'){?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goHitung()">
									<img src="images/calc.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Hitung</span>
								</div>
							</div>
							<?}?>
							<?if($r_setting == 'M'){?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goSimpan()" title="Simpan THR halaman <?= $r_page?>">
									<img src="images/disk.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Save</span>
								</div>
							</div>
							<?}?>
							<?if(!empty($isExist)){?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goBayar()">
									<img src="images/check.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Bayarkan</span>
								</div>
							</div>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goHitPajak()">
									<img src="images/calc.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Hit. Pajak</span>
								</div>
							</div>
							<?}?>
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
						<th width="120">Gaji THR</th>
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
						<td align="right">
							<input type="hidden" name="id[]" value="<?= $row['idpegawai']; ?>" />
							<?= $r_setting == 'M' ? UI::createTextBox('gajiditerima_'.$row['idpegawai'],CStr::formatNumber($row['gajiditerima']),'ControlStyle',14,14,$c_edit,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"') : CStr::formatNumber($row['gajiditerima']); ?>
						</td>
						<td align="center">
							<img id="<?= $t_key.'::list_gahitungthr' ?>" title="Tampilkan Slip Gaji" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
							<?if($row['isfinish'] == 'Y'){?>
							<img title="Sudah dibayarkan" src="images/check.png">
							<?}?>
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

function goHitung() {
	var hitung = confirm("Apakah anda ingin melakukan perhitungan gaji THR?");
	if(hitung) {
		document.getElementById("act").value = "hitung";
		goSubmit();
	}
}

function goSimpan() {
	var simpan = confirm("Anda yakin untuk menyimpan gaji THR?");
	if (simpan){
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

function goReset() {
	document.getElementById("act").value = "reset";
	goSubmit();
}

function goHitPajak() {
	document.getElementById("act").value = "hitpajak";
	goSubmit();
}

function goBayar() {
	var hitung = confirm("Apakah anda ingin membayarkan gaji periode ini?");
	if(hitung) {
		document.getElementById("act").value = "bayar";
		goSubmit();
	}
}
</script>
</body>
</html>
