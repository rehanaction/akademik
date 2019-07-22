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
	$a_label['pangkat'] = array('title' => 'Riwayat Kepangkatan', 'key' => $p_model::keyPangkat);
	$a_label['jabatan'] = array('title' => 'Pengalaman Jabatan', 'key' => $p_model::keyJabatan);
	
	// struktur field
	// $a_pangkat = mPangkat::getArray($conn);
	// $a_fungsional = $p_model::jabatanFungsional($conn);
	// $a_struktural = $p_model::jabatanStruktural($conn);
	
	// $a_jabatan = array();
	// foreach($a_fungsional as $k => $v)
		// $a_jabatan['F_'.$k] = $v;
	// foreach($a_struktural as $k => $v)
		// $a_jabatan['S_'.$k] = $v;
	
	// $conn->debug = true;
	
	$t_field = array();
	$t_field[] = array('kolom' => 'namapangkat', 'label' => 'Pangkat', 'type' => 'S');
	$t_field[] = array('kolom' => 'tmtpangkat', 'label' => 'TMT', 'type' => 'D');
	$t_field[] = array('kolom' => 'gajipokok', 'label' => 'Gaji Pokok', 'type' => 'N', 'size' => 14, 'maxlength' => 14, 'list' => false);
	$t_field[] = array('kolom' => 'pejabatsk', 'label' => 'Pejabat', 'size' => 30, 'maxlength' => 50, 'list' => false);
	$t_field[] = array('kolom' => 'nosk', 'label' => 'No. SK', 'size' => 30, 'maxlength' => 50);
	$t_field[] = array('kolom' => 'tglsk', 'label' => 'Tgl. SK', 'type' => 'D');
	$t_field[] = array('kolom' => 'peraturandasar', 'label' => 'Peraturan Dasar', 'size' => 30, 'maxlength' => 50, 'list' => false);
	
	$a_field['pangkat'] = $t_field;
	
	$t_field = array();
	$t_field[] = array('kolom' => 'jenispejabat', 'label' => 'Jenis Jabatan', 'type' => 'S', 'add' => 'onchange="changeJenisJabatan()"');
	$t_field[] = array('kolom' => 'jabatanstruktural', 'label' => 'Jabatan', 'type' => 'S');
	/* $t_field[] = array('kolom' => 'jabatanfungsional', 'label' => 'Jabatan Fungsional', 'type' => 'S', 'option' => $a_fungsional);
	$t_field[] = array('kolom' => 'jabatanstruktural', 'label' => 'Jabatan Struktural', 'type' => 'S', 'option' => $a_struktural); */
	$t_field[] = array('kolom' => 'tmtmulai', 'label' => 'Mulai', 'type' => 'D');
	$t_field[] = array('kolom' => 'tmtselesai', 'label' => 'Selesai', 'type' => 'D');
	$t_field[] = array('kolom' => 'kodepangkat', 'label' => 'Pangkat', 'type' => 'S', 'list' => false);
	// $t_field[] = array('kolom' => 'gajipokok', 'label' => 'Gaji Pokok', 'type' => 'N', 'size' => 14, 'maxlength' => 14);
	$t_field[] = array('kolom' => 'pejabatsk', 'label' => 'Pejabat', 'size' => 30, 'maxlength' => 50, 'list' => false);
	$t_field[] = array('kolom' => 'nosk', 'label' => 'No. SK', 'size' => 30, 'maxlength' => 50);
	$t_field[] = array('kolom' => 'tglsk', 'label' => 'Tgl. SK', 'type' => 'D', 'list' => false);
	
	$a_field['jabatan'] = $t_field;
	
	// ada aksi
	$r_act = $_REQUEST['act'];
	if($r_act == 'data' and !empty($r_subkey)) {
		if($r_label == 'pangkat')
			$a_data = $p_model::getDataPangkat($conn,$r_subkey);
		else
			$a_data = $p_model::getDataJabatan($conn,$r_subkey);
		
		foreach($a_field[$r_label] as $t_field) {
			$t_value = uForm::getValue($t_field,$a_data[$t_field['kolom']]);
			$t_label = uForm::getLabel($t_field,$a_data[$t_field['kolom']]);
			
			$a_data[$t_field['kolom']] = array('value' => $t_value, 'label' => $t_label);
		}
		
		$t_data = json_encode($a_data);
		die($t_data);
	}
	else if(!empty($r_act)) {
		if($r_label == 'pangkat') {
			if($r_act == 'save' and $c_edit) {
				list(,$record) = uForm::getPostRecord($a_field[$r_label],$_POST,'','_'.$r_label);
				if(empty($r_subkey)) {
					$record['nip'] = $r_key;
					
					$err = $p_model::insertPangkat($conn,$record);
				}
				else
					$err = $p_model::updatePangkat($conn,$record,$r_subkey);
				
				$r_msg = 'Penyimpanan data riwayat pangkat';
			}
			else if($r_act == 'delete' and $c_delete and !empty($r_subkey)) {
				$err = $p_model::deletePangkat($conn,$r_subkey);
				
				$r_msg = 'Penghapusan data riwayat pangkat';
			}
		}
		else {
			if($r_act == 'save' and $c_edit) {
				list(,$record) = uForm::getPostRecord($a_field[$r_label],$_POST,'','_'.$r_label);
				
				$record['jabatan'] = substr($record['jabatan'],2);
				
				if(empty($r_subkey)) {
					$record['nip'] = $r_key;
					
					$err = $p_model::insertJabatan($conn,$record);
				}
				else
					$err = $p_model::updateJabatan($conn,$record,$r_subkey);
				
				$r_msg = 'Penyimpanan data pengalaman jabatan';
			}
			else if($r_act == 'delete' and $c_delete and !empty($r_subkey)) {
				$err = $p_model::deleteJabatan($conn,$r_subkey);
				
				$r_msg = 'Penghapusan data pengalaman jabatan';
			}
		}
		
		$a_status = array();
		$a_status['error'] = $err;
		$a_status['message'] = $r_msg.' '.($err ? 'gagal' : 'berhasil');
		
		$t_status = json_encode($a_status);
		die($t_status);
	}
	
	// ambil data
	$a_data['pangkat'] = $p_model::getListPangkat($conn,$r_key);
	$a_data['jabatan'] = $p_model::getListJabatan($conn,$r_key);
	
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