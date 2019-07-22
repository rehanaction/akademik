<?php
	// model periode wisuda
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSetOnline extends mModel {
		const schema = 'akademik';
		const table = 'ms_setonline';
		const order = 'kodeunit , periode , semmk';
		const key = 'kodeunit , periode , semmk';
		const label = 'Setting Aturan perkuliahan Online';
		
		function listQuery() {
			$sql="select o.* from ".static::table()." o
				join gate.ms_unit u on o.kodeunit=u.kodeunit";
			
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
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']; 
			}
		}
		// mendapatkan array data
		function getArray($conn,$periode='',$kodeunit='') {
			$sql = "select s.kodeunit,s.periode,s.semmk,s.pertemuan from ".static::table()." s";
			if(!empty($kodeunit)){
				require_once(Route::getModelPath('unit'));
				$row = mUnit::getData($conn,$kodeunit);
				$sql.=" join gate.ms_unit u on u.kodeunit=s.kodeunit and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			if(!empty($periode))
				$sql.=" where s.periode='$periode'";
			
			$sql.=" and s.isaktif=-1 order by s.kodeunit , s.periode , s.semmk";
			$data=$conn->GetArray($sql);
			$a_data=array();
			foreach($data as $row){
				$a_pertemuan=explode(',',$row['pertemuan']);
				foreach($a_pertemuan as $pertemuan){
					$key=$row['kodeunit'].'|'.$row['periode'].'|'.$row['semmk'].'|'.$pertemuan;
					$a_data[$key]=$row['pertemuan'];
				}
			}
			
			return $a_data;
		}
		function copy($conn,$periodeasal,$periodetujuan) {
			$ok = true;
			$conn->BeginTrans();
			
			// masukkan mata kuliah
			$sql = "insert into ".static::table()." (kodeunit,periode,semmk,pertemuan,isaktif,t_updateact)
					select s.kodeunit,'$periodetujuan',s.semmk,s.pertemuan,s.isaktif,'salin' from ".static::table()." s
					where s.periode='$periodeasal'";
			$ok = $conn->Execute($sql);
			
			$err = $conn->ErrorNo();
			$conn->CommitTrans($ok);
			
			if($ok)
				$msg = 'Salin '.self::label.' berhasil';
			else
				$msg = 'Salin '.self::label.' gagal';
			
			return array($err,$msg);
		}
		
		
	}
?>
