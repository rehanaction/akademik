<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//$a_auth = Modul::getFileAuth();
	//$conn->debug = true;
	
	$c_insert = false;
	$c_edit = true;
	$c_delete = false;
	
	$isshow = true;
	
	// properti halaman
	$p_title = 'Daftar Saldo Awal';
	$p_tbwidth = 1300;
	$p_aktivitas = 'saldo awal';
	
	// mendapatkan data ex
    $r_doc = Modul::setRequest($_POST['doc'],'DOC');
    $r_unit = Modul::setRequest($_POST['unit'],'UNITA');
    $r_lokasi = Modul::setRequest($_POST['lokasi'],'LOKASIA');
    $r_pegawai = Modul::setRequest($_POST['pegawai'],'PEGAWAIA');

	//if(empty($r_doc))
	//    $r_doc = $conn->GetOne("select doc from aset.aa_saldoawal where idsaldoawal = 1");

	//if(empty($r_unit))
	//    $r_unit = '63';
	
	$sql = "select doc from aset.aa_saldoawal group by doc order by doc";
	$a_doc = Query::arrQuery($conn,$sql);
	$l_doc = UI::createSelect('doc',$a_doc,$r_doc,'ControlStyle',true,'',true,'-- Pilih doc --');
	
	$sql = "select a.idunit,u.namaunit from aset.aa_saldoawal a join aset.ms_unit u on u.idunit = a.idunit group by a.idunit,u.namaunit order by u.namaunit";
	$a_unit = Query::arrQuery($conn,$sql);
	$l_unit = UI::createSelect('unit',$a_unit,$r_unit,'ControlStyle',true,'',true,'-- Pilih unit --');
	
	$sql = "select s.idlokasi,s.idlokasi+' - '+l.namalokasi 
	    from aset.aa_saldoawal s join aset.ms_lokasi l on s.idlokasi = l.idlokasi where (1=1) ";
    if(!empty($r_unit))
        $sql .= "and s.idunit = '$r_unit' ";
	$sql .= "group by s.idlokasi,l.namalokasi order by s.idlokasi";
	$a_lokasi = Query::arrQuery($conn,$sql);
	$l_lokasi = UI::createSelect('lokasi',$a_lokasi,$r_lokasi,'ControlStyle',true,'',true,'-- Pilih lokasi --');
	
	$sql = "select s.idpegawai,p.namalengkap 
	    from aset.aa_saldoawal s join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
	    where (1=1) ";
    if(!empty($r_unit))
        $sql .= "and s.idunit = '$r_unit' ";
	$sql .= "group by s.idpegawai,p.namalengkap order by p.namalengkap";
	$a_pegawai = Query::arrQuery($conn,$sql);
	$l_pegawai = UI::createSelect('pegawai',$a_pegawai,$r_pegawai,'ControlStyle',true,'',true,'-- Pilih pegawai --');
	
	
    $sql = "select s.*,b.idbarang+' - '+b.namabarang as barang,u.namaunit as unit,
        l.idlokasi as lokasi,p.namalengkap as pegawai,o.idbarang as idbrg 
        from aset.aa_saldoawal s 
        left join aset.ms_barang b on b.idbarang = s.idbarang 
        left join aset.ms_unit u on u.idunit = s.idunit 
        left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
        left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
        left join aset.as_perolehan o on o.idsaldoawal = s.idsaldoawal where (1=1) ";
    if(!empty($r_doc))
        $sql .= "and s.doc = '$r_doc' ";
    if(!empty($r_unit))
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi))
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_pegawai))
        $sql .= "and s.idpegawai = '$r_pegawai' ";
    $sql .= "order by s.idsaldoawal";
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
				        <td width="100">Doc</td>
				        <td>: <?= $l_doc ?></td>
				    </tr>
				    <tr>
				        <td>Unit</td>
				        <td>: <?= $l_unit ?></td>
				    </tr>
				    <tr>
				        <td>Lokasi</td>
				        <td>: <?= $l_lokasi ?></td>
				    </tr>
				    <tr>
				        <td>Pemakai</td>
				        <td>: <?= $l_pegawai ?></td>
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
						<th>Lokasi</th>
						<th>Unit</th>
						<th>Barang</th>
						<th>Pemakai</th>
						<th>Nama Barang</th>
						<th>Jml.</th>
						<th>Merk</th>
						<th>Spec.</th>
						<th>Kondisi</th>
						<th>Pemakai</th>
						<th>Doc</th>
					</tr>
				    <?	$i = 0;
						while($row = $rs->FetchRow()) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
					<tr id="tr_<?= $row['idsaldoawal'] ?>" valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['idsaldoawal'] ?></td>
						<td><?= $row['lokasi'] ?></td>
						<td><?= $row['unit'] ?></td>
						<td><?//= ($isshow == true) ? $row['idbrg'].'<br>' : '' ?><?= $row['barang'] ?></td>
						<td><?= $row['pegawai'] ?></td>
						<td><?= $row['xnamabarang'] ?></td>
						<td><?= $row['jml'] ?></td>
						<td><?= $row['merk'] ?></td>
						<td><?= $row['spesifikasi'] ?></td>
						<td><?= $row['idkondisi'] ?></td>
						<td><?= $row['xnamapegawai'] ?></td>
						<td><?= $row['doc'].'/'.$row['sheet'] ?></td>
						<td align="center">
							<img title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(<?= $row['idsaldoawal'] ?>)" style="cursor:pointer">
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
	
	$('#doc, #unit, #lokasi, #pegawai').change(function(){
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
