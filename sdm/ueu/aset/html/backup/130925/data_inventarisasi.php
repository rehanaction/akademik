<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_inventarisasi');
	
	//$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	//$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('perolehan'));
	require_once(Route::getModelPath('perolehandetail'));
	require_once(Route::getModelPath('histdepresiasi'));
	require_once(Route::getModelPath('seri'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Inventarisasi';
	$p_tbwidth = 800;
	$p_aktivitas = 'Inventarisasi';
	$p_listpage = Route::getListPage();
	
	$p_model = mPerolehan;
	$p_modeldet = mPerolehanDetail;

	//user
    $r_role = Modul::getRole();
    
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_unit = mCombo::unitSave($conn,false);
	$a_lokasi = mCombo::lokasi($conn,false);
	$a_jenisperolehan = mCombo::jenisperolehan($conn);
	$a_sumberdana = mCombo::sumberdana($conn);
	$a_coa = mCombo::coa($conn);
	$a_dasarharga = mCombo::dasarharga($conn);
	$a_kondisi = mCombo::kondisi($conn);
	$a_verify = array('1' => 'Verified');
	
	$now = date('Y-m-d');
	
	//init
	$isro = true;
	$isrokeu = true;
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];
	}

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idperolehan', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idjenisperolehan', 'label' => 'Jenis Perolehan', 'type' => 'S', 'option' => $a_jenisperolehan, 'default' => '101', 'add' => 'style="width:150px"', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglperolehan', 'label' => 'Tgl. Perolehan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti','maxlength' => 20, 'size' => 20, 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D', 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'nospk', 'label' => 'No. SPK','maxlength' => 20, 'size' => 20, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglspk', 'label' => 'Tgl. SPK', 'type' => 'D', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nopo', 'label' => 'No. PO','maxlength' => 20, 'size' => 20, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpo', 'label' => 'Tgl. PO', 'type' => 'D', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idsumberdana', 'label' => 'Sumber Dana', 'type' => 'S', 'option' => $a_sumberdana, 'add' => 'style="width:150px"', 'empty' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idsupplier', 'type' => 'H');
	$a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 35, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'type' => 'DT');
	$a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	
    $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 50, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idbarang1', 'type' => 'H', 'notnull' => true);
    $a_input[] = array('kolom' => 'qty', 'label' => 'Jumlah', 'type' => 'N', 'maxlength' => 6, 'size' => 6, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'harga', 'label' => 'Harga Satuan', 'type' => 'N,2', 'maxlength' => 17, 'size' => 14, 'notnull' => true, 'readonly' => $isrokeu);
    $a_input[] = array('kolom' => 'total', 'label' => 'Total Perolehan', 'type' => 'N,2', 'maxlength' => 17, 'size' => 14, 'readonly' => true);
    //$a_input[] = array('kolom' => 'iddasarharga', 'label' => 'Dasar Harga', 'type' => 'S', 'option' => $a_dasarharga,'default' => '1', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idkondisi', 'label' => 'Kondisi Perolehan', 'type' => 'S', 'option' => $a_kondisi,'default' => 'B', 'readonly' => $isro);
    //$a_input[] = array('kolom' => 'idcoa', 'label' => 'COA', 'type' => 'S', 'option' => $a_coa,'empty' => true, 'readonly' => $isro);

    $a_input[] = array('kolom' => 'thnprod', 'label' => 'Tahun Produksi', 'maxlength' => 4, 'size' => 5, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'merk', 'label' => 'Merk & Ukuran', 'maxlength' => 45, 'size' => 30, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'ukuran', 'label' => 'Ukuran', 'maxlength' => 45, 'size' => 30, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'type' => 'A', 'rows' => 3, 'cols' => 45, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglgaransi', 'label' => 'Garansi s/d', 'type' => 'D', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'kmgaransi', 'label' => 'Garansi', 'type' => 'N,2', 'maxlength' => 20, 'size' => 20, 'readonly' => $isro);

    //detail
	$a_detail = $p_modeldet::getInputAttr();

	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];

	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if($record['isverify'] == '1'){
		    $record['verifyuser'] = Modul::getUserDesc();
			$record['verifytime'] = date('Y-m-d H:i:s');
			list($p_posterr,$p_postmsg) = $p_model::doVerified($conn,$record,$r_key);
			
			if(!$p_posterr) $ok = mHistDepresiasi::setPenyusutan($conn, $r_key);
			
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet'){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
		print_r($det);
		if(empty($r_keydet)){
		    $record['idperolehan'] = $r_key;
			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else
			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_actdet == 'deletedet'){
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
	}


	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
    //ambil detail
	if(!empty($r_key)){
	    //$det = $p_modeldet::getRowByIDP($conn, $r_key);
	    $det = mSeri::getSeriByP($conn, $r_key);
	    
    	$a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];

	    if($r_isverify == '1')
	        $c_edit = false;
	    
	    $r_qtyin = (int)$a_mdata['qty'];
	    $r_qtynow = (int)$p_modeldet::getSumQty($conn, $r_key);

        if($r_isverify){
        	$a_btnprint = array();
        	$a_btnprint[] = array('id' => 'be_cetaklabel', 'label' => 'Label', 'onclick' => 'goCetakLabel()');
        	$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');
    	}
	}
	
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
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
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">					
					    <? if($r_isverify) { ?>
					    <img src="images/verified.png" style="position:absolute;left:70%" alt="Verified" width="300" />
					    <? } ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
				            <tr>
					            <td colspan="4" class="DataBG">Detail Perolehan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idperolehan') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idperolehan') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'nobukti') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'nobukti') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglbukti') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglbukti') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjenisperolehan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idjenisperolehan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'nospk') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'nospk') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglperolehan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglperolehan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglspk') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglspk') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpembukuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpembukuan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'nopo') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'nopo') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= ($r_qtyin == $r_qtynow) ? Page::getDataInput($row,'isverify') : '<i>Jumlah barang dengan total distribusi barang belum sama</i>' ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpo') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpo') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifyuser') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifytime') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
				            <tr>
					            <td colspan="4" class="DataBG">Detail Barang</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'barang') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            <?= Page::getDataInput($row,'barang') ?>
		                            <?= Page::getDataInput($row,'idbarang1') ?>
		                        </td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'qty') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            <?= Page::getDataInput($row,'qty') ?>&nbsp;&nbsp;
		                            <?= Page::getDataInput($row,'idsatuan') ?>
		                        </td>
	                        </tr>
	                        <?/*				    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'iddasarharga') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'iddasarharga') ?></td>
	                        </tr>
	                        */?>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idkondisi') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idkondisi') ?></td>
	                        </tr>					    
	                        <?/*
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idcoa') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idcoa') ?></td>
	                        </tr>
	                        */?>
				            <tr>
					            <td colspan="4" class="DataBG">Keterangan Tambahan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglgaransi') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            Tanggal:&nbsp;<?= Page::getDataInput($row,'tglgaransi') ?>&nbsp;&nbsp;/&nbsp;&nbsp;
		                            Km:&nbsp;<?= Page::getDataInput($row,'kmgaransi') ?>
		                        </td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'thnprod') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'thnprod') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'merk') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            <?= Page::getDataInput($row,'merk') ?> &nbsp;&nbsp;,
		                            <?= Page::getDataInput($row,'ukuran') ?>
		                        </td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'spesifikasi') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'spesifikasi') ?></td>
	                        </tr>					    
				            <tr>
					            <td colspan="4">&nbsp;</td>
				            </tr>
					    </table>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
					        <tr>
					            <td colspan="8" class="DataBG">Daftar Seri</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th width="60">ID. Lokasi</th>
					            <th width="150">Pemakai</th>
					            <th width="50">No. Seri</th>
					            <th>Barang</th>
					            <th width="100">Merk</th>
					            <th width="130">Spesifikasi</th>
					        </tr>
					        <?
				            $i = 0;
				            while($val = $det->FetchRow()){
				                $i++;
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idlokasi'] ?></td>
					            <td><?= $val['namalengkap'] ?></td>
					            <td><?= Aset::formatNoSeri($val['noseri']) ?></td>
					            <td><?= $val['barang'] ?></td>
					            <td><?= $val['merk'] ?></td>
					            <td><?= $val['spesifikasi'] ?></td>
					        </tr>
					        <?
					        }
					        if($i == 0){
					        ?>
					        <tr><td colspan="8" align="center"><b>-- Data tidak ditemukan --</b></td></tr>
					        <?
					        }
					        ?>
					    </table>
					</div>
				</center>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="iddetail" id="iddetail">
				<input type="hidden" name="format" id="format">
				
				<input type="hidden" name="from" id="from" value="inv">
				<input type="hidden" name="startno" id="startno">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_inventarisasidetail') ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";
var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	$('#btnAddDetail').click(function(){
        openDetail($('#key').val(), '');
	});
});

$("#checkall").click(function(){
    if($(this).is(':checked'))
        $(":checkbox").attr('checked', true);
    else
        $(":checkbox").attr('checked', false);
});

$(":checkbox").not("#checkall").click(function(){
    $("#checkall").attr('checked', false);
});

function goDetailDet(pkeydet){
    openDetail($('#key').val(), pkeydet);
}

function openDetail(pkey, pkeydet){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey, keydet : pkeydet},
        success: function(data){
            $.facybox(data);
        }
    });
}

function goVerify(){
    if($('#idlokasi').val() == '' || $('#idpegawai').val() == ''){
        alert('Lokasi atau pemakai belum ditentukan');
    }else{
        if(confirm('Sudah yakin untuk memverifikasi data ini ?')){
            $('#isverify').val('1');
            goSave();
        }
    }
}

function goUnVerify(){
    $('#isverify').val('0');
    goSave();
}

function goCetakBASTB() {
    $('#iddetail').val();
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbnew") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

function goCetakLabel() {
    var startno = prompt("Print label dimulai label ke ?","1");
    if(startno != null && startno != ""){
	    $('#startno').val(startno);

	    $('#pageform').attr('action','<?= Route::navAddress("set_label") ?>');
	    $('#pageform').attr('target','_blank');
	    goSubmit();
	    $('#pageform').attr('action','');
	    $('#pageform').attr('target','');
    }
}

</script>
</body>
</html>
