<div id="items">
	<table class="table table-bordered table-striped">
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap" width="220"><?= Page::getDataLabel($row,'nama')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'nama')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'sex')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'sex')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepropinsilahir,kodekotalahir,tgllahir')?></td>
		<td class="RightColumnBG"><?= $arrPropinsi[Page::getDataValue($row,'kodepropinsilahir')].','.$arrKota[Page::getDataValue($row,'kodekotalahir')].','. CStr::formatDateInd(Page::getDataValue($row,'tgllahir'))?></td>
	</tr>
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
					<td><?= Page::getDataValue($row,'jalan')?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'rt')?> / <?= Page::getDataLabel($row,'rw')?></td>
					<td>:</td>
					<td><?= Page::getDataValue($row,'rt')?> / <?= Page::getDataValue($row,'rw')?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'kel')?></td>
					<td>:</td>
					<td><?= Page::getDataValue($row,'kel')?></td>
				</tr>
				<tr>
					<td><?= Page::getDataLabel($row,'kec') ?></td>
					<td>:</td>
					<td><?= Page::getDataValue($row,'kec') ?></td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepropinsi')?></td>
		<td class="RightColumnBG"><?= $arrPropinsi[((int) Page::getDataValue($row,'kodepropinsi'))]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodekota')?></td>
		<td class="RightColumnBG"><?= $arrKota[( (int) Page::getDataValue($row,'kodekota'))]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepos')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodepos')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'telp')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'telp')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'hp')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'hp')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'hp2')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'hp2')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'email')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'email')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodeagama')?></td>
		<td class="RightColumnBG"><?= $arrAgama[( (int)Page::getDataValue($row,'kodeagama'))]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodewn')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kodewn')?></td>
	</tr>
	</table>

</div>
