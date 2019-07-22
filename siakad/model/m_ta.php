<?php
	// model tugas akhir
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTa extends mModel {
		const schema = 'akademik';
		const table = 'ak_ta';
		const sequence = 'ak_ta_idta_seq';
		const order = 'tglmulai desc,nim';
		const key = 'idta';
		const label = 'skripsi';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select r.*, up.namaunit as namafakultas from ".self::table('r_ta2')." r
					join gate.ms_unit u on r.kodeunit = u.kodeunit
					join gate.ms_unit up on up.kodeunit = u.kodeunitparent";
			
			return $sql;
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data = array();
			$data['unit'] = 'r.kodeunit';
			
			return $data;
		}
		
		// mendapatkan potongan kueri filter list
		function getArrayListFilter() {
			$data = array();
			$data['pembimbing:1'] = "r.niputama is null and r.nipco is null";
			$data['pembimbing:2'] = "r.niputama is not null";
			$data['lama:1'] = "(r.statusta = 'A' and (extract(year from age(r.tglmulai)) >= 1 or extract(month from age(r.tglmulai)) >= 6))";
			$data['lama:2'] = "(r.statusta = 'A' and extract(year from age(r.tglmulai)) >= 1)";
			$data['lama:3'] = "(r.statusta = 'A' and extract(year from age(r.tglmulai)) >= 2)";
			
			return $data;
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'pembimbing': return "'$key' in (r.niputama,r.nipco)";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				default:
					return parent::getListFilter($col,$key);
			}
		}
		
		// filter lanjut
		function pembimbingSkripsi() {
			$data = array();
			$data['1'] = 'Tidak memiliki pembimbing skripsi';
			$data['2'] = 'Memiliki pembimbing skripsi';
			
			return $data;
		}
		
		function lamaSkripsi() {
			$data = array();
			$data['1'] = 'Skripsi lebih dari 6 bulan';
			$data['2'] = 'Skripsi lebih dari 1 tahun';
			$data['3'] = 'Skripsi lebih dari 2 tahun';
			
			return $data;
		}
		
		// mendapatkan daftar sidang akhir
		function getListSidangAkhir($conn,$kodeunit,$tgl) {
			$sql = "select s.tglujian, s.waktumulai, s.waktuselesai, s.koderuang, t.nim, m.nama, t.judulta, s.penguji1, s.penguji2, s.sekretaris, s.ketuamajelis,
						akademik.f_namalengkap(p1.gelardepan,p1.namadepan,p1.namatengah,p1.namabelakang,p1.gelarbelakang) as namapenguji1,
						akademik.f_namalengkap(p2.gelardepan,p2.namadepan,p2.namatengah,p2.namabelakang,p2.gelarbelakang) as namapenguji2,
						p1.idpegawai::text as nippenguji1, p2.idpegawai::text as nippenguji2
					from ".static::table('r_ujianta')." s
					join ".static::table('ak_ta')." t on s.idta = t.idta
					join ".static::table('ms_mahasiswa')." m on t.nim = m.nim
					join gate.ms_unit u on m.kodeunit = u.kodeunit
					join gate.ms_unit p on u.infoleft >= p.infoleft and u.inforight <= p.inforight and p.kodeunit = '$kodeunit'
					left join sdm.ms_pegawai p1 on s.penguji1 = p1.idpegawai::text 
					left join sdm.ms_pegawai p2 on s.penguji2 = p2.idpegawai::text 
					where s.jenisujian = 'A' and s.tglujian = '$tgl'
					order by s.waktumulai, s.koderuang";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			$sql = "select r.*, p.sekretaris,p.penguji1,p.penguji2,
					dp1.idpegawai::text||' - '||akademik.f_namalengkap(dp1.gelardepan, dp1.namadepan, dp1.namatengah, dp1.namabelakang, dp1.gelarbelakang) as niputama_,
					dp2.idpegawai::text||' - '||akademik.f_namalengkap(dp2.gelardepan, dp2.namadepan, dp2.namatengah, dp2.namabelakang, dp2.gelarbelakang) as nipco_,
					j.judul as juduljurnal, j.topik, j.namajurnal, j.edisi, j.url, j.statusvalidasi,
					p.tglujian as tglujianproposal, p.waktumulai as waktumulaiproposal, p.waktuselesai as waktuselesaiproposal, p.koderuang as koderuangproposal,
					uji1.idpegawai::text||' - '||akademik.f_namalengkap(uji1.gelardepan, uji1.namadepan, uji1.namatengah, uji1.namabelakang, uji1.gelarbelakang) as penguji1proposal_, 
					uji2.idpegawai::text||' - '||akademik.f_namalengkap(uji2.gelardepan, uji2.namadepan, uji2.namatengah, uji2.namabelakang, uji2.gelarbelakang) as penguji2proposal_, 
					sek.idpegawai::text||' - '||akademik.f_namalengkap(sek.gelardepan, sek.namadepan, sek.namatengah, sek.namabelakang, sek.gelarbelakang) as sekretarisproposal_, 
					p.nilaiujian as nilaiujianproposal, p.mengulang as mengulangproposal,
					a.tglujian as tglujianakhir, a.waktumulai as waktumulaiakhir, a.waktuselesai as waktuselesaiakhir, a.koderuang as koderuangakhir,
					a.penguji1 as penguji1akhir, a.penguji2 as penguji2akhir, a.sekretaris as sekretarisakhir, a.ketuamajelis as ketuamajelisakhir,
					ujia1.idpegawai::text||' - '||akademik.f_namalengkap(ujia1.gelardepan, ujia1.namadepan, ujia1.namatengah, ujia1.namabelakang, ujia1.gelarbelakang) as textpenguji1akhir, 
					ujia2.idpegawai::text||' - '||akademik.f_namalengkap(ujia2.gelardepan, ujia2.namadepan, ujia2.namatengah, ujia2.namabelakang, ujia2.gelarbelakang) as textpenguji2akhir, 
					seka.idpegawai::text||' - '||akademik.f_namalengkap(seka.gelardepan, seka.namadepan, seka.namatengah, seka.namabelakang, seka.gelarbelakang) as textsekretarisakhir,
					kama.idpegawai::text||' - '||akademik.f_namalengkap(kama.gelardepan, kama.namadepan, kama.namatengah, kama.namabelakang, kama.gelarbelakang) as textketuamajelisakhir,
					a.nilaiujian as nilaiujianakhir, a.mengulang as mengulangakhir
					from ".static::table('r_tadetail')." r
					left join ".static::table('ak_jurnalilmiah')." j on r.nim = j.nim
					left join ".static::table('r_ujianta')." p on r.idta = p.idta and p.jenisujian = 'P'
					left join ".static::table('r_ujianta')." a on r.idta = a.idta and a.jenisujian = 'A'
					left join sdm.ms_pegawai dp1 on dp1.idpegawai::text = r.niputama::text
					left join sdm.ms_pegawai dp2 on dp2.idpegawai::text = r.nipco::text
					left join sdm.ms_pegawai sek on sek.idpegawai::text = p.sekretaris::text
					left join sdm.ms_pegawai uji1 on uji1.idpegawai::text = p.penguji1::text
					left join sdm.ms_pegawai uji2 on uji2.idpegawai::text = p.penguji2::text
					left join sdm.ms_pegawai ujia1 on ujia1.idpegawai::text = a.penguji1::text
					left join sdm.ms_pegawai ujia2 on ujia2.idpegawai::text = a.penguji2::text
					left join sdm.ms_pegawai seka on seka.idpegawai::text = a.sekretaris::text
					left join sdm.ms_pegawai kama on kama.idpegawai::text = a.ketuamajelis::text
					where ".static::getCondition($key,'','r');
			
			return $sql;
		}
		
		// insert record
		function insertCRecord($conn,$kolom,$record,&$key) {
			$conn->BeginTrans();
			
			$t_nim = $record['nim'];
				
			$sql = "select 1 from ".static::table('ak_krs')." s
					join ".static::table('ak_matakuliah')." a on s.thnkurikulum = a.thnkurikulum and s.kodemk = a.kodemk and a.tipekuliah = 'T'
					where s.nim = '$t_nim'";
			$isambil = $conn->GetRow($sql);
			
			if(empty($isambil))
				return array(true,'Mahasiswa dengan NIM '.$t_nim.' belum mengambil mata kuliah skripsi');
			
			// unset record
			if(!empty($kolom))
				foreach($kolom as $datakolom)
					if($datakolom['readonly'])
						unset($record[$datakolom['kolom']]);
			
			$err = Query::recInsert($conn,$record,static::table());
			if(!$err) {
				$seq = static::sequence;
				if(empty($seq))
					$tkey = static::getRecordKey($key,$record);
				else
					$tkey = static::getLastValue($conn);
			}
			
			// pembimbing utama
			if(!$err and $record['niputama'] != 'null') {
				$recdet = array();
				$recdet['idta'] = $tkey;
				$recdet['nip'] = $record['niputama'];
				$recdet['tipepembimbing'] = 'U';
				
				$err = Query::recInsert($conn,$recdet,static::table('ak_pembimbing'));
			}
			
			// pembimbing pendamping
			if(!$err and $record['nipco'] != 'null') {
				$recdet = array();
				$recdet['idta'] = $tkey;
				$recdet['nip'] = $record['nipco'];
				$recdet['tipepembimbing'] = 'C';
				
				$err = Query::recInsert($conn,$recdet,static::table('ak_pembimbing'));
			}
			
			if(!$err)
				$key = $tkey;
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return static::insertStatus($conn,$kolom);
		}
		
		// update record
		function updateCRecord($conn,$kolom,$record,&$key,$keykrs='',$unsurnilai='') {
			$conn->BeginTrans();
			
			// unset record
			if(!empty($kolom))
				foreach($kolom as $datakolom)
					if($datakolom['readonly'])
						unset($record[$datakolom['kolom']]);
			
			$err = Query::recUpdate($conn,$record,static::table(),static::getCondition($key));
			if(!$err)
				$tkey = static::getRecordKey($key,$record);
			
			// pembimbing utama
			if(!$err) {
				if($record['niputama'] == 'null') {
					$err = Query::qDelete($conn,static::table('ak_pembimbing'),static::getCondition($tkey)." and tipepembimbing = 'U'");
				}
				else {
					$recdet = array();
					$recdet['idta'] = $tkey;
					$recdet['nip'] = $record['niputama'];
					$recdet['tipepembimbing'] = 'U';
					
					$err = Query::recSave($conn,$recdet,static::table('ak_pembimbing'),static::getCondition($tkey)." and tipepembimbing = 'U'");
				}
			}
			
			// pembimbing pendamping
			if(!$err) {
				if($record['nipco'] == 'null') {
					$err = Query::qDelete($conn,static::table('ak_pembimbing'),static::getCondition($tkey)." and tipepembimbing = 'C'");
				}
				else {
					$recdet = array();
					$recdet['idta'] = $tkey;
					$recdet['nip'] = $record['nipco'];
					$recdet['tipepembimbing'] = 'C';
					
					$err = Query::recSave($conn,$recdet,static::table('ak_pembimbing'),static::getCondition($tkey)." and tipepembimbing = 'C'");
				}
			}
			
			// jurnal ilmiah
			if(!$err and isset($record['juduljurnal'])) {
				$err = Query::qDelete($conn,static::table('ak_jurnalilmiah'),"nim = '".$record['nim']."'");
				
				if(!$err) {
					$recdet = array();
					$recdet['nim'] = $record['nim'];
					$recdet['judul'] = $record['juduljurnal'];
					$recdet['topik'] = $record['topik'];
					$recdet['namajurnal'] = $record['namajurnal'];
					$recdet['edisi'] = $record['edisi'];
					$recdet['url'] = $record['url'];
					
					$err = Query::recInsert($conn,$recdet,static::table('ak_jurnalilmiah'));
				}
			}
			if(!$err and isset($record['statusvalidasi'])) {
				$recdet = array();
				$recdet['statusvalidasi'] = ($record['statusvalidasi'] == 'null' ? 0 : $record['statusvalidasi']);
					
				$err = Query::recUpdate($conn,$recdet,static::table('ak_jurnalilmiah'),"nim = '".$record['nim']."'");
			}
			
			// ujian skripsi
			$a_ujian = array('P' => 'proposal', 'S' => 'seminar', 'A' => 'akhir');
			
			foreach($a_ujian as $t_jenis => $t_ujian) {
				if(!$err) {
					$n_ujian = -1*strlen($t_ujian);
					
					$t_ada = false;
					$recdet = array();
					foreach($record as $k => $v) {
						if(substr($k,$n_ujian) == $t_ujian) {
							$recdet[substr($k,0,strlen($k)+$n_ujian)] = $v;
							if($v != 'null')
								$t_ada = true;
						}
					}
					
					// cek ujian
					$sql = "select idujianta from ".static::table('ak_ujianta')." where
							".static::getCondition($tkey)." and jenisujian = '$t_jenis'";
					$t_id = $conn->GetOne($sql);
					
					if($t_ada) {
						if(empty($t_id)) {
							$recdet['idta'] = $tkey;
							$recdet['jenisujian'] = $t_jenis;
							
							$err = Query::recInsert($conn,$recdet,static::table('ak_ujianta'));
							if(!$err)
								$t_id = $conn->GetOne("select last_value from ".static::table('ak_ujianta_idujianta_seq'));
						}
						else
							$err = Query::recUpdate($conn,$recdet,static::table('ak_ujianta'),"idujianta = '$t_id'");
					}
					else if(!empty($t_id)) {
						$err = Query::qDelete($conn,static::table('ak_penguji'),"idujianta = '$t_id'");
						if(!$err) {
							$err = Query::qDelete($conn,static::table('ak_ujianta'),"idujianta = '$t_id'");
							unset($t_id);
						}
					}
					
					if(!$err and !empty($t_id)) { 
						// hapus penguji
						$err = Query::qDelete($conn,static::table('ak_penguji'),"idujianta = '$t_id'");
						
						// masukkan penguji
						if(!$err and CStr::cEmChg($record['ketuamajelis'.$t_ujian],'null') != 'null') {
							$recdet = array();
							$recdet['idujianta'] = $t_id;
							$recdet['nip'] = $record['ketuamajelis'.$t_ujian];
							$recdet['tipepenguji'] = 'K';
							
							$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
						}
						
						if(!$err and $record['sekretaris'.$t_ujian] != 'null') {
							$recdet = array();
							$recdet['idujianta'] = $t_id;
							$recdet['nip'] = $record['sekretaris'.$t_ujian];
							//$recdet['nip'] = $record['sekretaris'];
							$recdet['tipepenguji'] = 'P';
							
							$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
						}
						if(!$err and $record['penguji1'.$t_ujian] != 'null') {
							$recdet = array();
							$recdet['idujianta'] = $t_id;
							$recdet['nip'] = $record['penguji1'.$t_ujian];
							//$recdet['nip'] = $record['penguji1'];
							$recdet['tipepenguji'] = 'U';
							
							$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
						}
						
						if(!$err and $record['penguji2'.$t_ujian] != 'null') {
							$recdet = array();
							$recdet['idujianta'] = $t_id;
							$recdet['nip'] = $record['penguji2'.$t_ujian];
							//$recdet['nip'] = $record['penguji2'];
							
							$recdet['tipepenguji'] = 'C';
							
							$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
						}
					}
				}
			}
			
			// nilai krs skripsi
			if(!$err and !empty($keykrs)) {
				$data = static::getData($conn,$tkey);
				
				if(!empty($data['cekprasyarat'])) {
					require_once(Route::getModelPath('krs'));
					require_once(Route::getModelPath('unsurnilai'));
					
					// masukkan unsur nilai mahasiswa
					$recdet = mKRS::getKeyRecord($keykrs);
					
					$t_allnull = true;
					foreach($unsurnilai as $t_unsur) {
						$t_idunsur = $t_unsur['idunsurnilai'];
						
						$recdet['idunsurnilai'] = $t_idunsur;
						$recdet['nilaiunsur'] = $record['n_'.$t_idunsur];
						
						if($recdet['nilaiunsur'] != 'null') {
							$t_allnull = false;
							list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::saveRecord($conn,$recdet,$keykrs.'|'.$t_idunsur,true);
						}
						else
							list($p_posterr,$p_postmsg) = mUnsurNilaiMhs::delete($conn,$keykrs.'|'.$t_idunsur);
						
						if($p_posterr) {
							$ok = false;
							break;
						}
					}
					
					// masukkan krs
					if($ok) {
						$recdet = array();
						$recdet['nnumerik'] = $record['nilaiujianakhir'];
						
						if($t_allnull)
							$record['nangka'] = 'null';
						
						list($p_posterr,$p_postmsg) = mKRS::updateRecord($conn,$recdet,$keykrs,true);
						if($p_posterr) {
							$ok = false;
							break;
						}
					}
				}
			}
			
			if(!$err)
				$key = $tkey;
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return static::updateStatus($conn,$kolom);
		}
		
		// simpan sidang akhir
		function saveSidangAkhir($conn,$record) {
			require_once(Route::getModelPath('pegawai'));
			// mencari ta
			$idta = self::getTAMahasiswa($conn,$record['nim']);
			
			if(!empty($idta)) {
				$err = false;
				
				// cek ujian
				$sql = "select idujianta from ".static::table('ak_ujianta')." where
						idta = '$idta' and jenisujian = 'A'";
				$t_id = $conn->GetOne($sql);
				
				// hapus penguji
				if(!empty($t_id))
					$err = Query::qDelete($conn,static::table('ak_penguji'),"idujianta = '$t_id'");
				
				if(!$err) {
					if(empty($t_id)) {
						$record['idta'] = $idta;
						$record['jenisujian'] = 'A';
						
						$err = Query::recInsert($conn,$record,static::table('ak_ujianta'));
						if(!$err)
							$t_id = $conn->GetOne("select last_value from ".static::table('ak_ujianta_idujianta_seq'));
					}
					else
						$err = Query::recUpdate($conn,$record,static::table('ak_ujianta'),"idujianta = '$t_id'");
				}
				
				if(!$err and !empty($t_id)) {
					// masukkan penguji
					if(!$err and $record['ketuamajelis'] != 'null') {						
						$record['ketuamajelis'] = mPegawai::getIdPegawai($conn, $record['ketuamajelis']);
						
						$recdet = array();
						$recdet['idujianta'] = $t_id;
						$recdet['nip'] = $record['ketuamajelis'];
						$recdet['tipepenguji'] = 'K';
						
						$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
					}
					
					if(!$err and $record['sekretaris'] != 'null') {
					$record['sekretaris'] = mPegawai::getIdPegawai($conn, $record['sekretaris']);
						$recdet = array();
						$recdet['idujianta'] = $t_id;
						$recdet['nip'] = $record['sekretaris'];
						$recdet['tipepenguji'] = 'P';
						
						$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
					}
					
					if(!$err and $record['penguji1'] != 'null') {
					$record['penguji1'] = mPegawai::getIdPegawai($conn, $record['penguji1']);
						$recdet = array();
						$recdet['idujianta'] = $t_id;
						$recdet['nip'] = $record['penguji1'];
						$recdet['tipepenguji'] = 'U';
						
						$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
					}
					
					if(!$err and $record['penguji2'] != 'null') {
					$record['penguji2'] = mPegawai::getIdPegawai($conn, $record['penguji2']);
						$recdet = array();
						$recdet['idujianta'] = $t_id;
						$recdet['nip'] = $record['penguji2'];
						$recdet['tipepenguji'] = 'C';
						
						$err = Query::recInsert($conn,$recdet,static::table('ak_penguji'));
					}
				}
				
				return $err;
			}
			else
				return false;
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'bimbingan':
					$info['table'] = 'ak_bimbingan';
					$info['key'] = 'idta,bimbinganke';
					$info['label'] = 'bimbingan';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// mendapatkan id ta mahasiswa
		function getTAMahasiswa($conn,$nim) {
			$sql = "select idta from ".static::table()." where nim = '$nim'
					order by (case when statusta = 'L' then 0 else 1 end), tglmulai desc";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan krs ta mahasiswa
		function getKRSTAMahasiswa($conn,$nim) {
			$sql = "select s.thnkurikulum, s.kodemk, s.kodeunit, s.periode, s.kelasmk
					from ".static::table('ak_krs')." s
					join ".static::table('ak_matakuliah')." a on s.thnkurikulum = a.thnkurikulum and s.kodemk = a.kodemk and a.tipekuliah = 'T'
					where s.nim = '$nim'
					order by akademik.f_periodeurut(s.periode) desc";
			
			return $conn->GetRow($sql);
		}
		
		// mendapatkan krs ta
		function getKRSTA($conn,$idta) {
			$sql = "select s.thnkurikulum, s.kodemk, s.kodeunit, s.periode, s.kelasmk, s.nim
					from ".static::table()." t
					join ".static::table('ak_krs')." s on s.nim = t.nim
					join ".static::table('ak_matakuliah')." a on s.thnkurikulum = a.thnkurikulum and s.kodemk = a.kodemk and a.tipekuliah = 'T'
					where t.idta = '$idta' and t.cekprasyarat = -1
					order by akademik.f_periodeurut(s.periode) desc";
			
			return $conn->GetRow($sql);
		}
		
		// beban pembimbing
		function getBebanPembimbing($conn) {
			$sql = "select nip, namadosen, count(*) as jumlahbimbingan from ".static::table('r_pembimbingta')."
					group by nip, namadosen order by nip";
			
			return $conn->GetArray($sql);
		}
		
		// bimbingan
		function getBimbingan($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('ak_bimbingan')." where idta = '$key' order by bimbinganke";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function validasiBimbingan($conn,$key) {
			$sql = "update ".static::table('ak_bimbingan')." set disetujui = abs(coalesce(disetujui,0)-1) where ".static::getCondition($key,'idta,bimbinganke');
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		// pembimbing
		function pembimbing($conn,$key) {
			$sql = "select p.idpegawai, p.idpegawai::text||' - '||akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) from sdm.ms_pegawai p
					join ".static::table('ak_pembimbing')." b on (p.idpegawai::text = b.nip) and b.idta = ".(int)$key."
					order by p.namadepan||p.namatengah||p.namabelakang";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// menemukan data pembimbing, untuk autocomplete
		function findPembimbing($conn,$str,$idta) {
			global $conf;
			
			$str = strtolower($str);
			
			$sql = "select p.idpegawai::text as key, p.idpegawai::text||' - '||akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as label from sdm.ms_pegawai p
					join ".static::table('ak_pembimbing')." b on (p.idpegawai::text = b.nip) and b.idta = ".(int)$idta."
					where lower(p.idpegawai::text||' - '||akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)::varchar) like '%$str%' order by p.namadepan";
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		// cek tanggal seminar proposal
		function isTglSeminarProposal($conn,$tgl) {
			$sql = "select 1 from ".static::table('ms_kalender')." where kodekegiatan = 'P' and '$tgl' between tglmulai and tglselesai";
			$istgl = $conn->GetOne($sql);
			
			return ($istgl == '1' ? true : false);
		}
		
		// cek tanggal ujian skripsi
		function isTglUjianSkripsi($conn,$tgl) {
			$sql = "select 1 from ".static::table('ms_kalender')." where kodekegiatan = 'S' and '$tgl' between tglmulai and tglselesai";
			$istgl = $conn->GetOne($sql);
			
			return ($istgl == '1' ? true : false);
		}
		
		// status ta
		function statusTa() {
			$data = array('A' => 'Aktif', 'S' => 'Sidang', 'T' => 'Tidak Aktif', 'L'=> 'Lulus');
			
			return $data;
		}
		
		// tahap ta
		function tahapTa() {
			$data = array('PREPROP' => 'PREPROP', 'PROP' => 'PROP', 'SEMINAR' => 'SEMINAR', 'SIDANG' => 'SIDANG');
			
			return $data;
		}
		
		// status mengulang ujian
		function mengulang() {
			$data = array('1' => 'Mengulang', '2' => 'Lulus');
			
			return $data;
		}
		function getDataTa($conn,$key){
			$sql="select r.nim,r.nama,r.semestermhs,j.namaunit as jurusan,f.namaunit as fakultas, j.kodeunit from akademik.r_ta2 r 
					join gate.ms_unit j on j.kodeunit=r.kodeunit
					join gate.ms_unit f on f.kodeunit=j.kodeunitparent
					where r.idta='$key'";
			return $conn->GetRow($sql);
		}
		function updatePoint($conn,$r_key,$a_minimalpoin,$a_pointmhs){
			
			if ($a_pointmhs >= $a_minimalpoin )
				$point = '-1';
			else
				$point = '0';
			
			
			$record = array();
			$record['cekpoint'] = $point;
			
			return self::updateRecord($conn,$record,$r_key,true);
			
		}
	}
?>
