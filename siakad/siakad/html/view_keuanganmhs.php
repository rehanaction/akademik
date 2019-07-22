<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete']; 
	
	// include 
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('tagihan')); 
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$legendstatusmhs=true;
	$r_act = $_POST['act'];
	if(!Akademik::isMhs()) {
		$display="block";
		if(empty($r_key)) {
			// cek aksi
			$r_nim = CStr::removeSpecial($_REQUEST['npm']);
			if(Akademik::isDosen())
			{
				$r_nip = Modul::getUserName();
				$display="none";
			}
			else
				$r_nip = '';
			
			if($r_act == 'first')
				$r_key = mMahasiswa::getFirstNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'prev')
				$r_key = mMahasiswa::getPrevNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'next')
				$r_key = mMahasiswa::getNextNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'last')
				$r_key = mMahasiswa::getLastNIM($conn,$r_nim,$r_nip);
			else
				$r_key = $r_nim;
		}
	}

	else{
		$r_key = Modul::getUserName();
		$display="none";
	}	
	$r_periode = Akademik::getPeriode();
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	
	// koneksi database
	$connh = Query::connect('h2h');
	$connh->debug = $conn->debug;
	
	// properti halaman
	$p_title = 'Rincian Keuangan Mahasiswa';
	$p_tbwidth = 740;
	$p_lwidth = 740;
	$p_aktivitas = 'SPP'; 
	$p_detailpage = Route::getDetailPage();
	$p_model = mTagihan;
	
	
	// cek periode dan isi biodata
	$a_postmsg = array();
	
	if(!empty($a_postmsg)) {
		$p_posterr = $p_sposterr;
		$p_postmsg = implode('<br>',$a_postmsg);
	}

	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'periode', 'label' => 'Periode');
	$a_kolom[] = array('kolom' => 'semester', 'label' => 'Semester');
	$a_kolom[] = array('kolom' => 'semester', 'label' => 'Status');
	$a_kolom[] = array('kolom' => 'billamount', 'label' => 'Jumlah', 'type' => 'N');
	$a_kolom[] = array('kolom' => 'trxdatetime', 'label' => 'Tanggal Bayar', 'type' => 'D');
	$a_kolom[] = array('kolom' => 'lunas', 'label' => 'Lunas');

	$p_colnum = count($a_kolom)+2;
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($connh,$r_key);
	}
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = '';
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	 
	$a_data = $p_model::getPagerDataHerWithKey($connh,$a_kolom,$r_row,$r_page,$r_sort,$r_key);
	 
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row); 
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
	<script type="text/javascript" src="scripts/perwalian.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<div style="float:left; width:18%; ">
			<?php require_once('inc_headermahasiswa.php'); ?>
			</div>
		<form name="pageform" id="pageform" method="post">
			 
			<?php require_once('inc_headermhs_krs.php') ?>
			 
			<br>
	<div style="width:860px;margin-left:120px">
		<div style="float:left;width:<?= $p_lwidth ?>px">
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_lwidth ?>px">
				<?= $p_postmsg ?>
			</div>
		 
			<div class="Break"></div>
			<?	}
				 ?>
			 
				<header style="width:<?= $p_lwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							<h1><?= $p_title ?></h1>
						</div>
						<div class="right">
							<img title="Cetak KRS" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
						</div>
					</div>
				</header>
			</center>
			<? 
				/**************
				*  LIST DATA *
				**************/
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
						<?	}  ?>
						 
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						$total=0;
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row);
							
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						 <td><?= Akademik::getNamaPeriode($row['periode']) ?></td>
						 <td><?= $row['semester'] ?></td> 
						 <td><?= $row['status'] ?></td>
						 <td>Rp. <?= $row['billamount'] ?></td>
						 <td><?= $row['accesstime']?></td>
						 <td><?= empty($row['lunas']) ? '' : '<img src="images/check.png">' ?></td>
						 <? $total+=$row['billamount']; ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data tidak ada</td>
					</tr>
					<?	}
					
						/**********/
						/* FOOTER */
						/**********/
						
					?>
					<tr>
						<td colspan="3" align="center"><b>Total:</b></td>
						<td><b>Rp. <?= $total; ?></b></td>
				</table>
				<? require_once ('inc_legendstatusmhs.php'); ?>
		</div>
		
	</div>
	
			<input type="hidden" name="act" id="act">
			<input type="hidden" name="key" id="key">
			<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
			<? if(Akademik::isDosen()) { ?>
			<input type="hidden" name="nip" id="nip" value="<?= Modul::getUserName() ?>">
			<? } ?>
		</form>
		
		</div>
		
	</div>
	
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	<? if(Akademik::isDosen()) { ?>
	$("#mahasiswa").xautox({strpost: "f=acmhswali", targetid: "npmtemp", postid: "nip"});
	<? } else { ?>
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "npmtemp"});
	<? } ?>
 
	});
 
</script>
</body>
</html>
