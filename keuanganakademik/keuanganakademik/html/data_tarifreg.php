<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// $c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('jenistagihan'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tarifreg'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$arr_key = explode('|',$r_key);
	$r_periode = $arr_key[1];
	$r_kodeunit = $arr_key[0];
	$r_jalur = $arr_key[2];
	$r_sistemkuliah = $arr_key[3];
	$r_gelombang = $arr_key[4];
	
	// combo
	$l_periode = uCombo::periode($conn,$r_periode,'periode','',true,false);
	$l_jalur = uCombo::jalur($conn,$r_jalur,'jalurpenerimaan','',true,false);
	$l_unit = uCombo::unit($conn,$r_kodeunit,'kodeunit','',true,false);
	$l_gelombang = uCombo::gelombang($conn,$r_gelombang,'gelombang','',true,false);
	$l_sistemkuliah = uCombo::sistemkuliah($conn,$r_sistemkuliah,'sistemkuliah','',true,false);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Tarif Pembayaran Registrasi Reguler';
	$p_tbwidth = 600;
	$p_aktivitas = 'KEUANGAN';
	$p_listpage = Route::getListPage();
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	//penyimpanan
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		// mengambil data simpanan
		$a_angsuran = $_POST['angsuran'];
		$a_tgldeadline = $_POST['tgldeadline'];
		
		$jmlangsuran = count($a_angsuran);
		
		// mengambil data terbaru
		$data = mTarifReg::getArrayAngsuran($conn,$r_jalur,$r_periode,$r_gelombang,$r_kodeunit,$r_sistemkuliah);
		$jmlangsurannow = count($data);
		
		// hapus data sekarang
		$err = false;
		for($i=$jmlangsurannow;$i>$jmlangsuran;$i--) {
			$err = mTarifReg::deleteAngsuran($conn,$r_jalur,$r_periode,$r_gelombang,$r_kodeunit,$r_sistemkuliah,$i);
			if(!$err) break;
		}
		
		// masukkan data
		if(!$err) {
			for($c=0;$c<$jmlangsuran;$c++) {
				$i = $c+1;
				
				$cek = (int)$data[$i]['jumlahdata'];
				if($data[$i]['jumlahdata'] > 1) {
					$err = mTarifReg::deleteAngsuran($conn,$r_jalur,$r_periode,$r_gelombang,$r_kodeunit,$r_sistemkuliah,$i);
					$cek = 0;
				}
				
				if(!$err) {
					$record = array();
					$record['angsuranke'] = $i;
					$record['nominaltarif'] = CStr::cStrDec($a_angsuran[$c]);
					$record['tgldeadline'] = CStr::cStrNull(Cstr::formatDate($a_tgldeadline[$c]));
					
					if(empty($cek))
						$err = mTarifReg::insertAngsuran($conn,$r_jalur,$r_periode,$r_gelombang,$r_kodeunit,$r_sistemkuliah,$i,$record);
					else
						$err = mTarifReg::updateAngsuran($conn,$r_jalur,$r_periode,$r_gelombang,$r_kodeunit,$r_sistemkuliah,$i,$record);
				}
				
				if($err) break;
			}
		}
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
		
		$p_posterr = $err;
		$p_postmsg = 'Penyimpanan tarif pembayaran registrasi reguler '.($err ? 'gagal' : 'berhasil');
	}
	
	// data tarif
	$data = mTarifReg::getArrayAngsuran($conn,$r_jalur,$r_periode,$r_gelombang,$r_kodeunit,$r_sistemkuliah,true);
	$infoPendaftaran = mAkademik::infoPendaftaran($conn,$r_periode,$r_jalur,$r_gelombang);
	$totalnominal = 0;
	foreach ($data as $row){
		$totalnominal += CStr::cStrDec($row['nominaltarif']);
		}
	$jmlangsuran = count($data);
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
			<form name="pageform" id="pageform" method="post"<?= $isupload ? ' enctype="multipart/form-data"' : '' ?>>
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
					<table id="table_data" width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
                    <tr>
                    	<td class="LeftColumnBG" width="35%" style="white-space:nowrap">Periode Tarif</td>
                        <td class="RightColumnBG" colspan="2"><?=$l_periode?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Jalur Penerimaan</td>
                        <td class="RightColumnBG" colspan="2"><?=$l_jalur?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Jurusan</td>
                        <td class="RightColumnBG" colspan="2"><?=str_replace('..','',$l_unit)?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Gelombang</td>
                        <td class="RightColumnBG" colspan="2"><?=$l_gelombang?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Sistem Kuliah</td>
                        <td class="RightColumnBG" colspan="2"><?=$l_sistemkuliah?></td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Tanggal Pendaftaran</td>
                        <td class="RightColumnBG" colspan="2"><?= Date::indoDate($infoPendaftaran['tglawaldaftar']).' s/d '.Date::indoDate($infoPendaftaran['tglakhirdaftar'])?></td>
                    </tr>
                    
					<tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Jumlah Total Nominal</td>
                        <td class="RightColumnBG" colspan="2"><?= UI::createTextBoxSpan('totalnominal',$totalnominal,'',9,9) ?></td>
                    </tr>
					<tr>
                    	<td class="LeftColumnBG" style="white-space:nowrap">Jumlah Angsuran</td>
                        <td class="RightColumnBG" colspan="2">
							<?= UI::createTextBoxSpan('jmlangsuran',$jmlangsuran,'',2,2) ?>
							<span id="edit" style="display:none">
								<input type="button" value="Refresh" onclick="goRefreshAngsuran()">
							</span>
						</td>
                    </tr>
                    <tr>
                    	<td class="LeftColumnBG" align="center"><strong>Detail Angsuran</strong></td>
						<td class="LeftColumnBG" align="center"><strong>Nominal Tagihan</strong></td>
						<td class="LeftColumnBG" align="center"><strong>Jatuh Tempo</strong></td>
                    </tr>
					<tr id="tr_template" style="display:none">
						<td class="LeftColumnBG" style="white-space:nowrap"></td>
                        <td class="RightColumnBG"><?= UI::createTextBox('angsuran[]','','',14,14,true,'disabled') ?></td>
						<td class="RightColumnBG">
							<?= UI::createTextBox('tgldeadline','','',10,10,true,'disabled') ?>
							<img src="images/cal.png" id="tgldeadline_trg" style="cursor:pointer;" title="Pilih tanggal jatuh tempo">
						</td>
					</tr>
					<?	if(!empty($data)) {
							for($i=1;$i<=$jmlangsuran;$i++) {
					?>
					<tr class="tr_main">
                    	<td class="LeftColumnBG" style="white-space:nowrap">Angsuran <?= $i ?></td>
                        <td class="RightColumnBG"><?= UI::createTextBoxSpan('angsuran[]',$data[$i]['nominaltarif'],'',14,14) ?></td>
						<td class="RightColumnBG">
							<?= UI::createTextBoxSpan('tgldeadline[]',$data[$i]['tgldeadline'],'',10,10,true,'','','tgldeadline_'.$i) ?>
							<span id="edit" style="display:none">
							<img src="images/cal.png" id="tgldeadline_trg_<?= $i ?>" style="cursor:pointer;" title="Pilih tanggal jatuh tempo">
							</span>
							<script type="text/javascript">
							Calendar.setup({
								inputField     :    "tgldeadline_<?= $i ?>",
								ifFormat       :    "%d-%m-%Y",
								button         :    "tgldeadline_trg_<?= $i ?>",
								align          :    "Br",
								singleClick    :    true
							});
							</script>
						</td>
                    </tr>
					<?		}
						}
					?>
					</table>
					</div>
				</center>
				
				<input type="hidden" name="act" id="act"> 
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<script type="text/javascript" src="scripts/jquery.number.min.js"></script>
<script type="text/javascript">
	
var listpage = "<?= Route::navAddress($p_listpage) ?>";
var thispage = "<?= Route::navAddress(Route::thisPage()) ?>";

var required = "<?= @implode(',',$a_required) ?>";

$(document).ready(function() {
	initEdit(<?= empty($post) ? false : true ?>);
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$("#jmlangsuran,[name='angsuran[]']").number(true,0,',','.');
});

function goRefreshAngsuran() {
	$(".tr_main").remove();
	var i, tr;
	var jml = $.number($("#jmlangsuran").val());
	var total = $("#totalnominal").val();
	nominal = total/jml;
	
	$("[name='angsuran[]']:gt(" + jml + ")").each(function() {
		$(this).parents("tr:eq(0)").remove();
	});
	
	var len = $("[name='angsuran[]']").length;
	
	for(i=len;i<=jml;i++) {
		tr = $("#tr_template").clone();
		
		tr.removeAttr("id");
		tr.attr("class","tr_main");
		tr.find("td:eq(0)").html("Angsuran " + i);
		tr.find("[name='angsuran[]']").removeAttr("disabled").number(true,0,',','.');
		tr.find("[name='angsuran[]']").val(nominal);
		tr.find("[id='tgldeadline']").removeAttr("disabled").attr("name","tgldeadline[]").attr("id","tgldeadline_" + i);
		tr.find("[id='tgldeadline_trg']").attr("id","tgldeadline_trg_" + i);
		tr.show();
		
		$("#table_data").append(tr);
		
		// kasih kalender
		Calendar.setup({
			inputField     :    "tgldeadline_" + i,
			ifFormat       :    "%d-%m-%Y",
			button         :    "tgldeadline_trg_" + i,
			align          :    "Br",
			singleClick    :    true
		});
	}
}

</script>
</body>
</html>
