<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorPenguji extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_pengujiujian';
		const order = 'idhonorpenguji';
		const key = 'idhonorpenguji';
		const label = 'Honor Penguji Sidang';
		
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
				case 'tglujian' :
					return "h.tglujian='$key'";	
				default:
					return parent::getListFilter($col,$key);
			}
		}
		 
		function genGaji($conn,$record){
			require_once(Route::getModelPath('ratehonor'));
			$a_rate=mRateHonor::getArray($conn);
			
			
			$a_mhs=$conn->GetRow("select coalesce(sistemkuliah,'R') as sistemkuliah,kodeunit from akademik.ms_mahasiswa where nim='".$record['nim']."'");
			
			$keyrate=$record['jenispenguji'].'|'.$a_mhs['sistemkuliah'];
			$record['honor']=$a_rate[$keyrate];
			$record['nopengajuan']=static::getNopengajuan($conn,$a_mhs['kodeunit'],$record['periode'],$record['periodegaji'],$record['jenispenguji']);
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
					
			
			if(!$err)
				return array(false,'Penambahan data '.static::label.' berhasil,'.$insert.' data baru ditambahkan,'.$update.' data digenerate ulang');
			else
				return array(true,'Penambahan data '.static::label.' Gagal');
		}
		
		function genGajiSkripsi($conn,$record){
			require_once(Route::getModelPath('ratehonor'));
			$a_rate=mRateHonor::getArray($conn);
			
			$periode=$record['periode'];
			$sql="select k.nim,p.nip as nipdosen,p.tipepenguji,coalesce(m.sistemkuliah,'R') as sistemkuliah,m.kodeunit,ut.tglujian from akademik.ak_transkrip k
					join akademik.ms_mahasiswa m on m.nim=k.nim
					join akademik.ak_matakuliah mk on mk.thnkurikulum = k.thnkurikulum and mk.kodemk = k.kodemk and mk.tipekuliah = 'T'
					join akademik.ak_ta t on t.nim=k.nim
					join akademik.ak_ujianta ut on ut.idta=t.idta and ut.jenisujian='A'
					join akademik.ak_penguji p on p.idujianta=ut.idujianta
					where k.periode='$periode' and ut.tglujian is not null";
			$rs=$conn->Execute($sql);
			
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			while($row=$rs->fetchRow()){
				$keyrate=$record['jenispenguji'].'|'.$row['sistemkuliah'];
				$record['nim']=CStr::cStrNull($row['nim']);
				$record['tglujian']=CStr::cStrNull($row['tglujian']);
				$record['nipdosen']=CStr::cStrNull($row['nipdosen']);
				$record['honor']=$a_rate[$keyrate];
				$record['nopengajuan']=static::getNopengajuan($conn,$ow['kodeunit'],$record['periode'],$record['periodegaji'],$record['jenispenguji']);
				$record['isvalid']=-1;
				
				$a_data=static::getRowData($conn,$record);
				if(empty($a_data)){
					$err = Query::recInsert($conn,$record,static::table());
					if(!$err)
						$insert++;
					else
						break;
				}else if(!empty($a_data) and empty($a_data['isvalid'])){
					$err = Query::recInsert($conn,$record,static::table());
					if(!$err)
						$update++;
					else
						break;
				}
			}
			if($err) $ok=false;
			$conn->CommitTrans($ok);
			
			if(!$err)
				return array(false,'Penambahan data '.static::label.' berhasil,'.$insert.' data baru ditambahkan,'.$update.' data digenerate ulang');
			else
				return array(true,'Penambahan data '.static::label.' Gagal');
		}
		
		function getJenisSidang(){
			$a_jenis=array('PPM'=>'Penguji Ujan Magang','PPP'=>'Penguji Sidang Proposal','PPS'=>'Penguji Sidang Skripsi');
			
			return $a_jenis;
		}
		
		function convertPeriodeGaji($periodegaji){
			$tahun=substr($periodegaji,0,4);
			$bulan=substr($periodegaji,4,2);
			
			return Date::indoMonth((int)$bulan).' '.$tahun;
		}
		
		function getRowData($conn,$row) {
			
			$where=" nim='".$row['nim']."' and jenispenguji='".$row['jenispenguji']."' and nipdosen='".$row['nipdosen']."' and tglujian='".$row['tglujian']."'";
			$sql="select nim,nipdosen,jenispenguji,isvalid from ".static::table()." where $where order by isvalid asc";
			
			$data = $conn->GetRow($sql);
			
			return $data;
		}
		function getNopengajuan($conn,$kodeunit,$periode,$periodepengajuan,$jenispenguji){
			$periode=Akademik::getNamaPeriode($periode,true);
			
			$bulanpengajuan=substr($periodepengajuan,4,2);
			$tahunpengajuan=substr($periodepengajuan,0,4);
			$sql="select max(substr(nopengajuan,length(nopengajuan)-2,3)) from ".static::table()." 
			where substr(nopengajuan,length(nopengajuan)-7,4)='$tahunpengajuan'";
			$max=$conn->GetOne($sql);
			$urut=(int)$max+1;
			$nourut=$jenispenguji.'/'.$periode.'/'.$kodeunit.'/'.$bulanpengajuan.'/'.$tahunpengajuan.'/'.str_pad($urut,3,'0',STR_PAD_LEFT);
		
			return $nourut;
		}
		
		function listNopengajuan($conn,$periode,$kodeunit,$periodegaji,$showunit=false,$jenispenguji=''){
			
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
			if(!empty($jenispenguji))
				$sql.=" and g.jenispenguji='$jenispenguji'";
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
