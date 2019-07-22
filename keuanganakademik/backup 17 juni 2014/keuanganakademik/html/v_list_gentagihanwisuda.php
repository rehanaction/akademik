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
                	<th width="10%" rowspan="2">NIM</th>
                	<th rowspan="2">Nama</th>
                	<th width="15%" rowspan="2">Jurusan</th>
                	<th width="15%" rowspan="2">Periode</th>
               <? if($arr_tagihan)
			   		foreach($arr_tagihan as $i => $v){
			   ?>
               		<th colspan="3" width="30%"><?=$v['namajenistagihan']?></th>
               <? } ?>
               </tr>
               <tr>
                <? if($arr_tagihan)
			   		foreach($arr_tagihan as $i => $v){
			   ?>
               		<th width="10%">Jumlah Tagihan</th>
                	<th width="10%">Jumlah Tarif</th>
                	<th width="10%">Status</th>
               <? } ?>
                </tr>
                <? if($datamhs)
					foreach($datamhs as $i => $v){
				?>
                <tr>
                	<td><?=$v['nim']?></td>
                    <td><?=$v['nama']?></td>
                    <td><?=$v['namaunit']?></td>
                    <td><?=$v['idyudisium'].' ( '.cStr::formatDateInd($v['tglyudisium']).' )'?></td>
                    <? if($arr_tagihan)
			   		foreach($arr_tagihan as $i => $val){
			   		?>
                    <td align="right"><?=CStr::FormatNumber($data[$v['nim']][$val['jenistagihan']][$v['idyudisium']]['nominaltagihan']) ?></td>
                    <td align="right"><?=CStr::FormatNumber($tarif[$v['idyudisium']][$v['kodeunit']][$val['jenistagihan']]['nominaltarif']) ?></td>
                    <td align="center">
					<? if($data[$v['nim']][$val['jenistagihan']][$v['idyudisium']]){
						if($data[$v['nim']][$val['jenistagihan']][$v['idyudisium']]['flaglunas']=='L')
							echo "Lunas";
						elseif($data[$v['nim']][$val['jenistagihan']][$v['idyudisium']]['flaglunas']=='BL')
							echo "Belum Lunas";
						elseif($data[$v['nim']][$val['jenistagihan']][$v['idyudisium']]['isedit']=='E')
							echo "Edited";
						else
							{?>
                            <img style="cursor:pointer" src="images/posting.gif" title="Batalkan Generate Tagihan" onClick="goVoid('<?=$data[$v['nim']][$val['jenistagihan']][$v['idyudisium']]['idtagihan']?>')">
                            <?
							}
					}
					else
					{
						?>
                         <img style="cursor:pointer" src="images/nonposting.gif" title="Generate Tagihan" onClick="goGenerate('<?=$v['nim']?>','<?=$v['kodeunit']?>','<?=$v['idyudisium']?>','<?=$val['jenistagihan']?>')">
                        <?
					}?>
                    </td>
                    <? }?>
                </tr>
                <? }?>
               <?php /*?> <tr>
                	<td colspan="5" align="right">
                    	<font color="#999999"><em>digunakan untuk melakukan generate tagihan wisuda seluruh peserta yudisium</em></font> &nbsp; 
                        <strong>Generate ALL</strong>
                    </td>
                	<td></td>
                </tr><?php */?>	
				</table>
    			<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="nimkey" id="nimkey">
				<input type="hidden" name="kodeunitkey" id="kodeunitkey">
				<input type="hidden" name="periodekey" id="periodekey">
				<input type="hidden" name="jenistagihankey" id="jenistagihankey">
				<input type="hidden" name="idkey" id="idkey">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
            
            <div style="clear:both"></div>
				<div>
					<fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                    <legend> Keterangan </legend>
                        Mahasiswa yang sudah tercatat dalam peserta yudisium yang dapat di generate tagihan wisudanya.
                    </fieldset>
				</div>
		</div>
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

function goVoid(id){
	var txt = confirm("Semua Tagihan yang telah di generate akan di hapus, Apakah anda yakin akan membatalkan generate tagihan ini?");
	if(txt) {
		document.getElementById("act").value = "void";
		document.getElementById("idkey").value = id;
		
		goSubmit();
	}
	}
	

function goGenerate(nim,kodeunit,periode,jenistagihan){
	var txt = confirm("Apakah anda yakin akan melakukan generate tagihan ini?");
	if(txt) {
		document.getElementById("act").value = "generate";
		document.getElementById("nimkey").value = nim;
		document.getElementById("kodeunitkey").value = kodeunit;
		document.getElementById("periodekey").value = periode;
		document.getElementById("jenistagihankey").value = jenistagihan;
		
		goSubmit();
	}
	}
	


</script>
</body>
</html>
