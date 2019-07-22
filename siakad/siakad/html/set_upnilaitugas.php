<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	$c_lock = $c_edit;
	$c_viewup = $c_edit;
	$c_upload = Akademik::isMhs();
	$c_viewownup = $c_upload;
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('tugas'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mTugas;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if($c_upload)
		$r_nim = Modul::getUserName();
	
	// properti halaman
	$p_title = 'Data Tugas';
	$p_tbwidth = 640;
	$p_aktivitas = 'KULIAH';
	$p_listpage = 'list_tugaskelas';
	$p_toppage = 'list_tugas';
	$p_uptype = 'tugaskumpul';
	
	// cek request
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	// cek data
	$a_cek = $p_model::getData($conn,$r_key);
	
	$r_kelas = mKelas::getKeyRow($a_cek);
	if($a_cek['locktugas'] == 't') {
		$c_edit = false;
		$c_upload = false;
	}
	
	// cek kelas
	if(Akademik::isMhs()) {
		require_once(Route::getModelPath('krs'));
		
		if(!mKRS::isAmbil($conn,$r_kelas))
			Route::navigate($p_toppage);
	}
	else if(Akademik::isDosen()) {
		require_once(Route::getModelPath('mengajar'));
		
		if(!mMengajar::isAjar($conn,$r_kelas))
			Route::navigate($p_toppage);
	}
	
	// struktur view
	$a_infokelas = mKelas::getDataSingkat($conn,$r_kelas,false);
	$p_kelas = $a_infokelas['kodemk'].' - '.$a_infokelas['namamk'].' ('.$a_infokelas['kelasmk'].')';
	
	$a_input = array();
	$a_input[] = array('kolom' => 'kelas', 'label' => 'Mata Kuliah', 'default' => $p_kelas, 'readonly' => true);
	$a_input[] = array('kolom' => 'waktuposting', 'label' => 'Waktu Posting', 'type' => 'DT', 'readonly' => true);
	$a_input[] = array('kolom' => 'judultugas', 'label' => 'Judul', 'maxlength' => 255, 'size' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'batasawal', 'label' => 'Batas Awal', 'type' => 'D');
	$a_input[] = array('kolom' => 'batasakhir', 'label' => 'Batas Akhir', 'type' => 'D');
	$a_input[] = array('kolom' => 'isi', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 4, 'cols' => 40);
	$a_input[] = array('kolom' => 'filetugas', 'label' => 'File Referensi', 'type' => 'U', 'uptype' => 'tugas', 'size' => 40);
	$a_input[] = array('kolom' => 'locktugas', 'label' => 'Kunci Tugas', 'type' => 'C', 'option' => array('t' => ''));
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		$ok = true;
		foreach($_POST['nim'] as $t_idx => $t_nim) {
			$t_nim = CStr::removeSpecial($t_nim);
			
			$record = array();
			$record['nilaitugas'] = CStr::cStrNull(CStr::cStrDec($_POST['nilai'][$t_idx]));
			
			list($p_posterr,$p_postmsg) = $p_model::saveRecordPengumpulan($conn,$record,$r_key,$t_nim,$t_key,true);
			if($p_posterr) {
				$ok = false;
				break;
			}
		}
		
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'lock' and $c_lock) {
		$record = array();
		$record['locktugas'] = empty($_POST['lock']) ? 'f' : 't';
		$record['t_updateact'] = 'lock';
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) {
			if($record['locktugas'] == 't') {
				$c_edit = false;
				$c_upload = false;
			}
			else {
				$c_edit = $a_auth['canupdate'];
				$c_upload = Akademik::isMhs();
			}
		}
	}
	else if($r_act == 'upload' and $c_upload) {
		$r_isitugas = CStr::cStrNull($_POST['isitugas']);
		
		list($p_posterr,$p_postmsg) = $p_model::uploadTugas($conn,$r_key,$r_nim,$_FILES['filesubmit'],$r_isitugas);
	}else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'file'.'tugas');
	
	// mendapatkan data
	$row = $p_model::getDataView($conn,$a_input,$r_key);
	$a_peserta = mKelas::getDataPeserta($conn,$r_kelas);
	$a_kumpul = $p_model::getListSubmit($conn,$r_key);
	
	// properti untuk upload
	if($c_upload) {
		$a_dataup = array('kolom' => 'filesubmit', 'label' => 'File Tugas', 'type' => 'U', 'uptype' => $p_uptype, 'size' => 40);
		$a_dataket = array('kolom' => 'isitugas', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 4, 'cols' => 40);
	}
	
	$p_subtitle = $a_infokelas['kodemk'].' - '.$a_infokelas['namamk'].' ('.$a_infokelas['kelasmk'].')';
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
					<div class="ForumCrumbsSmall" align="left" style="width:<?= $p_tbwidth ?>px">
						Forum : <span class="ULink" onclick="goTop()">Tugas</span>
						&raquo; <span class="ULink" onclick="goUp()"><?= $p_subtitle ?></span>
						&raquo; <span class="ULink" onclick="goReload()"><?= $row['judultugas']['value'] ?></span>
					</div>
				</center>
				<?	if(!empty($p_postmsg)) { ?>
				<br>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<br>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<?	foreach($row as $t_col => $t_row) { ?>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">
								<?= $t_row['label'] ?>
							</td>
							<td class="RightColumnBG">
							<?	if($t_col == 'locktugas' and $c_lock) { ?>
								<input type="checkbox" name="lock" value="1"<?= $t_row['realvalue'] == 't' ? ' checked' : '' ?> onclick="goLock()">
								&nbsp; <em>Setelah dikunci, mahasiswa tidak bisa upload dan data nilai tidak bisa diganti</em>
							<?	} else { ?>
								<?= $t_row['value'] ?>
							<?	} ?>
							</td>
						</tr>
					<?	} ?>
					</table>
					</div>
					<?	if($c_viewup) { ?>
					<br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/ABSENSI.png" onerror="loadDefaultActImg(this)"> <h1>Pengumpulan Tugas</h1>
							</div>
						</div>
					</header>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
						<tr>
							<th width="80">NIM</th>
							<th>Nama Mahasiswa</th>
							<th width="150">Waktu Posting</th>
							<? if($c_edit) { ?>
							<th width="50">Nilai</th>
							<? } ?>
							<th width="40">Unduh</th>
						</tr>
					<?	$i = 0;
						foreach($a_peserta as $t_row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							
							$t_rowk = $a_kumpul[$t_row['nim']];
							
							// cek batas waktu
							if(empty($t_rowk['waktuposting']))
								$rowstyle = 'RedBG';
							if($t_rowk['waktuposting'] > $row['batasakhir']['realvalue'])
								$rowstyle = 'YellowBG';
					?>
						<tr class="<?= $rowstyle ?>">
							<td align="center"><?= $t_row['nim'] ?></td>
							<td><?= $t_row['nama'] ?></td>
							<td align="center"><?= CStr::formatDateDiff($t_rowk['waktuposting']) ?></td>
							<? if($c_edit) { ?>
							<td align="center">
								<input type="hidden" name="nim[]" value="<?= $t_row['nim'] ?>">
								<?= UI::createTextBox('nilai[]',$t_rowk['nilaitugas'],'ControlStyle XCell',6,6,$c_edit,'onkeydown="return onlyNumber(event,this,true,false)"') ?>
							</td>
							<? } ?>
							<td align="center">
							<? if(!empty($t_rowk['waktuposting'])) { ?>
								<img title="Unduh Tugas" src="images/download.png" onclick="goDownload('<?= $p_uptype ?>','<?= $t_rowk['idtugasdikumpulkan'] ?>')" style="cursor:pointer">
							<? } ?>
							</td>
						</tr>
					<?	}
						if($i == 0) {
					?>
						<tr>
							<td colspan="5" align="center">Kelas ini tidak memiliki peserta</td>
						</tr>
					<?	}
						else if($c_edit) { ?>
						<tr class="LeftColumnBG">
							<td colspan="5" align="center">
								<input type="button" value="Simpan Nilai" onclick="goSaveNilai()">
							</td>
						</tr>
					<?	} ?>
					</table>
					<?	}
						if($c_viewownup) { ?>
					<br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/SUBMIT.png" onerror="loadDefaultActImg(this)"> <h1>Pengumpulan Tugas</h1>
							</div>
						</div>
					</header>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<?	$t_rowk = $a_kumpul[$r_nim];
						if(!empty($t_rowk)) {
							$t_file = $conf['upload_dir'].$p_uptype.'/'.$t_rowk['idtugasdikumpulkan'];
							
							if(file_exists($t_file)) {
								$t_size = filesize($t_file);
								$finfo = finfo_open(FILEINFO_MIME_TYPE);
								$t_ext = finfo_file($finfo,$t_file);
								finfo_close($finfo);
							}
							
							if($c_upload) {
					?>
						<tr class="DataBG">
							<td colspan="2">Data Pengumpulan Tugas</td>
						</tr>
					<?		} ?>
						<tr>
							<td class="LeftColumnBG" width="150">Waktu Upload</td>
							<td class="RightColumnBG"><?= CStr::formatDateTimeInd($t_rowk['waktuposting']) ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">File</td>
							<td class="RightColumnBG"><u class="ULink" onclick="goDownload('<?= $p_uptype ?>','<?= $t_rowk['idtugasdikumpulkan'] ?>')"><?= $t_rowk['filetugasdikumpulkan'] ?></u></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Informasi File</td>
							<td class="RightColumnBG"><?= $t_ext ?>; <?= CStr::formatSize($t_size) ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Keterangan</td>
							<td class="RightColumnBG"><?= $t_rowk['isitugas'] ?></td>
						</tr>
					<?		if(!empty($t_rowk['waktupostingnilai'])) { ?>
						<tr>
							<td class="LeftColumnBG">Nilai</td>
							<td class="RightColumnBG"><?= $t_rowk['nilaitugas'] ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Waktu Nilai</td>
							<td class="RightColumnBG"><?= CStr::formatDateTimeInd($t_rowk['waktupostingnilai']) ?></td>
						</tr>
					<?		}
						}
						if($c_upload) {
					?>
						<tr>
							<td colspan="2">&nbsp;</td>
						</tr>
						<tr class="DataBG">
							<td colspan="2">Upload Tugas Baru</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="150">Upload</td>
							<td class="RightColumnBG"><?= uForm::getInput($a_dataup) ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Keterangan</td>
							<td class="RightColumnBG"><?= uForm::getInput($a_dataket) ?></td>
						</tr>
						<tr>
							<td colspan="2" align="center">
								<input type="button" value="Upload" onclick="goUpload()">
							</td>
						</tr>
					</table>
					<?	} ?>
					</div>
					<?	} ?>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function() {
	initXCell();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goUp() {
	goView("<?= $p_listpage ?>&kelas=<?= $r_kelas ?>");
}

function goTop() {
	goView("<?= $p_toppage ?>");
}

function goLock() {
	document.getElementById("act").value = "lock";
	goSubmit();
}

function goSaveNilai() {
	document.getElementById("act").value = "save";
	goSubmit();
}

function goUpload() {
	document.getElementById("act").value = "upload";
	goSubmit();
}

</script>
</body>
</html>
