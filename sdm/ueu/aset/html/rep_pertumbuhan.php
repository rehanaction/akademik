<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('repp_pertumbuhan');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_showchild = CStr::removeSpecial($_REQUEST['showchild']);
	$r_bulan = CStr::removeSpecial($_REQUEST['bulan']);
	$r_tahun = CStr::removeSpecial($_REQUEST['tahun']);
	$r_format = CStr::removeSpecial($_REQUEST['format']);
	$r_level = CStr::removeSpecial($_REQUEST['level']);
	
	//$ltgl = $r_tahun.'-'.$r_bulan.'-1';
	//echo $ltgl;
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Aktivitas Barang :.';	
	$p_tbwidth = 900;
	$p_ncol = 9;
	$p_namafile = 'pertumbuhan_barang_'.$r_unit;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
    $a_bulan = mCombo::bulan();
    $a_barang = array();
	$a_level = mCombo::level($conn);
    
	/** Query Lama    //barang saja
	$sql = "select p.idbarang1, b.namabarang
        from aset.as_perolehan p join aset.ms_barang1 b on b.idbarang1 = p.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
    $sql .= " group by p.idbarang1, b.namabarang order by p.idbarang1";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}	Akhir Query Lama **/
	
	if($r_level == '1'){	/*	Barang Level 1	*/
	$sql = "select substring(p.idbarang1,1,1) as idbarang1, b.namabarang
			from aset.as_perolehan p 
			join aset.ms_barang1 b on substring(p.idbarang1,1,1)+'000000000' = b.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,1) = substring(p.idbarang1,1,1) ";
	$sql .= "group by substring(p.idbarang1,1,1),b.namabarang order by substring(p.idbarang1,1,1)";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}	}
	else if($r_level == '2'){	/*	Barang Level 2	*/
	$sql = "select substring(p.idbarang1,1,3) as idbarang1, b.namabarang
			from aset.as_perolehan p 
			join aset.ms_barang1 b on substring(p.idbarang1,1,3)+'0000000' = b.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,3) = substring(p.idbarang1,1,3) ";
	$sql .= "group by substring(p.idbarang1,1,3),b.namabarang order by substring(p.idbarang1,1,3)";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}	}
	else if($r_level == '3'){	/*	Barang Level 3	*/
	$sql = "select substring(p.idbarang1,1,5) as idbarang1, b.namabarang
			from aset.as_perolehan p 
			join aset.ms_barang1 b on substring(p.idbarang1,1,5)+'00000' = b.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,5) = substring(p.idbarang1,1,5) ";
	$sql .= "group by substring(p.idbarang1,1,5),b.namabarang order by substring(p.idbarang1,1,5)";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}	}
	else if($r_level == '4'){	/*	Barang Level 4	*/
	$sql = "select substring(p.idbarang1,1,7) as idbarang1, b.namabarang
			from aset.as_perolehan p 
			join aset.ms_barang1 b on substring(p.idbarang1,1,7)+'000' = b.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,7) = substring(p.idbarang1,1,7) ";
	$sql .= "group by substring(p.idbarang1,1,7),b.namabarang order by substring(p.idbarang1,1,7)";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}	}
	else if($r_level == '5'){	/*	Barang Level 5	*/
	$sql = "select substring(p.idbarang1,1,10) as idbarang1, b.namabarang
			from aset.as_perolehan p 
			join aset.ms_barang1 b on p.idbarang1 = b.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,10) = substring(p.idbarang1,1,10) ";
	$sql .= "group by substring(p.idbarang1,1,10),b.namabarang order by substring(p.idbarang1,1,10)";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}	}
	else if($r_level == '6' or empty($r_level)){	/*	Barang Level 6	*/
	$sql = "select p.idbarang1 as idbarang1, b.namabarang
			from aset.as_perolehan p 
			join aset.ms_barang1 b on p.idbarang1 = b.idbarang1 ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) <= '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight'];
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and p.idbarang1 = p.idbarang1 ";
	$sql .= "group by p.idbarang1,b.namabarang order by p.idbarang1";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
	    $a_barang[$row['idbarang1']]['jmla'] = 0;
	    $a_barang[$row['idbarang1']]['nilaia'] = 0;
	    $a_barang[$row['idbarang1']]['jmln'] = 0;
	    $a_barang[$row['idbarang1']]['nilain'] = 0;
	}	}
	
	//Barang Awal
	if($r_level == '1'){		/*	Barang Awal Level 1	*/
	$sql = "select substring(p.idbarang1,1,1) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) < '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,1) = substring(p.idbarang1,1,1) ";
    $sql .= "group by substring(p.idbarang1,1,1) order by substring(p.idbarang1,1,1) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmla'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilaia'] = (int)$row['total'];
	}	}
	else if($r_level == '2'){		/*	Barang Awal Level 2	*/
	$sql = "select substring(p.idbarang1,1,3) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) < '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,3) = substring(p.idbarang1,1,3) ";
    $sql .= "group by substring(p.idbarang1,1,3) order by substring(p.idbarang1,1,3) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmla'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilaia'] = (int)$row['total'];
	}	}
	else if($r_level == '3'){		/*	Barang Awal Level 3	*/
	$sql = "select substring(p.idbarang1,1,5) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) < '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,5) = substring(p.idbarang1,1,5) ";
    $sql .= "group by substring(p.idbarang1,1,5) order by substring(p.idbarang1,1,5) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmla'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilaia'] = (int)$row['total'];
	}	}
	else if($r_level == '4'){		/*	Barang Awal Level 4	*/
	$sql = "select substring(p.idbarang1,1,7) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) < '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,7) = substring(p.idbarang1,1,7) ";
    $sql .= "group by substring(p.idbarang1,1,7) order by substring(p.idbarang1,1,7) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmla'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilaia'] = (int)$row['total'];
	}	}
	else if($r_level == '5' or empty($r_level)){		/*	Barang Awal Level 5	*/
	$sql = "select p.idbarang1 as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) <= '$r_tahun' and month(p.tglperolehan) < '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
	if(!empty($r_level)) 
        $sql .= "and p.idbarang1 = p.idbarang1 ";
    $sql .= "group by p.idbarang1 order by p.idbarang1 ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmla'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilaia'] = (int)$row['total'];
	}	}

	//Barang Bulan Ini
	if($r_level == '1'){		/*	Barang Bulan Ini Level 1	*/
	$sql = "select substring(p.idbarang1,1,1) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) = '$r_tahun' and month(p.tglperolehan) = '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
    if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,1) = substring(p.idbarang1,1,1) ";
    $sql .= "group by substring(p.idbarang1,1,1) order by substring(p.idbarang1,1,1) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmln'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilain'] = (int)$row['total'];
	}	}
	else if($r_level == '2'){		/*	Barang Bulan Ini Level 2	*/
	$sql = "select substring(p.idbarang1,1,3) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) = '$r_tahun' and month(p.tglperolehan) = '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
    if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,3) = substring(p.idbarang1,1,3) ";
    $sql .= "group by substring(p.idbarang1,1,3) order by substring(p.idbarang1,1,3) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmln'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilain'] = (int)$row['total'];
	}	}
	else if($r_level == '3'){		/*	Barang Bulan Ini Level 3	*/
	$sql = "select substring(p.idbarang1,1,5) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) = '$r_tahun' and month(p.tglperolehan) = '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
    if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,5) = substring(p.idbarang1,1,5) ";
    $sql .= "group by substring(p.idbarang1,1,5) order by substring(p.idbarang1,1,5) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmln'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilain'] = (int)$row['total'];
	}	}
	else if($r_level == '4'){		/*	Barang Bulan Ini Level 4	*/
	$sql = "select substring(p.idbarang1,1,7) as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) = '$r_tahun' and month(p.tglperolehan) = '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
    if(!empty($r_level)) 
        $sql .= "and substring(p.idbarang1,1,7) = substring(p.idbarang1,1,7) ";
    $sql .= "group by substring(p.idbarang1,1,7) order by substring(p.idbarang1,1,7) ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmln'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilain'] = (int)$row['total'];
	}	}
	else if($r_level == '5' or empty($r_level)){		/*	Barang Bulan Ini Level 5	*/
	$sql = "select p.idbarang1 as idbarang1, sum(p.qty) as jumlah, sum(p.total) as total
			from aset.as_perolehan p ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = p.idunit ";
    $sql .= "where year(p.tglperolehan) = '$r_tahun' and month(p.tglperolehan) = '$r_bulan' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
       $sql .= "and p.idunit = '$r_unit' ";
    if(!empty($r_level)) 
        $sql .= "and p.idbarang1 = p.idbarang1 ";
    $sql .= "group by p.idbarang1 order by p.idbarang1 ";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()){
	    $a_barang[$row['idbarang1']]['jmln'] = (int)$row['jumlah'];
	    $a_barang[$row['idbarang1']]['nilain'] = (int)$row['total'];
	}	}

	// header
	Page::setHeaderFormat($r_format,$p_namafile);
?>

<html>

<head>
    <title><?= $p_title ?></title>
    <meta http-equiv="content-type" content="text/html;charset=iso-8859-1">
    <link rel="icon" type="image/x-icon" href="images/favicon.png">
    <link href="style/stylerep.css" rel="stylesheet" type="text/css">
</head>
<body>
<div align="center">
<?php
    include('inc_headerlap.php');
?>
<div class="div_head">
    Laporan Aktivitas Aset<br/>
    Universitas Esa Unggul<br/>
    Periode <?= $a_bulan[$r_bulan] ?> <?= $r_tahun ?>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="100">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
	<?  if(!empty($r_level)){ ?>
	<tr valign="top">
		<td>Level Barang</td>
		<td>:</td>
		<td><?= $a_level[$r_level] ?></td>
	</tr>
	<?  } ?>
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
</table>
<table class="tb_data" width="<?= $p_tbwidth ?>">
    <tr>
        <th rowspan="2" width="30">No.</th>
	    <th rowspan="2" width="100">Kode. Barang</th>
	    <th rowspan="2">Nama Barang</th>
	    <th colspan="2" width="100">Saldo Awal</th>
	    <th colspan="2" width="100">Aktivitas</th>
	    <th colspan="2" width="100">Saldo Akhir</th>
    </tr>
	<tr>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
		<th width="50">Jumlah</th>
		<th width="95">Nilai</th>
	</tr>
	<? 
	$i=0;
	foreach($a_barang as $idbarang1 => $val){
	    $i++;
	    $jml = $val['jmla']+$val['jmln'];
	    $nilai = $val['nilaia']+$val['nilain'];
		$jmla += (float)$val['jmla'];
		$jmln += (float)$val['jmln'];
		$nilaia += (float)$val['nilaia'];
		$nilain += (float)$val['nilain'];
		$jmlsaldo += $jml;
		$nilaisaldo += $nilai;	    
	?>
	<tr valign="top">
	    <td align="center"><?=$i;?>.</td>
	    <td><?= Aset::formatLevelBarang($idbarang1) ?></td>
	    <td><?= $val['namabarang'] ?></td>
	    <td align="right"><?= CStr::formatNumber($val['jmla']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilaia'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['jmln']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilain'],2) ?></td>
	    <td align="right"><?= CStr::formatNumber($jml) ?></td>
	    <td align="right"><?= CStr::formatNumber($nilai,2) ?></td>
	</tr>
	<?
	}
    if($i == 0) {
	?>
	<tr>
	    <td colspan="<?= $p_ncol ?>" align="center">-- Data tidak ditemukan --</td>
	</tr>
	<? } ?>
	<tr>
		<td colspan="3" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmla) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilaia,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmln) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilain,2) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$jmlsaldo) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilaisaldo,2) ?></b></td>
    </tr>
</table>
</div>
</body>
</html>
