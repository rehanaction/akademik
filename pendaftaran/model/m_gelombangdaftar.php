<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mGelombangDaftar extends mModel {
		const schema = 'pendaftaran';
		const table = 'pd_gelombangdaftar';
		const order = 'periodedaftar';
		const key = 'jalurpenerimaan,periodedaftar,idgelombang';
		const label = 'jalur penerimaan';

		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periodedaftar = '$key'";
				case 'jalur': return "jalurpenerimaan = '$key'";
				case 'gelombang':return "idgelombang = '$key'";
				
				
			}
		}
		function deleteFile($conn,$key){
			$cond=static::getCondition($key);
			$file=$conn->GetOne("select filependaftaran from pendaftaran.pd_gelombangdaftar where $cond");
			$del=unlink("uploads/pengumuman/".$file);
			if($del)
				$update=$conn->Execute("update pendaftaran.pd_gelombangdaftar set filependaftaran='' where $cond");
			return static::updateStatus($conn,"filependaftaran");
		}
		
		function deleteCFile($conn,$key,$jenis='filependaftaran'){
			$cond=static::getCondition($key);
			
				$folder = str_replace('file','pengumuman',$jenis);
			
			$file=$conn->GetOne("select ".$jenis." from pendaftaran.pd_gelombangdaftar where $cond");
			
			$del=unlink("uploads/".$folder."/".$file);
			if($del)
				$update=$conn->Execute("update pendaftaran.pd_gelombangdaftar set ".$jenis."='' where $cond");
			return static::updateStatus($conn,$jenis);
		}
		
		
		function getInfoDaftarulang($conn,$key){
			$cond=static::getCondition($key);
			$file=$conn->GetRow("select filedaftarulang,pengumumandu from pendaftaran.pd_gelombangdaftar where $cond");
			
			return $file;
		}
	}
?>
