<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug = true;	
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_permintaanhp');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('transhp'));
	require_once(Route::getModelPath('transhpdetail'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_keydet = CStr::removeSpecial($_REQUEST['keydet']);
	$r_tok = 'K';
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Permintaan Habis Pakai';
	$p_tbwidth = 800;
	$p_aktivitas = 'Permintaan HP';
	$p_listpage = Route::getListPage();
	
	$p_model = mTransHP;
	$p_modeldet = mTransHPDetail;
	
    //user role
    $r_role = Modul::getRole();
    if(in_array($r_role, Aset::getRoleRM())){
        $c_verify = true;
		$s_jml = true;
	}

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//init
	$isro = true;
	$isroverify = true;
	$isrominta = true;
	$isrokeu = false;

	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];

	    if($r_isverify != '1'){
	        $isro = false;
	    }
	    
	    if($c_verify){
	        $isro = true;
	        $isroverify = false;

	        //if($r_isverify != ''){
	        //    $isroverify = true;
	       // }
	    }
	}else{
	    $isro = false;
	}

	$a_unit = mCombo::unitSave($conn,false);
	$a_status = mCombo::statusproses();
	$a_verify = array('1' => 'Verified');
	$now = date('Y-m-d');
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idtranshp', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunitaju', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'default' => $now, 'readonly' => $isro);
	//$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 25, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'option' => $a_status, 'readonly' => true, 'issave' => true);

	$a_input[] = array('kolom' => 'tgltransaksi', 'label' => 'Tgl. Pengambilan', 'type' => 'D', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'pegawai', 'label' => 'Diambil Oleh', 'size' => 25, 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');
    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
	$a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
/*
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
*/	
	//detail
	$a_detail = $p_modeldet::getInputAttr();
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
	    $record['idunit'] = '63';
        $record['tok'] = $r_tok;
        $record['idjenistranshp'] = '306';

		if(empty($r_key)){
		    Aset::setInsert($record);
			
			if(!empty($record['tglpengajuan'])){
				$lockmsg = Aset::isLock($conn, Aset::setTglToPeriode($record['tglpengajuan']));
				if(!empty($lockmsg)){
				    $p_posterr = true;
		            $p_postmsg = $lockmsg;
				}
			}

			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		}else{
			Aset::setVerify($record, $r_isverify, $record['isverify']);
			if($record['status'] == 'V')
			    $record['tgltransaksi'] = date('Y-m-d');
			    
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$r_isverify);
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savedet' and $c_edit){
		// list($post,$record) = uForm::getInsertRecord($a_detail,$_POST);
		// print_r($record);die;
		if(empty($r_keydet)){
    		list($post,$record) = uForm::getInsertRecord($a_detail,$_POST);

		    $record['idtranshp'] = $r_key;
		    $record['qty'] = $record['qtyaju'];

			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else{
    		list($post,$record) = uForm::getUpdateRecord($a_detail,$_POST);
    
    		if(!$c_verify)
    		    $record['qty'] = $record['qtyaju'];

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
	else if($r_act == 'proses' and $c_verify){
	    list($p_posterr,$p_postmsg) = $p_model::proses($conn,$r_key);
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

	    $r_isverify = $a_data['isverify'];	    
		$r_idunit = $a_data['idunit'];
		$r_tgltransaksi = $a_data['tgltransaksi'];
		$r_tok = $a_data['tok'];
		$r_idjenistranshp = $a_data['idjenistranshp'];

		if($r_ndet > 0){
            $i_idunitaju = str_replace('&nbsp;','',$a_unit[Page::getDataValue($row,'idunitaju')]);
            $i_idunitaju .= '<input type="hidden" id="idunitaju" name="idunitaju" value="'.$r_idunitaju.'">';

            /*$i_tgltransaksi = str_replace('&nbsp;','',$a_tgltransaksi[Page::getDataValue($row,'tgltransaksi')]);
            $i_tgltransaksi .= '<input type="hidden" id="tgltransaksi" name="tgltransaksi" value="'.$r_tgltransaksi.'">';*/

            $a_pass[] = 'idunit';
            $a_pass[] = 'tok';
            $a_pass[] = 'idjenistranshp';

		}

    	if($r_isverify == '1'){
			$c_delete = false;

        	$i_isverify = '<input type="hidden" id="isverify" name="isverify" value="1">';
        	$i_isverify .= '<img src="images/check.png">';	

			$a_btnprint = array();
			$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');
		}
	}

	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'] and !in_array($t_row['id'],$a_pass))
			$a_required[] = $t_row['id'];
    }
    
    
    $c_adddet = false;
    if($r_isverify != '1' and !$c_verify){
        $c_adddet = true;
        $a_brgstock = mCombo::brgstock($conn);
    }

    $c_editdet = false;
    if($r_isverify != '1' and $c_verify){
        $c_editdet = true;
    }

    $lockmsg = Aset::isLock($conn, Aset::setTglToPeriode($a_data['tglpengajuan']));
    if(!empty($lockmsg)){
        $c_insert = false;
	    $c_edit = false;
    	$c_delete = false;

        $p_posterr = true;
        $p_postmsg = $lockmsg;
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
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idtranshp') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idtranshp') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunitaju') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idunitaju') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpengajuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpengajuan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG">
		                            <?= Page::getDataInput($row,'status') ?>
		                            <?  if($c_verify and $a_data['status'] == 'A'){ ?>
		                                &nbsp;&nbsp;<input type="button" onclick="goProses()" class="ControlStyle" value="Proses">
		                            <?  }?>
		                        </td>
	                        </tr>
	                        <tr>
								<td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
	                        <?  if($r_isverify == '1' or $c_verify){ ?>                     				        
							<tr>
					            <td colspan="4" class="DataBG">Proses Pengajuan</td>
				            </tr>
							<tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tgltransaksi') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tgltransaksi') ?></td>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'pegawai') ?></td>
		                        <td class="RightColumnBG">
		                            <?= Page::getDataInput($row,'pegawai') ?>
		                            <?= Page::getDataInput($row,'idpegawai') ?>
		                        </td>
							</tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG" colspan="3" id="td_isverify"><?= $i_isverify;//Page::getDataInput($row,'isverify') ?></td>
	                        </tr>
							<tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifyuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifytime') ?></td>
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
					            <td colspan="<?= ($c_adddet or $c_editdet) ? '6' : '5' ?>" class="DataBG">Detail Permintaan Habis Pakai</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th>Barang</th>
					            <th width="100">Jml. Diajukan</th>
					            <th width="100">Jml. Disetujui</th>
					            <th width="80">Satuan</th>
						        <?	if($c_adddet or $c_editdet) { ?>
						        <th width="40">Aksi</th>
						        <?	} ?>
					        </tr>
					        <?
                            if(count($det) > 0){
					            $i = 0;
					            $nred = 0;
					            foreach($det as $id => $val){
					                $i++;
					                if($r_ideditdet == $val['iddettranshp']){
        					            $a_reqdet = array('u_qtyaju','u_qty');
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang1'].' - '.$val['namabarang'] ?></td>
					            <td align="right">
					                <?  if($c_adddet) {?>
					                <input type="text" id="u_qtyaju" name="u_qtyaju" value="<?= CStr::formatNumber($val['qtyaju'],2) ?>" size="4" maxlength="10" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					                <?  }else{ ?>
					                <?= CStr::formatNumber($val['qtyaju'],2) ?>
							        <input type="hidden" name="u_qtyaju" value="<?= CStr::formatNumber($val['qtyaju'],2) ?>">
					                <?  }?>
					            </td>
					            <td align="right">
					                <?  if($c_editdet) {?>
					                <input type="text" id="u_qty" name="u_qty" value="<?= CStr::formatNumber($val['qty'],2) ?>" size="4" maxlength="10" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					                <br/>
					                <i>Stock : <?= CStr::formatNumber($val['jmlstock'],2) ?></i>
					                <?  }else{ ?>
					                <?= CStr::formatNumber($val['qty'],2) ?>
							        <input type="hidden" name="u_qty" value="<?= CStr::formatNumber($val['qty'],2) ?>">
					                <?  }?>
					            </td>
					            <td><?= $val['idsatuan'] ?></td>
						        <?	if($c_adddet or $c_editdet) { ?>
						        <td align="center">
							        <img title="Simpan" src="images/disk.png" onclick="goSaveDet()" style="cursor:pointer">
							        <input type="hidden" name="u_idbarang1" value="<?= $val['idbarang1'] ?>">
							        <input type="hidden" name="u_idsatuan" value="<?= $val['idsatuan'] ?>">
						        </td>
						        <?	} ?>
					        </tr>
					        <?      }else{
					                    $isred = false;
					                    if($r_isverify != '1'){
					                        if($val['qty'] > $val['jmlstock']){
					                            $isred = true;
					                            $nred++;
				                            }
			                            }
					        ?>
					        <tr valign="top" <?= $isred ? 'class="RedBG"' : '' ?> >
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang1'].' - '.$val['namabarang'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtyaju'],2) ?></td>
					            <td align="right">
					                <?= CStr::formatNumber($val['qty'],2) ?>
					                <?  if($isred) {?>
					                <br>
					                <i>Stock : <?= CStr::formatNumber($val['jmlstock'],2) ?></i>
					                <?  } ?>
					            </td>
					            <td><?= $val['idsatuan'] ?></td>
						        <?	if($c_adddet or $c_editdet) { ?>
						        <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddettranshp'] ?>')" style="cursor:pointer">
							        <?  if($c_adddet) {?>
							        <img title="Hapus" src="images/delete.png" onclick="goDeleteDet('<?= $val['iddettranshp'] ?>')" style="cursor:pointer">
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
					                <?= UI::createSelect('i_idbarang1',$a_brgstock,'','ControlStyle',true,'style="width:400px"',true,'-- Pilih barang stock --'); ?>
					            </td>
					            <td align="right">
					                <input type="text" id="i_qtyaju" name="i_qtyaju" size="4" maxlength="10" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					            </td>
					            <td align="right">&nbsp;</td>
					            <td>
					                <span id="span_i_idsatuan"></span>
					                <input type="hidden" name="i_idsatuan" id="i_idsatuan">
					            </td>
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
var detform = "<?= Route::navAddress('pop_permintaanhpdetail') ?>";
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
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<?  if($nred > 0){ ?>
	    $('#td_isverify').html('<i>Tidak dapat dilakukan verifikasi karena terdapat permintaan yang melebihi stock</i>');
	<?  } ?>
});

$('#i_idbarang').change(function(){
    $.post(ajaxpage, 
        {f: 'getdefsatuan', idbarang1: $(this).val()}, 
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

function goProses(){
    if(confirm('Apakah anda yakin akan memproses permintaan ini!')){
        $("#act").val('proses');
        goSubmit();
    }
}

function goCetakBASTB() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbpermintaanhp") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

</script>
</body>
</html>
