<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mIjinSeminar extends mModel {
		const schema = 'seminar';
		const table = 'sm_ijinseminar';
		const order = 'idijinseminar';
		const sequence = 'sm_ijinseminar_idijinseminar_seq';
		const key = 'idijinseminar';
		const label = 'Ijin Seminar';
		const uptype = 'ijinseminar';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select * , 
					case typepengajuijin
				         when 'P' then nippengajuijinseminar 
				         when 'M' then nimpengajuijinseminar 
				      	 end as pengaju 
					from ".static::table();
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select b.*
					from ".static::table()." b
					where ".static::getCondition($key);
			
			return $sql;
		}

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
				$path = $conf['upload_dir'].'ijinseminar/'.$tkey;
				
				$ok = move_uploaded_file($file['tmp_name'],$path);
			}
			else
				$ok = false;
			
			$conn->CommitTrans($ok);
			
			$err = Query::isErr($ok);
			$msg = 'Upload data '.$label.' '.($ok ? 'berhasil' : 'gagal');
			
			
			return array($err,$msg);
		}
		
		
	}
?>
