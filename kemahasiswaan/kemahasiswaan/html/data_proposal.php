<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('unit'));
	require_once(Route::getModelPath('proposal'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('organisasi'));
	require_once(Route::getModelPath('periode'));
	require_once(Route::getModelPath('program'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if (isset ($_GET['key']))
	$r_key = CStr::removeSpecial($_GET['key']);

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;

	// properti halaman
	$p_title = 'Data Proposal';
	$p_tbwidth = 600;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	$p_model = mProposal;
	
	$p_cupbyte = 3*1024*1024; // 3 MB
	$p_upbyte = CStr::revertSize(ini_get('upload_max_filesize'));
	
	if($p_cupbyte < $p_upbyte)
		$p_upbyte = $p_cupbyte;
	
	$p_upsize = CStr::formatSize($p_upbyte);
	
	$a_unit = mUnit::getComboUnit($conn);
	$a_organisasi = mOrganisasi::getArrayUnit($conn,Modul::getUnit());
	$a_periode = mPeriode::getArray($conn);
	$a_kegiatan = mKegiatan::getArray($conn);

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;
	$c_anggaran = $a_auth['canother']['A'];
	$c_valid = $a_auth['canother']['V'];

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeorganisasi', 'label' => 'Nama Organisasi','type'=>'S', 'option' => $a_organisasi);
	$a_input[] = array('kolom' => 'namaprogram', 'label' => 'Nama Kegiatan', 'size' => 40);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode','type'=>'S', 'option' => $a_periode);
	$a_input[] = array('kolom' => 'jmlpermohonandana', 'label' => 'Jumlah Permohonan Dana','type'=>'N');
	$a_input[] = array('kolom' => 'jmlpersetujuandana', 'label' => 'Jumlah Persetujuan Dana','type'=>'N');
	$a_input[] = array('kolom' => 'tglpermohonan', 'label' => 'Tanggal Permohonan','type'=>'D');
	$a_input[] = array('kolom' => 'tglpengambilan', 'label' => 'Tanggal Pengambilan Dana','type'=>'D','readonly' => !$c_valid);
	$a_input[] = array('kolom' => 'tempatkegiatan', 'label' => 'Tempat Kegiatan');
	$a_input[] = array('kolom' => 'nosurat', 'label' => 'Nomor Surat');
	$a_input[] = array('kolom' => 'nimpengambil','readonly' => !$c_valid);
	$a_input[] = array('kolom' => 'fileproposal', 'label' => 'File Proposal', 'type' => 'U', 'uptype' => $p_model::uptype, 'size' => 40);
	//$a_input[] = array('kolom' => 'isstatus1', 'label' => 'Validasi DPMU', 'type' => 'C', 'option' => array('-1' => ''),'readonly'=> !Akademik::isDPMU());
	//$a_input[] = array('kolom' => 'tglpersetujuan1', 'label' => 'Tanggal Persetujuan DPMU','type'=>'D','readonly'=> !Akademik::isDPMU());
	$a_input[] = array('kolom' => 'isstatus2', 'label' => 'Validasi BEMU', 'type' => 'C', 'option' => array('-1' => ''),'readonly'=> !Akademik::isBEMU());
	$a_input[] = array('kolom' => 'tglpersetujuan2', 'label' => 'Tanggal Persetujuan BEMU','type'=>'D','readonly'=> !Akademik::isBEMU());
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Validasi Kemahasiswaan', 'type' => 'C', 'option' => array('-1' => ''),'readonly' => !$c_valid);
	$a_input[] = array('kolom' => 'tglpersetujuan3', 'label' => 'Tanggal Persetujuan Kemahasiswaan','type'=>'D','readonly' => !$c_valid);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 5, 'cols' => 40);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		// cek file
		switch($_FILES['fileproposal']['error']) {
			case UPLOAD_ERR_OK:
				// cek tipe data
				$finfo = finfo_open();
				$t_type = finfo_file($finfo,$_FILES['fileproposal']['tmp_name'],FILEINFO_MIME_TYPE);
				finfo_close($finfo);

				if(!in_array($t_type,array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/pdf','image/gif','image/jpeg','image/png'))) {
					$p_posterr = true;
					$p_postmsg = 'Format File Proposal tidak sesuai';
				}

				// cek ukuran data
				if(empty($p_posterr)) {
					$t_size = filesize($_FILES['fileproposal']['tmp_name']);
					if($t_size > $p_upbyte) {
						$p_posterr = true;
						$p_postmsg = 'Ukuran File Proposal melebihi '.$p_upsize;
					}
				}

				// hapus file
				if(!empty($p_posterr))
					@unlink($_FILES['fileproposal']['tmp_name']);

				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$p_posterr = true;
				$p_postmsg = 'Ukuran File Proposal melebihi batas';
				break;
		}

		if(empty($p_posterr)) {
			list($post,$record) = uForm::getPostRecord($a_input,$_POST);
			$conn->BeginTrans();
			if(empty($_REQUEST['isvalid']))
				$record['isvalid'] = 0;
			$record['nimpengaju'] = Modul::getUserName();
			if(empty($r_key))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			else
			{
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);

				//insert ke kegiatan jika valid mhs
				if(!empty($r_key))
				{
					if($_REQUEST['isvalid'] == -1)
					{
						$record['idproposal'] = $r_key;
						//cek ada atau tidak kegiatan
						$cek = mKegiatan::getByProposal($conn,$r_key);
						if(empty($cek))
						{
						//kodeorganisasi,namakegiatan,periode
							list($p_posterr,$p_postmsg) = mKegiatan::insertCRecord($conn,$a_input,$record);
						}
					}
					else if($_REQUEST['isvalid'] == 0)
					{
						$ok = mKegiatan::deleteByProposal($conn,$r_key);
					}
				}
			}
			$conn->CommitTrans(!$p_posterr);
		}

		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);

	}else if($r_act == 'deletefile' and $c_edit){
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'fileproposal');

	}

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);


	$r_pengambil = Page::getDataValue($row,'nimpengambil');


	if(!empty($r_pengambil))
		$r_namapengambil = $r_pengambil.' - '.mMahasiswa::getNama($conn,trim($r_pengambil),false);


	$r_keterangan = Page::getDataValue($row,'keterangan');
	
	//valid tidak bisa dihapus
	$filelpj = Page::getDataValue($row,'fileproposal');
	$ext = end(explode('.',$filelpj));
	$valid = Page::getDataValue($row,'isvalid');
	if(!empty($valid))
		$c_delete = false;
?>
<div id="detailfile" style="display:block"> </div>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<link href="style/modal.css" rel="stylesheet" type="text/css">
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
			<form name="pageform" id="pageform" method="post"  enctype="multipart/form-data">
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
					/*******\**********/

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

						$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<?= Page::getDataTR($row,'nosurat') ?>
							<?= Page::getDataTR($row,'kodeorganisasi') ?>
							<?= Page::getDataTR($row,'periode') ?>
							<?= Page::getDataTR($row,'namaprogram') ?>
							<?= Page::getDataTR($row,'tglpermohonan') ?>
							<?= Page::getDataTR($row,'tempatkegiatan') ?>
							<?php // echo Page::getDataTR($row,'fileproposal') ?>
							<tr>
								<td class="LeftColumnBG" width="120" style="white-space:nowrap">
									<?= Page::getDataLabel($row,'fileproposal') ?>
								</td>
								<?php /* <td class="RightColumnBG">
									<?= Page::getDataInput($row,'fileproposal') ?>
									<div id="edit" style="display:none;font-style:italic;margin-top:5px">
										<div>Type file: image, pdf, atau doc</div>
										<div>Ukuran file: max. <?php echo $p_upsize ?></div>
									</div>
								</td> */ ?>
								<td class="RightColumnBG">
									<span id="show">
										<u class="ULink" onclick="popup('popUpDiv')"><?=Page::getDataValue($row,'fileproposal')?></u>
									<div class="Break"></div>
									<?php
									$cek = Page::getDataValue($row,'fileproposal');
									if(!empty($cek)){
									?>
									<u class="ULink" onclick="goDeleteFile('fileproposal')">Hapus file</u>
									<?php } ?>
									</span>
									<span id="edit" style="display:none">
									<?=Page::getDataInput($row,'fileproposal')?>
									<div style="font-style:italic;margin-top:5px">
										<div>Type file: image, pdf, atau doc</div>
										<div>Ukuran file: max. <?php echo $p_upsize ?></div>
									</div>
									</span>
								</td>
							</tr>
							<?= Page::getDataTR($row,'jmlpermohonandana') ?>
							<?php
								if($c_anggaran){
									echo Page::getDataTR($row,'jmlpersetujuandana');
								}else{
							?>
									<tr>
										<td class="LeftColumnBG">Jumlah Persetujuan Dana </td>
										<td class="RightColumnBG">
											<span id="show"><?=Page::getDataValue($row,'jmlpersetujuandana')?></span>
											<span id="edit" style="display:none"><?=Page::getDataValue($row,'jmlpersetujuandana')?></span>
										</td>
									</tr>
							<?php
								}
								 ?>
								<?
								/* TIDAK ADA PERSETUJUAN DPMU
							<?= Page::getDataTR($row,'isstatus1')  ?>
							<?= Page::getDataTR($row,'tglpersetujuan1')  ?>
							*/
							?>
							<?= Page::getDataTR($row,'isstatus2')  ?>
							<?= Page::getDataTR($row,'tglpersetujuan2')  ?>
							<?= Page::getDataTR($row,'isvalid')  ?>
							<?= Page::getDataTR($row,'tglpersetujuan3')  ?>
							<?= Page::getDataTR($row,'tglpengambilan') ?>
							<tr>
								<td class="LeftColumnBG">Pengambil Dana</td>
								<?php
								if($c_anggaran){
								?>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namapengambil,
										UI::createTextBox('pengambil',$r_namapengambil,'ControlStyle',30,30)) ?>
									<input type="hidden" name="nimpengambil" id="nimpengambil" value="<?=$r_pengambil?>">
								</td>
								<?php
								}else
								{
								echo '<td class="RightColumnBG">'.$r_namapengambil.'</td>';
								}
								?>
							</tr>
							<?= Page::getDataTR($row,'keterangan')  ?>
						</table>
					</div>
				</center>

				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
		</div>
		<div id="popUpDiv" style="position: absolute; top: 97px; left: 301px; z-index: 10000; display:none ">
			<div id="popUpDivInner"></div>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>
<div id="blanket" style="display:none"></div>
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);

	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	// autocomplete
	$("#pengambil").xautox({strpost: "f=acmahasiswa", targetid: "nimpengambil"});
});

function popup(idpop) {

	var pop = $("#"+idpop);

	// pop.offset({ top: e.pageY, left: e.pageX });

	<?php
	$filename = Route::getUploadedFile('proposal',$r_key);
	$getfile = Route::getUploadedFile('proposal/temp/',$r_key).'_'.session_id().'.'.$ext;
	$isext = array('pdf','PDF','odf','ODF','ODS','ods','jpg','JPG','jpeg','JPEG','png','PNG');
	$extImg = array('jpg','JPG','jpeg','JPEG','png','PNG');
	$img = false;

	if(in_array($ext,$isext)){
		copy($filename, $getfile);
		$open = true;
		if(in_array($ext,$extImg))
			$img = true;
	}else{
		$open = false;
	}
	?>
	pop.show();
	<?
	if($open){
		if($img){
	?>
			popImage('<?=$getfile?>');
	<?php
		}else{
	?>
			$('#popUpDivInner').html('<iframe id="frameload" src="<?= Route::navAddress('viewerjs').'/#../kemahasiswaan/'.$getfile ?>" width="700" height="700">');
	<?
		}
	} else { ?>
		goDownload('proposal');
	<? }
	?>

	//$('#popUpDivInner').html('<<?= Route::navAddress('viewerjs') ?>');
	$(document).bind("mouseup",function(e) {
		if(pop.has(e.target).length === 0) {
			pop.hide();
		}
	});

}

function popImage(img)
{
$.ajax({
    url: 'index.php?page=viewimg',
    type: 'POST',
    data: 'src='+img+'&type=proposal&key=<?=$r_key?>', //send some unique piece of data like the ID to retrieve the corresponding user information
    success: function(data){
      //construct the data however, update the HTML of the popup div
	   $('#detailfile').html(data);
 	document.getElementById('overlay').style.display='block';
 	document.getElementById('fade').style.display='block';
    }
  });
}
function goClose()
{
document.getElementById('overlay').style.display='none';
document.getElementById('fade').style.display='none';
}

</script>
</body>
</html>
