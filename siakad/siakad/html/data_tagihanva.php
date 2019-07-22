<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('tagihanva'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	
	// cek mahasiswa
	$ismhs = (Akademik::isMhs() ? true : false);
	
	// variabel request
	$r_key = $_REQUEST['key'];
	
	$r_nim = ($ismhs ? Modul::getUserName() : $_POST['nim']);
	$r_kelompok = Modul::setRequest($_POST['kelompok'],'KELOMPOKTAGIHAN');
	
	// properti halaman
	$p_title = 'Data Transaksi Pembayaran';
	$p_tbwidth = '100%';
	$p_aktivitas = 'DEFAULT';
	$p_model = mTagihanVA;
	$p_key = $p_model::key;
	
	$p_listpage = Route::getListPage();
	$c_readlist = (Modul::getFileAuth($p_listpage) ? true : false);
	
	if(empty($r_key) and empty($c_insert)) {
		if($c_readlist)
			Route::navigate($c_readlist);
		else
			Route::navigate('home');
	}
	
	// combo
	$a_kelompok = $p_model::arrQueryKelompokTagihan($conn);
	unset($a_kelompok['FRM']);
	
	if(!isset($r_kelompok))
		$r_kelompok = Modul::setRequest(key($a_kelompok),'KELOMPOKTAGIHAN');
	
	$l_kelompok = UI::createSelect('kelompok',$a_kelompok,$r_kelompok,'ControlStyle',true,($ismhs ? 'onchange="goSubmit()"' : null));
	
	// mengambil data
	if(!empty($r_key)) {
		$row = $p_model::getData($conn,$r_key);
		if(!empty($row)) {
			$r_kelompok = $row['kodekelompok'];
			if(empty($row['nim']))
				$r_nim = $row['nopendaftar'];
			else
				$r_nim = $row['nim'];
			
			// cek mahasiswa atau tidak ada nim/no. pendaftar
			if(($ismhs and $r_nim != Modul::getUserName()) or empty($r_nim)) {
				if($c_readlist)
					Route::navigate($p_listpage);
				else
					Route::navigate('home');
			}
			
			// cek status
			if($row['status'] == 'L' or $row['status'] == 'C') {
				$c_insert = false;
				$c_update = false;
				$c_delete = false;
			}
			
			// ambil detail
			$arr_tagihan = $p_model::getListDetail($conn,$r_key);
		}
		else
			$r_key = null;
	}
	
	$r_namakelompok = $a_kelompok[$r_kelompok];
	list(,$r_cnamakelompok) = explode(' - ',$r_namakelompok);
	
	if(!empty($r_nim)) {
		// ambil detail  mahasiswa/pendaftar
		$mhs = $p_model::getDatamhs($conn,$r_nim);
		if(empty($mhs)) {
			$mhs = $p_model::getDatapendaftar($conn,$r_nim);
			if(empty($mhs))
				$r_nim = null;
			else
				$isdatamhs = false;
		}
		else
			$isdatamhs = true;
	}
	
	if(!empty($r_nim)) {
		// ambil setting
		$settingdetail = $p_model::getDataSetting($conn,$r_kelompok);
		$r_periode = $settingdetail['periodesekarang'];
		
		// cek tagihan va aktif
		if(empty($r_key))
			$idaktif = $p_model::getTagihanAktif($conn,$r_nim);
		
		if(empty($idaktif)) {
			if(empty($r_key))
				$arr_tagihan = $p_model::getInquiry($conn,$r_nim,$r_kelompok);
			
			$jmltagihan = 0;
			$arr_idtagihan = array();
			foreach($arr_tagihan as $i => $tagihan) {
				if(empty($tagihan['isdeposit'])) {
					$arr_idtagihan['idtagihan'][] = $tagihan['idtagihan'];
					$jmltagihan++;
				}
				else
					$arr_idtagihan['iddeposit'][] = $tagihan['iddeposit'];
			}
		}
	}
	
	// ada aksi
	$c_edit = ((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update));
	
	$r_act = $_POST['act'];
	if($r_act == 'check' and !empty($r_key)) {
		// tidak pakai transaksi karena hanya inquiry
		$req = array();
		$req['trx_id'] = $r_key;
		$req['get_payment'] = 1;
		
		$resp = Pay::inquiryBilling($req);
		
		// cek status
		$record = array();
		if($resp['status'] == Pay::ERROR_OK) {
			$record['kodeva'] = $resp['data']['virtual_account'];
			$record['expiredtime'] = $resp['data']['datetime_expired'];

			if($resp['data']['va_status'] == '2') {
				if(empty($resp['data']['datetime_payment_iso8601']))
					$record['status'] = 'C';
				else
					$record['status'] = 'L';
			}
			else
				$record['status'] = 'A';
		}
		else if($resp['status'] == Pay::ERROR_NOTFOUND) {
			$record['kodeva'] = null;
			$record['status'] = 'S';
		}
		else
			$err = true;
		
		if(empty($err)) {
			// mulai transaksi
			$conn->BeginTrans();
			
			// update tagihan va
			$err = $p_model::updateRecord($conn,$record,$r_key);
			
			// jika ada pembayaran, masukkan
			if(empty($err) and !empty($resp['data']['payment_list'])) {
				foreach($resp['data']['payment_list'] as $rowp) {
					$err = $p_model::bayarVA($conn,$r_key,$rowp);
					if(!empty($err))
						break;
				}
			}
			
			// selesai transaksi
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
		}
		
		$p_posterr = $err;
		$p_postmsg = 'Pengecekan status transaksi pembayaran '.(empty($err) ? 'berhasil' : 'gagal');
		
		if(empty($err)) {
			$a_flash = array();
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash,Route::thisPage().'&key='.$r_key);
		}
	}
	else if($r_act == 'save' and $c_edit and $jmltagihan > 0) {
		$conn->BeginTrans();

		// bisa memilih tagihan
		$jmltagihanpilih = 0;
		$totaltagihanpilih = 0;
		$arr_idtagihanpilih = array();
		foreach($_POST['idtagihan'] as $t_id) {
			if($t_id[0] == 'D') {
				$t_isdeposit = true;
				$t_id = substr($t_id,1);
			}
			else
				$t_isdeposit = false;
			
			foreach($arr_tagihan as $i => $tagihan) {
				$cek1 = ($tagihan['isdeposit'] === $t_isdeposit);
				$cek2 = ($t_isdeposit and $t_id == $tagihan['iddeposit']);
				$cek3 = (empty($t_isdeposit) and $t_id == $tagihan['idtagihan']);

				if($cek1 and ($cek2 or $cek3)) {
					if(empty($t_isdeposit)) {
						$arr_idtagihanpilih['idtagihan'][] = $t_id;
						$jmltagihanpilih++;
					}
					else
						$arr_idtagihanpilih['iddeposit'][] = $t_id;
					
					if(empty($r_key))
						$totaltagihanpilih += ((float)$tagihan['nominaltagihan'] - (float)$tagihan['potongan'] + (float)$tagihan['denda']);
					else
						$totaltagihanpilih += ((float)$tagihan['nominalcek'] - (float)$tagihan['potongancek'] + (float)$tagihan['dendacek']);
				}
			}
		}
		
		if($jmltagihanpilih > 0 and $totaltagihanpilih > 0) {
			// jika bayar all tinggal ganti $arr_idtagihanpilih dengan $arr_idtagihanall
			list($err,$rowc) = $p_model::createVATagihan($conn,$arr_idtagihanpilih,$r_nim,$r_kelompok,$isdatamhs,$r_key);
		}
		else {
			$err = true;
			$t_postmsg = 'Silahkan pilih tagihan dan pastikan total bayarnya lebih dari 0';
		}
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
		
		$p_posterr = $err;
		$p_postmsg = (empty($r_key) ? 'Pemrosesan' : 'Update').' transaksi pembayaran '.($ok ? 'berhasil' : 'gagal').(empty($t_postmsg) ? '' : '. '.$t_postmsg);
		
		if($ok) {
			$a_flash = array();
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash,Route::thisPage().'&key='.$rowc['trxid']);
		}
	}
	else if($r_act == 'delete' and $c_delete and !empty($r_key)) {
		$conn->BeginTrans();
		
		// update dulu, bisa dirollback kalau update billing gagal
		$record = array();
		$record['status'] = 'C';
		
		$err = $p_model::updateRecord($conn,$record,$r_key);
		
		// kirim ke bank
		if(empty($err)) {
			$resp = Pay::cancelBilling($r_key);
			$err = ($resp['status'] == Pay::ERROR_OK ? false : true);
		}
		
		$ok = Query::isOK($err);
		$conn->CommitTrans($ok);
		
		$p_posterr = $err;
		$p_postmsg = 'Pembatalan transaksi pembayaran '.($ok ? 'berhasil' : 'gagal');
		
		if(empty($err)) {
			$a_flash = array();
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash,Route::thisPage().'&key='.$r_key);
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
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
			<table border="0" cellspacing="10" align="center">
				<tr>
					<?	if($c_readlist) { ?>
					<td id="be_list" class="TDButton" onclick="goList()">
						<img src="images/list.png"> Daftar
					</td>
					<?	} if($c_insert and !empty($r_key)) { ?>
					<td id="be_add" class="TDButton" onclick="goNew()">
						<img src="images/add.png"> Bayar Tagihan Baru
					</td>
					<?	} if(!empty($r_key)) { ?>
					<td id="be_cek" class="TDButton" onclick="goSave()">
						<img src="images/magnify.png"> Cek Status
					</td>
					<?	} if($c_edit and $jmltagihan > 0) { ?>
					<td id="be_save" class="TDButton payButton" onclick="goSave()">
						<img src="images/disk.png"> <?php echo empty($r_key) ? 'Proses Transaksi' : 'Update Transaksi' ?>
					</td>
					<?	} if($c_delete and !empty($r_key)) { ?>
					<td id="be_delete" class="TDButton" onclick="goDelete()">
						<img src="images/delete.png"> Batalkan
					</td>
					<?	} ?>
				</tr>
			</table>
			<form name="pageform" id="pageform" method="post">
				<?	if(empty($r_key)) { ?>
				<center>
					<div class="filterTable" style="width:<?= $p_tbwidth ?>;box-sizing:border-box">
						<table width="100%" cellpadding="0" cellspacing="0" align="center">
							<tr>
								<td valign="top" width="50%">
									<table width="100%" cellspacing="0" cellpadding="4">
										<tr>
											<td width="50" style="white-space:nowrap"><strong>Jenis Tagihan</strong></td>
											<td><strong>:</strong> <?=$l_kelompok?></td>
										</tr>
										<?	if(!$ismhs) { ?>
										<tr>
											<td width="50" style="white-space:nowrap"><strong>NIM/No Pendaftar</strong></td>
											<td valign="middle">
												<strong>:</strong> 
												<?= UI::createTextBox('nimtext',$r_nim,'ControlStyle','20','20',true) ?>
												<input type="hidden" name="nim" id="nim" value="<?= $r_nim ?>">
												<input type="button" class="payButton" value="Inquiry Tagihan" id="be_inquiry">
											 </td>
										</tr>
										<?	} ?>
									</table>
								</td>
							</tr>
						</table>
					</div>
				</center>
				<br />
				<?	} ?>
				<?	if(!empty($p_postmsg)) { ?>
				<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>;box-sizing:border-box">
					<?= $p_postmsg ?>
				</div>
				</center>
				<div class="Break"></div>
				<?	} ?>
				<?	if(!empty($r_nim)) { ?>
				<center>
					<header style="width:<?= $p_tbwidth ?>">
						<div class="inner">
							<div class="left title">
								<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)"> <h1><?= $p_title ?></h1>
							</div>
						</div>
					</header>
				</center>
				<table cellpadding="4" cellspacing="0" align="center" style="width:<?= $p_tbwidth ?>">
					<tr>
						<td>
							<table width="100%">
								<tr>
									<td  width="13%"><strong> NIM/No Pendaftar</strong></td>
									<td width="1%"><strong>:</strong></td>
									<td width="35%" style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$r_nim?></td>
									<td width="13%"><strong> Sistem Kuliah</strong></td>
									<td width="1%"><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['namasistem']?></td>
								</tr>
								<tr>
									<td><strong> Nama</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['nama']?></td>
									<td><strong> Jalur Penerimaan</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['jalurpenerimaan'].' '.$mhs['namagelombang']?></td>
								</tr>
								<tr>
									<td><strong> Jurusan</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$mhs['kodeunit'].' '.$mhs['namaunit']?></td>
									<td><strong> Periode Inquiry</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$r_periode?></td>
								</tr>
								<? if(!empty($r_key)) { ?>
								<tr>
									<td><strong> Billing ID</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=Pay::PAY_CLIENT_ID.$row['trxid']?></td>
									<td><strong> Kelompok Tagihan</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$r_namakelompok?></td>
								</tr>
								<tr>
									<td><strong> Kode VA</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><strong><?=implode('.',str_split($row['kodeva'],4))?></strong></td>
									<td><strong> Status</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999">
										<strong><?	switch($row['status']) {
														case 'A': echo 'Belum Bayar'; break;
														case 'C': echo '<span style="color:red">Batal/Kadaluarsa</span>'; break;
														case 'L': echo '<span style="color:green">Lunas</span>'; break;
														case 'S': echo '<span style="color:grey">Belum Dikirim</span>'; break;
													} ?></strong>
									</td>
								</tr>
								<tr>
									<td><strong> Nama Nasabah</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><strong><?=$row['customername']?></strong></td>
									<td><strong> Waktu Kadaluarsa</strong></td>
									<td><strong>:</strong></td>
									<td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><strong><?=CStr::formatDateTimeInd($row['expiredtime'],true,true)?></strong></td>
								</tr>
								<? } ?>
							</table>
						</td>
					</tr>
					<tr>
						<td colspan="9"></td>
					</tr>
					<tr>
						<td colspan="6">
							<?php if(!empty($idaktif)) { ?>
							<div class="DivError">
								Mahasiswa/Pendaftar ini memiliki <strong>transaksi pembayaran</strong> yang harus <strong>dibatalkan terlebih dahulu</strong> sebelum membuat transaksi baru.
								Klik <a href="<?php echo Route::navAddress('data_tagihanva&key='.$idaktif) ?>" style="color:blue">di sini</a> untuk melihat transaksi tersebut.
							</div>
							<?php } else { ?>
							<table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
								<tr>
									<?	if($c_edit) { ?>
									<th width="3%"><input type="checkbox" id="idtagihan_all" checked /></th>
									<?	} ?>
									<th width="3%">No.</th>
									<th>ID Tagihan / Voucher</th>
									<th>Jenis Tagihan</th>
									<th width="11%">Periode Tagihan</th>
									<th width="11%">Jumlah Tagihan</th>
									<th width="10%">Potongan</th>
									<th width="10%">Denda</th>
									<th width="10%">Jumlah Bayar</th>
									<?	if(!empty($r_key)) { ?>
									<th>Cek</th>
									<?	} ?>
								</tr>
								<?	$total = $totpotongan = $totdenda = 0;
									if(!empty($arr_tagihan)) {
										$i = 0;
										foreach($arr_tagihan as $tagihan) {
											$nominal = (float)$tagihan['nominaltagihan'];
											$potongan = (float)$tagihan['potongan'];
											$denda = (float)$tagihan['denda'];

											$total += $nominal;
											$totpotongan += $potongan;
											$totdenda += $denda;

											if($tagihan['isdeposit']) {
												$id = $tagihan['novoucher'];
												$idcb = 'D'.$tagihan['iddeposit'];
												if($tagihan['jenisdeposit'] == 'V')
													$jenistagihan = 'VOUCHER';
												else
													$jenistagihan = 'DEPOSIT';

												if(!empty($tagihan['idcek'])) {
													$statusdet = 'OK';
													if($status == 'A' or $status == 'S') {
														if(!empty($tagihan['isinvalid']))
															$statusdet = 'Tidak Bisa Digunakan';
														else if($tagihan['nominalcek'] != $potongan)
															$statusdet = 'Nominal: '.CStr::FormatNumber($tagihan['nominalcek']);
													}
												}
												else
													$statusdet = 'Tidak Ada';
											}
											else {
												$id = $tagihan['idtagihan'];
												$idcb = $id;
												$jenistagihan = $tagihan['jenistagihan'].' - '.$tagihan['namajenistagihan'];
												
												if(!empty($tagihan['idcek'])) {
													$status = $row['status'];
													if(($status == 'A' or $status == 'S') and $tagihan['flaglunas'] == 'L')
														$statusdet = 'Lunas';
													else if($status == 'L' and $tagihan['flaglunas'] != 'L')
														$statusdet = 'Belum Lunas';
													else if($status == 'A' and !empty($tagihan['isinvalid']))
														$statusdet = 'Tidak Bisa Dibayar';
													else if($tagihan['nominalcek'] != $nominal)
														$statusdet = 'Nominal: '.CStr::FormatNumber($tagihan['nominalcek']);
													else if($tagihan['potongancek'] != $potongan)
														$statusdet = 'Potongan: '.CStr::FormatNumber($tagihan['potongancek']);
													else if($tagihan['dendacek'] != $denda)
														$statusdet = 'Denda: '.CStr::FormatNumber($tagihan['dendacek']);
													else
														$statusdet = 'OK';
												}
												else
													$statusdet = 'Tidak Ada';
											}
								?>
								<tr>
									<?	if($c_edit) { ?>
									<td align="center"><input type="checkbox" name="idtagihan[]" value="<?php echo $idcb ?>" checked /></td>
									<?	} ?>
									<td><?= ++$i ?></td>
									<td><?=$id?></td>
									<td><?=$jenistagihan?></td>
									<td align="center"><?=empty($tagihan['bulantahun']) ? $tagihan['periode'] : $tagihan['bulantahun']?></td>
									<td align="right"><?=CStr::FormatNumber($nominal)?></td>
									<td align="right"><?=CStr::FormatNumber($potongan)?></td>
									<td align="right"><?=CStr::FormatNumber($denda)?></td>
									<td align="right"><?=CStr::FormatNumber($nominal-$potongan+$denda)?></td>
									<?	if(!empty($r_key)) { ?>
									<td><span style="color:<?= ($statusdet == 'OK' ? 'green' : 'red') ?>"><?=$statusdet?></span></td>
									<?	} ?>
								</tr>
								<?		}

										// biaya administrasi
										if(isset($row['adminamount']))
											$t_admin = $row['adminamount'];
										else
											$t_admin = Pay::PAY_ADMIN_AMOUNT;
										
										if(!empty($t_admin)) {
								?>
								<tr>
									<?	if($c_edit) { ?>
									<td></td>
									<?	} ?>
									<td><?= ++$i ?></td>
									<td></td>
									<td>BIAYA ADMINISTRASI</td>
									<td></td>
									<td></td>
									<td></td>
									<td></td>
									<td align="right"><?=CStr::FormatNumber($t_admin)?></td>
									<?	if(!empty($r_key)) { ?>
									<td></td>
									<?	} ?>
								</tr>
								<?		}
									}
									else {
								?>
								<tr>
									<td colspan="<?= (empty($r_key) ? 8 : 9) + ($c_edit ? 1 : 0) ?>" align="center">Tidak ada data tagihan yang belum lunas</td>
								</tr>
								<?	} ?>
								<tr>
									<th colspan="<?= 4 + ($c_edit ? 1 : 0) ?>" style="text-align:right">TOTAL</th>
									<th style="text-align:right"><?=CStr::FormatNumber($total)?></th>
									<th style="text-align:right"><?=CStr::FormatNumber($totpotongan)?></th>
									<th style="text-align:right"><?=CStr::FormatNumber($totdenda)?></th>
									<th style="text-align:right"><?=CStr::FormatNumber($total-$totpotongan+$totdenda+$t_admin)?></th>
									<?	if(!empty($r_key)) { ?>
									<th>&nbsp;</th>
									<?	} ?>
								</tr>
								<?	if($c_edit) { ?>
								<tr>
									<th colspan="5" style="text-align:right">TOTAL BAYAR</th>
									<?php for($i=5;$i<=8;$i++) { ?>
									<th style="text-align:right"><span id="span_total_<?php echo $i ?>">0</span></th>
									<?php } ?>
									<?	if(!empty($r_key)) { ?>
									<th>&nbsp;</th>
									<?	} ?>
								</tr>
								<?	} ?>
							</table>
							<?php } ?>
						</td>
					</tr>
					<?	if(!empty($r_key) or empty($idaktif)) { ?>
					<tr>
						<td colspan="9"></td>
					</tr>
					<tr>
						<td>
							<center>
								<table width="100%">
								<tr>
									<td colspan="2"><strong>Informasi :</strong></td>
								</tr>
								<?	if(!empty($r_key)) { ?>
								<tr valign="top">
									<td width="15">1.</td>
									<td>
										Untuk melakukan pembayaran silahkan transfer <strong><?=CStr::FormatNumber($row['trxamount']+$row['adminamount'])?></strong>
										rupiah ke rekening BNI <strong><?=implode('.',str_split($row['kodeva'],4))?></strong>
										a.n. <strong><?=$row['customername']?></strong>.
									</td>
								</tr>
								<tr valign="top">
									<td>2.</td>
									<td>
										Pembayaran hanya bisa dilakukan pada hari yang sama.
										Jika status transaksi sudah <strong><span style="color:red">Batal/Kadaluarsa</span></strong>
										silahkan membuat transaksi baru dengan klik tombol <strong>Bayar Tagihan Baru</strong>.
									</td>
								</tr>
								<tr valign="top">
									<td>3.</td>
									<td>Untuk mengecek <strong>Status</strong> transaksi ini di Bank, klik tombol <strong>Cek Status</strong>.</td>
								</tr>
								<tr valign="top">
									<td>4.</td>
									<td>
										Jika isi kolom <strong>Cek</strong> pada tabel di atas bukan <span style="color:green">OK</span> berarti ada perubahan tagihan di bagian keuangan universitas.
										Untuk melakukan update transaksi ini, klik tombol <strong>Update Transaksi</strong>.
									</td>
								</tr>
								<tr valign="top">
									<td>5.</td>
									<td>Jika anda ingin membatalkan transaksi ini, klik tombol <strong>Batalkan</strong>. Transaksi yang sudah dibatalkan tidak bisa diproses ke pembayaran.</td>
								</tr>
								<?	} else if(empty($idaktif)) { ?>
								<tr valign="top">
									<td>Cek terlebih dahulu detail tagihan yang akan dibayar pada tabel di atas. Jika sudah benar klik tombol <strong>Proses Transaksi</strong> untuk mengirimkan tagihan ini ke Bank.</td>
								</tr>
								<?	} ?>
								</table>
							</center>
						</td>
					</tr>
					<?	} ?>
				</table>
				<?	} ?>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
			</form>
		</div>
	</div>
</div>

<script type="text/javascript">

var totaltagihan, totalbayar;

$(function() {
	<?	if(!$ismhs) { ?>
	$("#be_inquiry").click(function() {
		$("#nim").val($("#nimtext").val());
		goSubmit();
	});
	$("#nimtext").keydown(function(e) {
		if(e.which == 13) {
			e.preventDefault();
			$("#be_inquiry").triggerHandler("click");
		}
	});
	<?	} ?>
	<?	if($c_readlist) { ?>
	$("#be_list").click(function() {
		location.href = "<?= Route::navAddress($p_listpage) ?>";
	});
	<?	} ?>
	<?	if($c_insert and !empty($r_key)) { ?>
	$("#be_add").click(function() {
		location.href = "<?= Route::navAddress(Route::thisPage()) ?>";
	});
	<?	} ?>
	<?	if(!empty($r_key)) { ?>
	$("#be_cek").click(function() {
		document.getElementById("act").value = "check";
		goSubmit();
	});
	<?	} ?>
	<?	if($c_edit and $jmltagihan > 0) { ?>
	$("#be_save").click(function() {
		if(totaltagihan > 0 && totalbayar > 0) {
			document.getElementById("act").value = "save";
			goSubmit();
		}
		else
			alert("Silahkan pilih tagihan dan pastikan total bayarnya lebih dari 0");
	});
	<?	} ?>
	<?	if($c_delete and !empty($r_key)) { ?>
	$("#be_delete").click(function() {
		var hapus = confirm("Apakah anda yakin akan membatalkan tagihan VA ini?\nPERHATIAN: Tagihan yang dibatalkan tidak bisa dibayar lagi.");
		if(hapus) {
			document.getElementById("act").value = "delete";
			goSubmit();
		}
	});
	<?	} ?>
	<?	if($c_edit) { ?>
	$("#idtagihan_all").click(function() {
		$("[name='idtagihan[]']").prop("checked",$(this).prop("checked")).change();
	});
	$("[name='idtagihan[]']").change(function() {
		goSum();
	});
	goSum();
	<?	} ?>
});

<?	if($c_edit) { ?>
function goSum() {
	var i, tr;

	var total = new Array();
	for(i=5;i<=8;i++)
		total[i] = 0;

	totaltagihan = 0;
	$("[name='idtagihan[]']:checked").each(function() {
		tr = $(this).parents("tr:eq(0)");

		if($(this).val().substr(0,1) != "D")
			totaltagihan++;

		for(i=5;i<=8;i++)
			total[i] += formatNumber(tr.children("td:eq(" + i + ")").text());
	});

	<?	if(!empty($t_admin)) { ?>
	total[8] += <?php echo $t_admin ?>;
	<?	} ?>

	totalbayar = total[8];

	for(i=5;i<=8;i++)
		$("#span_total_" + i).html(formatStr(total[i]));
}
<?	} ?>

</script>

</body>
</html>