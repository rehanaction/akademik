<?php
// model status mahasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('statusmhs'));
	require_once(Route::getModelPath('sistemkuliah'));

	$a_datalegend = mStatusmhs::getList($conn,'');
	
	$sistemKuliah=mSistemkuliah::getArray($conn);
?>
<fieldset style="background:#E0FFF3; border:1px solid #CCC;">
	<legend> Keterangan </legend>
	<table>
	<? if ($a_datalegend){ ?>
		<tr>
			<td colspan="9"><b>Status mahasiswa :</b></td>
		</tr>
		<tr>
		<? foreach ($a_datalegend as $l_row) { ?>
			<td><b><?= $l_row['statusmhs']?></b>=<?= $l_row['namastatus']?> &nbsp;&nbsp;&nbsp;&nbsp; </td>
			<? } ?>
		</tr>
		<tr>
			<td colspan="6"></td>
		</tr>
		
	<? } if ($legendjkmhs) { ?>
		<tr>
			<td colspan="6"><b>Jenis kelamin :</b></td>
		</tr>
		<tr>
			<td colspan="6"><b>L</b>=Laki-laki &nbsp;&nbsp;&nbsp;&nbsp; <b>P</b>=Perempuan</td>
		</tr>
	<? } if ($sistemKuliah){ ?>
		<tr>
			<td colspan="<?=count($sistemKuliah)*2?>"><b>Basis mahasiswa :</b></td>
		</tr>
		<tr>
		<? foreach ($sistemKuliah as $key=>$sistem) { ?>
			<td colspan="2" valign="top"><b><?= $key?></b>=<?= $sistem?> </td>
			<? } ?>
		</tr>
		<tr>
			<td colspan="<?=count($sistemKuliah)*2?>"></td>
		</tr>
	<?} ?>
	</table>
</fieldset>
