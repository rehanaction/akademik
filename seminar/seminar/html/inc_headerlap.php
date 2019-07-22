<?php
	$t_fakultas = strtoupper($row['fakultas']);
	if(!empty($t_fakultas))
		$t_fakultas = str_replace('FAK. ','',$t_fakultas);
?>

<br>
<table class="tab_header" width="<?= $p_tbwidth ?>">
<thead>
	<tr>
		<td width="70" align="center">
			<img src="<?=$conf['image_fullpath']?>images/esaunggul.png" width="65">
		</td>
		<td valign="middle">
			<!--div class="div_preheader">KEMENTERIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</div-->
			<div class="div_header">UNIVERSITAS ESA UNGGUL</div>
			<? if(!empty($t_fakultas)) { ?>
			<div class="div_header"><?= $t_fakultas ?></div>
			<? } ?>
			<div class="div_headertext">Jalan Arjuna Utara No.9, Kebon Jeruk - Jakarta Barat 11510 <br/>
			021 - 5674223 (hunting) 021- 5682510 (direct) Fax : 021 - 5674248<br> 
			Website: www.esaunggul.ac.id, email: info@esaunggul.ac.id</div>
		</td>
	</tr>
	</thead>
</table>
