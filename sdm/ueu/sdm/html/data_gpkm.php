<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_readlist = true;		
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('pengembangan'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Pengabdian Masyarakat';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPengembangan;
	$p_dbtable = "pe_pkm";
	$where = 'idpkm';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Pegawai yang bisa edit', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglawal', 'label' => 'Tgl. Mulai', 'type' => 'D', 'notnull' => true,'add' => 'onchange="getCekTgl(this);"');
	$a_input[] = array('kolom' => 'tglakhir', 'label' => 'Tgl. Selesai', 'type' => 'D', 'notnull' => true,'add' => 'onchange="getCekTgl(this);"');
	$a_input[] = array('kolom' => 'namakegiatan', 'label' => 'Nama Kegiatan','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255, 'notnull' => true);
	$a_input[] = array('kolom' => 'kodepkm', 'label' => 'Jenis PKM', 'type' => 'S', 'empty' => true, 'notnull' => true, 'option' => $p_model::jenisPKM($conn));
	$a_input[] = array('kolom' => 'tempatkegiatan', 'label' => 'Lokasi','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'issertifikat', 'label' => 'Jadikan Sertifikat?', 'type' => 'C', 'option' => array('Y' => ''), 'add' => 'title="Centang sebagai sertifikat"');
	$a_input[] = array('kolom' => 'lingkup', 'label' => 'Lingkup', 'type' => 'S', 'empty' => true, 'option' => $p_model::lingkup());
	$a_input[] = array('kolom' => 'penyelenggara', 'label' => 'Penyelenggara','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'mandiriteam', 'label' => 'Mandiri/ Team', 'type' => 'R', 'option' => $p_model::mandiriTeam(), 'default' => 'T', 'add' => 'onchange="changeMandiriTeam(this.value)"');
	$a_input[] = array('kolom' => 'kontributorke', 'label' => 'Kontributor Ke', 'type' => 'S', 'empty' => true, 'option' => $p_model::kontributor());	
	$a_input[] = array('kolom' => 'kota', 'label' => 'Kota','maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'negara', 'label' => 'Negara','maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	//$a_input[] = array('kolom' => 'statuskegiatan', 'label' => 'Status', 'type' => 'S', 'empty' => true, 'option' => $p_model::status());
	
	if($c_valid)
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);	
		
	$a_input[] = array('kolom' => 'filepkm', 'label' => 'File PKM', 'type' => 'U', 'uptype' => 'filepkm', 'size' => 40);
	
	$a_kont = $p_model::kontributor();
	$a_status = $p_model::statusTim();
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);		
		$record['idpegawai'] = $r_key;
		
		$a_table = 'pe_pkm|pe_timpkm';
		$a_id = 'idpkm|notimpkm';
		$a_idlain = 'idpkm|refpkm|notimpkm';
		
		$record['tahun'] = substr($record['tglawal'],0,4);
		$record['jmlteam'] = $p_model::jmlTeam($_POST);
		if($record['kontributorke'] != 'null')
			$record['jmlteambagi'] = $p_model::jmlTeamBagi($_POST,$record['kontributorke']);
			
		if(empty($r_subkey)){
			$record['t_insertuser'] = $r_key;
			$record['t_inserttime'] = date('Y-m-d H:i:s');
			$record['t_insertipaddress'] = $_SERVER['REMOTE_ADDR'];
			
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		}else{
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);			
		}
		
		//insert tim	
		if(!$p_posterr)		
			list($p_posterr,$p_postmsg) = $p_model::insertTim($conn,'pe_timpkm',$a_id,$r_subkey,$_POST);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::insertTimLain($conn,$a_table,$a_idlain,$r_subkey,$_POST);
			
		//jika pkm dijadikan sertifikat
		if(!$p_posterr)
			$p_model::setSertifikat($conn,$record,$r_subkey,'pkm');
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
			
		if($ok) 
			unset($post);
		else
			Route::setFlashDataPost($post);
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();
	}
	else if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		
		$a_table = 'pe_pkm|pe_timpkm';
		$a_id = 'idpkm|refpkm';
		list($p_posterr,$p_postmsg) = $p_model::deleteRef($conn,$r_subkey,$a_table,$a_id);
		
		//unset sertifikat
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::unsetSertifikat($conn,$r_subkey,'pkm');
			
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','filepkm');
		
		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			
			Route::navListpage($p_listpage,$r_key);
		}
	}
	else if($r_act == 'deletefile' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,'filepkm',$where);				
		?>
		
		<html>
			<script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
			<script type="text/javascript" src="scripts/jquery.common.js"></script>
			<script type="text/javascript" src="scripts/commonx.js"></script>
			<script type="text/javascript" src="scripts/foreditx.js"></script>
			<script type="text/javascript">
				var xlist = "<?= Route::navAddress(Route::thisPage()) ?>";
				var sent = "key=<?= $r_key ?>&subkey=<?= $r_subkey ?>&err=<?= $p_posterr?>&msg=<?= $p_postmsg?>";
				window.parent.parent.$("#contents").divpost({page: xlist, sent: sent});
			</script>
		</html>
		<?php
		exit();	
	}
	else if($r_act == 'deletedet' and $c_delete) {
		$conn->BeginTrans();
		
		$a_table = 'pe_pkm|pe_timpkm';
		$a_id = 'idpkm|refpkm';
		
		$r_keydet = CStr::removeSpecial($_POST['keydet']);	
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetailTim($conn,$a_table,$a_id,$r_subkey,$r_keydet);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
		
	$p_postmsg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : $p_postmsg;
	$p_posterr = !empty($_REQUEST['err']) ? $_REQUEST['err'] : $p_posterr;
	if($p_posterr)
		$post = Route::getFlashDataPost();
	
	$sql = $p_model::getDataEditPKM($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	if(!empty($r_subkey)){
		$a_id = 'idpkm|notimpkm';
		$arrow = $p_model::getTim($conn,'pe_timpkm',$a_id,$r_subkey);
	}
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
			
		//pengecekan hak akses utk pegawai ybs, bila sudah valid
		if($t_row['id'] == 'isvalid'){
			$isvalid = $t_row['value'];
			if($isvalid == 'Ya' and $r_self){
				$c_edit = false;
				$c_delete = false;
			}
			
			if(!empty($r_subkey)){
				$insertuser = $p_model::insertUser($conn,$p_dbtable,$where,$r_subkey);
				if($insertuser != $r_key){
					$c_edit = false;
					$c_delete = false;
				}
			}
		}
	}
		
	//cek apakah sudah diinputkan ke kum
	$iskum = $p_model::isPKMKUM($conn,$r_key);
	
	if(!empty($r_subkey) and count($iskum)>0){
		if(in_array($r_subkey,$iskum)){
			$c_edit = false;
			$c_delete = false;
		}
	}
		
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="scripts/foreditx.js"></script>
	<style>
		#tbl_tim tr:nth-child(2n+3) {background: #F4F4F4}
		#tbl_tim tr:nth-child(2n+4) {background: #FFFFFF}
	</style>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>" enctype="multipart/form-data">
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
						require_once('inc_databuttonajax.php');
					
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
						<?if(!empty($r_subkey)){?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'insertuser') ?></td>
							<td  class="RightColumnBG" colspan="3"><font color="red"><b><?= Page::getDataInput($row,'insertuser') ?></b></font></td>
						</tr>
						<?}?>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglawal') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'tglawal') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglakhir') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglakhir') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namakegiatan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'namakegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepkm') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'kodepkm') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tempatkegiatan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tempatkegiatan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'issertifikat') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'issertifikat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'lingkup') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'lingkup') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'penyelenggara') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'penyelenggara') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kota') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'kota') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'negara') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'negara') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'keterangan') ?></td>
						</tr>
						<?/*
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statuskegiatan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'statuskegiatan') ?></td>
						</tr>
						*/?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filepkm') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filepkm') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'mandiriteam') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'mandiriteam') ?></td>
						</tr>
						<tr id="tr_tim">
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kontributorke') ?></td>
							<td  class="RightColumnBG" colspan="3">
								<?= Page::getDataInput($row,'kontributorke') ?>&nbsp;
								<span id="show"></span>
								<span id="edit" style="display:none">
									<input type="button" name="badd" id ="badd" value="Tambah Tim" class="ControlStyle" onClick="tambahTim('<?= $r_key ?>','<?= $r_subkey ?>','')" />
								</span>
							</td>
						</tr>
						<tr id="tr_tim">
							<td colspan="4">
								<table id="tbl_tim" width="100%" cellpadding="3" cellspacing="0" class="GridStyle">
									<tr class="DataBG" height="30px">
										<td align="center" colspan="4">Daftar Tim Penelitian</td>
									</tr>
									<tr>
										<th align="center" width="45%">Nama</td>
										<th align="center" width="25%">Kontributor Ke</td>
										<th align="center" width="20%">Jenis</td>
										<th align="center" width="10%">Aksi</td>
									</tr>
									<?
										if(count($arrow)>0){
											foreach($arrow as $rowt){
												list($kont,$nama,$status,$id,$no) = explode('::',$rowt);
									?>
									<tr>
										<td>
											<?= $nama?>
											<input type="hidden" id="kontributorke" name="<?= $status?>[]" value="<?= $rowt?>">
										</td>
										<td align="center"><?= $a_kont[$kont]?></td>
										<td align="center"><?= $a_status[$status]?></td>
										<td align="center">
											<span id="show"></span>
											<span id="edit" style="display:none">
												<?if($c_delete){?>
												<img style="cursor:pointer" onclick="goDeleteDet('<?= $rowt;?>')" src="images/delete.png" title="Hapus Tim">
												<?}?>
											</span>
										</td>
									</tr>
									<?
											}
										}
									?>
								</table>
							</td>
						</tr>							
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>

<iframe name="upload_iframe" style="display:none"></iframe>
	
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";
var detform = "<?= Route::navAddress('pop_timpkm') ?>";

$(document).ready(function() {	
	initEdit(<?= empty($post) ? false : true ?>);
	changeMandiriTeam($("#mandiriteam_T").is(":checked"));
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSave() {
	var pass = true;
	if(typeof(required) != "undefined") {
		var requiredkont = '';
		if($("#mandiriteam_T").is(":checked")){
			requiredkont = ',kontributorke';
		}
		if(!cfHighlight(required+requiredkont))
			pass = false;
	}
	
	if(pass) {
		document.getElementById("pageform").target = "upload_iframe";
		document.getElementById("act").value = "save";
		document.getElementById("pageform").submit();
	}
}

function tambahTim(pkey,psubkey, pkeydet){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey, subkey : psubkey, keydet : pkeydet},
        success: function(data){
            $.facybox(data);
        }
    });
}

function goDeleteDet(keydet){
	var hapus = confirm("Anda yakin untuk menghapus data tim ini ?");
	if (hapus){
		var sent = "key=<?=$r_key?>&subkey=<?= $r_subkey?>&keydet="+keydet+"&act=deletedet";
		goPost(thispage,sent);
	}
}

function changeMandiriTeam(mt){
	if(!mt || mt == 'M'){
		$("[id='tr_tim']").hide();
	}else{
		$("[id='tr_tim']").show();		
	}
}

function deleteBaris(img) {
	$(img).parent().parent().parent().replaceWith("");
}

function getCekTgl(elem){
	if(elem.id=='tglawal')
		tglm=elem.value;
	else
		tglm=$("#tglawal").val();
	
	if(elem.id=='tglakhir')
		tgls=elem.value;
	else
		tgls=$("#tglakhir").val();
	
	tgl = tglm.split('-');
	tglmc = tgl[2]+'-'+tgl[1]+tgl[0];
	tgl = tgls.split('-');
	tglsc = tgl[2]+'-'+tgl[1]+tgl[0];
	
	if(tglm != '' && tgls != ''){
		if(tglmc > tglsc){
			doHighlight(document.getElementById(elem.id));
			alert("Tanggal selesai harus lebih besar dari pada tanggal mulai");
			$('#'+elem.id).val('');
		}
	}
}
</script>
</body>
</html>
