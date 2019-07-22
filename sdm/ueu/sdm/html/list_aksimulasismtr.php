<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_edit = $a_auth['canupdate'];
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('angkakredit'));
	require_once(Route::getUIPath('combo'));
	
	// variabel esensial
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_insert = $a_auth['caninsert'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
		
	// properti halaman
	$p_title = 'Simulasi Perhitungan Angka Kredit Semester';
	$p_tbwidth = 800;
	$p_aktivitas = 'HISTORY';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mAngkaKredit;
	$p_key = 'periodeakreditasi,idpegawai';
	$p_dbtable = 'ak_skdosensmtr';
	
	// struktur view
	$a_kolom = array();

	$a_kolom[] = array('kolom' => 'tglsimulasi', 'label' => 'Tgl. Simulasi', 'type' => 'D', 'align' => 'center', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'semester', 'label' => 'Semester', 'width' => '150px');
	$a_kolom[] = array('kolom' => 'tahun', 'label' => 'Tahun', 'width' => '50px');

	$p_colnum = count($a_kolom)+7;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		$a_key = $r_key.'|'.$r_subkey;
		$where = 'idpegawai,periodeakreditasi,idpegawai';
		
		$p_posterr = $p_model::updateRWTAkreditasi($conn,$r_subkey);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$a_key,$p_dbtable,$where);

		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if(empty($r_sort)) $r_sort = 'periodeakreditasi desc';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	$sql = $p_model::listQuerySimulasiAKSMTR($r_key);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	$p_lastpage = Page::getLastPage();

	$a_nilaikredit = $p_model::getNilaiKredit($conn,$r_key);
	
	if(empty($p_detailpage))
		$p_detailpage = Route::getDetailPage();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpagerx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfilterajax.php'); ?>
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
							<?	if($c_insert) { ?>
							<div class="right">
								<div class="addButton" onClick="goNew()">+</div>
							</div>
							<? } ?>
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
						<?	} ?>
						<th width="80">Kredit IA</th>
						<th width="80">Kredit IB</th>
						<th width="80">Kredit II</th>
						<th width="80">Kredit III</th>
						<th width="80">Kredit IV</th>
						<th width="80">Status</th>
						<th width="80">Aksi</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row, $p_key);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="right"><?= $a_nilaikredit['IA'][$row['periodeakreditasi']]?></td>
						<td align="right"><?= $a_nilaikredit['IB'][$row['periodeakreditasi']]?></td>
						<td align="right"><?= $a_nilaikredit['II'][$row['periodeakreditasi']]?></td>
						<td align="right"><?= $a_nilaikredit['III'][$row['periodeakreditasi']]?></td>
						<td align="right"><?= $a_nilaikredit['IV'][$row['periodeakreditasi']]?></td>
						<td align="center"><?= $row['isvalid'] == 'Y' ? 'Sudah divalidasi' : 'Belum divalidasi'?></td>
						<td align="center">
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
						<?		
								if($c_delete and (Modul::getRole() == 'A' or $row['isvalid'] != 'Y')) { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
						<? }?>
						</td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goLimit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= Page::getTheLastPage();?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnavajax.php'); ?>
				<? } ?>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)(empty($_POST['scroll']) ? 220 : $_POST['scroll']) ?>">
				
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";
var reportpage = "<?= Route::navAddress('rep_akdupak') ?>";
var xtdid = "contents";
var sent = "key=<?= $r_key; ?>";

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
</script>
</body>
</html>
