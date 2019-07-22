<div class="tab-pane" id="akademik">
	<table class="table table-bordered table-striped">

		<?= Page::getDataTR($row,'mhstransfer') ?>

		<tr>
			<td colspan="2" class="DataBG">Informasi Sekolah (SMU/SMK)</td>
		</tr>
		<tr>
			<td class="LeftColumnBG"> <?= Page::getDataLabel($row,'asalsmu') ?> *</td>
			<td class="RightColumnBG"> 
				<span id="show">
					<?php $namasmu = $r_asalsmu ? mSmu::getNamasmu($conn,$r_asalsmu) : ''; ?>
					<?= $namasmu; ?></span>
				<span id="edit" style="display:none">
					<?= UI::createTextBox('xasalsmu',$namasmu,'ControlAuto',30,30) ?> <i>
					<input type="hidden" id="asalsmu" name="asalsmu" value="<?= $r_asalsmu ?>">
				</span>
			</td>
		</tr>						
		<?= Page::getDataTR($row,'jenissekolah') ?>
		<?= Page::getDataTR($row,'thnlulussmaasal') ?>
		<?= Page::getDataTR($row,'noijasahsmu') ?>
		
		
		<?= Page::getDataTR($row,'jurusansmaasal') ?>
		<?= Page::getDataTR($row,'nemsmu')?>
		<tr>
			<td colspan="2" class="DataBG">Informasi Perguruan Tinggi sebelumnya</td>
		</tr>
		<tr>
			<td colspan="2" class="DataBG">(diisi oleh yang pernah menempuh pendidikan tinggi (program S1)/pendaftar bagi program S2)</td>
		</tr>
		<?= Page::getDataTR($row,'ptasal') ?>
		<?= Page::getDataTR($row,'ptjurusan') ?>
		<?= Page::getDataTR($row,'ptthnlulus') ?>
		<tr>
			<td>Jumlah SKS Kuliah Asal</td>
			<?= Page::getDataTD($row,'sksasal') ?>
		</tr>


	</table>
</div>
