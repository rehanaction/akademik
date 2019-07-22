<?php
	// model perkuliahan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJadwalUjian extends mModel {
		const schema = 'akademik';
		const table = 'ak_jadwalujian';
		const sequence = 'ak_jadwalujian_idjadwalujian_seq';
		const order = 'tglujian, kelompok';
		const key = 'idjadwalujian';
		const label = 'Jadwal Ujian';
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'kelas':
					require_once(Route::getModelPath('kelas'));
					
					return mKelas::getCondition($key, 'thnkurikulum , kodemk , kodeunit , periode , kelasmk, jeniskuliah, kelompokkul');
				case 'periode': return "k.periode = '$key'";
				case 'tglkuliah': return "k.tglkuliah = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		
		function dataQuery($key) {
		$sql = "select j.*,u1.username||' - '||u1.userdesc as pengawas1, 
					u2.username||' - '||u2.userdesc as pengawas2 
					from ".static::table()." j 
					left join gate.sc_user u1 on u1.username=j.nippengawas1
					left join gate.sc_user u2 on u2.username=j.nippengawas2
					where ".static::getCondition($key,'','j');
			
			return $sql;
		}
		function getKelompok(){
			return array('1'=>'1','2'=>'2','3'=>'3','4'=>'4','5'=>'5','6'=>'6','7'=>'7','8'=>'8','9'=>'9','10'=>'10');
		}
		
		function genPesertaUjian($conn,$jenisujian,$key_kelas){
			require_once(Route::getModelPath('kelas'));
			require_once(Route::getModelPath('pesertaujian'));
			list(,,,,,$jeniskuliah,$kelompok)=explode('|',$key_kelas);
			if($jeniskuliah=='P')
				$peserta = mKelas::getDataPeserta($conn,$key_kelas,$kelompok);
			else
				$peserta = mKelas::getDataPeserta($conn,$key_kelas);
			$cond=mKelas::getCondition($key_kelas,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskuliah,kelompokkul');
			$jadwal=$conn->GetArray("select idjadwalujian from ".static::table()."  where ".$cond." and jenisujian='$jenisujian'");
			$arr_jadwal=array();
			foreach($jadwal as $row_jadwal){
				$arr_jadwal[]=$row_jadwal['idjadwalujian'];
			}
			$anggota=ceil(count($peserta)/count($jadwal));
			$arr_peserta=array();
			$i=0;
			foreach($peserta as $row){
				$j++;
				$arr_peserta[$arr_jadwal[$i]][$j]=$row['nim'];
				if($j==$anggota){
					$j=0;
					$i++;
				}
			}
			
			//print_r($arr_peserta);die();
			$conn->BeginTrans();
			$ok=true;
			$sukses=0;
			$lewat=0;
			$a_nim='';
			foreach($arr_peserta as $idujian=>$p_ujian){
				$ok=$conn->Execute("delete from ".static::table('ak_pesertaujian')." where idjadwalujian='$idujian'");
				if(!$ok)
					break;
				foreach($p_ujian as $nim){
					/*$sql="select 1 from ".static::table('ak_pesertaujian')." p join ".static::table()." j using (idjadwalujian) 
							where ".$cond." and p.nim='$nim' and p.idjadwalujian='$idujian'";
					$cek=$conn->GetOne($sql);
					if(empty($cek)){*/
						$record=array();
						$record['nim']=$nim;
						$record['idjadwalujian']=$idujian;
						
						list($p_posterr,$p_postmsg)=mPesertaUjian::insertRecord($conn,$record,true);
						if(!$p_posterr){
							$ok=true;
							$sukses++;
						}else if ($p_posterr){
							$ok=false;
							$a_nim=$nim;
							break;
						}
					/*}else
						$lewat++;*/
				}
			}
			$conn->CommitTrans($ok);
			if($ok)
				$msg=$sukses.' Data Berhasil Digenerate, '.$lewat.' Data Tidak Digenerate';
			else
				$msg='Gagal generate,'.(!empty($a_nim)?'Proses Terhenti pada NIM '.$a_nim:'');
			return array(true,$msg);
		}
		function deletejadwal($conn,$key){
			$delpeserta=$conn->Execute("delete from ".static::table('ak_pesertaujian')." where idjadwalujian='$key'");
			if($delpeserta)
				list($p_posterr,$p_postmsg) = static::delete($conn,$key);
				
			return array($p_posterr,$p_postmsg);
		}
		
		function getXvalue($conn,$key){
			$sql="select nippengawas1,nippengawas2 from ".static::table()." where ".static::getCondition($key);
			$row=$conn->GetRow($sql);
			$a_data=array();
			for($i=1;$i<=2;$i++){
				$sqld="select username,username||' - '||userdesc as nama from gate.sc_user where username='".$row['nippengawas'.$i]."'";
				$data=$conn->GetRow($sqld);
				$a_data[$data['username']]=$data['nama'];
			}
			
			return $a_data;
		}
		
		
		function getDataPengawas($conn,$nodosen){
			$sql="select  * from akademik.ms_pegawaipenunjang where ";
			if(is_array($nodosen))
				$sql.=" nopegawai in ('".implode("','",$nodosen)."')";
			else if(!is_array($nodosen))
				$sql.=" nopegawai='$nodosen'";
				
			$data=$conn->GetArray($sql);
			$a_pegawai=array();
			foreach($data as $row){
				
				$a_pegawai[$row['nopegawai']]=array(
												'nodosen'=>$row['nopegawai'],
												'norekening'=>$row['norekening'],
												'norekeninghonor'=>$row['norekening'],
												'npwp'=>$row['nonpwp'],
												'anrekeninghonor'=>$row['namarekening'],
												'biatrans'=>$row['biatrans'],
												'pph'=>$row['pajak']);
			}
			
			
			return $a_pegawai;
		}
	}
?>
