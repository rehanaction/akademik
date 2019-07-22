<?php
// model status mahasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('statuspeg'));

	$a_datalegend = mStatuspeg::getList($conn,'');
	$x=1;$y=1;
	$data_status=array();
	foreach ($a_datalegend as $l_row) {
		if($y==4){
			$y=1;
			$x++;
		}
		$data_status[$x][$y]=array('idstatusaktif'=>$l_row['idstatusaktif'],'namastatusaktif'=>$l_row['namastatusaktif']);
		$y++;
	}
	
?>
<fieldset style="background:#E0FFF3; border:1px solid #CCC;">
	<legend>Keterangan</legend>
	<table style="width:100%">
	<? if ($legendstatuspeg){ ?>
		<tr>
			<td colspan="9"><b>Status pegawai :</b></td>
		</tr>
		
		<? foreach ($data_status as $x) { ?>
			<tr>
				<?php foreach($x as $row){?>
					<td><b><?= $row['idstatusaktif']?></b>=<?= $row['namastatusaktif']?> </td>
				<? } ?>
			</tr>
		<? } ?>
		
		<tr>
			<td colspan="6"></td>
		</tr>
		
	<? } if ($legendjkpeg) { ?>
		<tr>
			<td colspan="6"><b>Jenis kelamin :</b></td>
		</tr>
		<tr>
			<td colspan="6"><b>L</b>=Laki-laki &nbsp;&nbsp;&nbsp;&nbsp; <b>P</b>=Perempuan</td>
		</tr>
	<?} ?>
	</table>
</fieldset>
