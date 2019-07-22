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
        <h2>Daya Tampung Jurusan</h2>
      </div>
      <div class="well">
      	<form method="post" role="form" class="form-inline">
            <label>Pilih Periode :</label>
            <select name="year" width="20%">
            <?php
            $periode=array();
            $periode=mCombo::periode($conn);
            $periode=array_values($periode);
            $length=count($periode);
            for($i=0;$i<$length;$i++){
		echo '<option value="'.$periode[$i].'">'.$periode[$i].'</option>';
            }
            ?>
	    </select>
            <input type="hidden" name="jadwal" value="jadwal"/>
	    <button name="periode" value="periode" class="btn btn-danger">Cari</button>
        </form>
      </div>
    <h3><?php echo $jadwal2['jalurpenerimaan']." ".$jadwal2['namagelombang'];  ?></h3>
      <table class="table table-striped table-bordered">
      	<tr>
        	 <th>Jurusan</th>
            <th>Pagu</th>
        </tr>
        <?php while($pagu2=$pagu->FetchRow()){ ?>
        
        <tr>
            <td><?php echo $pagu2['namaunit']; ?></td>
            <td><?php echo $pagu2['pagu']; ?></td>
        </tr>
        <?
            
        }
        ?>
      </table>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>  