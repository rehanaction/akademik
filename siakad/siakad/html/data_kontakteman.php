<?php
		
		require_once(Route::getModelPath('kontakteman'));
		
        $b_input = array();
		$b_input[] = array('kolom' => 'nimteman', 'label' => 'Nim teman','maxlength' => 20, 'size' => 15);
		$b_input[] = array('kolom' => 'namateman', 'label' => 'Nama teman','maxlength' => 20, 'size' => 15);
		$b_input[] = array('kolom' => 'telp_teman', 'label' => 'Telepon Teman','maxlength' => 12, 'size' => 10);
		$b_input[] = array('kolom' => 'hp_teman', 'label' => 'Handphone Teman','maxlength' => 12, 'size' => 10);
		$kontak_teman = mKontakTeman::getArray($conn,$r_key);
		
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="10" class="DataBG">Data Kontak Teman</td>
	</tr>
	<tr>
		
		
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
		if($c_delete) { ?>
		
		<th width="50">Operasi</th>
		<?	} ?>
	</tr>
	<?	/********/
		/* ITEM */
		/********/
		
		$i = 0;
		foreach($kontak_teman as $rowx) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $rowx['nim'].'|'.$rowx['nimteman'];

				$rowc = Page::getColumnRow($b_input,$rowx);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="detail<?=$t_key?>">
		
						
		<td><?= $rowx['nimteman'] ?></td>
		<td><?= $rowx['namateman'] ?></td>
		<td><?= $rowx['telp_teman'] ?></td>
		<td><?= $rowx['hp_teman'] ?></td>
		
		</td>
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delDatateman(this)" style="cursor:pointer">
		<?			} ?>
		</td>
		
	</tr>
	<?		//}
		}
		if($i == 0) {
	?>
	<tr>
		<td colspan="9" align="center">Data kosong</td>
	</tr>
	<?	}
		if($c_insert) { ?>
	<tr align="center" valign="top" class="LeftColumnBG NoHover">
		<td colspan="4">
			<?= UI::createTextBox('i_namateman','','ControlStyle',70,70, true, '', 'Masukkan Nim / Nama') ?>
			<input type="hidden" id="i_nimteman" name="i_nimteman">	
		</td>
		<td align="center">
			<img title="Tambah Data" src="images/disk.png" onclick="inDataTeman()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>
$(document).ready(function() {
$("#i_namateman").xautox({strpost: "f=acmahasiswa", targetid: "i_nimteman"});
});
function delDatateman(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "f=delDatateman&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_kontakteman").html(text);
		});
	}
}


function inDataTeman() {
	var nimteman=$("#i_nimteman").val();
	var posted = "f=inDataTeman&q[]="+nimteman+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_kontakteman").html(text);
		});
}
</script>
