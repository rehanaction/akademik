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
			<img src="<?=$conf['image_fullpath']?>images/logo.jpg" width="65">
		</td>
		<td valign="middle">
			<!--div class="div_preheader">KEMENTERIAN PENDIDIKAN NASIONAL DAN KEBUDAYAAN</div-->
			<div class="div_header"><b>STIE INABA</b></div>
			<div class="div_headertext">Jl. Soekarno Hatta No.448 Bandung, Jawa Barat 40266 <br/>
			(022) 7563919<br> 
			Website: www.inaba.ac.id, email: info@inaba.ac.id</div>
		</td>
	</tr>
	</thead>
</table>
