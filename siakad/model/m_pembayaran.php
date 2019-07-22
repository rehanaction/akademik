<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPembayaran extends mModel {
		const schema = 'h2h';
		const table = 'ke_pembayaran';
		const order = 'idpembayaran desc';
		const key = 'idpembayaran';
		const label = 'idpembayaran';
		const sequence = 'ke_pembayaran_idpembayaran_seq';
		
	function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periodebayar = '$key'";
				case 'kodeunit': return "kodeunit = '$key'";
				case 'jalurpenerimaan': return "jalurpenerimaan = '$key'";
				case 'sistemkuliah': return "sistemkuliah = '$key'";
			}
		}
		
	function listQuery() {
			$ispmb = mAkademik::isRolePMB();
			
			/*$sql = "select * from ".self::schema.".".self::table." p
				left JOIN akademik.ms_mahasiswa t ON p.nim=t.nim
				left JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit
				";*/
			$sql = "select * from (";
			if(empty($ispmb)) {
				$sql .= "(select 
					p.idpembayaran,
					p.tglbayar,
					p.nip ,
					p.ish2h ,
					p.refno ,
					p.idcurrency ,
					p.flagrekon ,
					p.periodebayar ,
					p.companycode ,
					p.terminalid ,
					p.trekontime ,
					p.trxdatetime ,
					p.transmissiontime ,
					p.keterangan ,
					p.flagbatal,
					p.nim ,
					p.jumlahbayar ,
					t.nama,t.jalurpenerimaan,t.sistemkuliah,t.kodeunit,u.namaunit from h2h.ke_pembayaran p
					JOIN akademik.ms_mahasiswa t ON p.nim=t.nim 
					JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit order by p.nim desc) 
					union ";
			}
			$sql .= "(select 
					p.idpembayaran,
					p.tglbayar,
					p.nip ,
					p.ish2h ,
					p.refno ,
					p.idcurrency ,
					p.flagrekon ,
					p.periodebayar ,
					p.companycode ,
					p.terminalid ,
					p.trekontime ,
					p.trxdatetime ,
					p.transmissiontime ,
					p.keterangan ,
					p.flagbatal,
					p.nopendaftar as nim ,
					p.jumlahbayar ,
					t.nama,t.jalurpenerimaan,t.sistemkuliah,t.pilihanditerima as kodeunit,u.namaunit from h2h.ke_pembayaran p
					JOIN pendaftaran.pd_pendaftar t ON p.nopendaftar=t.nopendaftar 
					JOIN gate.ms_unit u ON u.kodeunit = t.pilihanditerima order by p.nopendaftar desc)) p";
			return $sql;
		}
		
			
	function idmaks($conn){
		$sql = "select coalesce(max(idpembayaran),0) from ".static::table()."";
		return $conn->getOne($sql);
	}
	
	function getDatapembayaran($conn,$id){
		
			$sql = "select b.*, a.bankname, z.userdesc as nama from ".static::table()." b
					left join ".static::table('ms_bank')." a on a.bankcode = b.companycode
					left join gate.sc_user z on b.nip = z.username
					where b.idpembayaran = '$id'";
		
		return $conn->getRow($sql);		
		}
	
	function getReversal($conn,$id){
			
			$sql = "update ".static::table()." set flagbatal ='1' where idpembayaran = '".$id."'";
			$conn->Execute($sql);
			
		return $conn->errorNo();
		}
		
	function cekRefno($conn,$refno){
			$sql = "select 1 from ".static::table()." where refno = '$refno'";
			$rs = $conn->GetOne($sql);
			if($rs)
				return false;
			else
				return true;
		}	
		
	function laporanbytanggal($conn,$tglmulai,$tglakhir,$jenis,$void = '0',$infounit,$sistem,$angkatan,$bank){
			if($tglmulai== '')
			$tglmulai = date('Y-m-d');
			if($tglakhir == '')
			$tglakhir = date('Y-m-d');
			
			$ispmb = mAkademik::isRolePMB();
		
			$sql = "select * from (
					select b.*, 
					coalesce(b.nim,b.nopendaftar) as nimpendaftar,
					coalesce(m.nama,p.nama) as nama,
					coalesce(m.jalurpenerimaan,p.jalurpenerimaan) as jalurpenerimaan,
					coalesce(m.sistemkuliah,p.sistemkuliah) as sistemkuliah,
					coalesce(m.kodeunit,p.pilihanditerima) as kodeunit,
					coalesce(um.namaunit,up.namaunit) as namaunit,
					substr(coalesce(m.periodemasuk,p.periodedaftar),1,4) as angkatan
					from ".static::table()." b
					".($ispmb ? '' : 'left ')."JOIN pendaftaran.pd_pendaftar p ON p.nopendaftar=b.nopendaftar 
					left JOIN akademik.ms_mahasiswa m ON b.nim=m.nim 
					left JOIN gate.ms_unit um ON um.kodeunit = m.kodeunit
					left JOIN gate.ms_unit up ON up.kodeunit = p.pilihanditerima
					) b
			 where (1=1) and to_char(tglbayar,'YYYY-MM-DD') between '$tglmulai' and '$tglakhir'";
			 
			 if(is_array($jenis))
			 	$sql .= " and ish2h in ('".implode("','",$jenis)."')";
			 else
				if($jenis)
					$sql .= " and ish2h = '".$jenis."'";
				
				if($void == '0')
					$sql .= " and (flagbatal = '$void' or flagbatal is null )";
					
				if($void <> '0')
					$sql .= " and (flagbatal = '$void' )";					
					
			if($infounit){
				$sql .= " and kodeunit in (
						select kodeunit from gate.ms_unit where infoleft >= '".$infounit['infoleft']."' and inforight <= '".$infounit['inforight']."'
				)";
			}
			
			if(!empty($sistem))
				$sql .= " and sistemkuliah = ".Query::escape($sistem);
			if(!empty($angkatan))
				$sql .= " and angkatan = ".Query::escape($angkatan);
			if(!empty($bank)) {
				$sql .= "and companycode = ".Query::escape($bank);
			}
			
			$sql .= " order by to_char(tglbayar,'YYYY-MM-DD'), nimpendaftar"; 
			$rs = $conn->getArray($sql);
			
			return $rs;
		}
		
		function laporanbybulan($conn,$tahun,$bulan,$jenis,$void = '0',$infounit){
			
			if($tahun== '')
			$tahun = date('Y');
			if($bulan == '')
			$bulan = date('m');
			
			$ispmb = mAkademik::isRolePMB();
			
			$sql = "select * from (
					select b.*, 
					coalesce(b.nim,b.nopendaftar) as nim,
					coalesce(m.nama,p.nama) as nama,
					coalesce(m.jalurpenerimaan,p.jalurpenerimaan) as jalurpenerimaan,
					coalesce(m.sistemkuliah,p.sistemkuliah) as sistemkuliah,
					coalesce(m.kodeunit,p.pilihanditerima) as kodeunit,
					coalesce(um.namaunit,up.namaunit) as namaunit 
					from ".static::table()." b
					".($ispmb ? '' : 'left ')."JOIN pendaftaran.pd_pendaftar p ON p.nopendaftar=b.nopendaftar 
					left JOIN akademik.ms_mahasiswa m ON b.nim=m.nim 
					left JOIN gate.ms_unit um ON um.kodeunit = m.kodeunit 
					left JOIN gate.ms_unit up ON up.kodeunit = p.pilihanditerima
					) b
			 where (1=1) 
			 			and to_char(tglbayar::timestamp with time zone, 'YYYY'::text) = '".$tahun."' 
			 			and to_char(tglbayar::timestamp with time zone, 'mm'::text) = '".str_pad($bulan,2,'0',STR_PAD_LEFT)."'";
			 
			
			 if(is_array($jenis))
			 	$sql .= " and ish2h in ('".implode("','",$jenis)."')";
			 else
				if($jenis)
					$sql .= " and ish2h = '".$jenis."'";
				
				if($void == '0')
					$sql .= " and (flagbatal = '$void' or flagbatal is null )";
					
				if($void <> '0')
					$sql .= " and (flagbatal = '$void' )";
				
			if($infounit){
				$sql .= " and kodeunit in (
						select kodeunit from gate.ms_unit where infoleft >= '".$infounit['infoleft']."' and inforight <= '".$infounit['inforight']."'
				)";
			}
			
			$sql .= " order by tglbayar"; 
			$rs = $conn->getArray($sql);
			
			return $rs;
		}
		
		function bayarBSM($conn,$nim,$jmlbayar,$tanggal=null,$notrans=null,$haruspas=true) {
			// cek pembayaran
			if(!empty($tanggal) and !empty($notrans)) {
				$sql = "select 1 from ".static::table()." where nim = ".Query::escape($nim)."
						and tglbayar = ".Query::escape($tanggal)." and notrans = ".Query::escape($notrans);
				$cek = $conn->GetOne($sql);
				
				if(!empty($cek))
					return array(-10001,'Sudah pernah dimasukkan');
			}
			
			if(empty($tanggal))
				$tanggal = date('Y-m-d');
			
			// cek deposit
			$sql = "select iddeposit, nominaldeposit-nominalpakai as nominal from ".static::table('ke_deposit')."
					where nim = ".Query::escape($nim)." and status = '-1' and nominaldeposit > nominalpakai
					and (tglexpired is null or tglexpired > current_date)
					order by case when tglexpired is null then 1 else 0 end, tglexpired, nominaldeposit-nominalpakai";
			$rs = $conn->Execute($sql);
			
			$kas = array('0' => $jmlbayar);
			while($row = $rs->FetchRow())
				$kas[strval($row['iddeposit'])] = (float)$row['nominal'];
			
			// ambil tagihan
			$sql = "select idtagihan, nominaltagihan, potongan, nominalbayar, denda from ".static::table('ke_tagihan')."
					where nim = ".Query::escape($nim)." and tgltagihan <= ".Query::escape($tanggal)." and flaglunas in ('BB','BL')
					order by periode, tgltagihan, nominaltagihan";
			$rs = $conn->Execute($sql);
			
			$kurang = 0;
			$bayar = array();
			while($row = $rs->FetchRow()) {
				$jmltagih = (float)$row['nominaltagihan']-(float)$row['potongan']+(float)$row['denda']-(float)$row['nominalbayar'];
				if($jmltagih <= 0)
					continue;
				
				$sisa = $jmltagih;
				foreach($kas as $id => $saldo) {
					if($saldo <= 0)
						continue;
					
					if($saldo < $sisa)
						$nominal = $saldo;
					else
						$nominal = $sisa;
					
					$kas[$id] -= $nominal;
					$bayar[$id][$row['idtagihan']] = $nominal;
					
					$sisa -= $nominal;
					if($sisa <= 0)
						break;
				}
				
				$kurang += $sisa;
			}
			
			if($haruspas and $kurang > 0) {
				$err = true;
				$msg = 'Kurang '.CStr::formatNumber($kurang);
				
				return array($err,$msg);
			}
			
			// masukkan pembayaran
			$jmlbayara = 0; // jumlah total, masuk ke jumlahuang
			$jmlbayarb = 0; // jumlah uang yang dibayar, masuk ke jumlahbayar
			foreach($bayar as $id => $bayarid) {
				foreach($bayarid as $jmlbayare) {
					$jmlbayara += $jmlbayare;
					if($id == '0')
						$jmlbayarb += $jmlbayare;
				}
			}
			
			$nobsm = static::getNoBSM($conn,substr($tanggal,0,4).substr($tanggal,5,2)); // format $tanggal yyyy-mm-dd
			
			$record = array();
			$record['tglbayar'] = $tanggal;
			$record['jumlahbayar'] = $jmlbayarb;
			$record['ish2h'] = '0';
			$record['periodebayar'] = Akademik::getPeriode();
			$record['nim'] = $nim;
			$record['jumlahuang'] = $jmlbayara;
			$record['nokuitansi'] = $nobsm;
			$record['notrans'] = (empty($notrans) ? $notrans : 'null');
			
			$err = Query::recInsert($conn,$record,static::table());
			
			// masukkan detailnya
			if(!$err) {
				$record = array();
				$record['idpembayaran'] = static::getLastValue($conn);
				
				$pakai = 0;
				foreach($bayar as $id => $bayarid) {
					foreach($bayarid as $idtagihan => $jmlbayare) {
						if($id != '0') {
							$pakai += $jmlbayare;
							$record['iddeposit'] = $id;
						}
						
						$record['idtagihan'] = $idtagihan;
						$record['nominalbayar'] = $jmlbayare;
						
						$err = Query::recInsert($conn,$record,static::table('ke_pembayarandetail'));
						if($err)
							break 2;
					}
				}
			}
			
			// sisa lebih masukkan deposit
			if(!$err and $kas['0'] > 0) {
				$record = array();
				$record['nim'] = $nim;
				$record['tgldeposit'] = date('Y-m-d H:i:s');
				$record['nominaldeposit'] = $kas['0'];
				$record['keterangan'] = 'Sisa '.$nobsm;
				$record['periode'] = Akademik::getPeriode();
				$record['status'] = -1;
				
				$err = Query::recInsert($conn,$record,static::table('ke_deposit'));
				if(!$err)
					$msg = 'Deposit '.CStr::formatNumber($kas['0']);
			}
			
			// set pesan
			if(empty($msg)) {
				if($kurang > 0)
					$msg = 'Kurang '.CStr::formatNumber($kurang);
				else if($pakai > 0)
					$msg = 'Pakai '.CStr::formatNumber($pakai);
			}
			
			return array($err,$msg);
		}
		
		function getNoBSM($conn,$bulan) {
			// diambil tahunnya saja
			$bulan = substr($bulan,0,4);
			$len = strlen($bulan);
			
			$sql = "select max(nokuitansi) from ".static::table()."
					where substr(nokuitansi,1,".($len+1).") = ".Query::escape($bulan.'-');
			$no = $conn->GetOne($sql);
			
			if(!empty($no)) {
				$last = (int)substr($no,$len+1);
				$no = $last+1;
			}
			else
				$no = 1;
			
			return $bulan.'-'.str_pad($no,6,'0',STR_PAD_LEFT);
		}
	}
?>
