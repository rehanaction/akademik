<div class="tab-pane" id="informasilain">
	<table class="table table-bordered table-striped">
		<tr>
			<td colspan="2" class="DataBG">Pekerjaan</td>
		</tr>
		<?= Page::getDataTR($row,'namaperusahaan') ?>
		<?= Page::getDataTR($row,'alamatperusahaan') ?>
		<?//= Page::getDataTR($row,'nomorkantor') ?>
		<?= Page::getDataTR($row,'rtkantor') ?>
		<?= Page::getDataTR($row,'rwkantor') ?>
		<?= Page::getDataTR($row,'kelkantor') ?>
		<?= Page::getDataTR($row,'kodepropinsikantor') ?>
		<?= Page::getDataTR($row,'kodekotakantor') ?>
		<?= Page::getDataTR($row,'jabatankerja') ?>
		<?= Page::getDataTR($row,'bagian') ?>
		<?= Page::getDataTR($row,'telpkantor') ?>
		<?= Page::getDataTR($row,'hpkantor') ?>
		<?= Page::getDataTR($row,'thnmasuk') ?>
	</table>
</div>
