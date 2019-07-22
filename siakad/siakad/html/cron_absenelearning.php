<?php
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
    
    
    require_once(Route::getModelPath('mengajar'));
    require_once(Route::getModelPath('absensikuliah'));

    $p_model = mMengajar;
    $a_data = $p_model::getDataKuliahOnline($conn);
    
    foreach($a_data as $row){
        $r_key2 = $row['kodemk']."|".$row['kodeunit']."|".$row['periode']."|".$row['kelasmk']."|".$row['jeniskul']."|".$row['kelompok'];
        $a_absen = mAbsensiKuliah::getListPerKelasOnlineCron($conn_moodle,$r_key2);
        $jmldata = count($a_absen);
        for($e=0;$e<=$jmldata;$e++){
            $a_absen[$e]['kodemk']=$row['kodemk'];
            $a_absen[$e]['kodeunit']=$row['kodeunit'];
            $a_absen[$e]['kelasmk']=$row['kelasmk'];
            $a_absen[$e]['periode']=$row['periode'];
            $a_absen[$e]['kelompok']=$row['kelompok'];
            $a_absen[$e]['jeniskuliah']=$row['jeniskul'];
            $a_absen[$e]['thnkurikulum']=$row['thnkurikulum'];
        }
        foreach($a_absen as $values){
           mAbsensiKuliah::insertAbsensiOnline($conn,$values);
        }
    }
    //print_r($a_absen);

 ?>