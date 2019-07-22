<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" style=" background: none repeat scroll 0 0 #D8FEEC;border: 1px solid #D0E8D0;border-radius:4px 4px 4px 4px;padding: 5px;">
	<tr valign="top">
		<td width="120"><strong>Nama Mahasiswa</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td width="30%"><?= STRTOUPPER($a_infomhs['nama']) ?></td>
		<td width="120"><strong>Jurusan</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['jenjang'] ?> - <?= $a_infomhs['jurusan'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>N I M</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['nim'] ?></td>
		<td><strong>Periode Masuk</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['namaperiodedaftar'] ?></td>
	</tr>
	<tr valign="top">
		<td><strong>Jenis Kelamin</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['namasex'] ?></td>
		<td><strong>Semester Mhs</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['semestermhs'] ?></td>
	</tr>
</table>