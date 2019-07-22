<?php
//$conn->debug = true;
require_once(Route::getModelPath('beasiswa'));
require_once(Route::getModelPath('kerjabeasiswamaba'));
require_once(Route::getUIPath('form'));

$t_detail = array();
$t_detail[] = array('kolom' => 'namaperusahaan', 'label' => 'Nama Perusahaan');
$t_detail[] = array('kolom' => 'bidang', 'label' => 'Bidang');
$t_detail[] = array('kolom' => 'jabatan', 'label' => 'Jabatan');


//post
$r_idpdendaftar = $q[0];
$r_key = $q[1];
$r_subkey = $q[2];
$r_act = $q[3];

//data post edit
$r_post = array();
$r_post['namaperusahaan'] = $q[4];
$r_post['bidang'] = $q[5];
$r_post['jabatan'] = $q[6];

if($r_act == 'savedetail')
{
	$record2 = array();
	if(empty($r_subkey)){
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		$record2['idpengajuanbeasiswa'] = $r_key;
		list($p_posterr,$p_postmsg) = mKerjaBeasiswaMaba::insertRecord($conn,$record2);
		
	}else{
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		list($p_posterr,$p_postmsg) = mKerjaBeasiswaMaba::updateRecord($conn,$record2,$r_subkey,true);
	}
}else if($r_act == 'deletedetail')
{
	list($p_posterr,$p_postmsg) = mKerjaBeasiswaMaba::delete($conn,$r_subkey,true);
}
$a_kerja = mBeasiswa::getKerja($conn,$r_key,'kerja');;

?>
<div class="DivError" style="display:none"></div>
<div class="DivSuccess" style="display:none"></div>

<table width="100%" cellspacing="2" cellpadding="4" align="center" class="GridStyle">
	<tr>
		<td class="DataBG" colspan="7">
			Kerja yang pernah diikuti di sekolah atau di luar sekolah
			<div class="pull-right">
				<button type="button" act="insert" onclick="goInsertKerja(this)">Tambah data </button>
			</div>
		</td>
	</tr>
	<tr>
		<th width="30" align="center" class="HeaderBG">No</th>
		<th align="center" class="HeaderBG">Nama Perusahaan</th>
		<th align="center" class="HeaderBG">Bidang</th>
		<th align="center" class="HeaderBG">Jabatan</th>
		<? //if($c_update) { 
			?>					
			<th align="center" colspan="2" class="HeaderBG" width="30" id="edit">Aksi</th>
		<? //} ?>
	</tr>
	<?php 
	$i = 1;
	foreach($a_kerja as $rowh){ 
		if($r_act == 'editdetail' and $rowh['idkerjabeasiswa'] == $r_subkey)
		{
			$rowc = Page::getColumnEdit($t_detail,'u_','',$rowh);
		?>
		<tr>
			<td><?=$i++;?></td>
			<?php

			foreach($rowc as $rowcc) {
			?>
				<td><?= $rowcc['input'] ?></td>
			<?php
			}
			 ?>
				<td id="edit" align="center"> 
					<img id="<?= $rowh['idkerjabeasiswa']?>" act="savedetail" title="Edit Data" src="images/disk.png" onclick="goUpdateKerja(this)" style="cursor:pointer">
				</td>
				<td id="edit" align="center" style="display:none"> 
					<img title="Tambah Data" src="images/disk.png" onclick="goUpdateKerja(this)" style="cursor:pointer">
				</td>
			<?	 ?> 
		</tr>	
		<?php
		}else
		{
		?>
		<tr>
			<td><?=$i++;?></td>
			<td><?=$rowh['namaperusahaan']?></td>
			<td align="center"><?=$rowh['bidang']?></td>
			<td align="center"><?=$rowh['jabatan']?></td>
			
			<td id="edit" align="center"> 
				<img id="<?= $rowh['idkerjabeasiswa']?>" act="editdetail" title="Edit Data" src="images/edit.png" onclick="goUpdateKerja(this)" style="cursor:pointer">
			</td>
			<td id="edit" align="center"> 
				<img id="<?= $rowh['idkerjabeasiswa']?>" act="deletedetail" title="Hapus Data" src="images/delete.png" onclick="goDeleteKerja(this)" style="cursor:pointer">
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
				<img title="Tambah Data" act="savedetail" src="images/disk.png" onclick="goSaveKerja(this)" style="cursor:pointer">
			</td>		
	</tr>
	<?php } ?>
</table>

<script>
function goUpdateKerja(elem){
	
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?=$r_key?>";
	param[2] = elem.id; //subkey
	param[3] = elem.getAttribute('act'); //act
	param[4] = $("#u_namaperusahaan").val(); 
	param[5] = $("#u_bidang").val(); 
	param[6] = $("#u_jabatan").val(); 
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadkerja", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-kerja").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function goSaveKerja(elem){
	
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?=$r_key?>";
	param[2] = elem.id; //subkey
	param[3] = elem.getAttribute('act'); //act
	param[4] = $("#i_namaperusahaan").val(); 
	param[5] = $("#i_bidang").val(); 
	param[6] = $("#i_jabatan").val(); 
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadkerja", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-kerja").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
function goInsertKerja(elem){
	
	var param = new Array();
	param[0] = $("#key").val();
	param[1] = "<?=$r_key?>";
	param[2] = elem.id; //subkey
	param[3] = elem.getAttribute('act'); //act
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadkerja", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#v-kerja").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}

function goDeleteKerja(elem){
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
						data: { f: "loadkerja", q: param }
					});
		
		jqxhr.done(function(data) {
			$("#v-kerja").html(data);
		});
		jqxhr.fail(function(xhr,status) {
			alert(status);
		});
	}
}
</script>
