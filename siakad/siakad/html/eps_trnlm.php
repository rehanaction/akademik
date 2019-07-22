<? 
	include("eps_tbkod.php");
	
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_periode = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	//$conn->debug=true;
	$sql = " SELECT f.periode, u.kodeunit, f.nim, f.kodemk, f.nhuruf, f.nangka, 
				case when length(mhs.thnkurikulum)>4 then '02' else '01' end as kelas
			   FROM akademik.ak_krs f
			   JOIN akademik.ak_matakuliah m ON f.thnkurikulum = m.thnkurikulum AND f.kodemk = m.kodemk
			   JOIN akademik.ms_mahasiswa mhs ON f.nim = mhs.nim
			   JOIN gate.ms_unit u ON u.kodeunit = mhs.kodeunit
			   where f.periode='$c_periode_pelaporan' ";
	if($c_unit<>'100000')
		$sql .= "and mhs.kodeunit='$c_unit'";
		// $sql .= " limit 100";
	$rs = $conn->Execute($sql);
	//$conn->debug=false;
	$jenjang = $conn->GetRow("select epskodeprodi,kode_jenjang_studi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$a = count($rs->fields['periode']); 
	if($a>0 and !empty($jenjang['epskodeprodi'])){
		$col = $conn->SelectLimit("select * from epsbed.trnlm",1);
		$nu=0;
		$ns=0;
		
		$conn->BeginTrans();
		while($row = $rs->FetchRow()){	
			$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);
			$nim = $row['nim'];
			$kelas=$arr_kelas[$row['kelas']];
			if(empty($kelas))
				$kelas=$row['kelas'];
			$cek = $conn->GetOne("select 1 from epsbed.trnlm 
					   where tahunpelaporan='$c_thnpelaporan' and thsmstrnlm='$c_periode_pelaporan' and
					   kdkmktrnlm='".$row['kodemk']."' and nimhstrnlm='$nim'");			
		
			$record = array();
			$record["tahunpelaporan"] = $c_thnpelaporan;
			$record["thsmstrnlm"] = $c_periode_pelaporan;
			$record["kdptitrnlm"] = $conf['kdpti'];
			$record["kdpsttrnlm"] = $jenjang['epskodeprodi'];
			$record["kdjentrnlm"] = $jenjangprodi;
			
			$record["nimhstrnlm"] = $row['nim'];
			$record["kdkmktrnlm"] = $row['kodemk'];
			$record["nlakhtrnlm"] = $row['nhuruf'];
			$record["bobottrnlm"] = $row['nangka'];
			$record["kelastrnlm"] = $row['kelas'];
			
			
			if($cek != 1){		
			
				//$ok = Query::recInsert($conn,$record,'epsbed.tbkmk');	
				$sql = $conn->GetInsertSQL($col,$record);
				$ok=$conn->Execute($sql);
				if($ok){	
					$ns++;	
					$sukses=true;
					$p_posterr = false;
				}else{
					$p_postmsg = 'Proses terhenti Pada Baris : '.$ns.' NIM '.$row['nim'];
					$p_posterr = true;
					$sukses=false;
					break;
				}
			}else if($cek == 1){
				
				$ok = Query::recUpdate($conn,$record,'epsbed.trnlm',"tahunpelaporan='$c_thnpelaporan' and thsmstrnlm='$c_periode_pelaporan' and kdkmktrnlm='".$row['kodemk']."' and nimhstrnlm='$nim'");
				if($ok!=0){
					$p_postmsg = 'Proses terhenti Pada Baris : '.$nu.' NIM '.$row['nim'];
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
		$rec['tabelpdpt'] = "trnlm";
		$rec['nip'] = Modul::getUserName();
		$rec['namapetugas'] = Modul::getUserDesc();
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		
		$ok = Query::recInsert($conn,$rec,'epsbed.ms_transferpdpt');
	$conn->CommitTrans($sukses);
	}else if(empty($jenjang['epskodeprodi'])){
		$p_posterr = true;
		$p_postmsg = 'Pastikan Kode Epsbed Untuk Prodi Ini Diisi';
	}else{
		$p_posterr = true;
		$p_postmsg = 'Tidak ada data yang ditemukan.';
	}
?>
