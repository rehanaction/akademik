<?php
		
		require_once(Route::getModelPath('organisasi'));
		
        $organisasi = array();
        $organisasi[] = array('kolom' => 'namaorganisasi', 'label' => 'Nama Organisasi','maxlength' => 100, 'size' => 20,'empty'=>true);
        $organisasi[] = array('kolom' => 'jabatan', 'label' => 'Jabatan','maxlength' => 100, 'size' => 20,'empty'=>true);
		$organisasi[] = array('kolom' => 'tahun','label' => 'Tahun', 'maxlength' => 4, 'size' => 3);
		$data_organisasi = mOrganisasi::getArray($conn,$r_key);
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="7" class="DataBG">Data Pengalaman Organisasi</td>
	</tr>
	<tr>
		
		<th id="koderuang">No </th>
		<?php
			foreach($organisasi as $datakolom_org) {
				if($t_col == $datakolom_org['kolom'])
					$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
				else
					$t_sortimg = '';
		?>
		<th id="<?= $datakolom_org['kolom'] ?>"><?= $datakolom_org['label'] ?> <?= $t_sortimg ?></th>
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
		foreach($data_organisasi as $row_org) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row_org['idorganisasi'];
			
			//if($t_key == $r_edit and $c_edit) {
				$rowc = Page::getColumnEdit($organisasi,'u_org'.$t_key.'_','onkeydown="etrUpdate(event)"',$row_org);
				
				$a_updatereq = array();
	?>
	<tr align="center" valign="top" class="AlternateBG2" id="u_organisasi<?=$t_key?>" style="display:none">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) {
					if($rowcc['notnull'])
						$a_updatereq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?		} ?>
		<td align="center" colspan=2>
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upOrganisasi(this)" style="cursor:pointer">
		</td>
	</tr>
	<?		//}
			//else {
				$rowc = Page::getColumnRow($organisasi,$row_org);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="organisasi<?=$t_key?>">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) { ?>					
		<td><?= $rowcc ?></td>
		<?		}
				if($c_edit or $c_delete) { ?>
		<td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editOrganisasi(this)" style="cursor:pointer">
		<?			}
		?>
		</td>
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delOrganisasi(this)" style="cursor:pointer">
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
		<?	$rowc = Page::getColumnEdit($organisasi,'i_org_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inOrganisasi()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>

function delOrganisasi(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delOrganisasi&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_organisasi").html(text);
		});
	}
}
function editOrganisasi(elem) {
	$("#organisasi"+elem.id).hide();
	$("#u_organisasi"+elem.id).show();
	
}
function upOrganisasi(elem) {
	var namaorganisasi=$("#u_org"+elem.id+"_namaorganisasi").val();
	var jabatan=$("#u_org"+elem.id+"_jabatan").val();
	var tahun=$("#u_org"+elem.id+"_tahun").val();
	
	var posted = "act=upOrganisasi&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]="+namaorganisasi+"&q[]="+jabatan+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_organisasi").html(text);
		});
	
}
function inOrganisasi() {
	var namaorganisasi=$("#i_org_namaorganisasi").val();
	var jabatan=$("#i_org_jabatan").val();
	var tahun=$("#i_org_tahun").val();

	var posted = "act=inOrganisasi&q[]=<?=$r_key?>&q[]="+namaorganisasi+"&q[]="+jabatan+"&q[]="+tahun+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_organisasi").html(text);
		});
}
</script>
