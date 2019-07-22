<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	$c_validasi = $a_auth['canother']['V'];
	
	// include
	require_once(Route::getModelPath('ta'));
	require_once(Route::getModelPath('yudisium'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	// properti halaman
	$p_title = 'Pengisian Data Terbaru Mahasiswa';
	$p_tbwidth = 550;
	$p_aktivitas = 'WISUDA';
	$p_colnum = 2;
	
	$p_model = mYudisium;
	
	// pengecekan mahasiswa
	if(!empty($r_key)) {
		$r_nim = $r_key;
		$r_mahasiswa = $r_nim.' - '.Akademik::getNamaMahasiswa($conn,$r_nim);
		
		// ambil data ta
		$t_idta = mTA::getTAMAhasiswa($conn,$r_nim);
		if(empty($t_idta)) {
			$p_posterr = true;
			$p_postmsg = $r_mahasiswa.' belum mengambil skripsi';
			
			$r_key = '';
		}
	}
	
	// pengecekan data
	if(!empty($r_key))
		$row = $p_model::getDataAkhirMahasiswa($conn,$r_key);
	
	$a_input = array();
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama Lengkap', 'size' => 50, 'maxlength' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'alamat', 'label' => 'Alamat', 'size' => 50, 'maxlength' => 50, 'notnull' => true);
	$a_input[] = array('kolom' => 'judulta', 'label' => 'Judul Skripsi', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'notnull' => true);
	
	if($c_validasi or !empty($row['datavalid']))
		$a_input[] = array('kolom' => 'datavalid', 'label' => 'Data Valid?', 'type' => 'C', 'option' => array('-1' => 'Data akhir mahasiswa valid'), 'readonly' => !$c_validasi);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit and !empty($r_key)) {
		$record = array();
		$record['nama'] = CStr::cStrNull($_POST['nama']);
		$record['alamat'] = CStr::cStrNull($_POST['alamat']);
		$record['judulta'] = CStr::cStrNull($_POST['judulta']);
		
		if(empty($_POST['idta']))
			$record['idta'] = $t_idta;
		else
			$record['idta'] = (int)$_POST['idta'];
		
		if($c_validasi)
			$record['datavalid'] = (empty($_POST['datavalid']) ? 0 : -1);
		
		$err = $p_model::saveDataAkhirMahasiswa($conn,$record,$r_key);
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Penyimpanan data terbaru mahasiswa '.($err ? 'gagal' : 'berhasil');
	}
	
	$row = $p_model::getDataAkhirMahasiswa($conn,$r_key);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forinplace.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<? if(!Akademik::isMhs()) { ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td width="70"><strong>Mahasiswa</strong></td>
											<td>
												<strong> : </strong>
												<?= UI::createTextBox('mahasiswa',$r_mahasiswa,'ControlStyle',55,55) ?>
												<input type="hidden" id="nim" name="nim" value="<?= $r_nim ?>">
												<input type="button" value="Ambil Data" onclick="goCek()">
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	}
					if(!empty($p_postmsg)) { ?>
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
				<?	}
					if(!empty($r_key)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<?	$a_required = array();
						foreach($a_input as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['kolom'];
							if(empty($t_row['input']))
								$t_row['input'] = uForm::getInput($t_row,$row[$t_row['kolom']]);
					?>
						<tr>
							<td class="LeftColumnBG" width="100" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<?= $t_row['input'] ?>
							</td>
						</tr>
					<?	}
						if($c_edit) { ?>
						<tr>
							<td colspan="<?= $p_colnum ?>" align="center">
								<input type="hidden" name="idta" value="<?= $row['idta'] ?>">
								<input type="button" value="Simpan" onclick="goSave()" style="font-size:14px">
							</td>
						</tr>
					<?	} ?>
					</table>
					</div>
				</center>
				<?	} ?>
				
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
	
	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
});

function goCek() {
	document.getElementById("key").value = document.getElementById("nim").value;
	goSubmit();
}

function goSave() {
	document.getElementById("act").value = "save";
	goSubmit();
}

</script>
</body>
</html>