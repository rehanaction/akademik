<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuthAJAX();
	
	/* $c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete']; */
	
	// include
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Tanda Jasa/Penghargaan';
	$p_label = 'penghargaan';
	$p_aktivitas = 'BIODATA';
	$p_model = mPegawai;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_subkey = $_REQUEST['key_'.$p_label];
	
	if((empty($r_subkey) and $c_insert) or (!empty($r_subkey) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// struktur field
	$a_field = array();
	// $a_field[] = array('kolom' => 'nopenghargaan', 'label' => 'No', 'type' => 'NP', 'size' => 2, 'maxlength' => 2, 'notnull' => true);
	$a_field[] = array('kolom' => 'namapenghargaan', 'label' => 'Jenis Penghargaan', 'size' => 30, 'maxlength' => 50);
	$a_field[] = array('kolom' => 'bentukreward', 'label' => 'Nama Penghargaan', 'size' => 30, 'maxlength' => 50);
	$a_field[] = array('kolom' => 'tglpenghargaan', 'label' => 'Tgl. Penghargaan', 'type' => 'D', 'size' => 10, 'maxlength' => 10);
	// $a_field[] = array('kolom' => 'namainstansi', 'label' => 'Pemberi Penghargaan', 'size' => 30, 'maxlength' => 50);
	
	$p_colnum = count($a_field)+1;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'data' and !empty($r_subkey)) {
		$a_data = $p_model::getDataPenghargaan($conn,$r_subkey);
		
		foreach($a_field as $t_field) {
			$t_value = uForm::getValue($t_field,$a_data[$t_field['kolom']]);
			$t_label = uForm::getLabel($t_field,$a_data[$t_field['kolom']]);
			
			$a_data[$t_field['kolom']] = array('value' => $t_value, 'label' => $t_label);
		}
		
		$t_data = json_encode($a_data);
		die($t_data);
	}
	else if(!empty($r_act)) {
		if($r_act == 'save' and $c_edit) {
			list(,$record) = uForm::getPostRecord($a_field,$_POST,'','_'.$p_label);
			
			if(empty($r_subkey)) {
				$record['nip'] = $r_key;
				
				$err = $p_model::insertPenghargaan($conn,$record);
			}
			else
				$err = $p_model::updatePenghargaan($conn,$record,$r_subkey);
			
			$r_msg = 'Penyimpanan data tanda jasa/penghargaan';
		}
		else if($r_act == 'delete' and $c_delete and !empty($r_subkey)) {
			$err = $p_model::deletePenghargaan($conn,$r_subkey);
			
			$r_msg = 'Penghapusan data tanda jasa/penghargaan';
		}
		
		$a_status = array();
		$a_status['error'] = $err;
		$a_status['message'] = $r_msg.' '.($err ? 'gagal' : 'berhasil');
		
		$t_status = json_encode($a_status);
		die($t_status);
	}
	
	// ambil data
	$a_data = $p_model::getListPenghargaan($conn,$r_key);
?>
<table width="100%" cellpadding="4" cellspacing="2" class="GridStyle" align="center">
	<tr>
		<td class="DataBG" colspan="<?= $p_colnum ?>">
			<?= $p_title ?>
			<? if($c_insert) { ?>
			<div class="addButton" onClick="goNewPop('<?= $p_label ?>')" style="float:right;font-size:14px">+</div>
			<? } ?>
		</td>
	</tr>
	<?	/**********/
		/* HEADER */
		/**********/
	?>
	<tr>
		<th>No.</th>
		<?	foreach($a_field as $t_field) {
				if(empty($t_field['label']) or $t_field['list'] === false)
					continue;
				
				if(empty($t_field['list']))
					$t_label = $t_field['label'];
				else
					$t_label = $t_field['list'];
				
				$t_width = $t_field['width'];
				if(!empty($t_width))
					$t_width = ' width="'.$t_width.'"';
		?>
		<th id="<?= $t_field['kolom'] ?>"<?= $t_width ?>><?= $t_label ?></th>
		<?	} ?>
	</tr>
	<?	/********/
		/* ITEM */
		/********/
		
		// mencegah warning foreach
		if(empty($a_data))
			$a_data = array();
		
		$i = 0;
		foreach($a_data as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			
			$t_key = $p_model::getKeyRow($row,$p_model::keyPenghargaan);
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align=center><?= $i;?>.</td>
		<?	foreach($a_field as $t_field) {
				if(empty($t_field['label']) or $t_field['list'] === false)
					continue;
				
				$t_align = $t_field['align'];
				if(!empty($t_align))
					$t_align = ' width="'.$t_align.'"';
		?>
		<td <?= $t_align ?>><?= uForm::getLabel($t_field,$row[$t_field['kolom']]) ?></td>
		<?	} ?>
	</tr>
	<?	}
		if($i == 0) {
	?>
	<tr>
		<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
	</tr>
	<?	} ?>
</table>
<br>
<?php
	/********/
	/* FORM */
	/********/
?>
<div id="div_dark_<?= $p_label ?>" class="Darken" style="display:none"></div>
<div id="div_light_<?= $p_label ?>" class="Lighten" style="display:none">
<div id="div_content_<?= $p_label ?>" style="background-color:white;width:600px;padding:0 11px 11px 11px">
<div id="div_info_<?= $p_label ?>" style="position:fixed;margin-top:170px;width:600px;visibility:hidden">
	<img id="div_load_<?= $p_label ?>" src="images/loading.gif">
	<div id="div_error_<?= $p_label ?>" class="DivError" style="width:500px;display:none"></div>
	<div id="div_success_<?= $p_label ?>" class="DivSuccess" style="width:500px;display:none"></div>
</div>
<table border="0" cellspacing="10" class="nowidth">
	<tr>
		<?	if($c_insert) { ?>
		<td id="be_add_<?= $p_label ?>" class="TDButton" onclick="goNewPop('<?= $p_label ?>')">
			<img src="images/add.png"> Data Baru
		</td>
		<?	} if($c_update) { ?>
	   <td id="be_edit_<?= $p_label ?>" class="TDButton" onclick="goEditPop('<?= $p_label ?>')">
			<img src="images/edit.png"> Sunting
		</td>
		<td id="be_save_<?= $p_label ?>" class="TDButton" onclick="goSavePop('<?= $p_label ?>')" style="display:none">
			<img src="images/disk.png"> Simpan
		</td>
		<td id="be_undo_<?= $p_label ?>" class="TDButton" onclick="goUndoPop('<?= $p_label ?>')" style="display:none">
			<img src="images/undo.png"> Batal
		</td>
		<?	} if($c_delete) { ?>
		<td id="be_delete_<?= $p_label ?>" class="TDButton" onclick="goDeletePop('<?= $p_label ?>')">
			<img src="images/delete.png"> Hapus
		</td>
		<?	} ?>
		<td class="TDButton" onclick="goClosePop('<?= $p_label ?>')">
			<img src="images/off.png"> Tutup
		</td>
	</tr>
</table>
<center>
	<header style="width:600px">
		<div class="inner">
			<div class="left title">
				<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
				<h1>Data <?= $p_title ?></h1>
			</div>
		</div>
	</header>
	<div class="box-content" style="width:578px">
	<table width="578" cellpadding="4" cellspacing="2" align="center">
	<?	$a_required = array();
		foreach($a_field as $t_field) {
			if(empty($t_field['label']))
				continue;
			
			$t_field['nameid'] = $t_field['kolom'].'_'.$p_label;
			
			if($t_field['notnull'])
				$a_required[] = $t_field['nameid'];
	?>
		<tr>
			<td class="LeftColumnBG" width="130" style="white-space:nowrap">
				<?= $t_field['label'] ?>
				<?= $t_field['notnull'] ? '<span id="edit_'.$p_label.'" style="display:none">*</span>' : '' ?>
			</td>
			<td class="RightColumnBG">
				<span id="show_<?= $p_label ?>"><span id="span_<?= $t_field['nameid'] ?>"></span></span>
				<span id="edit_<?= $p_label ?>" style="display:none"><?= uForm::getInput($t_field) ?></span>
			</td>
		</tr>
	<?	} ?>
	</table>
	</div>
</center>
</div>
<input type="hidden" name="key_<?= $p_label ?>" id="key_<?= $p_label ?>">
<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
</div>

<script type="text/javascript">
	
if(typeof(ajax_pop) == "undefined")
	var ajax_pop = new Array();
if(typeof(required_pop) == "undefined")
	var required_pop = new Array();

ajax_pop["<?= $p_label ?>"] = "<?= $i_page ?>";
required_pop["<?= $p_label ?>"] = "<?= @implode(',',$a_required) ?>";

</script>