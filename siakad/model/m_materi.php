<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('forum'));
	
	class mMateri extends mForum {
		const schema = 'elearning';
		const table = 'el_materi';
		const sequence = 'el_materi_idmateri_seq';
		const order = 'idmateri';
		const key = 'idmateri';
		const label = 'materi';
		const uptype = 'materi';
		
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
	}
?>
