<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	//ini_set('display_errors', 1);
	//error_reporting(E_ALL);
	// include
//	$conn->debug=-true;
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('sistemkuliah'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_tglkuliah 	= Modul::setRequest($_POST['tglkuliah'],'TGLKULIAH');
	$r_tglpindah 	= Modul::setRequest($_POST['tglpindah'],'TGLPINDAH');
	$r_kodemkajx 	= Modul::setRequest($_POST['kodemkajx'],'KODEMKAJX');
	
	if(!empty($r_tglkuliah))
		$r_tglkuliah=date('Y-m-d',strtotime($r_tglkuliah));
	//else
	//	$r_tglkuliah=date('Y-m-d');
	$r_kodemk=Modul::setRequest($_POST['kodemkajx'],'MATKUL');
	
	// combo
	
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$a_input= array('kolom' => 'tglkuliah', 'label' => 'Tanggal Perencanaan', 'type' => 'D','add' => 'onchange="goSubmit()"');
	$i_tanggal=uForm::getInput($a_input,$r_tglkuliah);
	$a_input2= array('kolom' => 'tglpindah', 'label' => 'Pindah ke Tanggal (*)', 'type' => 'D');
	$i_tanggal2=uForm::getInput($a_input2,'');
	$l_kodemk=UI::createTextBox('matkul','','ControlStyle',0,40,true, 'onchange="goSubmitCari()"', 'Cari Mata Kuliah');	
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Monitoring Kegiatan Perkuliahan';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	$p_printpage='rep_perkuliahan';
	
	$p_model = mKuliah;
	$status=array('0'=>'Tatap Muka','-1'=>'Online');
	$statusKuliah=$p_model::statusKuliah();
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis');
	$a_kolom[] = array('kolom' => 'tglkuliah', 'label' => 'Tgl Kuliah');
	$a_kolom[] = array('kolom' => 'waktumulai', 'label' => 'Waktu');
	
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Kode MK');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama Matakuliah');
	$a_kolom[] = array('kolom' => 'kr.sks', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'kelasmk', 'label' => 'Sesi'); 
	$a_kolom[] = array('kolom' => 'sistemkuliah', 'label' => 'Basis','option'=>mSistemkuliah::getArray($conn)); 
	$a_kolom[] = array('kolom' => 'kl.jumlahpeserta', 'label' => 'Peserta'); 
	$a_kolom[] = array('kolom' => 'p.namadepan', 'label' => 'Dosen');
	$a_kolom[] = array('kolom' => 'k.koderuang', 'label' => 'Ruang');
	$a_kolom[] = array('kolom' => 'isonline', 'label' => 'Pertemuan');
	$a_kolom[] = array('kolom' => 'statusperkuliahan', 'label' => 'Status');
	$a_kolom[] = array('kolom' => 'tglkuliahrealisasi', 'label' => 'Tgl Realisasi');
	$a_kolom[] = array('kolom' => 'waktumulairealisasi', 'label' => 'Waktu');
	$a_kolom[] = array('kolom' => 'isopen', 'label' => 'Open?');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	
	if($r_act == 'refresh')
		Modul::refreshList();
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}else if($r_act == 'open' and $c_edit) {
		$record['isopen'] = '-1';
		$r_key=$r_tglkuliah.'|'.$r_tahun.$r_semester;
		$kolom='tglkuliahrealisasi, periode';
		if($r_kodemkajx!=''){
			$r_key.='|'.$r_kodemkajx;
			$kolom.=', kodemk';
		}
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$kolom,true);
		
	}else if($r_act == 'close' and $c_edit) {
		$record['isopen'] = '0';
		$r_key=$r_tglkuliah.'|'.$r_tahun.$r_semester;
		$kolom='tglkuliahrealisasi, periode';
		
		if($r_kodemkajx!=''){
			$r_key.='|'.$r_kodemkajx;
			$kolom.=', kodemk';
		}
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true,$kolom,true);
	
	}else if($r_act == 'pindah' and $c_edit) {
		$record['tglkuliahrealisasi'] = date('Y-m-d',strtotime($r_tglpindah));
		$record['tglkuliahsemula'] = $r_tglkuliah;
		$record['tglkuliah'] = date('Y-m-d',strtotime($r_tglpindah));
		
		list($p_posterr,$p_postmsg) = $p_model::pindahTanggalKuliah($conn,true,$record, $r_tahun.$r_semester, $r_tglkuliah, $r_kodemkajx);
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_tglkuliah)) $a_filter[] = $p_model::getListFilter('tglkuliah',$r_tglkuliah);
	if(!empty($r_kodemk)) $a_filter[] = $p_model::getListFilter('kodemk',$r_kodemk);
	
	$a_data = $p_model::getListPerkuliahan($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	$a_jenispertemuan=$p_model::jenisKuliah($conn);
	
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi Pengelola', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	$a_filtercombo[] = array('label' => 'Tanggal Perencanaan', 'combo' => $i_tanggal);
	$a_filtercombo[] = array('label' => 'Pindah ke Tanggal (*)', 'combo' => $i_tanggal2);
	$a_filtercombo[] = array('label' => 'Matakuliah','combo' => $l_kodemk);
	
	
	$forcesearch=true;
	
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	 <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?	if($p_headermhs) { ?>
				<center>
				<?php require_once('inc_headermhs.php') ?>
				</center>
				<br>
				<?	} ?>
				<!--List filter custom-->
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
										<tr>
										<td></td>
										<td>
											<input type="button" value="Open" class="ControlStyle" onClick="goOpenKul()">
											<input type="button" value="Close" class="ControlStyle" onClick="goClose()">
											<input type="button" value="Pindah" class="ControlStyle" onClick="goChangeKul()">
										</td>
										</tr>
									</table>
									(*) Diisi untuk mengganti tgl perkuliahan
								</td>
								<?	}
									/**********************/
									/* COMBO FILTER KOLOM */
									/**********************/
									
									if(!empty($r_page) or $forcesearch) {
										$r_filterstr = Page::getFilterAll();
								?>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>
											<td width="40" style="white-space:nowrap"><strong>Cari :</strong></td>
											<td style="display:none"><?= uCombo::listColumn($a_kolom) ?></td>
											<? /* <td width="210"><input name="tfilter" id="tfilter" class="ControlStyle" size="25" onkeydown="etrFilterCombo(event)" type="text"></td>
											<td width="40"><input type="button" value="Filter" class="ControlStyle" onClick="goFilterCombo()"></td> */ ?>
											<td width="260"><input name="tfilter" id="tfilter" class="ControlStyle" size="40" onkeydown="etrFilterAll(event)" type="text" value="<?= $r_filterstr ?>"></td>
											<td><input type="button" value="Cari" class="ControlStyle" onClick="goFilterAll()"></td>
											<? /* <td><input type="button" value="Refresh" class="ControlStyle" onClick="goRefresh()"></td> */ ?>
										</tr>
									</table>
									<?	/********************/
										/* INFORMASI FILTER */
										/********************/
										
										/* if(!empty($a_datafilter)) { ?>
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
									<?	} */ ?>
								</td>
							<?	} ?>
							</tr>
							
						</table>
					</div>
					<br>
					
					<?php if(!empty($a_salin)) { ?>
						<div class="filterTable" style="width:<?= $p_tbwidth ?>px;  margin-top:10px">
							<table width="<?= $p_tbwidth-100 ?>" cellpadding="5" cellspacing="0" style="text-align:center">
								<tr>
									<td colspan="2" align="center;"><strong><?=$a_salin['title']?></strong></td>
								</tr> 
								<tr>
									<td valign="top" width="50%"> <strong><?=$a_salin['label']?></strong>&nbsp; &nbsp; &nbsp;  <?=$a_salin['tujuan']?></td> 
									<td> <input type="button" value="Salin" onclick="goSalin()"> </td> 
								</tr>
							</table>
						</div>
					<?php } ?>
					<br>
					<?	if(!empty($r_page)) { ?>
					<div class="Break"></div>
					<table width="<?= $p_tbwidth ?>">
						<tr>
							<td>Menampilkan <?= $r_page > 1 ? 'halaman '.CStr::formatNumber($r_page).' dari ' : '' ?><?= CStr::formatNumber($p_rownum) ?> data <?= empty($r_filterstr) ? '' : 'hasil pencarian "'.$r_filterstr.'" ' ?>(<?= CStr::formatNumber($p_time,4) ?> detik)</td>
						</tr>
					</table>
					<?	} ?>
				</center>
				<br>
				<?php
					}
				?>

				
				<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
					<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
				</div>
				<input type="hidden" name="kodemkajx" id="kodemkajx">	
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
					if($c_edit) { ?>
				<center id="div_impor" style="display:none">
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="2">Impor Data dari Format Excel</td>
						<td align="center">Salin Data Periode Lain</td>
					</tr>
					<tr class="NoHover NoGrid">		
						<td width="55"> &nbsp; <strong>Upload </strong></td>
						<td>
							<strong> : </strong> <input type="file" name="xls" id="xls" size="50" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpXLS()"> &nbsp; &nbsp; &nbsp;
							<u class="ULink" onclick="goDownXLS()">Download Template Excel...</u>
						</td>
						<td>
							<strong>Periode : </strong> <?= $l_csemester ?> <?= $l_ctahun ?>
							<input type="button" value="Salin" onclick="goSalin()">
						</td>
					</tr>
				</table>
				<br>
				</center>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<div class="right">
								<img title="Cetak Jurnal" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
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
						<th colspan="13">Perencanaan</th>
						<th colspan="2">Target Realisasi</th>
						<th></th>
					</tr>
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
						foreach($a_data as $row) {
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
							// cek makeup
							if(($row['tglkuliah']!=$row['tglkuliahrealisasi']) or ($row['waktumulai']!=$row['waktumulairealisasi']) or ($row['waktuselesai']!=$row['waktuselesairealisasi']))
								$rowstyle = 'YellowBG';
							else if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><b><?= $a_jenispertemuan[$row['jeniskuliah']] ?></b></td>
						<td><?= Date::indoDate($row['tglkuliah']) ?></td>
						<td><?= $row['waktuperencanaan'] ?></td>
						<td><?= $row['kodemk'] ?></td>
						<td><?= $row['namamk'] ?></td>
						<td><?= $row['sks'] ?></td>
						<td><?= $row['kelasmk'] ?></td>
						<td><?= $row['sistemkuliah'] ?></td>
						<td><?= $row['jumlahpeserta'] ?></td>
						<td><?= ($row['namadosenperencanaan']!='')?$row['namadosenperencanaan']:$row['namadosenrealisasi'] ?></td>
						<td><?= $row['koderuang'] ?> / <?= $row['dayatampung'] ?></td>
						<td><?= $status[$row['isonline']] ?></td>
						<td><?= $statusKuliah[$row['statusperkuliahan']] ?></td>
						<td><?= Date::indoDate($row['tglkuliahrealisasi']) ?></td>
						<td><?= $row['wakturealisasi'] ?></td>
						<td align="center">
						<?php if($row['isopen']=='-1') echo '<img src="images/check.png">';?>
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
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				<br><br>
				<center>
				<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
					<table width="<?= $p_tbwidth-700 ?>" cellpadding="0" cellspacing="0" align="center" >
						<tr>
							
							
							<td ><div class="YellowBG" style="width:40px">&nbsp;</div></td>
							<td ><b> : Make up Class</b></td>
							
						</tr>
					</table>
				</div>
				</center>
				
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
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	$("#matkul").xautox({strpost: "f=rep_acmatkulview&p=<?=$r_periode?>|<?=$r_unit?>", targetid: "kodemkajx"});

	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	/* $("#xls").change(function() {
		goUpXLS();
	}); */
});

function goChooseXLS() {
	$("#xls").click();
}

function goDownXLS() {
	document.getElementById("act").value = "downxls";
	goSubmit();
}

function goUpXLS() {
	var upload = confirm("Apakah anda yakin akan mengupdate data dari format excel?");
	if(upload) {
		document.getElementById("act").value = "upxls";
		goSubmit();
	}
}

function goSalin() {
	var fsemester = $("#csemester option:selected").text();
	var ftahun = $("#ctahun option:selected").text();
	
	var salin = confirm("Apakah anda yakin akan menyalin data "+fsemester+" "+ftahun+"?");
	if(salin) {
		document.getElementById("act").value = "copy";
		goSubmit();
	}
}
function goSubmitCari(){
//alert($('#kodemkajx').val());
//goSubmit();
}
function toggleImpor() {
	$("#div_impor").toggle();
}
function goPrint() {
	$('#act').val('');
	goOpen('<?= $p_printpage ?>');
}
function goOpenKul(){
	document.getElementById("act").value = "open";
	tanggal=$('#tglkuliah').val();
	if(tanggal==''){
		alert('tanggal tidak boleh kosong');
	}else{
		var konfirm = confirm("Apakah anda yakin akan membuka perkuliahan tanggal "+tanggal);
		if(konfirm) {
			goSubmit();
		}
	}
}
function goChangeKul(){
	document.getElementById("act").value = "pindah";
	tanggal=$('#tglkuliah').val();
	tanggalpindah=$('#tglpindah').val();
	
	if(tanggal==''||tanggalpindah==''){
		alert('tanggal tidak boleh kosong');
	}else{
		var konfirm = confirm("Apakah anda yakin akan memindah perkuliahan tanggal "+tanggal+" ke tanggal "+tanggalpindah);
		if(konfirm) {
			goSubmit();
		}
	}
}
function goClose(){
	document.getElementById("act").value = "close";
	tanggal=$('#tglkuliah').val();
	if(tanggal==''){
		alert('tanggal tidak boleh kosong');
	}else{
		var konfirm = confirm("Apakah anda yakin akan menutup perkuliahan tanggal "+tanggal);
		if(konfirm) {
			goSubmit();
		}
	}
}
</script>
</body>
</html>
