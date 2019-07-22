<div class="col-md-3">
      <div class="page-header">
        <h2>Login</h2>
      </div>
      <div class="well">
      <?php
		if (Modul::isAuthenticatedFront()){
			?>
			<form method="post">
				<button type="submit" name="logout" id="btnSave" class="btn btn-danger"> LogOut </button>
			</form>
			<br>
			<a onClick="goView('data_input')" style="cursor: pointer">Profil</a><br/>
<!--
			<a onClick="goView('list_tagihan')" style="cursor: pointer">Tagihan Pendaftar</a><br/>
-->
			<!--a  onClick="goOpen('rep_kartu')"  style="cursor: pointer">Cetak Kartu Ujian</a><br/-->
			<a  onClick="goOpen('rep_formulir')"  style="cursor: pointer">Cetak Formulir Pendaftaran</a><br/>
			<a  onClick="goView('data_gantipswd')"  style="cursor: pointer">Ganti Password</a><br/>
			<? $nopendaftar = Modul::pendaftarLogin();
				$lulus=$conn->GetRow("select lulusujian,pilihanditerima from pendaftaran.pd_pendaftar where nopendaftar='".$nopendaftar."'");
				if($lulus['lulusujian']=='-1' and $lulus['pilihanditerima'] != ''){
			?>
<!--
			<a  onClick="goView('pengumuman_lulus')"  style="cursor: pointer">Pengumuman Kelulusan</a>
			<?}?>
-->
			<?php
		}else{
		?>
        <form role="form" method="post">
            <?if(isset($msg)){?>
				<div class="alert alert-danger"><?=$msg?></div>
			<?}?>
          <div class="form-group">
            <label>No Pendaftaran</label>
            <input type="text" class="form-control" name="nopendaftaran" placeholder="No Pendaftaran">
          </div>
          <div class="form-group">
            <label>Password</label>
            <input type="password" class="form-control" name="password" placeholder="Password">
          </div>
          <button type="submit" class="btn btn-success" name="login" id="btnSave">Login</button><br>
		  <a href="index.php?page=data_forgotpass">Lupa password?</a>
        </form>
        <? }?>
      </div>
      <div class="page-header">
        <h2>Berita</h2>
      </div>
      <a href="http://www.esaunggul.ac.id/" style="cursor:pointer;">Universitas Esa Unggul</a><br/>
    </div>
