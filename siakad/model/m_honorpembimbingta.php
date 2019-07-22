<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorPembimbingTa extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_pembimbingskripsi';
		const order = 'idhonorpembimbing';
		const key = 'idhonorpembimbing';
		const label = 'Honor Dosen Pembimbing Tugas Akhir';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select h.*,t.topikta,m.nama,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
				from ".static::table()." h
				join akademik.ms_mahasiswa m on m.nim=h.nim
				join akademik.ak_pembimbing pb on pb.nip::text=h.nipdosen::text
				join akademik.ak_ta t on pb.idta=t.idta and t.statusta='A'
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
			
			$sql="select p.idta,p.nip as nipdosen,t.nim
					from akademik.ak_pembimbing p
					join akademik.ak_ta t on t.idta=p.idta
					join akademik.ms_mahasiswa m on m.nim=t.nim
					where t.statusta='A' and m.kodeunit='$unit' and m.sistemkuliah='$sistemkuliah'";	
			$datadpa=$conn->GetArray($sql);
			
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			$r_nopengajuan=static::getNopengajuan($conn,$unit,$periode,$periodegaji);
			foreach($datadpa as $row){
				$keyrate='PS|'.$row['sistemkuliah'];
				
				$record=array();
				$record=$row;
				$record['periodegaji']=$periodegaji;
				$record['honor']=$a_rate[$keyrate];
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
		
		
		function getRowData($conn,$row) {
			
			$where=" idta='".$row['idta']."' and nipdosen='".$row['nipdosen']."'";
			$sql="select isvalid from ".static::table()." where $where order by isvalid asc";
			
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
			$nourut='PS/'.$periode.'/'.$kodeunit.'/'.$bulanpengajuan.'/'.$tahunpengajuan.'/'.str_pad($urut,3,'0',STR_PAD_LEFT);
		
			return $nourut;
		}
		
		function listNopengajuan($conn,$kodeunit,$periodegaji,$showunit=false){
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
			$sql.=" where g.periodegaji='$periodegaji' and g.isvalid=-1";
			
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
		
		function listNoPembayaran($conn,$periodegaji){
			$sql="select distinct g.nopembayaran as kode,g.nopembayaran as nomor from 
				".static::table()." g";
			$sql.=" where periodegaji='$periodegaji'";
			
			return Query::arrQuery($conn,$sql);
		}
	
	}
?>
