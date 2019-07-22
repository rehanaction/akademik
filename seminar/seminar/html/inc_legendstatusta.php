<?php
// model status mahasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('statusmhs'));

	$a_datalegend = mStatusmhs::getList($conn,'');
 
?>
<fieldset style="background:#E0FFF3; border:1px solid #CCC;width:500px">
	<legend> Keterangan </legend>
	<table >
		<tr>
			<td colspan="9"><b>Status Skripsi :</b></td>
		</tr>
		<tr>
			<td>P = Pengajuan&nbsp;&nbsp;&nbsp;&nbsp;S = Disetujui&nbsp;&nbsp;&nbsp;&nbsp;T = Ditolak </td>
		</tr>
		<tr>
			<td colspan="6"></td>
		</tr>
	</table>
</fieldset>