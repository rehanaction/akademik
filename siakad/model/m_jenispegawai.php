<?php
	// model program pendidikan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mJenispegawai extends mModel {
		const schema = 'sdm';
		const table = 'ms_jenispeg';
		const order = 'idjenispegawai';
		const key = 'idjenispegawai';
		const label = 'Jenis Pegawai';
		


		function listQuery() {
			// $sql = "select * from v_mhslist";
			$sql = "select a.*, b.tipepeg, c.namarole, (case when a.isaktif='1' then '".self::getCheckImages()."' end) as isaktif, (case when a.isnaikpangkat='1' then '".self::getCheckImages()."' end) as isnaikpangkat from ".static::table()." a
					left join sdm.ms_tipepeg b on a.idtipepeg = b.idtipepeg
					left join gate.sc_role c on a.koderole = c.koderole";
			
			return $sql;
		}		

		function getCheckImages(){
			return "<div align=center><img src=images/check.png></div>";
		}

		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'kodeunit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit')); 
					$row = mUnit::getData($conn,$key); 
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
	}
?>
