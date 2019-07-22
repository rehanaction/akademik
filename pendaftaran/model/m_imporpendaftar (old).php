<?php
	// model impor pendaftar
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	// class mapping
	class mMapping extends mModel {
		const schema = 'pendaftaran';
		const table = 'ms_mappingdata';
		const order = 'kodemapping';
		const key = 'kodemapping';
		const label = 'mapping data';
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select kodemapping, keterangan from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
	}
	
	class mMappingDetail extends mModel {
		const schema = 'pendaftaran';
		const table = 'ms_mappingdetail';
		const order = 'datasumber';
		const key = 'kodemapping,datasumber';
		const label = 'detail mapping data';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'mapping': return "kodemapping = '$key'";
			}
		}
	}
	
	class mMappingKolom extends mModel {
		const schema = 'pendaftaran';
		const table = 'ms_mappingkolom';
		const order = 'kolomsumber';
		const key = 'kolomsumber';
		const label = 'mapping kolom';
		
		// mendapatkan jenis kolom
		function jenisKolom() {
			$data = array('date' => 'TANGGAL');
			
			return $data;
		}
	}
	
	// class impor
	class mImporPendaftar {
		function noPendaftar($periode,$jalur,$ctr) {
			return substr($periode,2,2).'01'.substr($jalur,0,2).str_pad($ctr,5,'0',STR_PAD_LEFT);
		}
		
		function getLog($conn,$periode,$jalur) {
			$sql = "select l.*, s.* from pendaftaran.pd_logimporpendaftar l
					left join pendaftaran.pd_templog s on s.idlog = 1
					where l.periodedaftar = '$periode' and l.jalurpenerimaan = '$jalur'";
			
			return $conn->GetRow($sql);
		}
		
		function uploadFile($conn,$periode,$jalur,$file) {
			// properti excel
			$a_colkey = array('nomor snmptn','no peserta');
			
			// menggunakan excel reader
			require_once('../includes/phpexcel/PHPExcel.php');
			
			$a_huruf = CStr::arrayHuruf(2); // 2 digit saja
			$xls = PHPExcel_IOFactory::load($file);
			
			// ambil dari seluruh worksheet
			// $a_data = array();
			
			$a_peserta = array(); // temp
			
			// temp
			$ok = true;
			$conn->BeginTrans();
			
			// temp
			$sql = "truncate table pendaftaran.pd_temppendaftar;
					select setval('pendaftaran.pd_temppendaftar_idpeserta_seq',1,false)";
			$ok = $conn->Execute($sql);
			
			if($ok) {
				$n_sheet = $xls->getSheetCount();
				for($i=0;$i<$n_sheet;$i++) {
					$tes = $xls->setActiveSheetIndex($i);
					$sheet = $xls->getActiveSheet();
					
					// cari kolom kunci di baris pertama
					$r = 0;
					$t_colkey = '';
					$t_kosong = false;
					$a_header = array();
					
					while(++$r) {
						$t_a = strtolower(trim($sheet->getCell('A'.$r)->getValue()));
						
						// bila kosong 2 baris berturut, break
						if(empty($t_a)) {
							if($t_kosong)
								break;
							else
								$t_kosong = true;
						}
						else {
							if(!empty($t_colkey)) {
								// baca per kolom
								$t_key = trim($sheet->getCell($t_colkey.$r)->getValue());
								
								$a_data = array(); // temp
								foreach($a_header as $t_huruf => $t_header) {
									$t_data = pg_escape_string(trim($sheet->getCell($t_huruf.$r)->getValue()));
									$t_data = (strval($t_data) == '' ? 'null' : "'$t_data'"); // temp
									
									// $a_data[$t_key][$t_header] = $t_data;
									$a_data[] = "('$t_key','$t_header',$t_data)"; // temp
								}
								
								// temp
								if(!empty($a_data)) {
									$a_peserta[$t_key] = true;
									
									$sql = "insert into pendaftaran.pd_temppendaftar (nopeserta,kolom,data)
											values ".implode(',',$a_data);
									$ok = $conn->Execute($sql);
									
									if(!$ok) break;
								}
							}
							else if(substr($t_a,0,2) == 'no') { // kolom header ditemukan
								foreach($a_huruf as $t_huruf) {
									$t_col = trim(strtolower($sheet->getCell($t_huruf.$r)->getValue()));
									
									// tidak cek kolom sebelah, langsung break
									if(empty($t_col))
										break;
									
									if(in_array($t_col,$a_colkey))
										$t_colkey = $t_huruf;
									else
										$a_header[$t_huruf] = $t_col;
								}
							}
							
							$t_kosong = false;
						}
					}
					
					if(!$ok) break; // temp
				}
			}
			
			// tulis log temp
			if($ok) {
				$record = array();
				$record['periodedaftar'] = $periode;
				$record['jalurpenerimaan'] = $jalur;
				
				$err = Query::recUpdate($conn,$record,'pendaftaran.pd_templog',"idlog = 1");
				$ok = Query::isOK($err);
			}
			
			// tulis log upload
			if($ok) {
				$record = array();
				$record['periodedaftar'] = $periode;
				$record['jalurpenerimaan'] = $jalur;
				$record['uploaduser'] = Modul::getUserName();
				$record['uploadtime'] = date('Y-m-d H:i:s');
				$record['uploadip'] = $_SERVER['REMOTE_ADDR'];
				$record['uploaddata'] = count($a_peserta);
				
				$err = Query::recSave($conn,$record,'pendaftaran.pd_logimporpendaftar',"periodedaftar = '$periode' and jalurpenerimaan = '$jalur'");
				$ok = Query::isOK($err);
			}
			
			$conn->CommitTrans($ok); // temp
			
			return Query::isErr($ok);
		}
		
		function importData($conn,$periode,$jalur) {
			// mulai transaksi
			$conn->BeginTrans();
			
			// mapping data
			$sql = "select kodemapping, datasumber, datatujuan from pendaftaran.ms_mappingdetail";
			$rs = $conn->Execute($sql);
			
			$a_mapdata = array();
			while($row = $rs->FetchRow())
				$a_mapdata[$row['kodemapping']][$row['datasumber']] = $row['datatujuan'];
			
			// mapping kolom
			$sql = "select kodemapping, kolomsumber, kolomtujuan, jeniskolom from pendaftaran.ms_mappingkolom";
			$rs = $conn->Execute($sql);
			
			$a_mapkolom = array();
			while($row = $rs->FetchRow())
				$a_mapkolom[$row['kolomsumber']] = array('tujuan' => $row['kolomtujuan'], 'jenis' => $row['jeniskolom'], 'mapping' => $row['kodemapping']);
			
			// hapus data
			$err = Query::qDelete($conn,'pendaftaran.pd_pendaftar',"periodedaftar = '$periode' and jalurpenerimaan = '$jalur'");
			
			// insert data
			if(!$err) {
				$sql = "select * from pendaftaran.pd_pendaftar where nopendaftar = '-1'";
				$col = $conn->Execute($sql);
				
				$sql = "select nopeserta, kolom, data from pendaftaran.pd_temppendaftar order by nopeserta";
				$rs = $conn->Execute($sql);
				
				$i = 0;
				$ok = true;
				while($row = $rs->FetchRow()) {
					if($t_key != $row['nopeserta']) {
						$t_key = $row['nopeserta'];
						$i++;
						
						// data
						$record = array();
						$record['nopendaftar'] = self::noPendaftar($periode,$jalur,$i);
						$record['nopesertaspmb'] = $t_key;
						$record['periodedaftar'] = $periode;
						$record['jalurpenerimaan'] = $jalur;
						$record['lulusujian'] = 't';
					}
					
					$t_kolom = strtolower($row['kolom']);
					$t_mapkolom = $a_mapkolom[$t_kolom];
					/* if(empty($t_mapkolom))
						$t_mapkolom = array('tujuan' => $t_kolom); */
					
					if(!empty($t_mapkolom)) {
						$t_value = $row['data'];
						$t_jenis = $t_mapkolom['jenis'];
						$t_mapping = $t_mapkolom['mapping'];
						
						if(!empty($t_jenis)) {
							if($t_jenis == 'date') {
								list($d,$m,$y) = preg_split('/[\/\-]+/',$t_value);
								if(empty($m)) {
									$t_time = date('Y-m-d',Date::fromOADate($t_value));
									list($y,$m,$d) = explode('-',$t_time);
								}
								
								$t_value = str_pad($y,4,'0',STR_PAD_LEFT).'-'.str_pad($m,2,'0',STR_PAD_LEFT).'-'.str_pad($d,2,'0',STR_PAD_LEFT);
							}
						}
						
						if(!empty($t_mapping)) {
							$t_mapvalue = $a_mapdata[$t_mapping][$t_value];
							if(isset($t_mapvalue))
								$t_value = $t_mapvalue;
						}
						
						$record[$t_mapkolom['tujuan']] = CStr::cStrNull($t_value);
					}
					
					// masukkan data
					if($rs->EOF or $rs->fields['nopeserta'] != $t_key) {
						$sql = $conn->GetInsertSQL($col,$record);
						$ok = $conn->Execute($sql);
						
						if(!$ok) {
							$err = $conn->ErrorNo();
							break;
						}
					}
				}
			}
			else
				$ok = false;
			
			// tulis log impor
			if($ok) {
				$record = array();
				$record['periodedaftar'] = $periode;
				$record['jalurpenerimaan'] = $jalur;
				$record['imporuser'] = Modul::getUserName();
				$record['importime'] = date('Y-m-d H:i:s');
				$record['imporip'] = $_SERVER['REMOTE_ADDR'];
				$record['impordata'] = $i;
				
				$err = Query::recSave($conn,$record,'pendaftaran.pd_logimporpendaftar',"periodedaftar = '$periode' and jalurpenerimaan = '$jalur'");
				$ok = Query::isOK($err);
			}
			
			// selesai transaksi
			$conn->CommitTrans($ok);
			
			return $err;
		}
	}	
?>