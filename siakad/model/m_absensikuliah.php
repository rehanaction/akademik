<?php
	// model absensi perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAbsensiKuliah extends mModel {
		const schema = 'akademik';
		const table = 'ak_absensikuliah';
		const order = 'nim';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,tglkuliah,perkuliahanke,nim';
		const label = 'absensi kuliah';
		
		// data per kelas
		function getListPerKelas($conn,$key) {
			$sql = "select perkuliahanke,nim,absen,jeniskuliah,tglkuliah from ".static::table()." where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk');
			
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['perkuliahanke']][$row['tglkuliah']][$row['nim']][$row['jeniskuliah']] = $row['absen'];
			
			return $data;
		}
		function getListPerKelasOnline($conn_moodle,$key) {
			$sql = "select  perkuliahanke,nim,absen,jeniskuliah,tglkuliah from moodle.v_absensionline where perkuliahanke is not null and ".static::getCondition($key,'kodemk,kodeunit,periode,kelasmk');
			$rs = $conn_moodle->Execute($sql);
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['perkuliahanke']][$row['tglkuliah']][$row['nim']][$row['jeniskuliah']] = $row['absen'];
			
			return $data;
		}
		function getListPerKelasOnlineCron($conn_moodle,$key) {
			$sql = "select distinct perkuliahanke,nim,absen,jeniskuliah,DATE(tglkuliah) as tglkuliah  from moodle.v_absensionline where tglkuliah>=date_trunc('s',localtimestamp)- interval '8 day' and tglkuliah<=date_trunc('s',localtimestamp) and perkuliahanke is not null and ".static::getCondition($key,'kodemk,kodeunit,periode,kelasmk');
			$rs = $conn_moodle->GetArray($sql);
			return $rs;
		}
		function insertAbsensiOnline($conn,$data){
			
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$i = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					$valuesArrays[$i] = $values;
				}else{
					$valuesArrays[$i]= "'".$values."'";
				}
				$i++;
			}
			$values = implode(',',$valuesArrays);
			$sql = "insert into akademik.ak_absensikuliah ($kolom) values($values)";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		// data per pertemuan
		function getListPerPertemuan($conn,$key) {
			$sql = "select nim, absen, wakturfid from akademik.ak_absensikuliah
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke');
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['nim']] = $row;
			
			return $data;
		}
		
		// data total per kelas set_nilai & set_nilaihuruf
		//Tak nonaktifkan ya, kalo mau dipakai lagi, mohon uji kembali proses perhitungan absenNya.
		 function getListPersenPerKelas($conn,$key) {
			$sql = "select thnkurikulum,periode,nim,kodeunit, kodemk,kelasmk,totalabsenmhs, totalabsenkelas, kelompok_prak, kelompok_tutor  from ".static::table('r_absenmhs')."
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk');
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(empty($row['totalabsenkelas']))
					$data[$row['nim']] = 100;
				else{
					//$data[$row['nim']] = round(($row['totalabsenmhs']*100)/$row['totalabsenkelas'],2);
					$data[$row['nim']] = $conn->GetOne("select akademik.f_absensi(".$row['thnkurikulum'].",'".$row['periode']."','".$row['kodeunit']."','".$row['kodemk']."','".$row['kelasmk']."','".$row['nim']."','".$row['kelompok_prak']."','".$row['kelompok_tutor']."')");
					//echo $data[$row['nim']];
				}
			}
			
			return $data;
		}
		
		function getProsAbsensikelas($conn,$keykelas){
			$sql="select nim,jeniskuliah,totalabsenkelas,totalabsenmhs from ".static::table('r_absenmhs')."
					where ".static::getCondition($keykelas,'thnkurikulum,kodemk,kodeunit,periode,kelasmk')." order by nim,jeniskuliah";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(empty($row['totalabsenkelas']))
					$data[$row['nim']][$row['jeniskuliah']] = 100;
				else
					$data[$row['nim']][$row['jeniskuliah']] = round(($row['totalabsenmhs']*100)/$row['totalabsenkelas'],2);
			}
			return $data;
		}
		
		function getProsAbsensikelas2($conn,$keykelas){
			$sql="select nim,jeniskuliah,totalabsenkelas,totalabsenmhs from ".static::table('r_absenmhs')."
					where ".static::getCondition($keykelas,'thnkurikulum,kodemk,kodeunit,periode,kelasmk')." order by nim,jeniskuliah";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(empty($row['totalabsenkelas']))
					$data[$row['nim']][$row['jeniskuliah']] = 100;
				else
					$data[$row['nim']][$row['jeniskuliah']] = round(($row['totalabsenmhs']*100)/$row['totalabsenkelas'],2);
			}
			return $data;
		}
		
		function getProsAbsensimhs($conn,$nim,$periode){
			$sql="select kodemk,jeniskuliah,totalabsenkelas,totalabsenmhs from ".static::table('r_absenmhs')."
					where nim = '$nim' and periode = '$periode' order by kodemk, kelasmk";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if(empty($row['totalabsenkelas']))
					$data[$row['kodemk']][$row['jeniskuliah']] = 100;
				else
					$data[$row['kodemk']][$row['jeniskuliah']] = round(($row['totalabsenmhs']*100)/$row['totalabsenkelas'],2);
			}
			return $data;
		}
		
		// data per periode view_absenmhs
		function getListPerMhsPeriode($conn,$nim,$periode) {
			$sql = "select thnkurikulum,periode,nim,kodeunit, kodemk, namamk, kelasmk, totalabsenmhs, totalabsenkelas,jeniskuliah,kelompok_prak,kelompok from ".static::table('r_absenmhs')."
					where nim = '$nim' and periode = '$periode' order by kodemk, kelasmk";
				
			return $conn->GetArray($sql);
		}
		//data absen
		function getAbsensi($conn,$nim,$periode,$kodemk) {
			$sql = "select thnkurikulum,periode,nim,kelompok_prak,kelompok_tutor,kodeunit, kodemk, namamk, kelasmk, totalabsenmhs, totalabsenkelas from ".static::table('r_absenmhs')."
					where nim = '$nim' and periode = '$periode' and kodemk='$kodemk'";
			
			return $conn->GetRow($sql);
		}
		
		function savePerPertemuan($conn,$key,$arrnim,$kelompok) {
			
			if(empty($kelompok))
				$kelompok=1;
			$err = false;
			
			foreach($arrnim as $nim=>$absen){
				$where = static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke,tglkuliah');
				
				if($absen=='A'){
					
					$err = Query::qDelete($conn,static::table(),$where." and nim='$nim'",false);
					if($err) break;
				}else{
					
					$data=$conn->GetOne("select absen from ".static::table()." where ".$where." and nim='$nim'");
					$record = static::getKeyRecord($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke,tglkuliah');
					$record['nim'] = $nim;
					$record['absen'] = $absen;
					$record['kelompok'] = $kelompok;
					if(empty($data)){
						$err = Query::recInsert($conn,$record,static::table());
						if($err) break;
					}else{
						$err = Query::recUpdate($conn,$record,static::table(),$where ." and nim='$nim'");
						if($err) break;
					}
					
				}
			}
			if(!$err) {
				require_once(Route::getModelPath('kuliah'));
				$where = static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke,tglkuliah');
				
				$record = array();
				$record['jumlahpeserta'] = $conn->GetOne("select coalesce(sum(1),0) from ".static::table()." where ".$where." and kelompok='$kelompok' and absen='H'");
				
				//$err = mKuliah::updateRecord($conn,$record,$key);
				//echo $record['jumlahpeserta'];
				$err = Query::recUpdate($conn,$record,static::table('ak_kuliah'),$where ." and kelompok='$kelompok'");
			}
			
		}
		function savePerPertemuan2($conn,$key,$arrnim,$kelompok) {
			
			if(empty($kelompok))
				$kelompok=1;
			$err = false;
			
			foreach($arrnim as $nim=>$absen){
				$where = static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke,tglkuliah');
				
				if($absen=='A'){
					
					$err = Query::qDelete($conn,static::table(),$where." and nim='$nim'",false);
					if($err) break;
				}else{
					
					$data=$conn->GetOne("select absen from ".static::table()." where ".$where." and nim='$nim'");
					$record = static::getKeyRecord($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke,tglkuliah');
					$record['nim'] = $nim;
					$record['absen'] = $absen;
					$record['kelompok'] = $kelompok;
					if(empty($data)){
						$err = Query::recInsert($conn,$record,static::table());
						if($err) break;
					}else{
						$err = Query::recUpdate($conn,$record,static::table(),$where ." and nim='$nim'");
						if($err) break;
					}
					
				}
			}
			if(!$err) {
				require_once(Route::getModelPath('kuliah'));
				$where = static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke,tglkuliah');
				
				$record = array();
				$record['jumlahpeserta'] = $conn->GetOne("select coalesce(sum(1),0) from ".static::table()." where ".$where." and kelompok='$kelompok' and absen='H'");
				
				//$err = mKuliah::updateRecord($conn,$record,$key);
				//echo $record['jumlahpeserta'];
				$err = Query::recUpdate($conn,$record,static::table('ak_kuliah'),$where ." and kelompok='$kelompok'");
			}
			
		}
		
		function updateIjin($conn,$key,$arrnim){
			require_once(Route::getModelPath('krs'));
			$a_absensi=self::getProsAbsensikelas($conn,$key);
			$arr_absen=array();
			foreach($arrnim as $t_nim => $t_status) { 
				$t_absenmhs=$a_absensi[$t_nim]['K'];//hardcode untuk jenis kuliah
				$arr_absen[$t_nim]=$t_absenmhs;
			}
			
			foreach($arr_absen as $nim => $pros_absen){
				$rec=array();
				if($pros_absen < Akademik::getProsAbsen()){
					$rec['isikututs']=0;
					$rec['isikutuas']=0;
				}else{
					$rec['isikututs']=-1;
					$rec['isikutuas']=-1;
				}
				$keykrs = $key.'|'.$nim;
				$err = mKrs::updateRecord($conn,$rec,$keykrs);
				//$err = Query::recUpdate($conn,$rec,static::table('ak_krs'),"nim='$nim' and periode='".$keyabsen[3]."' and kodemk='".$keyabsen[1]."'");
			}
			return $err;
		}
		
		function updateFlagUjian($conn,$key,$arrnim){
			require_once(Route::getModelPath('krs'));
			$a_absensi=self::getProsAbsensikelas($conn,$key);
			$arr_absen=array();
			foreach($arrnim as $t_nim) { 
				$t_absenmhs=$a_absensi[$t_nim]['K'];//hardcode untuk jenis kuliah
				$arr_absen[$t_nim]=$t_absenmhs;
			}
			//var_dump($arr_absen);exit;
			foreach($arr_absen as $nim => $pros_absen){
				$rec=array();
				if($pros_absen < Akademik::getProsAbsen($conn)){
					$rec['isikututs']=0;
					$rec['isikutuas']=0;
				}else{
					$rec['isikututs']=-1;
					$rec['isikutuas']=-1;
				}
				$keykrs = $key.'|'.$nim;
				$err = mKrs::updateRecord($conn,$rec,$keykrs);
				//$err = Query::recUpdate($conn,$rec,static::table('ak_krs'),"nim='$nim' and periode='".$keyabsen[3]."' and kodemk='".$keyabsen[1]."'");
			}
			return $err;
		}
		
		function updateJumlahPeserta($conn,$key){
			require_once(Route::getModelPath('kuliah'));
			$where = static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompok,perkuliahanke,tglkuliah');
			
			$record = array();
			$jumlahpeserta = $conn->GetOne("select coalesce(sum(1),0) from ".static::table()." where ".$where." and absen='H'");
			$record['jumlahpeserta'] =$jumlahpeserta+1;
			$err = Query::recUpdate($conn,$record,static::table('ak_kuliah'),$where);
			
			
			return $err;
		}
	}
?>
