
<?php
  defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
  //$conn->debug = true;
  // hak akses
 // $a_auth = Modul::getFileAuth();
  header("Refresh:30");
  require_once(Route::getModelPath('mahasiswa'));
  require_once(Route::getModelPath('krs'));
  require_once(Route::getModelPath('perwalian'));
  require_once(Route::getModelPath('akademik'));
  require_once(Route::getModelPath('tagihan'));
  require_once(Route::getModelPath('pembayaran'));
  require_once(Route::getModelPath('pembayarandetail'));
  require_once(Route::getUIPath('form'));
  $p_title = 'Form Pembayaran Rutin';
$p_tbwidth = '100%';
  $p_aktivitas = 'Transaksi';
  $r_act = $_POST['act'];
  $r_semester = CStr::removeSpecial($_REQUEST['semester']);
  $r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
  $r_periode=$r_tahun.$r_semester;
$r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));

  $p_foto = uForm::getPathImageMahasiswa($conn,$r_key);
  $a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
  $a_tagihan2 = mMahasiswa::getTagihanMhsBB($conn,$r_key);
  $r_periode = mAkademik::getPeriodeSekarang($conn);
  $date = date('Ymdhms');
$tmpbayar = mTagihan::getTmpPembayaran($conn,$a_infomhs['nim']);
if(!empty($tmpbayar)){
    //cek status tagihan
    //print_r($tmpbayar);
    $adm = 0;
    if($tmpbayar['sof_id']=="finpay021"){
        $adm = 2000;
    }else if($tmpbayar['sof_id']=="vamandiri"){
        $adm = 5500;
    }else if($tmpbayar['sof_id']=="vabni"){
        $adm = 5500;
    }else if($tmpbayar['sof_id']=="cc"){
        $adm = ($tmpbayar['total']*0.025)+2000;
    }
    $data = array();
    $data['invoice']=$tmpbayar['noinvoice'];
    $data['merchant_id']=$conf['merchant_id'];
    $data['sof_id']=$tmpbayar['sof_id'];
    $data['sof_type']='check';
    $password=$conf['merchant_password'];
    ksort($data);
    $componetSignature ='';
    foreach ($data as $key => $val) {
        //echo $key." : ".$val."<br/>";
        $componetSignature = $componetSignature."".strtoupper($val)."%";
    }
   
    $data['mer_signature']=strtoupper(hash("sha256",$componetSignature."".$password));
    $result=mMahasiswa::BayarTagihan($data);
    
    $paydet = json_decode($result['payment_detail']);

    if($result['status_code']==201){
                $t_tgl = date('Y-m-d H:i:s',strtotime($paydet->pay_date));
               
                if(empty($t_tgl)){
                    $t_tgl = $row['expireddate'];
                }
                
                $paydate = new DateTime($t_tgl);
                $datecreate = new DateTime($tmpbayar['datecreate']);
                $conn->BeginTrans();
                $record = array();
                // $record['idpembayaran'] = (mPembayaran::idmaks($conn))+1;
                $record['tglbayar'] = $t_tgl;
                $record['jumlahbayar'] = $tmpbayar['total'];
                $record['jumlahuang'] = str_replace('.','',$tmpbayar['total']);
                $record['ish2h'] = 1;
                $record['companycode'] ='FINNET';
               
                //$record['nip'] = $_SESSION[SITE_ID]['MODUL']['USERNAME'];
                do{
                    $record['refno'] = mAkademik::random(10);
                    $cek = mPembayaran::cekRefno($conn,$record['refno']);
                      }
               while(!$cek);
              $record['periodebayar'] = $r_periode;
              $record['nokuitansi'] = mPembayaran::getNoBSM($conn,substr($t_tgl,0,4).substr($t_tgl,5,2));
                      $record['nim'] = $a_infomhs['nim'];
                      $recdetail = mTagihan::getAllTmpPembayaran($conn,$tmpbayar['kodepembayaran']);
                     
            if($paydate >= $datecreate) {
               
              $err = mPembayaran::insertRecord($conn,$record);
             // print_r($record);
          
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
                    if($err <> '0'){
                        $p_postmsg = " Gagal melakukan Pembayaran";
                        $p_posterr = true;
                        $c_inquiry = false;
                      }
                    else
                    {
                     
                            $subject = "Kode Pembayaran Kuliah";
                            $body = 'Pembayaran Kuliah STIE INABA'
                                    .'<br>  Kode Pembayaran             : '.$tmpbayar['kodepembayaran']
                                    .'<br>  NIM                         : '.$a_infomhs['nim']
                                    .'<br>  Nama Mahasiswa              : '.$a_infomhs['nama']
                                    .'<br> Perbayaran Telah Berhasil Dilakukan Cek ulang tagihan anda di siakad.inaba.ac.id jika belum terupdate segera hubungi bagian IT dengan membawa bukti pembayaran <br>';
                            mTagihan::sendMail($a_infomhs['email'],$subject,$body);
                            $ok = mTagihan::updateTmpPembayaran($conn,$row['kodepembayaran']);
                            $_SESSION['message_done'] = "Pembayaran Berhasil";
                }
              }else{
                $conn->CommitTrans();
                $ok = mTagihan::updateTmpPembayaranCancel($conn,$tmpbayar['kodepembayaran']);
                $_SESSION['message_cancel'] = "Pembayaran Di Batalkan Oleh sistem";

              }
    }elseif($result['status_code']==211 OR $result['status_code']==203){
        $ok = mTagihan::updateTmpPembayaranCancel($conn,$tmpbayar['kodepembayaran']);
        $_SESSION['message_cancel'] = "Pembayaran Di Batalkan Oleh sistem";
    }
}else{
   
    header("Location: https://siakad.inaba.ac.id/siakad/siakad/index.php?page=pembayaran");
}

if(!empty($_POST['updcancel'])){
  $conn->CommitTrans();
  $ok = mTagihan::updateTmpPembayaranCancel($conn,$_POST['updcancel']);
  $_SESSION['message_cancel'] = "Pembayaran Di Batalkan Oleh sistem";
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>Detail Pembayaran</title>

    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <link href="style/pembayaran/all.min.css" rel="stylesheet">
    <link href="style/pembayaran/ionicons.min.css" rel="stylesheet">
    <link rel="stylesheet" href="style/pembayaran/bracket.css">
    <link rel="stylesheet" href="style/pembayaran/bracket.oreo.css">
    <script src="scripts/pembayaran/jquery.min.js"></script>
    <script src="scripts/pembayaran/bootstrap.bundle.min.js"></script>
    <script src="scripts/pembayaran/moment.min.js"></script>
    <script src="scripts/pembayaran/jquery.peity.min.js"></script>
    <script src="scripts/pembayaran/bracket.js"></script>
  </head>

  <body>

    <div class="br-logo"><a href=""><span>[</span>STIE <i>INABA</i><span>]</span></a></div>

    <div class="br-header">
      <div class="br-header-left">
        <div class="navicon-left hidden-md-down"><a href="javascript:void(0);" title="Kembali" onclick="goView('home')"><i class="icon ion-arrow-left-a"></i></a></div>
        <div class="navicon-left hidden-lg-up"><a href="javascript:void(0);" title="Kembali" onclick="goView('home')"><i class="icon ion-arrow-left-a"></i></a></div>
      </div>

    </div>

    <div class="br-mainpanel br-profile-page" style="margin-left: 0px; margin-bottom: 50px;">

      <div class="tab-content br-profile-body">

        <div class="tab-pane fade active show" id="posts">
          <div class="row">
            <div class="col-lg-6">
              <div class="card card-body pd-25 bd-gray-400">
                <div class="row">
                  <div class="col-sm-12">
                    <h6 class="card-title tx-uppercase tx-12">KODE PEMBAYARAN</h6>
                    <p class="display-4 tx-medium tx-danger mg-b-20 tx-lato"><?=$tmpbayar['kodepembayaran']?></p>
                    
                    <h6 class="card-title tx-uppercase tx-12">BATAS PEMBAYARAN</h6>
                    
                    <p class="tx-20 tx-medium tx-black mg-b-5 tx-lato"><?=date('H:i:s',strtotime($tmpbayar['expireddate']))?> (2 Jam)</p>
                    <?php 
                    
                   if(date("Y-m-d h:i:s")>$tmpbayar['expireddate']){ ?> 
                    <form method="post">
                    <input type="hidden" value=<?=$tmpbayar['kodepembayaran']?> name="updcancel">
                    <button class="btn btn-primary btn-block mg-b-10" id="btnpayment">Batalkan</i></button>
                    </form>
                    <?php  } ?>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-lg-6">
              <div class="card card-body pd-25 bd-gray-400">
                <div class="row">
                  <div class="col-sm-12">
                    <h6 class="card-title tx-uppercase tx-12">DETAIL PEMBAYARAN</h6>
                    <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">JUMLAH BAYAR</label>
                    <p class="tx-black mg-b-25"><?="Rp. ".CStr::FormatNumber($tmpbayar['total']+$adm)?></p>
                    <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">METODE PEMBAYARAN</label>
                    <p class="tx-inverse mg-b-25"><?= mTagihan::metodePembayaran($tmpbayar['sof_id']) ?></p>
                    
                    
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="row row-sm mg-t-20">

            <div class="col-lg-4 mg-t-30 mg-lg-t-0">
              <div class="card pd-20 pd-xs-30 bd-gray-400">
                <h6 class="tx-gray-800 tx-uppercase tx-semibold tx-13 mg-b-25">Detail</h6>

                <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">NAMA LENGKAP</label>
                <p class="tx-inverse mg-b-25"><?=$a_infomhs['nama']?></p>

                <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">NIM</label>
                <p class="tx-inverse mg-b-25"><?=$a_infomhs['nim']?></p>

                <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">NOMOR HANDPHONE</label>
                <p class="tx-info mg-b-25"><?=$a_infomhs['nohp']?></p>

                <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">ALAMAT EMAIL</label>
                <p class="tx-inverse mg-b-25"><?=$a_infomhs['email']?></p>

                <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">JURUSAN</label>
                <p class="tx-inverse mg-b-25"><?=$a_infomhs['jurusan']?></p>

                <label class="tx-10 tx-uppercase tx-mont tx-medium tx-spacing-1 mg-b-2">BASIS</label>
                <p class="tx-inverse mg-b-10"><?=$a_infomhs['namasistemkuliah']?></p>

              </div>

              
            </div>
            <div class="col-lg-8">
              <div class="media-list bg-white rounded bd bd-gray-400">
                <div class="media pd-20 pd-xs-30">
                 
                  <div class="media-body mg-l-20">
                    <div class="d-flex justify-content-between mg-b-10">
                      <div>
                        <h6 class="mg-b-2 tx-black tx-14">TATA CARA PEMBAYARAN</h6>
                        
                      </div>
                      
                    </div><!-- d-flex -->
                    <p class="mg-b-20">
                    <?php if($tmpbayar['sof_id']=='finpay021'){ ?>
                         <ul>
                                <li class="tx-bold tx-black">ATM MANDIRI</li>
                                <ul style="list-style-type:square">
                                    <li> Pilih Menu Bayar / Beli</li>
                                    <li>    Pilih Menu Telepon / HP</li>
                                    <li>    Pilih CDMA / Telkom</li>
                                    <li>    Pilih Telkom / Speedy Vision</li>
                                    <li>    Masukkan 12 digit Kode Pembayaran (021xxxxxxxxx) yang Anda terima dari siakad</li>
                                    <li>    Pilih YA untuk melanjutkan pembayaran</li>
                                </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black">ATM BRI</li>
                                <ul style="list-style-type:square">
                                  <li>  Pilih Menu TRANSAKSI LAIN.</li>
                                  <li> Pilih Jenis Transaksi PEMBAYARAN.</li>
                                  <li>Pilih Transaksi Pembayaran TELKOM/FLEXI/SPEEDY.</li>
                                  <li>Pilih Transaksi Pembayaran TELKOM/FLEXI/SPEEDY.</li>
                                  <li>Masukkan 12 digit Kode Pembayaran (021xxxxxxxxx) yang Anda terima dari siakad </li>
                                  <li>Pilih YA untuk melanjutkan pembayaran.</li>
                                  </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black">ATM BCA</li>
                                <ul style="list-style-type:square">
                                <li>  Pilih Menu TRANSAKSI LAINNYA</li>
                                <li> Pilih Transaksi PEMBAYARAN</li>
                                <li>  Pilih Jenis Pembayaran TELEPON/HANDPHONE</li>
                                <li>  Pilih Operator Telepon TELKOM</li>
                                <li>   Masukkan 12 digit Kode Pembayaran (021xxxxxxxxx) yang Anda terima dari siakad </li>
                                <li> Pilih YA untuk melanjutkan pembayaran.</li>
                                </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black"> ATM Danamon </li>
                                <ul style="list-style-type:square">
                                <li>  Pilih Jenis Transaksi PEMBAYARAN</li>
                                <li>Pilih Jenis Pembayaran TELEPON</li>
                                <li>Masukkan 3 digit awal (021) dari Kode Pembayaran yang Anda dapatkan dari siakad dan tambahkan angka Nol "0" di depannya (0021).</li>
                                <li>Masukkan 9 digit terakhir Kode Pembayaran yang anda dapatkan dari siakad</li>
                                <li>Pilih Sumber Dana TABUNGAN</li>
                                <li>Pilih YA untuk melanjutkan pembayaran</li>
                                </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black">ATM BNI</li>
                                <ul style="list-style-type:square">
                                <li>   Pilih Menu MENU LAIN</li>
                                <li>  Pilih Transaksi PEMBAYARAN</li>
                                <li> Pilih Jenis Pembayaran TELEPON/HP</li>
                                <li>  Pilih Jenis PembayaranTelepon TELKOM</li>
                                <li>   Pilih Jenis Pembayaran Telkom TELEPON/FLEXI/SPEEDY</li>
                                <li>   Masukkan 12 digit Kode Pembayaran yang Anda terima dari siakad.inaba.ac.id dan tambahkan angka NOL ‘0’ di depannya sehingga menjadi "0021xxxxxxxxxx"</li>
                                <li>  Pilih Sumber Dana TABUNGAN h. Pilih YA untuk melanjutkan pembayaran</li>
                                </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black"> ATM BII</li>
                                <ul style="list-style-type:square">
                                <li>   Pilih Menu PEMBAYARAN/PEMBELIAN</li>
                                <li>  Pilih Jenis Pembayaran LAYANAN UMUM</li>
                                <li>  Pilih Jenis Pembayaran TELKOM/SPEEDY/FLEXI CLASSY/YES TV</li>
                                <li>  Masukkan 12 digit Kode Pembayaran yang Anda terima dari siakad dan tambahkan angka NOL ‘0’ di depannya sehingga menjadi "0021xxxxxxxxxx".</li>
                                <li>  Pilih Jenis Rekening TABUNGAN</li>
                                <li>  Pilih YA untuk melanjutkan pembayaran</li>
                                </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black">ATM OCBC NISP</li>
                                <ul style="list-style-type:square">
                                  <li>    Pilih MENU LAINNYA </li>
                                  <li>   Pilih Transaksi PEMBAYARAN</li>
                                  <li>   Pilih Jenis Pembayaran TELEPON/TELKOM</li>
                                  <li>  Pilih Jenis Pembayaran TELP/FLEXI/SPEEDY</li>
                                  <li>  Masukkan 12 digit Kode Pembayaran yang Anda terima dari siakad dan tambahkan angka NOL ‘0’ di depannya sehingga menjadi "0021xxxxxxxxxx"</li>
                                  <li>  Pilih YA untuk melanjutkan pembayaran</li>
                                </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black">ALFAMART, KANTOR POS DAN PEGADAIAN</li>
                                <ul style="list-style-type:square">
                                    <li>Catat 12 digit Kode Pembayaran FinPay yang Anda dapatkan pada halaman terakhir saat melakukan pembayaran  di website siakad (021xxxxxxxxx)</li>

                                    <li>    Lakukan pembayaran ke gerai Alfamart, Kantor Pos atau Pegadaian terdekat.</li>

                                    <li>      Informasikan kepada Kasir bahwa Anda akan melakukan pembayaran Finpay021, lalu tunjukan Kode Pembayaran yang Anda terima dari siakad</li>

                                    <li>     Selanjutnya, Kasir akan memasukkan Kode Pembayaran yang Anda berikan tersebut ke dalam aplikasi yang ada di Kasir dan akan menginformasikan jumlah yang harus Anda bayarkan.</li>

                                    <li>     Lakukan pembayaran sesuai jumlah yang diinformasikan, dan tunggu hingga proses selesai.</li>

                                    <li>     Mintalah bukti pembayaran Finpay dari Kasir.</li>

                                    <li>     Anda tidak perlu melakukan konfirmasi pembayaran di website siakad Jika pembayaran Anda berhasil, maka Anda akan menerima  Email notifikasi dari siakad</li>

                                    <li>     Jika Anda tidak melakukan pembayaran pada batas waktu yang ditentukan, maka order Anda akan dibatalkan otomatis oleh sistem</li>
                                </ul>
                              </ul>
                      <?php }elseif($tmpbayar['sof_id']=="vamandiri"){ ?>
                        <ul>
                                <li class="tx-bold tx-black">Melalui transfer ATM</li>
                                <ul style="list-style-type:square">
                                  <li>Silahkan pilih Bayar/Beli</li>
                                  <li>Kemudian pilih Lainnya > Lainnya > Multi Payment<</li>
                                  <li>Silahkan masukkan kode perusahaan dan pilih Benar</li>
                                  <li>Masukkan kode bayar dengan nomor Virtual Account Anda (contoh: 7810202001539202) dan klik Benar</li>
                                  <li>Jangan lupa untuk memeriksa informasi yang tertera pada layar. Pastikan Merchant adalah Finnet Indonesia  dan total tagihan sudah benar. Jika benar, tekan angka 1 dan pilih Ya</li>
                                  <li>Periksa layar konfirmasi dan pilih Ya</li>
                                </ul>
                            </ul>

                            <ul>
                                <li class="tx-bold tx-black">Mbanking Mandiri</li>
                                <ul style="list-style-type:square">
                                  <li>Silahkan login ke mBanking Anda. Pilih Bayar. Atau apabila Anda menggunakan bahasa inggris, silahkan pilih Payment. Setelah itu klik Lainnya atau Other</li>
                                  <li>Klik Penyedia Layanan atau Service Provider kemudian pilih Transferpay Masukkan kode bayar dengan Virtual Account Number Anda (contoh: 7810202001539202), kemudian pilih Lanjut atau Proceed.</li>
                                  <li>Jangan lupa untu memeriksa informasi yang tertera di layar! Pastikan Merchant adalah Finnet Indonesia dan total tagihan sudah benar. Jika sudah benar, masukkan PIN Anda dan pilih OK.</li>
                                </ul>
                            </ul>
                     <?php }elseif($tmpbayar['sof_id']=="vabni"){ ?>
                          <ul>
                                <li class="tx-bold tx-black">Melalui transfer ATM</li>
                                <ul style="list-style-type:square">
                                  <li>Pilih Menu Lain > Transfer</li>
                                  <li>Pilih rekening asal dan pilih rekening tujuan ke rekening BNI<</li>
                                  <li>Masukkan nomor rekening dengan nomor Virtual Account Anda (contoh: 7810202001539202) dan pilih Benar</li>
                                  <li>Masukkan jumlah pembayaran sejumlah tagihan Anda dan pilih Benar</li>
                                  <li>Periksa data di layar. Pastikan Nama adalah nama penerima Anda di siakad dan Total Tagihan benar. Apabila ata sudah benar, pilih Ya </li>
                                </ul>
                            </ul>
                            <ul>
                                <li class="tx-bold tx-black">M-Banking BNI</li>
                                <ul style="list-style-type:square">
                                  <li>Pilih Transfer > Antar Rekening BNI</li>
                                  <li>Pilih rekening asal dan pilih rekening tujuan ke rekening BNI<</li>
                                  <li>Pilih Rekening Tujuan > Input Rekening Baru. Masukkan nomor rekening dengan nomor Virtual Account Anda (contoh: 7810202001539202) dan klik Lanjut, kemudian klik Lanjut lagi.</li>
                                  <li>Masukkan jumlah pembayaran sejumlah tagihan Anda. Lalu, klik Lanjutkan</li>
                                  <li>Periksa detail konfirmasi. Pastikan Nama Rekening Tujuan adalah nama penerima Anda dan nominal transfer sudah benar. Jika benar, masukkan password transaksi dan klik Lanjut </li>
                                </ul>
                            </ul>

                      <?php }elseif($tmpbayar['sof_id']=="vapermata"){ ?>
                        <ul>
                                <li class="tx-bold tx-black">Melalui transfer ATM</li>
                                <ul style="list-style-type:square">
                                  <li>Silahkan pilih menu Transaksi Lainnya. Setelah itu klik menu Transfer lalu klik menu Rek NSB Lain Permata</li>
                                  <li>Masukkan nomor rekening dengan nomor Virtual Account Anda (contoh: 7810202001539202) dan pilih Benar<</li>
                                  <li>Kemudian masukkan jumlah nominal transaksi sesuai dengan invoice yang ditagihkan pada anda. Setelah itu pilih Benar</li>
                                  <li>Lalu pilih rekening anda. Tunggu sebentar hingga muncul konfirmasi pembayaran. Kemudian pilih Ya</li>
                                  <li>Periksa data di layar. Pastikan Nama adalah nama penerima Anda di siakad dan Total Tagihan benar. Apabila ata sudah benar, pilih Ya </li>
                                </ul>
                            </ul>

                      <?php } ?>
                          </p>
                    
                  </div>
                </div>
                
              </div>
            </div>
            
          </div>
        </div>
        
      </div>

    </div>

    <script type="text/javascript">
      function goTo(page) {
            location.href = page;
        }

        function getPage(page) {
            return "index.php?page="+page;
        }
        function goCancel(){

        }

        function goView(page) {
            location.href = getPage(page);
        }
    </script>
  </body>
</html>