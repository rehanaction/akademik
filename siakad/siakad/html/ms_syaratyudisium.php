<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('syaratyudisium'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_periode = Modul::setRequest($_POST['periode'],'PERIODEWISUDA');
	$r_periodecopy = Modul::setRequest($_POST['periodecopy']);
	
	// combo
	$l_periode = uCombo::periodeWisuda($conn,$r_periode,'periode','onchange="goSubmit()"',false);
	$l_periodecopy = uCombo::periodeWisuda($conn,$r_periodecopy,'periodecopy','',false);
	
	// properti halaman
	$p_title = 'Syarat Yudisium';
	$p_tbwidth = 550;
	$p_aktivitas = 'WISUDA';
	
	$p_model = mSyaratYudisium;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No', 'width' => 20);
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Syarat', 'size' => 70, 'maxlength' => 400, 'notnull' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan kurikulum
		$a_kolom[] = array('kolom' => 'idyudisium', 'value' => $r_periode);
		$a_kolom[] = array('kolom' => 'role', 'value' => $_POST['role']);
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi kurikulum dan progpend
		array_pop($a_kolom);
		array_pop($a_kolom);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		$a_kolom[] = array('kolom' => 'role', 'value' => $_POST['role_'.$_POST['key']]);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
		array_pop($a_kolom);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'copy' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::copy($conn,$r_periode,$r_periodecopy);
		
		// load combo kurikulum
		if(!$p_posterr) {
			$r_periode = Modul::setRequest($r_periodecopy,'PERIODEWISUDA');
			$l_periode = uCombo::periodeWisuda($conn,$r_periode,'periode','onchange="goSubmit()"',false);
		}
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	if(!empty($r_periode)) $a_filter[] = $p_model::getListFilter('periodewisuda',$r_periode);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Periode', 'combo' => $l_periode);
	
	$rs_role = $conn->Execute("select koderole, namarole from gate.sc_role order by koderole");
	$arr_role = array();
	while($row = $rs_role->FetchRow()){
		$arr_role[$row['koderole']] = $row['namarole'];
	}
	$rs_role->MoveFirst();
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
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? $t_filter = $a_filtercombo[0]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
											<td width="200" style="white-space:nowrap"><strong>Salin ke Periode : </strong><?= $l_periodecopy ?> <input type="button" value="Salin" onclick="goSalin()"></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
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
					<header style="width:<?= $p_tbwidth+300 ?>px">
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
				<table width="<?= $p_tbwidth+300 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
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
						<?	}?>
							<th id="role">Role yang Berhak</th>
						<?
							if($c_edit) { ?>
						<th width="30">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							if($t_key == $r_edit and $c_edit) {
								$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);
								
								$a_updatereq = array();
					?>
					<tr valign="top" class="AlternateBG2">
						<?		foreach($rowc as $rowcc) {
									if($rowcc['notnull'])
										$a_updatereq[] = $rowcc['id'];
						?>					
						<td><?= $rowcc['input'] ?></td>
						<?		} ?>
						<td align="center">
							<select name="role_<?= $row['idsyaratyudisium']?>" id="role_<?= $row['idsyaratyudisium']?>">
								<?while($row = $rs_role->FetchRow()){?>
									<option value="<?=$row['koderole']?>"><?= $row['namarole']?></option>
								<?}$rs_role->MoveFirst();?>
							</select>
						</td>
						<td align="center" colspan="2">
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else {
								$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?		foreach($rowc as $rowcc) { ?>					
						<td><?= $rowcc ?></td>
						<?		}?>
						<td>
							<?= $arr_role[$row['role']]?>
						</td>
						<?
								if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(this)" style="cursor:pointer"></td>
						<?		}
								if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?		} ?>
					</tr>
					<?		}
						}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						if($c_insert) { ?>
					<tr valign="top" class="LeftColumnBG NoHover">
						<?	$rowc = Page::getColumnEdit($a_kolom,'i_','onkeydown="etrInsert(event)"');
							
							$a_insertreq = array();
							foreach($rowc as $rowcc) {
								if($rowcc['notnull'])
									$a_insertreq[] = $rowcc['id'];
						?>					
						<td><?= $rowcc['input'] ?></td>
						<?	} ?>
						<td align="center">
							<select name="role" id="role">
								<?while($row = $rs_role->FetchRow()){?>
									<option value="<?=$row['koderole']?>"><?= $row['namarole']?></option>
								<?}?>
							</select>
						</td>
						<td align="center" colspan="2">
							<img title="Tambah Data" src="images/disk.png" onclick="goInsert()" style="cursor:pointer">
						</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
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

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}

</script>
</body>
</html>