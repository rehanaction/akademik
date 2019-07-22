<?php


// include
require_once(Route::getModelPath('pegawai'));
require_once(Route::getUIPath('combo'));
//$conn->debug=true;
// variabel request
$r_unit = Modul::setRequest($_POST['unit'],'UNIT');

// combo
// $l_unit = uCombo::unit($conn,$r_unit,'unit','onchange="goSubmit()"',false);

// struktur view
$a_kolom = array();
$a_kolom[] = array('kolom' => 'idpegawai', 'label' => 'ID Pegawai');

$a_kolom[] = array('kolom' => 'nik', 'label' => 'NIK');
$a_kolom[] = array('kolom' => 'namadepan', 'label' => 'Nama');
$a_kolom[] = array('kolom' => 'isdosen', 'label' => 'Dosen ? ' ); 
$a_kolom[] = array('kolom' => 'nidn', 'label' => 'NIDN');
$a_kolom[] = array('kolom' => 'jabatan', 'label' => 'Jabatan');
// $a_kolom[] = array('label' =>'Contact');
$a_kolom[] = array('kolom' => 'idstatusaktif', 'label' => 'Status');

// properti halaman
$p_title = 'Daftar Pegawai';
$p_tbwidth = "100%";
$p_aktivitas = 'BIODATA';
$p_detailpage = Route::getDetailPage();
$legendstatuspeg=true;
$legendjkpeg=true;

$p_model = mPegawai;
$p_colnum = count($a_kolom)+2;

// ada aksi
$r_act = $_REQUEST['act'];
if($r_act == 'delete' and $c_delete) {
    $r_key = CStr::removeSpecial($_POST['key']);
    
    list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
}
else if($r_act == 'refresh')
    Modul::refreshList();
// mendapatkan data ex
$r_page = Page::setPage($_POST['page']);
$r_row = -1;
$r_sort = Page::setSort($_POST['sort']);
$a_filter = Page::setFilter($_POST['filter'],$p_model::getArrayListFilterCol());
$a_datafilter = Page::getFilter($a_kolom);

// mendapatkan data
// if(!empty($r_unit)) $a_filter[] = $p_model::getListFilter('unit',$r_unit);
$a_filter[] = $p_model::getListFilter('unit',Modul::getUnit());

$a_data = $p_model::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter);

$p_lastpage = Page::getLastPage();
$p_time = Page::getListTime();
$p_rownum = Page::getRowNum();
$p_pagenum = ceil($p_rownum/$r_row);

//membuat filter
$a_filtercombo = array();
//$a_filtercombo[] = array('label' => 'Jurusan', 'combo' => $l_unit);

// filter tree
$a_filtertree = array();
//$a_filtertree['unit'] = array('label' => 'Prodi', 'data' => mCombo::unitTree($conn,true));
//print_r($a_data[0]);
$c_upload = true;
?>
<style>
.pagebreak { page-break-before: always; } /* page-break-after works, as well */
#imgfoto{
    width:100%;
}
</style>
<?php require_once('inc_header.php'); ?>
<div id="wrapper">
        <div class="SideItem" id="SideItem">
<?php 
foreach($a_data as $row){ ?>
<table>
    <tr>   
        <td class="LeftColumnBG" style="white-space:nowrap">NIK</td>
        <td class="RightColumnBG"><?=$row['nik'] ?></td> 
        <td  width="60px" align="center" valign="top" rowspan="12">
					
				
                            	<?= uForm::getImagePegawai($conn,$row['idpegawai'],$c_upload) ?>

							</td>
    </tr>
    <tr>
        <td>Nama Lengkap</td>
        <td><?=$row['nama'] ?></td>
    </tr>
    <tr>
        <td>Unit</td>
        <td><?=$row['namaunit'] ?></td>
    </tr>
    <tr>
        <td>Tipe pegawai</td>
        <td><?=$row['tipepeg'] ?></td>
    </tr>
    <tr>
        <td>Jenis pegawai</td>
        <td><?=$row['jenispegawai'] ?></td>
    </tr>
    <tr>
        <td>Jabatan Fungsional</td>
        <td><?=$row['jabatanfungsional'] ?></td>
    </tr>
    <tr>
        <td>Jabatan</td>
        <td><?=$row['jabatan'] ?></td>
    </tr>
    <tr>
        <td>Status Aktif</td>
        <td><?php if($row['idstatusaktif']=="AA"){ echo "Aktif"; }else{ echo "Tidak Aktif"; } ?></td>
    </tr>
    <tr>
        <td>No KTP</td>
        <td><?=$row['noktp'] ?></td>
    </tr>
    <tr>
        <td>NPWP</td>
        <td><?=$row['npwp'] ?></td>
    </tr>
    <tr>
        <td>NIDN</td>
        <td><?=$row['nidn'] ?></td>
    </tr>
    <tr>
        <td>Tempat, Tanggal Lahir</td>
        <td><?=$row['tmplahir'].", ". date("d-m-Y",strtotime($row['tgllahir'])) ?></td>
    </tr>
    <tr>
        <td>Kewarganegaraan</td>
        <td><?php if($row['idkewarganegaraan']=="WNA"){echo "WNI";}else{echo $row['idkewarganegaraan']; } ?></td>
    </tr>
    <tr>
        <td>Agama</td>
        <td><?=$row['namaagama'] ?></td>
    </tr>
    <tr>
        <td>Status Nikah</td>
        <td><?php if($row['statusnikah']==0){ echo "Belum menikah"; }else{ echo "Menikah";} ?></td>
    </tr>
    <tr>
        <td>Alamat</td>
        <td><?=$row['alamat'] ?></td>
    </tr>
    <tr>
        <td>Jenis Kelamin</td>
        <td><?php if($row['jeniskelamin']=="L"){ echo "Laki-Laki"; }else{echo "Perempuan";} ?></td>
    </tr>
    <tr>
        <td>Email</td>
        <td><?=$row['emailpribadi'] ?></td>
    </tr>
    <tr>
        <td>No Hp</td>
        <td><?=$row['nohp'] ?></td>
    </tr>
    </table>
    <div class="pagebreak"> </div>
<? } ?>
</div>
</div>