<?php
	if(!(empty($a_filtercombo) and empty($r_page))) {
?>
<center>
	<div class="filterTable"<?php echo empty($p_tbwidth) ? '' : ' style="width:'.($p_tbwidth-12).'px"' ?>>
		<table width="100%" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<td valign="top" width="50%">
					<table width="100%" cellspacing="0" cellpadding="4">
						<? foreach($a_filtercombo as $t_filter) { ?>
						<tr>		
							<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
							<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
						</tr>
						<? } ?>
					</table>
				</td>
				<?	}
					
					/**********************/
					/* COMBO FILTER KOLOM */
					/**********************/
					
					if(!empty($r_page)) {
						$r_filterstr = Page::getFilterAll();
				?>
				<td valign="top" width="50%">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="40" style="white-space:nowrap"><strong>Cari :</strong></td>
							<td style="display:none"><?= uCombo::listColumn($a_kolom) ?></td>
							<? /* <td width="210"><input name="tfilter" id="tfilter" class="ControlStyle" size="25" onkeydown="etrFilterCombo(event)" type="text"></td>
							<td width="40"><input type="button" value="Filter" class="ControlStyle" onClick="goFilterCombo()"></td> */ ?>
							<td width="200"><input name="tfilter" id="tfilter" class="ControlStyle" size="30" onkeydown="etrFilterAll(event)" type="text" value="<?= $r_filterstr ?>"></td>
							<td><input type="button" value="Cari" class="ControlStyle" onClick="goFilterAll()"></td>
							<? /* <td><input type="button" value="Refresh" class="ControlStyle" onClick="goRefresh()"></td> */ ?>
						</tr>
					</table>
					<?	/********************/
						/* INFORMASI FILTER */
						/********************/
						
						/* if(!empty($a_datafilter)) { ?>
					<table cellpadding="4" cellspacing="0" class="LiteHeaderBG">
					<?	$i = 0;
						foreach($a_datafilter as $t_idx => $t_data) { ?>
						<tr>
							<td width="30" style="white-space:nowrap"><?= $t_data['label'] ?></td>
							<td align="center" width="5">:</td>
							<td><?= $t_data['str'] ?></td>
							<td valign="top" align="right"><u title="Hapus Filter" id="remfilter" style="color:#3300FF;cursor:pointer;text-decoration:none" onclick="goRemoveFilter(<?= $i++ ?>)">x</u></td>
						</tr>
					<?	} ?>
					</table>
					<?	} */ ?>
				</td>
			<?	} ?>
			</tr>
		</table>
	</div>
	<?	if(!empty($r_page) and false) { ?>
	<div class="Break"></div>
	<table width="<?= $p_tbwidth ?>">
		<tr>
			<td>Menampilkan <?= $r_page > 1 ? 'halaman '.CStr::formatNumber($r_page).' dari ' : '' ?><?= CStr::formatNumber($p_rownum) ?> data <?= empty($r_filterstr) ? '' : 'hasil pencarian "'.$r_filterstr.'" ' ?>(<?= CStr::formatNumber($p_time,4) ?> detik)</td>
		</tr>
	</table>
	<?	} ?>
</center>
<br>
<?php
	}
?>
