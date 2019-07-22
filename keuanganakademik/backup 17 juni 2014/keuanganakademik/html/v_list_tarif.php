<?php
	
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
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
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
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
					<header style="width:<?= $p_tbwidth ?>">
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
						<th rowspan="2">Jurusan</th>
                      <th rowspan="2" width="10%">Sistem Kuliah</th>
                        <th colspan="<?=count($arr_jenistagihan)?>">Jenis Tagihan</th>
                      <th rowspan="2" width="3%">Detail</th>
					</tr>
                    <tr>
					<? foreach($arr_jenistagihan as $i => $val){?>
                   	  <th width="7%" valign="top"><?=$val['jenistagihan']?></th>
					<? }?>
                    </tr>
					<?	/********/
						/* ITEM */
						/********/
						 if($arr_unit)
						 foreach($arr_unit as $kodeunit => $namaunit){
						 ?>
                         	<tr>
                           	  <td rowspan="<?=count($arr_sistemkuliah)?>"><strong><?=$kodeunit.' - '.$namaunit?></strong></td>
                         <?  foreach($arr_sistemkuliah as $i => $val){
							 if($i>'0'){?>
                  			 </tr>
                         	 <tr>
                             <? }?>
                            	<td><strong><?=$val['namasistem'].' '.$val['tipeprogram']?></strong></td>
                                <? foreach($arr_jenistagihan as $j => $v){?>
                                <td align="right">
								<?=CStr::FormatNumber($data[$kodeunit][$val['sistemkuliah']][$v['jenistagihan']])?>
                                </td>
								<? }?>
                                <? if($i==0){?>
                                <td rowspan="<?=count($arr_sistemkuliah)?>" valign="middle" align="center">
                                <img id="<?=$kodeunit.'|'.$r_periode.'|'.$r_jalur?>" title="Tampilkan Detail" src="images/edit.png" onClick="goDetail(this)" style="cursor:pointer">
                                </td>
								<? }?>
                         <? }?>
						 <? 
						 }?>
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
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

$(document).ready(function() {
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>
