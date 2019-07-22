<? 
	include("eps_tbkod.php");
	
	$c_unit = $r_unit;	
	$c_periode = $_POST["periode"];
	$c_kurikulum = $_POST["thnkurikulum"];
	$c_thnpelaporan = $_POST["tahun"];
	
	$sql = "select * from ms_unit where epskodeprodi<>'' ";
	if($c_unit<>'000000')
		$sql .= "and kodeunit='$c_unit'";
	$rs = $conn->Execute($sql);
	//program/jenjang studi
	$rs_jenjang = $conn->GetRow("select program,epskodeprodi from ms_unit where kodeunit='$c_unit'");
	
	$a = count($rs->fields['epskodeprodi']);		
	if($a>0){
		while(!$rs->EOF){		
			$kode_program_studi = $rs->fields["epskodeprodi"];		
			
			//$connp->debug=true;
			$cek = $connp->GetOne("select 1 from tmst_program_studi where kode_program_studi='$kode_program_studi'");			
		
			//$record["kode_tmst_program_studi"] = CStrNull($rs->fields["pt"]);
			$record["kode_perguruan_tinggi"] = "405016";
			$record["kode_fakultas"] = NULL;
			$record["kode_jenjang_studi"] = getJenjangEPS($rs->fields["program"]);
			$record["kode_program_studi"] = CStrNull($rs->fields["epskodeprodi"]);
			$record["nama_program_studi"] = CStrNull($rs->fields["namaunit"]);
			
			$record["tgl_berdiri"] = null;		
			$record["semester_awal"] = null;
			$record["status_program_studi"] = 'A';
			$record["mulai_semester"] = null;					
			$record["sks_lulus"] = null;
			$record["email"] = null;
			$record["no_sk_dikti"] = null;
			$record["tgl_sk_dikti"] = null;
			$record["tgl_akhir_sk_dikti"] = null;
			$record["no_sk_ban"] = null;
			$record["tgl_sk_ban"] = null;		
			$record["tgl_akhir_sk_ban"] = null;
			$record["kode_akreditasi"] = null;
			$record["frekuensi_kurikulum"] = null;			
			$record["pelaksanaan_kurikulum"] = null;
			$record["nidn"] = null;
			$record["hp_ketua"] = null;
			$record["telepon_kantor"] = null;
			$record["fax"] = null;
			$record["nama_operator"] = null;
			$record["hp_operator"] = null;	
			
			//}		
			if($cek<>'1'){		
				$ns++;				
				$ok = $connp->AutoExecute('tmst_program_studi',$record,'INSERT');
				$message = 'Transfer Data Sukses, sebanyak : '.$ns;
			}
			else if($cek=='1'){
				$nu++;
				$connp->AutoExecute('tmst_program_studi',$record,'UPDATE',"kode_program_studi='$kode_program_studi'",false);
				$message = 'Update Data Sukses, sebanyak : '.$nu;
			}
			
			$rs->MoveNext();
		}	
		$rec = array();
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $rs_jenjang['epskodeprodi'];
		$rec['periode'] = $c_periode;
		$rec['thnkurikulum'] = $c_kurikulum;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = 'tmst_program_studi';
		$rec['nip'] = $_SESSION['SIAKAD_USER'];
		$rec['namapetugas'] = $_SESSION['SIAKAD_NAME'];
		$rec['t_updatetime'] = date("Y-m-d H:i:s");
		$rec['t_ipaddress'] = $_SERVER['REMOTE_ADDR']; 
		$ok = $connp->AutoExecute('ms_transferpdpt',$rec,'INSERT');	
	}else{
		$message = 'Tidak ada data yang ditemukan';
	}
?>