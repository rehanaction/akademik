<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarifreg'));
	require_once(Route::getModelPath('gelombangdaftar'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jalur = Modul::setRequest($_POST['jalurpenerimaan'],'JALUR');
	$r_gelombang = Modul::setRequest($_POST['gelombang'],'GELOMBANG');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
	$r_sistemkuliah = Modul::setRequest($_POST['sistemkuliah'],'SISTEMKULIAH');
	
	$r_periodesalin = Modul::setRequest($_POST['periodesalin'],'PERIODESALIN');
	$r_jalursalin = Modul::setRequest($_POST['jalurpenerimaansalin'],'JALURPENERIMAANSALIN');
	
	// properti halaman
	$p_title = 'Tarif Pembayaran Registrasi Reguler';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Master';
	$p_detailpage = Route::getDetailPage();
	
	// ada aksi
	$r_act = $_POST['act'];
	if ($r_act == 'salin' and $c_edit) {
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = mTarifReg::salinTarif($conn,$r_jalur,$r_periode,$r_jalursalin,$r_periodesalin);
		
		$ok = ($p_posterr ? false : true);
		$conn->CommitTrans($ok);
		
		if($ok) {
			$r_periode = $r_periodesalin;
			$r_jalur = $r_jalursalin;
		}
	}
	
	// cek post
	if (empty ($r_periode))
	{
		$p_postmsg='Silahkan Pilih Periode';
		$p_posterr=true;
	}
    if (empty ($r_jalur))
	{
		$p_postmsg='Silahkan Pilih Jalur Penerimaan';
		$p_posterr=true;
	}
    if (empty ($r_sistemkuliah))
	{
		$p_postmsg='Silahkan Pilih sistem Kuliah';
		$p_posterr=true;
	}
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','onchange="goSubmit()"',true);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','onchange="goSubmit()"',true);
	$l_gelombang = uCombo::gelombang($conn,$r_gelombang,'gelombang','onchange="goSubmit()"',true);
	$l_periodesalin = uCombo::periode($conn,$r_periodesalin,'periodesalin','',true);
	$l_jalursalin = uCombo::jalur($conn,$r_jalursalin,'jalurpenerimaansalin','',true);
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',true);
	$l_sistemkuliah = uCombo::sistemkuliah($conn,$r_sistemkuliah,'sistemkuliah','onchange="goSubmit()"',true);
	
	// daftar gelombang
	if(!empty($r_jalur) and !empty($r_periode)) {
		$arr_gelombang = mGelombangDaftar::getListGelombang($conn,$r_jalur,$r_periode);
		if(!empty($r_gelombang)) {
			if(empty($arr_gelombang[$r_gelombang]))
				$arr_gelombang = array();
			else
				$arr_gelombang = array($r_gelombang => $arr_gelombang[$r_gelombang]);
		}
	}
	else
		$arr_gelombang = array();
		
	// daftar jurusan
	$arr_unit = mAkademik::getArrayunit($conn,false,'2',$r_unit);
		
	// data tarif
	$data = array();
	if(!empty($r_jalur) and !empty($r_periode) and !empty ($r_sistemkuliah))
		$data = mTarifReg::getArrayList($conn,$r_jalur,$r_periode,$r_gelombang,$r_unit,$r_sistemkuliah);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode Daftar', 'combo' => $l_periode);
	$a_filtercombo[] = array('label' => 'Jalur Penerimaan', 'combo' => $l_jalur);
	$a_filtercombo[] = array('label' => 'Gelombang', 'combo' => $l_gelombang);
	$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Sistem Kuliah', 'combo' => $l_sistemkuliah);
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
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<? if($c_edit) { ?>50<? } else { ?>100<? } ?>%; float:left; min-height:100px">
						<table cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td colspan="3" align="center" ><b>Filter Data</b></td>
							</tr>
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
					<? if($c_edit) { ?>
					<div class="filterTable" style="width:40%; float:right; min-height:100px">
						<table cellpadding="3" cellspacing="0" align="center">
							<tr>
								<td align="center" style="font-weight:bold" colspan="3">Salin Tarif</td>
							</tr>
							<tr>
								<td>Periode Daftar</td>
								<td>:</td>
								<td><?= $l_periodesalin ?></td>				
							</tr>
							<tr>
								<td>Jalur Penerimaan</td>
								<td>:</td>
								<td><?= $l_jalursalin ?></td>								
							</tr>
							<tr>
								<td align="center" style="font-weight:bold" colspan="3"><input type="button" value="Salin Tarif" id="salin" name="salin" onclick="goSalin()"></td>
							</tr>
						</table>
					</div>
					<? } ?>
				</center>
				<div style="clear:both"></div>
				<br>
				<?	}
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
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
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle Pointerless" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th rowspan="2">Jurusan</th>
						<? foreach($arr_gelombang as $k => $v) { ?>
						<th colspan="3"><?= $v ?></th>
						<? } ?>
					</tr>
                    <tr>
					<?	foreach($arr_gelombang as $k => $v) { ?>
						<th>Angsur</th>
						<th>Total</th>
						<th width="2%">Detail</th>
					<?	} ?>
                    </tr>
					<?	/********/
						/* ITEM */
						/********/
						if($arr_unit)
							$i = 0;
							foreach($arr_unit as $kodeunit => $namaunit) {
								if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
                        <td><strong><?=$kodeunit.' - '.$namaunit?></strong></td>
						<? foreach($arr_gelombang as $k => $v) { ?>
						<td align="center"><?= $data[$kodeunit][$k]['angsuran'] ?></td>
						<td align="right"><?= CStr::formatNumber($data[$kodeunit][$k]['total']) ?></td>
						<td align="center">
                            <img id="<?=$kodeunit.'|'.$r_periode.'|'.$r_jalur.'|'.$r_sistemkuliah.'|'.$k?>" title="Tampilkan Detail" src="images/edit.png" onClick="goDetail(this)" style="cursor:pointer">
                        </td>
						<? } ?>
					</tr>
					<?		} ?>
				</table>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var insertreq = "<?= @implode(',',$a_insertreq) ?>";
var updatereq = "<?= @implode(',',$a_updatereq) ?>";
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

<? if($c_edit) { ?>

function goSalin(){
	var con = confirm ('apakah anda yakin melakukan Salin Tarif?');
	if (con){
		document.getElementById("act").value = 'salin';
		goSubmit();
	}
}

<? } ?>

</script>
</body>
</html>
