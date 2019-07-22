<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mHonorPengawas extends mModel {
		const schema = 'honorakademik';
		const table = 'hn_pengawasujian';
		const order = 'idpengawasujian';
		const key = 'idpengawasujian';
		const label = 'Honor Pengawas Ujian';
		
		// mendapatkan kueri list
		function listQuery() {
			
			$sql="select h.*,ju.tglujian,ju.kodemk,ju.kelasmk,ju.jenisujian,ju.kelompok,kr.namamk,
				p.userdesc As namadosen
				from ".static::table()." h
				join akademik.ak_jadwalujian ju using (idjadwalujian)
				join akademik.ak_kelas kl using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
				join akademik.ak_kurikulum kr on kr.kodeunit=ju.kodeunit and kr.thnkurikulum=ju.thnkurikulum and kr.kodemk=ju.kodemk
				join gate.sc_user p on p.username::text=h.nipdosen::text
				join gate.ms_unit j on j.kodeunit=ju.kodeunit";
			
		
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
					return "ju.jenisujian='$key'";
				case 'periode' :
					return "ju.periode='$key'";
				case 'unit' :
					return "ju.kodeunit='$key'";
				case 'sistemkuliah' :
					return "kl.sistemkuliah='$key'";	
				default:
					return parent::getListFilter($col,$key);
			}
		}
		 
		function genGaji($conn,$unit,$periode,$periodegaji,$jenisujian,$sistemkuliah){
			require_once(Route::getModelPath('ratepengawas'));
			$a_rate=mRatePengawas::getArray($conn);
			
			$q_kuliah="select j.idjadwalujian,j.nippengawas1,j.nippengawas2,j.tglujian,k.sks,kl.sistemkuliah
						from akademik.ak_jadwalujian j 
						join akademik.ak_kelas kl using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
						join akademik.ak_kurikulum k using (thnkurikulum, kodeunit, kodemk)
						where (j.nippengawas1 is not null or j.nippengawas2 is not null) and 
						j.kodeunit='$unit' and j.periode='$periode' and j.jenisujian='$jenisujian'
						and kl.sistemkuliah='$sistemkuliah'";
			$datakuliah=$conn->GetArray($q_kuliah);
			
			$conn->BeginTrans();
			$ok=true;
			$insert=0;
			$update=0;
			$r_nopengajuan=static::getNopengajuan($conn,$unit,$periode,$periodegaji,$jenisujian);
			foreach($datakuliah as $row){
				//for untuk pengawas 1 dan pengawas 2
				for($i=1;$i<=2;$i++){
					if(!empty($row['nippengawas'.$i])){
						$nohariujian = date('N',strtotime($row['tglujian']));
						$jenispengawas=static::getJenisPegawas($conn,$row['nippengawas'.$i]);
						$keyrate=$row['sistemkuliah'].'|'.$row['sks'].'|'.$jenispengawas.'|'.$nohariujian;
						//echo $keyrate;die();
						$record=array();
						$record['idjadwalujian']=$row['idjadwalujian'];
						$record['nipdosen']=$row['nippengawas'.$i];
						$record['periodegaji']=$periodegaji;
						$record['honor']=$a_rate[$keyrate];
						$record['nopengajuan']=$r_nopengajuan;
						$record['isvalid']=-1;
						
						$keyhonor=$record['idjadwalujian'];
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
			
			$where=" idjadwalujian=".$row['idjadwalujian']." and nipdosen='".$row['nipdosen']."'";
			$sql="select idpengawasujian,isvalid from ".static::table()." where $where order by isvalid asc";
			
			$data = $conn->GetRow($sql);
			
			return $data;
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
				$nourut='PU/'.$periode.'/'.$a_jenisujian[$jenisujian].'/'.$kodeunit.'/'.$bulanpengajuan.'/'.$tahunpengajuan.'/'.str_pad($urut,3,'0',STR_PAD_LEFT);
			
			return $nourut;
		}
		
		function getJenisPegawas($conn,$idpegawai){
			$sql="select j.jenispengawas from sdm.ms_pegawai p
					JOIN sdm.ms_jenispeg j on j.idjenispegawai=p.idjenispegawai
					 where p.idpegawai::text='$idpegawai'";
			$row=$conn->GetRow($sql);
			
			if(empty($row))
				return 'P';
			else
				return $row['jenispengawas'];
			
		}
		
		function listNoPembayaran($conn,$periode,$periodegaji,$kodeunit=''){
			$sql="select distinct g.nopembayaran as kode,g.nopembayaran as nomor from 
				".static::table()." g
				join akademik.ak_jadwalujian ju using (idjadwalujian)";
			if(!empty($kodeunit)){
				require_once(Route::getModelPath('unit'));
				$unit = mUnit::getData($conn,$kodeunit);
				$sql.=" join gate.ms_unit j on j.kodeunit=ju.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
			}
			$sql.=" where ju.periode='$periode' and g.periodegaji='$periodegaji'";
			
			return Query::arrQuery($conn,$sql);
		}
	
	}
?>
