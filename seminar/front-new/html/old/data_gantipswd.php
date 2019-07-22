<?php
	require_once($conf['model_dir'].'m_pendaftar.php');
    require_once ($conf['helpers_dir'].'date.class.php');
    require_once ($conf['helpers_dir'].'pendaftaran.class.php');
    // Pendaftaran::delSession();
    
	//cek session
	if($_SESSION['PENDAFTARAN']['FRONT']['USERID'] == null or $_SESSION['PENDAFTARAN']['FRONT']['USERID'] == ''){
		header("Location: index.php");
	}
	// var_dump($_SESSION['PENDAFTARAN']['FRONT']['USERID']);
    if(isset($_POST['changepswd'])){
		if (!empty($_POST['captcha'])) {
			if (empty($_SESSION['captcha']) || trim(strtolower($_POST['captcha'])) != $_SESSION['captcha']) {
				$alert = "Kode keamanan tidak cocok";
			} 
			else {
				$nopendaftar=$_SESSION['PENDAFTARAN']['FRONT']['USERID'];
				//cek password lama, jika benar bisa ganti
				$cek_pswdlama = $conn->GetOne("select pswd from pendaftaran.pd_pendaftar where nopendaftar='$nopendaftar'");
				$pswdbaru = $_POST['pswdbaru'];
				$pswdbarulagi = $_POST['pswdbarulagi'];
				if(md5($_POST['pswdlama']) == $cek_pswdlama){
					if($pswdbaru == $pswdbarulagi){
						$passwordbaru = md5($pswdbaru);
						$update = $conn->Execute("update pendaftaran.pd_pendaftar set pswd='$passwordbaru' where nopendaftar='".$nopendaftar."'");
						$succ="Password Lama Anda berhasil diganti dengan Password baru!";
					}else{
						$alert="Password Baru dan Konfimasi Password Baru yang Anda masukkan tidak sama! Ulangi kembali!";
					}
				}else{
					$alert="Password Lama yang Anda masukkan salah! Ulangi kembali!";
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
        <h2>Ganti Password Login Pendaftaran</h2>
      </div>
      <div class="col-md-6 col-md-offset-3">
		<?php
			if(!empty($alert)){
		?>
		<div class="alert alert-danger"><?php echo $alert; ?></div>
		<?php
		}
		if(!empty($succ)){
		?>
		<div class="alert alert-success"><?php echo $succ; ?></div>
		<?}?>
     <form role="form" action="" method="post">
     	  <div class="form-group">
            <label>Password Lama <span style="color: #ff0404">*</span></label>
            <input type="password" name="pswdlama" class="form-control">
          </div>
		  <div class="form-group">
            <label>Password Baru <span style="color: #ff0404">*</span></label>
            <input type="password" name="pswdbaru" class="form-control">
          </div>
		  <div class="form-group">
            <label>Konfirmasi Password Baru <span style="color: #ff0404">*</span></label>
            <input type="password" name="pswdbarulagi" class="form-control">
          </div>
          <div class="form-group">
            <label>Kode Konfirmasi <span style="color: #ff0404">*</span></label><br/>
            <img src="../includes/cool-php-captcha/captcha.php" class="col-md-6"/>
            <span class=" col-md-6"><input type="text" name="captcha"  class="form-control"></span>
          </div>
          <div class="form-group">
            <div class="pull-right" style="margin-top:20px">
              <button type="submit" class="btn btn-success" name="changepswd" id="btnSave">Ganti Password</button>
            </div>
          </div>
     </form>
      </div>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>
