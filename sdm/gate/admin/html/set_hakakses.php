<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('menu'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_modul = Modul::setRequest($_POST['modul'],'MODUL');
	$r_role = Modul::setRequest($_POST['role'],'ROLE');
	
	$a_expand = explode(':',$_POST['expand']);
	
	// combo
	$l_modul = uCombo::modul($conn,$r_modul,'modul','onchange="goSubmit()"',false);
	$l_role = uCombo::role($conn,$r_role,'role','onchange="goSubmit()"',false);
	
	// properti halaman
	$p_title = 'Hak Akses Menu';
	$p_tbwidth = 520;
	$p_aktivitas = 'AKSES';
	
	$p_model = mMenu;
	
	// array hak akses
	$a_aksesmenu = $p_model::aksesMenu();
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		if(!empty($_POST['click'])) {
			$a_akses = array();
			$a_click = explode('|',$_POST['click']);
			
			foreach($a_click as $t_click) {
				list($t_id,$t_akses,$t_val) = explode('_',$t_click);
				
				$t_id = CStr::removeSpecial($t_id);
				$t_akses = CStr::removeSpecial($t_akses);
				$t_key = (empty($a_aksesmenu[$t_akses]) ? 'E' : 'I'); // hak akses dasar atau tidak
				
				$a_akses[$t_id][$t_key][$t_akses] = $t_val;
			}
			
			list($p_posterr,$p_postmsg) = $p_model::saveAksesRole($conn,$r_role,$a_akses);
		}
	}
	
	// mengambil data
	$a_data = $p_model::getArrMenu($conn,$r_modul);
	$a_akseskode = $p_model::getAksesKode($conn,$r_modul);
	$a_aksesrole = $p_model::getAksesRole($conn,$r_modul,$r_role);
	
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
	$a_filtercombo[] = array('label' => 'Role', 'combo' => $l_role);
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
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="MenuStyle">
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
			if(in_array($t_id,$a_expand))
				$t_imgtoggle = 1;
			else
				$t_imgtoggle = 2;
		}
		
		if(!empty($t_trclass))
			$t_trclass = ' class="'.trim($t_trclass).'"';
		if(!empty($t_tdclass))
			$t_tdclass = ' class="'.trim($t_tdclass).'"';
?>
	<tr id="<?= $t_trid ?>"<?= $t_trclass ?> >
		<td<?= $t_tdclass ?> width="200">
			<?	if($t_imgtoggle) { ?>
			<img id="<?= $t_imgid ?>_expand" class="ImgToggle<?= $t_imgtoggle == 1 ? ' Hidden' : '' ?>" src="images/expand.png" onclick="expandItem(this)" title="Expand menu">
			<img id="<?= $t_imgid ?>_collapse" class="ImgToggle<?= $t_imgtoggle == 2 ? ' Hidden' : '' ?>" src="images/collapse.png" onclick="collapseItem(this)" title="Collapse menu">
			<?	} else { ?>
			<span class="SpaceToggle"></span>
			<?	} ?>
			<?= $row['namamenu'] ?>
		</td>
		<td style="background:#f7d286" >
			<input type="checkbox" name="checkall_<?=$t_id?>" id="checkall_<?=$t_id?>" onchange="selectAll(<?=$t_id?>)" />
		</td>
		<td>
		<?	$t_akses = $a_aksesrole[$t_id];
			$t_aksesmenu = $a_aksesmenu;
			if(!empty($a_akseskode[$t_id]))
				$t_aksesmenu += $a_akseskode[$t_id];
			
			$i=0;
			foreach($t_aksesmenu as $t_kode => $t_hak) {
				$t_idc = $t_id.'_'.$t_kode;
				$i++;
				if($i%5 == 0){
					echo '<br>';
					$i=0;
				}
		?>
			<input type="checkbox" id="<?= $t_idc ?>" value="1"<?= empty($t_akses[$t_kode]) ? '' : ' checked' ?> onclick="logClick(this)"> <label for="<?= $t_idc ?>"><?= $t_hak ?></label>
		<?	} ?>
		</td>
	</tr>
<?	} ?>
</table>
				</center>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="expand" id="expand">
				<input type="hidden" name="scroll" id="scroll">
				<input type="hidden" name="click" id="click">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.menuedit.js"></script>
<script type="text/javascript">
	
var hakclick = new Array();

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

function logClick(elem) {
	hakclick[elem.id] = (elem.checked ? 1 : 0);
}

function saveData() {
	var arrclick = new Array();
	for(x in hakclick)
		arrclick.push(x+'_'+hakclick[x]);
	
	document.getElementById("click").value = arrclick.join("|");
	goSave();
}

function selectAll(id){
	if (document.getElementById("checkall_"+id).checked==true) {
		document.getElementById(id+"_read").checked=true;
		document.getElementById(id+"_insert").checked=true;
		document.getElementById(id+"_update").checked=true;
		document.getElementById(id+"_delete").checked=true;
	}else{
		document.getElementById(id+"_read").checked=false;
		document.getElementById(id+"_insert").checked=false;
		document.getElementById(id+"_update").checked=false;
		document.getElementById(id+"_delete").checked=false;
	}
	
	logClick(document.getElementById(id+"_read"));
	logClick(document.getElementById(id+"_insert"));
	logClick(document.getElementById(id+"_update"));
	logClick(document.getElementById(id+"_delete"));
}
</script>
</body>
</html>
