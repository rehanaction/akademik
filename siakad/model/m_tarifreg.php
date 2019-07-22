<?php
	// model tarif registrasi
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTarifReg extends mModel {
		const schema = 'h2h';
		const table = 'ke_tarifreg';
		const order = 'periodedaftar desc,kodeunit,jalurpenerimaan,idgelombang';
		const key = 'idtarifreg';
		const label = 'tarif registrasi';
		
		const jenisTagihan = 'SPPRG';
		
		// mendapatkan data
		
		function getArrayList($conn,$jalur,$periode,$gelombang=null,$unit=null,$sistemkuliah = null) {
			$sql = "select t.gelombang, t.kodeunit,
					count(t.idtarifreg) as angsuran, sum(t.nominaltarif) as total
					from ".static::table()." t
					where t.jalurpenerimaan = ".Query::escape($jalur)."
					and t.periodetarif = ".Query::escape($periode);
			if(!empty($gelombang))
				$sql .= " and t.gelombang = ".Query::escape($gelombang);
			if(!empty($kodeunit))
				$sql .= " and t.kodeunit = ".Query::escape($unit);
			if(!empty($sistemkuliah))
				$sql .= " and t.sistemkuliah = ".Query::escape($sistemkuliah);
			$sql .= " group by t.gelombang, t.kodeunit";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['kodeunit']][$row['gelombang']] = array('angsuran' => (int)$row['angsuran'], 'total' => (float)$row['total']);
			
			return $data;
		}
		
		function getArrayAngsuran($conn,$jalur,$periode,$gelombang,$unit,$sistemkuliah,$format=false) {
			$sql = "select t.angsuranke, t.nominaltarif, t.tgldeadline from ".static::table()." t
					where t.jalurpenerimaan = ".Query::escape($jalur)."
					and t.periodetarif = ".Query::escape($periode)."
					and t.gelombang = ".Query::escape($gelombang)."
					and t.kodeunit = ".Query::escape($unit)."
					and t.sistemkuliah = ".Query::escape($sistemkuliah)."
					order by t.angsuranke";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(empty($data[$row['angsuranke']]))
					$jmldata = 0;
				else
					$jmldata = $data[$row['angsuranke']]['jumlahdata'];
				
				$tdata = array();
				$tdata['nominaltarif'] = (float)$row['nominaltarif'];
				$tdata['tgldeadline'] = $row['tgldeadline'];
				$tdata['jumlahdata'] = $jmldata+1;
				
				if($format) {
					$tdata['nominaltarif'] = CStr::formatNumber($row['nominaltarif']);
					$tdata['tgldeadline'] = CStr::formatDate($row['tgldeadline']);
				}
				
				$data[$row['angsuranke']] = $tdata;
			}
			
			return $data;
		}
		
		function getArraytarif($conn,$periode='',$jalur='',$kodeunit='',$gelombang='',$sistemkuliah='') {
			$sql = "select t.* from ".static::table()." t
					join gate.ms_unit u on t.kodeunit=u.kodeunit
					where (1=1)";
			
			if($periode <> '')
				$sql .= " and t.periodetarif = '$periode'";
			if($jalur<>'') // sebelumnya dikomen
				$sql .= " and t.jalurpenerimaan = '$jalur'";
			if($kodeunit <> ''){
				$unit=$conn->GetRow("select infoleft,inforight from gate.ms_unit where kodeunit='$kodeunit'");
				$sql .= " and (u.infoleft >= '".$unit['infoleft']."' and u.inforight <= '".$unit['inforight']."')";
			}
			if($gelombang <> '')
				$sql .= " and t.gelombang = '$gelombang'";
			if($sistemkuliah <> '')
				$sql .= " and t.sistemkuliah = '$sistemkuliah'";
		
			return $conn->GetArray($sql);
		}
		
		function salinTarif($conn,$jalur,$periode,$jalursalin,$periodesalin){
			$ok = $conn->Execute("delete from ".static::table()." where jalurpenerimaan = ".Query::escape($jalursalin)." and periodetarif=".Query::escape($periodesalin));
			
			$sql = "insert into ".static::table()." (periodetarif, jalurpenerimaan, gelombang, kodeunit,sistemkuliah, angsuranke, nominaltarif, tgldeadline, t_updateuser, t_updatetime, t_updateip) 
					select ".Query::escape($periodesalin).", ".Query::escape($jalursalin).", gelombang, kodeunit,sistemkuliah,angsuranke, nominaltarif, tgldeadline,
					'".Modul::getUserName()."', '".date('Y-m-d H:i:s')."', '".$_SERVER['REMOTE_ADDR']."'
					from ".static::table()."
					where periodetarif = ".Query::escape($periode)." and jalurpenerimaan = ".Query::escape($jalur);
			$ok = $conn->Execute($sql);
			
			if($ok)
				return array(false,'Salin Tarif Berhasil');
			else
				return array(true,'Salin Tarif Gagal');
		}
		
		// aksi
		
		function deleteAngsuran($conn,$jalur,$periode,$gelombang,$unit,$sistemkuliah,$angsuran) {
			$sql = "jalurpenerimaan = ".Query::escape($jalur)."
					and periodetarif = ".Query::escape($periode)."
					and gelombang = ".Query::escape($gelombang)."
					and kodeunit = ".Query::escape($unit)."
					and sistemkuliah = ".Query::escape($sistemkuliah)."
					and angsuranke = ".Query::escape($angsuran);
			
			return Query::qDelete($conn,static::table(),$sql);
		}
		
		function insertAngsuran($conn,$jalur,$periode,$gelombang,$unit,$sistemkuliah,$angsuran,$record) {
			$record['jalurpenerimaan'] = $jalur;
			$record['periodetarif'] = $periode;
			$record['gelombang'] = $gelombang;
			$record['kodeunit'] = $unit;
			$record['sistemkuliah'] = $sistemkuliah;
			$record['angsuranke'] = $angsuran;
			
			return Query::recInsert($conn,$record,static::table());
		}
		
		function updateAngsuran($conn,$jalur,$periode,$gelombang,$unit,$sistemkuliah, $angsuran,$record) {
			$sql = "jalurpenerimaan = ".Query::escape($jalur)."
					and periodetarif = ".Query::escape($periode)."
					and gelombang = ".Query::escape($gelombang)."
					and kodeunit = ".Query::escape($unit)."
					and sistemkuliah = ".Query::escape($sistemkuliah)."
					and angsuranke = ".Query::escape($angsuran);
			
			return Query::recUpdate($conn,$record,static::table(),$sql);
		}
	}
?>
