<? 
	include("eps_tbkod.php");
	//$conn->debug=true;
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_periode = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];
	
	//--LEFT JOIN akademik.ak_kuliah k ON k.kodemk = m.kodemk and k.kelasmk=m.kelasmk and k.periode=m.periode and k.kodeunit=m.kodeunit and k.thnkurikulum=m.thnkurikulum
	$sql ="select m.periode, m.thnkurikulum, m.kodeunit, m.kodemk, m.kelasmk,m.nipdosen,mk.namamk,mk.semmk, 
			substr(m.kelasmk::text,(-2)) AS kelas,count(kd.kodemk) AS jumlahterjadwal,p.nidn,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadosen
			from akademik.ak_mengajar m
			left JOIN akademik.ak_detailkelas kd using (periode, thnkurikulum, kodeunit, kodemk, kelasmk) 
			join sdm.ms_pegawai p on p.idpegawai::text=m.nipdosen
			join akademik.ak_kurikulum mk on mk.thnkurikulum=m.thnkurikulum and mk.kodeunit=m.kodeunit and mk.kodemk=m.kodemk  
			where m.periode='$c_periode_pelaporan'";
	if($c_unit<>'100000')
		$sql .= " and m.kodeunit='$c_unit'";		
	$sql.= " group by m.periode, m.thnkurikulum, m.kodeunit, m.kodemk, m.kelasmk,m.nipdosen,p.nidn,p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang,mk.namamk,mk.semmk";
	
	$rs = $conn->Execute($sql);
	
	$jenjang = $conn->GetRow("select epskodeprodi,kode_jenjang_studi from akademik.ak_prodi where kodeunit='$c_unit'");
	//die();
	$a = count($rs->fields['nipdosen']);		
	if($a>0 and !empty($jenjang['epskodeprodi'])){
		$col = $conn->SelectLimit("select * from epsbed.trakd",1);
		$nu=0;
		$ns=0;
		$conn->BeginTrans();
		$arr_kelas=array('A'=>'01','B'=>'02','C'=>'03','D'=>'04','E'=>'05','F'=>'06','G'=>'07','H'=>'08','I'=>'09','J'=>'10','K'=>'11',
					'L'=>'12','M'=>'13','N'=>'14','O'=>'15','P'=>'16','Q'=>'17','R'=>'18','S'=>'19','T'=>'20',
					'U'=>'21','V'=>'22','W'=>'23','X'=>'24','Y'=>'25','Z'=>'26');
		//$conn->debug=true;
		while($row = $rs->FetchRow()){				
			$nodos = CStr::cStrNull($row['nidn']);
			$kelas=$arr_kelas[$row['kelasmk']];
			if(empty($kelas))
				$kelas=$row['kelasmk'];
			if(empty($row['nidn']))
				$sqlnodos="nodostrakd is null";
			else
				$sqlnodos="nodostrakd='$nodos'";
			$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);
			$sql="select 1 from epsbed.trakd where tahunpelaporan='$c_thnpelaporan' and thsmstrakd='$c_periode_pelaporan' and kdpsttrakd='".$jenjang['epskodeprodi']."' and $sqlnodos and kdkmktrakd='".$row['kodemk']."' and kelastrakd='".$kelas."'";
			$cek = $conn->GetOne($sql);			
		
			$record = array();
			$record["tahunpelaporan"] = $c_thnpelaporan;
			$record["thsmstrakd"] = $c_periode_pelaporan;
			$record["kdptitrakd"] = $conf['kdpti'];
			$record["kdpsttrakd"] = $jenjang['epskodeprodi'];
			$record["kdjentrakd"] = $jenjangprodi;
			
			$record["nodostrakd"] = $nodos;
			$record["kdkmktrakd"] = $row['kodemk'];
			$record["kelastrakd"] = $kelas;
			$record["semestertrakd"] = $row['semmk'];
			$record["tmrentrakd"] = $row['jumlahterjadwal'];
			
			$record["namamk"] = $row['namamk'];
			$record["namadosen"] = $row['namadosen'];
			
			$rs_real = $conn->GetOne("select count(*) as jml from akademik.ak_kuliah where periode='".$row['periode']."' and thnkurikulum='".$row['thnkurikulum']."' and kodemk='".$row['kodemk']."' and kelasmk='".$row['kelasmk']."' and kodeunit='".$row['kodeunit']."' and nipdosen='".$row['nipdosen']."'");
			$record["tmreltrakd"] = $rs_real;
				
			
			if($cek != 1){		
			
				//$ok = Query::recInsert($conn,$record,'epsbed.tbkmk');	
				$sql = $conn->GetInsertSQL($col,$record);
				$ok=$conn->Execute($sql);
				if($ok){	
					$ns++;	
					$sukses=true;
					$p_posterr = false;
				}else{
					$p_postmsg = 'Proses terhenti Pada Baris : '.$ns.' NIP '.$row['nipdosen'];
					$p_posterr = true;
					$sukses=false;
					break;
				}
			}
			else if($cek == 1){
				
				$ok = Query::recUpdate($conn,$record,'epsbed.trakd',"tahunpelaporan='$c_thnpelaporan' and thsmstrakd='$c_periode_pelaporan' and kdpsttrakd='".$jenjang['epskodeprodi']."' and $sqlnodos and kdkmktrakd='".$row['kodemk']."' and kelastrakd='$kelas'");
				if($ok!=0){
					$p_postmsg = 'Proses terhenti Pada Baris : '.$nu.' NIP '.$row['nipdosen'];
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
		$rec['tabelpdpt'] = "trakd";
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
