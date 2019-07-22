<? 
	include("eps_tbkod.php");
	
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_periode = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];
	//$conn->debug=true;
	$sql = " SELECT p.periode, u.kodeunit, p.nim, p.statusmhs, p.ipk, p.skslulus, m.noijasah, m.nidnpromotor,m.nidnkopromotor1,m.nidnkopromotor2,
				m.nidnkopromotor3, m.nidnkopromotor4, y.noskyudisium, y.tglskyudisium, y.noijasah as noijasahyudisium, y.tglijasah, ta.tglmulai, 
				ta.tglselesai,p1.nidn as pemb1,p2.nidn as pemb2
				FROM akademik.ak_perwalian p
				left join akademik.ak_yudisium y on y.nim=p.nim
				left join akademik.ak_ta ta on ta.nim=p.nim
				left join akademik.ak_pembimbing pb on ta.idta=pb.idta
				left join sdm.ms_pegawai p1 on pb.nip=p1.nik and pb.tipepembimbing='U'
				left join sdm.ms_pegawai p2 on pb.nip=p2.nik and pb.tipepembimbing='C'
				JOIN akademik.ms_mahasiswa m ON m.nim = p.nim
				JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
				where p.periode='$c_periode_pelaporan' ";
	if($c_unit<>'100000')
		$sql .= "and m.kodeunit='$c_unit'";
		//$sql .= " limit 100";
	$rs = $conn->Execute($sql);
	
	$jenjang = $conn->GetRow("select epskodeprodi,kode_jenjang_studi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$a = count($rs->fields['nim']);		
	if($a>0 and !empty($jenjang['epskodeprodi'])){
		$col = $conn->SelectLimit("select * from epsbed.trlsm",1);
		$nu=0;
		$ns=0;
		$conn->BeginTrans();
		
		while($row = $rs->FetchRow()){	
			$nim = $row['nim'];
			$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);
			$cek = $conn->GetOne("select 1 from epsbed.trlsm 
								   where tahunpelaporan='$c_thnpelaporan' and thsmstrlsm='$c_periode_pelaporan' and nimhstrlsm='$nim'");			
		
			$record = array();
			$record["tahunpelaporan"] = $c_thnpelaporan;
			$record["thsmstrlsm"] = $c_periode_pelaporan;
			$record["kdptitrlsm"] = $conf['kdpti'];
			$record["kdpsttrlsm"] = $jenjang['epskodeprodi'];
			$record["kdjentrlsm"] = $jenjangprodi;
			
			$record["nimhstrlsm"] = $row['nim'];	
			$record["stmhstrlsm"] = getStatusMhsEPS($row['statusmhs']);	
			
			if($record["stmhstrlsm"]=='L'){
				//diisi jika status mhs lulus
				$record["tgllstrlsm"] = $row['tglijasah']; //tgl lulus	
				$record["skstttrlsm"] = $row['skslulus']; //sks lulus
				$record["nlipktrlsm"] = $row['ipk']; //ipk
				$record["noskrtrlsm"] = $row['noskyudisium']; //sk yudisium
				$record["tglretrlsm"] = $row['tglskyudisium']; // tgl yudisium
				$record["noijjatrlsm"] = $row['noijasahyudisium']; // noijasah
				if($jenjangprodi=='A' or $jenjangprodi=='B' or $jenjangprodi=='C' or $jenjangprodi=='D' or $jenjangprodi=='E' or $jenjangprodi=='F' or $jenjangprodi=='G')
					$record["stllstrlsm"] = 'S'; //N=non skripsi atau S=skripsi	
				else if($jenjangprodi=='J')
					$record["stllstrlsm"] = 'N'; //N=non skripsi atau S=skripsi	
				else
					$record["stllstrlsm"] = null; //N=non skripsi atau S=skripsi	
				$record["jnllstrlsm"] = 'I'; //I=individu, K=kelompok	
				
				$record["blawltrlsm"] = $row['tglmulai'];	
				$record["blakhtrlsm"] = $row['tglselesai'];	
				$record["nods1trlsm"] = $row['pemb1'];	
				$record["nods2trlsm"] = $row['pemb2'];	
				$record["nods3trlsm"] = null;//$row['nidnkopromotor2'];	
				$record["nods4trlsm"] = null;//$row['nidnkopromotor3'];	
				$record["nods5trlsm"] = null;//$row['nidnkopromotor4'];
			}else{
				$record["tgllstrlsm"] = null; //tgl lulus	
				$record["skstttrlsm"] = null; //sks lulus
				$record["nlipktrlsm"] = null; //ipk
				$record["noskrtrlsm"] = null; //sk yudisium
				$record["tglretrlsm"] = null; // tgl yudisium
				$record["noijjatrlsm"] = null; // noijasah
				$record["stllstrlsm"] = null; //N=non skripsi atau S=skripsi	
				$record["jnllstrlsm"] = null; //I=individu, K=kelompok	
				
				$record["blawltrlsm"] = null;	
				$record["blakhtrlsm"] = null;	
				$record["nods1trlsm"] = null;	
				$record["nods2trlsm"] = null;	
				$record["nods3trlsm"] = null;	
				$record["nods4trlsm"] = null;	
				$record["nods5trlsm"] = null;
			}
					
			
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
				
				$ok = Query::recUpdate($conn,$record,'epsbed.trlsm',"tahunpelaporan='$c_thnpelaporan' and thsmstrlsm='$c_periode_pelaporan' and nimhstrlsm='$nim'");
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
		$rec['tabelpdpt'] = "trlsm";
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
