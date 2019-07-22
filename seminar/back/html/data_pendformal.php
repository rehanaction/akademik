<?php
	
		require_once(Route::getModelPath('pendformal'));
		$pendidikan = array('PERGURUAN TINGGI' => 'PERGURUAN TINGGI', 'SMA' => 'SMA', 'SMP' => 'SMP', 'SD' => 'SD');
        //kolom pendidikan formal
        $pend_formal = array();
		$pend_formal[] = array('kolom' => 'namapend', 'label' => 'Nama Pendidikan', 'type'=>'S', 'option' => $pendidikan,'empty'=>true);
		$pend_formal[] = array('kolom' => 'tempatpend', 'label' => 'Tempat','maxlength' => 100, 'size' => 20,'empty'=>true);
		$pend_formal[] = array('kolom' => 'tahunmasuk','label' => 'Tahun Masuk', 'maxlength' => 4, 'size' => 3);
		$pend_formal[] = array('kolom' => 'tahunlulus', 'label' => 'Tahun Lulus','maxlength' => 4, 'size' => 3);
		$pendidikan_formal = mPendFormal::getArray($conn,$r_key);
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="7" class="DataBG">Data Riwayat Pendidikan Formal</td>
	</tr>
	<tr>
		
		<th id="koderuang">No </th>
		<?php
			foreach($pend_formal as $datakolom_pendf) {
				if($t_col == $datakolom_pendf['kolom'])
					$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
				else
					$t_sortimg = '';
		?>
		<th id="<?= $datakolom_pendf['kolom'] ?>"><?= $datakolom_pendf['label'] ?> <?= $t_sortimg ?></th>
		<?	} ?>
		<?	
		if($c_edit or $c_delete) { ?>
		<th width="50">Edit</th>
		<th width="50">Hapus</th>
		<?	} ?>
	</tr>
	<?	/********/
		/* ITEM */
		/********/
		
		$i = 0;
		foreach($pendidikan_formal as $row_pendformal) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row_pendformal['idpendformal'];
			
			//if($t_key == $r_edit and $c_edit) {
				$rowc = Page::getColumnEdit($pend_formal,'u'.$t_key.'_','onkeydown="etrUpdate(event)"',$row_pendformal);
				
				$a_updatereq = array();
	?>
	<tr align="center" valign="top" class="AlternateBG2" id="u_pendformal<?=$t_key?>" style="display:none">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) {
					if($rowcc['notnull'])
						$a_updatereq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?		} ?>
		<td align="center" colspan=2>
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upPendidikanformal(this)" style="cursor:pointer">
		</td>
	</tr>
	<?		//}
			//else {
				$rowc = Page::getColumnRow($pend_formal,$row_pendformal);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="pendformal<?=$t_key?>">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) { ?>					
		<td><?= $rowcc ?></td>
		<?		}
				if($c_edit or $c_delete) { ?>
		<td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editPendidikanformal(this)" style="cursor:pointer">
		<?			}
		?>
		</td>
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delPendidikanformal(this)" style="cursor:pointer">
		<?			} ?>
		</td>
		<?		} ?>
	</tr>
	<?		//}
		}
		if($i == 0) {
	?>
	<tr>
		<td colspan="5" align="center">Data kosong</td>
	</tr>
	<?	}
		if($c_insert) { ?>
	<tr align="center" valign="top" class="LeftColumnBG NoHover">
		<td>&nbsp;</td>
		<?	$rowc = Page::getColumnEdit($pend_formal,'i_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inPendidikanformal()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>

function delPendidikanformal(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delpendformal&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_pendformal").html(text);
		});
	}
}
function editPendidikanformal(elem) {
	//document.getElementById("act").value = "editpendformal";
	//document.getElementById("subkey").value = elem.id;
	//goSubmit();
	$("#pendformal"+elem.id).hide();
	$("#u_pendformal"+elem.id).show();
	
}
function upPendidikanformal(elem) {
	var namapend=$("#u"+elem.id+"_namapend").val();
	var tempatpend=$("#u"+elem.id+"_tempatpend").val();
	var tahunmasuk=$("#u"+elem.id+"_tahunmasuk").val();
	var tahunlulus=$("#u"+elem.id+"_tahunlulus").val();
	
	var posted = "act=uppendformal&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]="+namapend+"&q[]="+tempatpend+"&q[]="+tahunmasuk+"&q[]="+tahunlulus+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_pendformal").html(text);
		});
	
}
function inPendidikanformal() {
	var namapend=$("#i_namapend").val();
	var tempatpend=$("#i_tempatpend").val();
	var tahunmasuk=$("#i_tahunmasuk").val();
	var tahunlulus=$("#i_tahunlulus").val();
	var posted = "act=inpendformal&q[]=<?=$r_key?>&q[]="+namapend+"&q[]="+tempatpend+"&q[]="+tahunmasuk+"&q[]="+tahunlulus+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_pendformal").html(text);
		});
}
</script>
