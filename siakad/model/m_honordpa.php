<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorDpa extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_dpa';
		const order = 'idhonordpa';
		const key = 'idhonordpa';
		const label = 'Honor Dosen Pembimbing Akademik';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select h.*,m.nama,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
				from ".static::table()." h
				join akademik.ms_mahasiswa m on m.nim=h.nim
				join gate.ms_unit j on j.kodeunit=m.kodeunit
				join sdm.ms_pegawai p on p.idpegawai=h.nipdosen";
			
		
			return $sql;
		}
		
		function getListFilter($col,$key) {
			
			
			switch($col) {
				case 'honorunit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					$row = mUnit::getData($conn,$key);
					return "j.infoleft >= ".(int)$row['infoleft']." and j.inforight <= ".(int)$row['inforight'];
				case 'periodegaji' :
					return "h.periodegaji='$key'";
				case 'periode' :
					return "h.periode='$key'";
				case 'unit' :
					return "m.kodeunit='$key'";	
				case 'sistemkuliah' :
					return "m.sistemkuliah='$key'";
				default:
					return parent::getListFilter($col,$key);
			}
		}
		
		function genGaji($conn,$unit,$periode,$periodegaji,$sistemkuliah){
			require_once(Route::getModelPath('ratehonor'));
			$a_rate=mRateHonor::getArray($conn);

			$sql="select p.nim,p.periode,p.nipdosenwali as nipdosen,m.sistemkuliah,count(k.idkonsultasi) as jmlkonsul
					from akademik.ak_perwalian p
					join akademik.ms_mahasiswa m using(nim)
					join akademik.ak_konsultasi k on p.nim = k.nim and p.periode = k.periode
					where p.nipdosenwali is not null and m.kodeunit='$unit' and p.periode='$periode'
					and m.sistemkuliah='$sistemkuliah' 
					and (semmhs <> 1  and periodemasuk <> p.periode) 
					group by p.nim,p.periode,p.nipdosenwali,m.sistemkuliah";	
			$datadpa=$conn->GetArray($sql);

			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			$r_nopengajuan=static::getNopengajuan($conn,$unit,$periode,$periodegaji);
			foreach($datadpa as $row){
				$keyrate='PA|'.$row['sistemkuliah'];

				$record=array();
				$record=$row;
				$record['periodegaji']=$periodegaji;
				
				//$record['honor']=$a_rate[$keyrate]*6;//6 hardcode untuk 6 bulan
				//hardcode FR. 46, konsultasi mempengaruhi honor
				//konsultasi max 3x
				if($row['jmlkonsul'] > 3)
					$record['honor']=$a_rate[$keyrate]*3;//6 hardcode untuk 6 bulan
				else
					$record['honor']=$a_rate[$keyrate]*$row['jmlkonsul'];
					
				$record['nopengajuan']=$r_nopengajuan;
				$record['isvalid']=-1;

				$a_data=static::getRowData($conn,$record);
				if(empty($a_data)){
					$err = Query::recInsert($conn,$record,static::table());
					$insert++;
					if($err) break;
				}else if(!empty($a_data) and empty($a_data['isvalid'])){
					$err = Query::recInsert($conn,$record,static::table());
					$update++;

					if($err) break;
				}


			}
			
			if($err) $ok=false;
			$conn->CommitTrans($ok);

			if($ok)
				return array(false,'Generate '.static::label.' berhasil,'.$insert.' data baru ditambahkan,'.$update.' data digenerate ulang');
			else
				return array(true,'Generate '.static::label.' Gagal');
		}
 
		/*
		function genGaji($conn,$unit,$periode,$periodegaji,$sistemkuliah){
			require_once(Route::getModelPath('ratehonor'));
			$a_rate=mRateHonor::getArray($conn);
			
			$sql="select p.nim,p.periode,p.nipdosenwali as nipdosen,m.sistemkuliah 
					from akademik.ak_perwalian p
					join akademik.ms_mahasiswa m using(nim)
					where p.nipdosenwali is not null and m.kodeunit='$unit' and p.periode='$periode' and m.sistemkuliah='$sistemkuliah'";	
			$datadpa=$conn->GetArray($sql);
			
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			$r_nopengajuan=static::getNopengajuan($conn,$unit,$periode,$periodegaji);
			foreach($datadpa as $row){
				$keyrate='PA|'.$row['sistemkuliah'];
				
				$record=array();
				$record=$row;
				$record['periodegaji']=$periodegaji;
				$record['honor']=$a_rate[$keyrate]*6;//6 hardcode untuk 6 bulan
				$record['nopengajuan']=$r_nopengajuan;
				$record['isvalid']=-1;
					
				$a_data=static::getRowData($conn,$record);
				if(empty($a_data)){
					$err = Query::recInsert($conn,$record,static::table());
					$insert++;
					if($err) break;
				}else if(!empty($a_data) and empty($a_data['isvalid'])){
					$err = Query::recInsert($conn,$record,static::table());
					$update++;
					
					if($err) break;
				}
					
					
			}
			
			if($err) $ok=false;
			$conn->CommitTrans($ok);
			
			if($ok)
				return array(false,'Generate '.static::label.' berhasil,'.$insert.' data baru ditambahkan,'.$update.' data digenerate ulang');
			else
				return array(true,'Generate '.static::label.' Gagal');
		}
		*/
		
		
		function convertPeriodeGaji($periodegaji){
			$tahun=substr($periodegaji,0,4);
			$bulan=substr($periodegaji,4,2);
			
			return Date::indoMonth((int)$bulan).' '.$tahun;
		}
		
		function getRowData($conn,$row) {
			
			$where=" nim='".$row['nim']."' and nipdosen='".$row['nipdosen']."' and periode='".$row['periode']."'";
			$sql="select nim,nipdosen,periode,isvalid from ".static::table()." where $where order by isvalid asc";
			
			$data = $conn->GetRow($sql);
			
			return $data;
		}
		function getNopengajuan($conn,$kodeunit,$periode,$periodepengajuan){
			$periode=Akademik::getNamaPeriode($periode,true);
			
			$bulanpengajuan=substr($periodepengajuan,4,2);
			$tahunpengajuan=substr($periodepengajuan,0,4);
			$sql="select max(substr(nopengajuan,length(nopengajuan)-2,3)) from ".static::table()." 
			where substr(nopengajuan,length(nopengajuan)-7,4)='$tahunpengajuan'";
			$max=$conn->GetOne($sql);
			$urut=(int)$max+1;
			$nourut='PA/'.$periode.'/'.$kodeunit.'/'.$bulanpengajuan.'/'.$tahunpengajuan.'/'.str_pad($urut,3,'0',STR_PAD_LEFT);
		
			return $nourut;
		}
		
		function listNopengajuan($conn,$periode,$kodeunit,$periodegaji,$showunit=false){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql="select distinct g.nopengajuan as kode,";
			
			if($showunit)
				$sql.=" g.nopengajuan||' '||j.namaunit as nomor";
			else
				$sql.=" g.nopengajuan as nomor";
				
			$sql.=" from ".static::table()." g";
			$sql.=" join akademik.ms_mahasiswa m on m.nim=g.nim";
			$sql.=" join gate.ms_unit j on j.kodeunit=m.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			$sql.=" where g.periode='$periode' and g.periodegaji='$periodegaji' and g.isvalid=-1";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function setNoPembayaran($conn,$nopengajuan){
			$a_nopengajuan=explode("/",$nopengajuan[0]);
			
			$bulan=$a_nopengajuan[3];
			$tahun=$a_nopengajuan[4];
			$sql="select max(substr(nopembayaran,length(nopembayaran)-2,3)) from ".static::table()." 
				where substr(nopembayaran,length(nopembayaran)-8,4)='$tahun'";
			$max=$conn->GetOne($sql);
			
			$urut=(int)$max+1;
			$nopembayaran='BB'.$bulan.'/'.$tahun.'/'.str_pad($urut,4,'0',STR_PAD_LEFT);
			$record=array();
			$record['nopembayaran']=$nopembayaran;
			$err = Query::recUpdate($conn,$record,static::table(),"isvalid=-1 and (nopembayaran='' or nopembayaran is null) and nopengajuan in ('".implode("','",$nopengajuan)."') ");
			
			if(!$err)
				return array(false,'Setting Pembayaran honor berhasil');
			else
				return array(true,'Setting Pembayaran honor gagal');
		}
	
	}
?>
