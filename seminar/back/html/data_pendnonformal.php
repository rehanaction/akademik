<?php
		require_once(Route::getUIPath('combo'));
		require_once(Route::getModelPath('pendnonformal'));
		$tingkat=mCombo::tkpelatihan();
        $pend_nonformal = array();
        $pend_nonformal[] = array('kolom' => 'namapelatihan', 'label' => 'Nama Pelatihan','maxlength' => 100, 'size' => 20,'empty'=>true);
		$pend_nonformal[] = array('kolom' => 'tingkatpelatihan', 'label' => 'Tingkat Pelatihan', 'type'=>'S', 'option' => mCombo::tkpelatihan(),'empty'=>true);
		$pend_nonformal[] = array('kolom' => 'tahun','label' => 'Tahun', 'maxlength' => 4, 'size' => 3);
		$pendidikan_nonformal = mPendNonFormal::getArray($conn,$r_key);
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="7" class="DataBG">Data Riwayat Pendidikan Non Formal</td>
	</tr>
	<tr>
		
		<th id="koderuang">No </th>
		<?php
			foreach($pend_nonformal as $datakolom_pendnf) {
				if($t_col == $datakolom_pendnf['kolom'])
					$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
				else
					$t_sortimg = '';
		?>
		<th id="<?= $datakolom_pendnf['kolom'] ?>"><?= $datakolom_pendnf['label'] ?> <?= $t_sortimg ?></th>
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
		foreach($pendidikan_nonformal as $row_pendnonformal) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row_pendnonformal['idpendnonformal'];
			
			//if($t_key == $r_edit and $c_edit) {
				$rowc = Page::getColumnEdit($pend_nonformal,'u_pend0'.$t_key.'_','onkeydown="etrUpdate(event)"',$row_pendnonformal);
				
				$a_updatereq = array();
	?>
	<tr align="center" valign="top" class="AlternateBG2" id="u_pendnonformal<?=$t_key?>" style="display:none">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) {
					if($rowcc['notnull'])
						$a_updatereq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?		} ?>
		<td align="center" colspan=2>
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upPendidikanNonformal(this)" style="cursor:pointer">
		</td>
	</tr>
	<?		//}
			//else {
				$rowc = Page::getColumnRow($pend_nonformal,$row_pendnonformal);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="pendnonformal<?=$t_key?>">
		<td><?=$i?></td>
							
		<td><?= $row_pendnonformal['namapelatihan'] ?></td>
		<td><?= $tingkat[$row_pendnonformal['tingkatpelatihan']] ?></td>
		<td><?= $row_pendnonformal['tahun'] ?></td>
		<?		
				if($c_edit or $c_delete) { ?>
		<td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editPendidikanNonformal(this)" style="cursor:pointer">
		<?			}
		?>
		</td>
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delPendidikanNonformal(this)" style="cursor:pointer">
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
		<?	$rowc = Page::getColumnEdit($pend_nonformal,'i_pend0_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inPendidikanNonformal()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>

function delPendidikanNonformal(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delpendNonformal&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_pendnonformal").html(text);
		});
	}
}
function editPendidikanNonformal(elem) {
	
	$("#pendnonformal"+elem.id).hide();
	$("#u_pendnonformal"+elem.id).show();
	
}
function upPendidikanNonformal(elem) {
	var namapelatihan=$("#u_pend0"+elem.id+"_namapelatihan").val();
	var tingkatpelatihan=$("#u_pend0"+elem.id+"_tingkatpelatihan").val();
	var tahun=$("#u_pend0"+elem.id+"_tahun").val();
	
	var posted = "act=uppendNonformal&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]="+namapelatihan+"&q[]="+tingkatpelatihan+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_pendnonformal").html(text);
		});
	
}
function inPendidikanNonformal() {
	var namapelatihan=$("#i_pend0_namapelatihan").val();
	var tingkatpelatihan=$("#i_pend0_tingkatpelatihan").val();
	var tahun=$("#i_pend0_tahun").val();

	var posted = "act=inpendNonformal&q[]=<?=$r_key?>&q[]="+namapelatihan+"&q[]="+tingkatpelatihan+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_pendnonformal").html(text);
		});
}
</script>
