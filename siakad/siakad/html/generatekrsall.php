<?php
require_once(Route::getModelPath('tagihan'));
require_once(Route::getModelPath('mahasiswa'));
$datatagihanmhs = mTagihan::cekdataTagihan($conn);
foreach($datatagihanmhs as $data){
    $datamhs = mMahasiswa::getDatamhs($conn,$data['nim']);
    mTagihan::generateTagihankrs($conn, $data['nim'],$data['periode'],$datamhs);
}
//print_r($datatagihanmhs);



 ?>