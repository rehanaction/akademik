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
	require_once(Route::getModelPath('detailkelas'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_basis = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	
	// combo
	$l_basis = uCombo::sistemkuliah($conn,$r_basis,'sistemkuliah','onchange="goSubmit()"',true);
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_semester = uCombo::semester($r_semester,false,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	
	$r_ctahun = $r_tahun-1;
	$l_csemester = uCombo::semester($r_semester,false,'csemester','',false);
	$l_ctahun = uCombo::tahun($r_ctahun,true,'ctahun','',false);
	
	// tambahan
	$r_periode = $r_tahun.$r_semester;
	
	// properti halaman
	$p_title = 'Data Kelas Perkuliahan';
	$p_tbwidth = 950;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	$p_printpage='rep_kelasnew';
	$p_model = mKelas;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'thnkurikulum', 'label' => 'Kur.');
	$a_kolom[] = array('kolom' => 'kodemk', 'label' => 'Kode');
	$a_kolom[] = array('kolom' => 'namamk', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'kelasmk', 'label' => 'Kelas');
	$a_kolom[] = array('kolom' => 'sks', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'semmk', 'label' => 'Sem.'); 
	$a_kolom[] = array('kolom' => 'jadwal', 'label' => 'Jadwal');
	$a_kolom[] = array('kolom' => 'namapengajar', 'label' => 'Pengajar');
	$a_kolom[] = array('kolom' => 'namakoordinator', 'label' => 'Koordinator');
	$a_kolom[] = array('kolom' => 'koderuang', 'label' => 'Ruang');
	$a_kolom[] = array('kolom' => 'jumlahpeserta', 'label' => 'Peserta');
	$a_kolom[] = array('kolom' => 'dayatampung', 'label' => 'Kap.');
	
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		$ok=true;
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg)=mDetailKelas:: deleteBlock($conn,$r_key.'|K|1');
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		if($p_posterr)
				$ok=false;
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'copy' and $c_edit) {
		$r_semestercopy = CStr::removeSpecial($_POST['csemester']);
		$r_tahuncopy = CStr::removeSpecial($_POST['ctahun']);
		$r_periodecopy = $r_tahuncopy.$r_semestercopy;
		
		list($p_posterr,$p_postmsg) = $p_model::copy($conn,$r_unit,$r_periode,$r_periodecopy);
	}
	else if($r_act == 'downxls') {
		$a_header = array('PERIODE','KODEUNIT','KODEMK','THNKURIKULUM','KELASMK','DAYATAMPUNG','HARI','JAMMULAI','JAMSELESAI','KODERUANG','HARI2','JAMMULAI2','JAMSELESAI2','KODERUANG2','NIPDOSEN','NIPDOSEN2','SISTEMKULIAH');
		$a_text = array('KODEMK' => true, 'KELASMK' => true, 'KODERUANG' => true, 'KODERUANG2' => true, 'NIPDOSEN' => true, 'NIPDOSEN2' => true, 'SISTEMKULIAH' => true);
		
		$a_dataxls = mKelas::getListXLS($conn,$r_periode,$r_unit);		
		$a_huruf = CStr::arrayHuruf();
		
		// menghilangkan segala echo
		ob_clean();
		
		header("Content-Type: application/msexcel");
		header('Content-Disposition: attachment; filename="template_kelas.xls"');
		
		// pakai phpexcel
		require_once($conf['includes_dir'].'phpexcel/PHPExcel.php');
		
		$xls = new PHPExcel();
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		
		// header
		$r = 1;
		foreach($a_header as $i => $t_header)
			$sheet->setCellValue($a_huruf[$i].$r,$t_header);
		
		// data
		foreach($a_dataxls as $row) {
			$r++;
			
			// memecah nip
			list($row['nipdosen'],$row['nipdosen2']) = explode(',',$row['nip']);
			
			$i = -1;
			foreach($a_header as $k => $v) {
				$i++;
				$t_data = $row[strtolower($v)];
				
				if($a_text[$v])
					$sheet->getCell($a_huruf[$i].$r)->setValueExplicit($t_data,PHPExcel_Cell_DataType::TYPE_STRING);
				else
					$sheet->setCellValue($a_huruf[$i].$r,$t_data);
			}
		}
		
		// paskan ukuran
		$n = count($a_header);
		for($i=0;$i<$n;$i++)
			$sheet->getColumnDimension($a_huruf[$i])->setAutoSize(true);
		
		$xlsfile = PHPExcel_IOFactory::createWriter($xls,'Excel5');
		$xlsfile->save('php://output');
		
		exit;
	}
	else if($r_act == 'upxls' and $c_edit) {
		$r_file = $_FILES['xls']['tmp_name'];
		
		// pakai excel reader
		require_once($conf['includes_dir'].'phpexcel/excel_reader2.php');
		$xls = new Spreadsheet_Excel_Reader($r_file);
		
		$cells = $xls->sheets[0]['cells'];
		$numrow = count($cells);
		
		// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
		if(empty($numrow)) {
			if(($handle = fopen($r_file, 'r')) !== false) {
				while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
					$numrow++;
					foreach($data as $k => $v)
						$cells[$numrow][$k+1] = $v;
				}
				fclose($handle);
			}
		}
		
		// baris pertama adalah header
		$conn->BeginTrans();
		
		$a_hari = Date::arrayDay();
		
		$ok = true;
		for($r=2;$r<=$numrow;$r++) {
			$data = $cells[$r];
			
			$record = array();
			foreach($cells[1] as $k => $v) {
				$v = strtolower($v);
				$record[$v] = CStr::cStrNull($data[$k]);
			}
			
			$record['nohari'] = CStr::cStrNull(array_search($record['hari'],$a_hari));
			$record['nohari2'] = CStr::cStrNull(array_search($record['hari2'],$a_hari));
			
			list($p_posterr,$p_postmsg) = mKelas::saveRecord($conn,$record,mKelas::getKeyRow($record),true);
			
			// masukkan data pengajar
			$t_key = mKelas::getKeyRow($record);
			
			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = mKelas::deleteMengajar($conn,$t_key);
			
			if(!$p_posterr and $record['nipdosen'] != 'null') {
				$recajar = array();
				$recajar['nipdosen'] = $record['nipdosen'];
				
				list($p_posterr,$p_postmsg) = mKelas::insertRecordMengajar($conn,$recajar,$t_key);
			}
			
			if(!$p_posterr and $record['nipdosen2'] != 'null') {
				$recajar = array();
				$recajar['nipdosen'] = $record['nipdosen2'];
				
				list($p_posterr,$p_postmsg) = mKelas::insertRecordMengajar($conn,$recajar,$t_key);
			}
			
			if($p_posterr) {
				$ok = false;
				break;
			}
		}
		
		if($ok)
			$p_postmsg = 'Impor data dari format excel berhasil';
		
		$conn->CommitTrans($ok);
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
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periode',$r_periode);
	if(!empty($r_basis)) $a_filter[] = $p_model::getListFilter('sistemkuliah',$r_basis);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
        
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi Pengelola', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
	$a_filtercombo[] = array('label' => 'Basis', 'combo' => $l_basis);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<style>
	.judullaporan{ display:none;font-size:14pt;margin-bottom:10px}	
	@media print{
		.WorkHeader, .sidemenu, .tombol, .filterTable, .inner, .FootBG, .infotime, .action, .pagination{ display:none;}	
		.SideItem{border:none;background:#fff}
		.judullaporan{display:block;}
	}
	</style>
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
				<?php require_once('inc_listfilter.php'); ?>
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
							<?	if(!empty($r_page) or $c_insert) { ?>
							<div class="right">
								<?	if(!empty($r_page)) { ?>
								<?php require_once('inc_listnavtop.php'); ?>
								<?	}
									if($c_insert) { ?>
								<div class="addButton" style="float:left;margin-left:10px" onClick="goNew()">+</div>
								<?	} ?>
							</div>
							<?	}
								if($c_edit) { ?>
							<div class="right">
								<div class="addButton" style="padding:0 0 3px 7px;margin-right:15px" title="Tampilkan/sembunyikan impor data" onClick="toggleImpor()">
									<img src="images/outbox.png" style="float:none;padding:0px 0px 2px;margin-right:6px">
								</div>
							</div>
							<?	} ?>
							<div class="right">
								<div class="addButton"  style="float:left;padding-top:0" title="Cetak Data Kelas" onclick="goPrint()">
									<img width="22" src="images/print.png" style="margin:0 0 3px 0;padding:0">
								</div>
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<center class="judullaporan">
					Jadwal Perkuliahan<br>
					<?=Akademik::getNamaPeriode($r_periode)?>
				</center>
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
						<?	}?>
						<th width="30">Gen.</th>
						<?	if($c_edit) { ?>
						<th width="30" class="action">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30" class="action">Hapus</th>
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
							
							// cek mengulang
							if(!empty($row['mengulang']))
								$rowstyle = 'YellowBG';
							else if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							
							// cek peserta
							if($rowc[10] > $rowc[11])
								$t_bgtd = '#F00';
							else if($rowc[10] == $rowc[11])
								$t_bgtd = '#FF0';
							else
								$t_bgtd = '';
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><font size=1><?= $rowc[$j++] ?></font></td>
						<td><font size=1><?= $rowc[$j++] ?></font></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td align="center"><?= $rowc[$j++] ?></td>
						<td><font size=1><?= $rowc[$j++] ?></font></td>
						<td><font size=1><?= $rowc[$j++] ?></font></td>
						<td><font size=1><?= $rowc[$j++] ?></font></td>
						<td><?= $rowc[$j++] ?></td>
						<td align=right <?= empty($t_bgtd) ? '' : ' bgcolor="'.$t_bgtd.'"' ?>><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td><? if($row['tgljadwal1']!=''||$row['tgljadwal2']!=''||$row['tgljadwal3']!=''||$row['tgljadwal4']!='') echo '<center><img src="images/check.png"></center>';?></td>
						<?	if($c_edit) { ?>
						<td align="center" class="action"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center" class="action"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
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
						<td colspan="<?= $p_colnum+1 ?>" align="right" class="FootBG">
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
					<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td width="50"><strong>Baris</strong></td>
							<td width="40"><div class="YellowBG" style="border:1px solid #CCC;width:30px">&nbsp;</div></td>
							<td width="150"> : Kelas mengulang</td>
							<td width="110"><strong>Kolom Peserta</strong></td>
							<td width="40"><div style="background-color:#FF0;border:1px solid #CCC;width:30px">&nbsp;</div></td>
							<td width="170"> : Kapasitas kelas terpenuhi</td>
							<td width="40"><div style="background-color:#F00;border:1px solid #CCC;width:30px">&nbsp;</div></td>
							<td> : Jumlah peserta melebihi kapasitas</td>
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

function toggleImpor() {
	$("#div_impor").toggle();
}

function goPrint() {
	$('#act').val('');
	goOpen('<?= $p_printpage ?>');
}
</script>
</body>
</html>
