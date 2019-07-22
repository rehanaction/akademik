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
                    <? if(!$r_key){?>
                    <tr>
                    	<td class="LeftColumnBG">Tagihan untuk</td>
                    	<td>
                        <input type="radio" name="pilihan" id="pilihan" value="nim" checked> Mahasiswa
                        <input type="radio" name="pilihan" id="pilihan" value="nopendaftar"> Pendaftar
                        </td>
                    </tr>
                    <? }?>
                    <tr>
                    	<td class="LeftColumnBG" width="150" style="white-space:nowrap"><strong>No Indentitas</strong></td>
                        <td>
                        <? if(!$r_key){?>
                        	<input type="text" name="txtnim" id="txtnim">
                        	<input type="button" value="Cek" onClick="getDatamhs()">
                        	<?php /*?><input type="hidden" name="nim" id="nim" value="<?=$nim?>"><?php */?>
                            <br>
                            <font color="#999999"><em>inputkan NIM bila mahasiswa, no pendaftar bila pendaftar</em></font>
                        <? } else {?>
                        <input type="text" readonly class="ControlRead" value="<?=$data['nim']?$data['nim']:$data['nopendaftar']?>">
                        <?php /*?><input type="hidden" name="nim" id="nim" value="<?=$nim?>"><?php */?>
						<? } ?>
                        </td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG">Nama</td>
                    	<td>
                        <span id="nama"><?=$data['nama']?></span>
                        </td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG">Jurusan</td>
                    	<td>
                        <span id="namaunit"><?=$data['namaunit']?></span>
                        </td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG">Sistem Kuliah</td>
                    	<td>
                        <span id="namasistem"><?=$data['sistemkuliah']?></span>
                        </td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG">Jalur Penerimaan</td>
                    	<td>
                        <span id="namajalur"><?=$data['jalurpenerimaan']?></span>
                        </td>
                    </tr>
                    <tr>
                    	<td colspan="2" bgcolor="#CCCCCC" align="center"><strong>Detail Tagihan</strong></td>
                    </tr>
					<?	$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
								//echo Page::getDataTR($t_row,$t_row['id']);
						
					?>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">
								<?= $t_row['label'] ?>
								<?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
							</td>
							<td class="RightColumnBG">
								<span id="show"><?= $t_row['value'] ?></span>
								<span id="edit" style="display:none">
								<? if($t_row['id'] == 'bulantahun'){
									echo UI::createSelect('bulan',$arr_bulan,substr($t_row['realvalue'],5,2),'ControlStyle',$r_key?false:true,'',true,'');
									echo ' '.UI::createSelect('tahun',$arr_tahun,substr($t_row['realvalue'],0,4),'ControlStyle',$r_key?false:true,'',true,'');
									 }else {?>
                                	<?= $t_row['input'] ?>
                                <? }?>
                                </span>
							</td>
						</tr>
					<?	} ?>
					</table>
               </div>
				</center>
				 <div style="clear:both"></div>
                    <div>
                        <fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                            <legend> Keterangan </legend>
                            Bulan Tahun hanya untuk Tagihan Bulanan
                        </fieldset>
                    </div>
				</div>
				<input type="hidden" name="act" id="act"> 
                <input type="hidden" name="cek" id="cek" value="<?=$r_key?>">
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


function getDatamhs() {
	var param = new Array();
	
	$("#nama").html('');
	$("#namasistem").html('');
	$("#namajalur").html('');
	$("#namaunit").html('');
	$("#cek").val('0');

	if(document.getElementById("pilihan").checked)
		param[1] = 'nim';
	else
		param[1] = 'nopendaftar';
		
	param[0] = $("#txtnim").val();
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "datamhs", q: param }
				});
	
	jqxhr.done(function(data) {
		datas = JSON.parse(data);
		$("#nama").html(datas.nama);
		$("#namasistem").html(datas.namasistem);
		$("#namajalur").html(datas.namajalur);
		$("#namaunit").html(datas.namaunit);
		if(datas.nama)
		$("#cek").val('1');
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
</script>
</body>
</html>
