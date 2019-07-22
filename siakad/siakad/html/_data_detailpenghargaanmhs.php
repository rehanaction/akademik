<?php

require_once(Route::getModelPath('mahasiswa'));
require_once(Route::getUIPath('form'));

$t_detail = array();
$t_detail[] = array('kolom' => 'tglpenghargaan', 'label' => 'Tanggal', 'type' => 'D','size' => 5);
$t_detail[] = array('kolom' => 'namapenghargaan', 'label' => 'Nama Penghargaan', 'size' => 18, 'maxlength' => 255);
$t_detail[] = array('kolom' => 'namapenghargaanenglish', 'label' => 'Nama Penghargaan English', 'size' => 18, 'maxlength' => 255);
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
$a_penghargaan = mMahasiswa::getPenghargaan($conn,$r_key,'penghargaan');;

?>
<div class="DivError" style="display:none"></div>
<div class="DivSuccess" style="display:none"></div>
<table width="100%" cellspacing="2" cellpadding="4" align="center" class="GridStyle">
	<tr>
		<td class="DataBG" colspan="6">Penghargaan</td>
		<td class="DataBG" class="TDButton">
			<button type="button" act="insert" onclick="goInsertPenghargaan(this)"><img src="images/add.png"></button>
		</td>
	</tr>
	<tr>
		<th width="30" align="center" class="HeaderBG">No</th>
		<th align="center" class="HeaderBG">Tanggal Penghargaan</th>
		<th align="center" class="HeaderBG">Nama Penghargaan</th>
		<th align="center" class="HeaderBG">Nama Penghargaan (eng)</th>
		<th align="center" class="HeaderBG">Valid?</th>
		<? //if($c_update) { 
			?>					
			<th align="center" colspan="2" class="HeaderBG" width="30" id="edit">Aksi</th>
		<? //} ?>
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
			foreach($rowc as $rowcc) {
			?>
				<td><?= $rowcc['input'] ?></td>
			<?php
			}
			 if( $rowh['isvalid']!='-1'){ ?>
				<td id="edit" align="center" colspan="2"> 
					<img id="<?= $rowh['idpenghargaan']?>" act="savedetail" title="Edit Data" src="images/disk.png" onclick="goUpdatePenghargaan(this)" style="cursor:pointer">
				</td>
			<?	} ?> 
		</tr>	
		<?php
		}else
		{
		?>
		<tr>
			<td><?=$i++;?></td>
			<td><?=CStr::formatDateInd($rowh['tglpenghargaan'])?></td>
			<td align="center"><?=$rowh['namapenghargaan']?></td>
			<td align="center"><?=$rowh['namapenghargaanenglish']?></td>
			<td align="center" >
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

			<? if( $rowh['isvalid']!='-1'){ ?>
				<td id="edit" align="center"> 
					<img id="<?= $rowh['idpenghargaan']?>" act="editdetail" title="Edit Data" src="images/edit.png" onclick="goUpdatePenghargaan(this)" style="cursor:pointer">
				</td>
				<td id="edit" align="center"> 
					<img id="<?= $rowh['idpenghargaan']?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('penghargaan',this)" style="cursor:pointer">
				</td>
			<?	} ?> 
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
			<td align="center" colspan="2">
				<img title="Tambah Data" act="savedetail" src="images/disk.png" onclick="goSavePenghargaan(this)" style="cursor:pointer">
			</td>		
	</tr>
	<?php } ?>
</table>

<script>
function goUpdatePenghargaan(elem){
	
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = elem.id; //subkey
	param[2] = elem.getAttribute('act'); //act
	param[3] = $("#u_tglpenghargaan").val(); 
	param[4] = $("#u_namapenghargaan").val(); 
	param[5] = $("#u_namapenghargaanenglish").val(); 
	
	
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
</script>
