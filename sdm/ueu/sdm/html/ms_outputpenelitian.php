<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
//	$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mastaktifitas'));
	require_once(Route::getUIPath('combo'));
	
	$p_model = mMastAktifitas;
	$p_dbtable = 'lv_outputpenelitian';
	$p_key = 'kodeoutput';
	
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeoutput', 'label' => 'Kode', 'size' => 5, 'maxlength' => 15, 'align' => 'center', 'notnull' => true);
	$a_kolom[] = array('kolom' => 'outputpenelitian', 'label' => 'Output Penelitian', 'size' => 40, 'maxlength' => 100, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namakegiatan', 'label' => 'Indeks Angka Kredit','type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 100, 'class' => 'ControlRead');
	$a_kolom[] = array('kolom' => 'idkegiatan', 'type' => 'H');

	// properti halaman
	$p_title = 'Daftar Output Penelitian';
	$p_tbwidth = 800;
	$p_aktivitas = 'BIODATA';
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST,$p_dbtable);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key,$p_dbtable,$p_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
		
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'kodeoutput';
	
	$sql = $p_model::listQueryOutputPenelitian();
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,'',$sql);
	print_r("j");
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
						<?	list($t_sort) = explode(',',$r_sort);
							trim($t_sort);
							list($t_col,$t_dir) = explode(' ',$t_sort);
							
							foreach($a_kolom as $datakolom) {
								if($t_col == $datakolom['kolom'])
									$t_sortimg = '<img src="images/'.(empty($t_dir) ? 'asc' : $t_dir).'.gif">';
								else
									$t_sortimg = '';
								
								if($datakolom['type'] == 'H')
									continue;
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="50">Aksi</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,$p_key);
							
							if($t_key == $r_edit and $c_edit) {
								$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);								
								$a_updatereq = array();
					?>
					<tr valign="top" class="AlternateBG2">
						<?		foreach($rowc as $j => $rowcc) {
									if($rowcc['notnull'])
										$a_updatereq[] = $rowcc['id'];
								}
						?>
						<td align="center"><?= Page::getDataInputInc($rowc,'u_kodeoutput') ?></td>
						<td><?= Page::getDataInputInc($rowc,'u_outputpenelitian') ?></td>
						<td>
							<?= Page::getDataInputInc($rowc,'u_namakegiatan') ?>
							<?= Page::getDataInputInc($rowc,'u_idkegiatan') ?>
							&nbsp;<img src="images/magnify.png" title="Pilih indeks kegiatan" style="cursor:pointer" onclick="showIndeks('u_idkegiatan','u_namakegiatan')">
						</td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onclick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else {
								$rowc = Page::getcolumnrow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								
								if($a_kolom[$j]['type'] == 'H')
									continue;
						?>					
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?		}
								if($c_edit or $c_delete) { ?>
						<td align="center">
						<?			if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(this)" style="cursor:pointer">
						<?			}
									if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<?			} ?>
						</td>
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
						<?	
							$rowc = Page::getColumnEdit($a_kolom,'i_','onkeydown="etrInsert(event)"');
							$a_insertreq = array();
							foreach($rowc as $j => $rowcc) {
								if($rowcc['notnull'])
									$a_insertreq[] = $rowcc['id'];
							}
						?>
						<td align="center"><?= Page::getDataInputInc($rowc,'i_kodeoutput') ?></td>
						<td><?= Page::getDataInputInc($rowc,'i_outputpenelitian') ?></td>
						<td>
							<?= Page::getDataInputInc($rowc,'i_namakegiatan') ?>
							<?= Page::getDataInputInc($rowc,'i_idkegiatan') ?>
							&nbsp;<img src="images/magnify.png" title="Pilih indeks kegiatan" style="cursor:pointer" onclick="showIndeks('i_idkegiatan','i_namakegiatan')">
						</td>
						<td align="center">
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

function showIndeks(id,nama){
	win = window.open("<?= Route::navAddress('pop_penilaian').'&m=2&b=II&id='?>"+id+"&nama="+nama,"popup_penilaian","width=650,height=500,scrollbars=1");
	win.focus();
}
</script>
</body>
</html>

