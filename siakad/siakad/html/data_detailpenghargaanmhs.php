<?php

require_once(Route::getModelPath('mahasiswa'));
require_once(Route::getModelPath('jenispenghargaan'));
require_once(Route::getModelPath('combo'));
require_once(Route::getUIPath('form'));

$a_kategori = mJenisPenghargaan::getArray($conn);

$t_detail = array();
$t_detail[] = array('kolom' => 'tglpenghargaan', 'label' => 'Tanggal', 'type' => 'D','size' => 5);
$t_detail[] = array('kolom' => 'namapenghargaan', 'label' => 'Nama Penghargaan', 'size' => 18, 'maxlength' => 255);
$t_detail[] = array('kolom' => 'namapenghargaanenglish', 'label' => 'Nama Penghargaan English', 'size' => 18, 'maxlength' => 255);
$t_detail[] = array('kolom' => 'idjenispenghargaan', 'label' => 'Kategori', 'type' => 'S', 'option' => $a_kategori);
$t_detail[] = array('kolom' => 'isvalid', 'label' => 'Valid?', 'type' => 'C', 'option' => array('-1' => ''),'readonly'=>Akademik::isMhs());

//post
$r_key = $q[0];
$r_subkey = $q[1];
$r_act = $q[2];

//data post edit
$r_post = array();
$r_post['tglpenghargaan'] = CStr::formatDate($q[3]);
$r_post['namapenghargaan'] = $q[4];
$r_post['namapenghargaanenglish'] = $q[5];
$r_post['idjenispenghargaan'] = $q[6];

if($r_act == 'savedetail')
{
	$record2 = array();
	if(empty($r_subkey)){
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		$record2['nim'] = $r_key;
		list($p_posterr,$p_postmsg) = mPenghargaan::insertRecord($conn,$record2);
	}else{
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		list($p_posterr,$p_postmsg) = mPenghargaan::updateRecord($conn,$record2,$r_subkey.'|'.$r_key,true);
	}
}
$a_penghargaan = mMahasiswa::getPenghargaan($conn,$r_key,'penghargaan');
?>
<div id="detailImg" style="display:block"> </div>
<div class="DivError" style="display:none"></div>
<div class="DivSuccess" style="display:none"></div>
<table width="100%" cellspacing="2" cellpadding="4" align="center" class="GridStyle">
	<tr>
		<td class="DataBG" colspan="7">Penghargaan</td>
		<td class="DataBG" colspan="2" align="center">
			<button type="button" act="insert" onclick="goInsertPenghargaan(this)"><img src="images/add.png"></button>
		</td>
	</tr>
	<tr>
		<th rowspan="2" width="30" align="center" class="HeaderBG">No</th>
		<th rowspan="2" width="115" align="center" class="HeaderBG">Tanggal</th>
		<th colspan="2" align="center" class="HeaderBG">Judul Penghargaan</th>
		<th rowspan="2" align="center" class="HeaderBG">Kategori</th>
		<th rowspan="2" align="center" class="HeaderBG">Valid?</th>
		<th rowspan="2"  align="center" class="HeaderBG">Sertifikat</th>
		<? // if($c_update) { ?>
		<th rowspan="2" align="center" class="HeaderBG" colspan="2" width="30" id="edit">Aksi</th>
		<? // } ?>
	</tr>
	<tr>
		<th align="center" class="HeaderBG">Indonesia</th>
		<th align="center" class="HeaderBG">Inggris</th>
	</tr>
	<?php
	$i = 1;
	foreach($a_penghargaan['penghargaan'] as $rowh){
		if($r_act == 'editdetail' and $rowh['idpenghargaan'] == $r_subkey)
		{
			$rowc = Page::getColumnEdit($t_detail,'u_','',$rowh);
		?>
		<tr>
			<td><?=$i++;?></td>
			<?php
			/*
			<td><?=$i++;?></td>
			<td><?=CStr::formatDateInd($rowh['tglpenghargaan'])?></td>
			<td align="center"><?='cok'.$rowh['namapenghargaan']?></td>
			<td align="center"><?='cok'.$rowh['namapenghargaanenglish']?></td>
			<td align="center" >
				<input type="checkbox" id="<?= $rowh['idpenghargaan'].'|'.$r_key?>" <?=($rowh['isvalid']=='-1')?'checked':''?> title="" onclick="validpenghargaan(this)" <?=$p_limited?'disabled':''?>>
			</td>
			*/
				foreach($rowc as $rowcc) { ?>
			<td><?= $rowcc['input'] ?></td>
			<?php } ?>
			<td align="center">
				<u onclick="choosePenghargaan()" class="ULink">Upload<br> (image,jpg dan pdf) max 2MB</u>
			</td>
			<td id="edit" align="center" colspan="2">
				<?php if($rowh['isvalid'] != '-1') { ?>
				<img id="<?= $rowh['idpenghargaan']?>" act="savedetail" title="Edit Data" src="images/disk.png" onclick="goUpdatePenghargaan(this)" style="cursor:pointer">
				<?php } ?>
			</td>
		</tr>
		<?php
		}else
		{
		?>
		<tr>
			<td><?=$i++;?></td>
			<td><?=CStr::formatDateInd($rowh['tglpenghargaan'])?></td>
			<td><?=$rowh['namapenghargaan']?></td>
			<td><?=$rowh['namapenghargaanenglish']?></td>
			<td>
				<?=$a_kategori[$rowh['idjenispenghargaan']]?>
			</td>
			<td>
				<?php
				if(!Akademik::isMhs()){
				?>
					<input type="checkbox" id="<?= $rowh['idpenghargaan'].'|'.$r_key?>" <?=($rowh['isvalid']=='-1')?'checked':''?> title="" onclick="validpenghargaan(this)" <?=(Akademik::isMhs())?'disabled':''?>>
				<?php
				}else{
					if( $rowh['isvalid']!='-1')
						echo "";
					else
						echo '<img src="images/check.png">';
				}
				?>
			</td>
			<td align="center" style="line-height:1.5">
				<?php if(!empty($rowh['filesertifikat'])) {
					$ext = end(explode('.',$rowh['filesertifikat']));
					$filename = Route::getUploadedFile('penghargaan',$rowh['idpenghargaan'].'|'.$r_key);
					$getfile = Route::getUploadedFile('penghargaan/temp/',$rowh['idpenghargaan'].'|'.$r_key).'_'.session_id().'.'.$ext;
					$isext = array('pdf','PDF','odf','ODF','ODS','ods','jpg','JPG','jpeg','JPEG','png','PNG');
					$extImg = array('jpg','JPG','jpeg','JPEG','png','PNG');
					$img = 0;

					if(in_array($ext,$isext)){
						$open = true;
						if(in_array($ext,$extImg)){
							$img = true;
						}
					}else{
						$open = false;
					}
				?>
				<?

				?>
				<u  onclick="popup('popUpDiv','<?=$filename?>','<?=$getfile?>',<?=$open?>,<?=$img?>,'<?= $rowh['idpenghargaan'].'|'.$r_key?>')" class="ULink">File</u><br />
				<u id="<?= $rowh['idpenghargaan']?>" onclick="goDeletePenghargaan(this)" class="ULink" style="color:red">Hapus File</u>
				<?php } ?>
			</td>
			<td id="edit" align="center">
				<? if( $rowh['isvalid']!='-1'){ ?>
				<img id="<?= $rowh['idpenghargaan']?>" act="editdetail" title="Edit Data" src="images/edit.png" onclick="goUpdatePenghargaan(this)" style="cursor:pointer">
				<? } ?>
			</td>
			<td id="edit" align="center">
				<? if( $rowh['isvalid']!='-1'){ ?>
				<img id="<?= $rowh['idpenghargaan']?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('penghargaan',this)" style="cursor:pointer">
				<? } ?>
			</td>
		</tr>
	<?php }
	}
	if($r_act == 'insert'){
	?>
	<tr valign="top" class="LeftColumnBG" >
		<td></td>
			<?
			foreach($t_detail as $datakolom) {
					$datakolom['nameid'] = 'i_'.CStr::cEmChg($datakolom['nameid'],$datakolom['kolom']);
			?>
				<td><?= uForm::getInput($datakolom) ?></td>
			<?	} ?>
			<td></td>
			<td align="center" colspan="2">
				<img title="Tambah Data" act="savedetail" src="images/disk.png" onclick="goSavePenghargaan(this)" style="cursor:pointer">
			</td>
	</tr>
	<?php } ?>
</table>
<input type="file" name="filepenghargaan" id="filepenghargaan" onchange="uploadPenghargaan()" style="display:none">
<div id="popUpDiv" style="position: absolute; top: 97px; left: 301px; z-index: 10000; display:none ">
	<div id="popUpDivInner"></div>
</div>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>

<script>
function goUpdatePenghargaan(elem){

	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act
	param[3] = $("#u_tglpenghargaan").val();
	param[4] = $("#u_namapenghargaan").val();
	param[5] = $("#u_namapenghargaanenglish").val();
	param[6] = $("#u_idjenispenghargaan").val();


	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpenghargaan", q: param }
				});

	jqxhr.done(function(data) {
		$("#item-penghargaan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function goSavePenghargaan(elem){

	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act
	param[3] = $("#i_tglpenghargaan").val();
	param[4] = $("#i_namapenghargaan").val();
	param[5] = $("#i_namapenghargaanenglish").val();
	param[6] = $("#i_idjenispenghargaan").val();


	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpenghargaan", q: param }
				});

	jqxhr.done(function(data) {
		$("#item-penghargaan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function goInsertPenghargaan(elem){

	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpenghargaan", q: param }
				});

	jqxhr.done(function(data) {
		$("#item-penghargaan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function choosePenghargaan() {
	$("#filepenghargaan").click();
}
function uploadPenghargaan() {
	$("#act").val("uploadpenghargaan");
	$("#subkey").val("<?php echo $r_subkey ?>");
	goSubmit();
}
function goDeletePenghargaan(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus file sertifikat penghargaan ini?");
	if(hapus) {
		$("#act").val("deletefilepenghargaan");
		$("#subkey").val(elem.id);
		goSubmit();
	}
}


function popup(idpop,filename,getfile,open,img,key) {

	var pop = $("#"+idpop);

	// pop.offset({ top: e.pageY, left: e.pageX });
	pop.show();
	if(open){
		$.ajax({
		    url: 'index.php?page=copytemp',
		    type: 'POST',
		    data: 'filename='+filename+'&getfile='+getfile, //send some unique piece of data like the ID to retrieve the corresponding user information
		    success: function(data){
		      //construct the data however, update the HTML of the popup div
					if(img == 1){
						popImage(getfile,key);
					}else{
						$('#popUpDivInner').html('<iframe id="frameload" src="<?= Route::navAddress('viewerjs').'/#../siakad/'?>'+getfile+'" width="700" height="700">');
					}
		    }
		  });
	}else{
		goDownload('penghargaan');
	}
	//$('#popUpDivInner').html('<<?= Route::navAddress('viewerjs') ?>');
	$(document).bind("mouseup",function(e) {
		if(pop.has(e.target).length === 0) {
			pop.hide();
		}
	});

}

function popImage(img,key)
{
$.ajax({
    url: 'index.php?page=viewimg',
    type: 'POST',
    data: 'src='+img+'&type=penghargaan&key='+key, //send some unique piece of data like the ID to retrieve the corresponding user information
    success: function(data){
      //construct the data however, update the HTML of the popup div
	   $('#detailImg').html(data);
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
