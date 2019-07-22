<?php
require_once($conf['helpers_dir'].'label.class.php');

$conn->debug = false;

$r_idseri = CStr::removeSpecial($_REQUEST['idseri']);
//$r_idseri = '29';

if(!empty($r_idseri)){
    $sql = "select s.noseri,s.idbarang1,b.namabarang,sd.sumberdana,p.tglperolehan,s.idlokasi
        from aset.as_seri s 
        left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
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
