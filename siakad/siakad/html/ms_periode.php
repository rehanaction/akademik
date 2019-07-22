<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('periode'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	
	$l_bulan = mCombo::periodebulan(false,false);
	$l_tahun = mCombo::tahun(true);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode', 'size' => 5, 'maxlength' => 6, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'tgluts', 'label' => 'Tgl UTS', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'tgluas', 'label' => 'Tgl UAS', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'blnawal', 'label' => 'Bln Awal', 'type' => 'S', 'option' => $l_bulan);
	$a_kolom[] = array('kolom' => 'thnawal', 'label' => 'Thn Awal', 'type' => 'S', 'option' => $l_tahun);
	$a_kolom[] = array('kolom' => 'blnakhir', 'label' => 'Bln Awal', 'type' => 'S', 'option' => $l_bulan);
	$a_kolom[] = array('kolom' => 'thnakhir', 'label' => 'Thn Akhir', 'type' => 'S', 'option' => $l_tahun);

	// properti halaman
	$p_title = 'Daftar Periode';
	$p_tbwidth = 900;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mPeriode;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// mengakali
		$a_kolomtemp = $a_kolom;
		$a_kolom = array_slice($a_kolom,0,3);
		
		$a_kolom[] = array('kolom' => 'bulanawal');
		$a_kolom[] = array('kolom' => 'bulanakhir');
		
		$_POST['i_bulanawal'] = $_POST['i_thnawal'].$_POST['i_blnawal'];
		$_POST['i_bulanakhir'] = $_POST['i_thnakhir'].$_POST['i_blnakhir'];
		$namaperiode = Akademik::getNamaPeriode($_POST['i_periode']);
		$data['nama']=$namaperiode;
		$data['idnumber']=$_POST['i_periode'];
		$periodemoodle = $_POST['i_periode'];
		$data['parent']=31;
		$category = $p_model::getUCategoryMoodle($conn,$_POST['i_periode']);
		if(empty($category)){
			$p_model::addCategory($data);
			$unit = mUnit::InquiryUnit($conn);
			foreach ($unit as $row) {
				$datas['nama'] = $row['namaunit'];
				$datas['idnumber'] = $row['kodeunit']."|".$periodemoodle;
				if(!empty($row['kodeunitparent'])){
					$categoryunit = $p_model::getUCategoryMoodle($conn,$row['kodeunitparent']."|".$periodemoodle);
				}else{
					$categoryunit = $p_model::getUCategoryMoodle($conn,$periodemoodle);
				}
					$datas['parent']=$categoryunit[0]['id'];
					$p_model::addCategory($datas);
			}
		}
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		$a_kolom = $a_kolomtemp;
	}
	else if($r_act == 'update' and $c_edit) {
	
		// mengakali
		$a_kolomtemp = $a_kolom;
		$a_kolom = array_slice($a_kolom,0,3);
		
		$a_kolom[] = array('kolom' => 'bulanawal');
		$a_kolom[] = array('kolom' => 'bulanakhir');
		
		$_POST['u_bulanawal'] = $_POST['u_thnawal'].$_POST['u_blnawal'];
		$_POST['u_bulanakhir'] = $_POST['u_thnakhir'].$_POST['u_blnakhir'];
		
		$r_key = CStr::removeSpecial($_POST['key']);
		$namaperiode = Akademik::getNamaPeriode($_POST['u_periode']);

		$data['nama']=$namaperiode;
		$data['idnumber']=$_POST['u_periode'];
		$periodemoodle = $_POST['u_periode'];
		$data['parent']=31;
		$category1 = new stdClass();
		$category1->name=$data['nama'];	
		$category1->parent=$data['parent'];					
		$category1->description='<p>'.$data['nama'].'</p>';
		$category1->idnumber=$data['idnumber'];					
		$category1->descriptionformat=1;			
		$category = $p_model::getUCategoryMoodle($conn,$_POST['u_periode']);
		$categories=array();
		if(empty($category)){
			$categories= array($category1);
			//$p_model::addCategory2($categories);
			$unit = mUnit::InquiryUnit($conn);
			$a=1;
			foreach ($unit as $row) {
				$category1 = new stdClass();
				$category1->name=$row['namaunit'];
				$category1->idnumber = $row['kodeunit']."|".$periodemoodle;
				if(!empty($row['kodeunitparent'])){
					$categoryunit = $p_model::getUCategoryMoodle($conn,$row['kodeunitparent']."|".$periodemoodle);
				}else{
					$categoryunit = $p_model::getUCategoryMoodle($conn,$periodemoodle);
				}
					$category1->parent=$categoryunit[0]['id'];
					$categories=array($category1);
					$a++;	
			}
			$p_model::addCategory2($categories);
		}
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
		
		$a_kolom = $a_kolomtemp;
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
	$r_sort = Page::setSort($_POST['sort']);
	
	$a_data = $p_model::getListData($conn,$a_kolom,$r_sort);
	
	// array periode
	$a_semester = mCombo::semester(false);
	$a_tahun = mCombo::tahun(false);
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
						<?	} ?>
						<th>Nama Periode</th>
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
							
							if($t_key == $r_edit and $c_edit) {
								$rowc = Page::getColumnEdit($a_kolom,'u_','onkeydown="etrUpdate(event)"',$row);
								
								$a_updatereq = array();
					?>
					<tr valign="top" class="AlternateBG2">
						<?		foreach($rowc as $rowcc) {
									if($rowcc['notnull'])
										$a_updatereq[] = $rowcc['id'];
						?>					
						<td nowrap><?= $rowcc['input'] ?></td>
						<?		} ?>
						<td>
							<?= Akademik::getNamaPeriode($row['periode']) ?>
							<? // = $a_semester[substr($row['periode'],-1)].' '.$a_tahun[substr($row['periode'],0,4)] ?>
						</td>
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
						<?		} ?>
						<td>
							<?= Akademik::getNamaPeriode($row['periode']) ?>
							<? // = $a_semester[substr($row['periode'],-1)].' '.$a_tahun[substr($row['periode'],0,4)] ?>
						</td>
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
					<tr class="LeftColumnBG">
						<?	$rowc = Page::getColumnEdit($a_kolom,'i_','onkeydown="etrInsert(event)"');
							
							$a_insertreq = array();
							foreach($rowc as $rowcc) {
								if($rowcc['notnull'])
									$a_insertreq[] = $rowcc['id'];
						?>					
						<td><?= $rowcc['input'] ?></td>
						<?	} ?>
						<td>(tahun+1/2/3) cth : 20041</td>
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

</script>
</body>
</html>