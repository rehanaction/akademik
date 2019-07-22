<?php
	// model periode akademik
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPeriode extends mModel {
		const schema = 'akademik';
		const table = 'ms_periode';
		const order = 'periode desc';
		const key = 'periode';
		const label = 'periode akademik';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *, substr(bulanawal,1,4) as thnawal, substr(bulanawal,5,2) as blnawal,
					substr(bulanakhir,1,4) as thnakhir, substr(bulanakhir,5,2) as blnakhir
					from ".static::table();
			
			return $sql;
		}
		
		// mendapatkan array data
		function getArray($conn,$singkat=true) {
			if($singkat)
				$separator = '/';
			else
				$separator = ' - ';
			
			$a_semester = Akademik::semester($singkat);
			
			$sql = "select periode from ".static::table()." order by ".static::order;
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($singkat)
					$t_tahun = substr($row['periode'],2,2);
				else
					$t_tahun = substr($row['periode'],0,4);
				
				$data[$row['periode']] = $a_semester[substr($row['periode'],-1)].' '.$t_tahun.$separator.str_pad($t_tahun+1,2,'0',STR_PAD_LEFT);
			}
			
			return $data;
		}
		
		function getMaxPeriode($conn){
			$periode=$conn->GetOne("select max(periode) from ".static::table());
			
			return substr($periode,0,4);
		}
		
		/**
		 * Mendapatkan periode sebelumnya
		 * @param string $periode
		 * @return string
		 */
		function getPeriodeLalu($periode) {
			$tahun = substr($periode,0,4);
			$semester = substr($periode,-1);
			
			if($semester == '1') {
				$tahun = (int)$tahun-1;
				$semester = '2';
			}
			else if($semester == '2')
				$semester = '1';
			else
				$semester = (int)$semester-2;
			
			return $tahun.$semester;
		}
	}
?>
