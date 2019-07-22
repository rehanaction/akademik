<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$conn->debug = false ;
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$p_detailpage = "list_pembayaran";
	// include
	require_once(Route::getModelPath('translog'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getModelPath('pembayaran'));
	require_once(Route::getModelPath('pembayarandetail'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('settingh2hdetail'));
	require_once(Route::getModelPath('settingh2h'));
	require_once(Route::getModelPath('bank'));
	require_once(Route::getModelPath('pesertaseminar'));
	//require_once(Route::getModelPath('jenistagihan'));
	
	// variabel request
	//$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	//$r_jenistagihan = Modul::setRequest($_POST['jenistagihan'],'JENISTAGIHAN');
	$r_kelompok = Modul::setRequest($_POST['jenistagihan'],'KELOMPOKTAGIHAN');
	$r_jenisperiode = Modul::setRequest($_POST['jenisperiode'],'JENIS PERIODE');
	
	// periode ambil dari setting
	//$r_periode = mSettingh2hdetail::getPeriodeSekarang($conn,$r_jenistagihan);
	$r_periode = mSettingh2hdetail::getPeriodeSekarang($conn,$r_kelompok);
	//if(empty($r_periode))
		//$r_periode = mAkademik::getPeriodeSekarang($conn);
	
	// combo
	//$l_periode = uCombo::periode($conn,$r_periode,'periode','',true);
	//$l_jenistagihan = uCombo::jenistagihan($conn,$r_jenistagihan,'jenistagihan','',true);
	//$l_jenistagihan = uCombo::kelompoktagihan($conn,$r_kelompok,'jenistagihan','',true);
	
	$a_bank = mBank::arrQuery($conn);
		
	// properti halaman
	$p_title = 'Form Pembayaran';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Transaksi';
	
	$p_model = mTagihan;
	$p_key = $p_model::key;
	$p_colnum = count($p_kolom)+1;
	
	$r_act = $_POST['act'];
	$r_nim = $_POST['nim'];
	
	$c_inquiry = $c_edit;
	$c_payment = $c_edit;
	$c_reversal = $c_edit;
	
	$setting = mSettingh2h::getDataSetting($conn);
	$settingdetail = mSettingh2hdetail::getDataSetting($conn);
	
 
	/* if($r_jenistagihan <> '')
		{
			$r_kelompok = mJenistagihan::getKodeKelompok($conn,$r_jenistagihan); */
	if($r_kelompok <> '')
		{
			if($settingdetail[$r_kelompok]['allow_inquiry']<>'1')
			{
				$p_postmsg = " Maaf, Proses Inquiry sementara ditutup. Silahkan hubungi admin keuangan";
				$p_posterr = true;
				$c_inquiry = false;
				}
			
			if($settingdetail[$r_kelompok]['allow_payment']<>'1')
			{
			$p_postmsg = " Maaf, Proses Payment sementara ditutup. Silahkan hubungi admin keuangan";
			$p_posterr = true;
			$c_payment = false;
			}
	if($settingdetail[$r_kelompok]['allow_reversal']<>'1')
			{
			$c_reversal = false;
			}
	}
	
	if($r_act == 'inquiry' and $r_nim){
			$mhs = mPesertaSeminar::getDataPeserta($conn,$r_nim);
			
			if(empty($mhs))
			{
				$p_postmsg = " Maaf, ".$r_nim." tidak tercatat di peserta seminar ";
				$p_posterr = true;
				$c_inquiry = false;
			}
				
			if($c_inquiry and $mhs)
			{
				// $arr_tagihan = mTagihan::getInquiry($conn,$r_nim,$r_jenisperiode,$r_jenistagihan);
				$arr_tagihan = mTagihan::getInquiry($conn,$r_nim,$r_kelompok);
				
			if(!$arr_tagihan)
			{
				$p_postmsg = " Tidak Ada Tagihan Belum Terbayar";
				$p_posterr = true;
				$c_inquiry = false;
				}
			}
			$log = array();
  			//$log['jenistagihan'] = $r_jenistagihan;
			$log['nopeserta'] = $r_nim;
  			$log['ish2h'] = '0';
  			$log['typeinq'] = 'inq';
			$log['periode'] = $r_periode;
			$log['ket'] = $p_postmsg;
			 
			$err = mTranslog::insertRecord($conn,$log);
		}
	else
		if($r_act == 'payment' and $r_nim){
			$mhs = mPesertaSeminar::getDataPeserta($conn,$r_nim);
			
			if($c_payment and $mhs)
			{
				$t_tgl = CStr::formatDate($_POST['tglbayar']);
				if(empty($t_tgl))
					$t_tgl = date('Y-m-d');
				
				$conn->BeginTrans();
				$record = array();
				// $record['idpembayaran'] = (mPembayaran::idmaks($conn))+1;
				$record['tglbayar'] = $t_tgl;
				$record['jumlahbayar'] = $_POST['jumlahtotal'];
				$record['jumlahuang'] = str_replace('.','',$_POST['jumlahbayar']);
				$record['ish2h'] = 0;
				$record['companycode'] = CStr::cStrNull($_POST['kodebank']);
				$record['nip'] = $_SESSION[SITE_ID]['MODUL']['USERNAME'];
				/*
				do{
				$record['refno'] = mAkademik::random(10);
				$cek = mPembayaran::cekRefno($conn,$record['refno']);
				}
				
				while(!$cek);*/
				foreach($_POST['tagihan'] as $i => $v)
					$record['idtagihan'] = $v;
				$record['periodebayar'] = $r_periode;
				$record['nokuitansi'] = mPembayaran::getNoBSM($conn,substr($t_tgl,0,4).substr($t_tgl,5,2));
				$record[$target] = $r_nim;
				
				$err = mPembayaran::insertRecord($conn,$record);
				
				$idpembayaran = mPembayaran::idmaks($conn);
				$record['idpembayaran'] = $idpembayaran;
				
				$conn->CommitTrans();
				if($err <> '0')
				{
					$p_postmsg = " Gagal melakukan Pembayaran";
					$p_posterr = true;
					$c_inquiry = false;
				}
				else
				{
					$data = mPembayaran::getDatapembayaran($conn,$record['idpembayaran']);
					$datadetail = mPembayaran::getDatapembayaran($conn,$record['idpembayaran']);
					$style = 'style="background-position:right; background-repeat:no-repeat; background-image:url(images/lunas.jpg)"';
					}
			}
			
			
			$log = array();
  			$log['jenistagihan'] = '';
			$log['nim'] = $r_nim;
  			$log['ish2h'] = '0';
  			$log['typeinq'] = 'pay';
  			$log['refno'] = $record['refno'];
			$log['periode'] = $r_periode;
			$log['ket'] = $p_postmsg;
			 
			$err = mTranslog::insertRecord($conn,$log);
			}
	else
		if($r_act == 'reversal'){
			$mhs = mPesertaSeminar::getDataPeserta($conn,$r_nim);
			
			if($c_reversal and $mhs)
				{
					$conn->BeginTrans();
					$r_id = $_POST['idpembayaran'];
					if($r_id){
						//$idtagihan = mTagihan::getTagihanFromPembayaran($conn, $r_id);
							//$datatagihan = mTagihan::getData($conn, $idtagihan);
							$err = mPembayaran::getReversal($conn,$r_id);
							
							// $err = mTagihan::updateReversal($conn,$r_id); // sudah menggunakan trigger
							
							/* if ($datatagihan['jenistagihan']  == 'DOP' and $err=='0' and $settingdetail['DOP']['periodesekarang'] == $datatagihan['periode']){
								
								$record['prasyaratspp']='0';
								$record['statusmhs']='T';
								$err = mAkademik::updateRecordPerwalian($conn, $record, " periode = '".$settingdetail['DOP']['periodesekarang']."' and nim = '".$datatagihan['nim']."'");
								
								if (!$err){
									
									$err = mAkademik::updateStatusMhs($conn, $record, $datatagihan['nim']);
									
									}
								
								}
							*/
						}
					$conn->CommitTrans();
					
					if($err <> '0')
					{
						//update perwalian set tidak aktif, mahasiswa juga
						$p_postmsg = " Gagal melakukan Pembatalan Pembayaran";
						$p_posterr = true;
						$c_inquiry = false;
						$style = 'style="background-position:right; background-repeat:no-repeat; background-image:url(images/lunas.jpg)"';
					}
					else
					{
						$style = 'style="background-position:right; background-repeat:no-repeat; background-image:url(images/void.jpg)"';
						}
				}
					
			$data = mPembayaran::getDatapembayaran($conn,$r_id);
			$datadetail = mPembayaran::getDatapembayaran($conn,$r_id);
			
			$log = array();
  			$log['jenistagihan'] = '';
			$log['nim'] = $r_nim;
  			$log['ish2h'] = '0';
  			$log['typeinq'] = 'rev';
  			$log['refno'] = $data['refno'];
			$log['periode'] = $r_periode;
			$log['ket'] = $p_postmsg;
			 
			$err = mTranslog::insertRecord($conn,$log);
			
			}
	
	
	require_once($conf['view_dir'].'v_list_pembayaran.php');
?>
