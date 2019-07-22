<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_edit = $a_auth['canupdate'];
	
	$c_tutup = $a_auth['canother']['C'];
	$c_buka = $a_auth['canother']['O'];
	
	// include
	require_once(Route::getModelPath('absensikuliah'));
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('setting'));
	require_once(Route::getModelPath('skalanilai'));
	require_once(Route::getModelPath('unsurnilai'));
	require_once(Route::getUIPath('combo'));
	
	// properti halaman
	$p_title = 'Pengisian Nilai Huruf';
	$p_tbwidth = 800;
	$p_aktivitas = 'NILAI';
	$p_listpage = 'list_nilai';
	$p_minriwayat = 12;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	if(empty($r_key))
		Route::navigate($p_listpage);
	
	$param = explode('|',$r_key);
	$rs_param = $conn->Execute("select *,coalesce(prosentasenilai::int, 0) as persen from akademik.ak_unsurpenilaian where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
				kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."'");
	
	$a_unsurnilai = array();
	$a_unsurnilaiparameter = array();
	$a_unsurnilaikey = array();
	while($row = $rs_param->FetchRow()){
		// $a_unsurnilai[] = $row['namaparameter'].'<br>('.$row['presentase'].'%)';
		$a_unsurnilai[] = $row['namaunsurnilai'];
		$a_unsurnilaiparameter[$row['idunsurnilai']] = $row['prosentasenilai'];
		$a_unsurnilaikey[] = $row['idunsurnilai'];
	}
	
	$n_unsurnilai = count($a_unsurnilai);
	$p_colnum = 8 + $n_unsurnilai;	
	
	// mendapatkan data
	$a_infokelas = mKelas::getDataSingkat($conn,$r_key);
	$a_skalanilai = mSkalaNilai::getDataKurikulum($conn,$a_infokelas['thnkurikulum']);
	
	// cek periode nilai dan nilai masuk
	if($a_infokelas['periode'] != Akademik::getPeriodeNilai()) {
		$c_edit = false;
		$c_tutup = false;
	}
	else if(!empty($a_infokelas['nilaimasuk']) or Akademik::getIsiNilai() == 'DITUTUP')
		$c_edit = false;
	
	// cek unsur nilai
	// $a_unsurnilai = mUnsurNilaiKelas::getDataKelas($conn,$r_key);
	// if(empty($a_unsurnilai))
		// $a_unsurnilai = mUnsurNilaiKelas::insertFromUnsurNilai($conn,$r_key);
	
	// cek jurnal/riwayat perkuliahan
	$n_riwayat = mKuliah::getJumlahPerKelas($conn,$r_key,true);
	
	// ambil data
	$p_pesan = mSetting::getPesanPengesahan($conn);
	
	//dapatkan combo nilai huruf untuk periodedaftar <2013, thnkurikulum ikut periodedaftarnya
	$programpend = $conn->GetOne("select kode_jenjang_studi from akademik.ak_prodi where kodeunit='".$param[2]."'");
	$rs_nilaihuruf = $conn->Execute("select * from akademik.ak_skalanilai where thnkurikulum = '".$param[0]."' and programpend = '".$programpend."' order by nangkasn asc");
	$arr_nilaihuruf = array();
	$arr_nangka = array();
	while($row_nh = $rs_nilaihuruf->FetchRow()){
		$arr_nilaihuruf[] = $row_nh['nhuruf'];
		$arr_nangka[$row_nh['nhuruf']] = $row_nh['nangkasn'];
	}
	
	$r_act = $_POST['act'];
	if($r_act == 'saveall' and $c_edit) {
		$ok = true;
		$conn->BeginTrans();
		// var_dump($_POST['simpan']);exit;
		if(!empty($_POST['npm'])) {
			foreach($_POST['npm'] as $t_idx => $t_npm) {
				// $t_simpan = (int)$_POST['simpan'][$t_idx];
				// if(empty($t_simpan))
					// continue;
				
				$t_npm = CStr::removeSpecial($t_npm);
				// masukkan krs
				// if($ok) {
					$t_dipakai = (int)$_POST['dipakai_'.$t_npm];
					
					$record = array();
					// $record['nnumerik'] = CStr::cStrNull(CStr::cStrDec($_POST['nnumerik'][$t_idx]));
					$record['nnumerik'] = null;
					$record['nhuruf'] = $_POST['nilaihuruf'][$t_idx];
					$record['dipakai'] = (empty($t_dipakai) ? 0 : -1);
					$record['nangka'] = $arr_nangka[$_POST['nilaihuruf'][$t_idx]];
										
					list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_key.'|'.$t_npm,true);
					if($p_posterr) {
						$ok = false;
						break;
					}
				// }
			}
		}
		
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'save' and $c_edit) {
		$ok = true;
		$conn->BeginTrans();
		
		$r_npm = CStr::removeSpecial($_POST['subkey']);
		
		if(!empty($_POST['npm'])) {
			foreach($_POST['npm'] as $t_idx => $t_npm) {
				$t_simpan = (int)$_POST['simpan'][$t_idx];
				if(empty($t_simpan))
					continue;
				
				$t_npm = CStr::removeSpecial($t_npm);
				if($t_npm == $r_npm) {
					
					// masukkan krs
					if($ok) {
						$t_dipakai = (int)$_POST['dipakai_'.$t_npm];
						
						$record = array();
						// $record['nnumerik'] = CStr::cStrNull(CStr::cStrDec($_POST['nnumerik'][$t_idx]));
						$record['nnumerik'] = null;
						$record['nhuruf'] = $_POST['nilaihuruf'][$t_idx];
						$record['dipakai'] = (empty($t_dipakai) ? 0 : -1);
						$record['nangka'] = $arr_nangka[$_POST['nilaihuruf'][$t_idx]];
						
						list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_key.'|'.$t_npm,true);
						if($p_posterr)
							$ok = false;
						
						break;
					}
				}
			}
		}
		
		$conn->CommitTrans($ok);
	}
	else if($r_act == 'close' and $c_tutup and $n_riwayat >= $p_minriwayat) {
		$record = array();
		$record['nilaimasuk'] = -1;
		$record['usernilaimasuk'] = Modul::getUserName();
		$record['waktunilaimasuk'] = date('Y-m-d H:i:s');
		
		list($p_posterr,$p_postmsg) = mKelas::updateRecord($conn,$record,$r_key,true);
		
		if(!$p_posterr)
			$a_infokelas['nilaimasuk'] = $record['nilaimasuk'];
	}
	else if($r_act == 'deleteparameter' and $c_edit) {
		$conn->Execute("delete from akademik.ak_unsurpenilaian where idunsurnilai='".$_POST['idparameter']."'");
	}
	else if($r_act == 'saveparameter' and $c_edit) {
		$record = array();
		$param = explode('|',$_POST['key']);
		$record['namaunsurnilai'] = $_POST['subjek'];
		$record['prosentasenilai'] = $_POST['bobot'];
		$record['thnkurikulum'] = $param[0];
		$record['periode'] = $param[3];
		$record['kodemk'] = $param[1];
		$record['kelasmk'] = $param[4];
		$record['kodeunit'] = $param[2];
		
		//cek apakah melebihi 100%
		$rs_persen = $conn->GetOne("select sum(prosentasenilai::numeric) as persen from akademik.ak_unsurpenilaian where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
							kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."'");
		$tot_persen = $rs_persen + $record['prosentasenilai'];
		if($tot_persen > 100 ){
			$p_posterr = true;
			$p_postmsg = 'Persentase melebihi 100%. Cek kembali persentase.';
		}else{
			Query::recInsert($conn,$record,'akademik.ak_unsurpenilaian');
		}
		
		
		// list($p_posterr,$p_postmsg) = mKelas::insertRecord($conn,$record,$r_key,true);
	}
	else if($r_act == 'open' and $c_buka) {
		$record = array();
		$record['nilaimasuk'] = 0;
		$record['usernilaimasuk'] = 'null';
		$record['waktunilaimasuk'] = 'null';
		
		list($p_posterr,$p_postmsg) = mKelas::updateRecord($conn,$record,$r_key,true);
		
		if(!$p_posterr)
			$a_infokelas['nilaimasuk'] = $record['nilaimasuk'];
	}
	else if($r_act == 'downxls') {
		$a_header = array('NIM','NAMA');
		$a_text = array('NIM' => true);
		
		// header dari unsur nilai, mengambil persen juga
		// $i = count($a_header);
		// $a_persen = array();
		// foreach($a_unsurnilai as $t_data) {
			// $a_header[] = strtoupper($t_data['namaunsurnilai']);
			// $a_persen[$i++] = (float)$t_data['prosentasenilai'];
		// }
		
		// header khusus
		$a_header[] = 'nh';
		
		// data peserta
		$a_huruf = CStr::arrayHuruf();
		$a_data = mKelas::getDataPeserta($conn,$r_key);
		$a_unsurnilaimhs = mUnsurNilaiMhs::getDataKelas($conn,$r_key);
		
		$a_dataxls = array();
		foreach($a_data as $t_data) {
			$t_key = trim($t_data['nim']);
			$t_nilai = $a_unsurnilaimhs[$t_key];
			
			$t_dataxls = array();
			$t_dataxls['nim'] = $t_key;
			$t_dataxls['nama'] = trim($t_data['nama']);
			
			// $t_dataxls['na'] = $t_data['nnumerik'];
			$t_dataxls['na'] = $t_data['nhuruf'];
			
			// dari unsur nilai
			foreach($a_unsurnilai as $t_data)
				$t_dataxls[strtolower($t_data['namaunsurnilai'])] = $t_nilai[$t_data['idunsurnilai']];
			
			$a_dataxls[] = $t_dataxls;
		}
		
		// menghilangkan segala echo
		ob_clean();
		
		header("Content-Type: application/msexcel");
		header('Content-Disposition: attachment; filename="template_nilai.xls"');
		
		// pakai phpexcel
		require_once($conf['includes_dir'].'phpexcel/PHPExcel.php');
		
		$xls = new PHPExcel();
		$xls->setActiveSheetIndex(0);
		$sheet = $xls->getActiveSheet();
		
		// header
		$r = 1;
		foreach($a_header as $i => $t_header)
			$sheet->setCellValue($a_huruf[$i].$r,$t_header);
		
		// data
		if(empty($a_unsurnilai))
			$t_hitungna = false;
		else
			$t_hitungna = true;
		
		foreach($a_dataxls as $row) {
			$r++;
			
			$i = -1;
			foreach($a_header as $k => $v) {
				$i++;
				
				// nilai angka pakai formula
				if($v == 'NA' and $t_hitungna) {
					$a_formula = array();
					foreach($a_persen as $j => $t_persen)
						$a_formula[] = '('.$a_huruf[$j].$r.'*'.$t_persen.')';
					
					$sheet->setCellValue($a_huruf[$i].$r,'=round(('.implode('+',$a_formula).')/100,0)');
				}
				else {
					$t_data = $row[strtolower($v)];
					
					if($a_text[$v])
						$sheet->getCell($a_huruf[$i].$r)->setValueExplicit($t_data,PHPExcel_Cell_DataType::TYPE_STRING);
					else
						$sheet->setCellValue($a_huruf[$i].$r,$t_data);
				}
			}
		}
		
		// paskan ukuran
		$n = count($a_header);
		for($i=0;$i<$n;$i++)
			$sheet->getColumnDimension($a_huruf[$i])->setAutoSize(true);
		
		$xlsfile = PHPExcel_IOFactory::createWriter($xls,'Excel5');
		$xlsfile->save('php://output');
		
		exit;
	}
	else if($r_act == 'upxls' and $c_edit) {
		$r_file = $_FILES['xls']['tmp_name'];
		
		// pakai excel reader
		require_once($conf['includes_dir'].'phpexcel/excel_reader2.php');
		$xls = new Spreadsheet_Excel_Reader($r_file);
		
		$cells = $xls->sheets[0]['cells'];
		$numrow = count($cells);
		
		// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
		if(empty($numrow)) {
			if(($handle = fopen($r_file, 'r')) !== false) {
				while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
					$numrow++;
					foreach($data as $k => $v)
						$cells[$numrow][$k+1] = $v;
				}
				fclose($handle);
			}
		}
		
		// baris pertama adalah header
		$conn->BeginTrans();
		
		$ok = true;
		for($r=2;$r<=$numrow;$r++) {
			$data = $cells[$r];
			
			$rowxls = array();
			foreach($cells[1] as $k => $v) {
				$v = strtolower($v);
				$rowxls[$v] = trim($data[$k]);
			}
			
			$t_npm = $rowxls['nim'];
				
			// masukkan unsur nilai mahasiswa
			$record = mKelas::getKeyRecord($r_key);
			$record['nim'] = $t_npm;
				
			// $t_allnull = true;
			// $t_numerik = '';
			// if(!empty($a_unsurnilai)) {
				// foreach($a_unsurnilai as $t_unsur) {
					// $t_idunsur = $t_unsur['idunsurnilai'];
					// $t_namaunsur = strtolower($t_unsur['namaunsurnilai']);
					
					// $record['idunsurnilai'] = $t_idunsur;
					// $record['nilaiunsur'] = CStr::cNumNull($rowxls[$t_namaunsur]);
					
					// if($record['nilaiunsur'] != 'null') {
						// $record['nilaiunsur'] = round($record['nilaiunsur'],2);
						
						// // untuk menghitung nilai numerik
						// if(empty($t_numerik))
							// $t_numerik = 0;
						
						// $t_numerik += ($record['nilaiunsur']*$t_unsur['prosentasenilai'])/100;
						
						// $t_allnull = false;
						// list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::saveRecord($conn,$record,$r_key.'|'.$t_npm.'|'.$t_idunsur,true);
					// }
					// else
						// list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::delete($conn,$r_key.'|'.$t_npm.'|'.$t_idunsur);
					
					// if($p_posterr) {
						// $ok = false;
						// break;
					// }
				// }
			// }
			// else
				$t_numerik = $rowxls['nh'];
			
			// masukkan krs
			if($ok) {
				$record = array();
				// $record['nnumerik'] = CStr::cNumNull($t_numerik);
				$record['nnumerik'] = null;
				$record['nhuruf'] = $t_numerik;
				$record['nangka'] = $arr_nangka[$record['nhuruf']];
				
				// if($record['nnumerik'] != 'null')
					// $record['nnumerik'] = round($record['nnumerik']);
				
				// if($t_allnull)
					// $record['nangka'] = ;
				
				list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$record,$r_key.'|'.$t_npm,true);
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
		}
		
		if($ok)
			$p_postmsg = 'Impor data dari format excel berhasil';
		
		$conn->CommitTrans($ok);
	}
	
	// cek ulang hak akses
	if($a_infokelas['periode'] == Akademik::getPeriodeNilai()) {
		if(!empty($a_infokelas['nilaimasuk'])) {
			$c_edit = false;
			$p_postmsg = 'Nilai perkuliahan kelas ini sudah disahkan';
		}
		else if(Akademik::getIsiNilai() == 'DITUTUP') {
			$c_edit = false;
			$p_postmsg = 'Periode penilaian sudah ditutup';
		}
		else
			$c_edit = $a_auth['canupdate'];
	}
	else
		$p_postmsg = 'Periode penilaian untuk perkuliahan ini sudah berlalu';
	
	// mendapatkan data
	$a_data = mKelas::getDataPeserta($conn,$r_key);
	$a_absensi = mAbsensiKuliah::getListPersenPerKelas($conn,$r_key);
	$a_unsurnilaimhs = mUnsurNilaiMhs::getDataKelas($conn,$r_key);
	
	//cek apakah dosen Koordinator atau admin? jika ya bisa nambahkan parameter
	$ispjmk = false;
	if(Modul::getRole() == 'A'){
		$ispjmk = true;
	}else{
		$isdosenpjmk = $conn->GetRow("select ispjmk from akademik.ak_mengajar where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
				kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."' and nipdosen='".Modul::getUserName()."'");
	}
	
	// $rs_param = $conn->Execute("select *,coalesce(prosentasenilai::int, 0) as persen from akademik.ak_unsurpenilaian where thnkurikulum='".$param[0]."' and periode='".$param[3]."' and 
				// kodeunit='".$param[2]."' and kodemk='".$param[1]."' and kelasmk='".$param[4]."' order by idunsurnilai");
	// $a_unsurnilai = array();
	// $a_unsurnilaiparameter = array();
	// $a_unsurnilaikey = array();
	// while($row = $rs_param->FetchRow()){
		// // $a_unsurnilai[] = $row['namaparameter'].'<br>('.$row['presentase'].'%)';
		// $a_unsurnilai[] = $row['namaunsurnilai'];
		// $a_unsurnilaiparameter[$row['idunsurnilai']] = $row['prosentasenilai'];
		// $a_unsurnilaikey[] = $row['idunsurnilai'];
	// }
	// $rs_param->MoveFirst();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<div style="float:left; width:15%">
				<? require_once('inc_sidemenudosen.php');?>
			</div>
			<div style="float:left; width:50%">
			<form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<center>
				<?php require_once('inc_headerkelas.php') ?>
				</center>
				<br>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<?	if(isset($p_posterr)) { ?>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
				<?	} else { ?>
				<div style="width:<?= $p_tbwidth ?>px;font-size:14px">
					<strong><?= $p_postmsg ?></strong>
				</div>
				<?	} ?>
				</center>
				<div class="Break"></div>
				<?	}
					if($c_edit or !empty($a_infokelas['nilaimasuk'])) {
				?>
				<center>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<?	if($c_edit) { ?>
						<td align="center" colspan="2">Impor Data dari Format Excel</td>
						<?	}
							if(!empty($a_infokelas['nilaimasuk'])) { ?>
						<td align="center">Versi Cetak</td>
						<?	} ?>
					</tr>
					<tr class="NoHover NoGrid">
						<?	if($c_edit) { ?>
						<td width="55"> &nbsp;
							<strong>Upload </strong>
						</td>
						<td>
							<strong> : </strong> <input type="file" name="xls" id="xls" size="30" class="ControlStyle">
							<input type="button" value="Upload" onclick="goUpXLS()"> &nbsp; &nbsp;
							<u class="ULink" onclick="goDownXLS()">Download Template Excel...</u>
						</td>
						<?	}
							if(!empty($a_infokelas['nilaimasuk'])) { ?>
						<td>
							<u class="ULink" onclick="goPrint(false)">Cetak Nilai...</u>
							<u class="ULink" onclick="goPrint(true)">Download Versi Excel...</u>
						</td>
						<?	} ?>
					</tr>
				</table>
				</center>
				<br>
				<!--
				<table width="100" cellpadding="4" cellspacing="0" class="GridStyle">
					<tr class="DataBG">
						<td align="center" colspan="3">Parameter Nilai</td>
					</tr>
					<tr class="DataBG">
						<td align="center">Subjek</td>
						<td align="center">Bobot(%)</td>
						<td align="center">&nbsp;</td>
					</tr>
					<? $i=0;while($row = $rs_param->FetchRow()){?>
						<tr class="NoHover NoGrid">
							<td align="center"><?= $row['namaunsurnilai']?></td>
							<td align="center"><?= $row['prosentasenilai']?></td>
							<td align="center"><img title="Hapus Data" src="images/delete.png" onclick="goDeleteParameter(this,'<?= $row['idunsurnilai']?>')" style="cursor:pointer"></td>
						</tr>
					<?$i++;}
					if($i==0){?>
						<tr class="NoHover NoGrid"><td colspan=3 align="center">Data tidak ditemukan</td></tr>
					<?}?>
					<?if($ispjmk){?>
					<tr class="NoHover NoGrid">
						<td align="center"><input type="text" name="subjek" maxlength="50" size="10"></td>
						<td align="center"><input type="text" name="bobot" maxlength="3" size="5"></td>
						<td align="center"><img id="<?= $t_key ?>" title="Simpan Parameter Nilai" src="images/disk.png" onclick="goSaveParameter(this)" style="cursor:pointer"></td>
					</tr>
					<?}?>
					
				</table>
				<br>-->
				<?	} ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
								<h1><?= $p_title ?></h1>
							</div>
							<div class="left" style="padding-top:8px;padding-right:50px">
								<div><input id="<?= $r_key?>" type="button" value="Input Nilai Angka" onclick="goNilaiAngka(this)"></div>
							</div>
							<div class="right" style="padding-top:8px;padding-right:10px">
								<div class="YellowBG" style="float:left;width:20px;height:20px;border:1px solid #CCC"></div>
								<div style="float:left;color:#FFF"> &nbsp; Absensi di bawah 75 %</div>
							</div>
							<? if(!empty($a_infokelas['nilaimasuk'])) { ?>
							<div class="right"> 
								<img title="Cetak Nilai" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							</div>
							<? } ?>
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
						<th rowspan="2" width="25">No.</th>
						<th rowspan="2" width="80">NIM</th>
						<th rowspan="2">Nama</th>
					<?	/*foreach($a_unsurnilai as $t_unsur) { ?>
						<th width="50"><?= $t_unsur ?></th>
					<?	}*/ ?>
						<th rowspan="2" width="40">NA</th>
						<th rowspan="2" width="30">NH</th>
						<th rowspan="2" width="30">Lulus</th>
						<th rowspan="2" width="30" onclick="toggleDipakai()" style="cursor:pointer;color:#00F">Dipakai</th>
						<?	if($c_edit) { ?>
						<th rowspan="2" width="50">Aksi</th>
						<?	} ?>
					</tr>
					<tr>
					<?	/*foreach($a_unsurnilaikey as $t_unsur) { ?>
						<th><?= $a_unsurnilaiparameter[$t_unsur] ?> %</th>
					<?	}*/ ?>
					</tr>
					<?	/********/
						/* ITEM */
						/********/
						
						$i = 0;
						foreach($a_data as $row) {
							$t_key = trim($row['nim']);
							
							// cek absen
							if($a_absensi[$t_key] < 75)
								$rowstyle = 'YellowBG';
							else if ($i % 2)
								$rowstyle = 'NormalBG';
							else
								$rowstyle = 'AlternateBG';
							$i++;
							
							$t_nilai = $a_unsurnilaimhs[$t_key];
							$t_nnumerik = $row['nnumerik'];
							if(strval($t_nnumerik) != '')
								$t_nnumerik = round($t_nnumerik);
					?>
					<tr valign="top" class="<?= $rowstyle ?>">
						<td><?= $i ?>.</td>
						<td align="center">
							<?= $row['nim'] ?>
							<input type="hidden" name="npm[]" value="<?= $t_key ?>">
							<input type="hidden" name="simpan[]" value="0">
						</td>
						<td><?= $row['nama'] ?></td>
					<?	/*foreach($a_unsurnilaikey as $t_unsur2) { ?>
						<td align="center"><?= UI::createTextBox('n_'.$t_unsur2.'[]',CStr::formatNumber($t_nilai[$t_unsur2],2,true),'ControlStyle XCell',5,3,$c_edit,'onkeydown="return onlyNumber(event,this,true)" onblur="hitungNilai(this)"') ?></td>
					<?	}
						if(empty($n_unsurnilai)) { ?>
						<td align="center"><?= UI::createTextBox('nnumerik[]',$t_nnumerik,'ControlStyle XCell',5,3,$c_edit,'onkeydown="return onlyNumber(event,this)" onblur="setSimpan(this)"') ?></td>
					<?	}
						else { */?>
						<td align="center"><?= UI::createTextBox('nnumerik[]',$t_nnumerik,'ControlRead XCell',5,3,$c_edit,'readonly') ?></td>
					<?	//} ?>
						<td align="center">
							<select name="nilaihuruf[]">
								<?foreach($arr_nilaihuruf as $nilaih){?>
									<option value="<?= $nilaih?>" <?= $nilaih == $row['nhuruf']?'selected':''?>><?= $nilaih?></option>
								<?}?>
							</select>
						</td>
						<td align="center"><?= empty($row['lulus']) ? '' : '<img src="images/check.png">' ?></td>
						<td align="center">
						<?	if($c_edit) { ?>
							<input type="checkbox" name="dipakai_<?= $t_key ?>" value="1"<?= empty($row['dipakai']) ? '' : ' checked' ?>>
						<?	} else { ?>
							<?= empty($row['dipakai']) ? '' : '<img src="images/check.png">' ?>
						<?	} ?>
						</td>
						<?	if($c_edit) { ?>
						<td align="center">
							<img id="<?= $t_key ?>" title="Simpan Nilai" src="images/disk.png" onclick="goSave(this)" style="cursor:pointer">
						</td>
						<?	} ?>
					</tr>
					<?	}
						if($i == 0) {
					?>
					<tr>
						<td colspan="<?= $p_colnum ?>" align="center">Data kosong</td>
					</tr>
					<?	}
						if($c_edit) { ?>
					<tr class="LeftColumnBG">
						<td colspan="<?= $p_colnum ?>" align="center">
							<input type="button" value="Simpan" onclick="goSaveAll()" style="font-size:14px">
						</td>
					</tr>
					<?	} ?>
				</table>
				
				<?	if($c_tutup and empty($a_infokelas['nilaimasuk'])) { ?>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr class="DataBG">
						<td>Pengesahan Nilai</td>
					</tr>
					<?	if($n_riwayat < $p_minriwayat) { ?>
					<tr>
						<td align="center" style="font-size:14px;padding:10px">
							Nilai tidak bisa disahkan karena jumlah jurnal perkuliahan yang berstatus Selesai <?= $n_riwayat ?>, kurang dari <?= $p_minriwayat ?>.<br>
							Untuk memasukkan jurnal perkuliahan, klik <u class="ULink" onclick="goSubmitBlank('<?= Route::navAddress('list_jurnal') ?>')">di sini</u>
						</td>
					</tr>
					<?	} else { ?>
					<tr>
						<td align="center" style="font-size:14px;padding:10px">
							<?= $p_pesan ?>
						</td>
					</tr>
					<tr class="LeftColumnBG">
						<td align="center"><input type="button" value="Sahkan Nilai" onclick="goClose()" style="font-size:14px"></td>
					</tr>
					<?	} ?>
				</table>
				<?	}
					else if($c_buka and !empty($a_infokelas['nilaimasuk'])) { ?>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr class="DataBG">
						<td>Pembatalan Pengesahan Nilai</td>
					</tr>
					<tr>
						<td align="center" style="font-size:14px;padding:10px">
							Lakukan pembatalan pengesahan nilai jika nilai terpaksa harus diubah.
						</td>
					</tr>
					<tr class="LeftColumnBG">
						<td align="center"><input type="button" value="Batalkan Pengesahan Nilai" onclick="goOpen()" style="font-size:14px"></td>
					</tr>
				</table>
				<?	} ?>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="format" id="format">
				<input type="hidden" name="idparameter" id="idparameter">
			</form>
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">

$(document).ready(function() {
	initXCell();
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
});

function toggleDipakai() {
	var check = $("[name^='dipakai_']").attr("checked");
	
	if(check)
		$("[name^='dipakai_']").attr("checked",false);
	else
		$("[name^='dipakai_']").attr("checked",true);
}

function hitungNilai(elem) {
	var tr = setSimpan(elem);
	
	var subnilai
	var nilai = "";
	<?	foreach($a_unsurnilaikey as $t_unsur) { ?>
	subnilai = jQuery.trim(tr.find("[name='n_<?= $t_unsur ?>[]']").val());
	if(subnilai != "") {
		if(nilai == "") nilai = 0;
		nilai += (formatNumber(subnilai) * <?= $a_unsurnilaiparameter[$t_unsur] ?>);
	}
	<?	} ?>
	
	if(nilai != "") {
		nilai = nilai/100;
		if(nilai > 100)
			nilai = 100;
		else
			nilai = Math.round(nilai);
	}
		
	tr.find("[name='nnumerik[]']").val(nilai);
}

// function hitungNilai(elem) {
	// var tr = setSimpan(elem);
	
	// var subnilai
	// var nilai = "";
	// <?	foreach($a_unsurnilai as $t_unsur) { ?>
	// subnilai = jQuery.trim(tr.find("[name='n_<?= $t_unsur['idunsurnilai'] ?>[]']").val());
	// if(subnilai != "") {
		// if(nilai == "") nilai = 0;
		// nilai += (formatNumber(subnilai) * <?= $t_unsur['prosentasenilai'] ?>);
	// }
	// <?	} ?>
	
	// if(nilai != "") {
		// nilai = nilai/100;
		// if(nilai > 100)
			// nilai = 100;
		// else
			// nilai = Math.round(nilai);
	// }
		
	// tr.find("[name='nnumerik[]']").val(nilai);
// }

function setSimpan(elem) {
	var tr = $(elem).parents("tr:eq(0)");
	
	tr.find("[name='simpan[]']").val(1);
	
	return tr;
}

function goSave(elem) {
	// aktifkan npm
	$(elem).parents("tr:eq(0)").find("[name='simpan[]']").val(1);
	
	document.getElementById("act").value = "save";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goSaveAll() {
	document.getElementById("act").value = "saveall";
	goSubmit();
}

function goClose() {
	var tutup = confirm("Apakah anda yakin akan mengesahkan nilai? Nilai tidak bisa diubah lagi.\nBila anda baru melakukan perubahan nilai, Simpan terlebih dahulu");
	if(tutup) {
		document.getElementById("act").value = "close";
		goSubmit();
	}
}

function goOpen() {
	var buka = confirm("Apakah anda yakin akan membatalkan pengesahan nilai?");
	if(buka) {
		document.getElementById("act").value = "open";
		goSubmit();
	}
}

function goDownXLS() {
	document.getElementById("act").value = "downxls";
	goSubmit();
}

function goUpXLS() {
	var upload = confirm("Apakah anda yakin akan mengupdate data dari format excel?");
	if(upload) {
		document.getElementById("act").value = "upxls";
		goSubmit();
	}
}

function goPrint(xls) {
	var form = document.getElementById("pageform");
	
	form.action = "<?= Route::navAddress('rep_nilai') ?>";
	form.target = "_blank";
	
	if(xls)
		document.getElementById("format").value = "xls";
	else
		document.getElementById("format").value = "html";
	
	goSubmit();
	
	form.action = "";
	form.target = "";
}

function goDeleteParameter(elem,idparam) {
	document.getElementById("act").value = "deleteparameter";
	document.getElementById("idparameter").value = idparam;
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goSaveParameter(elem) {
	document.getElementById("act").value = "saveparameter";
	document.getElementById("subkey").value = elem.id;
	goSubmit();
}

function goNilaiAngka(elem){
	gParam = elem.id;
	submitPage('key','<?= Route::navAddress('set_nilai') ?>');
}

</script>
</body>
</html>
