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
	$p_tbwidth = 800;
	$p_aktivitas = 'opname habis pakai';
	$p_listpage = Route::getListPage();
	
	$p_model = mOpnameHP;
	$p_modeldet = mOpnameHPDetail;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//$a_unit = mCombo::unitSave($conn,false);
	$a_unit = array('63' => '0211410 - Bagian Rumah Tangga');
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
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglopname', 'label' => 'Tgl. Opname', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'status', 'label' => 'Status', 'type' => 'R', 'option' => $a_statusopnamehp,'default' => 'D', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);

	$a_input[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti','maxlength' => 20, 'size' => 20, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');	
    
	//detail
	$a_detail = array();
	$a_detail[] = array('kolom' => 'qtyakhir', 'label' => 'Jumlah Akhir', 'type' => 'N,2');
	$a_detail[] = array('kolom' => 'catatan', 'label' => 'Catatan');
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
		    $record['status'] = 'D';
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		    
		    if(!$p_posterr){
		        $sqla = "insert into aset.as_opnamehpdetail (idopnamehp,idbarang,qtyawal,qtyakhir,idsatuan) 
		            select $r_key,idbarang,jmlstock as qtyawal,jmlstock,idsatuan as qtyakhir from aset.as_stockhp 
		            where jmlstock > 0 and idunit = '{$record['idunit']}'";
	            $ok = $conn->Execute($sqla);
            }
		}else{
		    $conn->BeginTrans();
		    
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			
			if(isset($record['status']) and $r_status != $record['status']){
			    if($record['status'] == 'S'){
			        $ok = $p_model::prosesOpname($conn,$r_key);
			        if(!$ok){ 
			            $p_posterr = true;
			            $p_postmsg = 'Perubahan status gagal';
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
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savedet' and $c_edit){
		list($post,$record) = uForm::getUpdateRecord($a_detail,$_POST);
		
		list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'suntingdet' and $c_edit){
	    $r_ideditdet = $r_keydet;
	}

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$i_catatan = Page::getDataInput($row,'catatan');
	
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        
        $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_status = $a_mdata['status'];
	    
	    if($r_status == 'S'){
	        $c_delete = false;
	        $c_edit = false;
	    }

        if(!empty($r_ideditdet)) 
            $i_catatan = Page::getDataValue($row,'catatan');
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
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idopnamehp') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idopnamehp') ?></td>
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
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglopname') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglopname') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= $i_catatan;//Page::getDataInput($row,'catatan') ?></td>
							</tr>
	                        <tr valign="top">
		                        <td colspan="4">&nbsp;</td>
	                        </tr>
					    </table>
					    <?  if(!empty($r_key)) { ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				            <tr>
					            <td colspan="<?= $c_edit ? '7' : '6' ?>" class="DataBG">Detail Opname Habis Pakai</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th>Barang</th>
					            <th width="80">Satuan</th>
					            <th width="80">Jml. Awal</th>
					            <th width="80">Jml. Opname</th>
					            <th width="100">Catatan</th>
						        <?	if($c_edit) { ?>
						        <th width="30">Aksi</th>
						        <?	} ?>
					        </tr>
					        <?
					        $a_reqdet = array('qtyakhir');
					        if(count($det) > 0){
					            $i = 0;
					            foreach($det as $id => $val){
					                $i++;
					                if($r_ideditdet == $val['iddetopnamehp']){
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang'].' - '.$val['namabarang'] ?></td>
					            <td><?= $val['idsatuan'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtyawal'],2) ?></td>
						        <td align="right">
						            <input type="text" name="u_qtyakhir" size="6" value="<?= CStr::formatNumber($val['qtyakhir'],2) ?>" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
					            </td>
					            <td>
					                <textarea name="u_catatan" cols="20" rows="2" class="ControlStyle" wrap="soft" onkeyup="return charNum(this,255)"><?= $val['catatan'] ?></textarea>
				                </td>
						        <td align="center"><img title="Simpan" src="images/disk.png" onclick="goSaveDet()" style="cursor:pointer"></td>
					        </tr>
					        <?      }else{ ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['idbarang'].' - '.$val['namabarang'] ?></td>
					            <td><?= $val['idsatuan'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qtyawal'],2) ?></td>
						        <td align="right"><?= CStr::formatNumber($val['qtyakhir'],2) ?></td>
					            <td><?= nl2br($val['catatan']) ?></td>
						        <?	if($c_edit) { ?>
						        <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetopnamehp'] ?>')" style="cursor:pointer">
						        </td>
						        <?	} ?>
					        </tr>
					        <?
					                }
					            }
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
				<input type="hidden" name="keydet" id="keydet" value="<?= $r_ideditdet ?>">
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
var detform = "<?= Route::navAddress('pop_penghapusandetail') ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";
var required = "<?= @implode(',',$a_required) ?>";
var reqdet = "<?= @implode(',',$a_reqdet) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
});

function goDetailDet(pkeydet){
    $("#keydet").val(pkeydet);
    $("#act").val('suntingdet');
    goSubmit();
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

</script>
</body>
</html>
