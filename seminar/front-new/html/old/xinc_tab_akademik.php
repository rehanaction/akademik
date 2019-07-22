<div class="tab-pane" id="akademik">
	<table class="table table-bordered table-striped">

		<?= Page::getDataTR($row,'mhstransfer') ?>

		<tr>
			<td colspan="2" class="DataBG">Asal Sekolah</td>
		</tr>
		<?= Page::getDataTR($row,'propinsismu') ?>
		<?= Page::getDataTR($row,'kodekotasmu') ?>
		<?= Page::getDataTR($row,'kodesekolah') ?>
		<tr>
			<td class="LeftColumnBG"> <?= Page::getDataLabel($row,'asalsmu') ?></td>
			<td class="RightColumnBG"> 
				<span id="show">
					<?php $namasmu = $r_asalsmu ? mSmu::getNamasmu($conn,$r_asalsmu) : ''; ?>
					<?= $namasmu; ?></span>
				<span id="edit" style="display:none">
					<?= UI::createTextBox('xasalsmu',$namasmu,'ControlAuto',30,30) ?> <i>* Penulisan sekolah yang tidak terdaftar di sistem kami tanpa nama kota</i>
					<input type="hidden" id="asalsmu" name="asalsmu" value="<?= $r_asalsmu ?>">
				</span>
			</td>
		</tr>						
		<?= Page::getDataTR($row,'jenissekolah') ?>
		<?= Page::getDataTR($row,'kodeagamasekolah') ?>
		<?= Page::getDataTR($row,'jurusansmaasal') ?>
		<?= Page::getDataTR($row,'thnlulussmaasal') ?>
		<?= Page::getDataTR($row,'negarasekolah') ?>
		<?= Page::getDataTR($row,'alamatsmu') ?>
		<?= Page::getDataTR($row,'kodepossekolah') ?>
		<?= Page::getDataTR($row,'telpsmu') ?>

		<tr>
			<td>Nilai Raport * </td>
			<td>
				<table border=0>
					<tr>
						<td colspan=2>Kelas X</td>
						<td colspan=2>Kelas XI</td>
						<td colspan=1>Kelas XII</td>
					</tr>
					<tr>
						<td>Smt. 1</td>
						<td>Smt. 2</td>
						<td>Smt. 1</td>
						<td>Smt. 2</td>
						<td>Smt. 1</td>
					</tr>
					<tr>
						<td><?= Page::getDataInput($row,'raport_10_1')?></td>
						<td><?= Page::getDataInput($row,'raport_10_2')?></td>
						<td><?= Page::getDataInput($row,'raport_11_1')?></td>
						<td><?= Page::getDataInput($row,'raport_11_2')?></td>
						<td><?= Page::getDataInput($row,'raport_12_1')?></td>
					</tr>
				</table>
			</td>
		</tr>

		<tr>
			<td colspan="2" class="DataBG">Informasi Mahasiswa Transfer</td>
		</tr>
		<tr>
			<td colspan="2" class="DataBG">Data Perguruan tinggi (hanya di isi bagi yang pernah/sedang kuliah di perguruan tinggi lain)</td>
		</tr>
		<?= Page::getDataTR($row,'ptasal') ?>
		<?= Page::getDataTR($row,'propinsiptasal') ?>
		<?= Page::getDataTR($row,'kodekotapt') ?>
		<?= Page::getDataTR($row,'negaraptasal') ?>
		<?= Page::getDataTR($row,'ptfakultas') ?>
		<?= Page::getDataTR($row,'ptjurusan') ?>
		<?= Page::getDataTR($row,'ptthnmasuk') ?>
		<?= Page::getDataTR($row,'ptthnlulus') ?>
		<?= Page::getDataTR($row,'semesterkeluar') ?>
		<?= Page::getDataTR($row,'ptipk') ?>
		<?= Page::getDataTR($row,'sksasal') ?>	
	</table>
</div>
