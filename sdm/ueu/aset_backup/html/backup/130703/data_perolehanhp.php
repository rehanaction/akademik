<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_perolehanhp');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = true;

	$c_verify = $c_other;
	
	
	// include
	require_once(Route::getModelPath('transhp'));
	require_once(Route::getModelPath('transhpdetail'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
	$r_tok = 'T';
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Perolehan Habis Pakai';
	$p_tbwidth = 800;
	$p_aktivitas = 'perolehan habis pakai';
	$p_listpage = Route::getListPage();

	$p_model = mTransHP;
	$p_modeldet = mTransHPDetail;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_unit = mCombo::unitSave($conn,false);
	$a_jenisperolehanhp = mCombo::jenisperolehanhp($conn);
	
	$now = date('Y-m-d');
	
	//init
	$isro = true;
	$isverify = true;
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];

	    if($r_isverify == ''){
	        $isro = false;
	    }
	}else{
	    $isro = false;
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idtranshp', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idjenistranshp', 'label' => 'Jenis Perolehan', 'type' => 'S', 'option' => $a_jenisperolehanhp, 'default' => '101', 'add' => 'style="width:150px"', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	$a_input[] = array('kolom' => 'tgltransaksi', 'label' => 'Tgl. Perolehan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tok', 'type' => 'H');
	$a_input[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idsupplier', 'type' => 'H', 'readonly' => $isro);

    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'R', 'option' => array('1' => 'Verified', '0' => 'Not Yet'), 'notnull' => true, 'readonly' => $isverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isverify);
	
	//detail
	$a_detail = $p_modeldet::getInputAttr(array('isro' => $isro));

	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
            $record['tok'] = $r_tok;
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	/*
	else if($r_actdet == 'savedet' and $c_edit){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
	
        $record['tok'] = $r_tok;
		$p_modeldet::setDataSave(&$record);
        $p_modeldet::setDataKonv($conn, &$record);

        $conn->BeginTrans();
            
		if(empty($r_keydet)){
		    $record['idtranshp'] = $r_key;
			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else
			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);
		
		//set saldo rata-rata
	    if(!$p_posterr) $ok = $p_modeldet::setSaldoAvg($conn,$record['idbarang'],$a_mdata['tgltransaksi'],$r_key);
	    
	    if($ok){ 
	        $conn->CommitTrans();
			$p_posterr = false;
        }else{ 
            $conn->RollbackTrans();
			$p_posterr = true;
			$p_postmsg = empty($p_postmsg) ? 'Perhitungan saldo rata - rata gagal' : $p_postmsg;
        }
            
		if(!$p_posterr) unset($post);
	}*/

	else if($r_actdet == 'deletedet' and $c_delete){
	    $a_mdetdata = $p_modeldet::getMDetData($conn, $r_keydet);

	    $conn->BeginTrans();
	    
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
		
		//set saldo rata-rata
	    if(!$p_posterr) $ok = $p_modeldet::setSaldoAvg($conn,$a_mdetdata['idbarang'],$a_mdata['tgltransaksi'],$r_key);
	    
	    if($ok){ 
	        $conn->CommitTrans();
			$p_posterr = false;
        }else{ 
            $conn->RollbackTrans();
			$p_posterr = true;
			$p_postmsg = empty($p_postmsg) ? 'Perhitungan saldo rata - rata gagal' : $p_postmsg;
        }
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        
        if(count($a_mdata) == 0)
            $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];

    	/*
    	$a_btnprint = array();
    	$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'SK Tim', 'onclick' => 'goCetakSKTim()');
    	$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'Pengumuman', 'onclick' => 'goCetakPengumuman()');
    	$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'Cetak BASTB', 'onclick' => 'goCetakBASTB()');
    	$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'Cetak BA', 'onclick' => 'goCetakBA()');
    	*/
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
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<tr>
					            <td colspan="4" class="DataBG">Detail Perolehan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idtranshp') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idtranshp') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjenistranshp') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idjenistranshp') ?></td>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'namasupplier') ?></td>
		                        <td class="RightColumnBG">
		                            <?= Page::getDataInput($row,'namasupplier') ?>
		                            <?= Page::getDataInput($row,'idsupplier') ?>
		                        </td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tgltransaksi') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tgltransaksi') ?></td>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
							<tr>
					            <td colspan="4" class="DataBG">Proses Perolehan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isverify') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifynote') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifynote') ?></td>
	                        </tr>
					    </table>
					    <?  if(!empty($r_key)) { ?>
					    <br><br>
					    <?  if($c_insert and !$isro) { ?>
					    <table align="left">
					        <tr>
					            <td>
                                    <input id="btnAddDetail" type="button" class="ControlStyle" value="Tambah Barang">
                                </td>
                            </tr>
					    </table>					    
					    <?  } ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				            <tr>
					            <td colspan="<?= $c_edit ? '7' : '6' ?>" class="DataBG">Detail Perolehan Habis Pakai</td>
				            </tr>
					        <tr>
					            <th width="100">Kode Barang</th>
					            <th>Nama Barang</th>
					            <th width="60">Jumlah</th>
					            <th width="50">Satuan</th>
					            <th width="80">Harga</th>
					            <th width="80">Total</th>
						        <?	if($c_edit and !$isro) { ?>
						        <th width="30">Aksi</th>
						        <?	} ?>
					        </tr>
					        <?
					        if(count($det) > 0){
					            $i = 0;
					            foreach($det as $id => $val){
					                $i++;
					        ?>
					        <tr valign="top">
					            <td><?= $val['idbarang'] ?></td>
					            <td><?= $val['namabarang'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qty'],2) ?></td>
					            <td><?= $val['idsatuan'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['harga'],2) ?></td>
					            <td align="right"><?= CStr::formatNumber($val['total'],2) ?></td>
						        <?	if($c_edit and !$isro) { ?>
						        <td align="center">
							        <img title="Hapus Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddettranshp'] ?>')" style="cursor:pointer">
						        </td>
						        <?	} ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= $c_edit ? '7' : '6' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
					        </tr>
					        <?
					        }
					        ?>
					    </table>
					    <?  }?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
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
var detform = "<?= Route::navAddress('pop_perolehanhpdetail') ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";
var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	$("#namasupplier").xautox({strpost: "f=acxsupplier", targetid: "idsupplier"});
	
	$('#btnAddDetail').click(function(){
        openDetail($('#key').val(), '');
	});

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


</script>
</body>
</html>
