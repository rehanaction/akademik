<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorTransportPenguji extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_transportpenguji';
		const order = 'idhonortransport';
		const key = 'idhonortransport';
		const label = 'Honor Transport Penguji Sidang';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select h.*,hj.periode,hj.nim,hj.jenispenguji,hj.tglujian,hj.nipdosen,m.nama,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
				from ".static::table()." h
				join ".static::table('hn_pengujiujian')." hj on h.idhonorpenguji=hj.idhonorpenguji
				join akademik.ms_mahasiswa m on m.nim=hj.nim
				join gate.ms_unit j on j.kodeunit=m.kodeunit
				join sdm.ms_pegawai p on p.idpegawai=hj.nipdosen";
			
		
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
					return "hj.periode='$key'";
				case 'sistemkuliah' :
					return "m.sistemkuliah='$key'";	
				default:
					return parent::getListFilter($col,$key);
			}
		}
		 
		function genGaji($conn,$periode,$periodegaji,$jenispenguji,$sistemkuliah){
			require_once(Route::getModelPath('ratehonor'));
			$a_rate=mRateHonor::getArray($conn);
			
			$sql="select h.*,m.kodeunit,coalesce(m.sistemkuliah,'R') as sistemkuliah from ".static::table('hn_pengujiujian')." h
				join akademik.ms_mahasiswa m on m.nim=h.nim
				where h.periode='$periode' and h.periodegaji='$periodegaji' and h.jenispenguji='$jenispenguji'
				and m.sistemkuliah='$sistemkuliah'";	
			$datadpa=$conn->GetArray($sql);
			
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			
			foreach($datadpa as $row){
				$keyrate='TPS|'.$row['sistemkuliah'];
				$r_nopengajuan=static::getNopengajuan($row['nopengajuan']);
				
				$record=array();
				$record=$row;
				$record['periodegaji']=$periodegaji;
				$record['honor']=$a_rate[$keyrate];
				$record['nopengajuan']=$r_nopengajuan;
				$record['isvalid']=-1;
				$record['nopembayaran']=null;
				
					
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
			
			$where=" idhonorpenguji='".$row['idhonorpenguji']."'";
			$sql="select isvalid from ".static::table()." where $where order by isvalid asc";
			
			$data = $conn->GetRow($sql);
			
			return $data;
		}
		function getNopengajuan($nopengajuan){
			$a_nopengajuan=explode('/',$nopengajuan);
			$a_nopengajuan[0]='TPS';
			
			$nourut=implode('/',$a_nopengajuan);
			
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
			$sql.=" join ".static::table('hn_pengujiujian')." h on h.idhonorpenguji=g.idhonorpenguji";
			$sql.=" join akademik.ms_mahasiswa m on m.nim=h.nim";
			$sql.=" join gate.ms_unit j on j.kodeunit=m.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			$sql.=" where h.periode='$periode' and h.periodegaji='$periodegaji' and g.isvalid=-1";
			if(!empty($jenispenguji))
				$sql.=" and h.jenispenguji='$jenispenguji'";
				
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
		
		function listNoPembayaran($conn,$periode,$periodegaji,$kodeunit=''){
			$sql="select distinct g.nopembayaran as kode,g.nopembayaran as nomor from 
				".static::table()." g";
			$sql.=" join ".static::table('hn_pengujiujian')." h on h.idhonorpenguji=g.idhonorpenguji";
			if(!empty($kodeunit)){
				require_once(Route::getModelPath('unit'));
				$unit = mUnit::getData($conn,$kodeunit);
				$sql.=" join gate.ms_unit j on j.kodeunit=g.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			}
			$sql.=" where h.periode='$periode' and g.periodegaji='$periodegaji'";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getPenerimaHonor($conn,$a_nopengajuan){
			$in_nopengajuan="'".implode("','",$a_nopengajuan)."'";
			$sql="select distinct p.nipdosen from ".static::table()." h 
				join ".static::table('hn_pengujiujian')." p using (idhonorpenguji)
				where h.nopengajuan in ($in_nopengajuan)";
				
			return Query::arrQuery($conn,$sql);;
		}
	}
?>
