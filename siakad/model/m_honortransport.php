<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorTransport extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_transportdosen';
		const order = 'nipdosen, tglmengajar';
		const key = 'idtransportdosen';
		const label = 'Honor Transport Dosen';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select h.*,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
				from ".static::table()." h
				join sdm.ms_pegawai p on p.idpegawai=h.nipdosen";
			
		
		return $sql;
		}
		
		function getListFilter($col,$key) {
			
			
			switch($col) {
				case 'periodegaji' :
					return "h.periodegaji='$key'";
				case 'tglmengajar' :
					return "h.tglmengajar='$key'";
					
				default:
					return parent::getListFilter($col,$key);
			}
		}
		 
		function genGaji($conn,$conn_sdm,$periode,$periodegaji){
			require_once(Route::getModelPath('ratehonor'));
			$a_rate=mRateHonor::getArray($conn);
			
			
			$bulan=substr($periodegaji,4,2);
			$tahun=substr($periodegaji,0,4);
			$q_kuliah="select k.periode,k.nipdosenrealisasi, k.tglkuliahrealisasi,kl.sistemkuliah
						from akademik.ak_kuliah k
						join akademik.ak_kelas kl using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
						where k.periode='$periode' and extract(MONTH from k.tglkuliahrealisasi) = '$bulan'
						and extract(YEAR from k.tglkuliahrealisasi) = '$tahun' and k.statusperkuliahan='S' and k.nipdosenrealisasi is not null
						and k.isonline=0
						group by k.periode,k.nipdosenrealisasi, k.tglkuliahrealisasi,kl.sistemkuliah";
			$datakuliah=$conn->GetArray($q_kuliah);
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			
			foreach($datakuliah as $row){
				$keyrate='TD|'.$row['sistemkuliah'];
				
				$record=array();
				$record['periode']=$row['periode'];
				$record['tglmengajar']=$row['tglkuliahrealisasi'];
				$record['nipdosen']=$row['nipdosenrealisasi'];
				$record['periodegaji']=$periodegaji;
				$record['honor']=$a_rate[$keyrate];
				$record['isvalid']=-1;
				
				$keyhonor=$record['nipdosen'].'|'.$record['tglmengajar'];
				$exist=static::isDataExist($conn,$keyhonor,'nipdosen, tglmengajar');
				if(!$exist){
					$err = Query::recInsert($conn,$record,static::table());
					$insert++;
					if($err) break;
				}	
			}
			
			if($err) $ok=false;
			$conn->CommitTrans($ok);
			
			if($ok)
				return array(false,'Generate Honor Transport berhasil,'.$insert.' data baru ditambahkan');
			else
				return array(true,'Generate Honor Mengajar Gagal');
		}
		
		
		
		function convertPeriodeGaji($periodegaji){
			$tahun=substr($periodegaji,0,4);
			$bulan=substr($periodegaji,4,2);
			
			return Date::indoMonth((int)$bulan).' '.$tahun;
		}
		
		
		
	
	}
?>
