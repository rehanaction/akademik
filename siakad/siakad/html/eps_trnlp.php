<? 
	include("eps_tbkod.php");
	//$conn->debug=true;
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_tahun = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];	
	
	/*$sql = " SELECT f.periode, u.kodeunit, f.nim, f.kodemk, f.nhuruf, f.nangka, substr(f.kelasmk, (-2)) AS kelas
			   FROM akademik.ak_krs f
			   JOIN akademik.ak_matakuliah m ON f.thnkurikulum = m.thnkurikulum AND f.kodemk = m.kodemk
			   JOIN akademik.ms_mahasiswa mhs ON f.nim = mhs.nim
			   JOIN gate.ms_unit u ON u.kodeunit = mhs.kodeunit
			   where f.periode='$c_periode_pelaporan' and substring(mhs.periodemasuk,1,4)::numeric<2013 ";*/
	$sql ="select t.nim,t.kodemk,t.nhuruf,t.nangka,substr(k.kelasmk, (-2)) AS kelas
			from akademik.ak_transkrip t
			join akademik.ak_krs k using (thnkurikulum, kodeunit, kodemk, nim)
			join akademik.ms_mahasiswa m on t.nim=m.nim
			where k.periode='$c_periode_pelaporan' and substring(m.periodemasuk,1,4)='$c_tahun'
			and m.statusmhs not in ('L','K','O')";
	if($c_unit<>'100000')
		$sql .= "and m.kodeunit='$c_unit'";
		// $sql .= " limit 100";
	$rs = $conn->Execute($sql);
	
	$jenjang = $conn->GetRow("select epskodeprodi,kode_jenjang_studi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$a = count($rs->fields['nim']); 
	if($a>0  and !empty($jenjang['epskodeprodi'])){
		$col = $conn->SelectLimit("select * from epsbed.trnlp",1);
		$nu=0;
		$ns=0;
		$conn->BeginTrans();
		while($row = $rs->FetchRow()){	
			$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);
			$nim = $row['nim'];
			
			$cek = $conn->GetOne("select 1 from epsbed.trnlp
					   where tahunpelaporan='$c_thnpelaporan' and thsmstrnlp='$c_periode_pelaporan' and
					   kdkmktrnlp='".$row['kodemk']."' and nimhstrnlp='$nim'");			
		
			$record = array();
			$record["tahunpelaporan"] = $c_thnpelaporan;
			$record["thsmstrnlp"] = $c_periode_pelaporan;
			$record["kdptitrnlp"] = $conf['kdpti'];
			$record["kdpsttrnlp"] = $jenjang['epskodeprodi'];
			$record["kdjentrnlp"] = $jenjangprodi;
			
			$record["nimhstrnlp"] = $row['nim'];
			$record["kdkmktrnlp"] = $row['kodemk'];
			$record["nlakhtrnlp"] = $row['nhuruf'];
			$record["bobottrnlp"] = $row['nangka'];
			$record["kelastrnlp"] = '01';
			
			
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
			}
			else if($cek == 1){
				
				$ok = Query::recUpdate($conn,$record,'epsbed.trnlp',"tahunpelaporan='$c_thnpelaporan' and thsmstrnlp='$c_periode_pelaporan' and kdkmktrnlp='".$row['kodemk']."' and nimhstrnlp='$nim'");
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
		$rec['tabelpdpt'] = "trnlp";
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
