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
	$p_title = 'Data Penelitian';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mPengembangan;
	$p_dbtable = "pe_penelitian";
	$where = 'idpenelitian';
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'insertuser', 'label' => 'Pegawai yang bisa edit', 'readonly' => true);
	$a_input[] = array('kolom' => 'tglmulai', 'label' => 'Tgl. Mulai', 'type' => 'D', 'notnull' => true,'add' => 'onchange="getDurasi(this);"');
	$a_input[] = array('kolom' => 'tglselesai', 'label' => 'Tgl. Selesai', 'type' => 'D', 'notnull' => true,'add' => 'onchange="getDurasi(this);"');
	$a_input[] = array('kolom' => 'jangkawaktu', 'label' => 'Jangka Waktu', 'maxlength' => 3, 'size' => 3, 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'alokasiwaktu', 'label' => 'Alokasi Waktu', 'maxlength' => 3, 'size' => 3, 'type' => 'N');
	$a_input[] = array('kolom' => 'judulpenelitian', 'label' => 'Judul Penelitian','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255, 'notnull' => true);
	$a_input[] = array('kolom' => 'issertifikat', 'label' => 'Jadikan Sertifikat?', 'type' => 'C', 'option' => array('Y' => ''), 'add' => 'title="Centang sebagai sertifikat"');
	$a_input[] = array('kolom' => 'lokasipenelitian', 'label' => 'Lokasi','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'kodeoutput', 'label' => 'Output Penelitian', 'type' => 'S', 'empty' => true, 'notnull' => true, 'option' => $p_model::OutputPenelitian($conn));
	$a_input[] = array('kolom' => 'lingkuppenelitian', 'label' => 'Lingkup', 'type' => 'S', 'empty' => true, 'option' => $p_model::lingkup());
	$a_input[] = array('kolom' => 'mandiriteam', 'label' => 'Mandiri/ Team', 'type' => 'R', 'option' => $p_model::mandiriTeam(), 'default' => 'T', 'add' => 'onchange="changeMandiriTeam(this.value)"');
	$a_input[] = array('kolom' => 'kontributorke', 'label' => 'Kontributor Ke', 'type' => 'S', 'empty' => true, 'option' => $p_model::kontributor());
	
	$a_input[] = array('kolom' => 'judulpublikasi', 'label' => 'Judul Publikasi','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 1000);
	$a_input[] = array('kolom' => 'katakunci', 'label' => 'Kata Kunci','maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'tglterbit', 'label' => 'Tgl. Terbit', 'type' => 'D');
	$a_input[] = array('kolom' => 'tingkatpublikasi', 'label' => 'Tingkat', 'type' => 'S', 'empty' => true, 'option' => $p_model::lingkup());
	$a_input[] = array('kolom' => 'issn', 'label' => 'ISSN','maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'isbn', 'label' => 'ISBN','maxlength' => 50, 'size' => 30);
	
	$a_input[] = array('kolom' => 'danapenelitian', 'label' => 'Dana Universitas', 'maxlength' => 12, 'size' => 20, 'type' => 'N');
	$a_input[] = array('kolom' => 'danapemerintah', 'label' => 'Dana Pemerintah', 'maxlength' => 12, 'size' => 20, 'type' => 'N');
	$a_input[] = array('kolom' => 'danainternal', 'label' => 'Dana Mandiri', 'maxlength' => 12, 'size' => 20, 'type' => 'N');
	$a_input[] = array('kolom' => 'danaeksternal', 'label' => 'Dana Eksternal', 'maxlength' => 12, 'size' => 20, 'type' => 'N');
	$a_input[] = array('kolom' => 'namainstansi', 'label' => 'Nama Instansi', 'maxlength' => 100, 'size' => 50);
	$a_input[] = array('kolom' => 'alamatinstansi', 'label' => 'Alamat', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'contactperson', 'label' => 'Contact Person', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'telpinstansi', 'label' => 'Telp', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'faxinstansi', 'label' => 'Fax', 'maxlength' => 30, 'size' => 30);
	$a_input[] = array('kolom' => 'emailinstansi', 'label' => 'Email', 'maxlength' => 100, 'size' => 50);
	
	$a_input[] = array('kolom' => 'istugaskhusus', 'label' => 'Tugas Khusus ?', 'type' => 'S', 'empty' => true, 'option' => $p_model::isTugasKhusus());
	$a_input[] = array('kolom' => 'statuskegiatan', 'label' => 'Status', 'type' => 'S', 'empty' => true, 'option' => $p_model::status());
	$a_input[] = array('kolom' => 'tglditerima', 'label' => 'Tgl. Diterima', 'type' => 'D');
	$a_input[] = array('kolom' => 'nosurat', 'label' => 'No. Surat', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglsurat', 'label' => 'Tgl. Surat', 'type' => 'D');
	
	if($c_valid)
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid());
	else
		$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'R', 'option' => SDM::getValid(), 'readonly' => true);	
		
	$a_input[] = array('kolom' => 'fileproposal', 'label' => 'File Proposal', 'type' => 'U', 'uptype' => 'fileproposal', 'size' => 40);
	$a_input[] = array('kolom' => 'filepenelitian', 'label' => 'File Penelitian', 'type' => 'U', 'uptype' => 'filepenelitian', 'size' => 40);
	$a_input[] = array('kolom' => 'filepublikasi', 'label' => 'File Publikasi', 'type' => 'U', 'uptype' => 'filepublikasi', 'size' => 40);
	
	$a_kont = $p_model::kontributor();
	$a_status = $p_model::statusTim();
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);		
		$record['idpegawai'] = $r_key;
		
		$a_table = 'pe_penelitian|pe_timpenelitian';
		$a_id = 'idpenelitian|notimpenelitian';
		$a_idlain = 'idpenelitian|refpenelitian|notimpenelitian';
		
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
			list($p_posterr,$p_postmsg) = $p_model::insertTim($conn,'pe_timpenelitian',$a_id,$r_subkey,$_POST);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::insertTimLain($conn,$a_table,$a_idlain,$r_subkey,$_POST);
			
		//jika penelitian dijadikan sertifikat
		if(!$p_posterr)
			$p_model::setSertifikat($conn,$record,$r_subkey,'penelitian');
		
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
		
		$a_table = 'pe_penelitian|pe_timpenelitian';
		$a_id = 'idpenelitian|refpenelitian';
		list($p_posterr,$p_postmsg) = $p_model::deleteRef($conn,$r_subkey,$a_table,$a_id);
		
		//unset sertifikat
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::unsetSertifikat($conn,$r_subkey,'penelitian');
			
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where,'','fileproposal,filepenelitian,filepublikasi');
		
		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			
			Route::navListpage($p_listpage,$r_key);
		}
	}
	else if($r_act == 'deletefile' and $c_delete) {
		$r_file = CStr::removeSpecial($_POST['file']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_subkey,$p_dbtable,$r_file,$where);				
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
		
		$a_table = 'pe_penelitian|pe_timpenelitian';
		$a_id = 'idpenelitian|refpenelitian';
		
		$r_keydet = CStr::removeSpecial($_POST['keydet']);	
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetailTim($conn,$a_table,$a_id,$r_subkey,$r_keydet);
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
	}
		
	$p_postmsg = !empty($_REQUEST['msg']) ? $_REQUEST['msg'] : $p_postmsg;
	$p_posterr = !empty($_REQUEST['err']) ? $_REQUEST['err'] : $p_posterr;
	if($p_posterr)
		$post = Route::getFlashDataPost();
	
	$sql = $p_model::getDataEditPenelitian($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	if(!empty($r_subkey)){
		$a_id = 'idpenelitian|notimpenelitian';
		$arrow = $p_model::getTim($conn,'pe_timpenelitian',$a_id,$r_subkey);
	}
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];

		if($t_row['id'] == 'fileproposal')
			$fileproposal = $t_row['realvalue'];

		if($t_row['id'] == 'filepenelitian')
			$filepenelitian = $t_row['realvalue'];

		if($t_row['id'] == 'filepublikasi')
			$filepublikasi = $t_row['realvalue'];
			
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
	$iskum = $p_model::isPenelitianKUM($conn,$r_key);
	$iskum = 0;//sementara

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
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
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
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglmulai') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'tglmulai') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglselesai') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglselesai') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jangkawaktu') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'jangkawaktu') ?>&nbsp;bulan</td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alokasiwaktu') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'alokasiwaktu') ?>&nbsp;bulan</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'judulpenelitian') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'judulpenelitian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'lokasipenelitian') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'lokasipenelitian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'issertifikat') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'issertifikat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeoutput') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'kodeoutput') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'lingkuppenelitian') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'lingkuppenelitian') ?></td>
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
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a href="javascript:void(0)">Publikasi</a></li>
						<li><a href="javascript:void(0)">Pendanaan</a></li>
						<li><a href="javascript:void(0)">Upload Berkas</a></li>
					</ul>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'judulpublikasi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'judulpublikasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'katakunci') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'katakunci') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglterbit') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tglterbit') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tingkatpublikasi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'tingkatpublikasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'issn') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'issn') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isbn') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isbn') ?></td>
						</tr>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'danapenelitian') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'danapenelitian') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'danapemerintah') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'danapemerintah') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'danainternal') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'danainternal') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'danaeksternal') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'danaeksternal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namainstansi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'namainstansi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alamatinstansi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'alamatinstansi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'contactperson') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'contactperson') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'telpinstansi') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'telpinstansi') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'faxinstansi') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'faxinstansi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'emailinstansi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'emailinstansi') ?></td>
						</tr>
					</table>
					</div>
					
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'istugaskhusus') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'istugaskhusus') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'statuskegiatan') ?></td>
							<td  class="RightColumnBG" width="40%"><?= Page::getDataInput($row,'statuskegiatan') ?></td>
							<td class="LeftColumnBG" width="20%" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglditerima') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglditerima') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nosurat') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nosurat') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglsurat') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglsurat') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'isvalid') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'fileproposal') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'fileproposal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filepenelitian') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filepenelitian') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'filepublikasi') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'filepublikasi') ?></td>
						</tr>
					</table>
					</div>
				</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<input type="hidden" name="file" id="file">
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
var detform = "<?= Route::navAddress('pop_timpenelitian') ?>";

$(document).ready(function() {	
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
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

		if(!cfHighlight(required+requiredkont)){
			pass = false;
		}
		else if($("#danapenelitian").val() == '' && $("#danainternal").val() == '' && $("#danaeksternal").val() == '' && $("#danapemerintah").val() == ''){
			doHighlight(document.getElementById('danapenelitian'));
			doHighlight(document.getElementById('danainternal'));
			doHighlight(document.getElementById('danaeksternal'));
			doHighlight(document.getElementById('danapemerintah'));
			chooseTab(1);
			alert("Mohon diisikan salah satu pendanaan penelitian");
			pass = false;
		}
		else if($("#fileproposal").val() == '' && $("#filepenelitian").val() == '' && $("#filepublikasi").val() == '' && "<?= $fileproposal?>" == "" && "<?= $filepenelitian?>" == "" && "<?= $filepublikasi;?>" == ""){			
			doHighlight(document.getElementById('fileproposal'));
			doHighlight(document.getElementById('filepenelitian'));
			doHighlight(document.getElementById('filepublikasi'));
			chooseTab(2);
			alert("Mohon upload salah satu file berkas penelitian");
			pass = false;
		}
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

function getDurasi(elem){
	if(elem.id=='tglmulai')
		tglm=elem.value;
	else
		tglm=$("#tglmulai").val();
	
	if(elem.id=='tglselesai')
		tgls=elem.value;
	else
		tgls=$("#tglselesai").val();
	
	tgl = tglm.split('-');
	tglmc = tgl[2]+'-'+tgl[1]+tgl[0];
	tgl = tgls.split('-');
	tglsc = tgl[2]+'-'+tgl[1]+tgl[0];
	
	if(tglm != '' && tgls != ''){
		if(tglmc <= tglsc){
			var posted = "f=gdurasi&q[]="+tglm+"&q[]="+tgls;
			$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
				$("#jangkawaktu").val(text);
			});
		}else{
			doHighlight(document.getElementById(elem.id));
			alert("Tanggal selesai harus lebih besar dari pada tanggal mulai");
			$('#'+elem.id).val('');
		}
	}
}
</script>
</body>
</html>
