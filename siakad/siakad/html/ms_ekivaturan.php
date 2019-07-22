<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('kurikulum'));
	require_once(Route::getModelPath('ekivaturan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_kurikulumlama = Modul::setRequest($_POST['kurikulumlama']);
	$r_kurikulumbaru = Modul::setRequest($_POST['kurikulumbaru'],'KURIKULUM');
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_kurikulumlama = uCombo::kurikulum($conn,$r_kurikulumlama,'kurikulumlama','onchange="goSubmit()"',false);
	$l_kurikulumbaru = uCombo::kurikulum($conn,$r_kurikulumbaru,'kurikulumbaru','onchange="goSubmit()"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodemklama', 'label' => 'Kode MK', 'type' => 'S', 'option' => mKurikulum::mkKurikulumUnit($conn,$r_kurikulumlama,$r_unit));
	$a_kolom[] = array('kolom' => 'namamklama', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'skslama', 'label' => 'SKS');
	$a_kolom[] = array('kolom' => 'kodemkbaru', 'label' => 'Kode MK', 'type' => 'S', 'option' => mKurikulum::mkKurikulumUnit($conn,$r_kurikulumbaru,$r_unit));
	$a_kolom[] = array('kolom' => 'namamkbaru', 'label' => 'Nama MK');
	$a_kolom[] = array('kolom' => 'sksbaru', 'label' => 'SKS');
	
	// properti halaman
	$p_title = 'Ekivalensi Mata Kuliah';
	$p_tbwidth = 900;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mEkivAturan;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan unit dan kurikulum
		$a_kolom[] = array('kolom' => 'kodeunitlama', 'value' => $r_unit);
		$a_kolom[] = array('kolom' => 'kodeunitbaru', 'value' => $r_unit);
		$a_kolom[] = array('kolom' => 'thnkurikulum', 'value' => $r_kurikulumlama);
		$a_kolom[] = array('kolom' => 'tahunkurikulumbaru', 'value' => $r_kurikulumbaru);
		$a_kolom[] = array('kolom' => 'relasi', 'value' => 'O');
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi unit dan kurikulum
		array_pop($a_kolom);
		array_pop($a_kolom);
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
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	// mendapatkan data
	$a_filter[] = $p_model::getListFilter('thnkurikulumlama',$r_kurikulumlama);
	$a_filter[] = $p_model::getListFilter('thnkurikulumbaru',$r_kurikulumbaru);
	$a_filter[] = $p_model::getListFilter('kodeunitlama',$r_unit);
	$a_filter[] = $p_model::getListFilter('kodeunitbaru',$r_unit);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Kurikulum Lama', 'combo' => $l_kurikulumlama);
	$a_filtercombo[] = array('label' => 'Kurikulum Baru', 'combo' => $l_kurikulumbaru);
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
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
					
					if(!empty($a_filtercombo)) {
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>px;box-sizing:border-box">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<? $t_filter = $a_filtercombo[0]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
										</tr>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong>Kurikulum </strong></td>
											<td><strong> : &nbsp; &nbsp; &nbsp; Lama : </strong> <?= $a_filtercombo[1]['combo'] ?> &nbsp; &nbsp; &nbsp; <strong>Baru :</strong> <?= $a_filtercombo[2]['combo'] ?></td>
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
						<th colspan="3">Kurikulum <?= $r_kurikulumlama ?></th>
						<th colspan="3">Kurikulum <?= $r_kurikulumbaru ?></th>
						<?	if($c_edit) { ?>
						<th width="30" rowspan="2">Edit</th>
						<?	}
							if($c_delete) { ?>
						<th width="30" rowspan="2">Hapus</th>
						<?	} ?>
					</tr>
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
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						$t_skslama = $t_sksbaru = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							// total sks
							$t_skslama += $row['skslama'];
							$t_sksbaru += $row['sksbaru'];
							
							if($t_key == $r_edit and $c_edit) {
								$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);
								
								$a_updatereq = array();
								foreach($rowc as $rowcc) {
									if($rowcc['notnull'])
										$a_updatereq[] = $rowcc['id'];
								}
					?>
					<tr valign="top" class="AlternateBG2">
						<td colspan="3"><?= Page::getDataInputOnly($rowc,'u_kodemklama') ?></td>
						<td colspan="3"><?= Page::getDataInputOnly($rowc,'u_kodemkbaru') ?></td>
						<td align="center" colspan="2">
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else {
								$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?		foreach($rowc as $rowcc) {
									list($rowcc) = explode(' - ',$rowcc);
						?>					
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
							}
						?>					
						<td colspan="3"><?= Page::getDataInputOnly($rowc,'i_kodemklama') ?></td>
						<td colspan="3"><?= Page::getDataInputOnly($rowc,'i_kodemkbaru') ?></td>
						<td align="center" colspan="2">
							<img title="Tambah Data" src="images/disk.png" onclick="goInsert()" style="cursor:pointer">
						</td>
					</tr>
					<?	} ?>
					<tr>
						<th colspan="2">Total SKS Kurikulum <?= $r_kurikulumlama ?></th>
						<th><?php echo $t_skslama ?></th>
						<th colspan="2">Total SKS Kurikulum <?= $r_kurikulumbaru ?></th>
						<th><?php echo $t_sksbaru ?></th>
						<?php if($c_edit or $c_delete) { ?>
						<th colspan="<?php echo ($c_edit and $c_delete) ? '2' : '1' ?>"></th>
						<?php } ?>
					</tr>
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