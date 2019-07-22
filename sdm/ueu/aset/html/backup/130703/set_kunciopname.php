<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('set_kunciopname');
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('kunciopname'));
	require_once(Route::getUIPath('combo'));
	
	$_SESSION['SCROLL_KUNCIOPNAME'] = $_REQUEST['scroll'];
	echo $r_idunit;
	// variabel request
	$r_idunit = CStr::removeSpecial($_REQUEST['idunit']);
	$r_kodeunit = CStr::removeSpecial($_REQUEST['kodeunit']);
	$r_act = CStr::removeSpecial($_REQUEST['act']);
	
	// properti halaman
	$p_title = 'Kunci Opname';
	$p_tbwidth = 550;
	$p_aktivitas = 'Kunci Opname';
	
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit', 'width' => 75, 'align' => 'center');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit');
	
	$p_colnum = count($a_kolom)+1;
	echo $c_edit;
	if($c_edit and $r_act == 'kunci' and !empty($r_idunit)){
		$conn->Execute("insert into aset.as_kunciopname (idunit) values ($r_idunit)");
		
		if($conn->ErrorNo() != 0){
			$msg = '<font color="red">Proses Kunci Opname Gagal !</font>';
		} else {
			$msg = '<font color="blue">Proses Kunci Opname Berhasil</font>';
		}
	} else if($c_edit and $r_act == 'buka' and !empty($r_idunit)) {
		$conn->Execute("delete from aset.as_kunciopname where idunit = $r_idunit");
		
		if($conn->ErrorNo() != 0){
			$msg = '<font color="red">Proses Buka Kunci Opname Gagal !</font>';
		} else {
			$msg = '<font color="blue">Proses Buka Kunci Opname Berhasil</font>';
		}
	}
	
	$sql = "select u.idunit, u.kodeunit, u.namaunit, u.level, k.idunit as unit
			from aset.ms_unit u left join aset.as_kunciopname k on u.idunit = k.idunit order by idunit";
    $rs = $conn->Execute($sql);					
	print_r($_POST);
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
<body onload="initScroll()">
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<center><p><?= empty($msg) ? '' : '<b>'.$msg.'</b>' ?></p></center>
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
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="40">Aksi</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0; $iskunci = false;
						while($row = $rs->FetchRow()) {
								if ($i % 2) $rowStyle = 'NormalBG';  else $rowStyle = 'AlternateBG'; $i++;
								$iskunci = empty($row['unit']) ? false : true;
						?>
							<tr class="<?= $rowStyle ?>" height="27"> 
								<td align="center"><?= $row['kodeunit'] ?></td>
								<td style="padding-left:<?= (int)$row['level']*15 ?>px"><?= $row['namaunit'] ?></td>
								<td align="center">
									<? if($iskunci){ ?>
									<img id="<?= $row['idunit'] ?>" title="Buka Opname" src="images/lock.png" onclick="goBuka(<?= $row['idunit'] ?>)" style="cursor:pointer">
									<? }else{ ?>
									<img id="<?= $row['idunit'] ?>" title="Kunci Opname" src="images/unlock.png" onclick="goKunci(<?= $row['idunit'] ?>)" style="cursor:pointer">
									<? } ?>
								</td>
							</tr>
						<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				<input type="hidden" name="scroll" id="scroll">
				<input type="hidden" name="idunit" id="idunit">
				<input type="hidden" name="act" id="act">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
function goSubmit() {
	$('#scroll').val(document.body.scrollTop);
	$('#pageform').submit();
}

function initScroll() {
	document.body.scrollTop = <?= $_SESSION['SCROLL_KUNCIOPNAME'] == '' ? '0' : $_SESSION['SCROLL_KUNCIOPNAME'] ?>;
}

function goKunci(idunit) {
	$('#idunit').val(idunit);
	$('#act').val('kunci');
	goSubmit();
}

function goBuka(idunit) {
	$('#idunit').val(idunit);
	$('#act').val('buka');
	goSubmit();
}
</script>
</body>
</html>
