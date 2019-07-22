<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
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
	
	// variabel request
	//$r_periode = Modul::setRequest($_POST['periode'],'PERIODE');
	$r_jenistagihan = Modul::setRequest($_POST['jenistagihan'],'JENISTAGIHAN');
	$r_jenisperiode = Modul::setRequest($_POST['jenisperiode'],'JENIS PERIODE');
	
	// combo
	//$l_periode = uCombo::periode($conn,$r_periode,'periode','',true);
	$l_jenistagihan = uCombo::jenistagihan($conn,$r_jenistagihan,'jenistagihan','',true);
		
	// properti halaman
	$p_title = 'Form Pembayaran Rutin';
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
	
	if($r_jenistagihan <> '')
		{
			if($settingdetail[$r_jenistagihan]['allow_inquiry']<>'1')
			{
				$p_postmsg = " Maaf, Proses Inquiry sementara ditutup. Silahkan hubungi admin keuangan";
				$p_posterr = true;
				$c_inquiry = false;
				}
			
			if($settingdetail[$r_jenistagihan]['allow_payment']<>'1')
			{
			$p_postmsg = " Maaf, Proses Payment sementara ditutup. Silahkan hubungi admin keuangan";
			$p_posterr = true;
			$c_payment = false;
			}
	if($settingdetail[$r_jenistagihan]['allow_reversal']<>'1')
			{
			$c_reversal = false;
			}
	}
	
	if($r_act == 'inquiry' and $r_nim){
		
			$mhs = mAkademik::getDatamhs($conn,$r_nim);
			
			if(!$mhs)
				$mhs = mAkademik::getDatapendaftar($conn,$r_nim);
			
			if(!$mhs)
			{
				$p_postmsg = " Maaf, ".$r_nim." tidak tercatat baik di pendaftaran maupun di database mahasiswa";
				$p_posterr = true;
				$c_inquiry = false;
				}
				
			if($c_inquiry and $mhs)
			{
				$arr_tagihan = mTagihan::getInquiry($conn,$r_nim,$r_jenisperiode,$r_jenistagihan);
				
			if(!$arr_tagihan)
			{
				$p_postmsg = " Tidak Ada Tagihan Belum Terbayar";
				$p_posterr = true;
				$c_inquiry = false;
				}
			}
			$log = array();
  			$log['jenistagihan'] = $r_jenistagihan;
			$log['nim'] = $r_nim;
  			$log['ish2h'] = '0';
  			$log['typeinq'] = 'inq';
			$log['periode'] = substr($r_jenisperiode,0,6);
			$log['ket'] = $p_postmsg;
			 
			$err = mTranslog::insertRecord($conn,$log);
		}
	else
		if($r_act == 'payment' and $r_nim){
			
			$mhs = mAkademik::getDatamhs($conn,$r_nim);
			$target = 'nim';
			if(!$mhs){
				$mhs = mAkademik::getDatapendaftar($conn,$r_nim);
				$target = 'nopendaftar';
			}
			
			if($c_payment)
			{
				$conn->BeginTrans();
				$record = array();
				$record['idpembayaran'] = (mPembayaran::idmaks($conn))+1;
				$record['tglbayar'] = date('Y-m-d');
				$record['jumlahbayar'] = $_POST['jumlahtotal'];
				$record['jumlahuang'] = str_replace('.','',$_POST['jumlahbayar']);
				$record['ish2h'] = 0;
				$record['nip'] = $_SESSION[SITE_ID]['MODUL']['USERNAME'];
				do{
				$record['refno'] = mAkademik::random(10);
				$cek = mPembayaran::cekRefno($conn,$record['refno']);
				}
				while(!$cek);
				$record['periodebayar'] = $r_periode;
				$record[$target] = $r_nim;
				$idpembayaran = $record['idpembayaran'];
				
				$err = mPembayaran::insertRecord($conn,$record);
				
				$r_tagihan = $_POST['tagihan'];
				if($r_tagihan)
					foreach($r_tagihan as $i => $val){
							$rec = array();
							$rec['idtagihan'] = $val;
							$rec['nominalbayar'] = str_replace('.','',$_POST[$val]);
							$rec['idpembayaran'] = $idpembayaran;
							$err = mPembayarandetail::insertRecord($conn,$rec);
							
							$rec = array();
							$rec['flaglunas'] = 'L';
							$err = mTagihan::updateRecord($conn,$rec,$val);
							
						}
						
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
					$datadetail = mPembayarandetail::getDatapembayaran($conn,$record['idpembayaran']);
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
			$mhs = mAkademik::getDatamhs($conn,$r_nim);
			
			if(!$mhs)
				$mhs = mAkademik::getDatapendaftar($conn,$r_nim);
			
			if($c_reversal)
				{
					$conn->BeginTrans();
					$r_id = $_POST['idpembayaran'];
					if($r_id){
							$err = mPembayaran::getReversal($conn,$r_id);
							
							$err = mTagihan::updateReversal($conn,$r_id);
						}
					
					$conn->CommitTrans();
					
					if($err <> '0')
					{
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
			$datadetail = mPembayarandetail::getDatapembayaran($conn,$r_id);
			
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