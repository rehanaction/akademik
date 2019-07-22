<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_opnamehp');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('opnamehp'));
	require_once(Route::getModelPath('opnamehpdetail'));
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
	$p_title = 'Data Opname Habis Pakai';
	$p_tbwidth = 700;
	$p_aktivitas = 'opname habis pakai';
	$p_listpage = Route::getListPage();
	
	$p_model = mOpnameHP;
	$p_modeldet = mOpnameHPDetail;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_unit = mCombo::unitSave($conn,false);
	$a_statusopnamehp = mCombo::statusopnamehp();
	
	$now = date('Y-m-d');
	
	//init
	$isro = false;
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_status = $a_mdata['status'];

	    if($r_status == 'S'){
	        $isro = true;
	    }
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idopnamehp', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:300px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglopname', 'label' => 'Tgl. Opname', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti','maxlength' => 20, 'size' => 20, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'R', 'option' => $a_statusopnamehp,'default' => 'D', 'readonly' => $isro);
    
	//detail
	$a_detail = array();
	$a_detail = $p_modeldet::getInputAttr(array('isro' => $isro));
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
		    $record['status'] = 'D';
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
	else if($r_actdet == 'savedet' and $c_edit){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
		
		if(empty($r_keydet)){
		    $record['idopnamehp'] = $r_key;
			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else
			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_actdet == 'deletedet' and $c_delete){
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        
        if(count($a_mdata) == 0)
            $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_status = $a_mdata['status'];

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
					        <?= Page::getDataTR($row,'idopnamehp') ?>
					        <?= Page::getDataTR($row,'idunit') ?>
					        <?= Page::getDataTR($row,'tglopname') ?>
					        <?= Page::getDataTR($row,'tglpembukuan') ?>
					        <?  if(!empty($r_key)) { ?>
					        <?= Page::getDataTR($row,'nobukti') ?>
					        <?= Page::getDataTR($row,'tglbukti') ?>
					        <?  } ?>
					        <?= Page::getDataTR($row,'catatan') ?>
					        <?  if(!empty($r_key)) { ?>
					        <?= Page::getDataTR($row,'status') ?>
					        <?  } ?>
					    </table>
					    <?  if(!empty($r_key)) { ?>
					    <br><br>
					    <? /* if($c_insert and !$isro) { ?>
					    <table align="left">
					        <tr>
					            <td>
                                    <input id="btnAddDetail" type="button" class="ControlStyle" value="Tambah Barang">
                                </td>
                            </tr>
					    </table>					    
					    <?  } */ ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				            <tr>
					            <td colspan="<?= $c_edit ? '7' : '6' ?>" class="DataBG">Detail Opname Habis Pakai</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th width="100">Kode Barang</th>
					            <th>Nama Barang</th>
					            <th width="80">Satuan</th>
					            <th width="80">Jml. Awal</th>
					            <th width="80">Jml. Opname</th>
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
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang'] ?></td>
					            <td><?= $val['namabarang'] ?></td>
					            <td><?= $val['idsatuan'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtyawal'],2) ?></td>
						        <td align="right"><?= CStr::formatNumber($val['qtyakhir'],2) ?></td>
						        <?	if($c_edit and !$isro) { ?>
						        <td align="center">
							        <img title="Lihat Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetopnamehp'] ?>')" style="cursor:pointer">
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

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_penghapusandetail') ?>";
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
