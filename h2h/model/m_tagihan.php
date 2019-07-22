<?php
	class mTagihan {
		// jenis tagihan
		function getJenisTagihanFromKode($conn,$kodetagihan) {
			/* // tipe data integer */
			$kodetagihan = (int)$kodetagihan;
			
			$sql = "select kodekelompok from h2h.lv_jenistagihan where kodetagihan = ?";
			
			return $conn->GetOne($sql,array($kodetagihan));
			
			//return $kodetagihan; // kodekelompok
		}
		
		function getNamaJenisTagihanFromKode($conn,$kodetagihan) {
			/* // tipe data integer
			$kodetagihan = (int)$kodetagihan;
			
			$sql = "select namajenistagihan from h2h.lv_jenistagihan where kodetagihan = ?";
			
			return $conn->GetOne($sql,array($kodetagihan)); */
			
			$sql = "select namakelompok from h2h.lv_kelompoktagihan where kodekelompok = ?";
			
			return $conn->GetOne($sql,array($kodetagihan));
		}
		
		// tarif
		function getKodeFormulirFromIDTarif($conn,$idtarif) {
			$sql = "select kodeformulir	from h2h.ke_tariffrm where idtariffrm = ?";
			
			return $conn->GetOne($sql,array($idtarif));
		}
		
		function getInfoTarifFormulir($conn,$pilihan) {
			$sql = "select t.idgelombang as gelombang, t.jalurpenerimaan, case when s.kodebasis = 'P' then 2 else 1 end as basis,
					t.programpend||' '||t.sistemkuliah||' '||t.jumlahpilihan||' pilihan' as keterangan
					from h2h.ke_tariffrm t
					left join akademik.ak_sistem s on t.sistemkuliah = s.sistemkuliah
					where t.kodeformulir = ?";
			
			return $conn->GetRow($sql,array($pilihan));
		}
		
		function getListInquiryFormulir($conn,$pilihan,$full=false) {
			$sql = "select t.* from h2h.ke_tariffrm t
					where t.kodeformulir = ? and t.isaktif = 1";			
			$row = $conn->GetRow($sql,array($pilihan));
			
			// tidak ada data atau cek di record pertama
			$data = false;
			if(empty($row)) {
				$err = 'ERROR_NO_DATA';
				$msg = 'tarif is not available';
			}
			else {
				$err = false;
				$msg = false;
				
				$data = array(array(
							'billID'=>$row['idtariffrm'],
							'billName'=>self::getNamaJenisTagihanFromKode($conn,H2H_KODEFORMULIR),
							'billAmount'=>$row['nominaltarif'],
							'periode'=>$row['periodedaftar'],
							'currency'=>H2H_CURRENCY
						));
			}
			
			if($full)
				return array($data,$err,$msg);
			else
				return $data;
		}
		
		function getPeriodeBayarFormulir($conn,$kodeformulir) {
			$sql = "select max(periodedaftar) from h2h.ke_tariffrm where kodeformulir = ?";
			
			return $conn->GetOne($sql,array($kodeformulir));
		}
		
		// tagihan
		function getListInquiry($conn,$nim,$periode=null,$full=false) {
			/* $sql = "select t.idtagihan, t.periode, t.flaglunas, t.isvalid, j.namajenistagihan,
					t.nominaltagihan-t.potongan+t.denda as nominaltagihan from h2h.ke_tagihan t
					left join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					where coalesce(t.nim,t.nopendaftar) = ? and t.jenistagihan = ? and not(t.flaglunas = 'F' and t.isvalid <> 0) and t.tgltagihan <= current_date".
					(empty($periode) ? '' : " and (t.periode = ? or ((t.periode is null or t.periode < ?) and t.flaglunas in ('BB','BL','S')))").
					" order by case t.flaglunas when 'BB' then 0 when 'BL' then 0 when 'S' then 1 when 'L' then 2 else 3 end,
					case when t.isvalid = 0 then 1 else 0 end, t.periode"; */
			$sql = "select t.idtagihan, t.periode, t.flaglunas, t.isvalid, j.namajenistagihan, k.namakelompok,
					t.nominaltagihan-t.potongan+t.denda-t.nominalbayar as nominaltagihan from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					join h2h.lv_kelompoktagihan k on j.kodekelompok = k.kodekelompok
					where coalesce(t.nim,t.nopendaftar) = ? and not(t.flaglunas = 'F' and t.isvalid <> 0) and t.tgltagihan <= current_date".
					(empty($periode) ? '' : " and (t.periode = ? or ((t.periode is null or t.periode < ?) and t.flaglunas in ('BB','BL','S')))").
					" order by case t.flaglunas when 'BB' then 0 when 'BL' then 0 when 'S' then 1 when 'L' then 2 else 3 end,
					case when t.isvalid = 0 then 1 else 0 end, t.periode";
			
			$param = array($nim);
			if(!empty($periode)) {
				$param[] = $periode;
				$param[] = $periode;
			}
          // print_r($param);
           //die();
			
			$rs = $conn->Execute($sql,$param);
			
			// tidak ada data atau cek di record pertama
			$data = false;
			if(!$rs) {
				$err = 'ERROR_DB';
				$msg = 'error db';
			}
			else if($rs->EOF) {
				$err = 'ERROR_NO_DATA';
				$msg = 'bill not exist';
			}
			else if(empty($rs->fields['isvalid'])) {
				$err = 'ERROR_BILL_PENDING';
				$msg = 'bill suspend';
			}
			else if($rs->fields['flaglunas'] == 'S') {
				$err = 'ERROR_BILL_PENDING';
				$msg = 'bill suspend';
			}
			else if($rs->fields['flaglunas'] == 'L') {
				$err = 'ERROR_BILL_PAID';
				$msg = 'bill paid';
			}
			else {
				$err = false;
				$msg = false;
				
				$total = 0;
				$data = array();
				$idtagihan = array();
				while($row = $rs->FetchRow()) {
					if(!empty($row['isvalid']) and ($row['flaglunas'] == 'BB' or $row['flaglunas'] == 'BL')) {
						// tagihan
						$jumlah = (float)$row['nominaltagihan'];
						$total += $jumlah;
						
						$idtagihan[] = $row['idtagihan'];
						$data[] = array(
									'billID'=>$row['idtagihan'],
									// 'billName'=>$row['namajenistagihan'],
									'billName'=>$row['namakelompok'],
									'billAmount'=>$jumlah,
									'periode'=>$row['periode'],
									'currency'=>H2H_CURRENCY,
								);
					}
				}
				
				// deposit
				$sql = "select iddeposit, periode, jenisdeposit, novoucher, nominaldeposit-nominalpakai as nominaldeposit
						from h2h.ke_deposit
						where coalesce(nim,nopendaftar) = ? and status = '-1' and nominaldeposit > nominalpakai
						and (tglexpired is null or tglexpired > current_date) and tgldeposit <= current_date
						and (idtagihan is null or idtagihan in ('".implode("','",$idtagihan)."'))
						order by case when tglexpired is null then 1 else 0 end, tglexpired, nominaldeposit-nominalpakai";
				$rs = $conn->Execute($sql,array($nim));
				
				while($row = $rs->FetchRow()) {
					$jumlah = -1*(float)$row['nominaldeposit'];
					$total += $jumlah;
					
					$data[] = array(
								'billID'=>($row['jenisdeposit'] == 'V' ? $row['novoucher'] : 'DEP'.str_pad($row['iddeposit'],21,'0',STR_PAD_LEFT)),
								'billName'=>($row['jenisdeposit'] == 'V' ? 'Voucher' : 'Deposit'), // nantinya dicek di payment
								'billAmount'=>$jumlah,
								'periode'=>$row['periode'],
								'currency'=>H2H_CURRENCY,
							);
				}
				
				// tagihan kosong atau minus, dianggap tidak ada
				if($total <= 0) {
					$data = false;
					
					$err = 'ERROR_NO_DATA';
					$msg = 'deposit or voucher greater than bill';
				}
			}
			
			if($full)
				return array($data,$err,$msg);
			else
				return $data;
		}
		
		function getPeriodeBayarTagihan($conn,$idpembayaran) {
			$sql = "select max(t.periode) from h2h.ke_tagihan t
					join h2h.ke_pembayarandetail d on d.idtagihan = t.idtagihan and d.idpembayaran = ?";
			
			return $conn->GetOne($sql,array($idpembayaran));
		}
		
		// pembayaran
		function isPaymentError($conn,&$billdet,$jumlah) {
			$err = false;
			$msg = false;
			
			$total = 0;
			$detids = array();
			$depids = array();
			$billids = array();
			foreach($billdet as $i => $cek) {
				$id = $cek['billID'];
				$name = $cek['billName'];
				$jmldet = (float)$cek['billAmount'];
				
				$total += $jmldet;
				
				// name bisa tidak diisi
				/* if($name == 'Deposit') {
					$id = (int)substr($id,3);
					$depids[] = $id;
				}
				else if($name == 'Voucher') {
					$id = self::getIDDepositFromNoVoucher($conn,$id);
					$depids[] = $id;
				}
				else
					$billids[] = $id; */
				
				if(substr($id,0,3) == 'DEP') {
					$id = (int)substr($id,3);
					$depids[] = $id;
				}
				else {
					// cek voucher
					$vid = self::getIDDepositFromNoVoucher($conn,$id);
					if(!empty($vid)) {
						$id = $vid;
						$depids[] = $id;
					}
					else
						$billids[] = $id;
				}
				
				$detids[$id] = $i;
			}
			
			/* if($total < $jumlah) {
				$err = 'ERROR_AMOUNT_UNDER';
				$msg = 'bill detail under - '.$total;
			}
			else if($total > $jumlah) {
				$err = 'ERROR_AMOUNT_OVER';
				$msg = 'bill detail over - '.$total;
			} */
			
			// cek tagihan
			if($err === false) {
				$sql = "select idtagihan, jenistagihan, nominaltagihan, potongan, denda, flaglunas, isvalid
						from h2h.ke_tagihan where idtagihan in ('".implode("','",$billids)."')";
				$rs = $conn->Execute($sql);
				
				$i = 0;
				$total = 0;
				if(!$rs) {
					$err = 'ERROR_DB';
					$msg = 'error db';
				}
				else if($rs->EOF) {
					$err = 'ERROR_NO_DATA';
					$msg = 'no data';
				}
				else {
					$idtagihan = array();
					while($row = $rs->FetchRow()) {
						$i++;
						$nominal = (float)$row['nominaltagihan'] + (float)$row['denda'] - (float)$row['potongan'];
						
						$total += $nominal;
						$idtagihan[$row['idtagihan']] = true;
						
						$billdet[$detids[$row['idtagihan']]]['billName'] = $row['jenistagihan'];
						$billdet[$detids[$row['idtagihan']]]['billAmount'] = $nominal;
						
						if(empty($row['isvalid'])) {
							$err = 'ERROR_BILL_PENDING';
							$msg = 'bill suspend';
						}
						else if($row['flaglunas'] == 'S') {
							$err = 'ERROR_BILL_PENDING';
							$msg = 'bill suspend';
						}
						else if($row['flaglunas'] == 'L') {
							$err = 'ERROR_BILL_PAID';
							$msg = 'bill paid';
						}
						
						if($err !== false)
							break;
					}
				}
			}
			
			// cek deposit
			if($err === false and !empty($depids)) {
				$sql = "select iddeposit, status, idtagihan, jenisdeposit, nominaldeposit-nominalpakai as nominaldeposit,
						case when (tglexpired is null or tglexpired > current_date) then 0 else 1 end as isexpired,
						case when (tgldeposit <= current_date) then 0 else 1 end as isforbidden
						from h2h.ke_deposit
						where iddeposit in ('".implode("','",$depids)."')";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()) {
					$i++;
					$nominal = (float)$row['nominaldeposit'];
					
					$total -= $nominal;
					
					$billdet[$detids[$row['iddeposit']]]['billName'] = ($row['jenisdeposit'] == 'V' ? 'Voucher' : 'Deposit');
					$billdet[$detids[$row['iddeposit']]]['billAmount'] = -1*$nominal;
					
					if(empty($row['status'])) {
						$err = 'ERROR_NO_DATA';
						$msg = 'inactive deposit';
					}
					else if((float)$row['nominaldeposit'] <= 0) {
						$err = 'ERROR_NO_DATA';
						$msg = 'used deposit';
					}
					else if(!empty($row['isexpired'])) {
						$err = 'ERROR_NO_DATA';
						$msg = 'expired deposit';
					}
					else if((!empty($row['idtagihan']) and empty($idtagihan[$row['idtagihan']])) or !empty($row['isforbidden'])) {
						$err = 'ERROR_NO_DATA';
						$msg = 'cannot use deposit';
					}
					
					if($err !== false)
						break;
				}
			}
			
			// cek gabungan
			if($err === false) {
				if($i != count($billdet)) {
					$err = 'ERROR_NO_DATA';
					$msg = 'different count data';
				}
				else if($total <= 0) {
					$err = 'ERROR_NO_DATA';
					$msg = 'deposit greater than bill';
				}
				/*else if($jumlah < $total) {
					$err = 'ERROR_AMOUNT_UNDER';
					$msg = 'bill under - '.$jumlah;
				}*/
				else if($jumlah > $total) {
					$err = 'ERROR_AMOUNT_OVER';
					$msg = 'bill over - '.$jumlah;
				}
			}
			
			return array($err,$msg);
		}
		
		function getPembayaranFromRefNo($conn,$refno) {
			$sql = "select * from h2h.ke_pembayaran where refno = ?";
			
			return $conn->GetRow($sql,array($refno));
		}
		
		function getPembayaranFormulirFromRefNo($conn,$refno) {
			$sql = "select * from h2h.ke_pembayaranfrm where refno = ?";
			
			return $conn->GetRow($sql,array($refno));
		}
		
		function getPembayaranAllFromRefNo($conn,$refno) {
			$row = self::getPembayaranFromRefNo($conn,$refno);
			if(empty($row)) {
				$row = self::getPembayaranFormulirFromRefNo($conn,$refno);
				if(!empty($row)) {
					// ambil nim (kode formulir)
					$row['nim'] = self::getKodeFormulirFromIDTarif($conn,$row['idtariffrm']);
				}
			}
			
			return $row;
		}
		
		function getJenisTagihanFromIDPembayaran($conn,$id) {
			// ambil salah satu
			/* $sql = "select t.jenistagihan from h2h.ke_tagihan t
					join h2h.ke_pembayarandetail d on t.idtagihan = d.idtagihan and d.idpembayaran = ?"; */
			$sql = "select j.kodekelompok from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan
					join h2h.ke_pembayarandetail d on t.idtagihan = d.idtagihan and d.idpembayaran = ?";
			
			return $conn->GetOne($sql,array($id));
		}
		
		function getNoToken($conn=false) {
			do {
				$notoken = strtoupper(Helper::randomString());
				
				// cek token
				if($conn) {
					$sql = "select 1 from h2h.ke_pembayaranfrm where notoken = ?";
					$cek = $conn->GetOne($sql,array($notoken));
				}
				else
					$cek = false;
			}
			while(!empty($cek));
			
			return $notoken;
		}
		
		function getNoKuitansi($conn,$bulan) {
			// diambil tahunnya saja
			$bulan = substr($bulan,0,4);
			$len = strlen($bulan);
			
			$sql = "select max(nokuitansi) from h2h.ke_pembayaran
					where substr(nokuitansi,1,".($len+1).") = ?";
			$no = $conn->GetOne($sql,array($bulan.'-'));
			
			if(!empty($no)) {
				$last = (int)substr($no,$len+1);
				$no = $last+1;
			}
			else
				$no = 1;
			
			return $bulan.'-'.str_pad($no,6,'0',STR_PAD_LEFT);
		}
		
		// deposit
		function getIDDepositFromNoVoucher($conn,$novo) {
			$sql = "select iddeposit from h2h.ke_deposit where novoucher = ?";
			
			return $conn->GetOne($sql,array($novo));
		}
		
		// action pembayaran
		function bayarFromRefNo($conn,$refno,$jumlah) {
			return self::bayarAllFromRefNo($conn,$refno,$jumlah,false);
		}
		
		function bayarFormulirFromRefNo($conn,$refno,$jumlah) {
			return self::bayarAllFromRefNo($conn,$refno,$jumlah,true);
		}
		
		function bayarAllFromRefNo($conn,$refno,$jumlah,$isfrm=false) {
			// tidak bisa menggunakan ? karena digunakan GetUpdateSQL
			if($isfrm)
				$sql = "select * from h2h.ke_pembayaranfrm where refno = ".Helper::escape($refno);
			else
				$sql = "select * from h2h.ke_pembayaran where refno = ".Helper::escape($refno);
			
			$rs = $conn->Execute($sql);
			
			if($rs->EOF)
				return 1; // tidak ada pembayaran
			else if(empty($rs->fields['flagbatal']))
				return 2; // sudah dibayar
			else if($jumlah < $rs->fields['jumlahbayar'])
				return 4; // uang kurang
			else if($jumlah > $rs->fields['jumlahbayar'])
				return 5; // uang lebih
			
			// cek tagihan
			if(empty($isfrm)) {
				$sql = "select t.flaglunas from h2h.ke_tagihan t
						join h2h.ke_pembayarandetail d on d.idtagihan = t.idtagihan
						where d.idpembayaran = ?";
				$rst = $conn->Execute($sql,array($rs->fields['idpembayaran']));
				
				if($rst->EOF)
					return 1; // tidak ada tagihan
				else {
					while($rowt = $rst->FetchRow()) {
						if($rowt['flaglunas'] == 'L')
							return 2; // sudah dibayar
					}
				}
			}
			
			$record = array();
			$record['flagbatal'] = 0;
			$record['flagrekon'] = 1;
			$record['trekontime'] = date('Y-m-d H:i:s');
			
			$record = Helper::addLog($record);
			
			$sql = $conn->GetUpdateSQL($rs,$record);
			$ok = $conn->Execute($sql);
			
			if($ok)
				return 0;
			else
				return 1; // error default
		}
		
		function insertPembayaran($conn,$record) {
			$record = Helper::addLog($record);
			
			$ok = $conn->AutoExecute('h2h.ke_pembayaran',$record,'INSERT');
			
			if($ok) {
				$id = $conn->GetOne("select last_value from h2h.ke_pembayaran_idpembayaran_seq");
				
				return $id;
			}
			else
				return false;
		}
		
		function updatePembayaran($conn,$record,$id) {
			$record = Helper::addLog($record);
			
			return $conn->AutoExecute('h2h.ke_pembayaran',$record,'UPDATE','idpembayaran = '.(int)$id);
		}
		
		function insertPembayaranDetail($conn,$record) {
			$record = Helper::addLog($record);
			
			return $conn->AutoExecute('h2h.ke_pembayarandetail',$record,'INSERT');
		}
		
		function insertPembayaranFormulir($conn,$record) {
			$record = Helper::addLog($record);
			
			$ok = $conn->AutoExecute('h2h.ke_pembayaranfrm',$record,'INSERT');
			
			if($ok) {
				$id = $conn->GetOne("select last_value from h2h.ke_pembayaranfrm_idpembayaranfrm_seq");
				
				return $id;
			}
			else
				return false;
		}
		
		function updatePembayaranFormulir($conn,$record,$id) {
			$record = Helper::addLog($record);
			
			return $conn->AutoExecute('h2h.ke_pembayaranfrm',$record,'UPDATE','idpembayaranfrm = '.(int)$id);
		}
	}
?>