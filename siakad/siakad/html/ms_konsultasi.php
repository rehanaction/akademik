<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('konsultasi'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	if(Akademik::isMhs())
		$r_nim = Modul::getUserName();
	else{
		$r_nim = Modul::setRequest($_POST['nim'],'NIM');
		//$r_pass = Modul::setRequest($_POST['password']);
	}
	
	if(Akademik::isDosen())
		$r_nip =  Modul::getUserIDPegawai();
	else
		$r_nip = Modul::setRequest($_POST['nip'],'NIP');
	
	
	
	// properti halaman
	$p_title = 'Konsultasi';
	$p_tbwidth = 640;
	$p_aktivitas = 'FORUM';
	
	$p_model = mKonsultasi;
	$p_key = $p_model::key;
	
	$a_jenis = $p_model::getJenisKonsultasi();
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'tglkonsultasi', 'label' => 'Tanggal', 'type' => 'D', 'width' => 100, 'readonly' => true);
	$a_kolom[] = array('kolom' => 'isikonsultasi', 'label' => 'Isi Konsultasi', 'type' => 'A', 'cols' => 60, 'rows' => 10);
	$a_kolom[] = array('kolom' => 'jeniskonsultasi', 'label' => 'Jenis', 'type' => 'S', 'option' => $a_jenis);
	
	$p_colnum = count($a_kolom)+2;
	
	if(empty($r_nip) or empty($r_nim)) {
		$c_insert = false;
		$c_edit = false;
		$c_delete = false;
	}
	if(!empty($r_nim))
		$r_mahasiswa = $r_nim.' - '.Akademik::getNamaMahasiswa($conn,$r_nim);
	if(!empty($r_nip) ){

		if (Akademik::isDosen()){
			$r_dosen = Akademik::getNamaPegawai($conn,$r_nip);	
		}else{
			$r_dosen = $_POST['dosen'];
		}
	}

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan nim dan nip
		$a_kolom[] = array('kolom' => 'nim', 'value' => $r_nim);
		$a_kolom[] = array('kolom' => 'nip', 'value' => $r_nip);
		$a_kolom[] = array('kolom' => 'periode', 'value' => Akademik::getPeriode());
		
		//check jumlah konsul FR.46
		$jmlkonsul = mKonsultasi::getCountKonsul($conn,$r_nip,$r_nim, Akademik::getPeriode());

		if($jmlkonsul > 3){
			list($p_posterr,$p_postmsg) = array(true,'jumlah konsultasi lebih dari 3.');
		}else{
			list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		}
		// buang lagi nim dan nip
		array_pop($a_kolom);
		array_pop($a_kolom);
		array_pop($a_kolom);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}else if($r_act == 'search'){
		
		//cek password dan set session
		//$passmhs = mMahasiswa::getPassMhs($conn,$r_nim);
		//if(md5(!$r_pass) != $passmhs){
		//	list($p_posterr,$p_postmsg) = array(true,'Password mahasiswa tidak cocok');
			
		//}
		
	}else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	if(!empty($r_nip) and !empty($r_nim)) {
		// mendapatkan data ex
		$r_sort = Page::setSort($_POST['sort']);
		
		// mendapatkan data
		$a_filter[] = $p_model::getListFilter('nim',$r_nim);
		$a_filter[] = $p_model::getListFilter('nip',$r_nip);
		
		$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	}
	else
		$a_data = array();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forinplace.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
									<?	if(!Akademik::isMhs()) { ?>
										<tr>		
											<td width="90"><strong>Mahasiswa</strong></td>
											<td>
												<strong> : </strong>
												<?= UI::createTextBox('mahasiswa',$r_mahasiswa,'ControlStyle',40,40) ?>
											</td>
										</tr>
										<tr>		
											<td ><strong>Password Mhs</strong></td>
											<td>
												<strong> : </strong>
												<?= UI::createPasswordBox('password',null,'ControlStyle',40,40) ?>
											</td>
										</tr>
									<?	}
										if(!Akademik::isDosen()) { ?>
										<tr>		
											<td><strong>Dosen Wali</strong></td>
											<td>
												<strong> : </strong>
												<?= UI::createTextBox('dosen',$r_dosen,'ControlStyle',40,40) ?>
											</td>
										</tr>
									<?	} ?>
										<tr>
											<td colspan="2">
												<input type="hidden" id="nim" name="nim" value="<?= $r_nim ?>">
												<input type="hidden" id="nip" name="nip" value="<?= $r_nip ?>">
												<input type="hidden" id="nimtemp" value="<?= $r_nim ?>">
												<input type="hidden" id="niptemp" value="<?= $r_nip ?>">
												<input type="button" value="Tampilkan" onclick="goSearch()">
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
					<br>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0">
						<tr valign="top">
							<td width="70"><strong>Mahasiswa</strong></td>
							<td width="10" align="center"><strong>:</strong></td>
							<td width="200"><?= $r_mahasiswa ?></td>
							<td width="70"><strong>Dosen Wali</strong></td>
							<td width="10" align="center"><strong>:</strong></td>
							<td><?= $r_dosen ?></td>
						</tr>
					</table>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
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
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit) { ?>
						<th width="30">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30">Hapus</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							if($t_key == $r_edit and $c_edit) {
								$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);
								
								$a_updatereq = array();
					?>
					<tr valign="top" class="AlternateBG2">
						<?		foreach($rowc as $rowcc) {
									if($rowcc['notnull'])
										$a_updatereq[] = $rowcc['id'];
						?>					
						<td><?= $rowcc['input'] ?></td>
						<?		} ?>
						<td align="center" colspan="2">
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else {
								$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?		foreach($rowc as $rowcc) { ?>					
						<td><?= $rowcc ?></td>
						<?		}
								if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(this)" style="cursor:pointer"></td>
						<?		}
								if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?		} ?>
					</tr>
					<?		}
						}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						if($c_insert) { ?>
					<tr valign="top" class="LeftColumnBG NoHover">
						<?	$rowc = Page::getColumnEdit($a_kolom,'i_','onkeydown="etrInsert(event)"');
							
							$a_insertreq = array();
							foreach($rowc as $rowcc) {
								if($rowcc['notnull'])
									$a_insertreq[] = $rowcc['id'];
						?>
						<td><?= $rowcc['input'] ?></td>
						<?	} ?>
						<td align="center" colspan="2">
							<img title="Tambah Data" src="images/disk.png" onclick="goInsert()" style="cursor:pointer">
						</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var insertreq = "<?= @implode(',',$a_insertreq) ?>";
var updatereq = "<?= @implode(',',$a_updatereq) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmhswali", targetid: "nim", postid: "nip"});
	$("#dosen").xautox({strpost: "f=acdosenwali", targetid: "nip", postid: "nim"});
	
	
	<? /* $("#nim").change(function() {
		if(this.value != "" && $("#nimtemp").val() != "") {
			$("#nip").val("");
			$("#niptemp").val("");
			$("#dosen").val("");
		}
		$("#nimtemp").val(this.value);
	});
	
	$("#nip").change(function() {
		if(this.value != "" && $("#niptemp").val() != "") {
			$("#nim").val("");
			$("#nimtemp").val("");
			$("#mahasiswa").val("");
		}
		$("#niptemp").val(this.value);
	}); */ ?>
});

function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}

function goSearch(){
	document.getElementById("act").value = "search";
	goSubmit();
}

</script>
</body>
</html>
