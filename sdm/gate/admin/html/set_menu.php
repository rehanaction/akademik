<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('menu'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_modul = Modul::setRequest($_POST['modul'],'MODUL');
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_pkey = CStr::removeSpecial($_REQUEST['pkey']);
	
	$a_expand = explode(':',$_POST['expand']);
	
	// combo
	$l_modul = uCombo::modul($conn,$r_modul,'modul','onchange="goSubmit()"',false);
	
	// hak akses
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Daftar Menu';
	$p_tbwidth = 700;
	$p_aktivitas = 'MENU';
	
	$p_model = mMenu;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		$record = CStr::cStrFill($_POST,array('namamenu','namafile'));
		
		if(empty($r_key)) {
			$record['kodemodul'] = $r_modul;
			$record['parentmenu'] = CStr::cStrNull($_POST['parentmenu']);
			
			list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
		}
		else
			list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_key,true);
		
		// file menu
		if(!$p_posterr) {
			$t_key = $r_key;
			if(empty($t_key)) {
				$seq = $p_model::sequence;
				if(empty($seq))
					$t_key = $p_model::getRecordKey($r_key,$record);
				else
					$t_key = $p_model::getLastValue($conn);
			}
			
			// masukkan file
			$a_file = array();
			if (count($_POST['filemenu']) > 0){
				foreach($_POST['filemenu'] as $t_file) {
					$t_file = CStr::removeSpecial($t_file);
					if(!empty($t_file))
						$a_file[] = $t_file;
				}
			}
			
			list($p_posterr,$p_postmsg) = $p_model::saveFile($conn,$t_key,$a_file);
		}
		
		// akses tambahan
		if(!$p_posterr) {
			$a_akses = array();
			if (count($_POST['kodeakses']) > 0){
				foreach($_POST['kodeakses'] as $t_idx => $t_kode) {
					$t_kode = CStr::removeSpecial($t_kode);
					if(!empty($t_kode))
						$a_akses[$t_kode] = CStr::removeSpecial($_POST['namaakses'][$t_idx]);
				}
			}
			list($p_posterr,$p_postmsg) = $p_model::saveAksesKode($conn,$t_key,$a_akses);
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		// lepaskan flag edit
		$r_key = '';
	}
	else if($r_act == 'up' and $c_edit) {
		list($p_posterr,$p_postmsg) = $p_model::moveUp($r_key);
		
		// lepaskan flag edit
		$r_key = '';
	}
	else if($r_act == 'down' and $c_edit) {
		list($p_posterr,$p_postmsg) = $p_model::moveDown($r_key);
		
		// lepaskan flag edit
		$r_key = '';
	}
	
	// mengambil data
	$a_data = $p_model::getArrMenu($conn,$r_modul);
	
	// mengambil detail file
	if(!empty($r_key)) {
		$a_file = $p_model::getFile($conn,$r_key);
		$a_hak = $p_model::getAksesKodeMenu($conn,$r_key);
	}
	
	// menentukan yang termasuk id first last
	$a_first = array();
	$a_last = array();
	foreach($a_data as $row) {
		$a_last[$row['parentmenu']] = $row['idmenu'];
		if(empty($a_first[$row['parentmenu']]))
			$a_first[$row['parentmenu']] = $row['idmenu'];
	}
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Modul', 'combo' => $l_modul);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/menuedit.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							&nbsp;<?= $p_title ?>
						</span>
					</div>
				</center>
				<br>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<?	if(!empty($a_filtercombo)) { ?>
								<td valign="top">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
								<?	} ?>
								<td width="70" id="be_save" class="TDButton" onclick="saveData()">
									<img src="images/disk.png"> Simpan
								</td>
							</tr>
						</table>
					</div>
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
<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0">
	<tr valign="top">
		<td width="55%" style="height:115px">
<table width="100%" cellpadding="4" cellspacing="0" class="MenuStyle">
<?php
	$a_namamenu = array();
	foreach($a_data as $row) {
		$t_id = $row['idmenu'];
		$t_trclass = '';
		$t_tdclass = 'ItemLv'.$row['levelmenu'];
		$t_imgtoggle = false;
		
		// nama menu
		$a_namamenu[$t_id] = $row['namamenu'];
		
		// cek first last
		if(in_array($t_id,$a_first))
			$t_isfirst = true;
		else
			$t_isfirst = false;
		
		if(in_array($t_id,$a_last))
			$t_islast = true;
		else
			$t_islast = false;
		
		// cek level
		if($row['levelmenu'] > 0 and !in_array($row['parentmenu'],$a_expand))
			$t_trclass .= ' Hidden';
		
		// cek parent
		$t_trid = 'item_'.$t_id;
		$t_imgid = 'img_'.$t_id;
		if(!empty($row['parentmenu']))
			$t_trid .= '_parent_'.$row['parentmenu'];
		
		// cek apakah parent
		if($row['inforight']-$row['infoleft'] > 1) {
			$c_deleteitem = false;
			
			if(in_array($t_id,$a_expand))
				$t_imgtoggle = 1;
			else
				$t_imgtoggle = 2;
		}
		else
			$c_deleteitem = $c_edit;
		
		// cek edit
		if($t_id == $r_key) {
			$rowe = $row;
			$rowe['namaparent'] = $a_namamenu[$rowe['parentmenu']];
			
			$t_tdclass .= ' Selected';
			
			// cek delete
			$c_deleteedit = $c_deleteitem;
		}
		else if($t_id == $r_pkey) {
			$rowe['parentmenu'] = $r_pkey;
			$rowe['namaparent'] = $a_namamenu[$rowe['parentmenu']];
			
			$t_tdclass .= ' Add';
		}
		
		if(!empty($t_trclass))
			$t_trclass = ' class="'.trim($t_trclass).'"';
		if(!empty($t_tdclass))
			$t_tdclass = ' class="'.trim($t_tdclass).'"';
?>
	<tr id="<?= $t_trid ?>"<?= $t_trclass ?> >
		<td<?= $t_tdclass ?>>
			<?	if($t_imgtoggle) { ?>
			<img id="<?= $t_imgid ?>_expand" class="ImgToggle<?= $t_imgtoggle == 1 ? ' Hidden' : '' ?>" src="images/expand.png" onclick="expandItem(this)" title="Expand menu">
			<img id="<?= $t_imgid ?>_collapse" class="ImgToggle<?= $t_imgtoggle == 2 ? ' Hidden' : '' ?>" src="images/collapse.png" onclick="collapseItem(this)" title="Collapse menu">
			<?	} else { ?>
			<span class="SpaceToggle"></span>
			<?	} ?>
			<?= $row['namamenu'] ?>
			<?	if($c_update) {
					if($t_islast) { ?>
			<img class="ImgRight ImgDisabled" src="images/down.png">
			<?		} else { ?>
			<img id="<?= $t_id ?>" class="ImgAct" src="images/down.png" title="Turunkan item" onclick="goDownItem(this)">
			<?		}
					if($t_isfirst) { ?>
			<img class="ImgRight ImgDisabled" src="images/up.png">
			<?		} else { ?>
			<img id="<?= $t_id ?>" class="ImgAct" src="images/up.png" title="Naikkan item" onclick="goUpItem(this)">
			<?		} ?>
			<img class="ImgRight" src="images/separator.png">
			<?	}
				if($c_delete) {
					if($c_deleteitem) { ?>
			<img id="<?= $t_id ?>" class="ImgAct" src="images/delete.png" title="Hapus item" onclick="goDeleteItem(this)">
			<?		} else { ?>
			<img id="<?= $t_id ?>" class="ImgRight ImgDisabled" src="images/delete.png">
			<?		}
				} ?>
			<img id="<?= $t_id ?>" class="ImgAct" src="images/edit.png" title="Edit item" onclick="goEditItem(this)">
			<?	if($c_insert and $row['levelmenu'] < 2) { ?>
			<img class="ImgRight" src="images/separator.png">
			<img id="<?= $t_id ?>" class="ImgAct" src="images/child.png" title="Buat sub-menu" onclick="goAddItem(this)">
			<?	} ?>
		</td>
	</tr>
<?	} ?>
</table>
		</td>
		<td id="td_fix" style="padding-left:10px">
<div id="div_fix"<?= $conn->debug ? '' : ' style="position:fixed"' ?>>
	<table width="305" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr>
			<td colspan="2" align="center" class="HeaderBG<?= empty($r_key) ? ' Add' : ' Selected' ?>">
				<? if(empty($r_key)) { ?>
				Tambah Menu Item
				<? } else { ?>
				Edit Menu Item
				<? } ?>
			</td>
		</tr>
		<tr>
			<td width="100" class="LeftColumnBG">Label Parent</td>
			<td class="RightColumnBG">
				<input type="hidden" name="parentmenu" id="parentmenu" value="<?= $rowe['parentmenu'] ?>">
				<span id="span_parent"><?= empty($rowe['parentmenu']) ? '(Tidak memiliki parent)' : $rowe['namaparent'] ?></span>
			</td>
		</tr>
		<tr>
			<td class="LeftColumnBG">Label Menu</td>
			<td class="RightColumnBG"><?= UI::createTextBox('namamenu',$rowe['namamenu'],'ControlStyle',30,30,$c_update) ?></td>
		</tr>
		<tr>
			<td class="LeftColumnBG">File Menu</td>
			<td class="RightColumnBG"><?= UI::createTextBox('namafile',$rowe['namafile'],'ControlStyle',30,30,$c_update) ?></td>
		</tr>
	</table>
<?	if(!empty($r_key)) { ?>
	<br>
	<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr>
			<td colspan="2" align="center" class="HeaderBG">File Pendukung</td>
		</tr>
<?		foreach($a_file as $rowf) { ?>
		<tr>
			<td width="100" class="LeftColumnBG">Nama File</td>
			<td class="RightColumnBG"><?= UI::createTextBox('filemenu[]',$rowf['filemenu'],'ControlStyle',30,30,$c_update) ?></td>
		</tr>
<?		}
		if($c_edit) { ?>
		<tr id="tr_filetpl" style="display:none">
			<td width="100" class="LeftColumnBG">Nama File</td>
			<td class="RightColumnBG"><?= UI::createTextBox('filemenu[]','','ControlStyle',30,30,$c_update) ?></td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="HeaderBG"><input type="button" value="Tambah" onclick="goAddFile()"></td>
		</tr>
<?		} ?>
	</table>
	<br>
	<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr>
			<td colspan="2" align="center" class="HeaderBG">Hak Akses Tambahan</td>
		</tr>
<?		foreach($a_hak as $t_kode => $t_hak) { ?>
		<tr>
			<td width="100" class="LeftColumnBG">Kode &amp; Hak Akses</td>
			<td class="RightColumnBG">
				<?= UI::createTextBox('kodeakses[]',$t_kode,'ControlStyle',1,1,$c_update) ?>
				<?= UI::createTextBox('namaakses[]',$t_hak,'ControlStyle',30,24,$c_update) ?>
			</td>
		</tr>
<?		}
		if($c_edit) { ?>
		<tr id="tr_haktpl" style="display:none">
			<td width="100" class="LeftColumnBG">Kode &amp; Hak Akses</td>
			<td class="RightColumnBG">
				<?= UI::createTextBox('kodeakses[]','','ControlStyle',1,1,$c_update) ?>
				<?= UI::createTextBox('namaakses[]','','ControlStyle',30,24,$c_update) ?>
			</td>
		</tr>
		<tr>
			<td colspan="2" align="center" class="HeaderBG"><input type="button" value="Tambah" onclick="goAddHak()"></td>
		</tr>
<?		} ?>
	</table>
<?	} ?>
</div>
		</td>
	</tr>
</table>
				</center>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="expand" id="expand">
				<input type="hidden" name="scroll" id="scroll">
				<input type="hidden" name="pkey" id="pkey" value="<?= $r_pkey ?>">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.menuedit.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	loadFixed();
});

function loadFixed() {
	$("#td_fix").height($("#div_fix").height());
}

function goSubmit() {
	// mengambil yang terexpand
	var arrid;
	var arrsid = new Array();
	$("[id$='_collapse']:visible").each(function(i) {
		arrid = this.id.split("_");
		arrsid[i] = arrid[1];
	});
	
	document.getElementById("expand").value = arrsid.join(":");
	document.getElementById("scroll").value = $(document).scrollTop();
	document.getElementById("pageform").submit();
}

function saveData() {
	if(cfHighlight("namamenu")) {
		goSave();
	}
}

function goAddItem(elem) {
	document.getElementById("key").value = "";
	document.getElementById("pkey").value = elem.id;
	goSubmit();
}

function goEditItem(elem) {
	document.getElementById("key").value = elem.id;
	document.getElementById("pkey").value = "";
	goSubmit();
}

function goDeleteItem(elem) {
	var nama = jQuery.trim($(elem).parent().text());
	var hapus = confirm('Apakah anda yakin akan menghapus "'+nama+'"?');
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("key").value = elem.id;
		document.getElementById("pkey").value = "";
		goSubmit();
	}
}

function goUpItem(elem) {
	document.getElementById("act").value = "up";
	document.getElementById("key").value = elem.id;
	document.getElementById("pkey").value = "";
	goSubmit();
}

function goDownItem(elem) {
	document.getElementById("act").value = "down";
	document.getElementById("key").value = elem.id;
	document.getElementById("pkey").value = "";
	goSubmit();
}

function goAddFile() {
	$("#tr_filetpl").clone().show().insertBefore($("#tr_filetpl"));
	loadFixed();
}

function goAddHak() {
	$("#tr_haktpl").clone().show().insertBefore($("#tr_haktpl"));
	loadFixed();
}

</script>
</body>
</html>
