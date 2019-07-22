<?
    defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	require_once($conf['model_dir'].'m_tagihanKUA.php');
	require_once($conf['model_dir'].'m_pendaftar.php');
       
	//cek session
	if(Modul::pendaftarLogin() == null or Modul::pendaftarLogin() == ''){
		header("Location: index.php");
	}
	$pendaftar = mPendaftar::getData($conn,Modul::pendaftarLogin());
	$data=mTagihanKUA::getTagihanPendaftar($conn, Modul::pendaftarLogin());
	$a_labellunas = array('BB'=>'Belum Bayar','BL'=>'Belum Lunas','L'=>'Lunas', 'S'=>'Suspend', 'F'=>'DiBebaskan');

	if (!empty ($pendaftar['isvalidbeasiswa']) or !empty ($pendaftar['isvalidregistrasi']) or !empty ($pendaftar['isvalidsemesterpendek']))
	list($p_posterr,$p_postmsg) = array(false,'Selamat anda mendapat potongan tagihan');
        
require_once('inc_header.php'); ?>
<div class="container">
	
	<div class="row">
		<div class="col-md-9">
			<?php		if(!empty($p_postmsg)) { ?>
			<center>
				<div class="<?= $p_posterr ? 'DivError' : 'DivSuccess' ?>" style="width:<?= $p_tbwidth ?>px">
					<?= $p_postmsg ?>
				</div>
			</center>
			<div class="Break"></div>
			<?php	} ?>

			<div class="page-header"> <h4>Potongan Tagihan Pendaftar</h4> </div>
			<div class="row">
				<table class="table tale-bordered table-stipped GridStyle table-responsive">
					<thead>
					<tr>
						<th>Nama Potongan</th>
						<th>Nominal</th>
						<th>Keterangan</th>
					</tr>					
					</thead>
					<tbody>
					<?php if ($pendaftar['isvalidbeasiswa']) { ?>
					<tr>
						<td>Potongan Beasiswa</td>
						<td><?= cStr::formatNumber($pendaftar['potonganbeasiswa'])?></td>
						<td><?= $pendaftar['keteranganpotonganbeasiswa']?></td>
					</tr>
					<?php } ?>
					<?php if ($pendaftar['isvalidregistrasi']) { ?>
					<tr>
						<td>Potongan Registrasi</td>
						<td><?= cStr::formatNumber($pendaftar['potonganregistrasi'])?></td>
						<td><?= $pendaftar['keteranganpotonganregistrasi']?></td>
					</tr>
					<?php } ?>
					<?php if ($pendaftar['isvalidsemesterpendek']) { ?>
					<tr>
						<td>Potongan Semester pendek</td>
						<td><?= cStr::formatNumber($pendaftar['potongansemesterpendek'])?></td>
						<td><?= $pendaftar['keteranganpotongansemesterpendek']?></td>
					</tr>
					<?php } ?>
					</tbody>
				</table>
			</div>
			<hr>
			<div class="page-header"> <h4>Tagihan Pendaftar</h4> </div>
			<div class="row">
				<table class="table tale-bordered table-stipped GridStyle table-responsive">
					<tr>
						<th>No</th>
						<th>ID Tagihan</th>
						<th>Tgl Tagihan</th>
						<th>Jenis Tagihan</th>
						<th>Nominal Tagihan</th>
						<th>Status</th>
					</tr>
					<? $no=0;  foreach ($data as $val){ $no++;?>
					<tr>
						<td align="center"><?= $no?></td>
						<td><?= $val['idtagihan']?></td>
						<td><?= date::indoDate($val['tgltagihan'])?></td>
						<td align="center"><?= $val['jenistagihan']?></td>
						<td align="right"><?= cStr::formatNumber($val['nominaltagihan'])?></td>
						<td align="center"><?= $a_labellunas[$val['flaglunas']]?></td>
					</tr>
					<? } ?>
				</table>
				
			</div>
		</div>
		<?php require_once('inc_sidebar.php'); ?>
	</div>
</div>
<?php require_once('inc_footer.php'); ?>
