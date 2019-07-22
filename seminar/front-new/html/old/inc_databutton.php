		<?	if($c_readlist) { ?>
		<button type="button" class="btn btn-primary" onclick="goList()"><span class="glyphicon glyphicon-list"></span> Kembali</button>
		<?	} if($c_insert) { ?>
		<button type="button" class="btn btn-primary" onclick="goNew()"><span class="glyphicon glyphicon-plus"></span> Data Baru</button>
		<?	} if($c_edit) { ?>
		<button id="show" type="button" class="btn btn-primary" onclick="goEdit()"><span class="glyphicon glyphicon-pencil"></span> Edit Data</button>
		<button id="edit" style="display:none" type="button" class="btn btn-success" onclick="goSave()"><span class="glyphicon glyphicon-ok"></span> Simpan</button>
		<button id="edit" style="display:none" type="button" class="btn btn-success" onclick="goUndo()"><span class="glyphicon glyphicon-remove"></span> Batal</button>
		<?	} if($c_delete and !empty($r_key)) { ?>
		<button type="button" class="btn btn-danger" onclick="goDelete()"><span class="glyphicon glyphicon-trash"></span> Hapus</button>
		<?	} if(!$c_edit){?>
		<span class="alert alert-danger">Data pendaftar tidak dapat di edit</span>
		<? }
		?>
