<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('userguide'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// properti halaman
	$p_title = 'Daftar User Guide';
	$p_tbwidth = 900;
	$p_aktivitas = 'UNIT';

	$p_model = mUserguide;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;

	//getroleuntuk optionnya
	$role=$p_model::getRole($conn);

	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'koderole', 'label' => 'Role', 'notnull' => true, 'type'=>'S', 'option' => $role);
	$a_kolom[] = array('kolom' => 'fileuserguide', 'label' => 'File','type' => 'U', 'uptype' => $p_model::uptype, 'size' => 40);


	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($post,$record) = uForm::getPostRecord($a_kolom,$_POST);

		$record['koderole'] = $_POST['i_koderole'];
		$record['fileuserguide'] = $_POST['i_fileuserguide'];
		if(empty($p_posterr)) {
			$_FILES['fileuserguide'] = $_FILES['i_fileuserguide'];
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_kolom,$record,$r_key);
		}

		if(!$p_posterr) unset($post);

	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);

		if(empty($p_posterr)) {
			$_FILES['fileuserguide'] = $_FILES['u_fileuserguide'];
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_kolom,$record,$r_key);
		}

		if(!$p_posterr) unset($post);

	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);

		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	else if($r_act == 'deletefile' and $c_edit){
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$_POST['key'],'file'.'userguide');
	}
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);

	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);

	//require_once($conf['view_dir'].'inc_ms.php');
?>
<?php
	$p_colnum = count($a_kolom)+2;
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forinplace.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/

					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}

					/************************/
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
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
										</tr>
										<? } ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	} ?>

				<?php if(!empty($a_salin)) { ?>
					<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;  margin-top:10px">
						<table width="<?= $p_tbwidth-100 ?>" cellpadding="5" cellspacing="0" style="text-align:center" >
							<tr>
								<td colspan="2" align="center;"><strong><?=$a_salin['title']?></strong></td>
							</tr>
							<tr>
								<td valign="top" width="50%"> <strong><?=$a_salin['label']?></strong>&nbsp; &nbsp; &nbsp;  <?=$a_salin['tujuan']?></td>
								<td align="left"> <input type="button" value="Salin" onclick="goSalin()"> </td>
							</tr>
						</table>
					</div>
					</center>
					<br>
				<?php } ?>

				<?php	if(!empty($p_postmsg)) { ?>
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
							$t_key = $row['real_koderole'];

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
						<td align="center" colspan="2">
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else {
								$rowc = Page::getColumnRow($a_kolom,$row);

					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	//foreach($rowc as $rowcc) {
							$file = explode('<div class="Break">',$rowc[1]);
							$file[0] = str_replace("'".$p_model::uptype."'","'".$p_model::uptype."','$t_key'",$file[0]);
						?>
						<td><?= $rowc[0] ?></td>
						<td>
							<?=$file[0]?>
							<div class="Break"></div>
							<? if(!empty($file[0])){ ?>
								<u class="ULink" onclick="goDeleteFile('fileuserguide','<?=$row['real_koderole']?>')">Hapus file</u>
							<? } ?>
						</td>
						<?		//}
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

	// handle focus
	// $("[id^='i_']:first").focus();

	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
<? if($a_salin) { ?>
function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}
<? } ?>


function goDeleteFile(elem,koderole) {
	var hapus = confirm("Apakah anda yakin akan menghapus file ini?");
	if(hapus) {
		document.getElementById("act").value = "deletefile";
		document.getElementById("key").value = koderole;
		goSubmit();
	}
}

</script>
</body>
</html>
