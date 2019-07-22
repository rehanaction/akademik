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
	if($c_edit and $r_act == 'tutup') {
		$conn->Execute("insert into aset.as_tutupbuku (periode) values ('$r_periode')");

		if($conn->ErrorNo() != 0){
		    $p_posterr = true;
			$p_postmsg = 'Proses Tutup Buku Gagal !';
		} else {
		    $p_posterr = false;
			$p_postmsg = 'Proses Tutup Buku Berhasil';
		}
	}
	else if($c_edit and $r_act == 'buka') {
		$conn->Execute("delete from aset.as_tutupbuku where periode = '$r_periode'");
		
		if($conn->ErrorNo() != 0){
		    $p_posterr = true;
			$p_postmsg = 'Proses Buka Tutup Buku Gagal !';
		} else {
		    $p_posterr = false;
			$p_postmsg = 'Proses Buka Tutup Buku Berhasil';
		}
	}

    $r_maxperiode = $p_model::getMaxPeriode($conn);
	$a_listperiode = $p_model::getListPeriode($r_maxperiode);
    $a_tutup = $p_model::getListTutup($conn);
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
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	$i = 0;
						    foreach($a_tahun as $tahun){
    						    foreach($a_bulan[$tahun] as $bulan){
    						        $periode = $tahun.$bulan;
						?>
						<td align="center">
			            <?  if(!empty($a_tutup[$periode])){ ?>
			            <?      if($a_tutup[$periode] == $r_maxperiode){ $i++; ?>
							<img title="Buka Buku" src="images/lock.gif" onclick="goUnLocked('<?= $periode ?>')" style="cursor:pointer">
			            <?      }else{ ?>
							<img src="images/locked.gif">
			            <?      } ?>
			            <?  }else{ ?>
			            <?      if($i == 1){ $i++; ?>
							<img title="Tutup Buku" src="images/unlock.gif" onclick="goLocked('<?= $periode ?>')" style="cursor:pointer">
			            <?      }else { ?>
							<img src="images/unlocked.gif">
			            <?      } ?>
			            <?  } ?>
						</td>
                        <?      }
    						}
						?>
					</tr>
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

function goUnLocked(periode) {
	$('#periode').val(periode);
	$('#act').val('buka');
	goSubmit();
}

function goLocked(periode) {
	$('#periode').val(periode);
	$('#act').val('tutup');
	goSubmit();
}

</script>
</body>
</html>
