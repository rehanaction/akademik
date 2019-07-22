<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('tagihanva'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// cek mahasiswa
	$ismhs = (Akademik::isMhs() ? true : false);
	if(!$ismhs) {
		// variabel request
		$r_unit = Modul::setRequest($_POST['kodeunit'],'UNIT');
		
		// combo
		$a_unit = mCombo::unit($conn);
		$l_unit = uCombo::combo($a_unit,$r_unit,'kodeunit','onchange="goSubmit()"',false);
	}
	
	// properti halaman
	$p_title = 'Daftar Transaksi Pembayaran';
	$p_tbwidth = '100%';
	$p_aktivitas = 'DEFAULT';
	$p_model = mTagihanVA;
	$p_key = $p_model::key;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => ':no', 'label' => 'No', 'width' => '1%');
	$a_kolom[] = array('kolom' => 'billingid', 'label' => 'ID', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'noid', 'label' => 'NIM/No Pendaftar', 'align' => 'center');
	$a_kolom[] = array('kolom' => 'nama', 'label' => 'Nama Mahasiswa/Pendaftar');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Jurusan');
	$a_kolom[] = array('kolom' => 'kodeva', 'label' => 'Kode VA');
	$a_kolom[] = array('kolom' => 'trxamount', 'label' => 'Jumlah Bayar', 'type' => 'N', 'align' => 'right');
	$a_kolom[] = array('kolom' => 'namastatus', 'label' => 'Status', 'align' => 'center');
	
	// ada aksi
	$r_act = $_POST['act'];
	$r_key = $_POST['key'];
	
	if($r_act == 'check' and !empty($r_key)) {
		// tidak pakai transaksi karena hanya inquiry
		$req = array();
		$req['trx_id'] = $r_key;
		$req['get_payment'] = 1;
		
		$resp = Pay::inquiryBilling($req);
		
		// cek status
		$record = array();
		if($resp['status'] == Pay::ERROR_OK) {
			$record['kodeva'] = $resp['data']['virtual_account'];
			$record['expiredtime'] = $resp['data']['datetime_expired'];
			
			if($resp['data']['va_status'] == '2') {
				if(empty($resp['data']['datetime_payment_iso8601']))
					$record['status'] = 'C';
				else
					$record['status'] = 'L';
			}
			else
				$record['status'] = 'A';
		}
		else if($resp['status'] == Pay::ERROR_NOTFOUND) {
			$record['kodeva'] = null;
			$record['status'] = 'S';
		}
		else
			$err = true;
		
		if(empty($err)) {
			// mulai transaksi
			$conn->BeginTrans();
			
			// update tagihan va
			$err = $p_model::updateRecord($conn,$record,$r_key);
			
			// jika ada pembayaran, masukkan
			if(empty($err) and !empty($resp['data']['payment_list'])) {
				foreach($resp['data']['payment_list'] as $rowp) {
					$err = $p_model::bayarVA($conn,$r_key,$rowp);
					if(!empty($err))
						break;
				}
			}
			
			// selesai transaksi
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
		}
		
		$p_posterr = $err;
		$p_postmsg = 'Pengecekan status tagihan VA '.(empty($err) ? 'berhasil' : 'gagal');
		
		if(empty($err)) {
			$a_flash = array();
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		} 
	}
	else if($r_act == 'delete' and $c_delete and !empty($r_key)) {
		$conn->BeginTrans();
		
		// update dulu, bisa dirollback kalau update billing gagal
		$record = array();
		$record['status'] = 'C';
		
		$err = $p_model::updateRecord($conn,$record,$r_key);
		
		// kirim ke bank
		if(empty($err)) {
			$resp = Pay::cancelBilling($r_key);
			$err = ($resp['status'] == Pay::ERROR_OK ? false : true);
		}
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
		
		$p_posterr = $err;
		$p_postmsg = 'Pembatalan tagihan VA '.($ok ? 'berhasil' : 'gagal');
		
		if(empty($err)) {
			$a_flash = array();
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		}
	}
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if($ismhs)
		$a_filter[] = $p_model::getListFilter('noid',Modul::getUserName());
	else if(!empty($r_unit))
		$a_filter[] = $p_model::getListFilter('kodeunit',$r_unit);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	if(empty($p_pagenum)) $p_pagenum = 1;
	
	// membuat filter
	if(!$ismhs) {
		$a_filtercombo = array();
		$a_filtercombo[] = array('label' => 'Unit', 'combo' => $l_unit);
	}
	
	// inc_list
	$p_colnum = count($a_kolom)+3;
	
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
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?php require_once('inc_listfilter.php'); ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>;box-sizing:border-box">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>;display:table">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<div class="right">
								<?php require_once('inc_listnavtop.php'); ?>
								<?	if($c_insert) { ?>
								<div class="addButton payButton" style="float:left;margin-left:10px" onClick="goNew()">Bayar Tagihan</div>
								<?	} ?>
							</div>
						</div>
					</header>
				</center>
				<?	/*************/
					/* LIST DATA */
					/*************/
				?>
				<table cellpadding="4" cellspacing="0" class="GridStyle" align="center" style="width:<?= $p_tbwidth ?>">
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
						<th width="40">Detail</th>
						<th width="40">Cek</th>
						<th width="40">Batal</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?	foreach($rowc as $j => $rowcc) {
								$t_align = $a_kolom[$j]['align'];
								if(!empty($t_align))
									$t_align = ' align="'.$t_align.'"';
								
								// status tagihan
								if($j == 7) {
									switch($rowcc) {
										case 'Belum': $color = 'grey'; break;
										case 'Lunas': $color = 'green'; break;
										case 'Batal': $color = 'red'; break;
										default: $color = null;
									}
									
									if(!empty($color))
										$rowcc = '<span style="color:'.$color.'">'.$rowcc.'</span>';
									// $rowcc = '<strong>'.$rowcc.'</strong>';
								}
						?>
						<td<?= $t_align ?>><?= $rowcc ?></td>
						<?	} ?>
						<td align="center">
							<?php if(!empty($row['noid'])) { ?>
							<img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onclick="goDetail(this)" style="cursor:pointer">
							<?php } ?>
						</td>
						<td align="center"><img id="<?= $t_key ?>" title="Cek Status" src="images/magnify.png" onclick="goCek(this)" style="cursor:pointer"></td>
						<td align="center">
							<?php if($row['status'] == 'A') { ?>
							<img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer">
							<?php } ?>
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
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="right" class="FootBG">
						<div style="float:left">
							Record : <?= uCombo::listRowNum($r_row,'onchange="goSubmit()"') ?>
						</div>
						<div style="float:right">
							Halaman <?= $r_page ?> / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
				</table>
				<?php require_once('inc_listnav.php'); ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

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

function goCek(elem) {
	document.getElementById("act").value = "check";
	document.getElementById("key").value = elem.id;
	goSubmit();
}

</script>
</body>
</html>