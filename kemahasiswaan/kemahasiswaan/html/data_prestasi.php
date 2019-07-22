<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('prestasi'));
	require_once(Route::getModelPath('jenisprestasi'));
	require_once(Route::getModelPath('jenispeserta'));
	require_once(Route::getModelPath('tingkatprestasi'));
	require_once(Route::getModelPath('kategoriprestasi'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('poinmhs'));
	require_once(Route::getModelPath('poinprestasi'));
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
	$p_title = 'Data Prestasi';
	$p_tbwidth = 600;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();
	$p_model = mPrestasi;
	
	$p_cupbyte = 2*1024*1024; // 2 MB
	$p_upbyte = CStr::revertSize(ini_get('upload_max_filesize'));
	
	if($p_cupbyte < $p_upbyte)
		$p_upbyte = $p_cupbyte;
	
	$p_upsize = CStr::formatSize($p_upbyte);

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;
	$c_validasi = $a_auth['canother']['V'];

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'Mahasiswa');
	$a_input[] = array('kolom' => 'kodejenisprestasi', 'label' => 'Jenis Prestasi', 'type' => 'S', 'option' => array('' => '') + mJenisprestasi::getArray($conn));
	$a_input[] = array('kolom' => 'kodetingkatprestasi', 'label' => 'Tingkat Prestasi', 'type' => 'S', 'option' => array('null' => '') + mTingkatprestasi::getArray($conn));
	$a_input[] = array('kolom' => 'kodekategoriprestasi', 'label' => 'Juara', 'type' => 'S', 'option' => array('' => '') + mKategoriprestasi::getArray($conn));
	$a_input[] = array('kolom' => 'kodejenispeserta', 'label' => 'Peserta', 'type' => 'S', 'option' => array('' => '') + mJenispeserta::getArray($conn));
	$a_input[] = array('kolom' => 'namaprestasi', 'label' => 'Prestasi', 'type' => 'A', 'cols' => 45);
	$a_input[] = array('kolom' => 'namaprestasien', 'label' => 'Prestasi (EN)', 'type' => 'A', 'cols' => 45);
	$a_input[] = array('kolom' => 'lokasi', 'label' => 'Lokasi', 'size' => 50, 'maxlength' => 50);
	$a_input[] = array('kolom' => 'penyelenggara', 'label' => 'Penyelenggara', 'size' => 50, 'maxlength' => 100);
	$a_input[] = array('kolom' => 'tglprestasi', 'label' => 'Tanggal Awal Prestasi','type' => 'D','add'=>'onchange="setHari1(this.value)"');
	$a_input[] = array('kolom' => 'fileprestasi', 'label' => 'File Prestasi', 'type' => 'U', 'uptype' => 'prestasi', 'size' => 40);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Validasi', 'type' => 'C', 'option' => array('-1' => ''), 'readonly' => !$c_validasi);
	$a_input[] = array('kolom' => 'istampil', 'label' => 'Tampil di SKPI', 'type' => 'C', 'option' => array('-1' => ''));
	$a_input[] = array('kolom' => 'tglprestasiakhir', 'label' => 'Akhir Tanggal Prestasi','type' => 'D');

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		// cek file
		switch($_FILES['fileprestasi']['error']) {
			case UPLOAD_ERR_OK:
				// cek tipe data
				$finfo = finfo_open();
				$t_type = finfo_file($finfo,$_FILES['fileprestasi']['tmp_name'],FILEINFO_MIME_TYPE);
				finfo_close($finfo);

				if(!in_array($t_type,array('application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document','application/pdf','image/jpeg','image/png','image/gif'))) {
					$p_posterr = true;
					$p_postmsg = 'Format File Prestasi tidak sesuai';
				}

				// cek ukuran data
				if(empty($p_posterr)) {
					$t_size = filesize($_FILES['fileprestasi']['tmp_name']);
					if($t_size > $p_upbyte) {
						$p_posterr = true;
						$p_postmsg = 'Ukuran File Proposal melebihi '.$p_upsize;
					}
				}

				// hapus file
				if(!empty($p_posterr))
					@unlink($_FILES['fileprestasi']['tmp_name']);

				break;
			case UPLOAD_ERR_INI_SIZE:
			case UPLOAD_ERR_FORM_SIZE:
				$p_posterr = true;
				$p_postmsg = 'Ukuran File Prestasi melebihi batas';
				break;
		}

		if(empty($p_posterr)) {
			list($post,$record) = uForm::getPostRecord($a_input,$_POST);
			$conn->BeginTrans();
			if(!empty($_POST['isvalid'])){
				$record['nipvalid'] = Modul::getUserName();
			}

			$inputpoin = false;
			if(($_POST['isvalid'] != $_POST['valid']) or (empty($r_key) and !empty($_POST['isvalid'])))
				$inputpoin = true;
			
			$record['istampil'] = (int)$_POST['istampil'];

			if(empty($r_key))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
			else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);

			//input poin
			if(empty($p_posterr) and $inputpoin) {
				//check poin mhs
				$i_poinmhs = mPoinmhs::getData($conn,$record['nim']);

				//cari poin prestasi
				$poin = mPoinprestasi::getPoin($conn,$record['kodejenisprestasi'].'|'.$record['kodetingkatprestasi'].'|'.$record['kodekategoriprestasi'].'|'.$record['kodejenispeserta']);
				if(!empty($poin)){
					$recordp = array();
					$recordp['nim'] = $record['nim'];
					if(!empty($_POST['isvalid']) and empty($_POST['valid']))
						$recordp['poinprestasi'] = $i_poinmhs['poinprestasi']+$poin;
					else if( empty($_POST['isvalid']))
						$recordp['poinprestasi'] = $i_poinmhs['poinprestasi']-$poin;

					if(empty($i_poinmhs)){
						list($p_posterr,$p_postmsg) = mPoinmhs::insertRecord($conn,$recordp);
					}else{
						list($p_posterr,$p_postmsg) = mPoinmhs::updateRecord($conn,$recordp,$record['nim']);
					}
				}else{
					list($p_posterr,$p_postmsg) = array(true,'Setting poin prestasi belum dilakukan');
				}
			}

			$conn->CommitTrans(Query::isOK($p_posterr));
		}

		if(empty($p_posterr)) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);
	}else if($r_act == 'deletefile' and $c_edit)
		list($p_posterr,$p_postmsg) = $p_model::deleteFile($conn,$r_key,'file'.'prestasi');

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	if(Akademik::isMhs())
		$r_mahasiswa = Modul::getUserName();
	else {
		$r_mahasiswa = Page::getDataValue($row,'nim');
	}

	if(!empty($r_mahasiswa))
		$r_namamahasiswa = $r_mahasiswa.' - '.$p_model::getNamaMahasiswa($conn,$r_mahasiswa);

	$tglprestasi = Page::getDataValue($row,'tglprestasi');
	$tglprestasiakhir = Page::getDataValue($row,'tglprestasiakhir');
	
	$fileprestasi = Page::getDataValue($row,'fileprestasi');
	$ext = end(explode('.',$fileprestasi));
	$poin_datavalid = Page::getDataValue($row,'isvalid');
	if(!empty($poin_datavalid)){
		$poinkodejenisprestasi = Page::getDataValue($row,'kodejenisprestasi');
		$poinkodetingkatprestasi = Page::getDataValue($row,'kodetingkatprestasi');
		$poinkodekategoriprestasi = Page::getDataValue($row,'kodekategoriprestasi');
		$poinkodejenispeserta = Page::getDataValue($row,'kodejenispeserta');
		//cari poin prestasi
		$poinvalid = (int)mPoinprestasi::getPoin($conn,$poinkodejenisprestasi.'|'.$poinkodetingkatprestasi.'|'.$poinkodekategoriprestasi.'|'.$poinkodejenispeserta);
		$c_delete = false;
	}
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
			<form name="pageform" id="pageform" method="post"   enctype="multipart/form-data">
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
						<?php
							if(Akademik::isMhs())
							{
						?>
								<tr>
									<td class="LeftColumnBG">Mahasiswa</td>
									<td class="RightColumnBG"><?=$r_namamahasiswa?></td>
								</tr>
								<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">
						<?php
						}else{
							?>

							<tr>
								<td class="LeftColumnBG">Mahasiswa</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namamahasiswa,
										UI::createTextBox('mahasiswa',$r_namamahasiswa,'ControlStyle',null,50)) ?>
									<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">
								</td>
							</tr>
						<?php } ?>
							<?= Page::getDataTR($row,'kodejenisprestasi') ?>
							<?= Page::getDataTR($row,'kodetingkatprestasi') ?>
							<?= Page::getDataTR($row,'kodekategoriprestasi') ?>
							<?= Page::getDataTR($row,'kodejenispeserta') ?>
							<?= Page::getDataTR($row,'namaprestasi') ?>
							<?= Page::getDataTR($row,'namaprestasien') ?>
							<?php
								if(!empty($poin_datavalid)){
							?>
							<tr>
								<td class="LeftColumnBG">Poin </td>
								<td class="RightColumnBG">
									<?php echo $poinvalid ?>
									<?php /* <span id="span_poin"><?php echo $poinvalid ?></span> */ ?>
								</td>
							</tr>
							<?php
								}
							?>
							<?//= Page::getDataTR($row,'tglprestasi') ?>
							<tr>
								<td class="LeftColumnBG">Tgl Prestasi</td>
								<td class="RightColumnBG">
									<span id="show"><?=date::indoDate($tglprestasi).' s/d '.date::indoDate($tglprestasiakhir)?></span>
									<span id="edit" style="display:none"><?=Page::getDataInput($row,'tglprestasi') ?> s/d <?=Page::getDataInput($row,'tglprestasiakhir') ?></span>
								</td>
							</tr>
							<?= Page::getDataTR($row,'lokasi') ?>
							<?= Page::getDataTR($row,'penyelenggara') ?>
							<?php /* Page::getDataTR($row,'fileprestasi') ?>
							<tr>
								<td class="LeftColumnBG" width="120" style="white-space:nowrap">
									<?= Page::getDataLabel($row,'fileprestasi') ?>
								</td>
								<td class="RightColumnBG">
									<?= Page::getDataInput($row,'fileprestasi') ?>
									<div id="edit" style="display:none;font-style:italic;margin-top:5px">
										<div>Type file: jpg, pdf, atau doc</div>
										<div>Ukuran file: max. 2 MB</div>
									</div>
								</td>
							</tr> */
							?>
							<tr>
								<td class="LeftColumnBG" style="white-space:nowrap" width="120"> <?= Page::getDataLabel($row,'fileprestasi') ?></td>
								<td class="RightColumnBG">
									<span id="show">
										<u class="ULink" onclick="popup('popUpDiv')"><?=Page::getDataValue($row,'fileprestasi')?></u>
									<div class="Break"></div>
									<?php
									$cek = Page::getDataValue($row,'fileprestasi');
									if(!empty($cek)){
									?>
									<u class="ULink" onclick="goDeleteFile('filelpj')">Hapus file</u>
									<?php } ?>
									</span>
									<span id="edit" style="display:none">
									<?=Page::getDataInput($row,'fileprestasi')?>
									<div style="font-style:italic;margin-top:5px">
										<div>Type file: image, pdf, atau doc</div>
										<div>Ukuran file: max. <?php echo $p_upsize ?></div>
									</div>
									</span>
								</td>
							</tr>
							<?= (!Akademik::isMhs())?Page::getDataTR($row,'isvalid'):'' ?>
							<?= Page::getDataTR($row,'istampil') ?>
						</table>
					</div>
				</center>

				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?php echo $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="valid" id="valid" value="<?=Page::getDataValue($row,'isvalid') ?>" >
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
<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	<?php if(empty($r_key)){?>
	initEdit(<?= empty($post) ? false : true ?>);
	<?}?>
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nim"});

	<?php /* // cek poin
	$("#kodejenisprestasi").change(cekPoin);
	$("#kodetingkatprestasi").change(cekPoin);
	$("#kodekategoriprestasi").change(cekPoin);
	$("#kodejenispeserta").change(cekPoin); */ ?>
});

<?php /* function cekPoin() {
	var param = new Array();
	param[0] = $("#kodejenisprestasi").val();
	param[1] = $("#kodetingkatprestasi").val();
	param[2] = $("#kodekategoriprestasi").val();
	param[3] = $("#kodejenispeserta").val();

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "poinprestasi", q: param }
				});

	jqxhr.done(function(poin) {
		$("#span_poin").html(poin);
	});
	jqxhr.fail(function(xhr,status) {
		alert(status);
	});
} */ ?>

function popup(idpop) {

	var pop = $("#"+idpop);

	// pop.offset({ top: e.pageY, left: e.pageX });

	<?php
	$filename = Route::getUploadedFile('prestasi',$r_key);
	$getfile = Route::getUploadedFile('prestasi/temp/',$r_key).'_'.session_id().'.'.$ext;
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
    data: 'src='+img+'&type=prestasi&key=<?=$r_key?>', //send some unique piece of data like the ID to retrieve the corresponding user information
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
