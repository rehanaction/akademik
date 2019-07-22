<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	// include
	require_once(Route::getModelPath('deposit'));
	require_once(Route::getModelPath('pembayaran'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Pembayaran Tagihan dengan Deposit dan Voucher';
	$p_aktivitas = 'SPP';
	$p_tbwidth = '800';
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label'=>'No', 'width'=>'3%');
	$a_kolom[] = array('kolom' => 'nim', 'label'=>'NIM', 'align'=>'center');
	$a_kolom[] = array('kolom' => 'nama', 'label'=>'Nama Mahasiswa');
	$a_kolom[] = array('kolom' => 'namaunit', 'label'=>'Prodi');
	$a_kolom[] = array('kolom' => 'nominaldeposit', 'label'=>'Deposit', 'type'=>'N', 'align'=>'right');
	$a_kolom[] = array('kolom' => 'nominalvoucher', 'label'=>'Voucher', 'type'=>'N', 'align'=>'right');
	$a_kolom[] = array('kolom' => 'nominaltagihan', 'label'=>'Tagihan', 'type'=>'N', 'align'=>'right');
	
	$p_model = mDeposit;
	$p_colnum = count($a_kolom);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'bayar' and $c_edit) {
		$r_nim = $_POST['key'];
		
		$conn->BeginTrans();
		
		list($p_posterr,$p_postmsg) = mPembayaran::bayarBSM($conn,$r_nim,0,null,null,true);
		
		$conn->CommitTrans($p_posterr ? false : true);
		
		if(substr($p_postmsg,0,6) == 'Kurang')
			$p_postmsg = "Pembayaran Tagihan ".$p_postmsg;
		else
			$p_postmsg = "Pembayaran Tagihan ".($p_posterr ? 'Gagal' : 'Berhasil');
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// biar nggak ambil sort default
	if(empty($r_sort))
		$r_sort = 'y.nim';
	
	// mendapatkan data
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$p_model::listQueryMhsDeposit(),$p_model::listConditionMhsDeposit());

	$p_lastpage = Page::getLastPage();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<link href="style/jquery.treeview.css" rel="stylesheet" type="text/css">
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<?php if ($p_mhspage) require_once('inc_headermahasiswa.php'); ?>
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfiltertree.php') ?>
				<?	if(!empty($a_filtertree)) { ?>
				<div style="float:left;width:760px">
				<?	} ?>
				<?php require_once('inc_listfilter.php'); ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header<?php echo empty($p_tbwidth) ? '' : ' style="width:'.$p_tbwidth.'px;display:table"' ?>>
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
				<table cellpadding="4" cellspacing="0" class="GridStyle" align="center" style="width:<?= empty($p_tbwidth) ? '100%' : $p_tbwidth.'px' ?>">
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
						<th width="30">Bayar</th>
						<?	} ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $row['nim'];
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								
								if(!empty($a_total) and $a_total['index'] == $j)
									$t_total += $rowcc;
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	}
							if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Bayar Tagihan" src="images/disk.png" onclick="goBayar(this)" style="cursor:pointer"></td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum+1 ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						
						/**********/
						/* FOOTER */
						/**********/
						
						if(!empty($r_page)) { ?>
					<tr>
						<td colspan="<?= $p_colnum+1 ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?><?php // / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<?	if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<?	} ?>
				
				<?	if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<?	if(!empty($a_filtertree)) { ?>
				</div>
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<?	if(!empty($a_filtertree)) { ?>
<script type="text/javascript" src="scripts/jquery.cookie.js"></script>
<script type="text/javascript" src="scripts/jquery.treeview.js"></script>
<script type="text/javascript" src="scripts/jquery-ui.js"></script>
<?	} ?>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";
var cookiename = '<?= $i_page ?>.accordion';

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	<?	if(!empty($a_filtertree)) { ?>
	initFilterTree();
	<?	} ?>
});

<?php if($c_edit) { ?>
function goBayar(elem) {
	$("#key").val(elem.id);
	$("#act").val("bayar");
	goSubmit();
}
<?php } ?>

</script>
</body>
</html>