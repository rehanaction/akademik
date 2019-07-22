<?php 

    require_once(Route::getModelPath('kelas'));
	require_once(Route::getModelPath('kuliah'));
	require_once(Route::getModelPath('kuliah'));

    $a_data = mKuliah::getDatajurnalCron($conn_moodle);

    //print_r($a_data);

    foreach($a_data as $values){
        $record['tglkuliah']=date('Y-m-d',strtotime($values['tglmulaiperkuliahan']));
        $record['tglkuliahrealisasi'] = date('Y-m-d',strtotime($values['tglmulaiperkuliahan']));
        if(isset($record['tglkuliah'])){
            $record['nohari'] = date('N',strtotime($record['tglkuliah']));
        }
        if(isset($record['tglkuliahrealisasi']))
        {
            $record['noharirealisasi'] = date('N',strtotime($record['tglkuliahrealisasi']));
        }
        $record['topikkuliah'] = $values['realisasi'];
        $record['statusperkuliahan'] = 'S';

        $where['kodemk']=$values['kodemk'];
        $where['kelasmk']=$values['seksi'];
        $where['periode']=$values['periode'];
        $kodeunit = explode('-',$values['kodeunit']);
        $where['kodeunit']=$kodeunit[1];
        mkuliah::updateJurnal($conn,$record,$where);

    }
?>