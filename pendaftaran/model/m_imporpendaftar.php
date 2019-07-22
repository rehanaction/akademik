<?php
	// model impor pendaftar
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	
	
	// class impor
	class mImporPendaftar extends mModel{
		const schema 	= 'pendaftaran';
		const table 	= 'pd_pendaftar';
		const order 	= 'nopendaftar';
		const key 	= 'nopendaftar';
		const label 	= 'pendaftar';
		
		function uploadFile($conn,$periode,$jalur,$file) {
			//$r_file = $_FILES['xls']['tmp_name'];
		
			// pakai excel reader
			require_once('../includes/phpexcel/excel_reader2.php');
			$xls = new Spreadsheet_Excel_Reader($file);
			
			$cells = $xls->sheets[0]['cells'];
			$numrow = count($cells);
			
			// jika cells kosong (mungkin bukan merupakan format excel), baca secara csv
			if(empty($numrow)) {
				if(($handle = fopen($r_file, 'r')) !== false) {
					while (($data = fgetcsv($handle, 1000, "\t")) !== false) {
						$numrow++;
						foreach($data as $k => $v)
							$cells[$numrow][$k+1] = $v;
					}
					fclose($handle);
				}
			}
			//print_r($cells);die();
			// baris pertama adalah header
			$conn->BeginTrans();
			
			$ok = true;
			for($r=2;$r<=$numrow;$r++) {
				$data = $cells[$r];
				$record=array();
				$record['nopendaftar']=$data[1];
				$record['nama']=$data[2];
				$record['jalurpenerimaan']=$data[3];
				$record['pilihan1']=$data[4];
				$record['pilihan2']=$data[5];
				$record['pilihan3']=$data[6];
				$record['pilihanditerima']=$data[7];
				$record['jalan']=$data[8];
				$record['telp']=$data[9];
				$record['periodedaftar']=$data[10];
				$record['idgelombang']=$data[11];
				$record['sistemkuliah']=$data[12];
				
				list($p_posterr,$p_postmsg)=self::insertCRecord($conn,"",$record,$r_key);
				if($p_posterr) {
					$ok = false;
					break;
				}
			}
			$conn->CommitTrans($ok);
			return $ok;
		}
		
		
	}	
?>
