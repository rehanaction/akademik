<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_seri');

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = false;
    
	// include
	require_once(Route::getModelPath('seri'));
	require_once(Route::getModelPath('pegawai'));
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
	$p_title = 'Data Seri Barang';
	$p_tbwidth = 700;
	$p_aktivitas = 'seri barang';
	$p_listpage = $_SESSION['SERI'] ? $_SESSION['SERI'] : Route::getListPage();

	$p_model = mSeri;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
    $a_merk = mCombo::merk($conn);
     
	$iskib = false;
    if(!empty($r_key)){
        $a_mdata = $p_model::getMData($conn, $r_key);
        $r_idbarang = $a_mdata['idbarang'];
        $r_noseri = Aset::setFormatNoSeri($a_mdata['noseri']);
        $r_id = $r_idbarang.$r_noseri;
        
	    if(substr($r_idbarang,0,3) == '201'){
			$iskib = true;
	        require_once(Route::getModelPath('kibtanah'));
	        $p_modelkib = mKIBTanah;
        }else if(substr($r_idbarang,0,3) == '302'){
            $iskib = true;
	        require_once(Route::getModelPath('kibkendaraan'));
	        $p_modelkib = mKIBKendaraan;
        }else if(substr($r_idbarang,0,3) == '401'){
			$iskib = true;
	        require_once(Route::getModelPath('kibbangunan'));
	        $p_modelkib = mKIBBangunan;
		}else if(substr($r_idbarang,0,3) == '300'){
			$iskib = true;
	        require_once(Route::getModelPath('kibalatteknis'));
	        $p_modelkib = mKIBAlatTeknis;
		}

    	$p_foto = uForm::getPathImage($conn,$r_id,false);
    }


	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'noseri', 'label' => 'No. Seri', 'readonly' => true);
	$a_input[] = array('kolom' => 'barang', 'label' => 'Barang', 'readonly' => true);
	$a_input[] = array('kolom' => 'unit', 'label' => 'Unit', 'readonly' => true);
	$a_input[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'readonly' => true);
	$a_input[] = array('kolom' => 'namalengkap', 'label' => 'Pemakai', 'size' => 30);
	$a_input[] = array('kolom' => 'idpegawai', 'type' => 'H');

	$a_input[] = array('kolom' => 'tglperolehan', 'label' => 'Tgl. Perolehan', 'type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'merk', 'label' => 'Merk', 'type' => 'S', 'option' => $a_merk, 'empty' => true, 'add' => 'style="width:125px;"');	
	$a_input[] = array('kolom' => 'spesifikasi', 'label' => 'Spesifikasi', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 250);

	$a_input[] = array('kolom' => 'idkondisi', 'label' => 'Kondisi', 'type' => 'S', 'option' => mCombo::kondisi($conn), 'add' => 'style="width:125px;"');	
	$a_input[] = array('kolom' => 'idstatus', 'label' => 'Status', 'type' => 'S', 'option' => mCombo::status($conn), 'add' => 'style="width:125px;"', 'readonly' => true);
	$a_input[] = array('kolom' => 'ishapus', 'label' => 'Hapus', 'type' => 'C', 'option' => array('1' => 'Dihapus'), 'readonly' => true);
	$a_input[] = array('kolom' => 'ispinjam', 'label' => 'Pinjam', 'type' => 'C', 'option' => array('1' => 'Dipinjam'), 'readonly' => true);
	$a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 250);
	//$a_input[] = array('kolom' => 'doc', 'label' => 'Doc', 'readonly' => true);
	//$a_input[] = array('kolom' => 'shet', 'label' => 'Shet', 'readonly' => true);


	if($iskib){
		$a_inputkib = array();
		$a_inputkib = $p_modelkib::getInputAttr();
	}
/*
	//Data Hist Depresiasi
	$t_detail = array();
	$t_detail[] = array('kolom' => 'periode', 'label' => 'Periode', 'maxlength' => 6, 'size' => 6, 'align' => 'center');
	$t_detail[] = array('kolom' => 'idjenispenyusutan', 'label' => 'Jenis Penyusutan', 'type' => 'S', 'option' => mCombo::jenispenyusutan($conn), 'add' => 'style="width:175px;"');
	$t_detail[] = array('kolom' => 'nilaisusut', 'label' => 'Nilai Susut', 'type' => 'N,2', 'maxlength' => 14, 'size' => 10, 'align' => 'right');
	$t_detail[] = array('kolom' => 'nilaiaset', 'label' => 'Nilai Aset', 'type' => 'N,2', 'maxlength' => 14, 'size' => 10, 'align' => 'right');
	
	$a_detail['depresiasi'] = array('key' => $p_model::getDetailInfo('depresiasi','key'), 'data' => $t_detail);
*/
	//Data Hist Perawatan
	$t_detail = array();
	$t_detail[] = array('kolom' => 'tglrawat', 'label' => 'Tgl. Perawatan');
	$t_detail[] = array('kolom' => 'jenisrawat', 'label' => 'Jenis Perawatan');
	$t_detail[] = array('kolom' => 'catatan', 'label' => 'Catatan');
	
	$a_detail['perawatan'] = array('key' => $p_model::getDetailInfo('perawatan','key'), 'data' => $t_detail);
	
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_actdet = $_POST['actdet'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else{
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
			if(!$p_posterr and $iskib){
        		list($post,$record) = uForm::getPostRecord($a_inputkib,$_POST);

			    if($p_modelkib::isExist($conn,$r_key))
			        list($p_posterr,$p_postmsgkib) = $p_modelkib::updateCRecord($conn,$a_inputkib,$record,$r_key);
		        else{
		            $record['idseri'] = $r_key;
			        list($p_posterr,$p_postmsgkib) = $p_modelkib::insertCRecord($conn,$a_inputkib,$record,$r_key);
		        }
			}
		}
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'savefoto' and $c_edit) {	
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,640,480);

			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$p_posterr = 'Upload gagal, '.$msg;
		}
		else
			$p_posterr = Route::uploadErrorMsg($_FILES['foto']['error']);
	}
	else if($r_act == 'hapusfoto' and $c_edit) {
		@unlink($p_foto);
	}

	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
	
	if(!empty($r_key)){
	    //$a_mdata = $p_model::getMData($conn, $r_key);
    	$r_idbarang = $a_mdata['idbarang'];
        
	    $isdep = true;
		$ishist = true;
		//$rowh = $p_model::getHistDepresiasi($conn,$r_key,'depresiasi',$post);
		//$rowp = $p_model::getHistPerawatan($conn,$r_key,'perawatan',$post);
		
		$rsr = $p_model::getPerawatan($conn, $r_key);

	    $rowk = array();
	    if(substr($r_idbarang,0,3) == '201'){
			$iskib = true;
			$r_kibnm = 'Tanah';
            $rowk = mKIBTanah::getDataEdit($conn,$a_inputkib,$r_key,$post);
        }else if(substr($r_idbarang,0,3) == '302'){
            $iskib = true;
            $r_kibnm = 'Kendaraan';
            $rowk = mKIBKendaraan::getDataEdit($conn,$a_inputkib,$r_key,$post);
        }else if(substr($r_idbarang,0,3) == '401'){
			$iskib = true;
			$r_kibnm = 'Gedung';
			$rowk = mKIBBangunan::getDataEdit($conn,$a_inputkib,$r_key,$post);
		}else if(substr($r_idbarang,0,3) == '300'){
			$iskib = true;
			$r_kibnm = 'Alat Teknis';
			$rowk = mKIBAlatTeknis::getDataEdit($conn,$a_inputkib,$r_key,$post);
		}
		
		$a_btnprint = array();
    	$a_btnprint[] = array('id' => 'be_cetaklabel', 'label' => 'Label', 'onclick' => 'goCetakLabel()');
    }
?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
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
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
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
				</center>
				<br>
				<center>
				    <div class="tabs" style="width:<?= $p_tbwidth ?>px">
					    <ul>
						    <li><a id="tablink" href="javascript:void(0)">Detail</a></li>
						    <?  if($iskib) { ?>
						    <li><a id="tablink" href="javascript:void(0)">KIB <?= $r_kibnm ?></a></li>
						    <?	}  ?>
    					    <?/*	if($isdep) { ?>					
						    <li><a id="tablink" href="javascript:void(0)">Depresiasi</a></li>
						    <?	}  */?>
    					    <?	if($ishist) { ?>
						    <li><a id="tablink" href="javascript:void(0)">History Perawatan</a></li>
						    <?	}  ?>
					    </ul>
					    <div id="items">
    					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
					            <tr>
					                <td colspan="2" class="DataBG">Detail Seri</td>
				                </tr>
	                            <tr>
		                            <td class="LeftColumnBG" width="150"><?= Page::getDataLabel($row,'noseri') ?></td>
		                            <td class="RightColumnBG"><?= Aset::setFormatNoSeri(Page::getDataValue($row,'noseri')) ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'barang') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'barang') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'tglperolehan') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'tglperolehan') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'unit') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'unit') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'lokasi') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'lokasi') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'namalengkap') ?></td>
		                            <td class="RightColumnBG">
		                                <?= Page::getDataInput($row,'namalengkap') ?>
		                                <?= Page::getDataInput($row,'idpegawai') ?>
		                            </td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'merk') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'merk') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'spesifikasi') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'spesifikasi') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idkondisi') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'idkondisi') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'idstatus') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'idstatus') ?></td>
	                            </tr>
	                            <? /*
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'ishapus') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'ishapus') ?></td>
	                            </tr>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'ispinjam') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'ispinjam') ?></td>
	                            </tr>
	                            */?>
	                            <tr>
		                            <td class="LeftColumnBG"><?= Page::getDataLabel($row,'catatan') ?></td>
		                            <td class="RightColumnBG"><?= Page::getDataInput($row,'catatan') ?></td>
	                            </tr>
	                            <tr>
		                            <td colspan="2" align="center">
		                                <?= uForm::getImage($conn,$r_id); ?>
		                                <br><input type="file" name="foto" id="foto" class="ControlStyle" size="35">&nbsp;&nbsp;
		                                <input id="btnuploadfoto" type="button" class="ControlStyle" value="Upload Foto">&nbsp;&nbsp;
		                                <input id="btnhapusfoto" type="button" class="ControlStyle" value="Hapus Foto">&nbsp;&nbsp;
		                            </td>
	                            </tr>
					        </table>
					    </div>
					    
					    <? 	if($iskib) { ?>					
					    <div id="items">
    					    <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						        <?	
					                foreach($rowk as $t_row) {
						                if($t_row['notnull'])
							                $a_required[] = $t_row['id'];
						                //if($t_row['hidden'])
				                ?>
					                <tr <?= $t_row['hidden'] ? 'style="display:none"' : '' ?> >
						                <td class="LeftColumnBG" width="150" style="white-space:nowrap">
							                <?= $t_row['label'] ?>
							                <?= $t_row['notnull'] ? '<span id="edit" style="display:none">*</span>' : '' ?>
						                </td>
						                <td class="RightColumnBG">
							                <span id="show"><?= $t_row['value'] ?></span>
							                <span id="edit" style="display:none"><?= $t_row['input'] ?></span>
						                </td>
					                </tr>
				                <?	} ?>
					        </table>
					    </div>
					    <? }  ?>

					    <?/*	if($isdep) { ?>					
					    <div id="items">
        					<?= Page::getDetailTable($rowh,$a_detail,'depresiasi','History Depresiasi', false, false) ?>
					    </div>
					    <? 	} */?>

					    <?	if($ishist) { ?>
					    <div id="items">
						    <?//= Page::getDetailTable($rowp,$a_detail,'perawatan','History Perawatan', true, false) ?>
					        <table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
				                <tr>
					                <td colspan="6" class="DataBG">History Perawatan</td>
				                </tr>
					            <tr>
					                <th width="30">No. </th>
					                <th width="80">Tgl. Perawatan</th>
					                <th width="120">Jenis Perawatan</th>
					                <th>Keluhan</th>
					                <th width="150">Nama Supplier</th>
					                <th width="100">Biaya</th>
					            </tr>
					            <?  $i = 0;
					                while($rowr = $rsr->FetchRow()){
						                if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					            ?>
					            <tr valign="top" class="<?= $rowstyle ?>">
		                            <td><?= $i ?></td>
					                <td><?= CStr::formatDateInd($rowr['tglrawat'],false) ?></td>
					                <td><?= $rowr['jenisrawat'] ?></td>
					                <td><?= $rowr['keluhan'] ?></td>
					                <td><?= $rowr['namasupplier'] ?></td>
					                <td><?= CStr::formatNumber($rowr['biaya']) ?></td>
					            </tr>
				                <?
					                }
					                if($i == 0){
					            ?>
				                <tr>
				                    <td colspan="6" align="center">Data Kosong</td>
				                </tr>
					            <?  } ?>
						    </table>
					    </div>
					    <?	} ?>
				    </div>
				    
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				
				<input type="hidden" name="from" id="from" value="seri">
				<input type="hidden" name="startno" id="startno">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	// autocomplete
	$("#namalengkap").xautox({strpost: "f=acxpegawai", targetid: "idpegawai"});

	// autocomplete
	$("#sopir").xautox({strpost: "f=acxpegawai", targetid: "idsopir"});

    $('#btnuploadfoto').click(function(){
        $('#act').val('savefoto');
        goSubmit();
    });

    $('#btnhapusfoto').click(function(){
        $('#act').val('hapusfoto');
        goSubmit();
    });

});

function goCetakLabel() {
    var startno = prompt("Print label dimulai label ke ?","1");
    if(startno != null && startno != ""){
	    $('#startno').val(startno);

	    $('#pageform').attr('action','<?= Route::navAddress("set_label") ?>');
	    $('#pageform').attr('target','_blank');
	    goSubmit();
	    $('#pageform').attr('action','');
	    $('#pageform').attr('target','');
    }
}

</script>
</body>
</html>
