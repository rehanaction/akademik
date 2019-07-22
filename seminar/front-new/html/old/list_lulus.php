<?php
defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );



require_once($conf['model_dir'].'m_pendaftar.php');
require_once($conf['helpers_dir'].'cstr.class.php');
require_once($conf['model_dir'].'m_smu.php');
require_once($conf['model_dir'].'m_combo.php');

$rs_sma = mSmu::getSmu();
$list_smu = array();
$list_alamatsmu = array();
while($row = $rs_sma->FetchRow()){
	$list_smu[$row['idsmu']] = $row['namasmu'];
	$list_alamatsmu[$row['idsmu']] = $row['alamatsmu'];
	$list_kotasmu[$row['idsmu']] = trim($row['kodekota']);
}
$mprop = mCombo::propinsi($conn);
$mkota = mCombo::getKota($conn);
	
$data=array();
if(isset($_POST['btn'])){
   $nama=CStr::removeSpecialAll($_POST['nama']);
   $data=mPendaftar::dataPendaftarlulus($conn, $nama, $page);
}


//$maxpage=mPendaftar::getMaxpage($conn);

?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Data Calon Mahasiswa Baru Universitas Esa Unggul Surabaya</h2>
      </div>
      <div class="well">
      	<form method="post" role="form" class="form-inline">
            <label>Nama Pendaftar :</label>
            <input type="text" name="nama" class="form-control" style="width:30%" />
	    <button name="btn" class="btn btn-danger">Cari</button>
        </form>
      </div>
    
	 <div class="table-responsive">
      <table class="table table-striped table-bordered">
      	<tr>
			<th rowspan="2">No</th>
        	<th rowspan="2">No. Pendaftaran</th>
            <th rowspan="2">Nama</th>
            <!--th rowspan="2">Alamat</th-->
            <th rowspan="2">Asal SMA</th>
            <th colspan="2">Diterima Di</th>
            <th rowspan="2">Daftar Ulang</th>
        </tr>
        <tr>
			<th>Fakultas</th>
            <th>Jurusan</th>
        </tr>
        <?
        if(empty($data)){
        ?>
        <tr><td colspan=8 align="center">Data Kosong</td></tr>
        <?
		}else{
			$no=0;
            foreach($data as $pendaftar){
			$no++;
		?>
        <tr>
			<td><?=$no?></td>
            <td><?=$pendaftar['nopendaftar']?></td>
            <td><?=$pendaftar['nama']?></td>
            <!--td>Jln. <?= $pendaftar['jalan'] ?> RT. <?= $pendaftar['rt'] ?> RW. <?= $pendaftar['rw'] ?> Kelurahan <?= $pendaftar['kel'] ?>
				<br>Kecamatan <?= $pendaftar['kec'] ?>, <?= $mkota[$pendaftar['kodekota']] ?>, <?= $mprop[$pendaftar['kodepropinsi']] ?>
			</td-->
            <td><?=$list_smu[$pendaftar['asalsmu']].", ".$list_alamatsmu[$pendaftar['asalsmu']].", ".$mkota[$list_kotasmu[$pendaftar['asalsmu']]]?></td>
            <td><?=$pendaftar['fakultas']?></td>
            <td><?=$pendaftar['jurusan']?></td>
            <td align="center"><?=$pendaftar['isdaftarulang']==-1?'<img src="images/check.png">':''?></td>
        </tr>
        <?
            }
        }
        ?>
      </table>
      </div>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>  
