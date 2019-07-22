<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	$a_auth = Modul::getFileAuth();

	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];

	// include
	require_once(Route::getModelPath('pelanggaran'));
	require_once(Route::getModelPath('jenispelanggaran'));
	require_once(Route::getModelPath('sanksipelanggaran'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('periode'));
	require_once(Route::getModelPath('poinmhs'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if (isset ($_GET['key']))
		$r_key = CStr::removeSpecial($_GET['key']);

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;

	// properti halaman
	$p_title = 'Data Pelanggaran';
	$p_tbwidth = 500;
	$p_aktivitas = 'SPP';
	$p_listpage = Route::getListPage();

	$p_model = mPelanggaran;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;

	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'Mahasiswa');
	$a_input[] = array('kolom' => 'periode', 'label' => 'periode', 'type' => 'S', 'option' => mPeriode::getArray($conn));
	$a_input[] = array('kolom' => 'idjenispelanggaran', 'label' => 'Jenis Pelanggaran', 'type' => 'S', 'option' => array('' => '') + mJenispelanggaran::getArray($conn),'add'=> 'onChange="setPoint(this)"');
	$a_input[] = array('kolom' => 'idsanksipelanggaran', 'label' => 'Sanksi Pelanggaran', 'type' => 'S', 'option' => array('' => '') + mSanksipelanggaran::getArray($conn));
	$a_input[] = array('kolom' => 'tglpelanggaran', 'label' => 'Tanggal Pelanggaran','type' => 'D','add'=>'onchange="setHari1(this.value)"');
	$a_input[] = array('kolom' => 'tglexpired', 'label' => 'Tanggal Expired','type' => 'D','add'=>'onchange="setHari1(this.value)"');
	$a_input[] = array('kolom' => 'poinpelanggaran', 'label' => 'Point','type' => 'N');
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'C', 'option' => array('-1' => ''));


	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($_POST['isvalid']))
			$record['isvalid'] = 0;
		
		$inputpoin = false;
		if($_POST['isvalid'] != $_POST['valid']) {
			if(empty($_POST['isvalid']))
				$inputpoin = -1 * $_POST['oldpoin'];
			else
				$inputpoin = $_POST['poinpelanggaran'];
		}
		else if(!empty($_POST['isvalid']) and $_POST['poinpelanggaran'] != $_POST['oldpoin'])
			$inputpoin = $_POST['poinpelanggaran'] - $_POST['oldpoin'];

		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		//input poin
		if(!$p_posterr and $inputpoin) {
			//check poin mhs
			$i_poinmhs = mPoinmhs::getData($conn,$record['nim']);
			$t_poin = (int)$i_poinmhs['poinpelanggaran'] + $inputpoin;
			if($t_poin < 0)
				$t_poin = 0;

			$recordp = array();
			$recordp['nim'] = $record['nim'];
			$recordp['poinpelanggaran'] = $t_poin;

			if(empty($i_poinmhs)){
				list($p_posterr,$p_postmsg) = mPoinmhs::insertRecord($conn,$recordp);
			}else{
				list($p_posterr,$p_postmsg) = mPoinmhs::updateRecord($conn,$recordp,$record['nim']);
			}

			if (empty($p_posterr)) {
				$p_postmsg = "Simpan Data Berhasil" ;
			}
		}
		
		if(!$p_posterr){
			unset($_POST);
			unset($post);
		}
	}
	
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);
	}

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	$poin_datavalid = Page::getDataValue($row,'isvalid');

	if(!empty($poin_datavalid)){
		$c_delete = false;
	}

	$r_mahasiswa = Page::getDataValue($row,'nim');
	if(!empty($r_mahasiswa))
		$r_namamahasiswa = $r_mahasiswa.' - '.$p_model::getNamaMahasiswa($conn,$r_mahasiswa);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foredit.js"></script>
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

					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/

					if(empty($p_fatalerr))
						require_once('inc_databutton.php');

					if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	}

					if(empty($p_fatalerr)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<?	/********/
						/* DATA */
						/********/

						$a_required = array('kodemk');
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
						<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
							<tr>
								<td class="LeftColumnBG">Mahasiswa</td>
								<td class="RightColumnBG">
									<?= Page::getDataInputWrap($r_namamahasiswa,
										UI::createTextBox('mahasiswa',$r_namamahasiswa,'ControlStyle',30,30)."\n".
										'<input type="hidden" id="nim" name="nim" value="'.$r_mahasiswa.'">') ?>
								</td>
							</tr>
							<?= Page::getDataTR($row,'periode') ?>
							<?= Page::getDataTR($row,'idjenispelanggaran') ?>
							<?= Page::getDataTR($row,'idsanksipelanggaran') ?>
							<?= Page::getDataTR($row,'tglpelanggaran') ?>
							<?= Page::getDataTR($row,'tglexpired') ?>
							<?= Page::getDataTR($row,'poinpelanggaran') ?>
							<?= Page::getDataTR($row,'isvalid') ?>

						</table>
					</div>
				</center>

				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="oldpoin" id="oldpoin" value="<?=Page::getDataValue($row,'poinpelanggaran') ?>" >
				<input type="hidden" name="valid" id="valid" value="<?=Page::getDataValue($row,'isvalid') ?>" >
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);

	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>

	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "nim"});
});

function setPoint(elem)
{
	var param = new Array();
	param[0] = elem.value; //subkey

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "getpoinpelanggaran", q: param }
				});

	jqxhr.done(function(data) {
		//alert(data);
		$("#poinpelanggaran").val(data);
    });
    jqxhr.fail(function(xhr,status) {
		alert(status);
	});
}
</script>
</body>
</html>
