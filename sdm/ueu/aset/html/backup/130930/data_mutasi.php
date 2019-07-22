<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_mutasi');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = true;

	//$c_ok1 = $c_other;
	
	// include
	require_once(Route::getModelPath('mutasi'));
	require_once(Route::getModelPath('mutasidetail'));
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
	$p_title = 'Data Mutasi Barang';
	$p_tbwidth = 800;
	$p_aktivitas = 'Mutasi';
	$p_listpage = Route::getListPage();
	
	$p_model = mMutasi;
	$p_modeldet = mMutasiDetail;
	
    //user role
    $r_role = Modul::getRole();
    if(in_array($r_role, Aset::getRoleRM()))
        $c_verify = true;
        
    if($r_role == 'kadu')
        $c_ok1 = true;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_unitasal = mCombo::unitSave($conn,false);
	$a_unittujuan = mCombo::unitSave($conn,false);
	$a_lokasiasal = mCombo::lokasi($conn,false);
	$a_lokasitujuan = mCombo::lokasi($conn,false);
	$a_verify = array('1' => 'Verified');
	$a_setuju = array('0' => 'Ditolak','1' => 'Disetujui');
	$a_status = mCombo::statusproses();
	
	$now = date('Y-m-d');
	
	//init
	$isro = true;
	$isroverify = true;
	$isrook1 = true;
	$isromutasi = true;

	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isok1 = $a_mdata['isok1'];
	    $r_isverify = $a_mdata['isverify'];

	    if($r_isverify != '1'){
	        $isro = false;
	    }
	    
	    if($c_verify){
	        $isro = true;
	        $isroverify = false;
	        $isromutasi = false;
	        
	        if($r_isok1 != ''){
	            $isroverify = true;
	        }
	    }
	    
	    if($c_ok1 and $r_isverify == '1'){
        	$isrook1 = false;
	    }
	}else{
	    $isro = false;
	}

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idmutasi', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunitasal', 'label' => 'Unit Asal', 'type' => 'S', 'option' => $a_unitasal, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idlokasiasal', 'label' => 'Lokasi Asal', 'type' => 'S', 'option' => $a_lokasiasal, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'pegawaiasal', 'label' => 'Pemakai Asal', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idpegawaiasal', 'type' => 'H');
    $a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'option' => $a_status, 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idunittujuan', 'label' => 'Unit Tujuan', 'type' => 'S', 'option' => $a_unittujuan, 'add' => 'style="width:250px"', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idlokasitujuan', 'label' => 'Lokasi Tujuan', 'type' => 'S', 'option' => $a_lokasitujuan, 'add' => 'style="width:250px"', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'namapegawai', 'label' => 'Pemakai Tujuan', 'size' => 30, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idpegawaitujuan', 'type' => 'H');

    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'isok1', 'label' => 'Setuju ?', 'type' => 'R', 'option' => $a_setuju, 'readonly' => $isrook1);
    $a_input[] = array('kolom' => 'memo1', 'label' => 'Memo', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isrook1);
	$a_input[] = array('kolom' => 'isok1user', 'label' => 'Setuju User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'isok1time', 'label' => 'Setuju Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');

	$a_input[] = array('kolom' => 'tglmutasi', 'label' => 'Tgl. Mutasi', 'type' => 'D', 'readonly' => $isromutasi);


	//detail
	$a_detail = $p_modeldet::getInputAttr(array('isro' => $isro));
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
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
			Aset::setOk1($record, $r_isok1, $record['isok1']);
            Aset::setVerify($record, $r_isverify, $record['isverify']);
            
            $conn->BeginTrans();

			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			
			if(!$p_posterr and $record['V']){
				$p_model::mailMutasi($conn, $key);
			}
			
            if($r_isok1 == '1'){
		        if(isset($record['tglmutasi']) and $a_mdata['tglmutasi'] != $record['tglmutasi']){
		            if(empty($record['tglmutasi']) or $record['tglmutasi'] == 'null')
    		            $ok = $p_model::setBatal($conn, $r_key);
		            else
    		            $ok = $p_model::setMutasi($conn, $r_key);
		            
		            if(!$ok){ 
		                $p_posterr = true;
		                $p_postmsg = 'Proses mutasi gagal !';
	                }
		        }
            }
			
			if($p_posterr){
			    $conn->RollbackTrans();
			}else{
			    $conn->CommitTrans();
			}
		}
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_actdet == 'savedet' and $c_edit){
		list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
		
		if(empty($r_keydet)){
		    $record['idmutasi'] = $r_key;
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
	
	$i_idunitasal = Page::getDataInput($row,'idunitasal');
	$i_idlokasiasal = Page::getDataInput($row,'idlokasiasal');
	$i_pegawaiasal = Page::getDataInput($row,'pegawaiasal');

	$i_isverify = Page::getDataInput($row,'isverify');
	$i_isok1 = Page::getDataInput($row,'isok1');
    
    $a_pass = array();
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        $r_ndet = count($det);

        $a_mdata = $p_model::getMData($conn,$r_key);

		$r_idunitasal = $a_mdata['idunitasal'];
		$r_idlokasiasal = $a_mdata['idlokasiasal'];

		$r_idunittujuan = $a_mdata['idunittujuan'];
		$r_idlokasitujuan = $a_mdata['idlokasitujuan'];

	    $r_isverify = $a_mdata['isverify'];	    
		$r_isok1 = $a_mdata['isok1'];

		if($r_ndet > 0){
            $i_idunitasal = str_replace('&nbsp;','',$a_unitasal[Page::getDataValue($row,'idunitasal')]);
            $i_idunitasal .= '<input type="hidden" id="idunitasal" name="idunitasal" value="'.$r_idunitasal.'">';

            $i_idlokasiasal = str_replace('&nbsp;','',$a_lokasiasal[Page::getDataValue($row,'idlokasiasal')]);
            $i_idlokasiasal .= '<input type="hidden" id="idlokasiasal" name="idlokasiasal" value="'.$r_idlokasiasal.'">';
            
			$i_pegawaiasal = Page::getDataValue($row,'pegawaiasal');

            $a_pass[] = 'idunitasal';
            $a_pass[] = 'idlokasiasal';
            $a_pass[] = 'pegawaiasal';
		}

    	if($r_isverify == '1'){
			$c_delete = false;
		}

		if($r_isok1 == '1'){
			$a_btnprint = array();
			$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');
		}
	}

	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'] and !in_array($t_row['id'],$a_pass))
			$a_required[] = $t_row['id'];
    }

    $lockmsg = Aset::isLock($conn, Aset::setTglToPeriode($a_mdata['tglpengajuan']));
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
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idmutasi') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idmutasi') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'status') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunitasal') ?></td>
		                        <td class="RightColumnBG"><?= $i_idunitasal //Page::getDataInput($row,'idunitasal') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunittujuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idunittujuan') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idlokasiasal') ?></td>
		                        <td class="RightColumnBG"><?= $i_idlokasiasal //Page::getDataInput($row,'idlokasiasal') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idlokasitujuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idlokasitujuan') ?></td>
	                        </tr>	                        				        
	                        <tr>
								<td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'pegawaiasal') ?></td>
		                        <td valign="top" class="RightColumnBG">
		                            <?= $i_pegawaiasal //Page::getDataInput($row,'pegawai') ?>
		                            <?= Page::getDataInput($row,'idpegawaiasal') ?>
		                        </td>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'namapegawai') ?></td>
		                        <td class="RightColumnBG">
		                            <?= Page::getDataInput($row,'namapegawai') ?>
		                            <?= Page::getDataInput($row,'idpegawaitujuan') ?>
		                        </td>
	                        </tr>
							<tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpengajuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpengajuan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
							<tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
							</tr>
							<tr>
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
	                        <tr>
		                        <td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'verifynote') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'verifynote') ?></td>
		                        <td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'memo1') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'memo1') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifyuser') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifytime') ?></td>
	                        </tr>	
							<? } ?>     
							<?	if($r_isok1 == '1' and $c_verify){ ?>                   				        
					        <tr>
					            <td colspan="4" class="DataBG">Proses Mutasi</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglmutasi') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglmutasi') ?></td>
	                        </tr>	
							<? } ?>             
					    </table>
					    <?  if(!empty($r_key)) { ?>
					    <br><br>
					    <?  if($r_isverify != '1' and !$c_verify) { ?>
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
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '8' : '7' ?>" class="DataBG">Detail Mutasi</td>
				            </tr>
					        <tr>
					            <th width="50">No. Seri</th>
					            <th>Barang</th>
					            <th width="80">Merk</th>
					            <th width="150">Spesifikasi</th>
						        <th width="60">ID. Lokasi</th>
						        <th width="100">Pemakai</th>
						        <th width="80">Tgl. Perolehan</th>
					            <?	if($r_isverify != '1' or $c_verify) { ?>
						        <th width="30">Aksi</th>
						        <?	} ?>
					        </tr>
					        <?
					        if(count($det) > 0){
					            foreach($det as $id => $val){
					        ?>
					        <tr valign="top">
					            <td><?= Aset::formatNoSeri($val['noseri']) ?></td>
					            <td><?= $val['barang'] ?></td>
					            <td><?= $val['merk'] ?></td>
					            <td><?= $val['spesifikasi'] ?></td>
					            <td><?= $val['idlokasi'] ?></td>
					            <td><?= $val['pemakai'] ?></td>
					            <td><?= CStr::formatDateInd($val['tglperolehan'],false) ?></td>
						        <?	if($r_isverify != '1' or $c_verify) { ?>
						        <td align="center">
							        <img title="Hapus Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetmutasi'] ?>')" style="cursor:pointer">
						        </td>
						        <?	} ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '8' : '7' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
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
				<!--input type="hidden" name="hidunitasal" id="hidunitasal" value="<?= $r_idunitasal ?>"-->
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
var detform = "<?= Route::navAddress('pop_mutasidetail') ?>";
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
	
	// autocomplete
	$("#namapegawai").xautox({strpost: "f=acxpegawai", targetid: "idpegawaitujuan"});

	loadCBLokasiAsal();
	loadCBLokasiTujuan();
    setAutoPegawai();
    setAutoPegawaiAsal();
});

$('#idunitasal').change(function(){
    loadCBLokasiAsal();
});
	
function loadCBLokasiAsal(){
    $.post(ajaxpage, 
        {f: 'optlokasi', idunit: $('#idunitasal').val(), idlokasi: $('#idlokasiasal').val()}, 
        function(data){
            $('#idlokasiasal').html(data);
        }
    );
}

$('#idunittujuan').change(function(){
    loadCBLokasiTujuan();
});
	
function loadCBLokasiTujuan(){
    $.post(ajaxpage, 
        {f: 'optlokasi', idunit: $('#idunittujuan').val(), idlokasi: $('#idlokasitujuan').val()}, 
        function(data){
            $('#idlokasitujuan').html(data);
        }
    );
}

// autocomplete
function setAutoPegawai(){
    $("#namapegawai").xautox({strpost: "f=acxpegawai&idunit="+$('#idunittujuan').val(), targetid: "idpegawaitujuan"});
}

$('#idunittujuan').change(function(){
    $('#namapegawai').unbind();
    $('#namapegawai').val('');
    $('#idpegawaitujuan').val('');

    setAutoPegawai();
});

function setAutoPegawaiAsal(){
    $("#pegawaiasal").xautox({strpost: "f=acxpegawai&idunit="+$('#idunitasal').val(), targetid: "idpegawaiasal"});
}

$('#idunitasal').change(function(){
    $('#pegawaiasal').unbind();
    $('#pegawaiasal').val('');
    $('#idpegawaiasal').val('');

    setAutoPegawaiAsal();
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
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbmutasi") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

</script>
</body>
</html>
