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
                    	<td class="LeftColumnBG" width="35%" style="white-space:nowrap">Periode Tarif</td>
                        <td class="RightColumnBG"><?=$l_periode?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Jalur Penerimaan</td>
                        <td class="RightColumnBG"><?=$l_jalur?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Jurusan</td>
                        <td class="RightColumnBG"><?=$l_unit?></td>
                    </tr>
                    <? 
					if($arr_sistemkuliah)
					foreach($arr_sistemkuliah as $i => $val){?>
                    <tr>
                    	<td colspan="2" class="LeftColumnBG" align="center"><strong><?=$val['namasistem'].' '.$val['tipeprogram']?></strong></td>
                    </tr>
                    <? if($arr_jenistagihan)
					foreach($arr_jenistagihan as $t => $v){
						$tarif = $data[$r_kodeunit][$val['sistemkuliah']][$v['jenistagihan']];
						$add .= ' onkeydown="return onlyNumber(event,this,'.true.','.true.')" style="text-align:right"';
						?>
                    	<tr>
                        	<td class="LeftColumnBG"><?=$v['namajenistagihan']?></td>
                        	<td class="RightColumnBG">
                            <span id="show"><?= CStr::FormatNumber($tarif) ?></span>
							<span id="edit" style="display:none">
                            <?= UI::createTextBox($val['sistemkuliah'].'|'.$v['jenistagihan'],CStr::FormatNumber($tarif),'',NULL,'15',true,$add); ?>
                            </span>
                            </td>
                        </tr>
					<? 
						}
					}?>
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
