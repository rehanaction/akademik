<?php
require_once('../helpers/label.class.php');

$conn->debug = false;

$r_idseri = CStr::removeSpecial($_REQUEST['idseri']);
//$r_idseri = '29';

if(!empty($r_idseri)){
    $sql = "select s.noseri,s.idbarang,b.namabarang,sd.sumberdana,p.tglperolehan 
        from aset.as_seri s 
        left join aset.ms_barang b on b.idbarang = s.idbarang 
        left join aset.as_perolehandetail d on d.iddetperolehan = s.iddetperolehan 
        left join aset.as_perolehan p on p.idperolehan = d.idperolehan 
        left join aset.ms_sumberdana sd on sd.idsumberdana = p.idsumberdana 
        where s.idseri = '$r_idseri'";

    $row = $conn->GetRow($sql);

    if(count($row) > 0){
        $row['noseri'] = Aset::setFormatNoSeri($row['noseri']);
        $row['tglperolehan'] = CStr::formatDateInd($row['tglperolehan'],false);

        Label::cetak($row);
    }
}
?>
