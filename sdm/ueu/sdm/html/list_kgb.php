<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kenaikan'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mKenaikan;
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNITKGB');
	$r_month = Modul::setRequest($_POST['bulan'],'BULANKGB');
	$r_month = empty($r_month) ? (int)date('m') : $r_month;
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUNKGB');
	$r_tahun = empty($r_tahun) ? date('Y') : $r_tahun;
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$l_bulan = uCombo::bulan($r_month,true,'bulan','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Daftar Kenaikan Gaji Berkala';
	$p_tbwidth = 1250;
	$p_aktivitas = 'DAFTAR';
	$p_key = 'nokgb';
	$p_dbtable = 'pe_kgb';
	$p_detailpage = Route::getDetailPage();
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'align' => 'center', 'filter' => 'p.nip');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai', 'filter' => 'sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)');
	$a_kolom[] = array('kolom' => 'golonganlama', 'label' => 'Pangkat Lama', 'align' => 'center', 'filter' => 'pl.golongan');
	$a_kolom[] = array('kolom' => 'mklama', 'label' => 'Masa Kerja Lama', 'align' => 'center', 'filter' => "substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkglama as varchar), 4),3,2)+' bulan'");
	$a_kolom[] = array('kolom' => 'golonganbaru', 'label' => 'Pangkat Baru', 'align' => 'center', 'filter' => 'pb.golongan');
	$a_kolom[] = array('kolom' => 'mkbaru', 'label' => 'Masa Kerja Baru', 'align' => 'center', 'filter' => "substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),1,2)+' tahun '+substring(right(replicate('0', 4) + cast(r.mkg as varchar), 4),3,2)+' bulan'");
	$a_kolom[] = array('kolom' => $p_key);
	
	$p_colnum = count($a_kolom)+4;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'validasi' and $c_edit) {
		for($i=0;$i<count($_POST['kode']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode'][$i]);
			$is = CStr::cAlphaNum($_POST['isvalid'.$r_ida]);
			if(!empty($is)){
				$rec['isvalid'] = 'Y';
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$rec,$r_ida,false,$p_dbtable,$p_key);
			}
		}
	}
	else if($r_act == 'setujui' and $c_edit) {
		for($i=0;$i<count($_POST['kode']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode'][$i]);
			$is = CStr::cAlphaNum($_POST['issetuju'.$r_ida]);
			if(!empty($is)){
				$rec['issetuju'] = $is;
				$rec['tglpersetujuan'] = date('Y-m-d');
				$rec['alasan'] = CStr::cAlphaNum($_POST['alasan'.$r_ida]);
				list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$rec,$r_ida,false,$p_dbtable,$p_key);
			}
		}
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
	if(empty($r_sort)) $r_sort = 'nik';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_month)) $a_filter[] = $p_model::getListFilter('bulankgb',str_pad($r_month, 2, "0", STR_PAD_LEFT));
	if(!empty($r_tahun)) $a_filter[] = $p_model::getListFilter('tahunkgb',$r_tahun);
	
	$sql = $p_model::listQueryKGB();
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
	<div id="wrapper" style="width:<?= $p_tbwidth+50 ?>px">
		<div class="SideItem" id="SideItem" style="width:<?= $p_tbwidth+10 ?>px">
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
				<?php 						
				if(!empty($p_postmsg)) { ?>
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
							<?	if($c_edit) { ?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:100px;position:relative;left:-5px;top:5px;" onClick="goSetujui()">
									<img src="images/disk.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Setujui</span>
								</div>
							</div>&nbsp;
							<?if(Modul::getRole() == 'A' or Modul::getRole() == 'admhrm'){?>
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:100px;position:relative;left:-5px;top:5px;" onClick="goValidasi()">
									<img src="images/disk.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Validasi</span>
								</div>
							</div>
							<? } ?>
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
								if($datakolom['kolom'] == $p_key)
									continue;
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit) { ?>
						<th width="80">
							Validasi
							<?	if(Modul::getRole() == 'A' or Modul::getRole() == 'admhrm') { ?>
							<input type="checkbox" id="checkall" title="Validasi semua daftar" onClick="toggleV(this)">
							<?}?>
						</th>
						<th width="120">Setujui</th>
						<th>Alasan</th>
						<th width="50">Aksi</th>
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
								if($a_kolom[$j]['kolom'] == $p_key)
									continue;
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} 
							if($c_edit){
						?>
						<td align="center">
							<input type="hidden" name="kode[]" id="kode[]" value="<?= $row[$p_key] ?>">
						<?	if(Modul::getRole() == 'A' or Modul::getRole() == 'admhrm') { ?>
							<input type="checkbox" id="isvalid" name="isvalid<?= $row[$p_key] ?>" title="Cek untuk validasi per item" <?= $row['isvalid'] == 'Y' ? 'checked' : ''?>>
						<?
							}else{
								if($row['isvalid'] == 'Y'){
						?>
							<img src="check.png" title="Sudah valid">
						<?
								}
							}
						?>
						</td>
						<td style="padding-left:10px">
							<input type="radio" id="issetuju_Y<?= $row[$p_key] ?>" name="issetuju<?= $row[$p_key] ?>" value="Y" <?= $row['issetuju'] == 'Y' ? 'checked' : ''?>> <label for="issetuju_Y<?= $row[$p_key] ?>">Disetujui</label><br>
							<input type="radio" id="issetuju_T<?= $row[$p_key] ?>" name="issetuju<?= $row[$p_key] ?>" value="T" <?= $row['issetuju'] == 'T' ? 'checked' : ''?>> <label for="issetuju_T<?= $row[$p_key] ?>">Ditangguhkan</label>
						</td>
						<td>
							<?= UI::createTextArea('alasan'.$row[$p_key],$row['alasan'],'ControlStyle',2,30,$c_edit);?>
						</td>
						<?	} ?>
						<? if($c_edit or $c_delete) { ?>
						<td align="center">
						<?		if($c_edit) { ?>
							<img id="<?= $row[$p_key] ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
								if($c_delete) { ?>
							<img id="<?= $row[$p_key] ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
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
					<?	
						}
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
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function toggleV(elem) {
	var check = elem.checked;
	
	$("[id='isvalid']").attr("checked",check);
}

function toggleS(elem) {
	var check = elem.checked;
	
	$("[id='issetuju']").attr("checked",check);
}

function goValidasi(){
	$("#act").val("validasi");
	goSubmit();
}

function goSetujui(){
	$("#act").val("setujui");
	goSubmit();
}
</script>
</body>
</html>
