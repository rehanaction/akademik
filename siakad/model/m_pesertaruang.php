<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mPesertaruang extends mModel {
		const schema = 'akademik';
		const table = 'v_jmlruang';
		const order = 'kodeunit,kodemk,thnkurikulum';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk';
		const label = 'peserta ruang';
		
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
		
		function ratakanPesertaPrak($conn,$key) {
			$conn->BeginTrans();
			
			$a_peserta = self::getDataPerKelas($conn,$key);
			$a_kelompok = self::getDataKelompok($conn,$key);
			
			$a_mhs = array();
			$a_kelas = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$a_kelas[] = $t_kelas;
				foreach($t_peserta as $row)
					$a_mhs[$row['nim']] = true;
			}
			
			ksort($a_mhs);
			
			$i = 1; $loop=0;
			$jml_mhs = count($a_mhs); //95
			$mod = $jml_mhs % $a_kelompok['kelpraktikum']; //kel. 7, mod 4
			$hasil_bagi = floor($jml_mhs/$a_kelompok['kelpraktikum']); //13
			if($mod == 0)
				$istambahan = false;
			else
				$istambahan = true;
			
			$a_peserta = array();
			// $n_kelas = count($a_kelas);
			$n_kelas = $a_kelompok['kelpraktikum'];
			foreach($a_mhs as $t_npm => $t_true) {
				$loop++;
				if($istambahan){
					if($loop % ($hasil_bagi+1) == 0){
						$a_peserta[$i][] = $t_npm;
						$i++;$loop=0;
					}else{
						$a_peserta[$i][] = $t_npm;
					}
				}else{
					if($loop % $hasil_bagi == 0){
						$a_peserta[$i][] = $t_npm;
						$i++;$loop=0;
					}else{
						$a_peserta[$i][] = $t_npm;
					}
				}
				// $t_kelas = $a_kelas[$i++];
				
				if($i > $mod)
					$istambahan = false;
				// if($i >= $n_kelas)
					// $i = 1;
			}
			
			// update kelas
			$ok = true;
			$record = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$record['kelompok_prak'] = $t_kelas;
				
				$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key)." and nim in ('".implode("','",$t_peserta)."')",true);
				if($err) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			$msg = 'Proses ratakan peserta praktikum '.($ok ? 'berhasil' : 'gagal').' diurutkan berdasarkan NIM';
			
			return array($err,$msg);
		}
		
		function ratakanPesertaPrakacak($conn,$key) {
			$conn->BeginTrans();
			
			$a_peserta = self::getDataPerKelas($conn,$key);
			$a_kelompok = self::getDataKelompok($conn,$key);
			
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
			// $n_kelas = count($a_kelas);
			$n_kelas = $a_kelompok['kelpraktikum'];
			foreach($a_mhs as $t_npm => $t_true) {
				$t_kelas = $a_kelas[$i++];
				$a_peserta[$i][] = $t_npm;
				
				if($i >= $n_kelas)
					$i = 0;
			}
			
			// update kelas
			$ok = true;
			$record = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$record['kelompok_prak'] = $t_kelas;
				
				$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key)." and nim in ('".implode("','",$t_peserta)."')",true);
				if($err) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			$msg = 'Proses ratakan peserta praktikum '.($ok ? 'berhasil' : 'gagal').'  diurutkan secara acak';
			
			return array($err,$msg);
		}
		
		function pindahRuang($conn,$key,$nimpindah,$keltujuan,$jenis) {
			$conn->BeginTrans();
			$ok = true;
			
			$record = array();
			if($jenis == 'praktikum')
				$record['kelompok_prak'] = $keltujuan;
			else if($jenis == 'tutorial')
				$record['kelompok_tutor'] = $keltujuan;
				
			$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key)." and nim='$nimpindah'",true);
			if($err) {
				$ok = false;
				break;
			}
			
			$conn->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			$msg = 'Proses pindah peserta '.($ok ? 'berhasil' : 'gagal');
			
			return array($err,$msg);
		}
		
		function ratakanPesertaTutor($conn,$key) {
			$conn->BeginTrans();
			
			$a_peserta = self::getDataPerKelas($conn,$key);
			$a_kelompok = self::getDataKelompok($conn,$key);
			
			$a_mhs = array();
			$a_kelas = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$a_kelas[] = $t_kelas;
				foreach($t_peserta as $row)
					$a_mhs[$row['nim']] = true;
			}
			
			ksort($a_mhs);
			
			$i = 1; $loop=0;
			$jml_mhs = count($a_mhs); //95
			$mod = $jml_mhs % $a_kelompok['keltutorial']; //kel. 7, mod 4
			$hasil_bagi = floor($jml_mhs/$a_kelompok['keltutorial']); //13
			if($mod == 0)
				$istambahan = false;
			else
				$istambahan = true;
			
			$a_peserta = array();
			// $n_kelas = count($a_kelas);
			$n_kelas = $a_kelompok['keltutorial'];
			foreach($a_mhs as $t_npm => $t_true) {
				$loop++;
				if($istambahan){
					if($loop % ($hasil_bagi+1) == 0){
						$a_peserta[$i][] = $t_npm;
						$i++;$loop=0;
					}else{
						$a_peserta[$i][] = $t_npm;
					}
				}else{
					if($loop % $hasil_bagi == 0){
						$a_peserta[$i][] = $t_npm;
						$i++;$loop=0;
					}else{
						$a_peserta[$i][] = $t_npm;
					}
				}
				// $t_kelas = $a_kelas[$i++];
				
				if($i > $mod)
					$istambahan = false;
				// if($i >= $n_kelas)
					// $i = 1;
			}
			
			// update kelas
			$ok = true;
			$record = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$record['kelompok_tutor'] = $t_kelas;
				
				$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key)." and nim in ('".implode("','",$t_peserta)."')",true);
				if($err) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			$msg = 'Proses ratakan peserta tutorial '.($ok ? 'berhasil' : 'gagal').' diurutkan berdasarkan NIM';
			
			return array($err,$msg);
		}
		
		function ratakanPesertaTutoracak($conn,$key) {
			$conn->BeginTrans();
			
			$a_peserta = self::getDataPerKelas($conn,$key);
			$a_kelompok = self::getDataKelompok($conn,$key);
			
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
			// $n_kelas = count($a_kelas);
			$n_kelas = $a_kelompok['keltutorial'];
			foreach($a_mhs as $t_npm => $t_true) {
				$t_kelas = $a_kelas[$i++];
				$a_peserta[$i][] = $t_npm;
				
				if($i >= $n_kelas)
					$i = 0;
			}
			
			// update kelas
			$ok = true;
			$record = array();
			foreach($a_peserta as $t_kelas => $t_peserta) {
				$record['kelompok_tutor'] = $t_kelas;
				
				$err = Query::recUpdate($conn,$record,static::table('ak_krs'),static::getCondition($key)." and nim in ('".implode("','",$t_peserta)."')",true);
				if($err) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			$msg = 'Proses ratakan peserta tutorial '.($ok ? 'berhasil' : 'gagal').' diurutkan secara acak';
			
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
			}
			
			$conn->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			$msg = 'Proses ratakan kelas '.($ok ? 'berhasil' : 'gagal');
			
			return array($err,$msg);
		}
		
		// mendapatkan peserta per kelas
		function getDataPerKelas($conn,$key) { echo $key;
			list($thnkurikulum, $kodemk, $kodeunit, $periode, $kelasmk)=explode('|', $key);
			$sql = "select k.kelompok_tutor, k.kelompok_prak, k.kelasmk, k.nim, m.nama from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					where k.thnkurikulum='$thnkurikulum' and k.kodeunit='$kodeunit'  and k.periode='$periode' and k.kelasmk='$kelasmk' and k.kodemk='$kodemk'
					order by k.kelasmk, k.nim";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['kelasmk']][] = array('nim' => $row['nim'], 'nama' => $row['nama'], 'kel_tutor' => $row['kelompok_tutor'], 'kel_prak' => $row['kelompok_prak']);
			
			return $data;
		}
		
		// mendapatkan peserta per ruangan
		function getDataRuang($conn,$key) {
			$sql = "select k.kelasmk, k.nim, m.nama from ".static::table('ak_krs')." k
					join ".static::table('ms_mahasiswa')." m on k.nim = m.nim
					where ".str_replace('kodeunit','k.kodeunit',static::getCondition($key))."
					order by k.kelasmk, k.nim";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['kelasmk']][] = array('nim' => $row['nim'], 'nama' => $row['nama']);
			
			return $data;
		}
		
		// mendapatkan peserta per kelompok
		function getDataKelompok($conn,$key) {
			$sql = " select coalesce(keltutorial, 1),coalesce(kelpraktikum, 1) from ".static::table('ak_kelas')."
					where ".str_replace('kodeunit','kodeunit',static::getCondition($key))."";
			$rs = $conn->GetRow($sql);
			
			return $rs;
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