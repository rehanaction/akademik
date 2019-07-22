<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('set_tutupbuku');
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('tutupbuku'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_idunit = CStr::removeSpecial($_REQUEST['idunit']);
	$r_periode = CStr::removeSpecial($_REQUEST['periode']);
	$r_act = CStr::removeSpecial($_REQUEST['act']);
	
	// properti halaman
	$p_title = 'Tutup Buku';
	$p_tbwidth = 700;
	$p_aktivitas = 'tutup buku';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mTutupBuku;
	
	// ada aksi
	$r_act = $_POST['act'];
	//echo $c_edit.'-'.$r_act;
	//echo $r_idunit.'-'.$periode;
	if($c_edit and $r_act == 'tutup' and !empty($r_idunit)) {
		$conn->Execute("insert into aset.as_tutupbuku (idunit,periode) values ($r_idunit,$r_periode)");
		
		if($conn->ErrorNo() != 0){
			$msg = '<font color="red">Proses Tutup Buku Gagal !</font>';
		} else {
			$msg = '<font color="blue">Proses Tutup Buku Berhasil</font>';
		}
	}
	else if($c_edit and $r_act == 'buka' and !empty($r_idunit)) {
		$conn->Execute("delete from aset.as_tutupbuku where idunit = $r_idunit");
		
		if($conn->ErrorNo() != 0){
			$msg = '<font color="red">Proses Buka Tutup Buku Gagal !</font>';
		} else {
			$msg = '<font color="blue">Proses Buka Tutup Buku Berhasil</font>';
		}
	}
	
	$a_unit = mUnit::getListUnit($conn);
	$a_listperiode = $p_model::getListPeriode();
    $a_tutupbuku = $p_model::getListTutupBuku($conn,$a_listperiode);
	$a_namabulan = mCombo::bulan(false);

	foreach($a_listperiode as $periode){
        $tahun = substr($periode,0,4);
        $bulan = substr($periode,4,2);
        
        $a_tahun[$tahun] = $tahun;
        $a_bulan[$tahun][] = $bulan;
    }
    
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
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
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
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr>
						<th rowspan="2" width="60">Kode Unit</th>
						<th rowspan="2">Nama Unit</th>
						<?  foreach($a_tahun as $tahun){ ?>
						<th colspan="<?= count($a_bulan[$tahun]) ?>"><?= $tahun ?></th>
                        <?  } ?>
					</tr>
					<tr>
						<?	foreach($a_tahun as $tahun){
    						    foreach($a_bulan[$tahun] as $bulan){
						?>
						<th width="30"><?= $a_namabulan[$bulan] ?></th>
                        <?      }
    						}
						?>
					</tr>
					<?
						$i = 0; $iskunci = false;
						foreach($a_unit as $idunit => $unit) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							//padding nama unit
							$pad = 'style="padding-left:'.(((int)$unit['level'])*15).'px"';
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $unit['kodeunit'] ?></td>
						<td <?= $pad ?>><?= $unit['namaunit'] ?></td>
						<?	foreach($a_tahun as $tahun){
    						    foreach($a_bulan[$tahun] as $bulan){
    						        $periode = $tahun.$bulan;
						?>
						<td align="center">
			            <? if($a_tutupbuku[$idunit][$periode] == 1){ ?>
							<img title="Buka Buku" src="images/unlocked.gif" onclick="goUnLocked('<?= $idunit ?>', '<?= $periode ?>')" style="cursor:pointer">
			            <? }else{ ?>
							<img title="Tutup Buku" src="images/locked.gif" onclick="goLocked('<?= $idunit ?>', '<?= $periode ?>')" style="cursor:pointer">
			            <? } ?>
						</td>
                        <?      }
    						}
						?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="8" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="idunit" id="idunit">
				<input type="hidden" name="periode" id="periode">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	// handle contact
	//$("[id='imgcontact']").balloon();
});

function goUnLocked(idunit,periode) {
	$('#idunit').val(idunit);
	$('#periode').val(periode);
	$('#act').val('buka');
	goSubmit();
}

function goLocked(idunit,periode) {
	$('#idunit').val(idunit);
	$('#periode').val(periode);
	$('#act').val('tutup');
	goSubmit();
}

</script>
</body>
</html>
