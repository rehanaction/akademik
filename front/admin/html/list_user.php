<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_editpass = $c_edit;
	
	// include
	require_once(Route::getModelPath('user'));
	require_once(Route::getUIPath('combo'));
	require_once($conf['helpers_dir'].'modul.class.php');
	
	// variabel request
	$r_role = Modul::setRequest($_POST['role'],'ROLE');
	
	// combo
	$role=Modul::getRole();
	//$l_role = uCombo::role($conn,$r_role,'role','onchange="goSubmit()"',$role);
	$l_role = uCombo::rolehusus($conn,$r_role,'role','onchange="goSubmit()"',$role);

	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'u.username', 'label' => 'Username');
	$a_kolom[] = array('kolom' => 'u.userdesc', 'label' => 'Nama');
	$a_kolom[] = array('kolom' => 'u.email', 'label' => 'Email');
	
	// properti halaman
	$p_title = 'Daftar User';
	$p_tbwidth = 700;
	$p_aktivitas = 'USER';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mUser;
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		$datas = $p_model::inquiryByuserid($conn,$r_key);
	
		$mood_datas =$p_model::getUserMoodle($conn,$datas['username']);
		foreach($mood_datas['users'] as $moodusers){
			$p_model::DelUserMoodle($conn,$moodusers['id']);
		}
		
		list($p_posterr,$p_postmsg) = $p_model::deleteRole($conn,$r_key);
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'resetpass' and $c_editpass) {
		$r_key = CStr::removeSpecial($_POST['key']);
			
		$err = $p_model::changePassword($conn,$r_key);
		$p_posterr = $err;
		$p_postmsg = 'Reset password '.($err ? 'gagal' : 'berhasil');
		if ( !$err ){
			$user=$p_model::getUser($conn,$r_key);
			if ( $p_model::isLdapUser($conn, $user["username"] ) ){
				$newpasswd=$user['hints'];
				$ldap=new Ldap(); 
				$ldap->userUpdatePassword($user["username"], $newpasswd, $err);
			}
		}
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	// mendapatkan data
	if(!empty($r_role)) $a_filter[] = $p_model::getListFilter('role',$r_role);
	
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	//$a_data = $p_model::inquiryDataUser($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Role', 'combo' => $l_role);
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
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<?	/************************/
									/* COMBO FILTER HALAMAN */
									/************************/
									
									if(!empty($a_filtercombo)) {
								?>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
								<?	}
									
									/**********************/
									/* COMBO FILTER KOLOM */
									/**********************/
									
									if(!empty($r_page)) {
								?>
								<td valign="top" width="50%">
									<?php require_once('inc_listcari.php'); ?>
								</td>
							<?	} ?>
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
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<? 	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
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
						?>
						<th id="<?= $datakolom['kolom'] ?>" align="center" class="HeaderBG"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_editpass or $c_edit or $c_delete) { ?>
						<th align="center" class="HeaderBG" width="70">Aksi</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>" onMouseOver="this.style.backgroundColor='#FFDAB9'" onMouseOut="this.style.backgroundColor=''">
						
						<?	foreach($rowc as $rowcc) { ?>					
								<td><?= $rowcc ?></td>
						<?	}
							if($c_editpass or $c_edit or $c_delete) { ?>
						<td align="center">
						<?		if($c_editpass) { ?>
							<img id="<?= $t_key ?>" title="Reset Password" src="images/keydelete.png" onclick="goResetPass(this)" style="cursor:pointer">
						<?		}
								if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		}
								if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?		} ?>
						</td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<? /*
						<div style="float:left">
							Halaman <?= $r_page ?>/<?= $rs->LastPageNo() ?> - Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Jumlah Data: <?= $rs->maxRecordCount() ?>
						</div>
						*/ ?>
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	// handle refresh
	$(document).keydown(function(e) {
		var ev = (window.event) ? window.event : e;
		var key = (ev.keyCode) ? ev.keyCode : ev.which;
		
		if(key == 116) {
			document.getElementById("act").value = "";
			goSubmit();
			
			return false;
		}
		
		return true;
	})
	<? } ?>
});

function goResetPass(elem) {
	var reset = confirm("Apakah anda yakin akan mereset password user ini?");
	if(reset) {
		document.getElementById("act").value = "resetpass";
		document.getElementById("key").value = elem.id;
		goSubmit();
	}
}

</script>
</body>
</html>
