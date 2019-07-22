<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//$conn->debug=true;
        // hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
		
	// include
	require_once(Route::getModelPath('lokasi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_self = (int)$_REQUEST['self'];
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getUserName();
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Ruangan Ujian';
	$p_tbwidth = 500;
	$p_aktivitas = 'UNIT';
	$p_listpage = Route::getListPage();
	
	$p_model = mLokasi;
	
        // hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
                
	// struktur view
        $r_act = $_POST['act'];
        
	if(empty($r_key))
		$p_edit = false;
	else
		$p_edit = true;
	
            //get ruang untuk optionnya
            $ruang=$p_model::getRuang($conn);
            $ruang=array_values($ruang);
            
	$a_input = array();
        $a_input[] = array('kolom' => 'kodelokasiujian', 'label' => 'Kode Ruang Ujian', 'maxlength' => 10, 'size' => 10, 'readonly' => $p_edit,'notnull' => true);
        $a_input[] = array('kolom' => 'namalokasi', 'label' => 'Lokasi', 'type'=>'S', 'option' => $ruang);
        $a_input[] = array('kolom' => 'jalurpenerimaan', 'label' => 'Jalur Penerimaan', 'type' => 'S', 'notnull' => true, 'option' => mCombo::jalurpenerimaan($conn),'readonly' => $p_edit);
	$a_input[] = array('kolom' => 'kapasitaslokasi', 'label' => 'Kapasitas', 'maxlength' => 4, 'size' => 5, 'notnull' => true);
        // ada aksi
        
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_name = CStr::cEmChg($t_detail['nameid'],$t_detail['kolom']);
			$a_value[$t_name] = $_POST[$r_detail.'_'.$t_name];
		}
		
		list(,$record) = uForm::getPostRecord($a_detail[$r_detail]['data'],$a_value);
		$record['nim'] = $r_key;
		
		list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit and !$p_limited) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);
		
		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}
        
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
        
        
        $r_kodekota = Page::getDataValue($row,'kodekota');
        $r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
        $r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
        $r_kodekotapt = Page::getDataValue($row,'kodekota_kotak');
        
        /*
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'User ini Tidak Mempunyai Profile';
	}
	*/
        //daftar peserta ujian
        $rowpeserta=$p_model::getpesertaperruang($conn,$row[2]['value'],$r_key);
       
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<link href="style/tabpane.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/common.js"></script>
        <script type="text/javascript" src="scripts/foredit.js"></script>
        <script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		#table_evaluasi { border-collapse:collapse }
		#table_evaluasi .td_ev { border:1px solid #666 }
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
        <script type="text/javascript">
$(".subnav").hover(function() {
    $(this.parentNode).addClass("borderbottom");
}, function() {
    $(this.parentNode).removeClass("borderbottom");
});

</script>
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
					?>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					<?	$a_required = array('nopendaftar','nama', 'tokenpendaftaran'); ?>
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
                                                        <tr>
                                                        <?= Page::getDataTR($row,'kodelokasiujian') ?>
                                                        <?= Page::getDataTR($row,'namalokasi') ?>
                                                        <?= Page::getDataTR($row,'jalurpenerimaan') ?>
                                                        <?= Page::getDataTR($row,'kapasitaslokasi') ?>
                                                        </tr>	
					</table>
					</div>
				</center>
				<br>
				<center>
				<div class="tabs" style="width:<?= $p_tbwidth ?>px">
					<ul>
						<li><a id="tablink" href="javascript:void(0)">Daftar Peserta Ujian</a></li>
					</ul>
				
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center" class="GridStyle">
                                            <tr bgcolor="#c5c5c5" style="font-weight: bold;">
                                                <td align="center">No Pendaftaran</td>
                                                <td align="center">Nama</td>
                                                <td align="center">Pilihan I</td>
                                                <td align="center">Pilihan II</td>
                                                <td align="center">Pilihan III</td>
                                            </tr>
					<?
                                            $i=0;
                                        while($rowp=$rowpeserta->FetchRow()){
                                            if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
                                        ?>
                                            <tr class="<?= $rowstyle ?>">
                                                <td align="center"><?=$rowp['nopendaftar']?></td>
                                                <td align="center"><?=$rowp['nama']?></td>
                                                <td align="center"><?=$rowp['pilihan1']?></td>
                                                <td align="center"><?=$rowp['pilihan2']?></td>
                                                <td align="center"><?=$rowp['pilihan3']?></td>
                                            </tr>
                                        <?
                                            $i++;
                                        }
                                        ?>
					</table>
					</div>                                        
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
	
	loadKota();
	loadKotaOrtu();
	loadKotaPerusahaan();
	loadKotaPonpes();
	loadKotaSMU();
	loadKotaPT();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

// ajax ganti kota
function loadKota() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsi.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        $kota = array_values($kota);
	$content = "document.getElementById('kodekota').innerHTML = \"";
	for($i=0;$i<count($kota);$i++)
	{
	    $content .= "<option value='".$kota[$i]."'>".$kota[$i]."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

// ajax ganti kota
function loadKotaOrtu() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsiortu.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        $kota = array_values($kota);
	$content = "document.getElementById('kodekotaortu').innerHTML = \"";
	for($i=0;$i<count($kota);$i++)
	{
	    $content .= "<option value='".$kota[$i]."'>".$kota[$i]."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}

// ajax ganti kota
function loadKotaSMU() {
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.propinsismu.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        $kota = array_values($kota);
	$content = "document.getElementById('kodekotasmu').innerHTML = \"";
	for($i=0;$i<count($kota);$i++)
	{
	    $content .= "<option value='".$kota[$i]."'>".$kota[$i]."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
}
function loadKotaKontak(){
<?php
    $propinsi = mCombo::getPropinsi();
    while ($data = $propinsi->FetchRow())
    {
	$idProp = $data['kodepropinsi'];
	echo "if (document.pageform.kodepropinsi_kontak.value == \"".$idProp."\")";
	echo "{";

	$kota = mCombo::kota($idProp);
        $kota = array_values($kota);
	$content = "document.getElementById('kodekota_kotak').innerHTML = \"";
	for($i=0;$i<count($kota);$i++)
	{
	    $content .= "<option value='".$kota[$i]."'>".$kota[$i]."</option>";
	}
	$content .= "\"";
	echo $content;
	echo "}\n";
	
    }
?>
} 
</script>
</body>
</html>