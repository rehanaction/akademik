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
	<script type="text/javascript" src="scripts/forpager.js"></script>
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
					
					if(empty($p_fatalerr)) {
				?>
				<table border="0" cellspacing="10" align="center">
					<tr>
						<?	if($c_readlist) { ?>
						<td id="be_list" class="TDButton" onclick="goList()">
							<img src="images/list.png"> Daftar
						</td>
						<?	} if($c_insert) { ?>
						<td id="be_add" class="TDButton" onclick="goNew()">
							<img src="images/add.png"> Data Baru
						</td>
						<?	} if($c_edit) { ?>
					   <td id="be_edit" class="TDButton" onclick="goEdit()">
							<img src="images/edit.png"> Sunting
						</td>
						<td id="be_save" class="TDButton" onclick="goSave()" style="display:none">
							<img src="images/disk.png"> Simpan
						</td>
						<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
							<img src="images/undo.png"> Batal
						</td>
						<?	} if($c_delete and !empty($r_key)) { ?>
						<td id="be_delete" class="TDButton" onclick="goDelete()">
							<img src="images/delete.png"> Hapus
						</td>
						<?	} ?>
						<td id="be_print" class="TDButton" onclick="showPage(null,'<?= Route::navAddress('rep_payment') ?>')">
							<img src="images/print.png" width="16"> Cetak
							<input type="hidden" id="idpembayaran" name="idpembayaran" value="D<?php echo $r_key ?>">
						</td>
					</tr>
				</table>
				<?php
					}
					
				if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}
				
					if(empty($p_fatalerr)) { ?>
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
						$a_xauto = array();
						
						foreach($row as $t_row) {
							if($t_row['skip'])
								continue;
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							if($t_row['xauto'])
								$a_xauto[] = $t_row['xauto'];
					?>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
								<span id="edit" style="display:none">
								<? if($t_row['id'] == 'bulantahun'){
									echo UI::createSelect('bulan',$arr_bulan,substr($t_row['realvalue'],5,2),'ControlStyle',$r_key?false:true,'',true,'');
									echo ' '.UI::createSelect('tahun',$arr_tahun,substr($t_row['realvalue'],0,4),'ControlStyle',$r_key?false:true,'',true,'');
									 }else {?>
                                	<?= $t_row['input'] ?>
                                <? }?>
                                </span>
							</td>
						</tr>
					<?	} ?>
					</table>
				</div>
				</center>
				<input type="hidden" name="act" id="act"> 
                <input type="hidden" name="cek" id="cek" value="<?=$r_key?>">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?		
					} ?>
			</form>
		</div>
	</div>
</div>

<?	if(!empty($a_xauto)) { ?>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<?	} ?>
<script type="text/javascript" src="scripts/jquery.number.min.js"></script>
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
	
	<? foreach($a_xauto as $t_kolom => $t_xauto) { ?>
	$("#<?= $t_xauto['text'] ?>").xautox({<?= $t_xauto['param'] ?>, targetid: "<?= $t_xauto['kolom'] ?>"});
	<? } ?>
	
	<?	foreach($a_input as $t_input) {
			if($t_input['type'] == 'N') {
	?>
	$("#<?= $t_input['kolom'] ?>").number(true,0,',','.');
	<?		}
		}
	?>
});

</script>
</body>
</html>
