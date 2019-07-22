<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_paperiodebobot',true);
	
	$c_readlist = true;
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_other = $a_auth['canother'];
	
	// include
	require_once(Route::getModelPath('pa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
	
	$r_key = CStr::removeSpecial($_REQUEST['key']);			
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Kategori Nilai';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	$p_listpage = Route::getListPage();
	
	$p_model = mPa;
	$p_dbtable = "pa_bobotnilaisubyektif";
	$p_key = "kodeperiodebobot,kodepajenis,kodekategori";
	
	$a_kategori = $p_model::getCKategori($conn);
	$a_jenispenilai = $p_model::getCJenisPenilai($conn);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'kodepajenis', 'label' => 'Jenis Penilai', 'type' => 'S', 'option' => $p_model::getCJenisPenilai($conn), 'notnull' => true);
	$a_inputdet = array();
	foreach($a_kategori as $kode => $label){
		$a_inputdet[] = array('kolom' => 'kodekategori_'.$kode, 'label' => 'Kategori', 'type' => 'S', 'option' => $a_kategori, 'notnull' => true);
		$a_inputdet[] = array('kolom' => 'nilai_'.$kode, 'label' => 'Nilai', 'type' => 'N,2', 'maxlength' => 5, 'size' => 5, 'notnull' => true);
	}
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		//list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$conn->BeginTrans();
		
		if (count($a_kategori) > 0){
			foreach($a_kategori as $kode){
				$record = array();
				$record['kodeperiodebobot'] = $r_key;
				$record['kodepajenis']  = CStr::cStrNull($_POST['kodepajenis']);
				$record['kodekategori'] = $kode;
				$record['nilai'] = CStr::cStrNull($_POST['nilai_'.$kode]);
				
				if(empty($r_subkey))
					list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true,$p_dbtable);
				else{
					$r_keyupdate = $record['kodeperiodebobot'].'|'.$record['kodepajenis'].'|'.$record['kodekategori'];
					list($p_posterr,$p_postmsg) = $p_model::updateRecord($conn,$record,$r_keyupdate,true,$p_dbtable,$p_key);
				}
			}
		}
		
		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);
		
		if(!$p_posterr){
			$r_subkey  = $r_key.'|'.$record['kodepajenis'];
			unset($post);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$p_key);
		
		if(!$p_posterr) Route::navListpage($p_listpage,$r_key);
	}
	
	if (!empty($r_subkey)){
		$row = $p_model::getDataBobotSubj($conn,$r_subkey);
		list($r_key,$jenispenilai) = explode('|', $r_subkey);
	}
	
	if(empty($p_listpage))
		$p_listpage = Route::getListPage();
		
	$isupload = false;
	foreach($a_input as $t_input) {
		if($t_input['type'][0] == 'U')
			$isupload = true;
	}
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
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
						require_once('inc_databuttonajax.php');
					
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">Jenis Penilai *</td>
							<td class="RightColumnBG">
							<span id="show"><?= $a_jenispenilai[$jenispenilai]; ?></span>
							<span id="edit" style="display:none">
							<?= UI::createSelect('kodepajenis',$a_jenispenilai, $jenispenilai,'ControlStyle',$c_edit); ?>
							</span>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" width="150" style="white-space:nowrap">Kategori</td>
							<td class="RightColumnBG">
								<? if (count($a_kategori) > 0){ ?>
								<table cellpadding="3" cellspacing="0" class="GridStyle">
									<tr>
										<? foreach($a_kategori as $kode => $label){	?>
										<td class="LeftColumnBG" width="50" align="center"><?= $label; ?></td>
										<? } ?>
									</tr>
									<tr>
										<? foreach($a_kategori as $kode => $label){	?>
										<td><span id="show"><?= $row['nilai'][$kode]; ?></span>
										<span id="edit" style="display:none">
										<?= UI::createTextBox('nilai_'.$kode,$row['nilai'][$kode],'ControlStyle',5,5,$c_edit) ?>
										</span>
										</td>
										<? } ?>
									</tr>
								</table>
								<? } ?>
							</td>
						</tr>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<?	} ?>
			</form>
		</td>
	</tr>
</table>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";
var xtdid = "contents";

$(document).ready(function() {

	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

</script>
</body>
</html>

