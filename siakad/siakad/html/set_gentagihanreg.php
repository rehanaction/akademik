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
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEDAFTAR');
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_jalur = Modul::setRequest($_POST['jalur'],'JALUR');
	
	$l_periode = uCombo::periodeDaftar($conn,$r_periode,'periode','onchange="goSubmit()"',false);
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_jalur = uCombo::jalurPenerimaan($conn,$r_jalur,'jalur','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Buat Tagihan Registrasi';
	$p_tbwidth = 650;
	$p_aktivitas = 'SPP';
	$p_colnum = 7;
	
	$p_model = mTagihan;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'generate' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		list($r_kodeunit,$r_jalur) = explode('|',$r_key);
		
		$err = $p_model::generateTagihanReg($conn,$r_periode,$r_kodeunit,$r_jalur);
		
		$p_posterr = Query::boolErr($err);
		$p_postmsg = 'Pembuatan tagihan registrasi '.($err ? 'gagal' : 'berhasil');
	}
	
	// mendapatkan data h2h
	$a_data = mTagihan::getRekapTagihanReg($conn,$r_periode, $r_unit, $r_jalur);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jalur', 'combo' => $l_jalur);
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
						<th>Fakultas</th>
						<th>Prodi</th>
						<th>Jalur</th>
						<th>Pendaftar</th>
						<th>Asing</th>
						<th>Tagihan</th>
						<? if($c_edit) { ?>
						<th>Buat</th>
						<? } ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $t_kodeunit => $t_data) {
							foreach($t_data as $t_jalurpenerimaan => $row) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
								
								$t_key = $t_kodeunit.'|'.$t_jalurpenerimaan;
								
								$t_jmlpendaftar = (int)$row['jmlpendaftar'];
								$t_jmlasing = (int)$row['jmlasing'];
								$t_jmltagihan = (int)$row['jmltagihan'];
								
								$t_tjmlpendaftar += $t_jmlpendaftar;
								$t_tjmlasing = $t_jmlasing;
								$t_tjmltagihan += $t_jmltagihan;
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['fakultas'] ?></td>
						<td><?= $row['jurusan'] ?></td>
						<td><?= $row['jalurpenerimaan'] ?></td>
						<td align="center"><?= CStr::formatNumber($t_jmlpendaftar) ?></td>
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
						<th><?= CStr::formatNumber($t_tjmlpendaftar) ?></th>
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