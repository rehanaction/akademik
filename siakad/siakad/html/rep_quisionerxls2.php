<?php
//print_r($_POST);
 // cek akses halaman
 defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
 //$conn->debug=true;
 // hak akses
 //Modul::getFileAuth();
 require_once(Route::getModelPath('monitoring'));
 require_once(Route::getModelPath('kelas'));
 require_once($conf['includes_dir'].'phpexcel/PHPExcel.php');
 // include
 $a_dataheader = mMonitoring::getHeaderQuisioner($conn,$_POST['key']);
 $objPHPExcel = new PHPExcel();
$namafile = "";
 $a=0;
 foreach($a_dataheader as $rowh){
    $r_key = $rowh['thnkurikulum']."|". $rowh['kodemk']."|".$rowh['kodeunit']."|".$rowh['periode']."|".$rowh['kelasmk']."|".$rowh['nipdosen'];
    $responden = mMonitoring::getJmlRes($conn,$rowh['thnkurikulum']."|". $rowh['kodemk']."|".$rowh['kodeunit']."|".$rowh['periode']."|".$rowh['kelasmk']."|".$rowh['nipdosen']);
    $key=explode('|',$r_key);
    $a_infokelas = mKelas::getDataSingkat($conn,$r_key,true,$key[5]);


  //$objPHPExcel->getDefaultStyle()->applyFromArray($styleArray);
    $sheet = $objPHPExcel->createSheet($a); //Setting index when creating

    $sheet->getStyle('A4:AM4')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('A4:AM4')->getFill()->getStartColor()->setARGB('A9A9A9');
    $sheet->getStyle('A4:AM4')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle('A4:AM4')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A4:AM4')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A4:AM4')->getFont()->setBold(true);

    $sheet->getStyle('A5:AM5')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('A5:AM5')->getFill()->getStartColor()->setARGB('A9A9A9');
    $sheet->getStyle('A5:AM5')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle('A5:AM5')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A5:AM5')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A5:AM5')->getFont()->setBold(true);

    $sheet->getStyle('A6:AM6')->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
    $sheet->getStyle('A6:AM6')->getFill()->getStartColor()->setARGB('A9A9A9');
    $sheet->getStyle('A6:AM6')->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
    $sheet->getStyle('A6:AM6')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
    $sheet->getStyle('A6:AM6')->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
    $sheet->getStyle('A6:AM6')->getFont()->setBold(true);

    $sheet->getStyle('G1')->getFont()->setBold(true);
    $sheet->getStyle('W1')->getFont()->setBold(true);
    $sheet->getStyle('G2')->getFont()->setBold(true);
    $sheet->getStyle('W2')->getFont()->setBold(true);
    $sheet->getStyle('W2')->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_LEFT);

    $namafile = $a_infokelas['pengajar'];
    $sheet->mergeCells('A1:E1');
    $sheet->setCellValue('A1', 'DOSEN');
    $sheet->setCellValue('F1', ':');
    $sheet->mergeCells('G1:O1');
    $sheet->setCellValue('G1', $a_infokelas['pengajar']);
    $sheet->mergeCells('Q1:U1');
    $sheet->setCellValue('Q1', 'MATAKULIAH');
    $sheet->setCellValue('V1', ':');
    $sheet->mergeCells('W1:AD1');
    $sheet->setCellValue('W1', $a_infokelas['kodemk']." - ".$a_infokelas['namamk']);


    $sheet->mergeCells('A2:E2');
    $sheet->setCellValue('A2', 'HARI/SEMESTER');
    $sheet->setCellValue('F2', ':');
    $sheet->mergeCells('G2:O2');
    $sheet->setCellValue('G2', $a_infokelas['jadwal']." / ".Akademik::getNamaPeriode($a_infokelas['periode'], false));
    $sheet->mergeCells('Q2:U2');
    $sheet->setCellValue('Q2', 'JUMLAH PESERTA');
    $sheet->setCellValue('V2', ':');
    $sheet->mergeCells('W2:AD2');
    $sheet->setCellValue('W2', $responden ." dari ". $a_infokelas['jumlahpeserta']);


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
    $sheet->setCellValue('AM4', 'Rata-Rata');

    // Rename sheet
   
  
    $er = 7;
    $no = 0;
    $a_data=mMonitoring::getHasilQuizAll($conn,$r_key);
    $bag11a = 0;
    $bag11b = 0;
    $bag11c = 0;
    $bag12 = 0;
    $bag13 = 0;
    $bag21 = 0;
    $bag22 = 0;
    $bag23 = 0;
    $bag24 = 0;
    $bag25 = 0;
    $bag26 = 0;
    $bag27 = 0;
    $bag28 = 0;
    $bag29 = 0;
    $bag210 = 0;
    $bag211 = 0;
    $bag212 = 0;
    $bag213 = 0;
    $bag214 = 0;
    $bag215 = 0;
    $bag216 = 0;
    $bag217 = 0;
    $bag318 = 0;
    $bag319 = 0;
    $bag320 = 0;
    $bag321 = 0;
    $bag322 = 0;
    $bag323 = 0;
    $bag324 = 0;
    $bag325 = 0;
    $bag326 = 0;
    $bag327 = 0;
    $bag328 = 0;
    $bag329 = 0;
    $jmlbagian2 = 0;
    $total2 = 0;
    $jmlbagian3 = 0;
    $total3 = 0;
    $ratarata = 0;
    $totalrata2 = 0;
    $ratarata2 = 0;
    $totalrata3 = 0;
    $rata21 = 0; 
    $rata22 = 0; 
    $rata23 = 0; 
    $rata24 = 0; 
    $rata25 = 0; 
    $rata26 = 0; 
    $rata27 = 0; 
    $rata28 = 0; 
    $rata29 = 0; 
    $rata210 = 0; 
    $rata211 = 0; 
    $rata212 = 0; 
    $rata213 = 0; 
    $rata214 = 0; 
    $rata215 = 0; 
    $rata216 = 0; 
    $rata217 = 0; 
    $rata318 = 0; 
    $rata319 = 0; 
    $rata320 = 0; 
    $rata321 = 0; 
    $rata322 = 0; 
    $rata323 = 0; 
    $rata324 = 0; 
    $rata325 = 0; 
    $rata326 = 0; 
    $rata327 = 0; 
    $rata328 = 0; 
    $rata329 = 0; 

    $totalratatata2 = 0; 
    $totalratatata3 = 0;
    
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
        
        $sheet->getStyle('A'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('A'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('A'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('A'.$er)->getFont()->setBold(true);

        $sheet->getStyle('B'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('B'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('B'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('B'.$er)->getFont()->setBold(true);

        $sheet->getStyle('C'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('C'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('C'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('C'.$er)->getFont()->setBold(true);

        $sheet->getStyle('D'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('D'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('D'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('D'.$er)->getFont()->setBold(true);

        $sheet->getStyle('E'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('E'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('E'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('E'.$er)->getFont()->setBold(true);

        $sheet->getStyle('F'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('F'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('F'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('F'.$er)->getFont()->setBold(true);

        $sheet->getStyle('G'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('G'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('G'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('G'.$er)->getFont()->setBold(true);

        $sheet->getStyle('H'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('H'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('H'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('H'.$er)->getFont()->setBold(true);

        $sheet->getStyle('I'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('I'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('I'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('I'.$er)->getFont()->setBold(true);

        $sheet->getStyle('J'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('J'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('J'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('J'.$er)->getFont()->setBold(true);

        $sheet->getStyle('K'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('K'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('K'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('K'.$er)->getFont()->setBold(true);

        $sheet->getStyle('L'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('L'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('L'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('L'.$er)->getFont()->setBold(true);

        $sheet->getStyle('M'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('M'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('M'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('M'.$er)->getFont()->setBold(true);

        $sheet->getStyle('N'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('N'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('N'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('N'.$er)->getFont()->setBold(true);

        $sheet->getStyle('O'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('O'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('O'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('O'.$er)->getFont()->setBold(true);

        $sheet->getStyle('P'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('P'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('P'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('P'.$er)->getFont()->setBold(true);

        $sheet->getStyle('Q'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('Q'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('Q'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('Q'.$er)->getFont()->setBold(true);

        $sheet->getStyle('R'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('R'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('R'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('R'.$er)->getFont()->setBold(true);

        $sheet->getStyle('S'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('S'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('S'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('S'.$er)->getFont()->setBold(true);

        $sheet->getStyle('T'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('T'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('T'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('T'.$er)->getFont()->setBold(true);

        $sheet->getStyle('U'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('U'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('U'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('U'.$er)->getFont()->setBold(true);

        $sheet->getStyle('V'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('V'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('V'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('V'.$er)->getFont()->setBold(true);

        $sheet->getStyle('W'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('W'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('W'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('W'.$er)->getFont()->setBold(true);

        $sheet->setCellValue('A'.$er, $no);
        $sheet->setCellValue('B'.$er, $hasilquiz['quiz_1_1_a']);
        $sheet->setCellValue('C'.$er, $hasilquiz['quiz_1_1_b']);
        $sheet->setCellValue('D'.$er, $hasilquiz['quiz_1_1_c']);
        $sheet->setCellValue('E'.$er, $hasilquiz['quiz_1_2']);
        $sheet->setCellValue('F'.$er, $hasilquiz['quiz_1_3']);
        
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
        $sheet->setCellValue('S'.$er, $hasilquiz['quiz_2_13']);
        $sheet->setCellValue('T'.$er, $hasilquiz['quiz_2_14']);
        $sheet->setCellValue('U'.$er, $hasilquiz['quiz_2_15']);
        $sheet->setCellValue('V'.$er, $hasilquiz['quiz_2_16']);
        $sheet->setCellValue('W'.$er, $hasilquiz['quiz_2_17']);


        $jmlbagian2 = $hasilquiz['quiz_2_1'] + $hasilquiz['quiz_2_2'] + $hasilquiz['quiz_2_3'] + $hasilquiz['quiz_2_4'] + $hasilquiz['quiz_2_5'] + $hasilquiz['quiz_2_6'] + $hasilquiz['quiz_2_7'] + $hasilquiz['quiz_2_8'] + $hasilquiz['quiz_2_9'] + $hasilquiz['quiz_2_10'] + $hasilquiz['quiz_2_11'] + $hasilquiz['quiz_2_12'] + $hasilquiz['quiz_2_13'] + $hasilquiz['quiz_2_14'] + $hasilquiz['quiz_2_15'] + $hasilquiz['quiz_2_16'] + $hasilquiz['quiz_2_17']; 
        $total2 = $jmlbagian2 + $total2; 
        $jmlbagian3 = $hasilquiz['quiz_3_18'] + $hasilquiz['quiz_3_19'] + $hasilquiz['quiz_3_20'] + $hasilquiz['quiz_3_21'] + $hasilquiz['quiz_3_22'] + $hasilquiz['quiz_3_23'] + $hasilquiz['quiz_3_24'] + $hasilquiz['quiz_3_25'] + $hasilquiz['quiz_3_26'] + $hasilquiz['quiz_3_27'] + $hasilquiz['quiz_3_28'] + $hasilquiz['quiz_3_29']; 
        $total3 = $jmlbagian3 + $total3; 
       
        $ratarata = $jmlbagian2 / 17; 
        $totalrata2 = $ratarata + $totalrata2; 
        $ratarata2 = $jmlbagian3 / 12; 
        $totalrata3 = $ratarata2 + $totalrata3; 
   
        $rata21 = $bag21 / $no; 
        $rata22 = $bag22 / $no; 
        $rata23 = $bag23 / $no; 
        $rata24 = $bag24 / $no; 
        $rata25 = $bag25 / $no; 
        $rata26 = $bag26 / $no; 
        $rata27 = $bag27 / $no; 
        $rata28 = $bag28 / $no; 
        $rata29 = $bag29 / $no; 
        $rata210 = $bag210 / $no; 
        $rata211 = $bag211 / $no; 
        $rata212 = $bag212 / $no; 
        $rata213 = $bag213 / $no; 
        $rata214 = $bag214 / $no; 
        $rata215 = $bag215 / $no; 
        $rata216 = $bag216 / $no; 
        $rata217 = $bag217 / $no; 
   
        $rata318 = $bag318 / $no; 
        $rata319 = $bag319 / $no; 
        $rata320 = $bag320 / $no; 
        $rata321 = $bag321 / $no; 
        $rata322 = $bag322 / $no; 
        $rata323 = $bag323 / $no; 
        $rata324 = $bag324 / $no; 
        $rata325 = $bag325 / $no; 
        $rata326 = $bag326 / $no; 
        $rata327 = $bag327 / $no; 
        $rata328 = $bag328 / $no; 
        $rata329 = $bag329 / $no; 
       
        $totalratatata2 = $totalrata2 / $no; 
        $totalratatata3 = $totalrata3 / $no; 
       
       
       if(round($rata21, 2) <= 1.80){
         $ket21 = "SK";
       }elseif(round($rata21, 2) <= 2.60){
         $ket21 = "K";
       }elseif(round($rata21, 2) <= 3.40){
         $ket21 = "C";
       }elseif(round($rata21, 2) <= 4.20){
         $ket21 = "B";
       }elseif(round($rata21, 2) <= 5.00){
         $ket21 = "SB";
       }
       
   
       
       if(round($rata22, 2) <= 1.80){
         $ket22 = "SK";
       }elseif(round($rata22, 2) <= 2.60){
         $ket22 = "K";
       }elseif(round($rata22, 2) <= 3.40){
         $ket22 = "C";
       }elseif(round($rata22, 2) <= 4.20){
         $ket22 = "B";
       }elseif(round($rata22, 2) <= 5.00){
         $ket22 = "SB";
       }
       
   
       
       if(round($rata23, 2) <= 1.80){
         $ket23 = "SK";
       }elseif(round($rata23, 2) <= 2.60){
         $ket23 = "K";
       }elseif(round($rata23, 2) <= 3.40){
         $ket23 = "C";
       }elseif(round($rata23, 2) <= 4.20){
         $ket23 = "B";
       }elseif(round($rata23, 2) <= 5.00){
         $ket23 = "SB";
       }
       
   
       
       if(round($rata24, 2) <= 1.80){
         $ket24 = "SK";
       }elseif(round($rata24, 2) <= 2.60){
         $ket24 = "K";
       }elseif(round($rata24, 2) <= 3.40){
         $ket24 = "C";
       }elseif(round($rata24, 2) <= 4.20){
         $ket24 = "B";
       }elseif(round($rata24, 2) <= 5.00){
         $ket24 = "SB";
       }
       
   
       
       if(round($rata25, 2) <= 1.80){
         $ket25 = "SK";
       }elseif(round($rata25, 2) <= 2.60){
         $ket25 = "K";
       }elseif(round($rata25, 2) <= 3.40){
         $ket25 = "C";
       }elseif(round($rata25, 2) <= 4.20){
         $ket25 = "B";
       }elseif(round($rata25, 2) <= 5.00){
         $ket25 = "SB";
       }
       
   
       
       if(round($rata26, 2) <= 1.80){
         $ket26 = "SK";
       }elseif(round($rata26, 2) <= 2.60){
         $ket26 = "K";
       }elseif(round($rata26, 2) <= 3.40){
         $ket26 = "C";
       }elseif(round($rata26, 2) <= 4.20){
         $ket26 = "B";
       }elseif(round($rata26, 2) <= 5.00){
         $ket26 = "SB";
       }
       
   
       
       if(round($rata27, 2) <= 1.80){
         $ket27 = "SK";
       }elseif(round($rata27, 2) <= 2.60){
         $ket27 = "K";
       }elseif(round($rata27, 2) <= 3.40){
         $ket27 = "C";
       }elseif(round($rata27, 2) <= 4.20){
         $ket27 = "B";
       }elseif(round($rata27, 2) <= 5.00){
         $ket27 = "SB";
       }
       
   
       
       if(round($rata28, 2) <= 1.80){
         $ket28 = "SK";
       }elseif(round($rata28, 2) <= 2.60){
         $ket28 = "K";
       }elseif(round($rata28, 2) <= 3.40){
         $ket28 = "C";
       }elseif(round($rata28, 2) <= 4.20){
         $ket28 = "B";
       }elseif(round($rata28, 2) <= 5.00){
         $ket28 = "SB";
       }
       
   
       
       if(round($rata29, 2) <= 1.80){
         $ket29 = "SK";
       }elseif(round($rata29, 2) <= 2.60){
         $ket29 = "K";
       }elseif(round($rata29, 2) <= 3.40){
         $ket29 = "C";
       }elseif(round($rata29, 2) <= 4.20){
         $ket29 = "B";
       }elseif(round($rata29, 2) <= 5.00){
         $ket29 = "SB";
       }
       
   
       
       if(round($rata210, 2) <= 1.80){
         $ket210 = "SK";
       }elseif(round($rata210, 2) <= 2.60){
         $ket210 = "K";
       }elseif(round($rata210, 2) <= 3.40){
         $ket210 = "C";
       }elseif(round($rata210, 2) <= 4.20){
         $ket210 = "B";
       }elseif(round($rata210, 2) <= 5.00){
         $ket210 = "SB";
       }
       
   
       
       if(round($rata211, 2) <= 1.80){
         $ket211 = "SK";
       }elseif(round($rata211, 2) <= 2.60){
         $ket211 = "K";
       }elseif(round($rata211, 2) <= 3.40){
         $ket211 = "C";
       }elseif(round($rata211, 2) <= 4.20){
         $ket211 = "B";
       }elseif(round($rata211, 2) <= 5.00){
         $ket211 = "SB";
       }
       
   
       
       if(round($rata212, 2) <= 1.80){
         $ket212 = "SK";
       }elseif(round($rata212, 2) <= 2.60){
         $ket212 = "K";
       }elseif(round($rata212, 2) <= 3.40){
         $ket212 = "C";
       }elseif(round($rata212, 2) <= 4.20){
         $ket212 = "B";
       }elseif(round($rata212, 2) <= 5.00){
         $ket212 = "SB";
       }
       
   
       
       if(round($rata213, 2) <= 1.80){
         $ket213 = "SK";
       }elseif(round($rata213, 2) <= 2.60){
         $ket213 = "K";
       }elseif(round($rata213, 2) <= 3.40){
         $ket213 = "C";
       }elseif(round($rata213, 2) <= 4.20){
         $ket213 = "B";
       }elseif(round($rata213, 2) <= 5.00){
         $ket213 = "SB";
       }
       
   
       
       if(round($rata214, 2) <= 1.80){
         $ket214 = "SK";
       }elseif(round($rata214, 2) <= 2.60){
         $ket214 = "K";
       }elseif(round($rata214, 2) <= 3.40){
         $ket214 = "C";
       }elseif(round($rata214, 2) <= 4.20){
         $ket214 = "B";
       }elseif(round($rata214, 2) <= 5.00){
         $ket214 = "SB";
       }
       
   
       
       if(round($rata215, 2) <= 1.80){
         $ket215 = "SK";
       }elseif(round($rata215, 2) <= 2.60){
         $ket215 = "K";
       }elseif(round($rata215, 2) <= 3.40){
         $ket215 = "C";
       }elseif(round($rata215, 2) <= 4.20){
         $ket215 = "B";
       }elseif(round($rata215, 2) <= 5.00){
         $ket215 = "SB";
       }
       
   
       
       if(round($rata216, 2) <= 1.80){
         $ket216 = "SK";
       }elseif(round($rata216, 2) <= 2.60){
         $ket216 = "K";
       }elseif(round($rata216, 2) <= 3.40){
         $ket216 = "C";
       }elseif(round($rata216, 2) <= 4.20){
         $ket216 = "B";
       }elseif(round($rata216, 2) <= 5.00){
         $ket216 = "SB";
       }
       
   
       
       if(round($rata217, 2) <= 1.80){
         $ket217 = "SK";
       }elseif(round($rata217, 2) <= 2.60){
         $ket217 = "K";
       }elseif(round($rata217, 2) <= 3.40){
         $ket217 = "C";
       }elseif(round($rata217, 2) <= 4.20){
         $ket217 = "B";
       }elseif(round($rata217, 2) <= 5.00){
         $ket217 = "SB";
       }
       
   
       
       if(round($rata318, 2) <= 1.80){
         $ket318 = "SK";
       }elseif(round($rata318, 2) <= 2.60){
         $ket318 = "K";
       }elseif(round($rata318, 2) <= 3.40){
         $ket318 = "C";
       }elseif(round($rata318, 2) <= 4.20){
         $ket318 = "B";
       }elseif(round($rata318, 2) <= 5.00){
         $ket318 = "SB";
       }
       
   
       
       if(round($rata319, 2) <= 1.80){
         $ket319 = "SK";
       }elseif(round($rata319, 2) <= 2.60){
         $ket319 = "K";
       }elseif(round($rata319, 2) <= 3.40){
         $ket319 = "C";
       }elseif(round($rata319, 2) <= 4.20){
         $ket319 = "B";
       }elseif(round($rata319, 2) <= 5.00){
         $ket319 = "SB";
       }
       
   
       
       if(round($rata320, 2) <= 1.80){
         $ket320 = "SK";
       }elseif(round($rata320, 2) <= 2.60){
         $ket320 = "K";
       }elseif(round($rata320, 2) <= 3.40){
         $ket320 = "C";
       }elseif(round($rata320, 2) <= 4.20){
         $ket320 = "B";
       }elseif(round($rata320, 2) <= 5.00){
         $ket320 = "SB";
       }
       
   
       
       if(round($rata321, 2) <= 1.80){
         $ket321 = "SK";
       }elseif(round($rata321, 2) <= 2.60){
         $ket321 = "K";
       }elseif(round($rata321, 2) <= 3.40){
         $ket321 = "C";
       }elseif(round($rata321, 2) <= 4.20){
         $ket321 = "B";
       }elseif(round($rata321, 2) <= 5.00){
         $ket321 = "SB";
       }
       
   
       
       if(round($rata322, 2) <= 1.80){
         $ket322 = "SK";
       }elseif(round($rata322, 2) <= 2.60){
         $ket322 = "K";
       }elseif(round($rata322, 2) <= 3.40){
         $ket322 = "C";
       }elseif(round($rata322, 2) <= 4.20){
         $ket322 = "B";
       }elseif(round($rata322, 2) <= 5.00){
         $ket322 = "SB";
       }
       
   
       
       if(round($rata323, 2) <= 1.80){
         $ket323 = "SK";
       }elseif(round($rata323, 2) <= 2.60){
         $ket323 = "K";
       }elseif(round($rata323, 2) <= 3.40){
         $ket323 = "C";
       }elseif(round($rata323, 2) <= 4.20){
         $ket323 = "B";
       }elseif(round($rata323, 2) <= 5.00){
         $ket323 = "SB";
       }
       
   
       
       if(round($rata324, 2) <= 1.80){
         $ket324 = "SK";
       }elseif(round($rata324, 2) <= 2.60){
         $ket324 = "K";
       }elseif(round($rata324, 2) <= 3.40){
         $ket324 = "C";
       }elseif(round($rata324, 2) <= 4.20){
         $ket324 = "B";
       }elseif(round($rata324, 2) <= 5.00){
         $ket324 = "SB";
       }
       
   
       
       if(round($rata325, 2) <= 1.80){
         $ket325 = "SK";
       }elseif(round($rata325, 2) <= 2.60){
         $ket325 = "K";
       }elseif(round($rata325, 2) <= 3.40){
         $ket325 = "C";
       }elseif(round($rata325, 2) <= 4.20){
         $ket325 = "B";
       }elseif(round($rata325, 2) <= 5.00){
         $ket325 = "SB";
       }
       
   
       
       if(round($rata326, 2) <= 1.80){
         $ket326 = "SK";
       }elseif(round($rata326, 2) <= 2.60){
         $ket326 = "K";
       }elseif(round($rata326, 2) <= 3.40){
         $ket326 = "C";
       }elseif(round($rata326, 2) <= 4.20){
         $ket326 = "B";
       }elseif(round($rata326, 2) <= 5.00){
         $ket326 = "SB";
       }
       
   
       
       if(round($rata327, 2) <= 1.80){
         $ket327 = "SK";
       }elseif(round($rata327, 2) <= 2.60){
         $ket327 = "K";
       }elseif(round($rata327, 2) <= 3.40){
         $ket327 = "C";
       }elseif(round($rata327, 2) <= 4.20){
         $ket327 = "B";
       }elseif(round($rata327, 2) <= 5.00){
         $ket327 = "SB";
       }
       
   
       
       if(round($rata328, 2) <= 1.80){
         $ket328 = "SK";
       }elseif(round($rata328, 2) <= 2.60){
         $ket328 = "K";
       }elseif(round($rata328, 2) <= 3.40){
         $ket328 = "C";
       }elseif(round($rata328, 2) <= 4.20){
         $ket328 = "B";
       }elseif(round($rata328, 2) <= 5.00){
         $ket328 = "SB";
       }
       
   
       
       if(round($rata329, 2) <= 1.80){
         $ket329 = "SK";
       }elseif(round($rata329, 2) <= 2.60){
         $ket329 = "K";
       }elseif(round($rata329, 2) <= 3.40){
         $ket329 = "C";
       }elseif(round($rata329, 2) <= 4.20){
         $ket329 = "B";
       }elseif(round($rata329, 2) <= 5.00){
         $ket329 = "SB";
       }
       
       
       
       if(round($totalratatata2, 2) <= 1.80){
         $ketbagian2 = "SK";
       }elseif(round($totalratatata2, 2) <= 2.60){
         $ketbagian2 = "K";
       }elseif(round($totalratatata2, 2) <= 3.40){
         $ketbagian2 = "C";
       }elseif(round($totalratatata2, 2) <= 4.20){
         $ketbagian2 = "B";
       }elseif(round($totalratatata2, 2) <= 5.00){
         $ketbagian2 = "SB";
       }
       
   
       
       if(round($totalratatata3, 2) <= 1.80){
         $ketbagian3 = "SK";
       }elseif(round($totalratatata3, 2) <= 2.60){
         $ketbagian3 = "K";
       }elseif(round($totalratatata3, 2) <= 3.40){
         $ketbagian3 = "C";
       }elseif(round($totalratatata3, 2) <= 4.20){
         $ketbagian3 = "B";
       }elseif(round($totalratatata3, 2) <= 5.00){
         $ketbagian3 = "SB";
       }

      $sheet->getStyle('X'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('X'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('X'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('X'.$er)->getFont()->setBold(true);

        $sheet->getStyle('Y'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('Y'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('Y'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('Y'.$er)->getFont()->setBold(true);

        $sheet->getStyle('Z'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('Z'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('Z'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('Z'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AA'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AA'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AA'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AA'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AB'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AB'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AB'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AB'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AC'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AC'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AC'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AC'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AD'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AD'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AD'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AD'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AE'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AE'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AE'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AE'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AF'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AF'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AF'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AF'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AG'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AG'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AG'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AG'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AH'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AH'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AH'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AH'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AI'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AI'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AI'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AI'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AJ'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AJ'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AJ'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AJ'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AK'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AK'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AK'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AK'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AL'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AL'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AL'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AL'.$er)->getFont()->setBold(true);

        $sheet->getStyle('AM'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
        $sheet->getStyle('AM'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
        $sheet->getStyle('AM'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
        $sheet->getStyle('AM'.$er)->getFont()->setBold(true);
        $sheet->setCellValue('X'.$er, $jmlbagian2);
        $sheet->setCellValue('Y'.$er, round($ratarata, 2));

        $sheet->setCellValue('Z'.$er,$hasilquiz['quiz_3_18']);
        $sheet->setCellValue('AA'.$er, $hasilquiz['quiz_3_19']);
        $sheet->setCellValue('AB'.$er, $hasilquiz['quiz_3_20']);
        $sheet->setCellValue('AC'.$er,$hasilquiz['quiz_3_21']);
        $sheet->setCellValue('AD'.$er, $hasilquiz['quiz_3_22']);
        $sheet->setCellValue('AE'.$er, $hasilquiz['quiz_3_23']);
        $sheet->setCellValue('AF'.$er, $hasilquiz['quiz_3_24']);
        $sheet->setCellValue('AG'.$er, $hasilquiz['quiz_3_25']);
        $sheet->setCellValue('AH'.$er, $hasilquiz['quiz_3_26']);
        $sheet->setCellValue('AI'.$er, $hasilquiz['quiz_3_27']);
        $sheet->setCellValue('AJ'.$er, $hasilquiz['quiz_3_28']);
        $sheet->setCellValue('AK'.$er, $hasilquiz['quiz_3_29']);
        $sheet->setCellValue('AL'.$er,$jmlbagian3);
        $sheet->setCellValue('AM'.$er, round($ratarata2, 2));
       
        $er++;


    }

      $sheet->getStyle('A'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('A'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('A'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('A'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('A'.$er)->getFont()->setBold(true);

$sheet->getStyle('B'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('B'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('B'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('B'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('B'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('B'.$er)->getFont()->setBold(true);

$sheet->getStyle('C'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('C'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('C'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('C'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('C'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('C'.$er)->getFont()->setBold(true);

$sheet->getStyle('D'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('D'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('D'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('D'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('D'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('D'.$er)->getFont()->setBold(true);

$sheet->getStyle('E'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('E'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('E'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('E'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('E'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('E'.$er)->getFont()->setBold(true);

$sheet->getStyle('F'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('F'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('F'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('F'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('F'.$er)->getFont()->setBold(true);

$sheet->getStyle('G'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('G'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('G'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('G'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('G'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('G'.$er)->getFont()->setBold(true);

$sheet->getStyle('H'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('H'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('H'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('H'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('H'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('H'.$er)->getFont()->setBold(true);

$sheet->getStyle('I'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('I'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('I'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('I'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('I'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('I'.$er)->getFont()->setBold(true);

$sheet->getStyle('J'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('J'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('J'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('J'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('J'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('J'.$er)->getFont()->setBold(true);

$sheet->getStyle('K'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('K'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('K'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('K'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('K'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('K'.$er)->getFont()->setBold(true);

$sheet->getStyle('L'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('L'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('L'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('L'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('L'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('L'.$er)->getFont()->setBold(true);

$sheet->getStyle('M'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('M'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('M'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('M'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('M'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('M'.$er)->getFont()->setBold(true);

$sheet->getStyle('N'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('N'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('N'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('N'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('N'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('N'.$er)->getFont()->setBold(true);

$sheet->getStyle('O'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('O'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('O'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('O'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('O'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('O'.$er)->getFont()->setBold(true);

$sheet->getStyle('P'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('P'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('P'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('P'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('P'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('P'.$er)->getFont()->setBold(true);

$sheet->getStyle('Q'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Q'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Q'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Q'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Q'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Q'.$er)->getFont()->setBold(true);

$sheet->getStyle('R'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('R'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('R'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('R'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('R'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('R'.$er)->getFont()->setBold(true);

$sheet->getStyle('S'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('S'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('S'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('S'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('S'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('S'.$er)->getFont()->setBold(true);

$sheet->getStyle('T'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('T'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('T'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('T'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('T'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('T'.$er)->getFont()->setBold(true);

$sheet->getStyle('U'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('U'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('U'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('U'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('U'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('U'.$er)->getFont()->setBold(true);

$sheet->getStyle('V'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('V'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('V'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('V'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('V'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('V'.$er)->getFont()->setBold(true);

$sheet->getStyle('W'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('W'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('W'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('W'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('W'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('W'.$er)->getFont()->setBold(true);

$sheet->getStyle('X'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('X'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('X'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('X'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('X'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('X'.$er)->getFont()->setBold(true);

$sheet->getStyle('Y'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Y'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Y'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Y'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Y'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Y'.$er)->getFont()->setBold(true);

$sheet->getStyle('Z'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Z'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Z'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Z'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Z'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Z'.$er)->getFont()->setBold(true);

$sheet->getStyle('AA'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AA'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AA'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AA'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AA'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AA'.$er)->getFont()->setBold(true);

$sheet->getStyle('AB'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AB'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AB'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AB'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AB'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AB'.$er)->getFont()->setBold(true);

$sheet->getStyle('AC'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AC'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AC'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AC'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AC'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AC'.$er)->getFont()->setBold(true);

$sheet->getStyle('AD'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AD'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AD'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AD'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AD'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AD'.$er)->getFont()->setBold(true);

$sheet->getStyle('AE'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AE'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AE'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AE'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AE'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AE'.$er)->getFont()->setBold(true);

$sheet->getStyle('AF'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AF'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AF'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AF'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AF'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AF'.$er)->getFont()->setBold(true);

$sheet->getStyle('AG'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AG'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AG'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AG'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AG'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AG'.$er)->getFont()->setBold(true);

$sheet->getStyle('AH'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AH'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AH'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AH'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AH'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AH'.$er)->getFont()->setBold(true);

$sheet->getStyle('AI'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AI'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AI'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AI'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AI'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AI'.$er)->getFont()->setBold(true);

$sheet->getStyle('AJ'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AJ'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AJ'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AJ'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AJ'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AJ'.$er)->getFont()->setBold(true);

$sheet->getStyle('AK'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AK'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AK'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AK'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AK'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AK'.$er)->getFont()->setBold(true);

$sheet->getStyle('AL'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AL'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AL'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AL'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AL'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AL'.$er)->getFont()->setBold(true);

$sheet->getStyle('AM'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AM'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AM'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AM'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AM'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AM'.$er)->getFont()->setBold(true);



        $sheet->setCellValue('A'.$er, "Jumlah");
        $sheet->setCellValue('B'.$er, $bag11a);
        $sheet->setCellValue('C'.$er, $bag11b);
        $sheet->setCellValue('D'.$er, $bag11c);
        $sheet->setCellValue('E'.$er, $bag12);
        $sheet->setCellValue('F'.$er, $bag13);
        
        $sheet->setCellValue('G'.$er, $bag21);
        $sheet->setCellValue('H'.$er, $bag22);
        $sheet->setCellValue('I'.$er, $bag23);
        $sheet->setCellValue('J'.$er, $bag24);
        $sheet->setCellValue('K'.$er, $bag25);
        $sheet->setCellValue('L'.$er, $bag26);
        $sheet->setCellValue('M'.$er, $bag27);
        $sheet->setCellValue('N'.$er, $bag28);
        $sheet->setCellValue('O'.$er, $bag29);
        $sheet->setCellValue('P'.$er, $bag210);
        $sheet->setCellValue('Q'.$er, $bag211);
        $sheet->setCellValue('R'.$er, $bag212);
        $sheet->setCellValue('S'.$er, $bag213);
        $sheet->setCellValue('T'.$er, $bag214);
        $sheet->setCellValue('U'.$er, $bag215);
        $sheet->setCellValue('V'.$er, $bag216);
        $sheet->setCellValue('W'.$er, $bag217);
        $sheet->setCellValue('X'.$er, $total2);
        $sheet->setCellValue('Y'.$er, round($totalrata2,2));
 
        $sheet->setCellValue('Z'.$er,$bag318);
        $sheet->setCellValue('AA'.$er, $bag319);
        $sheet->setCellValue('AB'.$er, $bag320);
        $sheet->setCellValue('AC'.$er,$bag321);
        $sheet->setCellValue('AD'.$er, $bag322);
        $sheet->setCellValue('AE'.$er, $bag323);
        $sheet->setCellValue('AF'.$er, $bag324);
        $sheet->setCellValue('AG'.$er, $bag325);
        $sheet->setCellValue('AH'.$er, $bag326);
        $sheet->setCellValue('AI'.$er, $bag327);
        $sheet->setCellValue('AJ'.$er, $bag328);
        $sheet->setCellValue('AK'.$er, $bag329);
        $sheet->setCellValue('AL'.$er,$total3);
        $sheet->setCellValue('AM'.$er, round($totalrata3, 2));

        $er = $er+1;

        $sheet->getStyle('A'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('A'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('A'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('A'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('A'.$er)->getFont()->setBold(true);

$sheet->getStyle('B'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('B'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('B'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('B'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('B'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('B'.$er)->getFont()->setBold(true);

$sheet->getStyle('C'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('C'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('C'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('C'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('C'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('C'.$er)->getFont()->setBold(true);

$sheet->getStyle('D'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('D'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('D'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('D'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('D'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('D'.$er)->getFont()->setBold(true);

$sheet->getStyle('E'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('E'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('E'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('E'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('E'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('E'.$er)->getFont()->setBold(true);

$sheet->getStyle('F'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('F'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('F'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('F'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('F'.$er)->getFont()->setBold(true);

$sheet->getStyle('G'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('G'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('G'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('G'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('G'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('G'.$er)->getFont()->setBold(true);

$sheet->getStyle('H'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('H'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('H'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('H'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('H'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('H'.$er)->getFont()->setBold(true);

$sheet->getStyle('I'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('I'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('I'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('I'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('I'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('I'.$er)->getFont()->setBold(true);

$sheet->getStyle('J'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('J'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('J'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('J'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('J'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('J'.$er)->getFont()->setBold(true);

$sheet->getStyle('K'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('K'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('K'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('K'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('K'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('K'.$er)->getFont()->setBold(true);

$sheet->getStyle('L'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('L'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('L'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('L'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('L'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('L'.$er)->getFont()->setBold(true);

$sheet->getStyle('M'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('M'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('M'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('M'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('M'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('M'.$er)->getFont()->setBold(true);

$sheet->getStyle('N'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('N'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('N'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('N'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('N'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('N'.$er)->getFont()->setBold(true);

$sheet->getStyle('O'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('O'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('O'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('O'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('O'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('O'.$er)->getFont()->setBold(true);

$sheet->getStyle('P'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('P'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('P'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('P'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('P'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('P'.$er)->getFont()->setBold(true);

$sheet->getStyle('Q'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Q'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Q'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Q'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Q'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Q'.$er)->getFont()->setBold(true);

$sheet->getStyle('R'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('R'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('R'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('R'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('R'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('R'.$er)->getFont()->setBold(true);

$sheet->getStyle('S'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('S'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('S'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('S'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('S'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('S'.$er)->getFont()->setBold(true);

$sheet->getStyle('T'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('T'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('T'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('T'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('T'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('T'.$er)->getFont()->setBold(true);

$sheet->getStyle('U'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('U'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('U'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('U'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('U'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('U'.$er)->getFont()->setBold(true);

$sheet->getStyle('V'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('V'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('V'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('V'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('V'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('V'.$er)->getFont()->setBold(true);

$sheet->getStyle('W'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('W'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('W'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('W'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('W'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('W'.$er)->getFont()->setBold(true);

$sheet->getStyle('X'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('X'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('X'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('X'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('X'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('X'.$er)->getFont()->setBold(true);

$sheet->getStyle('Y'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Y'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Y'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Y'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Y'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Y'.$er)->getFont()->setBold(true);

$sheet->getStyle('Z'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Z'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Z'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Z'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Z'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Z'.$er)->getFont()->setBold(true);

$sheet->getStyle('AA'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AA'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AA'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AA'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AA'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AA'.$er)->getFont()->setBold(true);

$sheet->getStyle('AB'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AB'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AB'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AB'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AB'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AB'.$er)->getFont()->setBold(true);

$sheet->getStyle('AC'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AC'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AC'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AC'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AC'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AC'.$er)->getFont()->setBold(true);

$sheet->getStyle('AD'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AD'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AD'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AD'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AD'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AD'.$er)->getFont()->setBold(true);

$sheet->getStyle('AE'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AE'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AE'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AE'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AE'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AE'.$er)->getFont()->setBold(true);

$sheet->getStyle('AF'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AF'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AF'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AF'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AF'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AF'.$er)->getFont()->setBold(true);

$sheet->getStyle('AG'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AG'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AG'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AG'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AG'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AG'.$er)->getFont()->setBold(true);

$sheet->getStyle('AH'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AH'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AH'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AH'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AH'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AH'.$er)->getFont()->setBold(true);

$sheet->getStyle('AI'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AI'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AI'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AI'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AI'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AI'.$er)->getFont()->setBold(true);

$sheet->getStyle('AJ'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AJ'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AJ'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AJ'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AJ'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AJ'.$er)->getFont()->setBold(true);

$sheet->getStyle('AK'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AK'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AK'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AK'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AK'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AK'.$er)->getFont()->setBold(true);

$sheet->getStyle('AL'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AL'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AL'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AL'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AL'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AL'.$er)->getFont()->setBold(true);

$sheet->getStyle('AM'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AM'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AM'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AM'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AM'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AM'.$er)->getFont()->setBold(true);

        $sheet->setCellValue('A'.$er, "Rata-Rata");
        $sheet->setCellValue('B'.$er, '');
        $sheet->setCellValue('C'.$er, '');
        $sheet->setCellValue('D'.$er, '');
        $sheet->setCellValue('E'.$er, '');
        $sheet->setCellValue('F'.$er, '');
        
        $sheet->setCellValue('G'.$er, round($rata21,2));
        $sheet->setCellValue('H'.$er, round($rata22,2));
        $sheet->setCellValue('I'.$er, round($rata23,2));
        $sheet->setCellValue('J'.$er, round($rata24,2));
        $sheet->setCellValue('K'.$er, round($rata25,2));
        $sheet->setCellValue('L'.$er, round($rata26,2));
        $sheet->setCellValue('M'.$er, round($rata27,2));
        $sheet->setCellValue('N'.$er, round($rata28,2));
        $sheet->setCellValue('O'.$er, round($rata29,2));
        $sheet->setCellValue('P'.$er, round($rata210,2));
        $sheet->setCellValue('Q'.$er, round($rata211,2));
        $sheet->setCellValue('R'.$er, round($rata212,2));
        $sheet->setCellValue('S'.$er, round($rata213,2));
        $sheet->setCellValue('T'.$er, round($rata214,2));
        $sheet->setCellValue('U'.$er, round($rata215,2));
        $sheet->setCellValue('V'.$er, round($rata216,2));
        $sheet->setCellValue('W'.$er, round($rata217,2));
        $sheet->setCellValue('X'.$er, '');
        $sheet->setCellValue('Y'.$er, round($totalratatata2,2));
 
        $sheet->setCellValue('Z'.$er,  round($rata318,2));
        $sheet->setCellValue('AA'.$er, round($rata319,2));
        $sheet->setCellValue('AB'.$er, round($rata320,2));
        $sheet->setCellValue('AC'.$er, round($rata321,2));
        $sheet->setCellValue('AD'.$er, round($rata322,2));
        $sheet->setCellValue('AE'.$er, round($rata323,2));
        $sheet->setCellValue('AF'.$er, round($rata324,2));
        $sheet->setCellValue('AG'.$er, round($rata325,2));
        $sheet->setCellValue('AH'.$er, round($rata326,2));
        $sheet->setCellValue('AI'.$er, round($rata327,2));
        $sheet->setCellValue('AJ'.$er, round($rata328,2));
        $sheet->setCellValue('AK'.$er, round($rata329,2));
        $sheet->setCellValue('AL'.$er,'');
        $sheet->setCellValue('AM'.$er, round($totalratatata3, 2));


        $er=$er+1;
        $sheet->getStyle('A'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('A'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('A'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('A'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('A'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('A'.$er)->getFont()->setBold(true);

$sheet->getStyle('B'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('B'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('B'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('B'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('B'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('B'.$er)->getFont()->setBold(true);

$sheet->getStyle('C'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('C'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('C'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('C'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('C'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('C'.$er)->getFont()->setBold(true);

$sheet->getStyle('D'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('D'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('D'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('D'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('D'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('D'.$er)->getFont()->setBold(true);

$sheet->getStyle('E'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('E'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('E'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('E'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('E'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('E'.$er)->getFont()->setBold(true);

$sheet->getStyle('F'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('F'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('F'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('F'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('F'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('F'.$er)->getFont()->setBold(true);

$sheet->getStyle('G'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('G'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('G'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('G'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('G'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('G'.$er)->getFont()->setBold(true);

$sheet->getStyle('H'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('H'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('H'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('H'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('H'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('H'.$er)->getFont()->setBold(true);

$sheet->getStyle('I'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('I'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('I'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('I'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('I'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('I'.$er)->getFont()->setBold(true);

$sheet->getStyle('J'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('J'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('J'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('J'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('J'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('J'.$er)->getFont()->setBold(true);

$sheet->getStyle('K'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('K'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('K'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('K'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('K'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('K'.$er)->getFont()->setBold(true);

$sheet->getStyle('L'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('L'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('L'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('L'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('L'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('L'.$er)->getFont()->setBold(true);

$sheet->getStyle('M'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('M'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('M'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('M'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('M'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('M'.$er)->getFont()->setBold(true);

$sheet->getStyle('N'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('N'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('N'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('N'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('N'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('N'.$er)->getFont()->setBold(true);

$sheet->getStyle('O'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('O'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('O'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('O'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('O'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('O'.$er)->getFont()->setBold(true);

$sheet->getStyle('P'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('P'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('P'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('P'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('P'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('P'.$er)->getFont()->setBold(true);

$sheet->getStyle('Q'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Q'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Q'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Q'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Q'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Q'.$er)->getFont()->setBold(true);

$sheet->getStyle('R'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('R'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('R'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('R'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('R'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('R'.$er)->getFont()->setBold(true);

$sheet->getStyle('S'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('S'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('S'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('S'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('S'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('S'.$er)->getFont()->setBold(true);

$sheet->getStyle('T'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('T'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('T'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('T'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('T'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('T'.$er)->getFont()->setBold(true);

$sheet->getStyle('U'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('U'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('U'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('U'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('U'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('U'.$er)->getFont()->setBold(true);

$sheet->getStyle('V'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('V'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('V'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('V'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('V'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('V'.$er)->getFont()->setBold(true);

$sheet->getStyle('W'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('W'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('W'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('W'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('W'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('W'.$er)->getFont()->setBold(true);

$sheet->getStyle('X'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('X'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('X'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('X'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('X'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('X'.$er)->getFont()->setBold(true);

$sheet->getStyle('Y'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Y'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Y'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Y'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Y'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Y'.$er)->getFont()->setBold(true);

$sheet->getStyle('Z'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('Z'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('Z'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('Z'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('Z'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('Z'.$er)->getFont()->setBold(true);

$sheet->getStyle('AA'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AA'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AA'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AA'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AA'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AA'.$er)->getFont()->setBold(true);

$sheet->getStyle('AB'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AB'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AB'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AB'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AB'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AB'.$er)->getFont()->setBold(true);

$sheet->getStyle('AC'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AC'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AC'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AC'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AC'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AC'.$er)->getFont()->setBold(true);

$sheet->getStyle('AD'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AD'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AD'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AD'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AD'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AD'.$er)->getFont()->setBold(true);

$sheet->getStyle('AE'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AE'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AE'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AE'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AE'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AE'.$er)->getFont()->setBold(true);

$sheet->getStyle('AF'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AF'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AF'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AF'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AF'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AF'.$er)->getFont()->setBold(true);

$sheet->getStyle('AG'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AG'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AG'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AG'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AG'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AG'.$er)->getFont()->setBold(true);

$sheet->getStyle('AH'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AH'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AH'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AH'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AH'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AH'.$er)->getFont()->setBold(true);

$sheet->getStyle('AI'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AI'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AI'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AI'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AI'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AI'.$er)->getFont()->setBold(true);

$sheet->getStyle('AJ'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AJ'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AJ'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AJ'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AJ'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AJ'.$er)->getFont()->setBold(true);

$sheet->getStyle('AK'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AK'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AK'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AK'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AK'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AK'.$er)->getFont()->setBold(true);

$sheet->getStyle('AL'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AL'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AL'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AL'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AL'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AL'.$er)->getFont()->setBold(true);

$sheet->getStyle('AM'.$er)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$sheet->getStyle('AM'.$er)->getFill()->getStartColor()->setARGB('A9A9A9');
$sheet->getStyle('AM'.$er)->getBorders()->getAllBorders()->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN);
$sheet->getStyle('AM'.$er)->getAlignment()->setHorizontal(PHPExcel_Style_Alignment::HORIZONTAL_CENTER);
$sheet->getStyle('AM'.$er)->getAlignment()->setVertical(PHPExcel_Style_Alignment::VERTICAL_CENTER);
$sheet->getStyle('AM'.$er)->getFont()->setBold(true);

        $sheet->setCellValue('A'.$er, "Kategori");
        $sheet->setCellValue('B'.$er, '');
        $sheet->setCellValue('C'.$er, '');
        $sheet->setCellValue('D'.$er, '');
        $sheet->setCellValue('E'.$er, '');
        $sheet->setCellValue('F'.$er, '');
        
        $sheet->setCellValue('G'.$er, $ket21);
        $sheet->setCellValue('H'.$er, $ket22);
        $sheet->setCellValue('I'.$er, $ket23);
        $sheet->setCellValue('J'.$er, $ket24);
        $sheet->setCellValue('K'.$er, $ket25);
        $sheet->setCellValue('L'.$er, $ket26);
        $sheet->setCellValue('M'.$er, $ket27);
        $sheet->setCellValue('N'.$er, $ket28);
        $sheet->setCellValue('O'.$er, $ket29);
        $sheet->setCellValue('P'.$er, $ket210);
        $sheet->setCellValue('Q'.$er, $ket211);
        $sheet->setCellValue('R'.$er, $ket212);
        $sheet->setCellValue('S'.$er, $ket213);
        $sheet->setCellValue('T'.$er, $ket214);
        $sheet->setCellValue('U'.$er, $ket215);
        $sheet->setCellValue('V'.$er, $ket216);
        $sheet->setCellValue('W'.$er, $ket217);
        $sheet->setCellValue('X'.$er, '');
        $sheet->setCellValue('Y'.$er, $ketbagian2);
 
        $sheet->setCellValue('Z'.$er,$ket318);
        $sheet->setCellValue('AA'.$er, $ket319);
        $sheet->setCellValue('AB'.$er, $ket320);
        $sheet->setCellValue('AC'.$er,$ket321);
        $sheet->setCellValue('AD'.$er, $ket322);
        $sheet->setCellValue('AE'.$er, $ket323);
        $sheet->setCellValue('AF'.$er, $ket324);
        $sheet->setCellValue('AG'.$er, $ket325);
        $sheet->setCellValue('AH'.$er, $ket326);
        $sheet->setCellValue('AI'.$er, $ket327);
        $sheet->setCellValue('AJ'.$er, $ket328);
        $sheet->setCellValue('AK'.$er, $ket329);
        $sheet->setCellValue('AL'.$er,'');
        $sheet->setCellValue('AM'.$er, $ketbagian3);

   // $sheet->setTitle($rowh['kodemk']."-".$a_infokelas['namamk']."-".$a_infokelas['pengajar']."-".$rowh['kelasmk']);
   $nama = $rowh['kodemk']."".str_replace($a_infokelas['namamk']," ","")."".str_replace($a_infokelas['pengajar']," ","")."".$row['kelasmk'];
   $sheet->setTitle("$nama");
    $a++;
   }


 
   ob_clean();
   // Redirect output to a client’s web browser (Excel5)
   header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
   header('Content-Disposition: attachment;filename="'.$namafile.'.xls"');
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