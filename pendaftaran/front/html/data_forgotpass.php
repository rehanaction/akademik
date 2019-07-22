<?php
	require_once($conf['model_dir'].'m_pendaftar.php');
    require_once ($conf['helpers_dir'].'date.class.php');
    require_once ($conf['helpers_dir'].'pendaftaran.class.php');
    // Pendaftaran::delSession();
    
    if(isset($_POST['forgot'])){
		if (!empty($_POST['captcha'])) {
			if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
				$alert = "Kode keamanan tidak cocok";
			} 
			else {
				$nopendaftar = $_POST['nopendaftar'];
				$email = $_POST['email'];
				
				$ok=mPendaftar::cekPassword($nopendaftar,$email);
				if($ok==true){
					//jika ada data, ganti/update password dg yg baru
					$rand = Date::RandomCode(6);
					$record['pswd'] = md5($rand);
					mPendaftar::updatePassword($nopendaftar,$record['pswd']);
					$alert = 'Password Anda berhasil di Reset. Password pendaftaran Anda adalah <b>'.$rand.'</b>. Mohon disimpan baik-baik.';
					// Route::navigate('data_forgotpass');
				}else{
					$alert = "Email dan No. Pendaftaran tidak match. Silakan hubungi Administrator Universitas Esa Unggul.";
				}
			}
		}else if( empty($_POST['captcha']) || empty($_POST['tokenpendaftaran']) || empty($_POST['pin'])){
			$alert="Silakan masukkan Kode Konfirmasi!";
		}
    }
 require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Lupa Password Login Pendaftar</h2>
      </div>
      <div class="col-md-6 col-md-offset-3">
      <?php
	if(!empty($alert)){
	    ?>
	    <div class="alert alert-danger"><?php echo $alert; ?></div>
	    <?php
	}
    ?>
     <form role="form" action="" method="post">
     	  <div class="form-group">
            <label>No. Pendaftaran <span style="color: #ff0404">*</span></label>
            <input type="text" name="nopendaftar" class="form-control">
          </div>
		  <div class="form-group">
            <label>Email <span style="color: #ff0404">*</span></label>
            <input type="text" name="email" class="form-control">
          </div>
          <div class="form-group">
            <label>Kode Konfirmasi <span style="color: #ff0404">*</span></label><br/>
            <img src="../includes/cool-php-captcha/captcha.php" class="col-md-6"/>
            <span class=" col-md-6"><input type="text" name="captcha"  class="form-control"></span>
          </div>
          <div class="form-group">
            <div class="pull-right" style="margin-top:20px">
              <button type="submit" class="btn btn-success" name="forgot" id="btnSave">Cek Password</button>
            </div>
          </div>
     </form>
      </div>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>
