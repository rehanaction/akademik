<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	//Modul::getFileAuth('list_inventarisasi');
	$conn->debug = false;
    
    require_once($conf['includes_dir'].'fpdf17/fpdf.php');
    
	// variabel request
	$r_from = CStr::removeSpecial($_REQUEST['from']);
	$r_startno = CStr::removeSpecial($_REQUEST['startno']);
    if(empty($r_startno))
        $r_startno = 1;

	$data = array();
	for($a=1; $a<$r_startno; $a++)
	    $data[] = '';
	
	if($r_from == 'inv'){
    	$r_key = CStr::removeSpecial($_REQUEST['key']);

	    $sql = "select s.idseri from aset.as_seri s 
	        left join aset.as_perolehandetail d on d.iddetperolehan = s.iddetperolehan 
	        left join aset.as_perolehan p on p.idperolehan = d.idperolehan 
	        where p.idperolehan = '$r_key' order by d.iddetperolehan";
	    $rs = $conn->Execute($sql);
	    while($row = $rs->FetchRow())
	        $data[] = $row['idseri'];

	}else if($r_from == 'dir'){
    	$r_idunit = CStr::removeSpecial($_REQUEST['unit']);
    	$r_idlokasi = CStr::removeSpecial($_REQUEST['lokasi']);
    	$r_idpemakai = CStr::removeSpecial($_REQUEST['pemakai']);
    	
    	$a_unit = $conn->GetRow("select infoleft,inforight from aset.ms_unit where idunit = '$r_idunit'");

	    $sql = "select s.idseri from aset.as_seri s join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
	        where s.idunit = '$r_idunit' ";

        if(!empty($r_idlokasi))
            $sql .= "and s.idlokasi = '$r_idlokasi' ";
        if(!empty($r_idpemakai))
            $sql .= "and s.idpegawai = '$r_idpemakai' ";
        
        $sql .= "order by b.namabarang,s.noseri";

	    $rs = $conn->Execute($sql);
	    while($row = $rs->FetchRow())
	        $data[] = $row['idseri'];
	}else if($r_from == 'seri'){
	    $r_key = CStr::removeSpecial($_REQUEST['key']);
	    
	    $data[] = $r_key;
	}

    if(!empty($r_from)){
        //parameter label
        $top = 10;
        $left = 8;

        $xspace = 14;
        $yspace = 19;
        
        $width = 55;
        $height = 20;

        $pdf = new FPDF('P','mm','A4');

        $n = 0;
	    //$nrow = ceil(count($data)/3);
	    $npage = ceil(count($data)/12);

        for($i=0; $i<$npage; $i++){         //halaman
            $pdf->AddPage();
            
            for($j=0; $j<4; $j++){          //baris
                $y = $top+($yspace*$j)+($height*$j);
                
                for($k=0; $k<3; $k++){      //per label
                    $x = $left+($xspace*$k)+($width*$k);
                    if(!empty($data[$n])){
                        $pdf->Image($conf['full_urlaset'].'index.php?page=label&idseri='.$data[$n],$x,$y,$width,0,'PNG');
                    }
                    $n++;
                }
            }
        } 

        $pdf->Output();
    }
?>

