<?php
	
		require_once(Route::getModelPath('detailkelas'));
		$ruang = mCombo::ruang($conn);
        $b_input = array();
		$b_input[] = array('kolom' => 'pertemuan', 'label' => 'pertemuan Ke','maxlength' => 2, 'size' => 1,'empty'=>true);
		$b_input[] = array('kolom' => 'tglpertemuan', 'label' => 'Tgl Pertemuan','maxlength' => 10, 'size' => 10,'notnull'=>true);
		$b_input[] = array('kolom' => 'jammulai','label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
		$b_input[] = array('kolom' => 'jamselesai', 'label' => 'Jam Selesai','maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam');
		$a_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type' => 'S', 'option' => $ruang);
		$detail_kelas = mPendFormal::getArray($conn,$r_key);
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="7" class="DataBG">Data Detail Jadwal Kelas</td>
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
		<th width="50">Edit</th>
		<th width="50">Hapus</th>
		<?	} ?>
	</tr>
	<?	/********/
		/* ITEM */
		/********/
		
		$i = 0;
		foreach($detail_kelas as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row['iddetailkelas'];
			
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
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upDetailKelas(this)" style="cursor:pointer">
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
				if($c_edit or $c_delete) { ?>
		<td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editDetailKelas(this)" style="cursor:pointer">
		<?			}
		?>
		</td>
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delDetailKelas(this)" style="cursor:pointer">
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
		<?	$rowc = Page::getColumnEdit($b_input,'i_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inDetailKelas()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>

function delDetailKelas(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delpendformal&q[]="+elem.id+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailkelas").html(text);
		});
	}
}
function editDetailKelas(elem) {
	//document.getElementById("act").value = "editpendformal";
	//document.getElementById("subkey").value = elem.id;
	//goSubmit();
	$("#detail"+elem.id).hide();
	$("#u_detail"+elem.id).show();
	
}
function upDetailKelas(elem) {
	var namapend=$("#u"+elem.id+"_pertemuan").val();
	var tempatpend=$("#u"+elem.id+"_tglpertemuan").val();
	var tahunmasuk=$("#u"+elem.id+"_jammulai").val();
	var tahunlulus=$("#u"+elem.id+"_jamselesai").val();
	var tahunlulus=$("#u"+elem.id+"_koderuang").val();
	
	var posted = "act=uppendformal&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]="+namapend+"&q[]="+tempatpend+"&q[]="+tahunmasuk+"&q[]="+tahunlulus+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailkelas").html(text);
		});
	
}
function inDetailKelas() {
	var namapend=$("#i_pertemuan").val();
	var tempatpend=$("#i_tglpertemuan").val();
	var tahunmasuk=$("#i_jammulai").val();
	var tahunlulus=$("#i_jamselesai").val();
	var tahunlulus=$("#i_koderuang").val();
	var posted = "act=inpendformal&q[]=<?=$r_key?>&q[]="+namapend+"&q[]="+tempatpend+"&q[]="+tahunmasuk+"&q[]="+tahunlulus+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailkelas").html(text);
		});
}
</script>
