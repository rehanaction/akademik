<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPembayaranfrm extends mModel {
		const schema = 'h2h';
		const table = 'ke_pembayaranfrm';
		const order = 'idpembayaranfrm';
		const key = 'idpembayaranfrm';
		const label = 'idpembayaranfrm';
		
	function listQuery() { 
			$sql = "select b.*,t.*,g.namagelombang,s.namasistem||' '||s.tipeprogram as namasistem,
			b.keterangan as catatan from ".static::table()." b
					left join h2h.ke_tariffrm t on t.idtariffrm = b.idtariffrm
					left join pendaftaran.lv_gelombang g on g.idgelombang = t.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = t.sistemkuliah";
			return $sql;
		}
		
	
	function cekRefno($conn,$refno){
			$sql = "select 1 from ".static::table()." where refno = '$refno'";
			$rs = $conn->GetOne($sql);
			if($rs)
				return false;
			else
				return true;
		}	
		
	function cekToken($conn,$token){
			$sql = "select 1 from ".static::table()." where notoken = '$token'";
			$rs = $conn->GetOne($sql);
			if($rs)
				return false;
			else
				return true;
		}	
		
	function getDatabytoken($conn,$token){
			$sql = "select b.*,t.*,g.namagelombang,s.namasistem,b.keterangan as catatan from ".static::table()." b
					left join h2h.ke_tariffrm t on t.idtariffrm = b.idtariffrm
					left join pendaftaran.lv_gelombang g on g.idgelombang = t.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = t.sistemkuliah
			 where notoken = '$token'";
			$rs = $conn->getRow($sql);
			return $rs;
		}
	
	function reversalPayment($conn,$token){
			$sql = "update ".static::table()." set flagbatal = '1' where notoken = '$token'";
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
			
		}
		
	function laporanbytanggal($conn,$tglmulai,$tglakhir,$jenis,$void = '0',$periode){
			if($tglmulai== '')
			$tglmulai = date('Y-m-d');
			if($tglakhir == '')
			$tglakhir = date('Y-m-d');
			
			$sql = "select b.*,t.*,g.namagelombang,s.namasistem,b.keterangan as catatan from ".static::table()." b
					left join h2h.ke_tariffrm t on t.idtariffrm = b.idtariffrm
					left join pendaftaran.lv_gelombang g on g.idgelombang = t.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = t.sistemkuliah
			 where (1=1) and b.tglbayar >= '$tglmulai' and b.tglbayar <= '$tglakhir'";
			 
			 if($periode <>'')
			 	$sql .= " and t.periodedaftar = '$periode'";
			 if(is_array($jenis))
			 	$sql .= " and b.ish2h in ('".implode("','",$jenis)."')";
			 else
				if($jenis)
					$sql .= " and b.ish2h = '".$jenis."'";
				
				if($void<>''){
					if($void=='0')
						$sql .= " and b.flagbatal = '$void' or b.flagbatal is null";
					else
						$sql .= " and b.flagbatal = '$void'";
				}
			
			$sql .= " order by b.tglbayar"; 
			$rs = $conn->getArray($sql);
			
			return $rs;
		}
		
		function laporanbybulan($conn,$tahun,$bulan,$jenis,$void = '0',$periode){
			if($tahun== '')
			$tahun = date('Y');
			if($bulan == '')
			$bulan = date('m');
			
			$sql = "select b.*,t.*,g.namagelombang,s.namasistem,b.keterangan as catatan from ".static::table()." b
					left join h2h.ke_tariffrm t on t.idtariffrm = b.idtariffrm
					left join pendaftaran.lv_gelombang g on g.idgelombang = t.idgelombang
					left join akademik.ak_sistem s on s.sistemkuliah = t.sistemkuliah
			 where (1=1) 
			 			and to_char(tglbayar::timestamp with time zone, 'YYYY'::text) = '".$tahun."' 
			 			and to_char(tglbayar::timestamp with time zone, 'mm'::text) = '".str_pad($bulan,2,'0',STR_PAD_LEFT)."'";
			 
			 if($periode <>'')
			 	$sql .= " and periodedaftar = '$periode'";
			 if(is_array($jenis))
			 	$sql .= " and ish2h in ('".implode("','",$jenis)."')";
			 else
				if($jenis)
					$sql .= " and ish2h = '".$jenis."'";
				
				if($void<>'')
					$sql .= " and flagbatal = '$void'";
			
			$sql .= " order by tglbayar"; 
			$rs = $conn->getArray($sql);
			
			return $rs;
		}
	}
?>
