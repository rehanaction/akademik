

<table border="0" cellspacing="10" align="center">
		<tr>
			<td><input type="button" value="Transfer" class="ControlStyle" onclick="goTransferEx()"></td>
		</tr>
</table>
	<?php
		if(!empty($p_postmsg)) { ?>
	<center>
	<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
		<?= $p_postmsg ?>
	</div>
	</center>
	<div class="Break"></div>
	<?	} ?>
	

	<center>
		<header style="width:<?= $p_tbwidth ?>px">
			<div class="inner">
				<div class="left title">
					<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
				</div>
				<!--div class="right" style="padding-top:7px">
					<input type="button" value="Transfer" class="ControlStyle" onclick="goTransferEx()">
				</div-->
			</div>
		</header>
		<?	/********/
			/* DATA */
			/********/
		?>
		<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
		<?
			$a_required = array();
			foreach($b_input as $t_input) {
				if($t_input['notnull'] === true)
					$a_required[] = CStr::cEmChg($t_input['nameid'],$t_input['kolom']);
			}
		?>
		
		<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
			<tr>
				<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nim') ?></td>
				<td class="RightColumnBG"><?= Page::getDataInput($row,'nim') ?></td>
				
			</tr>
			<?= Page::getDataTR($row,'nama') ?>
			<?= Page::getDataTR($row,'kodefakultas') ?>
			<?= Page::getDataTR($row,'kodeunit') ?>
			<?= Page::getDataTR($row,'semesterdaftar,tahundaftar') ?>	
		</table>
		</div>
	</center>
	<br>
	<center>
	<div class="tabs" style="width:<?= $p_tbwidth ?>px">
		<ul>
			<li><a id="tablink" href="javascript:void(0)">Biodata</a></li>
			<li><a id="tablink" href="javascript:void(0)">Akademik</a></li>
			<li><a id="tablink" href="javascript:void(0)">Pendidikan</a></li>
		</ul>
	
		<div id="items">
		<table cellpadding="4" cellspacing="2" align="center">
			<?= Page::getDataTR($row,'sex') ?>
			<?= Page::getDataTR($row,'kodeagama') ?>
			<?= Page::getDataTR($row,'goldarah') ?>
			<?= Page::getDataTR($row,'tmplahir,tgllahir',', ') ?>
			<tr>
				<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
				<td class="RightColumnBG">
		<table>
			<tr>
				<td width="80"><?= Page::getDataLabel($row,'alamat') ?></td>
				<td width="5">:</td>
				<td><?= Page::getDataInput($row,'alamat') ?></td>
			</tr>
			<tr>
				<td><?= Page::getDataLabel($row,'rt') ?>/<?= Page::getDataLabel($row,'rw') ?></td>
				<td>:</td>
				<td><?= Page::getDataInput($row,'rt') ?>/<?= Page::getDataInput($row,'rw') ?></td>
			</tr>
			<tr>
				<td><?= Page::getDataLabel($row,'kelurahan') ?></td>
				<td>:</td>
				<td><?= Page::getDataInput($row,'kelurahan') ?></td>
			</tr>
			<tr>
				<td><?= Page::getDataLabel($row,'kecamatan') ?></td>
				<td>:</td>
				<td><?= Page::getDataInput($row,'kecamatan') ?></td>
			</tr>
		</table>
				</td>
			</tr>
			<?= Page::getDataTR($row,'kodepropinsi') ?>
			<?= Page::getDataTR($row,'kodekota') ?>
			<?= Page::getDataTR($row,'kodepos') ?>
			<?= Page::getDataTR($row,'telp,telp2',', ') ?>
			<?= Page::getDataTR($row,'hp,hp2',', ') ?>
			<?= Page::getDataTR($row,'email,email2','<div class="Break"></div>') ?>
		</table>
		</div>
		
		<div id="items">
		<table cellpadding="4" cellspacing="2" align="center">
			<?= Page::getDataTR($row,'sistemkuliah') ?>
			<?= Page::getDataTR($row,'jalurpenerimaan') ?>
		</table>
		</div>
		
		<div id="items">
		<table cellpadding="4" cellspacing="2" align="center">
			<tr>
				<td colspan="2" class="DataBG">Informasi Mahasiswa Transfer</td>
			</tr>
			<?= Page::getDataTR($row,'ptasal') ?>
			<?= Page::getDataTR($row,'kodepropinsipt') ?>
			<?= Page::getDataTR($row,'kodekotapt') ?>
			<?= Page::getDataTR($row,'ptjurusan') ?>
			<?= Page::getDataTR($row,'ptnimlama') ?>
			<?= Page::getDataTR($row,'ptnoijasah') ?>
			<?= Page::getDataTR($row,'ptthnlulus') ?>
			<?= Page::getDataTR($row,'ptipk') ?>
		</table>
		</div>
		
	</div>
	</center>
	
	


