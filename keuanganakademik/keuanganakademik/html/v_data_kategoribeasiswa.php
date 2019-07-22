<?php
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post"<?= $isupload ? ' enctype="multipart/form-data"' : '' ?>>
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr))
						require_once('inc_databutton.php');
					
					if(!empty($p_postmsg)) {
				?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
					if(empty($p_fatalerr)) {
				?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<?	$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
								//echo Page::getDataTR($t_row,$t_row['id']);
						
					?>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
								<span id="edit" style="display:none"><?= $t_row['input'] ?></span>
							</td>
						</tr>
					<?	} ?>
					</table>
					</div>
					<?php if(!empty($r_key)) { ?>
					<br />
					<span id="edit" style="display:none">
					</span>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/MASTER.png" onerror="loadDefaultActImg(this)"> <h1>Daftar Potongan Beasiswa</h1>
							</div>
						</div>
					</header>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
						<tr>
							<th>No</th>
							<th>Jenis Tagihan</th>
							<th>Nilai Potongan</th>
							<th>Jenis Potongan</th>
							<?php if($c_update) { ?>
							<th width="30" id="edit" style="display:none">Aksi</th>
							<?php } ?>
						</tr>
						<?php foreach($a_potongan as $i => $rowp) { ?>
						<tr>
							<td><?php echo ++$i ?></td>
							<td><?php echo $rowp['namajenistagihan'] ?></td>
							<td align="right"><?php echo CStr::formatNumber($rowp['jumlahpotongan']) ?></td>
							<td><?php echo (empty($rowp['ispersen'])) ? 'Rupiah' : 'Persentase' ?></td>
							<?php if($c_update) { ?>
							<td align="center" id="edit" style="display:none"><img id="<?= $rowp['jenistagihan'] ?>" title="Hapus Potongan" src="images/delete.png" onclick="goDeletePotongan(this)" style="cursor:pointer"></td>
							<?php } ?>
						</tr>
						<?php } ?>
						<?php if($c_update) { ?>
						<tr id="edit" style="display:none">
							<td>*</td>
							<td><?php echo UI::createSelect('pot_jenis',$a_jenistagihan,'','ControlStyle') ?></td>
							<td align="right"><?php echo UI::createTextBox('pot_jumlah','','ControlStyle',17,17) ?></td>
							<td><?php echo UI::createCheckBox('pot_persen',array('1' => 'Persentase')) ?></td>
							<td align="center"><img title="Tambah Potongan" src="images/add.png" onclick="goAddPotongan()" style="cursor:pointer"></td>
						</tr>
						<?php } ?>
					</table>
					<?php } ?>
				</center>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="keydet" id="keydet">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
		
	$("img").not("[src]").each(function() {
		this.src = "index.php?page=img_datathumb&type="+this.id+"&id="+document.getElementById("key").value;
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

<?php if($c_update) { ?>

function goAddPotongan() {
	$("#act").val("addpotongan");
	
	goSubmit();
}

function goDeletePotongan(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus potongan beasiswa ini?");
	if(hapus) {
		$("#keydet").val($(elem).attr("id"));
		$("#act").val("deletepotongan");
		
		goSubmit();
	}
}

<?php } ?>

</script>
</body>
</html>
