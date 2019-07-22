<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class mCombo {
		function akreditasi($conn,$dot=true) {
			require_once(Route::getModelPath('akreditasi'));

			return mAkreditasi::getArray($conn,$dot);
		}

		function angkatan($conn) {
			require_once(Route::getModelPath('mahasiswa'));

			return mMahasiswa::angkatan($conn);
		}

		function beasiswa($conn) {
			require_once(Route::getModelPath('beasiswa'));

			return mSumberBeasiswa::getArrayNama($conn);
		}

		function jenisbeasiswa($conn) {
			require_once(Route::getModelPath('jenisbeasiswa'));

			return mJenisbeasiswa::getArray($conn);
		}
		function jenispenerima() {
			require_once(Route::getModelPath('beasiswa'));

			return mBeasiswa::getArrayJenisPenerima();
		}
		function sumberbeasiswa($conn) {
			require_once(Route::getModelPath('beasiswa'));

			return mSumberBeasiswa::getArray($conn);
		}

		function bidangIlmu($conn) {
			require_once(Route::getModelPath('bidangilmu'));

			return mBidangilmu::getArray($conn);
		}

		function bidangStudi($conn,$kodeunit) {
			require_once(Route::getModelPath('bidangstudi'));

			return mBidangStudi::getArray($conn);
		}

		function bulan() {
			return Date::arrayMonth();
		}

		function dosen($conn,$unit='') {
			require_once(Route::getModelPath('pegawai'));

			return mPegawai::getArray($conn,$unit);
		}

		function dosenLengkap($conn,$unit='') {
			require_once(Route::getModelPath('pegawai'));

			return mPegawai::getArrayLengkap($conn,$unit);
		}

		function fakultas($conn) {
			require_once(Route::getModelPath('unit'));

			return mUnit::fakultas($conn);
		}
		function fakjur($conn) {
			require_once(Route::getModelPath('unit'));

			return mUnit::fakultas($conn);
		}
		function frekkurikulum($conn,$dot=true) {
			require_once(Route::getModelPath('frekuensikurikulum'));

			return mFrekuensikurikulum::getArray($conn,$dot);
		}

		function hari($full=true) {
			return Date::arrayDay($full);
		}

		function jalurPenerimaan($conn) {
			require_once(Route::getModelPath('jalurpenerimaan'));

			return mJalurPenerimaan::getArray($conn,$unit);
		}

		function jenisTarif($conn) {
			require_once(Route::getModelPath('tarif'));

			return mTarif::jenisTarif($conn);
		}

		function jurusan($conn,$fakultas='',$skippamu=false) {
			require_once(Route::getModelPath('unit'));

			return mUnit::jurusan($conn,$fakultas,$skippamu);
		}

		function kegiatan() {
			$data = array('P' => 'Seminar Proposal', 'S' => 'Sidang Akhir');

			return $data;
		}

		function kerjasama($conn,$dot=true) {
			require_once(Route::getModelPath('universitas'));

			return mUniversitas::getArray($conn,$dot);
		}

		function kota($conn,$propinsi='') {
			require_once(Route::getModelPath('kota'));

			return mKota::getArray($conn,$propinsi);
		}

		function kurikulum($conn) {
			require_once(Route::getModelPath('thnkurikulum'));

			return mThnkurikulum::getArray($conn);
		}
		function matkul($conn, $kurikulum='', $unit='') {
			require_once(Route::getModelPath('kurikulum'));

			return mKurikulum::getArray($conn, $kurikulum, $unit);
		}


		function mahasiswa($conn,$unit='',$periode='') {
			require_once(Route::getModelPath('mahasiswa'));

			return mMahasiswa::getArray($conn,$unit,$periode);
		}

		function namabeasiswa($conn) {
			require_once(Route::getModelPath('beasiswa'));

			return mBeasiswa::getArrayNama($conn);
		}

		function nAngkaKurikulum($conn,$kurikulum) {
			require_once(Route::getModelPath('skalanilai'));

			return mSkalaNilai::getDataKurikulum($conn,$kurikulum);
		}

		function negara($conn) {
			require_once(Route::getModelPath('negara'));

			return mNegara::getArray($conn);
		}

		function noSemester($singkat=false) {
			$data = array();
			for($i=1;$i<=8;$i++)
				$data[$i] = ($singkat ? '' : 'Semester ').$i;

			return $data;
		}

		function pelkurikulum($conn,$dot=true) {
			require_once(Route::getModelPath('pelaksanaankurikulum'));

			return mPelaksanaankurikulum::getArray($conn,$dot);
		}

		function periode($conn,$singkat=true) {
			require_once(Route::getModelPath('periode'));

			return mPeriode::getArray($conn,$singkat);
		}

		function periodeDaftar($conn) {
			$sql = "select periodedaftar from pendaftaran.ms_periodedaftar order by periodedaftar desc";

			return Query::arrQuery($conn,$sql);
		}

		function periodeWisuda($conn) {
			require_once(Route::getModelPath('periodeyudisium'));

			return mPeriodeYudisium::getArray($conn);
		}

		function programPendidikan($conn) {
			require_once(Route::getModelPath('progpend'));

			return mProgrampendidikan::getArray($conn);
		}

		function propinsi($conn) {
			require_once(Route::getModelPath('propinsi'));

			return mPropinsi::getArray($conn);
		}

		function ruang($conn) {
			require_once(Route::getModelPath('ruang'));

			return mRuang::getArray($conn);
		}

		function semester($singkat=false) {
			return Akademik::semester($singkat);
		}

		function statusMhs($conn) {
			require_once(Route::getModelPath('statusmhs'));

			return mStatusMhs::getArray($conn);
		}
		function sistemKuliah($conn,$short=false) {
			require_once(Route::getModelPath('sistemkuliah'));

			return mSistemkuliah::getArray($conn,$short);
		}
		function statusMasukMhs() {
			$data = array('0' => 'Mahasiswa Baru', '-1' => 'Mahasiswa Transfer');

			return $data;
		}
		function batalNim() {
			$data = array('0' => 'Tidak', '-1' => 'Ya');

			return $data;
		}
		function statusuniversitas() {
			$data = array('0' => 'PT Dalam Negeri', '-1' => 'PT Luar Negeri');

			return $data;
		}

		function tahun($singkat=true,$min=1996) {
			global $conn;
			require_once(Route::getModelPath('periode'));
			//$max_periode=mPeriode::getMaxPeriode($conn);
			$max_periode=(int)date('Y')+5;
			$data = array();
			for($i=$max_periode;$i>=$min;$i--)
				$data[$i] = ($singkat ? $i : $i.' - '.($i+1));

			return $data;
		}

		function tahun_angkatan($singkat=true,$min=1996) {
			$data = array();
			$data['*'] = '--Semua--';
			for($i=date('Y')+1;$i>=$min;$i--)
				$data[$i] = ($singkat ? $i : $i.' - '.($i+1));

			return $data;
		}

		function unit($conn,$dot=true,$skippamu=false,$cekauth=true) {
			require_once(Route::getModelPath('unit'));

			return mUnit::getArray($conn,$dot,$skippamu,$cekauth);
		}

		function unitTree($conn,$skippamu=false) {
			require_once(Route::getModelPath('unit'));

			return mUnit::getArrayTree($conn,$skippamu);
		}

		function universitas($conn) {
			require_once(Route::getModelPath('universitas'));

			return mUniversitas::getArray($conn);
		}

		function tingkatPrestasi($conn) {
			require_once(Route::getModelPath('tingkatprestasi'));

			return mTingkatPrestasi::getArray($conn);
		}

		function role($conn) {
			$sql = "select koderole, namarole from gate.sc_role order by koderole";

			return Query::arrQuery($conn,$sql);
		}
		function roleMhs($conn) {
			$sql = "select koderole, namarole from gate.sc_role where kemahasiswaan = 1 order by koderole";

			return Query::arrQuery($conn,$sql);
		}
		function jenisQuiz($conn) {

			require_once(Route::getModelPath('jenisquiz'));

			return mJenisQuiz::getArrayCombo($conn);
		}
		function kelompokKelas($conn){
			require_once(Route::getModelPath('kelas'));

			return mKelas::getMaxKelompok($conn);
		}
		function listKelasmk($conn,$periode,$kurikulum,$kodeunit){
			require_once(Route::getModelPath('kelas'));

			return mKelas::listKelasmk($conn,$periode,$kurikulum,$kodeunit);
		}
		function agama($conn) {
			$sql = "select kodeagama, namaagama from akademik.lv_agama order by kodeagama";

			return Query::arrQuery($conn,$sql);
		}
		function aktif(){
			return array('1'=>'Aktif','0'=>'Tidak Aktif');
		}
		function istoefl(){
			return array('1'=>'Toefl 1','2'=>'Toefl 2','0'=>'Bukan MK Toefl');
		}
		function listJumpingClass($conn) {
			require_once(Route::getModelPath('jumpingclass'));

			return mJumpingClass::getArray($conn);
		}
		function periodebulan($singkat=true,$isint=true) {
			$data = array();
			$bulan = array('Januari','Februari','Maret','April','Mei','Juni','Juli','Agustus','September','Oktober','Nopember','Desember');
			for($i=1;$i<13;$i++)
				$data[$i] = $bulan[$i-1];

			if(!$isint) {
				$cek = $data;
				$data = array();
				foreach($cek as $k => $v)
					$data[str_pad($k,2,'0',STR_PAD_LEFT)] = $v;
			}

			return $data;
		}
		function semmk($singkat=false) {
			$data = array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10');

			return $data;
		}
		function getJenisUjian(){
			return array('T'=>'UTS','A'=>'UAS','R'=>'UTS Susulan','S'=>'UAS Susulan');
		}
		function getKota(){
			global $conn;
			$sql = "select kodekota,namakota from akademik.ms_kota ORDER BY namakota";

			return Query::arrQuery($conn,$sql);
		}
		//combo kewarganegaraan
		function wargaNegara(){
			global $conn;
			$sql = "select kodewn from akademik.lv_warganegara order by kodewn";

			return Query::arrQuery($conn,$sql);
		}
		function namaWarga($wn){
			global $conn;
			$sql = "select kodewn, namawn from akademik.lv_warganegara where kodewn='$wn' order by namawn";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$wn];
		}
		//combo pekerjaan
		function kodeKerja(){
			global $conn;
			$sql = "select kodepekerjaan from akademik.lv_pekerjaan order by kodepekerjaan";

			return Query::arrQuery($conn,$sql);
		}
		function namaKerja($kodekerja){
			global $conn;
			$sql = "select kodepekerjaan, namapekerjaan from akademik.lv_pekerjaan where kodepekerjaan='$kodekerja'";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$kodekerja];
		}
		//combo pendidikan
		function kodePendidikan(){
			global $conn;
			$sql = "select kodependidikan from akademik.lv_pendidikan";

			return Query::arrQuery($conn,$sql);
		}
		function namaPendidikan($kodependidikan){
			global $conn;
			$sql = "select kodependidikan, namapendidikan from akademik.lv_pendidikan where kodependidikan='$kodependidikan' order by namapendidikan";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$kodependidikan];
		}
		//combo pendapatan
		function kodePendapatan(){
			global $conn;
			$sql = "select kodependapatan from akademik.lv_pendapatan";

			return Query::arrQuery($conn,$sql);
		}
		function namaPendapatan($kodependapatan){
			global $conn;
			$sql = "select kodependapatan, namapendapatan from akademik.lv_pendapatan where kodependapatan='$kodependapatan' order by namapendapatan";
			$ok=Query::arrQuery($conn,$sql);
			return $ok[$kodependapatan];
		}
		// jenis kelamin
		function jenisKelamin() {
			$data = array('L' => 'Laki-Laki', 'P' => 'Perempuan');

			return $data;
		}
		function statusNikah(){
			global $conn;
			$sql = "select statusnikah, namastatus from akademik.lv_statusnikah order by namastatus";

			return Query::arrQuery($conn,$sql);
		}
		function jurusan_spmb($conn,$jalur,$periode,$idgelombang){

			$sqlpagu="select kodeunit,pagu from pendaftaran.pd_paguunit where jalurpenerimaan='$jalur'
						and periodedaftar='$periode' and idgelombang='$idgelombang'";
			$arr_pagu=Query::arrQuery($conn,$sqlpagu);

			$sqljurusan="select pilihanditerima,coalesce(sum(1),0) as jumlah from pendaftaran.pd_pendaftar
						where jalurpenerimaan='$jalur' and periodedaftar='$periode' and idgelombang='$idgelombang' and isdaftarulang=-1
						group by pilihanditerima";
			$arr_jurusan=Query::arrQuery($conn,$sqljurusan);

			$arr_unit=array();
			foreach($arr_pagu as $kodeunit=>$pagu){
					$sisa=(int)$pagu-(int)$arr_jurusan[$kodeunit];
					if($sisa>0)
						$arr_unit[]=$kodeunit;

			}
			$inunit = implode("','",$arr_unit);
			/*$sql = "select kodeunit, namaunit from gate.ms_unit where level = 2 and isakad=-1 and
					kodeunit in (select kodeunit from pendaftaran.lv_prodijalurpenerimaan where jalurpenerimaan='$jalur')";	*/
			$sql = "select kodeunit, namaunit from gate.ms_unit where level = 2 and isakad=-1 ";
			if(!empty($inunit))
				$sql.=" AND kodeunit in ('$inunit')";
			$sql.=" order by infoleft";
			$data =  Query::arrQuery($conn,$sql);

			return $data;
		}


		// pekerjaan
		function pekerjaan($conn) {
			$sql = "select kodepekerjaan, namapekerjaan from akademik.lv_pekerjaan order by kodepekerjaan";

			return Query::arrQuery($conn,$sql);
		}

		// pendidikan
		function pendidikan($conn) {
			$sql = "select kodependidikan, namapendidikan from akademik.lv_pendidikan order by kodependidikan";

			return Query::arrQuery($conn,$sql);
		}
		// pendapatan
		function pendapatan($conn) {
			$sql = "select kodependapatan, namapendapatan from akademik.lv_pendapatan order by kodependapatan";

			return Query::arrQuery($conn,$sql);
		}


	}
?>
