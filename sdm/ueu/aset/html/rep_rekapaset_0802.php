<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	$conn->debug = true;
	// hak akses
	Modul::getFileAuth('repp_rekapaset');
	
	// variabel post
	$r_unit = CStr::removeSpecial($_REQUEST['unit']);
	$r_lokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    $r_showchild = CStr::removeSpecial($_REQUEST['showchild']);

	$r_cabang = CStr::removeSpecial($_REQUEST['cabang']);
	$r_gedung = CStr::removeSpecial($_REQUEST['gedung']);
	$r_lantai = CStr::removeSpecial($_REQUEST['lantai']);
	$r_jenisruang = CStr::removeSpecial($_REQUEST['jenisruang']);
	$r_level = CStr::removeSpecial($_REQUEST['level']);
	$r_tahun = Modul::setRequest($_POST['tahun'],'TAHUN');
	$r_bulan1 = Modul::setRequest($_POST['bulan1'],'BULAN1');
	$r_bulan2 = Modul::setRequest($_POST['bulan2'],'BULAN2');

	$r_format = CStr::removeSpecial($_REQUEST['format']);
	
	require_once(Route::getModelPath('laporan'));
	require_once(Route::getModelPath('combo'));
	
	// definisi variable halaman
	$p_title = '.: Laporan Rekap Daftar Barang :.';	
	$p_tbwidth = 600;
	$p_ncol = 4;
	$p_namafile = 'rekap_barang_'.$r_unit.'_'.$r_lokasi;
	
	$a_unit = mLaporan::getDataUnit($conn, $r_unit);
	if(!empty($r_lokasi))
    	$a_lokasi = mLaporan::getDataLokasi($conn, $r_lokasi);
	if(!empty($r_cabang))
    	$a_cabang = mLaporan::getCabang($conn, $r_cabang);
	if(!empty($r_gedung))
    	$a_gedung = mLaporan::getGedung($conn, $r_gedung);

	$a_lantai = mCombo::lantai();
	$a_jenisruang = mCombo::jenislokasi($conn);
	$a_level = mCombo::level($conn);
    $a_bulan = mCombo::bulan();    
	
	$a_barang = array();

	/*	------------------ Pengecekan Nama Barang berdasarkan Kelompok Barang----------------------	*/

	if($r_level == '1'){	/*	Pengecekan Barang Saja Level 1	*/
	$sql ="select substring(s.idbarang1,1,1) as idbarang1, b.namabarang
			from aset.as_seri s 
			left join aset.ms_barang1 b on substring(s.idbarang1,1,1)+'000000000' = b.idbarang1 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,1) = substring(s.idbarang1,1,1) ";
    $sql .= "group by substring(s.idbarang1,1,1),b.namabarang order by substring(s.idbarang1,1,1)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
		$a_barang[$row['idbarang1']]['total'] = 0;
		$a_barang[$row['idbarang1']]['nilai'] = 0;
	}	}
	else if($r_level == '2'){	/*	Pengecekan Barang Saja Level 2	*/
	$sql ="select substring(s.idbarang1,1,3) as idbarang1, b.namabarang
			from aset.as_seri s 
			join aset.ms_barang1 b on substring(s.idbarang1,1,3)+'0000000' = b.idbarang1 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,3) = substring(s.idbarang1,1,3) ";
    $sql .= "group by substring(s.idbarang1,1,3),b.namabarang order by substring(s.idbarang1,1,3)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
		$a_barang[$row['idbarang1']]['total'] = 0;
		$a_barang[$row['idbarang1']]['nilai'] = 0;
	}	}	
	else if($r_level == '3'){	/*	Pengecekan Barang Saja Level 3	*/
	$sql ="select substring(s.idbarang1,1,5) as idbarang1, b.namabarang
			from aset.as_seri s 
			join aset.ms_barang1 b on substring(s.idbarang1,1,5)+'00000' = b.idbarang1 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,5) = substring(s.idbarang1,1,5) ";
    $sql .= "group by substring(s.idbarang1,1,5),b.namabarang order by substring(s.idbarang1,1,5)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
		$a_barang[$row['idbarang1']]['total'] = 0;
		$a_barang[$row['idbarang1']]['nilai'] = 0;
	}	}
	else if($r_level == '4'){	/*	Pengecekan Barang Saja Level 4	*/
	$sql ="select substring(s.idbarang1,1,7) as idbarang1, b.namabarang
			from aset.as_seri s 
			join aset.ms_barang1 b on substring(s.idbarang1,1,7)+'000' = b.idbarang1 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,7) = substring(s.idbarang1,1,7) ";
    $sql .= "group by substring(s.idbarang1,1,7),b.namabarang order by substring(s.idbarang1,1,7)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
		$a_barang[$row['idbarang1']]['total'] = 0;
		$a_barang[$row['idbarang1']]['nilai'] = 0;
	}	}
    else if($r_level == '5'){   /*  Pengecekan Barang Saja Level 5  */
    $sql ="select substring(s.idbarang1,1,10) as idbarang1, b.namabarang
            from aset.as_seri s 
            join aset.ms_barang1 b on substring(s.idbarang1,1,10) = b.idbarang1 
            left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
            left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
             and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,10) = substring(s.idbarang1,1,10) ";
    $sql .= "group by substring(s.idbarang1,1,10),b.namabarang order by substring(s.idbarang1,1,10)";

    $rs = $conn->Execute($sql);     
    while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
        $a_barang[$row['idbarang1']]['total'] = 0;
        $a_barang[$row['idbarang1']]['nilai'] = 0;
    }   }
	else if($r_level == '6' or empty($r_level)){	/*	Pengecekan Barang Saja Level 6	*/
	$sql ="select s.idbarang1 as idbarang1, b.namabarang
			from aset.as_seri s 
			join aset.ms_barang1 b on s.idbarang1 = b.idbarang1 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and s.idbarang1 = s.idbarang1 ";
    $sql .= "group by s.idbarang1,b.namabarang order by s.idbarang1 ";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['namabarang'] = $row['namabarang'];
		$a_barang[$row['idbarang1']]['total'] = 0;
		$a_barang[$row['idbarang1']]['nilai'] = 0;
	}	}
	/*	------------------ Pengecekan Jumlah Barang berdasarkan Kelompok Barang----------------------	*/

	if($r_level == '1'){	/*	Pengecekan Jumlah Barang Level 1	*/
	$sql ="select substring(s.idbarang1,1,1) as idbarang1, count(s.idseri) as total,sum(s.nilaiaset) as nilai
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,1) = substring(s.idbarang1,1,1) ";
    $sql .= "group by substring(s.idbarang1,1,1) order by substring(s.idbarang1,1,1)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['total'] += (float)$row['total'];
		$a_barang[$row['idbarang1']]['nilai'] += (float)$row['nilai'];
	}	}
	else if($r_level == '2'){	/*	Pengecekan Jumlah Barang Level 2	*/
	$sql ="select substring(s.idbarang1,1,3) as idbarang1, count(s.idseri) as total,sum(s.nilaiaset) as nilai
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,3) = substring(s.idbarang1,1,3) ";
    $sql .= "group by substring(s.idbarang1,1,3) order by substring(s.idbarang1,1,3)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['total'] += (float)$row['total'];
		$a_barang[$row['idbarang1']]['nilai'] += (float)$row['nilai'];
	}	}
	else if($r_level == '3'){	/*	Pengecekan Jumlah Barang Level 3	*/
	$sql ="select substring(s.idbarang1,1,5) as idbarang1, count(s.idseri) as total,sum(s.nilaiaset) as nilai
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,5) = substring(s.idbarang1,1,5) ";
    $sql .= "group by substring(s.idbarang1,1,5) order by substring(s.idbarang1,1,5)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['total'] += (float)$row['total'];
		$a_barang[$row['idbarang1']]['nilai'] += (float)$row['nilai'];
	}	}
	else if($r_level == '4'){	/*	Pengecekan Jumlah Barang Level 4	*/
	$sql ="select substring(s.idbarang1,1,7) as idbarang1, count(s.idseri) as total,sum(s.nilaiaset) as nilai
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,7) = substring(s.idbarang1,1,7) ";
    $sql .= "group by substring(s.idbarang1,1,7) order by substring(s.idbarang1,1,7)";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['total'] += (float)$row['total'];
		$a_barang[$row['idbarang1']]['nilai'] += (float)$row['nilai'];
	}	}
    else if($r_level == '5'){   /*  Pengecekan Jumlah Barang Level 5    */
    $sql ="select substring(s.idbarang1,1,10) as idbarang1, count(s.idseri) as total,sum(s.nilaiaset) as nilai
            from aset.as_seri s 
            left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
            left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
             and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
    if(!empty($r_level)) 
        $sql .= "and substring(s.idbarang1,1,10) = substring(s.idbarang1,1,10) ";
    $sql .= "group by substring(s.idbarang1,1,10) order by substring(s.idbarang1,1,10)";

    $rs = $conn->Execute($sql);     
    while($row = $rs->FetchRow()){
        $a_barang[$row['idbarang1']]['total'] += (float)$row['total'];
        $a_barang[$row['idbarang1']]['nilai'] += (float)$row['nilai'];
    }   }
	else if($r_level == '6' or empty($r_level)){	/*	Pengecekan Jumlah Barang Level 6	*/
	$sql ="select s.idbarang1 as idbarang1, count(s.idseri) as total,sum(s.nilaiaset) as nilai
			from aset.as_seri s 
			left join aset.ms_lokasi l on l.idlokasi = s.idlokasi 
			left join aset.ms_gedung g on g.idgedung = l.idgedung ";
    if($r_showchild) 
        $sql .= "join aset.ms_unit u on u.idunit = s.idunit ";
    $sql .= "where (1=1) 
			 and datepart(year,s.tglperolehan) = '$r_tahun' and datepart(month,s.tglperolehan) between '$r_bulan1' and '$r_bulan2' ";
    if($r_showchild) 
        $sql .= "and u.infoleft >= ".(int)$a_unit['infoleft']." and u.inforight <= ".(int)$a_unit['inforight']." ";
    else
        $sql .= "and s.idunit = '$r_unit' ";
    if(!empty($r_lokasi)) 
        $sql .= "and s.idlokasi = '$r_lokasi' ";
    if(!empty($r_cabang)) 
        $sql .= " and g.idcabang = '$r_cabang' ";
    if(!empty($r_gedung)) 
        $sql .= "and g.idgedung = '$r_gedung' ";
    if(!empty($r_lantai)) 
        $sql .= "and l.lantai = '$r_lantai' ";
    if(!empty($r_jenisruang)) 
        $sql .= "and l.idjenislokasi = '$r_jenisruang' ";
        
    $sql .= "and s.idbarang1 = s.idbarang1 ";
    $sql .= "group by s.idbarang1 order by s.idbarang1 ";

	$rs = $conn->Execute($sql);		
	while($row = $rs->FetchRow()){
		$a_barang[$row['idbarang1']]['total'] += (float)$row['total'];
		$a_barang[$row['idbarang1']]['nilai'] += (float)$row['nilai'];
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
    Laporan Rekap Daftar Barang<br/>
    Universitas Esa Unggul<br/>
	Periode 
	<? if($r_bulan1 == $r_bulan2) { ?>
		<?= $a_bulan[$r_bulan1] ?>
	<? } else { ?>
		<?= $a_bulan[$r_bulan1] ?> - <?= $a_bulan[$r_bulan2] ?> 
	<? } ?>
	<?= $r_tahun ?>
</div>
<table class="tb_head" width="<?= $p_tbwidth ?>">
	<tr>
	    <td colspan="3">&nbsp;</td>
    </tr>
	<tr valign="top">
		<td width="80">Unit</td>
		<td width="5">:</td>
		<td><?= $a_unit['kodeunit'] ?> - <?= $a_unit['namaunit'] ?></td>
	</tr>
    <?  if(!empty($r_lokasi)){ ?>
	<tr valign="top">
		<td>Lokasi</td>
		<td>:</td>
		<td><?= $a_lokasi['idlokasi'] ?> - <?= $a_lokasi['namalokasi'] ?></td>
	</tr>
	<?  } ?>
    <?  if(!empty($r_cabang)){ ?>
	<tr valign="top">
		<td>Cabang</td>
		<td>:</td>
		<td><?= $a_cabang['idcabang'] ?> - <?= $a_cabang['namacabang'] ?></td>
	</tr>
	<?  } ?>
    <?  if(!empty($r_lantai)){ ?>
	<tr valign="top">
		<td>Lantai</td>
		<td>:</td>
		<td><?= $a_lantai[$r_lantai] ?></td>
	</tr>
	<?  } ?>
	<?  if(!empty($r_jenisruang)){ ?>
	<tr valign="top">
		<td>Jenis Ruang</td>
		<td>:</td>
		<td><?= $a_jenisruang[$r_jenisruang] ?></td>
	</tr>
	<?  } ?>
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
        <th width="30">No.</th>
	    <th>Barang</th>
	    <th width="80">Jumlah</th>
	    <th width="100">Nilai Aset</th>
    </tr>
	<? 
	$i=0;
	/*while ($row = $rs->FetchRow ()){
	    $i++;
		$total += $row['total'];
		$nilai += $row['nilai'];
	*/
	foreach($a_barang as $idbarang1 => $val){
	    $i++;
		$total += (float)$val['total'];
		$nilai += (float)$val['nilai'];
	 ?>
	<tr valign="top">
	    <td><?= $i; ?>.</td>
	    <td><?= Aset::formatLevelBarang($idbarang1).' - '.$val['namabarang'] ?></td>
	    <td align="right"><?= CStr::formatNumber($val['total']) ?></td>
	    <td align="right"><?= CStr::formatNumber($val['nilai'],2) ?></td>
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
		<td colspan="2" align="right"><b>Total</b>&nbsp;&nbsp;</td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$total) ?></b></td>
		<td align="right"><b><?= CStr::formatNumberRep($r_format,$nilai,2) ?></b></td>
	</tr>
</table>
<table class="tb_foot" width="<?= $p_tbwidth ?>">
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td>Penanggung Jawab Unit</td>
		<td>Pengelola Aset</td>
		<td>Mengetahui</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td colspan="3">&nbsp;</td>
	</tr>
	<tr>
		<td></td>
		<td>Kabag. RT</td>
		<td>Ka. Dept. Umum</td>
	</tr>
</table>
</div>
</body>
</html>
