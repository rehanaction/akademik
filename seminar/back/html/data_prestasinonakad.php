<?php
		require_once(Route::getUIPath('combo'));
		require_once(Route::getModelPath('prestasinonakad'));
		$tingkat=mCombo::tkpelatihan();
        $prestasinonakad = array();
		$prestasinonakad[] = array('kolom' => 'namaprestasi', 'label' => 'Nama Prestasi','maxlength' => 100, 'size' => 20,'empty'=>true);
		$prestasinonakad[] = array('kolom' => 'juara','label' => 'Juara', 'maxlength' => 4, 'size' => 3);
		$prestasinonakad[] = array('kolom' => 'tingkat', 'label' => 'Tingkat', 'type'=>'S', 'option' => mCombo::tkpelatihan(),'empty'=>true);
		$prestasinonakad[] = array('kolom' => 'tahun', 'label' => 'Tahun','maxlength' => 4, 'size' => 3);
		$data_prestasinonakad = mPrestasiNonAkad::getArray($conn,$r_key);
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="7" class="DataBG">Data Prestasi Non Akademik</td>
	</tr>
	<tr>
		
		<th id="koderuang">No </th>
		<?php
			foreach($prestasinonakad as $datakolom_presnakad) {
				if($t_col == $datakolom_presnakad['kolom'])
					$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
				else
					$t_sortimg = '';
		?>
		<th id="<?= $datakolom_presnakad['kolom'] ?>"><?= $datakolom_presnakad['label'] ?> <?= $t_sortimg ?></th>
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
		foreach($data_prestasinonakad as $row_prestasinonakad) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row_prestasinonakad['idprestasinonakad'];
			
			//if($t_key == $r_edit and $c_edit) {
				$rowc = Page::getColumnEdit($prestasinonakad,'u_pres1'.$t_key.'_','onkeydown="etrUpdate(event)"',$row_prestasinonakad);
				
				$a_updatereq = array();
	?>
	<tr align="center" valign="top" class="AlternateBG2" id="u_prestasinonakad<?=$t_key?>" style="display:none">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) {
					if($rowcc['notnull'])
						$a_updatereq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?		} ?>
		<td align="center" colspan=2>
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upPrestasiNonAkad(this)" style="cursor:pointer">
		</td>
	</tr>
	<?		//}
			//else {
				$rowc = Page::getColumnRow($prestasinonakad,$row_prestasinonakad);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="prestasinonakad<?=$t_key?>">
		<td><?=$i?></td>
		<td><?= $row_prestasinonakad['namaprestasi'] ?></td>
		<td><?= $row_prestasinonakad['juara'] ?></td>
		<td><?= $tingkat[$row_prestasinonakad['tingkat']] ?></td>
		<td><?= $row_prestasinonakad['tahun'] ?></td>
		<?		
				if($c_edit or $c_delete) { ?>
		<td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editPrestasiNonAkad(this)" style="cursor:pointer">
		<?			}
		?>
		</td>
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delPrestasiNonAkad(this)" style="cursor:pointer">
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
		<?	$rowc = Page::getColumnEdit($prestasinonakad,'i_pres0_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inPrestasiNonAkad()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>

function delPrestasiNonAkad(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delPrestasiNonAkad&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_prestasinonakad").html(text);
		});
	}
}
function editPrestasiNonAkad(elem) {
	//document.getElementById("act").value = "editpendformal";
	//document.getElementById("subkey").value = elem.id;
	//goSubmit();
	$("#prestasinonakad"+elem.id).hide();
	$("#u_prestasinonakad"+elem.id).show();
	
}
function upPrestasiNonAkad(elem) {
	var namaprestasi=$("#u_pres1"+elem.id+"_namaprestasi").val();
	var juara=$("#u_pres1"+elem.id+"_juara").val();
	var tingkat=$("#u_pres1"+elem.id+"_tingkat").val();
	var tahun=$("#u_pres1"+elem.id+"_tahun").val();
	
	var posted = "act=upPrestasiNonAkad&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]="+namaprestasi+"&q[]="+juara+"&q[]="+tingkat+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_prestasinonakad").html(text);
		});
	
}
function inPrestasiNonAkad() {
	var namaprestasi=$("#i_pres0_namaprestasi").val();
	var juara=$("#i_pres0_juara").val();
	var tingkat=$("#i_pres0_tingkat").val();
	var tahun=$("#i_pres0_tahun").val();
	var posted = "act=inPrestasiNonAkad&q[]=<?=$r_key?>&q[]="+namaprestasi+"&q[]="+juara+"&q[]="+tingkat+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_prestasinonakad").html(text);
		});
}
</script>
