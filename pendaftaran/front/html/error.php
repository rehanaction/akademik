<?php
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

require_once($conf['model_dir'].'m_pagu.php');
require_once($conf['model_dir'].'m_combo.php');
$year=date('Y');
if(isset ($_POST['year'])){
    $year=$_POST['year'];
}
$pagu=mPagu::getPagu($year);

?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>OOPS ! 404 ERROR </h2>
      </div>
      <p>Mohon Maaf, halaman yang Anda cari tidak dapat ditemukan.</p>
  </div>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>  