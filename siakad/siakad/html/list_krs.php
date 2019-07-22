<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('krs'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_key = Modul::getUserName();
	else
		$r_key = CStr::removeSpecial($_REQUEST['npm']);
	
	// properti halaman
	$p_title = 'Daftar KRS yang Pernah Diambil';
	$p_tbwidth = 700;
	$p_aktivitas = 'NILAI';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mKRS;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'set' and $c_edit) {
		$t_key = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['dipakai'] = -1;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$t_key,true);
	}
	else if($r_act == 'unset' and $c_edit) {
		$t_key = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['dipakai'] = 0;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$t_key,true);
	}else if($r_act == 'setmasuk' and $c_edit) {
		$t_key = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['nilaimasuk'] = -1;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$t_key,true);
	}
	else if($r_act == 'unsetmasuk' and $c_edit) {
		$t_key = CStr::removeSpecial($_POST['key']);
		
		$record = array();
		$record['nilaimasuk'] = 0;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$t_key,true);
	}
	else if($r_act == 'delete' and $c_delete) {
		$t_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$t_key);
	}
	
	// mendapatkan data
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_data = mKRS::getDataPerSemester($conn,$r_key,$a_infomhs['periodedaftar'],true);
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
			<div style="float:left; width:18%">
				<?php require_once('inc_headermahasiswa.php'); ?>
			</div>
			<div style="float:left; width:50%">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							<? if($c_insert) { ?>
							&nbsp;<?= $p_title ?> <div class="addButton right" onClick="goNew()">+</div>
							<? } ?>
						</span>
					</div>
				</center>
				<br>
				<center>
				<?php require_once('inc_headermhs.php') ?>
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
				<center>
	<?php
		$t = 0;
		$n_data = count($a_data);
		
		foreach($a_data as $t_semester => $t_data) {
			$t++;
	?>
	<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr class="DataBG">
			<td colspan="11" align="center"><?= Akademik::getNamaPeriode($t_data[0]['periode']) ?></td>
		</tr>
		<tr>
			<th>No</th>
			<th>Kurikulum</th>
			<th>Kode MK</th>
			<th>Nama MK</th>
			<th>KelasMK</th>
			<th>SKS</th>
			<th>Nilai</th>
			<th>Dipakai</th>
			<th>Nilai Masuk</th>
			<?	if($c_edit) { ?>
			<th width="30">Edit</th>
			<?	}
				if($c_delete) { ?>
			<th width="30">Hapus</th>
			<?	} ?>
		</tr>
	<?php
			$i = 0;
			foreach($t_data as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				$t_key = $p_model::getKeyRow($row);
				
				$t_sks = $row['sks'];
				$t_tsks += $t_sks;
	?>
		<tr valign="top" class="<?= $rowstyle ?>">
			<td align="center"><?= $i ?></td>
			<td align="center"><?= $row['thnkurikulum'] ?></td>
			<td><?= $row['kodemk'] ?></td>
			<td><?= $row['namamk'] ?></td>
			<td align="center"><?= $row['kelasmk'] ?></td>
			<td align="center"><?= $t_sks ?></td>
			<td align="center"><?= empty($row['nilaimasuk']) ? '&nbsp;' : $row['nhuruf'] ?></td>
			<td align="center">
			<?	if($c_edit) { ?>
				<input type="checkbox" value="<?= $t_key ?>" onclick="goDipakai(this)"<?= empty($row['dipakai']) ? '' : ' checked' ?>>
			<?	} else if(!empty($row['dipakai'])) { ?>
				<img src="images/check.png">
			<?	} ?>
			</td>
			<td align="center">
			<?	if($c_edit) { ?>
				<input type="checkbox" value="<?= $t_key ?>" onclick="goNilaiMasuk(this)"<?= empty($row['nilaimasuk']) ? '' : ' checked' ?>>
			<?	} else if(!empty($row['nilaimasuk'])) { ?>
				<img src="images/check.png">
			<?	} ?>
			</td>
			<?	if($c_edit) { ?>
			<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
			<?	}
				if($c_delete) { ?>
			<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
			<?	} ?>
		</tr>
	<?php
			}
	?>
		<tr>
			<th colspan="5">Jumlah SKS</th>
			<th><?= $t_tsks ?></th>
			<th colspan="5">&nbsp;</th>
		</tr>
	</table>
	<?php
			if($t < $n_data) {
	?>
	<br>
	<?php
			}
		}
	?>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
			</form>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
	
function goDipakai(elem) {
	document.getElementById("act").value = (elem.checked ? 'set' : 'unset');
	document.getElementById("key").value = elem.value;
	
	goSubmit();
}
function goNilaiMasuk(elem) {
	document.getElementById("act").value = (elem.checked ? 'setmasuk' : 'unsetmasuk');
	document.getElementById("key").value = elem.value;
	
	goSubmit();
}
function goNew() {
	location.href = detailpage + "&npm=<?= $r_key ?>";
}

</script>
</body>
</html>
