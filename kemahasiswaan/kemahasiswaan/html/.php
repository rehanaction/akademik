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
	require_once(Route::getModelPath('pengajuanbeasiswa'));
	require_once(Route::getModelPath('beasiswa'));
	require_once(Route::getModelPath('tahapbeasiswa'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('berkasbeasiswa'));
	require_once(Route::getModelPath('jenisprestasi'));
	require_once(Route::getModelPath('tingkatprestasi'));
	require_once(Route::getModelPath('kategoriprestasi'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));

	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);

	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;

	// properti halaman
	$p_title = 'Data Pengajuan Beasiswa';
	$p_tbwidth = 800;
	$p_aktivitas = 'Asuransi Mahasiswa';
	$p_listpage = Route::getListPage();

	$p_model = mPengajuanBeasiswa;

	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);

	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);

	if(!empty($r_key)) {
		$r_idbeasiswa = $p_model::getDataField($conn,$r_key,'idbeasiswa');
		$a_beasiswa = mBeasiswa::getArrayBeasiswaMahasiswa($conn,false,$r_idbeasiswa);
	}
	else
		$a_beasiswa = mBeasiswa::getArrayBeasiswaMahasiswa($conn,true);

	$a_tahapbeasiswa = mTahapbeasiswa::getArray($conn);
	$a_pendidikan = mMahasiswa::pendidikan($conn);
	$a_pekerjaan = mMahasiswa::pekerjaan($conn);
	// struktur view
	$a_input = array();
	$a_input[] = array('kolom' => 'idbeasiswa', 'label' => 'Beasiswa', 'type' => 'S', 'option' => $a_beasiswa);
	$a_input[] = array('kolom' => 'nim', 'label' => 'Mahasiswa');
	$a_input[] = array('kolom' => 'tglpengajuan', 'label' => 'Tgl Pengajuan', 'type' => 'D');
	$a_input[] = array('kolom' => 'penghasilanortu', 'label' => 'Penghasilan Ortu', 'type' => 'N', 'maxlength' => 10);
	$a_input[] = array('kolom' => 'ipk', 'label' => 'IPK', 'type' => 'N','readonly'=>true);
	$a_input[] = array('kolom' => 'namastatus', 'label' => 'Status Mahasiswa','readonly'=>true);
	$a_input[] = array('kolom' => 'keterangan', 'label' => 'Keterangan', 'type' => 'M','readonly'=> Akademik::isMhs());
	$a_input[] = array('kolom' => 'isditerima', 'label' => 'Status Beasiswa', 'type' => 'R', 'option' => array('-1' => 'Lulus', '0' => 'Tidak Lulus'));
	$a_input[] = array('kolom' => 'tglterima', 'label' => 'Tgl Ditetapkan', 'type' => 'D','readonly'=> Akademik::isMhs());
	$a_input[] = array('kolom' => 'idtahapbeasiswa', 'label' => 'Tahap', 'type' => 'S', 'option' => $a_tahapbeasiswa);

	// mengambil data pelengkap
	$a_detail = array();

	$t_detail = array();
	$t_detail[] = array('kolom' => 'namasyaratbeasiswa', 'label' => 'Nama Syarat');
	$t_detail[] = array('kolom' => 'qty', 'label' => 'Qty');

	$a_detail['syarat'] = array('key' => mPengajuanBeasiswa::getDetailInfo('syarat','key'), 'data' => $t_detail);

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		$conn->BeginTrans();

		list($post,$record) = uForm::getPostRecord($a_input,$_POST);

		if(empty($post['isvalid']))
			$record['isvalid'] = 0;

		if(empty($post['isditerima']) or Akademik::isMhs())
			$record['isditerima'] = 0;

		foreach ($a_input as $key => $value) {
			if($value['type']=='M')
				$record[$value['kolom'].':skip'] = true;
		}

		if(empty($r_key)) {
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($conn,$a_input,$record,$r_key);
		}
		else{
			//delete dulu syarat
			list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_key,'syarat');

			//insert syarat
			if(!empty($_POST['a_syaratmhs'])){
				$recd['idpengajuanbeasiswa'] = $r_key;
				$recd['idbeasiswa'] = $_POST['idbeasiswa'];

				foreach($_POST['a_syaratmhs'] as $val){
					$recd['kodesyaratbeasiswa'] = $val;
					list($p_posterr,$p_postmsg) = $p_model::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$recd,'syarat');

					if(!empty($p_posterr))
						break;
				}
			}
			//insert tabel utama
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($conn,$a_input,$record,$r_key);
		}

		// cek kuota, jumlahpenerima diset di trigger
		if(empty($p_posterr) and !empty($record['isditerima'])) {
			$rowb = mBeasiswa::getData($conn,$record['idbeasiswa']);
			if($rowb['jumlahpenerima'] > $rowb['jumlahbeasiswa']) {
				$p_posterr = true;
				$p_postmsg = 'Kuota Beasiswa telah terpenuhi';
			}
		}

		$ok = Query::isOK($p_posterr);
		$conn->CommitTrans($ok);

		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);

		if(!$p_posterr) Route::navigate($p_listpage);
	}
	else if($r_act == 'insertdet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);

		$record = array('idasuransi' => $r_key);
		foreach($a_detail[$r_detail]['data'] as $t_detail) {
			$t_value = $_POST[$t_detail['kolom']];
			$record[$t_detail['kolom']] = CStr::cStrNull($t_value);
		}

		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = mSyaratasuransi::insertCRecordDetail($conn,$a_detail[$r_detail]['data'],$record,$r_detail);
	}
	else if($r_act == 'deletedet' and $c_edit) {
		$r_detail = CStr::removeSpecial($_POST['detail']);
		$r_subkey = CStr::removeSpecial($_POST['subkey']);

		list($p_posterr,$p_postmsg) = $p_model::deleteDetail($conn,$r_subkey,$r_detail);
	}else if($r_act == 'upload' and $c_edit) {
		$tipe=array('image/jpeg','image/jpg','image/gif','image/png','application/pdf');
		$ext=array('image/jpg'=>'jpg','image/jpeg'=>'jpg','image/gif'=>'gif','image/png'=>'png','application/pdf'=>'pdf');
		list($idpengajuanbeasiswa,$kodesyaratbeasiswa,$idbeasiswa) = explode("|",$_POST['subkey']);

		$file_types=$_FILES['fileberkas']['type'];
		$file_nama = $_FILES['fileberkas']['name'];
		$file_ext = pathinfo($file_nama,PATHINFO_EXTENSION);
		if($file_ext != $ext[$file_types])
			$file_nama .= '.'.$ext[$file_types];

		$file_name=str_replace('|',';',$_POST['subkey']).'.'.$ext[$file_types];

		if(in_array($file_types,$tipe) && !empty($tipe)){
			$upload=move_uploaded_file($_FILES['fileberkas']['tmp_name'],'uploads/syaratbeasiswa/'.$file_name);
			if($upload){
				$recordu=array();
				$recordu['idpengajuanbeasiswa']=$idpengajuanbeasiswa;
				$recordu['kodesyaratbeasiswa']=$kodesyaratbeasiswa;
				$recordu['idbeasiswa']=$idbeasiswa;
				$recordu['fileberkas']=$file_nama;

				//delete berkas
				list($p_posterr,$p_postmsg) = mBerkasBeasiswa::delete($conn,$_POST['subkey']);
				//insert berkas
				list($p_posterr,$p_postmsg) = mBerkasBeasiswa::insertRecord($conn,$recordu);

			}else{
				$p_posterr=true;
				$p_postmsg='Upload Gagal';
			}
		}else{
			$p_posterr=true;
			$p_postmsg='Pastikan Tipe File Berupa Gambar/Pdf, Upload Gagal';
		}
	}else if($r_act == 'deletefile' and $c_edit){

		$file = $conf['upload_dir'].'syaratbeasiswa/'.str_replace('|',';',$_POST['subkey']).'.'.$_POST['filetype'];

		$ok = @unlink($file);
		if($ok)
			list($p_posterr,$p_postmsg) = mBerkasBeasiswa::delete($conn,$_POST['subkey']);
		else{
			$p_posterr=true;
			$p_postmsg='File Gagal Dihapus.';
		}
	}

	// cek data
	if(!empty($r_key)) {
		$a_cek = $p_model::getData($conn,$r_key);
	}

	// ambil data halaman
	$row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);

	if(!empty($r_key)) {
		$a_pengajuan = $p_model::getData($conn,$r_key);

		$r_mahasiswa = $a_pengajuan['nim'];
		if(!empty($a_pengajuan))
			$r_namamahasiswa = $r_mahasiswa.' - '.mMahasiswa::getNama($conn,$r_mahasiswa,false);

		$r_idbeasiswa = $a_pengajuan['idbeasiswa'];
		$rowd = $p_model::getSyarat($conn,$a_pengajuan['idbeasiswa'],$a_pengajuan['idpengajuanbeasiswa']);
	}

	if(Akademik::isMhs()){
		$r_mahasiswa = Modul::getUserName();
		$r_namamahasiswa = $r_mahasiswa.' - '.mMahasiswa::getNama($conn,$r_mahasiswa,false);
	}
	if ($r_mahasiswa)
	$a_datamhs = mMahasiswa::getData($conn,$r_mahasiswa);
	/*$datamhs = array();
	$datamhs[] = array('left'=>array('label'=>'label','value'=>'value left'), 'right'=>array('label'=>'label','value'=>'value right'));
	$datamhs[] = array('left'=>array('label'=>'label 2','value'=>'value 2'), 'right'=>array('label'=>'label 2','value'=>'value 2'));*/

	

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
			<form name="pageform" id="pageform" method="post"  enctype="multipart/form-data">
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
					<table width="<?= $p_tbwidth-22 ?>" cellpadding="4" cellspacing="2" align="center">
						<?php
							if(Akademik::isMhs())
							{
						?>
								<tr>
									<td class="LeftColumnBG">Mahasiswa</td>
									<td class="RightColumnBG"><?=$r_namamahasiswa?></td>
								</tr>
								<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">
						<?php
					}else{
						?>
						<tr>
							<td class="LeftColumnBG">Mahasiswa <span id="edit" style="display:none">*</span></td>
							<td class="RightColumnBG">
								<?= Page::getDataInputWrap($r_namamahasiswa,
									UI::createTextBox('mahasiswa',$r_namamahasiswa,'ControlStyle',30,30)) ?>
								<input type="hidden" name="nim" id="nim" value="<?=$r_mahasiswa?>">
							</td>
						</tr>
					<?php } ?>
						<?= Page::getDataTR($row,'idbeasiswa') ?>
						<?/*
						<?= Page::getDataTR($row,'nilairapor') ?>
						<?= Page::getDataTR($row,'nilaiun') ?>
						*/?>
						<?= Page::getDataTR($row,'penghasilanortu') ?>
						<?
						 if(Akademik::isMhs()){
					 	?>
						<tr>
							<td class="LeftColumnBG">IPK</td>
							<td class="RightColumnBG"><?=$a_datamhs['ipk']?></td>
						</tr>
						<?
						}else
							Page::getDataTR($row,'ipk')
						?>
						<?= Page::getDataTR($row,'namastatus') ?>
						<?= Akademik::isMhs()?'':Page::getDataTR($row,'idtahapbeasiswa') ?>
						<?php
						if(!Akademik::isMhs())
							echo Page::getDataTR($row,'isditerima');
						else{
							$dit = Page::getDataValue($row,'isditerima');
							$tgl = Page::getDataValue($row,'tglterima');
						?>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120"> Status Beasiswa</td>
							<td>
								<?php
									if(empty($tgl))
										echo '';
									if(empty($dit))
										echo 'Tidak Lulus';
									else
										echo 'Lulus';
								?>
							</td>
						</tr>
						<?
						}
						?>
						<?= Page::getDataTR($row,'tglterima') ?>
						<?= Page::getDataTR($row,'keterangan') ?>
					</table>
					<?php
					if(!empty($r_key))
					{
					?>
					<br />
					<div class="tabs" style="width:<?= $p_tbwidth-22 ?>px">
						<ul>
							<!--li><a id="tablink" href="javascript:void(0)">A. Data Pribadi</a></li-->
							<li><a id="tablink" href="javascript:void(0)">B. Data Orangtua</a></li>
							<li><a id="tablink" href="javascript:void(0)">Prestasi</a></li>
							<li><a id="tablink" href="javascript:void(0)">Syarat</a></li>
						</ul>
						
						<!--div id="items">
							<table width="100%" border="0">
								
								<?php 
									foreach ($datamhs as $k => $row) {?>
								<tr>
									<td width="80"><?php echo $row['left']['label']?></td>
									<td width="10"> : </td>
									<td width="100"> <?php echo $row['left']['value']?> </td>
									<td width="60"></td>
									<td width="80"><?php echo $row['right']['label']?></td>
									<td width="10"> : </td>
									<td width="100"> <?php echo $row['right']['value']?> </td>
								</tr>
								<?php } ?>
							</table>
						</div-->
					<div id="items">
					<table cellpadding="4" cellspacing="2" align="center">
						<tr>
							<td colspan="2" class="DataBG">Orang Tua</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Nama Ayah</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['namaayah']?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Nama Ayah</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['namaibu']?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap">Alamat</td>
							<td class="RightColumnBG">
								<table>
									<tr>
										<td width="80">Alamat Ortu</td>
										<td width="5">:</td>
										<td><?= $a_datamhs['alamatortu'] ?></td>
									</tr>
									<tr>
										<td>RT / RW</td>
										<td>:</td>
										<td><?= $a_datamhs['rtortu'] ?>/<?= $a_datamhs['rwortu'] ?></td>
									</tr>
									<tr>
										<td>Kelurahan</td>
										<td>:</td>
										<td><?= $a_datamhs['kelurahanortu'] ?></td>
									</tr>
									<tr>
										<td>Kecamatan</td>
										<td>:</td>
										<td><?= $a_datamhs['kecamatanortu'] ?></td>
									</tr>
								</table>
							</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Propinsi</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['namapropinsiortu']?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Kota</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['namakotaortu']?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">kode pos</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['kodeposortu']?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Telp</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['telportu']?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Pendidikan ayah</td>
							<td class="RightColumnBG"><?php echo$a_pendidikan[$a_datamhs['kodependidikanayah']]?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Pendidikan ibu</td>
							<td class="RightColumnBG"><?php echo $a_pendidikan[$a_datamhs['kodependidikanibu']]?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Pekerjaan ayah</td>
							<td class="RightColumnBG"><?php echo $a_pekerjaan[$a_datamhs['kodepekerjaanayah']]?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Pekerjaan ibu</td>
							<td class="RightColumnBG"><?php echo $a_pekerjaan[$a_datamhs['kodepekerjaanibu']]?> </td>
						</tr>

						<tr>
							<td colspan="2" class="DataBG">Kontak yang Bisa Dihubungi Saat Darurat</td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Nama Kontak Darurat</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['namacpdarurat']?> </td>
						</tr>
						<tr>
							<td class="LeftColumnBG" style="white-space:nowrap" width="120">Telp Kontak Darurat</td>
							<td class="RightColumnBG"><?php echo $a_datamhs['telpcpdarurat']?> </td>
						</tr>
					</table>

					</div>
						
						<div id="items" >
							<div id="item-prestasi"></div>
						</div>
						<div id="items">
							<? if(!empty($r_key)) { ?>
							<br>
							<?	/**********/
								/* DETAIL */
								/**********/

								$t_field = 'syarat';
								$t_colspan = count($a_detail[$t_field]['data'])+3;
								$t_dkey = $a_detail[$t_field]['key'];

								if(!is_array($t_dkey))
									$t_dkey = explode(',',$t_dkey);

							?>
							<table width="100%" cellpadding="4" cellspacing="2" align="center" class="GridStyle">
								<tr>
									<td colspan="<?= $t_colspan ?>" class="DataBG">Daftar Syarat</td>
								</tr>
								<tr>
									<th align="center" class="HeaderBG" width="30">No</th>
								<?	foreach($a_detail[$t_field]['data'] as $datakolom) { ?>
									<th align="center" class="HeaderBG"><?= $datakolom['label'] ?></th>
								<?	} ?>
									<th align="center" class="HeaderBG"> File</th>
									<th align="center" class="HeaderBG" width="30" colspan="2"> Check</th>
								</tr>
								<?	$i = 0;
									if(!empty($rowd)) {
										foreach($rowd as $rowdd) {
											if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;

											$t_keyrow = array();
											foreach($t_dkey as $t_key)
												$t_keyrow[] = $rowdd[trim($t_key)];

											$t_key = implode('|',$t_keyrow);
								?>
								<tr valign="top" class="<?= $rowstyle ?>">
									<td><?= $i ?></td>
								<?		foreach($a_detail[$t_field]['data'] as $datakolom) {
									?>
									<td <?= empty($datakolom['align']) ? '' : ' align="'.$datakolom['align'].'"' ?>><?=$rowdd[$datakolom['kolom']]?></td>
								<?		} ?>
									<td>
										<?php
										if(!empty($rowdd['isberkas'])) {
											if(!empty($rowdd['fileberkas'])) {
										?>
										<a href="<?=$conf['upload_dir'].'syaratbeasiswa/'.$r_key.';'.$rowdd['kodesyaratbeasiswa'].';'.$r_idbeasiswa.'.'.end(explode('.',$rowdd['fileberkas']))?>" target="_blank"><?=$rowdd['fileberkas']?></a>
										<u class="ULink" data-id="<?=$r_key.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" data-type="<?=end(explode('.',$rowdd['fileberkas']))?>" onclick="goDeleteFile(this)">Hapus file</u>
										<?php
											}
										?>
										<div id="edit" style="display:none;margin-top:5px">
											<input type="file" name="fileberkas" data-id="<?=$r_key.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" size="30" class="ControlStyle">
											<br />(image dan pdf,ukuran maks 2 MB)
											<br /><input type="button" data-id="<?=$r_key.'|'.$rowdd['kodesyaratbeasiswa'].'|'.$r_idbeasiswa?>" value="Upload" onclick="goUpload(this)">
										</div>
										<?php
										} ?>
									</td>
									<td align="center">
										<span id="show">
											<?php
												if(!empty($rowdd['syarat']))
													echo '<img src="images/check.png">';
												else
													echo '';
											?>
										</span>
										<span id="edit" style="display: none;">
										<?php
										if(Akademik::isMhs()){
											if(!empty($rowdd['syarat']))
														echo '<img src="images/check.png">';
													else
														echo '';
											} else { ?>
												<input id="a_syaratmhs[]" name="a_syaratmhs[]" value="<?= $rowdd['kodesyaratbeasiswa'] ?>" type="checkbox"  <?= ($rowdd['syarat'] == 1)? ' checked' : '' ?> >
										<?php } ?>
										</span>

									</td>
								</tr>
								<?
									}
									}
									if($i == 0) { ?>
								<tr>
									<td align="center" colspan="<?= $t_colspan ?>">Data kosong</td>
								</tr>
								<?	} ?>
							</table>
							<? } ?>
						</div>
					</div>
					<?
					}
					?>
				</center>

				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<input type="hidden" name="filetype" id="filetype">
				<?	} ?>
			</form>
		</div>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
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
	// autocomplete
	$("#mahasiswa").xautox({strpost: "f=acmahasiswakemahasiswaan", targetid: "nim"});
});

function goSave() {
	var pass = true;
	if(typeof(required) != "undefined") {
		if(!cfHighlight(required))
			pass = false;
	}

	// cek nim
	var cek = $("#nim");
	if(cek.length > 0 && cek.val() == "") {
		doHighlight($("#mahasiswa").get(0));

		if(pass) {
			alert("Mohon mengisi isian-isian yang berwana kuning dengan benar terlebih dahulu.");
			pass = false;
		}
	}

	if(pass) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

$('#nim').change(function(){
     loadAsuransi();
})

// ajax ganti kota
function loadAsuransi() {
	var param = new Array();
	param[0] = $("#nim").val();
	param[1] = "<?= $r_idasuransimhs ?>";

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "optasuransi", q: param }
				});

	jqxhr.done(function(data) {
		$("#idasuransimhs").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
loadAsuransi();
loadPenghargaan();

function goUpload(elem) {
	var subkey = $(elem).attr('data-id');

	$("[name='fileberkas'][data-id!='" + subkey + "']").prop("disabled",true);

	document.getElementById("act").value = "upload";
	document.getElementById("subkey").value = subkey;
	goSubmit();
}

function goDeleteFile(elem) {
	document.getElementById("act").value = "deletefile";
	document.getElementById("subkey").value = $(elem).attr('data-id');
	document.getElementById("filetype").value = $(elem).attr('data-type');
	goSubmit();
}

function loadPenghargaan(){
	var param = new Array();
	param[0] = $("#key").val();
	<?php /* param[1] = "<?= $r_key ?>"; */ ?>

	var jqxhr = $.ajax({
					url: ajaxpage,
					timeout: ajaxtimeout,
					data: { f: "loadprestasi", q: param }
				});

	jqxhr.done(function(data) {
		$("#item-prestasi").html(data);
    });
    /* jqxhr.fail(function(xhr,status) {
		alert(status);
	}); */
}
</script>
</body>
</html>
