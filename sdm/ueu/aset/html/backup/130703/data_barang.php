<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('list_barang');
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	//print_r($_POST);
	
	// include
	require_once(Route::getModelPath('barang'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_pkey = CStr::removeSpecial($_REQUEST['pkey']);
	$r_act = $_POST['act'];

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Barang';
	$p_tbwidth = 500;
	$p_aktivitas = 'barang';
	$p_dbtable = 'ms_barang1';
	$p_key = 'idbarang1';
	$p_listpage = Route::getListPage();
	
	$p_model = mBarang;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);

	//cek bila add child, lalu ambil field dari parent
    $a_parent = array();
	if(empty($r_pkey)){
		$a_parent = array('idbarang1' => null, 'level' => 0);
	}else{
		$a_parent = $p_model::getData($conn,$r_pkey);
	}
	
	$isreadonly = !($r_act == 'save' and empty($r_key));
	
    // satuan tidak boleh diedit untuk kondisi tertentu
	$isROSatuan = false;
	if(!empty($r_key)){
	    if(substr($r_key,0,1) == '1'){
	        if($p_model::getNKonversi($conn,$r_key) > 0){ 
	            $isROSatuan = true;
            }
            
            if(!$isROSatuan){
    	        if($p_model::getNTransHP($conn,$r_key) > 0){ 
    	            $isROSatuan = true;
	            }
	        }
	    }
    }

	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idbarang1', 'label' => 'ID. Barang', 'maxlength' => 16, 'size' => 16, 'notnull' => true);
	$a_input[] = array('kolom' => 'namabarang', 'label' => 'Nama Barang', 'maxlength' => 100, 'size' => 30, 'notnull' => true);
	$a_input[] = array('kolom' => 'idsatuan', 'label' => 'Satuan', 'type' => 'S', 'option' => mCombo::satuan($conn), 'add' => 'style="width:100px;"','empty' => true,'readonly' => $isROSatuan);
	$a_input[] = array('kolom' => 'level', 'label' => 'Level', 'readonly' => $isreadonly, 'default' => (int)$a_parent['level']+1);
	$a_input[] = array('kolom' => 'idcoaaset', 'label' => 'COA', 'type' => 'S', 'option' => mCombo::coa($conn), 'add' => 'style="width:300px;"','empty' => true);
	$a_input[] = array('kolom' => 'idparent', 'label' => 'ID. Parent', 'readonly' => $isreadonly, 'default' => $a_parent['idbarang1']);
	$a_input[] = array('kolom' => 'idjenispenyusutan', 'label' => 'Jenis Penyusutan', 'type' => 'S', 'option' => mCombo::jenispenyusutan($conn), 'add' => 'style="width:230px;"','empty' => true);
	$a_input[] = array('kolom' => 'isaktif', 'label' => 'Aktif ?', 'type' => 'R', 'option' => mCombo::aktif(), 'default' => '1');
	$a_input[] = array('kolom' => 'catatan', 'label' => 'Catatan', 'type' => 'A', 'rows' => 3, 'cols' => 30, 'maxlength' => 255);
	
	//Data stock konversi
	$t_detail = array();
	$t_detail[] = array('kolom' => 'idtujuan', 'label' => 'Satuan Tujuan', 'type' => 'S', 'option' => mCombo::satuanTujuan($conn,$r_key), 'add' => 'style="width:100px;"');
	$t_detail[] = array('kolom' => 'nilai', 'label' => 'Nilai', 'type' => 'N,2', 'maxlength' => 14, 'size' => 8);

	$a_detail['stockkonversi'] = array('key' => $p_model::getDetailInfo('stockkonversi','key'), 'data' => $t_detail);
	
	//Data History Perawatan
	$t_detail = array();
	$t_detail[] = array('kolom' => 'idjenisrawat', 'label' => 'Jenis Perawatan', 'type' => 'S', 'option' => mCombo::jenisrawat($conn), 'add' => 'style="width:250px;"');
	$t_detail[] = array('kolom' => 'periode', 'label' => 'Periode', 'type' => 'N', 'align' => 'right', 'maxlength' => 4, 'size' => 4,'width' => 50);
	$t_detail[] = array('kolom' => 'satuanperiode', 'label' => 'Satuan Periode', 'type' => 'S', 'option' => mCombo::satuanperiode(), 'add' => 'style="width:50px;"','width' => 100);
	
	$a_detail['jadwalrawat'] = array('key' => $p_model::getDetailInfo('jadwalrawat','key'), 'data' => $t_detail);
	
	// ada aksi
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key)){
		    $record['level'] = (int)$a_parent['level']+1;
		    $record['idparent'] = $a_parent['idbarang1'];
		    
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_name = CStr::cEmChg($t_detail['nameid'],$t_detail['kolom']);
			$a_value[$t_name] = $_POST[$r_detail.'_'.$t_name];
		}
		
		list(,$record) = uForm::getPostRecord($a_detail[$r_detail]['data'],$a_value);
		$record['idbarang1'] = $r_key;
		if($r_detail == 'stockkonversi')
    		$record['idasal'] = $p_model::getSatuanByID($conn,$r_key);
		    
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}
	
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post,$p_dbtable,$p_key);
	
	//require_once(Route::getViewPath('inc_data'));
	//print_r($_POST);
	
	if(!empty($r_key)){
        $r_defsatuan = $p_model::getSatuanByID($conn,$r_key);
	    $rowd = array();

	    if(substr($r_key,0,1) == '1' and !empty($r_defsatuan)){
	        $ishp = true;
            $rowd = $p_model::getStockKonversi($conn,$r_key,'stockkonversi',$post);
        }else if(substr($r_key,0,1) != '1'){
	        $isaset = true;
            $rowd = $p_model::getJadwalRawat($conn,$r_key,'jadwalrawat',$post);
        }
	        
    }
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
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
				</center>
				<br>
				<center>
				    <div class="tabs" style="width:<?= $p_tbwidth ?>px">
					    <ul>
						    <li><a id="tablink" href="javascript:void(0)">Detail Barang</a></li>
						    <? if($ishp or $isaset) { ?>
						    <?      if($ishp) {?>
						    <li><a id="tablink" href="javascript:void(0)">Stok Konversi</a></li>
						    <?      }else if($isaset){ ?>
						    <li><a id="tablink" href="javascript:void(0)">Jadwal Perawatan</a></li>
						    <? }} ?>
					    </ul>
					    <div id="items">
					        <table cellpadding="4" cellspacing="2" align="center">
						        <?= Page::getDataTR($row,'idbarang1') ?>
						        <?= Page::getDataTR($row,'namabarang') ?>
						        <?= Page::getDataTR($row,'idsatuan') ?>
						        <?= Page::getDataTR($row,'level') ?>
						        <?= Page::getDataTR($row,'idcoaaset') ?>
						        <?= Page::getDataTR($row,'idparent') ?>
						        <?= Page::getDataTR($row,'idjenispenyusutan') ?>
						        <?= Page::getDataTR($row,'isaktif') ?>
						        <?= Page::getDataTR($row,'catatan') ?>
					        </table>
					    </div>
					    <? if($ishp or $isaset) { ?>
					    <?      if($ishp) {?>
					    <div id="items">
					        <?= Page::getDetailTable($rowd,$a_detail,'stockkonversi','Stock Konversi',false) ?>
					    </div>
					    <?      }else if($isaset){ ?>
					    <div id="items">
					        <?= Page::getDetailTable($rowd,$a_detail,'jadwalrawat','Jadwal Perawatan',false) ?>
					    </div>
					    <? }} ?>
				    </div>
				</center>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">

				<?	} ?>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	initTab();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});


</script>
</body>
</html>
