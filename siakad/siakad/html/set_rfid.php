<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	
	// variabel request
	$r_npm = $_POST['npm'];
	
	// properti halaman
	$p_title = 'Registrasi RFID Mahasiswa';
	$p_tbwidth = 500;
	$p_aktivitas = 'BIODATA';
	
	$p_model = mMahasiswa;
	
	// ada submit
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$record = array();
		$record['rfid'] = CStr::cStrNull($_POST['rfid']);
		
		$p_posterr = $p_model::updateRecord($conn,$record,$r_npm);
		$p_postmsg = 'Registrasi RFID mahasiswa '.(empty($p_posterr) ? 'berhasil' : 'gagal, RFID telah terdaftar');
	}
	
	// ambil data mahasiswa
	if(!empty($r_npm)) {
		$row = mMahasiswa::getDataSingkat($conn,$r_npm);
		if(empty($row['nim'])) {
			unset($row);
			
			$p_posterr = true;
			$p_postmsg = 'Mahasiswa dengan N I M '.$r_npm.' tidak ditemukan';
		}
	}
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth-12 ?>px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
			<?	} ?>
			<center>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/BIODATA.png" onerror="loadDefaultActImg(this);"> <h1>Registrasi RFID Mahasiswa</h1>
						</div>
					</div>
				</header>
				<form id="form_set" method="post">
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table width="100%" cellpadding="4" cellspacing="2" align="center">
							<tr>
								<td class="LeftColumnBG" width="100">N I M</td>
								<td class="RightColumnBG">
									<?= UI::createTextBox('npmtxt',$r_npm,'ControlStyle',20,20) ?>&nbsp;
									<input type="button" value="Cek Mahasiswa" class="ControlStyle" onClick="goCekNPM();">
								</td>
							</tr>
							<? if(!empty($row)) { ?>
							<tr>
								<td colspan="2"></td>
							</tr>
							<tr>
								<td class="LeftColumnBG">N I M</td>
								<td class="RightColumnBG"><?= $row['nim'] ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Nama Mahasiswa</td>
								<td class="RightColumnBG"><?= $row['nama'] ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Fakultas</td>
								<td class="RightColumnBG"><?= $row['fakultas'] ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Prodi</td>
								<td class="RightColumnBG"><?= $row['jurusan'] ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Periode Daftar</td>
								<td class="RightColumnBG"><?= $row['namaperiodedaftar'] ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG">RFID</td>
								<td class="RightColumnBG"><?= $row['rfid'] ?></td>
							</tr>
							<tr>
								<td class="LeftColumnBG">RFID Baru</td>
								<td class="RightColumnBG"><?= UI::createTextBox('rfid',null,'ControlStyle',100,50,true,'autocomplete="off"') ?></td>
							</tr>
							<? } ?>
						</table>
						<? if(!empty($row)) { ?>
						<div class="Break"></div>
						<input type="button" value="Simpan RFID" class="ControlStyle" onclick="goSave();">
						<?php } ?>
					</div>
					<input type="hidden" name="npm" id="npm" value="<?= $r_npm ?>">
					<input type="hidden" name="act" id="act">
				</form>
			</center>
		</div>
	</div>
</div>

<script type="text/javascript">

<? if(!empty($row)) { ?>
$(function() {
	$("#rfid").focus();
});
<? } ?>

function goCekNPM() {
	$("#npm").val($("#npmtxt").val());
	$("#form_set").submit();
}

function goSave() {
	$("#act").val("save");
	$("#form_set").submit();
}

</script>

</body>
</html>
