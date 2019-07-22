<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// empty buffer
	ob_clean();
	
	$conn->debug = false;
	
	// require tambahan
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
	
	// function
	if($f == 'acuser') {
		require_once(Route::getModelPath('user'));
		
		$a_data = mUser::find($conn,$q,"username||' - '||userdesc",'username');
		
		echo json_encode($a_data);
	}
	else if($f == 'acmahasiswa') {
		require_once(Route::getModelPath('mahasiswa'));
		
		$a_data = mMahasiswa::find($conn,$q,"nim||' - '||nama",'nim');
		
		echo json_encode($a_data);
	}
	
	else if($f == 'acpegawai') {
		require_once(Route::getModelPath('pegawai'));
		
		$a_data = mPegawai::find($conn,$q,"nip||' - '||nama",'nip');
		
		echo json_encode($a_data);
	}
	else if($f == 'acdosen') {		
		require_once(Route::getModelPath('pegawai'));
		
		$a_data = mPegawai::findDosen($conn,$q,"nip||' - '||akademik.f_namalengkap(gelardepan,nama,gelarbelakang)",'nip');
		
		echo json_encode($a_data);
	}
	else if($f == 'acmhswali') {
		$t_nip = $q[0];
		$t_str = $q[1];
		
		require_once(Route::getModelPath('konsultasi'));
		
		$a_data = mKonsultasi::findMhsWali($conn,$t_str,$t_nip,"m.nim||' - '||m.nama",'m.nim');
		
		echo json_encode($a_data);
	}
	else if($f == 'acdosenwali') {
		$t_nim = $q[0];
		$t_str = $q[1];
		
		require_once(Route::getModelPath('konsultasi'));
		
		$a_data = mKonsultasi::findDosenWali($conn,$t_str,$t_nim,"p.nip||' - '||p.nama",'p.nip');
		
		echo json_encode($a_data);
	}
	else if($f == 'acmahasiswakp') {
		$t_periode = $q[0];
		$t_str = $q[1];
		
		require_once(Route::getModelPath('kerjapraktek'));
		
		$a_data = mKerjaPraktek::findPengambil($conn,$t_str,$t_periode,"k.nim||' - '||m.nama",'k.nim');
		
		echo json_encode($a_data);
	}
	else if($f == 'acpembimbing') {
		$t_idta = $q[0];
		$t_str = $q[1];
		
		require_once(Route::getModelPath('ta'));
		
		$a_data = mTa::findPembimbing($conn,$t_str,$t_idta);
		
		echo json_encode($a_data);
	}
	//matakuliah
	else if($f == 'acmatkul') { 
		require_once(Route::getModelPath('matakuliah'));
		
		$a_data = mMatakuliah::findMatkul($conn,$q,"kodemk||' - '||namamk",'kodemk');
		
		echo json_encode($a_data);
	}
	
	else if($f == 'optjurusan') {
		$a_jurusan = mCombo::jurusan($conn,$q[0]);
		$t_jurusan = $q[1];
		
		echo UI::createOption($a_jurusan,$t_jurusan);
	}
	else if($f == 'optpilihan2') {
		$pil1 = $q[0];
		$sistemkuliah = $q[2] ? $q[2] : $q[1];
		$a_pilihan2 = mCombo::pilihan2($conn,$pil1,$sistemkuliah);
		$t_pilihan2 = $q[3];
		
		echo UI::createOption($a_pilihan2,$t_pilihan2);
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
	else if($f == 'optkota') {
		if(empty($q[0])) {
			$a_kota = array();
			
			echo UI::createOption($a_kota,'',true,'-- Pilih Propinsi terlebih dahulu --');
		}
		else {
			$a_kota = mCombo::getArrkota($conn,$q[0]);
			$t_kota = $q[1];
			
			echo UI::createOption($a_kota,$t_kota);
		}
	}
	else if($f == 'optsmu') {
		if(empty($q[0])) {
			$a_smu = array();
			
			echo UI::createOption($a_kota,'',true,'-- Pilih Kota terlebih dahulu --');
		}
		else {
			$a_smu = mCombo::getArrsmu($conn,$q[0]);
			$t_smu = $q[1];
			
			echo UI::createOption($a_smu,$t_smu);
		}
	}
	
	else if($f == 'getKuota') {
		$id = $q[0];		
		$hasil = $conn->GetRow("select coalesce(jumlahpeserta,0) as jumlahpeserta,kuota from pendaftaran.pd_jadwal where idjadwal='$id'");
		 
		echo $hasil['jumlahpeserta']."#".$hasil['kuota'];
		// return $kuota;
	}
	else if($f == 'getUrl') {
		$periode = $q[0];
		$gelombang = $q[1];
		$jalur = $q[2];
	
		if (isset ($_SESSION[SITE_ID]['URL']))
			unset ($_SESSION[SITE_ID]['URL']); 
			
		if (isset ($_SESSION[SITE_ID]['PENDAFTAR']))
			unset ($_SESSION[SITE_ID]['PENDAFTAR']); 

		
		$_SESSION[SITE_ID]['URL']['periodedaftar'] = $periode;
		$_SESSION[SITE_ID]['URL']['gelombang'] = $gelombang;
		$_SESSION[SITE_ID]['URL']['jalurpenerimaan'] = $jalur;	
		$_SESSION[SITE_ID]['URL']['gratis'] = true;	
		
	}
	else if($f == 'getDetailSmu') {
		$idsmu = $q[0];		
		$hasil = $conn->GetRow("select alamatsmu,telpsmu from pendaftaran.lv_smu where idsmu='$idsmu'");
		 
		echo $hasil['alamatsmu']."#".$hasil['telpsmu'];
		// return $kuota;
	}
	else if($f == 'trpadanan') {
		require_once(Route::getModelPath('transkrip'));
		require_once(Route::getModelPath('ekivaturan'));
		
		$row = mTranskrip::getKeyRecord($q);
		$a_data = mEkivAturan::getListLama($conn,$row['thnkurikulum'],$row['kodemk'],$row['kodeunit']);
		
		$i = 0;
		foreach($a_data as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG';
			
			$t_key = $row['thnkurikulum'].'|'.$row['kodemk'].'|'.$row['kodeunit'];
?>
<tr valign="top" class="<?= $rowstyle ?>">
	<td align="center"><input type="radio" name="padanan" value="<?= $t_key ?>"<?= (($i++ == 0) ? ' checked' : '') ?>></td>
	<td align="center"><?= $row['thnkurikulum'] ?></td>
	<td align="center"><?= $row['kodemk'] ?></td>
	<td><?= $row['namamk'] ?></td>
	<td align="center"><?= $row['sks'] ?></td>
</tr>
<?php
		}
	}
	else if($f == 'trpaket') {
		require_once(Route::getModelPath('kurikulum'));
		
		list($periode,$kurikulum,$kodeunit,$semmk) = explode('|',$q);
		$a_data = mKurikulum::getListMKPaket($conn,$periode,$kurikulum,$kodeunit,$semmk);
		
		$i = 0;
		foreach($a_data as $row) {
			if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG';
			
			// cek kelas
			if(empty($row['kelas']))
				$t_bgtd = '#FF0';
			else
				$t_bgtd = '';
?>
<tr valign="top" class="<?= $rowstyle ?>">
	<td align="right"><?= ++$i ?>.</td>
	<td align="center"><?= $row['kodemk'] ?></td>
	<td><?= $row['namamk'] ?></td>
	<td align="center"><?= $row['sks'] ?></td>
	<td align="center"<?= empty($t_bgtd) ? '' : ' bgcolor="'.$t_bgtd.'"' ?>><?= $row['kelas'] ?></td>
</tr>
<?php
		}
	}
	else if ($f=='getSmu'){ 
		 
		require_once(Route::getModelPath('smu'));
		
		$a_data = mSmu::findSmu($conn,$q,"m.namasmu||' - '||t.namakota",'idsmu');
		
		echo json_encode($a_data);	
	}	
?>
