<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_opname');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('opname'));
	require_once(Route::getModelPath('opnamedetail'));
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
	$p_title = 'Data Opname';
	$p_tbwidth = 800;
	$p_aktivitas = 'opname';
	$p_listpage = Route::getListPage();
	
	$p_model = mOpname;
	$p_modeldet = mOpnameDetail;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_unit = mCombo::unitSave($conn,false);
	$a_lokasi = mCombo::lokasi($conn);
	$a_pemakai = array('' => '-- Pilih pemakai --');//mCombo::pemakai($conn);
	$a_statusopname = mCombo::statusopname();
	$a_status = mCombo::status($conn);

	$now = date('Y-m-d');

	//init
	$isro = false;
    $isroadd = true;
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn, $r_key);
	    $r_status = $a_mdata['status'];    
	    $r_idunit = $a_mdata['idunit'];    

	    if($r_status == 'S'){
	        $isro = true;
	    }
	}else{
	    $isroadd = false;
	    $r_idunit = $_POST['idunit'];
    }
    
    if(!empty($r_idunit))
	    $a_pemakai = mCombo::pemakai($conn,$r_idunit);

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idopname', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isroadd);
	$a_input[] = array('kolom' => 'idlokasi', 'label' => 'Lokasi', 'type' => 'S', 'option' => $a_lokasi, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isroadd);
	$a_input[] = array('kolom' => 'idpegawai', 'label' => 'Pemakai', 'type' => 'S', 'option' => $a_pemakai, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isroadd);
//    $a_input[] = array('kolom' => 'pegawai', 'label' => 'Pemakai', 'size' => 25, 'notnull' => true, 'readonly' => $isro);
//	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');	

	$a_input[] = array('kolom' => 'tglopname', 'label' => 'Tgl. Opname', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'R', 'default' => 'D', 'option' => $a_statusopname);

	//detail
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
		    $sqla = "insert into aset.as_opnamedetail (idopname,idseri) 
		        select $r_key, idseri from aset.as_seri 
		        where idunit = '{$record['idunit']}' and idlokasi = '{$record['idlokasi']}' and idpegawai = '{$record['idpegawai']}'";
	        $ok = $conn->Execute($sqla);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'setstatus' and $c_edit){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
		
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
        
        $a_mdata = $p_model::getMData($conn, $r_key);
	    $r_status = $a_mdata['status'];
	    
    	/*
    	$a_btnprint = array();
    	$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'SK Tim', 'onclick' => 'goCetakSKTim()');
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
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idopname') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idopname') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'tglopname') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'tglopname') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idlokasi') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idlokasi') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idpegawai') ?></td>
		                        <td class="RightColumnBG">
		                            <?//=// Page::getDataInput($row,'pegawai') ?>
		                            <?= Page::getDataInput($row,'idpegawai') ?>
	                            </td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'catatan') ?></td>
							</tr>
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
					            <td colspan="7" class="DataBG">Detail Opname</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th width="50">No. Seri</th>
					            <th>Barang</th>
					            <th width="100">Merk</th>
					            <th width="150">Spesifikasi</th>
					            <th width="100">Tgl. Perolehan</th>
						        <th width="40">Ada ?</th>
					        </tr>
					        <?
					        if(count($det) > 0){
					            $i = 0;
					            foreach($det as $id => $val){
					                $i++;
					                //$checked = '';
					                //if($val['idstatus'] == 'A') $checked = 'checked="checked"';
					                //$check = UI::createRadio('check',$a_status,$val['idstatus'],$c_edit,true,' class="check ControlStyle"');
					                $cb = UI::createSelect('check',$a_status,$val['idstatus'],'check ControlStyle',$c_edit,'onchange="setStatus(this,'.$val['iddetopname'].')"',true);
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= Aset::setFormatNoSeri($val['noseri']) ?></td>
					            <td><?= $val['barang'] ?></td>
					            <td><?= $val['merk'] ?></td>
					            <td><?= $val['spesifikasi'] ?></td>
					            <td><?= CStr::formatDateInd($val['tglperolehan'],false) ?></td>
						        <td align="center">
						        <?	if($c_edit and !$isro) { ?>
							        <?/*<input type="checkbox" class="check ControlStyle" value="<?= $val['iddetopname'] ?>" <?= $checked ?> />*/?>
							        <?= $cb ?>
						        <?	} ?>
						        </td>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="7" align="center"><b>-- Data tidak ditemukan --</b></td>
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
				<input type="hidden" name="keydet" id="keydet">
				<input type="hidden" name="idstatus" id="idstatus">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_opnamedetail') ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";
var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
//	$("#pegawai").xautox({strpost: "f=acxpegawai", targetid: "idpegawai"});
	
	loadCBLokasi();
    loadCBPemakai();

	// handle scrolltop
	$(window).scrollTop($("#scroll").val());

});
/*
$('.check').click(function(){
    if($(this).is(':checked'))
        $('#idstatus').val('A');
    else
        $('#idstatus').val('H');
    
    $('#keydet').val($(this).val());
    $('#act').val('setstatus');
    goSubmit();
});
*/
function setStatus(id,iddet){
    $('#idstatus').val($(id).val());
    $('#keydet').val(iddet);
    $('#act').val('setstatus');
    goSubmit();
}



$('#idunit').change(function(){
    loadCBLokasi();
    loadCBPemakai();
});
	
function loadCBLokasi(){
    $.post(ajaxpage, 
        {f: 'optlokasi', idunit: $('#idunit').val(), idlokasi: $('#idlokasi').val()}, 
        function(data){
            $('#idlokasi').html(data);
        }
    );
}

function loadCBPemakai(){
    $.post(ajaxpage, 
        {f: 'optpemakai', idunit: $('#idunit').val(), idpemakai: $('#idpegawai').val()}, 
        function(data){
            $('#idpegawai').html(data);
        }
    );
}

</script>
</body>
</html>
