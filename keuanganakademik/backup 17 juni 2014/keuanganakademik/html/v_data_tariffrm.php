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
					
                    <tr>
                    	<td class="LeftColumnBG" width="35%" style="white-space:nowrap">Tahun Pendaftaran</td>
                        <td class="RightColumnBG" colspan="2"><?=$r_periodedaftar?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Jalur Penerimaan</td>
                        <td class="RightColumnBG" colspan="2"><?=$l_jalur?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Gelombang</td>
                        <td class="RightColumnBG" colspan="2"><?=$l_gelombang?></td>
                    </tr>
                    <? if($arr_programpend)
					foreach($arr_programpend as $programpend => $v){?>
                    <tr bgcolor="#CCCCCC" align="center">
                    	<td colspan="3">
							<strong><?=$v?></strong>
                         </td>
                    </tr>
                    <? 
						$add .= ' onkeydown="return onlyNumber(event,this,'.true.','.true.')" style="text-align:right"';
						if($arr_sistemkuliah)
					foreach($arr_sistemkuliah as $i => $val){
						$tarif1 = $data[$r_periodedaftar][$r_jalur][$r_gelombang][$val['sistemkuliah']][1][$programpend]['nominaltarif'];
						$kode1 = $data[$r_periodedaftar][$r_jalur][$r_gelombang][$val['sistemkuliah']][1][$programpend]['kodeformulir'];
						$tarif2 = $data[$r_periodedaftar][$r_jalur][$r_gelombang][$val['sistemkuliah']][2][$programpend]['nominaltarif'];
						$kode2 = $data[$r_periodedaftar][$r_jalur][$r_gelombang][$val['sistemkuliah']][2][$programpend]['kodeformulir'];
						$tarif3 = $data[$r_periodedaftar][$r_jalur][$r_gelombang][$val['sistemkuliah']][3][$programpend]['nominaltarif'];
						$kode3 = $data[$r_periodedaftar][$r_jalur][$r_gelombang][$val['sistemkuliah']][3][$programpend]['kodeformulir'];
						?>
                    	<tr>
                        	<td class="LeftColumnBG" colspan="3" >
                            	<strong><?=$val['namasistem'].' '.$val['tipeprogram']?></strong>
                            </td>
                        </tr>
                         <tr>
                        	<td class="LeftColumnBG">1 Pilihan</td>
                        	<td class="RightColumnBG">
                            <span id="show"><?= CStr::FormatNumber($tarif1)?></span>
							<span id="edit" style="display:none">
                            <?= UI::createTextBox('tarif|'.$programpend.'|'.$val['sistemkuliah'].'|1',CStr::FormatNumber($tarif1),'',NULL,'15',true,$add); ?>
                            </span>
                            </td>
                            <td><span id="show"><?=' Kode :'.$kode1 ?></span>
							<span id="edit" style="display:none">
                            Kode : 
                            <?= UI::createTextBox('kode|'.$programpend.'|'.$val['sistemkuliah'].'|1',$kode1,'',NULL,'5',true,$add); ?>
                            </span></td>
                        </tr> 
                        <tr>
                        	<td class="LeftColumnBG">2 Pilihan</td>
                        	<td class="RightColumnBG">
                            <span id="show"><?= CStr::FormatNumber($tarif2)?></span>
							<span id="edit" style="display:none">
                            <?= UI::createTextBox('tarif|'.$programpend.'|'.$val['sistemkuliah'].'|2',CStr::FormatNumber($tarif2),'',NULL,'15',true,$add); ?>
                            </span>
                            </td>
                            <td><span id="show"><?=' Kode :'.$kode2 ?></span>
							<span id="edit" style="display:none">
                            Kode : 
                            <?= UI::createTextBox('kode|'.$programpend.'|'.$val['sistemkuliah'].'|2',$kode2,'',NULL,'5',true,$add); ?>
                            </span></td>
                        </tr> 
                        <tr>
                        	<td class="LeftColumnBG">3 Pilihan</td>
                        	<td class="RightColumnBG">
                            <span id="show"><?= CStr::FormatNumber($tarif3)?></span>
							<span id="edit" style="display:none">
                            <?= UI::createTextBox('tarif|'.$programpend.'|'.$val['sistemkuliah'].'|3',CStr::FormatNumber($tarif3),'',NULL,'15',true,$add); ?>
                            </span>
                            </td>
                            <td><span id="show"><?=' Kode :'.$kode3 ?></span>
							<span id="edit" style="display:none">
                            Kode : 
                            <?= UI::createTextBox('kode|'.$programpend.'|'.$val['sistemkuliah'].'|3',$kode3,'',NULL,'5',true,$add); ?>
                            </span></td>
                        </tr> 
                    <? }?>
                    <? }?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act"> 
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?		
					} ?>
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

</script>
</body>
</html>
