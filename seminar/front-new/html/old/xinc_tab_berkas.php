<div class="tab-pane" id="berkas">
	<table class="table table-bordered table-striped">
	<tr>
		<td colspan="2" class="DataBG">Berkas Wajib</td>
	</tr>
	<?= Page::getDataTR($row,'filektp') ?>
	<?= Page::getDataTR($row,'fileraport') ?>
	<tr>
		<td colspan="2" class="DataBG">Berkas Tambahan</td>
	</tr>
	<?= Page::getDataTR($row,'filekk') ?>
	<?= Page::getDataTR($row,'filektpibu') ?>
	<?= Page::getDataTR($row,'filektpayah') ?>
	<?= Page::getDataTR($row,'fileijazah') ?>
	</table>
	
</div>
