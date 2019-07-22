<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug=false;
	// hak akses
	$a_auth = Modul::getFileAuth();
	//$conn->debug=-true;
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	$c_buka = $a_auth['canother']['B'];

	$admin = Akademik::isAdmin();
	
	// include
	require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kelaspraktikum'));
	require_once(Route::getModelPath('krs'));
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getModelPath('perwalian'));
	require_once(Route::getModelPath('transkrip'));
	require_once(Route::getModelPath('periode'));
	require_once(Route::getModelPath('setting'));
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getModelPath('mengajar'));
	require_once(Route::getModelPath('akademikkeu'));
	require_once(Route::getModelPath('tagihan'));
	require_once(Route::getUIPath('combo'));
	
	// variabel request
	$r_act = $_POST['act'];
	if(!Akademik::isMhs()) {
		
		$display="block";
		if(empty($r_key)) {
			// cek aksi
			$r_nim = CStr::removeSpecial($_REQUEST['npm']);
			if(Akademik::isDosen()){
				$r_nip = Modul::getUserName();
				$display="none";
				}
			else
				$r_nip = '';
			
			if($r_act == 'first')
				$r_key = mMahasiswa::getFirstNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'prev')
				$r_key = mMahasiswa::getPrevNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'next')
				$r_key = mMahasiswa::getNextNIM($conn,$r_nim,$r_nip);
			else if($r_act == 'last')
				$r_key = mMahasiswa::getLastNIM($conn,$r_nim,$r_nip);
			else
				$r_key = $r_nim;
		}
	}
	else{
		
		$r_key = Modul::getUserName();
		$display="none";
	}
	
	$r_kurikulum = Akademik::getKurikulum();
	$r_periode = Akademik::getPeriode();
	$r_periodespa = Akademik::getPeriodeSpa();
	
	//$periodemasuk=$conn->GetOne("select substr(periodemasuk,1,4) from akademik.ms_mahasiswa where nim=''");
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	if(substr($a_infomhs['periodemasuk'],0,4)==substr($r_periodespa,0,4))
		$r_periode=Akademik::getPeriodeSpa();
	$r_keywali = $r_key.'|'.$r_periode;
	
	$r_periodelalu = mPeriode::getPeriodeLalu($r_periode);
	$r_keywalilalu = $r_key.'|'.$r_periodelalu;

	$a_infomhs['semesterkrs']=Akademik::getSemMhs($a_infomhs['periodemasuk'],$r_periode);
	
	// properti halaman
	$p_title = 'Kartu Rencana Studi (KRS)';
	$p_tbwidth = "100%";
	$p_lwidth = "100%";
	$p_aktivitas = 'ABSENSI';
	$p_model = mKRS;
	
	// cek periode dan isi biodata
	$a_postmsg = array();
	if(Akademik::getTahap() != 'KRS') {
		$p_sposterr = true;
		//$a_postmsg[] = 'Tahap Pendaftaran KRS Belum Dibuka';
                $a_postmsg[] = '' ;
	}
	
	$a_wali = mPerwalian::getData($conn,$r_keywali);
	$a_walilalu = mPerwalian::getData($conn,$r_keywalilalu);

	if(Akademik::isMhs() and empty($a_infomhs['biodataterisi'])) {
		$p_sposterr = true;
		$a_postmsg[] = '<b>Untuk Mengisi KRS, Biodata WAJIB dilengkapi, untuk mengisi Biodata klik <u class="ULink" onclick="goOpen(\'data_mahasiswa&self=1\')">di sini</u></b>';
	}
	
	/*if(Akademik::isMhs() and !mKrs::cekQuisioner($conn,$r_key,Akademik::getPrevPeriode($r_periode))){
		$p_sposterr = true;
		$a_postmsg[] = 'Quisioner '.Akademik::getNamaPeriode(Akademik::getPrevPeriode($r_periode)).' belum diisi lengkap, untuk mengisi klik <u class="ULink" onclick="goOpen(\'list_mkquiz\')">di sini</u>';
	}*/
	
	if($p_sposterr) {
		$c_insert = false;
		$c_update = true;
		$c_delete = false;
	}
	
	if($c_buka) {
		$c_insert = true;
		$c_delete = true;
	}

	if($admin) {
		$c_insert = true;
		$c_delete = true;
		$c_buka = true;
	}
	if(Akademik::IsBAA()){
		$c_insert = true;
		$c_delete = true;
		$c_buka = true;

	}

	

	
	
	// cek perwalian (atas)
	//$sql = mPerwalian::dataQuery($r_keywali);
	

	$tunggakan=(int)$a_wali['hutang']+(int)$a_wali['biaya']-(int)$a_wali['diskon'];
	if(empty($a_wali['prasyaratspp'])) {
		$c_insert = false;
		$c_update = false;
		$c_delete = false;
	}
	/*if(!empty($a_wali['frsdisetujui'])) {
		$c_insert = false;
		$c_delete = false;
	}*/
	
	// cek kelas
	$r_kelasmk = Modul::setRequest($_POST['kelasmk'],'KELASMK');
	$t_lintas = mSetting::getLintasKurikulum($conn);
	
	// $a_kelas = mKelas::getDataPeriode($conn,$r_periode,$a_infomhs['kurikulum'],$a_infomhs['kodeunit'],$t_lintas,$r_kelasmk,$a_infomhs);
	$a_kelas = mKelas::getDataPeriode($conn,$r_periode,$r_kurikulum,$a_infomhs['kodeunit'],$t_lintas,$r_kelasmk,$a_infomhs); // cek: ekivalensi
	
	if(!empty($t_postmsg))
		$a_postmsg[] = $t_postmsg;
	
	// ada aksi
	if($r_act == 'insert' and $c_insert) {
		$a_mkambil = array();
		if(!empty($_POST['mkambil'])) {
			foreach($_POST['mkambil'] as $t_key) {
				$t_key = CStr::removeSpecial($t_key);
				list($t_kurikulum,$t_kodemk,$t_kodeunit,$t_kelasmk) = explode('|',$t_key);
				
				// cek dengan daftar kelas
				$t_find = false;
				foreach($a_kelas as $t_row) {
					if($t_row['thnkurikulum'] == $t_kurikulum and $t_row['kodemk'] == $t_kodemk and $t_row['kodeunit'] == $t_kodeunit and $t_row['kelasmk'] == $t_kelasmk) {
						$t_find = true;
						break;
					}
				}
				
				if($t_find)
					$a_mkambil[$t_key] = true;
			}
		}
		
		$ok = true;
		//$conn->BeginTrans();
		
		$t_posterr = false;
		$t_postmsg = '';
		
		//if(Akademik::isMhs()||!Akademik::isMhs()) {
			foreach($a_mkambil as $t_key => $t_true) {
				list($t_kurikulum,$t_kodemk,$t_kodeunit,$t_kelasmk) = explode('|',$t_key);
				$kelompok=$_POST['praktikum'][$t_key];
				list($p_posterr,$p_postmsg) = $p_model::insertByMhs($conn,$t_kurikulum,$r_periode,$t_kodeunit,$t_kodemk,$t_kelasmk,$r_key,$kelompok);
				//tambahan rehan log krs
				$p_model::insertLogKrs($conn,$r_key,$t_kodemk,$t_kelasmk,'Insert',Modul::getUserName());
				$course = mMengajar::getCourseByPass($conn_moodle,$t_kodeunit."".$r_periode."".$t_kodemk."".$t_kelasmk);
					if(!empty($course)){
						$mooduser = mMengajar::getUserMoodle($conn,$r_key);
						if(!empty($mooduser['users'])){
							$key = $course."|".$r_key;
							mMengajar::enrolMahasiswa($conn_moodle,$conn,$key);
						}else{
							$d_users=mMengajar::inquiryByusername($conn,$r_key);
							$key = $course."|".$r_key;
							mMengajar::syncUserToElearning($conn,$d_users);
							mMengajar::enrolMahasiswa($conn_moodle,$conn,$key);
						}
					}
				if($p_posterr) {
					$ok = false;
					$t_posterr = false;
					if($p_postmsg != $t_postmsg) {
						if(!empty($t_postmsg)) $t_postmsg .= '<br>';
						$t_postmsg .= $p_postmsg;
					}
				}
			}
		//}
		/*else {
			$record = array();
			$record['nim'] = $r_key;
			$record['periode'] = $r_periode;
			// $record['thnkurikulum'] = $a_infomhs['thnkurikulum'];
			// $record['kodeunit'] = $a_infomhs['kodeunit'];
			$record['semestermhs'] = $a_infomhs['semmhs'];
			
			foreach($a_mkambil as $t_key => $t_true) {
				list($record['thnkurikulum'],$record['kodemk'],$record['kodeunit'],$record['kelasmk']) = explode('|',$t_key);
				$record['kelompok_prak']=$_POST['praktikum'][$t_key];
				list($p_posterr,$p_postmsg) = $p_model::insertRecord($conn,$record,true);
				if($p_posterr) {
					$ok = false;
					// break;
					
					$t_posterr = true;
					if($p_postmsg != $t_postmsg) {
						if(!empty($t_postmsg)) $t_postmsg .= '<br>';
						$t_postmsg .= $p_postmsg;
					}
				}
			}
		}*/
		
		// $conn->CommitTrans($ok);
		
		$p_posterr = $t_posterr;
		/* if(!empty($t_postmsg))
			$a_postmsg[] = $t_postmsg; */
		
		if(!$p_posterr) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			$a_flash['t_postmsg'] = $t_postmsg;
			
			Route::setFlashData($a_flash);
		}
	}
	else if($r_act == 'delete' and $c_delete) {
		$t_key = CStr::removeSpecial($_POST['key']);
		list($t_kurikulum,$t_kodemk,$t_kodeunit,$t_kelasmk) = explode('|',$t_key);
		
		if(Akademik::isMhs()) {
			$t_key = $t_kurikulum.'|'.$t_kodemk.'|'.$t_kodeunit.'|'.$r_periode.'|'.$t_kelasmk.'|'.$r_key;
			$p_model::insertLogKrs($conn,$r_key,$t_kodemk,$t_kelasmk,'Delete',Modul::getUserName());
			list($p_posterr,$p_postmsg) = $p_model::deleteByMhs($conn,$t_kurikulum,$r_periode,$t_kodeunit,$t_kodemk,$t_kelasmk,$r_key);
			//tambahan rehan log krs
		
			$course = mMengajar::getCourseByPass($conn_moodle,$t_kodeunit."".$r_periode."".$t_kodemk."".$t_kelasmk);
					if(!empty($course)){
						$mooduser = mMengajar::getUserMoodle($conn,$r_key);
						if(!empty($mooduser['users'])){
							$key = $course."|".$r_key;
							mMengajar::UnEnrolMahasiswa($conn_moodle,$conn,$key);
						}
					}
		}
		else {
			$t_key = $t_kurikulum.'|'.$t_kodemk.'|'.$t_kodeunit.'|'.$r_periode.'|'.$t_kelasmk.'|'.$r_key;
			$p_model::insertLogKrs($conn,$r_key,$t_kodemk,$t_kelasmk,'Delete',Modul::getUserName());
			list($p_posterr,$p_postmsg) = $p_model::delete($conn,$t_key);
			//tambahan rehan log krs
			
			$course = mMengajar::getCourseByPass($conn_moodle,$t_kodeunit."".$r_periode."".$t_kodemk."".$t_kelasmk);
					if(!empty($course)){
						$mooduser = mMengajar::getUserMoodle($conn,$r_key);
						if(!empty($mooduser['users'])){
							$key = $course."|".$r_key;
							mMengajar::UnEnrolMahasiswa($conn_moodle,$conn,$key);
						}
					}
		}
		
		if(!$p_posterr) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		}
	}
	else if($r_act == 'kunci' and $c_update) {
		//cek password
		
		$record = array();
		$record['frsdisetujui'] = -2;
		
		if(!$p_posterr)
			list($p_posterr,$p_postmsg) = mPerwalian::updateRecord($conn,$record,$r_keywali,true);
		$nim = CStr::removeSpecial($_REQUEST['npm']);
		$datamhs = mMahasiswa::getDatamhs($conn,$nim);
		if(!$p_posterr) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			$a_filter = array();
			$a_jenis = array();
			$a_filter['sistemkuliah'] = $datamhs['sistemkuliah'];
			$a_filter['kodeunit'] = $datamhs['kodeunit'];
			$a_filter['nim'] = $datamhs['nim'];
			$a_jenis['PGMBN'] = 'PGMBN';
			if($datamhs['jalurpenerimaan']=='YIM'){
				$skripsi = $p_model::CekMatakuliahYIMSkripsi($conn, $r_nim, $r_periode);
				$sup = $p_model::CekMatakuliahYIMSup($conn, $r_nim, $r_periode);
				$tfl = $p_model::CekMatakuliahYIMToefl($conn, $r_nim, $r_periode);
				$pasarmodal = $p_model::CekMatakuliahPasarmodal($conn, $r_nim, $r_periode);
				if(!empty($pasarmodal)){
					$a_jenis['PSRMD'] = 'PSRMD';
				}
				if(!empty($skripsi)){
					$a_jenis['SKRS'] = 'SKRS';
				}
				if(!empty($sup)){
					$a_jenis['SUP'] = 'SUP';
				}
				if(!empty($tfl)){
					$a_jenis['TFL'] = 'TFL';
				}
				if($r_periode!='20183'){
					mTagihan::generateTagihan($conn,$a_filter,$r_periode,$a_jenis);
				}
				//$dt_tg = mTagihan::generateTagihankrs($conn, $r_nim, $r_periode, $datamhs);
				//Route::setFlashData($a_flash);
					
			}else{
					$pasarmodal = $p_model::CekMatakuliahPasarmodal($conn, $r_nim, $r_periode);
					if(!empty($pasarmodal)){
						$a_jenis['PSRMD'] = 'PSRMD';
					}
					if($r_periode!='20183'){
						mTagihan::generateTagihan($conn,$a_filter,$r_periode,$a_jenis);
					}
					//$dt_tg = mTagihan::generateTagihankrs($conn, $r_nim, $r_periode, $datamhs);
					//Route::setFlashData($a_flash);
				
			}
			

		}
		if($r_periode!='20183'){
			$dt_tg = mTagihan::generateTagihankrs($conn, $r_nim, $r_periode, $datamhs);
		}
		Route::setFlashData($a_flash);
		
	}
	
	else if($r_act == 'buka' and $c_update and $c_buka) {
		$record = array();
		$record['frsdisetujui'] = 0;
		
		list($p_posterr,$p_postmsg) = mPerwalian::updateRecord($conn,$record,$r_keywali,true);
		
		if(!$p_posterr) {
			$a_flash = array();
			$a_flash['r_key'] = $r_key;
			$a_flash['p_posterr'] = $p_posterr;
			$a_flash['p_postmsg'] = $p_postmsg;
			
			Route::setFlashData($a_flash);
		}
	}
	
	// cek perwalian (bawah)
	if($a_wali['cekalakad']==-1) {
		$p_sposterr = true;
		$a_postmsg[] = 'Harap Melakukan Aktivasi di Keuangan';
		
		$c_insert = false;
		$c_update = false;
		$c_delete = false;
	}
	if(empty($a_wali['prasyaratspp']) and $a_wali['cekalakad']!=-1) {
		$p_sposterr = true;
		
		if($a_wali['hutang']>0)
			$a_postmsg[] = 'Hutang Anda Sebesar : '.number_format($a_wali['hutang']);
		else
			$a_postmsg[] = '<b>Anda belum bisa mengisi KRS, silahkan konfirmasi ke BAGIAN KEUANGAN dengan menyertakan Bukti Pembayaran Registrasi. <br>(CP Keuangan: 081278608086)</b><br>Deposit Anda Sebesar : '.number_format(str_replace("-","",$a_wali['hutang']));
		$a_postmsg[] = 'Biaya Semester Ini : '.number_format($a_wali['biaya']);
		if($a_wali['diskon']>0)
			$a_postmsg[] = 'Keringanan Yang Anda Peroleh : '.number_format($a_wali['diskon']);
		if($tunggakan>0)
			$a_postmsg[] = 'Total Tunggakan Yang Harus Anda Bayar Semester Ini : '.number_format($tunggakan);
		//$a_postmsg[] = 'Anda belum melakukan Pembayaran';
		$c_insert = false;
		$c_update = false;
		$c_delete = false;
	}
	if(!empty($a_wali['frsdisetujui'])) {
		$p_sposterr = true;

		if($a_wali['frsdisetujui'] == -1) {
			$a_postmsg[] = 'KRS Langsung terkunci, untuk mengubah silahkan hubungi administrator';
			//$c_update = false;
		}
		else if($a_wali['frsdisetujui'] == -2) {
			$t_postmsg = '<strong>KRS Telah disetujui</strong> ';
			if(!empty($a_wali['t_updateuser']))
				$t_postmsg .=' oleh '.mPegawai::getNamaPegawai2($conn,$a_wali['t_updateuser']);
				
			if(!empty($a_wali['t_updatetime']))
				$t_postmsg .= ' pada tanggal '.CStr::formatDateTimeInd($a_wali['t_updatetime']);
			
			//if(!$c_buka)
				//$t_postmsg .= ', hubungi petugas akademik untuk mengubah';
			
			
			$t_postmsg .='<br>Mahasiswa <strong>wajib cetak KRS</strong> untuk bukti anda terdaftar pada matakuliah/kelas tersebut<br><br>Jika KRS sudah disetujui dosen wali, <br>maka setelahnya bukan tanggung jawab petugas IT & Akademik';
			
			$a_postmsg[] = $t_postmsg;
		}
		
		$c_insert = false;
		$c_delete = false;
	}
	
	// mendapatkan krs
	$a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
	$a_infomhs['semesterkrs']=Akademik::getSemMhs($a_infomhs['periodemasuk'],$r_periode);
	$a_tidaklulus = mTranskrip::getDataTidakLulus($conn,$r_key);
	
$a_jadwal = mKelas::getFormatJadwal($a_kelas);
$a_jadwalpraktukum=mKelasPraktikum::getJadwalPrak($conn,$a_infomhs['kurikulum'],$r_periode,$a_infomhs['kodeunit']);	
//print_r($a_jadwalpraktukum);
	$l_kelasmk=uCombo::listKelasMk($conn,$r_periode,$a_infomhs['kurikulum'],$a_infomhs['kodeunit'],$r_kelasmk,'kelasmk','onchange="goSubmit()"');
	//print_r($a_kelas);
	/*if(Akademik::isMhs())
		$a_kelas = mKelas::getFormatPerJadwal($a_kelas);
	else*/
		$a_kelas = mKelas::getFormatPerSemester($a_kelas);

	$a_data = mKRS::getDataPeriode($conn,$r_key,$r_periode);
	
	$key_krs=array();
	foreach($a_data as $datakrs){
		$key_krs[]=$datakrs['kodemk'].'|'.$datakrs['kelasmk'];
	}
	
	//print_r($a_postmsg);
	if(Akademik::isMhs() and !empty($a_data) and empty($a_wali['frsdisetujui'])){
	    $p_sposterr=true;
		$a_postmsg[]='Jika sudah yakin dengan matakuliah yang diambil, silahkan konfirmasi ke dosen wali untuk validasi.<br>KRS yang sudah divalidasi tidak dapat diubah<br>';
	}
	
	/*if(Akademik::isDosen() and $a_walilalu['ips']<3 and empty($a_wali['frsdisetujui'])){
		$p_sposterr=true;
	    $a_postmsg[]='Untuk <strong>Mengunci KRS</strong> diwajibkan memasukkan password karena IPS Mahasiswa kurang dari 3.00';
	}*/
	
	//cek jumlah semester mahasiswa
	$infoProgpend=mPerwalian::infoProgpend($conn,$r_key);
	$infoMhs=mPerwalian::infoMhs($conn,$r_key);
	
	if((!empty($infoProgpend['lamastudi']) or $infoProgpend['lamastudi']>0) and $a_infomhs['semesterkrs']>$infoProgpend['lamastudi']){
		$p_sposterr = true;
		$a_postmsg[] = 'Anda Sudah Melampaui Batas Studi Maksimal';
	}
	if((!empty($infoProgpend['lamastudi']) or $infoProgpend['lamastudi']>0) and $a_infomhs['semesterkrs']==$infoProgpend['lamastudi']){
		$p_sposterr = true;
		$a_postmsg[] = 'Ini Merupakan Semester Terakhir Anda';
	}
	
	//cek status KRS per prodi
	if(mKRS::getStatusKrs($conn,$a_infomhs['kodeunit'])!='') {
		
		$p_sposterr = true;
		$a_postmsg[] = 'Periode Pendaftaran KRS Prodi '.mKRS::getStatusKrs($conn,$a_infomhs['kodeunit']).' Belum Dibuka';
		
		$c_insert = false;
		$c_update = true;
		$c_delete = false;
	}
	if(!empty($a_postmsg)) {
		$p_posterr = isset($p_posterr) ? $p_posterr : $p_sposterr;
		$p_postmsg = implode('<br>',$a_postmsg);
	}
	
	// data untuk grafik
	$a_semester = array();
	$a_skssemester = array();
	$a_ipssemester = array();
	$a_nhuruf = array();

	$a_datasmt = mKRS::getDataPerSemester($conn,$r_key,$a_infomhs['periodedaftar'],false,true);
	
	foreach($a_datasmt as $t_semester => $t_data) {
		$t_tsks = 0;
		$t_tbobot = 0;
		
		foreach($t_data as $row) {
			$t_sks = (int)$row['sks'];
			$t_tsks += $t_sks;
			
			if(!empty($row['nilaimasuk'])) {
				$t_bobot = $t_sks * (float)$row['nangka'];
				$t_tbobot += $t_bobot;
				
				$t_nh = trim($row['nhuruf']);
				$a_nhuruf[$t_nh]++;
			}
		}
		
		$t_ttsks += $t_tsks;
		$t_ttbobot += $t_tbobot;
		
		if($t_tsks == 0)
			$t_ips = 0;
		else
			$t_ips = number_format(round($t_tbobot/$t_tsks,2),2);
		
		// untuk grafik
		if(!empty($t_semester)) {
			$a_semester[] = $t_semester;
			$a_skssemester[] = $t_tsks;
			$a_ipssemester[] = $t_ips;
		}
	}
	
	// menghitungan persentase nilai huruf
	ksort($a_nhuruf);
	
	$t_jumlahnilai = 0;
	foreach($a_nhuruf as $t_nhuruf => $t_jumlah)
		$t_jumlahnilai += $t_jumlah;
	
	$a_nhurufpie = array();
	foreach($a_nhuruf as $t_nhuruf => $t_jumlah)
		$a_nhurufpie[] = "'$t_nhuruf', ".round(($t_jumlah*100)/$t_jumlahnilai,2);
		

$a_ambilpraktikum=array();
foreach($a_jadwalpraktukum as $keyprak=>$val_prak){
	foreach($val_prak as $row_prak){
		$p_key=$keyprak.'|'.$row_prak['kelompok'];
		$a_ambilpraktikum[$p_key]=$row_prak;
	}
}


//if($_SERVER['REMOTE_ADDR']=='36.85.91.184')
//print_r($a_jadwalpraktukum);
?>
<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/officexp.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/perwalian.js"></script>
	<script type="text/javascript" src="scripts/md5.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
	<div id="wrapper">
		<div class="SideItem" id="SideItem">
		<?php require_once('inc_headermahasiswa.php'); ?>
		<form name="pageform" id="pageform" method="post">
			 
			<?php require_once('inc_headermhs_krs.php') ?>
		 
			<br>
			
	<div style="width:100%;">
		<div style="float:left;width:100%">
			<?	if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivSuccess' : 'DivError' ?>" style="width:<?= $p_lwidth ?>px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
			<?	}
				if($c_update and empty($a_wali['frsdisetujui'])) { ?>
			<center>
			
			</center>
			<div class="Break"></div>
			<?	} ?>
			<center>
				<header style="width:<?= $p_lwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img id="img_workflow" width="24px" src="images/aktivitas/<?= $p_aktivitas ?>.png" onerror="loadDefaultActImg(this)">
							<h1><?= $p_title ?> - <?= Akademik::getNamaPeriode($r_periode) ?></h1>
						</div>
						<div class="right">
							<?php if(!empty($a_wali['frsdisetujui'])) { ?>
							<img title="Cetak KRS" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrint()">
							<?php } ?>
							<!--img title="Cetak KRS DM" width="24px" src="images/print.png" style="cursor:pointer" onclick="goPrintDm()"-->
							<?php/* if(Akademik::isMhs() and empty($a_wali['frsdisetujui'])) { ?>
							<img title="Kunci KRS By Mhs" width="24px" src="images/tablelock.png" style="cursor:pointer" onclick="goLockByMhs()">
							<?php }*/ ?>
							<?	if($c_update and !Akademik::isMhs()) {
									if(empty($a_wali['frsdisetujui'])) { ?>
										<img title="Kunci KRS" width="24px" src="images/tablelock.png" style="cursor:pointer" onclick="<?php echo (Akademik::isDosen() /*and $a_walilalu['ips']<3*/)?'goLockByDosen()':'goLock()'?>">
							<?		}
									else if($c_buka || $admin) { ?>
							<img title="Buka Kunci KRS" width="24px" src="images/tableunlock.png" style="cursor:pointer" onclick="goUnlock()">
							<?		}
								} ?>
						</div>
					</div>
				</header>
			</center>

<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th width="25">No.</th>
		<th style="text-align: left !important;" width="90">Kode</th>
		<th style="text-align: left !important;" width="200">Nama Matakuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
		<th width="30">Perkuliahan</th>
		<th width="150">Waktu</th>
		<th style="text-align: left !important;" width="200">Dosen</th>
		<? if($c_delete) { ?>
		<th>Aksi</th>
		<? } ?>
	</tr>
<?php
	$i = 0;
	$t_totalsks = 0;
	foreach($a_data as $row) {
		if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
		$key_ambilprak=$row['kodemk'].'|'.$row['kelasmk'].'|'.$row['kelompok_prak'];
		$t_sks = (int)$row['sks'];
		$t_totalsks += $t_sks;
		$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
?>
	<tr valign="top" class="<?= $rowstyle ?>">
		<td align="center"><?= $i ?>.</td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $t_sks ?></td>
		<td align="center"><?= $row['isonline'] ?></td>
		<td align="center"><?= $a_jadwal[$t_key] ?></td>

		<? if($c_delete) { 
			if($row['isonline']!="Online"){
			?>
		
			<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
		
		<? }elseif(Akademik::isAdmin()){?>
			<td align="center"><img id="<?= $t_key ?>" title="Hapus Data" src="images/delete.png" onclick="goDelete(this)" style="cursor:pointer"></td>
		<?	}else{ ?>


			<td></td>
	<? }

} ?>
<?php
			$a_dosen = mMengajar::getDosen($conn, Akademik::getPeriode(), $row['kodemk'], $row['kelasmk'],$row['kodeunit']);
			//print_r($a_dosen);
		?>
		<td><?php foreach ($a_dosen as $doseng) {
			echo $doseng['namapengajar'];
		} ?></td>
	</tr>
<?php
	}
?>
	<tr>
		<th colspan="4">Total SKS</th>
		<th><?= $t_totalsks ?></th>
		<th colspan="3">&nbsp;</th>
	</tr>
</table>
<?	if($c_update and empty($a_wali['frsdisetujui'])) { ?>
<div class="Break"></div>
<center>
<?php if(Akademik::isDosen()) { ?>
	<div style="width:<?= $p_lwidth ?>px">
		<h2 style="color: red">* Sebelum Memvalidasi Pastikan Bahwa KRS Sudah Benar. <br>KRS yang Sudah Divalidasi tidak Dapat Dibatalkan.<br>Apabila Ada Perubahan Dapat Dilakukan Pada Masa PKRS Tanggal 11 - 16 Maret 2019.</h2>
	</div>
	<br>
<?php } ?>
<div style="width:<?= $p_lwidth ?>px">
	<h2>* Jika KRS sudah disetujui dosen wali, <br>maka setelahnya bukan tanggung jawab petugas IT & Akademik</h2>
</div>
</center>
<?
	}
	if($c_insert) { ?>
			<br>
			<? /* <center>
				<header style="width:<?= $p_tbwidth ?>px">
					<div class="inner">
						<div class="left title">
							<img width="24px" src="images/aktivitas/DEFAULT.png">
							<h1>Daftar Kelas Perkuliahan</h1>
						</div>
					</div>
				</header>
			</center> */ ?>
			<center>
				<div class="ViewTitle" style="width:<?= $p_lwidth ?>px;">
					<span>
						<img id="img_workflow" width="24px" src="images/aktivitas/DEFAULT.png" onerror="loadDefaultActImg(this)">
						&nbsp;Pilihan Kelas Perkuliahan
						<div style="float:right">
							<input type="button" value="Tampilkan Daftar" class="ControlStyle" onClick="showPilihan()">
						</div>
					</span>
				</div>
			</center>
			<br>
<div id="div_pilihan">

<?php
//echo '<b>Kelas Mata Kuliah :</b>'.$l_kelasmk.'<br><br>';
//hidupkan kembali jika ingin format jadwal harian berlaku untuk mahasiswa
	/*if(Akademik::isMhs()) { 
?>
<?php
//hidupkan kembali jika ingin format jadwal harian berlaku untuk mahasiswa
		foreach($a_kelas as $t_nohari => $t_kelas) {
			$i = 0;
?>

<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th colspan="9" class="SubHeaderBG"><?= Date::indoDay($t_nohari) ?></th>
	</tr>
	<tr>
		<th width="25">&nbsp;</th>
		<th width="60">Mulai</th>
		<th width="60">Selesai</th>
		<th width="90">Kode</th>
		<th>Nama MataKuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
		<th width="30">Kapasitas</th>
		<th width="30">Peserta</th>
	</tr>
<?php
			foreach($t_kelas as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
				
				// pewarnaan
				$t_class = '';
				if($row['semmk'] == $a_infomhs['semmhs'])
					$t_class = 'YellowBG';
				else if($row['semmk'] < $a_infomhs['semmhs'] and $a_tidaklulus[$row['kodemk']])
					$t_class = 'RedBG';
				if($row['jumlahpeserta']>=$row['dayatampung'])
					$t_class = 'RedBG';
				
				$key_ceklist=$row['kodemk'].'|'.$row['kelasmk'];
				if(in_array($key_ceklist,$key_krs))
					$disable='disabled checked';
				else
					$disable='';
?>
	<tr class="<?= $rowstyle ?> <?= $t_class ?>">
		<td>
			<input type="checkbox" name="mkambil[]" value="<?= $t_key ?>" <?=$disable?>></td>
		<td align="center"><?= $row['jammulai'] ?></td>
		<td align="center"><?= $row['jamselesai'] ?></td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['sks'] ?></td>
		<td align="center"><?= $row['dayatampung'] ?></td>
		<td align="center"><?= $row['jumlahpeserta'] ?></td>
	</tr>
<?php
			}
?>
</table>
<br>
<?php
		}
		if(empty($a_kelas)) {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<td align="center" colspan="7">
			Data kelas prodi <?= $a_infomhs['jurusan'] ?> kurikulum <?= $a_infomhs['kurikulum'] ?> tidak ada
		</td>
	</tr>
</table>
<?php
		}
		else {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="LeftColumnBG">
		<td align="center" colspan="7">
			<input type="button" value="Ambil Mata Kuliah" class="ControlStyle" onClick="goAmbil()">
		</td>
	</tr>
</table>

<?php

//hidupkan kembali jika ingin format jadwal harian berlaku untuk mahasiswa

		}
	}
	else {*/

?>
<? /* <table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th width="25">&nbsp;</th>
		<th width="90">Kode</th>
		<th>Nama Matakuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
		<th width="200">Waktu</th>
	</tr>
<?php
		// menyusun semester
		$t_semmhs = $a_infomhs['semmhs'];
		
		$a_tempkelas = array();
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester >= $t_semmhs)
				break;
			
			foreach($t_kelas as $row) {
				if($a_tidaklulus[$row['kodemk']])
					$a_tempkelas[$t_semester.'U'][] = $row;
			}
		}
		
		if(!empty($a_kelas[$t_semmhs]))
			$a_tempkelas[$t_semmhs] = $a_kelas[$t_semmhs];
		
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester == $t_semmhs)
				continue;
			
			$a_tempkelas[$t_semester] = $t_kelas;
		}
		
		$a_kelas = $a_tempkelas;
		
		$i = 0;
		foreach($a_kelas as $t_semester => $t_kelas) {
			// pewarnaan
			$t_class = '';
			if(substr($t_semester,-1) == 'U') {
				$t_semester = substr($t_semester,0,strlen($t_semester)-1);
				$t_class = 'RedBG';
			}
			else if($t_semester == $a_infomhs['semmhs'])
				$t_class = 'YellowBG';
?>
	<tr class="LiteHeaderBG <?= $t_class ?>">
		<td align="center" colspan="6">SEMESTER <?= $t_semester ?></td>
	</tr>
<?php
			foreach($t_kelas as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
?>
	<tr class="<?= $rowstyle ?>">
		<td><input type="checkbox" name="mkambil[]" value="<?= $t_key ?>"></td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['sks'] ?></td>
		<td><?= $a_jadwal[$t_key] ?></td>
	</tr>
<?php
			}
		}
		if($i == 0) {
?>
	<tr>
		<td align="center" colspan="6">
			Data kelas prodi <?= $a_infomhs['jurusan'] ?> kurikulum <?= $a_infomhs['kurikulum'] ?> tidak ada
		</td>
	</tr>
<?php
		}
?>
	<tr class="LeftColumnBG">
		<td align="center" colspan="6">
			<input type="button" value="Ambil Mata Kuliah" class="ControlStyle" onClick="goAmbil()">
		</td>
	</tr>
</table> */ ?>
<?php

		// menyusun semester
		$t_semmhs = $a_infomhs['semmhs'];
		
		$a_tempkelas = array();
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester >= $t_semmhs)
				break;
			
			foreach($t_kelas as $row) {
				if($a_tidaklulus[$row['kodemk']])
					$a_tempkelas[$t_semester.'U'][] = $row;
			}
		}
		
		if(!empty($a_kelas[$t_semmhs]))
			$a_tempkelas[$t_semmhs] = $a_kelas[$t_semmhs];
		
		foreach($a_kelas as $t_semester => $t_kelas) {
			if($t_semester == $t_semmhs)
				continue;
			
			$a_tempkelas[$t_semester] = $t_kelas;
		}
		
		$a_kelas = $a_tempkelas;
		
		foreach($a_kelas as $t_semester => $t_kelas) {
			$i = 0;
			
			// pewarnaan
			$t_class = '';
			
			if(substr($t_semester,-1) == 'U') {
				$t_semester = substr($t_semester,0,strlen($t_semester)-1);
				$t_class = 'RedBG';
			}
			else if($t_semester == $a_infomhs['semmhs'])
				$t_class = 'YellowBG';
			
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<th colspan="10" class="SubHeaderBG <?= $t_class ?>"> <span style="float:left; padding-left:10px">  <?=Akademik::namaSemesterEsa($t_semester) ?> </span><span style="float:right; padding-right:10px"><?= Akademik::getNamaPeriode($r_periode) ?></span></th>
	</tr>
	<tr>
		<th width="25">&nbsp;</th>
		<th width="50">Kode</th>
		<th width="150">Nama Matakuliah</th>
		<th width="40">Kelas</th>
		<th width="30">SKS</th>
		<th width="30">Kapasitas</th>
		<th width="30">Peserta</th>
		<th width="120">Jadwal kuliah</th>
		<th width="200">Dosen</th>
		
	</tr>
<?php
			foreach($t_kelas as $row) {
				if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
				
				$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'].'|'.$row['kelasmk'];
				$key_praktikum=$row['kodemk'].'|'.$row['kelasmk'];
				// pewarnaan
				$t_class = '';
				//if(Akademik::isMhs()) { 
				$checkbox='<input type="checkbox" name="mkambil[]" value="'.$t_key.'">';
				if($row['semmk'] == $a_infomhs['semmhs']){
					$t_class = 'YellowBG';
					$rowstyle='';
				}else if($row['semmk'] < $a_infomhs['semmhs'] and $a_tidaklulus[$row['kodemk']]){
					$t_class = 'RedBG';
					$rowstyle='';
					if( !Akademik::isPerwalianProdi() && !Akademik::isAdminDAA() ) $checkbox='&nbsp;';
				}
				if($row['jumlahpeserta']>=$row['dayatampung']){
					$t_class = 'RedBG';
					$rowstyle='';
					if( !Akademik::isPerwalianProdi() && !Akademik::isAdmin() ) $checkbox='&nbsp;';
				}
				//}
?>
	<tr class="<?= $rowstyle ?> <?=$t_class?>">
		<td><?= $checkbox ?></td>
		<td><?= $row['kodemk'] ?></td>
		<td><?= $row['namamk'] ?></td>
		<td align="center"><?= $row['kelasmk'] ?></td>
		<td align="center"><?= $row['sks'] ?></td>
		<td align="center"><?= $row['dayatampung'] ?></td>
		<td align="center"><?= $row['jumlahpeserta'] ?></td>
		<td><?= $a_jadwal[$t_key] ?></td>

		<?php
			$a_dosen = mMengajar::getDosen($conn, Akademik::getPeriode(), $row['kodemk'], $row['kelasmk'],$row['kodeunit']);
			//print_r($a_dosen);
		?>
		<td><?php foreach ($a_dosen as $doseng) {
			echo $doseng['namapengajar'];
		} ?></td>
	</tr>
<?php
			} 
?>
</table>
<br>
<?php
		}
		if(empty($a_kelas)) {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr>
		<td align="center" colspan="9">
			Data kelas prodi <?= $a_infomhs['jurusan'] ?> kurikulum <?= $a_infomhs['kurikulum'] ?> tidak ada
		</td>
	</tr>
</table>
<?php
		}
		else {
?>
<table width="<?= $p_lwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
	<tr class="LeftColumnBG">
		<td align="center" colspan="6">
			<input type="button" value="Ambil Mata Kuliah" class="ControlStyle" onClick="goAmbil()">
		</td>
	</tr>
</table>
<?php
		}
	//}//hidupkan kembali jika ingin format jadwal harian berlaku untuk mahasiswa
?>
</div>
<? } ?>
		</div>
		<!--div style="float:left;padding-left:15px">
			<div id="container_sks" style="width:290px;height:200px"></div>
			<br>
			<div id="container_ips" style="width:290px;height:200px"></div>
			<br>
			<div id="container_nh" style="width:290px;height:200px"></div>
		</div-->
	</div>
			<input type="hidden" name="act" id="act">
			<input type="hidden" name="cekpass" id="cekpass">
			<input type="hidden" name="key" id="key">
			<input type="hidden" name="npm" id="npm" value="<?= $r_key ?>">
			<? if(Akademik::isDosen()) { ?>
			<input type="hidden" name="nip" id="nip" value="<?= Modul::getUserName() ?>">
			<? } ?>
			
			<!-- dialog password -->
			<div id="div_dark" class="Darken" style="display:none"></div>
			<div id="div_light" class="Lighten" align="center" style="display:none">
				<div id="div_content" style="background-color:white;border-radius:5px;border:1px solid #999;width:40%">
					<div class="filterTable">
						<strong>Persetujuan KRS Mahasiswa</strong>	
					</div>
					<br>
					<center>
						Masukkan Password Mahasiswa
						<br>
						<?= UI::createPasswordBox('password','','ControlStyle',100,20) ?>	
					</center>
									
					<table border="0" cellspacing="10" class="nowidth">
						<tr>
							<td class="TDButton" onclick="goLock()"><img src="images/tablelock.png" width="16"> Kunci</td>
							<td class="TDButton" onclick="goClose()"><img src="images/off.png"> Tutup</td>
						</tr>
					</table>
				</div>
			</div>
			<!-- end -->
			
		</form>
		</div>		
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>



				
<script type="text/javascript" src="scripts/highcharts/highcharts.js"></script>
<script type="text/javascript" src="scripts/highcharts/modules/exporting.js"></script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript">

$(document).ready(function() {
	<? if(Akademik::isDosen()) { ?>
	$("#mahasiswa").xautox({strpost: "f=acmhswali", targetid: "npmtemp", postid: "nip"});
	<? } else { ?>
	$("#mahasiswa").xautox({strpost: "f=acmahasiswa", targetid: "npmtemp"});
	<? } ?>
	
	Highcharts.setOptions({
		title: {
			style: {
				fontSize: '14px'
			}
		},
		xAxis: {
			labels: {
				style: {
					fontSize: '10px'
				}
			}
		},
		yAxis: {
			labels: {
				style: {
					fontSize: '10px'
				}
			}
		}
	});
	
	chart_sks = new Highcharts.Chart({
		chart: {
			renderTo: 'container_sks',
			type: 'line'
		},
		title: {
			text: 'SKS Mahasiswa',
			x: -20 //center
		},
		xAxis: {
			title: {
				text: 'Semester'
			},
			categories: ['<?= implode("', '",$a_semester) ?>']
		},
		yAxis: {
			title: {
				text: 'SKS'
			}
		},
		tooltip: {
			formatter: function() {
				return '<strong>' + this.series.name + ': </strong>' + this.y;
			}
		},
		plotOptions: {
			line: {
				dataLabels: {
					enabled: true,
					style: {
						fontSize: '10px'
					}
				}
			}
		},
		legend: {
			enabled: false
		},
		series: [{
			name: 'SKS',
			data: [<?= implode(', ',$a_skssemester) ?>]
		}]
	});
	
	chart_ips = new Highcharts.Chart({
		chart: {
			renderTo: 'container_ips',
			type: 'line'
		},
		title: {
			text: 'IPS Mahasiswa',
			x: -20 //center
		},
		xAxis: {
			title: {
				text: 'Semester'
			},
			categories: ['<?= implode("', '",$a_semester) ?>'],
		},
		yAxis: {
			title: {
				text: 'IPS'
			}
		},
		tooltip: {
			formatter: function() {
				return '<strong>' + this.series.name + ': </strong>' + this.y;
			}
		},
		plotOptions: {
			line: {
				dataLabels: {
					enabled: true,
					style: {
						fontSize: '10px'
					}
				}
			}
		},
		legend: {
			enabled: false
		},
		series: [{
			name: 'IPS',
			data: [<?= implode(', ',$a_ipssemester) ?>]
		}]
	});
	
	chart_nh = new Highcharts.Chart({
		chart: {
			renderTo: 'container_nh',
			plotBackgroundColor: null,
			plotBorderWidth: null,
			plotShadow: false
		},
		title: {
			text: 'Perbandingan Nilai'
		},
		tooltip: {
			pointFormat: '<strong>{point.percentage}%</strong>',
			percentageDecimals: 2
		},
		/* plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: false
				},
				showInLegend: true
			}
		}, */
		plotOptions: {
			pie: {
				allowPointSelect: true,
				cursor: 'pointer',
				dataLabels: {
					enabled: true,
					color: '#000000',
					connectorColor: '#000000',
					formatter: function() {
						return '<b>'+ this.point.name +'</b>: '+ this.percentage.toFixed(2) +' %';
					},
					distance: 5,
					style: {
						fontSize: '10px'
					}
				}
			}
		},
		series: [{
			type: 'pie',
			name: 'Perbandingan Nilai',
			data: [
				[<?= implode('],[',$a_nhurufpie) ?>]
			]
		}]
	});
});
	
function goAmbil() {
	var konfirmasi=confirm('Yakin ambil mata kuliah tersebut ?');
	if(konfirmasi){
	    document.getElementById("act").value = "insert";
	    goSubmit();
	}else{
	    document.getElementById("act").value="";
	    goSubmit();
	}
}

function goLockByDosen() {
   if(confirm("Yth Dosen wali. Sebelum mengunci, pastikan dulu kepada mahasiswa apakah SKS dan Matakuliah yang diambil sudah tepat. Apabila KRS sudah dikunci, maka tidak bisa dibuka kembali")){
		document.getElementById("act").value = "kunci";
		goSubmit();
    }
}

function goLock() {
	if(confirm("Yth Dosen wali. Sebelum mengunci, pastikan dulu kepada mahasiswa apakah SKS dan Matakuliah yang diambil sudah tepat. Apabila KRS sudah dikunci, maka tidak bisa dibuka kembali. Terima Kasih")){
		document.getElementById("act").value = "kunci";
		goSubmit();
    }
}

function goLockByMhs() {
	document.getElementById("act").value = "kuncimhs";
	var txt="Jika Dikunci Maaka KRS tidak dapat diedit kecuali melalui administrator\nYakin akan mengunci ?";
	if(confirm(txt)){
		goSubmit();
	}
}
function goUnlock() {
	document.getElementById("act").value = "buka";
	if(confirm("Yakin akan membuka valdiasi KRS ?\nMahasiswa dapat mengisi KRS kembali"))
		goSubmit();
}

function goDelete(elem) {
	var drop = confirm("Apakah anda yakin akan menghapus mata kuliah ini dari KRS?");
	if(drop) {
		document.getElementById("act").value = "delete";
		document.getElementById("key").value = elem.id;
		goSubmit();
	}
}

function goPrint() {
	showPage('npm','<?= Route::navAddress('rep_frs') ?>');
}

function goPrintDm() {
	showPage('npm','<?= Route::navAddress('rep_frs_dm') ?>');
}

function showPilihan() {
	$("#div_pilihan").show();
}

function goClose() {
	$("#div_light").hide();
	$("#div_dark").hide();
}
</script>
</body>
</html>
