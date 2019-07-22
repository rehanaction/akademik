<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" style=" background: none repeat scroll 0 0 #D8FEEC;border: 1px solid #D0E8D0;border-radius:4px 4px 4px 4px;padding: 5px;">
	<tr valign="top">
		<td width="110"><strong>Nama Matakuliah</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td width="30%"><?= $a_infokelas['namamk'] ?></td>
		<td valign="top" rowspan="4" width="50"><strong>Dosen</strong></td>
		<td valign="top" rowspan="4" width="10" align="center"><strong>:</strong></td>
		<td valign="top" rowspan="4"><?= $a_infokelas['pengajar'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Kode MK & Kelas</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= substr($a_infokelas['kodemk'],0,3).' '.substr($a_infokelas['kodemk'],3) ?> (<?= $a_infokelas['kelasmk'] ?>)</td>
	</tr>
	<tr valign="top">
		<td><strong>Jadwal & Ruang</strong></td>
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
</table>
