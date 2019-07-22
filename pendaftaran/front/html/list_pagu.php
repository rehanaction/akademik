
<?php require_once('inc_header.php');


	$r_periode = $_POST['periode'];
	$arrperiode = mPeriode::getPEriode($conn);
	
	//datapagu
	$arr_pagu = mPagu::getDataPagu($conn, $r_periode);
	
	

 ?>
 
<div class="container">
  <div class="row">
    <div class="col-md-9">
     
      <div class="page-header">
        <h3>Data Pagu Masing-Masing Jurusan </h3>
      </div>
      <div class="table-responsive">
		  
      <form method="post" action="" id="pageform" name="pageform">
		  Pilih Periode Pendaftaran :  <?= UI::createSelect('periode',$arrperiode,$r_periode,'',true,'onChange="goSubmit()"',true,'--Pilih Periode--')?>
		  <br><br>
        <table cellspacing=0 width=600px class="table table-striped table-bordered" >
          <thead>
            <tr>
              <th>No</th>
              <th>Jurusan</th>
              <th>Jalur Penerimaan</th>
              <th>Gelombang</th>
              <th>pagu</th>                
            </tr>
          </thead>
          <tbody>
			  <?php $no=1;foreach($arr_pagu as $row ){ ?>
			  <tr>
				  <td><?=$no?></td>
				  <td><?=$row['namaunit']?></td>
				  <td><?=$row['jalurpenerimaan']?></td>
				  <td><?=$row['namagelombang']?></td>
				  <td><?=$row['pagu']?></td>
			  </tr>
			  <?php $no++; } ?>
          </tbody>
        </table>
      </form>
      </div>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>

<script type="text/javascript">
function getJalur(periodedaftar,gelombang,jalur){
	<?php
		$_SESSION[SITE_ID]['PENDAFTAR']['periodedaftar'] = periodedaftar;
		$_SESSION[SITE_ID]['PENDAFTAR']['idgelombang'] = gelombang;
		$_SESSION[SITE_ID]['PENDAFTAR']['jalurpenerimaan'] = jalur;
	?>
}

function goSubmit(){
	document.getElementById("pageform").submit();
	}

</script>
