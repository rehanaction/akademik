<?php
	// model public
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPengumuman extends mModel {
		const schema = 'aset';
		const table = 'ms_pengumuman';
		const order = 'idpengumuman desc';
		const key = 'idpengumuman';
		const label = 'pengumuman';

	    //list pengumuman
	    function listQuery() {
		    $sql = "select *
				    from ".self::table()." ";
		
		    return $sql;
	    }

		// mendapatkan kueri list
		function getPengumuman($conn) {
			$sql = "select * from ".static::schema.".ms_pengumuman where getdate() between tglmulai and tglselesai order by tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}

		function getDetailPengumuman($conn, $r_key) {
			$sql = "select * from ".static::schema.".ms_pengumuman where idpengumuman=$r_key";
			
			$a_data = array();
			$a_data = $conn->GetRow($sql);
			
			return $a_data;
		}	

	}

?>
