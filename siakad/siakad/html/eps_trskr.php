<? 
	include("eps_tbkod.php");
	// $conn->debug = true;
	$c_unit = $r_unit;	
	$c_periode_pelaporan = $_POST["tahun"].$_POST["semester"];	
	$c_periode = $_POST["tahun"];	
	$c_thnpelaporan = $_POST["tahunpelaporan"];		
	
	$sql = "select m.nim,m.nama, t.judulta, t.judultaen from akademik.ak_ta t left join akademik.ms_mahasiswa m on m.nim=t.nim where 1=1 ";
	if($c_unit<>'100000')
		$sql .= "and m.kodeunit='$c_unit'";
	$rs = $conn->Execute($sql);
	
	//cek jenjang
	$jenjang = $conn->GetRow("select epskodeprodi,kode_jenjang_studi from akademik.ak_prodi where kodeunit='$c_unit'");
	
	$a = count($rs->fields['nim']);		
	if($a>0 and !empty($jenjang['epskodeprodi'])){	
		$col = $conn->SelectLimit("select * from epsbed.trskr",1);
		$nu=0;
		$ns=0;
		$conn->BeginTrans();	
		while($row = $rs->FetchRow()){
			$jenjangprodi = getJenjangEPS($jenjang['kode_jenjang_studi']);		
			$cek = $conn->GetOne("select 1 from epsbed.trskr where thnpelaporan='$c_thnpelaporan'  and nimhstrskr='".$row['nim']."'");			
			//olah judul
			$len_judul = strlen($row['judulta']);
			$jml_loop = ceil($len_judul/75);
			$arr_loop = array(1=>0, 2=>75, 3=>150, 4=>225, 5=>300);
			
			for($i=1;$i<=$jml_loop;$i++){
			
				$record = array();
				$record["thnpelaporan"] = $c_thnpelaporan;
				$record["thsmstrskr"] = $c_periode_pelaporan;
				$record["kdptitrskr"] = $conf['kdpti'];
				$record["kdpsttrskr"] = $jenjang['epskodeprodi'];
				$record["kdjentrskr"] = $jenjangprodi;
				$record["nimhstrskr"] = $row['nim'];
				
				$record["noruttrskr"] = $i;
				$record["judultrskr"] = substr($row['judulta'],$arr_loop[$i],75);
				
				
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
					
					$ok = Query::recUpdate($conn,$record,'epsbed.trskr'," nimhstrskr='".$row['nim']."' and thnpelaporan='$c_thnpelaporan'");
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
		}
	if(!$p_posterr){
		$p_postmsg = 'Sukses Transfer Data sebanyak : '.$ns;
		$p_postmsg .= '<br> Sukses Update Data sebanyak : '.$nu;
	}	
		$rec['kode_program_studi'] = $c_unit;
		$rec['kode_program_studi_eps'] = $conn->GetOne("select epskodeprodi from akademik.ak_prodi where kodeunit='$c_unit'");
		$rec['periode'] = $c_periode_pelaporan;
		$rec['thnpelaporan'] = $c_thnpelaporan;
		$rec['tabelpdpt'] = "trskr";
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
