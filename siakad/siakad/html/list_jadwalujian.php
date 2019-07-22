<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//hak akses manual
	if(Akademik::isDosen()){
		$c_update = false;
		$c_delete = false;
	}
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('jadwalujian'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'List Jadwal Ujian (UTS/UAS)';
	$p_tbwidth = "100%";
	$p_aktivitas = 'ABSENSI';
	$p_listpage = 'list_absensi';
	$p_printpage = 'rep_jurnal';
	$p_detailrealisasipage = 'data_jadwalujian';
	$p_detailpage = 'data_jadwalujian';
	
	
	$p_model = mJadwalUjian;
	
	// variabel request

	$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'jenisujian', 'label' => 'Jenis Ujian','type' => 'S', 'option' => mCombo::getJenisUjian());
	$a_kolom[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis Pertemuan','type' => 'S', 'option' => mKuliah::jenisKuliah($conn));
	$a_kolom[] = array('kolom' => 'kelompok', 'label' => 'Kelompok Ujian','type' => 'S', 'option' => $p_model::getKelompok());
	$a_kolom[] = array('kolom' => 'tglujian', 'label' => 'Tanggal', 'type' => 'D', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'waktumulai','label' => 'Mulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'waktuselesai', 'label' => 'Selesai','maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'koderuang', 'label' => 'Ruang','type' => 'S', 'option' => mCombo::ruang($conn));
	
	$f = 3;
	
	// properti halaman tambahan
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deletejadwal($conn,$r_subkey);
	}else if($r_act == 'genPeserta' and $c_delete) {
		$o_jenisujian = CStr::removeSpecial($_POST['o_jenisujian']);
		
		
		list($p_posterr,$p_postmsg) = $p_model::genPesertaUjian($conn,$o_jenisujian,$r_key);
	}
	
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	$a_filter = array($p_model::getListFilter('kelas',$r_key));
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=utf-8">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<div>
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				 
				<?php require_once('inc_headerkelas.php') ?>
			 
				<br>
				<?	if(!empty($p_postmsg)) { ?>
			 
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				 
				<div class="Break"></div>
				<?	} ?>
				
				<br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?php if($c_insert){ ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<?php } ?>
							<!--div class="right">
								<img title="Cetak Jurnal" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							</div-->
						</div>
					</header>
				 
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<?	/**********/
						/* HEADER */
						/**********/
					?>
					
					<tr>
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $i => $datakolom) {
								if(empty($datakolom['label']))
									continue;
								
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						
						
						
						<th width="30">Edit</th>
						<?	if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} ?>
						<th width="30">Absensi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) { 
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							 
							$j = $f;
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['jenisujian'] ?></td>
						<td><?= $row['jeniskuliah'] ?></td>
						<td><?= $row['kelompok'] ?></td> 
						<td><?= $row['tglujian'] ?></td>
						<td><?= $row['waktumulai'] ?></td>
						<td><?= $row['waktuselesai'] ?></td> 
						<td><?= $row['koderuang'] ?></td> 
						
						
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						</td>
						<?if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?		} ?>
						<td align="center"><img id="<?= $t_key ?>" title="Cetak Peserta Ujian" src="images/user.png" onclick="goPeserta(this)" style="cursor:pointer"></td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum+1 ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<br>
				<br>
				<?php if($c_insert){ ?>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1>Set Peserta UTS/UAS</h1>
						</div>
						
					</div>
				</header>
				<table width="<?= $p_tbwidth ?>" cellpadding="6" cellspacing="0" class="GridStyle">
					<tr class="NoHover NoGrid">		
						<td width="100" style="padding-left:200px"> &nbsp; <strong>Pilih jenis Ujian </strong></td>
						<td>
							<?=uCombo::jenisujian($conn,$o_jenisujian,'o_jenisujian','',false);?>
						</td>
						<td ><input type="button" value="Generate Peserta Ujian" onclick="goGenPeserta()"></td>
					</tr>
					
				</table>
				<?php } ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= Akademik::base64url_encode($r_key) ?>" >
				<input type="hidden" name="keyjadwal" id="keyjadwal">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>&pkey=<?= $r_key ?>";
var jurnalpage = "<?= Route::navAddress($p_jurnalpage) ?>&pkey=<?= $r_key ?>";
var jurnalrealisasipage = "<?= Route::navAddress('data_jurnal')?>&pkey=<?= $r_key ?>";
var realisasipage = "<?= Route::navAddress($p_detailrealisasipage)?>&pkey=<?= $r_key ?>";


$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goPrint() {
	goOpen('<?= $p_printpage ?>&key=' + document.getElementById("key").value);
}


function goDelete(elem) {
	var hapus = confirm("Peserta pada jadwal ini akan dihapus, Mohon lakukan Generate ulang peserta ujian\nyakin tetap menghapus?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}
function goGenPeserta(){
	document.getElementById("act").value = "genPeserta";
	goSubmit();
}
function goPeserta(elem) {
	// location.href = "<?= Route::navAddress('set_pesertakelas') ?>&key=" + elem.id;
	
	gParam = elem.id;
	showPage('keyjadwal','<?= Route::navAddress('rep_absensiujian') ?>');
}
</script>
</body>
</html>
