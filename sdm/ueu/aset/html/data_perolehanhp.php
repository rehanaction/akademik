<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_perolehanhp');
	
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
	
	//user role
	$c_verify = false;
    $r_role = Modul::getRole();
    if(in_array($r_role, Aset::getRoleRM()))
        $c_verify = true;
    
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//$a_unit = mCombo::unitSave($conn,false);
	$a_unit = array('63' => '0211410 - Bagian Rumah Tangga');
	$a_jenisperolehanhp = mCombo::jenisperolehanhp($conn);
	$a_verify = array('1' => 'Verified');
	$now = date('Y-m-d');
	
	//init
	$isro = true;
	$isroverify = true;
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

	        if($r_isverify != ''){
	            $isroverify = true;
	        }
	    }
	    
	}else{
	    $isro = false;
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idtranshp', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	//$a_input[] = array('kolom' => 'unit', 'label' => 'Unit', 'size' => 35, 'readonly' => $isro);
    //$a_input[] = array('kolom' => 'idunit', 'type' => 'H', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idjenistranshp', 'label' => 'Jenis Perolehan', 'type' => 'S', 'option' => $a_jenisperolehanhp, 'default' => '101', 'add' => 'style="width:150px"', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tgltransaksi', 'label' => 'Tgl. Perolehan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti','maxlength' => 20, 'size' => 20, 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D', 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'nospk', 'label' => 'No. SPK','maxlength' => 20, 'size' => 20, 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'tglspk', 'label' => 'Tgl. SPK', 'type' => 'D', 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'nopo', 'label' => 'No. PO','maxlength' => 20, 'size' => 20, 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'tglpo', 'label' => 'Tgl. PO', 'type' => 'D', 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'size' => 30, 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'idsupplier', 'type' => 'H');
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	//$a_input[] = array('kolom' => 'tok', 'type' => 'H');

    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
	$a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	
	//detail
	$a_detail = $p_modeldet::getInputAttr(array('isro' => $isro));

	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
        $record['tok'] = $r_tok;
		
		if(empty($r_key)){
			Aset::setInsert($record);
			
			if(!empty($record['tgltransaksi'])){
				$lockmsg = Aset::isLock($conn, Aset::setTglToPeriode($record['tgltransaksi']));
				if(!empty($lockmsg)){
				    $p_posterr = true;
		            $p_postmsg = $lockmsg;
				}
			}

			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		}else{
		    Aset::setVerify($record, $r_isverify, $record['isverify']);
	        
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key,$r_isverify);
		}
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet' and $c_edit){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
        $record['tok'] = $r_tok;

		if(empty($r_keydet)){
		    $record['idtranshp'] = $r_key;
			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else
			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);

		if(!$p_posterr) unset($post);
	}
	else if($r_actdet == 'deletedet' and $c_delete){
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);

		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'uploadexcel' and $c_edit) {	
		if(empty($_FILES['hpexcel']['error'])) {
		    require_once('../includes/excelreader/reader.php');

		    $data = new Spreadsheet_Excel_Reader();
            $data->setOutputEncoding('CP1251');
            $data->read($_FILES['hpexcel']['tmp_name']);
            
            error_reporting(E_ALL ^ E_NOTICE);

            $a_satuan = mCombo::satuan($conn);

            $numRows = 100;
            $startRow = 2;

            $record = array();
            $conn->BeginTrans();
            $p_posterr = false;
            for ($i = 2; $i <= $numRows; $i++) {
                if(!empty($data->sheets[0]['cells'][$i][1])){

                    unset($record);
                    $record['idbarang1'] = CStr::removeSpecial($data->sheets[0]['cells'][$i][1]);
                    $record['qty'] = CStr::removeSpecial($data->sheets[0]['cells'][$i][3]);
                    $record['idsatuan'] = CStr::removeSpecial($data->sheets[0]['cells'][$i][4]);
                    $record['harga'] = CStr::removeSpecial($data->sheets[0]['cells'][$i][5]);
                    $record['total'] = (float)$record['qty']*(float)$record['harga'];
                    
                    //$sql .= "insert into aset.as_transhpdetail (idtranshp,idbarang1,qty,idsatuan,harga,total) 
                    //    values ($r_key,'$idbarang1','$qty','$satuan','$harga','$total');";
                        
                    if(empty($record['idbarang1']) or empty($record['qty']) or empty($record['idsatuan']) or empty($record['harga'])){
                        $sql = '';
			            $p_posterr = true;
			            $p_postmsg = 'ID. Barang, jumlah, satuan, dan harga tidak boleh kosong !';
			            break;
                    }else if(!in_array($record['idsatuan'],$a_satuan)){
                        $sql = '';
			            $p_posterr = true;
			            $p_postmsg = 'Terdapat satuan yang tidak tercatat didalam sistem !';
			            break;
                    }else{
                        $record['tok'] = $r_tok;
                        $record['idtranshp'] = $r_key;

			            list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,'');
                    }
                    
                    if($p_posterr) break;
                }else continue;
            }
            
            if($p_posterr)
                $conn->RollbackTrans();
            else
                $conn->CommitTrans();

            //if(!empty($sql))
            //    $ok = $conn->execute($sql);
		}
		else{
			$p_posterr = true;
			$p_postmsg = 'Upload excel gagal';
		}
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$i_isverify = Page::getDataInput($row,'isverify');
	//$i_idunit = Page::getDataInput($row,'idunit');
	
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        
        $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];
	    
        if($r_isverify == '1'){
            $c_delete = false;
			//$i_idunit = '';

        	$i_isverify = '<input type="hidden" id="isverify" name="isverify" value="1">';
        	$i_isverify .= '<img src="images/check.png">';
			
			
        }
		
			$a_btnprint = array();
			$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');	
	}

	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
    }

	$lockmsg = Aset::isLock($conn, Aset::setTglToPeriode($a_mdata['tgltransaksi']));
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
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<tr>
					            <td colspan="4" class="DataBG">Data Perolehan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idtranshp') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idtranshp') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'nobukti') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'nobukti') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td>
                                <!--td class="LeftColumnBG"><//?= Page::getDataLabel($row,'unit') ?></td-->
                                <!--td class="RightColumnBG">
                                    <//?= Page::getDataInput($row,'unit') ?>
                                    <//?= $i_idunit;//Page::getDataInput($row,'idunit') ?>
                                </td-->
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglbukti') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglbukti') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjenistranshp') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idjenistranshp') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'nospk') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'nospk') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tgltransaksi') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tgltransaksi') ?></td>
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
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'namasupplier') ?></td>
		                        <td class="RightColumnBG">
		                            <?= Page::getDataInput($row,'namasupplier',' ',$r_namasupplier) ?>
		                            <?= Page::getDataInput($row,'idsupplier') ?>
		                        </td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpo') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpo') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= $i_isverify;//Page::getDataInput($row,'isverify') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifyuser') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
							<tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifytime') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
	                        <tr valign="top">
								<td class="LeftColumnBG">Total</td>
		                        <td class="RightColumnBG"><span id="span_total"></span></td>
	                        </tr>
	                        <tr valign="top">
		                        <td colspan="4">&nbsp;</td>
	                        </tr>
					    </table>
					    <?  if(!empty($r_key)) { ?>
					    <?  if($r_isverify != '1' and !$c_verify) { ?>
					    <table align="left">
					        <tr>
					            <td width="130">
                                    <input id="btnAddDetail" type="button" class="ControlStyle" value="Tambah Barang">
                                </td>
					            <td width="50"><i>Atau</i></td>
					            <td>
                                    <input type="file" name="hpexcel" id="hpexcel" class="ControlStyle" size="35">&nbsp;&nbsp;&nbsp;
		                            <input id="btnuploadexcel" type="button" class="ControlStyle" value="Upload Excel">&nbsp;&nbsp;&nbsp;
		                            <a href="template/perolehan_hp.xls">Download Template</a>
                                </td>
                            </tr>
					    </table>
					    <?  } ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				            <tr>
					            <td colspan="<?= ($r_isverify != '1' and !$c_verify) ? '7' : '6' ?>" class="DataBG">Detail Perolehan Habis Pakai</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th>Barang</th>
					            <th width="60">Jumlah</th>
					            <th width="70">Satuan</th>
					            <th width="80">Harga Satuan</th>
					            <th width="80">Total</th>
						        <?	if($r_isverify != '1' and !$c_verify) { ?>
						        <th width="30">Aksi</th>
						        <?	} ?>
					        </tr>
					        <?
					        if(count($det) > 0){
					            $i = 0;
					            foreach($det as $id => $val){
					                $i++;
					                $total += (float)$val['total'];
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang1'].' - '.$val['namabarang'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qty'],2) ?></td>
					            <td><?= $val['idsatuan'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['harga'],2) ?></td>
					            <td align="right"><?= CStr::formatNumber($val['total'],2) ?></td>
						        <?	if($r_isverify != '1' and !$c_verify) { ?>
						        <td align="center">
							        <img title="Edit Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddettranshp'] ?>')" style="cursor:pointer">
						        </td>
						        <?	} ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= ($r_isverify != '1' and !$c_verify) ? '7' : '6' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
					        </tr>
					        <?
					        }
					        ?>
					        <tr>
					            <td colspan="5" align="right"><b>Total&nbsp;&nbsp;&nbsp;</b></td>
					            <td align="right"><span id="span_dettotal"><b><?= CStr::formatNumber($total,2) ?></b></span></td>
						        <?	if($r_isverify != '1' and !$c_verify) { ?>
						        <td align="center">&nbsp;</td>
						        <?	} ?>
					        </tr>
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
    
    // autocomplete
    $("#unit").xautox({strpost: "f=acxunit", targetid: "idunit"});
	$("#namasupplier").xautox({strpost: "f=acxsupplier", targetid: "idsupplier"});
	
	$('#btnAddDetail').click(function(){
        openDetail($('#key').val(), '');
	});
    
	$('#btnuploadexcel').click(function(){
        $('#act').val('uploadexcel');
        goSubmit();
	});
	
	$('#span_total').html($('#span_dettotal').html());
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

function goCetakBASTB() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbperolehanhp") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

</script>
</body>
</html>
                    
