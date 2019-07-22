<? 
	include("eps_tbkod.php");
	//$conn->debug=true;
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_periode = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	
	$jenjang = $conn->GetRow("select epskodeprodi,kode_jenjang_studi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	/*$sqls = " SELECT v.nim, v.periode, m.kodeunit, v.ips, v.jumlahsks AS jmlsksperiode, ipk.ipk, ipk.jumlahsks AS totsks
			FROM akademik.vc_ips v
			LEFT JOIN akademik.vc_ipk ipk ON ipk.nim = v.nim
			JOIN akademik.ms_mahasiswa m ON m.nim = v.nim
			JOIN gate.ms_unit u ON u.kodeunit = m.kodeunit
			WHERE m.statusmhs = 'A' AND v.ips IS NOT NULL
			AND periode='$c_periode_pelaporan' ";
	if($c_unit<>'100000')
		$sqls .= "and m.kodeunit='$c_unit'";
	*/
	$sqls = "select p.ips,p.ipk,p.sks,p.jumlahsks,p.nim from akademik.ak_perwalian p 
				join akademik.ms_mahasiswa m on m.nim=p.nim
				where p.statusmhs='A' and p.ips is not null and p.periode='$c_periode_pelaporan'";
	if($c_unit<>'100000')
		$sqls .= "and m.kodeunit='$c_unit'";
	$rs = $conn->Execute($sqls);
	
	$a = count($rs->fields['nim']);
	if($a>0){ 
		$col = $conn->SelectLimit("select * from epsbed.trakm",1);
		$nu=0;
		$ns=0;
		$conn->BeginTrans();
		while($row = $rs->FetchRow()){	
			$nim = $row['nim'];
			$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);		
			
			$cek = $conn->GetOne("select 1 from epsbed.trakm where tahunpelaporan='$c_thnpelaporan' and thsmstrakm='$c_periode_pelaporan' and nimhstrakm='$nim'");			
		
			$record = array();
			$record["tahunpelaporan"] = $c_thnpelaporan;
			$record["thsmstrakm"] = $c_periode_pelaporan;
			$record["kdptitrakm"] = $conf['kdpti'];
			$record["kdpsttrakm"] = $jenjang['epskodeprodi'];
			$record["kdjentrakm"] = $jenjangprodi;
			
			$record["nimhstrakm"] = $nim;
			$record["nlipstrakm"] = $row['ips'];
			$record["sksemtrakm"] = $row['jumlahsks'];
			$record["nlipktrakm"] = $row['ipk'];
			$record["skstttrakm"] = $row['sks'];
			
			
			if($cek != 1){		
			
				//$ok = Query::recInsert($conn,$record,'epsbed.tbkmk');	
				$sql = $conn->GetInsertSQL($col,$record);
				$ok=$conn->Execute($sql);
				if($ok){	
					$ns++;	
					$sukses=true;
					$p_posterr = false;
				}else{
					$p_postmsg = 'Proses terhenti Pada Baris : '.$ns.' NIM '.$nim;
					$p_posterr = true;
					$sukses=false;
					break;
				}
			}else if($cek == 1){
				
				$ok = Query::recUpdate($conn,$record,'epsbed.trakm',"tahunpelaporan='$c_thnpelaporan' and thsmstrakm='$c_periode_pelaporan' and nimhstrakm='$nim'");
				if($ok!=0){
					$p_postmsg = 'Proses terhenti Pada Baris : '.$nu.' NIM '.$nim;
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
		}	
		if(!$p_posterr){
			$p_postmsg = 'Sukses Transfer Data sebanyak : '.$ns;
			$p_postmsg .= '<br> Sukses Update Data sebanyak : '.$nu;
		}
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
		$rec['periode'] = $c_periode_pelaporan;
		// $rec['thnkurikulum'] = $c_kurikulum;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = "trakm";
		$rec['nip'] = Modul::getUserName();
		$rec['namapetugas'] = Modul::getUserDesc();
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		
		$ok = Query::recInsert($conn,$rec,'epsbed.ms_transferpdpt');
		$conn->CommitTrans($sukses);
	}else{
		$p_posterr = true;
		$p_postmsg = 'Tidak ada data yang ditemukan.';
	}
?>
