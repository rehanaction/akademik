<?php
	// fungsi pembantu modul pendaftaran
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class Pendaftaran {
		// apakah role mahasiswa
		function setDataToken($token){
			$_SESSION[SITE_ID]['PENDAFTAR']['tokenpendaftaran'] =$token;
		}
		
		function setDataPribadi($pendaftar){
			$_SESSION[SITE_ID]['PENDAFTAR']['jalurpenerimaan']  =$pendaftar['jalurpenerimaan'];
			$_SESSION[SITE_ID]['PENDAFTAR']['periodedaftar']    =$pendaftar['periodedaftar'];
			$_SESSION[SITE_ID]['PENDAFTAR']['idgelombang']      =$pendaftar['idgelombang'];
			
			$_SESSION[SITE_ID]['PENDAFTAR']['gelardepan']   	=$pendaftar['gelardepan'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nama']         	=strtoupper($pendaftar['nama']);
			$_SESSION[SITE_ID]['PENDAFTAR']['gelarbelakang']	=$pendaftar['gelarbelakang'];
			$_SESSION[SITE_ID]['PENDAFTAR']['sex']     			=$pendaftar['sex'];
			$_SESSION[SITE_ID]['PENDAFTAR']['tmplahir']     	=$pendaftar['tmplahir'];
			$_SESSION[SITE_ID]['PENDAFTAR']['tgllahir']     	=$pendaftar['tgllahir'];
			$_SESSION[SITE_ID]['PENDAFTAR']['goldarah']     	=$pendaftar['goldarah'];
			$_SESSION[SITE_ID]['PENDAFTAR']['statusnikah']  	=$pendaftar['statusnikah'];
			$_SESSION[SITE_ID]['PENDAFTAR']['jalan']        	=$pendaftar['jalan'];
			$_SESSION[SITE_ID]['PENDAFTAR']['rt']           	=$pendaftar['rt'];
			$_SESSION[SITE_ID]['PENDAFTAR']['rw']           	=$pendaftar['rw'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kel']          	=$pendaftar['kel'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kec']          	=$pendaftar['kec'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodepos']      	=$pendaftar['kodepos'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodekota']     	=$pendaftar['kodekota'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodepropinsi'] 	=$pendaftar['kodepropinsi'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodeagama']    	=$pendaftar['kodeagama'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodewn']       	=$pendaftar['kodewn'];
			$_SESSION[SITE_ID]['PENDAFTAR']['telp']         	=$pendaftar['telp'];
			$_SESSION[SITE_ID]['PENDAFTAR']['telp2']        	=$pendaftar['telp2'];
			$_SESSION[SITE_ID]['PENDAFTAR']['hp']           	=$pendaftar['hp'];
			$_SESSION[SITE_ID]['PENDAFTAR']['hp2']          	=$pendaftar['hp2'];
			$_SESSION[SITE_ID]['PENDAFTAR']['email']        	=$pendaftar['email'];
			$_SESSION[SITE_ID]['PENDAFTAR']['email2']       	=$pendaftar['email2'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nomorktp']     	=$pendaftar['nomorktp'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nomorkk']      	=$pendaftar['nomorkk'];
			$_SESSION[SITE_ID]['PENDAFTAR']['namaayah']             =$pendaftar['namaayah'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodepekerjaanayah']    =$pendaftar['kodepekerjaanayah'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodependidikanayah']   =$pendaftar['kodependidikanayah'];
			$_SESSION[SITE_ID]['PENDAFTAR']['namaibu']              =$pendaftar['namaibu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodepekerjaanibu']     =$pendaftar['kodepekerjaanibu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodependidikanibu']    =$pendaftar['kodependidikanibu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['telportu']             =$pendaftar['telportu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['jalanortu']            =$pendaftar['jalanortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['rtortu']      	    	=$pendaftar['rtortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['rwortu']      	    	=$pendaftar['rwortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kelortu']              =$pendaftar['kelortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kecortu']              =$pendaftar['kecortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodeposortu']          =$pendaftar['kodeposortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodekotaortu']         =$pendaftar['kodekotaortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodepropinsiortu']     =$pendaftar['kodepropinsiortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodependapatanortu']   =$pendaftar['kodependapatanortu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kontaknama']           =$pendaftar['kontaknama'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kontaktelp']           =$pendaftar['kontaktelp'];
			$_SESSION[SITE_ID]['PENDAFTAR']['jalankontak']          =$pendaftar['jalankontak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['rtkontak']      	=$pendaftar['rtkontak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['rwkontak']      	=$pendaftar['rwkontak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kelkontak']            =$pendaftar['kelkontak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['keckontak']            =$pendaftar['keckontak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodeposkontak']        =$pendaftar['kodeposkontak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodekotakotak']        =$pendaftar['kodekotakotak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodepropinsikontak']   =$pendaftar['kodepropinsikontak'];
			$_SESSION[SITE_ID]['PENDAFTAR']['idsmu']        	=$pendaftar['idsmu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['asalsmu']      	=$pendaftar['asalsmu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['alamatsmu']    	=$pendaftar['alamatsmu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodekotasmu']  	=$pendaftar['kodekotasmu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['propinsismu']  	=$pendaftar['propinsismu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['telpsmu']      	=$pendaftar['telpsmu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nemsmu']       	=$pendaftar['nemsmu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['noijasahsmu']  	=$pendaftar['noijasahsmu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nis']          	=$pendaftar['nis'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pernahponpes'] 	=$pendaftar['pernahponpes'];
			$_SESSION[SITE_ID]['PENDAFTAR']['namaponpes']   	=$pendaftar['namaponpes'];
			$_SESSION[SITE_ID]['PENDAFTAR']['alamatponpes'] 	=$pendaftar['alamatponpes'];
			$_SESSION[SITE_ID]['PENDAFTAR']['lamaponpes']   	=$pendaftar['lamaponpes'];
			$_SESSION[SITE_ID]['PENDAFTAR']['mhstransfer']  	=$pendaftar['mhstransfer'];
			$_SESSION[SITE_ID]['PENDAFTAR']['ptasal']       	=$pendaftar['ptasal'];
			$_SESSION[SITE_ID]['PENDAFTAR']['ptjurusan']    	=$pendaftar['ptjurusan'];
			$_SESSION[SITE_ID]['PENDAFTAR']['ptipk']        	=$pendaftar['ptipk'];
			$_SESSION[SITE_ID]['PENDAFTAR']['ptthnlulus']   	=$pendaftar['ptthnlulus'];
			$_SESSION[SITE_ID]['PENDAFTAR']['sksasal']      	=$pendaftar['sksasal'];
			$_SESSION[SITE_ID]['PENDAFTAR']['sksdiakui']    	=$pendaftar['sksdiakui'];
			$_SESSION[SITE_ID]['PENDAFTAR']['bhsarab']      	=$pendaftar['bhsarab'];
			$_SESSION[SITE_ID]['PENDAFTAR']['bhsinggris']   	=$pendaftar['bhsinggris'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pengkomp']     	=$pendaftar['pengkomp'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pilihan1']     	=$pendaftar['pilihan1'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pilihan2']     	=$pendaftar['pilihan2'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pilihan3']     	=$pendaftar['pilihan3'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nopesertaspmb']	=$pendaftar['nopesertaspmb'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nopendaftar']		=$pendaftar['nopendaftar'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pswd']				=$pendaftar['pswd'];
			$_SESSION[SITE_ID]['PENDAFTAR']['password']			=$pendaftar['password'];
			
			$_SESSION[SITE_ID]['PENDAFTAR']['facebook']			=$pendaftar['facebook'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodepropinsilahir']=$pendaftar['kodepropinsilahir'];
			$_SESSION[SITE_ID]['PENDAFTAR']['kodekotalahir']	=$pendaftar['kodekotalahir'];
			$_SESSION[SITE_ID]['PENDAFTAR']['jurusansmaasal']	=$pendaftar['jurusansmaasal'];
			$_SESSION[SITE_ID]['PENDAFTAR']['thnlulussmaasal']	=$pendaftar['thnlulussmaasal'];
			$_SESSION[SITE_ID]['PENDAFTAR']['raport_10_1']		=$pendaftar['raport_10_1'];
			$_SESSION[SITE_ID]['PENDAFTAR']['raport_10_2']		=$pendaftar['raport_10_2'];
			$_SESSION[SITE_ID]['PENDAFTAR']['raport_11_1']		=$pendaftar['raport_11_1'];
			$_SESSION[SITE_ID]['PENDAFTAR']['raport_11_2']		=$pendaftar['raport_11_2'];
			$_SESSION[SITE_ID]['PENDAFTAR']['raport_12_1']		=$pendaftar['raport_12_1'];
			$_SESSION[SITE_ID]['PENDAFTAR']['raport_12_2']		=$pendaftar['raport_12_2'];
			// $_SESSION[SITE_ID]['PENDAFTAR']['onedayservice']	=$pendaftar['onedayservice'];
			$_SESSION[SITE_ID]['PENDAFTAR']['idjadwaldetail']	=$pendaftar['idjadwaldetail'];
			
			$_SESSION[SITE_ID]['PENDAFTAR']['iskartanu']		=$pendaftar['iskartanu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['namapemilikkartanu']	=$pendaftar['namapemilikkartanu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['nopemilikkartanu']	=$pendaftar['nopemilikkartanu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['hubungankartanu']	=$pendaftar['hubungankartanu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pendapatanayah']	=$pendaftar['pendapatanayah'];
			$_SESSION[SITE_ID]['PENDAFTAR']['pendapatanibu']	=$pendaftar['pendapatanibu'];
			$_SESSION[SITE_ID]['PENDAFTAR']['tglregistrasi']	=$pendaftar['tglregistrasi'];
		}
            
            //deletesession
            function delSession(){
                unset ($_SESSION[SITE_ID]['PENDAFTAR']);
                unset ($_SESSION[SITE_ID]['URL']);
            }
		// apakah role mahasiswa
		function isMhs() {
			$role = Modul::getRole();
			
			if($role == 'M')
				return true;
			else
				return false;
		}
		
		// apakah role dosen
		function isDosen() {
			$role = Modul::getRole();
			
			if($role == 'D')
				return true;
			else
				return false;
		}
		
		// mendapatkan data session
		function getIsiBiodataMhs() {
			return $_SESSION[SITE_ID]['PMBESAUNGGUL']['BIODATAMHS'];
		}
		
		function getIsiNilai() {
			return $_SESSION[SITE_ID]['PMBESAUNGGUL']['ISINILAI'];
		}
		
		function getKurikulum() {
			return $_SESSION[SITE_ID]['PMBESAUNGGUL']['KURIKULUM'];
		}
		
		function getPeriode() {
			return $_SESSION[SITE_ID]['PMBESAUNGGUL']['PERIODE'];
		}
		
		function getTahap() {
			return $_SESSION[SITE_ID]['PMBESAUNGGUL']['TAHAP'];
		}
		
		// mengambil setting global
		function setGlobal($conn) {
			// ambil model setting global
			require_once(Route::getModelPath('setting'));
			
			$_SESSION[SITE_ID]['AKADEMIK'] = mSetting::getDataSession($conn);
		}
		
		// mengambil data semester
		function semester($singkat=false) {
			if($singkat)
				$data = array('1' => 'Gasal', '2' => 'Genap', '3' => 'Pendek');
			else
				$data = array('1' => 'Semester Gasal', '2' => 'Semester Genap', '3' => 'Semester Pendek');
			
			return $data;
		}
		
		// shortcut :D
		function getNamaPeriode($periode,$singkat=false) {
			$a_semester = self::semester($singkat);
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return $a_semester[$t_semester].' '.$t_tahun.' - '.($t_tahun+1);
		}
		
		function getNamaPeriodeShort($periode) {
			$a_semester = self::semester(true);
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return $a_semester[$t_semester].' '.substr($t_tahun,-2).'/'.substr($t_tahun+1,-2);
		}
		
		function getNamaPeriodeLong($periode) {
			$a_semester = self::semester();
			
			$t_semester = substr($periode,-1);
			$t_tahun = substr($periode,0,4);
			
			return $a_semester[$t_semester].' TAHUN AKADEMIK '.$t_tahun.'/'.($t_tahun+1);
		}
		
		function getNamaMahasiswa($conn,$npm) {
			// ambil model pegawai
			require_once(Route::getModelPath('mahasiswa'));
			
			return mMahasiswa::getNama($conn,$npm);
		}
		
		function getNamaPegawai($conn,$nip) {
			// ambil model pegawai
			require_once(Route::getModelPath('pegawai'));
			
			return mPegawai::getNamaPegawai($conn,$nip);
		}
		
		function getNamaUnit($conn,$kodeunit) {
			// ambil model unit
			require_once(Route::getModelPath('unit'));
			
			return mUnit::getNamaUnit($conn,$kodeunit);
		}
		
		function getNamaParentUnit($conn,$kodeunit) {
			// ambil model unit
			require_once(Route::getModelPath('unit'));
			
			return mUnit::getNamaParentUnit($conn,$kodeunit);
		}
		
		function getJalurCamaba($conn,$key){
			$sql="SELECT nopendaftar, periodedaftar, jalurpenerimaan, idgelombang FROM pendaftaran.pd_pendaftar WHERE nopendaftar='$key' ";
			$ok=$conn->SelectLimit($sql);
			
			$data  =$ok->FetchRow();
			$jalur =$data['periodedaftar']."-".$data['jalurpenerimaan']."-".$data['idgelombang'];
			
			return $jalur;
		}
		
	}
?>
