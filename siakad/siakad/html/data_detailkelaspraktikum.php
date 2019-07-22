<?php
	
		require_once(Route::getModelPath('detailkelas'));
		require_once(Route::getUIPath('combo'));
		$ruang = mCombo::ruang($conn);
		$jeniskul=array('P'=>'Praktikum');
		$kelompok=array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10');
        $hari=Date::arrayDay();
        $b_input = array();
		$b_input[] = array('kolom' => 'pertemuan', 'label' => 'pertemuan Ke','maxlength' => 2, 'size' => 1,'empty'=>true);
		$b_input[] = array('kolom' => 'tglpertemuan', 'label' => 'Tgl Pertemuan','type' => 'D','notnull'=>true,'add'=>'onchange="setHariDet(this.value)"');
		$b_input[] = array('kolom' => 'nohari', 'label'=>'Hari','type' => 'S', 'option' => $hari, 'empty' => true);
		$b_input[] = array('kolom' => 'jammulai','label' => 'Jam Mulai', 'maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
		$b_input[] = array('kolom' => 'jamselesai', 'label' => 'Jam Selesai','maxlength' => 4, 'size' => 4, 'format' => 'CStr::formatJam','class'=>'ControlStyle jam');
		$b_input[] = array('kolom' => 'koderuang', 'label' => 'Ruang', 'type' => 'S', 'option' => $ruang,'empty'=>true);
		$b_input[] = array('kolom' => 'jeniskul', 'label' => 'Jenis Pertemuan', 'type' => 'S', 'option' => $jeniskul,'empty'=>false);
		$b_input[] = array('kolom' => 'kelompok', 'label'=>'Kelompok','type' => 'S', 'option' => $kelompok, 'empty' => true,'class'=>'ControlStyle kelompok');
		
		$detail_kelas = mDetailKelas::getArray($conn,$r_key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok');
		
?>


<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<td colspan="10" class="DataBG">Data Detail Jadwal Kelas</td>
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
		if($c_edit or $c_delete) { ?>
		<!--th width="50">Edit</th-->
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
		
						
		<td><?= $row['pertemuan'] ?></td>
		<td><?= Date::indoDate($row['tglpertemuan'],false) ?></td>
		<td><?= $hari[$row['nohari']] ?></td>
		<td><?= CStr::formatJam($row['jammulai']) ?></td>
		<td><?= CStr::formatJam($row['jamselesai']) ?></td>
		<td><?= $row['koderuang'] ?></td>
		<td><?= $jeniskul[$row['jeniskul']] ?></td>
		<td><?= $row['kelompok'] ?></td>
		<?		
				if($c_edit or $c_delete) { ?>
		<!--td align="center">
		<?			if($c_edit) { ?>
			<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="editDetailKelas(this)" style="cursor:pointer">
		<?			}
		?>
		</td-->
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
		<td colspan="9" align="center">Data kosong</td>
	</tr>
	<?	}
		if($c_insert) { ?>
	<tr align="center" valign="top" class="LeftColumnBG NoHover">
		
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
 $(function() {
        $.mask.definitions['~'] = "[+-]";
		$("#i_jammulai").mask("99:99");
		$("#i_jamselesai").mask("99:99");
		$(".jam").mask("99:99");
		
    });
function delDetailKelas(elem) {
	if (confirm("yakin akan menghapus data ?")){
		
		var posted = "f=delDetailKelas&q[]="+elem.id+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
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
	var pertemuan=$("#u"+elem.id+"_pertemuan").val();
	var tglpertemuan=$("#u"+elem.id+"_tglpertemuan").val();
	var jammulai=$("#u"+elem.id+"_jammulai").val();
	var jamselesai=$("#u"+elem.id+"_jamselesai").val();
	var koderuang=$("#u"+elem.id+"_koderuang").val();
	var jeniskul=$("#u"+elem.id+"_jeniskul").val();
	var nohari=$("#u"+elem.id+"_nohari").val();
	var kelompok=$("#u"+elem.id+"_kelompok").val();
	
	var posted = "f=upDetailKelas&q[]="+elem.id+"&q[]="+pertemuan+"&q[]="+tglpertemuan+"&q[]="+jammulai+"&q[]="+jamselesai+"&q[]="+koderuang+"&q[]="+jeniskul+"&q[]="+nohari+"&q[]="+kelompok+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailkelas").html(text);
		});
	
}
function inDetailKelas() {
	var pertemuan=$("#i_pertemuan").val();
	var tglpertemuan=$("#i_tglpertemuan").val();
	var jammulai=$("#i_jammulai").val();
	var jamselesai=$("#i_jamselesai").val();
	var koderuang=$("#i_koderuang").val();
	var jeniskul=$("#i_jeniskul").val();
	var nohari=$("#i_nohari").val();
	var kelompok=$("#i_kelompok").val();
	var posted = "f=inDetailKelas&q[]="+pertemuan+"&q[]="+tglpertemuan+"&q[]="+jammulai+"&q[]="+jamselesai+"&q[]="+koderuang+"&q[]="+jeniskul+"&q[]="+nohari+"&q[]="+kelompok+"&q[]=<?=$r_key?>&q[]=<?=$c_insert?>&q[]=<?=$c_edit?>&q[]=<?=$c_delete?>";
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			$(".data_detailkelas").html(text);
		});
}
function setHariDet(val){
	var arr_date=val.split('-');
	var date = new Date(arr_date[2]+' '+arr_date[1]+' '+arr_date[0]);
	var day = date.getDay();
	if(day==0)
		day=7;
	$("#i_nohari").val(day);
}

</script>
