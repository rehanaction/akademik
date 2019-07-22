<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDetailKelas extends mModel {
		const schema = 'akademik';
		const table = 'ak_detailkelas';
		const order = 'tglpertemuan';
		const key = 'iddetailkelas';
		const label = 'Detail Kelas Perkuliahan';
		
		function getArray($conn,$key,$col=''){
			if(empty($col))
				$col='thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok';
				
			$data = $conn->GetArray("select * from ".static::table()." where ".static::getCondition($key,$col)." order by ".static::order."");
			return $data;
		}
		
		function getArrayJadwal($conn,$key,$col=''){
			if(empty($col))
				$col='d.thnkurikulum,d.kodemk,d.kodeunit,d.periode,d.kelasmk,d.jeniskul,d.kelompok';
				
			$data = $conn->GetArray("select d.*,k.tglkuliahrealisasi,waktumulairealisasi,k.waktuselesairealisasi,k.koderuangrealisasi from ".static::table()." d
					join ".static::table('ak_kuliah')." k on d.tglpertemuan=k.tglkuliah and d.pertemuan=k.perkuliahanke and d.periode=k.periode 
					and d.thnkurikulum=k.thnkurikulum and d.kodeunit=k.kodeunit and d.kodemk=k.kodemk and d.kelasmk=k.kelasmk and d.jeniskul=k.jeniskuliah and d.kelompok=k.kelompok
					 where ".static::getCondition($key,$col)." order by ".static::order."");
			return $data;
		}
		function getArrCekKres($conn,$key,$col='',$kelompok=''){
			if(empty($col))
				$col='thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok';
			$sql="select * from ".static::table()." where ".static::getCondition($key,$col);
			if(!empty($kelompok))
				$sql.=" and case when jeniskul='P' then kelompok='$kelompok' else 1=1 end";
			$sql.=" order by ".static::order;	
			$data = $conn->GetArray($sql);
			return $data;
		}
		function insertNonBlock($conn,$a_input,$record,$r_key){
			$arr_jadwal=self::getArrJadwal($a_input,$record,$r_key);
			$jumtgl=0;
			if($record['tgljadwal1']!='null') $jumtgl++;
			if($record['tgljadwal2']!='null') $jumtgl++;
			if($record['tgljadwal3']!='null') $jumtgl++;
			if($record['tgljadwal4']!='null') $jumtgl++;
			foreach($arr_jadwal as $pert=>$row){
				$record=$row;
				//seleksi jumlah minggu yang diloncati
				/*if($jumtgl==1)
					//$jump=2;
				else
					//$jump=3;
				//mulai dari pert 8 dia loncat 2 minggu
				/*if($pert==8){
					//definisi tanggalnya
					
					$tglpertemuan=$record['tglpertemuan'];
					$tmp_tgl=date('Y-m-d', strtotime("$tglpertemuan +$jump week"));
					$day=$jumtgl-1;
					$record['tglpertemuan']=date('Y-m-d', strtotime("$tmp_tgl -$day day"));
				}else if($pert>8){
					//definisi tanggalnya
					
					$tglpertemuan=$record['tglpertemuan'];
					$tmp_tgl=date('Y-m-d', strtotime("$tglpertemuan +$jump week"));
					$day=($jumtgl-1)*2;
					$record['tglpertemuan']=date('Y-m-d', strtotime("$tmp_tgl -$day day"));
				}*/
				if($pert>=8){
					//definisi tanggalnya
					$record['tglpertemuan']=$record['tglpertemuan'];
					//$tglpertemuan=$record['tglpertemuan'];
					//$record['tglpertemuan']=date('Y-m-d', strtotime("$tglpertemuan +$jump week"));
					//$record['tglpertemuan']=date('Y-m-d', strtotime($tglpertemuan));
				}
				if($pert==15)
				//	break;
				$cek=self::cekKresJadwal($conn,$record['periode'],$record['tglpertemuan'],$record['koderuang'],$record['jammulai'],$record['jamselesai']);
				if(!empty($cek['namamk'])){
					$err=array(true,'Kres dengan Prodi '.$cek['prodi'].', Mata kuliah '.$cek['kodemk'].' '.$cek['namamk'].'('.$cek['kelasmk'].') tgl '.Date::indoDate($cek['tglpertemuan'],false).' '.CStr::formatJam($cek['jammulai']).'-'.CStr::formatJam($cek['jamselesai']).' Ruang '.$cek['koderuang']);
					break;
				}else{
					$err = static::insertRecord($conn,$record,true);
				}
			}
			
			return $err;
			
		}
		function getArrJadwal($a_input,$record,$r_key){
			$jumtgl=0;
			if($record['tgljadwal1']!='null') $jumtgl++;
			if($record['tgljadwal2']!='null') $jumtgl++;
			if($record['tgljadwal3']!='null') $jumtgl++;
			if($record['tgljadwal4']!='null') $jumtgl++;
			$batas=$jumtgl>0?ceil((16/(int)$jumtgl)):0;
			
			$arr_jadwal=array();
			//jadwal 1
			if($record['tgljadwal1']!='null'){
				$record['kelompok']=1;
				$tgl_pertemuan=$record['tgljadwal1'];
				$pert=1;
				for($i=1;$i<=$batas;$i++){
					$record['pertemuan']=$pert;
					$record['jeniskul']='K';
						$pert+=$jumtgl;
					
					if($i>1){
						$record['tglpertemuan']=date('Y-m-d', strtotime("$tgl_pertemuan +1 week"));
						$tgl_pertemuan=$record['tglpertemuan'];
					}else{
						$record['tglpertemuan']=$record['tgljadwal1'];
					}
					$arr_jadwal[$record['pertemuan']]=$record;
				}
			}
			//jadwal 2
			
			if($record['tgljadwal2']!='null'){
				$record['kelompok']=1;
				$tgl_pertemuan2=$record['tgljadwal2'];
				$record['jammulai']=$record['jammulai2'];
				$record['jamselesai']=$record['jamselesai2'];
				$record['nohari']=$_POST['nohari2'];
				
				$pert=2;
				for($j=1;$j<=$batas;$j++){
					$record['pertemuan']=$pert;
					$record['jeniskul']='K';
					$pert+=$jumtgl;
					if($j>1){
						$record['tglpertemuan']=date('Y-m-d', strtotime("$tgl_pertemuan2 +1 week"));
						$tgl_pertemuan2=$record['tglpertemuan'];
					}else{
						$record['tglpertemuan']=$record['tgljadwal2'];
					}
					$arr_jadwal[$record['pertemuan']]=$record;
				}	
			}
			
			//jadwal 3
			
			if($record['tgljadwal3']!='null'){
				$record['kelompok']=1;
				$tgl_pertemuan3=$record['tgljadwal3'];
				$record['jammulai']=$record['jammulai3'];
				$record['jamselesai']=$record['jamselesai3'];
				$record['nohari']=$_POST['nohari3'];
				
				$pert=3;
				for($k=1;$k<=$batas;$k++){
					$record['pertemuan']=$pert;
					$record['jeniskul']='K';
					$pert+=$jumtgl;
					if($k>1){
						$record['tglpertemuan']=date('Y-m-d', strtotime("$tgl_pertemuan3 +1 week"));
						$tgl_pertemuan3=$record['tglpertemuan'];
					}else{
						$record['tglpertemuan']=$record['tgljadwal3'];
					}
					$arr_jadwal[$record['pertemuan']]=$record;
				}	
			}
			
			//jadwal 4
			
			if($record['tgljadwal4']!='null'){
				$record['kelompok']=1;
				$tgl_pertemuan4=$record['tgljadwal4'];
				$record['jammulai']=$record['jammulai4'];
				$record['jamselesai']=$record['jamselesai4'];
				$record['nohari']=$_POST['nohari4'];
				
				$pert=4;
				for($l=1;$l<=$batas;$l++){
					$record['pertemuan']=$pert;
					$record['jeniskul']='K';
					$pert+=$jumtgl;
					if($l>1){
						$record['tglpertemuan']=date('Y-m-d', strtotime("$tgl_pertemuan4 +1 week"));
						$tgl_pertemuan4=$record['tglpertemuan'];
					}else{
						$record['tglpertemuan']=$record['tgljadwal4'];
					}
					$arr_jadwal[$record['pertemuan']]=$record;
				}	
			}
			
			ksort($arr_jadwal);
			return $arr_jadwal;
		}
		function insertDetailPraktikum($conn,$a_input,$record,$r_key){
			if($record['tglawalkuliah']!='null'){
			$tgl_pertemuan=$record['tglawalkuliah'];
			for($i=1;$i<=14;$i++){
				$record['pertemuan']=$i;
				$record['jeniskul']='P';
				if($i>1){
					if($i==8)
						$record['tglpertemuan']=date('Y-m-d', strtotime("$tgl_pertemuan +3 week"));
					else
						$record['tglpertemuan']=date('Y-m-d', strtotime("$tgl_pertemuan +1 week"));
					$tgl_pertemuan=$record['tglpertemuan'];
				}else{
					$record['tglpertemuan']=$record['tglawalkuliah'];
				}
				$cek=self::cekKresJadwal($conn,$record['periode'],$record['tglpertemuan'],$record['koderuang'],$record['jammulai'],$record['jamselesai']);
				
				if(!empty($cek['namamk'])){
					$err=array(true,'Kres dengan Prodi '.$cek['prodi'].', Mata kuliah '.$cek['kodemk'].' '.$cek['namamk'].'('.$cek['kelasmk'].') tgl '.Date::indoDate($cek['tglpertemuan'],false).' '.CStr::formatJam($cek['jammulai']).'-'.CStr::formatJam($cek['jamselesai']).' Ruang '.$cek['koderuang']);
					break;
				}else{
					$sql="select 1 from ".static::table()." 
						where ".static::getCondition($r_key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok')." and pertemuan='$i'";
					$data=$conn->GetOne($sql);
					if(!empty($data))
						$err = static::updateRecord($conn,$record,$r_key,true,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok');
					else
						$err = static::insertRecord($conn,$record,true);
				}
			}
			}
			
			return $err;
		}
		function deleteBlock($conn,$key){
			$condition = static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok'); 
			$table = static::table();
			$sql = "delete from $table where $condition";
			$conn->Execute($sql);
			return $conn->ErrorNo();
		}
		function cekKresJadwal($conn,$periode,$tgl,$ruang,$start,$end){
			$thnspa=(int)substr($periode,0,4)+1;
			$periodespa=(string)$thnspa.'0';
			$str="select m.kodemk,m.namamk,j.kelasmk,j.tglpertemuan,j.koderuang,j.jammulai,j.jamselesai,u.namaunit as prodi
					from ".static::table()." j 
					join ".static::table('ak_matakuliah')." m using (kodemk,thnkurikulum)
					join gate.ms_unit u on u.kodeunit=j.kodeunit
					where (j.periode='$periode' or j.periode='$periodespa') and j.tglpertemuan='$tgl' and j.koderuang='$ruang' and 
					((j.jammulai::integer between $start and $end-1) or (j.jamselesai::integer between $start+1 and $end))
					 limit 1";
			$data=$conn->GetRow($str);
			return $data;
			
		}
		function cekKresJadwalMhs($conn,$periode,$kodeunit,$tgl,$nim,$start,$end){
			
			$str = "select m.kodemk,m.namamk,j.kelasmk,j.tglpertemuan,j.koderuang,j.jammulai,j.jamselesai
					from ".static::table('ak_krs')." k 
					join ".static::table()." j on j.kodemk=k.kodemk and j.kelasmk=k.kelasmk and j.kodeunit=k.kodeunit 
					and case when j.jeniskul='P' then j.kelompok=k.kelompok_prak end
					join ".static::table('ak_matakuliah')." m on m.kodemk=j.kodemk and m.thnkurikulum=j.thnkurikulum
					where k.nim = '$nim' and k.periode = '$periode' and k.kodeunit='$kodeunit' and j.tglpertemuan='$tgl' and 
					((j.jammulai::integer between $start and $end-1) or (j.jamselesai::integer between $start+1 and $end))
					limit 1";
			$data=$conn->GetRow($str);
			return $data;
		}
		function ubahDetail($rec,$post){
			$post=CStr::cArrayNull($post);
			if($rec['tgljadwal1']!=$post['old_tgljadwal1'] or $rec['jammulai']!=$post['old_jammulai'] or $rec['jamselesai']!=$post['old_jamselesai'] or $rec['koderuang']!=$post['old_koderuang']){
				return true;
			}else if($rec['tgljadwal2']!=$post['old_tgljadwal2'] or $rec['jammulai2']!=$post['old_jammulai2'] or $rec['jamselesai2']!=$post['old_jamselesai2'] or $rec['koderuang2']!=$post['old_koderuang2']){
				return true;
			}else if($rec['tgljadwal3']!=$post['old_tgljadwal3'] or $rec['jammulai3']!=$post['old_jammulai3'] or $rec['jamselesai3']!=$post['old_jamselesai3'] or $rec['koderuang3']!=$post['old_koderuang3']){
				return true;
			}else if($rec['tgljadwal4']!=$post['old_tgljadwal4'] or $rec['jammulai4']!=$post['old_jammulai4'] or $rec['jamselesai4']!=$post['old_jamselesai4'] or $rec['koderuang4']!=$post['old_koderuang4']){
				return true;
			}else if($rec['tglawalkuliah']!=$post['old_tglawalkuliah'] or $rec['jammulai']!=$post['old_jammulai'] or $rec['jamselesai']!=$post['old_jamselesai'] or $rec['koderuang']!=$post['old_koderuang']){
				return true;
			}else{
				return false;
			}
		}
		
	}
?>
