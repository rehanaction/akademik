<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	// hak akses
	Modul::getFileAuth('list_inventarisasi');
	$conn->debug = false;
    
    require_once($conf['includes_dir'].'fpdf17/fpdf.php');
    
	// variabel request
	$r_key = CStr::removeSpecial($_REQUEST['key']);
	$r_startno = CStr::removeSpecial($_REQUEST['startno']);
    if(empty($r_startno))
        $r_startno = 1;

	$data = array();
	for($a=1; $a<$r_startno; $a++)
	    $data[] = '';
	
	$sql = "select s.idseri from aset.as_seri s 
	    left join aset.as_perolehandetail d on d.iddetperolehan = s.iddetperolehan 
	    left join aset.as_perolehan p on p.idperolehan = d.idperolehan 
	    where p.idperolehan = '$r_key' order by d.iddetperolehan";
	$rs = $conn->Execute($sql);

	while($row = $rs->FetchRow())
	    $data[] = $row['idseri'];
	
	//$nrow = ceil(count($data)/3);
	$npage = ceil(count($data)/12);

    //parameter label
    $top = 10;
    $left = 5;

    $xspace = 12;
    $yspace = 17;
    
    $width = 55;
    $height = 20;

    $pdf = new FPDF('L','mm','A5');
    //$pdf->SetAutoPageBreak(true);

    $n = 0;
    for($i=0; $i<$npage; $i++){         //halaman
        $pdf->AddPage();
        for($j=0; $j<4; $j++){      //baris
            $y = $top+($yspace*$j)+($height*$j);
            for($k=0; $k<3; $k++){      //per label
                $x = $left+($xspace*$k)+($width*$k);
                if(!empty($data[$n])){
                    $pdf->Image('http://'.$_SERVER['HTTP_HOST'].'/ueu/aset/index.php?page=label&idseri='.$data[$n],$x,$y,$width,0,'PNG');
                }
                $n++;
            }
        }
    } 

    $pdf->Output();
?>

