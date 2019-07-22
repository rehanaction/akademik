<?php
	require_once('inc_header.php');
?>
    <div class="container bg-white">
        <div class="row">
            <div class="col-md-8">
                <div class="col-md-12">
                    <!--<div class="well">
                    <div class="row">
                        <div class="col-md-8">
                            <h4>Febria Retno Ramadhani</h4>
                        </div>
                        <div class="col-md-4" align="right">
                            <button class="btn btn-xs btn-warning"><small class="glyphicon glyphicon-pencil"></small> Edit</button>
                        </div>

                    </div>
                    <div class="row">
                        <div class="col-md-5">
                            <small class="glyphicon glyphicon-envelope"></small> febria.ramadhani@gmail.com
                            <hr/>
                            <small class="glyphicon glyphicon-user"></small> No. KTP: 12345678910
                            <hr/>
                            <small class="glyphicon glyphicon-phone-alt"></small> -
                            <hr/>
                            <small class="glyphicon glyphicon-phone"></small> 087855393838
                        </div>
                        <div class="col-md-7">
                            <small class="glyphicon glyphicon-envelope"></small> Surabaya, 26 Jan 2016
                            <hr/>
                            <small class="glyphicon glyphicon-user"></small> Griya Benowo Indah Blok O-25 Surabaya, Jawa Timur, 60197
                            <hr/>
                            <small class="glyphicon glyphicon-phone-alt"></small> Designer
                            <hr/>
                            <small class="glyphicon glyphicon-phone"></small> PT. Sentra Vidya Utama
                        </div>
                    </div>
                </div>
                <!-- Nav tabs
      <div class="card">
        <ul class="nav nav-tabs" role="tablist">
          <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab"><i class="fa fa-home"></i>  <span>Seminar Anda</span></a></li>
          <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab"><i class="fa fa-user"></i>  <span>Riwayat Seminar Anda</span></a></li>
        </ul>

        <!-- Tab panes
        <div class="tab-content">
          <div role="tabpanel" class="tab-pane active" id="home">
                <div class="pahge-header">
                    <h3>Seminar yang Anda Ikuti</h3>
                </div>
            </div>
        </div>
      </div>-->
                    <div class="card hovercard">
                        <div class="row">
                        <div class="col-md-2">
                            <div class="custom-input-file">
                                    <img src="images/no-image.gif" width="100px">
                                    <label class="uploadPhoto">
                                        Ganti
                                        <input type="file" class="change-avatar" name="avatar" id="avatar">
                                    </label>
                                </div>
                        </div>
                        <div class="col-md-10">
                        <div class="row">
                            <div class="col-md-8">
                                <h4>Halo, Febria Retno Ramadhani.</h4>
                            </div>
                            <div class="col-md-4" align="right">
                                <button class="btn btn-xs btn-warning"><small class="glyphicon glyphicon-pencil"></small> Edit</button>
                            </div>

                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <small class="glyphicon glyphicon-user"></small> &nbsp;<b>No. KTP:</b> 12345678910
                                <hr/>
                                <small class="glyphicon glyphicon-calendar"></small> &nbsp;Surabaya, 8 Februari 1995
                                <hr/>
                                <small class="glyphicon glyphicon-envelope"></small> &nbsp;febria.ramadhani@gmail.com
                                <hr/>
                                <small class="glyphicon glyphicon-phone-alt"></small> &nbsp; (031) 7408840, 087855393838
                                <hr/>
                            </div>
                            <div class="col-md-6">
                                <small class="glyphicon glyphicon-map-marker"></small> &nbsp;Medokan Asri Tengah MA 2 Blok Q-16 Surabaya, Jawa Timur 60235
                                <hr/>
                                <small class="glyphicon glyphicon-globe"></small> &nbsp;PT. Sentra Vidya Utama
                                <hr/>
                                <small class="glyphicon glyphicon-briefcase"></small> &nbsp;Designer
                            </div>
                        </div>
                        </div>
                            </div>
                    </div>

                    <ul class="nav nav-tabs" role="tablist">
                      <li role="presentation" class="active"><a href="#home" aria-controls="content1" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-star"></span> <span>Seminar Anda</span></a></li>
                      <li role="presentation"><a href="#profile" aria-controls="content2" role="tab" data-toggle="tab"><span class="glyphicon glyphicon-tasks"></span> <span>Riwayat Seminar Anda</span></a></li>
                    </ul>

                    <!-- Tab panes-->
                    <div class="tab-content">
                      <div role="tabpanel" class="tab-pane active" id="home">
												<? if(isset($posterr)) { ?>
														<div class="alert alert-<?= empty($posterr) ? 'success' : 'danger' ?>">
																<?= $postmsg ?>
														</div>
														<? } ?>
																<section id="content1">
																	<div class="col-md-12">
											                <div class="page-header">
											                <h3>Daftar Seminar Anda</h3>
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
											            <hr class="divider1">

											            </div>
																		<!-- <?php foreach($rows as $row) { ?>
																		<div class="row">
																				<div class="col-md-3 img-seminar">
																						<img src="<?php echo $row['thumbnail'] ?>" width="100%">
																						<div class="date custom-date">
																								<?php echo $row['tglblnjadwal'] ?><br>
																								<?php echo $row['thnjadwal'] ?>
																						</div>
																				</div>
																				<div class="col-md-9">
																						<a href="<?php echo Route::navAddress('data_seminar') ?>&key=<?php echo $row['idseminar'] ?>&back=1">
																								<h4 class="title">
																										<?php echo $row['namaseminar'] ?>
																								</h4>
																						</a>
																						<?php echo $row['keterangan'] ?><br/>
																						<?php if(!empty($row['namaruang'])) { ?>
																						<span class="label label-info"><small class="glyphicon glyphicon-map-marker"></small> <?php echo $row['namaruang'] ?></span>
																						<?php } ?>
																						<?php if(!empty($row['jam'])) { ?>
																						<span class="label label-info"><small class="glyphicon glyphicon-time"></small> <?php echo $row['jam'] ?></span>
																						<?php } ?>
																						<?php if(!empty($row['typepeserta'])) { ?>
																						<span class="label label-<?php echo $row['classtypepeserta'] ?>"><small class="glyphicon glyphicon-user"></small> <?php echo $row['namatypepeserta'] ?></span>
																						<?php } ?>
																						<?php if(!empty($row['kategori'])) { ?>
																						<span class="label label-success">Kategori: <?php echo $row['kategori'] ?></span>
																						<?php } ?>
																				</div>
																		</div>
																		<hr class="divider">
																		<?php } ?> -->
																	</section>
  															</div>
  														<div role="tabpanel" class="tab-pane" id="profile">
																<section id="content2">
																	<div class="col-md-12">
																			<div class="page-header">
																					<h3>Riwayat Seminar Anda</h3>
																			</div>
																			<div class="main">
																				<ul class="cbp_tmtimeline">
																					<li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
																					<li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
                                                                                    <li>
																						<time class="cbp_tmtime"><span>4 Okt 2016</span> <span>18:30</span></time>
																						<div class="cbp_tmicon"><div class="glyphicon glyphicon-ok"></div></div>
																						<a onClick="goView('data_seminar')"><div class="cbp_tmlabel">
																							<b>Seminar “My Mom My Hero” Universitas Esa Unggul</b><br/>
                                                                                            <p>Berkaitan dengan Hari Ibu yang jatuh pada bulan Desember, kami LDK IKMI Universitas Esa Unggul menyelenggarakan Seminar bertajuk “My Mom My Hero”. TERBUKA UNTUK UMUM...</p>
																						</div></a>
																					</li>
																				</ul>
																				<div align="right">
												                    <div class="btn btn-primary btn-sm">Lihat Selanjutnya &raquo;</div>
												                </div>
																			</div>
																	</div>
																</section>
															</div>
                    		</div>
                    <br/>
                </div>
            </div>
            <?php #require_once('inc_sidebar.php'); ?>
        </div>
    </div>
    <?php require_once('inc_footer.php'); ?>
