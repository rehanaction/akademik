<div class="tab-pane" id="informasi">
	<table class="table table-bordered table-striped">
	<tr>
		<td colspan="2" class="DataBG">Wali</td>
	</tr>
	<?= Page::getDataTR($row,'jeniswali') ?>
	<?= Page::getDataTR($row,'statuswali') ?>
	<?= Page::getDataTR($row,'namaayah') ?>
	<?= Page::getDataTR($row,'alamatayah') ?>
	<?//= Page::getDataTR($row,'nomorrumahayah') ?>
	<?= Page::getDataTR($row,'rtayah') ?>
	<?= Page::getDataTR($row,'rwayah') ?>
	<?= Page::getDataTR($row,'kelayah') ?>
	<?= Page::getDataTR($row,'kecayah') ?>
	<?= Page::getDataTR($row,'kodepropinsiayah') ?>
	<?= Page::getDataTR($row,'kodekotaayah') ?>
	<?= Page::getDataTR($row,'telpayah') ?>
	<?= Page::getDataTR($row,'hpayah') ?>
	<?= Page::getDataTR($row,'emailayah') ?>
	<?= Page::getDataTR($row,'kodepekerjaanayah') ?>
	<?= Page::getDataTR($row,'jabatankerjaayah') ?>
	<?= Page::getDataTR($row,'namaperusahaanayah') ?>
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
	<?= Page::getDataTR($row,'emailibu') ?>
	<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
	<?= Page::getDataTR($row,'jabatankerjaibu') ?>
	<?= Page::getDataTR($row,'namaperusahaanibu') ?>
	<?= Page::getDataTR($row,'kodependidikanibu') ?>
	<tr>
		<td colspan="2" class="DataBG">Data Pendapatan</td>
	</tr>
	<?= Page::getDataTR($row,'kodependapatanortu') ?>
	
	</table>
	
</div>
