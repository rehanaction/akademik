<?php
	// model syarat yudisium mahasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mCekSyaratYudisium extends mModel {
		const schema = 'akademik';
		const table = 'ak_ceksyaratyudisium';
		const order = 'idsyaratyudisium';
		const key = 'nim,idsyaratyudisium';
		const label = 'syarat yudisium mahasiswa';
		
		// mendapatkan data list
		function getListMhsPeriode($conn,$nim,$periodewisuda) {
			$sql = "select m.idsyaratyudisium from ".static::table()." m
					join ".static::table('ak_syaratyudisium')." s on
						m.idsyaratyudisium = s.idsyaratyudisium and s.idyudisium = '$periodewisuda'
					where m.nim = '$nim'";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[$row['idsyaratyudisium']] = true;
			
			return $data;
		}
		
		// simpan syarat yudisium mahasiswa
		function saveSyaratMahasiswa($conn,$nim,$arrid) {
			$conn->BeginTrans();
			
			// hapus dulu
			$err = Query::qDelete($conn,static::table(),"nim = '$nim'");
			
			// baru masukkan
			if(!$err) {
				$record = array();
				$record['nim'] = $nim;
				
				foreach($arrid as $t_id) {
					$record['idsyaratyudisium'] = $t_id;
					
					$err = static::insertRecord($conn,$record);
					if($err) break;
				}
			}
			
			$err = Query::boolErr($err);
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return $err;
		}
		
		// pengecekan mahasiswa
		function cekPrasyaratMahasiswa($conn,$nim) {
			$err = 0;
			
			$sql = "select 1 from ".static::table('ak_transkrip')." t
					join ".static::table('ak_matakuliah')." m using (thnkurikulum,kodemk)
					where t.nim = '$nim' and t.lulus <> 0 and m.tipekuliah = 'A'";
			$islulus = $conn->GetOne($sql);
			
			if(empty($islulus))
				$err = -1;
			
			return $err;
		}
	}
?>