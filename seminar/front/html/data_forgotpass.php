<?php
	  require_once(Route::getModelPath('pendaftar','front'));

    // Pendaftaran::delSession();
    if(isset($_POST['forgot'])){
      
      if (!empty($_POST['captcha'])) {

  			if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
  				$alert = "Kode keamanan tidak cocok";
  			}else {

  				$nopendaftar = $_POST['nopendaftar'];
  				$email = $_POST['email'];
  				
  				$ok = mPendaftarFront::cekPassword($conn,$nopendaftar,$email);

				if($ok==true){
  					//jika ada data, ganti/update password dg yg baru
  					$rand = Date::RandomCode(6);
  					$record['pswd'] = $rand;

  					mPendaftarFront::updatePassword($conn,$nopendaftar,$record['pswd']);

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

<div class="container bg-white">
  <div class="col-md-8">
          <div class="page-header">
              <h3>Lupa Password Login Pendaftar</h3>
          </div>
      <div class="well col-md-12 pull-left">

<?php if(!empty($alert)) { ?>
          <div class="alert alert-danger">
              <?php echo $alert; ?>
          </div>
<?php } ?>
                  <form role="form" action="" method="post" class="col-md-8 col-md-offset-2">
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
                          <div class="row">
                          <img src="../includes/cool-php-captcha/captcha.php" class="col-md-6" />
                          <span class=" col-md-6"><input type="text" name="captcha"  class="form-control"></span>
                          </div>
                      </div>
                      <div class="form-group">
                          <div class="pull-right" style="margin-top:20px">
                              <a href="<?php echo Route::navAddress('login_form') ?>">&laquo; Kembali ke Halaman Login</a> &nbsp;&nbsp;
                              <button type="submit" class="btn btn-success" name="forgot" id="btnSave">Cek Password</button>
                          </div>
                      </div>
                  </form>
      </div>
  </div>
        <?php require_once('inc_sidebar.php'); ?>
</div>

<?php require_once('inc_footer.php'); ?>
