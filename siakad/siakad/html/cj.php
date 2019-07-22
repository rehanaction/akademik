<?php
  require_once(Route::getModelPath('mahasiswa'));
  require_once(Route::getModelPath('krs'));
  require_once(Route::getModelPath('perwalian'));
  require_once(Route::getModelPath('akademik'));
  require_once(Route::getModelPath('tagihan'));
  require_once(Route::getModelPath('pembayaran'));
  require_once(Route::getModelPath('pembayarandetail'));
  require_once(Route::getUIPath('form'));
$tmpbayar = mTagihan::getTmpPembayaranAll($conn);
if(!empty($tmpbayar)){
    foreach ($tmpbayar as $row) {
        $data = array();
        $data['invoice']=$row['noinvoice'];
        $data['merchant_id']=$conf['merchant_id'];
        $data['sof_id']=$row['sof_id'];
        $data['sof_type']='check';
        $password=$conf['merchant_password'];
        ksort($data);
        $componetSignature = '';
        foreach ($data as $key => $val) {
            //echo $key." : ".$val."<br/>";
            $componetSignature = $componetSignature."".strtoupper($val)."%";
        }
        $data['mer_signature']=strtoupper(hash("sha256",$componetSignature."".$password));
        $result=mMahasiswa::BayarTagihan($data);
        $paydet = json_decode($result['payment_detail']);
       
       
    //print_r(date('Y-m-d H:m:s',strtotime($paydet->pay_date)));
       if($result['status_code']==201){
                    $t_tgl = date('Y-m-d H:i:s',strtotime($paydet->pay_date));
                    if(empty($t_tgl)){
                         $t_tgl = $row['expireddate'];
                    }
                    //print_r($t_tgl);
                    $paydate = new DateTime($t_tgl);
                    $datecreate = new DateTime($row['datecreate']);
                    $conn->BeginTrans();
                    $record = array();
                    // $record['idpembayaran'] = (mPembayaran::idmaks($conn))+1;
                    $record['tglbayar'] = $t_tgl;
                    $record['jumlahbayar'] = $row['total'];
                    $record['jumlahuang'] = str_replace('.','',$row['total']);
                    $record['ish2h'] = 1;
                    $record['companycode'] ='FINNET';
                    //$record['nip'] = $_SESSION[SITE_ID]['MODUL']['USERNAME'];
                    do{
                        $record['refno'] = mAkademik::random(10);
                        $cek = mPembayaran::cekRefno($conn,$record['refno']);
                          }
                  while(!$cek);
                    $record['periodebayar'] = Akademik::getPeriode();
                    $record['nokuitansi'] = mPembayaran::getNoBSM($conn,substr($t_tgl,0,4).substr($t_tgl,5,2));
                    $record['nim'] = $row['nim'];
                    $recdetail = mTagihan::getAllTmpPembayaran($conn,$row['kodepembayaran']);
                    //print_r($paydate);
                    //print_r($datecreate['date']);
                        
                 if($paydate->format('Y-m-d H:i:s') > $datecreate->format('Y-m-d H:i:s')){
                   
                    $err = mPembayaran::insertRecord($conn,$record);
                   
                    $idpembayaran = mPembayaran::idmaks($conn);
                          $record['idpembayaran'] = $idpembayaran;
                          $rec = array();
                  $rec['idpembayaran'] = $idpembayaran;
                          foreach($recdetail as $row){
                              $rec['idtagihan']=$row['idtagihan'];
                              $rec['nominalbayar'] = $row['nominaltagihan'];
                              $err = mPembayarandetail::insertRecord($conn,$rec);
                          }
                        
                          $conn->CommitTrans();
                          if($err <> '0')
                        {
                                $p_postmsg = " Gagal melakukan Pembayaran";
                                $p_posterr = true;
                                $c_inquiry = false;
                        }
                        else
                        {
                            $ok = mTagihan::updateTmpPembayaran($conn,$row['kodepembayaran']);
                            $_SESSION['message_done'] = "Pembayaran Berhasil";
                            print_r($ok);
                        }
                }else{
                    print_r("gagal");
                    die();   
                }
        }elseif($result['status_code']==211 OR $result['status_code']==203){
            $ok = mTagihan::updateTmpPembayaranCancel($conn,$row['kodepembayaran']);
            //$ok = mTagihan::deleteTmpPembayaran($conn,$row['kodepembayaran']);
            $_SESSION['message_cancel'] = "Pembayaran Di Batalkan Oleh sistem";
       }
    }
}
?>