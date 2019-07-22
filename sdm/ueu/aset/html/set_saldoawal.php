<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	//ini_set('max_execution_time',10000);
	// hak akses
    $conn->debug = true;

/*
    //$now = date('Y-m-d');
	$now = '2012-12-31';
	$conn->BeginTrans();
	
	$i = 1;
	$j = 237;

	$sql = "select * from aset.aaa_saldoawal order by idsaldoawal";

	$rs = $conn->Execute($sql);
	while($row = $rs->FetchRow()) {
	    //perolehan
	    $idunit = $row['idunit'];
	    $idbarang1 = $row['idbarang1'];
	    $idlokasi = $row['idlokasi'];
	    $idpegawai = CStr::cStrNull($row['idpegawai']);
	    $jml = 1;//(int)$row['jml'];

	    $idsaldoawal = $row['idsaldoawal'];
	    
	    $sqlp = "insert into aset.as_perolehan (idunit,idjenisperolehan,idbarang1,idsaldoawal) values 
	        ($idunit,'100','$idbarang1',$idsaldoawal);";
        $ok = $conn->Execute($sqlp);
        $idp = $conn->Insert_ID();

	    //perolehan detail
        if($ok){
            $sqld = "insert into aset.as_perolehandetail (idperolehan,idlokasi,idpegawai,qty) values 
                ('$idp','$idlokasi',$idpegawai,'$jml');";
            $ok = $conn->Execute($sqld);
            $idd = $conn->Insert_ID();
            
            //seri
            if($ok){
		        $sqls = '';
		        
		        $maxSeri = (int)$conn->GetOne("select max(noseri) from aset.as_seri where idbarang1 = '$idbarang1'");
		        //for($i=1;$i<=$jml;$i++){
		            //$noseri = $maxSeri+$i;
		            $noseri = $maxSeri+1;
		            $sqls .= "insert into aset.as_seri (idbarang1,noseri,iddetperolehan,idunit,idsaldoawal) values 
		            ('$idbarang1',$noseri,'$idd','$idunit',$idsaldoawal);";
		        //}
		        $ok = $conn->Execute($sqls);
            }
        }
        
        if(!$ok){ 
            break;
            echo 'break';
        }
	}
*/
	/*
    if($ok){
        $sql = "update p set
            idjenisperolehan = '100',
            merk = a.merk,
            spesifikasi = a.spesifikasi,
            tglperolehan = '2012-12-31',
            tglpembukuan = '2012-12-31',
            idkondisi = a.idkondisi,
            qty = a.jml,
            harga = 1,
            total = a.jml,
            catatan = null,
            doc = a.doc+'/'+a.sheet,
            isverify = 1 
        from aset.as_perolehan p 
        join aset.aa_saldoawal a on a.idsaldoawal = p.idsaldoawal 
        where p.idsaldoawal = a.idsaldoawal";
        
        $ok = $conn->Execute($sql);
    }

    if($ok){
        $sql = "update s set 
            idlokasi = a.idlokasi,
            idpegawai = a.idpegawai,
            merk = a.merk,
            spesifikasi = a.spesifikasi,
            tglperolehan = '2012-12-31',
            idkondisi = a.idkondisi,
            idstatus = 'A',
            nilaiawal = 1,
            nilaiaset = 1, 
            catatan = null 
        from aset.as_seri s 
        join aset.aa_saldoawal a on a.idsaldoawal = s.idsaldoawal 
        where s.idsaldoawal = a.idsaldoawal";
        
        $ok = $conn->Execute($sql);
    }
*/	

/*
    //$ok = false;
    if($ok)
        $conn->CommitTrans();
    else
        $conn->RollbackTrans();
*/       
/*------------------------------------------------------------------------------------------------------------*/
/*
    $sql = "update p set
        idjenisperolehan = '100',
        merk = a.merk,
        spesifikasi = a.spesifikasi,
        tglperolehan = '2012-12-31',
        tglpembukuan = '2012-12-31',
        idkondisi = a.idkondisi,
        qty = a.jml,
        harga = 1,
        total = a.jml,
        catatan = 'migrasi file :'+a.doc 
    from aset.as_perolehan p 
    join aset.aa_saldoawal a on a.idsaldoawal = p.idsaldoawal 
    where p.idsaldoawal = a.idsaldoawal and s.idsaldoawal between $i and $j";


    $sql = "update s set 
        idlokasi = a.idlokasi,
        idpegawai = a.idpegawai,
        merk = a.merk,
        spesifikasi = a.spesifikasi,
        tglperolehan = '2012-12-31',
        idkondisi = a.idkondisi,
        idstatus = 'A',
        nilaiawal = 1,
        nilaiaset = 1 
    from aset.as_seri s 
    join aset.aa_saldoawal a on a.idsaldoawal = s.idsaldoawal 
    where s.idsaldoawal = a.idsaldoawal and s.idsaldoawal between $i and $j";



set identity_insert aset.aa_saldoawal on;
update aset.aa_saldoawal set idsaldoawal = idsaldoawal-220;
set identity_insert aset.aa_saldoawal off;

insert into aset.ms_merk
select merk from aset.aa_saldoawal 
where merk != '' and merk not in (select merk from aset.ms_merk)
group by merk




//perolehan
dbcc checkident ('ueudb.aset.as_perolehan', reseed, 5000)


--insert into aset.as_perolehan (idunit,idjenisperolehan,tglperolehan,tglpembukuan,idsumberdana,nobukti,tglbukti,nospk,tglspk,nopo,tglpo,nosk,tglsk,
idsupplier,catatan,insertuser,inserttime,isverify,verifyuser,verifytime,idbarang1,idsatuan,qty,harga,total,iddasarharga,idkondisi,
tglgaransi,kmgaransi,thnprod,merk,ukuran,spesifikasi,idcoa,status,void,
t_insertuser,t_insertip,t_inserttime,t_updateuser,t_updateip,t_updatetime,idsaldoawal,doc) 
select idunit,idjenisperolehan,tglperolehan,tglpembukuan,idsumberdana,nobukti,tglbukti,nospk,tglspk,nopo,tglpo,nosk,tglsk,
idsupplier,catatan,insertuser,inserttime,isverify,verifyuser,verifytime,idbarang1,idsatuan,qty,harga,total,iddasarharga,idkondisi,
tglgaransi,kmgaransi,thnprod,merk,ukuran,spesifikasi,idcoa,status,void,
t_insertuser,t_insertip,t_inserttime,t_updateuser,t_updateip,t_updatetime,idsaldoawal,doc
from aset.as_perolehan order by idperolehan


//detail
dbcc checkident ('ueudb.aset.as_perolehandetaildetail', reseed, 5000)


--insert into aset.as_perolehandetail (idperolehan,idlokasi,idpegawai,qty,t_insertuser,t_insertip,t_inserttime,t_updateuser,t_updateip,t_updatetime) 
select idperolehan+4995,idlokasi,idpegawai,qty,t_insertuser,t_insertip,t_inserttime,t_updateuser,t_updateip,t_updatetime 
from aset.as_perolehandetail order by iddetperolehan



--dbcc checkident ('ueudb.aset.as_perolehan', reseed, 0)
--dbcc checkident ('ueudb.aset.as_perolehandetail', reseed, 0)
--dbcc checkident ('ueudb.aset.as_seri', reseed, 0)

--delete from aset.as_seri where idsaldoawal is not null;
--delete from aset.as_perolehandetail where idperolehan < 5000;
--delete from aset.as_perolehan where idperolehan < 5000;

--select count(*) from aset.as_seri where idsaldoawal is not null;
--select count(*) from aset.as_perolehandetail where idperolehan < 5000;
--select count(*) from aset.as_perolehan where idperolehan < 5000;


//seri



http://www.howtogeek.com/howto/database/reset-identity-column-value-in-sql-server/
*/
/*
$sql = "select * from aset.aa_saldoawal order by idsaldoawal";

$rs = $conn->Execute($sql);
$i = 0;
while($row = $rs->FetchRow()) {
    $sql = '';
    for($j=0; $j<(int)$row['jml']; $j++){
        $i++;
        $sql .= "insert into aset.aaa_saldoawal values 
            ($i,{$row['idunit']},'{$row['idlokasi']}',{$row['idpegawai']},'{$row['idbarang1']}',1,'{$row['merk']}',
            '{$row['spesifikasi']}','{$row['idkondisi']}','{$row['doc']}','{$row['sheet']}',{$row['idsaldoawal']});";
    }
    //echo $sql.'<br>';
    $ok = $conn->Execute($sql);
    if(!$ok) break;
}
*/
?>

