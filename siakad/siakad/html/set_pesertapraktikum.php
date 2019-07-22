<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_insert = false;
	$c_edit = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	$c_delete = false;
	
	// hak akses manual :D
	if(Akademik::isDosen()) {
		$c_insert = false;
		$c_edit = false;
		$c_delete = false;
	}
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Peserta Praktikum';
	$p_tbwidth = 700;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_absensi';
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	
	$r_kelompok = Modul::setRequest($_POST['kelompokbaru']);
	
	
	$r_periode = $r_tahun.$r_semester;
	
	
	
	
	$r_act = $_POST['act'];
	if($r_act == 'pindahpilih' and $c_edit) {
		$r_npm = $_POST['cnpm'];
		
		if(!empty($r_npm)) {
			$record = array();
			$record['kelompok_prak'] = CStr::cStrNull($_POST['kelompokbaru']);
			
			$ok = true;
			$conn->BeginTrans();
			
			foreach($r_npm as $t_npm) {
				list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk,,)=explode('|',$r_key);
				$t_subkey=$thnkurikulum.'|'.$kodemk.'|'.$kodeunit.'|'.$periode.'|'.$kelasmk.'|'.CStr::removeSpecial($t_npm);
				
				list($p_posterr,$p_postmsg)=mKRS::cekKresKrs($conn,$thnkurikulum,$periode,$kodeunit,$kodemk,$kelasmk,$t_npm,$record['kelompok_prak']);
				
				if(!$p_posterr) 
				list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$t_subkey,true);
				
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
		}
	}else if($r_act == 'pindahnim' and $c_edit) {
		$r_npm = CStr::removeSpecial($_POST['subkey']);
		list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk,,)=explode('|',$r_key);
		$r_subkey=$thnkurikulum.'|'.$kodemk.'|'.$kodeunit.'|'.$periode.'|'.$kelasmk.'|'.$r_npm;
		
		
		$record = array();
		$record['kelompok_prak'] = CStr::cStrNull($_POST['kelompokbaru']);
		
		list($p_posterr,$p_postmsg)=mKRS::cekKresKrs($conn,$thnkurikulum,$periode,$kodeunit,$kodemk,$kelasmk,$r_npm,$record['kelompok_prak']);
	
		if(!$p_posterr) 
		list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_subkey,true);
	}
	
	list(,,,,,,$kelompok_prak)=explode('|',$r_key);
	// mendapatkan data
	$a_kelompoklain = mKelasPraktikum::getKelompokKelas($conn,$r_key,$kelompok_prak);
	$a_pesertakelompok=mKelasPraktikum::getPesertaKel($conn,$r_key);
	
	$l_kelompok = UI::createSelect('kelompokbaru',$a_kelompoklain,$r_kelompok,'ControlStyle',true,'onchange="goRefresh()"',true);
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);
	
	$a_data = mKelas::getDataPeserta($conn,$r_key,$kelompok_prak);
	
	
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
		<div style="float:left; width:15%">
			<? require_once('inc_sidemenudosen.php');?>
			</div>
		<div style="float:left; width:50%">
			
			<form name="pageform" id="pageform" method="post">
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<?	if($c_insert or $c_edit) { ?>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
					<?	if($c_insert) { ?>
						<td colspan="2" align="center">Tambah Peserta Kelas</td>
					<?	}
						if($c_edit) { ?>
						<td width="200" align="center">Pindahkan Peserta Praktikum</td>
					<?	} ?>
					</tr>
					<tr>
					<?	if($c_insert) { ?>
						<th>Tambah 1 Peserta</th>
						<th>Pilih dari Angkatan</th>
					<?	}
						if($c_edit) { ?>
						<th>Masukkan Kelompok Tujuan</th>
					<?	} ?>
					</tr>
					<tr valign="top" class="NoHover">
					<?	
						if($c_edit) { ?>
						<td>
							<strong>Kelompok Tujuan:</strong> <?= $l_kelompok ?>
							<div class="Break"></div>
							<strong>Yang Dipindahkan</strong>
							<div class="Break"></div>
							<input type="button" class="ControlStyle" value="Pindahkan yg Dipilih" onclick="goPindahPilih()">
							<div class="Break"></div>
							<? foreach($a_pesertakelompok as $t_kelompok => $t_peserta) { ?>
							<div style="float:left">
							<a title="Peserta Kelompok <?= $t_kelompok ?>" href="javascript:goKelas('<?= $t_kelompok ?>')" style="color:#00F;text-decoration:none">
								<?= $t_kelompok ?> (<?= $t_peserta ?>)
							</a> &nbsp;
							</div>
							<? } ?>
						</td>
					<?	} ?>
					</tr>
				</table>
				</center>
				<br>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?> Kelompok <?=$kelompok_prak?></h1>
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
						<th width="30">No.</th>
						<?	if($c_edit) { ?>
						<th width="30">&nbsp;</th>
						<?	} ?>
						<th width="100">NIM</th>
						<th>Nama</th>
						
						<?	if($c_edit) { ?>
						<th width="30">Pindah</th>
						<?	}
							if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						$kres=false;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $row['nim'];
							$conn->debug=false;
							list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode("|",$r_key);
							
							
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<?	if($c_edit) { ?>
						<td><input type="checkbox" name="cnpm[]" value="<?= $row['nim'] ?>"></td>
						<?	} ?>
						<td align="center"><?= $row['nim'] ?></td>
						<td><?= $row['nama'] ?></td>
						
						
						<?	if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Pindahkan Peserta" src="images/out.png" onclick="goPindah(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="7" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
			</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
function goRefresh() {
	
	document.getElementById("act").value = "";
	goSubmit();
}
function goKelas(kel) {
	// modif key
	key = document.getElementById("key").value;
	
	arkey = key.split('|');
	arkey[6] = kel;
	key = arkey.join('|');
	
	document.getElementById("key").value = key;
	goSubmit();
}

function goAddNIM() {
	document.getElementById("act").value = "insertnim";
	goSubmit();
}

function goAddPilih() {
	document.getElementById("act").value = "insertpilih";
	goSubmit();
}

function goPindah(elem) {
	var kelbaru=document.getElementById("kelompokbaru").value;
	document.getElementById("act").value = "pindahnim";
	document.getElementById("subkey").value = elem.id;
	if(kelbaru=='')
		alert('Mohon Pilih kelompok tujuan');
	else
		goSubmit();
	
}



function goPindahPilih() {
	var kelbaru=document.getElementById("kelompokbaru").value;
	document.getElementById("act").value = "pindahpilih";
	if(kelbaru=='')
		alert('Mohon Pilih kelompok tujuan');
	else
		goSubmit();
}

function goDelete(elem) {
	document.getElementById("act").value = "delete";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

</script>
</body>
</html>
