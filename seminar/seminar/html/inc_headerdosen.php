<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0">
	<tr valign="top">
		<td width="50"><strong>Dosen</strong></td>
		<td width="10" align="center"><strong>:</strong></td>
		<td width="50%"><?= $r_dosen ?></td>
		<td>
		<table>
		<?php if(!empty($a_combodosen)){ ?>
		<? foreach($a_combodosen as $t_filterdosen) { ?>
			<tr>		
				<td width="50" style="white-space:nowrap"><strong><?= $t_filterdosen['label'] ?> </strong></td>
				<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filterdosen['width'].'"' ?>><strong> : </strong><?= $t_filterdosen['combo'] ?></td>		
			</tr>
		<? } ?>
		<?php }else {?>
			<tr>
				<td width="60"><strong>Periode</strong></td>
				<td><?=Akademik::getNamaPeriode()?></td>
			</tr>
		<?php } ?>
		</table>
		</td>
	</tr>
</table>
