<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	
	// include
	require_once(Route::getModelPath('mhsasuransi'));
	require_once(Route::getModelPath('asuransi'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('klaimasuransi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Klaim Asuransi';
	$p_tbwidth = 600;
	$p_aktivitas = 'Asuransi Mahasiswa';
	$p_listpage = Route::getListPage();
	
	$p_model = mKlaimasuransi;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_asuransi = mAsuransi::getArray($conn);

	// struktur view
	$a_input = array();	
	$a_input[] = array('kolom' => 'idasuransimhs', 'label' => 'Nama Asuransi', 'type' => 'S', 'option' => $a_asuransi);
	$a_input[] = array('kolom' => 'nim', 'label' => 'Mahasiswa');
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl Pengajuan', 'type' => 'D');
	$a_input[] = array('kolom' => 'jumlahklaim', 'label' => 'Jumlah Pengajuan', 'type' => 'N');
	$a_input[] = array('kolom' => 'tglkejadian', 'label' => 'Tgl Kejadian', 'type' => 'D');
	$a_input[] = array('kolom' => 'namakejadian', 'label' => 'Nama Kejadian');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'M');
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'C', 'option' => array('-1' => ''));
	$a_input[] = array('kolom' => 'isditerima', 'label' => 'Disetujui', 'type' => 'C', 'option' => array('-1' => ''));
	$a_input[] = array('kolom' => 'tglterima', 'label' => 'Tgl Disetujui', 'type' => 'D');

	// mengambil data pelengkap
	$a_detail = array();
	
	$t_detail = array();
	$t_detail[] = array('kolom' => 'namasyarat', 'label' => 'Nama Syarat');
	
	$a_detail['syarat'] = array('key' => mAsuransi::getDetailInfo('syarat','key'), 'data' => $t_detail);
		
	// ada aksi
	$r_act = $_POST['act'];	
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($post['isvalid']))
			$record['isvalid'] = 0;
			
		if(empty($post['isditerima']))
			$record['isditerima'] = 0;
		
		foreach ($a_input as $key => $value) {
			if($value['type']=='M')
				$record[$value['kolom'].':skip'] = true;
		}
		
		if(empty($r_key)) {
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}
		else{
			
			//delete dulu syarat
			list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_key,'syarat');
			
			//insert syarat
			if(!empty($_POST['a_syaratmhs'])){
				$recd['idklaim'] = $r_key;
				$recd['idasuransimhs'] = $_POST['idasuransimhs'];
				
				foreach($_POST['a_syaratmhs'] as $val){
					$recd['kodesyaratklaim'] = $val;
					list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$recd,'syarat');
					
					if(!empty($p_posterr))
						break;
				}
			}
			//insert tabel utama
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}	
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		$record = array('idasuransi' => $r_key);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$t_detail['kolom']];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = mSyaratasuransi::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}

	// cek data
	if(!empty($r_key)) {
		$a_cek = $p_model::getData($conn,$r_key);		
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	if(!empty($r_key)) {
		$rowd = array();
		$a_klaim = $p_model::getData($conn,$r_key);
		$a_mhsasuransi = mMhsasuransi::getData($conn,$a_klaim['idasuransimhs']);
		$namaasuransi = mAsuransi::getNama($conn,$a_mhsasuransi['idasuransi']);
		
		$r_mahasiswa = $a_mhsasuransi['nim'];
		if(!empty($a_mhsasuransi)){
			$r_namamahasiswa = $r_mahasiswa.' - '.mMahasiswa::getNama($conn,$r_mahasiswa,false);
			$r_status = mMahasiswa::getDataSingkat($conn,$r_mahasiswa);
			$statusmhs = $r_status['namastatus'];
			$nopolis = $a_mhsasuransi['nopolis'];
		}
		$rowd += $p_model::getSyarat($conn,$a_klaim['idasuransimhs'],'syarat',$post);
	}
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
			<form name="pageform" id="pageform" method="post">
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
						
						$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG">Mahasiswa</td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namamahasiswa,
									UI::createTextBox('mahasiswa',$r_namamahasiswa,'ControlStyle',30,30)) ?>
								<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Status Mahasiswa</td>
							<td class="RightColumnBG">
								<span id="span_statusmhs"><?=$statusmhs?></span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idasuransimhs') ?></td>
							<td class="RightColumnBG">
								<span id="show"><?=$namaasuransi?></span>
								<span id="edit" style="display:none"><?=Page::getDataInput($row,'idasuransimhs')?></span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG">Nomor Polis</td>
							<td class="RightColumnBG">
								<span id="span_nopolis"><?=$nopolis?></span>
							</td>
						</tr>
						<?= Page::getDataTR($row,'tglpengajuan') ?>
						<?= Page::getDataTR($row,'jumlahklaim') ?>
						<?= Page::getDataTR($row,'tglkejadian') ?>
						<?= Page::getDataTR($row,'namakejadian') ?>
						<?= Page::getDataTR($row,'keterangan') ?>
						<?= Page::getDataTR($row,'isvalid') ?>
						<?= Page::getDataTR($row,'isditerima') ?>
						<?= Page::getDataTR($row,'tglterima') ?>
					</table>
					<? if(!empty($r_key)) { ?>
					<br>
					<?	/**********/
						/* DETAIL */
						/**********/
						
						$t_field = 'syarat';
						$t_colspan = count($a_detail[$t_field]['data'])+3;						
						$t_dkey = $a_detail[$t_field]['key'];
						
						if(!is_array($t_dkey))
							$t_dkey = explode(',',$t_dkey);
							
					?>
					<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
						<tr>
							<td colspan="<?= $t_colspan ?>" class="DataBG">Daftar Syarat</td>
						</tr>
						<tr>
							<th align="center" class="HeaderBG" width="30">No</th>
						<?	foreach($a_detail[$t_field]['data'] as $datakolom) { ?>
							<th align="center" class="HeaderBG"><?= $datakolom['label'] ?></th>
						<?	} ?>
							<th align="center" class="HeaderBG" width="30" colspan="2"> Check</th>
						</tr>
						<?	$i = 0;
							if(!empty($rowd)) {
								foreach($rowd as $rowdd) {
									if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
									
									$t_keyrow = array();
									foreach($t_dkey as $t_key)
										$t_keyrow[] = $rowdd[trim($t_key)];
									
									$t_key = implode('|',$t_keyrow);
						?>
						<tr valign="top" class="<?= $rowstyle ?>">
							<td><?= $i ?></td>
						<?		foreach($a_detail[$t_field]['data'] as $datakolom) { 
							?>
							<td<?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?=$rowdd['namasyaratklaim']?></td> 
						<?		} ?>
							<td align="center">

								<span id="show">
									<?php 
										if(!empty($rowdd['syarat'])) 
											echo '<img src="images/check.png">';
										else
											echo '';
									?>
								</span>		
								<span id="edit" style="display: none;">
									<input id="a_syaratmhs[]" name="a_syaratmhs[]" value="<?= $rowdd['kodesyaratklaim'] ?>" type="checkbox"  <?= ($rowdd['syarat'] == 1)? ' checked' : '' ?> >
								</span>	
							</td>
						</tr>
						<?
							}
							}
							if($i == 0) { ?>
						<tr>
							<td align="center" colspan="<?= $t_colspan ?>">Data kosong</td>
						</tr>
						<?	} ?>
					</table>
					<? } ?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
});

$('#nim').change(function(){
     loadAsuransi();
	 loadStatus();
});

$('#idasuransimhs').change(function(){
	loadNoPolis();
});

// ajax ganti kota
function loadAsuransi() {
	var param = new Array();
	param[0] = $("#nim").val();
	param[1] = "<?= $r_idasuransimhs ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optasuransi", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#idasuransimhs").html(data).triggerHandler("change");
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
loadAsuransi();

function loadStatus() {
	var param = new Array();
	param[0] = $("#nim").val();
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "getstatusmhs", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#span_statusmhs").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function loadNoPolis() {
	var param = new Array();
	param[0] = $("#idasuransimhs").val();
	
	if(param[0] == "") {
		$("#span_nopolis").empty();
	}
	else {
		var jqxhr = $.ajax({
						url: ajaxpage,
						timeout: ajaxtimeout,
						data: { f: "getnopolis", q: param }
					});
		
		jqxhr.done(function(data) {
			$("#span_nopolis").html(data);
		});
		jqxhr.fail(function(xhr,status) {
			alert(status);
		});
	}
}
</script>
</body>
</html>
