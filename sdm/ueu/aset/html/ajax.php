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
		
		$sql = "select top 20 idbarang1,namabarang, idbarang1+' - '+namabarang as barang 
		    from aset.ms_barang1 
		    where level = 5 and substring(idbarang1,1,1) != '1' and lower(idbarang1+' '+namabarang) like '%$q%' 
		    order by idbarang1";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
    else if($f == 'acxbaranginv') {
		require_once(Route::getModelPath('barang'));
		$filter = "level = 6 and substring(idbarang1,1,1) != '1'";
		
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
		
		$r_idunit = $_REQUEST['idunit'];
		$r_idlokasi = $_REQUEST['idlokasi'];
		$r_idpegawai = $_REQUEST['idpegawai'];
		
		$sql = "select top 20 s.idseri,s.idbarang1,b.namabarang,s.merk,s.spesifikasi,
		    right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		    s.idbarang1+'.'+right('000000'+convert(varchar(6), s.noseri), 6) as barangseri 
		    from aset.as_seri s
		    left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		    where (1=1) ";
	    if(!empty($r_idunit))
	        $sql .= "and s.idunit = '$r_idunit' ";
	    if(!empty($r_idlokasi))
	        $sql .= "and s.idlokasi = '$r_idlokasi' ";
	    if(!empty($r_idpegawai))
	        $sql .= "and s.idpegawai = '$r_idpegawai' ";
	    $sql .= "and lower(s.idbarang1+'.'+right('000000'+convert(varchar(6), s.noseri), 6)+' - '+b.namabarang) like '%$q%' 
		    order by s.idbarang1,s.noseri";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
    else if($f == 'acnoseri') {
		$q = strtolower($q);
		$r_idunit = $_REQUEST['idunit'];
		$r_idbarang = $_REQUEST['idbarang1'];
		
		$sql = "select top 20 s.idseri,s.idbarang1,s.merk,s.spesifikasi,s.tglperolehan,s.tglgaransi,
		    right('000000'+convert(varchar(6), s.noseri), 6) as noseri,
		    s.idbarang1+'.'+right('000000'+convert(varchar(6), s.noseri), 6) as barang 
		    from aset.as_seri s left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		    where s.idunit = '$r_idunit' and "; 
	    if(!empty($r_idbarang))
	        $sql .= "s.idbarang1 = '$r_idbarang' and "; 
	    $sql .= "lower(right('000000'+convert(varchar(6), s.noseri), 6)) like '%$q%' 
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
	else if($f == 'acstockhp') {
		$q = strtolower($q);
		//$conn->debug = true;
		
		$sql = "select top 20 b.idbarang1,b.namabarang,b.idsatuan,b.idbarang1+' '+b.namabarang as barang 
		    from aset.ms_barang1 b join aset.as_stockhp s on s.idbarang1 = b.idbarang1 
		    where s.jmlstock > 0 and b.idsatuan is not null 
		    and lower(b.idbarang1+' '+b.namabarang) like '%$q%' 
		    order by b.idbarang1";
        $res = $conn->GetArray($sql);

		echo json_encode($res);
	}
    else if($f == 'acbarangunit') {
        $r_idunit = $_REQUEST['idunit'];
        $r_idlokasi = $_REQUEST['idlokasi'];
        $r_idpegawai = $_REQUEST['idpegawai'];

		$sql  = "select top 20 s.idbarang1,s.idbarang1+' - '+b.namabarang as barang 
		    from aset.as_seri s left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		    where s.idunit = '$r_idunit' ";
	    if(!empty($r_idlokasi)) 
	        $sql .= "and s.idlokasi = '$r_idlokasi' ";
	    if(!empty($r_idpegawai)) 
	        $sql .= "and s.idpegawai = '$r_idpegawai' ";
		$sql .= "and lower(cast((s.idbarang1+' - '+b.namabarang) as varchar)) like '%$q%' 
		    group by s.idbarang1,b.namabarang order by s.idbarang1";

		$res = $conn->GetArray($sql);
		echo json_encode($res);
	}
	else if($f == 'acxbarangunit') {
        $r_idunit = $_REQUEST['idunit'];
        $r_idlokasi = $_REQUEST['idlokasi'];

		$sql  = "select s.idbarang1, (s.idbarang1+' - '+b.namabarang) as label 
		    from aset.as_seri s left join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 
		    where s.idunit = '$r_idunit' ";
	    if(!empty($r_idlokasi)) 
	        $sql .= "and s.idlokasi = '$r_idlokasi' ";
		$sql .= "and lower(cast((s.idbarang1+' - '+b.namabarang) as varchar)) like '%$q%' 
		    group by s.idbarang1,b.namabarang order by s.idbarang1";
		$rs = $conn->SelectLimit($sql,20);
		
		$a_data = array();
		while($row = $rs->FetchRow())
			$a_data[] = array('key' => $row['idbarang1'], 'label' => $row['label']);

		echo json_encode($a_data);
	}
	else if($f == 'optgedung') {
		require_once(Route::getModelPath('combo'));

        $r_idcabang = $_POST['idcabang'];
        $r_idgedung = $_POST['idgedung'];
        $r_isempty = $_POST['isempty'];
        $r_emptylabel = $_POST['emptylabel'];

		if(empty($r_idcabang) or $r_idcabang == 'null') {
			$a_gedung = array();
			echo UI::createOption($a_gedung,'',true,'-- Pilih cabang dahulu --');
		}
		else {
			$a_gedung = mCombo::gedung($conn,$r_idcabang);
			if(empty($a_gedung)){
			    $a_gedung[''] = '-- Gedung tidak ditemukan --';
    			echo UI::createOption($a_gedung,$r_idgedung);
		    }else
    			echo UI::createOption($a_gedung,$r_idgedung,$r_isempty,$r_emptylabel);
		}
	}
	else if($f == 'getkonvsatuan'){
	    $r_idbarang = $_REQUEST['idbarang1'];
	    
	    echo $r_idbarang;
	}
	else if($f == 'getdefsatuan'){
	    $r_idbarang = $_REQUEST['idbarang1'];
	    echo $conn->GetOne("select idsatuan from aset.ms_barang1 where idbarang1 = '$r_idbarang'");
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
		
		$a_data = mUnit::find($conn,$q,"namaunit",'idunit','','namaunit');		
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
	else if($f == 'optlokasibrg') {
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
			$a_lokasi = mCombo::lokasibrg($conn,$r_idunit);
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
		
		$sql = "select top 20 idpegawai,nip,namalengkap,namalengkap as pegawai 
		    from sdm.v_biodatapegawai 
		    where lower(namalengkap) like '%$q%' 
		    order by nip";
        $res = $conn->GetArray($sql);			

		echo json_encode($res);
	}
	/*
	else if($f == 'acxpegawai') {
		require_once(Route::getModelPath('pegawai'));

		$r_idunit = $_REQUEST['idunit'];
		if(!empty($r_idunit))
		    $filter = "idunit = '$r_idunit' ";
		
		$a_data = mPegawai::find($conn,$q,"namalengkap",'idpegawai','sdm.v_biodatapegawai','namalengkap',$filter);
		
		echo json_encode($a_data);
	}
	*/
	else if($f == 'acxpegawai') {
        $r_idunit = $_REQUEST['idunit'];
        
        if(!empty($r_idunit))
            $a_unit = $conn->GetRow("select infoleft,inforight from aset.ms_unit where idunit = '$r_idunit'");
            
		$sql  = "select p.idpegawai, p.namalengkap as label 
		    from sdm.v_biodatapegawai p ";
	    if(!empty($r_idunit)) 
		    $sql .= "join sdm.ms_unit u on p.idunit = u.idunit ";
	    $sql .= "where (1=1) ";
	    if(!empty($r_idunit)) 
	        $sql .= "and infoleft >= ".(int)$a_unit['infoleft']." and inforight <= ".(int)$a_unit['inforight'];
		$sql .= " and lower(p.namalengkap) like '%$q%' order by p.namalengkap";
		$rs = $conn->SelectLimit($sql,20);
		
		$a_data = array();
		while($row = $rs->FetchRow())
			$a_data[] = array('key' => $row['idpegawai'], 'label' => $row['label']);

		echo json_encode($a_data);
	}	
	else if($f == 'acxpemakai') {
        $r_idunit = $_REQUEST['idunit'];
        
		$sql = "select s.idpegawai, p.namalengkap 
		    from aset.as_seri s left join sdm.v_biodatapegawai p on p.idpegawai = s.idpegawai 
		    where s.idpegawai is not null ";
	    if($r_idunit != '')
	        $sql .= "and s.idunit = '$r_idunit' ";
		$sql .= "and lower(p.namalengkap) like '%$q%' 
		    group by s.idpegawai,p.namalengkap order by p.namalengkap";
            
		$rs = $conn->SelectLimit($sql,20);
		
		$a_data = array();
		while($row = $rs->FetchRow())
			$a_data[] = array('key' => $row['idpegawai'], 'label' => $row['namalengkap']);

		echo json_encode($a_data);
	}	
	else if($f == 'acsupplier') {
		$q = strtolower($q);
		
		$sql = "select top 20 idsupplier,namasupplier 
		    from aset.ms_supplier
		    where lower(namasupplier) like '%$q%' 
			and isblacklist != '1' 
			or isblacklist is null
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
		$r_idbarang = $_POST['idbarang1'];
		$r_noseri = (int)$_POST['noseri'];
		
        echo $conn->GetOne("select idseri from aset.as_seri where idunit = '$r_idunit' and idbarang1 = '$r_idbarang' and noseri = '$r_noseri'");			
	}
	else if($f == 'cekserisd'){
		$r_idbarang = $_POST['idbarang1'];
		$r_fnoseri = (int)$_POST['fnoseri'];
		$r_enoseri = (int)$_POST['enoseri'];
		
        echo (int)$conn->GetOne("select 1 from aset.as_seri where idbarang1 = '$r_idbarang' and noseri between '$r_fnoseri' and '$r_enoseri'");			
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
			//$child['data'] = $row['idbarang1'].' ('.$row['nchild'].') - '.$row['namabarang'];
			$child['data'] = $row['idbarang1'].' - '.$row['namabarang'];
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
	else if($f == 'acxbarangseri'){

		$sql  = "select s.idbarang1, (s.idbarang1+' - '+b.namabarang) as label 

		    from aset.as_seri s join aset.ms_barang1 b on b.idbarang1 = s.idbarang1 ";
		$sql .= "and lower(cast((s.idbarang1+' - '+b.namabarang) as varchar)) like '%$q%' 
		    group by s.idbarang1,b.namabarang order by s.idbarang1";
		$rs = $conn->SelectLimit($sql,20);
		
		$a_data = array();
		while($row = $rs->FetchRow())
			$a_data[] = array('key' => $row['idbarang1'], 'label' => $row['label']);

		echo json_encode($a_data);
	}
?>
