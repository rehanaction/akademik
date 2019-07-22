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
	<link href="style/jMenu.css" rel="stylesheet" type="text/css">
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
					/* INQUIRY FORM */
					/************************/
					
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
                                        <tr>
                                        	<td width="50" style="white-space:nowrap"><strong>Kode Formulir</strong></td>
                                            <td valign="middle">
                                            	<strong>:</strong> 
                                                <?= UI::createTextBox('kodeformulir',$r_kodefrm,'ControlStyle','20','5',true)?>
                                             	<? if($c_inquiry){?>
                                                <input type="button" value="Inquiry Tarif" onClick="goInquiry()">
                                             	<? }?>
                                             </td>
                                             <td align="right">
                                             <img src="images/search.png" title="Daftar Kode Formulir" style="cursor:pointer">
                                             
                                             <a href="#pop_anggaran" rel="leanModal" rev="ulbl_detailrekening">Daftar Tarif & Kode Formulir
                                             </a>
                                             </td>
                                        </tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	
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
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?> ( <?=$_SESSION[SITE_ID]['MODUL']['USERNAME']?> , <?=date('d-m-Y')?> )</h1>
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
						
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" align="center" <?=$style?>>
					<?	/**********/
						/* HEADER  DATA MHS*/
						/**********/
					if($r_kodefrm and $tarif){
					?>
					<tr>
                    	<td  width="13%"><strong> Tahun Pendaftaran</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td width="35%" style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=$tarif['periodedaftar']?>
                        </td>
                        <td width="13%"><strong> Sistem Kuliah</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=$tarif['namasistem']?>
                        </td>
                    </tr>
					<tr>
                    	<td><strong> Gelombang</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=$tarif['namagelombang']?>
                         </td>
                        <td><strong> Jalur Penerimaan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=$tarif['jalurpenerimaan']?>
                        </td>
                    </tr>
					<tr>
                    	<td><strong> Jumlah Pilihan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=$tarif['jumlahpilihan']?>
                        </td>
                        <td><strong> Program Pendidikan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=$tarif['programpend']?>
                       </td>
                    </tr>
                    <tr>
                        <td><strong> Tarif</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
							<?=CSTr::FormatNumber($tarif['nominaltarif'])?>
                        </td>
                        <? if($r_act == 'payment' or $r_act == 'reversal'){?>
                        <td><strong> No Kuitansi</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
						<?=$refno?>
                        &nbsp;&nbsp;
                        <span style="cursor:pointer" onClick="showPage('notoken','<?= Route::navAddress('rep_paymentfrm') ?>')"> 
                        <font color="#0000FF">
                        <u>( Cetak Kwitansi )</u>
                        </font>
                        </span>
                        <input type="hidden" name="refno" id="refno" value="<?=$refno?>">
                        <input type="hidden" name="notoken" id="notoken" value="<?=$token?>">
                        </td>
						<? }?>
                    </tr>
                    <? if($r_act == 'inquiry') {?>
                    <tr>
                    	<td colspan="6"><strong>Catatan</strong>
                        <br>
                        	<?=UI::createTextArea('catatan','','ControlStyle','3','68')?>
                        <? if($c_payment){?>
                         <input type="button" value="Beli Formulir" onClick="goPayment()">
                        <? }?>
                        </td>
                    </tr>
                    <? }?>
					<? if($r_act == 'payment' or $r_act == 'reversal') {?>
                    <tr valign="top">
                    	<td colspan="3"><strong>Catatan</strong>
                        <br>
                        	<em>( <?=$catatan?> )</em>
                        </td>
                        <td colspan="3"><strong>TOKEN</strong>
                        <br>
                        <font color="#FF0000" size="+3">
                        	<em><?=$token?></em>
                        </font>
                        </td>
                    </tr>
                    <? if($r_act == 'payment') {?>
                    <tr>
                    	<td colspan="5"></td>
                        <td align="right">
                        <? if($c_reversal){?>
                         <input type="button" value="Batalkan Pembayaran" onClick="goReversal()">
                        <? }?>
                        </td>
                    </tr>
                    <? }?>
                    <tr>
                    	<td colspan="6">
                        <font color="#CCCCCC">
                        <em>Token / Voucher hanya dapat di gunakan sekali untuk pendaftaran online</em>
                        </font>
                        </td>
                    </tr>
                    <? }?>
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
<div id="pop_anggaran" style="display:none;" class="detail">
	<div style="text-align: center; font-weight: bold;">TARIF FORMULIR TAHUN <?=$settingdetail['FRM']['periodesekarang']?></div>
	<br>
	<div class="accordian">
		<table width="100%" class="GridStyle">
        	<tr>
            	<th rowspan="2" width="15%">Jalur Penerimaan</th>
            	<th rowspan="2" width="15%">Gelombang</th>
            	<th rowspan="2" width="10%">Program Studi</th>
                <th rowspan="2" width="15%">Sistem Kuliah</th>
                <th colspan="3">Jumlah Pilihan <br> (kode) Jumlah Tarif</th>
            </tr>
            <tr>
            	<th>1</th>
            	<th>2</th>
            	<th>3</th>
            </tr>
            <? 
			//$data[$v['jalurpenerimaan']][$v['idgelombang']][$v['sistemkuliah']][$v['programpend']][$v['jumlahpilihan']] 
			if($data)
			foreach($data as $kode => $vj ){	
			?>
            <tr>
            	<td><?=$vj['jalurpenerimaan']?></td>
            	<td><?=$vj['namagelombang']?></td>
            	<td align="center"><?=$vj['programpend']?></td>
            	<td><?=$vj['namasistem']?></td>
            	<td align="right">
                <span  style="cursor:pointer;" onClick="setLabel('<?=$datatarif[$kode]['1']['kodeformulir']?>')">
                <font color="#0000FF">
				<u>
				<? if($datatarif[$kode]['1']['kodeformulir'])
					echo '('.$datatarif[$kode]['1']['kodeformulir'].') '.CSTr::FormatNumber($datatarif[$kode]['1']['nominaltarif'])?>
                </u>
                </font>
                </span>
                </td>
            	<td align="right">
				<span  style="cursor:pointer;" onClick="setLabel('<?=$datatarif[$kode]['2']['kodeformulir']?>')">
                <font color="#0000FF">
				<u>
				<? if($datatarif[$kode]['2']['kodeformulir'])
					echo '('.$datatarif[$kode]['2']['kodeformulir'].') '.CSTr::FormatNumber($datatarif[$kode]['2']['nominaltarif'])?>
                </u>
                </font>
                </span>
                </td>
            	<td align="right">
				 <span  style="cursor:pointer;" onClick="setLabel('<?=$datatarif[$kode]['3']['kodeformulir']?>')">
                <font color="#0000FF">
				<u>
				<? if($datatarif[$kode]['3']['kodeformulir'])
					echo '('.$datatarif[$kode]['3']['kodeformulir'].') '.CSTr::FormatNumber($datatarif[$kode]['3']['nominaltarif'])?>
                </u>
                </font>
                </span>
                </td>
            </tr>
            <? }
			?>
        </table>
        <br><br>
	</div>
</div>
<script type="text/javascript" src="scripts/jMenu.js"></script>
<script type="text/javascript" src="scripts/jquery.leanModal.min.js"></script>
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
	$("a[rel*=leanModal]").leanModal();
});

function goInquiry(){
		document.getElementById("act").value = "inquiry";
		goSubmit();
	}
	
function goReversal(){
		document.getElementById("act").value = "reversal";
		goSubmit();
	}
	
function hitungTotal(id,tagihan){
		var total;
		var tagihan;
		
		total = document.getElementById("jumlahtotal").value;
		
		if(document.getElementById('cek'+id).checked){
			document.getElementById(id).value = numberFormat(tagihan);
			total = parseInt(total)+parseInt(tagihan);
			}
		else
			{
			total = parseInt(total)-parseInt(tagihan);
			document.getElementById(id).value = 0;
			}
		
		document.getElementById("labeltotal").value = numberFormat(total); 
		document.getElementById("jumlahtotal").value = total; 
		
	}
	
function goPayment(){
		
			document.getElementById("act").value = "payment";
			goSubmit();
	}

function noFormat(str) {
	if(str == "")
		return "";
	else {
		var nof = str.replace(/\./g,'');
		return parseInt(nof);
	}
}

function numberFormat(num) {
	var ret = '';
	var ismin = false;
	var j = 0;
	
	// tanpa desimal
	num = String(num);
	arrnum = num.split('.');
	num = arrnum[0];
	
	if(num.charAt(0) == '-') {
		ismin = true;
		num = num.substr(1);
	}
	
	for(i=num.length-1;i>=0;i--) {
		if(j == 3) {
			ret = "." + ret;
			j = 0;
		}
		ret = num.charAt(i) + ret;
		j++;
	}
	
	if(ismin)
		ret = '-'+ret;
	
	return ret;
}

function setLabel(kode){
	document.getElementById("kodeformulir").value=kode;
	document.getElementById("pop_anggaran").style.display='none';
	document.getElementById("lean_overlay").style.display='none';
	
}

</script>
</body>
</html>
