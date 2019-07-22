<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// tanpa debug
	$conn->debug = false;
	
	// clean buffer
	ob_clean();

	require_once(Route::getUIPath('combo'));

	$act = CStr::removeSpecial($_REQUEST['act']);
	$q = $_REQUEST['q'];
	$f = $_REQUEST['f'];
	
	// filtering
	if(is_array($q)) {
		for($i=0;$i<count($q);$i++)
			$q[$i] = CStr::removeSpecial($q[$i]);
	}
	else
		$q = CStr::removeSpecial($q);
	
	if ($act=='updatejadwal') {
		require_once(Route::getModelPath('jadwalseminar'));

		$record=array();
		$record['idjadwalseminar']=$q[0];
		$record['tgljadwal']=date('Y-m-d',strtotime($q[1]));
		$record['koderuang']=$q[2];				
		$record['jammulai']=CStr::cStrNull(str_replace(':','',$q[3]));
		$record['jamselesai']=CStr::cStrNull(str_replace(':','',$q[4]));

		//require_once('data_detailjadwalseminar.php');
		list($p_posterr,$p_postmsg) = mJadwalSeminar::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo '<div class="DivError">'.$p_postmsg.'</div>';
		}else{
			echo '<div class="DivSuccess">'.$p_postmsg.'</div>';
		}
		
		$r_key=$q[0];
		$c_insert=$q[5];
		$c_edit=$q[6];
		$c_delete=$q[7];
		
		require_once('data_detailjadwalseminar.php');
	}

	else if($act == 'insertjadwal') {
		
		require_once(Route::getModelPath('jadwalseminar'));
		
		$record=array();
		$record['idseminar']=$q[0];
		$record['tgljadwal']=date('Y-m-d',strtotime($q[1]));
		$record['koderuang']=$q[2];		
		$record['jammulai']=CStr::cStrNull(str_replace(':','',$q[3]));
		$record['jamselesai']=CStr::cStrNull(str_replace(':','',$q[4]));

		
		$row = mJadwalSeminar::getDataJadwal($conn,$record['tgljadwal'],$record['koderuang']);

		if (!empty($row)) {
			echo '<div class="DivError"> Jadwal Sudah di Pakai </div>';

		} else {
			list($p_posterr,$p_postmsg) = mJadwalSeminar::insertCRecord($conn,$kolom,$record,$q[0]);

			if($p_posterr){
				echo '<div class="DivError">'.$p_postmsg.'</div>';
			}else{
				echo '<div class="DivSuccess">'.$p_postmsg.'</div>';
			}
		}	
		$r_key=$q[0];
		$c_insert=$q[5];
		$c_edit=$q[6];
		$c_delete=$q[7];
		
		require_once('data_detailjadwalseminar.php');
	}
	else if($act == 'delDetailKelas') {
		require_once(Route::getModelPath('jadwalseminar'));
		//$conn->debug=true;
		//print_r($q);

		list($p_posterr,$p_postmsg) = mJadwalSeminar::delete($conn,$q[0]);
		
		if($p_posterr){
			echo 'error7*';
		}
		
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		
		require_once('data_detailjadwalseminar.php');
	}
	else if($f == 'acmahasiswa') {
		
		require_once(Route::getModelPath('mahasiswa'));
		
		$a_data = mMahasiswa::find($conn,$q,"nim||' - '||nama",'nim');
		
		echo json_encode($a_data);
	}
	else if($f == 'acpegawai') {
		require_once(Route::getModelPath('pegawai'));

		//yang keluar di autocompletenya id - nama ( || - || )
		$a_data = mPegawai::find($conn,$q,"nik::text||' - '||akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)",'idpegawai');

		echo json_encode($a_data);
	}

	else if($act=='token') {
		require_once(Route::getModelPath('pendaftar'));
		require_once(Route::getModelPath('token'));
		$token = CStr::removeSpecial($_REQUEST['token']);
			
		$result=mToken::getTokenDetail($token);
			
		echo json_encode($result);
	}
	else if($act == 'getDetailSmu') {
		$idsmu = $q[0];		
		$hasil = $conn->GetRow("select alamatsmu,telpsmu from pendaftaran.lv_smu where idsmu='$idsmu'");
		 
		echo $hasil['alamatsmu']."#".$hasil['telpsmu'];
		// return $kuota;
	}else if($act == 'getKuota') {
		$id = $q[0];		
		$hasil = $conn->GetRow("select coalesce(jumlahpeserta,0) as jumlahpeserta,kuota from pendaftaran.pd_jadwal where idjadwal='$id'");
		 
		echo $hasil['jumlahpeserta']."#".$hasil['kuota'];
		
	}else if($act == 'getKotaLahir') {
		require_once(Route::getModelPath('kota'));
		$a_data = mKota::find($conn,$q,"kodekota||' - '||namakota","kodekota");
		
		echo json_encode($a_data);
	}else if($act == 'inpendformal') {
		require_once(Route::getModelPath('pendformal'));
		$record=array();
		$record['nopendaftar']=$q[0];
		$record['namapend']=$q[1];
		$record['tempatpend']=$q[2];
		$record['tahunmasuk']=$q[3];
		$record['tahunlulus']=$q[4];
		list($p_posterr,$p_postmsg) = mPendFormal::insertCRecord($conn,$kolom,$record,$kosong);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[0];
		$c_insert=$q[5];
		$c_edit=$q[6];
		$c_delete=$q[7];
		require_once('data_pendformal.php');
	}else if($act == 'uppendformal') {
		require_once(Route::getModelPath('pendformal'));
		$record=array();
		$record['namapend']=$q[2];
		$record['tempatpend']=$q[3];
		$record['tahunmasuk']=$q[4];
		$record['tahunlulus']=$q[5];
		list($p_posterr,$p_postmsg) = mPendFormal::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[6];
		$c_edit=$q[7];
		$c_delete=$q[8];
		require_once('data_pendformal.php');
	}else if($act == 'delpendformal') {
		require_once(Route::getModelPath('pendformal'));
		
		list($p_posterr,$p_postmsg) = mPendFormal::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_pendformal.php');
	}else if($act == 'inpendNonformal') {
		require_once(Route::getModelPath('pendnonformal'));
		$record=array();
		$record['nopendaftar']=$q[0];
		$record['namapelatihan']=$q[1];
		$record['tingkatpelatihan']=$q[2];
		$record['tahun']=$q[3];
		
		list($p_posterr,$p_postmsg) = mPendNonFormal::insertCRecord($conn,$kolom,$record,$kosong);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[0];
		$c_insert=$q[4];
		$c_edit=$q[5];
		$c_delete=$q[6];
		require_once('data_pendnonformal.php');
	}else if($act == 'uppendNonformal') {
		require_once(Route::getModelPath('pendnonformal'));
		$record=array();
		$record['namapelatihan']=$q[2];
		$record['tingkatpelatihan']=$q[3];
		$record['tahun']=$q[4];
		
		list($p_posterr,$p_postmsg) = mPendNonFormal::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[5];
		$c_edit=$q[6];
		$c_delete=$q[7];
		require_once('data_pendnonformal.php');
	}else if($act == 'delpendNonformal') {
		require_once(Route::getModelPath('pendnonformal'));
		
		list($p_posterr,$p_postmsg) = mPendNonFormal::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_pendnonformal.php');
	}else if($act == 'inOrganisasi') {
		require_once(Route::getModelPath('organisasi'));
		$record=array();
		$record['nopendaftar']=$q[0];
		$record['namaorganisasi']=$q[1];
		$record['jabatan']=$q[2];
		$record['tahun']=$q[3];
		
		list($p_posterr,$p_postmsg) = mOrganisasi::insertCRecord($conn,$kolom,$record,$kosong);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[0];
		$c_insert=$q[4];
		$c_edit=$q[5];
		$c_delete=$q[6];
		require_once('data_organisasi.php');
	}else if($act == 'upOrganisasi') {
		require_once(Route::getModelPath('organisasi'));
		$record=array();
		$record['namaorganisasi']=$q[2];
		$record['jabatan']=$q[3];
		$record['tahun']=$q[4];
		
		list($p_posterr,$p_postmsg) = mOrganisasi::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[5];
		$c_edit=$q[6];
		$c_delete=$q[7];
		require_once('data_organisasi.php');
	}else if($act == 'delOrganisasi') {
		require_once(Route::getModelPath('organisasi'));
		
		list($p_posterr,$p_postmsg) = mOrganisasi::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_organisasi.php');
	}else if($act == 'inPrestasiAkad') {
		require_once(Route::getModelPath('prestasiakad'));
		$record=array();
		$record['nopendaftar']=$q[0];
		$record['namaprestasi']=$q[1];
		$record['juara']=$q[2];
		$record['tingkat']=$q[3];
		$record['tahun']=$q[4];
		
		list($p_posterr,$p_postmsg) = mPrestasiAkad::insertCRecord($conn,$kolom,$record,$kosong);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[0];
		$c_insert=$q[5];
		$c_edit=$q[6];
		$c_delete=$q[7];
		require_once('data_prestasiakad.php');
	}else if($act == 'upPrestasiAkad') {
		require_once(Route::getModelPath('prestasiakad'));
		$record=array();
		$record['namaprestasi']=$q[2];
		$record['juara']=$q[3];
		$record['tingkat']=$q[4];
		$record['tahun']=$q[5];
		
		list($p_posterr,$p_postmsg) = mPrestasiAkad::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[6];
		$c_edit=$q[7];
		$c_delete=$q[8];
		require_once('data_prestasiakad.php');
	}else if($act == 'delPrestasiAkad') {
		require_once(Route::getModelPath('prestasiakad'));
		
		list($p_posterr,$p_postmsg) = mPrestasiAkad::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_prestasiakad.php');
	}else if($act == 'inPrestasiNonAkad') {
		require_once(Route::getModelPath('prestasinonakad'));
		$record=array();
		$record['nopendaftar']=$q[0];
		$record['namaprestasi']=$q[1];
		$record['juara']=$q[2];
		$record['tingkat']=$q[3];
		$record['tahun']=$q[4];
		
		list($p_posterr,$p_postmsg) = mPrestasiNonAkad::insertCRecord($conn,$kolom,$record,$kosong);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[0];
		$c_insert=$q[5];
		$c_edit=$q[6];
		$c_delete=$q[7];
		require_once('data_prestasinonakad.php');
	}else if($act == 'upPrestasiNonAkad') {
		require_once(Route::getModelPath('prestasinonakad'));
		$record=array();
		$record['namaprestasi']=$q[2];
		$record['juara']=$q[3];
		$record['tingkat']=$q[4];
		$record['tahun']=$q[5];
		
		list($p_posterr,$p_postmsg) = mPrestasiNonAkad::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[6];
		$c_edit=$q[7];
		$c_delete=$q[8];
		require_once('data_prestasinonakad.php');
	}else if($act == 'delPrestasiNonAkad') {
		require_once(Route::getModelPath('prestasinonakad'));
		
		list($p_posterr,$p_postmsg) = mPrestasiNonAkad::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_prestasinonakad.php');
	}else if($act == 'inSaudaraKandung') {
		require_once(Route::getModelPath('saudarakandung'));
		
		$record=array();
		$record['nopendaftar']=$q[0];
		$record['namasaudara']=$q[1];
		$record['kodepropinsisaudara']=$q[2];
		$record['kodekotasaudara']=$q[3];
		$record['tgllahirsaudara']=!empty($q[4])?date('Y-m-d',strtotime($q[4])):null;
		$record['kodependidikan']=$q[5];
		$record['status']=$q[6];
		
		list($p_posterr,$p_postmsg) = mSaudaraKandung::insertCRecord($conn,$kolom,$record,$kosong);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[0];
		$c_insert=$q[7];
		$c_edit=$q[8];
		$c_delete=$q[9];
		require_once('data_saudarakandung.php');
	}else if($act == 'upSaudaraKandung') {
		require_once(Route::getModelPath('saudarakandung'));
		$record=array();
		$record['namasaudara']=$q[2];
		$record['kodepropinsisaudara']=$q[3];
		$record['kodekotasaudara']=$q[4];
		$record['tgllahirsaudara']=!empty($q[5])?date('Y-m-d',strtotime($q[5])):null;
		$record['kodependidikan']=$q[6];
		$record['status']=$q[7];
		
		list($p_posterr,$p_postmsg) = mSaudaraKandung::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[8];
		$c_edit=$q[9];
		$c_delete=$q[10];
		require_once('data_saudarakandung.php');
	}else if($act == 'delSaudaraKandung') {
		require_once(Route::getModelPath('saudarakandung'));
		
		list($p_posterr,$p_postmsg) = mSaudaraKandung::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_saudarakandung.php');
	}else if($f == 'optkota') {
		
		if(empty($q[0])) {
			$a_kota = array();
			
			echo UI::createOption($a_kota,'',true,'-- Pilih Propinsi terlebih dahulu --');
		}
		else {
			$a_kota = mCombo::getArrkota($conn,$q[0]);
			$t_kota = $q[1];
			
			echo UI::createOption($a_kota,$t_kota);
		}
	}else if($f == 'optsmu') {
		if(empty($q[0])) {
			$a_smu = array();
			
			echo UI::createOption($a_kota,'',true,'-- Pilih Kota terlebih dahulu --');
		}
		else {
			$a_smu = mCombo::getArrsmu($conn,$q[0]);
			$t_kota = $q[1];
			
			echo UI::createOption($a_smu,$t_kota);
		}
	}
	else if($f == 'acpendaftar') {
		require_once(Route::getModelPath('pendaftar'));
		
		$a_data = mPendaftar::find($conn,$q,"nopendaftar||' - '||nama",'nopendaftar');
		
		echo json_encode($a_data);
	} 	
	else if ($f=='getSmu'){ 
		 
		require_once(Route::getModelPath('smu'));
		
		$a_data = mSmu::findSmu($conn,$q,"m.namasmu||' - '||t.namakota",'idsmu');
		
		echo json_encode($a_data);	
	}
	else if($f == 'optseminar') {
		require_once(Route::getModelPath('seminar'));
		
		$a_data = mSeminar::arrQuery($conn,(empty($q) ? '' : 'periode = '.Query::escape($q)));
		
		echo json_encode($a_data);
	}
	else if($f == 'getkegiatanchild') {
		require_once(Route::getModelPath('strukturkegiatan','kemahasiswaan'));

		if(empty($q[0])) {
			$a_kegiatan = array();

			echo UI::createOption($a_kegiatan,'',true,'-- Pilih Kegiatan Terlebih Dahulu --');
		}
		else {
			$a_kegiatan = mStrukturKegiatan::getByParent($conn,$q[0]);
			$t_kegiatan = $q[1];

			echo UI::createOption($a_kegiatan,$t_kegiatan);
		}
	}
?>
