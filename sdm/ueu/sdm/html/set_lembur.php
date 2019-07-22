<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_month = Modul::setRequest($_POST['bulan'],'BULAN');
	$r_month = empty($r_month) ? (int)date('m') : $r_month;
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_tahun = empty($r_tahun) ? date('Y') : $r_tahun;
	$r_validasi = Modul::setRequest($_POST['validasi'],'VALIDASI');
	$r_persetujuan = Modul::setRequest($_POST['persetujuan'],'PERSETUJUAN');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$l_bulan = uCombo::bulan($r_month,true,'bulan','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$a_validasi = mPresensi::getCVal($conn);
	$l_validasi = UI::createSelect('validasi',$a_validasi,$r_validasi,'ControlStyle',true,'onchange="goSubmit()"');
	$l_persetujuan = UI::createSelect('persetujuan',$a_validasi,$r_persetujuan,'ControlStyle',true,'onchange="goSubmit()"');
	
	// properti halaman
	$p_title = 'Validasi Data Lembur Pegawai';
	$p_tbwidth = 900;
	$p_aktivitas = 'TIME';
	$p_key = 'idsuratlembur';
	$p_dbtable = 'pe_suratlembur';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mPresensi;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'tgllembur', 'label' => 'Tgl. Lembur', 'type' => 'D', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Pegawai',  'filter' => "sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)");
	
	$p_colnum = count($a_kolom)+7;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'valid' and $c_edit) {
		$a_valid = $_POST['valid'];
		
		if (count($a_valid) > 0){
			foreach($a_valid as $id){
				if (!empty($_POST['issetujuatasan_'.$id]) or !empty($_POST['isvalid_'.$id])){
					list($r_pegawai, $r_tglpresensi) = explode(':', $id);
					$record = array();
					$record['totlembur'] = CStr::cStrNull($_POST['totlembur_'.$id]);
					$record['issetujuatasan'] = CStr::cStrNull($_POST['issetujuatasan_'.$id]);
					$record['isvalid'] = CStr::cStrNull($_POST['isvalid_'.$id]);
					
					$r_subkey = $r_tglpresensi.'|'.$r_pegawai;
					
					list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record, $r_subkey, true, 'pe_presensidet','tglpresensi,idpegawai');
		
				}
			}
		}
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'tglpenugasan';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);

	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_month)) $a_filter[] = $p_model::getListFilter('bulan',str_pad($r_month, 2, "0", STR_PAD_LEFT));
	if(!empty($r_tahun)) $a_filter[] = $p_model::getListFilter('tahun',$r_tahun);
	if(!empty($r_validasi)) $a_filter[] = $p_model::getListFilter('validasi',$r_validasi);
	if(!empty($r_persetujuan)) $a_filter[] = $p_model::getListFilter('persetujuan',$r_persetujuan);
	
	$sql = $p_model::listQueryValLembur();
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();

	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
		
	//combo untuk show halaman
	$data = array('10'=>'10','15'=>'15','20'=>'20','25'=>'25','30'=>'30');			
	$l_record = UI::createSelect('row',$data,$r_row,'ControlStyle',true,'onchange="goLimit()"');
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
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td width="100" style="white-space:nowrap"><strong>Status Validasi </strong></td>
											<td><strong> : </strong><?= $l_validasi ?></td>		
										</tr>
										<tr>		
											<td style="white-space:nowrap"><strong>Persetujuan Atasan </strong></td>
											<td><strong> : </strong><?= $l_persetujuan ?></td>		
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
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="TDButton" onclick="goValidasi()" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;">
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
						<th>Jam Normal</th>
						<th>Hand Key</th>
						<th>Jam Lembur</th>
						<th>Total Lembur</th>
						<th>Total Pembulatan</th>
						<th>Persetujuan Atasan</th>
						<th width="50">Validasi</th>
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
						<?	}  ?>
						<td align="center"><?= $row['jamnormal']; ?></td>
						<td align="center"><?= $row['jamreal']; ?> <input type="hidden" name="valid[]" value="<?= $row['idpegawai'].':'.$row['tglpresensi']; ?>" /> </td>
						<td align="center"><?= $row['jamlembur']; ?></td>
						<td align="center"><?= floor($row['totallembur']/60); ?></td>
						<?	
							$totallembur = 0;
							$totallembur = (empty($row['totlembur']) ? (int)($row['totallembur']/60) : $row['totlembur']);
						?>
						<td align="center"><?= UI::createTextBox('totlembur_'.$row['idpegawai'].':'.$row['tglpresensi'],CStr::formatNumber($totallembur),'ControlStyle',5,5,$c_edit,'onkeydown="return onlyNumber(event,this,true)" style="text-align:right"'); ?>&nbsp;&nbsp;<em>Jam</em></td>
						<td align="center"><?= UI::createRadio('issetujuatasan_'.$row['idpegawai'].':'.$row['tglpresensi'],SDM::getValid(),$row['issetujuatasan'],$c_edit); ?></td>
						<td align="center" width="100px">
						<?		if($c_edit) { ?>
						<?= UI::createRadio('isvalid_'.$row['idpegawai'].':'.$row['tglpresensi'],SDM::getValid(),$row['isvalid'],$c_edit); ?>
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
							Record : <?= $l_record ?>
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

function goValidasi(){
	document.getElementById('act').value = "valid";
	goSubmit();
}
</script>
</body>
</html>
