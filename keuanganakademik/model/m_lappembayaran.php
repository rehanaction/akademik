<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mLapPembayaran {
		// master
		function getListMaster($conn,$periode,$sistem,$unit,$angkatan=null,$statusmhs=null,$bank=null,$tgltagihan=null,$beasiswa=false) {
			$eperiode = Query::escape($periode);
			$esistem = Query::escape($sistem);
			$eunit = Query::escape($unit);
			$eangkatan = Query::escape($angkatan);
			$estatusmhs = Query::escape($statusmhs);
			$ebank = Query::escape($bank);
			
			// ambil mahasiswa
			$sqlm = "select m.nim, m.nama, m.kodeunit, m.keteranganbeasiswa, m.potongan,
					m.periodemasuk, u.kodeunitparent from akademik.ms_mahasiswa m
					join gate.ms_unit u on u.kodeunit = m.kodeunit
					join gate.ms_unit p on u.infoleft >= p.infoleft and u.inforight <= p.inforight and p.kodeunit = $eunit
					join akademik.ak_perwalian w on w.nim = m.nim and w.periode = $eperiode";
			if($beasiswa)
				$sqlm .= " and m.potongan > 0 and ((m.potsmtawal is null or m.potsmtawal <= w.semmhs)
							and (m.potsmtakhir is null or m.potsmtakhir >= w.semmhs))";
			if(!empty($sistem))
				$sqlm .= " and m.sistemkuliah = $esistem";
			if(!empty($angkatan)) {
				$len = strlen($angkatan);
				$sqlm .= " and substr(m.periodemasuk,1,$len) = $eangkatan";
			}
			if(!empty($statusmhs))
				$sqlm .= " and w.statusmhs = $estatusmhs";
			
			// ambil tagihan
			$sql = "select t.nim, t.periode, t.idtagihan, t.nominaltagihan, t.potongan, t.denda, e.iddeposit, d.nominalbayar,
					to_char(b.tglbayar,'YYYYMMDDHH24MISS') as tglbayar, n.bankname
					from h2h.ke_tagihan t
					join h2h.lv_jenistagihan j on t.jenistagihan = j.jenistagihan and j.kodekelompok in ('02','03')
					join ($sqlm) m on t.nim = m.nim
					left join (h2h.ke_pembayarandetail d join h2h.ke_pembayaran b on d.idpembayaran = b.idpembayaran and b.flagbatal = '0'";
			if(!empty($bank)) {
				if($bank == '000')
					$sql .= " and b.ish2h = '0'";
				else
					$sql .= " and b.ish2h = '1' and b.companycode = $ebank";
			}
			$sql .= ") on t.idtagihan = d.idtagihan
					left join h2h.ke_deposit e on d.iddeposit = e.iddeposit and e.jenisdeposit = 'V'
					left join h2h.ms_bank n on b.companycode = n.bankcode
					where (t.periode = $eperiode or (t.periode < $eperiode and (d.idtagihan is null or b.periodebayar >= $eperiode)))";
			if(!empty($tgltagihan))
				$sql .= " and t.tgltagihan <= to_date(".Query::escape($tgltagihan).",'YYYY-MM-DD')";
			$sql .= " order by t.idtagihan";
			$rs = $conn->Execute($sql);
			
			$a_bank = array();
			$a_tglbayar = array();
			$a_tagihan = array();
			$a_deposit = array();
			while($row = $rs->FetchRow()) {
				$t_nim = $row['nim'];
				
				// cek bank
				if(!empty($row['bankname'])) {
					if(empty($a_bank[$t_nim]) or $row['tglbayar'] < $a_tglbayar[$t_nim]) {
						$a_bank[$t_nim] = $row['bankname'];
						$a_tglbayar[$t_nim] = $row['tglbayar'];
					}
				}
				
				if(!isset($a_tagihan[$t_nim]))
					$a_tagihan[$t_nim] = array('tagihan' => 0, 'potongan' => 0, 'bayar' => 0, 'ambil' => 0);
				
				$bayar = (float)$row['nominalbayar'];
				if(empty($row['iddeposit']))
					$a_tagihan[$t_nim]['bayar'] += $bayar;
				else
					$a_tagihan[$t_nim]['potongan'] += $bayar;
				
				if($rs->EOF or strcmp($rs->fields['idtagihan'],$row['idtagihan']) != 0) {
					$a_tagihan[$t_nim]['tagihan'] += ($row['nominaltagihan']+$row['denda']);
					$a_tagihan[$t_nim]['potongan'] += $row['potongan'];
				}
			}
			
			// ambil voucher yang masih bisa atau sudah dipakai
			$sql = "select d.nim, d.iddeposit, d.jenisdeposit, d.periode, d.nominaldeposit-d.nominalpakai as nominaldeposit, d.status
					from h2h.ke_deposit d
					join ($sqlm) m on d.nim = m.nim
					and d.nominaldeposit > d.nominalpakai and d.tglexpired > current_date";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				if(!isset($a_tagihan[$row['nim']]))
					$a_tagihan[$row['nim']] = array('tagihan' => 0, 'potongan' => 0, 'bayar' => 0, 'ambil' => 0);
				
				if(empty($row['status'])) {
					if($row['periode'] == $periode)
						$a_tagihan[$row['nim']]['ambil'] += $row['nominaldeposit'];
				}
				else if($row['jenisdeposit'] == 'V')
					$a_tagihan[$row['nim']]['potongan'] += $row['nominaldeposit'];
				else
					$a_tagihan[$row['nim']]['deposit'] += $row['nominaldeposit'];
			}
			
			// ambil mahasiswa
			$sql = $sqlm." order by m.nim";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(!isset($a_tagihan[$row['nim']]))
					continue;
				
				// ambil bank
				if(empty($bank))
					$row['bankname'] = $a_bank[$row['nim']];
				else
					$row['bankname'] = $namabank;
				
				$data[] = array_merge($row,$a_tagihan[$row['nim']]);
			}
			
			return $data;
		}
		
		// rekap
		function getListRekap($conn,$periode,$sistem,$unit,$angkatan=null,$statusmhs=null,$bank=null,$tgltagihan=null) {
			$data = self::getListMaster($conn,$periode,$sistem,$unit,$angkatan,$statusmhs,$bank,$tgltagihan);
			
			$a_tagihan = array();
			foreach($data as $row) {
				if(!isset($a_tagihan[$row['kodeunitparent']]))
					$a_tagihan[$row['kodeunitparent']] = array('mhs' => 0, 'tagihan' => 0, 'potongan' => 0, 'pembayaran' => 0, 'hutang' => 0, 'deposit' => 0);
				
				$a_tagihan[$row['kodeunitparent']]['mhs']++;
				$a_tagihan[$row['kodeunitparent']]['tagihan'] += $row['tagihan'];
				$a_tagihan[$row['kodeunitparent']]['potongan'] += $row['potongan'];
				$a_tagihan[$row['kodeunitparent']]['pembayaran'] += $row['bayar'];
				
				$t_saldo = $row['tagihan']-$row['potongan']-$row['bayar'];
				if($t_saldo < 0)
					$a_tagihan[$row['kodeunitparent']]['deposit'] += $t_saldo;
				else
					$a_tagihan[$row['kodeunitparent']]['hutang'] += $t_saldo;
			}
			
			$sql = "select kodeunit, namaunit from gate.ms_unit where level = 1 order by kodeunit";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(!isset($a_tagihan[$row['kodeunit']]))
					continue;
				
				$data[] = array_merge($row,$a_tagihan[$row['kodeunit']]);
			}
			
			return $data;
		}
		
		// lunas
		function getListLunas($conn,$periode,$sistem,$unit,$angkatan=null,$statusmhs=null,$bank=null,$tgltagihan=null) {
			$data = self::getListMaster($conn,$periode,$sistem,$unit,$angkatan,$statusmhs,$bank,$tgltagihan);
			
			$a_tagihan = array();
			foreach($data as $row) {
				if(!isset($a_tagihan[$row['kodeunitparent']]))
					$a_tagihan[$row['kodeunitparent']] = array('lunas' => 0, 'total' => 0);
				
				$a_tagihan[$row['kodeunitparent']]['total']++;
				
				$t_saldo = $row['tagihan']-$row['potongan']-$row['bayar'];
				if($t_saldo <= 0)
					$a_tagihan[$row['kodeunitparent']]['lunas']++;
			}
			
			$sql = "select kodeunit, namaunit from gate.ms_unit where level = 1 order by kodeunit";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(!isset($a_tagihan[$row['kodeunit']]))
					continue;
				
				$data[] = array_merge($row,$a_tagihan[$row['kodeunit']]);
			}
			
			return $data;
		}
		
		// beasiswa
		function getListBeasiswa($conn,$periode,$sistem,$unit,$angkatan,$statusmhs) {
			// return self::getListMaster($conn,$periode,$sistem,$unit,$angkatan,$statusmhs,null,null,true);
			
			// ambil data
			$len = strlen($angkatan);
			
			$sql = "select m.nim, m.nama, m.kodeunit, m.keteranganbeasiswa, m.potongan
					from akademik.ms_mahasiswa m
					join akademik.ak_perwalian w on w.nim = m.nim and w.periode = ".Query::escape($periode)." and w.statusmhs = ".Query::escape($statusmhs)."
					join gate.ms_unit u on u.kodeunit = m.kodeunit
					join gate.ms_unit p on u.infoleft >= p.infoleft and u.inforight <= p.inforight and p.kodeunit = ".Query::escape($unit)."
					where m.potongan > 0 and ((m.potsmtawal is null or m.potsmtawal <= w.semmhs) and (m.potsmtakhir is null or m.potsmtakhir >= w.semmhs))".
					(empty($sistem) ? '' : " and m.sistemkuliah = ".Query::escape($sistem)).
					(empty($angkatan) ? '' : " and substr(m.periodemasuk,1,$len) = ".Query::escape($angkatan)).
					" and m.potongan > 0 and ((m.potsmtawal is null or m.potsmtawal <= w.semmhs) and (m.potsmtakhir is null or m.potsmtakhir >= w.semmhs))
					order by m.nim";
			
			return $conn->getArray($sql);
		}
	}
?>
