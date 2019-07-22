<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorAsisten extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_asisten';
		const order = 'idhonorasisten';
		const key = 'idhonorasisten';
		const label = 'Honor Asisten';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select h.idhonorasisten,h.tglkuliah , h.perkuliahanke , h.periode , h.thnkurikulum , h.kodeunit , h.kodemk , h.kelasmk , h.jeniskuliah , h.kelompok,
				s.tipeprogram as basis,kl.sistemkuliah,h.nopembayaran,h.honor,h.nopengajuan,h.isvalid,
				p.namapegawai as namaasisten,h.nipasisten,
				j.namaunit as jurusan,mk.namamk,coalesce(h.skshonor,mk.sks) as sks,k.tglkuliahrealisasi,k.isonline
				from ".static::table()." h
				join akademik.ak_kuliah k using (tglkuliah, perkuliahanke, periode, thnkurikulum, kodeunit, kodemk, kelasmk, jeniskuliah, kelompok)
				join akademik.ak_kurikulum mk using (thnkurikulum , kodeunit, kodemk )
				join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
				left join akademik.ak_sistem s on s.sistemkuliah=kl.sistemkuliah
				join akademik.ms_pegawaipenunjang p on h.nipasisten = p.nopegawai
				join gate.ms_unit j on j.kodeunit=h.kodeunit";
			
		
			return $sql;
		}
		
		function getListFilter($col,$key) {
			
			
			switch($col) {
				case 'fakultas':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					$row = mUnit::getData($conn,'90');
					return "j.infoleft >= ".(int)$row['infoleft']." and j.inforight <= ".(int)$row['inforight'];
				case 'honorunit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					$row = mUnit::getData($conn,$key);
					return "j.infoleft >= ".(int)$row['infoleft']." and j.inforight <= ".(int)$row['inforight'];
				case 'unit':
					return "j.kodeunit='$key'";
				case 'periode' :
					return "h.periode='$key'";
				case 'periodegaji' :
					return "h.periodegaji='$key'";
				case 'nopengajuan' :
					return "h.nopengajuan='$key'";
				case 'nopembayaran' :
					return "h.nopembayaran='$key'";
				default:
					return parent::getListFilter($col,$key);
			}
		}
		 
		function genGaji($conn,$unit,$periode,$periodegaji){
			
			
			$bulan=substr($periodegaji,4,2);
			$tahun=substr($periodegaji,0,4);
			$sql="select k.tglkuliah, k.perkuliahanke, k.periode, k.thnkurikulum, k.kodeunit, k.kodemk, k.kelasmk, k.jeniskuliah, k.kelompok,
						k.nipasisten, k.tglkuliahrealisasi,k.jeniskuliah,k.isonline,mk.sks,mk.skspraktikum,mk.skstatapmuka,
						mk.sksprakteklapangan,kl.sistemkuliah
						from akademik.ak_kuliah k
						join akademik.ak_matakuliah mk using (thnkurikulum , kodemk )
						join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
						where k.kodeunit='$unit' and k.periode='$periode' and extract(MONTH from k.tglkuliahrealisasi) = '$bulan'
						and extract(YEAR from k.tglkuliahrealisasi) = '$tahun' and k.statusperkuliahan='S' and k.nipasisten is not null";
			$rs=$conn->Execute($sql);
			
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			$r_nopengajuan=static::getNopengajuan($conn,$unit,$periode,$periodegaji);
			while($row=$rs->fetchRow()){
				
				$record=array();
				$record=$row;
				$record['periodegaji']=$periodegaji;
				$record['nopengajuan']=$r_nopengajuan;
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
			
			if($ok)
				return array(false,'Generate '.static::label.' berhasil,'.$insert.' data baru ditambahkan,'.$update.' data digenerate ulang');
			else
				return array(true,'Generate '.static::label.' Gagal');
		}
		
		
		
		function convertPeriodeGaji($periodegaji){
			$tahun=substr($periodegaji,0,4);
			$bulan=substr($periodegaji,4,2);
			
			return Date::indoMonth((int)$bulan).' '.$tahun;
		}
		
		function getRowData($conn,$row) {
			
			$where=" tglkuliah='".$row['tglkuliah']."' and perkuliahanke='".$row['perkuliahanke']."' and periode='".$row['periode']."' 
					and thnkurikulum='".$row['thnkurikulum']."' and kodeunit='".$row['kodeunit']."' and kodemk='".$row['kodemk']."' 
					and kelasmk='".$row['kelasmk']."' and jeniskuliah='".$row['jeniskuliah']."' and kelompok='".$row['kelompok']."'";
			$sql="select nipasisten,isvalid from ".static::table()." where $where order by isvalid asc";
			
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
			$nourut='HA/'.$periode.'/'.$kodeunit.'/'.$bulanpengajuan.'/'.$tahunpengajuan.'/'.str_pad($urut,3,'0',STR_PAD_LEFT);
		
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
			$sql.=" join gate.ms_unit j on j.kodeunit=g.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			$sql.=" where g.periode='$periode' and g.periodegaji='$periodegaji' and g.isvalid=-1";
			
			return Query::arrQuery($conn,$sql);
		}
		
		
	
	}
?>
