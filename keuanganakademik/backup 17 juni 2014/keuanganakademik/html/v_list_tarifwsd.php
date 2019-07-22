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
						<th>Jurusan</th>
                        <? if($arr_tagihan)
							foreach($arr_tagihan as $b => $vt){?>
						<th>Tarif <?=$vt['namajenistagihan']?></th>
                        <? }?>
						<?	
							if($c_edit) { ?>
						<th width="50">Edit</th>
						<?	} ?>
					</tr>
                    <? if($arr_unit)
						foreach($arr_unit as $i => $v){
							$t_key = $i;
							if($t_key == $r_edit and $c_edit and $r_edit) {
								?>
                        <tr>
                        	<td><?=$v?></td>
                            <? if($arr_tagihan)
								foreach($arr_tagihan as $b => $vt){?>
                            	<td align="right">
								<?=UI::createTextbox($vt['jenistagihan'],CStr::FormatNumber($data[$r_periode][$i][$vt['jenistagihan']]['nominaltarif']),'ControlStyle','20','20',true,'onkeydown="return onlyNumber(event,this,'.true.','.true.')" style="text-align:right"')?>
                                </td>
                        	<? }?>
                            
                            <td align="center">
                            	<img id="<?= $i ?>" title="Simpan Data" src="images/disk.png" onClick="goUpdate(this)" style="cursor:pointer">
                            </td>
                        </tr>
                        <? } else {
							
							?>
                        <tr>
                        	<td><?=$v?></td>
							<? if($arr_tagihan)
								foreach($arr_tagihan as $b => $vt){?>
                            <td align="right"><?=CStr::FormatNumber($data[$r_periode][$i][$vt['jenistagihan']]['nominaltarif'])?></td>
                        	<? }?>
                            <td align="center">
                            <?	if($c_edit) { ?>
                                <img id="<?= $i ?>" title="Tampilkan Detail" src="images/edit.png" onClick="goEdit(this)" style="cursor:pointer">
                            <?			}
                            ?>
                            </td>
                        </tr>
                        <? } ?>
                        <? }?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
            
           <!-- <div style="clear:both"></div>
				<div>
					<fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                        <legend> Keterangan </legend>
                        
                    </fieldset>
				</div>
		</div>-->
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

</script>
</body>
</html>
