<table border="0" cellspacing="10" align="center">
	<tr>
		<?	if($c_readlist) { ?>
		<td style="cursor:pointer" id="be_list" class="TDButton" onclick="goList()">
			<img src="images/list.png" > Daftar
		</td>
		<?	} if($c_insert) { ?>
		<td style="cursor:pointer" id="be_add" class="TDButton" onclick="goNew()">
			<img src="images/add.png"> Data Baru
		</td>
		<?	} if($c_edit) { ?>
		<td style="cursor:pointer" id="be_edit" class="TDButton" onclick="goEdit()">
			<img src="images/edit.png"> Sunting
		</td>
		<td id="be_save" class="TDButton" onclick="goSave()" style="display:none;cursor:pointer">
			<img src="images/disk.png"> Simpan
		</td>
		<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none;cursor:pointer">
			<img src="images/undo.png"> Batal
		</td>
		<?	} if($c_delete and !empty($r_key)) { ?>
		<td style="cursor:pointer" id="be_delete" class="TDButton" onclick="goDelete()">
			<img src="images/delete.png"> Hapus
		</td>
		<?	} ?>
	</tr>
</table>
