<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPemdaftarSeminar extends mModel {
		const schema = 'seminar';
		const table = 'ms_pendaftar';
		const order = 'nopendaftar';
		const key = 'nopendaftar';
		const value = 'nama';
		const label = 'Pendaftar Seminar';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *
					from ".static::table()." p";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *
					from ".static::table()." where nopendaftar = '$key'";
			
			return $sql;
		}

		function getListFilter($col,$key) {
			switch($col) {
				case 'nopendaftar': 
					return " p.nopendaftar  = '$key'";
				case 'nim': 
					return " p.nim  is not null";
				case 'nip': 
					return " p.nip  is not null";
				case 'umum': 
					return " p.nip  is null and p.nim is null";	
			}
		}

		function getArrayListFilterCol() {
			$data['nopendaftar'] = 'p.nopendaftar';
			return $data;
		}

		// mendapatkan pendaftar mahasiswa 
		function dataMahasiswa($conn) {
			$sql = "select p.*
					from ".static::table()." p
					where p.nim is not null ";	

			return $conn->GetArray($sql);	
		}

		// mendapatkan pendaftar pegawai 
		function dataPegawai($conn) {
			$sql = "select p.*
					from ".static::table()." p
					where p.nip is not null ";
			
			return $conn->GetArray($sql);	
		}
		
		// mendapatkan nopendaftar baru
		function getNoPendaftarBaru($conn,$record) {
			$prefix = date('Y');
			$length = strlen($prefix);
			
			$sql = "select max(substr(nopendaftar,".($length+1).")::int)
					from ".static::table()."
					where substr(nopendaftar,1,$length) = ".Query::escape($prefix);
			$max = $conn->GetOne($sql);
			
			return $prefix.str_pad((int)$max+1,4,'0',STR_PAD_LEFT);
		}
		
		// mendapatkan data mahasiswa
		function getRecordMahasiswa($conn,$record) {
			$sql = "select m.*, k.kodepropinsi from akademik.ms_mahasiswa m
					left join akademik.ms_kota k on m.kodekota = k.kodekota
					where m.nim = ".Query::escape($record['nim']);
			$row = $conn->GetRow($sql);
			
			if(!empty($row)) {
				$record['nama'] = CStr::CStrNull($row['nama']);
				$record['sex'] = CStr::CStrNull($row['sex']);
				$record['noktp'] = CStr::CStrNull($row['nik']);
				$record['telp'] = CStr::CStrNull($row['telp']);
				$record['hp'] = CStr::CStrNull($row['hp']);
				$record['email'] = CStr::CStrNull($row['email']);
				$record['tmplahir'] = CStr::CStrNull($row['tmplahir']);
				$record['tgllahir'] = CStr::CStrNull($row['tgllahir']);
				$record['kodepropinsi'] = CStr::CStrNull($row['kodepropinsi']);
				$record['kodekota'] = CStr::CStrNull($row['kodekota']);
				$record['kodepos'] = CStr::CStrNull($row['kodepos']);
				$record['alamat'] = CStr::CStrNull($row['alamat']);
				$record['iskerja'] = CStr::CStrNull($row['statuskerja']);
				$record['jabatan'] = CStr::CStrNull($row['jabatan']);
				$record['namaperusahaan'] = CStr::CStrNull($row['namaperusahaan']);
			}
			
			return $record;
		}
		
		// mendapatkan data pegawai
		function getRecordPegawai($conn,$record) {
			$sql = "select p.*, sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap
					from sdm.ms_pegawai p
					where p.idpegawai = ".Query::escape($record['nip']);
			$row = $conn->GetRow($sql);
			
			if(!empty($row)) {
				$record['nama'] = CStr::CStrNull($row['namalengkap']);
				$record['sex'] = CStr::CStrNull($row['jeniskelamin']);
				$record['noktp'] = CStr::CStrNull($row['noktp']);
				$record['telp'] = CStr::CStrNull($row['telp']);
				$record['hp'] = CStr::CStrNull($row['nohp']);
				$record['email'] = CStr::CStrNull($row['email']);
				$record['tmplahir'] = CStr::CStrNull($row['tmplahir']);
				$record['tgllahir'] = CStr::CStrNull($row['tgllahir']);
				$record['kodepos'] = CStr::CStrNull($row['kodepos']);
				$record['alamat'] = CStr::CStrNull($row['alamat']);
			}
			
			return $record;
		}
		
		// update rfid mahasiswa
		function updateRFIDMahasiswa($conn) {
			$sql = "update ".static::table()." p set rfid = m.rfid from akademik.ms_mahasiswa m
					where p.nim = m.nim and coalesce(p.rfid,'') <> coalesce(m.rfid,'')";
			$conn->Execute($sql);
			
			$err = $conn->ErrorNo();
			$msg = 'Update RFID pendaftar mahasiswa '.(empty($err) ? 'berhasil' : 'gagal');
			
			return array($err,$msg);
		}
	}
?>
