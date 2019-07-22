<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();

	$conn->debug = true;
	
	$c_insert = false;
	$c_edit = true;
	$c_delete = false;
	
	// include
	require_once(Route::getModelPath('saldoawal'));
	
	// struktur view
	$a_kolom = array();
	$a_kolom[] = array('kolom' => 'id', 'label' => 'ID.', 'size' => 2, 'maxlength' => 2, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'idunit', 'label' => 'ID. Unit', 'size' => 4, 'maxlength' => 45, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'idlokasi', 'label' => 'ID. Lokasi', 'size' => 4, 'maxlength' => 45, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'ruang', 'label' => 'Ruang', 'size' => 4, 'maxlength' => 45, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'idgedung', 'label' => 'Gedung', 'size' => 4, 'maxlength' => 45, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'kodebarang', 'label' => 'ID. Barang', 'size' => 4, 'maxlength' => 45, 'notnull' => true);
	$a_kolom[] = array('kolom' => 'namabarang', 'label' => 'Nama Barang', 'size' => 4, 'maxlength' => 45, 'notnull' => true);
	
	// properti halaman
	$p_title = 'Daftar Saldo Awal';
	$p_tbwidth = 1300;
	$p_aktivitas = 'saldo awal';
	
	$p_model = mSaldoAwal;
	$p_key = $p_model::key;
	$p_colnum = count($a_kolom)+1;
	
	// ada aksi
	$r_act = $_POST['act'];
	if($r_act == 'insert' and $c_insert) {
		list($p_posterr,$p_postmsg) = $p_model::insertInPlace($conn,$a_kolom,$_POST);
	}
	else if($r_act == 'update' and $c_edit) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::updateInPlace($conn,$a_kolom,$_POST,$r_key);
	}
	else if($r_act == 'delete' and $c_delete) {
		$r_key = CStr::removeSpecial($_POST['key']);
		
		list($p_posterr,$p_postmsg) = $p_model::delete($conn,$r_key);
	}
	else if($r_act == 'edit' and $c_edit)
		$r_edit = CStr::removeSpecial($_POST['key']);
	
	// mendapatkan data ex
//	$r_sort = Page::setSort($_POST['sort']);
    $r_doc = Modul::setRequest($_POST['doc'],'DOC');
	
	$sql = "select doc from aset.rekap_kodebarang group by doc order by doc";
	$a_doc = Query::arrQuery($conn,$sql);
	$l_doc = UI::createSelect('doc',$a_doc,$r_doc,'ControlStyle',true,'',true,'-- Pilih doc --');
	
    //print_r($a_doc);
	
	$sql = "select r.*,b.namabarang as mnamabarang,u.kodeunit,u.namaunit,l.namalokasi,p.nip,p.namalengkap  
	    from aset.rekap_kodebarang r 
	    left join aset.ms_barang b on b.idbarang = r.kodebarang 
	    left join aset.ms_unit u on u.idunit = r.idunit 
	    left join aset.ms_lokasi l on l.idlokasi = r.idlokasi 
	    left join sdm.v_biodatapegawai p on p.idpegawai = r.idpegawai ";
    if(!empty($r_doc))
        $sql .= "where doc = '$r_doc' ";
    $sql .= "order by id";
	$rs = $conn->Execute($sql);
	
?>

<html>
<head>
	<title><?= $p_title ?></title>
	<meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
	<link rel="icon" type="image/x-icon" href="images/favicon.png">
	<link href="style/style.css" rel="stylesheet" type="text/css">
	<link href="style/pager.css" rel="stylesheet" type="text/css">
	<link href="style/calendar.css" rel="stylesheet" type="text/css">
	<link href="scripts/facybox/facybox.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="scripts/forinplace.js"></script>
	<script type="text/javascript" src="scripts/calendar.js"></script>
	<script type="text/javascript" src="scripts/calendar-id.js"></script>
	<script type="text/javascript" src="scripts/calendar-setup.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
    <br><br>
			<form name="pageform" id="pageform" method="post">
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" align="center">
				    <tr>
				        <td>Doc : <?= $l_doc ?></td>
				    </tr>
				</table>
				<br>
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr>
						<th rowspan="2">ID.</th>
						<th colspan="4">Data Kebutuhan Sistem</th>
						<th colspan="7">Data dari Excel</th>
						<th rowspan="2">Aksi</th>
                    </tr>
					<tr>
						<th>Unit</th>
						<th>Lokasi</th>
						<th>Barang</th>
						<th>Pemakai</th>
						<th>Ruang</th>
						<th>Lantai</th>
						<th>Gedung</th>
						<th>Nama Barang</th>
						<th>Jumlah</th>
						<th>Petugas</th>
						<th>Doc</th>
					</tr>
				    <?	$i = 0;
						while($row = $rs->FetchRow()) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
					<tr id="tr_<?= $row['id'] ?>" valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['id'] ?></td>
						<td><?= $row['kodeunit'].' - '.$row['namaunit'] ?></td>
						<td><?= $row['idlokasi'].' - '.$row['namalokasi'] ?></td>
						<td><?= $row['kodebarang'].' - '.$row['mnamabarang'] ?></td>
						<td><?= $row['nip'].' - '.$row['namalengkap'] ?></td>
						<td><?= $row['ruang'] ?></td>
						<td><?= $row['lantai'] ?></td>
						<td><?= $row['idgedung'] ?></td>
						<td><?= $row['namabarang'] ?></td>
						<td><?= $row['jumlah'] ?></td>
						<td><?= $row['petugas'] ?></td>
						<td><?= $row['doc'] ?></td>
						<td align="center">
							<img title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(<?= $row['id'] ?>)" style="cursor:pointer">
						</td>
					</tr>
					<?
						}
					?>
				</table>
				
				<input type="hidden" name="sort" id="sort">
				<input type="hidden" name="act" id="act">
				<input type="hidden" name="key" id="key">
				<input type="hidden" name="scroll" id="scroll" value="<?= (int)$_POST['scroll'] ?>">
			</form>
</div>

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
var detform = "<?= Route::navAddress('pop_saldoawal') ?>";

$(document).ready(function() {
	// handle sort
	$("th[id]").css("cursor","pointer").click(function() {
		$("#sort").val(this.id);
		goSubmit();
	});
	
	// handle scrolltop
	$(window).scrollTop($("#scroll").val());
	
	<? if($p_posterr === false) { ?>
	initRefresh();
	<? } ?>
	
	$('#doc').change(function(){
	    goSubmit();
	});
});

function goEdit(pkey){
    $.ajax({
        url: detform,
        type: "POST",
        data: {key : pkey},
        success: function(data){
            $.facybox(data);
        }
    });
}

</script>
</body>
</html>
