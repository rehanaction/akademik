<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('userguide'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// variabel request
	$r_key = 1;

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;

	// properti halaman
	$p_title = 'File User Guide';
	$p_tbwidth = 640;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();

	$p_model = mUserguide;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);

	//struktur view
	$a_input = array();

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['kodeunit']= $r_key;
		$cek = $p_model::getData($conn,$r_key);
		if(empty($cek))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);

		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::deleteProdi($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);
	}else if($r_act == 'savefoto') {
		if($_FILES['foto']['size']>512000){
			$msg = 'Upload gagal, Maksimal File 500 KB';
		}else if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],uForm::getPathImageSKPI($conn,$r_key),300,300);
			//var_dump($err);die;
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;

		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);

		uForm::reloadImageSKPI($conn,$r_key,$msg);
	}
	else if($r_act == 'deletefoto' and $c_upload) {
		@unlink($p_foto);

		uForm::reloadImageSKPI($conn,$r_key);
	}
  else if($r_act == 'upload') {
      $tipe=array('application/pdf');
      $ext=array('image/jpg'=>'jpg','image/jpeg'=>'jpeg','image/gif'=>'gif','image/png'=>'png','application/pdf'=>'pdf');

  		//var_dump($idpengajuanbeasiswa);die;
  		$file_types=$_FILES['fileuserguide_'.$_POST['key']]['type'];
  		$file_nama=$_FILES['fileuserguide_'.$_POST['key']]['name'].'.'.$ext[$file_types];

  		if(in_array($file_types,$tipe) && !empty($tipe)){
  			$upload=move_uploaded_file($_FILES['fileuserguide_'.$_POST['key']]['tmp_name'],'uploads/guide/'.$file_nama);

  			if($upload){
  				$recordu=array();
  				$recordu['koderole'] = $_POST['key'];
  				$recordu['fileuserguide'] = $file_nama;

  				//delete berkas
  				list($p_posterr,$p_postmsg) = $p_model::delete($conn,$_POST['key']);
  				//insert berkas
  				list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$recordu);

  			}else{
  				$p_posterr=true;
  				$p_postmsg='Upload Gagal';
  			}
  		}else{
  			$p_posterr=true;
  			$p_postmsg='Pastikan Tipe File Berupa Pdf, Upload Gagal';
  		}
  	}

	$r_namaunit = mUnit::getNamaUnit($conn,$r_key);

	//$sql = $p_model::dataQueryProdi($r_key);
	$rowd = $p_model::getList($conn,$a_input);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<script type="text/javascript" src="scripts/tiny_mce/tiny_mce.js"></script>
  <script type="text/javascript" src="scripts/jquery-1.7.1.min.js"></script>
	<script type="text/javascript">
	tinyMCE.init({
		mode: "textareas",
		height: "300",
		theme: "advanced",
		theme_advanced_toolbar_location : "top",
		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,sub,sup,|,justifyleft,justifycenter,justifyright,justifyfull,|,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "bullist,numlist,|,outdent,indent,blockquote,|,hr,removeformat,|,charmap,|,link,unlink,anchor,image,cleanup,code,|,forecolor,backcolor",
		theme_advanced_buttons3 : ""
	});
	</script>
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
					?>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">User Guide</a></li>
					</ul>

					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
            <?
        		//var_dump($rowd);
        		foreach($rowd as $row){
        		?>
        		<tr>
        			<td><?=($row['koderole'] == 'A')?'Admin' : 'Mahasiswa' ?></td>
        			<td class="inline">
        				<?php
        					if(!empty($row['fileuserguide'])){
        				?>
        					<a href="<?=$conf['upload_dir'].'guide/'.$row['fileuserguide'] ?>" target="_blank"><?=$row['fileuserguide']?></a>
        				<?php
              }else{
        				?>
        				<input type="file" name="fileuserguide_<?=$row['koderole']?>" id="fileuserguide" ><br>
        				<button class="btn-success" type="button" data-id="<?=$row['koderole'] ?>" value="Upload" onclick="goUploadSyarat(this)"> Upload</button>
        				<?php
              }
        				if(!empty($row['fileuserguide'])){
        				?>
        					<u class="ULink" data-id="<?=$r_idpengajuan.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" data-type="<?=end(explode('.',$rowdd['fileberkas']))?>" onclick="goDeleteFileSyarat(this)">Hapus file</u>

        				<?php
        				}
        				?>
        			</td>
        		</tr>
        		<?
        		}
        		?>
					</table>
					</div>
				</div>

				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<?	} ?>
			</form>
		</div>
	</div>
</div>
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
});

function goUploadSyarat(elem) {
	pageform.act.value='upload';
	pageform.key.value = $(elem).attr('data-id');
  document.getElementById("pageform").submit();
}

function goDeleteFileSyarat(elem) {
	document.getElementById("act").value = "deletefile";
	document.getElementById("key").value = $(elem).attr('data-id');
	goSubmit();
}

</script>
</body>
</html>
