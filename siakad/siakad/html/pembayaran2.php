<?php
  defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
  //$conn->debug = true;
  // hak akses
 // $a_auth = Modul::getFileAuth();

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
  $a_tagihan2 = mTagihan::getInquiry($conn,$r_key,$r_kelompok);;
  $r_periode = mAkademik::getPeriodeSekarang($conn);
  $date = date('Ymdhms');

 //print_r($a_tagihan2);
 //print_r($_POST);
 //echo $date;
 //echo $r_act;
$tmpbayar = mTagihan::getTmpPembayaran($conn,$a_infomhs['nim']);
if(empty($tmpbayar)){
        if($r_act == 'payment' and !empty($a_infomhs)){
            $inv = $date."".$a_infomhs['nim'];
            $data=array();
            $datasort = array();
            $data['add_info1']=$a_infomhs['nama'];
            $data['add_info2']='pay register';
            $data['add_info3']=$a_infomhs['nama'];
            $data['add_info4']=$a_infomhs['nama'];
            $data['add_info5']=$a_infomhs['nama'];
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
           print_r($result);
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
                        .'<br> <strong> Setelah melakukan pembayaran harap cek kembali data tagihan anda di siakad.inaba.ac.id jika data tagihan belum terupdate segera hubungi bagian IT STIE INABA Dengan Membawa Bukti pembayaran </strong><br>'
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
        }else{
                $p_msg =$result['status_desc'];
            }
        }
}else{
    header("Location: https://siakad.inaba.ac.id/siakad/siakad/index.php?page=v_pembayaran");
}


?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
    <link href="style/sweetalert2.css" rel="stylesheet" type="text/css">
	<script type="text/javascript" src="scripts/forpager.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
    <script type="text/javascript" src="scripts/sweetalert2.min.js"></script>
</head>
<body>
<div id="main_content">
  <?php require_once('inc_header.php'); ?>
    <div id="wrapper">
		  <div class="SideItem" id="SideItem">
			    <form name="pageform" id="pageform" method="post">

            <?	if(!empty($_SESSION['message_done'])) { unset($_SESSION['message_done']); ?>
                   
                    <div class="DivSuccess" style="width:<?= $p_tbwidth ?>px">
                        <?php echo "Pembayaran Berhasil Dilakukan" ?>
                    </div>
                    
                    <div class="Break"></div>
             <?	} ?>
             <?	if(!empty($_SESSION['message_cancel'])) { unset($_SESSION['message_cancel']); ?>
                   
                   <div class="DivError" style="width:<?= $p_tbwidth ?>px">
                       <?php echo "Pembayaran Dibatalkan" ?>
                   </div>
                   
                   <div class="Break"></div>
            <?	} ?>


            <?	/**************/
				      	/* JUDUL LIST */
				    	/**************/
					
					      if(!empty($p_title) and false) {
				    ?>
            <center><div class="ViewTitle" style="width:<?= $p_tbwidth ?>px;"><span><?= $p_title ?></span></div></center>
			    	<br>
			    	<?	}
					
				      	/************************/
				      	/* INQUIRY FORM */
				      	/************************/
					
			    	?>
            <table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" align="center">
                <?  	/**********/
                     /* HEADER  DATA MHS*/
                    /**********/
				          	if($a_infomhs['nim']) {
					        ?>
                    <tr>
                    <td>
                    <table <?=$style?> width="100%">
                    <tr>
                    	<td  width="13%"><strong> NIM</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td width="35%" style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$a_infomhs['nim']?></td>
                        <td width="13%"><strong> Sistem Kuliah</strong></td>
                        <td width="1%"><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$a_infomhs['namasistemkuliah']?></td>
                    </tr>
					          <tr>
                    	<td><strong> Nama</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$a_infomhs['nama']?></td>
                        <td><strong> Jalur Penerimaan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$a_infomhs['jurusan']?></td>
                    </tr>
				          	<tr>
                    	<td><strong> Jurusan</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$a_infomhs['jurusan'].' '.$a_infomhs['namaunit']?></td>
                        <td><strong> Periode Inquiry</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$r_periode?></td>
                    </tr>
                    <tr>
                        <td><strong>No. Hp</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$a_infomhs['hp']?></td>
                        <td><strong>Email</strong></td>
                        <td><strong>:</strong></td>
                        <td style="border-bottom:dashed; border-bottom-width:thin; border-bottom-color:#999"><?=$a_infomhs['email']?></td>
                    </tr>
                    </table>
                    </td>
                    </tr>
                    <? }?>
                    <tr>
                    <td colspan="5">&nbsp;</td>
                    </tr>
                    <tr>
                    <td colspan="5">
                <? if (mMahasiswa::validate_email($a_infomhs['email']) and !empty($a_infomhs['hp'])){ ?>
                    <table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
                    <tr>
                        <th width="15%">Id Tagihan</th>
                        <th>Jenis Tagihan</th>
                        <th width="15%">Jumlah Tagihan</th>
                        <th width="15%">Sisa Tagihan</th>
                        <th width="15%">Jumlah Bayar</th>
                        <th width="2%"></th>
                    </tr>
                    
                    <? 
                        foreach($a_tagihan2 as $row){
                            $t_key = $row['idtagihan'];                
                            $jumlah = $row['nominaltagihan']-$row['nominalbayar'];
                    ?>
                    <tr>
                      <td><?=$row['idtagihan']?></td>
                      <td><?=$row['namajenistagihan']?></td>
                      <td>Rp. <?=CStr::FormatNumber($row['nominaltagihan'])?></td>
                      <td align="right">Rp. <?=CStr::FormatNumber($jumlah)?></td>
                      <td align="right">
                            <?= UI::createTextBox($t_key,0,'ControlStyle ControlNumber','20','20',true,"readonly style='text-align:right'")?>
                      </td>
                      <td>
                        <input type="checkbox" name="tagihan[]" id="cek<?=$t_key?>" value="<?=$t_key?>" onChange="hitungTotal('<?=$t_key?>','<?=$jumlah?>')">
                        </td>
                    </td>
                    </tr>
                    <? }?>
                    <tr>
                            <td align="center" colspan=4><strong>Biaya Transfer</strong></td>
                            <td style="text-align:right "><input type="text" name="adm" id="adm" value="0" style="text-align:right" readonly><td>
                    </tr>
                    <tr>
                        <th></th>
                        <th style="text-align:right" align="right"></th>
                        <th style="text-align:left" align="left"></th>
                        <th style="text-align:right" align="right">TOTAL</th>
                        <th style="text-align:right ">
                            <input type="hidden" name="jumlahtotal" id="jumlahtotal" value="0">
                            <?= UI::createTextBox('labeltotal',0,'ControlRead','20','20',true,'readonly style="text-align:right;"')?>
                        </th>
                        <th></th>
                    </tr>
                    </table>
                    <table width="100%" cellpadding="4" cellspacing="0" class="GridStyle">
                    <tr>
                        <th colspan=8>Pilih Metode Pembayaran</th>
                        
                    </tr>

                    <tr>
                            <td align="center">
                            <input type="radio" id="sof_id" name="sof_id" value="vabni" onchange="BeforePayment(value)"><br/> 
                             <img width="128px" height="85px" src="images/BankBNI.png" onerror="loadDefaultActImg(this)"><br/>
                             Virtual Account Bank BNI
                             </td>
                            <td align="center">
                            <input type="radio" id="sof_id" name="sof_id" value="vamandiri"  onchange="BeforePayment(value)"><br/>
                            <img width="128px"  src="images/BankMandiri.png" onerror="loadDefaultActImg(this)"> <br/>
                                Virtual Account Bank Mandiri
                            </td>
                            <td align="center" colspan=4><input type="radio" id="sof_id" name="sof_id" value="finpay021"  onchange="BeforePayment(value)"><br/>
                            
                            <img width="200px" height="85px" src="images/finpaygabung.png" onerror="loadDefaultActImg(this)"> <br/>
                            Alfamart/Kantor POS/Pengadaian</td>
                            <td align="center" colspan=4><input type="radio" id="sof_id" name="sof_id" value="cc"  onchange="BeforePayment(value)" disabled><br/>
                            
                            <img width="200px" height="85px" src="images/cc.png" onerror="loadDefaultActImg(this)"> <br/>
                            Kartu Kredit</td>
                    </tr>
                    <tr>
                        <th style="text-align:right" colspan="4" align="right"></th>
                        <th colspan=3>
                             <input type="button" style="height:40px;"  id="btnpayment" value="Payment Tagihan" onClick="goPayment()" disabled>
                        </th>
                       
                        
                    </tr>
                    
                        <? }else{
                              echo "<center><strong>Pastikan Alamat Email Dan No Hp Pada Biodata Sudah Terinput Dan Data email & No Hp yang diinput valid</strong></center>";
                            }
                            
                        ?>
            </table>
            <input type="hidden" name="act" id="act">
          </form>
      </div>
    </div>
</div>
<script type="text/javascript">
function hitungTotal(id,tagihan){
		var total;
		var tagihan;
		total = document.getElementById("jumlahtotal").value;
		if(document.getElementById('cek'+id).checked){
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
                    for(var i=0;i<ele.length;i++)
                        ele[i].checked = false;
                 }
               
                
			}
    
		document.getElementById("labeltotal").value = total; 
		document.getElementById("jumlahtotal").value = total;
		document.getElementById("jumlahbayar").value = total;
       
		
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
            var ele = document.getElementsByName("sof_id");
            document.getElementById("adm").value=0;
            for(var i=0;i<ele.length;i++)
                ele[i].checked = false;
            swal("error","Harap Memilih Tagihan yang akan di bayar terlebih dahulu","error");
           
        }

       
        
    }

</script>
</body>
</html>