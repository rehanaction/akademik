<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPembayaran extends mModel {
		const schema = 'h2h';
		const table = 'ke_pembayaran';
		const order = 'idpembayaran';
		const key = 'idpembayaran';
		const label = 'idpembayaran';
		
	function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periodebayar = '$key'";
			}
		}
		
	function listQuery() { 
			/*$sql = "select * from ".self::schema.".".self::table." p
				left JOIN akademik.ms_mahasiswa t ON p.nim=t.nim
				left JOIN gate.ms_unit u ON u.kodeunit = t.kodeunit
				";*/
			$sql = 	"select * from ((select 
					p.idpembayaran,
					p.tglbayar,
					p.jumlahbayar ,
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
					union ( 
					select 
					p.idpembayaran,
					p.tglbayar,
					p.jumlahbayar ,
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
					 t.nama,t.jalurpenerimaan,t.sistemkuliah,t.pilihanditerima as kodeunit,u.namaunit from 
					 h2h.ke_pembayaran p
					JOIN pendaftaran.pd_pendaftar t ON p.nopendaftar=t.nopendaftar 
					JOIN gate.ms_unit u ON u.kodeunit = t.pilihanditerima order by p.nopendaftar desc ))
					 p 
					";
			return $sql;
		}
		
			
	function idmaks($conn){
		$sql = "select coalesce(max(idpembayaran),0) from ".static::table()."";
		return $conn->getOne($sql);
	}
	
	function getDatapembayaran($conn,$id){
		
			$sql = "select b.* from ".static::table()." b where b.idpembayaran = '$id'";
		
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
		
	function laporanbytanggal($conn,$tglmulai,$tglakhir,$jenis,$void = '0',$infounit){
			if($tglmulai== '')
			$tglmulai = date('Y-m-d');
			if($tglakhir == '')
			$tglakhir = date('Y-m-d');
		
			$sql = "select * from (
					select b.*, 
					coalesce(b.nim,b.nopendaftar) as nim,
					coalesce(m.nama,p.nama) as nama,
					coalesce(m.jalurpenerimaan,p.jalurpenerimaan) as jalurpenerimaan,
					coalesce(m.sistemkuliah,p.sistemkuliah) as sistemkuliah,
					coalesce(m.kodeunit,p.pilihanditerima) as kodeunit,
					coalesce(um.namaunit,up.namaunit) as namaunit 
					from ".static::table()." b
					left JOIN akademik.ms_mahasiswa m ON b.nim=m.nim 
					left JOIN gate.ms_unit um ON um.kodeunit = m.kodeunit 
					left JOIN pendaftaran.pd_pendaftar p ON p.nopendaftar=b.nopendaftar 
					left JOIN gate.ms_unit up ON up.kodeunit = p.pilihanditerima
					) b
			 where (1=1) and b.tglbayar >= '$tglmulai' and b.tglbayar <= '$tglakhir'";
			 
			 if(is_array($jenis))
			 	$sql .= " and ish2h in ('".implode("','",$jenis)."')";
			 else
				if($jenis)
					$sql .= " and ish2h = '".$jenis."'";
				
				if($void<>'')
					$sql .= " and flagbatal = '$void'";
					
			if($infounit){
				$sql .= " and kodeunit in (
						select kodeunit from gate.ms_unit where infoleft >= '".$infounit['infoleft']."' and inforight <= '".$infounit['inforight']."'
				)";
			}
			
			$sql .= " order by tglbayar"; 
			$rs = $conn->getArray($sql);
			
			return $rs;
		}
		
		function laporanbybulan($conn,$tahun,$bulan,$jenis,$void = '0',$infounit){
			
			if($tahun== '')
			$tahun = date('Y');
			if($bulan == '')
			$bulan = date('m');
			
			$sql = "select * from (
					select b.*, 
					coalesce(b.nim,b.nopendaftar) as nim,
					coalesce(m.nama,p.nama) as nama,
					coalesce(m.jalurpenerimaan,p.jalurpenerimaan) as jalurpenerimaan,
					coalesce(m.sistemkuliah,p.sistemkuliah) as sistemkuliah,
					coalesce(m.kodeunit,p.pilihanditerima) as kodeunit,
					coalesce(um.namaunit,up.namaunit) as namaunit 
					from ".static::table()." b
					left JOIN akademik.ms_mahasiswa m ON b.nim=m.nim 
					left JOIN gate.ms_unit um ON um.kodeunit = m.kodeunit 
					left JOIN pendaftaran.pd_pendaftar p ON p.nopendaftar=b.nopendaftar 
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
				
				if($void<>'')
					$sql .= " and flagbatal = '$void'";
					
			if($infounit){
				$sql .= " and kodeunit in (
						select kodeunit from gate.ms_unit where infoleft >= '".$infounit['infoleft']."' and inforight <= '".$infounit['inforight']."'
				)";
			}
			
			$sql .= " order by tglbayar"; 
			$rs = $conn->getArray($sql);
			
			return $rs;
		}
	
	}
?>