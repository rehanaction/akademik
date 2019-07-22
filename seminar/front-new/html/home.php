<?php 
 require_once(Route::getModelPath('berita'));
 
        $rs = mBerita::getBerita($conn);
        while ($arrBerita  = $rs->fetchRow()){
			$databerita[] = $arrBerita;
			}


unset($_SESSION[SITE_ID]['URL']);
require_once('inc_header.php'); 

?>
<div class="container bg-white">
    <div class="row">
        <div class="col-md-8">
            <div id="carousel-example-generic" class="carousel slide" data-ride="carousel"> 
                <!-- Wrapper for slides -->
                <div class="carousel-inner" role="listbox">
                    <div class="item active"> <img src="images/seminar1.jpg" width="100%"> </div>
                    <div class="item"> <img src="images/seminar1.jpg" width="100%"> </div>
                </div>
                <!-- Controls --> 
                <a class="left carousel-control" href="#carousel-example-generic" role="button" data-slide="prev"> 
                    <span class="glyphicon glyphicon-chevron-left" aria-hidden="true"></span> 
                    <span class="sr-only">Previous</span> 
                </a> 
                <a class="right carousel-control" href="#carousel-example-generic" role="button" data-slide="next"> 
                    <span class="glyphicon glyphicon-chevron-right" aria-hidden="true"></span> 
                    <span class="sr-only">Next</span> 
                </a> 
            </div>
            <div class="col-md-12">
                <div class="page-header">
                <h3>Jadwal Seminar Terbaru</h3>
                </div>
                <hr/><br/>
                <div class="row">
                    <div class="col-md-3 img-seminar">
                        <img src="images/no-img.jpg" width="100%">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-9">
                        <a href="#" onClick="goView('data_seminar')"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”.
TERBUKA UNTUK UMUM...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>
                        <span class="label label-success">Kategori: Seminar</span>
                    </div>
                </div>
                <hr class="divider">
                <div class="row">
                    <div class="col-md-3 img-seminar">
                        <img src="images/contoh1.jpg" width="100%">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-9">
                        <a href="#"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”.
TERBUKA UNTUK UMUM...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>
                        <span class="label label-success">Kategori: Seminar</span>
                    </div>
                </div>
                <hr class="divider">
                <div class="row">
                    <div class="col-md-3 img-seminar">
                        <img src="images/contoh1.jpg" width="100%">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-9">
                        <a href="#"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”.
TERBUKA UNTUK UMUM...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>
                        <span class="label label-success">Kategori: Seminar</span>
                    </div>
                </div>
                <hr class="divider">
                <div class="row">
                    <div class="col-md-3 img-seminar">
                        <img src="images/contoh1.jpg" width="100%">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-9">
                        <a href="#"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”.
TERBUKA UNTUK UMUM...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>
                        <span class="label label-success">Kategori: Seminar</span>
                    </div>
                </div>
                <hr class="divider">
                <div class="row">
                    <div class="col-md-3 img-seminar">
                        <img src="images/contoh1.jpg" width="100%">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-9">
                        <a href="#"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”.
TERBUKA UNTUK UMUM...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>
                        <span class="label label-success">Kategori: Seminar</span>
                    </div>
                </div>
                <hr class="divider">
                <div align="center">
                    <div class="btn btn-primary">Lihat Semua Acara</div>
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
				<a onClick="goView('list_tagihan')" style="cursor: pointer">Tagihan Pendaftar</a><br/>
				<!--a  onClick="goOpen('rep_kartu')"  style="cursor: pointer">Cetak Kartu Ujian</a><br/-->
				<a  onClick="goOpen('rep_formulir')"  style="cursor: pointer">Cetak Formulir Pendaftaran</a><br/>
				<a  onClick="goView('data_gantipswd')"  style="cursor: pointer">Ganti Password</a><br/>
				<? $nopendaftar = Modul::pendaftarLogin();
					$lulus=$conn->GetRow("select lulusujian,pilihanditerima from pendaftaran.pd_pendaftar where nopendaftar='".$nopendaftar."'");
					if($lulus['lulusujian']=='-1' and $lulus['pilihanditerima'] != ''){
				?>
				<a  onClick="goView('pengumuman_lulus')"  style="cursor: pointer">Pengumuman Kelulusan</a>
				<?}?> 
            </div>
            <?} else { ?>
            <div class="box blue" onClick="goView('data_input')"><span class="glyphicon glyphicon-pencil"></span>&nbsp; Daftar Jadi Peserta Sekarang</div>
            <br/>
            <div class="box" onClick="goView('login_form')"><span class="glyphicon glyphicon-user"></span>&nbsp; Login Peserta Seminar</div>
            <? } ?>
            <br/>
            <div class="col-md-12">
                <div class="page-header">
                    <h4>Jadwal Seminar Minggu Ini</h4>
                </div>
                <hr/><br/>
                <div class="row">
                    <div class="col-md-2">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-10">                        
                        <a onClick="goView('data_seminar')"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember kami LDK IKMI Universitas Esa Unggul...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>

                    </div>
                </div>
                <hr class="divider">
                <div class="row">
                    <div class="col-md-2">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-10">                        
                        <a onClick="goView('data_seminar')"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember kami LDK IKMI Universitas Esa Unggul...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>
                    </div>
                </div>
                <hr class="divider">
                <div class="row">
                    <div class="col-md-2">
                        <div class="date">5 Des <br>2016</div>
                    </div>
                    <div class="col-md-10">                        
                        <a href="#"><h4 class="title">Seminar “My Mom My Hero” Universitas Esa Unggul</h4></a>
                        Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember kami LDK IKMI Universitas Esa Unggul...<br/>
                        
                        <span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> AULA</span>
                        <span class="label label-info"><small class="glyphicon glyphicon-time"></small> 08.00 - 12.00 WIB</span>
                        <span class="label label-warning"><small class="glyphicon glyphicon-user"></small> Mahasiswa</span>
                    </div>
                </div>
                <hr class="divider">
                <div align="right">
                    <div class="btn btn-primary btn-sm">Lihat Acara Minggu Ini &raquo;</div>
                </div>
                
            </div>
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
