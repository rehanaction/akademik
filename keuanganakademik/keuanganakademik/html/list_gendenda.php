<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	
	if (empty ($r_periode))
		list($p_posterr, $p_postmsg) = array(true, 'Silahkan Pilih Periode');
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
        
	// properti halaman
	$p_title = 'Generate Denda';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	$p_label = 'Denda';
	
	$p_model = mTagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	// array unit
	$arr_unit = mAkademik::getArrayunit($conn,false,'2');
	
	// array denda
	$a_jenisdenda = $p_model::getListJenisDenda($conn);
	$p_default = $p_model::dendaDefault;
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_key = $_POST['key'];
	if($r_act == 'generate' or $r_act == 'void') {
		list($t_kodeunit,$t_jenis) = explode(':',$r_key);
		
		$conn->BeginTrans();
		
		// di-void dulu
		$where = array();
		$where['kodeunit'] = $t_kodeunit;
		$where['periodetagihan'] = $r_periode;
		
		if($t_jenis == $p_default) {
			$p_posterr = mTagihan::deleteDenda($conn,$where);
		}
		else {
			$where['jenistagihan'] = $t_jenis;
			
			$p_posterr = mTagihan::delete($conn,$where);
			if(empty($p_posterr))
				$p_posterr = mTagihan::deletetagihanawal($conn,$where);
		}
		
		// baru di-generate
		if($r_act == 'generate' and empty($p_posterr)) {
			if($t_jenis == $p_default)
				$p_posterr = mTagihan::updateDenda($conn,$where);
			else
				$p_posterr = mTagihan::generateTagihanDenda($conn,$where);
			
			if($p_posterr == 0)
				$p_postmsg = 'Berhasil Melakukan Generate Denda';
			else	
				$p_postmsg = 'Gagal Melakukan Generate Denda';
		}
		else {
			if($p_posterr == 0)
				$p_postmsg = 'Berhasil Melakukan Pembatalan Denda';
			else	
				$p_postmsg = 'Gagal Melakukan Pembatalan Denda';
		}
		
		$ok = Query::isOK($p_posterr);
		
		$conn->CommitTrans($ok);
	}
	
	// cek denda
	$a_denda = $p_model::getListDenda($conn,$r_periode);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
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
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
					if(!empty($r_periode)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>">
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
						<th rowspan="2">Jurusan</th>
						<? foreach($a_jenisdenda as $k => $v) { ?>
						<th colspan="3"><?= $v ?></th>
						<? } ?>
					</tr>
					<tr>
						<? foreach($a_jenisdenda as $k => $v) { ?>
						<th style="width:20px">&nbsp;</th>
						<th style="width:80px">Denda</th>
						<th style="width:80px">Bayar</th>
						<? } ?>
					</tr>
					<?	$i = 0;
						foreach($arr_unit as $kodeunit => $namaunit) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
                    <tr class="<?= $rowstyle ?>">
                    	<td><?=$namaunit?></td>
						<? foreach($a_jenisdenda as $k => $v) { ?>
						<td style="text-align:center">
							<img title="Generate Denda" src="images/disk.png" onClick="goGenerate('<?= $kodeunit ?>','<?= $k ?>')" style="cursor:pointer">
						</td>
						<td style="text-align:right"><?= CStr::formatNumber($a_denda['tagihan'][$kodeunit][$k]) ?></td>
						<td style="text-align:right"><?= CStr::formatNumber($a_denda['bayar'][$kodeunit][$k]) ?></td>
						<? } ?>
					</tr>
                    <? } ?>
				</table>
				<? } ?>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<? if(isset($p_posterr) and empty($p_posterr)) { ?>
	initRefresh();
	<? } ?>
});

function goVoid(kodeunit,jenis){
	var txt = confirm("Semua denda yang telah di generate akan di hapus, apakah anda yakin akan membatalkan generate denda ini?");
	if(txt) {
		document.getElementById("act").value = "void";
		document.getElementById("key").value = kodeunit + ":" + jenis;
		
		goSubmit();
	}
}

function goGenerate(kodeunit,jenis){
	var txt = confirm("Apakah anda yakin akan melakukan generate denda ini?");
	if(txt) {
		document.getElementById("act").value = "generate";
		document.getElementById("key").value = kodeunit + ":" + jenis;
		
		goSubmit();
	}
}

</script>