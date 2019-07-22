<div id="items">
	<div id="biodata-beasiswa">

	</div>
	<table class="table table-bordered table-striped">
	<tr>
		<td colspan="2" class="DataBG">Wali</td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jeniswali')?></td>
		<td class="RightColumnBG"><?= $arrWali[Page::getDataValue($row,'jeniswali')]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statuswali')?></td>
		<td class="RightColumnBG"><?= $arrStatusWali[Page::getDataValue($row,'statuswali')]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'namaayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'rtayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'rtayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'rwayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'rwayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kelayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kelayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kecayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kecayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepropinsiayah')?></td>
		<td class="RightColumnBG"><?= $arrPropinsi[Page::getDataValue($row,'kodepropinsiayah')]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodekotaayah')?></td>
		<td class="RightColumnBG"><?= $arrKota[Page::getDataValue($row,'kodekotaayah')]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'telpayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'telpayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'hpayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'hpayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'emailayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'emailayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepekerjaanayah')?></td>
		<td class="RightColumnBG"><?= $arrPekerjaan[Page::getDataValue($row,'kodepekerjaanayah')]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jabatankerjaayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'jabatankerjaayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaperusahaanayah')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'namaperusahaanayah')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodependidikanayah')?></td>
		<td class="RightColumnBG"><?= $arrPendidikan[Page::getDataValue($row,'kodependidikanayah')]?></td>
	</tr>

	<tr>
		<td colspan="2" class="DataBG">Data Ibu</td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statusibu')?></td>
		<td class="RightColumnBG"><?= $arrStatusWali[Page::getDataValue($row,'statusibu')]?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'namaibu')?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'alamatibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'alamatibu')?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'rtibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'rtibu')?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'rwibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'rwibu')?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kelibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kelibu')?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kecibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'kecibu')?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepropinsiibu')?></td>
		<td class="RightColumnBG"><?= $arrPropinsi[Page::getDataValue($row,'kodepropinsiibu')]?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodekotaibu')?></td>
		<td class="RightColumnBG"><?= $arrKota[Page::getDataValue($row,'kodekotaibu')]?></td>
	</tr>

	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'telpibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'telpibu')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'hpibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'hpibu')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'emailibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'emailibu')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodepekerjaanibu')?></td>
		<td class="RightColumnBG"><?= $arrPekerjaan[Page::getDataValue($row,'kodepekerjaanibu')]?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jabatankerjaibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'jabatankerjaibu')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namaperusahaanibu')?></td>
		<td class="RightColumnBG"><?= Page::getDataValue($row,'namaperusahaanibu')?></td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodependidikanibu')?></td>
		<td class="RightColumnBG"><?= $arrPendidikan[Page::getDataValue($row,'kodependidikanibu')]?></td>
	</tr>
	<tr>
		<td colspan="2" class="DataBG">Data Pendapatan</td>
	</tr>
	<tr>
		<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'kodependapatanortu')?></td>
		<td class="RightColumnBG"><?= $arrPendapatan[Page::getDataValue($row,'kodependapatanortu')]?></td>
	</tr>
	</table>

</div>
