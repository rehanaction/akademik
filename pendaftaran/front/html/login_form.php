<?php

	require_once(Route::getModelPath('pendaftar'));
	$r_nopendaftar = $_SESSION[SITE_ID]['FRONT']['USERID'];

	if(isset($_POST['login'])){
		$user=CStr::removeSpecialAll($_POST['nopendaftaran']);
		$pass=CStr::removeSpecialAll($_POST['password']);
		
		$log=Modul::logInFront($conn, $user, $pass);
		if($log){
			Route::navigate('data_input');
		}else {
			$msg="No.Pendaftaran atau Password Anda tidak dikenali";
		}
		if($_SESSION['EXPIRED']){
			 $msg="No.Pendaftaran Anda telah di Suspend, karena melebihi batas Daftar Ulang!";
		}	
		
	}if (isset($_POST['logout'])){
		Modul::logOutFront();
		Route::navigate('home');
	}

	//$dataPendaftar = mPendaftar::getBiodata($conn,$r_nopendaftar);
?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-8">
      <div class="page-header">
        <h3><span class="glyphicon glyphicon-user"></span>&nbsp; Login Pendaftar</h3>
      </div>
		
      <div class="well">
	  <p>Untuk melihat hasil test Anda, silahkan login dengan mengisi form di bawah ini.</p>
        <form role="form" method="post">
            <?if(isset($msg)){?>
				<div class="alert alert-danger"><?=$msg?></div>
			<?}?>
          <div class="form-group">
            <label>No Pendaftaran</label>
            <input type="text" class="form-control" name="nopendaftaran" placeholder="No Pendaftaran">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password">
          </div>
          <button type="submit" class="btn btn-success" name="login" id="btnSave">Login</button><br>
		  <a href="index.php?page=data_forgotpass">Lupa password?</a>
        </form>
      </div>
    </div>
    <?php #require_once('inc_sidebar.php'); ?>
  </div>
</div>

<script type="text/javascript">
	
</script>
<?php require_once('inc_footer.php'); ?>
