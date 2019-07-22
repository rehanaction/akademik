<table border="0" cellspacing="10" align="center">
	<tr>
		<?	if($c_edit) { ?>
	    <td id="be_editdet" class="TDButton" onclick="goEditDet()">
			<img src="images/edit.png"> Sunting
		</td>
		<td id="be_savedet" class="TDButton" onclick="goSaveDet()" style="display:none">
			<img src="images/disk.png"> Simpan
		</td>
		<td id="be_undodet" class="TDButton" onclick="goUndoDet()" style="display:none">
			<img src="images/undo.png"> Batal
		</td>
		<?	} if($c_delete and !empty($r_keydet)) { ?>
		<td id="be_deletedet" class="TDButton" onclick="goDeleteDet()">
			<img src="images/delete.png"> Hapus
		</td>
		<?	} ?>
	</tr>
</table>
