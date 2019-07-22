<?php
	// fungsi user interface
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class cExcel {
	
		function xlsFromArray($data,$info,$download=true) {
			global $conf;
			
			require_once('../includes/phpexcel/PHPExcel.php');
			
			// membuat excel
			$xls = new PHPExcel();
			$xls->setActiveSheetIndex(0);
			$sheet = $xls->getActiveSheet();
			
			if(!empty($info['sheetname']))
				$sheet->setTitle($info['sheetname']);
			
			// loop array (2 dimensi) dan tulis ke sheet
			$a_huruf = Helper::arrayHuruf();
			
			foreach($data as $r => $row) {
				foreach($row as $c => $cell) {
					$ic = $a_huruf[$c];
					$ir = $r+1;
					
					if(is_array($cell)) {
						$sheet->setCellValue($ic.$ir,$cell['data']);
						
						if(!empty($cell['type']))
							$sheet->getCell($ic.$ir)->setDataType(PHPExcel_Cell_DataType::TYPE_STRING);
					}
					else
						$sheet->setCellValue($ic.$ir,$cell);
				}
			}
			
			foreach($row as $c => $cell)
				$sheet->getColumnDimension($a_huruf[$c])->setAutoSize(true);
			
			// tulis output excel
			$xlsfile = PHPExcel_IOFactory::createWriter($xls,'Excel5');
			
			// bila menggunakan output buffer, bersihkan
			ob_clean();
			
			// nama file / path
			if(empty($info['filename']))
				$filename = 'Excel.xls';
			else
				$filename = $info['filename'];
			
			if($download) {
				// header download excel
				header("Content-Type: application/msexcel");
				header('Content-Disposition: attachment; filename="'.$filename.'"');
				
				// output ke php
				$xlsfile->save('php://output');
			}
			else
				$xlsfile->save($filename);
				
			exit;
		}
		
		function readExcel($file) {			
			require_once('../includes/phpexcel/PHPExcel.php');
			
			$xls = PHPExcel_IOFactory::load($file);
			$xls->setActiveSheetIndex(0);
			$sheet = $xls->getActiveSheet();
			
			// baca mulai baris kedua
			$r = 1; $a_data = array();
			while(++$r) {
				$t_nim = $sheet->getCell('A'.$r)->getValue();
				
				// cek nim
				if(empty($t_nim))
					break;
				else if(empty($a_nim[$t_nim]))
					continue;
				
				$a_nilai = array();
				foreach($a_param as $i => $t_param) {
					$c = $a_huruf[$i+2];
					$t_nilai = $sheet->getCell($c.$r)->getValue();
					if($t_nilai[0] == '=')
						$t_nilai = $sheet->getCell($c.$r)->getCalculatedValue();
					
					$a_nilai[$t_param['id']] = $t_nilai;
				}
				
				$a_data[$t_nim] = $a_nilai;
			}
			
			return $a_data;
		}


	}
?>