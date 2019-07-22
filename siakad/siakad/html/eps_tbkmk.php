<? 
	include("eps_tbkod.php");
	//$conn->debug = true;
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_periode = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];		
	//$conn->debug=true;
	$sql = "select k.kodeunit, k.kodemk,k.namamk, k.sks, k.semmk, mk.isaktif, mk.skstatapmuka, mk.skspraktikum, mk.sksprakteklapangan, mk.diktat, mk.bahanajar, mk.sap, mk.silabus,p.nidn,mk.nipdosenpengampu,
			   CASE 
					WHEN mk.tipekuliah='T' THEN 'S'
					WHEN k.wajibpilihan='W' THEN 'A'
					WHEN k.wajibpilihan='P' THEN 'B'
					ELSE NULL
				END AS wajib, NULL::unknown AS inti, 
				CASE mk.kodejenis
					WHEN 'MPK' THEN 'A'
					WHEN 'MKK' THEN 'B'
					WHEN 'MKB' THEN 'C'
					WHEN 'MPB' THEN 'D'
					WHEN 'MBB' THEN 'E'
					WHEN 'MKU' THEN 'F'
					WHEN 'MKDU' THEN 'F'
					WHEN 'MKDK' THEN 'G'
					WHEN 'MKK' THEN 'I'
					ELSE NULL
				END AS jenismk
			   from akademik.ak_kurikulum k join akademik.ak_matakuliah mk on mk.thnkurikulum=k.thnkurikulum and mk.kodemk=k.kodemk
			   left join sdm.ms_pegawai p on p.nik=mk.nipdosenpengampu ";
	if($c_unit<>'100000')
		$sql .= " where k.kodeunit='$c_unit'";
	$rs = $conn->Execute($sql);
	//$conn->debug=false;
	//die();
	//cek jenjang
	$jenjang = $conn->GetRow("select epskodeprodi,kode_jenjang_studi from akademik.ak_prodi where kodeunit='$c_unit'");
		
	$a = count($rs->fields['kodemk']);
	//echo $a;die();		
	if($a>0 and !empty($jenjang['epskodeprodi'])){
	$col = $conn->SelectLimit("select * from epsbed.tbkmk",1);
	$nu=0;
	$ns=0;
	$conn->BeginTrans();
	while($row = $rs->FetchRow()){	
		$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);
		$cek = $conn->GetOne("select 1 from epsbed.tbkmk where tahunpelaporan='$c_thnpelaporan' and thsmstbkmk='$c_periode_pelaporan' and kdpsttbkmk='".$jenjang['epskodeprodi']."' and kdkmktbkmk='".$row['kodemk']."'");			
		// $cek = $connp->GetOne("select 1 from epsbed.tbkmk where kode_mata_kuliah='$kodemk' and kode_kurikulum='$thnkurikulum' and kode_program_studi='$kode_program_studi' and semester='$semester'");			
	
		$record = array();
		$record["tahunpelaporan"] = $c_thnpelaporan;
		$record["thsmstbkmk"] = $c_periode_pelaporan;
		$record["kdptitbkmk"] = $conf['kdpti'];
		$record["kdpsttbkmk"] = $jenjang['epskodeprodi'];
		$record["kdjentbkmk"] = $jenjangprodi;
		$record["kdkmktbkmk"] = $row['kodemk'];
		
		$record["nakmktbkmk"] = strtoupper($row['namamk']);
		$record["sksmktbkmk"] = $row['sks'];
		$record["skstmtbkmk"] = $row['skstatapmuka'];
		$record["sksprtbkmk"] = $row['skspraktikum'];
		$record["skslptbkmk"] = $row['sksprakteklapangan'];
		
		$record["semestbkmk"] = str_pad($row['semmk'],2,'0',STR_PAD_LEFT); //str_pad($input, 10, "-=", STR_PAD_LEFT);
		$record["kdwpltbkmk"] = $row['wajib'];
		$record["kdkurtbkmk"] = null;//$row['inti'];
		$record["kdkeltbkmk"] = null;//$row['jenismk'];
		$record["nodostbkmk"] = $row['nidn'];
		
		$record["jenjatbkmk"] = null;//$jenjangprodi;
		$record["proditbkmk"] = null;
		if($row['isaktif'] == '1' or $row['isaktif'] == '-1')
			$aktif = 'A';
		else
			$aktif = 'H';
		$record["stkmktbkmk"] = $aktif;
		if($row['silabus']=='1')
			$silabus = 'Y';
		else
			$silabus = 'T';
		if($row['bahanajar']=='1')
			$bahanajar = 'Y';
		else
			$bahanajar = 'T';
		if($row['diktat']=='1')
			$diktat = 'Y';
		else
			$diktat = 'T';
		if($row['sap']=='1')
			$sap = 'Y';
		else
			$sap = 'T';
		$record["slbustbkmk"] = $silabus;
		$record["sappptbkmk"] = $sap;
		$record["bhnajtbkmk"] = $bahanajar;
		$record["diktttbkmk"] = $diktat;
		
		if($cek != 1){		
			
			//$ok = Query::recInsert($conn,$record,'epsbed.tbkmk');	
			$sql = $conn->GetInsertSQL($col,$record);
			$ok=$conn->Execute($sql);
			if($ok){	
				$ns++;	
				$sukses=true;
				$p_posterr = false;
			}else{
				$p_postmsg = 'Proses terhenti Pada Baris : '.$ns.' Matakuliah '.$row['namamk'];
				$p_posterr = true;
				$sukses=false;
				break;
			}
		}
		else if($cek == 1){
			
			$ok = Query::recUpdate($conn,$record,'epsbed.tbkmk', "tahunpelaporan='$c_thnpelaporan' and thsmstbkmk='$c_periode_pelaporan' and kdpsttbkmk='".$jenjang['epskodeprodi']."' and kdkmktbkmk='".$row['kodemk']."'");
			if($ok!=0){
				$p_postmsg = 'Proses terhenti Pada Baris : '.$nu.' Matakuliah '.$row['namamk'];
				$p_posterr = true;
				$sukses=false;
				break;
			}else{
				$nu++;
				$sukses=true;
				//$p_postmsg = 'Update Data Sukses, sebanyak : '.$nu;
				$p_posterr = false;
			}
		}
		$i++;
			
	}
	
	if(!$p_posterr){
		$p_postmsg = 'Sukses Transfer Data sebanyak : '.$ns;
		$p_postmsg .= '<br> Sukses Update Data sebanyak : '.$nu;
	}
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
		$rec['periode'] = $c_periode_pelaporan;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = "tbkmk";
		$rec['nip'] = Modul::getUserName();
		$rec['namapetugas'] = Modul::getUserDesc();
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		
		$ok = Query::recInsert($conn,$rec,'epsbed.ms_transferpdpt');
		//if($)
	$conn->CommitTrans($sukses);
	}else if(empty($jenjang['epskodeprodi'])){
		$p_posterr = true;
		$p_postmsg = 'Pastikan Kode Epsbed Untuk Prodi Ini Diisi';
	}else{
		$p_posterr = true;
		$p_postmsg = 'Tidak ada data yang ditemukan';
	}
?>
