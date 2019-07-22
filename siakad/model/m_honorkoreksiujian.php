<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorKoreksiUjian extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_koreksiujian';
		const order = 'h.nipdosen, h.kodemk,h.kelasmk';
		const key = 'idkoreksiujian';
		const label = 'Honor Naskah Ujian';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select h.*,m.namamk,kl.sistemkuliah,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen
				from ".static::table()." h
				join akademik.ak_kelas kl using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
				join akademik.ak_matakuliah m on m.kodemk=h.kodemk and m.thnkurikulum=h.thnkurikulum
				join sdm.ms_pegawai p on p.idpegawai=h.nipdosen
				join gate.ms_unit j on j.kodeunit=h.kodeunit";
			
		
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
				case 'jenisujian' :
					return "h.jenisujian='$key'";
				case 'periode' :
					return "h.periode='$key'";
				case 'unit' :
					return "h.kodeunit='$key'";	
				case 'sistemkuliah' :
					return "kl.sistemkuliah='$key'";	
				default:
					return parent::getListFilter($col,$key);
			}
		}
		 
		function genGaji($conn,$unit,$periode,$periodegaji,$jenisujian,$jeniskuliah,$sistemkuliah){
			require_once(Route::getModelPath('kelas'));
			require_once(Route::getModelPath('ratehonor'));
			$a_rate=mRateHonor::getArray($conn);
			
			
			$bulan=substr($periodegaji,4,2);
			$tahun=substr($periodegaji,0,4);
			$q_ujian="select distinct kl.sistemkuliah,m.nipdosen,k.periode,k.thnkurikulum,k.kodeunit,k.kodemk,k.kelasmk,k.jenisujian,k.jeniskuliah,k.kelompokkul 
						from akademik.ak_jadwalujian k
						join akademik.ak_kelas kl using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
						join akademik.ak_mengajar m on m.periode=k.periode and m.thnkurikulum=k.thnkurikulum and m.kodeunit=k.kodeunit
						and m.kodemk=k.kodemk and m.kelasmk=k.kelasmk and m.jeniskul=k.jeniskuliah and m.kelompok=k.kelompok and m.ispjmk=1 
						where k.kodeunit='$unit' and k.periode='$periode' and k.jenisujian='$jenisujian' and k.jeniskuliah='$jeniskuliah' and kl.sistemkuliah='$sistemkuliah'";
			$dataujian=$conn->GetArray($q_ujian);
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			$r_nopengajuan=static::getNopengajuan($conn,$unit,$periode,$periodegaji,$jenisujian);
			foreach($dataujian as $row){
				$keyrate='KU|'.$row['sistemkuliah'];
				
				$record=array();
				$record=$row;
				$record['kelompok']=$row['kelompokkul'];
				$record['periodegaji']=$periodegaji;
				$record['nopengajuan']=$r_nopengajuan;
				$record['isvalid']=-1;
				$record['idjadwalujian']=static::getIdjadwalUjian($conn,$record);
					
				$keykrs=mKelas::getKeyRow($record);
				$where=mKelas::getCondition($keykrs);
				$jum_krs=$conn->GetOne("select count(nim) from akademik.ak_krs where $where");
				$record['jumlahpeserta']=$jum_krs;
				$record['honor']=$a_rate[$keyrate]*$jum_krs;
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
		
		
		
		function convertPeriodeGaji($periodegaji){
			$tahun=substr($periodegaji,0,4);
			$bulan=substr($periodegaji,4,2);
			
			return Date::indoMonth((int)$bulan).' '.$tahun;
		}
		
		function getRowData($conn,$row) {
			require_once(Route::getModelPath('kelas'));
			$keykelas=mKelas::getKeyRow($row);
			$where=mKelas::getCondition($keykelas);
			$where.=" and jeniskuliah='".$row['jeniskuliah']."' and kelompok='".$row['kelompok']."' and jenisujian='".$row['jenisujian']."'";
			$sql="select idkoreksiujian,isvalid from ".static::table()." where $where order by isvalid asc";
			
			$data = $conn->GetRow($sql);
			
			return $data;
		}
		
		function getIdjadwalUjian($conn,$row) {
			require_once(Route::getModelPath('jadwalujian'));
			require_once(Route::getModelPath('kelas'));
			$keykelas=mKelas::getKeyRow($row);
			$where=static::getCondition($keykelas,'thnkurikulum,kodemk,kodeunit,periode,kelasmk');
			$where.=" and jeniskuliah='".$row['jeniskuliah']."' and kelompokkul='".$row['kelompok']."' and jenisujian='".$row['jenisujian']."'";
			$sql="select idjadwalujian from akademik.ak_jadwalujian where $where";
			
			$id = $conn->GetOne($sql);
			
			return $id;
		}
		
		function getNopengajuan($conn,$kodeunit,$periode,$periodepengajuan,$jenisujian){
				require_once(Route::getModelPath('combo'));
				$periode=Akademik::getNamaPeriode($periode,true);
				$a_jenisujian=mCombo::getJenisUjian();
				$bulanpengajuan=substr($periodepengajuan,4,2);
				$tahunpengajuan=substr($periodepengajuan,0,4);
				$sql="select max(substr(nopengajuan,length(nopengajuan)-2,3)) from ".static::table()." 
				where substr(nopengajuan,length(nopengajuan)-7,4)='$tahunpengajuan'";
				$max=$conn->GetOne($sql);
				$urut=(int)$max+1;
				$nourut='KU/'.$periode.'/'.$a_jenisujian[$jenisujian].'/'.$kodeunit.'/'.$bulanpengajuan.'/'.$tahunpengajuan.'/'.str_pad($urut,3,'0',STR_PAD_LEFT);
			
			return $nourut;
		}
		
		
	
	}
?>
