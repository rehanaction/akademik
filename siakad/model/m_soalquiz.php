<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSoalQuiz extends mModel {
		const schema = 'akademik';
		const table = 'ms_soalquiz';
		const order = 'soal';
		const key = 'idsoal';
		const label = 'Soal Quisioner';
		const sequence = 'ms_soalquiz_idsoal_seq';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select s.idsoal,s.soal,(case when s.status='1' then 'Aktif' else 'Non Aktif' end) as status,
					j.idjenissoal,j.namajenissoal
					from ".static::table()." s
					join ".static::table('ms_jenisquiz')." j using (idjenissoal)";
			
			return $sql;
		}
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periode = '$key'";
				case 'status': return "status = '$key'";
				case 'idjenissoal': return "idjenissoal = '$key'";
			}
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data = array();
			$data['periode'] = 'periode';
			
			return $data;
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select s.*,substr(periode,1,4) as tahun,substr(periode,5,1) as semester from ".static::table()." s where ".static::key."=$key ";
			return $sql;
		}
		
		// mendapatkan data mata kuliah quiz per periode
		function getMkQuiz($conn,$nim,$periode) {
			$sql = "select distinct(k.periode,k.thnkurikulum,k.kodeunit,k.kodemk,m.namamk,k.kelasmk,m.sks,mj.nipdosen,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)),
				k.periode,k.thnkurikulum, k.kodeunit, k.kodemk, m.namamk, k.kelasmk, m.sks, mj.nipdosen,akademik.f_namalengkap(p.gelardepan,
				p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadosen
					from ".static::table('ak_krs')." k 
					left join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk)
					join ".static::table('ak_mengajar')." mj using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					join sdm.ms_pegawai p on p.idpegawai::text=mj.nipdosen 
					where k.nim = '$nim' and k.periode = '$periode' and mj.tugasmengajar='-1' order by m.namamk, k.kelasmk";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function copy($conn,$periodeasal,$periodetujuan) {
			$ok = true;
			$conn->BeginTrans();
			
			// masukkan mata kuliah
			$sql = "insert into ".static::table()." (soal,status,periode,idjenissoal)
					select s.soal,s.status,'$periodetujuan',s.idjenissoal from ".static::table()." s
					where s.periode='$periodeasal'";
			$ok = $conn->Execute($sql);
			
			$err = $conn->ErrorNo();
			$conn->CommitTrans($ok);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
	}
?>
