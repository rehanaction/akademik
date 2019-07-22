<div class="tab-pane" id="items">
	<table class="table table-bordered table-striped" cellpadding="5">
	<tr>
		<td colspan="2" class="DataBG">Wali</td>
	</tr>
	<?= Page::getDataTR($row,'jeniswali') ?>
	<?= Page::getDataTR($row,'statuswali') ?>
	<?= Page::getDataTR($row,'namaayah') ?>
	<?= Page::getDataTR($row,'alamatayah') ?>
	<?//= Page::getDataTR($row,'nomorrumahayah') ?>
	<?php // Page::getDataTR($row,'rtayah') ?>
	<?php // Page::getDataTR($row,'rwayah') ?>
	<?php // Page::getDataTR($row,'kelayah') ?>
	<?php // Page::getDataTR($row,'kecayah') ?>
	<?php // Page::getDataTR($row,'kodepropinsiayah') ?>
	<?php // Page::getDataTR($row,'kodekotaayah') ?>
	<?= Page::getDataTR($row,'telpayah') ?>
	<?= Page::getDataTR($row,'hpayah') ?>
	<?php // Page::getDataTR($row,'emailayah') ?>
	<?= Page::getDataTR($row,'kodepekerjaanayah') ?>
	<?php // Page::getDataTR($row,'jabatankerjaayah') ?>
	<?php // Page::getDataTR($row,'namaperusahaanayah') ?>
	<?= Page::getDataTR($row,'kodependidikanayah') ?>

	<tr>
		<td colspan="2" class="DataBG">Data Ibu</td>
	</tr>
	<?= Page::getDataTR($row,'statusibu') ?>
	<?= Page::getDataTR($row,'namaibu') ?>
	<?= Page::getDataTR($row,'alamatibu') ?>
	<?//= Page::getDataTR($row,'nomorrumahibu') ?>
	<?= Page::getDataTR($row,'rtibu') ?>
	<?= Page::getDataTR($row,'rwibu') ?>
	<?= Page::getDataTR($row,'kelibu') ?>
	<?= Page::getDataTR($row,'kecibu') ?>
	<?= Page::getDataTR($row,'kodepropinsiibu') ?>
	<?= Page::getDataTR($row,'kodekotaibu') ?>
	<?= Page::getDataTR($row,'telpibu') ?>
	<?= Page::getDataTR($row,'hpibu') ?>
	<?php // Page::getDataTR($row,'emailibu') ?>
	<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
	<?php // Page::getDataTR($row,'jabatankerjaibu') ?>
	<?php // Page::getDataTR($row,'namaperusahaanibu') ?>
	<?= Page::getDataTR($row,'kodependidikanibu') ?>
	
	</table>
	
</div>
