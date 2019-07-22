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
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
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
                                        <tr>
                                        	<td><strong>Jenis</strong></td>
                                            <td><strong>: </strong>
                                            <? 
											if($arr_jenis)
											foreach($arr_jenis as $i => $v){?>
                                            	<input type="checkbox" name="jenis[]" value="<?=$i?>"  <?=(in_array($i,$r_jenis)?'checked':'')?> onChange="goSubmit()"><?=$v?>
                                            <? }?>
                                            </td>
                                        </tr>
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
                	<div align="right">
                    <img src="images/posting.gif"> Tagihan Telah di Generate
                    &nbsp;&nbsp;&nbsp;
                    <img src="images/nonposting.gif"> Tagihan Belum di Generate
                    </div>
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
						<th rowspan="3">Jurusan</th>
                      	<th colspan="<?=$col?>">Jenis Tagihan</th>
					</tr>
                    <tr>
                    <?
					if($frekuensi) 
					foreach($frekuensi as $i => $val){
						if($val)
						foreach($val as $a => $v){?>
                    	<th colspan="<?=count($jt[$i])?>">
                        <?
						if($i=='B')
							echo substr($v,0,4).' '.Date::indoMonth(substr($v,-2),false);
						else
                        	echo $v;
						?></th>
                        <? }?>
                    <? }?>
                    </tr>
                    <tr>
                    <?
					if($frekuensi) 
					foreach($frekuensi as $i => $val){
						if($val)
						foreach($val as $a => $v){
							foreach($jt[$i] as $d => $vk){?>
                            <th width="5%"><?=$vk?></th>
                    		<? }?>
                        <? }?>
                    <? }?>
                    </tr>
                    <? foreach($arr_unit as $kodeunit => $namaunit){?>
                    <tr>
                    	<td><?=$namaunit?></td>
                    <? 
					if($frekuensi)
					foreach($frekuensi as $i => $val){
						if($val)
						foreach($val as $a => $v){
							if($jt[$i])
							foreach($jt[$i] as $d => $vk){
								$edited = floor($jmltagihan[$kodeunit][$vk][$r_periode][$a]['1']);
								$generated = floor($jmltagihan[$kodeunit][$vk][$r_periode][$a]['0']);
								?>
                            <td align="center">
                            <? if($data[$kodeunit][$vk][$r_periode][$a] and $jenisgen[$data[$kodeunit][$vk][$r_periode][$a]]=='G'){?>
                            	<img src="images/posting.gif" style="cursor:pointer" title="Batalkan Generate Tagihan" onClick="goVoid('<?=$kodeunit?>','<?=$a?>','<?=$vk?>','<?=$data[$kodeunit][$vk][$r_periode][$a]?>')">
                               <? } else {?>
                            	<img src="images/nonposting.gif" style="cursor:pointer" title="Generate Tagihan" onClick="goGenerate('<?=$kodeunit?>','<?=$a?>','<?=$vk?>')">
                                <? }?>
                                <img id="<?= $t_key ?>" title="Detail Tagihan" src="images/link.png" onClick="goPoptes('popMenu',this,event,'<?=CStr::FormatNumber($generated)?>','<?=CStr::FormatNumber($edited)?>','<?=$a?>','<?=$vk?>','<?=$kodeunit?>')" style="cursor:pointer">
                            </td>
                    		<? }?>
                        <? }?>
                    <? }?>
                    </tr>
					<? }?>
				</table>
    			<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="tahunbulankey" id="tahunbulankey">
				<input type="hidden" name="jenistagihankey" id="jenistagihankey">
				<input type="hidden" name="isedit" id="isedit">
				<input type="hidden" name="jenistagihan" id="jenistagihan">
				<input type="hidden" name="tahunbulan" id="tahunbulan">
				<input type="hidden" name="kodeunit" id="kodeunit">
				<input type="hidden" name="unitkey" id="unitkey">
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
<div id="popMenu" class="menubar" style="position:absolute; display:none; top:0px; left:0px;z-index:10000;" onMouseOver="javascript:overpopupmenu=true" onMouseOut="javascript:overpopupmenu=false">
<table class="menu-body">
    <tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('G','<?= Route::navAddress('list_tagihan') ?>')">
        <span id="generated" title="Tagihan yang di generate"></span></td>
    </tr>
	<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
        <td onClick="showPage('E','<?= Route::navAddress('list_tagihan') ?>')">
        <span id="edited" title="Tagihan yang di edit"></span></td>
    </tr>
</table>
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

function goVoid(kodeunit,thnbulan,jenistagihan,idloggen){
	var txt = confirm("Semua Tagihan yang telah di generate akan di hapus, Apakah anda yakin akan membatalkan generate tagihan ini?");
	if(txt) {
		document.getElementById("act").value = "void";
		document.getElementById("tahunbulankey").value = thnbulan;
		document.getElementById("jenistagihankey").value = jenistagihan;
		document.getElementById("unitkey").value = kodeunit;
		document.getElementById("key").value = idloggen;
		
		goSubmit();
	}
	}
	

function goGenerate(kodeunit,thnbulan,jenistagihan){
	var txt = confirm("Apakah anda yakin akan melakukan generate tagihan ini?");
	if(txt) {
		document.getElementById("act").value = "generate";
		document.getElementById("tahunbulankey").value = thnbulan;
		document.getElementById("jenistagihankey").value = jenistagihan;
		document.getElementById("unitkey").value = kodeunit;
		
		goSubmit();
	}
	}
	
// pop up
function goPoptes(idpop,elem,e,generated,edited,thnbulan,jenistagihan,kodeunit) {
	
	var gParam;

	gParam = elem.id;
	
	var pop = $("#"+idpop);
	
	// pop.offset({ top: e.pageY, left: e.pageX });
	
	//var x = String(e.pageX)+"px";
	var x = String(e.pageX+20)+"px";
	var y = String(e.pageY-20)+"px";
	
	pop.css("top",y);
	pop.css("left",x);
	pop.show();
	
	$(document).bind("mouseup",function(e) {
		if(pop.has(e.target).length === 0) {
			pop.hide();
		}
	});
	$("#generated").html(generated+' Generated');
	$("#edited").html(edited+' Edited');
	
	document.getElementById("tahunbulan").value = thnbulan;
	document.getElementById("jenistagihan").value = jenistagihan;
	document.getElementById("kodeunit").value = kodeunit;
		
}

function showPage(isedit,file) {
	
	document.getElementById("isedit").value = isedit;
	goSubmitBlank(file);
}



</script>
</body>
</html>
