<? 
	include("eps_tbkod.php");
	
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_periode = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];		
	
	$sql = "select m.*,m.tgllahir,u.epskodeprodi,y.tglijasah from akademik.ms_mahasiswa m 
			left join gate.ms_unit u on m.kodeunit=u.kodeunit
			left join akademik.ak_yudisium y on y.nim=m.nim where substr(periodemasuk,1,4)='$c_periode' ";
	if($c_unit<>'100000')
		$sql .= "and u.kodeunit='$c_unit'";
	$rs = $conn->Execute($sql);
	
	//cek jenjang
	$jenjang = $conn->GetRow("select j.programpend,j.lamastudi/2::int as lamabelajar,p.epskodeprodi,p.kode_jenjang_studi from akademik.ak_prodi p
								left join akademik.ms_programpend j on p.kode_jenjang_studi=j.programpend
								where kodeunit='$c_unit'");
	$lamabelajar=$jenjang['lamabelajar'];
	if(empty($lamabelajar)){
		if(strtoupper($jenjang['kode_jenjang_studi'])=='S1')
			$lamabelajar=7;
		else if(strtoupper($jenjang['kode_jenjang_studi'])=='D4')
			$lamabelajar=7;
		else if(strtoupper($jenjang['kode_jenjang_studi'])=='D3')
			$lamabelajar=5;
		else
			$lamabelajar=5;
	}
	$a = count($rs->fields['nim']);		
	if($a>0  and !empty($jenjang['epskodeprodi'])){	
		$nu=0;	
		$ns=0;	
		$conn->BeginTrans();
		$col = $conn->SelectLimit("select * from epsbed.msmhs",1);
		while($row = $rs->FetchRow()){
			$nim = $row["nim"];
			$statusmhs = $row["statusmhs"];			
			$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);		
			
			$cek = $conn->GetOne("select 1 from epsbed.msmhs where nimhsmsmhs='$nim' and tahunpelaporan='$c_thnpelaporan'");	
					
			//olah nama yang lbh dr 30 huruf
			$nama = strlen($row['nama']);
			if($nama > 30){
				$nama_fix = substr($row['nama'],0,30);
				// $exp_nama = explode(' ', $row['nama']);
				// $jml_kata = count($exp_nama);
				// $selisih = $nama - 25;
				// if(strlen($exp_nama[$jml_kata-1]) > $selisih){ //jika kata belakang lbh dr kelebihan huruf, maka singkat kata belakang saja
					// $nama_singkat = strtoupper(substr($exp_nama[$jml_kata-1],0,1));
					// array_pop($exp_nama);
					// $nama_fix = implode(" ",$exp_nama)." ".$nama_singkat;
				// }
				// else if(strlen($exp_nama[$jml_kata-1]) < $selisih){
					// if(strlen($exp_nama[$jml_kata-1]." ".$exp_nama[$jml_kata-2]) > $selisih){
						// $nama_singkat = strtoupper(substr($exp_nama[$jml_kata-2],0,1)).".".strtoupper(substr($exp_nama[$jml_kata-1],0,1));
						// $nama_fix = implode(" ",array_pop($exp_nama))." ".$nama_singkat;
					// }
				
				// }
			}else{
				$nama_fix = $row['nama'];
			}
			
			$record = array();
			$record["tahunpelaporan"] = $c_thnpelaporan;
			$record["kdptimsmhs"] = $conf['kdpti']; //ambil dari config
			$record["kdpstmsmhs"] = $jenjang['epskodeprodi'];
			$record["kdjenmsmhs"] = $jenjangprodi;
			$record["nimhsmsmhs"] = $nim;
			
			$record["nmmhsmsmhs"] = strtoupper($nama_fix);
			$record["shiftmsmhs"] = null;
			$record["tplhrmsmhs"] = !empty($row['tmplahir'])?substr(strtoupper($row['tmplahir']),0,2):null;
			// $record["tglhrmsmhs"] = str_replace("-","",$row['tgllahir']);
			$record["tglhrmsmhs"] = $row['tgllahir'];
			$record["kdjekmsmhs"] = getJenisKelamin($row['sex']);
			$record["tahunmsmhs"] = substr($row['periodemasuk'],0,4);
			
			$record["smawlmsmhs"] = $row['periodemasuk'];
			$record["btstumsmhs"] = substr($row['periodemasuk'],0,4)+(int)$lamabelajar .substr($row['periodemasuk'],4,1)+1;
			$record["assmamsmhs"] = !empty($row['kodekota'])?substr($row['kodekota'],0,2):null;
			$record["tgmskmsmhs"] = null;
			$record["tgllsmmsmhs"] = $row['tglijasah'];
			
			$record["stmhsmsmhs"] = $row['statusmhs'];
			$record["stpidmsmhs"] = 'B';
			$record["sksdimsmhs"] = 0;
			$record["asnimmsmhs"] = null;
			$record["asptimsmhs"] = null;
			$record["asjenmsmhs"] = null;
			$record["aspstmsmhs"] = null;
			
			$record["bistumsmhs"] = null;
			$record["peksbmsmhs"] = null;
			$record["nmpekmsmhs"] = null;
			
			$record["ptpekmsmhs"] = null;
			$record["pspekmsmhs"] = null;
			$record["nmprmmsmhs"] = null;
			$record["nokp1msmhs"] = null;
			$record["nokp2msmhs"] = null;
			$record["nokp3msmhs"] = null;
			$record["nokp4msmhs"] = null;
			
			
			if($cek != 1){		
			
				//$ok = Query::recInsert($conn,$record,'epsbed.tbkmk');	
				$sql = $conn->GetInsertSQL($col,$record);
				$ok=$conn->Execute($sql);
				if($ok){	
					$ns++;	
					$sukses=true;
					$p_posterr = false;
				}else{
					$p_postmsg = 'Proses terhenti Pada Baris : '.$ns.' Atas Nama '.$nama_fix;
					$p_posterr = true;
					$sukses=false;
					break;
				}
			}else if($cek == 1){
				
				$ok = Query::recUpdate($conn,$record,'epsbed.msmhs'," nimhsmsmhs='$nim' and tahunpelaporan='$c_thnpelaporan'");
				if($ok!=0){
					$p_postmsg = 'Proses terhenti Pada Baris : '.$nu.' Atas Nama '.$nama_fix;
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
		$rec['tabelpdpt'] = "msmhs";
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
		$p_postmsg = 'Tidak ada data yang ditemukan';
	}
?>
