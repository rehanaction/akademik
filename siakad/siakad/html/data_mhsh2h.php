<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	
	$c_insert = $a_auth['caninsert'];
	$c_update = $a_auth['canupdate'];
	$c_delete = $a_auth['candelete'];
	
	// include
	require_once(Route::getModelPath('mahasiswa'));
	require_once(Route::getUIPath('combo'));
	require_once(Route::getUIPath('form'));
	
	// koneksi database
	$connh = Query::connect('h2h');
	$connh->debug = $conn->debug;
	
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	
	if((empty($r_key) and $c_insert) or (!empty($r_key) and $c_update))
		$c_edit = true;
	else
		$c_edit = false;
	
	// properti halaman
	$p_title = 'Data Mahasiswa H2H';
	$p_tbwidth = 500;
	$p_aktivitas = 'BIODATA';
	$p_listpage = Route::getListPage();
	
	$p_model = mMahasiswaH2H;
	
	// hak akses tambahan
	$a_authlist = Modul::getFileAuth($p_listpage);
	
	$c_readlist = empty($a_authlist) ? false : true;
	if(empty($r_key) and !$c_insert)
		Route::navigate($p_listpage);
	
	// struktur view
	$a_unit = $p_model::unit($connh);
	
	$a_tree = array();
	$a_fakultas = array();
	foreach($a_unit as $t_fakultas => $t_unit) {
		$a_tree[':'.$t_fakultas] = $t_fakultas;
		foreach($t_unit['data'] as $t_jurusan => $t_namajurusan) {
			$a_tree[$t_jurusan] = '&nbsp;&nbsp;&nbsp;&nbsp;'.$t_namajurusan;
			$a_fakultas[$t_jurusan] = $t_fakultas;
		}
	}
	
	$a_input = array();
	$a_input[] = array('kolom' => 'nim', 'label' => 'NIM', 'maxlength' => 10, 'size' => 9, 'notnull' => true);
	$a_input[] = array('kolom' => 'nama', 'label' => 'Nama', 'maxlength' => 60, 'size' => 40, 'notnull' => true);
	$a_input[] = array('kolom' => 'jurusan', 'label' => 'Prodi', 'type' => 'S', 'option' => $a_tree);
	$a_input[] = array('kolom' => 'kelamin', 'label' => 'Jenis Kelamin', 'type' => 'R', 'option' => mMahasiswa::jenisKelamin(), 'default' => 'L');
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'save' and $c_edit) {
		list($post,$record) = uForm::getPostRecord($a_input,$_POST);
		
		if(empty($r_key))
			list($p_posterr,$p_postmsg) = $p_model::insertCRecord($connh,$a_input,$record,$r_key);
		else
			list($p_posterr,$p_postmsg) = $p_model::updateCRecord($connh,$a_input,$record,$r_key);
		
		if(!$p_posterr) unset($post);
	}
	else if($r_act == 'delete' and $c_delete) {
		list($p_posterr,$p_postmsg) = $p_model::delete($connh,$r_key);
		
		if(!$p_posterr) Route::navigate($p_listpage);
	}
	
	// ambil data halaman
	$row = $p_model::getDataEdit($connh,$a_input,$r_key,$post);
	
	require_once(Route::getViewPath('inc_data'));
?>
<script type="text/javascript">

function goSave() {
	// cek unit
	var pass = true;
	
	if(document.getElementById("jurusan").value.charAt(0) == ":") {
		pass = false;
		doHighlight(document.getElementById("jurusan"));
		
		alert("Mohon pilih unit Prodi");
	}
	
	if(pass) {
		if(typeof(required) != "undefined") {
			if(!cfHighlight(required))
				pass = false;
		}
	}
	
	if(pass) {
		document.getElementById("act").value = "save";
		goSubmit();
	}
}

</script>