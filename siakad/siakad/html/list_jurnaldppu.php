<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	//$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_open = $a_auth['canother']['O'];
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('kuliah'));
	
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Rencana Pembelajaran Semester';
	$p_aktivitas = 'ABSENSI';
	$p_tbwidth = "100%";
	$p_listpage = 'list_absensi';
	$p_printpage = 'rep_jurnal';
	$p_detailrealisasipage = 'data_jurnalp';
	$p_detailpage = 'data_jurnalp';
	
	//$conn->debug=true;
	$p_model = mKuliah;
	
	if(Akademik::isDosen() or Akademik::isPPA())
		$pengajar=true;
		
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if(empty($r_key))
		Route::navigate($p_listpage);
	$a_genhonor=array('0'=>'','-1'=>'<img src="images/check.png">');
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'waktumulai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'waktuselesai', 'maxlength' => 4, 'size' => 3, 'format' => 'CStr::formatJam');
	$a_kolom[] = array('kolom' => 'nohari', 'type' => 'S', 'option' => mCombo::hari()); // tidak untuk dipilih
	$a_kolom[] = array('kolom' => 'koderuang', 'type' => 'S', 'option' => mCombo::ruang($conn));
	
	$f = 3;
	$a_kolom[] = array('kolom' => 'perkuliahanke', 'label' => 'Pertemuan ke', 'size' => 2, 'maxlength' => 2, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'tglkuliah', 'label' => 'Tanggal/Jam', 'type' => 'D', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'nipdosen', 'label' => 'Dosen/Ruang', 'type' => 'S', 'option' => mKelas::dosenPengajar($conn,$r_key), 'add' => 'style="width:150px"');
	$a_kolom[] = array('kolom' => 'jeniskuliah', 'label' => 'Jenis', 'type' => 'S', 'option' => $p_model::jenisKuliah($conn));
	$a_kolom[] = array('kolom' => 'jumlahpeserta', 'label' => 'Peserta', 'type' => 'N', 'size' => 3, 'maxlength' => 3);
 
	$a_kolom[] = array('kolom' => 'topikkuliah', 'label' => 'Rencana Materi Perkuliahan', 'type' => 'A', 'rows' => 3, 'cols' => 13, 'maxlength' => 100);
	$a_kolom[] = array('kolom' => 'isonline', 'label' => 'Pelaksanaan', 'type' => 'S', 'option' => array('0'=>'Tatap Muka','-1'=>'Online'));
	$a_kolom[] = array('kolom' => 'keterangan', 'label' => 'Materi/Kegiatan', 'type' => 'A', 'rows' => 5, 'cols' => 50, 'maxlength' => 255,'notnull'=>true);
	$a_kolom[] = array('kolom' => 'kesandosen', 'label' => 'Catatan Dosen', 'type' => 'A', 'rows' => 3, 'cols' => 13, 'maxlength' => 100);
    $a_kolom[] = array('kolom' => 'statusperkuliahan', 'label' => 'Status', 'type' => 'S', 'option' => $p_model::statusKuliah($r_jenis));			
    //$a_kolom[] = array('kolom' => 'validhonorkuliah', 'label' => 'Gen. Honor', 'type' => 'S', 'option' => $a_genhonor);			

	// properti halaman tambahan
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey);
	}else if($r_act == 'upload' and $c_edit) {
		$tipe=array('image/jpeg','image/jpg','image/gif','image/png');
		$ext=array('image/jpg'=>'jpg','image/jpeg'=>'jpeg','image/gif'=>'gif','image/png'=>'png');
		$file_types=$_FILES['filejurnal']['type'];
		$file_name=str_replace('|',';',$r_key).'.'.$ext[$file_types];
		if(in_array($file_types,$tipe) && !empty($tipe)){
			$upload=move_uploaded_file($_FILES['filejurnal']['tmp_name'],'uploads/jurnal/'.$file_name);
			if($upload){
				$record=array();
				$record['filejurnal']=$file_name;
				list($p_posterr,$p_postmsg) = mKelas::updateCRecord($conn,"",$record,$r_key);				
				
			}else{
				$p_posterr=true;
				$p_postmsg='Upload Gagal';
			}
		}else{
			$p_posterr=true;
			$p_postmsg='Pastikan Tipe Gambar Benar, Upload Gagal';
		}
	}if($r_act == 'open' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$record = array();
		$record['isopen'] = -1;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true);
	}
	else if($r_act == 'close' and $c_edit) {
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$record = array();
		$record['isopen'] = 0;
		
		list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_subkey,true);
	}
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	$a_filter = array($p_model::getListFilter('kelas',$r_key));
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	//$r_realisasi=mKuliah::getRealisasi($conn, $r_key);
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
				 <!--center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<?	if($c_edit) { ?>
						<td align="center" colspan="2">Upload File Scan Jurnal Kuliah Sebagai Bukti Jurnal Manual</td>
						<? } ?>
					</tr>
					<tr class="NoHover NoGrid">
						<?	if($c_edit) { ?>
						<td width="55"> &nbsp;
							<strong>Upload </strong>
						</td>
						<td>
							<strong> : </strong> <input type="file" name="filejurnal" id="filejurnal" size="30" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpload()">
						</td>
						<?	}?>
					</tr>
				</table>
				</center-->
				<br>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?php if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<?php } ?>
							<div class="right">
								<img title="Cetak Jurnal" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							</div>
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
						<th colspan="7">Rencana Perkuliahan</th>
						<th colspan="3">Realisasi Perkuliahan</th>
						
					</tr>
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
							//$openwithtime=$p_model::cekRangeTime($row['tglkuliahrealisasi'],$row['waktumulairealisasi'],$row['tglkuliahrealisasi'],$row['waktuselesairealisasi']);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[++$j]?></td>
						<td>
							<?= $rowc[2] ?>, <?= $rowc[++$j] ?>
							<div class="Break"></div>
							<?= $rowc[0] ?> - <?= $rowc[1] ?>
						</td>
						<td>
							<?= $rowc[++$j] ?>
							<div class="Break"></div>
							Ruang : <?= $rowc[3] ?>
						</td>
						<td><?= $row['jeniskuliah'] ?></td>
						<td><?= $row['jumlahpeserta'] ?></td> 
						<td><?= $row['topikkuliah'] ?></td>
						<td><?= $row['isonline'] ?></td>
						<td><?= $row['keterangan'] ?></td>
						<td><?= $row['kesandosen'] ?></td> 
						<td><?= $row['statusperkuliahan'] ?></td> 
						<?php /*<td align="center"><?php if($row['statusperkuliahan']=='Terjadwal'){ ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
							<?php } ?></td> */?>
						
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
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

function goNewRealisasi() {
	location.href = realisasipage;
}
function goDetailRealisasi(elem) {
	var open=elem.name;
	location.href = detailpage + "&key=" + elem.id+'&jenis=R'+'&open='+open;
}

function goPreviewRealisasi(elem) {
	location.href = jurnalrealisasipage+ "&key=" + elem.id;
 
}
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
function goUpload(elem) {
	document.getElementById("act").value = "upload";
	goSubmit();
}
function goRealisasi(elem) {
	location.href = realisasipage + "&key=" + elem.id;
}

function goDelete(elem) {
	var hapus = confirm("Apakah anda yakin akan menghapus data ini?");
	if(hapus) {
		document.getElementById("act").value = "delete";
		document.getElementById("subkey").value = elem.id;
		goSubmit();
	}
}
function goOpenJurnal(elem) {
	document.getElementById("act").value = (elem.checked ? 'open' : 'close');
	document.getElementById("subkey").value = elem.value;
	
	goSubmit();
}
</script>
</body>
</html>
