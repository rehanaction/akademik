<?php
	// model Kerjasama luar negeri
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKerjasamaln extends mModel {
		const schema = 'akademik';
		const table = 'ak_kerjasamaln';
		const order = 'kodeuniversitas';
		const key = 'nourut';
		const label = 'Kerjasama Luar Negeri';		
	
	function listQuery() {
			// $sql = "select * from v_mhslist";
			$sql = "select m.*, u.namauniversitas from ".static::table()." m
					left join akademik.ms_universitas u on m.kodeuniversitas = u.kodeuniversitas";
			
			return $sql;
		}			
	}

?>