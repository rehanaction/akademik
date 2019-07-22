<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	//$conn->debug = true;
	// include
	require_once(Route::getModelPath('honor'));
	require_once(Route::getModelPath('gaji'));
	require_once(Route::getUIPath('combo'));
		
	$p_model = mHonor;
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');		
	$r_jenispegawai = CStr::removeSpecial($_POST['jenispegawai']);
	$r_status = CStr::removeSpecial($_POST['status']);

	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);	
	$a_jenispegawai = $p_model::getCAllJenisDosen($conn);
	$l_jenispegawai = UI::createSelect('jenispegawai',$a_jenispegawai,$r_jenispegawai,'ControlStyle',true,'onchange="goSubmit()"');
	$a_status = $p_model::getStatusRateHonor($conn);
	$l_status = UI::createSelect('status',$a_status,$r_status,'ControlStyle',true,'onchange="goSubmit()"');
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nodosen', 'label' => 'Kode Dosen', 'align' => 'center', 'width' => '80px');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama Dosen','filter'=>'sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Unit Homebase');
	$a_kolom[] = array('kolom' => 'namapendidikan', 'label' => 'Pendidikan');
	$a_kolom[] = array('kolom' => 'procpph', 'label' => 'Proc. PPh. Sistem (%)','align'=>'center');
	
	// properti halaman
	$p_title = 'Daftar Tarif Mengajar Dosen';
	$p_tbwidth = 1150;
	$p_aktivitas = 'ANGGARAN';
	$p_dbtable = "ga_ajardosen";
	$p_key = "idpegawai";	
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	if (empty($r_sort))
		$r_sort = 'nodosen';
		
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_jenispegawai)) $a_filter[] = $p_model::getListFilter('jenispegawai',$r_jenispegawai);
	if(!empty($r_status)) $a_filter[] = $p_model::getListFilter('status',$r_status);
		
	$sql = $p_model::listQueryHitHonor();
	$a_sql = $p_model::getListQuery($r_sort,$a_filter,$sql,$table,$r_row);
		
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($p_posterr,$p_postmsg) = $p_model::saveHonor($conn,$_POST);
		
		$ok = Query::isOK($p_posterr);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
		
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jenis Pegawai', 'combo' => $l_jenispegawai);
	$a_filtercombo[] = array('label' => 'Jenis Pegawai', 'combo' => $l_status);
	
	$a_msjnsrate = $p_model::getMsJnsRate($conn);
	$a_ratehonor = $p_model::getRateHonor($conn);
	$p_colnum = count($a_kolom)+count($a_msjnsrate)+5;
	
	//pengaturan role
	$c_dppu = false;
	if(Modul::getRole() == 'dppu')
		$c_dppu = $c_insert;
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper" style="width:<?= $p_tbwidth+50 ?>px;">
		<div class="SideItem" id="SideItem" style="width:<?= $p_tbwidth+15 ?>px;">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?php require_once('inc_listfilter.php'); ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<? if ($c_insert) { ?>
							<div class="right">
								<div class="TDButton" onclick="goSave()" style="padding:7px 0px 7px;width:95px;position:relative;left:-5px;top:5px;">
								<img style="position:relative;left:-60px;top:-7px;" src="images/disk.png">
								<span style="position:relative;left:15px">Simpan</span>
								</div>
							</div>
							<? } ?>
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
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?> rowspan="2"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						<th width="70" rowspan="2">Proc. PPh (%)</th>
						<th width="70" rowspan="2">Biaya Transfer</th>
						<th width="70" rowspan="2">Transport</th>
						<th colspan="<?= count($a_msjnsrate)?>">Rate Mengajar</th>
						<? if($c_edit and !$c_dppu) { ?>
						<th width="50" rowspan="2">Valid <input type="checkbox" id="checkall" title="Cek semua daftar halaman <?= $r_page?>" onClick="toggle(this)"></th>
						<?	} ?>
						<th width="50" rowspan="2">Info</th>
					</tr>
					<tr>
						<?
						if(!empty($a_msjnsrate)){
							foreach($a_msjnsrate as $keyjnsrate => $msjnsrate) {?>
								<th>
									<?= $msjnsrate['namajnsrate']?>
									<input type="hidden" name="kodejnsrate[]" id="kodejnsrate" value="<?= $keyjnsrate?>">
								</th>
							<?}
						}?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,$p_key);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="right">
							<input type="hidden" name="id[]" value="<?= $row['pidpegawai']; ?>" />
							<?= UI::createTextBox('procpphmanual_'.$row['pidpegawai'],$row['procpphmanual'],'ControlStyle',5,5,$c_dppu,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"'); ?>
						</td>
						<td align="right">
							<?= UI::createTextBox('biatrans_'.$row['pidpegawai'],CStr::formatNumber($row['biatrans']),'ControlStyle',10,10,$c_dppu,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"'); ?>
						</td>
						<td align="right">
							<?= UI::createTextBox('transport_'.$row['pidpegawai'],CStr::formatNumber($row['transport']),'ControlStyle',10,10,$c_dppu,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"'); ?>
						</td>
						<?
						if(!empty($a_msjnsrate)){
							foreach($a_msjnsrate as $keyjnsrate => $msjnsrate) {?>
								<td align="right">
								<? 
									if($msjnsrate['ismanual']=='Y'){
										echo UI::createTextBox('nominal_'.$row['pidpegawai'].'_'.$keyjnsrate,CStr::formatNumber($a_ratehonor[$row['idpegawai']][$keyjnsrate]),'ControlStyle',14,14,($c_edit and !$c_dppu),'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"');
									}
									else{
										echo CStr::formatNumber($a_ratehonor[$row['pidpegawai']][$keyjnsrate]);
									}
								?>
								</td>
							<?}
						}?>
						<? if($c_edit and !$c_dppu) { ?>
						<td align="center">
							<input type="checkbox" id="check" name="check<?= $row['idpegawai'] ?>" title="Cek untuk merubah validasi rate mengajar" <?= $row['isvalid'] == 'Y' ? 'checked' : ''?>>
						</td>
						<? }?>
						<td align="center"><img src="images/magnify.png" style="cursor:pointer" onclick="openDetail('<?= $row['pidpegawai'] ?>')"></td>
					</tr>
					<?	}}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goLimit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= Page::getTheLastPage();?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.balloon.min.js"></script>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var detform = "<?= Route::navAddress('pop_detailpegawai') ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSave(){
	var simpan = confirm("Anda yakin untuk menyimpan tarif mengajar ini ?");
	if (simpan){
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

function goHitung(){
	var hitung = confirm("Anda yakin untuk menghitung tarif mengajar ini ?");
	if (hitung){
		document.getElementById("act").value = "hitung";
		goSubmit();
	}
}

function toggle(elem) {
	var check = elem.checked;
	
	$("[id='check']").attr("checked",check);
}

function openDetail(pkey){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey},
        success: function(data){
            $.facybox(data);
        }
    });
}
</script>
</body>
</html>
