<?php
require_once($conf['model_dir'].'m_jadwal.php');
require_once($conf['model_dir'].'m_combo.php');
 
$jalur="";
if(isset ($_POST['jlr'])){
    $jalur=$_POST['jlr'];
}
$jadwal=mJadwal::getJadwal($jalur);

?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
<!--        <h2>Jadwal Ujian Masuk Universitas Esa Unggul</h2>-->
      </div>
      <div class="well">
      	<form method="post" role="form" class="form-inline">
            <!--<label>Pilih Jalur :</label>-->
            <!--<select name="jlr" class="form-control" style="width:30%">-->
				
            <?php
           /* $periode=array();
            $periode=mCombo::jalur($conn);
            $periode=array_values($periode);
            $length=count($periode);*/
            $a_jalur=array();
            while($jp=$jalur->FetchRow()){
				$a_jalur[$jp['jalurpenerimaan']]=$jp;
			}
            foreach($a_jalur as $row){
	    ?>
            <!--tr>
		<option value="<?=$row['jalurpenerimaan']?>"
		<?
		if(isset($_POST['jlr']) && $row['jalurpenerimaan']==$_POST['jlr']) echo "selected='selected'";
		?>
		><?=$row['jalurpenerimaan']?></option>';
            </tr-->
	    <?}
            ?>
	    <!--</select>-->
            <input type="hidden" name="jadwal" value="jadwal"/>
	    <!--<button name="periode" value="periode" class="btn btn-danger">Lihat Jadwal</button>-->
        </form>
      </div>
      <?php
    while($jadwal2=$jadwal->FetchRow()){
		
    ?>
<!--    <h3><?php echo $jadwal2['jalurpenerimaan']." Gelombang ".$jadwal2['idgelombang'];  ?></h3>-->
      <table class="table table-striped">
      	<tr>
        <!--tr>	<td><strong>Pendaftaran</strong></td>
            <td>:</td>
            <td>
		    <?php
			$tgl= new DateTime($jadwal2['tglawaldaftar']);
			$tgl2= new DateTime($jadwal2['tglakhirdaftar']);
			echo $tgl->format('d-M-Y')." s.d ".$tgl2->format('d-M-Y');
		    ?>
			</td></tr-->
        </tr>
        <tr>
	<!--tr>	<td><strong>Ujian</strong></td>
		<td> : </td>
		<td>
		    <?php
			$tgl= new DateTime($jadwal2['tglujian']);
			echo $tgl->format('d-M-Y');
		    ?>
		</td> </tr-->
	    </tr>
	    <tr>
		<!--tr><td><strong>Pengumuman</strong></td>
		<td> : </td>
		<td>
		    <?php
		        $tgl= new DateTime($jadwal2['tglpengumuman']);
		        echo $tgl->format('d-M-Y');
		    ?>
		</td></tr-->
	    </tr>
	    <tr>
		<!--tr><td><strong>Registrasi</strong></td>
		<td> : </td>
		<td>
		    <?php
			$tgl= new DateTime($jadwal2['tglawalregistrasi']);
			$tgl2= new DateTime($jadwal2['tglakhirregistrasi']);
			echo $tgl->format('d-M-Y')." s.d ".$tgl2->format('d-M-Y');
		    ?>
		</td></tr-->
	    </tr>
	    <!--tr>
		<td><strong>Sistem Kuliah</strong></td>
		<td> : </td>
		<td><?php echo $jadwal2['sistemkuliah']; ?></td>
	    </tr-->
        <tr>
        <!--	<td> <strong>Info</strong></td> -->

            <!-- <td>:</td> -->
            <td><?php echo $jadwal2['pengumuman']; ?></td>
        </tr>
        <?php if(!empty($jadwal2['filependaftaran'])){?>
        <tr>
        	<td> <strong>File Pendaftaran</strong></td>
            <td>:</td>
            <td><u class="ULink" onclick="goDownload('pengumumanpendaftaran','<?= $jadwal2['jalurpenerimaan'].'|'.$jadwal2['periodedaftar'].'|'.$jadwal2['idgelombang']?>')" style="cursor:pointer; color:blue">Download Pengumuman Pendaftaran</u></td>
        </tr>
        <?php } ?>
        <?php if(!empty($jadwal2['filedaftarulang'])){?>
        <tr>
        	<td> <strong>File Daftar Ulang</strong></td>
            <td>:</td>
            <td><u class="ULink" onclick="goDownload('pengumumandaftarulang','<?= $jadwal2['jalurpenerimaan'].'|'.$jadwal2['periodedaftar'].'|'.$jadwal2['idgelombang']?>')" style="cursor:pointer; color:blue">Download Pengumuman Daftar Ulang</u></td>
        </tr>
        <?php } ?>
      </table>
      <hr>
      <?php } ?>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>  
