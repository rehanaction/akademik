<?php 
 require_once(Route::getModelPath('berita'));
 
        $rs = mBerita::getBerita($conn);
        while ($arrBerita  = $rs->fetchRow()){
			$databerita[] = $arrBerita;
			}


unset($_SESSION[SITE_ID]['URL']);
require_once('inc_header.php'); 

?>
<div class="container">
    <div class="row">
        <div class="col-md-8">
            <img src="images/1.png" width="100%">
            <div class="jalur-box">
                <div class="page-header">
                    <h3><span class="glyphicon glyphicon-calendar"></span>&nbsp; Jadwal Penerimaan/Pendaftaran Mahasiswa Baru </h3>
                </div>
                <div class="table-responsive">
                    <form method="post" action="index.php?page=list_jadwal">
                        <table class="table" >
                            <tr style="background:#0165FE; color:#fff;">
                                <th width="30%">Tanggal Pendaftaran</th>
                                <th width="25%">Jalur</th>
                                <th width="15%">Status</th>
                                <th width="20%">Pendaftaran</th>
                                <th></th>
                            </tr>
                            <?
                                while($jp=$jalur->FetchRow()){  
									
                                    $jalurpenerimaan = $jp['jalurpenerimaan'];
                                    $idgelombang = $jp['idgelombang'];
                                    $periodedaftar = $jp['periodedaftar'];
                                    $tglawaldaftar = $jp['tglawaldaftar'];
                                    $tglakhirdaftar = $jp['tglakhirdaftar'];
                                    $tglsekarang = date('Y-m-d');
                                    $isopen = $jp['isopen'];
                                    $isbayar = $jp['isbayar'];
                                    $key = $periodedaftar.'|'.$idgelombang.'|'.$jalurpenerimaan;
                            ?>
                            <tr>
                                <td class="tgl-pendaftaran"><?=(!empty($tglawaldaftar) && !empty($tglakhirdaftar)) ? Date::indoDate($tglawaldaftar)." s/d ".Date::indoDate($tglakhirdaftar) : 'Jadwal belum ditentukan' ?></td>
                                <td>
                                    <button type="submit" name="jlr" id="btnSave" class="link" value="<?= $jalurpenerimaan?>">
                                        <b><?=$jalurpenerimaan?></b>
                                    </button>
                                    <br>
                                    <small>Periode <?=Pendaftaran::getNamaPeriode($jp['periodedaftar'],true)?> <br> <?=$jp['namagelombang']?></small>
                                </td>
                                <td><?= ($isopen=='t') ?  "<span class='label label-success'>Buka</span>" : "<span class='label label-default'>Tutup</span>" ?></td>
                                <td align="center">
                                  <?if($jp['isopen']=='t' and ($tglsekarang >= $tglawaldaftar and $tglsekarang <= $tglakhirdaftar)){	?>
                                      <button style="margin-bottom: 5px;" type="button" 
                                        <?if($isbayar=='t') {?>
                                            onClick="goView('data_token&q=<?= base64_encode($key) ?>')"
                                        <? }else{?>
                                            onClick="goChange('<?=$key?>')"
                                        <?} ?> 
                                        class="btn btn-primary btn-sm">
                                            <span class="glyphicon glyphicon-list"></span> Daftar Sekarang
                                    </button>				
                                  <?}else{ ?>
									  <button class="btn btn-danger btn-sm">Pendaftaran Belum Dibuka</button>
								<?}?>
                                    <!--<button type="submit" name="jlr" id="btnSave" value="<?= $jalurpenerimaan?>" class="btn btn-primary btn-sm">
                                        <span class="glyphicon glyphicon-search"></span> Lihat informasi pendaftaran
                                    </button>-->      
                                </td>
                            </tr>
                            <? } ?>
                        </table>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-md-4">	  
            <? if (Modul::isAuthenticatedFront()){?>
            <div class="well">
                <form method="post">
                    <button type="submit" name="logout" id="btnSave" class="btn btn-danger"> LogOut </button>
                </form>
                <br>
                <a onClick="goView('data_input')" style="cursor: pointer">Profil</a><br/>
				<!--<a onClick="goView('list_tagihan')" style="cursor: pointer">Tagihan Pendaftar</a><br/>-->
				<!--a  onClick="goOpen('rep_kartu')"  style="cursor: pointer">Cetak Kartu Ujian</a><br/-->
				<a  onClick="goOpen('rep_formulir')"  style="cursor: pointer">Cetak Formulir Pendaftaran</a><br/>
				<a  onClick="goView('data_gantipswd')"  style="cursor: pointer">Ganti Password</a><br/>
				<!--<a  onClick="goView('data_beasiswa')"  style="cursor: pointer">Pengajuan Beasiswa</a><br/>-->
				<? $nopendaftar = Modul::pendaftarLogin();
					$lulus=$conn->GetRow("select lulusujian,pilihanditerima from pendaftaran.pd_pendaftar where nopendaftar='".$nopendaftar."'");
					if($lulus['lulusujian']=='-1' and $lulus['pilihanditerima'] != ''){
				?>
				<!--<a  onClick="goView('pengumuman_lulus')"  style="cursor: pointer">Pengumuman Kelulusan</a>-->
				<?}?> 
            </div>
            <?} else { ?>
            <!--<div class="box blue" onClick="goView('list_jalur')"><span class="glyphicon glyphicon-pencil"></span>&nbsp; Daftar sekarang</div>-->
            <div class="box" onClick="goView('login_form')"><span class="glyphicon glyphicon-user"></span>&nbsp; Login Pendaftar</div>
            <? } ?>

            <br/>
            <div class="info-wrapper">
                <div class="page-header">
                    <h3><span class="glyphicon glyphicon-bullhorn"></span>&nbsp; Info Penting </h3>
                </div>
                <ul class="pengumuman">
                    <li>
                        <a href="https://inaba.ac.id/pmb-s1/">Detail Pendaftaran Mahasiswa Baru S1</a>
                    </li>
                    <li>
                        <a href="https://inaba.ac.id/pmb-s2/">Detail Pendaftaran Mahasiswa Baru S2</a>
                    </li>
                </ul>
            </div>
        </div>
        <?php /** require_once('inc_sidebar.php'); **/?>
    </div>
    <br/>
<!--
    <div class="row">
        <div class="col-md-8"></div>
    </div>
-->
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
