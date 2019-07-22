<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('ta'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_tgl = CStr::removeSpecial($_REQUEST['tanggal']);
	if(empty($r_tgl))
		$r_tgl = date('d-m-Y');
	
	$r_ftgl = CStr::formatDate($r_tgl);
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false,true);
	
	// properti halaman
	$p_title = 'Jadwal Sidang Skripsi';
	$p_tbwidth = 900;
	$p_aktivitas = 'JADWAL';
	$p_colnum = 6;
	
	$p_model = mTA;
	
	$r_act = $_POST['act'];
	if($r_act == 'downxls') {
		$a_header = array('TGLUJIAN','WAKTUMULAI','WAKTUSELESAI','KODERUANG','NIM','PENGUJI1','PENGUJI2','SEKRETARIS','KETUAMAJELIS');
		$a_text = array('NIM' => true, 'PENGUJI1' => true, 'PENGUJI2' => true, 'SEKRETARIS' => true, 'KETUAMAJELIS' => true);
		
		// data sidang
		$a_huruf = CStr::arrayHuruf();
		$a_data = $p_model::getListSidangAkhir($conn,$r_unit,$r_ftgl);
		
		$a_dataxls = array();
		foreach($a_data as $t_data)
			$a_dataxls[] = $t_data;
		
		// menghilangkan segala echo
		ob_clean();
		
		header("Content-Type: application/msexcel");
		header('Content-Disposition: attachment; filename="template_jadwal_sidang.xls"');
		
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
		
		$ok = true;
		for($r=2;$r<=$numrow;$r++) {
			$data = $cells[$r];
			
			$record = array();
			foreach($cells[1] as $k => $v) {
				$v = strtolower($v);
				$record[$v] = CStr::cStrNull($data[$k]);
			}
			
			$p_posterr = $p_model::saveSidangAkhir($conn,$record);
			if($p_posterr) {
				$ok = false;
				break;
			}
		}
		
		$p_postmsg = 'Impor data dari format excel '.($ok ? 'berhasil' : 'gagal');
		
		$conn->CommitTrans($ok);
	}
	
	// mendapatkan data
	$a_data = $p_model::getListSidangAkhir($conn,$r_unit,$r_ftgl);
	 
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
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
				<?	if($c_edit) { ?>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="2">Impor Data dari Format Excel</td>
						<?	if(!empty($a_infokelas['nilaimasuk'])) { ?>
						<td align="center">Versi Cetak</td>
						<?	} ?>
					</tr>
					<tr class="NoHover NoGrid">		
						<td width="65"> &nbsp; <strong>Upload </strong></td>
						<td>
							<strong> : </strong> <input type="file" name="xls" id="xls" size="30" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpXLS()"> &nbsp; &nbsp;
							<u class="ULink" onclick="goDownXLS()">Download Template Excel...</u>
						</td>
					</tr>
				</table>
				</center>
				<br>
				<?	} ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td width="60"><strong>Unit</strong></td>
											<td><strong> : </strong> <?= $l_unit ?></td>
										</tr>
										<tr>		
											<td><strong>Tanggal</strong></td>
											<td>
												<strong> : </strong>
												<?= UI::createTextBox('tanggal',$r_tgl,'ControlStyle',10,10) ?>
												<img src="images/cal.png" id="tanggal_trg" style="cursor:pointer;" title="Pilih Tanggal">
												<script type="text/javascript">
												Calendar.setup({
													inputField     :    "tanggal",
													ifFormat       :    "%d-%m-%Y",
													button         :    "tanggal_trg",
													align          :    "Br",
													singleClick    :    true
												});
												</script> &nbsp;
												<input type="button" value="Lihat Jadwal" onclick="goSubmit()">
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<?	if(isset($p_posterr)) { ?>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				<?	} else { ?>
				<div style="width:<?= $p_tbwidth ?>px">
					<strong><?= $p_postmsg ?></strong>
				</div>
				<?	} ?>
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
						<th>Jam Mulai</th>
						<th>Jam Selesai</th>
						<th>Ruang</th>
						<th>Mahasiswa</th>
						<th>Judul Skripsi</th>
						<th>Penguji 1</th>
						<th>Penguji 2</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= CStr::formatJam($row['waktumulai']) ?></td>
						<td align="center"><?= CStr::formatJam($row['waktuselesai']) ?></td>
						<td align="center"><?= $row['koderuang'] ?></td>
						<td>
							<?= $row['nim'] ?><hr>
							<?= $row['nama'] ?>
						</td>
						<td width="330"><?= $row['judulta'] ?></td>
						<td>
							<?= $row['nippenguji1'] ?><hr>
							<?= $row['namapenguji1'] ?>
						</td>
						<td>
							<?= $row['nippenguji2'] ?><hr>
							<?= $row['namapenguji2'] ?>
						</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goDownXLS() {
	document.getElementById("act").value = "downxls";
	goSubmit();
	
	// reset karena tidak reload
	document.getElementById("act").value = "";
}

function goUpXLS() {
	var upload = confirm("Apakah anda yakin akan mengupdate data dari format excel?");
	if(upload) {
		document.getElementById("act").value = "upxls";
		goSubmit();
	}
}

</script>
</body>
</html>
