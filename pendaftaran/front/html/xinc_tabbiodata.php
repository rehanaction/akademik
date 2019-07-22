
<div class="tab-pane" id="biodata">
	<table class="table table-bordered table-striped">
	<?= Page::getDataTR($row,'nama') ?>
	<?= Page::getDataTR($row,'sex') ?>
	<?= Page::getDataTR($row,'kodepropinsilahir,kodekotalahir,tgllahir') ?>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
		<td class="RightColumnBG">
			<table>
				<? /*<tr>
					<td><?= Page::getDataLabel($row,'nomorrumah')?></td>
					<td>:</td>
					<td><?= Page::getDataInput($row,'nomorrumah')?></td>
				</tr> */ ?>
				<tr>
					<td><?= Page::getDataLabel($row,'jalan')?></td>
					<td>:</td>
					<td><?= Page::getDataInput($row,'jalan')?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'rt')?> / <?= Page::getDataLabel($row,'rw')?></td>
					<td>:</td>
					<td><?= Page::getDataInput($row,'rt')?> / <?= Page::getDataInput($row,'rw')?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'kel')?></td>
					<td>:</td>
					<td><?= Page::getDataInput($row,'kel')?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'kec') ?></td>
					<td>:</td>
					<td><?= Page::getDataInput($row,'kec') ?></td>
				</tr>
				<? /*<tr>
					<td><?= Page::getDataLabel($row,'negara') ?></td>
					<td>:</td>
					<td><?= Page::getDataInput($row,'negara') ?></td>
				</tr>*/ ?>
			</table>
		</td>
	</tr>
	<?= Page::getDataTR($row,'kodepropinsi') ?>
	<?= Page::getDataTR($row,'kodekota') ?>
	<?= Page::getDataTR($row,'kodepos') ?>
	<?= Page::getDataTR($row,'telp') ?>
	<?= Page::getDataTR($row,'hp') ?>
	<?= Page::getDataTR($row,'hp2') ?>
	<?= Page::getDataTR($row,'email') ?>
	<?= Page::getDataTR($row,'kodeagama') ?>
	<?= Page::getDataTR($row,'kodewn') ?>
	</table>
	<div id="biodata-beasiswa">
	</div>
</div>
