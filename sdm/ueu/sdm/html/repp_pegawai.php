<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	// include
	require_once(Route::getModelPath('pegawai'));	
	require_once(Route::getModelPath('riwayat'));	
	require_once(Route::getModelPath('gaji'));	
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// properti halaman
	$p_title = 'Laporan Daftar Pegawai';
	$p_tbwidth = 950;
	$p_aktivitas = 'LAPORAN';
	
	$p_model = mPegawai;	
	
	$role = Modul::getRole();
	$a_kolom = $p_model::kolom($role);
	$a_urut = $p_model::jenisUrutan();
	$a_kriteria = $p_model::kriteria();
	
	$lk_kolom = UI::createSelect('k_kolom',$a_kolom,'','ControlStyle');
	$lu_kolom = UI::createSelect('u_kolom',$a_kolom,'','ControlStyle');
	$lu_urut = UI::createSelect('u_urut',$a_urut,'','ControlStyle');
	$l_kriteria = UI::createSelect('s_kriteria',$a_kriteria,'','ControlStyle',true,'onchange="gantiKriteria()"');
	
	$a_pendidikan = mRiwayat::jenjangPendidikan($conn);
	if(count($a_pendidikan)>0){
		foreach($a_pendidikan as $key => $val){
			$a_kparam['pendidikan'][$key] = $val;
		}
	}	
	$l_pendidikan = UI::createSelect('s_pendidikan',$a_pendidikan,'','ControlStyle',true,'multiple size="10"');
	
	$a_tipepegawaibaru = mCombo::tipepegawaibaru($conn);
	if(count($a_tipepegawaibaru)>0){
		foreach($a_tipepegawaibaru as $key => $val){
			$a_kparam['tipepegawaibaru'][$key] = $val;
		}
	}
	$l_tipepegawaibaru = UI::createSelect('s_tipepegawaibaru',$a_tipepegawaibaru,'','ControlStyle',true,'multiple size="3"');
		
	$a_tipepegawai = mCombo::tipepegawai($conn);
	if(count($a_tipepegawai)>0){
		foreach($a_tipepegawai as $key => $val){
			$a_kparam['tipepegawai'][$key] = $val;
		}
	}
	$l_tipepegawai = UI::createSelect('s_tipepegawai',$a_tipepegawai,'','ControlStyle',true,'multiple size="3"');
	
	$a_jenispegawaibaru = mCombo::jenispegawaibaru($conn);
	if(count($a_jenispegawaibaru)>0){
		foreach($a_jenispegawaibaru as $key => $val){
			$a_kparam['jenispegawaibaru'][$key] = $val;
		}
	}
	$l_jenispegawaibaru = UI::createSelect('s_jenispegawaibaru',$a_jenispegawaibaru,'','ControlStyle',true,'multiple size="8"');
	
	$a_kelompokpeg = mCombo::kelompokpeg($conn);
	if(count($a_kelompokpeg)>0){
		foreach($a_kelompokpeg as $key => $val){
			$a_kparam['kelompokpeg'][$key] = $val;
		}
	}
	$l_kelompokpeg = UI::createSelect('s_kelompokpeg',$a_kelompokpeg,'','ControlStyle',true,'multiple size="8"');

	$a_jenispegawai = $p_model::jenispegawai($conn);
	if(count($a_jenispegawai)>0){
		foreach($a_jenispegawai as $key => $val){
			$a_kparam['jenispegawai'][$key] = $val;
		}
	}
	$l_jenispegawai = UI::createSelect('s_jenispegawai',$a_jenispegawai,'','ControlStyle',true,'multiple size="8"');

	$a_hubungankerja = mCombo::hubungankerja($conn);
	if(count($a_hubungankerja)>0){
		foreach($a_hubungankerja as $key => $val){
			$a_kparam['hubungankerja'][$key] = $val;
		}
	}
	$l_hubungankerja = UI::createSelect('s_hubungankerja',$a_hubungankerja,'','ControlStyle',true,'multiple size="8"');
	
	$a_statusaktif = mCombo::statusaktif($conn);
	if(count($a_statusaktif)>0){
		foreach($a_statusaktif as $key => $val){
			$a_kparam['statusaktif'][$key] = $val;
		}
	}
	$l_statusaktif = UI::createSelect('s_statusaktif',$a_statusaktif,'','ControlStyle',true,'multiple size="8"');
	
	$a_statusaktifhb = $p_model::statusAktifHomebase($conn);
	if(count($a_statusaktifhb)>0){
		foreach($a_statusaktifhb as $key => $val){
			$a_kparam['statusaktifhb'][$key] = $val;
		}
	}
	$l_statusaktifhb = UI::createSelect('s_statusaktifhb',$a_statusaktifhb,'','ControlStyle',true,'multiple size="8"');
	
	$a_golongan = mRiwayat::namaPangkat($conn);
	if(count($a_golongan)>0){
		foreach($a_golongan as $key => $val){
			$a_kparam['golongan'][$key] = $val;
		}
	}
	$l_golongan = UI::createSelect('s_golongan',$a_golongan,'','ControlStyle',true,'multiple size="10"');
	
	$a_fungsional = mRiwayat::jabatanFungsional($conn);
	if(count($a_fungsional)>0){
		foreach($a_fungsional as $key => $val){
			$a_kparam['fungsional'][$key] = $val;
		}
	}
	$l_fungsional = UI::createSelect('s_fungsional',$a_fungsional,'','ControlStyle',true,'multiple size="10"');
	
	$l_noserdos = UI::createSelect('s_noserdos',array('Y' => 'Kosong', 'N' => 'Tidak Kosong'),'','ControlStyle',true,'multiple size="3"');
	$l_sesuaibidang = UI::createSelect('s_sesuaibidang',array('Y' => 'Sesuai Bidang', 'N' => 'Tidak Sesuai Bidang'),'','ControlStyle',true,'multiple size="3"');

	$a_periodegaji = mGaji::getCPeriodeGaji($conn);
	if(count($a_periodegaji)>0){
		foreach($a_periodegaji as $key => $val){
			$a_kparam['periodegaji'][$key] = $val;
		}
	}
	$l_periodegaji = UI::createSelect('s_periodegaji',$a_periodegaji,'','ControlStyle',true);
	
	$a_unitkerja = mCombo::unitSave($conn,false);
	if(count($a_unitkerja)>0){
		foreach($a_unitkerja as $key => $val){
			$a_kparam['unitkerja'][$key] = $val;
		}
	}
	$l_unitkerja = UI::createSelect('s_unitkerja',$a_unitkerja,'','ControlStyle',true,'multiple size="10"');
		
	$a_unithomebase = mCombo::unitSave($conn,false,true);
	if(count($a_unithomebase)>0){
		foreach($a_unithomebase as $key => $val){
			$a_kparam['unithomebase'][$key] = $val;
		}
	}
	$l_unithomebase = UI::createSelect('s_unithomebase',$a_unithomebase,'','ControlStyle',true,'multiple size="10"');
		
	if(!empty($_POST)) {
		$r_template = CStr::removeSpecial($_POST['template']);
		$rowt = $p_model::getTemplate($conn,$r_template);
		$namatemplate = $rowt['namatemplate'];
		
		// masih menampilkan data ketika disubmit
		if(!empty($_POST['kolom']))
			$al_kolom = $_POST['kolom'];
		if(!empty($_POST['urutan']))
			$al_urut = $_POST['urutan'];
		
		if(!empty($_POST['kriteria'])) {
			$al_kriteria = array();
			$n = count($_POST['kriteria']);
			for($i=0;$i<$n;$i++)
				$al_kriteria[] = $_POST['kriteria'][$i].'|'.$_POST['paramkriteria'][$i];
		}
		
		// ada aksi
		$r_act = $_POST['act'];
		if($r_act == 'simpantemplat') {
			$record = array();
			$record['namatemplate'] = CStr::CStrNull($_POST['namatemplate']);
			
			if(!empty($_POST['kolom']))
				$record['listkolom'] = CStr::CStrNull(implode(',',$_POST['kolom']));
			if(!empty($_POST['urutan']))
				$record['listurutan'] = CStr::CStrNull(implode(',',$_POST['urutan']));
			
			if(!empty($_POST['kriteria'])) {
				$a_listkriteria = array();
				$n = count($_POST['kriteria']);
				for($i=0;$i<$n;$i++)
					$a_listkriteria[] = $_POST['kriteria'][$i].'|'.$_POST['paramkriteria'][$i];
				$record['listkriteria'] = CStr::CStrNull(implode(',',$a_listkriteria));
			}
			
			if($namatemplate != $record['namatemplate'])
				$err = $p_model::insertRecord($conn,$record,$status=false,'pe_templatereport');
			else
				$err = $p_model::updateRecord($conn,$record,$r_template,$status=false,'pe_templatereport','idtemplate');
			
			if(!$err and $namatemplate != $record['namatemplate']){
				$r_template = $p_model::getLastValue($conn);
				$namatemplate = $record['namatemplate'];
			}
			
			if($err)
				$p_postmsg = 'Penyimpanan template laporan gagal';
			else
				$p_postmsg = 'Penyimpanan template laporan berhasil';
		}
		else if($r_act == 'hapustemplat') {
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_template,'pe_templatereport','idtemplate');
			
			$r_template = '';
			$namatemplate = '';
			
			if($p_posterr)
				$p_postmsg = 'Penghapusan template laporan gagal';
			else
				$p_postmsg = 'Penghapusan template laporan berhasil';
		}
		else if($r_act == 'muattemplat') {
			$rowt = $p_model::getTemplate($conn,$r_template);
			$namatemplate = $rowt['namatemplate'];
			
			if(!empty($rowt['listkolom']))
				$al_kolom = explode(',',$rowt['listkolom']);
			else
				unset($al_kolom);
			if(!empty($rowt['listurutan']))
				$al_urut = explode(',',$rowt['listurutan']);
			else
				unset($al_urut);
			if(!empty($rowt['listkriteria']))
				$al_kriteria = explode(',',$rowt['listkriteria']);
			else
				unset($al_kriteria);
		}
	}
	
	// default kolom, NIP dan nama
	if(empty($al_kolom))
		$al_kolom = array('nik','nama');
		
	$l_template = UI::createSelect('template',$p_model::template($conn),$r_template,'ControlStyle',true,'',true);
	
	$l_judul = UI::createTextBox('judullaporan','','ControlStyle',100,70,$edit=true);
	
	if(empty($p_reportpage))
		$p_reportpage = Route::getReportPage();
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forreport.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
	<style>
		#tab_kolom tr:nth-child(2n+3) {background: #F4F4F4}
		#tab_kolom tr:nth-child(2n+4) {background: #FFFFFF}
		#tab_urut tr:nth-child(2n+3) {background: #F4F4F4}
		#tab_urut tr:nth-child(2n+4) {background: #FFFFFF}
		#tab_kriteria tr:nth-child(2n+3) {background: #F4F4F4}
		#tab_kriteria tr:nth-child(2n+4) {background: #FFFFFF}
	</style>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<center>
					
					<? if(!empty($p_postmsg)) { ?>
					<center>
					<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
						<?= $p_postmsg ?>
					</div>
					</center>
					<div class="Break"></div>
					<?	}?>
					
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
					<div class="box-content" style="width:<?= $p_tbwidth-22 ?>px">
					
					<div class="filterTable" style="width:<?= $p_tbwidth-50 ?>px;">
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2">
								<strong>Nama Template</strong>
								<?= UI::createTextBox('namatemplate',$namatemplate,'ControlStye',100,50); ?>
								<input type="button" value="Simpan" class="ControlStyle" onclick="simpanTemplat()">
								&nbsp;&nbsp;&nbsp;
								<strong>Daftar Template</strong>
								<?= $l_template ?>
								<input type="button" value="Muat" class="ControlStyle" onclick="muatTemplat()">
								<input type="button" value="Hapus" class="ControlStyle" onclick="hapusTemplat()">
							</td>
						</tr>
					</table>
					</div>
					
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td valign="top" width="30%">
								<table cellpadding="4" width="100%">
									<tr>
										<td><strong>Daftar Kolom :</strong></td>
									</tr>
									<tr>
										<td><?= $lk_kolom ?> <input type="button" class="ControlStyle" value="Tambah" onClick="tambahKolom()"></td>
									</tr>
									<tr>
										<td>
											<table id="tab_kolom" cellpadding="3" cellspacing="0" class="GridStyle">
												<tr class="DataBG" height="30px">
													<td align="center" colspan="2">Daftar Kolom</td>
												</tr>
												<tr>
													<th align="center" width="130">Nama Kolom</td>
													<th align="center" width="80">Aksi</td>
												</tr>
												<?	if(!empty($al_kolom)) {
														$n = count($al_kolom);
														for($i=0;$i<$n;$i++) { 
												?>
												<tr>
													<td>
														<?= $a_kolom[$al_kolom[$i]] ?>
														<input type="hidden" name="kolom[]" value="<?= $al_kolom[$i] ?>">
													</td>
													<td align="center">
														<? if($i == ($n-1)) { ?>
														<img id="down-dis" src="images/down.png" class="ImgDisabled">
														<? } else { ?>
														<img id="down-en" src="images/down.png" style="cursor:pointer" onClick="turunBaris(this)">
														<? } ?>
														<? if($i == 0) { ?>
														<img id="up-dis" src="images/up.png" class="ImgDisabled">
														<? } else { ?>
														<img id="up-en" src="images/up.png" style="cursor:pointer" onClick="naikBaris(this)">
														<? } ?>
														<img src="images/delete.png" style="cursor:pointer" onClick="deleteBaris(this)">
													</td>
												</tr>
												<?		}
													} ?>
											</table>
										</td>
									</tr>
									<tr>
										<td>&nbsp;</td>
									</tr>
									<tr>
										<td><strong>Daftar Urutan :</strong></td>
									</tr>
									<tr>
										<td><?= $lu_kolom ?></td>
									</tr>
									<tr>
										<td><?= $lu_urut ?> <input type="button" class="ControlStyle" value="Tambah" onClick="tambahUrutan()"></td>
									</tr>
									<tr>
										<td>
											<table id="tab_urut" cellpadding="3" class="GridStyle">
												<tr class="DataBG" height="30px">
													<td align="center" colspan="3">Daftar Urutan</td>
												</tr>
												<tr>
													<th align="center" width="130">Nama Kolom</td>
													<th align="center" width="50">Urutan</td>
													<th align="center" width="80">Aksi</td>
												</tr>
												<?	if(!empty($al_urut)) {
														$n = count($al_urut);
														for($i=0;$i<$n;$i++) {
															list($c_kolom,$c_urut) = explode(':',$al_urut[$i]); 
												?>
												<tr>
													<td>
														<?= $a_kolom[$c_kolom] ?>
														<input type="hidden" name="urutan[]" value="<?= $c_kolom ?>:<?= $c_urut ?>">
													</td>
													<td><?= $a_urut[$c_urut] ?></td>
													<td align="center">
														<? if($i == ($n-1)) { ?>
														<img id="down-dis" src="images/down.png" class="ImgDisabled">
														<? } else { ?>
														<img id="down-en" src="images/down.png" style="cursor:pointer" onClick="turunBaris(this)">
														<? } ?>
														<? if($i == 0) { ?>
														<img id="up-dis" src="images/up.png" class="ImgDisabled">
														<? } else { ?>
														<img id="up-en" src="images/up.png" style="cursor:pointer" onClick="naikBaris(this)">
														<? } ?>
														<img src="images/delete.png" style="cursor:pointer" onClick="deleteBaris(this)">
													</td>
												</tr>
												<?		}
													} ?>
											</table>
										</td>
									</tr>
								</table>
							</td>							
							<td valign="top">
								<table cellpadding="4">
									<tr>
										<td width="450px" valign="top">
											<table cellpadding="3" cellspacing="0">
												<tr>
													<td><strong>Daftar Kriteria :</strong></td>
												</tr>
												<tr>
													<td><?= $l_kriteria ?>
														<input name="button" type="button" class="ControlStyle" onClick="tambahKriteria()" value="Tambah">
													</td>
												</tr>
												<tr>
													<td><div id="div_kriteria"> &nbsp; </div></td>
												</tr>               
												<tr>
													<td valign="top">
														<table id="tab_kriteria" cellpadding="3" cellspacing="0" class="GridStyle">
															<tr class="DataBG" height="30px">
																<td align="center" colspan="3">Daftar Kriteria</td>
															</tr>
															<tr>
																<th align="center" width="150">Nama Kriteria</td>
																<th align="center" width="300">Nilai Kriteria</td>
																<th align="center" width="50">Aksi</td>
															</tr>
															<?	if(!empty($al_kriteria)) {
																	$n = count($al_kriteria);
																	
																	for($i=0;$i<$n;$i++) {																		
																		list($c_kriteria,$c_param) = explode('|',$al_kriteria[$i]);
																		$ac_param = explode(':',$c_param);
																		
																		if(empty($a_kparam[$c_kriteria])) {
																			$c_paramlabel = $ac_param[0].' - '.$ac_param[1].' '.$ac_param[2];
																		}
																		else {
																			$ac_paramlabel = array();
																			
																			$nonbsp = array('&nbsp;' => '');
																			$m = count($ac_param);
																			for($j=0;$j<$m;$j++)
																				$ac_paramlabel[] = strtr($a_kparam[$c_kriteria][$ac_param[$j]],$nonbsp);
																			
																			$c_paramlabel = implode(', ',$ac_paramlabel);
																		} 
															?>
															<tr>
																<td><?= $a_kriteria[$c_kriteria] ?></td>
																<td><?= $c_paramlabel ?></td>
																<td align="center">
																	<input type="hidden" name="kriteria[]" value="<?= $c_kriteria ?>">
																	<input type="hidden" name="paramkriteria[]" value="<?= $c_param ?>">
																	<img src="images/delete.png" style="cursor:pointer" onClick="deleteBaris(this)">
																</td>
															</tr>
															<?		}
																} ?>
														</table>
													</td>
												</tr>
												<tr>
													<td></td>
												</tr>
												<tr>
													<td></td>
												</tr>
												<tr>
													<td></td>
												</tr>
												<tr>
													<td><strong>Judul Laporan :</strong></td>
												</tr>
												<tr>
													<td><?= $l_judul ?>
													</td>
												</tr>
												
											</table>
										</td>
										
										<td valign="top">
											<table>
												<tr>
													<td> 
														<table cellpadding="4" style="border:1px solid #ccc;border-collapse:collapse;font-size:11px;">
															<tr>  				         
																<td colspan="2"><b>Keterangan langkah:</b></td>
															</tr>
															<tr>
																<td valign="top">1. </td>        
																<td>Daftar kolom, adalah kolom yang akan pilih untuk di tampilkan di tabel. </td>
															</tr>
															<tr>
																<td valign="top">2.</td>
																<td>Daftar urutan, adalah untuk mengurutkan berdasarkan daftar kolom.</td>
															</tr>
															<tr>
																<td valign="top">3.</td>
																<td>Daftar kriteria, berfungsi untuk menyeleksi kriteria-kriteria tertentu. </td>
															</tr>
															<tr>
																<td colspan="2"><b>Template :</b></td>
															</tr>
															<tr>
																<td valign="top">1.</td>
																<td>Untuk menyimpan template, pada input teks sebelah kanan Nama template, di beri nama template sesuai dengan yang di inginkan.</td>
															</tr>
															<tr>
																<td valign="top">2.</td>
																<td>Untuk membuka template, pada combo pilihan daftar template, pilih nama template yang telah di simpan, dan tekan tombol muat.</td>
															</tr>
															<tr>
																<td valign="top">3.</td>
																<td>Untuk menghapus template, pada combo pilihan daftar template, pilih nama template yang telah di simpan, dan tekan tombol hapus.</td>
															</tr>
														</table>
													</td>
												</tr>	
											</table>
										</td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td colspan="2" align="center"><span class="EditTabLabel">Format</span>&nbsp;&nbsp;&nbsp;<?= uCombo::format() ?></td>
						</tr>
					</table>
					<div class="Break"></div>
					<?	if(empty($a_laporan)) { ?>
					<input type="button" value="Tampilkan" class="ControlStyle" onclick="goReport()">
					<?	} else {
							foreach($a_laporan as $t_file => $t_label) { ?>
					<input type="button" value="<?= $t_label ?>" class="ControlStyle" onclick="goReport('<?= $t_file ?>')">
					<?		}
						} ?>
					</div>
				</center>
				
			<input type="hidden" name="act" id="act">

			</form>			
			
			<div id="divk_pendidikan" style="display:none">
				<?= $l_pendidikan ?>
			</div>
			<div id="divk_tipepegawaibaru" style="display:none">
				<?= $l_tipepegawaibaru ?>
			</div>
			<div id="divk_tipepegawai" style="display:none">
				<?= $l_tipepegawai ?>
			</div>
			<div id="divk_jenispegawaibaru" style="display:none">
				<?= $l_jenispegawaibaru ?>
			</div>
			<div id="divk_kelompokpeg" style="display:none">
				<?= $l_kelompokpeg ?>
			</div>
			<div id="divk_jenispegawai" style="display:none">
				<?= $l_jenispegawai ?>
			</div>
			<div id="divk_hubungankerja" style="display:none">
				<?= $l_hubungankerja ?>
			</div>
			<div id="divk_statusaktif" style="display:none">
				<?= $l_statusaktif ?>
			</div>
			<div id="divk_statusaktifhb" style="display:none">
				<?= $l_statusaktifhb ?>
			</div>
			<div id="divk_golongan" style="display:none">
				<?= $l_golongan ?>
			</div>
			<div id="divk_fungsional" style="display:none">
				<?= $l_fungsional ?>
			</div>
			<div id="divk_unitkerja" style="display:none">
				<?= $l_unitkerja ?>
			</div>
			<div id="divk_unithomebase" style="display:none">
				<?= $l_unithomebase ?>
			</div>
			<div id="divk_noserdos" style="display:none">
				<?= $l_noserdos ?>
			</div>
			<div id="divk_sesuaibidang" style="display:none">
				<?= $l_sesuaibidang ?>
			</div>
			<div id="divk_periodegaji" style="display:none">
				<?= $l_periodegaji ?>
			</div>
			<div id="divk_mkseluruh" style="display:none">
				<input type="text" id="t_min" class="ControlStyle" size="2" maxlength="2"> -
				<input type="text" id="t_max" class="ControlStyle" size="2" maxlength="2"> tahun
				<input type="hidden" id="t_add" value="tahun">
			</div>
			<div id="divk_mkgolongan" style="display:none">
				<input type="text" id="t_min" class="ControlStyle" size="2" maxlength="2"> -
				<input type="text" id="t_max" class="ControlStyle" size="2" maxlength="2"> tahun
				<input type="hidden" id="t_add" value="tahun">
			</div>
			<div id="divk_tmtcoba" style="display:none">
				<input type="text" id="t_min" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_min_trg" style="cursor:pointer;" title="Pilih tanggal pertama">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_min",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_min_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script> -
				<input type="text" id="t_max" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_max_trg" style="cursor:pointer;" title="Pilih tanggal kedua">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_max",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_max_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script>
			</div>
			<div id="divk_tmttetap" style="display:none">
				<input type="text" id="t_min" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_min_trg" style="cursor:pointer;" title="Pilih tanggal pertama">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_min",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_min_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script> -
				<input type="text" id="t_max" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_max_trg" style="cursor:pointer;" title="Pilih tanggal kedua">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_max",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_max_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script>
			</div>
			<div id="divk_tglmasuk" style="display:none">
				<input type="text" id="t_min" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_min_trg" style="cursor:pointer;" title="Pilih tanggal pertama">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_min",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_min_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script> -
				<input type="text" id="t_max" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_max_trg" style="cursor:pointer;" title="Pilih tanggal kedua">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_max",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_max_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script>
			</div>

			<div id="divk_tmtpensiun" style="display:none">
				<input type="text" id="t_min" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_min_trg" style="cursor:pointer;" title="Pilih tanggal pertama">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_min",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_min_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script> -
				<input type="text" id="t_max" class="ControlStyle" size="10" maxlength="10">
				<img src="images/cal.png" id="t_max_trg" style="cursor:pointer;" title="Pilih tanggal kedua">
				<script type="text/javascript">
				Calendar.setup({
					inputField     :    "t_max",
					ifFormat       :    "%d-%m-%Y",
					button         :    "t_max_trg",
					align          :    "Br",
					singleClick    :    true
				});
				</script>
			</div>

			<div id="divk_usia" style="display:none">
				<input type="text" id="t_min" class="ControlStyle" size="2" maxlength="2"> -
				<input type="text" id="t_max" class="ControlStyle" size="2" maxlength="2"> tahun
				<input type="hidden" id="t_add" value="tahun">
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	
var reportpage = "<?= Route::navAddress($p_reportpage) ?>";
var required = "<?= @implode(',',$a_required) ?>";

var nkbaris = <?= count($al_kolom) ?>;
var nubaris = <?= count($al_urut) ?>;
var nwbaris = <?= count($al_kriteria) ?>;
var kname, ktype;

$(document).ready(function() {
	gantiKriteria();
});

function etrSimpanTemplat(e) {
	var ev= (window.event) ? window.event : e;
	var key = (ev.keyCode) ? ev.keyCode : ev.which;
	
	if (key == 13)
		simpanTemplat();
}

function simpanTemplat() {
	if(cfHighlight("namatemplate")) {
		$("#pageform").attr("action","<?= $i_phpfile ?>");
		$("#pageform").removeAttr("target");
		$("#act").val("simpantemplat");
		$("#pageform").submit();
	}
}

function muatTemplat() {
	$("#pageform").attr("action","<?= $i_phpfile ?>");
	$("#pageform").removeAttr("target");
	$("#act").val("muattemplat");
	$("#pageform").submit();
}

function hapusTemplat() {
	if($("#template").val() != "") {
		var hapus = confirm('Anda yakin akan menghapus templat "'+$("#template option:selected").text()+'"?');
		if(hapus) {
			$("#pageform").attr("action","<?= $i_phpfile ?>");
			$("#pageform").removeAttr("target");
			$("#act").val("hapustemplat");
			$("#pageform").submit();
		}
	}
}

function tambahKolom() {
	var baris, up, down;
	
	if($("#tab_kolom").find("input[value='"+$("#k_kolom").val()+"']").size() > 0)
		return false;
	
	if(nkbaris == 0)
		up = '<img id="up-dis" src="images/up.png" class="ImgDisabled"> ';
	else
		up = '<img id="up-en" src="images/up.png" style="cursor:pointer" onclick="naikBaris(this)"> ';
	
	nkbaris++;
	
	baris = '<tr>' + "\n" +
				'<td>' + "\n" +
					$("#k_kolom option:selected").text() + "\n" +
					'<input type="hidden" name="kolom[]" value="'+$("#k_kolom").val()+'">' + "\n" +
				'</td>' + "\n" +
				'<td align="center">' + "\n" +
					'<img id="down-dis" src="images/down.png" class="ImgDisabled"> ' + up + "\n" +
					'<img src="images/delete.png" style="cursor:pointer" onclick="deleteBaris(this)">' + "\n" +
				'</td>' + "\n" +
			'</tr>';
	
	down = $("#tab_kolom").find("tr:last").find("#down-dis");
	if(down.size() > 0)
		switchImg(down,true);
	
	$("#tab_kolom").append(baris);
}

function tambahUrutan() {
	var baris, up, down;
	
	if($("#tab_urut").find("input[value^='"+$("#u_kolom").val()+":']").size() > 0)
		return false;
	
	if(nubaris == 0)
		up = '<img id="up-dis" src="images/up.png" class="ImgDisabled"> ';
	else
		up = '<img id="up-en" src="images/up.png" style="cursor:pointer" onclick="naikBaris(this)"> ';
	
	nubaris++;
	baris = '<tr>' + "\n" +
				'<td>' + "\n" +
					$("#u_kolom option:selected").text() + "\n" +
					'<input type="hidden" name="urutan[]" value="'+$("#u_kolom").val()+':'+$("#u_urut").val()+'">' + "\n" +
				'</td>' + "\n" +
				'<td>'+$("#u_urut option:selected").text()+'</td>' + "\n" +
				'<td align="center">' + "\n" +
					'<img id="down-dis" src="images/down.png" class="ImgDisabled"> ' + up + "\n" +
					'<img src="images/delete.png" style="cursor:pointer" onclick="deleteBaris(this)">' + "\n" +
				'</td>' + "\n" +
			'</tr>';
	
	down = $("#tab_urut").find("tr:last").find("#down-dis");
	if(down.size() > 0)
		switchImg(down,true);
	
	$("#tab_urut").append(baris);
}

function deleteBaris(img) {
	var imgef;
	var tr = $(img).parent().parent();
	var table = tr.parent().parent();
	
	var ddis = tr.find("#down-dis");
	if(ddis.size() > 0) {
		imgef = tr.prev().find("#down-en");
		if(imgef.size() > 0)
			switchImg(imgef,false);
	}
	
	var udis = tr.find("#up-dis");
	if(udis.size() > 0) {
		imgef = tr.next().find("#up-en");
		if(imgef.size() > 0)
			switchImg(imgef,false);
	}
	
	if(table.attr("id") == "tab_kolom")
		nkbaris--;
	else if(table.attr("id") == "tab_urut")
		nubaris--;
	else if(table.attr("id") == "tab_kriteria")
		nwbaris--;
	
	$(img).parent().parent().replaceWith("");
}

function turunBaris(img) {
	var trr = $(img).parent().parent();
	var tre = trr.next();
	
	var tdr = $(img).parent();
	var tde = tre.children("td:last");
	
	tdrt = tdr.clone();
	tdet = tde.clone();
	
	tdr.replaceWith(tdet);
	tde.replaceWith(tdrt);
	
	trr.before(tre);
}

function naikBaris(img) {
	var trr = $(img).parent().parent();
	var tre = trr.prev();
	
	var tdr = $(img).parent();
	var tde = tre.children("td:last");
	
	tdrt = tdr.clone();
	tdet = tde.clone();
	
	tdr.replaceWith(tdet);
	tde.replaceWith(tdrt);
	
	trr.after(tre);
}

function switchImg(img,on) {
	if(img.attr("id").substr(0,2) == "do") {
		if(on) {
			img.attr("id","down-en");
			img.attr("src","images/down.png");
			img.css("cursor","pointer");
			img.removeAttr("class");
			img.attr("onclick","turunBaris(this)");
		}
		else {
			img.attr("id","down-dis");
			img.attr("src","images/down.png");
			img.attr("class","ImgDisabled");
			img.removeAttr("style");
			img.removeAttr("onclick");
		}
	}
	else if(img.attr("id").substr(0,2) == "up") {
		if(on) {
			img.attr("id","up-en");
			img.attr("src","images/up.png");
			img.css("cursor","pointer");
			img.removeAttr("class");
			img.attr("onclick","naikBaris(this)");
		}
		else {
			img.attr("id","up-dis");
			img.attr("src","images/up.png");
			img.attr("class","ImgDisabled");
			img.removeAttr("style");
			img.removeAttr("onclick");
		}
	}
}

function gantiKriteria() {
	kname = $("#s_kriteria").val();
	
	var clone = $("#divk_"+kname).clone();
	clone.removeAttr("style");
	
	if(clone.find("select").size() > 0)
		ktype = "select";
	else
		ktype = "text";
	
	$("#div_kriteria").html(clone);
}

function tambahKriteria() {
	var namakr, nilaikr, valkr, paramkr, baris;
	var arval = new Array();
	var arlabel = new Array();
	
	if($("#tab_kriteria").find("input[value='"+kname+"']").size() > 0)
		return false;
	
	if(ktype == 'select') {
		if($("#div_kriteria option:selected").size() == 0) {
			alert("Mohon pilih nilai kriteria yang diinginkan.");
			return false;
		}
		
		namakr = $("#s_kriteria option:selected").text();
		valkr = kname;
		
		$("#div_kriteria option:selected").each(function(i) {
			arval[i] = $(this).val();
			arlabel[i] = $(this).text().replace(/\u00a0/g,"");
		});
		
		nilaikr = arlabel.join(', ');
		paramkr = arval.join(':');
	}
	else {
		if(!cfHighlight("t_min,t_max"))
			return false;
		
		namakr = $("#s_kriteria option:selected").text();
		nilaikr = $("#div_kriteria #t_min").val();
		if($("#div_kriteria #t_max").val() != '') 
			nilaikr = nilaikr + ' - ' + $("#div_kriteria #t_max").val();
		if($("#div_kriteria #t_add").size() > 0)
			nilaikr = nilaikr + ' ' + $("#div_kriteria #t_add").val();
		
		valkr = kname;
		paramkr = $("#div_kriteria #t_min").val();
		if($("#div_kriteria #t_max").val() != '') 
			paramkr = paramkr + ':' + $("#div_kriteria #t_max").val();
		if($("#div_kriteria #t_add").size() > 0)
			paramkr = paramkr + ':' + $("#div_kriteria #t_add").val();
	}
	
	nwbaris++;
	baris = '<tr>' + "\n" +
				'<td>'+namakr+'</td>' + "\n" +
				'<td>'+nilaikr+'</td>' + "\n" +
				'<td align="center">' + "\n" +
					'<input type="hidden" name="kriteria[]" value="'+valkr+'">' + "\n" +
					'<input type="hidden" name="paramkriteria[]" value="'+paramkr+'">' + "\n" +
					'<img src="images/delete.png" style="cursor:pointer" onclick="deleteBaris(this)">' + "\n" +
				'</td>' + "\n" +
			'</tr>';
	
	$("#tab_kriteria").append(baris);
}

function showData() {
	$("#pageform").submit();
}

</script>
</body>
</html>
