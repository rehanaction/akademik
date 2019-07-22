<?php
//$conn->debug = true;
require_once(Route::getModelPath('beasiswa'));
require_once(Route::getModelPath('pelatihanbeasiswamaba'));
require_once(Route::getUIPath('form'));

$t_detail = array();
$t_detail[] = array('kolom' => 'namapelatihan', 'label' => 'Nama Pelatihan');
$t_detail[] = array('kolom' => 'lembaga', 'label' => 'Lembaga');
$t_detail[] = array('kolom' => 'tahun', 'label' => 'Tahun');


//post
$r_idpdendaftar = $q[0];
$r_key = $q[1];
$r_subkey = $q[2];
$r_act = $q[3];

//data post edit
$r_post = array();
$r_post['namapelatihan'] = $q[4];
$r_post['lembaga'] = $q[5];
$r_post['tahun'] = $q[6];

if($r_act == 'savedetail')
{
	$record2 = array();
	if(empty($r_subkey)){
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		$record2['idpengajuanbeasiswa'] = $r_key;
		list($p_posterr,$p_postmsg) = mPelatihanBeasiswaMaba::insertRecord($conn,$record2);
		
	}else{
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		list($p_posterr,$p_postmsg) = mPelatihanBeasiswaMaba::updateRecord($conn,$record2,$r_subkey,true);
	}
}else if($r_act == 'deletedetail')
{
	list($p_posterr,$p_postmsg) = mPelatihanBeasiswaMaba::delete($conn,$r_subkey,true);
}
$a_pelatihan = mBeasiswa::getPelatihan($conn,$r_key,'pelatihan');;

?>
<div class="DivError" style="display:none"></div>
<div class="DivSuccess" style="display:none"></div>

<table width="100%" cellspacing="2" cellpadding="4" align="center" class="GridStyle">
	<tr>
		<td class="DataBG" colspan="7">
			Pelatihan yang pernah diikuti di sekolah atau di luar sekolah
			<div class="pull-right">
				<button type="button" act="insert" onclick="goInsertPelatihan(this)">Tambah data </button>
			</div>
		</td>
	</tr>
	<tr>
		<th width="30" align="center" class="HeaderBG">No</th>
		<th align="center" class="HeaderBG">Nama Pelatihan</th>
		<th align="center" class="HeaderBG">Lembaga</th>
		<th align="center" class="HeaderBG">Tahun</th>
		<? //if($c_update) { 
			?>					
			<th align="center" colspan="2" class="HeaderBG" width="30" id="edit">Aksi</th>
		<? //} ?>
	</tr>
	<?php 
	$i = 1;
	foreach($a_pelatihan as $rowh){ 
		if($r_act == 'editdetail' and $rowh['idpelatihanbeasiswa'] == $r_subkey)
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
			 ?>
				<td id="edit" align="center"> 
					<img id="<?= $rowh['idpelatihanbeasiswa']?>" act="savedetail" title="Edit Data" src="images/disk.png" onclick="goUpdatePelatihan(this)" style="cursor:pointer">
				</td>
				<td id="edit" align="center" style="display:none"> 
					<img title="Tambah Data" src="images/disk.png" onclick="goUpdatePelatihan(this)" style="cursor:pointer">
				</td>
			<?	 ?> 
		</tr>	
		<?php
		}else
		{
		?>
		<tr>
			<td><?=$i++;?></td>
			<td><?=$rowh['namapelatihan']?></td>
			<td align="center"><?=$rowh['lembaga']?></td>
			<td align="center"><?=$rowh['tahun']?></td>
			
			<td id="edit" align="center"> 
				<img id="<?= $rowh['idpelatihanbeasiswa']?>" act="editdetail" title="Edit Data" src="images/edit.png" onclick="goUpdatePelatihan(this)" style="cursor:pointer">
			</td>
			<td id="edit" align="center"> 
				<img id="<?= $rowh['idpelatihanbeasiswa']?>" act="deletedetail" title="Hapus Data" src="images/delete.png" onclick="goDeletePelatihan(this)" style="cursor:pointer">
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
			<td align="center">
				<img title="Tambah Data" act="savedetail" src="images/disk.png" onclick="goSavePelatihan(this)" style="cursor:pointer">
			</td>		
	</tr>
	<?php } ?>
</table>

<script>
function goUpdatePelatihan(elem){
	
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?=$r_key?>";
	param[2] = elem.id; //subkey
	param[3] = elem.getAttribute('act'); //act
	param[4] = $("#u_namapelatihan").val(); 
	param[5] = $("#u_lembaga").val(); 
	param[6] = $("#u_tahun").val(); 
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpelatihan", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-pelatihan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function goSavePelatihan(elem){
	
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?=$r_key?>";
	param[2] = elem.id; //subkey
	param[3] = elem.getAttribute('act'); //act
	param[4] = $("#i_namapelatihan").val(); 
	param[5] = $("#i_lembaga").val(); 
	param[6] = $("#i_tahun").val(); 
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpelatihan", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-pelatihan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function goInsertPelatihan(elem){
	
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?=$r_key?>";
	param[2] = elem.id; //subkey
	param[3] = elem.getAttribute('act'); //act
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadpelatihan", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-pelatihan").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function goDeletePelatihan(elem){
	var conf = confirm("Anda yakin ingin menghapus?");
	if(conf){
		var param = new Array();
		param[0] = $("#key").val();
		param[1] = "<?=$r_key?>";
		param[2] = elem.id; //subkey
		param[3] = elem.getAttribute('act'); //act
		
		var jqxhr = $.ajax({
						url: ajaxpage,
						timeout: ajaxtimeout,
						data: { f: "loadpelatihan", q: param }
					});
		
		jqxhr.done(function(data) {
			$("#v-pelatihan").html(data);
		});
		jqxhr.fail(function(xhr,status) {
			alert(status);
		});
	}
}
</script>
