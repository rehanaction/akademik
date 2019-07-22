<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth('data_pegawai',true);
	
	$c_readlist = true;		
	$c_other = $a_auth['canother'];
	$c_kepeg = $c_other['K'];
	$c_valid = $c_other['V'];
	
	// include
	require_once(Route::getModelPath('angkakredit'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));	
		
	// variabel request
	$r_scroll = Modul::setRequest($_POST['scroll'],'AK_SCROLL');
	
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_edit = $a_auth['canupdate'];
		$c_delete = $a_auth['candelete'];
	}
	
	if(empty($r_self))
		$r_key = CStr::removeSpecial($_REQUEST['key']);
	else
		$r_key = Modul::getIDPegawai();
	
	$r_subkey = CStr::removeSpecial($_REQUEST['subkey']);
	
	// properti halaman
	$p_title = 'Data Simulasi Perhitungan Angka Kredit';
	$p_tbwidth = 800;
	$p_aktivitas = 'DATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mAngkaKredit;
	$p_dbtable = "ak_skdosen";
	$where = 'nourutakd';
	$p_col = 9;
	
	$asal = $p_model::getFungsional($conn,$r_key);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tglusulan', 'label' => 'Tgl. Usulan', 'type' => 'D', 'notnull' => true, 'default' => date('Y-m-d'));
	$a_input[] = array('kolom' => 'jabfungsionalasal', 'label' => 'Jabatan Asal', 'maxlength' => 50, 'size' => 30, 'notnull' => true, 'default' => $asal['jabatanfungsional'], 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'fungsionalasal', 'type' => 'H', 'default' => $asal['idjfungsional']);
	$a_input[] = array('kolom' => 'tmtasal', 'label' => 'TMT. Jabatan Asal', 'type' => 'D', 'notnull' => true, 'class' => 'ControlRead', 'default' => $asal['tmtmulai']);
	$a_input[] = array('kolom' => 'fungsionaltujuan', 'label' => 'Jabatan Tujuan', 'type' => 'S', 'option' => $p_model::jabatanFungsional($conn,$asal['idjfungsional']));
	$a_input[] = array('kolom' => 'nosk', 'label' => 'No. SK', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglsk', 'label' => 'Tgl. SK', 'type' => 'D');
	$a_input[] = array('kolom' => 'namapejabat', 'label' => 'Pejabat', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 40, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'statususulan', 'label' => 'Status KUM', 'type' => 'S', 'option' => $p_model::statusUsulan(), 'readonly' => true);
	$a_input[] = array('kolom' => 'angkakredit', 'type' => 'H');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		//simpan ke ak_dosen
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['idpegawai'] = $r_key;
		$record['jenisjabatan'] = 'K'; //untuk jabatan kopertis
		$record['statususulan'] = 'A'; //status diajukan
		
		if(empty($r_subkey))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where,true);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr){
			$p_posterr = $p_model::updateHitungSmtr($conn,$r_key,$r_subkey,$_POST['smtr']);
			if($p_posterr)
				$p_postmsg = "Update Perhitungan Semester Gagal";
		}

		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			unset($post);
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'validasi' and $c_edit) {
		$conn->BeginTrans();
		
		//simpan ke ak_dosen
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		$record['idpegawai'] = $r_key;
		$record['jenisjabatan'] = 'K'; //untuk jabatan kopertis
		$record['statususulan'] = 'Y'; //untuk validasi
		$record['nilaibidang1a'] = CStr::formatNumber($_POST['nilaibidang1a']);
		$record['nilaibidang1b'] = CStr::formatNumber($_POST['nilaibidang1b']);
		$record['nilaibidang2'] = CStr::formatNumber($_POST['nilaibidang2']);
		$record['nilaibidang3'] = CStr::formatNumber($_POST['nilaibidang3']);
		$record['nilaibidang4'] = CStr::formatNumber($_POST['nilaibidang4']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr){
			$p_posterr = $p_model::updateValidasiSmtr($conn,$r_subkey);
			if($p_posterr)
				$p_postmsg = "Validasi Perhitungan Semester Gagal";
		}

		if(!$p_posterr){
			$p_posterr = $p_model::validasiRWT($conn,$r_subkey,$r_key);
			if($p_posterr)
				$p_postmsg = "Validasi Perhitungan Bidang Gagal";
		}

		if(!$p_posterr){
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			unset($post);
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		
		$p_posterr = $p_model::updateSimulasiSmtr($conn,$r_subkey);
		if(!$p_posterr)
			$p_posterr = $p_model::updateRWTAkreditasiFinal($conn,$r_subkey,$r_key);

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			
			Route::navListpage($p_listpage,$r_key);
		}else
			$conn->RollbackTrans();
	}
	
	$sql = $p_model::getDataEditSimulasiAK($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
		if($t_row['id'] == 'statususulan')
			$status = $t_row['value'];
		if($t_row['id'] == 'angkakredit')
			$kredittujuan = $t_row['value'];
		if($t_row['id'] == 'fungsionaltujuan')
			$fungsionaltujuan = $t_row['realvalue'];
	}
	
	if($status == 'Y' and $r_self == '1'){
		$c_edit = false;
		$c_delete = false;
	}

	if(!empty($r_subkey))
		$a_smtr = $p_model::getKreditSmtr($conn,$r_subkey);

	$a_ceksmtr = $p_model::getSemester($conn,$r_subkey,$r_key);
			
	/*=== list beberapa bidang ===*/
	if(!empty($r_subkey)){	
		//bidang pendidikan
		$nbidang1a = $p_model::getNilaiIA($conn,$r_key,$r_subkey);
		
		//bidang pengajar
		$nbidang1b = $p_model::getNilaiIB($conn,$r_key,$r_subkey);
		
		//bidang penelitian
		$nbidang2 = $p_model::getNilaiII($conn,$r_key,$r_subkey);
		
		//bidang pengabdian
		$nbidang3 = $p_model::getNilaiIII($conn,$r_key,$r_subkey);
		
		//bidang penunjang
		$nbidang4 = $p_model::getNilaiIV($conn,$r_key,$r_subkey);
		
		//prosentase
		$prosentase = $p_model::getProsentase($conn);
		
		//jumlah nilai angka kredit
		$nilai1 = $nbidang1a + $nbidang1b + $asal['sisabidang1b'];
		$nilai2 = $nbidang2 + $asal['sisabidang2'];
		$nilai3 = $nbidang3 == '' ? 0 : $nbidang3;
		$nilai4 = $nbidang4 == '' ? 0 : $nbidang4;
		$nilaiutama = $nilai1 + $nilai2 + $nilai3;
		$nilaitotal = $nilaiutama + $nilai4;
		
		//cek syarat nilai yang harus dikumpulkan
		$selisihsyarat = $kredittujuan - $asal['angkakredit'];
		if($nilaitotal > $selisihsyarat){
			$lebih = $nilaitotal - $selisihsyarat;
		}

		$kurang = $selisihsyarat - $nilaitotal;
		$kurang = $kurang <= 0 ? 0 : $kurang;
	
	    $idpendidikan = $p_model::lastPendidikan($conn,$r_key);
	    $AKnilai1 = $prosentase['I'][$fungsionaltujuan][$idpendidikan]['prosentase'] / 100 * $selisihsyarat;
	    $AKnilai2 = $prosentase['II'][$fungsionaltujuan][$idpendidikan]['prosentase'] / 100 * $selisihsyarat;
	    $AKnilai3 = $prosentase['III'][$fungsionaltujuan][$idpendidikan]['prosentase'] / 100 * $selisihsyarat;
	    $AKnilai4 = $prosentase['IV'][$fungsionaltujuan][$idpendidikan]['prosentase'] / 100 * $selisihsyarat;
		
	    //pengecekan bidang 1
	    if(!empty($prosentase['I'][$fungsionaltujuan][$idpendidikan]['operator'])){
	        if(strpos($prosentase['I'][$fungsionaltujuan][$idpendidikan]['operator'], '<') !== false){
	            eval('$bidang1 = '.$nilai1.' '.$prosentase['I'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai1.';');
	            $bidang1 = ($bidang1 and !empty($nilai1)) ? true : false;
	        }
	        else
	            eval('$bidang1 = '.$nilai1.' '.$prosentase['I'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai1.';');
	    }

	    //pengecekan bidang 2
	    if(!empty($prosentase['II'][$fungsionaltujuan][$idpendidikan]['operator'])){
	        if(strpos($prosentase['II'][$fungsionaltujuan][$idpendidikan]['operator'], '<') !== false){
	            eval('$bidang2 = '.$nilai2.' '.$prosentase['II'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai2.';');
	            $bidang2 = ($bidang2 and !empty($nilai2)) ? true : false;
	        }
	        else
	            eval('$bidang2 = '.$nilai2.' '.$prosentase['II'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai2.';');
	    }

	    //pengecekan bidang 3    
	    if(!empty($prosentase['III'][$fungsionaltujuan][$idpendidikan]['operator'])){
	        if(strpos($prosentase['III'][$fungsionaltujuan][$idpendidikan]['operator'], '<') !== false){
	            eval('$bidang3 = '.$nilai3.' '.$prosentase['III'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai3.';');
	            $bidang3 = ($bidang3 and !empty($nilai3)) ? true : false;
	        }
	        else
	            eval('$bidang3 = '.$nilai3.' '.$prosentase['III'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai3.';');
	    }

	    //pengecekan bidang 4
	    if(!empty($prosentase['IV'][$fungsionaltujuan][$idpendidikan]['operator'])){
	        if(strpos($prosentase['IV'][$fungsionaltujuan][$idpendidikan]['operator'], '<') !== false){
	            eval('$bidang4 = '.$nilai4.' '.$prosentase['IV'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai4.';');
	            $bidang4 = ($bidang4 and !empty($nilai4)) ? true : false;
	        }
	        else
	            eval('$bidang4 = '.$nilai4.' '.$prosentase['IV'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai4.';');
	    }
		
		//pengecekan apakah sudah memenuhi
        if (!$bidang1)
            $msg = 'Total Bidang I belum memenuhi<br>';
        if (!$bidang2)
            $msg .= 'Total Bidang II belum memenuhi<br>';
        if (!$bidang3)
            $msg .= 'Total Bidang III belum memenuhi<br>';
        if (!$bidang4)
            $msg .= 'Total Bidang IV belum memenuhi';
		
		if(!empty($msg)){
			echo '<div align="center"><font color="red"><blink>'.$msg.'</blink></font></div>';
		}
	}
	
	$rowstyle = array( '0' => 'NormalBG', '1' => 'AlternateBG');
	$a_semester = $p_model::PeriodeSemester();
	
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/foreditx.js"></script>
</head>
<body>
	<table width="100%">
		<tr>
			<td>
			<form name="pageform" id="pageform" method="post" action="<?= Route::navAddress(Route::thisPage()) ?>">
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
					
					if(empty($p_fatalerr)){
				?>
						<table border="0" cellspacing="10" align="center">
							<tr>
								<?	if($c_readlist) { ?>
								<td id="be_list" class="TDButton" onclick="goList()">
									<img src="images/list.png"> Daftar
								</td>
								<?	} if($c_edit) { ?>
							   <td id="be_edit" class="TDButton" onclick="goEdit()">
									<img src="images/edit.png"> Sunting
								</td>
								<td id="be_save" class="TDButton" onclick="goSave()" style="display:none">
									<img src="images/disk.png"> Simpan
								</td>
								<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
									<img src="images/undo.png"> Batal
								</td>
								<?if(!empty($r_subkey) and (Modul::getRole() == 'A' or Modul::getRole() == 'admhrm')){?>
								<td id="be_valid" class="TDButton" onclick="goValidasi()">
									<img src="images/disk.png"> Validasi
								</td>
								<?	}} if($c_delete and !empty($r_subkey)) { ?>
								<td id="be_delete" class="TDButton" onclick="goDelete()">
									<img src="images/delete.png"> Hapus
								</td>
								<?	} ?>
								<?if(!empty($status) and !empty($r_subkey)){?>
								<td id="be_print" class="TDButton" onclick="goPrint('<?= $r_subkey?>')">
									<img src="images/small-print.png"> Cetak
								</td>
								<?}?>
							</tr>
						</table>
				<?
					}
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
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%">Semester yang dipilih</td>
							<td  class="RightColumnBG" width="40%">
							<?php
								if(!empty($status) and !empty($r_subkey))
									$variable = $a_smtr;
								else
									$variable = $a_ceksmtr;

								$ic=0;
								foreach ($variable as $key => $value) {
									if($ic > 0)
										echo '<br>';
							?>
								<span id="show"><?= in_array($value, $a_smtr) ? '<img src="images/check.png">' : ''?> <?= substr($value,0,4).' '.$a_semester[substr($value,4,2)]?></span>
								<span id="edit" style="display: none">
								<input name="smtr[]" id="smtr_<?= $key?>" value="<?= $value?>" <?= in_array($value, $a_smtr) ? 'checked' : ''?> type="checkbox"><label for="smtr_<?= $key?>"><?= substr($value,0,4).' '.$a_semester[substr($value,4,2)]?></label>
								</span>
							<?
								$ic++;
								}
							?>
							</td>
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'tglusulan') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglusulan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'jabfungsionalasal') ?></td>
							<td  class="RightColumnBG">
								<?= Page::getDataInput($row,'jabfungsionalasal') ?>
								<?= Page::getDataInput($row,'fungsionalasal') ?>
							</td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmtasal') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tmtasal') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'fungsionaltujuan') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'fungsionaltujuan') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nosk') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nosk') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'keterangan') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglsk') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglsk') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statususulan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'statususulan') ?></td>
						</tr>
					</table>
					</div>
				</center>
				<br>
				
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr>
						<th width="50">No.</th>
						<th>Nama Bidang</th>
						<th width="100">Syarat Kopertis</th>
						<th width="100">Nilai Kredit</th>
						<th width="100">Sisa Lalu</th>
						<th width="100">Total Kredit</th>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td rowspan="6" align="center" valign="top">I.</td>
						<td colspan="5"><b>UNSUR UTAMA</b></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td>BIDANG IA (PENDIDIKAN)</td>
						<td rowspan="2"><?= $prosentase['I'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai1 ?></td>
						<td align="right"><?= number_format($nbidang1a,2);?></td>
						<td align="right">&nbsp;</td>
						<td align="right"><?= number_format($nbidang1a,2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td>BIDANG IB (PENGAJARAN)</td>
						<td align="right"><?= number_format($nbidang1b,2);?></td>
						<td align="right"><?= number_format($asal['sisabidang1b'],2);?></td>
						<td align="right"><?= number_format($nbidang1b+$asal['sisabidang1b'],2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td>BIDANG II (PENELITIAN/KARYA ILMIAH)</td>
						<td><?= $prosentase['II'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai2 ?></td>
						<td align="right"><?= number_format($nbidang2,2);?></td>
						<td align="right"><?= number_format($asal['sisabidang2'],2);?></td>
						<td align="right"><?= number_format($nbidang2+$asal['sisabidang2'],2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td>BIDANG III (PENGABDIAN MASYARAKAT)</td>
						<td><?= $prosentase['III'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai3 ?></td>
						<td align="right"><?= number_format($nbidang3,2);?></td>
						<td align="right">&nbsp;</td>
						<td align="right"><?= number_format($nbidang3,2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td><b>JUMLAH</b></td>
						<td>&nbsp;</td>
						<td align="right"><?= number_format($nbidang1a+$nbidang1b+$nbidang2+$nbidang3,2);?></td>
						<td align="right"><?= number_format($asal['sisabidang1b']+$asal['sisabidang2'],2);?></td>
						<td align="right"><?= number_format($nilaiutama,2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td rowspan="2" align="center" valign="top">II.</td>
						<td colspan="5"><b>UNSUR PENUNJANG</b></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td>BIDANG IV (KEGIATAN PENUNJANG)</td>
						<td><?= $prosentase['IV'][$fungsionaltujuan][$idpendidikan]['operator'].' '.$AKnilai4 ?></td>
						<td align="right"><?= number_format($nbidang4,2);?></td>
						<td align="right">&nbsp;</td>
						<td align="right"><?= number_format($nbidang4,2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td colspan="2"><b>PEROLEHAN NILAI ANGKA KREDIT</b></td>
						<td>&nbsp;</td>
						<td align="right"><?= number_format($nbidang1a+$nbidang1b+$nbidang2+$nbidang3+$nbidang4,2);?></td>
						<td align="right"><?= number_format($asal['sisabidang1b']+$asal['sisabidang2'],2);?></td>
						<td align="right"><?= number_format($nilaitotal,2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td colspan="5"><b>SYARAT NILAI ANGKA KREDIT</b></td>
						<td align="right"><?= number_format($selisihsyarat,2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td colspan="5"><b>KEKURANGAN ANGKA KREDIT</b></td>
						<td align="right"><?= number_format($kurang,2);?></td>
					</tr>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td colspan="5"><b>KELEBIHAN ANGKA KREDIT</b></td>
						<td align="right"><?= number_format($lebih,2);?></td>
					</tr>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$r_scroll ?>">
				<input type="hidden" name="nilaibidang1a" id="nilaibidang1a" value="<?= $nbidang1a ?>">
				<input type="hidden" name="nilaibidang1b" id="nilaibidang1b" value="<?= $nbidang1b+$asal['sisabidang1b'] ?>">
				<input type="hidden" name="nilaibidang2" id="nilaibidang2" value="<?= $nbidang2+$asal['sisabidang2'] ?>">
				<input type="hidden" name="nilaibidang3" id="nilaibidang3" value="<?= $nbidang3 ?>">
				<input type="hidden" name="nilaibidang4" id="nilaibidang4" value="<?= $nbidang4 ?>">
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
});

function goPrint(elem){
	var elem = elem.split('|');
	window.open("<?= Route::navAddress('rep_akdupak') ?>"+"&kode="+elem[0]+"&unit="+elem[1]+"&tahun="+elem[2]+"&semester="+elem[3],"_blank");
}

function goValidasi(){
	var retval;
	retval = confirm("Anda yakin untuk validasi Angka Kredit periode ini?");
	if (retval){
		$("#act").val("validasi");
		goSubmit();
	}
}
</script>
</body>
</html>
