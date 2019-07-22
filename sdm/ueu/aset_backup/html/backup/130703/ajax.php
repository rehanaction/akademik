<?php
	// cek akses halaman
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	$conn->debug = false;
	
	// require tambahan
	require_once(Route::getUIPath('combo'));
	
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
		
		$sql = "select top 20 idbarang,namabarang, idbarang+' - '+namabarang as barang 
		    from aset.ms_barang 
		    where level = 5 and substring(idbarang,1,1) != '1' and lower(idbarang+' '+namabarang) like '%$q%' 
		    order by idbarang";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
    else if($f == 'acxbaranginv') {
		require_once(Route::getModelPath('barang'));
		$filter = "level = 5 and substring(idbarang,1,1) != '1'";
		
		$a_data = mBarang::find($conn,$q,"(idbarang+' - '+namabarang)",'idbarang','aset.ms_barang','idbarang',$filter);
		
		echo json_encode($a_data);
	}	
	else if($f == 'acbarangseri') {
		$q = strtolower($q);
		
		$sql = "select top 20 s.idbarang,b.namabarang,s.idbarang+' '+b.namabarang as barang 
		    from aset.as_seri s 
		    left join aset.ms_barang b on b.idbarang = s.idbarang 
		    where lower(s.idbarang+' '+b.namabarang) like '%$q%' 
		    order by s.idbarang";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
    else if($f == 'acseri') {
		$q = strtolower($q);
		$r_idunit = $_REQUEST['idunit'];
		
		$sql = "select top 20 s.idseri,s.idbarang,b.namabarang,s.merk,s.spesifikasi,
		    right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		    s.idbarang+'.'+right('000000'+convert(varchar(6), s.noseri), 6) as barangseri 
		    from aset.as_seri s
		    left join aset.ms_barang b on b.idbarang = s.idbarang 
		    where ";
	    if(!empty($r_idunit))
	        $sql .= "s.idunit = '$r_idunit' and "; 
	    $sql .= "lower(s.idbarang+'.'+right('000000'+convert(varchar(6), s.noseri), 6)+' - '+b.namabarang) like '%$q%' 
		    order by s.idbarang,s.noseri";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
    else if($f == 'acnoseri') {
		$q = strtolower($q);
		$r_idunit = $_REQUEST['idunit'];
		$r_idbarang = $_REQUEST['idbarang'];
		
		$sql = "select top 20 s.idseri,s.idbarang,s.merk,s.spesifikasi,s.tglperolehan,s.tglgaransi,
		    right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		    s.idbarang+'.'+right('000000'+convert(varchar(6), s.noseri), 6) as barang 
		    from aset.as_seri s left join aset.ms_barang b on b.idbarang = s.idbarang 
		    where s.idunit = '$r_idunit' and "; 
	    if(!empty($r_idbarang))
	        $sql .= "s.idbarang = '$r_idbarang' and "; 
	    $sql .= "lower(right('000000'+convert(varchar(6), s.noseri), 6)) like '%$q%' 
		    order by s.idbarang,s.noseri";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acbaranghp') {
		$q = strtolower($q);
		
		$sql = "select top 20 idbarang,namabarang,idsatuan,idbarang+' '+namabarang as barang 
		    from aset.ms_barang 
		    where level = 6 and substring(idbarang,1,1) = '1' and idsatuan is not null 
		    and lower(idbarang+' '+namabarang) like '%$q%' 
		    order by idbarang";
        $res = $conn->GetArray($sql);

		echo json_encode($res);
	}
    else if($f == 'acxbaranghp') {
		require_once(Route::getModelPath('barang'));
		$filter = "level = 6 and substring(idbarang,1,1) = '1' and idsatuan is not null";
		
		$a_data = mBarang::find($conn,$q,"(idbarang+' - '+namabarang)",'idbarang','aset.ms_barang','idbarang',$filter);
		
		echo json_encode($a_data);
	}	
    else if($f == 'acxbarangunit') {
        $r_idunit = $_REQUEST['idunit'];
        $r_idlokasi = $_REQUEST['idlokasi'];

		$sql  = "select s.idbarang, (s.idbarang+' - '+b.namabarang) as label 
		    from aset.as_seri s left join aset.ms_barang b on b.idbarang = s.idbarang 
		    where s.idunit = '$r_idunit' ";
	    if(!empty($r_idlokasi)) 
	        $sql .= "and s.idlokasi = '$r_idlokasi' ";
		$sql .= "and lower(cast((s.idbarang+' - '+b.namabarang) as varchar)) like '%$q%' 
		    group by s.idbarang,b.namabarang order by s.idbarang";
		$rs = $conn->SelectLimit($sql,20);
		
		$a_data = array();
		while($row = $rs->FetchRow())
			$a_data[] = array('key' => $row['idbarang'], 'label' => $row['label']);

		echo json_encode($a_data);
	}	
	else if($f == 'getkonvsatuan'){
	    $r_idbarang = $_REQUEST['idbarang'];
	    
	    echo $r_idbarang;
	}
	else if($f == 'acunit') {
		$q = strtolower($q);
		
		$sql = "select top 20 idunit,kodeunit,namaunit,kodeunit+' - '+namaunit as unit 
		    from aset.ms_unit 
		    where lower(kodeunit+' - '+namaunit) like '%$q%' 
		    order by kodeunit";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acxunit') {
		require_once(Route::getModelPath('unit'));
		
		$a_data = mUnit::find($conn,$q,"(kodeunit+' - '+namaunit)",'idunit','','kodeunit');		
		echo json_encode($a_data);
	}
	else if($f == 'aclokasi') {
		$q = strtolower($q);
		
		$sql = "select top 20 idlokasi,namalokasi,idlokasi+' - '+namalokasi as lokasi 
		    from aset.ms_lokasi 
		    where lower(idlokasi+' - '+namalokasi) like '%$q%' 
		    order by idlokasi";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acxlokasi') {
		require_once(Route::getModelPath('lokasi'));
        $r_idunit = $_REQUEST['idunit'];
		if(!empty($r_idunit))
		    $filter = "idunit = '$r_idunit' ";
		
		$a_data = mLokasi::find($conn,$q,"(idlokasi+' - '+namalokasi)",'idlokasi','','',$filter);
		
		echo json_encode($a_data);
	}
	else if($f == 'optlokasi') {
		require_once(Route::getModelPath('lokasi'));

        $r_idunit = $_POST['idunit'];
        $r_idlokasi = $_POST['idlokasi'];
        $r_isempty = $_POST['isempty'];
        $r_emptylabel = $_POST['emptylabel'];

		if(empty($r_idunit) or $r_idunit == 'null') {
			$a_lokasi = array();
			echo UI::createOption($a_lokasi,'',true,'-- Pilih unit dahulu --');
		}
		else {
			$a_lokasi = mLokasi::lokasi($conn,$r_idunit);
			if(empty($a_lokasi)){
			    $a_lokasi[''] = '-- Lokasi tidak ditemukan --';
    			echo UI::createOption($a_lokasi,$r_idlokasi);
		    }else
    			echo UI::createOption($a_lokasi,$r_idlokasi,$r_isempty,$r_emptylabel);
		}
	}
	else if($f == 'optpemakai') {
		require_once(Route::getModelPath('seri'));

        $r_idunit = $_POST['idunit'];
        $r_idpemakai = $_POST['idpemakai'];
        $r_isempty = $_POST['isempty'];
        if($r_isempty)
            $r_emptylabel = '-- Pilih pemakai --';

		if(empty($r_idunit) or $r_idunit == 'null') {
			$a_pemakai = array();
			echo UI::createOption($a_pemakai,'',true,'-- Pilih unit dahulu --');
		}
		else {
			$a_pemakai = mSeri::pemakai($conn,$r_idunit);
			if(empty($a_pemakai)){
			    $a_pemakai[''] = '-- Pemakai tidak ditemukan --';
    			echo UI::createOption($a_pemakai,$r_idpemakai);
		    }else
    			echo UI::createOption($a_pemakai,$r_idpemakai,$r_isempty,$r_emptylabel);
		}
	}
	else if($f == 'acpegawai') {
		$q = strtolower($q);
		
		$sql = "select top 20 idpegawai,nip,namalengkap,nip+' - '+namalengkap as pegawai 
		    from sdm.v_biodatapegawai 
		    where lower(nip+' - '+namalengkap) like '%$q%' 
		    order by nip";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acxpegawai') {
		require_once(Route::getModelPath('pegawai'));
		//$conn->debug = true;
		$r_idunit = $_REQUEST['idunit'];
		if(!empty($r_idunit))
		    $filter = "idunit = '$r_idunit' ";
		
		$a_data = mPegawai::find($conn,$q,"(nip+' - '+namalengkap)",'idpegawai','sdm.v_biodatapegawai','nip',$filter);
		
		echo json_encode($a_data);
	}
	else if($f == 'acsupplier') {
		$q = strtolower($q);
		
		$sql = "select top 20 idsupplier,namasupplier 
		    from aset.ms_supplier
		    where lower(namasupplier) like '%$q%' 
		    order by namasupplier";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	else if($f == 'acxsupplier') {
		require_once(Route::getModelPath('supplier'));
		
		$a_data = mSupplier::find($conn,$q,"namasupplier",'idsupplier','aset.ms_supplier','namasupplier');
		
		echo json_encode($a_data);
	}
	else if($f == 'cekseribarang'){
		$r_idunit = $_POST['idunit'];
		$r_idbarang = $_POST['idbarang'];
		$r_noseri = (int)$_POST['noseri'];
		
        echo $conn->GetOne("select idseri from aset.as_seri where idunit = '$r_idunit' and idbarang = '$r_idbarang' and noseri = '$r_noseri'");			
	}
	else if($f == 'cekserisd'){
		$r_idbarang = $_POST['idbarang'];
		$r_fnoseri = (int)$_POST['fnoseri'];
		$r_enoseri = (int)$_POST['enoseri'];
		
        echo (int)$conn->GetOne("select 1 from aset.as_seri where idbarang = '$r_idbarang' and noseri between '$r_fnoseri' and '$r_enoseri'");			
	}
	else if($f == 'treebarang'){  //getBarangTreeOld
        $id = $_REQUEST['id'];
        $level = $_REQUEST['level'];
        		
		if(empty($id)){
			$sql = "select idbarang,namabarang,level from aset.ms_barang where level = 1 order by idbarang";
		}else{
			$l = Aset::getLengthBrg($level);
			$cid = substr($id,0,$l);
			$clevel = $level+1;
			$cl = Aset::getLengthBrg($clevel);

			$sql = "select b.idbarang,b.namabarang,b.level,count(c.idbarang) as nchild 
				from aset.ms_barang b join aset.ms_barang c on substring(b.idbarang,1,$cl) = substring(c.idbarang,1,$cl) 
				where b.level = $clevel and b.idbarang like '$cid%' 
				group by b.idbarang,b.namabarang,b.level order by b.idbarang";
		}
		
		$data = array();
		$rs = $conn->Execute($sql);
		while($row = $rs->FetchRow()){
			unset($child);
			$child['attr'] = array(
			                    'id'=>$row['idbarang'],
			                    'level'=>$row['level'],
			                    'namabarang'=>$row['namabarang'],
			                    'idbaru'=>$row['idbaru'],
			                    'nchild'=>$row['nchild']
			                );
			//$child['data'] = $row['idbarang'].' ('.$row['nchild'].') - '.$row['namabarang'];
			$child['data'] = $row['idbarang'].' - '.$row['namabarang'];
			$child['state'] = ($row['nchild'] == '1') ? '' : 'closed';

			$data[] = $child;
		}
		
		echo json_encode($data);
	}
	else if($f == 'treebarangtmp'){  //getBarangTreeNew
        $id = $_REQUEST['id'];
        $level = $_REQUEST['level'];
		
		if(empty($id)){
			$sql = "select idbarang,namabarang,level from aset.ms_barangtmp where level = 1 order by idbarang";
		}else{
			$l = Aset::getLengthBrg($level);
			$cid = substr($id,0,$l);
			$clevel = $level+1;
			$cl = Aset::getLengthBrg($clevel);

			$sql = "select b.idbarang,b.namabarang,b.level,count(c.idbarang) as nchild 
				from aset.ms_barangtmp b join aset.ms_barangtmp c on substring(b.idbarang,1,$cl) = substring(c.idbarang,1,$cl) 
				where b.level = $clevel and b.idbarang like '$cid%' 
				group by b.idbarang,b.namabarang,b.level order by b.idbarang";
		}
		
		$data = array();
		$rs = $conn->Execute($sql);
		while($row = $rs->FetchRow()){
			unset($child);
			$child['attr'] = array(
			                    'id'=>$row['idbarang'],
			                    'level'=>$row['level'],
			                    'namabarang'=>$row['namabarang'],
			                    'idlama'=>$row['idlama'],
			                    'nchild'=>$row['nchild']
			                );
			$child['data'] = $row['idbarang'].' ('.$row['nchild'].') - '.$row['namabarang'];
			$child['state'] = ($row['nchild'] == '1') ? '' : 'closed';

			$data[] = $child;
		}
		
		echo json_encode($data);
	}
	else if($f == 'ayohapus'){
		$id = $_POST['idbarang'];
		$level = $_POST['level'];
	    
	    // 1 00 00 00 000 000000
	    /*
	    if($level == 6){
	        $idparent = substr($id,0,10);
	        
	        $sqld = "delete from a_barang where idbarang like '".substr($id,0,16)."%'";
	        $sqlu = "update a_barang set 
	            idbarang = substring(idbarang,1,10)||
	            lpad((substring(idbarang,11,6)::int-1)::character varying,6,'0') 
                where substring(idbarang,1,10) = '$idparent' and idbarang > '$id'";
	    }else 
	    */
	    if($level == 5){
	        $idparent = substr($id,0,7);
	        
	        $sqld = "delete from a_barang where idbarang like '".substr($id,0,10)."%'";
	        $sqlu = "update a_barang set 
	            idbarang = substring(idbarang,1,7)+
	            lpad((substring(idbarang,8,3)::int-1)::character varying,3,'0')+
	            right('000'+convert(varchar(3), cast(substring(idbarang,8,3) as int)-1), 3)
	            substring(idbarang,11,length(idbarang))
	            where substring(idbarang,1,7) = '$idparent' and idbarang > '$id'";
	    }else if($level == 4){
	        $idparent = substr($id,0,5);
	        
	        $sqld = "delete from a_barang where idbarang like '".substr($id,0,7)."%'";
	        $sqlu = "update a_barang set 
	            idbarang = substring(idbarang,1,5)+
	            lpad((substring(idbarang,6,2)::int-1)::character varying,2,'0')+
	            substring(idbarang,8,length(idbarang))
	            where substring(idbarang,1,5) = '$idparent' and idbarang > '$id'";
	    }
	    /*
	    else if($level == 3){
	        $idparent = substr($id,0,3);
	        
	        $sqld = "delete from a_barang where idbarang like '".substr($id,0,5)."%'";
	        $sqlu = "update a_barang set 
	            idbarang = substring(idbarang,1,3)||
	            lpad((substring(idbarang,4,2)::int-1)::character varying,2,'0')||
	            substring(idbarang,6,length(idbarang)) 
	            where substring(idbarang,1,3) = '$idparent' and idbarang > '$id'";
	    }else if($level == 2){
	        $idparent = substr($id,0,1);
	        
	        $sqld = "delete from a_barang where idbarang like '".substr($id,0,3)."%'";
	        $sqlu = "update a_barang set 
	            idbarang = substring(idbarang,1,1)||
	            lpad((substring(idbarang,2,2)::int-1)::character varying,2,'0')||
	            substring(idbarang,4,length(idbarang)) 
                where substring(idbarang,1,1) = '$idparent' and idbarang > '$id'";
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
