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
	$p_dbtable = "ak_skdosensmtr";
	$where = 'periodeakreditasi,idpegawai';
	$p_col = 8;
	
	//struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'tahun', 'label' => 'Periode Perhitungan', 'maxlength' => 4, 'size' => 4, 'default' => date('Y'));
	$a_input[] = array('kolom' => 'semester', 'type' => 'S', 'option' => $p_model::PeriodeSemester());
	$a_input[] = array('kolom' => 'tglsimulasi', 'label' => 'Tgl. Simulasi', 'type' => 'D', 'notnull' => true, 'default' => date('Y-m-d'));
	$a_input[] = array('kolom' => 'tglvalidasi', 'label' => 'Tgl. Validasi', 'type' => 'D', 'readonly' => true);
	$a_input[] = array('kolom' => 'isvalid', 'label' => 'Valid', 'type' => 'C', 'option' => array('Y' => ''), 'readonly' => true);
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();
		
		if(!empty($_POST['semester']) and !empty($_POST['tahun']))
			$record['periodeakreditasi'] = $_POST['tahun'].str_pad($_POST['semester'],2,'0', STR_PAD_LEFT);
		
		//update pendidikan
		for($i=0;$i<count($_POST['kode1a']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode1a'][$i]);
			$is = CStr::cAlphaNum($_POST['check1a'.$r_ida]);
			if(!empty($is)){
				$record['statusvalidasi'] = 'A';					
				$record['nilaikreditman'] = $record['nilaikredit'] = !empty($_POST['nilaikredit1a'.$r_ida]) ? $_POST['nilaikredit1a'.$r_ida] : $_POST['stdkredit1a'.$r_ida];
				$jmln1a += $record['nilaikredit'];
			}else{
				$record['statusvalidasi'] = 'null';
				$record['nilaikreditman'] = 'null';
				$jmln1a += 0;
			}
		
			$p_posterr = $p_model::updateRecord($conn,$record,$r_ida,$status=false,'ak_bidang1a','nobidangia');
			if($p_posterr)
				break;
		}
		
		//update pengajaran
		for($i=0;$i<count($_POST['kode1b']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode1b'][$i]);
			$is = CStr::cAlphaNum($_POST['check1b'.$r_ida]);
			if(!empty($is)){
				$record['statusvalidasi'] = 'A';
				$record['nilaikreditman'] = $record['nilaikredit'] = !empty($_POST['nilaikredit1b'.$r_ida]) ? $_POST['nilaikredit1b'.$r_ida] : $_POST['stdkredit1b'.$r_ida];
				$jmln1b += $record['nilaikredit'];
			}else{
				$record['statusvalidasi'] = 'null';
				$record['nilaikreditman'] = 'null';
				$jmln1b += 0;
			}
		
			$p_posterr = $p_model::updateRecord($conn,$record,$r_ida,$status=false,'ak_bidang1b','nobidangib');
			if($p_posterr)
				break;
		}
	
		//update penelitian
		for($i=0;$i<count($_POST['kode2']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode2'][$i]);
			$is = CStr::cAlphaNum($_POST['check2'.$r_ida]);
			if(!empty($is)){
				$record['statusvalidasi'] = 'A';
				$record['nilaikreditman'] = $record['nilaikredit'] = !empty($_POST['nilaikredit2'.$r_ida]) ? $_POST['nilaikredit2'.$r_ida] : $_POST['stdkredit2'.$r_ida];
				$jmln2 += $record['nilaikredit'];
			}else{
				$record['statusvalidasi'] = 'null';
				$record['nilaikreditman'] = 'null';
				$jmln2 += 0;
			}
		
			$p_posterr = $p_model::updateRecord($conn,$record,$r_ida,$status=false,'ak_bidang2','nobidangii');
			if($p_posterr)
				break;
		}
	
		//update pengabdian kepada masyarakat
		for($i=0;$i<count($_POST['kode3']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode3'][$i]);
			$is = CStr::cAlphaNum($_POST['check3'.$r_ida]);
			if(!empty($is)){
				$record['statusvalidasi'] = 'A';
				$record['nilaikreditman'] = $record['nilaikredit'] = !empty($_POST['nilaikredit3'.$r_ida]) ? $_POST['nilaikredit3'.$r_ida] : $_POST['stdkredit3'.$r_ida];
				$jmln3 += $record['nilaikredit'];
			}else{
				$record['statusvalidasi'] = 'null';
				$record['nilaikreditman'] = 'null';
				$jmln3 += 0;
			}
		
			$p_posterr = $p_model::updateRecord($conn,$record,$r_ida,$status=false,'ak_bidang3','nobidangiii');
			if($p_posterr)
				break;
		}
	
		//update kegiatan penunjang
		for($i=0;$i<count($_POST['kode4']);$i++){
			$r_ida = CStr::cAlphaNum($_POST['kode4'][$i]);
			$is = CStr::cAlphaNum($_POST['check4'.$r_ida]);
			if(!empty($is)){
				$record['statusvalidasi'] = 'A';
				$record['nilaikreditman'] = $record['nilaikredit'] = !empty($_POST['nilaikredit4'.$r_ida]) ? $_POST['nilaikredit4'.$r_ida] : $_POST['stdkredit4'.$r_ida];
				$jmln4 += $record['nilaikredit'];
			}else{
				$record['statusvalidasi'] = 'null';
				$record['nilaikreditman'] = 'null';
				$jmln4 += 0;
			}
		
			$p_posterr = $p_model::updateRecord($conn,$record,$r_ida,$status=false,'ak_bidang4','nobidangiv');
			if($p_posterr)
				break;
		}
	
		$jml = $jmln1a + $jmln1b + $jmln2 + $jmln3 + $jmln4;
		
		//simpan ke ak_dosen
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		$record['idpegawai'] = $r_key;
		unset($record['isvalid']);

		if($record['semester'] != 'null' and $record['tahun'] != 'null')
			$record['periodeakreditasi'] = $record['tahun'].str_pad($record['semester'],2,'0', STR_PAD_LEFT);
		
		if(!$p_posterr){
			if(empty($r_subkey))
				list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
			else
				list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_subkey,$p_dbtable,$where);
		}

		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			unset($post);
		}else
			$conn->RollbackTrans();
	}
	else if($r_act == 'delete' and $c_delete) {
		$conn->BeginTrans();
		
		$p_posterr = $p_model::updateRWTAkreditasi($conn,$r_subkey);
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_subkey,$p_dbtable,$where);
			
		if(!$p_posterr){
			$ok = Query::isOK($p_posterr);
			$conn->CommitTrans($ok);
			
			Route::navListpage($p_listpage,$r_key);
		}else
			$conn->RollbackTrans();
	}
	
	$sql = $p_model::getDataEditSimulasiAKSMTR($r_subkey);
	$row = $p_model::getDataEdit($conn,$a_input,$r_subkey,$post,$p_dbtable,$where,$sql);
	
	//utk not null
	$a_required = array();
	foreach($row as $t_row) {
		if($t_row['notnull'])
			$a_required[] = $t_row['id'];
		if($t_row['id'] == 'isvalid')
			$isvalid = $t_row['realvalue'];
	}
	
	if($isvalid == 'Y' and $r_self == '1'){
		$c_edit = false;
		$c_delete = false;
	}	
	
	//pengecekan utk validasi
	$c_akreditasi = false;
	$c_validasi = false;
	if(Modul::getRole() == 'A' or Modul::getRole() == 'admhrm'){
		$c_akreditasi = true;
		$c_validasi = true;
	}
	
	$rowstyle = array( '0' => 'NormalBG', '1' => 'AlternateBG');
	$crow = 0;
		
	/*=== list beberapa bidang ===*/
	if(!empty($r_subkey))
		list($periode,$idpegawai) = explode('|', $r_subkey);
	
	//bidang pendidikan
	$a_data1a = $p_model::getBidangIA($conn,$r_key,$periode);
	if(count($a_data1a) > 0){
		foreach($a_data1a as $key => $val){
			foreach($val as $keys => $row1a){
				$rs1a[$row1a['nobidangia']] = $row1a;
				$col1a[$row1a['kodeparent']] += 1;
			}
		}
	}
	
	//bidang pengajar
	$a_data1b = $p_model::getBidangIB($conn,$r_key,$periode);
	if(count($a_data1b) > 0){
		foreach($a_data1b as $key => $val){
			foreach($val as $keys => $row1b){
				$rs1b[$row1b['nobidangib']] = $row1b;
				$col1b[$row1b['kodeparent']] += 1;
			}
		}
	}
	
	//bidang penelitian
	$a_data2 = $p_model::getBidangII($conn,$r_key,$periode);
	if(count($a_data2) > 0){
		foreach($a_data2 as $key => $val){
			foreach($val as $keys => $row2){
				$rs2[$row2['nobidangii']] = $row2;
				$col2[$row2['kodeparent']] += 1;
			}
		}
	}
	
	//bidang pengabdian
	$a_data3 = $p_model::getBidangIII($conn,$r_key,$periode);
	if(count($a_data3) > 0){
		foreach($a_data3 as $key => $val){
			foreach($val as $keys => $row3){
				$rs3[$row3['nobidangiii']] = $row3;
				$col3[$row3['kodeparent']] += 1;
			}
		}
	}
	
	//bidang penunjang
	$a_data4 = $p_model::getBidangIV($conn,$r_key,$periode);
	if(count($a_data4) > 0){
		foreach($a_data4 as $key => $val){
			foreach($val as $keys => $row4){
				$rs4[$row4['nobidangiv']] = $row4;
				$col4[$row4['kodeparent']] += 1;
			}
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
								<?	} if($c_delete and !empty($r_subkey)) { ?>
								<td id="be_delete" class="TDButton" onclick="goDelete()">
									<img src="images/delete.png"> Hapus
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
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'tglsimulasi') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglsimulasi') ?></td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="20%"><?= Page::getDataLabel($row,'isvalid') ?></td>
							<td  class="RightColumnBG"><?= Page::getDataInput($row,'isvalid') ?></td>
							<td class="LeftColumnBG" style="white-space:nowrap"><?= Page::getDataLabel($row,'tglvalidasi') ?></td>
							<td  class="RightColumnBG" width="20%"><?= Page::getDataInput($row,'tglvalidasi') ?></td>
						</tr>
					</table>
					</div>
				</center>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					
					<?// ======= Bidang Pendidikan =========?>
					<tr height="30">
						<td colspan="<?= $p_col?>" class="DataBG">Bidang IA : Pendidikan</td>
					</tr>				
					<tr height="30">
						<th width="50">No.</th>
						<th>Nama Kegiatan</th>
						<th colspan="3">Indeks Angka Kredit</th>
						<th width="100">Kredit Max</th>
						<th width="100">Nilai Kredit</th>
						<th width="30">
							<span id="edit" style="display:none">
								<input type="checkbox" id="checkall1a" title="Ajukan Bidang 1A" onClick="toggle1a(this)">
							</span>
						</th>
					</tr>
					<?
						$i=0;$no=0;
						if(count($rs1a) > 0){
							foreach($rs1a as $row1a){
								if(empty($row1a['statusvalidasi']))
									$hitung = false;
								else
									$hitung = true;						
								$crow++;
								$noid = $row1a['nobidangia'];
					?>	
					<? if($cekr != $row1a['kodeparent']){$no++;?>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td align="center" valign="top" rowspan="<?= $col1a[$row1a['kodeparent']]+1?>"><?= $no;?></td>
						<td height="30px" colspan="<?= $p_col-1?>"><?= $row1a['kodeparent'].' - '.$row1a['namaparent']?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<? }else{?>						
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<?}?>
						<td><?= $row1a['namakegiatan'];?></td>
						<td colspan="3"><?= $row1a['indeks'].' - '.$row1a['namaindeks']?></td>
						<td align="center"><?= number_format($row1a['kreditmax'],2)?></td>
						<td align="right" style="padding-right:30px">
							<span id="show">
								<?= !empty($row1a['nilaikredit']) ? number_format($row1a['nilaikredit'],2) : '';?>
							</span>
							<span id="edit" style="display:none">
								<?= UI::createTextBox('nilaikredit1a'.$noid,number_format($row1a['nilaikredit'],2),'ControlStyle',6,5,$c_akreditasi,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"');?>
							</span>
						</td>
						<td align="center">
						<?
						if(($row1a['statusvalidasi'] == 'Y') and !$c_validasi){
							echo '<img src="images/green.gif" title="Diterima">';
						?>
							<input type="hidden" name="check1a<?= $noid ?>" value="<?= $row1a['statusvalidasi'] ?>">
						<?}else{?>						
							<span id="show">
								<?= !empty($row1a['statusvalidasi']) ? '<img src="images/check.png" title="Sudah diajukan">' : '';?>
							</span>
							<span id="edit" style="display:none">
								<input type="checkbox" id="check1a" name="check1a<?= $noid ?>" <?= !empty($row1a['statusvalidasi']) ? 'checked' : ''?>>
							</span>
						<?}?>
							<input type="hidden" name="kode1a[]" value="<?= $noid ?>">
							<input type="hidden" name="stdkredit1a<?= $noid ?>" value="<?= $row1a['stdkredit'] ?>">
						</td>
					</tr>
					<?
								$cekr = $row1a['kodeparent'];
								
								if(empty($r_act)){
									$jmln1a += $hitung == true ? ($row1a['nilaikredit']!='' ? $row1a['nilaikredit'] : 0) : 0;
									$jml += $hitung == true ? ($row1a['nilaikredit']!='' ? $row1a['nilaikredit'] : $row1a['stdkredit']) : 0;
								}
							}
						}else{
					?>
					<tr>
						<td colspan="<?= $p_col ?>" align="center">Data kosong</td>
					</tr>
					<?}?>
					<tr style="font-weight:bold">
						<td colspan="<?= $p_col-2;?>">Nilai Bidang 1A</td>
						<td align="right" style="padding-right:30px"><?= number_format((empty($jmln1a) ? '0' : $jmln1a),2)?></td>
						<td>&nbsp;</td>
					</tr>
					
					<?// ======= Bidang Pengajaran =========?>
					<tr height="30">
						<td colspan="<?= $p_col?>" class="DataBG">Bidang IB : Pengajaran</td>
					</tr>				
					<tr height="30">
						<th width="50">No.</th>
						<th>Nama Kegiatan</th>
						<th>Indeks Angka Kredit</th>
						<th width="100">Periode</th>
						<th width="100">SKS</th>
						<th width="100">Kredit Max</th>
						<th width="100">Nilai Kredit</th>
						<th width="30">
							<span id="edit" style="display:none">
								<input type="checkbox" id="checkall1b" title="Ajukan Bidang 1B" onClick="toggle1b(this)">
							</span>
						</th>
					</tr>
					<?
						$i=0;$no=0;
						if(count($rs1b) > 0){
							foreach($rs1b as $row1b){
								if(empty($row1b['statusvalidasi']))
									$hitung = false;
								else
									$hitung = true;						
								$crow++;
								$noid = $row1b['nobidangib'];
					?>	
					<? if($cekr != $row1b['kodeparent']){$no++;?>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td align="center" valign="top" rowspan="<?= $col1b[$row1b['kodeparent']]+1?>"><?= $no;?></td>
						<td height="30px" colspan="<?= $p_col-1?>"><?= $row1b['kodeparent'].' - '.$row1b['namaparent']?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<? }else{?>						
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<?}?>
						<td><?= $row1b['namakegiatan'];?></td>
						<td><?= $row1b['indeks'].' - '.$row1b['namaindeks']?></td>
						<td align="center"><?= $row1b['periodekuliah']?></td>
						<td align="center"><?= $row1b['sksdiakui']?></td>
						<td align="center"><?= number_format($row1b['kreditmax'],2)?></td>
						<td align="right" style="padding-right:30px">
							<span id="show">
								<?= !empty($row1b['nilaikredit']) ? number_format($row1b['nilaikredit'],2) : '';?>
							</span>
							<span id="edit" style="display:none">
								<?= UI::createTextBox('nilaikredit1b'.$noid,number_format($row1b['nilaikredit'],2),'ControlStyle',6,5,$c_akreditasi,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"');?>
							</span>
						</td>
						<td align="center">
						<?
						if(($row1b['statusvalidasi'] == 'Y') and !$c_validasi){
							echo '<img src="images/green.gif" title="Diterima">';
						?>
							<input type="hidden" name="check1b<?= $noid ?>" value="<?= $row1b['statusvalidasi'] ?>">
						<?}else{?>					
							<span id="show">
								<?= !empty($row1b['statusvalidasi']) ? '<img src="images/check.png" title="Sudah diajukan">' : '';?>
							</span>
							<span id="edit" style="display:none">
								<input type="checkbox" id="check1b" name="check1b<?= $noid ?>" <?= !empty($row1b['statusvalidasi']) ? 'checked' : ''?>>
							</span>
						<?}?>
							<input type="hidden" name="kode1b[]" value="<?= $noid ?>">
							<input type="hidden" name="stdkredit1b<?= $noid ?>" value="<?= $row1b['stdkredit'] ?>">
						</td>
					</tr>
					<?
								$cekr = $row1b['kodeparent'];
								
								if(empty($r_act)){
									$jmln1b += $hitung == true ? ($row1b['nilaikredit']!='' ? $row1b['nilaikredit'] : 0) : 0;
									$jml += $hitung == true ? ($row1b['nilaikredit']!='' ? $row1b['nilaikredit'] : $row1b['stdkredit']) : 0;
								}
							}
						}else{
					?>
					<tr>
						<td colspan="<?= $p_col ?>" align="center">Data kosong</td>
					</tr>
					<?}?>
					<tr style="font-weight:bold">
						<td colspan="<?= $p_col-2;?>">Nilai Bidang IB</td>
						<td align="right" style="padding-right:30px"><?= number_format((empty($jmln1b) ? '0' : $jmln1b),2)?></td>
						<td>&nbsp;</td>
					</tr>
					
					<?// ======= Bidang Penelitian =========?>
					<tr height="30">
						<td colspan="<?= $p_col?>" class="DataBG">Bidang II : Penelitian</td>
					</tr>				
					<tr height="30">
						<th width="50">No.</th>
						<th>Nama Kegiatan</th>
						<th colspan="2">Indeks Angka Kredit</th>
						<th width="100">Periode</th>
						<th width="100">Kredit Max</th>
						<th width="100">Nilai Kredit</th>
						<th width="30">
							<span id="edit" style="display:none">
								<input type="checkbox" id="checkall2" title="Ajukan Bidang II" onClick="toggle2(this)">
							</span>
						</th>
					</tr>
					<?
						$i=0;$no=0;
						if(count($rs2) > 0){
							foreach($rs2 as $row2){
								if(empty($row2['statusvalidasi']))
									$hitung = false;
								else
									$hitung = true;						
								$crow++;
								$noid = $row2['nobidangii'];
					?>	
					<? if($cekr != $row2['kodeparent']){$no++;?>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td align="center" valign="top" rowspan="<?= $col2[$row2['kodeparent']]+1?>"><?= $no;?></td>
						<td height="30px" colspan="<?= $p_col-1?>"><?= $row2['kodeparent'].' - '.$row2['namaparent']?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<? }else{?>						
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<?}?>
						<td><?= $row2['namakegiatan'];?></td>
						<td colspan="2"><?= $row2['indeks'].' - '.$row2['namaindeks']?></td>
						<td align="center"><?= $row2['periode'];?></td>
						<td align="center"><?= number_format($row2['kreditmax'],2)?></td>
						<td align="right" style="padding-right:30px">
							<span id="show">
								<?= !empty($row2['nilaikredit']) ? number_format($row2['nilaikredit'],2) : '';?>
							</span>
							<span id="edit" style="display:none">
								<?= UI::createTextBox('nilaikredit2'.$noid,number_format($row2['nilaikredit'],2),'ControlStyle',6,5,$c_akreditasi,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"');?>
							</span>
						</td>
						<td align="center">
						<?
						if(($row2['statusvalidasi'] == 'Y') and !$c_validasi){
							echo '<img src="images/green.gif" title="Diterima">';
						?>
							<input type="hidden" name="check2<?= $noid ?>" value="<?= $row2['statusvalidasi'] ?>">
						<?}else{?>					
							<span id="show">
								<?= !empty($row2['statusvalidasi']) ? '<img src="images/check.png" title="Sudah diajukan">' : '';?>
							</span>
							<span id="edit" style="display:none">
								<input type="checkbox" id="check2" name="check2<?= $noid ?>" <?= !empty($row2['statusvalidasi']) ? 'checked' : ''?>>
							</span>
						<?}?>
							<input type="hidden" name="kode2[]" value="<?= $noid ?>">
							<input type="hidden" name="stdkredit2<?= $noid ?>" value="<?= $row2['stdkredit'] ?>">
						</td>
					</tr>
					<?
								$cekr = $row2['kodeparent'];
								
								if(empty($r_act)){
									$jmln2 += $hitung == true ? ($row2['nilaikredit']!='' ? $row2['nilaikredit'] : 0) : 0;
									$jml += $hitung == true ? ($row2['nilaikredit']!='' ? $row2['nilaikredit'] : $row2['stdkredit']) : 0;
								}
							}
						}else{
					?>
					<tr>
						<td colspan="<?= $p_col ?>" align="center">Data kosong</td>
					</tr>
					<?}?>
					<tr style="font-weight:bold">
						<td colspan="<?= $p_col-2;?>">Nilai Bidang II</td>
						<td align="right" style="padding-right:30px"><?= number_format((empty($jmln2) ? '0' : $jmln2),2)?></td>
						<td>&nbsp;</td>
					</tr>
					
					<?// ======= Bidang Pengabdian Masyarakat =========?>
					<tr height="30">
						<td colspan="<?= $p_col?>" class="DataBG">Bidang III : Pengabdian Masyarakat</td>
					</tr>				
					<tr height="30">
						<th width="50">No.</th>
						<th>Nama Kegiatan</th>
						<th colspan="2">Indeks Angka Kredit</th>
						<th width="100">Tgl. Kegiatan</th>
						<th width="100">Kredit Max</th>
						<th width="100">Nilai Kredit</th>
						<th width="30">
							<span id="edit" style="display:none">
								<input type="checkbox" id="checkall3" title="Ajukan Bidang III" onClick="toggle3(this)">
							</span>
						</th>
					</tr>
					<?
						$i=0;$no=0;
						if(count($rs3) > 0){
							foreach($rs3 as $row3){
								if(empty($row3['statusvalidasi']))
									$hitung = false;
								else
									$hitung = true;						
								$crow++;
								$noid = $row3['nobidangiii'];
					?>	
					<? if($cekr != $row3['kodeparent']){$no++;?>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td align="center" valign="top" rowspan="<?= $col3[$row3['kodeparent']]+1?>"><?= $no;?></td>
						<td height="30px" colspan="<?= $p_col-1?>"><?= $row3['kodeparent'].' - '.$row3['namaparent']?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<? }else{?>						
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<?}?>
						<td><?= $row3['namakegiatan'];?></td>
						<td colspan="2"><?= $row3['indeks'].' - '.$row3['namaindeks']?></td>
						<td align="center"><?= CStr::formatDateInd($row3['tgl'],2)?></td>
						<td align="center"><?= number_format($row3['kreditmax'],2)?></td>
						<td align="right" style="padding-right:30px">
							<span id="show">
								<?= !empty($row3['nilaikredit']) ? number_format($row3['nilaikredit'],2) : '';?>
							</span>
							<span id="edit" style="display:none">
								<?= UI::createTextBox('nilaikredit3'.$noid,number_format($row3['nilaikredit'],2),'ControlStyle',6,5,$c_akreditasi,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"');?>
							</span>
						</td>
						<td align="center">
						<?
						if(($row3['statusvalidasi'] == 'Y') and !$c_validasi){
							echo '<img src="images/green.gif" title="Diterima">';
						?>
							<input type="hidden" name="check3<?= $noid ?>" value="<?= $row3['statusvalidasi'] ?>">
						<?}else{?>					
							<span id="show">
								<?= !empty($row3['statusvalidasi']) ? '<img src="images/check.png" title="Sudah diajukan">' : '';?>
							</span>
							<span id="edit" style="display:none">
								<input type="checkbox" id="check3" name="check3<?= $noid ?>" <?= !empty($row3['statusvalidasi']) ? 'checked' : ''?>>
							</span>
						<?}?>
							<input type="hidden" name="kode3[]" value="<?= $noid ?>">
							<input type="hidden" name="stdkredit3<?= $noid ?>" value="<?= $row3['stdkredit'] ?>">
						</td>
					</tr>
					<?
								$cekr = $row3['kodeparent'];
								
								if(empty($r_act)){
									$jmln3 += $hitung == true ? ($row3['nilaikredit']!='' ? $row3['nilaikredit'] : 0) : 0;
									$jml += $hitung == true ? ($row3['nilaikredit']!='' ? $row3['nilaikredit'] : $row3['stdkredit']) : 0;
								}
							}
						}else{
					?>
					<tr>
						<td colspan="<?= $p_col ?>" align="center">Data kosong</td>
					</tr>
					<?}?>
					<tr style="font-weight:bold">
						<td colspan="<?= $p_col-2;?>">Nilai Bidang III</td>
						<td align="right" style="padding-right:30px"><?= number_format((empty($jmln3) ? '0' : $jmln3),2)?></td>
						<td>&nbsp;</td>
					</tr>
					
					<?// ======= Bidang Penunjang =========?>
					<tr height="30">
						<td colspan="<?= $p_col?>" class="DataBG">Bidang IV : Penunjang</td>
					</tr>				
					<tr height="30">
						<th width="50">No.</th>
						<th>Nama Kegiatan</th>
						<th colspan="2">Indeks Angka Kredit</th>
						<th width="100">Tgl. Kegiatan</th>
						<th width="100">Kredit Max</th>
						<th width="100">Nilai Kredit</th>
						<th width="30">
							<span id="edit" style="display:none">
								<input type="checkbox" id="checkall4" title="Ajukan Bidang IV" onClick="toggle4(this)">
							</span>
						</th>
					</tr>
					<?
						$i=0;$no=0;
						if(count($rs4) > 0){
							foreach($rs4 as $row4){
								if(empty($row4['statusvalidasi']))
									$hitung = false;
								else
									$hitung = true;						
								$crow++;
								$noid = $row4['nobidangiv'];
					?>	
					<? if($cekr != $row4['kodeparent']){$no++;?>
					<tr class="<?= $rowstyle[$i++%2] ?>">
						<td align="center" valign="top" rowspan="<?= $col4[$row4['kodeparent']]+1?>"><?= $no;?></td>
						<td height="30px" colspan="<?= $p_col-1?>"><?= $row4['kodeparent'].' - '.$row4['namaparent']?></td>
					</tr>
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<? }else{?>						
					<tr class="<?= $rowstyle[$i++%2] ?>">
					<?}?>
						<td><?= $row4['namakegiatan'];?></td>
						<td colspan="2"><?= $row4['indeks'].' - '.$row4['namaindeks']?></td>
						<td align="center"><?= CStr::formatDateInd($row4['tglmulai'],2)?></td>
						<td align="center"><?= number_format($row4['kreditmax'],2)?></td>
						<td align="right" style="padding-right:30px">
							<span id="show">
								<?= !empty($row4['nilaikredit']) ? number_format($row4['nilaikredit'],2) : '';?>
							</span>
							<span id="edit" style="display:none">
								<?= UI::createTextBox('nilaikredit4'.$noid,number_format($row4['nilaikredit'],2),'ControlStyle',6,5,$c_akreditasi,'style="text-align:right;" onkeydown="return onlyNumber(event,this,true,true);"');?>
							</span>
						</td>
						<td align="center">
						<?
						if(($row4['statusvalidasi'] == 'Y') and !$c_validasi){
							echo '<img src="images/green.gif" title="Diterima">';
						?>
							<input type="hidden" name="check4<?= $noid ?>" value="<?= $row4['statusvalidasi'] ?>">
						<?}else{?>					
							<span id="show">
								<?= !empty($row4['statusvalidasi']) ? '<img src="images/check.png" title="Sudah diajukan">' : '';?>
							</span>
							<span id="edit" style="display:none">
								<input type="checkbox" id="check4" name="check4<?= $noid ?>" <?= !empty($row4['statusvalidasi']) ? 'checked' : ''?>>
							</span>
						<?}?>
							<input type="hidden" name="kode4[]" value="<?= $noid ?>">
							<input type="hidden" name="stdkredit4<?= $noid ?>" value="<?= $row4['stdkredit'] ?>">
						</td>
					</tr>
					<?
								$cekr = $row4['kodeparent'];
								
								if(empty($r_act)){
									$jmln4 += $hitung == true ? ($row4['nilaikredit']!='' ? $row4['nilaikredit'] : 0) : 0;
									$jml += $hitung == true ? ($row4['nilaikredit']!='' ? $row4['nilaikredit'] : $row4['stdkredit']) : 0;
								}
							}
						}else{
					?>
					<tr>
						<td colspan="<?= $p_col ?>" align="center">Data kosong</td>
					</tr>
					<?}?>
					<tr style="font-weight:bold">
						<td colspan="<?= $p_col-2;?>">Nilai Bidang IV</td>
						<td align="right" style="padding-right:30px"><?= number_format((empty($jmln4) ? '0' : $jmln4),2)?></td>
						<td>&nbsp;</td>
					</tr>		
					<tr style="font-weight:bold;" height="30px">
						<td class="FootBG" colspan="<?= $p_col-2;?>">Perolehan Nilai</td>
						<td class="FootBG" align="right" style="padding-right:30px"><?= number_format((empty($jml) ? '0' : $jml),2)?></td>
						<td class="FootBG">&nbsp;</td>
					</tr>
				</table>
				
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="subkey" id="subkey" value="<?= $r_subkey; ?>">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$r_scroll ?>">
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
		
	if(<?= $crow?> == 0){
		alert("Silahkan isi terlebih dahulu masing-masing bidang Angka Kredit");
		goList();
	}
	
	if('<?= $r_subkey?>' == ''){
		$("[id='check1a']").attr("checked",true);
		$("[id='check1b']").attr("checked",true);
		$("[id='check2']").attr("checked",true);
		$("[id='check3']").attr("checked",true);
		$("[id='check4']").attr("checked",true);
	}
});

function toggle1a(elem) {
	var check = elem.checked;
	
	$("[id='check1a']").attr("checked",check);
}

function toggle1b(elem) {
	var check = elem.checked;
	
	$("[id='check1b']").attr("checked",check);
}

function toggle2(elem) {
	var check = elem.checked;
	
	$("[id='check2']").attr("checked",check);
}

function toggle3(elem) {
	var check = elem.checked;
	
	$("[id='check3']").attr("checked",check);
}

function toggle4(elem) {
	var check = elem.checked;
	
	$("[id='check4']").attr("checked",check);
}
</script>
</body>
</html>
