<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('mahasiswa'));
	
	class mTransferMhs extends mMahasiswa {
		const label = 'transfer mahasiswa';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select r.nim, r.nama, r.sex, u.namaunit, r.semestermhs, p.idpegawai::text||' - '||akademik.f_namalengkap(p.gelardepan, p.namadepan, p.namatengah, p.namabelakang, p.gelarbelakang) as nipdosenwali from ".self::table()." r
					left join sdm.ms_pegawai p on r.nipdosenwali=p.idpegawai::character varying
					join gate.ms_unit u on r.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		// mendapatkan kondisi kueri list
		function listCondition() {
			return "r.statusmhs = 'A'";
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'angkatan': return "substring(r.periodemasuk,1,4) = '$key'";
				case 'periode': return "r.periodemasuk = '$key'";
				case 'unit': return "r.kodeunit = '$key'";
			}
		}
		
		// transfer mahasiswa
		function transfer($conn,$npm,$kodeunit,$npmbaru) {
			require_once(Route::getModelPath('mahasiswa'));
			require_once(Route::getModelPath('unit'));
			
			$conn->BeginTrans();
			
			// cek npm baru
			$cek = static::isDataExist($conn,$npmbaru);
			if(empty($cek)) {
				$a_idx = static::getIndeksNilai($conn,$npm);
				$field = static::getFields($conn);
				
				$record = $field;
				$record['nim'] = "'$npmbaru'";
				$record['nimlama'] = "'$npm'";
				$record['kodeunit'] = "'$kodeunit'";
				$record['skslulus'] = 0;
				$record['mhstransfer'] = 1;
				$record['ptasal'] = "'Univ. Esa unggul'";
				$record['ptjurusan'] = "'".mUnit::getNamaUnit($conn,$a_idx['kodeunit'])."'";
				$record['statusmhs'] = "'A'";
				$record['batassks'] = (int)$a_idx['batasbaru'];
				$record['ipk'] = 0;
				$record['ipslalu'] = (int)$a_idx['ipslalu'];
				
				$sql = "insert into ".static::table()." (".implode(',',$field).")
						select ".implode(',',$record)." from ".static::table()."
						where ".static::getCondition($npm);
				$conn->Execute($sql);
				$err = $conn->ErrorNo();
				
				if($err)
					$msg = 'Transfer mahasiswa gagal';
				else
					$msg = 'Transfer mahasiswa berhasil';
			}
			else {
				$err = -1;
				$msg = 'NIM '.$npmbaru.' sudah ada, Gunakan NIM Lain';
			}
			
			// update
			if(!$err) {
				$record = array();
				$record['statusmhs'] = 'T';
				
				list($err,$msg) = static::updateRecord($conn,$record,$npm,true);
			}
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return array($err,$msg);
		}
	}
?>
