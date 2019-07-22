<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	 
	
	// include model
	require_once(Route::getModelPath('seminar','front'));
	require_once(Route::getModelPath('alasanseminar'));

	// ambil user login
	$r_key = $_REQUEST['key'];
	if(empty($r_key)) {
		$a_flash = array();
		$a_flash['posterr'] = true;
		$a_flash['postmsg'] = 'Harap pilih seminar yang dimaksud terlebih dahulu';
		
		Route::setFlashData($a_flash,(empty($_REQUEST['back']) ? 'home' : 'list_jadwal'));
	}
	
	// properti halaman
	$p_title = 'Informasi Seminar';
	$p_model = mSeminarFront;
	
	// array input
	$a_input = $p_model::inputColumn($conn);
	//$a_inputd = $p_model::inputColumnJadwal($conn);

	// cek login
	$is_login = Seminar::isAuthenticated();		

	// mengambil data
	$row = $p_model::getData($conn,$r_key);
	
	// mengambil data alsan
	$r_alasan = mAlasanSeminar::getAlasan($conn);

	// cek peserta
	$nim = Seminar::getNIM();
	$nip = Seminar::getNIP();
	$nop = Seminar::getNoPendaftar();

	// cek apakah sudah mendaftar seminar ini
	$is_daftar = $p_model::getValidDaftar($conn,$r_key);

	// get jurusan peserta
	$prodi = $p_model::getProdiPeserta($conn,$r_key);
	$a_prodi = array();
	foreach ($prodi as $key => $value) {
		array_push($a_prodi,$value['kodeunit']);
	}

	// sisa kuota
	$a_kuota = $p_model::getSisaKuota($conn,$r_key);

	// semester string to array
	$a_semester = explode(',',$row['semmhs']);

	// sistemkuliah string to array
	$a_sistem = explode(',',$row['skuliah']);
	
	// get properti mhs 
	$properti = $p_model::getPropertiMhs($conn,$nim);
	$a_properti = array();

	foreach ($properti as $rows=> $value) {
		$a_properti['kodeunit'] = $value['kodeunit'] ;
		$a_properti['semester'] = $value['semestermhs'] ;
		$a_properti['skuliah'] = $value['sistemkuliah'] ;
	}
	
	if (!empty($nim)) {
		if (in_array($a_properti['kodeunit'], $a_prodi)) { 
			if (in_array($a_properti['semester'], $a_semester) or ($a_properti['semester'] > 8 and in_array(9,$a_semester))) {
				if (in_array($a_properti['skuliah'], $a_sistem)) {
					$allowmhs = true ;
				} else {
					$allowmhs = false ;
				}
			} else {
				$allowmhs = false ;
			}
		} else {
			$allowmhs = false ;
		}
	}

	// get jenis peserta dan jumlah pagu
	$a_peserta = explode(',',$row['typepeserta']);
	
	// cek is mhs or is pegawai or is umum
	if (empty($nim)) {
		if (empty($nip)) {
			$typepeserta = 'U' ;
			
			// pagu 
			if (!empty($row['paguumum'])) {
				$sisapagu = $row['paguumum'] - $a_kuota['jmlumum'];
			} else {
				$sisapagu = '100' ;
			}

		} else {
			$typepeserta = 'P' ;
			// pagu 
			if (!empty($row['pagupgw'])) {
				$sisapagu = $row['pagupgw'] - $a_kuota['jmlnip'];
			} else {
				$sisapagu = '100' ;
			}

		}
	} else { 
		if ($allowmhs) { 
			$typepeserta = 'M' ;
			
			// pagu 
			if (!empty($row['pagumhs'])) {
				$sisapagu = $row['pagumhs'] - $a_kuota['jmlnim'];
			} else {
				$sisapagu = '100' ;
			}

		}		
	}
	
	if(in_array($typepeserta, $a_peserta)) {
		$allowdaftar = true ;
	}

	// get pembicara
	$a_pembicara = $p_model::getPembicara($conn,$r_key);
	
	//cek semester
	if(!empty($row['semester'])) {
		$t_semester = explode(',',substr($row['semester'],1,strlen($row['semester'])-2));
		
		$n = count($t_semester);
		switch($n) {
			case 1: $t_semester = $t_semester[0]; break;
			case 2: $t_semester = implode(' dan ',$t_semester); break;
			default: $t_semester[$n-1] = 'dan '.$t_semester[$n-1]; $t_semester = implode(', ',$t_semester); break;
		}
		
		$row['namasemester'] = $t_semester;
	}
	if(!empty($rowbs)) {
		$t_basis = array();
		foreach($rowbs as $rowb)
			$t_basis[] = $rowb['namabasis'].(empty($rowb['iswajib']) ? '' : ': Wajib');
		
		$row['basis'] = implode('<br />',$t_basis);
	}
	
	// gambar
	$t_dir = __DIR__.'/../';
	$t_file = $conf['upload_dir'].'seminar/'.$row['idseminar'].'.jpg';
	if(is_readable($t_dir.$t_file))
		$p_image = $t_file;
	else
		$p_image = 'images/no-img.jpg';
	
	// cek tarif 
	if (empty($nim)) {
		if (empty($nip)){
			$tarif = $row['tarifseminaru'];
		} else {
			$tarif = $row['tarifseminarp'];
		}
	} else {
		$tarif = $row['tarifseminarm'];
	}

	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'daftar' and empty($is_daftar)) {
		
		$record = array();
		$record['idseminar'] = $r_key;
		$record['alasan'] = implode(',', $_POST['alasan']);
		
		$err = $p_model::insertRecordPeserta($conn,$record,$tarif);

		if(empty($err))
			$msg = 'Pendaftaran seminar berhasil, jika seminar berbayar silahkan melakukan pembayaran agar bisa mengikuti seminar. Anda bisa menghubungi pihak administrasi seminar untuk informasi lebih lanjut.';
		else
			$msg = 'Pendaftaran seminar gagal';

		$conn->CommitTrans(empty($err) ? true : false);
		
		
		if(empty($err)) {
			$a_flash = array();
			$a_flash['posterr'] = $err;
			$a_flash['postmsg'] = $msg;
			
			Route::setFlashData($a_flash,'list_jadwal');
		}
	}
	
	$today = date(Ymd);
	$akhir = $row['tglakhirdaftar'];

	if ($today <= str_replace('-','',$akhir)) {
		$daftartutup = false;
	} else {
		$daftartutup = true;
	}

	if (!in_array('M', $a_peserta)) {
		$displaym = 'style="display: none;"';
	} 

	if (!in_array('P', $a_peserta)) {
		$displayp = 'style="display: none;"';
	} 

	if (!in_array('U', $a_peserta)) {
		$displayu = 'style="display: none;"';
	}

	// include header
	require_once('inc_header.php');
?>
<div class="container bg-white">
	<div class="row">		
		<div class="col-md-8">
			<div class="col-md-12">
				<div class="page-header">
					<h3><?php echo $row['namaseminar'] ?></h3>
                    <hr/>
				</div>
				<? if(isset($err)) { ?>
				<div class="alert alert-<?= empty($err) ? 'success' : 'danger' ?>">
					<?= $msg ?>
				</div>
				<? } ?>
				<div class="row">
					<div class="col-md-11">
                        <img src="
						<?php if (empty($row['filepooster'])) {
							echo "index.php?page=img_datathumb&type=seminar&id=".$row['idseminar'];
						} else {
							echo "images/logo.png";
						}
						?>" width="100%">
                        <div class="clearfix"><br/></div>
						<div class="page-header custom-header">
							<h4><b>Informasi Seminar</b></h4>
						</div>

						<?php if(empty($is_login)) { ?> 
							<div class="alert alert-danger"> 
								Untuk mendaftar seminar, silahkan login terlebih dahulu di halaman 
								<a class="btn btn-primary btn-xs" href="<?php echo Route::navAddress('login_form') ?>">Login Peserta</a>.
							</div>
						<?php } ?>
						
						<table width="100%" cellpadding="4" cellspacing="2" class="table table-striped">
							<tr>
								<td>Tema</td>
								<td class="text-center">:</td>
								<td> <?= $row['namaseminar']?> </td>
							</tr>
							<tr>
								<td>Pendaftaran</td>
								<td class="text-center">:</td>
								<td> <?= CStr::formatDateInd($row['tglawaldaftar'] ).' s.d '. CStr::formatDateInd($row['tglakhirdaftar'])?></td>
							</tr>
							<tr>
								<td>Periode</td>
								<td class="text-center">:</td>
								<td><?= $row['periode']?></td>
							</tr>
							<tr>
								<td>Peserta</td>
								<td class="text-center">:</td>
								<td>
									<?php  
										foreach ($a_peserta as $rows) {
											if ($rows == 'M') {
												echo "Mahasiswa ";
											} else if ($rows == 'P') {
												echo ", Pegawai ";
											} else if ($rows == 'U') {
												echo ", Umum ";
											} else {
												echo " ";
											}
										}
									?>
								</td>
							</tr>

							<tr>								
								<td>Tarif Seminar</td>
								<td class="text-center">:</td>
								<!--
								<td>
									<ul style="list-style: none;padding: 0;">
										<li <?= $displaym ?> >
										Mahasiswa 
										<?=str_repeat('&nbsp;', 1);?>
										<span class="label label-success"> 
																							
												<? //empty($row['tarifseminarm'])&&($row['typepeserta']=='M') ? 'Gratis' : 'Rp '.$row['tarifseminarm']
												if (empty($row['tarifseminarm'])&&($row['typepeserta']=='M'))
													echo ('Gratis');
												else if (!empty($row['tarifseminarm'])&&($row['typepeserta']=='M'))
													echo ('Rp '.cStr::formatNumber($row['tarifseminarm']));
												else 
													echo(''); 
												
												?>
																								    
											</span>
										</li>

										<li <?= $displayp ?> >Pegawai   <?=str_repeat('&nbsp;', 7);?>
											<span class="label label-success"> 
												
												
												<? //empty($row['tarifseminarp'])&&($row['typepeserta']=='P') ? 'Gratis' : 'Rp '.$row['tarifseminarp']
													 
													 if (empty($row['tarifseminarp'])&&($row['typepeserta']=='P'))
														echo ('Gratis');
													else if (!empty($row['tarifseminarp'])&&($row['typepeserta']=='P'))
														echo ('Rp '.cStr::formatNumber($row['tarifseminarp']));
													else 
														echo('');
												?>
												
											</span> 
										</li>
										<li <?= $displayu ?> >Umum
										<?=str_repeat('&nbsp;', 10);?>
											<span class="label label-success"> 
												<? //empty($row['tarifseminaru'])&&($row['typepeserta']=='U') ? 'Gratis' : 'Rp '.$row['tarifseminaru'] 
												
													 if (empty($row['tarifseminaru'])&&($row['typepeserta']=='U'))
														echo ('Gratis');
													 else if (!empty($row['tarifseminaru'])&&($row['typepeserta']=='U'))
														echo ('Rp '.cStr::formatNumber($row['tarifseminaru']));
													 else 
														echo('');
																										
												?>
												
											</span> 
										</li>
									</ul>
								</td>
								-->
								<td>
									<ul style="list-style: none;padding: 0;">
										<li <?= $displaym ?>> Mahasiswa  
											<span class="label label-success"> 
												<?= empty($row['tarifseminarm']) ? 'Gratis' : 'Rp. '.cStr::formatNumber($row['tarifseminarm'])?>
											</span> 
										</li>

										<li <?= $displayp ?>> Pegawai 
											<span class="label label-success"> 
												<?= empty($row['tarifseminarp']) ? 'Gratis' : 'Rp. '.cStr::formatNumber($row['tarifseminarp']) ?>
											</span> 
										</li>
										<li <?= $displayu ?>> Umum
											<span class="label label-success"> 
												<?= empty($row['tarifseminaru']) ? 'Gratis' : 'Rp. '.cStr::formatNumber($row['tarifseminaru']) ?>
											</span> 
										</li>
									</ul>
								</td>
							</tr>
					
							<tr>
								<td>Pembicara Seminar</td>
								<td class="text-center">:</td>
								<td>
									<ul style="list-style: none;padding: 0;">
										<?php  
											foreach ($a_pembicara as $key => $value) { ?>	
												<li>
													<?= $value['idpembicara'];?>
												</li>
										<?php } ?>
									</ul>
								</td>
							</tr>
							
							<tr>
								<td>Batas Bayar</td>
								<td class="text-center">:</td>
								<td> <?= CStr::formatDateInd($row['batasbayar'])?> </td>
							</tr>
							<tr>
								<td>Referensi</td>
								<td class="text-center">:</td>
								<td> 
									<u class="ULink" onclick="goDownload('brosurseminar',<?= $row['idseminar']?>)"> <?= $row['filereferensi']?> </u> 
								</td>
							</tr>
							<tr>
								<td>Goal Seminar</td>
								<td class="text-center">:</td>
								<td> <?= $row['keterangan']?> </td>
							</tr>
						</table>
						<br/>

						<?php  
							if (!empty($is_login)) { ?>
							<div align="center">
								<?php if ($daftartutup) { ?>
									<button class="btn btn-info btn-lg disabled"><span class="glyphicon glyphicon-ok"></span> Pendaftaran Ditutup </button>
								<?php } else { 
											if ($allowdaftar) { ?>
												<?php  
													if (!$is_daftar) { 
														if ($sisapagu < 1) { ?>
															<button type="button" class="btn btn-info btn-lg disabled">Kuota Penuh</button>
														<?php } else { ?>
															<!--
															<button type="button" class="btn btn-success btn-lg" data-toggle="modal" data-target="#alasan">Daftar Seminar</button>
															-->
															<form role="form" method="post">
																<input type="hidden" name="act" value="daftar">																
																<button type="submit" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok"></span> Daftar Seminar</button>																
															</form>			          
														<?php }
													} else { ?>
														<button type="button" class="btn btn-info btn-lg disabled">Anda Sudah Mendaftar</button>
												<?php } ?>
									<?php   } else { ?>	
												<button type="button" class="btn btn-info btn-lg disabled">Anda Tidak Diperkenankan Mendaftar</button>
									<?php		
											}
										}
									} 
								if (!empty($is_login)) {
									echo '</div>' ;
								} else {
									echo '' ;
								}
							?>																		
					</div>
				</div>
				<br/>
				<hr/>
			</div>	
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	
		<!--modal-->
		<div class="modal fade" id="alasan" role="dialog">
		    <div class="modal-dialog">
		    
		      <!-- Modal content-->
		      <div class="modal-content">
		        <div class="modal-header">
		          <button type="button" class="close" data-dismiss="modal">&times;</button>
		          <h4 class="modal-title">Kenapa Kamu Mau mendaftar Seminar Ini ? </h4>
		        </div>
		        <form role="form" method="post">
			        <div class="modal-body">
		        		<?php  foreach ($r_alasan as $key => $value) { ?>			          	
			          		<input type="checkbox" name="alasan[]_" id="alasan[]_<?= $value['idalasan']?>" style="width:50px;" value="<?= $value['idalasan']?>"> <?= $value['alasan']?> <br>			          		
			          	<?php  } ?>
			          	<input type="hidden" name="act" value="daftar">		          	
			        </div>

			        <div class="modal-footer">
			          <button type="submit" class="btn btn-success btn-lg"><span class="glyphicon glyphicon-ok"></span> Daftar Seminar Ini</button>
			        </div>

		        </form>
		      </div>
		      
		    </div>
		  </div>
		
	</div>
</div>


