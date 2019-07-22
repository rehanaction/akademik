<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$conn->debug = false;
	
	// require tambahan
	//require_once(Route::getUIPath('combo'));
	
	// variabel reuqest
	$f = $_REQUEST['f'];
	$q = $_REQUEST['q'];
	
	// filtering
	if(is_array($q)) {
		for($i=0;$i<count($q);$i++)
			$q[$i] = CStr::removeSpecial($q[$i]);
	}
	else
		$q = CStr::removeSpecial($q);
	
	// option jurusan
	if($f == 'acbaranginv') {
		$q = strtolower($q);
		
		$sql = "select top 20 idbarang1,namabarang, idbarang1+' - '+namabarang as barang 
		    from aset.ms_barang1 
		    where level = 5 and substring(idbarang1,1,1) != '1' and lower(idbarang1+' '+namabarang) like '%$q%' 
		    order by idbarang1";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
    else if($f == 'acxbaranginv') {
		require_once(Route::getModelPath('barang'));
		$filter = "level = 5 and substring(idbarang1,1,1) != '1'";
		
		$a_data = mBarang::find($conn,$q,"(idbarang1+' - '+namabarang)",'idbarang1','aset.ms_barang1','idbarang1',$filter);
		
		echo json_encode($a_data);
	}	
	else if($f == 'acbarangseri') {
		$q = strtolower($q);
		
		$sql = "select top 20 s.idbarang1,b.namabarang,s.idbarang1+' '+b.namabarang as barang 
		    from aset.as_seri s 
		    left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		    where lower(s.idbarang1+' '+b.namabarang) like '%$q%' 
		    order by s.idbarang1";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
    else if($f == 'acseri') {
		$q = strtolower($q);
		
		$sql = "select top 20 s.idseri,s.idbarang1,b.namabarang,s.merk,s.spesifikasi,
		    right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		    s.idbarang1+'.'+right('000000'+convert(varchar(6), s.noseri), 6) as barang 
		    from aset.as_seri s
		    left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		    where lower(s.idbarang1+'.'+right('000000'+convert(varchar(6), s.noseri), 6)) like '%$q%' 
		    order by s.idbarang1,s.noseri";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acbaranghp') {
		$q = strtolower($q);
		
		$sql = "select top 20 idbarang1,namabarang,idsatuan,idbarang1+' '+namabarang as barang 
		    from aset.ms_barang1 
		    where level = 6 and substring(idbarang1,1,1) = '1' and idsatuan is not null 
		    and lower(idbarang1+' '+namabarang) like '%$q%' 
		    order by idbarang1";
        $res = $conn->GetArray($sql);

		echo json_encode($res);
	}
    else if($f == 'acxbaranghp') {
		require_once(Route::getModelPath('barang'));
		$filter = "level = 6 and substring(idbarang1,1,1) = '1' and idsatuan is not null";
		
		$a_data = mBarang::find($conn,$q,"(idbarang1+' - '+namabarang)",'idbarang1','aset.ms_barang1','idbarang1',$filter);
		
		echo json_encode($a_data);
	}	
	else if($f == 'getkonvsatuan'){
	    $r_idbarang = $_REQUEST['idbarang1'];
	    
	    echo $r_idbarang;
	}
	else if($f == 'acunit') {
		$q = strtolower($q);
		
		$sql = "select top 20 idunit,kodeunit,namaunit,kodeunit+' - '+namaunit as unit 
		    from aset.ms_unit 
		    where kodeunit+' - '+namaunit like '%$q%' 
		    order by kodeunit";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acxunit') {
		require_once(Route::getModelPath('unit'));
		
		$a_data = mUnit::find($conn,$q,"(kodeunit+' - '+namaunit)",'idunit');
		
		echo json_encode($a_data);
	}
	else if($f == 'aclokasi') {
		$q = strtolower($q);
		
		$sql = "select top 20 idlokasi,namalokasi,idlokasi+' - '+namalokasi as lokasi 
		    from aset.ms_lokasi 
		    where idlokasi+' - '+namalokasi like '%$q%' 
		    order by idlokasi";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acxlokasi') {
		require_once(Route::getModelPath('lokasi'));
		
		$a_data = mLokasi::find($conn,$q,"(idlokasi+' - '+namalokasi)",'idlokasi');
		
		echo json_encode($a_data);
	}
	else if($f == 'acpegawai') {
		$q = strtolower($q);
		
		$sql = "select top 20 idpegawai,nip,namalengkap,nip+' - '+namalengkap as pegawai 
		    from sdm.v_biodatapegawai 
		    where nip+' - '+namalengkap like '%$q%' 
		    order by nip";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acxpegawai') {
		require_once(Route::getModelPath('pegawai'));
		
		$a_data = mPegawai::find($conn,$q,"(nip+' - '+namalengkap)",'idpegawai','sdm.v_biodatapegawai','nip');
		
		echo json_encode($a_data);
	}
	else if($f == 'acsupplier') {
		require_once(Route::getModelPath('supplier'));
		
		$a_data = mSupplier::find($conn,$q,"namasupplier",'idsupplier','aset.ms_supplier','namasupplier');
		
		echo json_encode($a_data);
	}
	else if($f == 'acxsupplier') {
		require_once(Route::getModelPath('supplier'));
		
		$a_data = mSupplier::find($conn,$q,"namasupplier",'idsupplier','aset.ms_supplier','namasupplier');
		
		echo json_encode($a_data);
	}
	else if($f == 'treebarang'){  //getBarangTreeOld
        $id = $_REQUEST['id'];
        $level = $_REQUEST['level'];
        		
		if(empty($id)){
			$sql = "select idbarang1,namabarang,level from aset.ms_barang1 where level = 1 order by idbarang1";
		}else{
			$l = Aset::getLengthBrg($level);
			$cid = substr($id,0,$l);
			$clevel = $level+1;
			$cl = Aset::getLengthBrg($clevel);

			$sql = "select b.idbarang1,b.namabarang,b.level,count(c.idbarang1) as nchild 
				from aset.ms_barang1 b join aset.ms_barang1 c on substring(b.idbarang1,1,$cl) = substring(c.idbarang1,1,$cl) 
				where b.level = $clevel and b.idbarang1 like '$cid%' 
				group by b.idbarang1,b.namabarang,b.level order by b.idbarang1";
		}
		
		$data = array();
		$rs = $conn->Execute($sql);
		while($row = $rs->FetchRow()){
			unset($child);
			$child['attr'] = array(
			                    'id'=>$row['idbarang1'],
			                    'level'=>$row['level'],
			                    'namabarang'=>$row['namabarang'],
			                    'idbaru'=>$row['idbaru'],
			                    'nchild'=>$row['nchild']
			                );
			$child['data'] = $row['idbarang1'].' ('.$row['nchild'].') - '.$row['namabarang'];
			//$child['data'] = $row['idbarang1'].' - '.$row['namabarang'];
			$child['state'] = ($row['nchild'] == '1') ? '' : 'closed';

			$data[] = $child;
		}
		
		echo json_encode($data);
	}
	else if($f == 'treebarangtmp'){  //getBarangTreeNew
        $id = $_REQUEST['id'];
        $level = $_REQUEST['level'];
		
		if(empty($id)){
			$sql = "select idbarang1,namabarang,level from aset.ms_barangtmp where level = 1 order by idbarang1";
		}else{
			$l = Aset::getLengthBrg($level);
			$cid = substr($id,0,$l);
			$clevel = $level+1;
			$cl = Aset::getLengthBrg($clevel);

			$sql = "select b.idbarang1,b.namabarang,b.level,count(c.idbarang1) as nchild 
				from aset.ms_barangtmp b join aset.ms_barangtmp c on substring(b.idbarang1,1,$cl) = substring(c.idbarang1,1,$cl) 
				where b.level = $clevel and b.idbarang1 like '$cid%' 
				group by b.idbarang1,b.namabarang,b.level order by b.idbarang1";
		}
		
		$data = array();
		$rs = $conn->Execute($sql);
		while($row = $rs->FetchRow()){
			unset($child);
			$child['attr'] = array(
			                    'id'=>$row['idbarang1'],
			                    'level'=>$row['level'],
			                    'namabarang'=>$row['namabarang'],
			                    'idlama'=>$row['idlama'],
			                    'nchild'=>$row['nchild']
			                );
			$child['data'] = $row['idbarang1'].' ('.$row['nchild'].') - '.$row['namabarang'];
			$child['state'] = ($row['nchild'] == '1') ? '' : 'closed';

			$data[] = $child;
		}
		
		echo json_encode($data);
	}
	else if($f == 'ayohapus'){
		$id = $_POST['idbarang1'];
		$level = $_POST['level'];
	    
	    // 1 00 00 00 000 000000
	    /*
	    if($level == 6){
	        $idparent = substr($id,0,10);
	        
	        $sqld = "delete from a_barang where idbarang1 like '".substr($id,0,16)."%'";
	        $sqlu = "update a_barang set 
	            idbarang1 = substring(idbarang1,1,10)||
	            lpad((substring(idbarang1,11,6)::int-1)::character varying,6,'0') 
                where substring(idbarang1,1,10) = '$idparent' and idbarang1 > '$id'";
	    }else 
	    */
	    if($level == 5){
	        $idparent = substr($id,0,7);
	        
	        $sqld = "delete from a_barang where idbarang1 like '".substr($id,0,10)."%'";
	        $sqlu = "update a_barang set 
	            idbarang1 = substring(idbarang1,1,7)+
	            lpad((substring(idbarang1,8,3)::int-1)::character varying,3,'0')+
	            right('000'+convert(varchar(3), cast(substring(idbarang1,8,3) as int)-1), 3)
	            substring(idbarang1,11,length(idbarang1))
	            where substring(idbarang1,1,7) = '$idparent' and idbarang1 > '$id'";
	    }else if($level == 4){
	        $idparent = substr($id,0,5);
	        
	        $sqld = "delete from a_barang where idbarang1 like '".substr($id,0,7)."%'";
	        $sqlu = "update a_barang set 
	            idbarang1 = substring(idbarang1,1,5)+
	            lpad((substring(idbarang1,6,2)::int-1)::character varying,2,'0')+
	            substring(idbarang1,8,length(idbarang1))
	            where substring(idbarang1,1,5) = '$idparent' and idbarang1 > '$id'";
	    }
	    /*
	    else if($level == 3){
	        $idparent = substr($id,0,3);
	        
	        $sqld = "delete from a_barang where idbarang1 like '".substr($id,0,5)."%'";
	        $sqlu = "update a_barang set 
	            idbarang1 = substring(idbarang1,1,3)||
	            lpad((substring(idbarang1,4,2)::int-1)::character varying,2,'0')||
	            substring(idbarang1,6,length(idbarang1)) 
	            where substring(idbarang1,1,3) = '$idparent' and idbarang1 > '$id'";
	    }else if($level == 2){
	        $idparent = substr($id,0,1);
	        
	        $sqld = "delete from a_barang where idbarang1 like '".substr($id,0,3)."%'";
	        $sqlu = "update a_barang set 
	            idbarang1 = substring(idbarang1,1,1)||
	            lpad((substring(idbarang1,2,2)::int-1)::character varying,2,'0')||
	            substring(idbarang1,4,length(idbarang1)) 
                where substring(idbarang1,1,1) = '$idparent' and idbarang1 > '$id'";
	    }
	    */
	    
	    //global $conn;
        //$conn->debug = true;
	    $conn->StartTrans();
	    
	    $conn->Execute($sqld);
	    $err = $conn->ErrorNo();
	    
	    if(!$err){
	        $conn->Execute($sqlu);
	        $err = $conn->ErrorNo();
	    }
        $conn->CompleteTrans();
	    
	    //$err = 1;
	    echo json_encode(array('err' => $err));
	}
?>
