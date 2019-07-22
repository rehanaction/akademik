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
	if(SDM::isPegawai()) {
		$r_self = 1;
		$c_kepeg = true;
	}
	
	if($c_kepeg){
		$c_edit = $a_auth['canupdate'];
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
	$p_listpage = 'data_aksimulasi';
	
	$p_model = mAngkaKredit;
	$p_dbtable = "ak_skdosen";
	$where = 'nourutakd';
	$p_col = 7;
	
	$asal = $p_model::getFungsional($conn,$r_key);
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tahun', 'label' => 'Periode Perhitungan', 'maxlength' => 4, 'size' => 4, 'default' => date('Y'));
	$a_input[] = array('kolom' => 'semester', 'type' => 'S', 'option' => $p_model::PeriodeSemester());
	$a_input[] = array('kolom' => 'tglusulan', 'label' => 'Tgl. Usulan', 'type' => 'D', 'notnull' => true);
	$a_input[] = array('kolom' => 'jabfungsionalasal', 'label' => 'Jabatan Asal', 'maxlength' => 50, 'size' => 30, 'default' => $asal['jabatanfungsional'], 'class' => 'ControlRead');
	$a_input[] = array('kolom' => 'fungsionalasal', 'type' => 'H', 'default' => $asal['idjfungsional']);
	$a_input[] = array('kolom' => 'tmtasal', 'label' => 'TMT. Jabatan Asal', 'type' => 'D', 'class' => 'ControlRead', 'default' => CStr::formatDate($asal['tmtmulai']));
	$a_input[] = array('kolom' => 'fungsionaltujuan', 'label' => 'Jabatan Tujuan', 'type' => 'S', 'option' => $p_model::jabatanFungsional($conn,$asal['idjfungsional']));
	$a_input[] = array('kolom' => 'tmtsk', 'label' => 'TMT. SK', 'type' => 'D', 'default' => date('Y-m-d'));
	$a_input[] = array('kolom' => 'statususulan', 'label' => 'Status KUM', 'type' => 'S', 'option' => $p_model::statusUsulan(), 'readonly' => true);
	$a_input[] = array('kolom' => 'nosk', 'label' => 'No. SK', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'tglsk', 'label' => 'Tgl. SK', 'type' => 'D');
	$a_input[] = array('kolom' => 'namapejabat', 'label' => 'Pejabat', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'arsip', 'label' => 'Arsip', 'maxlength' => 50, 'size' => 30);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'A', 'rows' => 2, 'cols' => 50, 'maxlength' => 255);
	$a_input[] = array('kolom' => 'angkakredit', 'type' => 'H');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		//simpan ke ak_dosen
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if($record['semester'] != 'null' and $record['tahun'] != 'null')
			$record['periodeakreditasi'] = $record['tahun'].str_pad($record['semester'],2,'0', STR_PAD_LEFT);
		
		$record['idpegawai'] = $r_key;
		$record['jenisjabatan'] = 'K'; //untuk jabatan kopertis
		$record['statususulan'] = 'Y'; //untuk validasi
		$record['nilaibidang1a'] = CStr::cAlphaNum($_POST['nilaibidang1a']);
		$record['nilaibidang1b'] = CStr::cAlphaNum($_POST['nilaibidang1b']);
		$record['nilaibidang2'] = CStr::cAlphaNum($_POST['nilaibidang2']);
		$record['nilaibidang3'] = CStr::cAlphaNum($_POST['nilaibidang3']);
		$record['nilaibidang4'] = CStr::cAlphaNum($_POST['nilaibidang4']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		
		if(!$p_posterr){
			$err = $p_model::validasiRWT($conn,$r_subkey);
			$ok = Query::isOK($err);
			
			if($ok){
				unset($post);
				$conn->CommitTrans($ok);
			}
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
		if($t_row['id'] == 'angkakredit')
			$kredittujuan = $t_row['value'];
	}
			
	/*=== list beberapa bidang ===*/
	if(!empty($r_subkey))
		$periode = $p_model::getPeriode($conn,$r_subkey);
	
	//bidang pendidikan
	$nbidang1a = $p_model::getNilaiIA($conn,$r_key,$periode);
	
	//bidang pengajar
	$nbidang1b = $p_model::getNilaiIB($conn,$r_key,$periode);
	
	//bidang penelitian
	$nbidang2 = $p_model::getNilaiII($conn,$r_key,$periode);
	
	//bidang pengabdian
	$nbidang3 = $p_model::getNilaiIII($conn,$r_key,$periode);
	
	//bidang penunjang
	$nbidang4 = $p_model::getNilaiIV($conn,$r_key,$periode);
	
	//prosentase
	$prosentase = $p_model::getProsentase($conn);
	
	//jumlah nilai angka kredit
	$nilai1 = $nbidang1a + $nbidang1b + $asal['sisabidang1b'];
	$nilai2 = $nbidang2 + $asal['sisabidang2'];
	$nilai3 = $nbidang3;
	$nilai4 = $nbidang4;
	$nilaiutama = $nilai1 + $nilai2 + $nilai3;
	$nilaitotal = $nilaiutama + $nilai4;
	
	//cek syarat nilai yang harus dikumpulkan
	$selisihsyarat = $kredittujuan - $asal['angkakredit'];
	if($nilaitotal > $selisihsyarat){
		$lebih = $nilaitotal - $selisihsyarat;
		//$selisihsyarat = $nilaitotal;
	}

	$kurang = $selisihsyarat - $nilaitotal;
	$kurang = $kurang <= 0 ? 0 : $kurang;
	
	$AKnilai1 = $prosentase['I']/100 * $selisihsyarat;
	$AKnilai2 = $prosentase['II']/100 * $selisihsyarat;
	$AKnilai3 = $prosentase['III']/100 * $selisihsyarat;
	$AKnilai4 = $prosentase['IV']/100 * $selisihsyarat;
	
	//pengecekan apakah sudah memenuhi
	if($nilai1 < $AKnilai1)
		$msg = 'Total Bidang I belum memenuhi';
	if($nilai2 < $AKnilai2)
		$msg .= '<br>Total Bidang II belum memenuhi';
	if($nilai3 > $AKnilai3 or empty($nilai3))
		$msg .= '<br>Total Bidang III belum memenuhi';
	if($nilai4 > $AKnilai4 or empty($nilai4))
		$msg .= '<br>Total Bidang IV belum memenuhi';
	
	if(!empty($msg)){
		echo '<div align="center"><font color="red"><blink>'.$msg.'</blink></font></div>';
		
		//bila terjadi kekurangan, maka tidak bisa validasi
		$c_edit = false;
	}
	
	$rowstyle = array( '0' => 'NormalBG', '1' => 'AlternateBG');
	$crow = 0;
	
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
								<td id="be_save" class="TDButton" onclick="goValidasi()" style="display:none">
									<img src="images/disk.png"> Validasi
								</td>
								<td id="be_undo" class="TDButton" onclick="goUndo()" style="display:none">
									<img src="images/undo.png"> Batal
								</td>
								<?	} ?>
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
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'tahun') ?></td>
							<td  class="RightColumnBG" width="40%">
								<?= Page::getDataInput($row,'tahun') ?>
								<?= Page::getDataInput($row,'semester') ?>
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
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tmtsk') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tmtsk') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'statususulan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'statususulan') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'nosk') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'nosk') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglsk') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'tglsk') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'namapejabat') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'namapejabat') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'arsip') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'arsip') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'keterangan') ?></td>
							<td  class="RightColumnBG" colspan="3"><?= Page::getDataInput($row,'keterangan') ?></td>
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
						<td rowspan="2"><?= 'Min '.$AKnilai1 ?></td>
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
						<td><?= 'Min '.$AKnilai2 ?></td>
						<td align="right"><?= number_format($nbidang2,2);?></td>
						<td align="right"><?= number_format($asal['sisabidang2'],2);?></td>
						<td align="right"><?= number_format($nbidang2+$asal['sisabidang2'],2);?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td>BIDANG III (PENGABDIAN MASYARAKAT)</td>
						<td><?= 'Maks '.$AKnilai3 ?></td>
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
						<td><?= 'Maks '.$AKnilai4 ?></td>
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

function goValidasi(){
	var retval;
	retval = confirm("Anda yakin untuk validasi Angka Kredit periode ini?");
	if (retval)
		goSave();
}
</script>
</body>
</html>
