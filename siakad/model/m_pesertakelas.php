<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mPesertakelas extends mModel {
		const schema = 'akademik';
		const table = 'v_jmlpeserta';
		const order = 'kodeunit,kodemk,thnkurikulum';
		const key = 'thnkurikulum,kodemk,kodeunit,periode';
		const label = 'peserta kelas';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select * from ".self::table();
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periode = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "infoleft >= ".(int)$row['infoleft']." and inforight <= ".(int)$row['inforight'];
			}
		}
		
		// meratakan peserta
		function ratakanPeserta($conn,$key) {
			$conn->BeginTrans();
			
			$a_peserta = self::getDataPerKelas($conn,$key);
			
			$a_mhs = array();
			$a_kelas = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$a_kelas[] = $t_kelas;
				foreach($t_peserta as $row)
					$a_mhs[$row['nim']] = true;
			}
			
			ksort($a_mhs);
			
			$i = 0;
			$a_peserta = array();
			$n_kelas = count($a_kelas);
			foreach($a_mhs as $t_npm => $t_true) {
				$t_kelas = $a_kelas[$i++];
				$a_peserta[$t_kelas][] = $t_npm;
				
				if($i >= $n_kelas)
					$i = 0;
			}
			
			// update kelas
			$ok = true;
			$record = array();
			$jmlpeserta = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$record['kelasmk'] = $t_kelas;
				
				$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key)." and nim in ('".implode("','",$t_peserta)."')",true);
				if($err) {
					$ok = false;
					break;
				}
				$jmlpeserta[$t_kelas] = count($t_peserta);
				//update jumlah peserta di ak_kelas
				// $rec = array();
				// $rec['jumlahpeserta'] = count($t_peserta);
				// $err2 = Query::recUpdate($conn,$rec,static::table('ak_kelas'),static::getCondition($key)." and kelasmk='$t_kelas'",true);
			}
			
			$conn->CommitTrans($ok);
			foreach($jmlpeserta as $kelas => $jmlpeserta){
				$rec = array();
				$rec['jumlahpeserta'] = $jmlpeserta;
				$err2 = Query::recUpdate($conn,$rec,static::table('ak_kelas'),static::getCondition($key)." and kelasmk='$kelas'",true);
			}
			
			$err = Query::isErr($ok);
			$msg = 'Proses ratakan peserta '.($ok ? 'berhasil' : 'gagal');
			
			return array($err,$msg);
		}
		
		// meratakan kelas
		function ratakanKelas($conn,$key) {
			$conn->BeginTrans();
			
			$sql = "select kelasmk, dayatampung from ".static::table('ak_kelas')."
					where ".static::getCondition($key)." order by kelasmk";
			$rs = $conn->Execute($sql);
			
			$a_kelas = array();
			while($row = $rs->FetchRow())
				$a_kelas[$row['kelasmk']] = $row['dayatampung'];
			
			$sql = "select nim from ".static::table('ak_krs')."
					where ".static::getCondition($key)." order by nim";
			$rs = $conn->Execute($sql);
			
			$a_mhs = array();
			while($row = $rs->FetchRow())
				$a_mhs[] = $row['nim'];
			
			$k = 0;
			$n_mhs = count($a_mhs);
			$n_kelas = count($a_kelas);
			$ok = true;
			$record = array();
			$jmlpeserta = array();
			foreach($a_kelas as $t_kelas => $t_kapasitas) {
				$record['kelasmk'] = $t_kelas;
				
				$a_peserta = array();
				for($i=0;$i<$t_kapasitas;$i++) {
					$t_npm = current($a_mhs);
					if($t_npm === false)
						break;
					
					$a_peserta[] = $t_npm;
					next($a_mhs);
				}
				
				// kelas terakhir, habiskan mahasiswa
				if(++$k >= $n_kelas) {
					while(($t_npm = current($a_mhs)) !== false) {
						$a_peserta[] = $t_npm;
						next($a_mhs);
					}
				}
				
				$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key)." and nim in ('".implode("','",$a_peserta)."')",true);
				if($err) {
					$ok = false;
					break;
				}
				
				$jmlpeserta[$t_kelas] = count($a_peserta);
				// //update jumlah peserta di ak_kelas
				// $rec = array();
				// $rec['jumlahpeserta'] = count($a_peserta);
				// $err2 = Query::recUpdate($conn,$rec,static::table('ak_kelas'),static::getCondition($key)." and kelasmk='$t_kelas'",true);
			}
			
			$conn->CommitTrans($ok);
			//update jumlah peserta di ak_kelas
			foreach($jmlpeserta as $kelas => $jmlpeserta){
				$rec = array();
				$rec['jumlahpeserta'] = $jmlpeserta;
				$err2 = Query::recUpdate($conn,$rec,static::table('ak_kelas'),static::getCondition($key)." and kelasmk='$kelas'",true);
			}
			
			$err = Query::isErr($ok);
			$msg = 'Proses ratakan kelas '.($ok ? 'berhasil' : 'gagal');
			
			return array($err,$msg);
		}
		
		// mendapatkan peserta per kelas
		function getDataPerKelas($conn,$key) {
		list($thnkurikulum, $kodemk, $kodeunit, $periode)= explode('|', $key);
			$sql = "select k.kelasmk, k.nim, m.nama from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					where k.thnkurikulum='$thnkurikulum' and k.kodeunit='$kodeunit'  and k.periode='$periode' and k.kelasmk='$kelasmk' and k.kodemk='$kodemk'
					order by k.kelasmk, k.nim";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['kelasmk']][] = array('nim' => $row['nim'], 'nama' => $row['nama']);
			
			return $data;
		}
		
		// mendapatkan info mk periode
		function getDataSingkat($conn,$key) {
			$row = static::getKeyRecord($key);
			
			$sql = "select namamk from ".static::table('ak_kurikulum')."
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit');
			$row['namamk'] = $conn->GetOne($sql);
			
			return $row;
		}
	}
?>