<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('yudisium'));
	require_once(Route::getModelPath('syaratyudisium'));
	require_once(Route::getModelPath('ceksyaratyudisium'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEWISUDA');
	
	// combo
	$l_periode = uCombo::periodeWisuda($conn,$r_periode,'periode','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Pengecekan Syarat Yudisium Mahasiswa';
	$p_tbwidth = 550;
	$p_aktivitas = 'WISUDA';
	$p_colnum = 3;
	
	$p_model = mCekSyaratYudisium;
	
	// pengecekan mahasiswa
	if(!empty($r_key)) {
		$r_nim = $r_key;
		$r_mahasiswa = $r_nim.' - '.Akademik::getNamaMahasiswa($conn,$r_nim);
		
		// cek yudisium
		$a_yudisium = mYudisium::getData($conn,$r_key.'|'.$r_periode);
		$t_yudisium = $a_yudisium['idyudisium'];
		
		// if(empty($t_yudisium)) {
			// $p_posterr = $p_model::cekPrasyaratMahasiswa($conn,$r_key);
			
			// if($p_posterr) {
				// switch($p_posterr) {
					// case -1: $p_postmsg = '<strong>'.$r_mahasiswa.'</strong> belum lulus skripsi'; break;
				// }
				
				// $r_key = '';
			// }
		// }
		// else {
			// $p_postmsg = $r_mahasiswa.' sudah didaftarkan yudisium';
			
			// $c_edit = false;
		// }
	}
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		// ambil checkbox
		$a_check = array();
		if(!empty($_POST['check'])) {
			foreach($_POST['check'] as $t_id)
				$a_check[] = (int)$t_id;
		}
		
		$p_posterr = $p_model::saveSyaratMahasiswa($conn,$r_key,$a_check);
		if(!$p_posterr){//tdk error
			//cek syarat yudisium dan cek syarat yudisium jika sama, brrti sdh sip
			$syarat = $conn->GetOne("select count(*) from akademik.ak_syaratyudisium");
			$ceksyarat = $conn->GetOne("select count(*) from akademik.ak_ceksyaratyudisium where nim='".$r_key."'");
			if($syarat == $ceksyarat){ //sama
				//inputkan ke ak_yudisium sbg peserta
				$record = array();
				$record['idyudisium'] = $r_periode;
				$record['nim'] = $r_key;
				$record['nama'] = CStr::cStrNull(Akademik::getNamaMahasiswa($conn,$record['nim']));
				$err = Query::recInsert($conn,$record,'akademik.ak_yudisium');
				
				if(!$err){
					//update status mhs jadi L = Lulus
					$conn->Execute("update akademik.ms_mahasiswa set statusmhs='L' where nim='$r_key'");
				}
			}
		}
		
		$p_postmsg = 'Penyimpanan '.$p_model::label.' '.($p_posterr ? 'gagal' : 'berhasil');
	}
	
	// mengambil data
	$a_filter = array();
	$a_filter[] = mSyaratYudisium::getListFilter('periodewisuda',$r_periode);
	
	$a_data = mSyaratYudisium::getList($conn,$a_filter);
	$a_cek = $p_model::getListMhsPeriode($conn,$r_key,$r_periode);
	
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
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
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td><strong>Periode</strong></td>
											<td><strong> : </strong><?= $l_periode ?></td>
										</tr>
										<tr>		
											<td width="70"><strong>Mahasiswa</strong></td>
											<td>
												<strong> : </strong>
												<?= UI::createTextBox('mahasiswa',$r_mahasiswa,'ControlStyle',55,55) ?>
												<input type="hidden" id="nim" name="nim" value="<?= $r_nim ?>">
												<input type="button" value="Cek Syarat" onclick="goCek()">
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
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					<tr>
						<th width="30">No</th>
						<th width="30">Cek</th>
						<th>Syarat</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$cekbutton = false;
							if(Modul::getRole()=='A'){
								$cekbutton = true;
							}else{
								if(Modul::getRole() == $row['role'])
									$cekbutton = true;
								else
									$cekbutton = false;
							}
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<td align="center">
							<?	if($c_edit) { ?>
							<input <? if(!$cekbutton) echo 'disabled'; else echo'';?> type="checkbox" name="check[]" value="<?= $row['idsyaratyudisium'] ?>"<?= empty($a_cek[$row['idsyaratyudisium']]) ? '' : ' checked' ?>>
							<?	} else if($a_cek[$row['idsyaratyudisium']]) { ?>
							<img src="images/check.png">
							<?	} ?>
						</td>
						<td><?= $row['keterangan'] ?></td>
					</tr>
					<?	}
						if($c_edit) { ?>
					<tr class="LeftColumnBG">
						<td colspan="<?= $p_colnum ?>" align="center">
							<input type="button" value="Simpan" onclick="goSave()" style="font-size:14px">
						</td>
					</tr>
					<?	} ?>
				</table>
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
