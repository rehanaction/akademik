<?php require_once('inc_listfilter.php'); ?>
	<?	if(!empty($p_postmsg)) { ?>
	<center>
	<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
		<?= $p_postmsg ?>
	</div>
	</center>
	<div class="Break"></div>
	<?	} ?>
	<?	if($c_edit) {	 ?>
	<center>
	<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr class="DataBG">
			<td colspan="3" align="center">Transfer Mahasiswa</td>
		</tr>
		<tr valign="top" class="NoHover">
			<td width="60" style="border:none"><strong>Tujuan</strong></td>
			<td width="340" style="border:none"><strong>:</strong> <?= $l_tujuan ?></td>
			<td rowspan="2">
				Masukkan <strong>Tujuan</strong> dan <strong>NIM Baru</strong>, lalu (pilih salah satu):<br>
				<ul style="margin:0 auto;padding:0 15px">
					<li>Masukkan <strong>NIM Lama</strong> dan klik tombol Transfer</li>
					<li>Klik tombol pada kolom <strong>Aksi</strong> di bawah</li>
				</ul>
			</td>
		</tr>
		<tr>
			<td style="border:none"><strong>NIM Baru</strong></td>
			<td style="border:none">
				<strong>:</strong> <?= UI::createTextBox('newnim','','ControlStyle',10,10) ?> &nbsp;
				<strong>NIM Lama &nbsp; :</strong>  <?= UI::createTextBox('oldnim','','ControlStyle',10,10) ?> &nbsp;
				<input type="button" value="Transfer" class="ControlStyle" onclick="goTransferNIM()">
			</td>
		</tr>
	</table>
	</center>
	<br>
	<?	} ?>
	<center>
		<header style="width:<?= $p_tbwidth ?>px">
			<div class="inner">
				<div class="left title">
					<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
				</div>
			</div>
		</header>
	</center>
	<?	/*************/
		/* LIST DATA */
		/*************/
	?>
	<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
		<?	/**********/
			/* HEADER */
			/**********/
		?>
		<tr>
			<?	list($t_sort) = explode(',',$r_sort);
				trim($t_sort);
				list($t_col,$t_dir) = explode(' ',$t_sort);
				
				foreach($a_kolom as $datakolom) {
					if($t_col == $datakolom['kolom'])
						$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
					else
						$t_sortimg = '';
					
					$t_width = $datakolom['width'];
					if(!empty($t_width))
						$t_width = ' width="'.$t_width.'"';
			?>
			<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
			<?	}
				if($c_edit) { ?>
			<th width="50">Aksi</th>
			<?	} ?>
		</tr>
		<?	/********/
			/* ITEM */
			/********/
			
			$i = 0;
			foreach($a_data as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				$t_key = $p_model::getKeyRow($row);
				
				$rowc = Page::getColumnRow($a_kolom,$row);
		?>
		<tr valign="top" class="<?= $rowstyle ?>">
			<?	foreach($rowc as $j => $rowcc) {
					$t_align = $a_kolom[$j]['align'];
					if(!empty($t_width))
						$t_align = ' align="'.$t_align.'"';
			?>
			<td<?= $t_align ?>><?= $rowcc ?></td>
			<?	}
				if($c_edit) { ?>
			<td align="center">
				<img id="<?= $t_key ?>" title="Transfer Mahasiswa" src="images/out.png" onclick="goTransfer(this.id)" style="cursor:pointer">
			</td>
			<?	} ?>
		</tr>
		<?	}
			if($i == 0) {
		?>
		<tr>
			<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
		</tr>
		<?	} ?>
	</table>
