<?php
		require_once(Route::getUIPath('combo'));
		require_once(Route::getModelPath('prestasiakad'));
		$tingkat=mCombo::tkpelatihan();
        $prestasiakad = array();
		$prestasiakad[] = array('kolom' => 'namaprestasi', 'label' => 'Nama Prestasi','maxlength' => 100, 'size' => 20,'empty'=>true);
		$prestasiakad[] = array('kolom' => 'juara','label' => 'Juara', 'maxlength' => 4, 'size' => 3);
		$prestasiakad[] = array('kolom' => 'tingkat', 'label' => 'Tingkat', 'type'=>'S', 'option' => mCombo::tkpelatihan(),'empty'=>true);
		$prestasiakad[] = array('kolom' => 'tahun', 'label' => 'Tahun','maxlength' => 4, 'size' => 3);
		$data_prestasiakad = mPrestasiAkad::getArray($conn,$r_key);
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="7" class="DataBG">Data Prestasi Akademik</td>
	</tr>
	<tr>
		
		<th id="koderuang">No </th>
		<?php
			foreach($prestasiakad as $datakolom_presakad) {
				if($t_col == $datakolom_presakad['kolom'])
					$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
				else
					$t_sortimg = '';
		?>
		<th id="<?= $datakolom_presakad['kolom'] ?>"><?= $datakolom_presakad['label'] ?> <?= $t_sortimg ?></th>
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
		foreach($data_prestasiakad as $row_prestasiakad) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row_prestasiakad['idprestasiakad'];
			
			//if($t_key == $r_edit and $c_edit) {
				$rowc = Page::getColumnEdit($prestasiakad,'u_pres1'.$t_key.'_','onkeydown="etrUpdate(event)"',$row_prestasiakad);
				
				$a_updatereq = array();
	?>
	<tr align="center" valign="top" class="AlternateBG2" id="u_prestasiAkad<?=$t_key?>" style="display:none">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) {
					if($rowcc['notnull'])
						$a_updatereq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?		} ?>
		<td align="center" colspan=2>
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upPrestasiAkad(this)" style="cursor:pointer">
		</td>
	</tr>
	<?		//}
			//else {
				$rowc = Page::getColumnRow($prestasiakad,$row_prestasiakad);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="prestasiAkad<?=$t_key?>">
		<td><?=$i?></td>
		<td><?= $row_prestasiakad['namaprestasi'] ?></td>
		<td><?= $row_prestasiakad['juara'] ?></td>
		<td><?= $tingkat[$row_prestasiakad['tingkat']] ?></td>
		<td><?= $row_prestasiakad['tahun'] ?></td>
		<?		
				if($c_edit or $c_delete) { ?>
		<td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editPrestasiAkad(this)" style="cursor:pointer">
		<?			}
		?>
		</td>
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delPrestasiAkad(this)" style="cursor:pointer">
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
		<?	$rowc = Page::getColumnEdit($prestasiakad,'i_pres1_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inPrestasiAkad()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>

function delPrestasiAkad(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delPrestasiAkad&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_prestasiakad").html(text);
		});
	}
}
function editPrestasiAkad(elem) {
	//document.getElementById("act").value = "editpendformal";
	//document.getElementById("subkey").value = elem.id;
	//goSubmit();
	$("#prestasiAkad"+elem.id).hide();
	$("#u_prestasiAkad"+elem.id).show();
	
}
function upPrestasiAkad(elem) {
	var namaprestasi=$("#u_pres1"+elem.id+"_namaprestasi").val();
	var juara=$("#u_pres1"+elem.id+"_juara").val();
	var tingkat=$("#u_pres1"+elem.id+"_tingkat").val();
	var tahun=$("#u_pres1"+elem.id+"_tahun").val();
	
	var posted = "act=upPrestasiAkad&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]="+namaprestasi+"&q[]="+juara+"&q[]="+tingkat+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_prestasiakad").html(text);
		});
	
}
function inPrestasiAkad() {
	var namaprestasi=$("#i_pres1_namaprestasi").val();
	var juara=$("#i_pres1_juara").val();
	var tingkat=$("#i_pres1_tingkat").val();
	var tahun=$("#i_pres1_tahun").val();
	var posted = "act=inPrestasiAkad&q[]=<?=$r_key?>&q[]="+namaprestasi+"&q[]="+juara+"&q[]="+tingkat+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_prestasiakad").html(text);
		});
}
</script>
