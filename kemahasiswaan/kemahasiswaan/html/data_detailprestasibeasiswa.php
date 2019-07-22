<?php
require_once(Route::getModelPath('jenisprestasi'));
require_once(Route::getModelPath('tingkatprestasi'));
require_once(Route::getModelPath('kategoriprestasi'));
require_once(Route::getUIPath('form'));

$t_detail = array();
$t_detail[] = array('kolom' => 'kodejenisprestasi', 'label' => 'Jenis Prestasi', 'type' => 'S', 'option' => array('' => '') + mJenisprestasi::getArray($conn));
$t_detail[] = array('kolom' => 'kodetingkatprestasi', 'label' => 'Tingkat Prestasi', 'type' => 'S', 'option' => array('null' => '') + mTingkatprestasi::getArray($conn));
$t_detail[] = array('kolom' => 'kodekategoriprestasi', 'label' => 'Juara', 'type' => 'S', 'option' => array('' => '') + mKategoriprestasi::getArray($conn));
$t_detail[] = array('kolom' => 'namaprestasi', 'label' => 'Prestasi');

//post
$r_key = $q[0];
$r_subkey = $q[1];
$r_act = $q[2];

//data post edit
$r_post = array();
$r_post['kodejenisprestasi'] = $q[3];
$r_post['kodetingkatprestasi'] = $q[4];
$r_post['kodekategoriprestasi'] = $q[5];
$r_post['namaprestasi'] = $q[6];

if($r_act == 'savedetail')
{
	$record2 = array();
	if(empty($r_subkey)){
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		$record2['idpengajuanbeasiswa'] = $r_key;
		list($p_posterr,$p_postmsg) = $p_modelprestasi::insertRecord($conn,$record2);
	}else{
		foreach($r_post as $i=>$v){
			$record2[$i] = $v;
		}
		list($p_posterr,$p_postmsg) = $p_modelprestasi::updateRecord($conn,$record2,$r_subkey,true);
	}
}

$a_penghargaan = $p_modelpengajuan::getPrestasi($conn,$r_key,'prestasi');;
?>
<div class="DivError" style="display:none"></div>
<div class="DivSuccess" style="display:none"></div>
<div class="pull-right">
	<button type="button" act="insert" onclick="goInsertPenghargaan(this)">Tambah data </button>
</div>
<table width="80%" cellspacing="2" cellpadding="4" align="center" class="GridStyle">
	<tr>
		<td class="DataBG" colspan="7">Penghargaan</td>
	</tr>
	<tr>
		<th width="30" align="center" class="HeaderBG">No</th>
		<th align="center" class="HeaderBG">Jenis Prestasi</th>
		<th align="center" class="HeaderBG">Tingkat Prestasi</th>
		<th align="center" class="HeaderBG">Kategori Prestasi</th>
		<th align="center" class="HeaderBG">Nama Prestasi</th>
		<? //if($c_update) {  ?>
			<th align="center" colspan="2" class="HeaderBG" width="30" id="edit">Aksi</th>
		<? //} ?>
	</tr>
	<?php 
	$i = 1;
	foreach($a_penghargaan as $rowh){ 
		if($r_act == 'editdetail' and $rowh['idprestasibeasiswa'] == $r_subkey)
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
					<img id="<?= $rowh['idprestasibeasiswa']?>" act="savedetail" title="Edit Data" src="images/disk.png" onclick="goUpdatePenghargaan(this)" style="cursor:pointer">
				</td>
				<td id="edit" align="center" style="display:none"> 
					<img title="Tambah Data" src="images/disk.png" onclick="goUpdatePenghargaan(this)" style="cursor:pointer">
				</td>
			<?	 ?> 
		</tr>	
		<?php
		}else
		{
		?>
		<tr>
			<td><?=$i++;?></td>
			<td><?=$rowh['namajenisprestasi']?></td>
			<td align="center"><?=$rowh['namatingkatprestasi']?></td>
			<td align="center"><?=$rowh['namakategoriprestasi']?></td>
			<td align="center"><?=$rowh['namaprestasi']?></td>
			<? if( $rowh['isvalid']!='-1'){ ?>
				<td id="edit" align="center"> 
					<img id="<?= $rowh['idprestasibeasiswa']?>" act="editdetail" title="Edit Data" src="images/edit.png" onclick="goUpdatePenghargaan(this)" style="cursor:pointer">
				</td>
				<td id="edit" align="center"> 
					<img id="<?= $rowh['idprestasibeasiswa']?>" title="Hapus Data" src="images/delete.png" onclick="goDeleteDetail('prestasi',this)" style="cursor:pointer">
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
			<td align="center">
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
	param[3] = $("#u_kodejenisprestasi").val(); 
	param[4] = $("#u_kodetingkatprestasi").val(); 
	param[5] = $("#u_kodekategoriprestasi").val(); 
	param[6] = $("#u_namaprestasi").val(); 
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "<?php echo $p_loadprestasi ?>", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#item-prestasi").html(data);
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
	param[3] = $("#i_kodejenisprestasi").val(); 
	param[4] = $("#i_kodetingkatprestasi").val(); 
	param[5] = $("#i_kodekategoriprestasi").val(); 
	param[6] = $("#i_namaprestasi").val(); 
	
	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "<?php echo $p_loadprestasi ?>", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#item-prestasi").html(data);
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
					data: { f: "<?php echo $p_loadprestasi ?>", q: param }
				});
	
	jqxhr.done(function(data) {
		$("#item-prestasi").html(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
</script>
