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
	require_once(Route::getModelPath('perolehan'));
	require_once(Route::getModelPath('perolehandetail'));
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
	
	$p_model = mPerolehan;
	$p_modeldet = mPerolehanDetail;

    //user
    $r_role = Modul::getRole();
    if($r_role == 'kakeu' or $r_role == 'pk')
        $c_edit = true;
		
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_unit = mCombo::unitSave($conn,false);
	$a_lokasi = mCombo::lokasi($conn,false);
	$a_jenisperolehan = mCombo::jenisperolehan($conn);
	$a_sumberdana = mCombo::sumberdana($conn);
	$a_coa = mCombo::coa($conn);
	$a_dasarharga = mCombo::dasarharga($conn);
	$a_kondisi = mCombo::kondisi($conn);
	$a_merk = mCombo::merk($conn);
	$a_verify = array('1' => 'Verified');
	
	$now = date('Y-m-d');
	
	//init
	$isro = false;
	$isrokeu = false;
	$isroedit = false;
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];

	    if($r_isverify == '1'){
	        $isro = true;
	        $c_delete = false;
	        
			if(!in_array($r_role,array('kakeu','pk')))
				$isrokeu = true;
				
	        if(in_array($r_role,array('A','kaproc')))
    	        $c_delete = true;
	    }
	    //$periode = substr(str_replace('-','',$a_mdata['tglbukti']),0,6);
	    //echo $periode;
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idperolehan', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	//$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'unit', 'label' => 'Unit', 'size' => 35, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idunit', 'type' => 'H', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idjenisperolehan', 'label' => 'Jenis Perolehan', 'type' => 'S', 'option' => $a_jenisperolehan, 'default' => '101', 'add' => 'style="width:150px"', 'readonly' => $isro);
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
	$a_input[] = array('kolom' => 'idsupplier', 'type' => 'H', 'readonly' => $isroedit);
	$a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => true);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 35, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	
    $a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'class' => 'ControlAuto', 'size' => 50, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idbarang1', 'type' => 'H', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'qty', 'label' => 'Jumlah', 'type' => 'N', 'maxlength' => 6, 'size' => 6, 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'harga', 'label' => 'Harga Satuan', 'type' => 'N,2', 'maxlength' => 17, 'size' => 14, 'notnull' => true, 'readonly' => $isrokeu);
    $a_input[] = array('kolom' => 'total', 'label' => 'Total Perolehan', 'type' => 'N,2', 'maxlength' => 17, 'size' => 14, 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'iddasarharga', 'label' => 'Dasar Harga', 'type' => 'S', 'option' => $a_dasarharga,'default' => '1', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idkondisi', 'label' => 'Kondisi Perolehan', 'type' => 'S', 'option' => $a_kondisi,'default' => 'B', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idcoa', 'label' => 'COA', 'type' => 'S', 'option' => $a_coa,'empty' => true, 'readonly' => $isrokeu);

    $a_input[] = array('kolom' => 'thnprod', 'label' => 'Tahun Produksi', 'maxlength' => 4, 'size' => 5, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'merk', 'label' => 'Merk & Ukuran', 'type' => 'S', 'option' => $a_merk, 'add' => 'style="width:150px"', 'empty' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'ukuran', 'label' => 'Ukuran', 'maxlength' => 45, 'size' => 30, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'type' => 'A', 'rows' => 3, 'cols' => 45, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglgaransi', 'label' => 'Garansi s/d', 'type' => 'D', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'kmgaransi', 'label' => 'Garansi', 'type' => 'N,2', 'maxlength' => 20, 'size' => 20, 'readonly' => $isro);

    //detail
	$a_detail = $p_modeldet::getInputAttr();
    
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];

	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($r_key)){
			Aset::setInsert($record);
            $record['total'] = (float)$record['qty']*(float)$record['harga'];

			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);   //insert data
		    $r_key = $conn->Insert_ID();
		}else{
	        $setsusut = false;
	        $settotal = false;
	        
		    if(isset($record['harga']) and $record['harga'] != $a_mdata['harga']){  //cek perubahan harga
		        $setsusut = true;
		        $settotal = true;
	        }

		    if(isset($record['qty']) and $record['qty'] != $a_mdata['qty']) //cek perubahan jumlah
		        $settotal = true;
	        
	        if($settotal){
	            if($r_isverify == '1')
	                $u_qty = $a_mdata['qty'];
                else
	                $u_qty = $record['qty'];
                    
                $record['total'] = (float)$u_qty*(float)$record['harga'];
            }
            
            $conn->BeginTrans();
            
            list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);   //update data

			if(!$p_posterr and $r_isverify == '1')
			{
			    if($settotal)
        	        list($p_posterr,$p_postmsg) = $p_model::updateHarga($conn, $record['harga'], $r_key);   //update nilaiawal

			    $otglbukti = substr($a_mdata['tglbukti'],0,4).substr($a_mdata['tglbukti'],5,2);
			    $ntglbukti = substr($record['tglbukti'],0,4).substr($record['tglbukti'],5,2);

			    if(isset($record['tglbukti']) and $otglbukti != $ntglbukti)
			        $setsusut = true;
		        
                $ok = true;
		        if($setsusut)
			        $ok = mHistDepresiasi::setPenyusutan($conn, $r_key);
		        
		        if(!$ok){
		            $p_posterr = true;
		            $p_postmsg = 'Perhitungan penyusutan aset gagal, silahkan ulangi sekali lagi !';
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
		
		if(empty($r_keydet)){
		    $record['idperolehan'] = $r_key;
			list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		}else
			list($p_posterr,$p_postmsg) = $p_modeldet::updateCRecord($conn,$a_detail,$record,$r_keydet);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_actdet == 'deletedet'){
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
    $i_qty = Page::getDataInput($row,'qty');
    $i_idbarang = Page::getDataInput($row,'idbarang1');
    $i_idunit = Page::getDataInput($row,'idunit');
    
    //ambil detail
	if(!empty($r_key)){
	    //$det = $p_modeldet::getRowByIDP($conn, $r_key);
	    
	    if(count($det) > 0){
            $r_qty = Page::getDataValue($row,'qty');
            $i_qty = $r_qty.'<input type="hidden" name="qty" id="qty" value="'.$r_qty.'">';
	    }
	    
    	$a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isverify = $a_mdata['isverify'];

	    if($r_isverify == '1'){
	        $i_idbarang = '';
			$i_idunit = '';
	        
	        //$c_edit = false;
	        $c_delete = false;
	        if(in_array($r_role,array('A','kaproc')))
    	        $c_delete = true;
        }
	    
	    $r_qtyin = (int)$a_mdata['qty'];
	    $r_qtynow = (int)$p_modeldet::getSumQty($conn, $r_key);

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
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idperolehan') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idperolehan') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'nospk') ?></td>
		                        <td class="RightColumnBG width="280""><?= Page::getDataInput($row,'nospk') ?></td>
	                        </tr>
	                        <tr>
		                        <!--td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunit') ?></td-->
		                        <!--td class="RightColumnBG"><?= Page::getDataInput($row,'idunit') ?></td-->
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
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isverify') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
				            <tr>
					            <td colspan="4" class="DataBG">Detail Barang</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'barang') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            <?= Page::getDataInput($row,'barang') ?>
		                            <?= $i_idbarang;//Page::getDataInput($row,'idbarang1') ?>
		                        </td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'qty') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            <?= Page::getDataInput($row,'qty') ?>&nbsp;&nbsp;
		                            <?= Page::getDataInput($row,'idsatuan') ?>
		                        </td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'harga') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'harga') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'total') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'total') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'iddasarharga') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'iddasarharga') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idkondisi') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idkondisi') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idcoa') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'idcoa') ?></td>
	                        </tr>					    
				            <tr>
					            <td colspan="4" class="DataBG">Keterangan Tambahan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglgaransi') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            Tanggal:&nbsp;<?= Page::getDataInput($row,'tglgaransi') ?>&nbsp;&nbsp;/&nbsp;&nbsp;
		                            Km:&nbsp;<?= Page::getDataInput($row,'kmgaransi') ?>
		                        </td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'thnprod') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'thnprod') ?></td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'merk') ?></td>
		                        <td class="RightColumnBG" colspan="3">
		                            <?= Page::getDataInput($row,'merk') ?> &nbsp;&nbsp;,
		                            <?= Page::getDataInput($row,'ukuran') ?>
		                        </td>
	                        </tr>					    
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'spesifikasi') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'spesifikasi') ?></td>
	                        </tr>					    
				            <tr>
					            <td colspan="4">&nbsp;</td>
				            </tr>
					    </table>
					    <? /* if($r_key){ ?>
					    <?      if($r_qtyin != $r_qtynow){ ?>
					    <table align="left" width="<?= $p_tbwidth-22 ?>">
					        <tr>
					            <td>
                                    <input id="btnAddDetail" type="button" class="ControlStyle" value="Tambah Lokasi">
                                </td>
                            </tr>
					        <tr>
					            <td align="center"><font color="red"><b>Total jumlah barang per lokasi belum sama dengan jumlah perolehan</b></span></td>
                            </tr>
					    </table>
					    <?  }  ?>
					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
					        <tr>
					            <td colspan="<?= $r_isverify ? '4' : '5' ?>" class="DataBG">Set Lokasi Barang</td>
				            </tr>
					        <tr>
					            <th width="30">No.</th>
					            <th>Lokasi</th>
					            <th width="240">Pemakai</th>
					            <th width="80">Jumlah</th>
					            <?  if(!$r_isverify){ ?>
					            <th width="30">Aksi</th>
					            <?  } ?>
					        </tr>
					        <?
				            $i = 0;
					        if(count($det) > 0){
					            foreach($det as $id => $val){
					                $totqty += (int)$val['qty'];
					                $i++;
					        ?>
					        <tr valign="top">
					            <td><?= $i ?></td>
					            <td><?= $val['lokasi'] ?></td>
					            <td><?= $val['pegawai'] ?></td>
					            <td align="right"><?= CStr::formatNumber($val['qty']) ?></td>
					            <?  if(!$r_isverify){ ?>
						        <td align="center">
							        <img title="Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetperolehan'] ?>')" style="cursor:pointer">
						        </td>
						        <?  } ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr><td colspan="<?= $r_isverify ? '4' : '5' ?>" align="center"><b>-- Data tidak ditemukan --</b></td></tr>
					        <?
					        }
					        ?>
					        <tr>
					            <td colspan="3" align="right"><b>Total</b>&nbsp;&nbsp;&nbsp;</td>
					            <td align="right"><?= CStr::formatNumber($totqty) ?></td>
					            <?  if(!$r_isverify){ ?>
					            <td>&nbsp;</td>
					            <?  } ?>
					        </tr>
					    </table>
                        <?  } */?>
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
var detform = "<?= Route::navAddress('pop_inventarisasidetail') ?>";
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
