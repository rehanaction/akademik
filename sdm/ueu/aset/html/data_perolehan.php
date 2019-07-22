<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_perolehan');
	//$conn->debug = false;

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('perolehanheader'));
	require_once(Route::getModelPath('perolehan'));
	require_once(Route::getModelPath('histdepresiasi'));
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
	$p_title = 'Data Perolehan';
	$p_tbwidth = 800;
	$p_aktivitas = 'Perolehan';
	//$p_listpage = Route::getListPage();
	$p_listpage = $_SESSION['PEROLEHAN'] ? $_SESSION['PEROLEHAN'] : Route::getListPage();
	
	$p_model = mPerolehanHeader;
	$p_modeldet = mPerolehan;
	
    //user
    $r_role = Modul::getRole();
    if($r_role == 'kakeu' or $r_role == 'pk')
        $c_edit = true;
		
	// hak akses tambahan
	//$a_authlist = Modul::getFileAuth($p_listpage);
	$a_authlist = Modul::getFileAuth('list_perolehan');
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//$a_unit = mCombo::unitSave($conn,false);
	$a_jenisperolehan = mCombo::jenisperolehan($conn);
	$a_sumberdana = mCombo::sumberdana($conn);
	$a_verify = array('1' => 'Verified');
	
	$now = date('Y-m-d');
	
	//init
	$isro = false;
	$isrokeu = false;
	$isroedit = false;
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    
	    if($p_model::getNVerified($conn, $r_key) > 0)
	        $r_isverify = '1';

	    if($r_isverify == '1'){
	        $isro = true;
	        $c_delete = false;
	        
			if(!in_array($r_role,array('A','kakeu','pk')))
				$isrokeu = true;
				
	        if(in_array($r_role,array('A','kaproc')))
    	        $c_delete = true;
	    }
	    //$periode = substr(str_replace('-','',$a_mdata['tglbukti']),0,6);
	    //echo $periode;
	}else{
		if(!in_array($r_role,array('A','kakeu','pk')))
			$isrokeu = true;
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idperolehanheader', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	//$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'unit', 'label' => 'Unit', 'size' => 30, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idunit', 'type' => 'H');
	$a_input[] = array('kolom' => 'idjenisperolehan', 'label' => 'Jenis Perolehan', 'type' => 'S', 'option' => $a_jenisperolehan, 'default' => '101', 'add' => 'style="width:150px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglperolehan', 'label' => 'Tgl. Perolehan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti','maxlength' => 20, 'size' => 20, 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D', 'readonly' => $isrokeu);
	$a_input[] = array('kolom' => 'nospk', 'label' => 'No. SPK','maxlength' => 20, 'size' => 20, 'readonly' => $isroedit);
	$a_input[] = array('kolom' => 'tglspk', 'label' => 'Tgl. SPK', 'type' => 'D', 'readonly' => $isroedit);
	$a_input[] = array('kolom' => 'nopo', 'label' => 'No. PO','maxlength' => 20, 'size' => 20, 'readonly' => $isroedit);
	$a_input[] = array('kolom' => 'tglpo', 'label' => 'Tgl. PO', 'type' => 'D', 'readonly' => $isroedit);
	$a_input[] = array('kolom' => 'idsumberdana', 'label' => 'Sumber Dana', 'type' => 'S', 'option' => $a_sumberdana, 'add' => 'style="width:150px"', 'empty' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'size' => 30, 'readonly' => $isroedit);
	$a_input[] = array('kolom' => 'idsupplier', 'type' => 'H');
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 35, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    
    //detail
	$a_detail = $p_modeldet::getInputAttr(array('isro' => $isro));
    
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
		
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($r_key)){
			Aset::setInsert($record);
            
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);   //insert data
		    $r_key = $conn->Insert_ID();
		}else{
            $conn->BeginTrans();
            
            list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);   //update data
		    
		    if(!$p_posterr){
		        $ok = $p_model::updatePerolehan($conn, $r_key);
		        if(!$ok){
		            $p_posterr = true;
		            $p_postmsg = 'Perubahan data perolehan gagal';
		        }
	        }

		    if(!$p_posterr){
		        $otglbukti = substr($a_mdata['tglbukti'],0,4).substr($a_mdata['tglbukti'],5,2);
			    $ntglbukti = substr($record['tglbukti'],0,4).substr($record['tglbukti'],5,2);

			    if(isset($record['tglbukti']) and $otglbukti != $ntglbukti){
		            $ok = $p_model::setSusut($conn, $r_key);
		            if(!$ok){
		                $p_posterr = true;
		                $p_postmsg = 'Perhitungan penyusutan gagal';
		            }
	            }
	        }
		
		    if($p_posterr)
                $conn->RollbackTrans();
	        else
		        $conn->CommitTrans();
		}
		
		if(!$p_posterr) unset($post);

	}
	else if($r_act == 'delete' and $c_delete) {
	    if(!$p_posterr) list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	    
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet'){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
		
		$conn->BeginTrans();
		if(empty($r_keydet)){
		    $record['idperolehanheader'] = $r_key;
		    $record['idunit'] = $a_mdata['idunit'];
		    $record['idjenisperolehan'] = $a_mdata['idjenisperolehan'];
		    
			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else
			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);

		if(!$p_posterr){
		    $ok = $p_model::updatePerolehan($conn, $r_key);
		    if(!$ok){
		        $p_posterr = true;
		        $p_postmsg = 'Penambahan/perubahan data barang gagal';
		    }else{
		        $ok = $p_model::setListBarang($conn, $r_key);
		        if(!$ok){
		            $p_posterr = true;
		            $p_postmsg = 'Penambahan/perubahan data barang gagal';
		        }		        
		    }
		}
			
		if($p_posterr)
            $conn->RollbackTrans();
        else
	        $conn->CommitTrans();
        
        if(!$p_posterr) unset($post);

	}
	else if($r_actdet == 'deletedet'){
	    $conn->BeginTrans();

		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);

		if(!$p_posterr){
		    $ok = $p_model::setListBarang($conn, $r_key);
	        if(!$ok){
	            $p_posterr = true;
	            $p_postmsg = 'Penghapusan data barang gagal';
	        }
        }
		    
		if($p_posterr)
            $conn->RollbackTrans();
        else
	        $conn->CommitTrans();
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
    $i_idunit = Page::getDataInput($row,'idunit');
    
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
	    
	    if(count($det) > 0){
	        $c_delete = false;
	        if(in_array($r_role,array('A','kaproc')) and $a_mdata['idjenisperolehan'] != '100')
    	        $c_delete = true;
        }
	    
    	$a_mdata = $p_model::getMData($conn,$r_key);

		$a_btnprint = array();
		$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');
	}

	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
    }

    $lockmsg = Aset::isLock($conn, Aset::setTglToPeriode($a_mdata['tglperolehan']));
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
					    <? if($r_isverify) { ?>
					    <img src="images/verified.png" style="position:absolute;left:70%;" alt="Verified" width="300" />
					    <? } ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
				            <tr>
					            <td colspan="4" class="DataBG">Detail Perolehan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idperolehanheader') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idperolehanheader') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'nospk') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'nospk') ?></td>
	                        </tr>
	                        <tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'unit') ?></td>
								<td class="RightColumnBG">
									<?= Page::getDataInput($row,'unit') ?>
									<?= $i_idunit;//Page::getDataInput($row,'idunit') ?>
								</td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglspk') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglspk') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjenisperolehan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idjenisperolehan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'nopo') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'nopo') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglperolehan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglperolehan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpo') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpo') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpembukuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpembukuan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'nobukti') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'nobukti') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idsumberdana') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idsumberdana') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglbukti') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglbukti') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'namasupplier') ?></td>
		                        <td class="RightColumnBG">
		                            <?= Page::getDataInput($row,'namasupplier',' ',$r_namasupplier) ?>
		                            <?= Page::getDataInput($row,'idsupplier') ?>
		                        </td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG" rowspan="2"><?= Page::getDataInput($row,'catatan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
				            <tr>
					            <td colspan="4">&nbsp;</td>
				            </tr>
					    </table>
					    <?  if($r_key){ ?>
					    <?      if($c_insert and $a_mdata['idjenisperolehan'] != '100'){ ?>
					    <table align="left" width="<?= $p_tbwidth-22 ?>">
					        <tr>
					            <td>
                                    <input id="btnAddDetail" type="button" class="ControlStyle" value="Tambah Barang">
                                </td>
                            </tr>
					    </table>
					    <?      }  ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
					        <tr>
					            <td colspan="9" class="DataBG">Daftar Barang</td>
				            </tr>
					        <tr>
					            <th width="25">No.</th>
					            <th>Barang</th>
					            <th width="60">Merk</th>
					            <th width="140">Spesifikasi</th>
					            <th width="45">Jumlah</th>
					            <th width="80">Harga</th>
					            <th width="80">Total</th>
					            <th width="30" align="center">?</th>
					            <th width="30">Aksi</th>
					        </tr>
					        <?
				            $i = 0;
					        if(count($det) > 0){
					            foreach($det as $id => $val){
					                $i++;
					                $tot = (float)$val['qty']*(float)$val['harga'];
					        ?>

					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang1'].' - '.$val['namabarang'] ?></td>
					            <td><?= $val['merk'] ?></td>
					            <td><?= $val['spesifikasi'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qty']) ?></td>
					            <td align="right"><?= CStr::formatNumber($val['harga']) ?></td>
					            <td align="right"><?= CStr::formatNumber($tot) ?></td>
					            <td align="center"><?= ($val['isverify'] == '1') ? '<img title="Verified" src="images/check.png">' : '' ?></td>
					            <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['idperolehan'] ?>')" style="cursor:pointer">
						        </td>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr><td colspan="9" align="center"><b>-- Data tidak ditemukan --</b></td></tr>
					        <?
					        }
					        ?>
					    </table>
                        <?  } ?>
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
var detform = "<?= Route::navAddress('pop_perolehandetail') ?>";
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
	$("#barang").xautox({strpost: "f=acxbaranginv", targetid: "idbarang1"});

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

function goCetakBASTB() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbperolehan") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

</script>
</body>
</html>
