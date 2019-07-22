<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth('list_penghapusan');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_verify = $a_auth['canother']['V'];
	$c_setuju = $a_auth['canother']['S'];
	$c_nilai = $a_auth['canother']['H'];
	$c_other = true;
	
	
	//echo 'verify :'.$c_verify.'<br> setuju : '.$c_setuju.'<br> show Nilai : '.$c_nilai;
	//$c_ok1 = $c_other;
	
	// include
	require_once(Route::getModelPath('penghapusan'));
	require_once(Route::getModelPath('penghapusandetail'));
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
	$p_title = 'Data Penghapusan Barang';
	$p_tbwidth = 850;
	$p_aktivitas = 'penghapusan';
	$p_listpage = Route::getListPage();
	
	$p_model = mPenghapusan;
	$p_modeldet = mPenghapusanDetail;
	
    //user role
    /*$r_role = Modul::getRole();
    if(in_array($r_role, Aset::getRoleRM())){
        $c_verify = true;
		$s_nilaihapus = true;
	}
        
    if($r_role == 'kadu')
        $c_ok1 = true;
	
	if(in_array($r_role, Aset::getRoleSuperAset()))
		$c_edithapus = true;*/
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	$a_unit = mCombo::unitSave($conn,false);
	$a_lokasi = mCombo::lokasi($conn,false);
	$a_jenispenghapusan = mCombo::jenispenghapusan($conn);
	$a_verify = array('1' => 'Verified');
	$a_setuju = array('0' => 'Ditolak','1' => 'Disetujui');
	$a_status = mCombo::statusproses();

	$now = date('Y-m-d');
	
	//init
	$isro = true;
	$isrokeu = true;
	$isroverify = true;
	$isrook1 = true;
	$isrohapus = true;
	$isroedit = true;

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
	        $isrohapus = false;

	        if($r_isok1 != ''){
	            $isroverify = true;
	        }
	    }
	    
	    if($c_setuju and $r_isverify == '1')
        	$isrook1 = false;
			
		if($c_edithapus)
			$isroedit = false;
	}else{
	    $isro = false;
	}
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idpenghapusan', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunit', 'label' => 'Unit', 'type' => 'S', 'option' => $a_unit, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'idlokasi', 'label' => 'Lokasi', 'type' => 'S', 'option' => $a_lokasi, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'default' => $now,'notnull' => true, 'readonly' => $isro);	
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'option' => $a_status, 'readonly' => true, 'issave' => true);

	$a_input[] = array('kolom' => 'idjenispenghapusan', 'label' => 'Jenis Penghapusan', 'type' => 'S', 'option' => $a_jenispenghapusan, 'default' => '301', 'add' => 'style="width:150px"', 'readonly' => $isro);
    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'isok1', 'label' => 'Setuju ?', 'type' => 'R', 'option' => $a_setuju, 'readonly' => $isrook1);
    $a_input[] = array('kolom' => 'memo1', 'label' => 'Memo', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isrook1);
	$a_input[] = array('kolom' => 'isok1user', 'label' => 'Setuju User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'isok1time', 'label' => 'Setuju Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');

	$a_input[] = array('kolom' => 'tglpenghapusan', 'label' => 'Tgl. Penghapusan', 'type' => 'D', 'readonly' => $isrohapus);

	/*$a_input[] = array('kolom' => 'idjenispenghapusan', 'label' => 'Jenis Penghapusan', 'type' => 'S', 'option' => $a_jenispenghapusan, 'default' => '101', 'add' => 'style="width:150px"', 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpenghapusan', 'label' => 'Tgl. Penghapusan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpembukuan', 'label' => 'Tgl. Pembukuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'nobukti', 'label' => 'No. Bukti','maxlength' => 20, 'size' => 20, 'readonly' => $isrokeu);
    $a_input[] = array('kolom' => 'tglbukti', 'label' => 'Tgl. Bukti', 'type' => 'D', 'readonly' => $isrokeu);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'isok1', 'label' => 'Setuju ?', 'type' => 'R', 'option' => array('1' => 'Disetujui', '0' => 'Ditolak'), 'notnull' => true, 'readonly' => $isrook1);
    $a_input[] = array('kolom' => 'memo1', 'label' => 'Memo', 'type' => 'A', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isrook1);
	*/

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
				$p_model::mailHapus($conn, $key);
			}
			
			if($r_isok1 == '1'){
				$ok = true;
			    if(isset($record['tglpenghapusan']) and $a_mdata['tglpenghapusan'] != $record['tglpenghapusan']){
			        if(empty($record['tglpenghapusan']) or $record['tglpenghapusan'] == 'null')
    			        $ok = $p_model::setBatal($conn, $r_key);
			        else
    			        $ok = $p_model::setHapus($conn, $r_key);
			    }
		        
	            if(!$ok){ 
                    $p_posterr = true;
                    $p_postmsg = 'Proses penghapusan aset gagal !';
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
	else if($r_act == 'deletedet' and $c_delete){
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
	}
	else if($r_actdet == 'savedet' and $c_edit){
		//list($post,$record) = uForm::getPostRecord($a_detail,$_POST);
		$record = array();
	    $record['idpenghapusan'] = $r_key;

		$a_seri = $_POST['seri'];
		$conn->BeginTrans();
		if(count($a_seri) > 0){
		    foreach($a_seri as $idseri){
        	    $record['idseri'] = $idseri;
		        list($p_posterr,$p_postmsg) = $p_modeldet::insertCRecord($conn,$a_detail,$record,$r_keydet);
		        if($p_posterr) break;
	        }
		}
		$record['nilaipenghapusan'] = $_POST['nilaihapus'];
		if($p_posterr){
		    $conn->RollbackTrans();
		}else{
		    $conn->CommitTrans();
		    unset($post);
		}
	}
	else if($r_actdet == 'deletedet' and $c_delete){
		list($p_posterr,$p_postmsg) = $p_modeldet::delete($conn,$r_keydet);
	}
	else if($r_act == 'updatehapus' and $c_nilai){
		if ( isset($_POST['iddetpenghapusan']) ) {
			foreach ( $_POST['iddetpenghapusan'] as $key => $val ) {
				list($num,$dec) = explode(',',$_POST['nilaihapus'][$key]);
				$record['nilaipenghapusan'] = str_replace('.','',$num);
				list($p_posterr,$p_postmsg) = $p_modeldet::updatehapus($conn,$_POST['iddetpenghapusan'][$key],$record);
			}
		}
	    if(!$p_posterr) unset($post);
	}
	
	
	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	$i_idunit = Page::getDataInput($row,'idunit');
	$i_idlokasi = Page::getDataInput($row,'idlokasi');
	$i_idjenispenghapusan = Page::getDataInput($row,'idjenispenghapusan');

    $a_pass = array();
    
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        $r_ndet = count($det);

        $a_mdata = mPenghapusan::getMData($conn,$r_key);
		//echo 'idunit - '.$a_mdata['idunit'].' idjenishapus - '.$a_mdata['idjenispenghapusan'].'  jenishapus - '.$r_jenishapus;
	    $r_isverify = $a_mdata['isverify'];	    
		$r_isok1 = $a_mdata['isok1'];
		$r_idunit = $a_mdata['idunit'];
		$r_idlokasi = $a_mdata['idlokasi'];
		$r_jenishapus = $a_mdata['idjenispenghapusan'];

		if($r_ndet > 0){
            $i_idunit = str_replace('&nbsp;','',$a_unit[Page::getDataValue($row,'idunit')]);
            $i_idunit .= '<input type="hidden" id="idunit" name="idunit" value="'.$r_idunit.'">';
			
			$i_idlokasi = str_replace('&nbsp;','',$a_lokasi[Page::getDataValue($row,'idlokasi')]);
            $i_idlokasi .= '<input type="hidden" id="idlokasi" name="idlokasi" value="'.$r_idlokasi.'">';

            $a_pass[] = 'idunit';
			$a_pass[] = 'idlokasi';
		}

    	if($r_isverify == '1'){
			$c_delete = false;			
		}

		if($r_isok1 == '1' and ($r_jenishapus == '302' or $r_jenishapus == '303')){
			$a_btnprint = array();
			$a_btnprint[] = array('id' => 'be_cetaksk', 'label' => 'SK Tim', 'onclick' => 'goCetakSKTim()');
			//$a_btnprint[] = array('id' => 'be_cetakpengumuman', 'label' => 'Pengumuman', 'onclick' => 'goCetakPengumuman()');
			$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');
			$a_btnprint[] = array('id' => 'be_cetakba', 'label' => 'BA', 'onclick' => 'goCetakBA()');
		} else if($r_isok1 == '1') {
			$a_btnprint = array();
			$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');
			$a_btnprint[] = array('id' => 'be_cetakba', 'label' => 'BA', 'onclick' => 'goCetakBA()');
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
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idpenghapusan') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idpenghapusan') ?></td>
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
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idlokasi') ?></td>
		                        <td class="RightColumnBG"><?= $i_idlokasi ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'status') ?></td>
	                        </tr>
	                        <tr valign="top">
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpengajuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpengajuan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
	                        <?  if($r_isverify == '1' or $c_verify){ ?>    
					        <tr>
					            <td colspan="4" class="DataBG">Proses Pengajuan</td>
				            </tr>
							<tr>
								<td class="LeftColumnBG"><?= Page::getDataLabel($row,'idjenispenghapusan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'idjenispenghapusan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isok1') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isok1') ?></td>
							</tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'isverify') ?></td>
		                        <td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'memo1') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'memo1') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td valign="top" class="LeftColumnBG"><?= Page::getDataLabel($row,'verifynote') ?></td>
		                        <td valign="top" class="RightColumnBG"><?= Page::getDataInput($row,'verifynote') ?></td>
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
					            <td colspan="4" class="DataBG">Proses Penghapusan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpenghapusan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpenghapusan') ?></td>
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
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '10' : '8' ?>" class="DataBG">Detail Penghapusan</td>
				            </tr>
					        <tr>
					            <th width="50">No. Seri</th>
					            <th>Barang</th>
						        <th width="80">Merk</th>
					            <th width="150">Spesifikasi</th>
						        <th width="60">ID. Lokasi</th>
						        <th width="100">Pemakai</th>
						        <th width="80">Tgl. Perolehan</th>
								<? if($c_nilai) { ?>
					            <th width="80">Nilai Hapus</th>
								<? } ?>
								<? if($r_isok1 == '1' and ($r_jenishapus == '302' or $r_jenishapus == '303')) { ?>
								<th width="30">Pengumuman</th>
								<? } else { ?>
						        <?	if($r_isverify != '1' or $c_verify) { ?>
						        <th width="30">Aksi</th>
						        <?	} ?>
						        <?	} ?>
					        </tr>
					        <?
					        if(count($det) > 0){
					            foreach($det as $id => $val){
								$seri = Aset::setFormatNoSeri($val['noseri']);
					        ?>
					        <tr valign="top">
					            <td>
									<?= Aset::setFormatNoSeri($val['noseri']) ?>
									<input type="hidden" name="noseri[]" value="<?= CStr::formatNumber($val['noseri']) ?>">
									<input type="hidden" name="iddetpenghapusan[]" value="<?= CStr::formatNumber($val['iddetpenghapusan']) ?>">
								</td>
					            <td><?= $val['barang'] ?></td>
					            <td><?= $val['merk'] ?></td>
					            <td><?= $val['spesifikasi'] ?></td>
					            <td><?= $val['idlokasi'] ?></td>
					            <td><?= $val['pemakai'] ?></td>
					            <td><?= CStr::formatDateInd($val['tglperolehan'],false) ?></td>
								<? if($c_nilai) { ?>
								<td align="right">
									<input type="text" id="nilaihapus" name="nilaihapus[]" value="<?= CStr::formatNumber($val['nilaipenghapusan'],2) ?>" size="10" maxlength="14" class="ControlStyle" onkeydown="return onlyNumber(event,this,true,true)">
								</td>
								<? } ?>
								<? if($r_isok1 == '1' and ($r_jenishapus == '302' or $r_jenishapus == '303')) { ?>
									<td align="center">
										<img title="Pengumuman" src="images/print.png" onclick="goCetakPengumuman('<?= $val['iddetpenghapusan'] ?>')" style="cursor:pointer">
									</td>
								<? } else { ?>
									<?	if ($c_nilai and ($r_isverify != '1' or $c_verify)) { ?>
										<td align="center">
											<img title="Simpan" src="images/disk.png" onclick="goSaveDet('<?= $val['iddetpenghapusan'] ?>')" style="cursor:pointer">
										</td>
									<? } else {?>
										<td align="center">
											<img title="Hapus Detail" src="images/delete.png" onclick="goDeleteDet('<?= $val['iddetpenghapusan'] ?>')" style="cursor:pointer">
										</td>
									<? } ?>
						        <?	} ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '10' : '8' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
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
				<input type="hidden" name="keydet" id="keydet" value="<?= $r_keydet ?>">
				<input type="hidden" name="noseri" id="noseri" value="<?= $seri ?>">
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
	
	loadCBLokasi();
});

$('#idunit').change(function(){
    loadCBLokasi();
});

function loadCBLokasi(){
    $.post(ajaxpage, 
        {f: 'optlokasibrg', idunit: $('#idunit').val(), idlokasi: $('#idlokasi').val()}, 
        function(data){
            $('#idlokasi').html(data);
        }
    );
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

function goSaveDet(keydet){
    var pass = true;
	if(typeof(reqdet) != "undefined") {
		if(!cfHighlight(reqdet))
			pass = false;
	}
	
	if(pass) {
        $("#act").val('updatehapus');
		$("#keydet").val(keydet);
        goSubmit();
    }
}

function goCetakSKTim() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_skpenghapusan") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

function goCetakBASTB() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbhapus") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

function goCetakBA() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_bahapus") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

function goCetakPengumuman() {
	$('#pageform').attr('action','<?= Route::navAddress("rep_pengumuman") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

function goDeleteDet(keydet){
    if(confirm('Apa anda yakin akan menghapus data ini ?')){
        $("#act").val('deletedet');
        $("#keydet").val(keydet);
        goSubmit();
    }
}

</script>
</body>
</html>
