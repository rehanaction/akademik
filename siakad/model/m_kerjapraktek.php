<?php
	// model kerja praktek
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKerjaPraktek extends mModel {
		const schema = 'akademik';
		const table = 'ak_kp';
		const sequence = 'ak_kp_idkp_seq';
		const order = 'periode desc,tglmulai desc,judulkp';
		const key = 'idkp';
		const label = 'kuliah kerja nyata';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periode = '$key'";
			}
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select *, substring(periode,1,4) as tahun, substring(periode,5,1) as semester
					from ".static::table()." where ".static::getCondition($key);
			
			return $sql;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'peserta':
					$info['table'] = 'ak_pesertakp';
					$info['key'] = 'idkp,nim';
					$info['label'] = 'peserta KP';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// menemukan data pengambil krs kp
		function findPengambil($conn,$str,$periode='',$col='',$key='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
						
			$sql = "select distinct $key as key, $col as label from ".static::table('ak_krs')." k
					join ".static::table('ak_matakuliah')." mk on k.thnkurikulum = mk.thnkurikulum and k.kodemk = mk.kodemk and mk.tipekuliah = 'K'
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					where lower($col::varchar) like '%$str%'";
			if(!empty($periode))
				$sql .= " and k.periode = '$periode'";
			$sql .= " order by $key";
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row['key'];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}
		
		// mendapatkan id kp mahasiswa
		function getKPMahasiswa($conn,$nim) {
			$sql = "select p.idkp from ".static::table('ak_pesertakp')." p
					join ".static::table()." k on p.idkp = k.idkp
					where p.nim = '$nim' order by k.statuskp, k.tglmulai desc";
			
			return $conn->GetOne($sql);
		}
		
		// penerima beasiswa
		function getPeserta($conn,$key,$label='',$post='') {
			$sql = "select p.idkp, p.nim, m.nama, p.nilaipembimbing, p.nilaipenguji, p.nilaiperusahaan, s.nnumerik, s.nhuruf
					from ".static::table('ak_pesertakp')." p
					join ".static::table()." k on p.idkp = k.idkp
					join ".static::table('ms_mahasiswa')." m on p.nim = m.nim
					left join
						(".static::table('ak_krs')." s join ".static::table('ak_matakuliah')." a using (thnkurikulum,kodemk))
					on s.nim = p.nim and s.periode = k.periode and a.tipekuliah = 'K'
					where p.idkp = '$key' order by p.nim";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// map nilai peserta kp - unsur nilai
		function mapNilaiKomponen() {
			return array('Pelatihan' => 'nilaiperusahaan', 'Proses' => 'nilaipembimbing', 'Laporan' => 'nilaipenguji');
		}
		
		// insert record detail
		function insertCRecordDetail($conn,$kolom,$record,$detail) {
			// ambil dari nilai unsur mahasiswa bila peserta
			if($detail == 'peserta') {
				require_once(Route::getModelPath('kelas'));
				require_once(Route::getModelPath('unsurnilai'));
				
				// mengambil data kelas
				$t_nim = $record['nim'];
				
				$sql = "select s.thnkurikulum, s.kodemk, s.kodeunit, s.periode, s.kelasmk, k.periode as periodekp
						from ".static::table()." k
						left join ".static::table('ak_krs')." s on s.nim = '$t_nim' and s.periode = k.periode
						left join ".static::table('ak_matakuliah')." a on s.thnkurikulum = a.thnkurikulum and s.kodemk = a.kodemk and a.tipekuliah = 'K'
						where k.idkp = '".$record['idkp']."'";
				$row = $conn->GetRow($sql);
				
				if(!empty($row['kodemk'])) {
					$keykls = mKelas::getKeyRow($row);
					$a_nilai = mUnsurNilaiMhs::getDataKelasMhs($conn,$keykls.'|'.$record['nim']);
					
					if(!empty($a_nilai)) {
						$a_map = self::mapNilaiKomponen();
						foreach($a_nilai as $t_nilai)
							$record[$a_map[$t_nilai['namaunsurnilai']]] = $t_nilai['nilaiunsur'];
					}
				}
				else
					return array(true,'Mahasiswa dengan NIM '.$t_nim.' tidak mengambil mata kuliah KKN pada '.Akademik::getNamaPeriode($row['periodekp']));
			}
			
			$info = static::getDetailInfo($detail);
			
			Query::recInsert($conn,$record,static::table($info['table']));
			
			return static::insertStatus($conn,$kolom,$info['label'],$info['table']);
		}
		
		// simpan unsur nilai mhs
		function saveUnsurNilaiMhs($conn,$subkey) {
			// default
			$err = false;
			
			// ambil key
			$colkey = self::getDetailInfo('peserta','key');
			$rowkey = static::getKeyRecord($subkey,$colkey);
			
			// mengambil data lengkap
			$sql = "select p.idkp, p.nim, p.nilaipembimbing, p.nilaipenguji, p.nilaiperusahaan, k.periode,
					s.thnkurikulum, s.kodemk, s.kodeunit, s.kelasmk, s.nnumerik, s.nangka, s.nhuruf, s.nilaimasuk
					from ".static::table('ak_pesertakp')." p
					join ".static::table()." k on p.idkp = k.idkp
					left join
						(".static::table('ak_krs')." s join ".static::table('ak_matakuliah')." a using (thnkurikulum,kodemk))
					on s.nim = p.nim and s.periode = k.periode and a.tipekuliah = 'K'
					where p.idkp = '".$rowkey['idkp']."' and p.nim = '".$rowkey['nim']."'";
			$row = $conn->GetRow($sql);
			
			if(!empty($row['idkp'])) {
				if(empty($row['kodemk'])) {
					$err = true;
					$msg = 'Mahasiswa dengan NIM '.$rowkey['nim'].' tidak mengambil kerja praktek pada '.Akademik::getNamaPeriode($row['periode']);
				}
				else if(!empty($row['nilaimasuk'])) {
					$err = true;
					$msg = 'Nilai KKN mahasiswa dengan NIM '.$rowkey['nim'].' sudah ditutup';
				}
				
				if(!$err) {
					require_once(Route::getModelPath('kelas'));
					require_once(Route::getModelPath('krs'));
					require_once(Route::getModelPath('unsurnilai'));
					
					$keykls = mKelas::getKeyRow($row);
					
					// cek unsur nilai
					$a_unsurnilai = mUnsurNilaiKelas::getDataKelas($conn,$keykls);
					if(empty($a_unsurnilai))
						$a_unsurnilai = mUnsurNilaiKelas::insertFromUnsurNilai($conn,$keykls);
					
					// map nilai kp - urutan unsur
					$a_map = self::mapNilaiKomponen();
					
					// masukkan unsur nilai mahasiswa
					$record = mKelas::getKeyRecord($keykls);
					$record['nim'] = $rowkey['nim'];
					
					// masukkan unsur nilai
					foreach($a_unsurnilai as $t_unsur) {
						$t_idunsur = $t_unsur['idunsurnilai'];
						
						$record['idunsurnilai'] = $t_idunsur;
						$record['nilaiunsur'] = CStr::cStrNull($row[$a_map[$t_unsur['namaunsurnilai']]]);
						
						if($record['nilaiunsur'] != 'null') {
							if(!isset($t_nnumerik)) $t_nnumerik = 0;
							$t_nnumerik += ($t_unsur['prosentasenilai']*$record['nilaiunsur']);
							
							list($err,$msg) = mUnsurNilaiMhs::saveRecord($conn,$record,$keykls.'|'.$rowkey['nim'].'|'.$t_idunsur,true);
						}
						else
							list($err,$msg) = mUnsurNilaiMhs::delete($conn,$keykls.'|'.$rowkey['nim'].'|'.$t_idunsur);
						
						if($err) break;
					}
					
					// masukkan krs
					if(!$err) {
						$record = array();
						if(!isset($t_nnumerik)) {
							$record['nnumerik'] = 'null';
							$record['nangka'] = 'null';
						}
						else							
							$record['nnumerik'] = round($t_nnumerik/100);
						
						list($err,$msg) = mKRS::updateRecord($conn,$record,$keykls.'|'.$rowkey['nim'],true);
						
						if($err) break;
					}
				}
			}
			
			$msg = 'Penyimpanan data KKN mahasiswa ';
			if($err)
				$msg .= 'gagal';
			else
				$msg .= 'berhasil';
			
			return array($err,$msg);
		}
		
		// status kp
		function status() {
			$data = array('0' => '0 - Aktif', '1' => '1 - Selesai');
			
			return $data;
		}
	}
?>