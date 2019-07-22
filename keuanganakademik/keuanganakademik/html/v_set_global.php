<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:950px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
			<?	} ?>
			<center>
				<div id="div_setting">
					<header style="width:<?= $p_tbwidth-50 ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/SETTING.png" onerror="loadDefaultActImg(this)"> <h1>Setting Global </h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/
					?>
				<form name="pageformglobal" id="pageformglobal" method="post">
					<div class="box-content" style="width:<?= $p_tbwidth-72 ?>px">
						<table width="<?= $p_tbwidth-72 ?>" cellpadding="4" cellspacing="2" align="center">
                        <tr>
							<td class="LeftColumnBG" width="30%" style="white-space:nowrap">
								Reversal Time
							</td>
							<td class="RightColumnBG">
								<?=UI::createTextBox('reversal_time',$data['reversal_time'],'ControlStyle','10','10')?>
								<font color="#666">
								<em>Batas waktu pembatalan pembayaran ( dalam detik )</em>
								</font>
							</td>
						</tr>
                        <tr>
                        	<td colspan="2">
                            	<table class="GridStyle" width="100%" cellpadding="4" cellspacing="2">
                                	<tr class="SubHeaderBG" align="center">
                                    	<td rowspan="2"><strong>Kelompok Tagihan</strong></td>
                                        <td rowspan="2"><strong>Periode Saat Ini</strong></td>
                                        <td colspan="3"><strong>Allow</strong></td>
                                     </tr>
                                     <tr class="SubHeaderBG" align="center">
                                        <td><strong>Inquiry</strong></td>
                                        <td><strong>Payment</strong></td>
                                        <td><strong>Reversal</strong></td>
                                    </tr>
                                     <? foreach($arr_tagihan as $k => $v){?>
                                     <input type="hidden" name="key_<?=$k?>" value="<?=$k?>">
                                     <tr>
                                     	<td class="LeftColumnBG"><strong><?=$v ?></strong></td>
										<td align="center">
                                        <? if($v['frekuensitagihan']=='W'){?>
                                        <?=UI::createSelect('periode_'.$k,$arr_periodeyudisium,$datadetail[$k]['periodesekarang'],'ControlStyle',true,'',true,'')?>
                                        <? } else { ?>
										<?=UI::createSelect('periode_'.$k,$arr_periode,$datadetail[$k]['periodesekarang'],'ControlStyle',true,'',true,'')?>
										<? } ?>
                                        </td>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="inq_<?=$k?>" <?=($datadetail[$k]['allow_inquiry']=='1'?'checked':'')?>>
                                        </td>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="pay_<?=$k?>" <?=($datadetail[$k]['allow_payment']=='1'?'checked':'')?>>
                                        </td>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="rev_<?=$k?>" <?=($datadetail[$k]['allow_reversal']=='1'?'checked':'')?>>
                                        </td>
                                     </tr>
                                     <? }?>
                                </table>
                            </td>
                        </tr>
                       
                        <tr>
                        <td colspan="2" align="center">
                        <? if($c_edit){?>
                        <div class="TDButton" onClick="goSetGlobal()" style="width: 150px">
							<img src="images/disk.png" />
							Simpan Setting Global
						</div>
                        <? }?>
                       </td>
                        </tr>
						</table>
					</div>
                    <div style="clear:both"></div>
                            <div>
                                <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                                    <legend> Keterangan </legend>
                                    <table width="100%" align="left">
                                    	<tr>
                                        	<td><strong>Inquiry</strong></td>
                                            <td><strong>:</strong></td>
                                            <td>Proses Memperoleh Informasi Tarif / Tagihan</td>
                                        </tr>
                                        <tr>
                                        	<td><strong>Payment</strong></td>
                                            <td><strong>:</strong></td>
                                            <td>Proses Membayar Tarif / Tagihan</td>
                                        </tr>
                                        <tr>
                                        	<td><strong>Reversal</strong></td>
                                            <td><strong>:</strong></td>
                                            <td>Proses Membatalkan pembayaran / pembelian</td>
                                        </tr>
                                    </table>
                                </fieldset>
                            </div>
					<input type="hidden" name="key" id="key" value="<?=$data['idsetting']?>">
					<input type="hidden" name="act" id="act" value="setGlobal">
				</form>
				</div>
			</center>
		</div>
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
});
function goSetGlobal() {
	document.getElementById("pageformglobal").submit();
}
</script>

</body>
</html>
