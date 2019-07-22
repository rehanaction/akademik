<?php
	// model tagihan va
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTagihanVA extends mModel {
		const schema = 'h2h';
		const table = 'ke_tagihanva';
		const order = "(case when status = 'A' then 0 else 1 end), trxid desc";
		const key = 'trxid';
		const label = 'trxamount';
		
		const tabledetail = 'ke_tagihanvadetail';
		const namauniversitas = 'Universitas Esa Unggul';
		
		function listQuery() { 
			$sql = "select a.* from (select t.trxid, t.trxamount+t.adminamount as trxamount, t.kodeva, t.status,
					case t.status when 'S' then 'Belum' when 'A' then 'Belum Bayar' when 'C' then 'Batal' when 'L' then 'Lunas' end as namastatus,
					coalesce(m.nim,p.nopendaftar) as noid, coalesce(m.nama,p.nama,f.namapeserta) as nama,
					u.namaunit, u.infoleft, u.inforight, '".Pay::PAY_CLIENT_ID."'||t.trxid::text as billingid
					from h2h.ke_tagihanva t
					left join akademik.ms_mahasiswa m on t.nim = m.nim
					left join pendaftaran.pd_pendaftar p on t.nopendaftar = p.nopendaftar and m.nim is null
					left join h2h.ke_pembayaranfrm f on t.idpembayaranfrm = f.idpembayaranfrm
					left join gate.ms_unit u on coalesce(m.kodeunit,p.pilihanditerima) = u.kodeunit) a";
			
			return $sql;
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'kodeunit':
					global $conn;
					
					$row = static::getDataUnit($conn,$key);
					
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
				default:
					return parent::getListFilter($col,$key);
			}
		}

		function getTagihanAktif($conn,$nim) {
			$sql = "select trxid from ".static::table()." where status = 'A'
					and (nim = ".Query::escape($nim)." or nopendaftar = ".Query::escape($nim).")";
			
			return $conn->GetOne($sql);
		}

		function getListDetail($conn,$key) {
			$sql = "select d.idtagihan, t.idtagihan as idcek, d.nominaltagihan, d.potongan, d.denda,
					t.nominaltagihan as nominalcek, t.potongan as potongancek, t.denda as dendacek,
					t.flaglunas, t.jenistagihan, j.namajenistagihan, t.angsuranke, t.tgltagihan, t.periode, t.bulantahun,
					case when t.isvalid = -1 and t.periode <= s.periodesekarang and t.tgltagihan <= current_date then 0 else 1 end as isinvalid
					from ".static::table(static::tabledetail)." d
					join h2h.ke_tagihan t on d.idtagihan = t.idtagihan
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					join h2h.ke_settingdetail s on s.jenistagihan = j.kodekelompok
					where d.trxid = ".Query::escape($key)." order by t.periode, d.idtagihan";
			$rs = $conn->Execute($sql);

			$data = array();
			$idtagihan = array();
			while($row = $rs->FetchRow()) {
				$row['isdeposit'] = false;

				$data[] = $row;
				$idtagihan[] = $row['idtagihan'];
			}

			$sql = "select d.iddeposit, e.iddeposit as idcek, d.nominaltagihan, d.potongan, d.denda,
					e.nominaldeposit-e.nominalpakai as nominalcek, e.jenisdeposit, e.novoucher,
					case when e.status = '-1' and e.nominaldeposit > e.nominalpakai and e.tgldeposit <= current_date
					and (e.tglexpired is null or e.tglexpired > current_date)
					and (e.idtagihan is null or e.idtagihan in ('".implode("','",$idtagihan)."')) then 0 else 1 end as invalid
					from ".static::table(static::tabledetail)." d
					join h2h.ke_deposit e on d.iddeposit = e.iddeposit
					where d.trxid = ".Query::escape($key)." order by d.iddeposit";
			$rs = $conn->Execute($sql);

			while($row = $rs->FetchRow()) {
				$row['isdeposit'] = true;

				$data[] = $row;
			}

			return $data;
		}

		// aksi

		function insertRecord($conn,$record,$status=false) {
			Query::setLogColumn($record);
			
			$col = $conn->SelectLimit("select * from ".static::table(),1);
			$sql = $conn->GetInsertSQL($col,$record).' returning '.static::key;
			$rs = $conn->Execute($sql);
			
			return array($conn->ErrorNo(),$rs->fields[static::key]);
		}

		function deleteDetail($conn,$key) {
			return Query::qDelete($conn,static::table(static::tabledetail),'trxid = '.Query::escape($key));
		}
		
		function insertDetail($conn,$record) {
			return Query::recInsert($conn,$record,static::table(static::tabledetail));
		}
		
		function createVATagihan($conn,$arrid,$nim,$kelompok,$isdatamhs=true,$key=null,$createnew=false) {
			// cek data yang sudah ada
			if(!empty($key) and $createnew) {
				// tidak pakai transaksi karena hanya inquiry
				$req = array();
				$req['trx_id'] = $key;
				
				$resp = Pay::inquiryBilling($req);
				
				$record = array();
				if($resp['status'] == Pay::ERROR_OK) {
					$record['kodeva'] = $resp['data']['virtual_account'];
					$record['expiredtime'] = $resp['data']['datetime_expired'];
					
					if($resp['data']['va_status'] == '2') {
						if(empty($resp['data']['datetime_payment_iso8601']))
							$record['status'] = 'C';
						else
							$record['status'] = 'L';
					}
					else
						$record['status'] = 'A';
				}
				else if($resp['status'] == Pay::ERROR_NOTFOUND) {
					$record['kodeva'] = null;
					$record['status'] = 'S';
				}
				else
					return false;
				
				$err = static::updateRecord($conn,$record,$key);
				if(empty($err) and $record['status'] == 'C')
					$key = null;
			}
			
			if(empty($err)) {
				// ambil data mahasiswa
				if($isdatamhs)
					$mhs = static::getDatamhs($conn,$nim);
				else
					$mhs = static::getDatapendaftar($conn,$nim);
				
				// ambil tagihan
				$a_id = array();
				foreach($arrid as $v)
					$a_id[] = Query::escape($v);

				$arr_tagihan = static::getInquiry($conn,$nim,$kelompok,$arrid);
				
				// hitung total
				$jmltagihan = 0;
				foreach($arr_tagihan as $tagihan)
					$jmltagihan += ((float)$tagihan['nominaltagihan'] - (float)$tagihan['potongan'] + (float)$tagihan['denda']);
				
				// ambil kelompok
				$sql = "select namakelompok from h2h.lv_kelompoktagihan where kodekelompok = ".Query::escape($kelompok);
				$namakelompok = $conn->GetOne($sql);
				
				if(empty($key)) {
					// buat parent
					$record = array();
					$record['trxamount'] = $jmltagihan;
					$record['kodekelompok'] = $kelompok;
					if($isdatamhs)
						$record['nim'] = $nim;
					else
						$record['nopendaftar'] = $nim;
					
					list($err,$t_key) = static::insertRecord($conn,$record);
				}
				else {
					$t_key = $key;
					
					// hapus child
					$err = static::deleteDetail($conn,$t_key);
				}
			}
			
			// masukkan child
			if(empty($err)) {
				foreach($arr_tagihan as $i => $tagihan) {
					$record = array();
					$record['trxid'] = $t_key;
					$record['nominaltagihan'] = (float)$tagihan['nominaltagihan'];
					$record['potongan'] = (float)$tagihan['potongan'];
					$record['denda'] = (float)$tagihan['denda'];

					if(empty($tagihan['iddeposit']))
						$record['idtagihan'] = $tagihan['idtagihan'];
					else
						$record['iddeposit'] = $tagihan['iddeposit'];
					
					$err = static::insertDetail($conn,$record);
					if($err)
						break;
				}
			}
			
			// kirim ke bank dan update parent
			if(empty($err)) {
				$req = array();
				$req['trx_id'] = $t_key;
				$req['trx_amount'] = $jmltagihan;
				$req['customer_id'] = $nim;
				$req['customer_name'] = $mhs['nama'];
				$req['customer_email'] = $mhs['email'];
				$req['customer_phone'] = $mhs['hp'];
				$req['description'] = 'Tagihan '.$namakelompok.' '.static::namauniversitas;
				$req['virtual_account'] = $nim;
				
				$resp = Pay::createBilling($req);
				
				$record = array();
				if($resp['status'] == Pay::ERROR_OK) {
					$record['trxamount'] = $jmltagihan;
					$record['adminamount'] = $resp['data']['direct_amount'];
					$record['kodeva'] = $resp['data']['virtual_account'];
					$record['customername'] = $resp['data']['customer_name'];
					$record['expiredtime'] = $resp['data']['datetime_expired'];
					$record['status'] = 'A';
				}
				else if(!empty($key)) {
					if($resp['status'] == Pay::ERROR_PAID)
						$record['status'] = 'L';
					else if($resp['status'] == Pay::ERROR_EXPIRED)
						$record['status'] = 'C';
				}
				
				if(empty($record))
					$err = true;
				else
					static::updateRecord($conn,$record,$t_key); // tidak mempengaruhi transaksi
			}
			
			// ambil data 
			if(empty($err))
				$row = static::getData($conn,$t_key);
			else
				$row = null;
			
			return array($err,$row);
		}

		function bayarVA($conn,$trxid,$data) {
			// include
			// require_once(Route::getModelPath('pembayaranfrm'));
			
			$rowv = static::getData($conn,$trxid);
			$rows = static::getListDetail($conn,$trxid);
			
			$kelompok = $rowv['kodekelompok'];
			
			$rowt = static::getDataSetting($conn,$kelompok);
			
			$refno = $trxid.'-'.$data['payment_ntb'];
			$tglbayar = date('Y-m-d H:i:s',DateTime::createFromFormat('Y-m-d H:i:sP',str_replace('T',' ',$data['datetime_payment_iso8601']))->getTimestamp());
			
			// if(empty($rowv['idpembayaranfrm'])) {
				$cek = static::cekRefno($conn,$refno);
				if($cek) {
					// cek detail va
					if(empty($rows)) {
						if(!empty($rowv['nim']))
							$nim = $rowv['nim'];
						else
							$nim = $rowv['nopendaftar'];

						$arr_tagihan = static::getInquiry($conn,$nim,$kelompok);

						$total = 0;
						foreach($arr_tagihan as $i => $tagihan)
							$total += ($tagihan['nominaltagihan']-$tagihan['potongan']+$tagihan['denda']);

						// masukkan detail transaksi
						if($total == $data['payment_amount']) {
							foreach($arr_tagihan as $i => $tagihan) {
								$record = array();
								$record['trxid'] = $trxid;
								$record['nominaltagihan'] = (float)$tagihan['nominaltagihan'];
								$record['potongan'] = (float)$tagihan['potongan'];
								$record['denda'] = (float)$tagihan['denda'];

								if(empty($tagihan['iddeposit']))
									$record['idtagihan'] = $tagihan['idtagihan'];
								else
									$record['iddeposit'] = $tagihan['iddeposit'];
								
								$err = static::insertDetail($conn,$record);
								if($err)
									break;
							}

							if(empty($err))
								$rows = $arr_tagihan;
						}
					}

					if(!empty($rows)) {
						$record = array();
						$record['tglbayar'] = $tglbayar;
						$record['jumlahbayar'] = $data['payment_amount'];
						$record['jumlahuang'] = $data['payment_amount'];
						$record['ish2h'] = 0;
						$record['companycode'] = '009'; // SevimaPay: BNI (default)
						$record['terminalid'] = 'SevimaPay'; // SevimaPay
						$record['refno'] = $refno;
						$record['periodebayar'] = $rowt['periodesekarang'];
						$record['jenistagihan'] = $rowv['kodekelompok'];
						
						if(!empty($rowv['nim']))
							$record['nim'] = $rowv['nim'];
						else
							$record['nopendaftar'] = $rowv['nopendaftar'];
						
						list($err,$idpembayaran) = static::insertPembayaran($conn,$record);
						
						// masukkan detail pembayaran
						if(empty($err)) {
							$tagihan = array();
							$deposit = array();
							foreach($rows as $row) {
								$nominal = $row['nominaltagihan']-$row['potongan']+$row['denda'];

								if(empty($row['iddeposit']))
									$tagihan[$row['idtagihan']] = $nominal;
								else
									$deposit[$row['iddeposit']] = -1 * $nominal; // karena deposit adalah potongan (minus)
							}

							$totalbayar = 0;
							$detbayar = array();
							foreach($tagihan as $idt => $jumlaht) {
								$totalbayar += $jumlaht;
								$sisatagihan = $jumlaht;
								foreach($deposit as $idd => $jumlahd) {
									if($jumlahd <= 0)
										continue;
									
									if($jumlahd >= $sisatagihan) {
										$detbayar[$idd][$idt] = $jumlaht;
										$deposit[$idd] -= $jumlaht;
										
										$sisatagihan = 0;
										break;
									}
									else {
										$detbayar[$idd][$idt] = $jumlahd;
										$deposit[$idd] = 0;
										
										$sisatagihan -= $jumlahd;
									}
								}
								
								if($sisatagihan > 0)
									$detbayar[0][$idt] = $sisatagihan;
							}
							
							foreach($detbayar as $id => $detbayarid) {
								foreach($detbayarid as $idt => $nominal) {
									$rec = array();
									$rec['idpembayaran'] = $idpembayaran;
									$rec['idtagihan'] = $idt;
									$rec['nominalbayar'] = $nominal;

									if($id !== 0)
										$rec['iddeposit'] = $id;
									
									$err = static::insertPembayaranDetail($conn,$rec);
									
									if(!empty($err))
										break 2;
								}
							}
						}
					}
					else
						$err = true;
				}
			/* }
			else {
				$cek = mPembayaranfrm::cekRefno($conn,$refno);
				if($cek) {
					$record = array();
					$record['isbayar'] = -1;
					$record['tglbayar'] = $tglbayar;
					$record['companycode'] = '009'; // SevimaPay: BNI (default)
					$record['terminalid'] = 'SevimaPay'; // SevimaPay
					$record['refno'] = $refno;
					
					$err = mPembayaranfrm::updateRecord($conn,$record,$rowv['idpembayaranfrm']);
				}
			} */
			
			return $err;
		}

		// lain-lain juga dimasukkan sini lah :D
		
		function getDataUnit($conn,$kodeunit){
			$sql = "select * from gate.ms_unit where kodeunit = ".Query::escape($kodeunit);
			
			return $conn->getRow($sql);
		}

		function arrQueryKelompokTagihan($conn,$short=false) {
			$label = 'namakelompok';
			if(!$short)
				$label = "kodekelompok||' - '||$label";
			
			$sql = "select kodekelompok, $label as namakelompok
					from h2h.lv_kelompoktagihan order by kodekelompok";
			
			return Query::arrQuery($conn,$sql);
		}

		function getDatamhs($conn,$mhs){
			$sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, m.jalurpenerimaan,
					m.gelombang, g.namagelombang, m.periodemasuk, m.sistemkuliah, s.namasistem,
					m.hp, m.email
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join pendaftaran.lv_gelombang g on m.gelombang = g.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = m.sistemkuliah
					where nim = ".Query::escape($mhs);
			return $conn->getRow($sql);
		}
		
		function getDatapendaftar($conn,$mhs){
			$sql = "select m.nopendaftar, m.nimpendaftar, m.nama, m.pilihanditerima as kodeunit, u.namaunit, m.jalurpenerimaan,
					m.idgelombang as gelombang, g.namagelombang, m.periodedaftar as periodemasuk, m.sistemkuliah, s.namasistem,
					m.hp, m.email
					from pendaftaran.pd_pendaftar m 
					left join gate.ms_unit u on m.pilihanditerima = u.kodeunit
					left join pendaftaran.lv_gelombang g on m.idgelombang = g.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = m.sistemkuliah
					where (nopendaftar = ".Query::escape($mhs)." or nimpendaftar = ".Query::escape($mhs).")";
			return $conn->getRow($sql);
		}

		function getDataSetting($conn,$kelompok) {
			$sql = "select * from h2h.ke_settingdetail where jenistagihan = ".Query::escape($kelompok);
			
			return $conn->GetRow($sql);
		}

		function getInquiry($conn,$mhs,$kelompok,$arrid=null) {
			$sql = "select t.idtagihan, t.jenistagihan, j.namajenistagihan, t.angsuranke, t.flaglunas,
					t.tgltagihan, t.periode, t.bulantahun, t.nominaltagihan, t.potongan, t.denda
					from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on j.jenistagihan = t.jenistagihan and j.kodekelompok = ".Query::escape($kelompok)."
					join h2h.ke_settingdetail s on s.jenistagihan = j.kodekelompok
					where";
			
			if(empty($arrid)) {
				$sql .= " (t.nim = ".Query::escape($mhs)." or t.nopendaftar = ".Query::escape($mhs).")
						and t.isvalid = -1 and t.flaglunas in ('BB','BL') and t.periode <= s.periodesekarang and t.tgltagihan <= current_date";
			}
			else
				$sql .= " idtagihan in ('".implode("','",$arrid['idtagihan'])."')";
			
			$sql .= " order by t.periode, t.idtagihan";
			$rs = $conn->Execute($sql);

			$data = array();
			$idtagihan = array();
			while($row = $rs->FetchRow()) {
				$row['isdeposit'] = false;

				$data[] = $row;
				$idtagihan[] = $row['idtagihan'];
			}

			if(empty($arrid) or !empty($arrid['iddeposit'])) {
				$sql = "select d.iddeposit, d.jenisdeposit, d.novoucher, 0 as nominaltagihan, d.nominaldeposit-d.nominalpakai as potongan, 0 as denda
						from h2h.ke_deposit d
						where";

				if(empty($arrid)) {
					$sql .= " (d.nim = ".Query::escape($mhs)." or d.nopendaftar = ".Query::escape($mhs).")
							and d.status = '-1' and d.nominaldeposit > d.nominalpakai and d.tgldeposit <= current_date
							and (d.tglexpired is null or d.tglexpired > current_date)
							and (d.idtagihan is null or d.idtagihan in ('".implode("','",$idtagihan)."'))";
				}
				else
					$sql .= " d.iddeposit in ('".implode("','",$arrid['iddeposit'])."')";
				
				$sql .= " order by d.iddeposit";
				$rs = $conn->Execute($sql);

				while($row = $rs->FetchRow()) {
					$row['isdeposit'] = true;

					$data[] = $row;
				}
			}

			return $data;
		}

		function cekRefno($conn,$refno) {
			$sql = "select 1 from ".static::table('ke_pembayaran')." where refno = ".Query::escape($refno);
			$rs = $conn->GetOne($sql);

			if($rs)
				return false;
			else
				return true;
		}

		function insertPembayaran($conn,$record,$status=false) {
			Query::setLogColumn($record);
			
			$col = $conn->SelectLimit("select * from ".static::table('ke_pembayaran'),1);
			$sql = $conn->GetInsertSQL($col,$record).' returning idpembayaran';
			$rs = $conn->Execute($sql);
			
			return array($conn->ErrorNo(),$rs->fields['idpembayaran']);
		}

		function insertPembayaranDetail($conn,$record,$status=false) {
			$err = Query::recInsert($conn,$record,static::table('ke_pembayarandetail'));
			
			if($status)
				return static::insertStatus($conn);
			else
				return $err;
		}
	}
?>