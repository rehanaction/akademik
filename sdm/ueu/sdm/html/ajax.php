<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// empty buffer
	ob_clean();
	
	$conn->debug = false;
	
	// require tambahan
	require_once(Route::getModelPath('pegawai'));
	require_once(Route::getUIPath('combo'));
	
	// variabel reuqest
	$f = $_REQUEST['f'];
	$q = $_REQUEST['q'];
	
	// filtering
	if(is_array($q)) {
		for($i=0;$i<count($q);$i++)
			$q[$i] = CStr::removeSpecial($q[$i]);
	}
	else
		$q = CStr::removeSpecial($q);
	
	// option jurusan
	if($f == 'acpt') {
		require_once(Route::getModelPath('riwayat'));
		
		$a_data = mRiwayat::find($conn,$q,"kodept || ' - ' || namapt",'kodept',"sdm.ms_pt",'namapt');
		
		echo json_encode($a_data);
	}
	else if($f == 'acfakultas') {
		require_once(Route::getModelPath('riwayat'));
		
		$a_data = mRiwayat::find($conn,$q,"kodefakultas || ' - ' || namafakultas",'kodefakultas',"sdm.ms_fakultas",'namafakultas');
		
		echo json_encode($a_data);
	}
	else if($f == 'acjurusan') {
		require_once(Route::getModelPath('riwayat'));
		
		$a_data = mRiwayat::find($conn,$q,"kodejurusan || ' - ' || namajurusan",'kodejurusan',"sdm.ms_jurusan",'namajurusan');
		
		echo json_encode($a_data);
	}
	else if($f == 'acbidang') {
		require_once(Route::getModelPath('riwayat'));
		
		$a_data = mRiwayat::find($conn,$q,"kodebidang || ' - ' || namabidang",'kodebidang',"sdm.ms_bidang",'namabidang');
		
		echo json_encode($a_data);
	}
	else if($f == 'optjurusan') {
		$a_jurusan = mCombo::jurusan($conn,$q[0]);
		$t_jurusan = $q[1];
		
		echo UI::createOption($a_jurusan,$t_jurusan);
	}
	else if($f == 'optbidangstudi') {
		if(empty($q[0])) {
			$a_bs = array();
			
			echo UI::createOption($a_bs,'',true,'-- Pilih Jurusan terlebih dahulu --');
		}
		else {
			$a_bs = mCombo::bidangStudi($conn,$q[0]);
			$t_bs = $q[1];
			
			echo UI::createOption($a_bs,$t_bs);
		}
	}
	else if($f == 'optkabupaten') {
		if(empty($q[0]) or $q[0] == 'null') {
			$a_kabupaten = array();
			$a_kecamatan = array();
			$a_kelurahan = array();
			
			echo UI::createOption($a_kabupaten,'',true,'-- Pilih Propinsi terlebih dahulu --').':::'.UI::createOption($a_kecamatan,'',true,'-- Pilih Kota/Kabupaten terlebih dahulu --').':::'.UI::createOption($a_kelurahan,'',true,'-- Pilih Kecamatan terlebih dahulu --');
		}
		else {
			$a_kabupaten = mPegawai::kabupaten($conn,$q[0]);
			$t_kabupaten = $q[1];
			
			echo UI::createOption($a_kabupaten,$t_kabupaten);
		}
	}
	else if($f == 'optkecamatan') {
		if(empty($q[0]) or $q[0] == 'null') {
			$a_kecamatan = array();
			$a_kelurahan = array();
			
			echo UI::createOption($a_kecamatan,'',true,'-- Pilih Kota/Kabupaten terlebih dahulu --').':::'.UI::createOption($a_kelurahan,'',true,'-- Pilih Kecamatan terlebih dahulu --');
		}
		else {
			$a_kecamatan = mPegawai::kecamatan($conn,$q[0]);
			$t_kecamatan = $q[1];
			
			echo UI::createOption($a_kecamatan,$t_kecamatan);
		}
	}
	else if($f == 'optkelurahan') {
		if(empty($q[0]) or $q[0] == 'null') {
			$a_kelurahan = array();
			
			echo UI::createOption($a_kelurahan,'',true,'-- Pilih Kecamatan terlebih dahulu --');
		}
		else {
			$a_kelurahan = mPegawai::kelurahan($conn,$q[0]);
			$t_kelurahan = $q[1];
			
			echo UI::createOption($a_kelurahan,$t_kelurahan);
		}
	}
	else if($f == 'acnamapegawai') { //nama pegawai		
		$a_data = mPegawai::find($conn,$q,"coalesce(nik||' - ','')|| sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)",'idpegawai',"sdm.ms_pegawai",'namadepan');
		
		echo json_encode($a_data);
	}
	else if($f == 'acnamapegawaiunit') { //nama pegawai dengan unit kerja
		$q = strtolower($q);
		
		$sql = "select top 20 p.idpegawai,coalesce(p.nik||' - ','')|| sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)||' - '||u.namaunit as namalengkap
				from sdm.ms_pegawai p
				left join sdm.ms_unit u on u.idunit = p.idunit
				where lower(cast(sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)||' - '||u.namaunit as varchar(1000))) like '%$q%'
				order by namalengkap";
        $rs = $conn->Execute($sql);
		
		while($row = $rs->FetchRow()) {			
			$a_data[] = array('key' => $row['idpegawai'], 'label' => $row['namalengkap']);
		}
		
		echo json_encode($a_data);
	}
	else if($f == 'acdosen') { //nama pegawai yang dosen
		$q = strtolower($q);
		
		$sql = "select top 20 idpegawai,coalesce(nik||' - ','')|| sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
				from sdm.ms_pegawai 
				where idtipepeg in ('D','AD') and lower(cast(sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as varchar(1000))) like '%$q%'
				order by namalengkap";
        $rs = $conn->Execute($sql);
		
		while($row = $rs->FetchRow()) {			
			$a_data[] = array('key' => $row['idpegawai'], 'label' => $row['namalengkap']);
		}
		
		echo json_encode($a_data);
	}
	else if($f == 'acpejabat') { //nama pegawai		
		$a_data = mPegawai::find($conn,$q,"coalesce(nik||' - ','')|| namalengkap || ' - ' || jabatanstruktural",'idpegawai',"sdm.v_pejabat",'namalengkap');
		
		echo json_encode($a_data);
	}
	else if($f == 'acpejabatatasan') { //nama pejabatatasan
		$q = strtolower($q);
		
		$sql = "select top 20 idpegawai,coalesce(nik||' - ','')|| namalengkap || ' - ' || jabatanstruktural as pejabat,idjstruktural
				from sdm.v_pejabat 
				where lower(cast(coalesce(nik||' - ','')|| namalengkap || ' - ' || jabatanstruktural as varchar(1000))) like '%$q%'
				order by namalengkap";
        $res = $conn->GetArray($sql);

		echo json_encode($res);
	}
	else if($f == 'acunit') { //nama unit
		$q = strtolower($q);
		
		$sql = "select top 20 idunit,namaunit
				from sdm.ms_unit 
				where lower(cast(namaunit as varchar(1000))) like '%$q%'
				order by idunit";
        $res = $conn->GetArray($sql);

		echo json_encode($res);
	}
	else if($f == 'gsisacuti') { //mendapatkan sisa cuti
		require_once(Route::getModelPath('cuti'));
			
		$idpegawai = $q[0];
		$jnscuti = $q[1];
		$tglm = CStr::formatDate($q[2]);
		$tgls = CStr::formatDate($q[3]);
		$tglp = CStr::formatDate($q[4]);
		$thnm = substr($tglm,0,4);
		$thns = substr($tgls,0,4);
		$thnp = substr($tglp,0,4);
		
		$sisadb = $conn->GetOne("select coalesce(sdm.get_sisacuti('$idpegawai','$jnscuti','$tglm'),0)");
		$standar = $conn->GetOne("select standarcuti from sdm.ms_cuti where idjeniscuti = '$jnscuti'");
					
		//keterangan pada saat form cuti ditampilkan
		if(empty($tgls)){
			//pengecekan tanggal
			/*if($tglp > $tglm){
				echo "err|Tanggal pengajuan anda lebih besar dari pada tanggal cuti";
				exit();
			}*/
				
			if(empty($standar)){
				echo "clear|tidak ada standarisasi cuti";
				exit();
			}else{						
				//pengecekan dengan jatah cuti
				if ((int)($sisadb) < 0){
					echo "err|Pengambilan cuti anda melebihi jatah cuti";
					exit();
				}else{
					echo "clear|".$sisadb;
					exit();
				}
			}
		}
					
		if(!empty($tglm) and !empty($tgls)){						
			//pengecekan tanggal mulai lebih besar dari tanggal selesai
			if($tglm > $tgls){
				echo "err|Tanggal mulai tidak boleh lebih besar dari pada tanggal selesai|";
				exit();
			}
								
			//Menghitung lama cuti dengan mengabaikan hari sabtu dan minggu	
			$lama = mCuti::getLamaCuti($conn,$tglm,$tgls,$idpegawai);
			
			//bila beda periode
			if($thnm < $thns){
				$tglakhir = $thnm.'-12-31';
				$lama = mCuti::getLamaCuti($conn,$tglm,$tglakhir,$idpegawai);
				
				//lamabaru
				$tglb = $thns.'-01-01';
				$lamabaru = mCuti::getLamaCuti($conn,$tglb,$tgls,$idpegawai);
			}
												
			//pengecekan lama cuti = 0 (kemungkinan hari cuti diambil pada hari libur)
			if($lama == 0){
				echo "err|Pengajuan hari cuti anda pada hari non efektif atau hari libur";
				exit();
			}
			
			if(empty($standar)){
				echo "clear|tidak ada standarisasi cuti";
				exit();
			}else{				
				//bila beda periode
				if(($thnp < $thnm) or ($thnp < $thns)){
					$sisadb = $conn->GetOne("select coalesce(sdm.get_sisacuti('$idpegawai','$jnscuti','$tglp'),0)");
					$sisadb = $sisadb - $lama;
					
					echo "alert|".$sisadb."|Tanggal cuti anda melewati periode cuti.\nApakah ingin mengambil jatah cuti ".($lamabaru)." hari dari tahun depan?";
					exit();
				}
				
				//pengecekan dengan jatah cuti
				if ((int)($sisadb - $lama) < 0){
					echo "err|Pengambilan cuti anda melebihi jatah cuti";
					exit();
				}else{
					$sisadb = $sisadb - $lama;
					echo "clear|".$sisadb;
					exit();
				}
			}
		}
	}
	else if($f == 'optjenispegawai') {		
		if(empty($q[0]) or $q[0] == 'null') {
			$a_jenispegawai = array();
			
			echo UI::createOption($a_jenispegawai,'',true,'-- Pilih Tipe Pegawai terlebih dahulu --');
		}
		else {
			require_once(Route::getModelPath('riwayat'));
			
			$a_jenispegawai = mRiwayat::jenisPegawai($conn,$q[0]);
			$t_jenispegawai = $q[1];
			
			echo UI::createOption($a_jenispegawai,$t_jenispegawai);
		}
	}
	else if($f == 'optjenispegawaibaru') {		
		if(empty($q[0]) or $q[0] == 'null') {
			$a_jenispegawai = array();
			
			echo UI::createOption($a_jenispegawai,'',true,'-- Pilih Tipe Pegawai terlebih dahulu --');
		}
		else {
			require_once(Route::getModelPath('riwayat'));
			
			$a_jenispegawai = mRiwayat::jenisPegawaiBaru($conn,$q[0]);
			$t_jenispegawai = $q[1];
			
			echo UI::createOption($a_jenispegawai,$t_jenispegawai);
		}
	}
	else if($f == 'optkelompok') {		
		if(empty($q[0]) or $q[0] == 'null') {
			$a_kelompok = array();
			
			echo UI::createOption($a_kelompok,'',true,'-- Pilih Jenis Pegawai terlebih dahulu --');
		}
		else {		
			require_once(Route::getModelPath('riwayat'));
				
			$a_kelompok = mRiwayat::kelompokpeg($conn,$q[0]);
			$t_kelompok = $q[1];
			
			echo UI::createOption($a_kelompok,$t_kelompok);
		}
	}	
	else if($f == 'acnamakelurahan') { //nama kelurahan	
		$select = "coalesce(prop.namapropinsi,'')||', '||coalesce(kab.namakabupaten,'')||', '||coalesce(kec.namakecamatan,'')||', '||coalesce(kel.namakelurahan,'')";
		$tabjoin = "sdm.lv_kelurahan kel 
					left join sdm.lv_propinsi prop on prop.idpropinsi = substring(kel.idkelurahan,1,2)
					left join sdm.lv_kabupaten kab on kab.idkabupaten = substring(kel.idkelurahan,1,4)
					left join sdm.lv_kecamatan kec on kec.idkecamatan = substring(kel.idkelurahan,1,6)";
					
		$a_data = mPegawai::find($conn,$q,$select,"idkelurahan",$tabjoin,'kel.namakelurahan');
		
		echo json_encode($a_data);
	}
	else if($f == 'gmkpensiun') { //masa kerja pensiun
		$idpegawai = $q[0];
		$tglpensiun = CStr::formatDate($q[1]);
		
		$hasil = $conn->GetOne("select sdm.hitung_mkpensiun($idpegawai,'$tglpensiun')");
		 
		echo $hasil;
	}
	else if($f == 'gdurasi') { //durasi tanggal
		$tglmulai = CStr::formatDate($q[0]);
		$tglselesai = CStr::formatDate($q[1]);
		
		$hasil = '';
		if(!empty($tglmulai) and !empty($tglselesai))
			$hasil = $conn->GetOne("select datediff(month,'$tglmulai','$tglselesai')");
		 
		echo $hasil;
	}	
	else if($f == 'gmasaikatan') { //durasi tugas
		$jenis = $q[0];
		$biaya = $q[1];
		$bln = $q[2];
		
		$data = $conn->GetRow("select lamastudidalam,lamastudiluar from sdm.ms_biayatugasbelajar where idbiaya = '$biaya'");
		$dalam = str_replace('n',$bln,$data['lamastudidalam']);
		$luar = str_replace('n',$bln,$data['lamastudiluar']);
		
		
		if($dalam != '' or $luar != ''){
			if($jenis == 'L')
				eval('$length = '.$luar.';');
			else
				eval('$length = '.$dalam.';');
		}
		
		echo $length;
	}
	else if($f == 'gnik'){ //melihat nik terakhir 
		$kel = $q;
		if(empty($kel))
			echo '0|<font color="red">Pilih Kelompok terlebih dahulu</font>';
		else{
			$thn = substr(date('Y'),2,2);
			$bln = date('m');
			$urut = $conn->GetOne("select coalesce(max(cast(substring(nik,6,4) as int)),0)+1 from sdm.ms_pegawai where len(nik) = 9");
			$urutnik = str_pad($urut,4,"0",STR_PAD_LEFT);
			
			$nik = $kel.$thn.$bln.$urutnik;	
			
			$lastnik = $conn->GetOne("select top 1 nik from sdm.ms_pegawai where len(nik) = 9 order by cast(substring(nik,6,4) as int) desc");

			echo '1|'.$nik.'|'.$lastnik;
		}
	}
	
	else if($f == 'gsksbidang'){ //mengecek jumlah sks, bila kegiatan pengajaran			
		$r_key = $q[0];
		$sks = $q[1];
		$thn = $q[2];
		$smtr = $q[3];
		$err = 0;
		$sql = "select top 1 idjfungsional,tmtmulai from sdm.pe_rwtfungsional where idpegawai = $r_key and isvalid = 'Y' order by tmtmulai desc";
		$jab = $conn->GetRow($sql);
		
		$sql = "select coalesce(sum(sksdiakui),0) as jmlsks from sdm.ak_bidang1b 
				where idpegawai = $r_key and tglawal > '".$jab['tmtmulai']."' and (statusvalidasi = '' or statusvalidasi is null)
				and ismengajar = 'Y' and thnakademik = '$thn' and semester = '$smtr'";
				
		$jmlsks = $conn->GetOne($sql);
		$totsks = (int)$jmlsks + (int)$sks;
		
		$aa = array('31','32'); //asisten ahli
		$bb = array('33','34','41','42','43','44','45'); //lektor ke atas		
		
		//asisten ahli
		if(in_array($jab['idjfungsional'],$aa)){
			if($jmlsks >= 12){
				$err = 1;
			}else{
				if($totsks <= 10 and $totsks > 0){
					$mk1 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 21");
					$sksdiakui1 = $sks;
				}else{
					if($jmlsks >= 10){
						$mk1 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 22");
						$sksdiakui1 = 12 - $jmlsks; //sisa sks yang diakui						
					}else if($jmlsks < 10 and $jmlsks >= 0){
						$mk1 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 21");
						$sksdiakui1 = 10 - $jmlsks; //sisa sks yang diakui
						
						$mk2 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 22");
						if($totsks < 12){
							$sksdiakui2 = 12 - $totsks;
						}else{
							$skss = (int)$sks - (int)$sksdiakui1;
							$sksdiakui2 = $skss - ($totsks-12); //sisa sks yang diakui
						}
					}
				}
			}
		}
		
		//lektor ke atas
		else if(in_array($jab['idjfungsional'],$bb)){
			if($jmlsks >= 12){
				$err = 1;
			}else{
				if($totsks <= 10 and $totsks > 0){
					$mk1 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 23");
					$sksdiakui1 = $sks;
				}else{
					if($jmlsks >= 10){
						$mk1 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 24");
						$sksdiakui1 = 12 - $jmlsks; //sisa sks yang diakui
					}else if($jmlsks < 10 and $jmlsks >= 0){
						$mk1 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 23");
						$sksdiakui1 = 10 - $jmlsks; //sisa sks yang diakui
						
						$mk2 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan from sdm.ms_penilaian where idkegiatan = 24");
						if($totsks < 12){
							$sksdiakui2 = 12 - $totsks;
						}else{
							$skss = (int)$sks - (int)$sksdiakui1;
							$sksdiakui2 = $skss - ($totsks-12); //sisa sks yang diakui
						}
					}
				}
			}			
		}
		
		//pengajar atau tidak punya jab. akademik
		else{
			$mk1 = $conn->GetRow("select idkegiatan,kodekegiatan||' - '||namakegiatan as kegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 21");
			$sksdiakui1 = $sks;
		}
				
		echo $err.'|'.$sksdiakui1.'|'.$mk1['idkegiatan'].'|'.$mk1['kegiatan'].'|'.$mk1['stdkredit'].'|'.$sksdiakui2.'|'.$mk2['idkegiatan'];
	}
	
	else if($f == 'gshift') { //mendapatkan shift pegawai
		$idpegawai = $q[0];
		$tglshift = CStr::formatDate($q[1]);
		
		//cek di pe_rwtkerja
		$sql = "select top 1 kodekelkerja from sdm.pe_rwtharikerja where idpegawai = $idpegawai order by tglberlaku desc";
		$kodekelkerja = $conn->GetOne($sql);
		
		if(!empty($kodekelkerja)){
			$fieldhari = $conn->GetOne("select datepart(dw,'$tglshift')");
			if($fieldhari == '1')
				$hari = 'minggu';
			else if($fieldhari == '2')
				$hari = 'senin';
			else if($fieldhari == '3')
				$hari = 'selasa';
			else if($fieldhari == '4')
				$hari = 'rabu';
			else if($fieldhari == '5')
				$hari = 'kamis';
			else if($fieldhari == '6')
				$hari = 'jumat';
			else if($fieldhari == '7')
				$hari = 'sabtu';
			
			$kodejam = $conn->GetOne("select $hari from sdm.ms_kelkerja where kodekelkerja = '$kodekelkerja'");
			$jam = $conn->GetRow("select jamdatang,jampulang from sdm.lv_jamhadir where kodejamhadir = '$kodejam'");
		}
		else{
			$jam = $conn->GetRow("select jamdatang,jampulang from sdm.v_pegawaishift where idpegawai = $idpegawai and tglshift = '$tglshift'");
		}
				 
		echo $jam['jamdatang'].'|'.$jam['jampulang'];
	}
	
	else if($f == 'ghadir') { //mendapatkan hadir pegawai
		$idpegawai = $q[0];
		$tglpresensi = CStr::formatDate($q[1]);
		
		//cek di pe_rwtkerja
		$sql = "select jamdatang,jampulang from sdm.pe_presensidet where idpegawai = $idpegawai and tglpresensi = '$tglpresensi'";
		$jam = $conn->GetRow($sql);
				 
		echo $jam['jamdatang'].'|'.$jam['jampulang'];
	}
	
	else if($f == 'gjmlpinjam') { //mendapatkan hadir pegawai
		$jmldisetujui = empty($q[0]) ? 0 : str_replace('.','',$q[0]);
		$biayaadministrasi = empty($q[1]) ? 0 : str_replace('.','',$q[1]);
		
		$totalpinjaman = $jmldisetujui + $biayaadministrasi;
		
		echo CStr::formatNumber($totalpinjaman);
	}
	
	else if($f == 'gsaldopinjamam') { //mendapatkan hadir pegawai
		$bayar = empty($q[0]) ? 0 : str_replace('.','',$q[0]);
		$saldo = empty($q[1]) ? 0 : str_replace('.','',$q[1]);
		
		$saldoskrg = $saldo - $bayar;
		
		echo CStr::formatNumber($saldoskrg);
	}
	
	else if($f == 'gbyrpinjam') { //mendapatkan hadir pegawai
		$jmlangs = empty($q[0]) ? 0 : str_replace('.','',$q[0]);
		$jmlbayar = empty($q[1]) ? 0 : str_replace('.','',$q[1]);
		
		if($jmlangs > $jmlbayar){
			echo 'err|Jumlah angsuran melebihi jumlah yang dibayarkan';
			exit();
		}else if($jmlangs < $jmlbayar){
			echo 'err|Jumlah angsuran lebih kecil daripada jumlah yang dibayarkan';
			exit();
		}
	}
	else if($f == 'gpotkehadiran') { //durasi tanggal
		$proctelat = empty($q[0]) ? '0' : $q[0]/100;
		$procpd = empty($q[1]) ? '0' : $q[1]/100;
		$proc = $proctelat + $procpd;
		$tglpresensi = CStr::formatDate($q[2]);
		$idpegawai = $q[3];
		
		$hasil ='';
		if(!empty($tglpresensi) and !empty($idpegawai))
			$tarifpotkehadian = $conn->GetOne("select tarifpotkehadiran from sdm.pe_presensidet where idpegawai='$idpegawai' and tglpresensi='$tglpresensi'");
		
		echo CStr::formatNumber($tarifpotkehadian * $proc);
	}

	else if($f == 'gpottransport') { //durasi tanggal
		$proctelat = empty($q[0]) ? '0' : $q[0]/100;
		$procpd = empty($q[1]) ? '0' : $q[1]/100;
		$proc = $proctelat + $procpd;
		$tglpresensi = CStr::formatDate($q[2]);
		$idpegawai = $q[3];
		
		$hasil ='';
		if(!empty($tglpresensi) and !empty($idpegawai))
			$tarifpottransport = $conn->GetOne("select tarifpottransport from sdm.pe_presensidet where idpegawai='$idpegawai' and tglpresensi='$tglpresensi'");
		
		echo CStr::formatNumber($tarifpottransport * $proc);
	}
?>
