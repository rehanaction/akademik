<?php

	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_edit = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	$p_detailpage = "list_pembayaranfrm";
	// include
	require_once(Route::getModelPath('translog'));
	require_once(Route::getModelPath('akademik'));
	require_once(Route::getModelPath('pembayaranfrm'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getModelPath('tarifformulir'));
	require_once(Route::getModelPath('settingh2hdetail'));
	
		
	// properti halaman
	$p_title = 'Form Pembayaran Formulir';
	$p_tbwidth = '100%';
	$p_aktivitas = 'Transaksi';

	$p_colnum = count($p_kolom)+1;
	
	$r_act = $_POST['act'];
	$r_kodefrm = $_POST['kodeformulir'];
	
	$c_inquiry = $c_edit;
	$c_payment = $c_edit;
	$c_reversal = $c_edit;
	
	$settingdetail = mSettingh2hdetail::getDataSetting($conn);
	if($settingdetail['01']['allow_inquiry']<>'1')
		{
			$p_postmsg = " Maaf, Proses Inquiry sementara ditutup. Silahkan hubungi admin keuangan";
			$p_posterr = true;
			$c_inquiry = false;
			}
	if($settingdetail['01']['allow_payment']<>'1')
		{
			$p_postmsg = " Maaf, Proses Payment sementara ditutup. Silahkan hubungi admin keuangan";
			$p_posterr = true;
			$c_payment = false;
			}
	if($settingdetail['01']['allow_reversal']<>'1')
		{
			$c_reversal = false;
			}
	
	if($r_act == 'inquiry' and $r_kodefrm and $c_inquiry){
		
			$mhs = mAkademik::getDatamhs($conn,$r_nim);
			$tarif = mTarifformulir::getTarifbykode($conn,$r_kodefrm,true);
			if(!$tarif)
			{
				$p_postmsg = " Maaf, Tarif Formulir dengan kode tersebut tidak ada";
				$p_posterr = true;
			}
			elseif($tarif['periodedaftar'] <> $settingdetail['01']['periodesekarang'])
			{
				$p_postmsg = " Maaf,Kode Formulir itu tidak berlaku untuk saat ini";
				$p_posterr = true;
				$c_payment = false;
			}
			
			$log = array();
  			$log['jenistagihan'] = '01';
			$log['nim'] = $r_nim;
  			$log['ish2h'] = '0';
  			$log['typeinq'] = 'inq';
			$log['periode'] = $tarif['periodedaftar'];
			$log['ket'] = $p_postmsg;
			 
			$err = mTranslog::insertRecord($conn,$log);
		}
	else
		if($r_act == 'payment' and $r_kodefrm and $c_payment){
			$tarif = mTarifformulir::getTarifbykode($conn,$r_kodefrm,true);
			if($tarif) {
				$record = array();
				$record['tglbayar'] = date('Y-m-d');
				$record['jumlahbayar'] = $tarif['nominaltarif'];
				$record['nip'] = $_SESSION[SITE_ID]['MODUL']['USERNAME'];
				$record['ish2h'] = 0;
				$record['keterangan'] = $_POST['catatan'];
				
				do{
					$record['refno'] = mAkademik::random(10);
					$cek = mPembayaranfrm::cekRefno($conn,$record['refno']);
					}
				while(!$cek);
				
				do{
					$record['notoken'] = mAkademik::random(10);
					$cek = mPembayaranfrm::cekToken($conn,$record['notoken']);
					}
				while(!$cek);
				
				$record['periodebayar'] = $r_periode;
				$record['idtariffrm'] = $tarif['idtariffrm'];
				$idpembayaran = $record['idpembayaran'];
				$catatan = $record['keterangan'];
				$refno = $record['refno'];
				$token = $record['notoken'];
				$err = mPembayaranfrm::insertRecord($conn,$record);
				
				if($err == 0)
				{
					$p_postmsg = " Pembelian Token Formulir Berhasil";
					$p_posterr = false;
				}
				else{
					$p_postmsg = " Pembelian Token Formulir gagal";
					$p_posterr = true;
				}
				
				$log = array();
				$log['jenistagihan'] = '01';
				$log['nim'] = $r_nim;
				$log['ish2h'] = '0';
				$log['typeinq'] = 'pay';
				$log['refno'] = $record['refno'];
				$log['periode'] = $tarif['periodedaftar'];
				$log['ket'] = $p_postmsg;
				 
				$err = mTranslog::insertRecord($conn,$log);
				$style = 'style="background-position:right; background-repeat:no-repeat; background-image:url(images/lunas.jpg)"';
			}
		}
	else
		if($r_act == 'reversal' and $_POST['notoken'] and $c_reversal){
			
			$tarif = mPembayaranfrm::getDatabytoken($conn,$_POST['notoken']);
			
			$refno = $tarif['refno'];
			$token = $tarif['notoken'];
			$catatan = $tarif['catatan'];
			
			$err = mPembayaranfrm::reversalPayment($conn,$_POST['notoken']);
			if($err == 0)
			{
				$p_postmsg = " Void Pembelian Token Formulir Berhasil";
				$p_posterr = false;
			}
			else{
				$p_postmsg = " Void Pembelian Token Formulir gagal";
				$p_posterr = true;
			}
			
			$log = array();
  			$log['jenistagihan'] = '01';
			$log['nim'] = $r_nim;
  			$log['ish2h'] = '0';
  			$log['typeinq'] = 'rev';
  			$log['refno'] = $_POST['refno'];
			$log['periode'] = $tarif['periodedaftar'];
			$log['ket'] = $p_postmsg;
			 
			$err = mTranslog::insertRecord($conn,$log);
			$style = 'style="background-position:right; background-repeat:no-repeat; background-image:url(images/void.jpg)"';
			}
			
	
	$arr_tarif = mTarifformulir::getArraytarif($conn,$settingdetail['01']['periodesekarang']);
	if($arr_tarif)
		foreach($arr_tarif as $i => $v){
			if(empty($v['isaktif']))
				continue;
			
			if($v['kodeformulir']<>'')
				$datatarif[$v['jalurpenerimaan'].'|'.$v['idgelombang'].'|'.$v['sistemkuliah'].'|'.$v['programpend']][$v['jumlahpilihan']] = $v;
			if($v['kodeformulir']<>'')
				$data[$v['jalurpenerimaan'].'|'.$v['idgelombang'].'|'.$v['sistemkuliah'].'|'.$v['programpend']] = $v;
			}
	
	require_once($conf['view_dir'].'v_list_pembayaranfrm.php');
?>
