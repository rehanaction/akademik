<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('ekivaturan'));
	require_once(Route::getModelPath('kurikulum'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$c_nim = CStr::cEmChg($_POST['nim'],$_POST['npm']);
	
	// cek anak wali
	if(!empty($c_nim) and Akademik::isDosen()) {
		$cek = mMahasiswa::isDosenWali($conn,$c_nim,Modul::getUserName());
		if(empty($cek))
			unset($c_nim);
	}
	
	// variabel request
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_kurikulumlama = Modul::setRequest($_POST['kurikulumlama']);
	$r_kurikulumbaru = Modul::setRequest($_POST['kurikulumbaru'],'KURIKULUM');
	$r_angkatan = Modul::setRequest($_POST['angkatan']);
	
	// combo
	$l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);
	$l_kurikulumlama = uCombo::kurikulum($conn,$r_kurikulumlama,'kurikulumlama','onchange="goSubmit()"',false);
	$l_kurikulumbaru = uCombo::kurikulum($conn,$r_kurikulumbaru,'kurikulumbaru','onchange="goSubmit()"',false);
	$l_angkatan = uCombo::angkatan($conn,$r_angkatan,'angkatan','',false);
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'thnkurikulum', 'label' => 'Kur. Lama');
	$a_kolom[] = array('kolom' => 'kodemklama', 'label' => 'Kode MK', 'type' => 'S', 'option' => mKurikulum::mkKurikulumUnit($conn,$r_kurikulumlama,$r_unit));
	$a_kolom[] = array('kolom' => 'namamklama', 'label' => 'Nama MK');
	
	$a_kolom[] = array('kolom' => 'tahunkurikulumbaru', 'label' => 'Kur. Baru');
	$a_kolom[] = array('kolom' => 'kodemkbaru', 'label' => 'Kode MK', 'type' => 'S', 'option' => mKurikulum::mkKurikulumUnit($conn,$r_kurikulumbaru,$r_unit));
	$a_kolom[] = array('kolom' => 'namamkbaru', 'label' => 'Nama MK');
	
	// properti halaman
	$p_title = 'Hasil Ekivalensi Mata Kuliah';
	$p_tbwidth = 900;
	$p_aktivitas = 'KULIAH';
	
	$p_model = mEkivAturan;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+2;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		// tambahan unit dan kurikulum
		$a_kolom[] = array('kolom' => 'kodeunitlama', 'value' => $r_unit);
		$a_kolom[] = array('kolom' => 'kodeunitbaru', 'value' => $r_unit);
		$a_kolom[] = array('kolom' => 'thnkurikulum', 'value' => $r_kurikulumlama);
		$a_kolom[] = array('kolom' => 'tahunkurikulumbaru', 'value' => $r_kurikulumbaru);
		$a_kolom[] = array('kolom' => 'relasi', 'value' => 'O');
		
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
		
		// buang lagi unit dan kurikulum
		array_pop($a_kolom);
		array_pop($a_kolom);
		array_pop($a_kolom);
		array_pop($a_kolom);
		array_pop($a_kolom);
	}
	else if($r_act=='setA'){
		$record['nim'] = $c_nim;
		$record['thnkurikulum'] = $r_kurikulumbaru;
		$record['kodeunit'] = $r_unit;
		$record['kodemk'] = $_POST['recordid'];
		
		$err = Query::recInsert($conn,$record,'akademik.ak_ekivmhs');
		if(!$err){	
			$p_posterr = false;
			$p_postmsg = "Data Berhasil di simpan";
		}
	}
	elseif($r_act=='unsetA'){
		$conn->Execute("delete from akademik.ak_ekivmhs where nim = '$c_nim' and kodemk = '".$_POST['recordid']."' and kodeunit = '$r_unit' and thnkurikulum = '$r_kurikulumbaru' ");
	}
	elseif($r_act == 'setK'){
		$arr_id = explode(";",$_POST['recordid']);
		$c_kur = $arr_id[1];
		$c_kodemk = $arr_id[0];
		$conn->Execute("update akademik.ak_krs set dipakai = '-1' where nim = '$c_nim' and kodemk = '$c_kodemk' and thnkurikulum = '$c_kur'");
	}
	elseif($r_act == 'unsetK'){
		$arr_id = explode(";",$_POST['recordid']);
		$c_kur = $arr_id[1];
		$c_kodemk = $arr_id[0];
		$conn->Execute("update akademik.ak_krs set dipakai = '0' where nim = '$c_nim' and kodemk = '$c_kodemk' and thnkurikulum = '$c_kur'");
	}
	
	//ambil data mhs
	if($c_nim!='' or $c_nim!=null){
		$sql = "select nim, nama, namaunit,periodemasuk,m.kodeunit from akademik.ms_mahasiswa m left join gate.ms_unit u on u.kodeunit = m.kodeunit
				where m.nim = '$c_nim' ";
		$mhs = $conn->GetRow($sql);
		if(count($mhs)==0){
			$p_posterr = true;
			$p_postmsg = "NIM tidak ditemukan";
		}
	}
	
	// sisi kiri
	$rows = mEkivAturan::getListKiri($conn,$c_nim,$mhs['kodeunit']);
	
	foreach($rows as $rowkiri) {
		$skskiri += (int)$rowkiri['sks'];
		
		$kur[$rowkiri['semester']][] = $rowkiri['thnkurikulum'];
		$kodemk[$rowkiri['semester']][] = $rowkiri['kodemk'];
		$namamk[$rowkiri['semester']][] = $rowkiri['namamk'];
		$sks[$rowkiri['semester']][] = $rowkiri['sks'];
		$nhuruf[$rowkiri['semester']][] = $rowkiri['nhuruf'];
	}
	
	// sisi kanan
	$rows = mEkivAturan::getListKanan($conn,$c_nim,$mhs['kodeunit'],$r_kurikulumbaru);
	
	foreach($rows as $rowkanan) {
		$kodemkA[$rowkanan['semmk']][] = $rowkanan['kodemk'];
		$namamkA[$rowkanan['semmk']][] = $rowkanan['namamk'];
		$sksA[$rowkanan['semmk']][] = $rowkanan['sks'];
		$statusA[$rowkanan['semmk']][] = $rowkanan['statusekivalen'];
		$nhurufA[$rowkanan['semmk']][] = $rowkanan['wajibpilihan'];
	}
	
	$rssem = $conn->Execute("select distinct(semmk) as sem from akademik.ak_kurikulum where thnkurikulum = '$r_kurikulumbaru' and kodeunit = '".$mhs['kodeunit']."' order by semmk asc");
	
	$a_filtercombo = array();
  //$a_filtercombo[] = array('label' => 'Kurikulum Lama', 'combo' => $l_kurikulumlama);
	$a_filtercombo[] = array('label' => 'Kurikulum Baru', 'combo' => $l_kurikulumbaru);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forinplace.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<?	/************************/
					/* COMBO FILTER HALAMAN */
					/************************/
				?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>px;box-sizing:border-box">
						<table width="100%" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>		
											<td width="50" style="white-space:nowrap"><strong>N I M</strong></td>		
											<td> : <input type="text" name="nim" id="nim" maxlength="20" value="<?= $c_nim?>" size="20">&nbsp;&nbsp;&nbsp;
											<input type="button" value="Tampilkan Data" class="ControlStyle" onclick="goTampil()">
											</td>		
										</tr>
										<?if(($c_nim!='' or $c_nim !=null) and count($mhs)>0){?>
											<tr>		
												<td width="50" style="white-space:nowrap"><strong>Nama</strong></td>		
												<td> : <?= $mhs['nama']?></td>		
											</tr>
											<tr>		
												<td width="50" style="white-space:nowrap"><strong>Program Studi</strong></td>		
												<td> : <?= $mhs['namaunit']?></td>		
											</tr>
											<tr>		
												<td width="50" style="white-space:nowrap"><strong>Periode Masuk</strong></td>		
												<td> : <?= substr($mhs['periodemasuk'],0,4)?></td>		
											</tr>
										<?}?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br>
				<?	
					if(!empty($p_postmsg)) { ?>
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
							<div class="right">
								<img title="Cetak Ekivalensi" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
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
						<th width="50%">Kurikulum Lama</th>
						<th width="50%">Kurikulum Baru&nbsp;&nbsp;&nbsp;<?= $a_filtercombo[0]['combo'] ?></th>
					</tr>
				</table><br>
				<? while($rowsem = $rssem->fetchRow()){?>
					<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0"  align="center">
						<tr>
							<td width="50%" valign="top"><br>
								<table width="100%" border="1" cellspacing="0" cellpadding="4" class="GridStyle" bgcolor="#e2f1fa">
									<tr class="HeaderBG">
										<td align="center" colspan="4">Semester <?=$rowsem['sem']?></td>
									</tr>
									<tr class="subHeaderBG" align="center">
									  <!--<td width="2%">&nbsp;</td>-->
									  <td width="5%">Kurikulum</td>
									  <td>Matakuliah</td>
									  <td width="5%">Nilai</td>
									</tr>
									<? for($i=0;$i<count($kodemk[$rowsem['sem']]);$i++){?>
										<tr>
											<!--<td align="center"><input type="checkbox" <?=$dipakai[$rowsem['sem']][$i]!=0?'checked':'';?> onClick="setK(this.checked,'<?= $kodemk[$rowsem['sem']][$i].';'.$kur[$rowsem['sem']][$i]?>');" ></td>-->
											<td align="center"><?=$kur[$rowsem['sem']][$i]?></td>
											<td align="left"><?=$kodemk[$rowsem['sem']][$i].' | '.$namamk[$rowsem['sem']][$i].' | '.$sks[$rowsem['sem']][$i].' sks'?></td>
											<td align="center"><?=$nhuruf[$rowsem['sem']][$i]?></td>
										</tr>
									<? }?>
								</table>
							</td>
							<td width="50%" valign="top">
								<br>
								<table width="100%" border="1" cellspacing="0" cellpadding="4" class="GridStyle" bgcolor="#e2f1fa">
									<tr class="HeaderBG">
										<td align="center" colspan="4">Semester <?=$rowsem['sem']?></td>
									</tr>
									<tr class="subHeaderBG" align="center">
									  <td width="2%">&nbsp;</td>
									  <td>Matakuliah</td>
									</tr>
									<? for($i=0;$i<count($kodemkA[$rowsem['sem']]);$i++){?>
										<tr>
											<td align="center"><input type="checkbox" <?=($statusA[$rowsem['sem']][$i])?'checked':''?> onClick="ekiv(this.checked,'<?= $kodemkA[$rowsem['sem']][$i]?>');" ></td>
											<td align="left"><?=$kodemkA[$rowsem['sem']][$i].' | '.$namamkA[$rowsem['sem']][$i].' | '.$sksA[$rowsem['sem']][$i].' sks'   ?></td>
											<td align="center"><?=$nhurufA[$rowsem['sem']][$i]?></td>
										</tr>
									<? }?>
								</table>
							</td>
						</tr>
					</table>
				<?}?>
                                 
                                SKS LULUS :

                                <?= $skskiri; ?>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="recordid" id="recordid">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var insertreq = "<?= @implode(',',$a_insertreq) ?>";
var updatereq = "<?= @implode(',',$a_updatereq) ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function goSalin() {
	document.getElementById("act").value = "copy";
	goSubmit();
}


function goTampil(){
	goSubmit();
}

function setK(sender,recordid) {
	alert(recordid);
	if (sender)	
		document.getElementById("act").value = "setK";
	else
		document.getElementById("act").value = "unsetK";
	
	document.getElementById("recordid").value = recordid;
	goSubmit();
}

function ekiv(sender,recordid) {
	if (sender)	
		document.getElementById("act").value = "setA";
	else
		document.getElementById("act").value = "unsetA";
	document.getElementById("recordid").value = recordid;
	goSubmit();
}

function goPrint() {
	goSubmitBlank("<?php echo Route::navAddress('rep_ekivalensi') ?>");
}

</script>
</body>
</html>
