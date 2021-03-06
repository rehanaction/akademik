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
	else if($f == 'acpendaftar') {
		require_once(Route::getModelPath('mahasiswa'));

		$a_data = mMahasiswa::findPendaftar($conn,$q,"nopendaftar||' - '||nama",'nopendaftar');

		echo json_encode($a_data);
	}
	else if($f == 'acmahasiswa') {

		require_once(Route::getModelPath('mahasiswa'));

		$a_data = mMahasiswa::find($conn,$q,"nim||' - '||nama",'nim');

		echo json_encode($a_data);
	}
	else if($f == 'acmahasiswauser') {

		require_once(Route::getModelPath('mahasiswa'));

		$a_data = mMahasiswa::findUser($conn,$q,"nim||' - '||nama",'userid');

		echo json_encode($a_data);
	}

	else if($f == 'acmahasiswakemahasiswaan') {

		require_once(Route::getModelPath('mahasiswa'));

		$a_data = mMahasiswa::findMhsBeasiswa($conn,$q,"nim||' - '||nama",'nim');

		echo json_encode($a_data);
	}

	else if($f == 'acsmu') {

		require_once(Route::getModelPath('smu'));

		$a_data = mSmu::find($conn,$q,"idsmu||' - '||namasmu",'idsmu');

		echo json_encode($a_data);
	}
	else if($f == 'acpegawai') {
		require_once(Route::getModelPath('pegawai'));

		$a_data = mPegawai::find($conn,$q,"idpegawai::text||' - '||akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)",'idpegawai');

		echo json_encode($a_data);
	}else if($f == 'acpegawaipenunjang') {
		require_once(Route::getModelPath('pegawaipenunjang'));

		$a_data = mPegawaiPenunjang::find($conn,$q,"nopegawai||' - '||namapegawai",'nopegawai');

		echo json_encode($a_data);
	}
	else if($f == 'acjurusan') {
		require_once(Route::getModelPath('unit'));

		$a_data = mUnit::findJurusan($conn,$q,"kodeunit||' - '||namaunit",'kodeunit');

		echo json_encode($a_data);
	}
	else if($f == 'acdosen') {
		require_once(Route::getModelPath('pegawai'));

		$a_data = mPegawai::findDosen($conn,$q,"idpegawai::text||' - '||akademik.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang)",'idpegawai');
		echo json_encode($a_data);
	}
	else if($f == 'acpengawas') {
		require_once(Route::getModelPath('user'));

		$a_data = mUser::findPengawas($conn,$q,"username::text||' - '||userdesc",'username');
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

		$a_data = mKonsultasi::findDosenWali($conn,$t_str,$t_nim,"p.idpegawai::text||' - '||akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)",'p.idpegawai');

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
	else if($f == 'acmatkulkurikulum') {
		//$conn->debug=true;
		require_once(Route::getModelPath('kurikulum'));
		$kurikulum=$_GET['kurikulum'];
		$a_data = mKurikulum::findmkKurikulum($conn,$q,"kodemk||' - '||namamk||' - '||sks||' sks'",'kodemk',$kurikulum);

		echo json_encode($a_data);
	}
	else if($f == 'rep_acmatkul') {
		//$conn->debug=true;
		require_once(Route::getModelPath('kelas'));
		$periode=$_GET['periode'];
		$kodeunit=$_GET['kodeunit'];
		$q=$_GET['q'];
		$a_data = mKelas::findMatkul($conn,$q,$periode,$kodeunit,"m.kodemk||' - '||m.namamk||' - '||'('||k.thnkurikulum||' - '||k.kelasmk||')'",'k.kodemk');

		echo json_encode($a_data);
	}
	else if($f == 'rep_acmatkulview') {
		//$conn->debug=true;
		require_once(Route::getModelPath('kelas'));
		$param=explode('|',$_GET['p']);
		$periode=$param[0];
		$kodeunit=$param[1];
		$q=$_GET['q'];
		$a_data = mKelas::findMatkul($conn,$q,$periode,$kodeunit,"namamk",'kodemk',true);

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
	else if($f == 'optkota') {
		if(empty($q[0])) {
			$a_kota = array();

			echo UI::createOption($a_kota,'',true,'-- Pilih Propinsi terlebih dahulu --');
		}
		else {
			$a_kota = mCombo::kota($conn,$q[0]);
			$t_kota = $q[1];

			echo UI::createOption($a_kota,$t_kota);
		}
	}
	else if($f == 'optasuransi') {
		require_once(Route::getModelPath('mhsasuransi'));
		if(empty($q[0])) {
			$a_asuransi = array();

			echo UI::createOption($a_asuransi,'',true,'-- Pilih Mahasiswa terlebih dahulu --');
		}
		else {
			$a_asuransi = mMhsasuransi::getAsuransiMhs($conn,$q[0]);
			$t_asuransi = $q[1];
			if(!empty($a_asuransi))
				echo UI::createOption($a_asuransi,$t_asuransi);
			else
				echo UI::createOption(array(''=>'Tidak Mempunyai Asuransi'),$t_asuransi);
		}
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
		//$conn->debug=true;
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
	else if($f == 'inDetailKelas') {
	//$conn->debug=true;
		require_once(Route::getModelPath('detailkelas'));
		$arr_kelas=explode('|',$q[8]);
		$record=array();
		$record['thnkurikulum']=$arr_kelas[0];
		$record['kodemk']=$arr_kelas[1];
		$record['kodeunit']=$arr_kelas[2];
		$record['periode']=$arr_kelas[3];
		$record['kelasmk']=$arr_kelas[4];
		$record['pertemuan']=$q[0];
		$record['tglpertemuan']=date('Y-m-d',strtotime($q[1]));
		$record['jammulai']=CStr::cStrNull(str_replace(':','',$q[2]));
		$record['jamselesai']=CStr::cStrNull(str_replace(':','',$q[3]));
		$record['koderuang']=$q[4];
		$record['jeniskul']=$q[5];
		$record['nohari']=$q[6];
		$record['kelompok']=$q[7];
		$cek= mDetailKelas::cekKresJadwal($conn,$record['periode'],$record['tglpertemuan'],$record['koderuang'],$record['jammulai'],$record['jamselesai']);
		if($cek==1){
			$p_posterr=true;
			$p_postmsg='Ada Kres Dengan Jadwal Lain';
		}else{
			list($p_posterr,$p_postmsg) = mDetailKelas::insertCRecord($conn,$kolom,$record,$kosong);
		}
		if($p_posterr){
			echo '<div class="DivError">'.$p_postmsg.'</div>';
		}else{
			echo '<div class="DivSuccess">'.$p_postmsg.'</div>';
		}
		$r_key=$q[8];
		$c_insert=$q[9];
		$c_edit=$q[10];
		$c_delete=$q[11];
		if($record['jeniskul']=='P')
			require_once('data_detailkelaspraktikum.php');
		else
			require_once('data_detailkelas.php');
	}else if($f == 'upDetailKelas') {
		//$conn->debug=true;
		require_once(Route::getModelPath('detailkelas'));
		$arr_kelas=explode('|',$q[9]);
		$record=array();
		$record['pertemuan']=$q[1];
		$record['tglpertemuan']=date('Y-m-d',strtotime($q[2]));
		$record['jammulai']=CStr::cStrNull(str_replace(':','',$q[3]));
		$record['jamselesai']=CStr::cStrNull(str_replace(':','',$q[4]));
		$record['koderuang']=$q[5];
		$record['jeniskul']=$q[6];
		$record['nohari']=$q[7];
		$record['kelompok']=$q[8];
		$cek= mDetailKelas::cekKresJadwal($conn,$arr_kelas[3],$record['tglpertemuan'],$record['koderuang'],$record['jammulai'],$record['jamselesai']);
		if($cek==1){
			$p_posterr=true;
			$p_postmsg='Ada Kres Dengan Jadwal Lain';
		}else{
			list($p_posterr,$p_postmsg) = mDetailKelas::updateCRecord($conn,$kolom,$record,$q[0]);
		}
		if($p_posterr){
			echo '<div class="DivError">'.$p_postmsg.'</div>';
		}else{
			echo '<div class="DivSuccess">'.$p_postmsg.'</div>';
		}
		$r_key=$q[9];
		$c_insert=$q[10];
		$c_edit=$q[11];
		$c_delete=$q[12];
		require_once('data_detailkelas.php');
	}else if($f == 'delDetailKelas') {
		require_once(Route::getModelPath('detailkelas'));

		list($p_posterr,$p_postmsg) = mDetailKelas::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];

		if($record['jeniskul']=='P')
			require_once('data_detailkelaspraktikum.php');
		else
			require_once('data_detailkelas.php');
	}else if($f == 'setSPP') {
		require_once(Route::getModelPath('perwalian'));
		$record=array();
		$record['statusmhs']='A';
		$record['prasyaratspp']=-1;
		list($p_posterr,$p_postmsg) = mPerwalian::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'unsetSPP') {
		require_once(Route::getModelPath('perwalian'));
		$record=array();
		$record['statusmhs']='T';
		$record['prasyaratspp']=0;
		list($p_posterr,$p_postmsg) = mPerwalian::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'cekal') {
		require_once(Route::getModelPath('perwalian'));
		$record=array();
		$record['cekalakad']=-1;
		list($p_posterr,$p_postmsg) = mPerwalian::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'bukaCekal') {
		require_once(Route::getModelPath('perwalian'));
		$record=array();
		$record['cekalakad']=0;
		list($p_posterr,$p_postmsg) = mPerwalian::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'setKelompok') {
		for($i=1;$i<=$q[0];$i++){
			echo '<option value="'.$i.'">'.$i.'</option>';
		}
	}else if($f == 'setKelompokJ') {
		require_once(Route::getModelPath('kelas'));
		$conn->debug=true;
		//list($kurikulum,$kodemk,$kodeunit,$periode,$kelasmk)=explode('|',$q[0]);
		//$key=thnkurikulum,kodemk,kodeunit,periode,kelasmk

		if($q[1]=='R'){
			$data=mKelas::getData($conn,$q[0]);
			$kelompok=$data['keltutorial'];
		}
		else if ($q[1]=='P'){
			$data=mKelas::getData($conn,$q[0]);
			$kelompok=$data['kelpraktikum'];
		}

		if(empty($kelompok))
			$kelompok=1;
		for($i=1;$i<=$kelompok;$i++){
			echo '<option value="'.$i.'" '.($i==$q[2]?"selected":"").'>'.$i.'</option>';
		}
	}else if($f == 'delDatateman') {
		require_once(Route::getModelPath('kontakteman'));
		list($x1,$x2)=explode('|',$q[0]);
		$key2=$x2.'|'.$x1;
		list($p_posterr,$p_postmsg) = mKontakTeman::delete($conn,$q[0]);
		if($p_posterr){
			echo 'error7*';
		}else{
			list($p_posterr,$p_postmsg) = mKontakTeman::delete($conn,$key2);
			if($p_posterr){
				echo 'error7*';
			}
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_kontakteman.php');
	}else if($f == 'inDataTeman') {

		require_once(Route::getModelPath('kontakteman'));

		$record=array();
		$record['nim']=$q[1];
		$record['nimteman']=$q[0];
		list($p_posterr,$p_postmsg) = mKontakTeman::insertCRecord($conn,$kolom,$record,$kosong);
		if($p_posterr){
			echo 'error7*';
		}else{
			$record['nim']=$q[0];
			$record['nimteman']=$q[1];
			list($p_posterr,$p_postmsg) = mKontakTeman::insertCRecord($conn,$kolom,$record,$kosong);
			if($p_posterr){
				echo 'error7*';
			}
		}
		$r_key=$q[1];
		$c_insert=$q[2];
		$c_edit=$q[3];
		$c_delete=$q[4];
		require_once('data_kontakteman.php');
	}else if($f == 'upKeteraangan') {
		require_once(Route::getModelPath('perwalian'));
		$record=array();
		$record['keterangan']=$q[1];
		list($p_posterr,$p_postmsg) = mPerwalian::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'setUAS') {
		require_once(Route::getModelPath('krs'));

		list($t_kurikulum,$t_kodemk,$t_kodeunit,$r_periode,$t_kelasmk) = explode('|',$q[1]);
		$_key=$t_kurikulum.'|'.$t_kodemk.'|'.$t_kodeunit.'|'.$r_periode.'|'.$t_kelasmk.'|'.$q[0];
		$record=array();
		$record['isikutuas']=-1;
		list($p_posterr,$p_postmsg) = mKrs::updateCRecord($conn,$kolom,$record,$_key);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'setUTS') {
		require_once(Route::getModelPath('krs'));

		list($t_kurikulum,$t_kodemk,$t_kodeunit,$r_periode,$t_kelasmk) = explode('|',$q[1]);
		$_key=$t_kurikulum.'|'.$t_kodemk.'|'.$t_kodeunit.'|'.$r_periode.'|'.$t_kelasmk.'|'.$q[0];
		$record=array();
		$record['isikututs']=-1;
		list($p_posterr,$p_postmsg) = mKrs::updateCRecord($conn,$kolom,$record,$_key);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'unsetUAS') {
		require_once(Route::getModelPath('krs'));

		list($t_kurikulum,$t_kodemk,$t_kodeunit,$r_periode,$t_kelasmk) = explode('|',$q[1]);
		$_key=$t_kurikulum.'|'.$t_kodemk.'|'.$t_kodeunit.'|'.$r_periode.'|'.$t_kelasmk.'|'.$q[0];
		$record=array();
		$record['isikutuas']=0;
		list($p_posterr,$p_postmsg) = mKrs::updateCRecord($conn,$kolom,$record,$_key);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'unsetUTS') {
		require_once(Route::getModelPath('krs'));

		list($t_kurikulum,$t_kodemk,$t_kodeunit,$r_periode,$t_kelasmk) = explode('|',$q[1]);
		$_key=$t_kurikulum.'|'.$t_kodemk.'|'.$t_kodeunit.'|'.$r_periode.'|'.$t_kelasmk.'|'.$q[0];
		$record=array();
		$record['isikututs']=0;
		list($p_posterr,$p_postmsg) = mKrs::updateCRecord($conn,$kolom,$record,$_key);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'valid') {
		require_once(Route::getModelPath('kuliah'));
		require_once(Route::getModelPath('honordosen'));
		$kelas=$conn->Execute("update akademik.ak_kuliah set validhonorkuliah=-1 where ".mKuliah::getCondition($q[0],'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok'));
		if($kelas)
			$honor=$conn->Execute("update akademik.ak_honordosen set validhonor=-1 where ".mHonorDOsen::getCondition($q[0]));
		if($honor){
			echo '|Validasi Berhasil';
		}else{
			echo '1|Validasi Gagal';
		}
	}else if($f == 'unvalid') {
		require_once(Route::getModelPath('kuliah'));
		require_once(Route::getModelPath('honordosen'));
		$honor=$conn->Execute("update akademik.ak_honordosen set validhonor=null where ".mHonorDOsen::getCondition($q[0]));
		if($honor){
			$cekvalid=$conn->GetRow("select 1 from akademik.ak_honordosen where validhonor=-1 and ".mKuliah::getCondition($q[0],'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok'));
			if(empty($cekvalid))
				$kelas=$conn->Execute("update akademik.ak_kuliah set validhonorkuliah=null where ".mKuliah::getCondition($q[0],'thnkurikulum , kodemk , kodeunit , periode , kelasmk , perkuliahanke, tglkuliah, jeniskuliah, kelompok'));
		}
		if($honor){
			echo '|Lepas Validasi Berhasil';
		}else{
			echo '1|Lepas Validasi Gagal';
		}
	}
	else if($f == 'setTugas') {
		require_once(Route::getModelPath('mengajar'));
		//$conn->debug=true;
		$record=array();
		$record['tugasmengajar']=-1;

		list($p_posterr,$p_postmsg) = mMengajar::updateRecord($conn,$record,$q[0],true);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'unsetTugas') {
		require_once(Route::getModelPath('mengajar'));
		$record=array();
		$record['tugasmengajar']=0;
		list($p_posterr,$p_postmsg) = mMengajar::updateRecord($conn,$record,$q[0],true);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}
	else if($f == 'validasihonor'){
		$model=$q[0];
		$key=$q[1];
		$valid=$q[2]=='true'?-1:0;

		$file=strtolower(substr($model,1));
		require_once(Route::getModelPath($file));

		$record=array();
		$record['isvalid']=$valid;
		list($p_posterr,$p_postmsg) = $model::updateRecord($conn,$record,$key,true);

		echo $p_posterr.'|'.$p_postmsg;
	}else if($f == 'cekalUts') {
		require_once(Route::getModelPath('perwalian'));
		$record=array();
		$record['isuts']=$q[1];
		list($p_posterr,$p_postmsg) = mPerwalian::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'cekalUas') {
		require_once(Route::getModelPath('perwalian'));
		$record=array();
		$record['isuas']=$q[1];
		list($p_posterr,$p_postmsg) = mPerwalian::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'unvalidpenghargaan') {
		require_once(Route::getModelPath('penghargaan'));
		$record=array();
		$record['isvalid']='0';
		list($p_posterr,$p_postmsg) = mPenghargaan::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'validpenghargaan') {
		require_once(Route::getModelPath('penghargaan'));
		$record=array();
		$record['isvalid']='-1';
		list($p_posterr,$p_postmsg) = mPenghargaan::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}else if($f == 'getpoinpelanggaran') {
		require_once(Route::getModelPath('jenispelanggaran'));

		$data = mJenispelanggaran::getData($conn,$q[0]);

		if(!empty($data["poinpelanggaran"])){
			echo $data["poinpelanggaran"];
		}else{
			echo "";
		}
	}else if($f == 'getkegiatanchild') {
		require_once(Route::getModelPath('strukturkegiatan'));

		if(empty($q[0])) {
			$a_kegiatan = array();

			echo UI::createOption($a_kegiatan,'',true,'-- Pilih Kegiatan Terlebih Dahulu --');
		}
		else {
			$a_kegiatan = mStrukturKegiatan::getByParent($conn,$q[0]);
			$t_kegiatan = $q[1];

			echo UI::createOption($a_kegiatan,$t_kegiatan);
		}
	}else if($f == 'loadprestasi') {
		require_once(Route::getModelPath('prestasibeasiswa'));
		require_once(Route::getModelPath('pengajuanbeasiswa'));

		$p_loadprestasi = $f;
		$p_modelprestasi = mPrestasiBeasiswa;
		$p_modelpengajuan = mPengajuanBeasiswa;

		require_once('data_detailprestasibeasiswa.php');
	}
	else if($f == 'loadprestasimaba') {
		require_once(Route::getModelPath('prestasibeasiswamaba'));
		require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));

		$p_loadprestasi = $f;
		$p_modelprestasi = mPrestasiBeasiswaMaba;
		$p_modelpengajuan = mPengajuanBeasiswaPd;

		require_once('data_detailprestasibeasiswa.php');
	}
	else if($f == 'poinprestasi') {
		require_once(Route::getModelPath('poinprestasi'));

		echo (int)mPoinprestasi::getPoin($conn,$q[0].'|'.$q[1].'|'.$q[2].'|'.$q[3]);
	}
	else if($f == 'unvalidpenghargaan') {
		require_once(Route::getModelPath('penghargaan'));
		$record=array();
		$record['isvalid']='0';
		list($p_posterr,$p_postmsg) = mPenghargaan::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}
	else if($f == 'validpenghargaan') {
		require_once(Route::getModelPath('penghargaan'));
		$record=array();
		$record['isvalid']='-1';
		list($p_posterr,$p_postmsg) = mPenghargaan::updateCRecord($conn,$kolom,$record,$q[0]);
		if($p_posterr){
			echo $p_posterr.'|'.$p_postmsg;
		}else{
			echo $p_posterr.'|'.$p_postmsg;
		}
	}
	else if($f == 'loadpenghargaan') {
		require_once(Route::getModelPath('penghargaan'));

		require_once('data_detailpenghargaanmhs.php');
	}
	else if($f == 'getstatusmhs') {
		require_once(Route::getModelPath('mahasiswa'));

		$row = mMahasiswa::getDataSingkat($conn,$q[0]);

		echo $row['namastatus'];
	}
	else if($f == 'getnopolis') {
		require_once(Route::getModelPath('mhsasuransi'));

		echo mMhsasuransi::getDataField($conn,$q[0],'nopolis');
	}
	else if ($f=='loadriwayatpd'){
		require_once('xinc_tab_riwayatpendidikan.php');
	}
	else if ($f=='loadalasanpd'){

		require_once('xinc_tab_datapd.php');
	}
	else if ($f=='loadprestasibsmaba'){
		require_once('xinc_tabprestasibeasiswa.php');
	}
	else if ($f=='loadorganisasi'){
		require_once('xinc_taborganisasibeasiswa.php');
	}
	else if ($f=='loadpelatihan'){

		require_once('xinc_tabpelatihanbeasiswa.php');
	}
	else if ($f=='loadkerja'){
		require_once('xinc_tabkerjabeasiswa.php');
	}
	else if ($f=='loadbiodata'){

		require_once('xinc_tabbiodataanak.php');
	}
	else if ($f=='loadpotensi'){

		require_once('xinc_tabpotensi.php');
	}
	else if ($f=='loadsyaratbeasiswa'){

		require_once('xinc_tabberkas.php');
	}
	else if ($f=='loadjumlahanak'){
		require_once('xinc_tabbiodataanak.php');
	}

?>
