<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('unsurnilai'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mUnsurNilai;
	
	// variabel request
	$r_kurikulum = Modul::setRequest($_POST['kurikulum'],'KURIKULUM');
	$r_kurikulumcopy = Modul::setRequest($_POST['kurikulumcopy']);
	$r_progpend = Modul::setRequest($_POST['progpend'],'PROGPEND');
	$r_tipekuliah = Modul::setRequest($_POST['tipekuliah'],'TIPEKULIAH');
	
	// combo
	$l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
	$l_kurikulumcopy = uCombo::kurikulum($conn,$r_kurikulumcopy,'kurikulumcopy','',false);
	$l_progpend = uCombo::programPendidikan($conn,$r_progpend,'progpend','onchange="goSubmit()"',false);
	
	$a_tipekuliah = $p_model::tipeKuliah();
	if(empty($r_tipekuliah)) $r_tipekuliah = key($a_tipekuliah);
	$l_tipekuliah = UI::createSelect('tipekuliah',$a_tipekuliah,$r_tipekuliah,'ControlStyle',true,'onchange="goSubmit()"');
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nounsurnilai', 'label' => 'No.', 'type' => 'NP', 'size' => 2, 'maxlength' => 2, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namasingkat', 'label' => 'Nama Singkat', 'size' => 10, 'maxlength' => 20, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namaunsurnilai', 'label' => 'Nama Unsur', 'size' => 30, 'maxlength' => 50, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'prosentasenilai', 'label' => 'Prosentase', 'type' => 'NP', 'size' => 3, 'maxlength' => 3, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Unsur Penilaian';
	$p_tbwidth = 500;
	$p_aktivitas = 'NILAI';
	
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan kurikulum dan progpend
		$a_kolom[] = array('kolom' => 'thnkurikulum', 'value' => $r_kurikulum);
		$a_kolom[] = array('kolom' => 'programpend', 'value' => $r_progpend);
		$a_kolom[] = array('kolom' => 'tipekuliah', 'value' => $r_tipekuliah);
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi kurikulum dan progpend
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
	}
	else if($r_act == 'copy' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::copy($conn,$r_kurikulum,$r_kurikulumcopy);
		
		// load combo kurikulum
		if(!$p_posterr) {
			$r_kurikulum = Modul::setRequest($r_kurikulumcopy,'KURIKULUM');
			$l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
		}
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	if(!empty($r_kurikulum)) $a_filter[] = $p_model::getListFilter('thnkurikulum',$r_kurikulum);
	if(!empty($r_progpend)) $a_filter[] = $p_model::getListFilter('progpend',$r_progpend);
	if(!empty($r_tipekuliah)) $a_filter[] = $p_model::getListFilter('tipekuliah',$r_tipekuliah);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	$totalprosentase=0;
	foreach ($a_data as $data){
	$totalprosentase+=$data['prosentasenilai'];
	}
	if ($totalprosentase <100 or $totalprosentase >100){
	$totalmsg="Jumlah Prosentase Harus 100";
	}
	 
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Kurikulum', 'combo' => $l_kurikulum);
	$a_filtercombo[] = array('label' => 'Jenjang', 'combo' => $l_progpend);
	$a_filtercombo[] = array('label' => 'Jenis Kuliah', 'combo' => $l_tipekuliah);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forinplace.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/************************/
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
										<? $t_filter = $a_filtercombo[0]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
											<td width="200" style="white-space:nowrap"><strong>Salin ke Kurikulum : </strong><?= $l_kurikulumcopy ?> <input type="button" value="Salin" onclick="goSalin()"></td>
										</tr>
										<? $t_filter = $a_filtercombo[1]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td colspan="2" <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
										</tr>
										<? $t_filter = $a_filtercombo[2]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td colspan="2" <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	}
					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} 
				if (!empty($totalmsg)){ ?>
				<center>
				<span style="color:red; font-weight:bold">
					<?= $totalmsg ?>
				 </span>
				</center>
				<div class="Break"></div>
				<?
				}
				?>
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
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
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
});

function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}

</script>
</body>
</html>