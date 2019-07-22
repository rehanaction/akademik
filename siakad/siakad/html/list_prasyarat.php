<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('prasyarat'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_kurikulum = Modul::setRequest($_POST['kurikulum'],'KURIKULUM');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
	$r_matkul = Modul::setRequest($_POST['kodematkul'],'MATAKULIAH');
	
	
	$r_kurikulumcopy = Modul::setRequest($_POST['kurikulumcopy']);
	
	// combo
	$l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',false);
	$l_matkul = uCombo::matkul($conn,$r_matkul,$r_kurikulum, $r_unit, 'kodematkul','onchange="goSubmit()"',true);
	
	$l_kurikulumcopy = uCombo::kurikulum($conn,$r_kurikulumcopy,'kurikulumcopy','',false);
	
	// properti halaman
	$p_title = 'Data Prasyarat Mata Kuliah';
	$p_tbwidth = 820;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mPrasyarat;
	
	// combo input
	$a_kodemk = $p_model::mkKurikulum($conn,$r_kurikulum,$r_unit);
	$a_kodemk_2 = $p_model::mkKurikulum2($conn,$r_kurikulum);
	$a_nilaimin = mCombo::nAngkaKurikulum($conn,$r_kurikulum);
	$a_relasi = $p_model::syarat();
	
	$l_kodemk1 = UI::createSelect('kodemk1',$a_kodemk,'','ControlStyle',true,'style="width:300px"');

	$l_kodemk2 = UI::createSelect('kodemk2',$a_kodemk_2,'','ControlStyle',true,'style="width:300px"');
	$l_nilaimin = UI::createSelect('nilaimin',$a_nilaimin,'','ControlStyle');
	$l_relasi = UI::createSelect('relasi',$a_relasi,'','ControlStyle');
		
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		$record = CStr::cStrFill($_POST);
		$record['thnkurikulum'] = $r_kurikulum;
		$record['kodeunit'] = $r_unit;
		
		list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'copy' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::copy($conn,$r_unit,$r_kurikulum,$r_kurikulumcopy);
		
		// load combo kurikulum
		if(!$p_posterr) {
			$r_kurikulum = Modul::setRequest($r_kurikulumcopy,'KURIKULUM');
			$l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
		}
	}
	
	// mendapatkan data
	$a_filter = array();
	if(!empty($r_kurikulum)) $a_filter[] = $p_model::getListFilter('thnkurikulum',$r_kurikulum);
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
	if(!empty($r_matkul)) $a_filter[] = $p_model::getListFilter('matkul',$r_matkul);
	
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort,$a_filter);
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Kurikulum', 'combo' => $l_kurikulum);
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'MataKuliah', 'combo' => $l_matkul);
	
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
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
			<form name="pageform" id="pageform" method="post">
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
						<table width="<?= $p_tbwidth-10 ?>" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%"> 
									<table width="100%" cellspacing="0" cellpadding="4"  >
										<tr>
											<td colspan="2" align="center"><strong>Pencarian Data Kurikulum</strong></td>
										</tr>
										<? $t_filter = $a_filtercombo[0]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?></strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td>
										</tr>
										<? $t_filter = $a_filtercombo[1]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td> 
										</tr>
										<? $t_filter = $a_filtercombo[2]; ?>
										<tr>		
											<td width="50" style="white-space:nowrap"><strong><?= $t_filter['label'] ?> </strong></td>
											<td <?= empty($t_filter['width']) ? '' : ' width="'.$t_filter['width'].'"' ?>><strong> : </strong><?= $t_filter['combo'] ?></td> 
										</tr>
										
									</table>
								</td>
							</tr>
						</table>
					</div>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;  margin-top:10px">
						<table width="<?= $p_tbwidth-230 ?>" cellpadding="5" cellspacing="0" style="text-align:center">
							<tr>
								<td colspan="2" align="center;"><strong>Salin Data Kurikulum</strong></td>
							</tr> 
							<tr>
								<td valign="top" width="50%"> <strong>Salin ke Kurikulum</strong>&nbsp; &nbsp; &nbsp;  <?= $l_kurikulumcopy ?></td> 
								<td> <input type="button" value="Salin Kurikulum" onclick="goSalin()"> </td> 
							</tr>
						</table>
					</div>
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
						<th colspan="2">Mata Kuliah</th>
						<th colspan="5">Prasyarat</th>
					</tr>
					<tr>
						<th>Kode</th>
						<th>Nama</th>
						<th>Kode</th>
						<th>Nama</th>
						<th>Nilai Min</th>
						<th>Relasi</th>
						<?	if($c_edit) { ?>
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
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['kodemk1'] ?></td>
						<td><?= $row['namamk1'] ?></td>
						<td><?= $row['kodemk2'] ?></td>
						<td><?= $row['namamk2'] ?></td>
						<td><?= $row['nilaimin'] ?></td>
						<td><?= $a_relasi[$row['relasi']] ?></td>
						<?	if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="8" align="center">Data kosong</td>
					</tr>
					<?	}
						if($c_insert) { ?>
					<tr class="LeftColumnBG" valign="top">
						<td colspan="2"><?= $l_kodemk1 ?></td>
						<td colspan="2"><?= $l_kodemk2 ?></td>
						<td><?= $l_nilaimin ?></td>
						<td><?= $l_relasi ?></td>
						<td align="center" colspan="2">
							<img title="Tambah Data" src="images/disk.png" onclick="goInsert()" style="cursor:pointer">
						</td>
					</tr>
					<?	} ?>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}

</script>
</body>
</html>