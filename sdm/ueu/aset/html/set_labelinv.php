<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('list_inventarisasi');
	$conn->debug = false;

	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_format = $_REQUEST['format'];
	
	// properti halaman
	$p_title = 'Cetak Label Inventarisasi';
	$p_tbwidth = 700;
	$p_ncol = 8;
	$p_namafile = 'label_'.$r_key;
	$r_format = 'html';
	
	$sql = "select s.idseri from aset.as_seri s 
	    left join aset.as_perolehandetail d on d.iddetperolehan = s.iddetperolehan 
	    left join aset.as_perolehan p on p.idperolehan = d.idperolehan 
	    where p.idperolehan = '$r_key' order by d.iddetperolehan";
	$rs = $conn->Execute($sql);

	while($row = $rs->FetchRow()){
	    $data[] = $row['idseri'];
	}
	
	$nrow = ceil(count($data)/3);
	$npage = ceil(count($data)/12);

	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>

<html>
<head>
<title><?= $p_title ?></title>
</head>
<body>
<div align="left">
    <?  $n = 0;
        for($i=0; $i<$npage; $i++){
    ?>
    <div style="height:40px;"></div>
    <table border="0" cellspacing="0">
        <?  for($j=0; $j<$nrow; $j++){ ?>
        <tr valign="top">
            <?  for($k=0; $k<3; $k++){ ?>
	        <td align="left">
	            <?  if(!empty($data[$n])){ ?>
                <img height="125" src="<?= 'http://'.$_SERVER['HTTP_HOST'].'/ueu/aset/index.php?page=label&idseri='.$data[$n] ?>" border="1" />
                <?  } ?>
            </td>
            <td width="30">&nbsp;</td>
            <?      $n++;
                }
            ?>
        </tr>
        <tr><td height="50">&nbsp;</td></tr>
        <?  } ?>
    </table>
    <div style="page-break-after:always"></div>
    <?  } ?>
</div>
</body>
</html>

