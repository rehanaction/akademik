<div class="tab-pane" id="items">
	<table class="table table-bordered table-striped" cellpadding="5" border="1" style="border-collapse:collapse; text-align:left; width:550px">
		<thead>
		<tr>
			<td colspan="10" class="DataBG">Tagihan Pendaftar</td>
		</tr>
			<th>ID Tagihan</th>
			<th>Tgl Tagihan</th>
			<th>Nominal</th>
			<th>Jenistagihan</th>
			<th>Status</th>
		<tr>
		</thead>
		<tbody>
		<? foreach ($arrTagihan as $key =>$val){ ?>
			<tr>
				<td><?= $val['idtagihan'] ?></td>
				<td><?= date::inDodate($val['tgltagihan']) ?></td>
				<td align="right"><?= cStr::formatNumber($val['nominaltagihan']) ?></td>
				<td><?= $val['jenistagihan'] ?></td>
				<td><?= $val['flaglunas'] ?></td>
			</tr>			
		<?}?>		
		</tbody>
	</table>
</div>
