<?php
require_once($conf['model_dir'].'m_jadwal.php');
require_once($conf['model_dir'].'m_combo.php');
$jalur="";
if(isset ($_POST['nama'])){
    $jalur=$_POST['jlr'];
}
$jadwal=mJadwal::getJadwal($jalur);

?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Data Pendaftar Yang Diterima</h2>
      </div>
      <div class="well">
      	<form method="post" role="form" class="form-inline">
            <label>Masukin Nama Pendaftar :</label>
            
            <input type="text" name="nama"/>
	    <button name="periode" value="periode" class="btn btn-danger">Cari Mahasiswa</button>
        </form>
      </div>
      
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>  
