<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_spb');
	
	//$conn->debug=true;

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('spb'));
	require_once(Route::getModelPath('spbdetail'));
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
	$p_title = 'Data Surat Permintaan Barang';
	$p_tbwidth = 800;
	$p_aktivitas = 'surat permintaan barang';
	$p_listpage = Route::getListPage();
	
	$p_model = mSpb;
	$p_modeldet = mSpbDetail;
	
    //user role
    $r_role = Modul::getRole();
    if($r_role == 'kaproc')
        $c_verify = true;
    if($r_role == 'karou')
        $c_ok1 = true;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//init
	$isro = true;
	$isroverify = true;
	$isrook1 = true;

	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isok1 = $a_mdata['isok1'];
		$r_isverify = $a_mdata['isverify'];

	    /*if($r_isok1 != '1'){
	        $isro = false;
	    }
	    
	    if($c_ok1){
	        $isro = true;
	        $isroverify = false;
	    }*/
		if($r_isverify != '1'){
	        $isro = false;
	    }
	    
	    if($c_verify){
	        $isro = true;
	        $isroverify = false;
	        $isrorawat = false;

	        if($r_isok1 != ''){
	            $isroverify = true;
	        }
	    }
	    
	    if($c_ok1 and $r_isverify == '1')
        	$isrook1 = false;
	}else{
	    $isro = false;
	}

	$a_unit = mCombo::unitSave($conn,false);
	$a_status = mCombo::statusproses();
	$a_verify = array('1' => 'Verified');
	$a_setuju = array('0' => 'Ditolak','1' => 'Disetujui');
	$now = date('Y-m-d');
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idspb', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglspb', 'label' => 'Tgl. SPB', 'type' => 'D', 'default' => $now, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nospb', 'label' => 'No. SPB', 'maxlength' => '20', 'size' => '20', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 25, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'option' => $a_status, 'readonly' => true, 'issave' => true);
	
	$a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	
    $a_input[] = array('kolom' => 'isok1', 'label' => 'Setuju ?', 'type' => 'R', 'option' => $a_setuju, 'readonly' => $isrook1);
    $a_input[] = array('kolom' => 'memo1', 'label' => 'Memo', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isrook1);
	$a_input[] = array('kolom' => 'isok1user', 'label' => 'Setuju User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'isok1time', 'label' => 'Setuju Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');

	//detail
	$a_detail = $p_modeldet::getInputAttr();
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($r_key)){
		    Aset::setInsert($record);
			
		    $record['idpegawai'] = Modul::getIDPegawai();

			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		}else{
			Aset::setOk1($record, $r_isok1, $record['isok1']);
			Aset::setVerify($record, $r_isverify, $record['isverify']);
			
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$r_isok1);
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savedet' and $c_edit){
		if(empty($r_keydet)){
    		list($post,$record) = uForm::getInsertRecord($a_detail,$_POST);

		    $record['idspb'] = $r_key;
		    $record['qtysetuju'] = $record['qtyaju'];

			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else{
    		list($post,$record) = uForm::getUpdateRecord($a_detail,$_POST);
    
    		/*if(!$c_ok1)
    		    $record['qtysetuju'] = $record['qtyaju'];*/

			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);
        }
        
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'deletedet' and $c_delete){
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
        if(!$p_posterr) unset($post);
	}
	else if($r_act == 'suntingdet' and $c_edit){
	    $r_ideditdet = $r_keydet;
	}
	else if($r_act == 'createpo' and $c_edit){
		list($p_posterr,$p_postmsg) = $p_model::setCreatePO($conn,$record,$r_key);
        if(!$p_posterr) unset($post);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

    $i_isverify = Page::getDataInput($row,'isverify');

    $a_pass = array();
    
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        $r_ndet = count($det);

		$a_data = $p_model::getMData($conn,$r_key);

	    $r_isok1 = $a_data['isok1'];	    
		$r_idunit = $a_data['idunit'];
		$r_tglspb = $a_data['tglspb'];
		$r_isverify = $a_data['isverify'];

		if($r_ndet > 0){
            $i_idunit = str_replace('&nbsp;','',$a_unit[Page::getDataValue($row,'idunit')]);
            $i_idunit .= '<input type="hidden" id="idunit" name="idunit" value="'.$r_idunit.'">';

            $a_pass[] = 'idunit';

		}

    	if($r_isok1 == '1'){
			$c_delete = false;

        	$i_isok1 = '<input type="hidden" id="isok1" name="isok1" value="1">';
        	$i_isok1 .= '<img src="images/check.png">';			
		}
	}

	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'] and !in_array($t_row['id'],$a_pass))
			$a_required[] = $t_row['id'];
    }
    
    
    $c_adddet = false;
    if($r_isok1 != '1' and !$c_ok1){
        $c_adddet = true;
        //$a_brgstock = mCombo::brgstock($conn);
    }

    $c_editdet = false;
    if($r_isverify != '1' and $c_verify){
        $c_editdet = true;
    }

	if($r_isok1 == '1'){
		$p_data = $p_model::getPOExist($conn,$r_key);
		$r_po = $p_data['ispo'];
		
    	$a_btnprint = array();
		if($r_po == 0 and $c_verify){
			$a_btnprint[] = array('id' => 'be_createpo', 'label' => 'Create PO', 'onclick' => 'goCreatePO()');
        } else if($c_verify) {
            $a_btnprint[] = array('id' => 'be_cetakspb', 'label' => 'Cetak SPB', 'onclick' => 'goCetakSPB()');
        }
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
					            <td colspan="4" class="DataBG">Detail Pengajuan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idspb') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idspb') ?></td>
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
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglspb') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglspb') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
	                        </tr>
	                        <tr>
								<td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'nospb') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'nospb') ?></td>
								<td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
	                        <?  if($r_isverify == '1' or $c_verify){ ?>                     				        
							<tr>
					            <td colspan="4" class="DataBG">Proses Pengajuan</td>
				            </tr>
							<tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isverify') ?></td>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'isok1') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isok1') ?></td>
							</tr>
							<tr valign="top">
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifynote') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifynote') ?></td>
                                <td class="LeftColumnBG"><?= Page::getDataLabel($row,'memo1') ?></td>
                                <td class="RightColumnBG"><?= Page::getDataInput($row,'memo1') ?></td>
							</tr>
							<tr valign="top">
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifyuser') ?></td>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'isok1user') ?></td>
                                <td class="RightColumnBG"><?= Page::getDataInput($row,'isok1user') ?></td>
							</tr>	
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifytime') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isok1time') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isok1time') ?></td>
	                        </tr>
	                        <?  } ?>
					        <tr>
					            <td colspan="4">&nbsp;</td>
				            </tr>
					    </table>
					    <?  if(!empty($r_key)) { ?>
					    <? /* if($r_isverify != '1' and !$c_verify) { ?>
					    <table align="left">
					        <tr>
					            <td>
                                    <input id="btnAddDetail" type="button" class="ControlStyle" value="Tambah Barang">
                                </td>
                            </tr>
					    </table>					    
					    <?  } */?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				            <tr>
					            <td colspan="<?= ($c_adddet or $c_editdet) ? '6' : '5' ?>" class="DataBG">Detail Permintaan Barang</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th>Barang</th>
					            <th width="100">Jml. Diajukan</th>
					            <th width="100">Jml. Disetujui</th>
						        <?	if($c_adddet or $c_editdet) { ?>
						        <th width="40">Aksi</th>
						        <?	} ?>
					        </tr>
					        <?
                            if(count($det) > 0){
					            $i = 0;
					            foreach($det as $id => $val){
					                $i++;
					                if($r_ideditdet == $val['iddetspb']){
        					            $a_reqdet = array('u_qtyaju','u_qtysetuju');
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang'].' - '.$val['namabarang'] ?></td>
					            <td align="right">
					                <?  if($c_adddet) {?>
					                <input type="text" id="u_qtyaju" name="u_qtyaju" value="<?= CStr::formatNumber($val['qtyaju']) ?>" size="4" maxlength="10" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					                <?  }else{ ?>
					                <?= CStr::formatNumber($val['qtyaju']) ?>
							        <input type="hidden" name="u_qtyaju" value="<?= CStr::formatNumber($val['qtyaju']) ?>">
					                <?  }?>
					            </td>
					            <td align="right">
					                <?  if($c_editdet) {?>
					                <input type="text" id="u_qtysetuju" name="u_qtysetuju" value="<?= CStr::formatNumber($val['qtysetuju']) ?>" size="4" maxlength="10" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					                <br/>
					                <?  }else{ ?>
					                <?= CStr::formatNumber($val['qtysetuju']) ?>
							        <input type="hidden" name="u_qtysetuju" value="<?= CStr::formatNumber($val['qtysetuju']) ?>">
					                <?  }?>
					            </td>
						        <?	if($c_adddet or $c_editdet) { ?>
						        <td align="center">
							        <img title="Simpan" src="images/disk.png" onclick="goSaveDet()" style="cursor:pointer">
							        <input type="hidden" name="u_idbarang" value="<?= $val['idbarang'] ?>">
						        </td>
						        <?	} ?>
					        </tr>
					        <?      }else{ ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang'].' - '.$val['namabarang'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtyaju']) ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtysetuju']) ?></td>
						        <?	if($c_adddet or $c_editdet) { ?>
						        <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetspb'] ?>')" style="cursor:pointer">
							        <?  if($c_adddet) {?>
							        <img title="Hapus" src="images/delete.png" onclick="goDeleteDet('<?= $val['iddetspb'] ?>')" style="cursor:pointer">
							        <?  } ?>
						        </td>
						        <?	} ?>
					        </tr>
					        <?
					                }
					            }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= ($c_adddet or $c_editdet) ? '6' : '5' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
					        </tr>
					        <?  }?>
					        <?  if($c_adddet and empty($r_ideditdet)){ ?>
					        <tr>
					            <td>&nbsp;</td>
					            <td>
					                <? /*<?= UI::createSelect('i_idbarang',$a_brgstock,'','ControlStyle',true,'style="width:400px"',true,'-- Pilih barang stock --'); ?> */ ?>
									<input id="barang" class="ControlAuto" type="text" size="50" maxlength="50" name="barang" autocomplete="off">
									<input id="i_idbarang" type="hidden" name="i_idbarang">
					            </td>
					            <td align="right">
					                <input type="text" id="i_qtyaju" name="i_qtyaju" size="4" maxlength="10" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					            </td>
								<td>&nbsp;</td>
					            <td align="center"><img title="Detail" src="images/disk.png" onclick="goSaveDet()" style="cursor:pointer"></td>
					        </tr>
					        <?  
    					            $a_reqdet = array('i_brgstock','i_qtyaju');
					            }
					        ?>
					    </table>
					    <?  }?>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="keydet" id="keydet" value="<?= $r_ideditdet ?>">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
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
var ajaxpage = "<?= Route::navAddress('ajax') ?>";
var required = "<?= @implode(',',$a_required) ?>";
var reqdet = "<?= @implode(',',$a_reqdet) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$('#btnAddDetail').click(function(){
        openDetail($('#key').val(), '');
	});
	
	$("#pegawai").xautox({strpost: "f=acxpegawai", targetid: "idpegawai"});
	$("#barang").xautox({strpost: "f=acxbaranginv", targetid: "i_idbarang"});

	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
});

$('#i_idbarang').change(function(){
    $.post(ajaxpage, 
        {f: 'getdefsatuan', idbarang: $(this).val()}, 
        function(data){
            $('#span_i_idsatuan').html(data);
            $('#i_idsatuan').val(data);
        }
    );
});

function goDetailDet(pkeydet){
    //openDetail($('#key').val(), pkeydet);

    $("#keydet").val(pkeydet);
    $("#act").val('suntingdet');
    //$('#pageform').submit();
    goSubmit();
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

function goSaveDet(){
    var pass = true;
	if(typeof(reqdet) != "undefined") {
		if(!cfHighlight(reqdet))
			pass = false;
	}
	
	if(pass) {
        $("#act").val('savedet');
        goSubmit();
    }
}

function goDeleteDet(pkeydet){
    if(confirm('Apakah anda yakin akan menghapus data ini!')){
        $("#keydet").val(pkeydet);
        $("#act").val('deletedet');
        goSubmit();
    }
}

function goCetakSPB() {
    $('#pageform').attr('action','<?= Route::navAddress("rep_spb") ?>');
    $('#pageform').attr('target','_blank');
    goSubmit();
    $('#pageform').attr('action','');
    $('#pageform').attr('target','');
}

function goCreatePO(pkey){

    $("#act").val('createpo');
    goSubmit();

}

</script>
</body>
</html>
