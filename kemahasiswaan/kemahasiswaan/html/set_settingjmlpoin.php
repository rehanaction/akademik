<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_edit = $a_auth['canupdate'];

	// include
	require_once(Route::getModelPath('setting'));
	require_once(Route::getModelPath('skalanilai'));
	require_once(Route::getModelPath('unit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// variabel request
	$a_global = mSetting::getData($conn,1);

	// properti halaman
	$p_title = 'Setting Poin Ujian Akhir';
	$p_tbwidth = 500;
	$p_aktivitas = 'SETTING';

	$p_model = mSetting;

	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'kodeunit', 'label' => 'Kode Unit');
	$a_kolom[] = array('kolom' => 'namaunit', 'label' => 'Nama Unit');
	$a_kolom[] = array('kolom' => 'jmlminimalpoin','label' => 'Jumlah Seminar');

	// ada submit
	$r_act = $_POST['act'];
	if($r_act == 'simpanperiodenilai' and $c_edit) {
		$record=array();
		$record['jmlminimalpoin']=$_POST['jmlminimalpoin_'.$_POST['kodeunit']];
		
		list($p_posterr,$p_postmsg) = mUnit::updateRecord($conn,$record,$_POST['kodeunit']);
	}

	$r_sort = Page::setSort($_POST['sort']);
	$a_filter = Page::setFilter($_POST['filter']);
	$a_datafilter = Page::getFilter($a_kolom);

	$a_data = mUnit::getFakJur($conn,$a_kolom,$r_sort,$a_filter);

?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/hint.min.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem" >
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:950px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
			<?	} ?>
			<center>

			<div style="clear:both"><br></div>

			<!-- untuk setting KRS per jurusan -->
			<div id="div_setting">
				<header style="width:<?= $p_tbwidth+300 ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/NILAI.png" onerror="loadDefaultActImg(this)">
							<h1>Setting Minimal Poin Ujian Akhir</h1>
						</div>
					</div>
				</header>
				<?	/********/
					/* DATA */
					/********/
				?>
			<form name="pageformkrsjur" id="pageformkrsjur" method="post">
				<table width="<?= $p_tbwidth+300 ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
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
					</tr>
					<?	/********/
						/* ITEM */
						/********/

						$i = 0;
						foreach($a_data as $row) {
							$t_key = $p_model::getKeyRow($row);

							$j = 0;
							if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							if($row['level']==2)
								$padding=$row['level']*5;
							else
								$padding=0;

					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td align="center"><?= $row['kodeunit'] ?></td>
						<td align="left" style="padding-left:<?=$padding?>px"><?= $row['namaunit'] ?></td>
						<td nowrap valign="bottom" align="center">
							<?php if($row['level']==2) {
							?>
								<input type="text" name="jmlminimalpoin_<?= $row['kodeunit']?>" id="jmlminimalpoin_<?= $row['kodeunit']?>" class="ControlStyle" maxlength="10" size="10" value="<?= $row['jmlminimalpoin']?>">
								<input type="button" value="Simpan" class="ControlStyle" onclick="goSetPeriode('<?= $row["kodeunit"]?>')">
							<?} ?>
						</td>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	} ?>
				</table>
				<div class="Break"></div>
				<!--input type="button" value="Simpan Setting KRS" class="ControlStyle" onclick="goSetKRSJur()"-->
				</div>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="kodemk" id="kodemk">
				<input type="hidden" name="kodeunit" id="kodeunit">
			</form>
			</div>
			</center>
		</div>
	</div>
</div>

<script type="text/javascript">

$(document).ready(function() {
	$("[id='div_setting']").hover(function() {
		$(this).siblings().fadeTo(0,0.5);
	}, function() {
		$(this).siblings().fadeTo(0,1);
	});
});

function goSetPeriode(kodeunit) {
	pageformkrsjur.kodeunit.value=kodeunit;
	pageformkrsjur.act.value='simpanperiodenilai';
	document.getElementById("pageformkrsjur").submit();
}
</script>

</body>
</html>
