<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_periode = $r_tahun.$r_semester;
	
	$l_semester = uCombo::semester($r_semester);
	$l_tahun = uCombo::tahun($r_tahun);
	
	// properti halaman
	$p_title = 'Buat Tagihan Her Registrasi';
	$p_tbwidth = 700;
	$p_aktivitas = 'SPP';
	$p_colnum = 8;
	
	$p_model = mTagihan;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'generate' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		list($r_kodeunit,$r_jalur) = explode('|',$r_key);
		
		$err = $p_model::generateTagihanHer($conn,$r_periode,$r_kodeunit,$r_jalur);
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Pembuatan tagihan her registrasi '.($err ? 'gagal' : 'berhasil');
	}
	
	// mendapatkan data h2h
	$a_data = mTagihan::getRekapTagihanHer($conn,$r_periode);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_semester.' '.$l_tahun);
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
				<?php require_once('inc_listfilter.php'); ?>
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
						<th rowspan="2">Fakultas</th>
						<th rowspan="2">Prodi</th>
						<th rowspan="2">Jalur</th>
						<th colspan="3">Mahasiswa</th>
						<th rowspan="2">Tagihan</th>
						<? if($c_edit) { ?>
						<th rowspan="2">Buat</th>
						<? } ?>
					</tr>
					<tr>
						<th>AKAD</th>
						<th>H2H</th>
						<th>Asing</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $t_kodeunit => $t_data) {
							foreach($t_data as $t_jalurpenerimaan => $row) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								
								$t_key = $t_kodeunit.'|'.$t_jalurpenerimaan;
								
								$t_jmlmahasiswa = (int)$row['jmlmahasiswa'];
								$t_jmlmhsh2h = (int)$row['jmlmhsh2h'];
								$t_jmlasing = (int)$row['jmlasing'];
								$t_jmltagihan = (int)$row['jmltagihan'];
								
								$t_tjmlmahasiswa += $t_jmlmahasiswa;
								$t_tjmlmhsh2h += $t_jmlmhsh2h;
								$t_tjmlasing = $t_jmlasing;
								$t_tjmltagihan += $t_jmltagihan;
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['fakultas'] ?></td>
						<td><?= $row['jurusan'] ?></td>
						<td><?= $row['jalurpenerimaan'] ?></td>
						<td align="center"><?= CStr::formatNumber($t_jmlmahasiswa) ?></td>
						<td align="center"><?= CStr::formatNumber($t_jmlmhsh2h) ?></td>
						<td align="center"><?= CStr::formatNumber($t_jmlasing) ?></td>
						<td align="center"><?= CStr::formatNumber($t_jmltagihan) ?></td>
						<? if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Buat Tagihan" src="images/disk.png" onclick="goGenerate(this)" style="cursor:pointer"></td>
						<? } ?>
					</tr>
					<?		}
						}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						else { ?>
					<tr valign="top" class="YellowBG">
						<th colspan="3">Total</th>
						<th><?= CStr::formatNumber($t_tjmlmahasiswa) ?></th>
						<th><?= CStr::formatNumber($t_tjmlmhsh2h) ?></th>
						<th><?= CStr::formatNumber($t_tjmlasing) ?></th>
						<th><?= CStr::formatNumber($t_tjmltagihan) ?></th>
						<? if($c_edit) { ?>
						<th>&nbsp;</th>
						<? } ?>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

<? if($c_edit) { ?>
$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goGenerate(elem) {
	document.getElementById("key").value = elem.id;
	document.getElementById("act").value = "generate";
	
	goSubmit();
}
<? } ?>

</script>

</body>
</html>