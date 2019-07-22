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
	$r_format = 'doc';
	
	$sql = "select s.idseri from aset.as_seri s 
	    left join aset.as_perolehandetail d on d.iddetperolehan = s.iddetperolehan 
	    left join aset.as_perolehan p on p.idperolehan = d.idperolehan 
	    where p.idperolehan = '$r_key' order by d.iddetperolehan";
	$rs = $conn->Execute($sql);
	
	// header
	//Page::setHeaderFormat($r_format,$p_namafile);
?>

<html>
<head>
<title><?= $p_title ?></title>
</head>
<body>
<div align="center">
    <table border="0" cellspacing="0">
        <tr>
    <?  $i = 1;
        while($row = $rs->FetchRow()){ 
            if ($i % 2 and $i > 1) echo "</tr><tr>";
        
    ?>
	        <td align="left">
                <img height="100" src="<?= 'http://'.$_SERVER['HTTP_HOST'].'/ueu/aset/index.php?page=label&idseri='.$row['idseri'] ?>" border="0" />
            </td>
    <?      $i++;
        } 
    ?>
        </tr>
    </table>

</div>
</body>
</html>

