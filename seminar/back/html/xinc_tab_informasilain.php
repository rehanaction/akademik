<div class="tab-pane" id="items">
	<table class="table table-bordered table-striped" cellpadding="5">
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

		<tr>
			<td colspan="2" class="DataBG">Potongan</td>
		</tr>


		<tr><td colspan="3"><i>*potongan yang telah disetujui oleh petugas KUA (keuangan akademik) tidak bisa di ubah kembali</i><br></td></tr>
		<tr><td><br></td></tr>
		<?= Page::getDataTR($row,'potonganbeasiswa') ?>
		<?= Page::getDataTR($row,'keteranganpotonganbeasiswa') ?>
		<?= Page::getDataTR($row,'isvalidbeasiswa') ?>
		<tr><td><br></td></tr>						
		<?= Page::getDataTR($row,'potonganregistrasi') ?>
		<?= Page::getDataTR($row,'keteranganpotonganregistrasi') ?>
		<?= Page::getDataTR($row,'isvalidregistrasi') ?>
		<tr><td><br></td></tr>
		<?= Page::getDataTR($row,'potongansemesterpendek') ?>
		<?= Page::getDataTR($row,'keteranganpotongansemesterpendek') ?>
		<?= Page::getDataTR($row,'isvalidsemesterpendek') ?>


	</table>
	
	
</div>
