<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_permintaanhp');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = true;
	
	//$c_verify = $c_other;
	
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
	$p_tbwidth = 700;
	$p_aktivitas = 'permintaan habis pakai';
	$p_listpage = Route::getListPage();
	
	$p_model = mTransHP;
	$p_modeldet = mTransHPDetail;
	
    //user role
    $r_role = Modul::getRole();
    if(in_array($r_role, Aset::getRoleRM()))
        $c_verify = true;
		$s_jml = true;
        
    if($r_role == 'wr2')
        $c_ok1 = true;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//init
	$isro = true;
	$isroverify = true;
	$isrominta = true;

	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];

	    if($r_isverify == ''){
	        $isro = false;
	    }

	    if($c_verify){
	        $isroverify = false;
	        $isro = true;
	    }

	}else{
	    $isro = false;
	}

	$a_unit = mCombo::unitSave($conn,false);
	$a_verify = array('1' => 'Verified');
	$now = date('Y-m-d');
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idtranshp', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'default' => $now, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 25, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'option' => $a_status, 'readonly' => true, 'issave' => true);

	$a_input[] = array('kolom' => 'tgltransaksi', 'label' => 'Tgl. Pengeluaran', 'type' => 'D', 'default' => $now, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	
	//detail
	$a_detail = $p_modeldet::getInputAttr();
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
            $record['tok'] = $r_tok;
            $record['idjenistranshp'] = '306';
		    $record['insertuser'] = Modul::getUserDesc();
			$record['inserttime'] = date('Y-m-d H:i:s');
			$record['status'] = 'A';
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		}else{
			if(isset($record['isverify']) and $record['isverify'] != $r_isverify){
		        if($record['isverify'] == '1'){
		            $record['verifyuser'] = Modul::getUserDesc();
			        $record['verifytime'] = date('Y-m-d H:i:s');
			        $record['status'] = 'V';
		        }else{
        	        $record['verifyuser'] = null;
		            $record['verifytime'] = null;
			        $record['status'] = 'A';

	                $record['isok1'] = null;
	                $record['memo1'] = null;
		            $record['isok1user'] = null;
		            $record['isok1time'] = null;
		        }
	        }
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet' and $c_edit){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);

		//$p_modeldet::setDataSave($record);
        //$p_modeldet::setDataKonv($conn,$record);

        //$conn->BeginTrans();
            
		if(empty($r_keydet)){
		    $record['idtranshp'] = $r_key;
			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else
			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);
		
		//set saldo rata-rata
	    //if(!$p_posterr) $ok = $p_modeldet::setSaldoAvg($conn,$record['idbarang1'],$a_mdata['tgltransaksi'],$r_key);
	    /*
	    if($ok){ 
	        $conn->CommitTrans();
			$p_posterr = false;
        }else{ 
            $conn->RollbackTrans();
			$p_posterr = true;
			$p_postmsg = empty($p_postmsg) ? 'Perhitungan saldo rata - rata gagal' : $p_postmsg;
        }
        */
		if(!$p_posterr) unset($post);
	}
	else if($r_actdet == 'deletedet' and $c_delete){
	    $a_mdetdata = $p_modeldet::getMDetData($conn, $r_keydet);

	    $conn->BeginTrans();
	    
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
		
		//set saldo rata-rata
	    if(!$p_posterr) $ok = $p_modeldet::setSaldoAvg($conn,$a_mdetdata['idbarang1'],$a_mdata['tgltransaksi'],$r_key);
	    
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
	
	$i_idunit = Page::getDataInput($row,'idunit');
	$i_tgltransaksi = Page::getDataInput($row,'tgltransaksi');
	$i_tok = Page::getDataInput($row,'tok');
	$i_idjenistranshp = Page::getDataInput($row,'idjenistranshp');

    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        $r_ndet = count($det);

		$a_data = $p_model::getMData($conn,$r_key);
		//echo 'idunit - '.$a_data['idunit'].' idjenishapus - '.$a_data['idjenispenghapusan'].'  jenishapus - '.$r_jenishapus;
	    $r_isverify = $a_data['isverify'];	    
		$r_idunit = $a_data['idunit'];
		$r_tgltransaksi = $a_data['tgltransaksi'];
		$r_tok = $a_data['tok'];
		$r_idjenistranshp = $a_data['idjenistranshp'];

		if($r_ndet > 0){
            $i_idunit = str_replace('&nbsp;','',$a_unit[Page::getDataValue($row,'idunit')]);
            $i_idunit .= '<input type="hidden" id="idunit" name="idunit" value="'.$r_idunit.'">';

            /*$i_tgltransaksi = str_replace('&nbsp;','',$a_tgltransaksi[Page::getDataValue($row,'tgltransaksi')]);
            $i_tgltransaksi .= '<input type="hidden" id="tgltransaksi" name="tgltransaksi" value="'.$r_tgltransaksi.'">';*/

            $i_tok .= '<input type="hidden" id="tok" name="tok" value="'.$r_tok.'">';
            $i_idjenistranshp .= '<input type="hidden" id="idjenistranshp" name="idjenistranshp" value="'.$r_idjenistranshp.'">';

            $a_pass[] = 'idunit';
            //$a_pass[] = 'tgltransaksi';
            $a_pass[] = 'tok';
            $a_pass[] = 'idjenistranshp';

		}

    	if($r_isverify == '1'){
			$c_delete = false;
		}

        /*if(count($a_mdata) == 0)
            $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];*/

	}

	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'] and !in_array($t_row['id'],$a_pass))
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
					            <td colspan="4" class="DataBG">Detail Pengajuan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idtranshp') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idtranshp') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
		                        <td class="RightColumnBG"><?= $i_idunit //Page::getDataInput($row,'idunit') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpengajuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpengajuan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
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
							</tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isverify') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifynote') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifynote') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifyuser') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifytime') ?></td>
	                        </tr>
	                        <?  } ?>
					        <tr>
					            <td colspan="4">&nbsp;</td>
				            </tr>
					    </table>
					    <?  if(!empty($r_key)) { ?>
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
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '6' : '5' ?>" class="DataBG">Detail Permintaan Habis Pakai</td>
				            </tr>
					        <tr>
					            <th width="120">Kode Barang</th>
					            <th>Nama Barang</th>
					            <th width="80">Jml. Diajukan</th>
								<? if($s_jml) { ?>
					            <th width="80">Jml. Disetujui</th>
								<? } ?>
					            <th width="80">Satuan</th>
						        <?	if($r_isverify != '1' or $c_verify) { ?>
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
					            <td><?= $val['idbarang1'] ?></td>
					            <td><?= $val['namabarang'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtyaju'],2) ?></td>
								<? if($s_jml) { ?>
					            <td align="right"><?= CStr::formatNumber($val['qty'],2) ?></td>
								<? } ?>
					            <td><?= $val['idsatuan'] ?></td>
						        <?	if($r_isverify != '1' or $c_verify) { ?>
						        <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddettranshp'] ?>')" style="cursor:pointer">
						        </td>
						        <?	} ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '5' : '4' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
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

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_permintaanhpdetail') ?>";
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
