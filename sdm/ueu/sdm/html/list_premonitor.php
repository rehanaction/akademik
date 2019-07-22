<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('dashboard'));
	require_once(Route::getModelPath('presensi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_presensi = CStr::removeSpecial($_POST['tglpresensi']);
	if (empty($r_presensi)) $r_presensi = date("d-m-Y");
	$r_jenisabsensi = CStr::removeSpecial($_POST['absensi']);
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()" style="width:300px"',false);
	$a_jenisabsensi = mPresensi::getCAbsensi($conn);
	$l_jenisabsensi = UI::createSelect('absensi',$a_jenisabsensi,$r_jenisabsensi,'ControlStyle',true,'onchange="goSubmit()"');
		
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'nip', 'label' => 'NIP');
	$a_kolom[] = array('kolom' => 'namalengkap', 'label' => 'Nama', 'filter' => 'sdm.f_namalengkap(gelardepan,namatengah,namadepan,namabelakang,gelarbelakang)');
	
	$p_model = mPresensi;
	
	$n_day = date("N", strtotime(CStr::formatDate($r_presensi)));
	$a_hari = $p_model::hariAbsensi();
	


	// properti halaman
	$p_title = 'Monitor Presensi '.$a_hari[$n_day].', '.(CStr::formatDateInd(CStr::formatDate($r_presensi)));
	$p_tbwidth = 920;
	$p_aktivitas = 'TIME';
	$p_detailpage = Route::getDetailPage();
	$p_dbtable = 'pe_presensidet';
	$p_colnum = count($a_kolom)+4;
	
	// ada aksi
	$r_act = CStr::removeSpecial($_POST['act']);
	if($r_act == 'refresh')
		Modul::refreshList();
	
	// mendapatkan data ex
	$r_page = Page::setPage($_POST['page']);
	$r_row = Page::setRow($_POST['row']);
	$r_sort = Page::setSort($_POST['sort']);
	if (empty($r_sort)) $r_sort = 'nip';
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);
	
	// mendapatkan data
	if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);	
	if(!empty($r_presensi)) $a_filter[] = $p_model::getListFilter('tglpresensi',$r_presensi);	
	if(!empty($r_jenisabsensi)) $a_filter[] = $p_model::getListFilter('jenisabsensi',$r_jenisabsensi);	
	
	$sql = $p_model::listQueryMonitor();
		
	$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
	//print_r("a");		
	$p_lastpage = Page::getLastPage();
							
	$script = '&nbsp;&nbsp;<img src="images/cal.png" id="tglpresensi_trg" style="cursor:pointer;" title="Pilih Tanggal Presensi">
					<script type="text/javascript">
					Calendar.setup({
						inputField     :    "tglpresensi",
						ifFormat       :    "%d-%m-%Y",
						button         :    "tglpresensi_trg",
						align          :    "Br",
						singleClick    :    true
					});
				</script>';
	$button = ' <input type="button" name=bsubmit class="ControlStyle" value="Tampilkan" onClick="goSubmit()" />';
	
	$a_absensi = $p_model::aJenisCuti($conn);
	$a_aturan = $p_model::aturanHadir($conn);
	
	$a_graph = mDashboard::graphAbsenMinggu($conn, CStr::formatDate($r_presensi));
	
	// membuat filter
	$a_filtercombo = array();
	$a_filtercombo[] = array('label' => 'Unit Kerja', 'combo' => $l_unit);
	$a_filtercombo[] = array('label' => 'Jenis Absensi', 'combo' => $l_jenisabsensi);
	$a_filtercombo[] = array('label' => 'Tgl. Presensi', 'combo' => UI::createTextBox('tglpresensi',$r_presensi,'ControlRead',10,10,$c_edit,'readonly').$script.$button);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<link href="style/calendar.css" type="text/css" rel="stylesheet">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		<? if (count($a_absensi['kode']) > 0){
				foreach($a_absensi['kode'] as $kode){
		?>
		.label<?= $kode; ?> {
			background-color: #<?= $a_absensi['color'][$kode]; ?>;
			border-radius: 3px 3px 3px 3px;
			color: #FFFFFF;
			display: block;
			float: left;
			font-size: 11.05px;
			font-weight: bold;
			margin: 0 2px 2px 0;
			padding: 2px 4px 3px;
			text-shadow: 0 -1px 0 rgba(0, 0, 0, 0.25);
		}
		<? }} ?>
	</style>
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
								
								$t_width = $datakolom['width'];
								if(!empty($t_width))
									$t_width = ' width="'.$t_width.'"';
						?>
						<th id="<?= $datakolom['kolom'] ?>"<?= $t_width ?>><?= $datakolom['label'] ?> <?= $t_sortimg ?></th>
						<?	} ?>
						<th>Kehadiran Wajib</th>
						<th>Kehadiran Real</th>
						<th>Total Jam</th>
						<th>Status</th>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						if (count($a_data) > 0){
						foreach($a_data as $row) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
							$t_key = $p_model::getKeyRow($row,'tglpresensi');
							
							$j = 0;
							$rowc = Page::getColumnRow($a_kolom,$row);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $rowc[$j++] ?></td>
						<td><?= $rowc[$j++] ?></td>
						<td align="center">
							<?= (empty($row['sjamdatang']) ? 'OFF' : substr($row['sjamdatang'],0,2).':'.substr($row['sjamdatang'],2,2).' - '.substr($row['sjampulang'],0,2).':'.substr($row['sjampulang'],2,2)); ?></td>
						</td>
						<td align="center">
							<?= (empty($row['jamdatang']) ? '' : substr($row['jamdatang'],0,2).':'.substr($row['jamdatang'],2,2)).' - '.(empty($row['jampulang']) ? '' : substr($row['jampulang'],0,2).':'.substr($row['jampulang'],2,2)); ?>
						</td>
						<? 
							if ($row['menit'] <> 0){
								$jam = floor($row['menit']/60);
								$menit = $row['menit'] % 60;
							}else{
								$jam = $menit = 0;
							}
						?>
						<td align="left"><?= $jam.' Jam '.$menit.' Menit'; ?></td>
						<td align="center"><div class="label<?= $row['kodeabsensi']; ?>"><?= $a_absensi['absensi'][$row['kodeabsensi']]; ?></div></td>
					</tr>
					<?	}}
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
				<?php require_once('inc_listnav.php'); ?>
				<? } ?>
				
				<? if(!empty($r_page)) { ?>
				<input type="hidden" name="page" id="page" value="<?= $r_page ?>">
				<input type="hidden" name="filter" id="filter">
				<?	} ?>
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<? if(!empty($p_printpage)) { ?>
				<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
				<? } ?>
			</form>
			
			<br /><br />
			<center>
			<div class="filterTable" style="width:<?= $p_tbwidth-12 ?>px;">
				<table cellspacing="0" cellpadding="0" align="center" width="890">
					<tr>
						<td colspan="2"><strong>Aturan Minimal Keterlambatan (Tanggal Berlaku <?= CStr::formatDateInd($a_aturan['tglberlaku']); ?>): </strong></td>
					</tr>
					<tr>
						<td width="150px">Toleransi Keterlambatan : </td>
						<td><?= $a_aturan['minterlambat']; ?> Menit</td>
					</tr>
				</table>
			</div>
			
			<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">			
				<tr>
					<td valign="top">
						<div id="container_absen" style="height:300px"></div>
					</td>
				</tr>
			</table>
			</center>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
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
});

$(function () {
	var chart_login, chart_waktu, chart_browser;
	$(document).ready(function() {
		chart_login = new Highcharts.Chart({
					chart: {
                renderTo: 'container_absen',
                type: 'spline',
                marginRight: 130,
                marginBottom: 50
            },
            title: {
                text: 'Grafik Kehadiran Pegawai 2 Minggu Terakhir',
                x: -20 //center
            },
            xAxis: {
				categories: ['<?= $a_graph['graph']['categori']; ?>']
			},
            yAxis: {
                title: {
                    text: 'Jumlah Kehadiran Pegawai'
                },
                plotLines: [{
                    value: 0,
                    width: 1,
                    color: '#808080'
                }]
            },
            tooltip: {
                formatter: function() {
                        //return '<b>'+ this.series.name +'</b><br/>'+this.x +': '+ numberFormat(this.y) ;
						return this.series.name +' '+ this.x +': '+ this.y;
                }
            },
            legend: {
                layout: 'vertical',
                align: 'right',
                verticalAlign: 'top',
                x: -10,
                y: 100,
                borderWidth: 0
            },
			series: [<?= $a_graph['graph']['series']; ?>]
		});
	});
});


</script>
</body>
</html>
