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
					/* INQUIRY FORM */
					/************************/
					
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>">
						<table width="<?= $p_tbwidth ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
                                        <?php /* <tr>
                                        	<td width="50" style="white-space:nowrap"><strong>Periode</strong></td>
                                            <td>
                                            	<strong>:</strong> 
												<? //=$l_periode?>
                                            <input type="radio" name="jenisperiode" value="setting" <?=($r_jenisperiode<>'all'?'checked':'')?>> Sesuai Setting Global
                                            <input type="radio" name="jenisperiode" value="all" <?=($r_jenisperiode=='all'?'checked':'')?>> Seluruh Tagihan
                                            </td>
                                        </tr>
                                        <tr>
                                        	<td width="50" style="white-space:nowrap"><strong>Jenis Tagihan</strong></td>
                                            <td><strong>:</strong> <?=$l_jenistagihan?></td>
                                        </tr> */ ?>
										<tr>
                                        	<td width="50" style="white-space:nowrap"><strong>Kelompok Tagihan</strong></td>
                                            <td><strong>:</strong> <?=$l_jenistagihan?></td>
                                        </tr>
                                        <tr>
                                        	<td width="50" style="white-space:nowrap"><strong>NIM</strong></td>
                                            <td valign="middle">
                                            	<strong>:</strong> 
                                                <?= UI::createTextBox('nim',$r_nim,'ControlStyle','20','20',true)?>
                                             	<input type="button" value="Inquiry Tagihan" onClick="goInquiry()">
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
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" align="center">
					<?	/**********/
						/* HEADER  DATA MHS*/
						/**********/
					if($r_nim) {
					?>
                    <tr>
                    <td>
                    <table <?=$style?> width="100%">
					<tr>
                    	<td  width="13%"><strong> NIM</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td width="35%" style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$r_nim?></td>
                        <td width="13%"><strong> Sistem Kuliah</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['namasistem']?></td>
                    </tr>
					<tr>
                    	<td><strong> Nama</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['nama']?></td>
                        <td><strong> Jalur Penerimaan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['namajalur']?></td>
                    </tr>
					<tr>
                    	<td><strong> Jurusan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['kodeunit'].' '.$mhs['namaunit']?></td>
                        <td><strong> Periode Inquiry</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$r_periode?></td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    <? }?>
                    <tr>
                    	<td colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                    <td colspan="5">
                    <? if($r_act == 'inquiry'){
						include('form_inquiry.php');
					}
					else if($r_act == 'payment'){
						include('form_payment.php');
						}
					else if($r_act == 'reversal'){
						include('form_reversal.php');
						}
					?>
                    </td>
                    </tr>
				</table>
    			<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="timereversal" id="timereversal" value="<?=$setting['reversal_time']?>">
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

<script type="text/javascript" src="scripts/jquery.number.min.js"></script>
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
	
	$(".ControlNumber").number(true,0,',','.');
});

function goInquiry(){
		document.getElementById("act").value = "inquiry";
		goSubmit();
	}
	
function goReversal(){
		document.getElementById("act").value = "reversal";
		goSubmit();
	}
function tes(elem){
		var total;
		total = document.getElementById("jumlahbayar").value;
		total = parseInt(total)+parseInt(document.getElementById(elem.id).value.replace('.',''));
		document.getElementById("jumlahbayar").value = total;
}
function hitungTotal(id,tagihan){
		var total;
		var tagihan;
		
		total = document.getElementById("jumlahtotal").value;
		
		if(document.getElementById('cek'+id).checked){
				document.getElementById(id).readOnly = true;
				total = parseInt(total)+parseInt(document.getElementById(id).value.replace(/\./g,''));
				//alert(document.getElementById(id).value.replace(/\./g,''));
			}
		else
			{
				document.getElementById(id).readOnly = false;
				total = parseInt(total)-parseInt(document.getElementById(id).value.replace(/\./g,''))
				document.getElementById(id).value = 0;
			}

		document.getElementById("labeltotal").value = numberFormat(total); 
		document.getElementById("jumlahtotal").value = total;
		document.getElementById("jumlahbayar").value = total;
		
	}
	
function goPayment(){
		var kas = noFormat(document.getElementById("jumlahbayar").value);
		var total = document.getElementById("jumlahtotal").value;
		if (isNaN(kas)) {
			alert('Harus Berupa Angka');
		}else{
			document.getElementById("act").value = "payment";
			goSubmit();
		}
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
<? if($r_act=='payment'){?>
     var milisec = 0 
     var seconds =  document.getElementById('timereversal').value; 
     document.getElementById('d2').value = '30' 
    
    function display(){ 
     if (milisec<=0){ 
        milisec=9 
        seconds-=1 
     } 
     if (seconds<=-1){ 
        milisec=0 
        seconds+=1 
     } 
     else 
        milisec-=1 
        document.getElementById('d2').value=seconds+"."+milisec 
        setTimeout("display()",100) 
    } 
    display();
<? }?>

</script>
</body>
</html>
