<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPengalaman extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'mw_pengalamanmhs';
		const sequence = 'pengalaman_mahasiswa_idpengalaman_seq';
		const order = 'periode desc,tglpengajuan desc';
		const key = 'idpengalaman';
		const label = 'pengalaman';
		const uptype = 'pengalaman';

		// mendapatkan kueri list
		function listQuery() {
			$sql = "select idpengalaman,p.periode, p.nim, m.nama, namakegiatan, p.tglpengajuan, istampil,
					(case when isvalid='-1' then '<div align=center><img src=images/check.png></div>' end) as isvalid,
					(case when isvalid='-1' then s.poinkegiatan else '0' end) as poinkegiatan 
					from ".static::table()." p 
					join akademik.ms_mahasiswa m  on p.nim = m.nim 
					join akademik.ak_prodi pr on pr.kodeunit = m.kodeunit
					left join kemahasiswaan.ms_strukturkegiatanpoin s on s.periode = p.periode and s.kodekegiatan::text = p.kodekegiatan::text and s.kodejenjang = pr.kode_jenjang_studi
					 ";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *, nama , p.keterangan as keterangan
					from ".static::table()." p
					join akademik.ms_mahasiswa m on p.nim = m.nim
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan nama mahasiswa
		function getNamaMahasiswa($conn,$nim) {
			$sql = "select nama from akademik.ms_mahasiswa where nim = '$nim'";
			
			$data = $conn->GetOne($sql);
			return $data;
		}

		// cek apakah ada seminar
		function getIDBySeminar($conn,$nim,$nopeserta) {
			$sql = "select idpengalaman from ".static::table()."
					where nim = ".Query::escape($nim)." and nopeserta = ".Query::escape($nopeserta);
			$cek = $conn->GetOne($sql);

			return empty($cek) ? false : true;
		}

		// daftar kategori
		function getListKategori() {
			return array('O' => 'Keikutsertaan dalam Organisasi', 'K' => 'Sertifikat Keahlian', 'S' => 'Keikutsertaan Seminar');
		}
		
		// impor pengalaman peserta seminar
		function importSeminar($conn,$periode) {
			$sql = "select v.*, p.idpengalaman from seminar.v_pengalaman v
					left join ".static::table()." p using (nopeserta)
					where v.periode = ".Query::escape($periode) /* ."
					and p.idpengalaman is null" */;
			$rs = $conn->Execute($sql);
			
			$a_ins = $a_upd = array(0,0);
			while($row = $rs->FetchRow()) {
				// cek insert atau update
				if(empty($row['idpengalaman'])) {
					// tambahan
					$row['tglpengajuan'] = date('Y-m-d');
					$row['isvalid'] = -1;
					$row['tglvalidasi'] = date('Y-m-d');
					$row['kodekategori'] = 'S';
					$row['jenisaktivitas'] = 'I';
					
					$err = static::insertRecord($conn,$row);
					if(empty($err))
						$a_ins[0]++;
					else
						$a_ins[1]++;
				}
				else {
					$err = static::updateRecord($conn,$record,$row['idpengalaman']);
					if(empty($err))
						$a_upd[0]++;
					else
						$a_upd[1]++;
				}
				
				if(!empty($err))
					break;
			}
			
			if(empty($a_ins[1]) and empty($a_upd[1]))
				$err = false;
			else
				$err = true;
			
			$a_msg = array(array(),array());
			if(!empty($a_ins[0])) $a_msg[0][] = $a_ins[0].' berhasil';
			if(!empty($a_ins[1])) $a_msg[0][] = $a_ins[1].' gagal';
			if(!empty($a_upd[0])) $a_msg[1][] = $a_upd[0].' berhasil';
			if(!empty($a_upd[1])) $a_msg[1][] = $a_upd[1].' gagal';
			if(empty($a_msg[0])) unset($a_msg[0]);
			else $a_msg[0] = implode(' dan ',$a_msg[0]).' ditambahkan';
			if(empty($a_msg[1])) unset($a_msg[1]);
			else $a_msg[1] = implode(' dan ',$a_msg[1]).' diubah';
			
			if(empty($a_msg))
				$msg = 'Tidak ada data yang bisa diimpor';
			else
				$msg = 'Impor seminar: '.implode(', ',$a_msg);
			
			return array($err,$msg);
		}
	}
?>
