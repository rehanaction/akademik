<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_dispen = $a_auth['canother']['D'];
	
	// hak akses manual :D
	if(Akademik::isDosen()) {
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
	$p_tbwidth = 700;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_absensi';
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
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
	
	$l_kelas = UI::createSelect('kelasbaru',$a_kelaslain,'','ControlStyle');
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
				<div class="DivError" style="display:none"></div>
				<div class="DivSuccess" style="display:none"></div>
				<div class="Break"></div>
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
						<th width="100">NIM</th>
						<th>Nama</th>
						<th width="50">Nilai</th>
						
						<th width="30">Bayar UTS ?</th>
						<th width="30">Bayar UAS ?</th>
						<th width="30">Pros. Asbsen</th>
						<th width="30">Boleh UTS ?</th>
						
						<th width="30">Boleh UAS ?</th>
					
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $row['nim'];
							
							$t_absenmhs = $conn->GetOne("select akademik.f_absensi(".$row['thnkurikulum'].",'".$row['periode']."','".$row['kodeunit']."','".$row['kodemk']."','".$row['kelasmk']."','".$row['nim']."','".$row['kelompok_prak']."','".$row['kelompok_tutor']."')");
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						
						<td align="center"><?= $row['nim'] ?></td>
						<td><?= $row['nama'] ?></td>
						<td align="center"><?= $row['nhuruf'] ?></td>
						<td align="center"><?= !empty($row['isuts'])?'<img src="images/check.png">':'' ?></td>
						<td align="center"><?= !empty($row['isuas'])?'<img src="images/check.png">':'' ?></td>
						<td align="center"><?= $t_absenmhs ?>%</td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['isikututs']==-1)?'checked':''?> title="<?=($row['isikututs']==0)?'Lepas Cekal':'Cekal'?>" onclick="bukaUTS(this)" <?=!$c_dispen?'disabled':''?>></td>
						<td align="center"><input type="checkbox" id="<?=$t_key?>" <?=($row['isikutuas']==-1)?'checked':''?> title="<?=($row['isikutuas']==0)?'Lepas Cekal':'Cekal'?>" onclick="bukaUAS(this)" <?=!$c_dispen?'disabled':''?>></td>
						
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

function sukses(msg){
	$(".DivSuccess").html(msg);
	$(".DivSuccess").show();
	$(".DivSuccess").fadeOut(2000);
}
function gagal(msg){
	$(".DivError").html(msg);
	$(".DivError").show();
	$(".DivError").fadeOut(2000);
}
function bukaUTS(elem){
	if(elem.checked){
		var posted = "f=setUTS&q[]="+elem.id+"&q[]=<?= $r_key ?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}else if(!elem.checked){
		var posted = "f=unsetUTS&q[]="+elem.id+"&q[]=<?= $r_key ?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}
}
function bukaUAS(elem){
	if(elem.checked){
		var posted = "f=setUAS&q[]="+elem.id+"&q[]=<?= $r_key ?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}else if(!elem.checked){
		var posted = "f=unsetUAS&q[]="+elem.id+"&q[]=<?= $r_key ?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var msg=text.split('|');
			if(msg[0]==''){
				sukses(msg[1]);
			}else{
				gagal(msg[1]);
			}
		});
	}
}
</script>
</body>
</html>
