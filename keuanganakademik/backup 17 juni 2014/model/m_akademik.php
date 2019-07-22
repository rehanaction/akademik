<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mAkademik {
		
		function infoUnit($conn,$kodeunit){
				$sql = "select * from gate.ms_unit where kodeunit = '$kodeunit'";
			
				return $conn->getRow($sql);
			}
		// mendapatkan array data
		function getArrayunit($conn,$dot=true,$level='') {
			$cek = Modul::getLeftRight();
			
			$sql = "select kodeunit, namaunit, level from gate.ms_unit
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'";
			if($level <> '')
				$sql .= " and level = '$level'";
						
			$sql .= " and isakad=-1 order by infoleft";
			$rs = $conn->Execute($sql);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($dot)
					$pref = '..';
				else
					$pref = '';
					//$pref = '&nbsp;&nbsp;';
				
				$data[$row['kodeunit']] = str_repeat($pref,$row['level']).$row['namaunit'];
			}
			
			return $data;
		}
		
		function getArrayjalur($conn){
			
				$sql = "select * from akademik.lv_jalurpenerimaan";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
					$data[$row['jalurpenerimaan']] = $row['jalurpenerimaan'];
					}
					
				return $data;
			}
			
		function getArraygelombang($conn){
			
				$sql = "select * from pendaftaran.lv_gelombang";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
					$data[$row['idgelombang']] = $row['namagelombang'];
					}
					
				return $data;
			}
			
		function getArrayperiode($conn){
			
				$sql = "select * from akademik.ms_periode order by periode desc";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
					
						$text = substr($row['periode'],0,4)+1;
						$name = substr($row['periode'],0,4).' / '.$text;
						$name .= substr($row['periode'],-1)=='1'?' Gasal':' Genap';
					
						$data[$row['periode']] = $name;
					}
					
				return $data;
			}
			
		function getArraysistemkuliah($conn){
				$sql = "select * from akademik.ak_sistem order by sistemkuliah";
				return $conn->GetArray($sql);
			}
		
		function sqlMhs($conn,$data){
				$sql = "select nim from akademik.ms_mahasiswa where (1=1)";
				if($data['jalurpenerimaan'] <> '')
					$sql .= " and jalurpenerimaan = '".$data['jalurpenerimaan']."'";
				if($data['kodeunit'] <> '')
					$sql .= " and kodeunit = '".$data['kodeunit']."'";
				return $sql;
			}
		function sqlpendaftar($conn,$data){
				$sql = "select 
					m.nopendaftar
			 		from pendaftaran.pd_pendaftar m 
					where (1=1) and lulusujian = 'TRUE'";
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and jalurpenerimaan = '".$data['jalurpenerimaan']."'";
			if($data['kodeunit'] <> '')
				$sql .= " and pilihanditerima = '".$data['kodeunit']."'";
			if($data['periodetagihan'] <> '')
				$sql .= " and periodedaftar = '".substr($data['periodetagihan'],0,4)."'";
				return $sql;
			}
			
		function getArraymhsperwalian($conn,$data){
			$sql = "select 
					m.nim, 
					m.jalurpenerimaan,
					m.sistemkuliah,
					m.periodemasuk,
					m.kodeunit,
					p.statusmhs as statusperwalian
			 		from akademik.ms_mahasiswa m 
					left join akademik.ak_perwalian p on m.nim = p.nim and p.periode = '".$data['periodesebelumnya']."' 
					where (1=1) and m.statusmhs not in ('O','U','W')";
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and jalurpenerimaan = '".$data['jalurpenerimaan']."'";
			if($data['kodeunit'] <> '')
				$sql .= " and kodeunit = '".$data['kodeunit']."'";
				
			return $conn->getArray($sql);
			}
			
		function getDatamhs($conn,$mhs){
				$sql = "select m.nim,m.nama,m.kodeunit,u.namaunit, coalesce(s.namasistem,'')||' '||coalesce(s.tipeprogram,'') as namasistem,
						m.jalurpenerimaan,
						m.jalurpenerimaan||' '||coalesce(j.keterangan,'') as namajalur,m.periodemasuk,m.sistemkuliah
						from akademik.ms_mahasiswa m 
						left join gate.ms_unit u on  m.kodeunit = u.kodeunit
						left join akademik.ak_sistem s on s.sistemkuliah = m.sistemkuliah
						left join akademik.lv_jalurpenerimaan j on j.jalurpenerimaan = m.jalurpenerimaan
						where nim = '$mhs'";
				return $conn->getRow($sql);
			}
		
		function getDatapendaftar($conn,$mhs){
				$sql = "select m.nopendaftar,m.nama,m.pilihanditerima as kodeunit,u.namaunit,m.jalurpenerimaan, 
						coalesce(s.namasistem,'')||' '||coalesce(s.tipeprogram,'') as namasistem,m.sistemkuliah,
						m.jalurpenerimaan||' '||coalesce(j.keterangan,'') as namajalur,m.periodedaftar
						from pendaftaran.pd_pendaftar m 
						left join gate.ms_unit u on  m.pilihanditerima = u.kodeunit
						left join akademik.ak_sistem s on s.sistemkuliah = m.sistemkuliah
						left join akademik.lv_jalurpenerimaan j on j.jalurpenerimaan = m.jalurpenerimaan
						where nopendaftar = '$mhs'";
				return $conn->getRow($sql);
		}
		
		
		function getArrayperiodeyudisium($conn){
				$sql = "select idyudisium,tglyudisium from akademik.ak_periodeyudisium order by tglyudisium desc";
				$rs = $conn->Execute($sql);
				while($row = $rs->fetchRow()){
					$data[$row['idyudisium']] = $row['idyudisium'].' ( '.cStr::formatDateInd($row['tglyudisium']).' )';
					}
				return $data;
			}
		
		function getGelombangdaftar($conn,$periode=''){
			$sql = "select periodedaftar,idgelombang,jalurpenerimaan from pendaftaran.pd_gelombangdaftar where 1=1 ";
			if($periode)
				$sql .= " and periodedaftar = '$periode'";
			$sql .= " group by periodedaftar,idgelombang,jalurpenerimaan
					 order by periodedaftar desc";
			
			return $conn->GetArray($sql);
			
			}
			
		function getPeriodedaftar($conn){
			$sql = "select * from pendaftaran.ms_periodedaftar where 1=1 ";
			$sql .= " order by periodedaftar desc";
			$rs = $conn->Execute($sql);
				while($row = $rs->fetchRow()){
					$data[$row['periodedaftar']] = $row['periodedaftar'];
					}
				return $data;
			
			}
			
		function getProgrampend($conn){
			$sql = "select * from akademik.ms_programpend where 1=1 ";
			$sql .= " order by programpend";
			$rs = $conn->Execute($sql);
				while($row = $rs->fetchRow()){
					$data[$row['programpend']] = $row['programpend'].' '.$row['namaprogram'];
					}
				return $data;
			}
			
		function getMhsyudisium($conn,$periode='',$unit=''){
			$sql = "select y.*,m.*, u.namaunit,p.tglyudisium from akademik.ak_yudisium y 
					join akademik.ms_mahasiswa m on m.nim = y.nim 
					left join gate.ms_unit u on u.kodeunit = m.kodeunit
					left join akademik.ak_periodeyudisium p on p.idyudisium = y.idyudisium
					where ( 1=1)";
			
			if($periode <>'')		
				$sql .=	" and y.idyudisium = '".$periode."'";
			
			if($unit <> '')
				$sql .= " and m.kodeunit = '$unit'";
				
			$sql .= " order by y.nim";
			$rs = $conn->Execute($sql);
				while($row = $rs->fetchRow()){
					$data[$row['nim']] = $row;
					}
				return $data;
			
			}
			
		function random($panjang)
		{
		   $karakter = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890abcdefghijklmnopqrstuvwxyz';
		   $string = '';
		   for($i = 0; $i < $panjang; $i++) {
		   $pos = rand(0, strlen($karakter)-1);
		   $string .= $karakter{$pos};
		   }
			return $string;
		}
		
		function getListPendaftar($conn,$data){
			$sql = "select 
					m.nopendaftar, 
					m.jalurpenerimaan,
					m.sistemkuliah,
					m.periodedaftar,
					m.pilihanditerima
			 		from pendaftaran.pd_pendaftar m 
					where (1=1) and lulusujian = 'TRUE'";
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and jalurpenerimaan = '".$data['jalurpenerimaan']."'";
			if($data['kodeunit'] <> '')
				$sql .= " and pilihanditerima = '".$data['kodeunit']."'";
			if($data['periodetagihan'] <> '')
				$sql .= " and periodedaftar = '".substr($data['periodetagihan'],0,4)."'";
				
			return $conn->getArray($sql);
			}
			
		function getDatakrs($conn,$r_nim,$r_periode){
			$sql = " select sum(m.sks) as sks,coalesce(m.tipekuliah,'A') as tipekuliah from akademik.ak_krs k
					left join akademik.ak_matakuliah m on m.thnkurikulum = k.thnkurikulum and m.kodemk = k.kodemk
					where k.periode = '$r_periode' and k.nim = '$r_nim' group by coalesce(m.tipekuliah,'A') 
					";
					
			$rs = $conn->Execute($sql);
			while($row = $rs->fetchRow()){
					$data[$row['tipekuliah']] = $row['sks'];
				}
				
			return $data;
			}
			
		function getDatakrsall($conn,$r_periode,$tipe){
			$sql = " select sum(m.sks) as sks,nim from akademik.ak_krs k
					left join akademik.ak_matakuliah m on m.thnkurikulum = k.thnkurikulum and m.kodemk = k.kodemk
					where k.periode = '$r_periode' and coalesce(m.tipekuliah,'A') = '$tipe' 
					group by nim 
					";
					
			$rs = $conn->Execute($sql);
			while($row = $rs->fetchRow()){
					$data[$row['nim']] = $row['sks'];
				}
				
			return $data;
			}
			
		function getFrekuensibulanan($conn,$periode){
				$sql = "select * from akademik.ms_periode where periode = '$periode'";
				$row = $conn->getRow($sql);
				$data = array();
				if($row){
					for($i = $row['bulanawal']; $i <= $row['bulanakhir']; $i++){
						$data[$i] = $i;
						if(substr($i,-2)=='12')
							{
								$thn = substr($i,0,4) + 1;
								$i=$thn.'00';
								} 
						}
					}
				return $data;
			}
			
	}
	
?>