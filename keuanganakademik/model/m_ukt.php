<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mUkt extends mModel {
		const schema = 'h2h';
		const table = 'ke_tarifukt';
		const order = 'periode, kodeunit';
		const key = 'periode, kodeunit, kodekategoriukt';
		const label = 'UKT';

	function getListFilter($col,$key) {
		global $conn;
			switch($col) {
				case 'periode': return " periode = '$key'";
				case 'kodeunit': 
					$sql="select infoleft, inforight from gate.ms_unit where kodeunit = '$key'";
					$rs = $conn->getRow($sql);
					$infoleft = $rs['infoleft'];
					$inforight = $rs['inforight'];
				return " m.infoleft >= $infoleft and m.inforight <= $inforight";

			}
		}

		
		function listQuery() {
			$sql = "select u.kodeunit, u.periode, u.kodekategoriukt, t.namakategoriukt, u.nilaitarif, u.keterangan, m.namaunit from ".static::table().' u
					join gate.ms_unit m on u.kodeunit=m.kodeunit 
					left join akademik.lv_kategoriukt t on t.kodekategoriukt=u.kodekategoriukt ';
			
			return $sql;
		}	}
?>
