<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_copy = $a_auth['canother']['C'];
	
	
	// include
	require_once(Route::getModelPath('jadwalujian'));
	require_once(Route::getUIPath('combo'));
	
	

	// properti halaman
	$p_title = 'Data Jadwal UJian Seleksi';
	$p_tbwidth = 700;
	$p_aktivitas = 'KULIAH';
	$p_detailpage = Route::getDetailPage();
	
	$p_model = mJadwalUjian;
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('label' => 'Hari');
	$a_kolom[] = array('kolom' => 'tgltes', 'label' => 'Tanggal tes');
	$a_kolom[] = array('kolom' => 'kuota', 'label' => 'Peserta/Kuota');
	$a_kolom[] = array('kolom' => 'status', 'label' => 'Status');
	$hari=array('1'=>'Senin','2'=>'Selasa','3'=>'Rabu','4'=>'Kamis','5'=>'Jumat','6'=>'Sabtu','7'=>'Minggu');
	
	$p_colnum = count($a_kolom)+2;
	$r_startweek 	= Modul::setRequest($_POST['minggu_mulai'],'minggu_mulai');
	$r_startmonth 	= Modul::setRequest($_POST['bulan_mulai'],'bulan_mulai');
	$r_startyear 	= Modul::setRequest($_POST['tahun_mulai'],'tahun_mulai');
	$r_startmonth2 	= Modul::setRequest($_POST['bulan_mulai2'],'bulan_mulai2');
	$r_startyear2 	= Modul::setRequest($_POST['tahun_mulai2'],'tahun_mulai2');
	
	$l_startmonth 	= uCombo::getMonth($conn,$r_startmonth,'','bulan_mulai','onchange="goSubmit()"',true);
	$l_startmonth2 	= uCombo::getMonth($conn,$r_startmonth2,'','bulan_mulai2');
	$l_endmonth 	= uCombo::getMonth($conn,$r_periode,'','bulan_selesai');
	$l_startyear	= uCombo::tahun($conn,$r_startyear,'','tahun_mulai','onchange="goSubmit()"',true);
	$l_startyear2	= uCombo::tahun($conn,$r_startyear2,'','tahun_mulai2');
	$l_endyear		= uCombo::tahun($conn,$r_periode,'','tahun_selesai');
	$l_startweek	= uCombo::getWeek($conn,$r_startweek,'','minggu_mulai','onchange="goSubmit()"',true);
	$l_endweek		= uCombo::getWeek($conn,$r_periode,'','minggu_selesai');
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		$del=$p_model::deleteSomeJadwal($conn,$r_key);
		if($del){
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
			//$p_posterr=false;
			//$p_postmsg="detail dihapus";
		}else{
			$p_posterr=true;
			$p_postmsg="Gagal Hapus, Sudah Ada Peserta Yang Mengambil Jadwal Tersebut";
		}
	}
	if($r_act == 'copybulanan' and $c_copy) {
		$start = CStr::removeSpecial($_POST['bulan_mulai2']);
		$end = CStr::removeSpecial($_POST['bulan_selesai']);
		$start_th = CStr::removeSpecial($_POST['tahun_mulai2']);
		$end_th = CStr::removeSpecial($_POST['tahun_selesai']);
		$copy = $p_model::copyJadwalPerbulan($conn,$start,$start_th,$end,$end_th);
		if($copy){
			$p_posterr=false;
			$p_postmsg="Proses Copy Jadwal Bulanan Sukses";
		}else{
			$p_posterr=true;
			$p_postmsg="Proses Copy Gagal, Data Sudah Ada";
		}
	}
	if($r_act == 'copymingguan' and $c_copy) {
		$copy = $p_model::copyJadwalPerminggu($conn,$r_startweek,$r_startmonth,$r_startyear);
		if($copy){
			$p_posterr=false;
			$p_postmsg="Proses Copy Jadwal Mingguan Sukses";
		}else{
			$p_posterr=true;
			$p_postmsg="Proses Copy Gagal, Data Sudah Ada";
		}
	}
	
	else if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	if(!empty($r_startmonth)) $a_filter[] 	= $p_model::getListFilter('bulan_mulai',$r_startmonth);
	if(!empty($r_startweek)) $a_filter[] 	= $p_model::getListFilter('minggu_mulai',$r_startweek);
	if(!empty($r_startyear)) $a_filter[] 	= $p_model::getListFilter('tahun_mulai',$r_startyear);
	//if(!empty($r_startyear2)) $a_filter[] 	= $p_model::getListFilter('tahun_mulai2',$r_startyear);
	//if(!empty($r_startmonth2)) $a_filter[] 	= $p_model::getListFilter('bulan_mulai2',$r_startyear);
	
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);
	$a_jadwal=$p_model::getArrJadwal($conn);
	$p_lastpage = Page::getLastPage();
	$p_time = Page::getListTime();
	$p_rownum = Page::getRowNum();
	$p_pagenum = ceil($p_rownum/$r_row);
	
	//print_r($a_data);
	
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
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?	/**************/
					/* JUDUL LIST */
					/**************/
					
					if(!empty($p_title) and false) {
				?>
				<center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
				<br>
				<?	} ?>
				<?php if ($c_copy) { ?>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="8">Salin Jadwal Untuk Satu bulan Kedepan</td>
					</tr>
					<tr class="NoHover NoGrid">		
						<td width="90"> &nbsp; <strong>Jadwal Bulan </strong></td>
						<td width="15" align="center"><strong>:</strong></td>
						<td width="150"><?=$l_startmonth2."&nbsp;".$l_startyear2?></td>
						<td width="50">&nbsp;</td>
						<td width="90"> <strong>Salin Ke Bulan </strong></td>
						<td width="15" align="center"><strong>:</strong></td>
						<td width="150"><?=$l_endmonth."&nbsp;".$l_endyear?></td>
						<td>
							<input type="button" value="Salin" onclick="goCopyBulanan()">
						</td>		
					</tr>
					
				</table>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="8">Salin Jadwal Per Satu Minggu</td>
					</tr>
					<tr class="NoHover NoGrid">		
						<td width="120"> &nbsp; <strong>Jadwal Minggu Ke </strong></td>
						<td width="15" align="center"><strong>:</strong></td>
						<td width="350"><?=$l_startweek."&nbsp;".$l_startmonth."&nbsp;".$l_startyear?></td>
						<!--td width="10">&nbsp;</td>
						<td width="130"> <strong>Salin Pada Minggu Ke </strong></td>
						<td width="15" align="center"><strong>:</strong></td>
						<td width="150"><?=$l_endweek."&nbsp;".$l_endmonth?></td-->
						<td>
							<input type="button" value="Salin Ke Minggu Berikutnya" onclick="goCopyMingguan()">
						</td>		
					</tr>
					
				</table>
				</center>
				<?php } ?>
				<br>
				<?php require_once('inc_listfilter.php'); ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}?>
					
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
							<?	if(!empty($r_page) or $c_insert) { ?>
							<div class="right">
								<?php
									if($c_insert) { ?>
								<div class="addButton" style="float:left;margin-left:10px; margin:right:10px;" onClick="goNew()">+</div>
								<?	} ?>
							</div>
							<?	} ?>
								
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
						<th>Jadwal</th>
						<?php	if($c_edit) { ?>
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
							$t_key = $p_model::getKeyRow($row);
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
							
							// cek mengulang
							if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							if($row['kuota']>=$row['jumlahpeserta'])
								$bg_td='#FF0';
							else
								$bg_td='#F00';
							
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<?php $nohari=date('N',strtotime($row['tgltes']))?>
						<td><?=$hari[$nohari]?></td>
						<td><?= date('d-m-Y',strtotime($row['tgltes'])); ?></td>
						<td bgcolor="<?=$bg_td?>"><?= (empty($row['jumlahpeserta'])?'0':$row['jumlahpeserta']).'/'.$row['kuota'] ?></td>
						<td><?= $row['isaktif']==0?'Tidak Aktif':'Aktif' ?></td>
						<td>
							<?php
								if(!empty($a_jadwal[$row['idjadwal']])){
								foreach($a_jadwal[$row['idjadwal']] as $data){
									echo $data."</br>";
								}
								}
							?>
						</td>
						<?	if($c_edit) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Tampilkan Detail" src="images/edit.png" onClick="goDetail(this)" style="cursor:pointer"></td>
						<?	}
							if($c_delete) { ?>
						<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onClick="goDelete(this)" style="cursor:pointer"></td>
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
							Halaman <?= $r_page ?> / <?= $p_pagenum ?>
						</div>
						</td>
					</tr>
					<?	} ?>
				</table>
				<? if(!empty($r_page)) { ?>
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				<br><br>
				
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">

<?	if(!empty($r_page)) { ?>
var lastpage = <?= '-1' // $rs->LastPageNo() ?>;
<?	} ?>
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
	
	/* $("#xls").change(function() {
		goUpXLS();
	}); */
});

function goCopyBulanan(){
	document.getElementById("act").value = "copybulanan";
	goSubmit();
}
function goCopyMingguan(){
	document.getElementById("act").value = "copymingguan";
	
	goSubmit();
}
</script>
</body>
</html>
