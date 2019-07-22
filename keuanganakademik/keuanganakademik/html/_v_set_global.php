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
                                    <font color="#CCCCCC">
                                    <em>Batas waktu pembatalan pembayaran ( dalam detik )</em>
                                    </font>
								</td>
						</tr> 
                        <?php /*?><tr>
								<td class="LeftColumnBG" width="30%" style="white-space:nowrap">
									Rekon Time
								</td>
								<td class="RightColumnBG">
									<input type="text">
                                    <font color="#CCCCCC">
                                    <em>Batas waktu antar transaksi dan Rekonsiliasi Bank ( dalam detik )</em>
                                    </font>
								</td>
						</tr><?php */?>
                        <tr>
                        	<td colspan="2">
                            	<table class="GridStyle" width="100%" cellpadding="4" cellspacing="2">
                                	<tr class="SubHeaderBG" align="center">
                                    	<td rowspan="2"><strong>Jenis Tagihan</strong></td>
                                        <td rowspan="2"><strong>Periode Saat Ini</strong></td>
                                        <td rowspan="2" width="15%"><strong>Bulan Tahun</strong></td>
                                        <?php /*?><td rowspan="2"><strong>Pakai<br>SKS Default?</strong></td>
                                        <td rowspan="2"><strong>Jumlah<br>SKS Default</strong></td><?php */?>
                                        <td colspan="3"><strong>Allow</strong></td>
                                     </tr>
                                     <tr class="SubHeaderBG" align="center">
                                        <td><strong>Inquiry</strong></td>
                                        <td><strong>Payment</strong></td>
                                        <td><strong>Reversal</strong></td>
                                    </tr>
                                    <tr>
                                     	<td class="LeftColumnBG"><strong>Formulir</strong></td>
                                        <td align="center"><?=UI::createSelect('periode_FRM',$arr_periodedaftar,$datadetail['FRM']['periodesekarang'],'ControlStyle',true,'',true,'')?></td>
                                        <td align="center"> - </td>
                                       <?php /*?> <td align="center"> - </td>
                                        <td align="center"> - </td><?php */?>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="inq_FRM" <?=($datadetail['FRM']['allow_inquiry']=='1'?'checked':'')?>>
                                        </td>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="pay_FRM" <?=($datadetail['FRM']['allow_payment']=='1'?'checked':'')?>>
                                        </td>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="rev_FRM" <?=($datadetail['FRM']['allow_reversal']=='1'?'checked':'')?>>
                                        </td>
                                        <input type="hidden" name="key_FRM" value="<?=$datadetail['FRM']['jenistagihan']?>">
                                     </tr>
                                     <? foreach($arr_tagihan as $i => $v){?>
                                     <input type="hidden" name="key_<?=$v['jenistagihan']?>" value="<?=$datadetail[$v['jenistagihan']]['jenistagihan']?>">
                                     <tr>
                                     	<td class="LeftColumnBG"><strong><?=$v['namajenistagihan'] ?></strong></td>
										<td align="center">
                                        <? if ($v['jenistagihan']=='FRM'){?>
										<?=UI::createSelect('periode_FRM',$arr_periodedaftar,$datadetail['FRM']['periodesekarang'],'ControlStyle',true,'',true,'')?>
										<? } else {?>

                                        <? if($v['frekuensitagihan']<>'W'){?>
                                        <?=UI::createSelect('periode_'.$v['jenistagihan'],$arr_periode,$datadetail[$v['jenistagihan']]['periodesekarang'],'ControlStyle',true,'',true,'')?>
                                        <? }else if($v['frekuensitagihan']=='W'){?>
                                        <?=UI::createSelect('periode_'.$v['jenistagihan'],$arr_periodeyudisium,$datadetail[$v['jenistagihan']]['periodesekarang'],'ControlStyle',true,'',true,'')?>
                                        <? }
										}
										?>
                                        </td>
                                        <td align="center">
                                         <? if($v['frekuensitagihan']=='B'){
											 $bulan = (substr($datadetail[$v['jenistagihan']]['bulantahunsekarang'],4,1)=='0')?substr($datadetail[$v['jenistagihan']]['bulantahunsekarang'],5,1):substr($datadetail[$v['jenistagihan']]['bulantahunsekarang'],4,2);
											 $tahun = substr($datadetail[$v['jenistagihan']]['bulantahunsekarang'],0,4);
											 
											 echo UI::createSelect('bulan_'.$v['jenistagihan'],$arr_bulan,$bulan,'ControlStyle',true,'',true,''); 
											 echo ' ';
											 echo UI::createSelect('tahun_'.$v['jenistagihan'],$arr_tahun,$tahun,'ControlStyle',true,'',true,'');
											 }else echo '-';?>
                                        </td>
                                        <? /*if($v['issks']=='1'){?>
                                        <td align="center">
                                        <input type="checkbox" value="D" name="aturansks_<?=$v['jenistagihan']?>">
                                        </td><td align="center">
                                        <input type="text" size="5" name="sksdefault_<?=$v['jenistagihan']?>">
                                        </td>
										<? }else{?>
                                        <td align="center"> -
                                        </td>
                                        <td align="center"> -
                                        </td>
                                        <? }*/?>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="inq_<?=$v['jenistagihan']?>" <?=($datadetail[$v['jenistagihan']]['allow_inquiry']=='1'?'checked':'')?>>
                                        </td>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="pay_<?=$v['jenistagihan']?>" <?=($datadetail[$v['jenistagihan']]['allow_payment']=='1'?'checked':'')?>>
                                        </td>
                                        <td align="center">
                                        <input type="checkbox" value="1" name="rev_<?=$v['jenistagihan']?>" <?=($datadetail[$v['jenistagihan']]['allow_reversal']=='1'?'checked':'')?>>
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
