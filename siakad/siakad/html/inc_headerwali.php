<form name="waliform" id="waliform" method="post">
<div class="menubar" style="position:fixed">
	<? /* <input type="button" value="KRS" class="ControlStyle" onClick="goKRS()">
	<input type="button" value="KHS" class="ControlStyle" onClick="goKHS()">
	<input type="button" value="Transkrip" class="ControlStyle" onClick="goTranskrip()">
	<input type="button" value="Kemajuan Belajar" class="ControlStyle" onClick="goKemajuan()"> */ ?>
	<table class="menu-body">
		<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
			<td onClick="goKRS()">KRS</td>
		</tr>
		<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
			<td onClick="goKHS()">KHS</td>
		</tr>
		<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
			<td onClick="goTranskrip()">Transkrip</td>
		</tr>
		<tr class="menu-button" onMouseMove="this.className = 'hover'" onMouseOut="this.className = ''">
			<td onClick="goKemajuan()">Kemajuan Belajar</td>
		</tr>
	</table>
	<input type="hidden" name="npm" value="<?= $r_key ?>">
</div>
</form>