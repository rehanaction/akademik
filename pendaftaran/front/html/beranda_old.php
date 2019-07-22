<?php 
unset($_SESSION[SITE_ID]['URL']);
require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Selamat Datang <small>| Pendaftaran Mahasiswa Baru </small></h2>
      </div>
      <p align="justify"><img src="images/pendaftaran-uwp.jpg" width="100%"><br/><br/>Selamat Datang di Sistem Informasi Manajemen Pendaftaran Mahasiswa Baru Universitas Esa Unggul Jakarta (UEU). <br/>
        Silahkan cek Menu Informasi untuk mengetahui bagaimana alur proses pendaftaran.
        Untuk melihat hasil Test anda dapat masuk melalui form login di sisi kanan. Jika Anda menemui kesalahan / kesulitan, silahkan menghubungi pihak Universitas. <br/>
        Terima Kasih.</p>
      <div class="page-header">
        <h3>Jalur Penerimaan </h3>
      </div>
      <div class="table-responsive">
      <form method="post" action="index.php?page=list_jadwal">
        <table cellspacing=0 width=600px class="table table-striped table-bordered" >
          <thead>
			<tr>
				  <th colspan="3">Informasi</th>
				  <th rowspan="2">Tanggal Pendaftaran</th>
				  <th rowspan="2">Status</th>
				  <th rowspan="2" colspan="2">Pendaftaran</th>
			</tr>
            <tr>
              <th>Jalur</th>                
              <th>periode</th>                
              <th>Gelombang</th>
                
            </tr>
          </thead>
          <?
				while($jp=$jalur->FetchRow()){  
					$jalurpenerimaan = $jp['jalurpenerimaan'];
					$idgelombang = $jp['idgelombang'];
					$periodedaftar = $jp['periodedaftar'];
					$tglawaldaftar = $jp['tglawaldaftar'];
					$tglakhirdaftar = $jp['tglakhirdaftar'];
					$isopen = $jp['isopen'];
					$isbayar = $jp['isbayar'];
					$key = $periodedaftar.'|'.$idgelombang.'|'.$jalurpenerimaan;
				?>
          <tr align="center">
            <td style="background: #c5c5c5;"><?=$jalurpenerimaan?></td>
            <td><?=$periodedaftar?></td>
            <td><?=$idgelombang?></td>
            <td>
				<?=(!empty($tglawaldaftar) && !empty($tglakhirdaftar)) ? Date::indoDate($tglawaldaftar)." s/d ".Date::indoDate($tglakhirdaftar) : 'Jadwal belum ditentukan' ?> 
			</td>
            <td>
				<?= ($isopen=='t') ?  "<span class='label label-success'>Buka</span>" : "<span class='label label-default'>Tutup</span>" ?>
			</td>
            <td>
				<button type="submit" name="jlr" id="btnSave" value="<?=$jalurpenerimaan?>" class="btn btn-info btn-xs"><span class="glyphicon glyphicon-search"></span> Info</button>
              <?if($jp['isopen']=='t'){	?>
				  <button type="button" 
					<?if($isbayar=='t') {?>
						onClick="goView('data_token&q=<?= base64_encode($key) ?>')"
					<? }else{?>
						onClick="goChange('<?=$key?>')"
					<?}?> 
					class="btn btn-danger btn-xs">
						<span class="glyphicon glyphicon-list"></span> Daftar
				</button>				
			  <?}?>
			</td>
          </tr>
          <?
				}
			?>
        </table>
      </form>
      </div>
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
<?php require_once('inc_footer.php'); ?>

<script>
	function goChange(key){
		param = key.split('|');
		var posted = "f=getUrl&q[]="+param[0]+"&q[]="+param[1]+"&q[]="+param[2];
		$.post("<?= Route::navAddress('ajax'); ?>",posted,function(text) {
			goView('data_input');
		});
	}
</script>
