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
	require_once(Route::getModelPath('lpj'));
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
	$p_title = 'Data Pertanggungjawaban';
	$p_tbwidth = 500;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	$p_model = mLpj;
	
	$p_cupbyte = 2*1024*1024; // 2 MB
	$p_upbyte = CStr::revertSize(ini_get('upload_max_filesize'));
	
	if($p_cupbyte < $p_upbyte)
		$p_upbyte = $p_cupbyte;
	
	$p_upsize = CStr::formatSize($p_upbyte);
	
	$a_unit = mUnit::getComboUnit($conn);
	$a_organisasi = mOrganisasi::getArray($conn);
	$a_periode = mPeriode::getArray($conn);
	$a_proposal = mProposal::getArray($conn);

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;
	
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodeorganisasi', 'label' => 'Nama Organisasi','type'=>'S', 'option' => $a_organisasi);
	$a_input[] = array('kolom' => 'periode', 'label' => 'Periode','type'=>'S', 'option' => $a_periode);
	$a_input[] = array('kolom' => 'idproposal', 'label' => 'Kegiatan','type'=>'S', 'option' => $a_proposal);
	$a_input[] = array('kolom' => 'tgllpj', 'label' => 'Tanggal LPJ','type'=>'D');
	$a_input[] = array('kolom' => 'jmltanggungjawab', 'label' => 'Jumlah Pertanggungjawaban','type'=>'N');
	$a_input[] = array('kolom' => 'jmlpemakaianormawa', 'label' => 'Jumlah Pemakaian','type'=>'N');
	$a_input[] = array('kolom' => 'jmlsisa', 'label' => 'Jumlah Sisa','type'=>'N');
	$a_input[] = array('kolom' => 'jmlpermintaan', 'label' => 'Jumlah permintaan','type'=>'N');
	$a_input[] = array('kolom' => 'filelpj', 'label' => 'File Pertanggungjawaban', 'type' => 'U', 'uptype' => $p_model::uptype, 'size' => 40);
	$a_input[] = array('kolom' => 'nrp', 'label' => 'Pelapor LPJ');
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'maxlength' => 100);
	$a_input[] = array('kolom' => 'tempatkegiatan', 'label' => 'Tempat Kegiatan');
	$a_input[] = array('kolom' => 'nosurat', 'label' => 'Nomor Surat');
	$a_input[] = array('kolom' => 'status', 'label' => 'Valid', 'type' => 'C', 'option' => array('-1' => ''));


	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		// cek file
		switch($_FILES['filelpj']['error']) {
			case UPLOAD_ERR_OK:
				// cek tipe data
				$finfo = finfo_open();
				$t_type = finfo_file($finfo,$_FILES['filelpj']['tmp_name'],FILEINFO_MIME_TYPE);
				finfo_close($finfo);

				if(!in_array($t_type,array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/pdf','image/gif','image/jpeg','image/png'))) {
					$p_posterr = true;
					$p_postmsg = 'Format File Pertanggungjawaban tidak sesuai';
				}

				// cek ukuran data
				if(empty($p_posterr)) {
					$t_size = filesize($_FILES['filelpj']['tmp_name']);
					if($t_size > $p_upbyte) {
						$p_posterr = true;
						$p_postmsg = 'Ukuran File Pertanggungjawaban melebihi '.$p_upsize;
					}
				}

				// hapus file
				if(!empty($p_posterr))
					@unlink($_FILES['filelpj']['tmp_name']);

				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$p_posterr = true;
				$p_postmsg = 'Ukuran File Pertanggungjawaban melebihi batas';
				break;
		}

		if(empty($p_posterr)) {
			list($post,$record) = uForm::getPostRecord($a_input,$_POST);

			if(empty($_REQUEST['status']))
				$record['status'] = 0;

			if(empty($r_key)){
				$record['tglmasuk'] = date('Y-m-d');
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			}else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}

		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);
	}else if($r_act == 'deletefile' and $c_edit){
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'filelpj');

	}

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	$r_pelapor = Page::getDataValue($row,'nrp');
	if(!empty($r_pelapor))
		$r_namapelapor = trim($r_pelapor).' - '.mMahasiswa::getNama($conn,trim($r_pelapor),false);

	$r_keterangan = Page::getDataValue($row,'keterangan');

	//valid tidak bisa dihapus
	$filelpj = Page::getDataValue($row,'filelpj');
	$ext = end(explode('.',$filelpj));
	$valid = Page::getDataValue($row,'status');
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
	<script type="text/javascript" src="scripts/forpager.js"></script>

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
							<?= Page::getDataTR($row,'idproposal') ?>
							<?= Page::getDataTR($row,'tgllpj') ?>
							<?= Page::getDataTR($row,'jmltanggungjawab') ?>
							<?= Page::getDataTR($row,'jmlpemakaianormawa') ?>
							<?= Page::getDataTR($row,'jmlsisa') ?>
							<tr>
								<td class="LeftColumnBG" style="white-space:nowrap" width="120"> File Pertanggungjawaban</td>
								<td class="RightColumnBG">
									<span id="show">
										<u class="ULink" onclick="popup('popUpDiv')"><?=Page::getDataValue($row,'filelpj')?></u>
									<div class="Break"></div>
									<?php
									$cek = Page::getDataValue($row,'filelpj');
									if(!empty($cek)){
									?>
									<u class="ULink" onclick="goDeleteFile('filelpj')">Hapus file</u>
									<?php } ?>
									</span>
									<span id="edit" style="display:none">
									<?=Page::getDataInput($row,'filelpj')?>
									<div style="font-style:italic;margin-top:5px">
										<div>Type file: image, pdf, atau doc</div>
										<div>Ukuran file: max. <?php echo $p_upsize ?></div>
									</div>
									</span>
								</td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Pelapor</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namapelapor,
										UI::createTextBox('pelapor',$r_namapelapor,'ControlStyle',30,30)) ?>
									<input type="hidden" name="nrp" id="nrp" value="<?=$r_pelapor?>">
								</td>
							</tr>
							<tr>
								<td class="LeftColumnBG">Keterangan</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_keterangan,
										UI::createTextArea('keterangan',$r_keterangan,'ControlStyle',2,30)) ?>
								</td>
							</tr>
							<?= Page::getDataTR($row,'status')  ?>
						</table>

						<span id="ngawur">

						</span>
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
	$("#pelapor").xautox({strpost: "f=acmahasiswa", targetid: "nrp"});
});

function popup(idpop) {

	var pop = $("#"+idpop);

	// pop.offset({ top: e.pageY, left: e.pageX });

	<?php
	$filename = Route::getUploadedFile('lpj',$r_key);
	$getfile = Route::getUploadedFile('lpj/temp/',$r_key).'_'.session_id().'.'.$ext;
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
		goDownload('lpj');
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
    data: 'src='+img+'&type=lpj&key=<?=$r_key?>', //send some unique piece of data like the ID to retrieve the corresponding user information
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
