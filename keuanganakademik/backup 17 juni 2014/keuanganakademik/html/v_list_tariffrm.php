
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
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?php  require_once('inc_listfilter.php'); ?>
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
                   	<th width="15%">Periode</th>
                   	<th>Jalur Penerimaan</th>
                   	<th width="15%">Gelombang</th>
                    <th width="10%">Detail Tarif</th>
                   </tr>
                   <? if($data)
					   foreach($data as $i =>$val){?>
                   <tr>
                   		<td><?=$val['periodedaftar']?></td>
                   		<td><?=$val['jalurpenerimaan']?></td>
                   		<td><?=$val['idgelombang']?></td>
                   		<td align="center">
                           <img id="<?=$val['periodedaftar'].'|'.$val['jalurpenerimaan'].'|'.$val['idgelombang']?>" title="Tampilkan Detail" src="images/edit.png" onClick="goDetail(this)" style="cursor:pointer">
                       </td>
                   </tr>
				   <? }?>
				</table>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
					
				<input type="hidden" name="npm" id="npm">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript">

var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle contact
	$("[id='imgcontact']").balloon();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});


</script>
</body>
</html>