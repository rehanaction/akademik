<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('forum'));
	
	class mTugas extends mForum {
		const schema = 'elearning';
		const table = 'el_tugas';
		const sequence = 'el_tugas_idtugas_seq';
		const order = 'idtugas';
		const key = 'idtugas';
		const label = 'tugas';
		const uptype = 'tugas';
		
		// hapus data, transaksi di dalam
		function delete($conn,$key) {
			global $conf;
			
			$conn->BeginTrans();
			
			$err = Query::qDelete($conn,static::table(),static::getCondition($key));
			
			// hapus file
			if(!$err) {
				$file = $conf['upload_dir'].self::uptype.'/'.$key;
				if(file_exists($file)){
					$ok = unlink($file);
					if(!$ok) $arr = array(true,'Penghapusan '.self::label.' gagal');
				}else{
					$ok=true;
				}
			}
			else
				$ok = false;
			
			if(empty($arr))
				$arr = static::deleteStatus($conn);
			
			$conn->CommitTrans($ok);
			
			return $arr;
		}
		
		// save record pengumpulan
		function saveRecordPengumpulan($conn,$record,$key,$nim,&$tkey,$status=false) {
			$table = 'el_tugasdikumpulkan';
			
			// cek pengumpulan
			$sql = "select idtugasdikumpulkan, nilaitugas from ".static::table($table)."
					where ".static::getCondition($key)." and nim = '$nim'";
			$a_cek = $conn->GetRow($sql);
			
			// pengecekan nilai tugas
			if($record['nilaitugas'] == 'null' and empty($a_cek['idtugasdikumpulkan'])) {
				if($status)
					return array(false,'Penyimpanan pengumpulan tugas berhasil');
				else
					return false;
			}
			
			if(isset($record['nilaitugas'])) {
				if(strval($a_cek['nilaitugas']) == '')
					$a_cek['nilaitugas'] = 'null';
				
				if($record['nilaitugas'] != $a_cek['nilaitugas'])
					$record['waktupostingnilai'] = date('Y-m-d H:i:s');
			}
			
			if(empty($a_cek['idtugasdikumpulkan'])) {
				$record['idtugas'] = $key;
				$record['nim'] = $nim;
				
				$err = Query::recInsert($conn,$record,static::table($table));
				if(!$err)
					$tkey = self::getLastValuePengumpulan($conn);
				
				if($status)
					return static::insertStatus($conn,'','pengumpulan tugas',$table);
				else
					return $err;
			}
			else {
				$tkey = $a_cek['idtugasdikumpulkan'];
				$err = Query::recUpdate($conn,$record,static::table($table),static::getCondition($key,'idtugasdikumpulkan'));
				
				if($status)
					return static::updateStatus($conn,'','pengumpulan tugas',$table);
				else
					return $err;
			}
		}
		
		// upload tugas
		function uploadTugas($conn,$key,$nim,$file,$keterangan) {
			global $conf;
			
			// cek file
			$err = false;
			if(!empty($file['error'])) {
				$err = true;
				$msg = Route::uploadErrorMsg($file['error']);
			}
			
			if(!$err) {
				$conn->BeginTrans();
				
				$record = array();
				$record['isitugas'] = $keterangan;
				$record['filetugasdikumpulkan'] = CStr::cStrNull($file['name']);
				$record['waktuposting'] = date('Y-m-d H:i:s');
				
				$err = self::saveRecordPengumpulan($conn,$record,$key,$nim,$tkey);
				
				if(!$err) {
					$path = $conf['upload_dir'].'tugaskumpul/'.$tkey;
					
					$ok = move_uploaded_file($file['tmp_name'],$path);
				}
				else
					$ok = false;
				
				$conn->CommitTrans($ok);
				
				$err = Query::isErr($ok);
				$msg = 'Upload data '.$label.' '.($ok ? 'berhasil' : 'gagal');
			}
			
			return array($err,$msg);
		}
		
		// mendapatkan data terakhir sequence pengumpulan tugas
		function getLastValuePengumpulan($conn) {
			$sql = 'select last_value from '.static::sequence('el_tugasdikumpulkan_idtugasdikumpulkan_seq');
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan jumlah tugas belum dikumpulkan
		function getNumUnsubmit($conn,$user='') {
			$periode = Akademik::getPeriode();
			if(empty($user))
				$user = Modul::getUserName();
			
			$sql = "select count(t.*) from ".static::table()." t
					join akademik.ak_krs k using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					left join ".static::table('el_tugasdikumpulkan')." s on s.idtugas = t.idtugas and s.nim = '$user'
					where t.periode = '$periode' and k.nim = '$user' and s.nim is null";
			
			return $conn->GetOne($sql);
		}
		
		// mendapatkan data
		function getDataPengumpulan($conn,$key) {
			$sql = "select * from ".static::table('el_tugasdikumpulkan')."
					where ".static::getCondition($key,'idtugasdikumpulkan');
			
			return $conn->GetRow($sql);
		}
		
		// data pengumpulan tugas mahasiswa
		function getListSubmitMhs($conn,$nim,$kelas) {
			$sql = "select d.* from ".static::table('el_tugasdikumpulkan')." d
					join ".static::table('el_tugas')." t on d.idtugas = t.idtugas
					where d.nim = '$nim' and ".mKelas::getCondition($kelas);
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['idtugas']] = true;
			
			return $data;
		}
		
		function getListSubmit($conn,$key) {
			$sql = "select *, m.nama from ".static::table('el_tugasdikumpulkan')." t
					join akademik.ms_mahasiswa m on m.nim = t.nim
					where ".static::getCondition($key)." order by waktuposting desc";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['nim']] = $row;
			
			return $data;
		}
	}
?>
