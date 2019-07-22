<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_insert = false;
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// hak akses manual :D
	if(Akademik::isDosen() or Akademik::isPPA()) {
		$c_insert = false;
		$c_edit = false;
		$c_delete = false;
	}
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Peserta Kelas';
	$p_tbwidth = "100%";
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_absensi';
	
	// variabel request
	$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
	
	list($thnkurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$r_key);
	$r_key=$thnkurikulum.'|'.$kodemk.'|'.$kodeunit.'|'.$periode.'|'.$kelasmk;
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	$r_semester = Modul::setRequest($_POST['semester'],'SEMESTER');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_unitadd = Modul::setRequest($_POST['unitadd'],'UNIT');
	$r_kelas = Modul::setRequest($_POST['kelasbaru']);
	
	
	$r_periode = $r_tahun.$r_semester;
	
	// combo
	$l_semester = uCombo::semester($r_semester,true,'semester','onchange="goSubmit()"',false);
	$l_tahun = uCombo::tahun($r_tahun,true,'tahun','onchange="goSubmit()"',false);
	$l_unitadd = uCombo::unit($conn,$r_unitadd,'unitadd','onchange="goSubmit()"',false);
	
	$a_mahasiswa = mCombo::mahasiswa($conn,$r_unitadd,$r_periode);
	$l_mahasiswa = UI::createSelect('nim',$a_mahasiswa,'','ControlStyle',true,'style="width:300px"',true,'-- Semua Mahasiswa --');
	
	$r_act = $_POST['act'];
	if($r_act == 'insertnim' and $c_insert) {
		$record = mKelas::getKeyRecord($r_key);
		
		$r_npm = CStr::removeSpecial($_POST['nimbaru']);
		$a_bayar = mPerwalian::getDataSudahBayarNPM($conn,$record['periode'],$r_npm);
		
		if($a_bayar[$r_npm]) {
			$record['nim'] = CStr::cStrNull($r_npm);
			
			list($p_posterr,$p_postmsg) = mKRS::insertRecord($conn,$record,true);
		}
		else {
			$p_posterr = -1;
			$p_postmsg = 'Mahasiswa '.$r_npm.' belum membayar SPP';
		}
	}
	else if($r_act == 'insertpilih' and $c_insert) {
		$record = mKelas::getKeyRecord($r_key);
		
		$r_npm = $_POST['nim'];
		
		$a_npm = array();
		if(empty($r_npm)) {
			foreach($a_mahasiswa as $t_npm => $t_label)
				$a_npm[] = $t_npm;
		}
		else
			$a_npm[] = $r_npm;
		
		$ok = true;
		$conn->BeginTrans();
		
		$a_bayar = mPerwalian::getDataSudahBayarNPM($conn,$record['periode'],$a_npm);
		foreach($a_bayar as $t_npm => $t_true) {
			$record['nim'] = $t_npm;
			
			list($p_posterr,$p_postmsg) = mKRS::insertRecord($conn,$record,true);
			
			if($p_posterr) {
				$ok = false;
				break;
			}
		}
		
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'pindahnim' and $c_edit) {
		$r_npm = CStr::removeSpecial($_POST['subkey']);
		$r_subkey = $r_key.'|'.$r_npm;
		
		$record = array();
		$record['kelasmk'] = CStr::cStrNull($_POST['kelasbaru']);
		
		list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_subkey,true);
	}
	else if($r_act == 'pindahpilih' and $c_edit) {
		$r_npm = $_POST['cnpm'];
		if(!empty($r_npm)) {
			$record = array();
			$record['kelasmk'] = CStr::cStrNull($_POST['kelasbaru']);
			
			$ok = true;
			$conn->BeginTrans();
			
			foreach($r_npm as $t_npm) {
				$t_subkey = $r_key.'|'.CStr::removeSpecial($t_npm);
				
				list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$t_subkey,true);
				
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
		}
	}
	else if($r_act == 'pindahsemua' and $c_edit) {
		$record = array();
		$record['kelasmk'] = CStr::cStrNull($_POST['kelasbaru']);
		
		list($p_posterr,$p_postmsg) = mKelas::updateRecordPeserta($conn,$record,$r_key,true);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_npm = CStr::removeSpecial($_POST['subkey']);
		$r_subkey = $r_key.'|'.$r_npm;
		
		list($p_posterr,$p_postmsg) = mKRS::delete($conn,$r_subkey);
	}
	
	// mendapatkan data
	$a_kelas = mKelas::getDataPararel($conn,$r_key);
	
	list(,,,,$t_kelascek) = explode('|',$r_key);
	
	$a_kelaslain = array();
	$a_pesertakelas = array();
	foreach($a_kelas as $row) {
		$a_pesertakelas[$row['kelasmk']] = $row['jumlahpeserta'];
		if($row['kelasmk'] != $t_kelascek)
			$a_kelaslain[$row['kelasmk']] = $row['kelasmk'];
	}
	
	$l_kelas = UI::createSelect('kelasbaru',$a_kelaslain,$r_kelas,'ControlStyle',true,'onchange="goSubmit()"',true);
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);
	
	$a_data = mKelas::getDataPeserta($conn,$r_key);
	
	
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
		<div>
			
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
						<td width="150" align="center">Pindahkan Peserta Kelas</td>
					<?	} ?>
					</tr>
					<tr>
					<?	if($c_insert) { ?>
						<th>Tambah 1 Peserta</th>
						<th>Pilih dari Angkatan</th>
					<?	}
						if($c_edit) { ?>
						<th>Masukkan Kelas Tujuan</th>
					<?	} ?>
					</tr>
					<tr valign="top" class="NoHover">
					<?	if($c_insert) { ?>
						<td>
							<strong>NIM: </strong><?= UI::createTextBox('nimbaru','','ControlStyle',10,10) ?>
							<div class="Break"></div>
							<input type="button" class="ControlStyle" value="Tambah Peserta" onclick="goAddNIM()">
						</td>
						<td>
							<table border="0" cellpadding="2" cellspacing="0" class="NoGrid">
								<tr class="NoHover">
									<td><strong>Periode Daftar</strong></td>
									<td><strong>:</strong></td>
									<td><?= $l_semester ?> <?= $l_tahun ?></td>
								</tr>
								<tr class="NoHover">
									<td><strong>Prodi</strong></td>
									<td><strong>:</strong></td>
									<td><?= $l_unitadd ?></td>
								</tr>
								<tr class="NoHover">
									<td><strong>Mahasiswa</strong></td>
									<td><strong>:</strong></td>
									<td><?= $l_mahasiswa ?></td>
								</tr>
							</table>
							<div class="Break"></div>
							<input type="button" class="ControlStyle" value="Tambah Peserta" onclick="goAddPilih()">
						</td>
					<?	}
						if($c_edit) { ?>
						<td>
							<strong>Kelas Tujuan:</strong> <?= $l_kelas ?>
							<div class="Break"></div>
							<strong>Yang Dipindahkan</strong>
							<div class="Break"></div>
							<input type="button" class="ControlStyle" value="Semua" onclick="goPindahSemua()">
							<input type="button" class="ControlStyle" value="Yg Dipilih" onclick="goPindahPilih()">
							<div class="Break"></div>
							<? foreach($a_pesertakelas as $t_kelas => $t_peserta) { ?>
							<div style="float:left">
							<a title="Peserta kelas <?= $t_kelas ?>" href="javascript:goKelas('<?= $t_kelas ?>')" style="color:#00F;text-decoration:none">
								<?= $t_kelas ?> (<?= $t_peserta ?>)
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
						<th width="30">No.</th>
						<?	if($c_edit) { ?>
						<th width="30">&nbsp;</th>
						<?	} ?>
						<th width="100">NIM</th>
						<th>Nama</th>
						<th>Mata Kuliah Kres</th>
						<th width="50">Nilai</th>
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
							if(substr($periode,4,1)=='0')
								$sqlcek="select akademik.f_cekkres_spa($thnkurikulum,'$periode','$kodeunit','$kodemk','$r_kelas','$t_key')";
							else
								$sqlcek="select akademik.f_cekkres($thnkurikulum,'$periode','$kodeunit','$kodemk','$r_kelas','$t_key')";
							$mkkres=$conn->GetOne($sqlcek);
							$arr_kres=explode(":",$mkkres);
							$kres_namamk=strtolower($arr_kres[0]);
							$kres_kelasmk=strtolower(str_replace(" ","",$arr_kres[1]));
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<?	if($c_edit) { ?>
						<td><input type="checkbox" name="cnpm[]" value="<?= $row['nim'] ?>"></td>
						<?	} ?>
						<td align="center"><?= $row['nim'] ?></td>
						<td><?= $row['nama'] ?></td>
						<?php if(!empty($arr_kres[0]) and $kres_namamk!=strtolower($a_infokelas['namamk']) and $kres_kelasmk!=strtolower($a_infokelas['namamk'])){ ?>
							<td width="200"><?= $arr_kres[0].' Seksi'.$arr_kres[1]?></td>
						<?php }else{ ?>
							<td width="200">&nbsp;</td>
						<?php } ?>
						<td align="center"><?= $row['nhuruf'] ?></td>
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
				<input type="hidden" name="key" id="key" value="<?= Akademik::base64url_encode($r_key) ?>">
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

function goKelas(kelas) {
	// modif key
	key = document.getElementById("key").value;
	
	arkey = key.split('|');
	arkey[4] = kelas;
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
	document.getElementById("act").value = "pindahnim";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goPindahSemua() {
	document.getElementById("act").value = "pindahsemua";
	goSubmit();
}

function goPindahPilih() {
	document.getElementById("act").value = "pindahpilih";
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
