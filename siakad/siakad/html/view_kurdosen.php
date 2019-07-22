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
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_kurikulum = Modul::setRequest($_POST['kurikulum'],'KURIKULUM');
	$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
	$r_kodemk=Modul::setRequest($_POST['kodemk']);
	
	$r_kurikulumcopy = Modul::setRequest($_POST['kurikulumcopy']);
	
	// combo
	$l_kurikulum = uCombo::kurikulum($conn,$r_kurikulum,'kurikulum','onchange="goSubmit()"',false);
	$l_unit = uCombo::unit($conn,$r_unit,'kodeunit','onchange="goSubmit()"',false);
	
	$l_kurikulumcopy = uCombo::kurikulum($conn,$r_kurikulumcopy,'kurikulumcopy','',false);
	
	// properti halaman
	$p_title = 'Data Kurikulum';
	$p_tbwidth = 640;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mKurikulum;
	
	// combo input
	//$a_kodemk = $p_model::mkKurikulum($conn,$r_kurikulum);
	
	//$l_kodemk = UI::createSelect('kodemk',$a_kodemk,'','ControlStyle',true,'style="width:400px"');
	$l_kodemk=UI::createTextBox('matkul','','ControlStyle',0,40,true, '', 'Cari Mata Kuliah');	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		if(empty($_POST['wajibpilihan']))
			$_POST['wajibpilihan'] = 'P';
		if(empty($_POST['paket']))
			$_POST['paket'] = 0;
		
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
	$a_data = $p_model::getDataPerSemester($conn,$r_kurikulum,$r_unit);
	//print_r($a_data);
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Kurikulum', 'combo' => $l_kurikulum);
	$a_filtercombo[] = array('label' => 'Prodi', 'combo' => $l_unit);
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
					<div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;">
						<span>
							<!--<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">-->
							<!--&nbsp;<?= $p_title ?> <div class="addButton right" onClick="goNew()">+</div>-->
						</span>
					</div>
				</center>
				<br>
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
									</table>
								</td>
							</tr>
						</table>
					</div>
					<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;  margin-top:10px">
						<table width="<?= $p_tbwidth-230 ?>" cellpadding="5" cellspacing="0" style="text-align:center">
							<tr>
								<!--<td colspan="2" align="center;"><strong>Salin Data Kurikulum</strong></td>-->
							</tr> 
							<tr>
								<!--<td valign="top" width="50%"> <strong>Salin ke Kurikulum</strong>&nbsp; &nbsp; &nbsp;  <?= $l_kurikulumcopy ?></td> -->
								<!--<td> <input type="button" value="Salin Kurikulum" onclick="goSalin()"> </td> -->
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
	<?php
		if($c_insert) {
	?>
	<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr class="DataBG">
			<td colspan="10" align="center">Tambah Data Mata Kuliah Kurikulum Baru</td>
		</tr>
		<tr>
			<th>Mata Kuliah</th>
			<th width="40">Sem</th>
			<th width="40">Sem. KRS</th>
			<th width="40">Wajib</th>
			<th width="40">Paket</th>
			 
			<th width="50">Aksi</th>
		</tr>
		<tr valign="top">
			<td><?= $l_kodemk ?></td>
			<td align="center"><?= UI::createTextBox('semmk','','ControlStyle',2,2) ?></td>
			<td align="center"><?= UI::createTextBox('semmk_old','','ControlStyle',2,2) ?></td>
			<td align="center"><input type="checkbox" name="wajibpilihan" value="W"></td>
			<td align="center"><input type="checkbox" name="paket" value="1"></td>
			 
			
			<td align="center">
				<img title="Tambah Data" src="images/disk.png" onclick="goInsert()" style="cursor:pointer">
			</td>
		</tr>
	</table>
	<br>
	<?php
		}
		
		$t = 0;
		$n_data = count($a_data);
		
		foreach($a_data as $t_semester => $t_data) {
			$t++;
	?>
	<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
		<tr class="DataBG">
			<td colspan="10" align="center">Semester <?= $t_semester ?></td>
		</tr>
		<tr>
			<th width="90">Kode</th>
			<th>Nama Matakuliah</th>
			<th width="40">SKS</th>
			<th width="40">Sem</th>
			<th width="40">Sem. KRS</th>
			<th width="40">Wajib</th>
			<th width="40">Paket</th>
			<!--<th width="40">Prasyarat</th>-->
			
			<?	if($c_edit) { ?>
			<th width="30">Edit</th>
			<?	}
				if($c_delete) { ?>
			<th width="30">Hapus</th>
			<?	} ?>
		</tr>
	<?php
			$i = 0;
			foreach($t_data as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				$t_key = $p_model::getKeyRow($row);
	?>
		<tr valign="top" class="<?= $rowstyle ?>">
			<td><?= $row['kodemk'] ?></td>
			<td><?= $row['namamk'] ?></td>
			<td align="center"><?= $row['sks'] ?></td>
			<td align="center"><?= $row['semmk'] ?></td>
			<td align="center"><?= $row['semmk_old'] ?></td>
			<td align="center"><?= $row['wajibpilihan'] ?></td>
			<td align="center"><?= empty($row['paket']) ? 'T' : 'Y' ?></td>
			<!--<td align="center"><img id="<?= $row['kodemk'] ?>" title="Prasyarat" src="images/link.png" onclick="goPrasyarat('<?= $row['kodemk'] ?>','<?= Route::navAddress('list_prasyarat') ?>', this)"  style="cursor:pointer"></td>-->
			
			<?	if($c_edit) { ?>
			<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer"></td>
			<?	}
				if($c_delete) { ?>
			<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
			<?	} ?>
		</tr>
	<?php
			}
	?>
	</table>
	<?php
			if($t < $n_data) {
	?>
	<br>
	<?php
			}
		}
	?>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="kodemk" id="kodemk">	
				<input type="hidden" name="kodematkul" id="kodematkul">				
			</form>
		</div>
	</div>
</div>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">
	
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
$(document).ready(function() {
	$("#matkul").xautox({strpost: "f=acmatkulkurikulum&kurikulum=<?=$r_kurikulum?>", targetid: "kodemk"});
	
});		
function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}

function goPrasyarat(kodemk) {
	document.getElementById("kodematkul").value = kodemk;
		goSubmitBlank('<?= Route::navAddress('list_prasyarat') ?>');

//	goSubmit();
}

</script>
</body>
</html>
