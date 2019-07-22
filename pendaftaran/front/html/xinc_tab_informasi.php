<div class="tab-pane" id="informasi">
	<table class="table table-bordered table-striped">

	<?= Page::getDataTR($row,'namaibu') ?>
	<?= Page::getDataTR($row,'alamatibu') ?>
	<?= Page::getDataTR($row,'rtibu') ?>
	<?= Page::getDataTR($row,'rwibu') ?>
	<?= Page::getDataTR($row,'kelibu') ?>
	<?= Page::getDataTR($row,'kecibu') ?>
	<?= Page::getDataTR($row,'kodepropinsiibu') ?>
	<?= Page::getDataTR($row,'kodekotaibu') ?>

	<?= Page::getDataTR($row,'telpibu') ?>
	<?= Page::getDataTR($row,'hpibu') ?>
	<tr>
		<td width="150">Pekerjaan Ayah</td>
		<?= Page::getDataTD($row,'kodepekerjaanayah') ?>
	</tr>
	
	<?= Page::getDataTR($row,'kodepekerjaanibu') ?>
	</table>
	
</div>
