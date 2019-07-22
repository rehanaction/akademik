<?php
	$p_tbwidth = 600;
	require_once(Route::getModelPath('seminar'));
	
	$ruang = mCombo::ruang($conn);
    $b_input = array();
	$b_input[] = array('kolom' => 'tgljadwal', 'label' => 'Tgl Pertemuan','type' => 'D','notnull'=>true);
	$b_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type' => 'S', 'option' => $ruang,'empty'=>true);
	$b_input[] = array('kolom' => 'jammulai','label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
	$b_input[] = array('kolom' => 'jamselesai', 'label' => 'Jam Selesai','maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');

	//$detail_kelas = mPendFormal::getArray($conn,$r_key);

	$detail_jadwal = array();
	$detail_jadwal = mSeminar::getJadwalSeminar($conn,$r_key);

?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="7" class="DataBG">Data Detail Seminar</td>
	</tr>
	<tr>
		
		<th id="koderuang">No </th>
		<?php
			foreach($b_input as $datakolom) {
				if($t_col == $datakolom['kolom'])
					$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
				else
					$t_sortimg = '';
		?>
		<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
		<?	} ?>
		<?	
		if($c_edit or $c_delete) { ?>
		<!--<th width="50">Edit</th>-->
		<th width="50">Hapus</th>
		<?	} ?>
	</tr>
	<?	/********/
		/* ITEM */
		/********/
		
		$i = 0;
		foreach($detail_jadwal as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row['idjadwalseminar'];
			
			//if($t_key == $r_edit and $c_edit) {
			$rowc = Page::getColumnEdit($b_input,'u'.$t_key.'_','onkeydown="etrUpdate(event)"',$row);
				
			$a_updatereq = array();
	?>
	<tr align="center" valign="top" class="AlternateBG2" id="u_detail<?=$t_key?>" style="display:none">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) {
					if($rowcc['notnull'])
						$a_updatereq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?		} ?>
		<td align="center" colspan=2>
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upDetailJadwal(this)" style="cursor:pointer">
		</td>
	</tr>
	<?		//}
			//else {
				$rowc = Page::getColumnRow($b_input,$row);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="detail<?=$t_key?>">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) { ?>					
		<td><?= $rowcc ?></td>
		<?		}
				if($c_delete) { ?>
		<td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delDetailKelas(this)" style="cursor:pointer">
		<?			}
		?>
		</td>

		<?		} ?>
	</tr>

	<?		}
		if($i == 0) {
	?>
	<tr>
		<td colspan="5" align="center">Data kosong</td>
	</tr>
	<?	}
		if($c_insert) { ?>
	<tr align="center" valign="top" class="LeftColumnBG NoHover">
		<td>&nbsp;</td>
		<?	$rowc = Page::getColumnEdit($b_input,'i_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inDetailJadwal(this)" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>
$(function() {
        $.mask.definitions['~'] = "[+-]";
		$("#i_jammulai").mask("99:99");
		$("#i_jamselesai").mask("99:99");
		$(".jam").mask("99:99");
		
    });
function editDetailKelas(elem) {
	//document.getElementById("act").value = "editpendformal";
	//document.getElementById("subkey").value = elem.id;
	//goSubmit();
	$("#detail"+elem.id).hide();
	$("#u_detail"+elem.id).show();
	
}
function upDetailJadwal(elem) {
	var idsem = <?=$r_key?>;
	var tgljadwal=$("#u"+elem.id+"_tgljadwal").val();
	var koderuang=$("#u"+elem.id+"_koderuang").val();
	var jammulai=$("#u"+elem.id+"_jammulai").val();
	var jamselesai=$("#u"+elem.id+"_jamselesai").val();

	var posted = "act=updatejadwal&q[]=<?=$t_key?>&q[]="+tgljadwal+"&q[]="+koderuang+"&q[]="+jammulai+"&q[]="+jamselesai+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailjadwalseminar").html(text);
		});
	
}
function inDetailJadwal() {
	var idsem = <?=$r_key?>;
	var tgljadwal=$("#i_tgljadwal").val();
	var koderuang=$("#i_koderuang").val();
	var jammulai=$("#i_jammulai").val();
	var jamselesai=$("#i_jamselesai").val();

	var posted = "act=insertjadwal&q[]=<?=$r_key?>&q[]="+tgljadwal+"&q[]="+koderuang+"&q[]="+jammulai+"&q[]="+jamselesai+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";


		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailjadwalseminar").html(text);
		});
}
function delDetailKelas(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delDetailKelas&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailjadwalseminar").html(text);
		});
	}
}

</script>
