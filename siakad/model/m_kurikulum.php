<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKurikulum extends mModel {
		const schema = 'akademik';
		const table = 'ak_kurikulum';
		const order = 'semmk,kodemk';
		const key = 'thnkurikulum,kodemk,kodeunit';
		const label = 'kurikulum';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'thnkurikulum': return "thnkurikulum = '$key'";
				case 'unit': return "kodeunit = '$key'";
				case 'semester': return "semmk = ".(int)$key;
				case 'paket': return "paket = ".(int)$key;
			}
		}
		
		// salin kurikulum
		function copy($conn,$kodeunit,$kurasal,$kurtujuan) {
			$ok = true;
			$conn->BeginTrans();
			
			// masukkan mata kuliah
			$sql = "insert into ".static::table('ak_matakuliah')." (thnkurikulum,kodemk,namamk,sks,nilaimin,kodejenis,tipekuliah)
					select '$kurtujuan'::numeric,m.kodemk,m.namamk,m.sks,m.nilaimin,m.kodejenis,m.tipekuliah from ".static::table('ak_matakuliah')." m
					join ".static::table()." k on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk
					and k.kodeunit = '$kodeunit' and k.thnkurikulum = '$kurasal'
					left join ".static::table('ak_matakuliah')." mn on m.kodemk = mn.kodemk and mn.thnkurikulum = '$kurtujuan'
					where mn.kodemk is null";
			$ok = $conn->Execute($sql);
			
			// masukkan kurikulum
			if($ok) {
				$sql = "insert into ".static::table()." (kodeunit,thnkurikulum,kodemk,semmk,wajibpilihan)
						select k.kodeunit,'$kurtujuan'::numeric,k.kodemk,k.semmk,k.wajibpilihan from ".static::table()." k
						left join ".static::table()." kn on k.kodemk = kn.kodemk and k.kodeunit = kn.kodeunit and kn.thnkurikulum = '$kurtujuan'
						where kn.kodemk is null and k.kodeunit = '$kodeunit' and k.thnkurikulum = '$kurasal'";
				$ok = $conn->Execute($sql);
			}
			
			$err = $conn->ErrorNo();
			$conn->CommitTrans($ok);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
		// ambil data paket unit
		function getListUnitPaket($conn,$periode,$kurikulum,$angkatan) {
			$a_lrunit = Modul::getLeftRight();
			
			// krs dulu
			$sql = "select c.kodeunit, k.semmk, count(c.thnkurikulum) as jumlahkrs
					from akademik.ak_krs c
					join akademik.ak_kurikulum k on c.thnkurikulum = k.thnkurikulum and c.kodemk = k.kodemk and c.kodeunit = k.kodeunit
						and k.thnkurikulum = '$kurikulum' and k.paket = 1 and /*k.semmk%2 = ".(substr($periode,-1) == '1' ? 1 : 0)."*/
						c.periode='$periode'
					join akademik.ms_mahasiswa m on c.nim = m.nim and substring(m.periodemasuk,1,4)::int = $angkatan
					where c.periode = '$periode'
					group by c.kodeunit, k.semmk";
			$rs = $conn->Execute($sql);
			
			$a_datakrs = array();
			while($row = $rs->FetchRow())
				$a_datakrs[$row['kodeunit']][$row['semmk']] = $row['jumlahkrs'];
			
			// baru kurikulum
			$sql = "select u.kodeunit, u.namaunit, p.namaunit as fakultas, k.semmk, k.kodemk, count(c.thnkurikulum) as jumlahkelas
					from akademik.ak_kurikulum k
					join gate.ms_unit u on k.kodeunit = u.kodeunit and u.infoleft >= '".$a_lrunit['LEFT']."' and u.inforight <= '".$a_lrunit['RIGHT']."'
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join akademik.ak_kelas c on c.thnkurikulum = k.thnkurikulum and c.kodemk = k.kodemk and c.kodeunit = k.kodeunit and c.periode = '$periode'
					where k.thnkurikulum = '$kurikulum' and k.paket = 1 and /*k.semmk%2 = ".(substr($periode,-1) == '1' ? 1 : 0)."*/ c.periode='$periode'
					group by u.kodeunit, u.infoleft, u.namaunit, p.namaunit, k.semmk, k.kodemk
					order by u.infoleft, k.semmk";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$t_data = array('jumlahmk' => 0, 'jumlahkelas' => 0);
			
			while($row = $rs->FetchRow()) {
				
				$t_data['jumlahmk']++;
				if(!empty($row['jumlahkelas']))
					$t_data['jumlahkelas']++;
				
				if($rs->EOF or !($rs->fields['kodeunit'] == $row['kodeunit'] and $rs->fields['semmk'] == $row['semmk'])) {
					
					$t_data['semmk'] = $row['semmk'];
					$t_data['kodeunit'] = $row['kodeunit'];
					$t_data['namaunit'] = $row['namaunit'];
					$t_data['fakultas'] = $row['fakultas'];
					//$t_data['angkatan'] = substr($periode,0,4)-floor($t_data['semmk']/2);
					$t_data['angkatan'] = $angkatan;
					$t_data['adakrs'] = ((empty($a_datakrs[$t_data['kodeunit']][$t_data['semmk']])) ? false : true);
					
					$a_data[] = $t_data;
					
					$t_data = array('jumlahmk' => 0, 'jumlahkelas' => 0);
				}
			}
			
			return $a_data;
		}
		
		// ambil data mata kuliah paket
		function getListMKPaket($conn,$periode,$kurikulum,$kodeunit,$semmk) {
			
			$sql = "select k.kodemk, k.namamk, k.sks, string_agg(c.kelasmk,', ') as kelas from ".static::table()." k
					left join akademik.ak_kelas c on c.thnkurikulum = k.thnkurikulum and c.kodemk = k.kodemk and c.kodeunit = k.kodeunit and c.periode = '$periode'
					where k.thnkurikulum = '$kurikulum' and k.kodeunit = '$kodeunit' and k.semmk = '$semmk'
					and k.paket = 1 group by k.kodemk, k.namamk, k.sks order by k.namamk";
			
			return $conn->GetArray($sql);
		}
		/*
		// ambil paket dengan pemotongan nim
		function setPaket($conn,$kurikulum,$kodeunit,$semmk,$angkatan,$kodejur='',$cmulai='',$csampai='',$kelas='A') {
			require_once(Route::getModelPath('perwalian'));
			require_once(Route::getModelPath('krs'));
			
			$conn->BeginTrans();
			
			// periode sekarang
			$periode = Akademik::getPeriode();
			$periodedaftar = $angkatan.'1';
			
			$a_kodemk = self::getDataPaket($conn,$kurikulum,$kodeunit,$semmk);
			$a_mhs = mPerwalian::getDataSudahBayar($conn,$periode,$kodeunit,$periodedaftar);
			
			// cek pola mahasiswa
			if(!(empty($kodejur) and empty($cmulai) and empty($csampai))) {
				$cmulai = (int)$cmulai;
				$csampai = (int)$csampai;
				$n_depan = strlen($kodejur);
				
				foreach($a_mhs as $k => $row) {
					$t_npm = trim($row['nim']);
					$t_ctr = (int)substr($t_npm,-3);
					
					if(!(substr($t_npm,0,$n_depan) == $kodejur and $t_ctr >= $cmulai and $t_ctr <= $csampai))
						unset($a_mhs[$k]);
				}
			}
			
			// cek mahasiswa
			$a_cek = self::getKRSMahasiswa($conn,$kurikulum,$kodeunit,$periode,$a_kodemk,$a_mhs);
			
			if(empty($a_cek)) {
				$err = 0;
				
				$record = array();
				$record['periode'] = $periode;
				$record['kodeunit'] = $kodeunit;
				$record['thnkurikulum'] = $kurikulum;
				$record['kelasmk'] = $kelas;
				
				foreach($a_mhs as $rowm) {
					$record['nim'] = $rowm['nim'];
					
					foreach($a_kodemk as $rowk) {
						$record['kodemk'] = $rowk['kodemk'];
						
						$err = mKRS::insertRecord($conn,$record);
						if($err) {
							$ok = false;
							break;
						}
					}
					
					if($err)
						$ok = false;
				}
			}
			else {
				$err = -1;
				$msg = 'Mahasiswa '.$a_cek[0]['nim'].' sudah mengambil '.$a_cek[0]['kodemk'].', harap dihapus sebelum mengambil paket';
			}
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			if($err) {
				if(empty($msg))
					$msg = 'Pengambilan paket mahasiswa gagal';
			}
			else {
				$n_mhs = count($a_mhs);
				$n_kelas = count($a_kodemk);
				
				$msg = $n_mhs.' mahasiswa angkatan '.$angkatan.' diambilkan '.$n_kelas.' kelas';
			}
			
			return array($err,$msg);
		}*/
		
		// ambil paket dengan satu nim
		function setPaket($conn,$kurikulum,$periode,$kodeunit,$semmk,$angkatan,$nim,$kelas,$sistemkuliah) {
			require_once(Route::getModelPath('perwalian'));
			require_once(Route::getModelPath('krs'));
			require_once(Route::getModelPath('kelas'));
			require_once(Route::getModelPath('sistemkuliah'));
			$namasistem=mSistemkuliah::getArray($conn);
			$conn->BeginTrans();
			
			// periode sekarang
			//$periode = Akademik::getPeriode();
			$periodedaftar = $angkatan.'1';
			
			$a_kodemk = self::getDataPaket($conn,$kurikulum,$kodeunit,$semmk);
			if(substr($periode,4,1)!='0')
				$a_mhs = mPerwalian::getDataSudahBayar($conn,$periode,$kodeunit,$periodedaftar,$nim);
			else
				$a_mhs[0]['nim']=$nim;
			
			
			// cek basis/sistemkuliah mahasiswa
			$a_mhs[0]['nim']=$conn->GetOne("select nim from akademik.ms_mahasiswa where nim='$nim' and sistemkuliah='$sistemkuliah'");
			if(!empty($a_mhs[0]['nim'])){
				$a_cek = self::getKRSMahasiswa($conn,$kurikulum,$kodeunit,$periode,$a_kodemk,$a_mhs);
				//print_r($a_mhs);die();
				if(empty($a_cek)) {
					$err = 0;
					
					$record = array();
					$record['periode'] = $periode;
					$record['kodeunit'] = $kodeunit;
					$record['thnkurikulum'] = $kurikulum;
					
					$record['nim'] = $a_mhs[0]['nim'];
					
					$x=0;
					//print_r($a_kodemk);die();
					foreach($a_kodemk as $rowk) {
						$kelasmk=!empty($kelas)?$kelas:'01';
						$record['kodemk'] = $rowk['kodemk'];
						$record['kelasmk'] = $kelasmk;
						$a_kelas = mKelas::getDataPararel2($conn,$kurikulum.'|'.$rowk['kodemk'].'|'.$kodeunit.'|'.$periode.'|'.$kelasmk);
						$n_kelas = count($a_kelas);
						if($n_kelas > 0) {
							$err = mKRS::insertRecord($conn,$record);
							if($err) {
								
								$ok = false;
								break;
							}else{
								$x++;
								
							}
						}
					}
						
						
				}
				else {
					$err = -1;
					$msg = 'Mahasiswa '.$a_cek[0]['nim'].' sudah mengambil '.$a_cek[0]['kodemk'].', harap dihapus sebelum mengambil paket';
				}
			}else{
					$err = -1;
					$msg = 'Mahasiswa '.$nim.' Basis '.$namasistem[$sistemkuliah].' Tidak Ditemukan';
				}
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			if($err) {
				if(empty($msg))
					$msg = 'Pengambilan paket mahasiswa gagal';
			}
			else {
				$n_mhs = count($a_mhs);
				//$n_kelas = count($a_kodemk);
				
				$msg = $n_mhs.' mahasiswa angkatan '.$angkatan.' diambilkan '.$x.' Matakuliah';
			}
			
			return array($err,$msg);
		}
		
		// ambil paket dengan aturan
		function setPaketAturan($conn,$kurikulum,$periode,$kodeunit,$semmk,$angkatan,$jmlkelas='',$sistemkuliah) {
			/* if(empty($jmlkelas))
				$jmlkelas = 1; */
			
			require_once(Route::getModelPath('perwalian'));
			require_once(Route::getModelPath('krs'));
			require_once(Route::getModelPath('kelas'));
			
			$conn->BeginTrans();
			
			// periode sekarang
			//$periode = Akademik::getPeriode();
			$periodedaftar = $angkatan.'1';
			
			$a_kodemk = self::getDataPaket($conn,$kurikulum,$kodeunit,$semmk);
			if(substr($periode,4,1)!='0')
				$a_mhs = mPerwalian::getDataSudahBayar($conn,$periode,$kodeunit,$periodedaftar);
			else
				$a_mhs = mPerwalian::getMhsPaket($conn,$kodeunit,$periodedaftar,$sistemkuliah,$periode);
			
			
			$record = array();
			$record['periode'] = $periode;
			$record['kodeunit'] = $kodeunit;
			$record['thnkurikulum'] = $kurikulum;
			$jum_kelas=0;
			
			foreach($a_mhs as $datamhs){
				
				$record['nim'] = $datamhs['nim'];
				$prodimhs=$conn->GetOne("select kodeunit from akademik.ms_mahasiswa where nim='".$record['nim']."'");
				$updateperwalian=$conn->Execute("update akademik.ak_perwalian set frsdisetujui=-1 where nim='".$record['nim']."' and periode='$periode'");
				//$kelasmhs=$conn->GetOne("select kelasmk from akademik.ak_krs where nim='".$record['nim']."' and periode='$periode'");
				$arr_kodemk=array();
				
				foreach($a_kodemk as $rowk) {
					$a_kelas = mKelas::getDataPararel2($conn,$kurikulum.'|'.$rowk['kodemk'].'|'.$kodeunit.'|'.$periode.'|'.$prodimhs);
					foreach($a_kelas as $row_kelas){
						 if((int)$row_kelas['dayatampung']>(int)$row_kelas['jumlahpeserta']){
							$arr_kodemk[$rowk['kodemk']]= $row_kelas['kelasmk'];
						}
					}
				}
				$jum_kelas=count($arr_kodemk);
				$kelasmhs='';
				foreach($arr_kodemk as $keymk=>$v_kelasmk){
					$record['kodemk'] = $keymk;
					if(empty($kelasmhs)){
						$record['kelasmk'] = $v_kelasmk;
						$kelasmhs=$v_kelasmk;
					}else{
						$record['kelasmk'] = $kelasmhs;
					}
					$err = mKRS::insertRecord($conn,$record);
					if($err) {
						$ok = false;
						break;
					}
					
				}
				
			}
			
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			if($err) {
				if(empty($msg))
					$msg = 'Pengambilan paket mahasiswa gagal';
			}
			else {
				$n_mhs = count($a_mhs);
				$msg = $n_mhs.' mahasiswa angkatan '.$angkatan.' diambilkan '.$jum_kelas.' Matakuliah';
			}
			
			return array($err,$msg);
		}
		
		// mendapatkan pengambilan mata kuliah mahasiswa
		function getKRSMahasiswa($conn,$kurikulum,$kodeunit,$periode,$kodemk,$npm) {
			$inkodemk = array();
			foreach($kodemk as $t_data)
				$inkodemk[] = trim($t_data['kodemk']);
			
			$innpm = array();
			foreach($npm as $t_data)
				$innpm[] = trim($t_data['nim']);
			
			$sql = "select nim, kodemk from ".self::table('ak_krs')." where thnkurikulum = '$kurikulum'
					and kodeunit = '$kodeunit' and periode = '$periode' and kodemk in
					('".implode("','",$inkodemk)."') and nim in ('".implode("','",$innpm)."')";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		// mendapatkan data mata kuliah paket
		function getDataPaket($conn,$kurikulum,$kodeunit,$semmk) {
			$sql = "select kodemk, sks from ".self::table()." where thnkurikulum = '$kurikulum'
					and kodeunit = '$kodeunit' and semmk = '$semmk' and paket = 1";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		// mendapatkan data per semester
		function getDataPerSemester($conn,$kurikulum,$kodeunit) {
			$sql = "select * from ".self::table()." where thnkurikulum = '$kurikulum'
					and kodeunit = '$kodeunit' order by ".self::order;
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['semmk']][] = $row;
			
			return $a_data;
		}
		
		// mendapatkan data unit
		function getDataUnit($conn,$kodeunit,$keyword='') {
			$sql = "select kodeunit,thnkurikulum,kodemk,namamk from ".self::table()."
					where kodeunit = '$kodeunit'";
			if(!empty($keyword)) {
				$sql .= " and (lower(thnkurikulum) like '%$keyword%' or
						lower(kodemk) like '%$keyword%' or
						lower(namamk) like '%$keyword%')";
			}
			$sql .= " order by namamk, kurikulum";
			
			return $conn->GetArray($sql);
		}
		
		// mata kuliah kurikulum unit
		function mkKurikulumUnit($conn,$kurikulum,$kodeunit) {
			$sql = "select kodemk, kodemk||' - '||namamk||'('||sks||' SKS)' from ".static::table()."
					where thnkurikulum = '$kurikulum' and  kodeunit = '$kodeunit' order by kodemk";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mata kuliah kurikulum
		function mkKurikulum($conn,$kurikulum) {
			$sql = "select kodemk, kodemk||' - '||namamk||' - '||sks||' sks' from ".static::table('ak_matakuliah')."
					where thnkurikulum = '$kurikulum' order by namamk";
			
			return Query::arrQuery($conn,$sql);
		}
		function findmkKurikulum($conn,$str,$col='',$key='',$kurikulum) {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = static::key;
			if(empty($key))
				$key = static::key;
			
			$sql = "select $key, $col as label from ".static::table('ak_matakuliah')."
					where thnkurikulum = '$kurikulum' and (lower(namamk) like '%".strtolower($str)."%' or lower(kodemk) like '%".strtolower($str)."%') order by namamk";
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == static::key)
					$t_key = static::getKeyRow($row);
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			
			return $data;
		}
		// mendapatkan array data
		function getArray($conn,$kurikulum='', $unit='') {
			$sql = "select kodemk, namamk from ".static::table();
			if(!empty($kurikulum))
				$sql .= " where thnkurikulum='$kurikulum' and kodeunit = '$unit'";
			$sql .= " order by kodemk, namamk";
			
			return Query::arrQuery($conn,$sql);
		}
		
	}
?>
