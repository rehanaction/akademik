<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	// hak akses
	//Modul::getFileAuth();

	// include
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	$r_unit = Modul::setRequest($_POST['unit'],'UNIT');
	$r_cabang = Modul::setRequest($_POST['cabang'],'CABANG');
	$r_gedung = Modul::setRequest($_POST['gedung'],'GEDUNG');
	$r_lantai = Modul::setRequest($_POST['lantai'],'LANTAI');
	$r_lokasi = Modul::setRequest($_POST['lokasi'],'LOKASI');
	$r_jenisruang = Modul::setRequest($_POST['jenisruang'],'JENISRUANG');
	$r_sumber = Modul::setRequest($_POST['sumber'],'SUMBER');
	$r_merk = Modul::setRequest($_POST['merk'],'MERK');
	$r_barang = Modul::setRequest($_POST['barang'],'BARANG');
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');
	
	$year = date("Y"); 
	$mon = date("m"); 

	if(empty($r_tahun)) $r_tahun = $year;
	if(empty($r_bulan2)) $r_bulan2 = $mon;
	
	$l_tahun = uCombo::tahun($conn,$r_tahun,'tahun','style="width:75px"',false);
	$l_bulan1 = uCombo::bulan($conn,$r_bulan1,'bulan1','style="width:90px"',false);
	$l_bulan2 = uCombo::bulan($conn,$r_bulan2,'bulan2','style="width:90px"',false);
	
	// properti halaman
	$p_title = 'Laporan Daftar Seri Barang';
	$p_tbwidth = 450;
	$p_aktivitas = 'LAPORAN';
	
	$i_check = '<br><input type="checkbox" name="showchild" id="showchild" value="1" checked="checked" class="ControlStyle" />';
	$i_check .= '&nbsp;&nbsp;<i>Beserta unit didalamnya</i>';

	$i_barang = '<input type="textbox" name="showbarang" id="showbarang" size="35" /> <input type="hidden" name="idbarang" id="idbarang" />';

	$i_gedung = uCombo::combo(array(''=>'-- Pilih cabang dahulu --'),$r_gedung,'gedung','style="width:250px"',false,'');
	$i_lantai = uCombo::combo(mCombo::lantai(),$r_lantai,'lantai','style="width:250px"',true,'lantai');
	$i_jenis = uCombo::combo(mCombo::jenislokasi($conn),$r_jenisruang,'jenisruang','style="width:250px"',true,'jenis ruang');
	$i_sumber = uCombo::combo(mCombo::sumberdana($conn),$r_sumber,'sumber','style="width:250px"',true,'sumber dana');
	$i_merk = uCombo::combo(mCombo::merk($conn),$r_merk,'merk','style="width:150px"',true,'merk');

	$a_input = array();
	$a_input[] = array('label' => 'Unit', 'input' => uCombo::unit($conn,$r_unit,'unit','style="width:300px"',false).$i_check);
	$a_input[] = array('label' => 'Cabang', 'input' => uCombo::cabang($conn,$r_cabang,'cabang','style="width:250px"',true));
	$a_input[] = array('label' => 'Gedung', 'input' => $i_gedung);
	$a_input[] = array('label' => 'Lantai', 'input' => $i_lantai);
	$a_input[] = array('label' => 'Lokasi/Ruang', 'input' => uCombo::lokasi($conn,$r_lokasi,'lokasi','style="width:300px"',true));
	$a_input[] = array('label' => 'Jenis Ruang', 'input' => $i_jenis);
	$a_input[] = array('label' => 'Sumber Dana', 'input' => $i_sumber);
	$a_input[] = array('label' => 'Merk', 'input' => $i_merk);
	$a_input[] = array('label' => 'Barang', 'input' => $i_barang);
	$a_input[] = array('label' => 'Periode' , 'input' => $l_bulan1.' s/d '.$l_bulan2.' - '.$l_tahun);

	require_once($conf['view_dir'].'inc_repp.php');
?>

