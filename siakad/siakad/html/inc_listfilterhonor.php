<?php
	if(!(empty($a_filtercombo) and empty($r_page))) {
?>
<center>
	<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
		<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
						if(!empty($a_filtercombo)) {
						?>
						<td valign="top" width="50%">
							<table cellspacing="0" cellpadding="4" >
								<? foreach($a_filtercombo as $t_filter) { ?>
								<tr>		
									<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
									<td ><strong> : </strong><?= $t_filter['combo'] ?></td>
									<? if($c_delete and $t_filter['delete']) { ?>
									<td  align="center"><img id="<?= $t_key ?>" title="Hapus Data Per nomor Pengajuan" src="images/delete.png" onclick="<?= $t_filter['function'] ?>" style="cursor:pointer"></td>
									<? } ?>		
								</tr>
								<? } ?>
							</table>
						</td>
						<?	}
					
					/**********************/
					/* COMBO FILTER KOLOM */
					/**********************/
					
					if(!empty($r_page) or $forcesearch) {
						$r_filterstr = Page::getFilterAll();
						if(!$a_addcolfilter)
							$a_addcolfilter=array();
						
				?>
				<td valign="top" width="50%">
					<table width="100%" cellspacing="0" cellpadding="4">
						<tr>
							<td width="40" style="white-space:nowrap"><strong>Cari :</strong></td>
							<td style="display:none"><?= uCombo::listColumn($a_kolom,'',$a_addcolfilter) ?></td>
							<td width="260"><input name="tfilter" id="tfilter" class="ControlStyle" size="40" onkeydown="etrFilterAll(event)" type="text" value="<?= $r_filterstr ?>"></td>
							<td><input type="button" value="Cari" class="ControlStyle" onClick="goFilterAll()"></td>
						
						</tr>
					</table>
					
				</td>
			<?	} ?>
			</tr>
		</table>
	</div>
	<br>
	
	
	<?	if(!empty($r_page)) { ?>
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
