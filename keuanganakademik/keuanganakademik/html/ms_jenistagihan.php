<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('kelompoktagihan'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	$arr_flag = mCombo::arrFlagtagihan();
	$arr_kelompok = mKelompokTagihan::arrQuery($conn);

	// variabel request
	$r_kodekelompok = Modul::setRequest($_POST['kodekelompok'],'KODEKELOMPOK');
	$r_frekuensitagihan = Modul::setRequest($_POST['frekuensitagihan'],'FREKUENSITAGIHAN');
	
	// combo
	$l_kodekelompok = UI::createSelect('kodekelompok',$arr_kelompok,$r_kodekelompok,'',true,'onchange="goSubmit()"',true,'-- Semua Kelompok--');
	$l_frekuensitagihan =  UI::createSelect('frekuensitagihan',$arr_flag,$r_frekuensitagihan,'',true,'onchange="goSubmit()"',true,'-- Semua Jenis --');
	//uCombo::frekuensitagihan($conn,$r_frekuensitagihan,'frekuensitagihan','onchange="goSubmit()"',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'jenistagihan', 'label' => 'Jenis Tagihan', 'size' => 3, 'maxlength' => 10, 'notnull' => true);
    $a_kolom[] = array('kolom' => 'namajenistagihan', 'label' => 'Nama Jenis Tagihan', 'size' => 12, 'maxlength' => 50, 'notnull' => false);
	$a_kolom[] = array('kolom' => 'kodetagihan', 'label' => 'Kode<br>Tagihan', 'size' => 1, 'maxlength' => 2, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'frekuensitagihan', 'label' => 'Digenerate',  'notnull' => true,'type'=>'S','option'=>$arr_flag, 'add'=>'style="width:100px"');
	$a_kolom[] = array('kolom' => 'issks', 'label' => 'SKS ?',  'notnull' => true,'type'=>'C','option'=>array('1'=>''));
    $a_kolom[] = array('kolom' => 'jumlahangsur', 'label' => 'Angs. (Kali)', 'size' => 1, 'maxlength' => 2, 'notnull' => false, 'add'=>'style="width:15px"');
    $a_kolom[] = array('kolom' => 'jumlahangsur2017', 'label' => 'Angs. (Kali) <br>Angkatan >= 2017', 'size' => 1, 'maxlength' => 2, 'notnull' => false, 'add'=>'style="width:15px"');
	$a_kolom[] = array('kolom' => 'nominaldenda', 'label' => 'Denda (%)', 'size' => 3, 'maxlength' => 2);
	$a_kolom[] = array('kolom' => 'tgldeadline', 'label' => 'Tgl Deadline', 'size' => 2, 'maxlength' => 2, 'type' => 'N');
	$a_kolom[] = array('kolom' => 'kodekelompok', 'label' => 'Kode<br>Kelompok', 'notnull' => true,'type'=>'S','option'=>$arr_kelompok);
	/* $a_kolom[] = array('kolom' => 'ismaba', 'label' => 'Pdftr', 'type'=>'R', 'option'=>array(0=>'Tdk', -1=>'Ya'));
	$a_kolom[] = array('kolom' => 'ismala', 'label' => 'Mala', 'type'=>'R', 'option'=>array(0=>'Tdk', -1=>'Ya'));
	$a_kolom[] = array('kolom' => 'isreguler', 'label' => 'Reg', 'type'=>'R', 'option'=>array(0=>'Tdk', -1=>'Ya'));
	$a_kolom[] = array('kolom' => 'isparalel', 'label' => 'Par', 'type'=>'R', 'option'=>array(0=>'Tdk', -1=>'Ya'));
	$a_kolom[] = array('kolom' => 'isd3', 'label' => 'D3', 'type'=>'R', 'option'=>array(0=>'Tdk', -1=>'Ya'));
	$a_kolom[] = array('kolom' => 'issmu', 'label' => 'SMU', 'type'=>'R', 'option'=>array(0=>'Tdk', -1=>'Ya')); */
	$a_kolom[] = array('kolom' => 'ismaba', 'label' => 'Pdftr', 'type'=>'C', 'option'=>array(-1=>''));
	$a_kolom[] = array('kolom' => 'ismala', 'label' => 'Mala', 'type'=>'C', 'option'=>array(-1=>''));
	$a_kolom[] = array('kolom' => 'isreguler', 'label' => 'Reg', 'type'=>'R', 'type'=>'C', 'option'=>array(-1=>''));
	$a_kolom[] = array('kolom' => 'isparalel', 'label' => 'Par', 'type'=>'R', 'type'=>'C', 'option'=>array(-1=>''));
	$a_kolom[] = array('kolom' => 'isd3', 'label' => 'D3', 'type'=>'R', 'type'=>'C', 'option'=>array(-1=>''));
	$a_kolom[] = array('kolom' => 'issmu', 'label' => 'SMU', 'type'=>'R', 'type'=>'C', 'option'=>array(-1=>''));
	
	// properti halaman
	$p_title = 'Jenis Tagihan';
	$p_tbwidth = '950';
	$p_aktivitas = 'Master';
	
	$p_model = mJenistagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		if ($_POST['i_jumlahangsur'] < '1')
		list($p_posterr,$p_postmsg) = array(true, 'Jumlah split Tanggungan Minimal 1 Kali');
		else
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {

		$r_key = CStr::removeSpecial($_POST['key']);
		if ($_POST['u_jumlahangsur'] < '1')
		list($p_posterr,$p_postmsg) = array(true, 'Jumlah split Tanggungan Minimal 1 Kali');
		else		
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
	if(!empty($r_kodekelompok)) $a_filter[] = $p_model::getListFilter('kodekelompok',$r_kodekelompok);
	if(!empty($r_frekuensitagihan)) $a_filter[] = $p_model::getListFilter('frekuensitagihan',$r_frekuensitagihan);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);

	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Generate', 'combo' => $l_frekuensitagihan);
	$a_filtercombo[] = array('label' => 'Kelompok', 'combo' => $l_kodekelompok);

	
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
						?>
						<th id="<?= $datakolom['kolom'] ?>"><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	}
							if($c_edit or $c_delete) { ?>
						<th width="30">Edit</th>
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
					<tr align="center" valign="top" class="AlternateBG2">
						<td><?= $rowc[0]['input'] ?></td>
						<td><?= $rowc[1]['input'] ?></td>
						<td><?= $rowc[2]['input'] ?></td>
						<td><?= $rowc[3]['input'] ?></td>
						<td><?= $rowc[4]['input'] ?></td>
						<td><?= $rowc[5]['input'] ?></td>
						<td><?= $rowc[6]['input'] ?></td>
						<td><?= $rowc[7]['input'] ?></td>
						<td><?= $rowc[8]['input'] ?></td>
						<td><?= $rowc[9]['input'] ?></td>
						<td><?= $rowc[10]['input'] ?></td>
						<td><?= $rowc[11]['input'] ?></td>
						<td><?= $rowc[12]['input'] ?></td>
						<td><?= $rowc[13]['input'] ?></td>
						<td><?= $rowc[14]['input'] ?></td>
						<td><?= $rowc[15]['input'] ?></td>
						<td align="center" colspan=2>
							<img id="<?= $t_key ?>" title="Simpan Data" src="images/disk.png" onClick="goUpdate(this)" style="cursor:pointer">
						</td>
					</tr>
					<?		}
							else {
								$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr align="center" valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['jenistagihan'] ?></td>
						<td><?= $row['namajenistagihan'] ?></td>
						<td><?= $row['kodetagihan'] ?></td>
						<td><?= $row['frekuensitagihan'] ?></td>
						<td><?= $row['issks'] ?></td>
						<td><?= $row['jumlahangsur'] ?></td>
						<td><?= $row['jumlahangsur2017'] ?></td>
						<td><?= $row['nominaldenda'] ?></td>
						<td><?= $row['tgldeadline'] ?></td>
						<td><?= $row['kodekelompok'] ?></td>
						<td><?= $row['ismaba'] ?></td>
						<td><?= $row['ismala'] ?></td>
						<td><?= $row['isreguler'] ?></td>
						<td><?= $row['isparalel'] ?></td>
						<td><?= $row['isd3'] ?></td>
						<td><?= $row['issmu'] ?></td>
						<?		
								if($c_edit or $c_delete) { ?>
						<td align="center">
						<?			if($c_edit) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onClick="goEdit(this)" style="cursor:pointer">
						<?			} ?>
						</td>
						<td align="center">
						<?			if($c_delete) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onClick="goDelete(this)" style="cursor:pointer">
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
					<tr align="center" valign="top" class="LeftColumnBG NoHover">
						<?	$rowc = Page::getColumnEdit($a_kolom,'i_','onkeydown="etrInsert(event)"',$rowd);
							
							$a_insertreq = array();
							foreach($rowc as $rowcc) {
								if($rowcc['notnull'])
									$a_insertreq[] = $rowcc['id'];
						?>					
						<td><?= $rowcc['input'] ?></td>
						<?	} ?>
						<td align="center" colspan=2>
							<img title="Tambah Data" src="images/disk.png" onClick="goInsert()" style="cursor:pointer">
						</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
            
           <!-- <div style="clear:both"></div>
				<div>
					<fieldset style="background:#E0FFF3; border:1px solid #CCC;">
                        <legend> Keterangan </legend>
                        
                    </fieldset>
				</div>
		</div>-->
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

</script>
</body>
</html>
