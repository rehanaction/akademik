 <?php
// cek akses halaman
defined('__VALID_ENTRANCE') or die('Akses terbatas');
//$conn->debug = true;
require_once(Route::getModelPath('token'));
require_once(Route::getModelPath('lokasi'));
require_once(Route::getModelPath('combo'));
require_once(Route::getModelPath('pendaftarbeasiswa'));
require_once(Route::getModelPath('keuangan'));
require_once(Route::getModelPath('gelombangdaftar'));
require_once(Route::getModelPath('actionpendaftar'));
require_once(Route::getModelPath('smu'));
require_once(Route::getModelPath('kuisioner'));
require_once(Route::getModelPath('beasiswa'));
require_once(Route::getModelPath('berkasbeasiswa'));
require_once(Route::getModelPath('pengajuanbeasiswapendaftar'));

require_once(Route::getUIPath('combo'));
require_once(Route::getUIPath('form'));

$c_upload   = false;
$c_insert   = false;
$c_delete   = false;
$c_readlist = true;
$c_edit     = true;

// properti halaman
$p_title     = 'Data Pendaftar';
$p_tbwidth   = 550;
$p_aktivitas = 'bio';
$p_listpage  = Route::getListPage();
$p_model     = mPendaftarBeasiswa;

$r_act = $_POST['act'];

$r_key  = Modul::pendaftarLogin();
$p_foto = uForm::getPathImageMahasiswa($conn, $r_key);

$arrFree = Modul::getFreeSession();
if (empty($arrFree)) {
    $r_token = $_SESSION[SITE_ID]['PENDAFTAR']['tokenpendaftaran'];

} else { //jika gratisan
    $r_periode = $arrFree['periodedaftar'];
    $r_gel     = $arrFree['gelombang'];
    $r_jalur   = $arrFree['jalurpenerimaan'];
}


if (!empty($r_key)) {
    $detailtoken = mPendaftarBeasiswa::getData($conn, $r_key);
    $infojalur   = mGelombangdaftar::getData($conn, $detailtoken['jalurpenerimaan'] . '|' . $detailtoken['periodedaftar'] . '|' . $detailtoken['idgelombang']);

    if ($infojalur['isbayar'] == 't') {
        $r_token = $detailtoken['tokenpendaftaran'];
    }
}

if (empty($arrFree)) { //jika tidak gratisan
    if ($r_token)
        $detailtoken = mKeuangan::getDetailtoken($conn, $r_token);

    $r_periode       = $detailtoken['periodedaftar'];
    $r_gel           = $detailtoken['idgelombang'];
    $r_jalur         = $detailtoken['jalurpenerimaan'];
    $r_jumlahpilihan = $detailtoken['jumlahpilihan'];
    $r_programpend   = $detailtoken['programpend'];
    $r_sistemkuliah  = $detailtoken['sistemkuliah'];
}

if (empty($r_periode) || empty($r_jalur) || empty($r_gel)) {
    list($p_posterr, $p_postmsg) = array(
        true,
        'PERIODE, JALURPENERIMAAN DAN GELOMBANG TIDAK DITEMUKAN'
    );
    $c_edit = false;
}

$arrSistemkuliah = mCombo::sistemKuliah($conn);


$a_input   = $p_model::inputColumn($conn, $r_key, $r_jalur, $r_periode, $r_gel);
$a_input[] = array(
    'kolom' => 'idperingkat',
    'label' => 'Peringkat',
    'type' => 'S',
    'option' => $a_peringkat,
    'readonly' => true
);
if (!empty($r_key))
    $a_input[] = array(
        'kolom' => 'tokenpendaftaran',
        'label' => 'Token / PIN',
        'maxlength' => 21,
        'size' => 15,
        'readonly' => true
    );



// ada aksi
if ($r_act == 'save' and $c_edit) {
    list($post, $record) = uForm::getPostRecord($a_input, $_POST);
    list($post, $record_quisioner) = uForm::getPostRecord($a_input_quisioner, $_POST);

    if (empty($arrFree)) {
        $record['sistemkuliah'] = $r_sistemkuliah ? $r_sistemkuliah : null;
    }

    if (empty($r_key)) {
        list($p_posterr, $p_postmsg, $p_posterrtagihan, $p_postmsgtagihan, $r_key) = mActionpendaftar::Insert($conn, $record, $r_periode, $r_gel, $r_jalur, $r_sistemkuliah, $r_token);

        if (!$p_posterr) {
            $record_quisioner['nopendaftar'] = $r_key;
            mKuisioner::insertCRecord($conn, $a_input_quisioner, $record_quisioner, $r_key);
        }
    } else {
        list($p_posterr, $p_postmsg) = mActionpendaftar::update($conn, $record, $r_key, $r_periode, $r_gel, $r_jalur);

        if (!$p_posterr) {
            $cek = mKuisioner::isDataExist($conn, $r_key);
            if ($cek)
                mKuisioner::updateCRecord($conn, $a_input_quisioner, $record_quisioner, $r_key);
            else {
                $record_quisioner['nopendaftar'] = $r_key;
                mKuisioner::insertCRecord($conn, $a_input_quisioner, $record_quisioner, $r_key, true);
            }
        }
    }

    if (!$p_posterr)
        unset($post);

} else if ($r_act == 'pengajuan') {
    //die('kenek');
    $record                 = array();
    $record['idbeasiswa']   = $_POST['idbeasiswa'];
    $record['nopendaftar']  = $_POST['nopendaftar'];
    $record['tglpengajuan'] = date('Y-m-d');
    $record['isditerima']   = 0;

    list($p_posterr, $p_postmsg) = mPengajuanBeasiswaPd::insertRecord($conn, $record);
} else if ($r_act == 'batalpengajuan') {
    //die('kenek');

    $r_idpengajuan = mPengajuanBeasiswaPd::getIdByPd($conn, $r_key);

    list($p_posterr, $p_postmsg) = mPengajuanBeasiswaPd::delete($conn, $r_idpengajuan);
}else if($r_act == 'upload') {

		$tipe=array('image/jpeg','image/jpg','image/gif','image/png','application/pdf');
		$ext=array('image/jpg'=>'jpg','image/jpeg'=>'jpeg','image/gif'=>'gif','image/png'=>'png','application/pdf'=>'pdf');
		list($idpengajuanbeasiswa,$kodesyaratbeasiswa,$idbeasiswa) = explode("|",$_POST['subkey']);
		//var_dump($idpengajuanbeasiswa);die;
		$file_types=$_FILES['fileberkas_'.$kodesyaratbeasiswa]['type'];
		$file_nama=$_FILES['fileberkas_'.$kodesyaratbeasiswa]['name'].'.'.$ext[$file_types];
		$file_name=str_replace('|',';',$_POST['subkey']).'.'.$ext[$file_types];

		if(in_array($file_types,$tipe) && !empty($tipe)){
			$upload=move_uploaded_file($_FILES['fileberkas_'.$kodesyaratbeasiswa]['tmp_name'],'uploads/syaratbeasiswa/'.$file_name);

			if($upload){
				$recordu=array();
				$recordu['idpengajuanbeasiswa']=$idpengajuanbeasiswa;
				$recordu['kodesyaratbeasiswa']=$kodesyaratbeasiswa;
				$recordu['idbeasiswa']=$idbeasiswa;
				$recordu['fileberkas']=$file_nama;

				//delete berkas
				list($p_posterr,$p_postmsg) = mBerkasBeasiswa::delete($conn,$_POST['subkey']);
				//insert berkas
				list($p_posterr,$p_postmsg) = mBerkasBeasiswa::insertRecord($conn,$recordu);

			}else{
				$p_posterr=true;
				$p_postmsg='Upload Gagal';
			}
		}else{
			$p_posterr=true;
			$p_postmsg='Pastikan Tipe File Berupa Gambar/Pdf, Upload Gagal';
		}
	}

$row           = $p_model::getDataEdit($conn, $a_input, $r_key, $post);

$r_kodekotalahir = Page::getDataValue($row, 'kodekotalahir');
$r_kodekota      = Page::getDataValue($row, 'kodekota');
$r_kodekotaortu  = Page::getDataValue($row, 'kodekotaortu');
$r_kodekotasmu   = Page::getDataValue($row, 'kodekotasmu');
$r_asalsmu       = Page::getDataValue($row, 'asalsmu');
$r_token         = Page::getDataValue($row, 'tokenpendaftaran');

$r_kodekotaayah = Page::getDataValue($row, 'kodekotaayah');
$r_kodekotaibu  = Page::getDataValue($row, 'kodekotaibu');

$r_kodekotapt        = Page::getDataValue($row, 'kodekotapt');
$r_kodekotalahirayah = Page::getDataValue($row, 'kodekotalahirayah');
$r_kodekotalahiribu  = Page::getDataValue($row, 'kodekotalahiribu');
$r_kodekotakantor    = Page::getDataValue($row, 'kodekotakantor');
if (!empty($r_key))
    $r_sistemkuliah = Page::getDataValue($row, 'sistemkuliah');
$a_beasiswa = mBeasiswa::getArrayNama($conn);

//check pengajuan
$r_idbeasiswa = mBeasiswa::getIdByPd($conn, $r_key);

if (empty($row[0]['value']) and !empty($r_key)) {
    $p_posterr  = true;
    $p_fatalerr = true;
    $p_postmsg  = 'User ini Tidak Mempunyai Profile';
}

if (!empty($r_idbeasiswa)) {
    $r_idpengajuan = mPengajuanBeasiswaPd::getIdByPd($conn, $r_key);
    $rowd = array();
    $keysyarat = array();
    $keysyarat['idbeasiswa'] = $r_idbeasiswa;
    $keysyarat['idpengajuanbeasiswa'] = $r_idpengajuan;
    var_dump($keysyarat['idpengajuanbeasiswa']);
    $rowd += mPengajuanBeasiswaPd::getSyarat($conn,$keysyarat,'syarat',$post);
}

?>
<?php
require_once('inc_header.php');
?>
<div class="container">
  <div class="row">
    <div class="col-md-9">
      <div class="page-header">
        <h2>Data Pengajuan Beasiswa</h2>
      </div>
        <form name="pagepengajuan" id="pagepengajuan" method="post" enctype="multipart/form-data">
            <div class="panel panel-default" style="margin-top:20px;">
              <div class="panel-heading"><span class="glyphicon glyphicon-list"></span> Data Beasiswa</div>
                  <div class="panel-body">
						<table width="100%" cellpadding="4" cellspacing="2" class="table table-striped">
							<tr>
								<td>Pilih Beasiswa</td>
								<td>:</td>
								<td><?= UI::createSelect('idbeasiswa', $a_beasiswa, $r_idbeasiswa, '', (empty($r_idbeasiswa) ? true : false)) ?></td>
								<td>
									<?php
									if (empty($r_idbeasiswa)) {
									?>
									  <button id="pengajuan" class="btn btn-success" type="button" name="pengajuan" onclick="pengajuanBeasiswa()"> Proses Pengajuan </button>
									<?php
									} else {
									?>
									  <button id="pengajuan" class="btn btn-danger" type="button" name="pengajuan" onclick="batalBeasiswa()"> Batalkan Pengajuan </button>
									<?php
									}
									?>
							  </td>
							</tr>
						</table>
                    </div>
            </div>
            <input type="hidden" name="act" id="act">
            <input type="hidden" name="nopendaftar" id="nopendaftar" value="<?= $r_key ?>">
        </form>
		<?php
		if (!empty($r_idbeasiswa)){
		?>
		<div class="alert alert-warning"><span class="glyphicon glyphicon-info-sign">
			</span> Beasiswa telah diajukan silakan melengkapi data dan persyaratan.
		</div>
		<?
			require_once('data_viewpdbeasiswa.php');
		}
		?>
	</div>

	<?php
	require_once('inc_sidebar.php');
	?>
	</div>
</div>

<div align="left" id="div_autocomplete" style="background-color:#FFFFFF;position:absolute;display:none;border:1px solid #999999;overflow:auto;overflow-x:hidden;">
    <table bgcolor="#FFFFFF" id="tab_autocomplete" cellpadding="3" cellspacing="0"></table>
</div>
<script>
function pengajuanBeasiswa(){
    pagepengajuan.act.value='pengajuan';
    document.getElementById("pagepengajuan").submit();
}
function batalBeasiswa(){
    pagepengajuan.act.value='batalpengajuan';
    document.getElementById("pagepengajuan").submit();
}
</script>
<script type="text/javascript" src="scripts/jquery.xautox.js"></script>
<script type="text/javascript" src="scripts/cstr.js"></script>
<?
require_once('xinc_script.php');
?>
<?php
require_once('inc_footer.php');
?>
