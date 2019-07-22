<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" style=" background: none repeat scroll 0 0 #D8FEEC;border: 1px solid #D0E8D0;border-radius:4px 4px 4px 4px;padding: 5px;">
	<tr valign="top">
		<td width="150"><strong>Nama Mata Kuliah</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td width="30%"><?= $a_infokelas['namamk'] ?></td>
		<td valign="top" width="200"><strong>Dosen</strong></td>
		<td valign="top" width="10" align="center"><strong>:</strong></td>
		<td valign="top"><?= $a_infokelas['pengajar'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Kode MK dan Kelas</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= substr($a_infokelas['kodemk'],0,3).' '.substr($a_infokelas['kodemk'],3) ?> (<?= $a_infokelas['kelasmk'] ?>)</td>
		<td><strong>Nama Mahasiswa</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['nim'] ?> - <?= $a_infomhs['nama'] ?></td>
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
	<tr valign="top">
		<td><strong>Jumlah Mahasiswa</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infokelas['jumlahpeserta']?></td>
	</tr>     
	<tr valign="top">
		<td><strong>Semester</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= STRTOUPPER(Akademik::getNamaPeriode($a_infokelas['periode']))?></td>
	</tr>
</table>
