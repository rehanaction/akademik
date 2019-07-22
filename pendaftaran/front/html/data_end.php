<?php 
unset($_SESSION[SITE_ID]['URL']);
require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Pendaftaran Berhasil</h2>
      </div>
      <p align="justify"> Untuk dapat mengikuti ujian, silakan cetak kartu ujian dan formulir pendaftaran Anda terlebih dahulu
            dengan login menggunakan :<br/>
            <strong>Username/No. Pendaftaran :</strong> <?=$_SESSION[SITE_ID]['PENDAFTAR']['nopendaftar']?><br />
           Gunakan tanggal lahir sebagai password default contoh 2014-01-30, password 20140130. 
       </p>
      
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>
