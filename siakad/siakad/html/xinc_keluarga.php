<?php
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuthAJAX();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('pangkat'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_aktivitas = 'BIODATA';
	$p_model = mPegawai;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_label = CStr::removeSpecial($_REQUEST['label']);
	$r_subkey = $_REQUEST['key_'.$r_label];
	
	if((empty($r_subkey) and $c_insert) or (!empty($r_subkey) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// daftar label
	$a_label = array();
	$a_label['istrisuami'] = array('title' => 'Suami/Istri');
	$a_label['anak'] = array('title' => 'Anak', 'key');
	// $conn->debug = true;
	
	$t_field = array();
	$t_field[] = array('kolom' => 'jeniskeluarga');
	// $a_field[] = array('kolom' => 'nokeluarga', 'label' => 'No', 'type' => 'NP', 'size' => 2, 'maxlength' => 2, 'notnull' => true);
	$t_field[] = array('kolom' => 'namapasangan', 'label' => 'Nama', 'size' => 30, 'maxlength' => 50);
	$t_field[] = array('kolom' => 'sex', 'label' => 'Jenis Kelamin', 'type' => 'S', 'option' => $p_model::jenisKelamin(), 'list' => false);
	$t_field[] = array('kolom' => 'tmplahir', 'label' => 'Tempat Lahir', 'maxlength' => 15, 'size' => 15, 'list' => false);
	$t_field[] = array('kolom' => 'tgllahir2', 'label' => 'Tanggal Lahir', 'type' => 'D');
	$t_field[] = array('kolom' => 'tglnikah', 'label' => 'Tanggal Menikah', 'type' => 'D', 'list' => false);
	$t_field[] = array('kolom' => 'pekerjaan', 'label' => 'Sekolah/Pekerjaan', 'size' => 30, 'maxlength' => 50);
	$a_field['istrisuami'] = $t_field;
	
	$t_field = array();
	$t_field[] = array('kolom' => 'namaanak', 'label' => 'Nama Anak', 'size' => 30, 'maxlength' => 50);
	$t_field[] = array('kolom' => 'jeniskelamin', 'label' => 'Jenis Kelamin', 'type' => 'S');
	$t_field[] = array('kolom' => 'tgllahiranak', 'label' => 'Tanggal Lahir', 'type' => 'D');
	$t_field[] = array('kolom' => 'anakke', 'label' => 'Anak Ke-');
	// $t_field[] = array('kolom' => 'pekerjaan', 'label' => 'Sekolah/Pekerjaan', 'size' => 30, 'maxlength' => 50);
	$a_field['anak'] = $t_field;
	
	// ambil data
	$a_data['istrisuami'] = $p_model::getListSuamiIstri($conn,$r_key);
	$a_data['anak'] = $p_model::getListAnak($conn,$r_key);
	
	foreach($a_label as $p_label => $t_label) {
		$p_colnum = count($a_field[$p_label])+1;
		
		$p_title = $t_label['title'];
		$p_colkey = $t_label['key'];
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
		<?	foreach($a_field[$p_label] as $t_field) {
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
		if(empty($a_data[$p_label]))
			$a_data[$p_label] = array();
		
		$i = 0;
		foreach($a_data[$p_label] as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
			
			$t_key = $p_model::getKeyRow($row,$p_colkey);
	?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<?	foreach($a_field[$p_label] as $t_field) {
				if(empty($t_field['label']) or $t_field['list'] === false)
					continue;
				
				$t_align = $t_field['align'];
				if(!empty($t_align))
					$t_align = ' width="'.$t_align.'"';
		?>
		<td<?= $t_align ?>><?= uForm::getLabel($t_field,$row[$t_field['kolom']]) ?></td>
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
		foreach($a_field[$p_label] as $t_field) {
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
</div>

<script type="text/javascript">
	
if(typeof(ajax_pop) == "undefined")
	var ajax_pop = new Array();
if(typeof(required_pop) == "undefined")
	var required_pop = new Array();

ajax_pop["<?= $p_label ?>"] = "<?= $i_page ?>";
required_pop["<?= $p_label ?>"] = "<?= @implode(',',$a_required) ?>";

</script>

<?	} ?>

<input type="hidden" name="key" id="key" value="<?= $r_key ?>">

<script type="text/javascript">

$(document).ready(function() {
	changeJenisJabatan();
});
	
function changeJenisJabatan() {
	var jenis = $("#jenisjabatan_jabatan").val();
	var option = $("#jabatan_jabatan option");
	var cek;
	
	cek = option.filter(":hidden");
	// cek.removeAttr("disabled");
	cek.show();
	
	cek = option.not("[value^='"+jenis+"_']");
	// cek.attr("disabled","disabled");
	cek.hide();
	
	$("#jabatan_jabatan").val(option.filter(":visible").eq(0).val());
}

</script>