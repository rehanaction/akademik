<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_perawatan');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('rawat'));
	require_once(Route::getModelPath('rawatdetail'));
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
	$p_title = 'Data Perawatan Barang';
	$p_tbwidth = 850;
	$p_aktivitas = 'Perawatan';
	$p_listpage = Route::getListPage();
	
	$p_model = mRawat;
	$p_modeldet = mRawatDetail;

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
	
	//init
	$isro = true;
	$isroverify = true;
	$isrook1 = true;
	$isrorawat = true;
	
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isok1 = $a_mdata['isok1'];
	    $r_isverify = $a_mdata['isverify'];
	    $r_idunit = $a_mdata['idunit'];

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
	$a_lokasi = mCombo::lokasi($conn, $r_idunit);
	//$a_jenisrawat = mCombo::jenisrawat($conn);
	$a_verify = array('1' => 'Verified');
	$a_setuju = array('0' => 'Ditolak','1' => 'Disetujui');
	$a_status = mCombo::statusproses();
    
	$now = date('Y-m-d');
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idrawat', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idlokasi', 'label' => 'Lokasi', 'type' => 'S', 'option' => $a_lokasi, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	//$a_input[] = array('kolom' => 'idjenisrawat', 'label' => 'Jenis Perawatan', 'type' => 'S', 'option' => $a_jenisrawat, 'notnull' => true, 'add' => 'style="width:150px"', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'default' => $now,'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    //$a_input[] = array('kolom' => 'keluhan', 'label' => 'Keluhan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'option' => $a_status, 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 30, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idbarang', 'type' => 'H');
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);

    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
	$a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'isok1', 'label' => 'Setuju ?', 'type' => 'R', 'option' => $a_setuju, 'readonly' => $isrook1);
    $a_input[] = array('kolom' => 'memo1', 'label' => 'Memo', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isrook1);
	$a_input[] = array('kolom' => 'isok1user', 'label' => 'Setuju User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'isok1time', 'label' => 'Setuju Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');

    $a_input[] = array('kolom' => 'tglrawat', 'label' => 'Tgl. Perawatan', 'type' => 'D', 'readonly' => $isrorawat);
    $a_input[] = array('kolom' => 'tglkembali', 'label' => 'Tgl. Kembali', 'type' => 'D', 'readonly' => $isrorawat);
    $a_input[] = array('kolom' => 'namasupplier', 'label' => 'Supplier', 'class' => 'ControlAuto', 'size' => 30, 'readonly' => $isrorawat);
    $a_input[] = array('kolom' => 'idsupplier', 'type' => 'H');
	//$a_input[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti','maxlength' => 20, 'size' => 20, 'readonly' => $isrorawat);
	//$a_input[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D', 'readonly' => $isrorawat);
	//$a_input[] = array('kolom' => 'nospk', 'label' => 'No. SPK','maxlength' => 20, 'size' => 20, 'readonly' => $isrorawat);
	//$a_input[] = array('kolom' => 'tglspk', 'label' => 'Tgl. SPK', 'type' => 'D', 'readonly' => $isrorawat);
    //$a_input[] = array('kolom' => 'biaya', 'label' => 'Biaya', 'type' => 'N,2', 'maxlength' => 20, 'size' => 10, 'readonly' => $isrorawat);

	//detail
	$a_detail = $p_modeldet::getInputAttr();
	
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
				$p_model::mailRawat($conn, $key);
			}
			
			if($r_isok1 == '1'){
			    if(isset($record['tglrawat']) and $a_mdata['tglrawat'] != $record['tglrawat'])
			        $ok = $p_model::setRawat($conn, $r_key, $a_mdata['tglrawat'], $record['tglrawat']);
			    else if(isset($record['tglkembali']) and $a_mdata['tglkembali'] != $record['tglkembali'])
			        $ok = $p_model::setKembali($conn, $r_key, $a_mdata['tglkembali'], $record['tglkembali']);
		        
	            if(!$ok){ 
                    $p_posterr = true;
                    $p_postmsg = 'Proses perawatan gagal !';
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
		    $record['idrawat'] = $r_key;
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

	$i_idunit = Page::getDataInput($row,'idunit');
	$i_idlokasi = Page::getDataInput($row,'idlokasi');
	$i_barang = Page::getDataInput($row,'barang');
	$i_isverify = Page::getDataInput($row,'isverify');
	$i_isok1 = Page::getDataInput($row,'isok1');
	$i_tglrawat = Page::getDataInput($row,'tglrawat');
	$i_tglkembali = Page::getDataInput($row,'tglkembali');
	
	$a_pass = array();
	
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        $r_ndet = count($det);

        $a_mdata = $p_model::getMData($conn,$r_key);
        
	    $r_isok1 = $a_mdata['isok1'];
	    $r_isverify = $a_mdata['isverify'];
	    $r_idunit = $a_mdata['idunit'];
	    $r_idlokasi = $a_mdata['idlokasi'];

        if($r_ndet > 0){
            $i_idunit = str_replace('&nbsp;','',$a_unit[Page::getDataValue($row,'idunit')]);
            $i_idunit .= '<input type="hidden" id="idunit" name="idunit" value="'.$r_idunit.'">';

            $i_idlokasi = $a_lokasi[Page::getDataValue($row,'idlokasi')];
            $i_idlokasi .= '<input type="hidden" id="idlokasi" name="idlokasi" value="'.$r_idlokasi.'">';
            
            $i_barang = Page::getDataValue($row,'barang');

            $a_pass[] = 'idunit';
            $a_pass[] = 'idlokasi';
            $a_pass[] = 'barang';
        }
        
        if(empty($a_mdata['tglrawat'])){
            //$i_tglkembali = Page::getDataValue($row,'tglkembali');
            //$i_tglkembali .= '<input type="hidden" id="tglkembali" name="tglkembali" value="'.$i_tglkembali.'">';
            $i_tglkembali = '<i>Silahkan mengisi Tgl. Perawatan terlebih dahulu</i>';
        }
        
        if(!empty($a_mdata['tglkembali'])){
            $i_tglrawat = CStr::formatDateInd(Page::getDataValue($row,'tglrawat'),false);
            $i_tglrawat .= '<input type="hidden" id="tglrawat" name="tglrawat" value="'.$a_mdata['tglrawat'].'">';
        }
        
    	if($r_isverify == '1'){
    	    $c_delete = false;
    	}

    	if($r_isok1 == '1'){
        	$a_btnprint = array();
        	$a_btnprint[] = array('id' => 'be_cetakbastbu', 'label' => 'BASTB Unit', 'onclick' => 'goCetakBASTBU()');
        	$a_btnprint[] = array('id' => 'be_cetakbastbp', 'label' => 'BASTB Supplier', 'onclick' => 'goCetakBASTBP()');
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
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idrawat') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idrawat') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'tglpengajuan') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'tglpengajuan') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td>
		                        <td class="RightColumnBG"><?= $i_idunit; //Page::getDataInput($row,'idunit') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idlokasi') ?></td>
		                        <td class="RightColumnBG"><?= $i_idlokasi; //Page::getDataInput($row,'idlokasi') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'barang') ?></td>
		                        <td class="RightColumnBG">
		                            <?= $i_barang; ?><?= Page::getDataInput($row,'idbarang') ?>
		                        </td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'catatan') ?></td>
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
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifyuser') ?></td>
		                        <?/*
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isok1user') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isok1user') ?></td>
		                        */?>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifytime') ?></td>
		                        <?/*
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isok1time') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isok1time') ?></td>
		                        */?>
	                        </tr>
	                        <?  }?>
	                        <?  if($r_isok1 == '1' and $c_verify){ ?>
				            <tr>
					            <td colspan="4" class="DataBG">Proses Perawatan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglrawat') ?></td>
		                        <td class="RightColumnBG"><?= $i_tglrawat;//Page::getDataInput($row,'tglrawat') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglkembali') ?></td>
		                        <td class="RightColumnBG"><?= $i_tglkembali;//Page::getDataInput($row,'tglkembali') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'namasupplier') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            <?= Page::getDataInput($row,'namasupplier') ?>
		                            <?= Page::getDataInput($row,'idsupplier') ?>
		                        </td>
	                        </tr>
	                        <?  } ?>
	                        <tr>
					            <td colspan="4">&nbsp;</td>
				            </tr>
					    </table>
					    <?  if(!empty($r_key) ) { ?>
					    <?  if($r_isverify != '1' and !$c_verify) { ?>
					    <table align="left" width="<?= $p_tbwidth-22 ?>">
					        <tr>
					            <td width="30">
                                    <input id="btnAddDetail" type="button" class="ControlStyle" value="Tambah Seri">
<?/*
					                No. Seri &nbsp;:&nbsp;&nbsp;
					                <input type="text" size="6" maxlength="6" class="ControlStyle" name="snoseri" id="snoseri" onkeydown="return onlyNumber(event,this,true,true)">&nbsp;&nbsp;
                                    <input type="button" class="ControlStyle" value="Tambah" onclick="goAddSingleSeri()">
					                &nbsp;&nbsp;&nbsp;&nbsp;<i><b>Atau</b></i>&nbsp;&nbsp;&nbsp;&nbsp;
					                No. Seri &nbsp;:&nbsp;&nbsp;
					                <input type="text" size="6" maxlength="6" class="ControlStyle" name="fnoseri" id="fnoseri" onkeydown="return onlyNumber(event,this,true,true)">&nbsp;&nbsp;s/d&nbsp;&nbsp;
					                <input type="text" size="6" maxlength="6" class="ControlStyle" name="enoseri" id="enoseri" onkeydown="return onlyNumber(event,this,true,true)">&nbsp;&nbsp;
                                    <input type="button" class="ControlStyle" value="Tambah" onclick="goAddSeri()">
*/?>
                                </td>
                            </tr>
					    </table>					    
					    <?  } ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				            <tr>
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '8' : '6' ?>" class="DataBG">Daftar Seri Barang</td>
				            </tr>
					        <tr>
					            <th width="50">No. Seri</th>
					            <th width="80">Tgl. Perolehan</th>
					            <th width="80">Tgl. Garansi</th>
					            <th>Spesifikasi</th>
					            <th width="150">Jenis Perawatan</th>
					            <th width="150">Keluhan</th>
					            <?  if($c_verify){ ?>
					            <th width="70">Biaya</th>
					            <?  } ?>
						        <?	if($r_isverify != '1' or $c_verify){ ?>
						        <th width="30">Aksi</th>
						        <?  } ?>
					        </tr>
					        <?
					        if(count($det) > 0){
					            foreach($det as $id => $val){
					        ?>
					        <tr valign="top">
					            <td><?= Aset::setFormatNoSeri($val['noseri']) ?></td>
					            <td><?= CStr::formatDateInd($val['tglperolehan'],false) ?></td>
					            <td><?= CStr::formatDateInd($val['tglgaransi'],false) ?></td>
					            <td><?= $val['spesifikasi'] ?></td>
					            <td><?= $val['jenisrawat'] ?></td>
					            <td><?= $val['keluhan'] ?></td>
					            <?  if($c_verify){ ?>
					            <td align="right"><?= CStr::formatNumber($val['biaya'],2) ?></td>
						        <?  } ?>
						        <?	if($r_isverify != '1' or $c_verify){ ?>
						        <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetrawat'] ?>')" style="cursor:pointer">
						        </td>
						        <?  } ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '7' : '6' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
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
				<input type="hidden" name="hidunit" id="hidunit" value="<?= $r_idunit ?>">
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
var detform = "<?= Route::navAddress('pop_perawatandetail') ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";
var required = "<?= @implode(',',$a_required) ?>";
var axcbarang = '';

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	// autocomplete
	//$("#barang").xautox({strpost: "f=acxbaranginv", targetid: "idbarang"});
	$("#namasupplier").xautox({strpost: "f=acxsupplier", targetid: "idsupplier"});
	
	$('#btnAddDetail').click(function(){
        openDetail($('#key').val(), '');
	});
    
    loadCBLokasi();
    setAutoBarang();
});

$('#idunit').change(function(){
    loadCBLokasi();
    
    $('#barang').val('');
    $('#idbarang').val('');
    setAutoBarang();
});

$('#idlokasi').change(function(){
    $('#barang').val('');
    $('#idbarang').val('');
    setAutoBarang();
});
	
function loadCBLokasi(){
    $.post(ajaxpage, 
        {f: 'optlokasi', idunit: $('#idunit').val(), idlokasi: $('#idlokasi').val()}, 
        function(data){
            $('#idlokasi').html(data);
            $("#idlokasi option:first").attr('selected','selected');
            setAutoBarang();
        }
    );
}

function setAutoBarang(){
    r_idunit = $('#idunit').val();
    r_idlokasi = $('#idlokasi').val();
    
    $('#barang').unbind();
	$("#barang").xautox({strpost: "f=acxbarangunit&idunit="+r_idunit+"&idlokasi="+r_idlokasi, targetid: "idbarang"});
}

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

function goAddSingleSeri(){
    if($('#snoseri').val() == '')
        alert('Silahkan mengisi no. seri terlebih dahulu !');
    else{
        $.post(ajaxpage, 
            { f: 'cekseribarang', idunit: $('#hidunit').val(), idbarang: $('#idbarang').val(), noseri: $('#snoseri').val() },
            function(idseri){
                if(idseri == ''){
                    alert('No. seri tersebut tidak dapat dipilih untuk unit ini');
                }else{
                    $("#idseri").val(idseri);
                    $("#act").val('savedet');
                    goSubmit();
                }
            }
        );
    }
}

function goDeleteDet(keydet){
    if(confirm('Apakah anda yakin akan menghapus data ini ?')){
        $("#keydet").val(keydet);
        $("#act").val('deletedet');
        goSubmit();
    }
}

function goCetakBASTBU() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbrawatunit") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

function goCetakBASTBP() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbrawatsupplier") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

</script>
</body>
</html>
