<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	//$conn->debug=true;
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_po');
	
	//$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('po'));
	require_once(Route::getModelPath('podetail'));
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
	$p_title = 'Data Purchase Order';
	$p_tbwidth = 800;
	$p_aktivitas = 'purchase order';
	$p_listpage = Route::getListPage();
	
	$p_model = mPo;
	$p_modeldet = mPoDetail;
	
    //user role
    $r_role = Modul::getRole();
    /*if(in_array($r_role, Aset::getRoleRM())){
        $c_verify = true;
	}*/

    if($r_role == 'kaproc')
        $c_ok1 = true;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//init
	$isro = true;

	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_status = $a_mdata['status'];

	    if($r_status != 'S'){
	        $isro = false;
	    }
	    
	    if($c_ok1){
	        $isro = true;
	    }
	}else{
	    $isro = false;
	}

	$a_unit = mCombo::unitSave($conn,false);
	$a_status = mCombo::statusproses();
	$a_sah = array('D' => 'Draft','S' => 'Disetujui');
	$now = date('Y-m-d');
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idpo', 'label' => 'ID.', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglpo', 'label' => 'Tgl. PO', 'type' => 'D', 'default' => $now, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nopo', 'label' => 'No. PO', 'maxlength' => '20', 'size' => '20', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idsupplier', 'type' => 'H', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 25, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'R', 'option' => $a_sah, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'status', 'label' => 'Sah ?', 'type' => 'R', 'option' => $a_sah, 'readonly' => $isro);
	
	$a_input[] = array('kolom' => 'idspb', 'label' => 'ID. SPB', 'readonly' => true);
	$a_input[] = array('kolom' => 'nospb', 'label' => 'No. SPB', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglspb', 'label' => 'Tgl. SPB', 'type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit', 'readonly' => true);
	$a_input[] = array('kolom' => 'userspb', 'label' => 'Pemohon SPB', 'readonly' => true);
	$a_input[] = array('kolom' => 'catatanspb', 'label' => 'Catatan', 'readonly' => true);


	//detail
	$a_detail = $p_modeldet::getInputAttr();
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($r_key)){
		    Aset::setInsert($record);
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		}else{
			//Aset::setOk1($record, $r_isok1, $record['isok1']);
			
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$r_status);
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

		    $record['idpo'] = $r_key;

			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else{
    		list($post,$record) = uForm::getUpdateRecord($a_detail,$_POST);

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
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

    $a_pass = array();
    
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        $r_ndet = count($det);

		$a_data = $p_model::getMData($conn,$r_key);

	    /*$r_isok1 = $a_data['isok1'];	    
		$r_idunit = $a_data['idunit'];
		$r_tglspb = $a_data['tglspb'];

		if($r_ndet > 0){
            $i_idunit = str_replace('&nbsp;','',$a_unit[Page::getDataValue($row,'idunit')]);
            $i_idunit .= '<input type="hidden" id="idunit" name="idunit" value="'.$r_idunit.'">';

            $a_pass[] = 'idunit';

		}

    	if($r_isok1 == '1'){
			$c_delete = false;

        	$i_isok1 = '<input type="hidden" id="isok1" name="isok1" value="1">';
        	$i_isok1 .= '<img src="images/check.png">';			
		}*/
	}

	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'] and !in_array($t_row['id'],$a_pass))
			$a_required[] = $t_row['id'];
    }
    
    
    /*$c_adddet = false;
    if($r_isok1 != '1' and !$c_ok1){
        $c_adddet = true;
    }*/

    $c_editdet = true;
    /*if($r_isoks1 != '1' and $c_ok1){
        $c_editdet = true;
    }*/

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
					            <td colspan="4" class="DataBG">Detail Pengajuan SPB</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idspb') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idspb') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'userspb') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'userspb') ?></td>
	                        </tr>
	                        <tr>
                    			<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nospb') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'nospb') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglspb') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglspb') ?></td>
	                        </tr>
	                        <tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'namaunit') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'namaunit') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatanspb') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'catatanspb') ?></td>
	                        </tr>
							<tr>
					            <td colspan="4" class="DataBG">Detail Pengajuan PO</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idpo') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idpo') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
                    			<td class="LeftColumnBG"><?= Page::getDataLabel($row,'nopo') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'nopo') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpo') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpo') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
	                        </tr>
	                        <tr>
		                        <td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'namasupplier') ?></td>
		                        <td valign="top" class="RightColumnBG">
		                            <?= Page::getDataInput($row,'namasupplier',' ',$r_namasupplier) ?>
		                            <?= Page::getDataInput($row,'idsupplier') ?>
		                        </td>
								<td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
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
					            <td colspan="<?= (/*$c_adddet or*/ $c_editdet) ? '6' : '5' ?>" class="DataBG">Detail Purchase Order</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th>Barang</th>
					            <th width="100">Jml. PO</th>
					            <th width="100">Harga</th>
						        <?	if(/*$c_adddet or */$c_editdet) { ?>
						        <th width="40">Aksi</th>
						        <?	} ?>
					        </tr>
					        <?
                            if(count($det) > 0){
					            $i = 0;
					            foreach($det as $id => $val){
									$totqty += (int)$val['qtypo'];
									$totharga += (int)$val['harga'];
					                $i++;
					                if($r_ideditdet == $val['iddetpo']){
        					            $a_reqdet = array('u_qtypo','u_harga');
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang'].' - '.$val['namabarang'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtypo']) ?>
									<input type="hidden" name="u_qtypo" value="<?= CStr::formatNumber($val['u_qtypo'],2) ?>">	
								</td>
					            <td align="right">
					                <?  if($c_editdet) {?>
					                <input type="text" id="u_harga" name="u_harga" value="<?= CStr::formatNumber($val['harga'],2) ?>" size="10" maxlength="10" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					                <br/>
					                <?  }else{ ?>
					                <?= CStr::formatNumber($val['harga'],2) ?>
							        <input type="hidden" name="u_harga" value="<?= CStr::formatNumber($val['harga'],2) ?>">
					                <?  }?>
					            </td>
						        <?	if(/*$c_adddet or */$c_editdet) { ?>
						        <td align="center">
							        <img title="Simpan" src="images/disk.png" onclick="goSaveDet()" style="cursor:pointer">
							        <input type="hidden" name="u_idbarang" value="<?= $val['idbarang'] ?>">
							        <input type="hidden" name="u_qtypo" value="<?= $val['qtypo'] ?>">
						        </td>
						        <?	} ?>
					        </tr>
					        <?      }else{ ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang'].' - '.$val['namabarang'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtypo'],2) ?></td>
					            <td align="right"><?= CStr::formatNumber($val['harga'],2) ?></td>
						        <?	if(/*$c_adddet or */$c_editdet) { ?>
						        <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetpo'] ?>')" style="cursor:pointer">
						        </td>
						        <?	} ?>
					        </tr>
					        <?
					                }
					            }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= (/*$c_adddet or */$c_editdet) ? '6' : '5' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
					        </tr>
					        <?  } ?>
							<tr>
					            <td colspan="<?= (/*$c_adddet or */$c_editdet) ? '2' : '1' ?>" align="right"><b>Total</b>&nbsp;&nbsp;&nbsp;</td>
					            <td align="right"><b><?= CStr::formatNumber($totqty,2) ?></b></td>
					            <td align="right"><b><?= CStr::formatNumber($totharga,2) ?></b></td>
					        </tr>
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

	$("#namasupplier").xautox({strpost: "f=acxsupplier", targetid: "idsupplier"});
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
</script>
</body>
</html>
