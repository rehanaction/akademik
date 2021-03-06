<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('token'));
	require_once(Route::getModelPath('lokasi'));
	require_once(Route::getModelPath('combo'));
	require_once(Route::getModelPath('pendaftar'));
	require_once(Route::getModelPath('keuangan'));
	require_once(Route::getModelPath('gelombangdaftar'));
	require_once(Route::getModelPath('actionpendaftar'));
	require_once(Route::getModelPath('smu'));
	require_once(Route::getModelPath('kuisioner'));
	
	require_once(Route::getUIPath('combo'));	
	require_once(Route::getUIPath('form'));
	
	$c_upload = false;
	$c_insert = false;
	$c_delete = false;
	$c_readlist = true;
	$c_edit = true;

	// properti halaman
	$p_title = 'Data Pendaftar';
	$p_tbwidth = 550;
	$p_aktivitas = 'bio';
	$p_listpage = Route::getListPage();	
	$p_model = mPendaftar;
	
    $r_act = $_POST['act'];
	
	$r_key = Modul::pendaftarLogin();
	$p_foto = uForm::getPathImageMahasiswa($conn,$r_key);
	
	$arrFree = Modul::getFreeSession();
	if (empty($arrFree)){
		$r_token=$_SESSION[SITE_ID]['PENDAFTAR']['tokenpendaftaran'];
		
	}else{ //jika gratisan
		$r_periode = $arrFree['periodedaftar'];
		$r_gel = $arrFree['gelombang'];
		$r_jalur = $arrFree['jalurpenerimaan'];
	}
	
	
	if (!empty($r_key)){
		$detailtoken = mPendaftar::getData($conn,$r_key);
		$infojalur = mGelombangdaftar::getData($conn,$detailtoken['jalurpenerimaan'].'|'.$detailtoken['periodedaftar'].'|'.$detailtoken['idgelombang']);
	
		if ($infojalur['isbayar'] =='t'){
				$r_token = $detailtoken['tokenpendaftaran'];
			}		
	}
	
	if (empty ($arrFree)){ //jika tidak gratisan
		if ($r_token)
		$detailtoken = mKeuangan::getDetailtoken($conn, $r_token);
		
		$r_periode = $detailtoken['periodedaftar'];
		$r_gel = $detailtoken['idgelombang'];
		$r_jalur = $detailtoken['jalurpenerimaan'];
		$r_jumlahpilihan = $detailtoken['jumlahpilihan'];
		$r_programpend = $detailtoken['programpend'];	
		$r_sistemkuliah = $detailtoken['sistemkuliah'];
	}
	
	if (empty ($r_periode) || empty($r_jalur) || empty($r_gel)){
		list($p_posterr, $p_postmsg) = array(true,'PERIODE, JALURPENERIMAAN DAN GELOMBANG TIDAK DITEMUKAN');
		$c_edit = false;
	}
	
		$arrSistemkuliah = mCombo::sistemKuliah($conn);


	$a_input = $p_model::inputColumn($conn,$r_key,$r_jalur, $r_periode, $r_gel);
	$a_input[] = array('kolom' => 'idperingkat', 'label' => 'Peringkat', 'type'=>'S', 'option'=>$a_peringkat,'readonly'=>true);
	if (!empty($r_key))
	$a_input[] = array('kolom' => 'tokenpendaftaran', 'label' => 'Token / PIN', 'maxlength' => 21, 'size' => 15, 'readonly' => true);
	
	$a_input_quisioner = mKuisioner::getColumn($conn);

	// ada aksi
	if($r_act == 'save' and $c_edit) {
			list($post,$record) = uForm::getPostRecord($a_input,$_POST);
			list($post,$record_quisioner) = uForm::getPostRecord($a_input_quisioner,$_POST);
			
			if (empty ($arrFree)){
				$record['sistemkuliah'] = $r_sistemkuliah ? $r_sistemkuliah : null;
			}
			
			if (empty ($r_key)){
				list($p_posterr,$p_postmsg,$p_posterrtagihan,$p_postmsgtagihan,$r_key) = mActionpendaftar::Insert($conn,$record,$r_periode, $r_gel, $r_jalur,$r_sistemkuliah,$r_token);

				if (!$p_posterr) {
					$record_quisioner['nopendaftar'] = $r_key;
					mKuisioner::insertCRecord($conn,$a_input_quisioner,$record_quisioner,$r_key);
				}
			}else{
				list($p_posterr,$p_postmsg) = mActionpendaftar::update($conn,$record,$r_key, $r_periode, $r_gel, $r_jalur);
				
				if (!$p_posterr){
					$cek = mKuisioner::isDataExist($conn,$r_key);
					if ($cek)
						mKuisioner::updateCRecord($conn,$a_input_quisioner,$record_quisioner,$r_key);
					else{
						$record_quisioner['nopendaftar'] = $r_key;
						mKuisioner::insertCRecord($conn,$a_input_quisioner,$record_quisioner,$r_key,true);
					}
				}
			}
			
			if (!$p_posterr)
				unset($post);
		
	}
	 
	else if($r_act == 'savefoto') {
		if(empty($_FILES['foto']['error'])) {
			$err = Page::createFoto($_FILES['foto']['tmp_name'],$p_foto,200,150);
			
			switch($err) {
				case -1:
				case -2: $msg = 'format foto harus JPG, GIF, atau PNG'; break;
				case -3: $msg = 'foto tidak bisa disimpan'; break;
				default: $msg = false;
			}
			if($msg !== false)
				$msg = 'Upload gagal, '.$msg;
		}
		else
			$msg = Route::uploadErrorMsg($_FILES['foto']['error']);
		
		uForm::reloadImageMahasiswa($conn,$r_key,$msg);
	}
	else if($r_act == 'deletefoto') {
		@unlink($p_foto);
		// uForm::reloadImageMahasiswa($conn,$r_key);
		uForm::reloadImageMahasiswa($conn,$r_key);
	}
       
        $row = $p_model::getDataEdit($conn,$a_input,$r_key,$post);
		$row_kuisioner = mKuisioner::getDataEdit($conn,$a_input_quisioner,$r_key,$post);
 
        $r_kodekotalahir = Page::getDataValue($row,'kodekotalahir');
        $r_kodekota = Page::getDataValue($row,'kodekota');
        $r_kodekotaortu = Page::getDataValue($row,'kodekotaortu');
        $r_kodekotasmu = Page::getDataValue($row,'kodekotasmu');
        $r_asalsmu = Page::getDataValue($row,'asalsmu');
        $r_token = Page::getDataValue($row,'tokenpendaftaran');

        $r_kodekotaayah = Page::getDataValue($row,'kodekotaayah');
        $r_kodekotaibu = Page::getDataValue($row,'kodekotaibu');
        
        $r_kodekotapt = Page::getDataValue($row,'kodekotapt');
		$r_kodekotalahirayah = Page::getDataValue($row,'kodekotalahirayah');
		$r_kodekotalahiribu = Page::getDataValue($row,'kodekotalahiribu');
		$r_kodekotakantor = Page::getDataValue($row,'kodekotakantor');
		if (!empty ($r_key))
		$r_sistemkuliah = Page::getDataValue($row,'sistemkuliah');


        
	if(empty($row[0]['value']) and !empty($r_key)) {
		$p_posterr = true;
		$p_fatalerr = true;
		$p_postmsg = 'User ini Tidak Mempunyai Profile';
	}

?>
<?php require_once('inc_header.php'); ?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Data Pendaftar</h2>
      </div>
      <form name="pageform" id="pageform" method="post" enctype="multipart/form-data">
				<?php
					
					/*****************/
					/* TOMBOL-TOMBOL */
					/*****************/
					
					if(empty($p_fatalerr)){
						require_once('inc_databutton.php');
						if(empty($p_postmsg)) ?>
							<div class="alert alert-info">Selamat Datang, Pendaftaran Periode <?= $r_periode?>, Gelombang <?= $r_gel?>, Jalur Penerimaan <?= $r_jalur?></span></div>
					<?}
					if(!empty($p_postmsg)) { ?>
						<br><div class="alert <?= $p_posterr ? 'alert-danger' : 'alert-success' ?>"><?= $p_postmsg ?></div>
						
				<?	} 
					if(!empty($p_postmsgtagihan)) { ?>
						<div class="alert <?= $p_posterrtagihan ? 'alert-danger' : 'alert-success' ?>">
							<?= $p_postmsgtagihan ?>
                          </div>          
				<?	} ?>
                <?php
					if(empty($p_fatalerr)) { ?>
					<div class="panel panel-default" style="margin-top:20px;">
                      <div class="panel-heading"><span class="glyphicon glyphicon-user"></span> Data Pendaftar</div>
                      <div class="panel-body">
					<?	$a_required = array();
						foreach($row as $t_row) {
							if($t_row['notnull'])
								$a_required[] = $t_row['id'];
							}
					?>						  
																	
                        <table width="100%" cellpadding="4" cellspacing="2" class="table table-bordered table-striped">
                        	<tr>
								<td align="center" valign="top" rowspan="<?= $r_key ? '11' : '6'?>">
									<? if (!empty($r_key)) {?>
										
									<?= uForm::getImageMahasiswa($conn,$r_key,true) ?>
									<span style="font-size: 10px"><br>untuk upload foto klik pada foto</span>
									<br>
									<button type="button" class="btn btn-primary" onclick="setUpload()"><span class="glyphicon glyphicon-upload"></span> Upload Foto</button>
									<? } ?>
								</td>
                            </tr>
                            <? if (!empty($r_key)) {?>
                            <tr>
                            	<td>Nomor Pendaftaran</td>
                                <td>:</td>
                                <td><?= $r_key?></td>
                            </tr>
                            <tr>
                            	<td>Token / PIN</td>
                                <td>:</td>
                                <td><?= $r_token?></td>
                            </tr>
								<? } ?>
                            <tr>
                            	<td><?= Page::getDataLabel($row,'nama')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'nama')?></td>
                            </tr>   
                            <? if (empty($r_key)){ ?>
                            <tr>
                                <td><?= Page::getDataLabel($row,'sistemkuliah')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'sistemkuliah')?></td>
                            </tr>
							<?	}?>
                            <tr>
                                <td><?= Page::getDataLabel($row,'pilihan1')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan1')?></td>
                            </tr>
                            <tr>
                                <td><?= Page::getDataLabel($row,'pilihan2')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'pilihan2')?></td>
                            </tr>
                            <? if (!empty ($r_key)){ ?>
                            <tr>
                                <td>Jalur Penerimaan</td>
                                <td>:</td>
                                <td><?= $r_jalur?></td>
                            </tr> 
                            <tr>
                                <td>Periode Daftar</td>
                                <td>:</td>
                                <td><?= Pendaftaran::getNamaPeriode($r_periode)?></td>
                            </tr> 
                            <tr>
                                <td>Gelombang</td>
                                <td>:</td>
                                <td><?= $r_gel?></td>
                            </tr> 
                            <tr>
                                <td>Sistem Kuliah</td>
                                <td>:</td>
                                <td><?= $arrSistemkuliah[$r_sistemkuliah]?></td>
                            </tr> 
							<?}?> 
							
							 <tr>
                                <td><?= Page::getDataLabel($row,'idperingkat')?></td>
                                <td>:</td>
                                <td><?= Page::getDataInput($row,'idperingkat')?></td>
                            </tr> 
									             
						</table>
                      </div>
                    </div>
                    
                    <div class="alert alert-info"><span class="glyphicon glyphicon-info-sign"></span> Harap mengisi kolom inputan pada semua tab</div>
                    <ul class="nav nav-tabs" id="myTab">
						<li class="active"><a href="#biodata">Data Biodata</a></li>
						<li><a href="#informasi">Data Keluarga</a></li>
						<li><a href="#akademik">Data Sekolah</a></li>
						<li><a href="#informasilain">Info Lain</a></li>
						<li><a href="#kuisioner">Quisioner</a></li>
						<li><a href="#berkas">Berkas</a></li>
                    </ul>
                    
                    <div class="tab-content">
						<? require_once($conf['view_dir'].'xinc_tab_biodata.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_akademik.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_informasi.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_informasilain.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_quisioner.php'); ?>
						<? require_once($conf['view_dir'].'xinc_tab_berkas.php'); ?>
                    </div>
                    
                    <script>
                      $('#myTab a').click(function (e) {
						  e.preventDefault()
						  $(this).tab('show')
						})
                    </script>
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key" value="<?= $r_key ?>">
				<input type="hidden" name="detail" id="detail">
				<input type="hidden" name="subkey" id="subkey">
				<?	} ?>
			</form>
      
    </div>
    <?php require_once('inc_sidebar.php'); ?>
  </div>
</div>
</div>
<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
	<table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>

<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/cstr.js"></script>
<? require_once('xinc_script.php')?>
<?php require_once('inc_footer.php'); ?>
