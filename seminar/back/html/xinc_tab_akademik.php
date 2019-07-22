<div class="tab-pane" id="items">
	<table class="table table-bordered table-striped" cellpadding="5">
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
					<?php $namasmu = (!empty($r_asalsmu) ? mSmu::getNamasmu($conn,$r_asalsmu) : '') ; ?>
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
						<td><input style="border-radius: 4px;height: 20px;width: 60px;border: 1px solid #bbb;" type="text" size="5" maxlength="5" id="raport_10_1" name="raport_10_1" <?if($_SESSION[SITE_ID]['PENDAFTAR']['raport_10_1']!=''){?>value="<?= $_SESSION[SITE_ID]['PENDAFTAR']['raport_10_1']?>" <?}else{?>value='0.00'<?}?>></td>
						<td><input style="border-radius: 4px;height: 20px;width: 60px;border: 1px solid #bbb;" type="text" size="5" maxlength="5" id="raport_10_2" name="raport_10_2" <?if($_SESSION[SITE_ID]['PENDAFTAR']['raport_10_2']!=''){?>value="<?= $_SESSION[SITE_ID]['PENDAFTAR']['raport_10_2']?>" <?}else{?>value='0.00'<?}?>></td>
						<td><input style="border-radius: 4px;height: 20px;width: 60px;border: 1px solid #bbb;" type="text" size="5" maxlength="5" id="raport_11_1" name="raport_11_1" <?if($_SESSION[SITE_ID]['PENDAFTAR']['raport_11_1']!=''){?>value="<?= $_SESSION[SITE_ID]['PENDAFTAR']['raport_11_1']?>" <?}else{?>value='0.00'<?}?>></td>
						<td><input style="border-radius: 4px;height: 20px;width: 60px;border: 1px solid #bbb;" type="text" size="5" maxlength="5" id="raport_11_2" name="raport_11_2" <?if($_SESSION[SITE_ID]['PENDAFTAR']['raport_11_2']!=''){?>value="<?= $_SESSION[SITE_ID]['PENDAFTAR']['raport_11_2']?>" <?}else{?>value='0.00'<?}?>></td>
						<td><input style="border-radius: 4px;height: 20px;width: 60px;border: 1px solid #bbb;" type="text" size="5" maxlength="5" id="raport_12_1" name="raport_12_1" <?if($_SESSION[SITE_ID]['PENDAFTAR']['raport_12_1']!=''){?>value="<?= $_SESSION[SITE_ID]['PENDAFTAR']['raport_12_1']?>" <?}else{?>value='0.00'<?}?>></td>
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
		<?= Page::getDataTR($row,'mhstransfer') ?>
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
