<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('poinprestasi'));
	require_once(Route::getModelPath('jenisprestasi'));
	require_once(Route::getModelPath('tingkatprestasi'));
	require_once(Route::getModelPath('kategoriprestasi'));
	require_once(Route::getModelPath('jenispeserta'));
	require_once(Route::getUIPath('combo'));
	
	$a_jenisprstasi = mJenisprestasi::getArray($conn);
	$a_tingkatprstasi = mTingkatprestasi::getArray($conn);
	$a_kategoriprstasi = mKategoriprestasi::getArray($conn);
	$a_jenispeserta = mJenispeserta::getArray($conn);

	$r_jenisawal = cstr::removeSpecial($_POST['jenisawal']);
	$r_tingkatawal = cstr::removeSpecial($_POST['tingkatawal']);
	
	$r_jenistujuan = cstr::removeSpecial($_POST['jenistujuan']);
	$r_tingkattujuan = cstr::removeSpecial($_POST['tingkattujuan']);

	/* filter */
	$a_ftingkatprestasi = array('' => '-- Semua Tingkat --') + $a_tingkatprstasi;
	$r_ftingkatprestasi = Modul::setRequest($_POST['ftingkatprestasi'],'FTINGKATPRESTASI',$a_ftingkatprestasi);
	$l_ftingkatprestasi = UI::createSelect('ftingkatprestasi',$a_ftingkatprestasi,$r_ftingkatprestasi,'ControlStyle',true,'onchange="goSubmit()"');

	$a_fjenisprestasi = array('' => '-- Semua Jenis --') + $a_jenisprstasi;
	$r_fjenisprestasi = Modul::setRequest($_POST['fjenisprestasi'],'FJENISPRESTASI',$a_fjenisprestasi);
	$l_fjenisprestasi = UI::createSelect('fjenisprestasi',$a_fjenisprestasi,$r_fjenisprestasi,'ControlStyle',true,'onchange="goSubmit()"');

	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodejenisprestasi', 'label' => 'Jenis Prestasi', 'type' => 'S','option'=>$a_jenisprstasi);
	$a_kolom[] = array('kolom' => 'kodetingkatprestasi', 'label' => 'Tingkat Prestasi', 'type' => 'S','option'=>$a_tingkatprstasi);
	$a_kolom[] = array('kolom' => 'kodekategoriprestasi', 'label' => 'Prestasi', 'type' => 'S','option'=>$a_kategoriprstasi);
	$a_kolom[] = array('kolom' => 'kodejenispeserta', 'label' => 'Peserta', 'type' => 'S','option'=>$a_jenispeserta);
	$a_kolom[] = array('kolom' => 'poin', 'label' => 'Poin','size'=>5,'maxlength'=>5);
	
	// properti halaman
	$p_title = 'Setting Poin Prestasi';
	$p_tbwidth = 650;
	$p_aktivitas = 'BIODATA';
	
	$p_model = mPoinprestasi;
	$p_key = $p_model::key;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	else if ($r_act == 'salinpoin' and $c_edit){
		list($p_posterr,$p_postmsg) = $p_model::copyPoinPrestasi($conn,$r_jenisawal,$r_jenistujuan,$r_tingkatawal,$r_tingkattujuan);
	} 
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);

	// mendapatkan data
	if(!empty($r_ftingkatprestasi)) $a_filter[] = $p_model::getListFilter('t.kodetingkatprestasi',$r_ftingkatprestasi);
	if(!empty($r_fjenisprestasi)) $a_filter[] = $p_model::getListFilter('t.kodejenisprestasi',$r_fjenisprestasi);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Jenis Prestasi', 'combo' => $l_fjenisprestasi);
	$a_filtercombo[] = array('label' => 'Tingkat Prestasi', 'combo' => $l_ftingkatprestasi);
	
	$p_colnum = count($a_kolom)+2;
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
	<script type="text/javascript" src="scripts/jquery.maskedinput.min.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	}
					
					/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? foreach($a_filtercombo as $t_filter) { ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>		
										</tr>
										<? } ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	} ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4" border="0">
										<tr>		
											<td>
												<table>
													<tr>
														<td>Jenis Prestasi</td>
														<td><?= UI::createSelect('jenisawal',$a_jenisprstasi,$r_jenisawal)?></td>
													</tr>
													<tr>
														<td>Tingkat Prestasi</td>
														<td><?= UI::createSelect('tingkatawal',$a_tingkatprstasi,$r_tingkatawal)?></td>
													</tr>
												</table>
											</td>
											<td>salin ke </td>		
											<td>
											<td>
												<table>
													<tr>
														<td>Jenis Prestasi</td>
														<td><?= UI::createSelect('jenistujuan',$a_jenisprstasi,$r_jenistujuan)?></td>
													</tr>
													<tr>
														<td>Tingkat Prestasi</td>
														<td><?= UI::createSelect('tingkattujuan',$a_tingkatprstasi,$r_tingkattujuan)?></td>
													</tr>
												</table>
											</td>
											
											
											</td>		
										</tr>
									</table>
								</td>
							</tr>
							<tr>
								<td align="center" colspan="3"><input type="button" value="salin poin prestasi" onClick="goCopyPoin()"></td>
							</tr>

						</table>
					</div>
				</center>
				<br>
				<?php	if(!empty($p_postmsg)) { ?>
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
						<td><?= $row['namajenisprestasi'] ?></td>
						<td><?= $row['namatingkatprestasi'] ?></td>
						<td><?= $row['namakategoriprestasi'] ?></td>
						<td><?= $row['namajenispeserta'] ?></td>
						<td align="center"><?= $row['poin'] ?></td>
						<?		if($c_edit) { ?>
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
	
	// handle focus
	// $("[id^='i_']:first").focus();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});
function goCopyPoin(){
	var conf = confirm("Apakah anda yakin Melakukan salin poin kegiatan?");
	if(conf) {
		document.getElementById("act").value = "salinpoin";
		goSubmit();
	}
	
}
</script>
</body>
</html>
