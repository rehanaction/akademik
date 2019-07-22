<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_peminjaman');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pinjam'));
	require_once(Route::getModelPath('pinjamdetail'));
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
	$p_title = 'Data Peminjaman Barang';
	$p_tbwidth = 800;
	$p_aktivitas = 'peminjaman';
	$p_listpage = Route::getListPage();
	
	$p_model = mPinjam;
	$p_modeldet = mPinjamDetail;
    
    //user role
    $r_role = Modul::getRole();
    if(in_array($r_role, Aset::getRoleRM()))
        $c_verify = true;
        
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
	$isrook1 = true;
	$isropinjam = true;
	
	if(!empty($r_key)){
	    $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isok1 = $a_mdata['isok1'];
	    $r_isverify = $a_mdata['isverify'];
	    $r_idunit = $a_mdata['idunit'];
	    $r_tglkembali = $a_mdata['tglkembali'];

	    if($r_isverify != '1'){
	        $isro = false;
	    }
	    
	    if($c_verify){
	        $isro = true;
	        $isroverify = false;
	        $isropinjam = false;
	    }
	    
	    if($c_ok1 and $r_isverify == '1')
        	$isrook1 = false;
	}else{
	    $isro = false;
	}


	$a_unitpeminjam = mCombo::unitSave($conn,false);
	$a_unitasal = mCombo::unitSave($conn,false,true);
	$a_lokasi = mCombo::lokasi($conn);
	$a_verify = array('1' => 'Verified');
	$a_setuju = array('0' => 'Ditolak','1' => 'Disetujui');
	$now = date('Y-m-d');
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idpinjam', 'label' => 'ID.','default' => 'Otomatis', 'readonly' => true);
	$a_input[] = array('kolom' => 'idunitpeminjam', 'label' => 'Unit Peminjam', 'type' => 'S', 'option' => $a_unitpeminjam, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
    $a_input[] = array('kolom' => 'peminjam', 'label' => 'Nama Peminjam', 'size' => 25, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idpeminjam', 'type' => 'H', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl. Pengajuan', 'type' => 'D', 'default' => $now, 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Insert User', 'readonly' => true, 'issave' => true);
	$a_input[] = array('kolom' => 'inserttime', 'label' => 'Insert Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
	$a_input[] = array('kolom' => 'status', 'label' => 'Status', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idunitasal', 'label' => 'Unit Asal', 'type' => 'S', 'option' => $a_unitasal, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);
	$a_input[] = array('kolom' => 'idlokasiasal', 'label' => 'Lokasi Asal', 'type' => 'S', 'option' => $a_lokasi, 'add' => 'style="width:250px"', 'notnull' => true, 'readonly' => $isro);

    $a_input[] = array('kolom' => 'isverify', 'label' => 'Verify ?', 'type' => 'C', 'option' => $a_verify, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifynote', 'label' => 'Verify Note', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isroverify);
    $a_input[] = array('kolom' => 'verifyuser', 'label' => 'Verify User', 'readonly' => true, 'issave' => true);
    $a_input[] = array('kolom' => 'verifytime', 'label' => 'Verify Time', 'readonly' => true, 'issave' => true, 'type' => 'DT');
    $a_input[] = array('kolom' => 'isok1', 'label' => 'Setuju ?', 'type' => 'R', 'option' => $a_setuju, 'readonly' => $isrook1);
    $a_input[] = array('kolom' => 'memo1', 'label' => 'Memo', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255, 'readonly' => $isrook1);

	$a_input[] = array('kolom' => 'tglpinjam', 'label' => 'Tgl. Peminjaman', 'type' => 'D', 'readonly' => $isropinjam);
    $a_input[] = array('kolom' => 'tgltenggat', 'label' => 'Tgl. Tenggat', 'type' => 'D', 'readonly' => $isropinjam);
    $a_input[] = array('kolom' => 'tglkembali', 'label' => 'Tgl. Kembali', 'type' => 'D', 'readonly' => $isropinjam);
	
	//detail
	$a_detail = $p_modeldet::getInputAttr(array('isro' => $isro));
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
		    $record['insertuser'] = Modul::getUserDesc();
			$record['inserttime'] = date('Y-m-d H:i:s');
			$record['status'] = 'A';
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		    $r_key = $conn->Insert_ID();
		}else{
            if(isset($record['isok1']) and $record['isok1'] != $r_isok1){
	            $record['isok1user'] = null;
		        $record['isok1time'] = null;
		        $record['status'] = 'A';

		        if(in_array($record['isok1'],array('0','1'))){
		            $record['isok1user'] = Modul::getUserDesc();
			        $record['isok1time'] = date('Y-m-d H:i:s');
			        if($record['isok1'] == '1'){
			            $record['status'] = 'S';
			            $p_model::setIsPinjam($conn, $r_key, '1');
			        }else if($record['isok1'] == '0'){
			            $record['status'] = 'T';
			            $p_model::setIsPinjam($conn, $r_key, '0');
		            }
		        }
	        }
		    
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
	        
	        if(isset($record['tglkembali']) and $record['tglkembali'] != $r_tglkembali){
	            echo $record['tglkembali'];
	            if($record['tglkembali'] == 'null')
		            $p_model::setIsPinjam($conn, $r_key, '0');
	            else if(!empty($record['tglkembali']))
		            $p_model::setIsPinjam($conn, $r_key, '1');
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
		
		if(empty($r_keydet)){
		    $record['idpinjam'] = $r_key;
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
	
	$i_idunitpeminjam = Page::getDataInput($row,'idunitpeminjam');
	$i_peminjam = Page::getDataInput($row,'peminjam');
	$i_idunitasal = Page::getDataInput($row,'idunitasal');
	$i_isverify = Page::getDataInput($row,'isverify');
	$i_isok1 = Page::getDataInput($row,'isok1');
    
    //ambil detail
	if(!empty($r_key)){
	    $det = $p_modeldet::getRowByIDP($conn, $r_key);
        $r_ndet = count($det);

        $a_mdata = $p_model::getMData($conn,$r_key);
	    $r_isok1 = $a_mdata['isok1'];
	    $r_isverify = $a_mdata['isverify'];
	    $r_idunitasal = $a_mdata['idunitasal'];

        if($r_ndet > 0){
            $i_idunitasal = str_replace('&nbsp;','',$a_unitasal[Page::getDataValue($row,'idunitasal')]);
            $i_idunitasal .= '<input type="hidden" id="idunitasal" name="idunitasal" value="'.$r_idunitasal.'">';
        }
        
	    if($r_isverify == '1'){
	        $i_idunitpeminjam = str_replace('&nbsp;','',$a_unitasal[Page::getDataValue($row,'idunitpeminjam')]);
	        $i_peminjam = Page::getDataValue($row,'peminjam');
            //$i_isverify = '<img src="images/check.png">';
            
        	$a_btnprint = array();
        	$a_btnprint[] = array('id' => 'be_cetakbastb', 'label' => 'BASTB', 'onclick' => 'goCetakBASTB()');
	    }

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
					            <td colspan="4" class="DataBG">Detail Pengajuan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'idpinjam') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'idpinjam') ?></td>
		                        <td class="LeftColumnBG" width="120"><?= Page::getDataLabel($row,'status') ?></td>
		                        <td class="RightColumnBG" width="280"><?= Page::getDataInput($row,'status') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunitpeminjam') ?></td>
		                        <td class="RightColumnBG"><?= $i_idunitpeminjam; //Page::getDataInput($row,'idunitpeminjam') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idunitasal') ?></td>
		                        <td class="RightColumnBG"><?= $i_idunitasal; //Page::getDataInput($row,'idunitasal') ?></td>
	                        </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'peminjam') ?></td>
		                        <td class="RightColumnBG">
		                            <?= $i_peminjam; //Page::getDataInput($row,'peminjam') ?>
		                            <?= Page::getDataInput($row,'idpeminjam') ?>
		                        </td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'insertuser') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'insertuser') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpengajuan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpengajuan') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'inserttime') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'inserttime') ?></td>
	                        </tr>	                        				        
	                        <tr valign="top">
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	                        </tr>
	                        <?  if($r_isverify == '1' or $c_verify){ ?>                     				        
					        <tr>
					            <td colspan="4" class="DataBG">Proses Pengajuan</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isverify') ?></td>
		                        <td class="RightColumnBG"><?= $i_isverify; //Page::getDataInput($row,'isverify') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'isok1') ?></td>
		                        <td class="RightColumnBG"><?= $i_isok1; //Page::getDataInput($row,'isok1') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifynote') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'verifynote') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'memo1') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'memo1') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifyuser') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifyuser') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'verifytime') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'verifytime') ?></td>
	                        </tr>
	                        <?  } ?>
	                        <?  if($r_isok1 == '1' and $c_verify){ ?>
					        <tr>
					            <td colspan="4" class="DataBG">Proses Peminjaman</td>
				            </tr>
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglpinjam') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglpinjam') ?></td>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglkembali') ?></td>
		                        <td class="RightColumnBG"><?= Page::getDataInput($row,'tglkembali') ?></td>
	                        </tr>	                        				        
	                        <tr>
		                        <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tgltenggat') ?></td>
		                        <td class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tgltenggat') ?></td>
	                        </tr>
	                        <?  } ?>
					        <tr>
					            <td colspan="4">&nbsp;</td>
				            </tr>
					    </table>
					    <?  if(!empty($r_key)) { ?>
					    <?  if(!$r_isverify) { ?>
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
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '6' : '5' ?>" class="DataBG">Detail Peminjaman</td>
				            </tr>
					        <tr>
					            <th width="50">No. Seri</th>
					            <th>Barang</th>
					            <th width="150">Spesifikasi</th>
						        <th width="150">Lokasi</th>
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
					            <td><?= $val['noseri'] ?></td>
					            <td><?= $val['barang'] ?></td>
					            <td><?= $val['spesifikasi'] ?></td>
					            <td><?= $val['lokasi'] ?></td>
					            <td><?= CStr::formatDateInd($val['tglperolehan'],false) ?></td>
						        <?	if($r_isverify != '1' or $c_verify) { ?>
						        <td align="center">
							        <img title="Hapus Detail" src="images/edit.png" onclick="goDetailDet('<?= $val['iddetpinjam'] ?>')" style="cursor:pointer">
						        </td>
						        <?	} ?>
					        </tr>
					        <?  }
					        }else{
					        ?>
					        <tr>
					            <td colspan="<?= ($r_isverify != '1' or $c_verify) ? '6' : '5' ?>" align="center"><b>-- Data tidak ditemukan --</b></td>
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

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var detform = "<?= Route::navAddress('pop_peminjamandetail') ?>";
var ajaxpage = "<?= Route::navAddress('ajax') ?>";
var required = "<?= @implode(',',$a_required) ?>";
var acxpegawai = null;

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$('#btnAddDetail').click(function(){
        openDetail($('#key').val(), '');
	});
	
    setAutoPeminjam();
});

// autocomplete
function setAutoPeminjam(){
    $("#peminjam").xautox({strpost: "f=acxpegawai&idunit="+$('#idunitpeminjam').val(), targetid: "idpeminjam"});
}

$('#idunitpeminjam').change(function(){
    $('#peminjam').unbind();
    $('#peminjam').val('');
    $('#idpeminjam').val('');

    setAutoPeminjam();
});

function goDetailDet(pkeydet){
    openDetail($('#key').val(), pkeydet);
}

function openDetail(pkey, pkeydet){
    //alert(pkey);

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
	$('#pageform').attr('action','<?= Route::navAddress("rep_bastbpinjam") ?>');
	$('#pageform').attr('target','_blank');
	goSubmit();
	$('#pageform').attr('action','');
	$('#pageform').attr('target','');
}

</script>
</body>
</html>
