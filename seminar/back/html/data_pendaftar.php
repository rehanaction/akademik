<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
        // hak akses
	$a_auth = Modul::getFileAuth();
 
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];	
	$c_upload = $a_auth['canother']['U'];
	// include
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('jadwalujian'));
	require_once(Route::getModelPath('kota'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getModelPath('actionpendaftar'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getModelPath('kuisioner'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	require_once($conf['model_dir'].'m_lokasi.php');
	
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Pendaftar';
	$p_tbwidth = 900;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	$p_foto = uForm::getPathImageMahasiswa($conn,$r_key); //membuat debug menjadi false, kalo ada debug tidak bisa upload foto :(        
	$p_model = mPendaftar;
	
	if ($r_key)
	list($p_beasiswa,$p_registrasi,$p_semesterpendek) = $p_model::getValidasipotongan($conn,$r_key); 
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
 
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
                
	// struktur view
	$r_act = $_POST['act'];
	$r_subkey = CStr::removeSpecial($_POST['subkey']);
	
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
		
	 $arrJurusan = mCombo::jurusan($conn);	
	 $a_peringkat = array('A'=>'A','B'=>'B','C'=>'C');
	
	$a_input = $p_model::inputColumn($conn,$r_key,'','','',$p_beasiswa,$p_registrasi,$p_semesterpendek);
	$a_input[] = array('kolom' => 'tokenpendaftaran', 'label' => 'Token / PIN', 'maxlength' => 21, 'size' => 15, 'readonly' => false,'add'=>'onBlur="isExist()"');
	$a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur Penerimaan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::jalur($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'periodedaftar', 'label' => 'Periode Daftar', 'type' => 'S', 'notnull' => true, 'option' => mCombo::periode($conn),'empty'=>true, 'readonly' => $p_edit);
	$a_input[] = array('kolom' => 'idgelombang', 'label' => 'Gelombang', 'type' => 'S', 'notnull' => true, 'option' => mCombo::gelombang($conn),'empty'=>true);
	$a_input[] = array('kolom' => 'pilihanditerima', 'label' => 'Pilihan diterima', 'type' => 'S', 'option' => $arrJurusan, 'empty' => true);
	$a_input[] = array('kolom' => 'nopendaftar', 'label' => 'No Pendaftar', 'maxlength' => 30, 'size' => 30, 'readonly' => $p_edit);
	$a_input[] = array('kolom' => 'isbatalnim', 'label' => 'Batal NIM?', 'type'=>'S', 'option'=>array(0=>'Tidak',-1=>'Batal'));
	$a_input[] = array('kolom' => 'keteranganbatalnim', 'label' => 'Keterangan batal NIM', 'type'=>'A', 'size'=>5, 'row'=>5);
	$a_input[] = array('kolom' => 'idperingkat', 'label' => 'Peringkat', 'type'=>'S', 'option'=>$a_peringkat,'empty'=>'-Pilih Peringkat-');
	
	$a_input_quisioner = mKuisioner::getColumn($conn);
	

		
        //ada aksi
	if($r_act == 'save' and $c_edit) {
		$record = array();
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		list($post,$record_quisioner) = uForm::getPostRecord($a_input_quisioner,$_POST);
		
				if (!empty($_POST['xasalsmu']) and ($record['asalsmu']=='null' or empty($record['asalsmu']))){
					list($p_posterr, $record['asalsmu']) = $p_model::insertSMU($conn,$_POST['xasalsmu'],$record['propinsismu'],$record['kodekotasmu']);
					}


			if(empty($r_key)){
				
				$record['nopendaftar'] = mPendaftar::nopendaftar($conn,$record['periodedaftar'], $record['idgelombang'], $record['jalurpenerimaan']);
				$record['pilihanditerima'] = $record['pilihan1'];
				$record['isadministrasi'] = -1;
				$record['lulusujian'] = -1;
				$record['isreg'] = ($record['mhstransfer']==1 ? -1 : 0);
				$record['tgldaftar'] = date('Y-m-d');
				$record['password'] = md5(str_replace('-','',$record['tgllahir']));
				$record['tglregistrasi'] = date('Y-m-d');
				$record_quisioner['nopendaftar'] = $record['nopendaftar'];

				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
				if (!$p_posterr)
				mKuisioner::insertCRecord($conn,$a_input_quisioner,$record_quisioner,$r_key);

				if (!$p_posterr)
					list($p_posterrtagihan,$p_postmsgtagihan,$jml) = mActionpendaftar::generateTagihanKUA($conn,$record);
				
			}else{ 
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
				$record_quisioner['nopendaftar'] = $r_key;
				$cek = mKuisioner::isDataExist($conn,$r_key);
				if ($cek)
					mKuisioner::updateCRecord($conn,$a_input_quisioner,$record_quisioner,$r_key);
				else
					mKuisioner::insertCRecord($conn,$a_input_quisioner,$record_quisioner,$r_key);
			}	
			
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_name = CStr::cEmChg($t_detail['nameid'],$t_detail['kolom']);
			$a_value[$t_name] = $_POST[$r_detail.'_'.$t_name];
		}
		
		list(,$record) = uForm::getPostRecord($a_detail[$r_detail]['data'],$a_value);
		$record['nim'] = $r_key;
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}
	else if($r_act == 'savefoto' and $c_upload) {
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,150);
			
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;
		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
		
		uForm::reloadImageMahasiswa($conn,$r_key,$msg);
	}
	else if($r_act == 'deletefoto' and $c_upload) {
		@unlink($p_foto);
		
		uForm::reloadImageMahasiswa($conn,$r_key);
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key);
		
	}
        
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$row_kuisioner = mKuisioner::getDataEdit($conn,$a_input_quisioner,$r_key,$post);
	
	$arrTagihan = mTagihan::getTagihan($conn, $r_key);
 
        $r_jalur=Page::getDataValue($row,'jalurpenerimaan');
        $r_gel=Page::getDataValue($row,'idgelombang');
        $r_periode=Page::getDataValue($row,'periodedaftar');
        
        $r_kodekota = Page::getDataValue($row,'kodekota');
        $r_kodekotaayah = Page::getDataValue($row,'kodekotaayah');
        $r_kodekotaibu = Page::getDataValue($row,'kodekotaibu');
        
        $r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
        $r_asalsmu = Page::getDataValue($row,'asalsmu');
        $r_kodekotapt = Page::getDataValue($row,'kodekotakotak');
        $r_kodekotalahir=Page::getDataValue($row,'kodekotalahir');
		$r_kodekotalahirayah = Page::getDataValue($row,'kodekotalahirayah');
		$r_kodekotalahiribu = Page::getDataValue($row,'kodekotalahiribu');
		$r_kodekotakantor = Page::getDataValue($row,'kodekotakantor');

   
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'User ini Tidak Mempunyai Profile';
	}
	if ($r_key){
		list($tarifregistrasi,$totalbiayasemester) = $p_model::getTarifregistrasi($conn,$r_key);
	}
    
    $r_validasibeasiswa=Page::getDataValue($row,'isvalidbeasiswa');
    $r_validasiregistrasi=Page::getDataValue($row,'isvalidregistrasi');
    $r_validasisemesterpendek=Page::getDataValue($row,'isvalidsemesterpendek');
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/jquery.js"></script>
	<script type="text/javascript" src="scripts/bootstrap.js"></script>
	<script type="text/javascript" src="scripts/common.js"></script>
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		#table_evaluasi { border-collapse:collapse }
		#table_evaluasi .td_ev { border:1px solid #666 }
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
        <script type="text/javascript">
$(".subnav").hover(function() {
    $(this.parentNode).addClass("borderbottom");
}, function() {
    $(this.parentNode).removeClass("borderbottom");
});

</script>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				
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
					<?	$a_required = array();
						/*foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							}*/
						if (!empty($r_key))
						$a_required = array('nama','periodedaftar','jalurpenerimaan','idgelombang','pilihan1','tgllahir');
						else
						$a_required = array('nama','periodedaftar','jalurpenerimaan','sistemkuliah','idgelombang','pilihan1','tgllahir');
						
					?>						
					
					<?//	$a_required = array('nama', 'tokenpendaftaran','periodedaftar','jalurpenerimaan','idgelombang','sistemkuliah','pilihan1','tgllahir','jurusansmaasal','thnlulussmaasal'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					<tr>
						<td colspan="3">
							<div style="width:<?= $p_tbwidth-50 ?>px; border-width:1px; background: #eceaea; border-radius:5px; border-style: solid; border-color:#a09e9e; padding: 10px;">
								Token dan PIN didapat dari data host-to-host
							</div>
						</td>
					</tr>
					<tr>
					<? if(!empty($r_key)){?>
						<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nopendaftar') ?></td>
						<td class="RightColumnBG"><?= Page::getDataInput($row,'nopendaftar') ?></td>
						<td align="center" valign="top" rowspan="<?= 8+(empty($a_evaluasi) ? 0 : 1) ?>">
							<?= uForm::getImageMahasiswa($conn,$r_key,$c_upload) ?>
							<br>
							<?php if ($c_upload){?>
							<span style="font-size: 9px">untuk upload / hapus foto klik foto</span><br>
							<input type="button" style="padding:4px" value="Upload" class="ControlStyle" onclick="setUpload()">
							<input type="button" style="padding:4px" value="Hapus" class="ControlStyle" onclick="goHapusFoto()">
							<input type="button" style="padding:4px" value="Capture" class="ControlStyle" onClick="popup('index.php?page=capture_cam&nopendaftar=<?=$r_key?>&jalur=<?=$r_jalur?>&gelombang=<?=$r_gel?>&periode=<?=$r_periode?>',250,360);">
							<?php }?>
						</td>
					<? } ?>
					</tr>
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tokenpendaftaran') ?></td>
						<td class="RightColumnBG">
							<?= Page::getDataInput($row,'tokenpendaftaran') ?>
							<div style="display:none" id="cektoken">Sedang pengecekan token...</div>
						</td>
					</tr>
						<?= Page::getDataTR($row,'nama') ?>
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">
							Info Pendaftaran<br>
							<p style="width:50px; font-size: 8px;">Data harus sesuai dengan<br> jalur yang dibuka</p>
						</td>
						<td class="RightColumnBG">
							<table>
								<tr>
									<td><?= Page::getDataLabel($row,'periodedaftar') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'periodedaftar') ?></td>
								</tr>
								<tr>
									<td><?= Page::getDataLabel($row,'jalurpenerimaan') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'jalurpenerimaan') ?></td>
								</tr>
								<tr>
									<td><?= Page::getDataLabel($row,'idgelombang') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'idgelombang') ?></td>
								</tr>
								<tr>
									<td><?= Page::getDataLabel($row,'sistemkuliah') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'sistemkuliah') ?></td>
								</tr>
							</table>
						</td>
					</tr>
					<tr>
						<td class="LeftColumnBG" style="white-space:nowrap">Info Pilihan SPMB</td>
						<td class="RightColumnBG">
							<table>
								<tr>
									<td><?= Page::getDataLabel($row,'pilihan1')?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'pilihan1')?></td>
								</tr>
								<tr>
									<td><?= Page::getDataLabel($row,'pilihan2')?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'pilihan2')?></td>
								</tr>
								<?
								if(!empty($r_key)){ ?>
								<tr>
									<td><?= Page::getDataLabel($row,'pilihanditerima') ?></td>
									<td>:</td>
									<td><?= Page::getDataInput($row,'pilihanditerima') ?></td>
								</tr>
								<? } ?>
							</table>
						</td>
					</tr>
                    <? /*<tr>
						<td class="LeftColumnBG">Tarif Registrasi</td>
						<td class="RightColumnBG">Rp. <?= number_format($tarifregistrasi['total'])?></td>
					</tr>
                    <tr>
						<td class="LeftColumnBG">Biaya Per Semester</td>
						<td class="RightColumnBG">Rp. <?= number_format($totalbiayasemester)?></td>
					</tr>
					*/ ?>
					<?= Page::getDataTR($row,'isbatalnim') ?>
					<?= Page::getDataTR($row,'keteranganbatalnim') ?>		
					<?= Page::getDataTR($row,'idperingkat') ?>		
					</table>

										
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
                    <ul>
						<li class="active"><a id="tablink" href="javascript:void(0)">Data Biodata</a></li>
						<li><a id="tablink" href="javascript:void(0)">Data Sekolah</a></li>
						<li><a id="tablink" href="javascript:void(0)">Data Keluarga</a></li>
						<li><a id="tablink" href="javascript:void(0)">Info Lain</a></li>
						<li><a id="tablink" href="javascript:void(0)">Tagihan Pendaftar</a></li>
						<li><a id="tablink" href="javascript:void(0)">Quisiner</a></li>
                    </ul>
                    
                    <div class="tab-content">
						<? require_once($conf['view_dir'].'xinc_tab_biodata.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_akademik.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_informasi.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_informasilain.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_tagihan.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_quisioner.php'); ?>
                    </div>
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
<script type="text/javascript" src="scripts/cstr.js"></script>

<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var ajax = "<?= Route::navAddress("ajax") ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {

	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	loadKotaLahir();
	loadKota();
	
	loadKotaSMU();
	loadKotaPTAsal();
	
	loadKotaayah();
	loadKotaibu();	
	loadKotaKantor();
	loadSMU();

	$("#xasalsmu").xautox({strpost: "f=getSmu", targetid: "asalsmu"});
	$('#nama').upperFirstAll();

	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
function numberOnly(evt) {
    evt = (evt) ? evt : window.event
    var charCode = (evt.which) ? evt.which : evt.keyCode
    if (charCode > 31 && (charCode < 48 || charCode > 57)) {
        return false
    }
    return true
} 
// ajax ganti kota
function loadKotaLahir() {
	var param = new Array();
	param[0] = $("#kodepropinsilahir").val();
	param[1] = "<?= $r_kodekotalahir ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotalahir").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
// ajax ganti kota
function loadKota() {
	var param = new Array();
	param[0] = $("#kodepropinsi").val();
	param[1] = "<?= $r_kodekota ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekota").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
// ajax ganti kota
function loadKotaSMU() {
	var param = new Array();
	param[0] = $("#propinsismu").val();
	param[1] = "<?= $r_kodekotasmu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotasmu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaPTAsal() {
	var param = new Array();
	param[0] = $("#propinsiptasal").val();
	param[1] = "<?= $r_kodekotapt ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotapt").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaayah() {
	var param = new Array();
	param[0] = $("#kodepropinsiayah").val();
	param[1] = "<?= $r_kodekotaayah ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotaayah").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaibu() {
	var param = new Array();
	param[0] = $("#kodepropinsiibu").val();
	param[1] = "<?= $r_kodekotaibu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotaibu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function loadKotaKantor() {
	var param = new Array();
	param[0] = $("#kodepropinsikantor").val();
	param[1] = "<?= $r_kodekotakantor ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optkota", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#kodekotakantor").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}


function loadSMU() {
	var param = new Array();
	param[0] = $("#kodekotasmu").val();
	param[1] = "<?= $r_asalsmu ?>";
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optsmu", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#asalsmu").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
} 
function getDetailSmu(){
	if (document.getElementById("asalsmu").value != ''){
		var e = document.getElementById("asalsmu");
		var str = e.options[e.selectedIndex].text;

		asalsmu = str[5];
	}
	
	if((asalsmu == '*') || ($("#asalsmu").val() == '*')){
		document.getElementById('namasmu').style.display = 'table-row';
		document.getElementById('alamatsmu').value = "";
		document.getElementById('telpsmu').value = "";
	}else{
		document.getElementById('namasmu').style.display = 'none';
		var posted = "act=getDetailSmu&q[]="+$("#asalsmu").val();
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			var text = text.split('#');
			document.getElementById('alamatsmu').value = text[0];
			document.getElementById('telpsmu').value = text[1];
		});
	}
}

function popup(url,width,height){
	var left   = (screen.width  - width)/2;
	var top    = (screen.height - height)/2;
	var params = 'width='+width+', height='+height;
	params += ', top='+top+', left='+left;
	params += ', directories=no';
	params += ', location=no';
	params += ', menubar=no';
	params += ', resizable=no';
	params += ', scrollbars=yes';
	params += ', status=no';
	params += ', toolbar=no';
	newwin=window.open(url,'windowname5', params);
	
	if (window.focus) {newwin.focus()}
	return false;
}

function upp(elem){
	var str = elem.val();
	str = str.toLowerCase().replace(/\b[a-z]/g, function(letter) {
		return letter.toUpperCase();
	});	
}

function isExist(){
	document.getElementById("cektoken").style.display='block';
    var token = document.getElementById("tokenpendaftaran").value;
    
    $.ajax({
	type: "POST",
	url: "<?= Route::navAddress('ajax') ?>",
	data: "act=token&token="+token,
	timeout: 20000,
        
	success: function(data) {
            datas = JSON.parse(data);
            document.getElementById("periodedaftar").value=datas.periodedaftar;
            document.getElementById("jalurpenerimaan").value=datas.jalurpenerimaan;
            document.getElementById("idgelombang").value=datas.idgelombang;
            document.getElementById("sistemkuliah").value=datas.sistemkuliah;
            //alert(data)
	},
        error: function(obj,err) {
	    if(err == "timeout")
		alert("Token tidak dikenali.");
	}
    });
    
    document.getElementById("cektoken").style.display='none';
}

 
</script>
</body>
</html>
