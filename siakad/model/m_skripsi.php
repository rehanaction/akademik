<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mSkripsi extends mModel {
		const schema = 'akademik';
		const table = 'ak_pengajuanta';
		const order = 'm.kodeunit';
		// const key = 'nim,judulta';
		const key = 'idpengajuanta';
		const label = 'pengajuan skripsi';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *,peg.idpegawai::text||' - '||akademik.f_namalengkap(peg.gelardepan,peg.namadepan,peg.namatengah,peg.namabelakang,peg.gelarbelakang) as pemb1, 
					peg2.idpegawai::text||' - '||akademik.f_namalengkap(peg2.gelardepan,peg2.namadepan,peg2.namatengah,peg2.namabelakang,peg2.gelarbelakang) as pemb2 from ".self::table()." p left join akademik.ms_mahasiswa m on m.nim=p.nim 
					left join gate.ms_unit u on u.kodeunit=m.kodeunit
					left join sdm.ms_pegawai peg on peg.idpegawai::text=p.pemb1
					left join sdm.ms_pegawai peg2 on peg2.idpegawai::text=p.pemb2";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periodemasuk': return "substring(m.periodemasuk,1,4) = '$key'";
				case 'nim_skripsi': return "p.nim = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
			}
		}
		
		function dataQuery($key) {
		$sql = "select *,m.nim||' - '||m.nama as nim_mhs,peg.idpegawai::text||' - '||akademik.f_namalengkap(peg.gelardepan,peg.namadepan,peg.namatengah,peg.namabelakang,peg.gelarbelakang) as dosenpembimbing, 
					peg2.idpegawai::text||' - '||akademik.f_namalengkap(peg2.gelardepan,peg2.namadepan,peg2.namatengah,peg2.namabelakang,peg2.gelarbelakang) as dosenpembimbing2 from ".self::table()." p left join akademik.ms_mahasiswa m on m.nim=p.nim 
					left join gate.ms_unit u on u.kodeunit=m.kodeunit
					left join sdm.ms_pegawai peg on peg.idpegawai::text=p.pemb1
					left join sdm.ms_pegawai peg2 on peg2.idpegawai::text=p.pemb2
					left join akademik.ms_mahasiswa mm on mm.nim=p.nim
					where ".static::getCondition($key,'','p');
			
			return $sql;
		}
		
		function insertCRecord($conn,$kolom,$record,&$key) {
			global $conf;
			
			//cek apakah mhs ini sdh ambil Skripsi di ak_krs nya
			$t_nim = $record['nim'];
			$sql = "select 1 from akademik.ak_krs s
					join akademik.ak_matakuliah a on s.thnkurikulum = a.thnkurikulum and s.kodemk = a.kodemk and a.tipekuliah = 'T'
					where s.nim = '$t_nim'";
			$isambil = $conn->GetRow($sql);
			
			//if(empty($isambil))
				//return array(true,'Mahasiswa dengan NIM '.$t_nim.' belum mengambil mata kuliah skripsi');
			
			// unset record
			$upload = array();
			if(!empty($kolom)) {
				foreach($kolom as $datakolom) {
					if($datakolom['readonly']) {
						unset($record[$datakolom['kolom']]);
					}
					else if($datakolom['type'][0] == 'U') {
						$name = empty($datakolom['nameid']) ? $datakolom['kolom'] : $datakolom['nameid'];
						$file = $_FILES[$name];
						
						if(!empty($file) and empty($file['error'])) {
							$record[$datakolom['kolom']] = CStr::cStrNull($file['name']);
							
							$file['uptype'] = $datakolom['uptype'];
							$upload[] = $file;
						}
						else
							unset($record[$datakolom['kolom']]);
					}
				}
			}
			
			$err = Query::recInsert($conn,$record,static::table());
			if(!$err) {
				$seq = static::sequence;
				if(empty($seq))
					$key = static::getRecordKey($key,$record);
				else
					$key = static::getLastValue($conn);
				
				if(!empty($upload)) {
					$ok = true;
					foreach($upload as $file) {
						$ok = Route::uploadFile($file['uptype'],$key,$file['tmp_name']);
						if(!$ok) break;
					}
					
					if(!$ok)
						return array(true,'Upload data '.$label.' gagal');
				}
			}
			
			return static::insertStatus($conn,$kolom);
		}
		
		// tipe kuliah
		function statuspengajuan() {
			$data = array('P' => 'Pengajuan', 'S' => 'Disetujui', 'T' => 'Ditolak');
			return $data;
		}
	}
?>
