<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
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
	$p_title = 'Data Mahasiswa Pengisi Quisioner';
	$p_tbwidth = 700;
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'mn_quisioner';
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if(empty($r_key))
		Route::navigate($p_listpage);
	$key=explode('|',$r_key);
	// mendapatkan data
	$a_kelas = mKelas::getDataPararel($conn,$r_key);
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key,true,$key[5]);
	//print_r($r_key);
	$a_data = mKelas::getDataPeserta($conn,$r_key);
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
		<div style="float:left; width:15%">
			<? //require_once('inc_sidemenudosen.php');?>
			.
			</div>
		<div style="float:left; width:50%">
			
			<form name="pageform" id="pageform" method="post">
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				
				
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
						<th>Link</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $r_key."|".$row['nim'];
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						
						<td align="center"><?= $row['nim'] ?></td>
						<td><?= $row['nama'] ?></td>
						<td><img id="<?= $t_key ?>" title="Hasil Quisioner" src="images/link.png" onclick="goPop('popMenu',this,event)" style="cursor:pointer"></td>
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
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table width="130" class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('key','<?= Route::navAddress('view_hasilquiz') ?>')">Hasil Quisioner</td>
    </tr>
</table>
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


</script>
</body>
</html>
