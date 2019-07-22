<table class="tb_head" width="<?= $p_tbwidth ?>" >  
    
       
	<!--$thsms = <?= SUBSTR($a_infokelas['periode'],0,4)?> - <?= SUBSTR($a_infokelas['periode'],4,2)?>;-->
	<tr valign="top">
		<td><strong>SEMESTER</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= Akademik::getNamaPeriode($a_infokelas['periode']) ?> </td>
	</tr

	<tr valign="top">
		<td width="110"><strong>Nama Mata Kuliah </strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td width="30%"><?= $a_infokelas['namamk'] ?></td>
		<td valign="top" rowspan="3" width="50"><strong>Dosen</strong></td>
		<td valign="top" rowspan="3" width="10" align="center"><strong>:</strong></td>
		<td valign="top" rowspan="3"><?= $a_infokelas['pengajar'] ?></td>
	</tr>
  
  
	<tr valign="top">
		<td><strong>Kode MK dan Seksi</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infokelas['kodemk'] ?> (<?= $a_infokelas['kelasmk'] ?>)</td>
	</tr>
  
	<tr valign="top">
		<td><strong>Jadwal dan Ruang</strong></td>
		<td align="center"><strong>:</strong></td>
		<td>
			<?= $a_infokelas['jadwal'] ?> (<?= $a_infokelas['koderuang'] ?>)
			<? if(!empty($a_infokelas['nohari2'])) { ?>
			<br><?= $a_infokelas['jadwal2'] ?>
			<? } ?>
		</td>
	</tr>
  
  
	<?php if(!empty($infomhs)){ ?>
	<tr>
		<td><strong>Mahasiswa</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $infomhs?></td>
	</tr>
	<?php } ?>
</table>
