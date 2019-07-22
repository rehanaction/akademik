<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	$a_auth = Modul::getFileAuth();
	//$conn->debug = true;
	
	$c_insert = true;
	$c_edit = true;
	$c_delete = false;
	
	$r_role = Modul::getRole();
	if($r_role == 'A')
	    $conn->debug = true;
	
	// properti halaman
	$p_title = 'Daftar Saldo Awal';
	$p_tbwidth = 950;
	$p_aktivitas = 'saldo awal';
    $p_detailpage = Route::navAddress('data_saldoawal');
	// mendapatkan data ex


    $r_idunitn = $_SESSION[SITE_ID]['VAR']['UNITA'];
    $r_unit = Modul::setRequest($_POST['unit'],'UNITA');
    $r_idunito = $_SESSION[SITE_ID]['VAR']['UNITA'];

    if($r_idunito != $r_idunitn){
        $_POST['lokasi'] = '';
        $_POST['pegawai'] = '';
    }

    $r_lokasi = Modul::setRequest($_POST['lokasi'],'LOKASIA');
    $r_pegawai = Modul::setRequest($_POST['pegawai'],'PEGAWAIA');

	
	$sql = "select a.idunit,u.namaunit from aset.aaa_saldoawal a join aset.ms_unit u on u.idunit = a.idunit group by a.idunit,u.namaunit order by u.namaunit";
	$a_unit = Query::arrQuery($conn,$sql);
	if(empty($r_unit))
	    $r_unit = '119';
	$l_unit = UI::createSelect('unit',$a_unit,$r_unit,'ControlStyle',true,'',true,'-- Pilih unit --');
	
	$sql = "select s.idlokasi,s.idlokasi+' - '+l.namalokasi 
	    from aset.aaa_saldoawal s join aset.ms_lokasi l on s.idlokasi = l.idlokasi where (1=1) ";
    if(!empty($r_unit))
        $sql .= "and s.idunit = '$r_unit' ";
	$sql .= "group by s.idlokasi,l.namalokasi order by s.idlokasi";
	$a_lokasi = Query::arrQuery($conn,$sql);
	$l_lokasi = UI::createSelect('lokasi',$a_lokasi,$r_lokasi,'ControlStyle',true,'',true,'-- Pilih lokasi --');
	
	$sql = "select s.idpegawai,p.namalengkap 
	    from aset.aaa_saldoawal s join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
	    where (1=1) ";
    if(!empty($r_unit))
        $sql .= "and s.idunit = '$r_unit' ";
	$sql .= "group by s.idpegawai,p.namalengkap order by p.namalengkap";
	$a_pegawai = Query::arrQuery($conn,$sql);
	$l_pegawai = UI::createSelect('pegawai',$a_pegawai,$r_pegawai,'ControlStyle',true,'',true,'-- Pilih pegawai --');
	
	
    $sql = "select s.*,b.idbarang1+' - '+b.namabarang as barang,u.namaunit as unit,
        l.idlokasi as lokasi,p.namalengkap as pegawai,r.noseri 
        from aset.aaa_saldoawal s 
        left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
        left join aset.ms_unit u on u.idunit = s.idunit 
        left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
        left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
        left join aset.as_seri r on r.idsaldoawal = s.idsaldoawal 
        where (1=1) ";
    if(!empty($r_unit))
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi))
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_pegawai))
        $sql .= "and s.idpegawai = '$r_pegawai' ";
    $sql .= "order by s.idpegawai,b.namabarang,r.noseri";
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
	<script type="text/javascript" src="scripts/forpager.js"></script>
</head>
<body>
<div id="main_content">
	<?php require_once('inc_header.php'); ?>
    <div id="wrapper">
		<div class="SideItem" id="SideItem">
			<form name="pageform" id="pageform" method="post">
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" align="center">
				    <tr>
				        <td width="100"></td>
				        <td></td>
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
				<center>
					<header style="width:<?= $p_tbwidth ?>px">
						<div class="inner">
							<?	if($c_insert or $p_printpage) { ?>
							<div class="right">
							<?
								if($c_insert) { ?>
								<div class="addButton" onClick="goNew()">+</div>
							<?	} ?>
							</div>
							<? } ?>
						</div>
					</header>
				</center>				
				<table width="<?= $p_tbwidth ?>" cellpadding="4" cellspacing="0" class="GridStyle" align="center">
					<tr>
						<th>ID.</th>
						<th>Lokasi</th>
						<th>Unit</th>
						<th>Pemakai</th>
						<th>No. Seri</th>
						<th>Nama Barang</th>
						<th>Merk</th>
						<th>Spec.</th>
						<th>Kondisi</th>
						<th>Aksi</th>
					</tr>
				    <?	$i = 0;
						while($row = $rs->FetchRow()) {
							if ($i % 2) $rowstyle = 'NormalBG';  else $rowstyle = 'AlternateBG'; $i++;
					?>
					<tr id="tr_<?= $row['idsaldoawal'] ?>" valign="top" class="<?= $rowstyle ?>">
						<td><?= $row['idsaldoawal'] ?></td>
						<td><?= $row['lokasi'] ?></td>
						<td><?= $row['unit'] ?></td>
						<td><?= $row['pegawai'] ?></td>
						<td><?= Aset::formatNoSeri($row['noseri']) ?></td>
						<td><?= $row['barang'] ?></td>
						<td><?= $row['merk'] ?></td>
						<td><?= $row['spesifikasi'] ?></td>
						<td><?= $row['idkondisi'] ?></td>
						<td align="center">
							<?/*img title="Tampilkan Detail" src="images/edit.png" onclick="goEdit(<?= $row['idsaldoawal'] ?>)" style="cursor:pointer"*/?>
							<img id="<?= $row['idsaldoawal'] ?>" style="cursor:pointer" onclick="goDetail(this)" src="images/edit.png" title="Tampilkan Detail">
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

    </div>
			
</div>

<script type="text/javascript" src="scripts/facybox/facybox.js"></script>
<script type="text/javascript">
var detform = "<?= Route::navAddress('pop_saldoawal') ?>";
var detailpage = "<?= Route::navAddress($p_detailpage) ?>";

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
