<div >
<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" style=" background: none repeat scroll 0 0 #D8FEEC;border: 1px solid #D0E8D0;border-radius:4px 4px 4px 4px;padding: 5px;">
<?	if(!Akademik::isMhs()) { ?>
	<tr valign="top" style="height:35px">
		<td><strong>Pilih Mhs</strong></td>
		<td align="center"><strong>:</strong></td>
		<td colspan="4">
			<?= UI::createTextBox('mahasiswa',$a_infomhs['nim'].' - '.$a_infomhs['nama'],'ControlStyle',70,70) ?>
			<input type="hidden" id="npmtemp" name="npmtemp" value="<?= $r_nim ?>">
			<input type="button" value="Tampilkan" onclick="goSwitch()">
		</td>
	</tr>
<?	} ?>
	<tr valign="top" style="height:25px;font-size:14px">
		<td><strong>Mahasiswa</strong></td>
		<td align="center"><strong>:</strong></td>
		<td colspan="4">
			<?	if(!Akademik::isMhs()) { ?>
			<img src="images/rfirst.png" style="cursor:pointer" onclick="goFirstNIM()">&nbsp;
			<img src="images/rprev.png" style="cursor:pointer" onclick="goPrevNIM()"> &nbsp;
			<?	} ?>
			<?= $a_infomhs['nim'] ?> - <?= $a_infomhs['nama'] ?>
			<?	if(!Akademik::isMhs()) { ?>
			&nbsp; <img src="images/rnext.png" style="cursor:pointer" onclick="goNextNIM()">
			&nbsp;<img src="images/rlast.png" style="cursor:pointer" onclick="goLastNIM()">
			<?	} ?>
		</td>
	</tr>
	<tr valign="top">
		<td width="150"><strong>Fakultas</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td width="30%"><?= $a_infomhs['fakultas'] ?></td>
		<td><strong>Dosen PA</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['dosenwali'] ?></td>
	</tr>
	<tr valign="top">
		<td width="150"><strong>Jurusan</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['jurusan'] ?></td>
		<td><strong>NIP Dosen</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['nipdosenwali'] ?></td>
	</tr>
	<tr valign="top">
		<td width="150"><strong>Jenis Kelamin</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['namasex'] ?></td>
		<td><strong>NIDN Dosen</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['nidn'] ?></td>
	</tr>
	<tr>
		<!--td width="150"><strong>Kur / Smt / SKS</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?= $a_infomhs['kurikulum'] ?> / <?= $a_infomhs['semestermhs'] ?> / <?= $a_infomhs['skslulus'] ?></td-->
		<td width="150"><strong>Basis</strong></td>
		<td align="center"><strong>:</strong></td>
		<td><?=$a_infomhs['namasistemkuliah']?></td>
		<td width="120"><strong>Batas SKS</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		
		<td><?= $a_infomhs['batassks'] ?></td>
	</tr>
</table>
<div>
