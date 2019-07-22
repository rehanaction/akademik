<?php
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
}
$mprop = mCombo::propinsi($conn);
$mkota = mCombo::getKota($conn);
	
$nopendaftar=$_SESSION['PENDAFTARAN']['FRONT']['USERID'];
$pendaftar=mPendaftar::infoLulus($conn, $nopendaftar);

if($pendaftar['isadministrasi'] == '-1'){
		$p_posterr = false;
		$p_postmsg = 'Anda Lolos pada tahap Administrasi!<br>';
}
if($pendaftar['lulusujian'] == '-1'){
		$p_posterr = false;
		$p_postmsg = '<font size="3">SELAMAT!! Anda Diterima di '.$pendaftar['fakultas'].' '.$pendaftar['jurusan'].'</font>' ;
		
}
?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Pengumuman Kelulusan</h2>
      </div>
      <?php
      if(empty($p_postmsg))
			echo '<div class="alert alert-info" align="center">Belum Ada Info Terkait Kelulusan!</div><br>';
		else
			echo '<div class="alert alert-info" align="center">'.$p_postmsg.'</div><br>';
		?>
		<h3 style="margin-top:0">Informasi Lebih Lanjut</h3>
      <table class="table table-striped">
      	<tr>
        	<td><strong>No Pendaftar</strong></td>
            <td>:</td>
            <td>
				<?=$pendaftar['nopendaftar']?>
			</td>
        </tr>
        <tr>
		<td><strong>Nama</strong></td>
		<td> : </td>
		<td>
		    <?=$pendaftar['nama']?>
		</td>
	    </tr>
	    <tr>
		<td><strong>Alamat</strong></td>
		<td> : </td>
		<td>
		   Jln. <?= $pendaftar['jalan'] ?> RT. <?= $pendaftar['rt'] ?> RW. <?= $pendaftar['rw'] ?> Kelurahan <?= $pendaftar['kel'] ?>
				<br>Kecamatan <?= $pendaftar['kec'] ?>, <?= $mkota[$pendaftar['kodekota']] ?>, <?= $mprop[$pendaftar['kodepropinsi']] ?>
		</td>
	    </tr>
	    <tr>
		<td><strong>Asal SMA</strong></td>
		<td> : </td>
		<td>
		    <?=$list_smu[$pendaftar['asalsmu']].", ".$list_alamatsmu[$pendaftar['asalsmu']]?>
		</td>
	    </tr>
	    <?php if($pendaftar['lulusujian']==-1){ ?>
	    <tr>
			<td><strong>Download</strong></td>
			<td> : </td>
			<td>
				<a onClick="goView('download&subdir=pengumuman&file=syaratdaftarulang.pdf')" style="cursor:pointer">Informasi Daftar Ulang</a>
			</td>
	    </tr>
	    <?php } ?>
        <?php if($pendaftar['lulusujian'] == '-1'){?>
        <tr>
			<td><strong>Cetak File</strong></td>
			<td> : </td>
			<td>
				<a href="index.php?page=rep_formulir" target="_blank">Cetak Biodata</a>
				<?php if($pendaftar['isdaftarulang'] == '-1'){ ?>
					<br><a href="index.php?page=ktm_sementara" target="_blank">Cetak KTM Sementara</a>
				<?php } ?>
			</td>
        </tr>
        <?php } ?>
      </table>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>  
