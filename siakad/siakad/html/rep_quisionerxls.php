<?php
       // cek akses halaman
       defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
       //$conn->debug=true;
       // hak akses
       //Modul::getFileAuth();
     
       // include
       require_once(Route::getModelPath('monitoring'));
       require_once(Route::getModelPath('kelas'));
       require_once($conf['includes_dir'].'phpexcel/PHPExcel.php');
       $a_dataheader = mMonitoring::getHeaderQuisioner($conn,$_POST['key']);
       $objPHPExcel = new PHPExcel();
       $a=0;
       foreach($a_dataheader as $rowh){
        $r_key = $rowh['thnkurikulum']."|". $rowh['kodemk']."|".$rowh['kodeunit']."|".$rowh['periode']."|".$rowh['kelasmk']."|".$rowh['nipdosen'];
        $key=explode('|',$r_key);
        $sheet = $objPHPExcel->createSheet($a); //Setting index when creating
        $sheet->setCellValue('A1', 'Nama Dosen');
        $sheet->setCellValue('B1', $a_infokelas['pengajar']);
        $sheet->setCellValue('A2', 'Hari/Jurusan/Semester');
        $sheet->setCellValue('B2', $a_infokelas['jadwal']);
        $sheet->setCellValue('C1', 'Mata Kuliah');
        $sheet->setCellValue('D1', $a_infokelas['kodemk']." - ".$a_infokelas['namamk']);
        $sheet->setCellValue('C2', 'Jumlah Responden');
        $sheet->setCellValue('D2', $a_infokelas['jumlahpeserta']);
 
        $sheet->mergeCells('A4:A6');
        $sheet->setCellValue('A4', 'No. Responden');
        $sheet->mergeCells('B4:F4');
        $sheet->setCellValue('B4', 'Bagian I');
        $sheet->mergeCells('B5:D5');
        $sheet->setCellValue('B5', '1');
        $sheet->setCellValue('B6', 'A');
        $sheet->setCellValue('C6', 'B');
        $sheet->setCellValue('D6', 'C');
        $sheet->mergeCells('E5:E6');
        $sheet->setCellValue('E5', '2');
        $sheet->mergeCells('F5:F6');
        $sheet->setCellValue('F5', '3');
        $sheet->mergeCells('G4:W4');
        $sheet->setCellValue('G4', 'Bagian II');
        $sheet->mergeCells('G5:G6');
        $sheet->setCellValue('G5', '1');
        $sheet->mergeCells('H5:H6');
        $sheet->setCellValue('H5', '2');
        $sheet->mergeCells('I5:I6');
        $sheet->setCellValue('I5', '3');
        $sheet->mergeCells('J5:J6');
        $sheet->setCellValue('J5', '4');
        $sheet->mergeCells('K5:K6');
        $sheet->setCellValue('K5', '5');
        $sheet->mergeCells('L5:L6');
        $sheet->setCellValue('L5', '6');
        $sheet->mergeCells('M5:M6');
        $sheet->setCellValue('M5', '7');
        $sheet->mergeCells('N5:N6');
        $sheet->setCellValue('N5', '8');
        $sheet->mergeCells('O5:O6');
        $sheet->setCellValue('O5', '9');
        $sheet->mergeCells('P5:P6');
        $sheet->setCellValue('P5', '10');
        $sheet->mergeCells('Q5:Q6');
        $sheet->setCellValue('Q5', '11');
        $sheet->mergeCells('R5:R6');
        $sheet->setCellValue('R5', '12');
        $sheet->mergeCells('S5:S6');
        $sheet->setCellValue('S5', '13');
        $sheet->mergeCells('T5:T6');
        $sheet->setCellValue('T5', '14');
        $sheet->mergeCells('U5:U6');
        $sheet->setCellValue('U5', '15');
        $sheet->mergeCells('V5:V6');
        $sheet->setCellValue('V5', '16');
        $sheet->mergeCells('W5:W6');
        $sheet->setCellValue('W5', '17');
        $sheet->mergeCells('X4:X6');
        $sheet->setCellValue('X4', 'JML');
        $sheet->mergeCells('Y4:Y6');
        $sheet->setCellValue('Y4', 'Rata-Rata');
        $sheet->mergeCells('Z4:AK4');
        $sheet->setCellValue('Z4', 'Bagian III');
        $sheet->mergeCells('Z5:Z6');
        $sheet->setCellValue('Z5', '18');
        $sheet->mergeCells('AA5:AA6');
        $sheet->setCellValue('AA5', '19');
        $sheet->mergeCells('AB5:AB6');
        $sheet->setCellValue('AB5', '20');
        $sheet->mergeCells('AC5:AC6');
        $sheet->setCellValue('AC5', '21');
        $sheet->mergeCells('AD5:AD6');
        $sheet->setCellValue('AD5', '22');
        $sheet->mergeCells('AE5:AE6');
        $sheet->setCellValue('AE5', '23');
        $sheet->mergeCells('AF5:AF6');
        $sheet->setCellValue('AF5', '24');
        $sheet->mergeCells('AG5:AG6');
        $sheet->setCellValue('AG5', '25');
        $sheet->mergeCells('AH5:AH6');
        $sheet->setCellValue('AH5', '26');
        $sheet->mergeCells('AI5:AI6');
        $sheet->setCellValue('AI5', '27');
        $sheet->mergeCells('AJ5:AJ6');
        $sheet->setCellValue('AJ5', '28');
        $sheet->mergeCells('AK5:AK6');
        $sheet->setCellValue('AK5', '26');
        $sheet->mergeCells('AL4:AL6');
        $sheet->setCellValue('AL4', 'JML');
        $sheet->mergeCells('AM4:AM6');
        $sheet->setCellValue('AM4 ', 'Rata-Rata');
  
        // Rename sheet
       
        $a_infokelas = mKelas::getDataSingkat($conn,$r_key,true,$key[5]);
        $er = 7;
        $no = 0;
        $a_data=mMonitoring::getHasilQuizAll($conn,$r_key);
        $bag11a = 0;
        $bag11b = 0;
        $bag11c = 0;
        $bag12 = 0;
        $bag13 = 0;
        
        $jmly = 0;
        foreach($a_data as $hasilquiz){
            $bag21 = $hasilquiz['quiz_2_1'] + $bag21;
            $bag22 = $hasilquiz['quiz_2_2'] + $bag22;
            $bag23 = $hasilquiz['quiz_2_3'] + $bag23;
            $bag24 = $hasilquiz['quiz_2_4'] + $bag24;
            $bag25 = $hasilquiz['quiz_2_5'] + $bag25;
            $bag26 = $hasilquiz['quiz_2_6'] + $bag26;
            $bag27 = $hasilquiz['quiz_2_7'] + $bag27;
            $bag28 = $hasilquiz['quiz_2_8'] + $bag28;
            $bag29 = $hasilquiz['quiz_2_9'] + $bag29;
            $bag210 = $hasilquiz['quiz_2_10'] + $bag210;
            $bag211 = $hasilquiz['quiz_2_11'] + $bag211;
            $bag212 = $hasilquiz['quiz_2_12'] + $bag212;
            $bag213 = $hasilquiz['quiz_2_13'] + $bag213;
            $bag214 = $hasilquiz['quiz_2_14'] + $bag214;
            $bag215 = $hasilquiz['quiz_2_15'] + $bag215;
            $bag216 = $hasilquiz['quiz_2_16'] + $bag216;
            $bag217 = $hasilquiz['quiz_2_17'] + $bag217;
        
            $bag318 = $hasilquiz['quiz_3_18'] + $bag318;
            $bag319 = $hasilquiz['quiz_3_19'] + $bag319;
            $bag320 = $hasilquiz['quiz_3_20'] + $bag320;
            $bag321 = $hasilquiz['quiz_3_21'] + $bag321;
            $bag322 = $hasilquiz['quiz_3_22'] + $bag322;
            $bag323 = $hasilquiz['quiz_3_23'] + $bag323;
            $bag324 = $hasilquiz['quiz_3_24'] + $bag324;
            $bag325 = $hasilquiz['quiz_3_25'] + $bag325;
            $bag326 = $hasilquiz['quiz_3_26'] + $bag326;
            $bag327 = $hasilquiz['quiz_3_27'] + $bag327;
            $bag328 = $hasilquiz['quiz_3_28'] + $bag328;
            $bag329 = $hasilquiz['quiz_3_29'] + $bag329;
            
            if($hasilquiz['quiz_1_1_a'] == "Y"){
              $bag11a = $bag11a + 1;
            };
        
            if($hasilquiz['quiz_1_1_b'] == "Y"){
              $bag11b = $bag11b + 1;
            };
        
            if($hasilquiz['quiz_1_1_c'] == "Y"){
              $bag11c = $bag11c + 1;
            };
        
            if($hasilquiz['quiz_1_2'] == "Y"){
              $bag12 = $bag12 + 1;
            };
        
            if($hasilquiz['quiz_1_3'] == "Y"){
              $bag13 = $bag13 + 1;
            };
            $no++;    
        
            $sheet->setCellValue('A'.$er, $no);
            $sheet->setCellValue('B'.$er, $hasilquiz['quiz_1_1_a']);
            $sheet->setCellValue('C'.$er, $hasilquiz['quiz_1_1_b']);
            $sheet->setCellValue('D'.$er, $hasilquiz['quiz_1_1_c']);
            $sheet->setCellValue('E'.$er, $hasilquiz['quiz_1_2']);
            $sheet->setCellValue('F'.$er, $hasilquiz['quiz_1_3']);
            /*
            $sheet->setCellValue('G'.$er, $hasilquiz['quiz_2_1']);
            $sheet->setCellValue('H'.$er, $hasilquiz['quiz_2_2']);
            $sheet->setCellValue('I'.$er, $hasilquiz['quiz_2_3']);
            $sheet->setCellValue('J'.$er, $hasilquiz['quiz_2_4']);
            $sheet->setCellValue('K'.$er, $hasilquiz['quiz_2_5']);
            $sheet->setCellValue('L'.$er, $hasilquiz['quiz_2_6']);
            $sheet->setCellValue('M'.$er, $hasilquiz['quiz_2_7']);
            $sheet->setCellValue('N'.$er, $hasilquiz['quiz_2_8']);
            $sheet->setCellValue('O'.$er, $hasilquiz['quiz_2_9']);
            $sheet->setCellValue('P'.$er, $hasilquiz['quiz_2_10']);
            $sheet->setCellValue('Q'.$er, $hasilquiz['quiz_2_11']);
            $sheet->setCellValue('R'.$er, $hasilquiz['quiz_2_12']);
            $sheet->setCellValue('S'.$er, $hasilquiz['quiz_2_14']);
            $sheet->setCellValue('T'.$er, $hasilquiz['quiz_2_15']);
            $sheet->setCellValue('U'.$er, $hasilquiz['quiz_2_16']);
            $sheet->setCellValue('V'.$er, $hasilquiz['quiz_2_17']);*/
             
            $er++;


        }
       // $sheet->setTitle($rowh['kodemk']."-".$a_infokelas['namamk']."-".$a_infokelas['pengajar']."-".$rowh['kelasmk']);
       $nama = $rowh['kodemk']."".str_replace($a_infokelas['namamk']," ","")."".str_replace($a_infokelas['pengajar']," ","")."".$row['kelasmk'];
       $sheet->setTitle("$nama");
        $a++;
       }

    
     
       ob_clean();
       // Redirect output to a client’s web browser (Excel5)
       header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
       header('Content-Disposition: attachment;filename="Quisioner.xls"');
       header('Cache-Control: max-age=0');
       // If you're serving to IE 9, then the following may be needed
       header('Cache-Control: max-age=1');
       
       // If you're serving to IE over SSL, then the following may be needed
       header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
       header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
       header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
       header ('Pragma: public'); // HTTP/1.0
       
       $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
       $objWriter->save('php://output');
       exit;
    






?>