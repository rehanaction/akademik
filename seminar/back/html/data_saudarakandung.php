<?php
	
		require_once(Route::getModelPath('saudarakandung'));
		require_once(Route::getModelPath('combo'));
		$status_keluarga=mCombo::statusKeluarga();
		$pendidikan=mCombo::pendidikan($conn);
		$propinsi=mCombo::propinsi($conn);
		$kota=mCombo::getKota();
        $s_kandung = array();
		$s_kandung[] = array('kolom' => 'namasaudara', 'label' => 'Nama Saudara','maxlength' => 100, 'size' => 15,'notnull'=>true);
		$s_kandung[] = array('kolom' => 'kodepropinsisaudara', 'label' => 'Propinsi Lahir','type' => 'S', 'option'=>$propinsi,'empty'=>false,'add' => 'onchange="loadKotaSaudara()"','class'=>'u_kodepropinsisaudara ControlStyle');
		$s_kandung[] = array('kolom' => 'kodekotasaudara', 'label' => 'Kota Lahir','type' => 'S', 'option' => $kota,'empty'=>true,'class'=>'u_kodekotasaudara ControlStyle');
		$s_kandung[] = array('kolom' => 'tgllahirsaudara','label' => 'Tgl Lahir', 'type' => 'D','notnull'=>true);
		$s_kandung[] = array('kolom' => 'kodependidikan', 'label' => 'Pendidikan','type'=>'S', 'option' => $pendidikan,'empty'=>true);
		$s_kandung[] = array('kolom' => 'status', 'label' => 'Status','type'=>'S', 'option' => $status_keluarga,'empty'=>true);
		$saudara_kandung = mSaudaraKandung::getArray($conn,$r_key);
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="9" class="DataBG">Data Saudara Kandung</td>
	</tr>
	<tr>
		
		<th id="koderuang">No </th>
		<?php
			foreach($s_kandung as $datakolom) {
				if($t_col == $datakolom['kolom'])
					$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
				else
					$t_sortimg = '';
		?>
		<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
		<?	} ?>
		<?	
		if($c_edit or $c_delete) { ?>
		<!--th width="50">Edit</th-->
		<th width="50">Hapus</th>
		<?	} ?>
	</tr>
	<?	/********/
		/* ITEM */
		/********/
		
		$i = 0;
		foreach($saudara_kandung as $row_sdr) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			$t_key = $row_sdr['idsaudara'];
			
			//if($t_key == $r_edit and $c_edit) {
				$rowc = Page::getColumnEdit($s_kandung,'u'.$t_key.'_','onkeydown="etrUpdate(event)"',$row_sdr);
				
				$a_updatereq = array();
	?>
	<tr align="center" valign="top" class="AlternateBG2" id="u_saudarakandung<?=$t_key?>" style="display:none">
		<td><?=$i?></td>
		<?		foreach($rowc as $rowcc) {
					if($rowcc['notnull'])
						$a_updatereq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?		} ?>
		<td align="center" colspan=2>
			<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="upSaudaraKandung(this)" style="cursor:pointer">
		</td>
	</tr>
	<?		//}
			//else {
				$rowc = Page::getColumnRow($s_kandung,$row_sdr);
	?>
	<tr align="center" valign="top" class="<?= $rowstyle ?>" id="saudarakandung<?=$t_key?>">
		<td><?=$i?></td>
						
		<td><?= $row_sdr['namasaudara'] ?></td>
		<td><?= $propinsi[$row_sdr['kodepropinsisaudara']] ?></td>
		<td><?= $kota[$row_sdr['kodekotasaudara']] ?></td>
		<td><?= Date::indoDate($row_sdr['tgllahirsaudara'],true) ?></td>
		<td><?= $pendidikan[$row_sdr['kodependidikan']] ?></td>
		<td><?= $status_keluarga[$row_sdr['status']] ?></td>
		<?		
				if($c_edit or $c_delete) { ?>
		<!--td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editSaudaraKandung(this)" style="cursor:pointer">
		<?			}
		?>
		</td-->
		<td align="center">
		<?			if($c_delete) { ?>
			<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="delSaudaraKandung(this)" style="cursor:pointer">
		<?			} ?>
		</td>
		<?		} ?>
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
		<td>&nbsp;</td>
		<?	$rowc = Page::getColumnEdit($s_kandung,'i_','onkeydown="etrInsert(event)"');
			
			$a_insertreq = array();
			foreach($rowc as $rowcc) {
				if($rowcc['notnull'])
					$a_insertreq[] = $rowcc['id'];
		?>					
		<td><?= $rowcc['input'] ?></td>
		<?	} ?>
		<td align="center" colspan=2>
			<img title="Tambah Data" src="images/disk.png" onclick="inSaudaraKandung()" style="cursor:pointer">
		</td>
	</tr>
	<?	} ?>
</table>

<script>
$(document).ready(function() {
	loadKotaSaudara();
});
function loadKotaSaudara() {

<?php
	
	
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.i_kodepropinsisaudara.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        //$kota = array_values($kota);
    
	$content = "document.getElementById('i_kodekotasaudara').innerHTML = \"";
	while($datakota= $kota->FetchRow())
	{
		//if($datakota['kodekota']==trim(Page::getDataValue($row,'kodekotasaudara')))
	   // $content .= "<option selected value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
		//else
	    $content .= "<option value='".$datakota['kodekota']."'>".$datakota['namakota']."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
	
    }
    
?>

}
function delSaudaraKandung(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "act=delSaudaraKandung&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_saudarakandung").html(text);
		});
	}
}
function editSaudaraKandung(elem) {
	//document.getElementById("act").value = "editpendformal";
	//document.getElementById("subkey").value = elem.id;
	//goSubmit();
	$("#saudarakandung"+elem.id).hide();
	$("#u_saudarakandung"+elem.id).show();
	
}
function upSaudaraKandung(elem) {
	var namasaudara=$("#u"+elem.id+"_namasaudara").val();
	var kodepropinsisaudara=$("#u"+elem.id+"_kodepropinsisaudara").val();
	var kodekotasaudara=$("#u"+elem.id+"_kodekotasaudara").val();
	var tgllahirsaudara=$("#u"+elem.id+"_tgllahirsaudara").val();
	var kodependidikan=$("#u"+elem.id+"_kodependidikan").val();
	var status=$("#u"+elem.id+"_status").val();
	
	var posted = "act=upSaudaraKandung&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]="+namasaudara+"&q[]="+kodepropinsisaudara+"&q[]="+kodekotasaudara+"&q[]="+tgllahirsaudara+"&q[]="+kodependidikan+"&q[]="+status+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_saudarakandung").html(text);
		});
	
}
function inSaudaraKandung() {
	var namasaudara=$("#i_namasaudara").val();
	var kodepropinsisaudara=$("#i_kodepropinsisaudara").val();
	var kodekotasaudara=$("#i_kodekotasaudara").val();
	var tgllahirsaudara=$("#i_tgllahirsaudara").val();
	var kodependidikan=$("#i_kodependidikan").val();
	var status=$("#i_status").val();
	var posted = "act=inSaudaraKandung&q[]=<?=$r_key?>&q[]="+namasaudara+"&q[]="+kodepropinsisaudara+"&q[]="+kodekotasaudara+"&q[]="+tgllahirsaudara+"&q[]="+kodependidikan+"&q[]="+status+"&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_saudarakandung").html(text);
		});
}
</script>
