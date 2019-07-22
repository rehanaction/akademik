<?php
  defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

  require_once(Route::getModelPath('mahasiswa'));
  require_once(Route::getModelPath('krs'));
  require_once(Route::getModelPath('perwalian'));
  require_once(Route::getModelPath('akademik'));
  require_once(Route::getModelPath('tagihan'));
  require_once(Route::getUIPath('form'));
  $p_title = 'Form Pembayaran Rutin';
  $p_tbwidth = '100%';
  $p_aktivitas = 'Transaksi';
  $r_act = $_POST['act'];
  $r_semester = CStr::removeSpecial($_REQUEST['semester']);
  $r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
  $r_periode=$r_tahun.$r_semester;
  if(!empty($_REQUEST['key'])){
    $r_key = CStr::removeSpecial(Akademik::base64url_decode($_REQUEST['key']));
  }else{
      $r_key=$_SESSION[SITE_ID]['MODUL']['USERNAME'];
  }
  $p_foto = uForm::getPathImageMahasiswa($conn,$r_key);
  $a_infomhs = mMahasiswa::getDataSingkat($conn,$r_key);
  $r_periode = mAkademik::getPeriodeSekarang($conn);
  $a_tagihan2 = mTagihan::getInquiry($conn,$r_key,$r_kelompok);
  $date = date('Ymdhms');

$tmpbayar = mTagihan::getTmpPembayaran($conn,$a_infomhs['nim']);
if(empty($tmpbayar)){
        if($r_act == 'payment' and !empty($a_infomhs)){
            //$inv = $date."".$a_infomhs['nim'];
            $inv = mTagihan::generateRandomString();
            if(empty($inv)){
                $inv = $date;
            }
            $data=array();
            $datasort = array();
            $data['add_info1']=$a_infomhs['nama'];
            $data['add_info2']='pay register';
            $data['add_info3']=CStr::removeSpecial($a_infomhs['nama']);
            $data['add_info4']=$a_infomhs['nim'];
            $data['add_info5']=$a_infomhs['jurusan'];
            $data['amount']=$_POST['labeltotal'];
            $data['cust_email']=$a_infomhs['email'];
            $data['cust_id'] =$a_infomhs['nim'];
            $data['cust_msisdn']=$a_infomhs['hp'];
            $data['cust_name']=$a_infomhs['nama'];
            $data['invoice']=$inv; 
            $data['merchant_id']=$conf['merchant_id']; //nanti di simpen di config
            $data['return_url']='https://siakad.inaba.ac.id/siakad/siakad/index.php?page=v_pembayaran';
            $data['sof_id']=$_POST['sof_id'];
            $data['sof_type']='pay';
            $data['success_url']='https://siakad.inaba.ac.id/siakad/siakad/index.php?page=v_pembayaran';
            $data['timeout']=120; //waktu transaksi expired 6 jam 360 menit
            $data['trans_date']=$date;
            $data['failed_url']='https://siakad.inaba.ac.id/siakad/siakad/index.php?page=pembayaran';
            $password=$conf['merchant_password']; // nanti di simpen di config 
            ksort($data);
            $componetSignature = '';
            
            foreach ($data as $key => $val) {
                //echo $key." : ".$val."<br/>";
                $componetSignature = $componetSignature."".strtoupper($val)."%";
            }
            
            //echo "<br/><br/> Componet Signature : ".$componetSignature."".$password."<br/><br/>";
            //print_r($componetSignature."".$password);
            //echo "Hash Result : ".strtoupper(hash("sha256",$componetSignature."".$password));
            $data['mer_signature']=strtoupper(hash("sha256",$componetSignature."".$password));
            $result=mMahasiswa::BayarTagihan($data);

        if(!empty($result['payment_code'])){
            $tmppayment = array();
            foreach($_POST['tagihan'] as $row){
            
                $tmppayment['kodepembayaran']=$result['payment_code'];
                $tmppayment['noinvoice']=$inv;
                $tmppayment['idtagihan']=$row;
                $tmppayment['nominaltagihan']=$_POST[$row];
                $tmppayment['sof_id']=$_POST['sof_id'];
                $tmppayment['nim']=$a_infomhs['nim'];
                $tmppayment['datecreate']=date('Y-m-d H:i:s');
                $tmppayment['expireddate']=date('Y-m-d H:i:s',strtotime('+'.$data['timeout'].' minutes',strtotime($tmppayment['datecreate'])));
                $ok=mTagihan::insertTmpPembayaran($conn,$tmppayment);
               
                //insert db tmp
            }
            $subject = "Kode Pembayaran Kuliah";
            $body = 'Pembayaran Kuliah STIE INABA'
                        .'<br>  Kode Pembayaran             : '.$result['payment_code']
                        .'<br>  NIM                         : '.$a_infomhs['nim']
                        .'<br>  Nama Mahasiswa              : '.$a_infomhs['nama']
                        .'<br>  Metode Pembayaran           : '.mTagihan::metodePembayaran($_POST['sof_id'])
                        .'<br>  Lakukan Transaksi Sebelum   : '.date('d-m-Y H:i:s',strtotime($tmppayment['expireddate'])).'<br>'
                        .'<br> <strong> Setelah melakukan pembayaran harap cek kembali data tagihan anda di siakad.inaba.ac.id jika data tagihan belum terupdate segera hubungi bagian Keuangan STIE INABA Dengan Membawa Bukti pembayaran </strong><br>'
                        .'<br> Jika Anda tidak melakukan pembayaran pada batas waktu yang ditentukan, maka pembayaran kuliah Anda akan dibatalkan otomatis oleh sistem <br>';
            mTagihan::sendMail($a_infomhs['email'],$subject,$body);
            header("Location: https://siakad.inaba.ac.id/siakad/siakad/index.php?page=v_pembayaran");
        }else if(!empty($result['redirect_url'])){
            $tmppayment = array();
                foreach($_POST['tagihan'] as $row){
            
                        $tmppayment['url']=$result['redirect_url'];
                        $tmppayment['kodepembayaran']=$inv;
                        $tmppayment['noinvoice']=$inv;
                        $tmppayment['idtagihan']=$row;
                        $tmppayment['nominaltagihan']=$_POST[$row];
                        $tmppayment['sof_id']=$_POST['sof_id'];
                        $tmppayment['nim']=$a_infomhs['nim'];
                        $tmppayment['datecreate']=date('Y-m-d h:i:s');
                        $tmppayment['expireddate']=date('Y-m-d h:i:s',strtotime('+'.$data['timeout'].' minutes',strtotime($tmppayment['datecreate'])));
                        $ok =mTagihan::insertTmpPembayaran($conn,$tmppayment);
                        
            
                //insert db tmp
                }   
                $subject = "Kode Pembayaran Kuliah";
                $body = 'Pembayaran Kuliah STIE INABA'
                            .'<br>  Link Pembayaran             : '.$result['url']
                            .'<br>  NIM                         : '.$a_infomhs['nim']
                            .'<br>  Nama Mahasiswa              : '.$a_infomhs['nama']
                            .'<br>  Metode Pembayaran           : '.mTagihan::metodePembayaran($_POST['sof_id'])
                            .'<br>  Lakukan Transaksi Sebelum   : '.date('d-m-Y H:i:s',strtotime($tmppayment['expireddate'])).'<br>'
                            .'<br> <strong> Setelah melakukan pembayaran harap cek kembali data tagihan anda di siakad.inaba.ac.id jika data tagihan belum terupdate segera hubungi bagian IT STIE INABA Dengan Membawa Bukti pembayaran </strong><br>'
                            .'<br> Jika Anda tidak melakukan pembayaran pada batas waktu yang ditentukan, maka pembayaran kuliah Anda akan dibatalkan otomatis oleh sistem <br>';
                mTagihan::sendMail($a_infomhs['email'],$subject,$body);
                header("Location: https://siakad.inaba.ac.id/siakad/siakad/index.php?page=v_pembayaran");
        }elseif($result['status_code'] == 307){
                $p_msg = $result['status_desc'];
           
        }else{

                $p_msg =$result['status_desc'];
            }
        }
}else{
    header("Location: https://siakad.inaba.ac.id/siakad/siakad/index.php?page=v_pembayaran");
}


?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title>FORM PEMBAYARAN</title>
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

  <body oncontextmenu="return false;">
    <div class="br-logo"><a href=""><span>[</span>STIE <i>INABA</i><span>]</span></a></div>

    <div class="br-header">
      <div class="br-header-left">
        <div class="navicon-left hidden-md-down"><a href="javascript:void(0);"  title="Kembali" onclick="goView('home')"><i class="icon ion-arrow-left-a"></i></a></div>
        <div class="navicon-left hidden-lg-up"><a href="javascript:void(0);" title="Kembali" onclick="goView('home')"><i class="icon ion-arrow-left-a"></i></a></div>
      </div>

    </div>

    <div class="br-mainpanel" style="margin-left: 0px; margin-bottom: 50px; margin-top: 100px;">

      <div class="br-pagebody">

        <div class="card bd-gray-400">
          <div class="card-body pd-30 pd-md-60">
            <div class="d-md-flex justify-content-between flex-row-reverse">
              <h1 class="mg-b-0 tx-uppercase tx-gray-400 tx-mont tx-bold">FORM PEMBAYARAN</h1>
              <div class="mg-t-25 mg-md-t-0">
                <h6 class="tx-primary"><?=$a_infomhs['nama']?></h6>
                <p class="lh-7"><?=$a_infomhs['nim']?><br><?=$a_infomhs['jurusan']?><br><?=$a_infomhs['namasistemkuliah']?><br>
                <?=$a_infomhs['nohp'] ?><br>
                <?=$a_infomhs['email'] ?></p>
              </div>
            </div>

            <form name="pageform" id="pageform" method="post">
            <div class="alert alert-danger" role="alert">
                   <h3> Prasyarat Pembayaran Tagihan </h3><br/>
                        Melihat Nilai Semester <br/>
                        &nbsp&nbsp&nbsp- Tidak Ada Tunggak Semester Sebelumnya <br/>
                        Input KRS <br/>
                        &nbsp&nbsp&nbsp- Melunasi BPP & Registrasi Semester <br/>
                        Mengikuti UTS <br/>
                        &nbsp&nbsp&nbsp- 50% Total Tagihan SKS dan 100% Tagihan Pengembangan <br/>
                        Mengikuti UAS <br/>
                        &nbsp&nbsp&nbsp- 100% Total Tagihan/ Tagihan Lunas <br/>
            </div>
                <?php  if(!empty($_SESSION['message_done'])) { unset($_SESSION['message_done']); ?>     
                    <div class="alert alert-success" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                        <?php echo "Pembayaran Berhasil Dilakukan" ?>
                    </div>
                 <?php } ?>    
                 <?php if(!empty($_SESSION['message_cancel'])) { unset($_SESSION['message_cancel']); ?>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                        <?php echo "Pembayaran Dibatalkan" ?>
                    </div>
                <?php  }elseif (!empty($p_msg)) { ?>
                    <div class="alert alert-danger" role="alert">
                        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">×</span>
                        </button>
                        <?php echo "Sedang Terjadi Gangguan / Coba Metode Pembayaran Lain" ?>
                    </div>
                <?php } ?>
            <? if (mMahasiswa::validate_email($a_infomhs['email']) and !empty($a_infomhs['hp'])){ ?>
            <div class="table-responsive mg-t-10">
              <table class="table" style="margin-bottom: 0px;">
                <thead>
                  <tr>
                    <th class="wd-5p">#</th>
                    <th class="wd-60p">Jenis Tagihan</th>
                    <th class="wd-60p">Periode</th>
                    <th class="wd-60p tx-right">Nominal</th>
                    <th class="tx-center">Pilih</th>
                  </tr>
                </thead>
                <tbody>
                    <? 
                        $no = 1;
                        $tagihansebelumnya = 0;
                        $ck = '';
                        $dpnone = '';
                        foreach($a_tagihan2 as $row){
                        $t_key = $row['idtagihan'];                
                        $jumlah = $row['nominaltagihan']-($row['nominalbayar']+$row['potongan']);
                     
                        if($row['periode']<$r_periode){
                          $tagihansebelumnya+=$jumlah;
                          $ck = 'checked style="visibility: hidden"';
      
                        }else{
                          $ck = '';
                        }
                    ?>
                      <tr>
                        <td style="display: none"><?= UI::createTextBox($t_key,$jumlah,'ControlStyle ControlNumber','20','20',true,"readonly style='text-align:right'")?></td>
                        <td><?php echo $no; ?></td>
                        <td><?= $row['namajenistagihan']."- Angsuran ".substr($row['idtagihan'],8,1); ?></td>
                        <td><?= Akademik::getNamaPeriode($row['periode']); ?></td>
                        <td class="tx-right">Rp. <?=CStr::FormatNumber($jumlah)?></td>
                        <td class="tx-center">
      
                              <input type="checkbox" name="tagihan[]" id="cek<?=$t_key?>" value="<?=$t_key?>" <?=$ck?> onChange="hitungTotal('<?=$t_key?>','<?=$jumlah?>')">
                              <span></span>

                        </td>
                      </tr>
                    <?php $no++; } ?>
                 </tbody>
                </table>
                <table class="table" style="margin-bottom: 0px;">
                <tbody>
                  <tr>
                    <td rowspan="3" class="wd-60p valign-middle">
                      <div class="mg-r-20">
                        <label class="tx-uppercase tx-13 tx-bold mg-b-10">Catatan</label>
                        <p class="tx-13">Maksimal Pembayaran 2 jam, setelahnya akan dianggap batal</p>
                      </div>
                    </td>
                    <td class="tx-right valign-middle">Metode Pembayaran</td>
                    <td class="tx-right valign-middle">
                        <select class="form-control" id="sof_id" name="sof_id" onchange="BeforePayment(value)">
                            <option value="0">Pilih</option>
                                <optgroup label="FinPay">
                                    <option value="finpay021">Alfamart/Pegadaian/Kantor Pos</option>
                                </optgroup>
                                <optgroup label="Virtual Account">
                                    <!--<option value="vabni">Bank BNI</option>-->
                                    <option value="vamandiri">Bank Mandiri</option>
                                    <!--<option value="vapermata">Bank Permata</option>-->
                                </optgroup> 
                                <?php /*
                                
                                <optgroup label="Kartu Kredit">
                                    <option value="cc" disabled>Visa</option>
                                    <option value="cc" disabled>Master Card</option>
                                </optgroup>
                                */ ?>
                        </select>
                    </td>
                  </tr>
                  <tr>
                    <td class="tx-right valign-middle">Biaya Transfer</td>
                    <td class="tx-right valign-middle wd-20p"><h4 class="tx-inverse tx-bold tx-lato">Rp. <?= UI::createTextBox('adm',0,'ControlStyle ControlNumber tx-inverse tx-bold tx-lato','9','9',true,'readonly style="text-align:right;border: 0px none;"')?></h4></td>
                  </tr>
                  <tr>
                    <td class="tx-right tx-uppercase tx-bold tx-inverse valign-middle">Total</td>
                    <td class="tx-right valign-middle wd-20p">
                        <h4 class="tx-primary tx-bold tx-lato"><input type="hidden" name="jumlahtotal" id="jumlahtotal" value=<?= $tagihansebelumnya ?>>
                            Rp. <?= UI::createTextBox('labeltotal',$tagihansebelumnya,'ControlStyle ControlNumber tx-primary tx-bold tx-lato','9','9',true,'readonly style="text-align:right;border: 0px none;"')?>
                        </h4>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>

            <hr class="mg-b-20">
              <div class="row justify-content-between">
                <div class="col-4">
                 <a href="javascript:void(0);" class="btn btn-danger btn-block mg-b-10" onclick="goView('home')"><i class="icon ion-arrow-left-a"></i> Kembali</a>
                </div>
                <div class="col-4">
                  <button class="btn btn-primary btn-block mg-b-10" id="btnpayment" onclick="goPayment()" disabled>Bayar <i class="icon ion-arrow-right-a"></i></button>
                </div>
              </div>
           
            <? }else{
                echo "<center><strong>Pastikan Alamat Email Dan No Hp Pada Biodata Sudah Terinput Dan Data email & No Hp yang diinput valid</strong></center>";
            } ?>
            <input type="hidden" name="act" id="act">
            </form>

          </div>
        </div>

      </div>
    </div>

   

    <script type="text/javascript">
        $(document).ready(function() {        
            $("#tab-va").remove();
            $("#tab-finpay").remove();
            $("#tab-kkredit").remove();
        });
        document.onkeydown = function(e) {
            if(event.keyCode == 123) {
            return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'I'.charCodeAt(0)){
            return false;
            }
            if(e.ctrlKey && e.shiftKey && e.keyCode == 'J'.charCodeAt(0)){
            return false;
            }
            if(e.ctrlKey && e.keyCode == 'U'.charCodeAt(0)){
            return false;
            }
        }
        
        function hitungTotal(id,tagihan){
                var total;
                var tagihan;
                total = document.getElementById("jumlahtotal").value;
                if(document.getElementById('cek'+id).checked){
                document.getElementById("sof_id").selectedIndex = "0";
                document.getElementById("adm").value=0;
                document.getElementById("btnpayment").disabled = true;
                document.getElementById(id).readOnly = true;
                document.getElementById(id).value = tagihan;

                        total = parseInt(total)+parseInt(document.getElementById(id).value.replace(/\./g,''));
                        //alert(document.getElementById(id).value.replace(/\./g,''));
                    }
                else
                    {
                     
                        
                        //document.getElementById(id).readOnly = false;
                        total = parseInt(total)-parseInt(document.getElementById(id).value.replace(/\./g,''))
                        document.getElementById(id).value = 0;
                      
                        if(total==0){
                            var ele = document.getElementsByName("sof_id");
                            document.getElementById("btnpayment").disabled = true;
                            document.getElementById("sof_id").selectedIndex = "0";
                            
                         }else{
                          document.getElementById("sof_id").selectedIndex = "0";
                          document.getElementById("adm").value=0;
                          document.getElementById("btnpayment").disabled = true;
                         }
                       
                        
                    }
            
                document.getElementById("labeltotal").value = total; 
                document.getElementById("jumlahtotal").value = total;
                //document.getElementById("jumlahbayar").value = total;
               
                
      }
      function goPayment(){
            //var kas = noFormat(document.getElementById("jumlahbayar").value);
        var total = document.getElementById("jumlahtotal").value;
            if (isNaN(total)) {
                alert('Harus Berupa Angka');
            }else{
                document.getElementById("act").value = "payment";
                goSubmit();
            }
        }
        function BeforePayment(el) {
            $total=document.getElementById("jumlahtotal").value;
            if($total>0){
                document.getElementById("btnpayment").disabled = false;
                if(el=="finpay021"){
                    document.getElementById("adm").value=2000;
                    document.getElementById("labeltotal").value=parseInt(document.getElementById("jumlahtotal").value)+parseInt(document.getElementById("adm").value)
                }else if(el=="vamandiri"){
                    document.getElementById("adm").value=5500;
                    document.getElementById("labeltotal").value=parseInt(document.getElementById("jumlahtotal").value)+parseInt(document.getElementById("adm").value)
                }else if(el=="vabni"){
                    document.getElementById("adm").value=5500;
                    document.getElementById("labeltotal").value=parseInt(document.getElementById("jumlahtotal").value)+parseInt(document.getElementById("adm").value)
                }else if(el=="cc"){
                    document.getElementById("adm").value=(parseInt(document.getElementById("jumlahtotal").value)*0.025)+2000;
                    document.getElementById("labeltotal").value=parseInt(document.getElementById("jumlahtotal").value)+parseInt(document.getElementById("adm").value)
                }
            }else{
                //var ele = document.getElementsByName("sof_id");
                document.getElementById("adm").value=0;
                
                alert("Harap Memilih Tagihan yang akan di bayar terlebih dahulu");
                document.getElementById("sof_id").selectedIndex = "0";
               
            }
        }

        function goTo(page) {
            location.href = page;
        }

        function getPage(page) {
            return "index.php?page="+page;
        }

        function goView(page) {
            location.href = getPage(page);
        }
    </script>

</body>
</html>