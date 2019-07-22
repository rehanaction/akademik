<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_presensi = CStr::removeSpecial($_POST['tglpresensi']);
	if (empty($r_presensi)) $r_presensi = date("d-m-Y");
	
	$p_model = mPresensi;
	
	$n_day = date("N", strtotime(CStr::formatDate($r_presensi)));
	$a_hari = $p_model::hariAbsensi();
	$a_jenisabsensi = $p_model::allAbsensi($conn);
	
	// properti halaman
	$p_title = 'Rekap Potongan Presensi '.$a_hari[$n_day].', '.(CStr::formatDateInd(CStr::formatDate($r_presensi)));
	$p_tbwidth = 1150;
	$p_aktivitas = 'TIME';
	$p_key = 'tglpresensi,idpegawai';
	$p_dbtable = 'pe_presensidet';
	$p_detailpage = Route::getDetailPage();
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	
	$script = '&nbsp;&nbsp;<img src="images/cal.png" id="tglpresensi_trg" style="cursor:pointer;" title="Pilih Tanggal Presensi">
					<script type="text/javascript">
					Calendar.setup({
						inputField     :    "tglpresensi",
						ifFormat       :    "%d-%m-%Y",
						button         :    "tglpresensi_trg",
						align          :    "Br",
						singleClick    :    true
					});
				</script>';
	$button = ' <input type="button" name=bsubmit class="ControlStyle" value="Tampilkan" onClick="goSubmit()" />';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIP', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama', 'filter' => 'sdm.f_namalengkap(gelardepan,namatengah,namadepan,namabelakang,gelarbelakang)');
	$p_colnum = count($a_kolom)+12;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		if(count($_POST['idpeg'])>0){
			foreach($_POST['idpeg'] as $id){
				$record = array();
				$record['procpotkehadirantelat'] = $_POST['prockehadirantelat_'.$id];
				$record['procpotkehadiranpd'] = $_POST['prockehadiranpd_'.$id];
				$record['potkehadiran'] = $_POST['inputpotkehadiran_'.$id];
				$record['procpottransporttelat'] = $_POST['proctransporttelat_'.$id];
				$record['procpottransportpd'] = $_POST['proctransportpd_'.$id];
				$record['pottransport'] = $_POST['inputpottransport_'.$id];
				
				$a_key = CStr::formatDate($r_presensi).'|'.$id;
				$p_posterr = $p_model::updateRecord($conn,$record,$a_key,false,$p_dbtable,$p_key);
			}
		}
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'nik desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_presensi)) $a_filter[] = $p_model::getListFilter('tglpresensi',$r_presensi);	
	
	$sql = $p_model::listQueryMonitor();
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
		
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
		
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit Kerja', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Tgl. Presensi', 'combo' => UI::createTextBox('tglpresensi',$r_presensi,'ControlRead',10,10,$c_edit,'readonly').$script.$button);
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" style="width:<?= $p_tbwidth+50 ?>px;">
		<div class="SideItem" id="SideItem" style="width:<?= $p_tbwidth+15 ?>px;">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
					<br>
					<?php require_once('inc_listfilter.php'); ?>
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
							<div class="right">
								<div class="TDButton" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;" onClick="goSimpan()">
									<img src="images/disk.png" style="position:relative;left:-60px;top:-7px;">
									<span style="position:relative;left:15px">Simpan</span>
								</div>
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
						<th rowspan="3">NPP</th>
						<th rowspan="3">Nama</th>
						<th rowspan="3">Kehadiran Wajib</th>
						<th rowspan="3">Kehadiran Real</th>
						<th width="50" rowspan="3">Kode Absensi</th>
						<th width="80" rowspan="3">Prosentase Potongan Absensi (%)</th>
						<th width="120" colspan="2">Tarif Tunjangan</th>
						<th width="200" colspan="4">Prosentase Potongan (%)</th>
						<th width="120" colspan="2">Potongan (Rp)</th>
					</tr>
					<tr>
						<th rowspan="2">Kehadiran</th>
						<th rowspan="2">Transport</th>
						<th colspan="2">Kehadiran</th>
						<th colspan="2">Transport</th>
						<th rowspan="2">Kehadiran</th>
						<th rowspan="2">Transport</th>
					</tr>
					<tr>
						<th>Terlambat</th>
						<th>Pulang Awal</th>
						<th>Terlambat</th>
						<th>Pulang Awal</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'tglpresensi');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td>
							<?= $rowc[$j++] ?>
						</td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center">
							<?= (empty($row['sjamdatang']) ? 'OFF' : substr($row['sjamdatang'],0,2).':'.substr($row['sjamdatang'],2,2).' - '.substr($row['sjampulang'],0,2).':'.substr($row['sjampulang'],2,2)); ?></td>
						</td>
						<td align="center">
							<?= (empty($row['jamdatang']) ? '' : substr($row['jamdatang'],0,2).':'.substr($row['jamdatang'],2,2)).' - '.(empty($row['jampulang']) ? '' : substr($row['jampulang'],0,2).':'.substr($row['jampulang'],2,2)); ?>
						</td>
						<td align="center"><b style="color:#<?= $row['color'] ?>;"><?= $row['kodeabsensi']; ?></b></td>
						<td align="center"><?= $row['prosentasehadir']; ?></td>
						<td align="right">
							<?= CStr::formatNumber($row['tarifpotkehadiran']); ?>
						</td>
						<td align="right">
							<?= CStr::formatNumber($row['tarifpottransport']); ?>
						</td>
						<td align="center">
							<input type="hidden" name="idpeg[]" id="idpeg" value="<?= $row['idpegawai']?>">
							<input type="text" id="prockehadirantelat_<?= $row['idpegawai'];?>" name="prockehadirantelat_<?= $row['idpegawai'];?>" value="<?= $row['procpotkehadirantelat'];?>" class="ControlStyle" size="3" maxlength="3" onkeydown="return onlyNumber(event,this,false,true)" onkeyup="getPotKehadiran('<?= $row['idpegawai'];?>')">
						</td>
						<td align="center">
							<input type="text" id="prockehadiranpd_<?= $row['idpegawai'];?>" name="prockehadiranpd_<?= $row['idpegawai'];?>" value="<?= $row['procpotkehadiranpd'];?>" class="ControlStyle" size="3" maxlength="3" onkeydown="return onlyNumber(event,this,false,true)" onkeyup="getPotKehadiran('<?= $row['idpegawai'];?>')">
						</td>
						<td align="center">
							<input type="text" id="proctransporttelat_<?= $row['idpegawai'];?>" name="proctransporttelat_<?= $row['idpegawai'];?>" value="<?= $row['procpottransporttelat'];?>" class="ControlStyle" size="3" maxlength="3" onkeydown="return onlyNumber(event,this,false,true)" onkeyup="getPotTransport('<?= $row['idpegawai'];?>')">
						</td>
						<td align="center">
							<input type="text" id="proctransportpd_<?= $row['idpegawai'];?>" name="proctransportpd_<?= $row['idpegawai'];?>" value="<?= $row['procpottransportpd'];?>" class="ControlStyle" size="3" maxlength="3" onkeydown="return onlyNumber(event,this,false,true)" onkeyup="getPotTransport('<?= $row['idpegawai'];?>')">
						</td>
						<td align="right" >
							<input type="hidden" name="potkehadiran[]" id="inputpotkehadiran_<?= $row['idpegawai'];?>">
							<span id="potkehadiran_<?= $row['idpegawai'];?>"><?= CStr::formatNumber($row['potkehadiran'])?></span>
						</td>
						<td align="center">
							<input type="hidden" name="pottransport[]" id="inputpottransport_<?= $row['idpegawai'];?>">
							<span id="pottransport_<?= $row['idpegawai'];?>"><?= CStr::formatNumber($row['pottransport'])?></span>
						</td>
					</tr>
					<?	}
						}
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
			
			<br />
			<br/>
			<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
				<table cellspacing="0" cellpadding="0" align="center" width="<?= $p_tbwidth-12 ?>" >
					<tr>
						<td colspan="4"><strong>Keterangan Jenis Absensi :</strong></td>
					</tr>
					<? $i=0;
					foreach ($a_jenisabsensi as $absensi => $label){
						if($i == 0){
							echo"<tr>";
						}
						echo'<td><b style="color:#'.$label['color'].'">'.$absensi.'</b> = '.$label['absensi'].'</td>';
						$i++;
						if($i == 4)
						{
							$i = 0;
							echo"</tr>";
						}
					}
						?>
				</table>
			</div>
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

function goSimpan(){
	document.getElementById("act").value = "save";
	goSubmit();	
}


function getPotKehadiran(idpegawai){
	var posted = "f=gpotkehadiran&q[]="+$("#prockehadirantelat_"+idpegawai).val()+"&q[]="+$("#prockehadiranpd_"+idpegawai).val()+"&q[]="+$("#tglpresensi").val()+"&q[]="+idpegawai;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#potkehadiran_"+idpegawai).text(text);
		$("#inputpotkehadiran_"+idpegawai).val(text);
	});
}

function getPotTransport(idpegawai){
	var posted = "f=gpottransport&q[]="+$("#proctransporttelat_"+idpegawai).val()+"&q[]="+$("#proctransportpd_"+idpegawai).val()+"&q[]="+$("#tglpresensi").val()+"&q[]="+idpegawai;
	$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
		$("#pottransport_"+idpegawai).text(text);
		$("#inputpottransport_"+idpegawai).val(text);
	});
}

</script>
</body>
</html>
