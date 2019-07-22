<?php


	require_once(Route::getModelPath('token'));
    Pendaftaran::delSession();
  
    list($r_periode,$r_gelombang,$r_jalur) = explode('|',base64_decode($_REQUEST['q']));
    $r_act = $_POST['act'];
    $r_capca = trim(strtolower($_POST['captcha']));
    $v_capca = $_SESSION['captcha'];
    $r_token = $_POST['tokenpendaftaran'];
  
    if ($r_act =='reg' and !empty($r_capca) and !empty($v_capca) and (!empty($r_token)) and ($r_capca == $v_capca)){
		$ok = true;
		/* mulai pengecekan token */
		$p_posterr=mToken::cekToken($conn, $r_token,$r_periode,$r_jalur,$r_gelombang); //cek token apakah ada di daftar pembayaran
		if ($p_posterr){
			list($p_posterr,$p_postmsg) = array(true,'Token Tidak dikenali di daftar pembelian token oleh sistem kami, silahkan masukkan token yang sesuai.');
			$ok = false;
		}else {
		$p_posterr = mToken::cekPeriode($conn,$r_token,$r_periode,$r_gelombang,$r_jalur); //cek periode token dan pilihan
			if ($p_posterr){
				list($p_posterr,$p_postmsg) = array(true,'Token yang anda masukkan tidak sesuai dengan periode, jalur dan gelombang yang anda pilih.');
				$ok = false;
			}else{
				$p_posterr = mToken::isUsed($conn, $r_token); // cek token apakah telah digunakan
				if ($p_posterr){
					list($p_posterr,$p_postmsg) = array(true,'Token  '.$p_posterr.' telah digunakan, token hanya bisa digunakan 1 kali.');
					$ok = false;
				}	
			}
		}
		/* selesai pengecekan token */
		
		if (empty($p_posterr) and $ok){
			session_start();
			
				pendaftaran::setDataToken($r_token);
				
				Route::navigate('data_input');
				
		}
	}else if (empty($r_capca) and !empty($r_act)){
		list($p_posterr,$p_postmsg) = array(true,'Silahkan isi captcha (Kode Keamanan)<br>Silahkan ulangi proses pendaftaran');
	}else if (empty($v_capca) and !empty($r_act)){
		list($p_posterr,$p_postmsg) = array(true,'Anda telah lama meninggalkan halaman ini<br>Silahkan ulangi proses pendaftaran');
	}else if (empty ($r_token) and !empty($r_act)){
		list($p_posterr,$p_postmsg) = array(true,'Token belum di isi<br>Silahkan ulangi proses pendaftaran');		
	}

 require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Registrasi Peserta <?= $r_periode.','.$r_jalur.','.$r_gelombang?></h2>
      </div>
      <div class="col-md-6 col-md-offset-3">
      <?php if(!empty($p_postmsg)) { ?>
			<center>
			<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
				<?= $p_postmsg ?>
			</div>
			</center>
			<div class="Break"></div>
		<?	} ?>
		
		
     <form role="form" action="" method="post" name="pageform" id="pageform">
     	 <div class="form-group">
            <label>No. Token <span style="color: #ff0404">*</span></label>
            <input type="text" name="tokenpendaftaran"  class="form-control">
          </div>
          <div class="form-group">
            <label>Kode Konfirmasi <span style="color: #ff0404">*</span></label><br/>
            <img src="../includes/cool-php-captcha/captcha.php" class="col-md-6"/>
            <span class=" col-md-6"><input type="text" name="captcha"  class="form-control"></span>
          </div>
          <div class="form-group">
            <div class="pull-right" style="margin-top:20px">
              <button type="button" onclick="goToken()" class="btn btn-success" name="reg" id="btnSave">Daftar</button>
            </div>
          </div>
          
          <input type="hidden" name="act" id="act">
     </form>
      </div>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>


<script type="text/javascript">

	function goToken(){
		$('#act').val('reg');
		goSubmit();
	}

</script>
