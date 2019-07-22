<?php
	// model combo box
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mAkademik {
		
		function unitRide($conn,$unit,$unitride) {
			if(strlen($unit) == 0 or strlen($unitride) == 0)
				return false;
			
			$sql = "select 1 from gate.ms_unit u 
					join gate.ms_unit ur on ur.infoleft >= u.infoleft and ur.inforight <= u.inforight
					where u.kodeunit = ".Query::escape($unit)." and ur.kodeunit = ".Query::escape($unitride);
			$cek = $conn->GetOne($sql);
			
			return (empty($cek) ? false : true);
		}
		
		function infoUnit($conn,$kodeunit){
				$sql = "select u.*, u2.namaunit as fakultas,  p.kode_jenjang_studi as strata from gate.ms_unit u 
				left join gate.ms_unit u2 on u.kodeunitparent = u2.kodeunit
				left join akademik.ak_prodi p on u.kodeunit = p.kodeunit";
				$sql.=" where u.kodeunit = '$kodeunit' ";
			
				return $conn->getRow($sql);
			}
		// mendapatkan array data
		function getArrayunit($conn,$dot=true,$level='',$kodeunit='') {
			$cek = Modul::getLeftRight();
			if(!empty($kodeunit)){
				$a_info=$conn->GetRow("select infoleft,inforight from gate.ms_unit where kodeunit='$kodeunit'");
				$cek['LEFT']=$a_info['infoleft'];
				$cek['RIGHT']=$a_info['inforight'];
			}
			
			$sql = "select kodeunit, namaunit, level from gate.ms_unit
					where infoleft >= '".$cek['LEFT']."' and inforight <= '".$cek['RIGHT']."'";
			if($level <> '')
				$sql .= " and level = '$level'";
						
			$sql .= " and isakad=-1 and ispamu=0 order by infoleft";
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
			
				$sql = "select * from pendaftaran.lv_gelombang order by idgelombang";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
					$data[$row['idgelombang']] = $row['namagelombang'];
					}
					
				return $data;
			}
		function getJumlahGelombang($conn){
				$sql = "select * from pendaftaran.lv_gelombang";
				$rs = $conn->Execute($sql);
				$data = 0;
				while($row = $rs->FetchRow()){
					$data++;
					}
					
				return $data;
			
			}
		
		function getDataPeriode($conn,$periode) {
			$sql = "select * from akademik.ms_periode where periode = ".Query::escape($periode);
			
			return $conn->GetRow($sql);
		}
		
		function getArrayperiode($conn){
			
				$sql = "select * from akademik.ms_periode order by periode desc";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
						$text = substr($row['periode'],0,4)+1; 
						$name = substr($row['periode'],0,4).' / '.$text; 
						
						$semester = substr($row['periode'],-1);
						if ($semester=='0')
							$name .= 'Pendek Awal';
						else if ($semester=='1')
							$name .= 'Gasal';
						else if ($semester=='2')
							$name .= 'Genap';
						else if ($semester=='3')
							$name .= 'Pendek';
							
						$data[$row['periode']] = $name;
					}
					
				return $data;
			}
			
		function getArraysistemkuliah($conn,$jenis=''){
			$basis = modul::getBasis();
			$kampus = modul::getKampus();
				$sql = "select * from akademik.ak_sistem where (1=1) ";
				
				if (!empty($jenis))
					$sql.=" and sistemkuliah  = '$jenis' ";
				if(!empty($basis))
					$sql .= " and kodebasis = '$basis' ";
				if(!empty($kampus))
					$sql .= " and kodekampus = '$kampus' ";
				$sql.="order by sistemkuliah";
				return $conn->GetArray($sql);
			}
		
		function getArraySistemKuliahCombo($conn) {
			$rows = self::getArraysistemkuliah($conn);
			
			$data = array();
			foreach($rows as $row)
				$data[$row['sistemkuliah']] = $row['namasistem'].' '.$row['tipeprogram'];
			
			return $data;
		}
		
		function getArraykategoriukt($conn){
				$sql = "select kodekategoriukt, namakategoriukt from akademik.lv_kategoriukt order by namakategoriukt";
				$rs = $conn->Execute($sql);
				
				while($row = $rs->FetchRow()){
					$data[$row['kodekategoriukt']] = $row['namakategoriukt'];
					}
					
				return $data;
			}
		
		function getArrayStatusMhs($conn) {
			$sql = "select statusmhs, namastatus from akademik.lv_statusmhs order by statusmhs";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function sqlMhs($conn,$data){
				$sql = "select nim from akademik.ms_mahasiswa where (1=1)";
				if($data['jalurpenerimaan'] <> '')
					$sql .= " and jalurpenerimaan = '".$data['jalurpenerimaan']."'";
				if($data['kodeunit'] <> '')
					$sql .= " and kodeunit = '".$data['kodeunit']."'";
                                if($data['sistemkuliah'] <> '')
                                        $sql .= " and sistemkuliah = '".$data['sistemkuliah'].".";
				if($data['gelombang'] <> '')
					$sql .= " and gelombang = '".$data['gelombang']."'";
				return $sql;
			}
		function sqlpendaftar($conn,$data){
				$sql = "select 
					m.nopendaftar
			 		from pendaftaran.pd_pendaftar m 
					where (1=1) and (lulusujian <> '0' or lulusujian is not null)";
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and jalurpenerimaan = '".$data['jalurpenerimaan']."'";
			if($data['kodeunit'] <> '')
				$sql .= " and pilihanditerima = '".$data['kodeunit']."'";
			if($data['gelombang'] <> '')
				$sql .= " and idgelombang = '".$data['gelombang']."'";
			if($data['periodetagihan'] <> '')
				$sql .= " and (periodedaftar = '".$data['periodetagihan']."' or periodedaftar = '".substr($data['periodetagihan'],0,4)."')";
				return $sql;
			}
			
		function getArraymhsperwalian($conn,$data){
			$sql = "select 
					m.nim, 
					m.jalurpenerimaan,
					m.gelombang,
					m.sistemkuliah,
					m.periodemasuk,
					m.kodeunit,
					/*m.kodekategoriukt,*/
					p.statusmhs as statusperwalian,
					m.mhstransfer
			 		from akademik.ms_mahasiswa m 
					left join akademik.ak_perwalian p on m.nim = p.nim and p.periode = '".$data['periodesebelumnya']."' 
					where (1=1) and m.statusmhs in ('A','C','T')";
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and m.jalurpenerimaan = ".Query::escape($data['jalurpenerimaan']);
			if($data['kodeunit'] <> '')
				$sql .= " and m.kodeunit = ".Query::escape($data['kodeunit']);
			if($data['gelombang'] <> '')
				$sql .= " and m.gelombang = ".Query::escape($data['gelombang']);
			if($data['sistemkuliah'] <> '')
				$sql .= " and m.sistemkuliah = ".Query::escape($data['sistemkuliah']);
			if($data['nim'] <> '')
				$sql .= " and m.nim = ".Query::escape($data['nim']);
			return $conn->getArray($sql);
			}
		
		function generatePerwalian($conn,$periode,$data) {
			$eperiode = Query::escape($periode);
			
			$sql = "insert into akademik.ak_perwalian (nim,periode,statusmhs,prasyaratspp,t_updateuser,t_updatetime,t_updateip,t_updateact)
					select m.nim, $eperiode, 'T', 0, ".Query::logInsert().", '".Query::getAct('generate')."' from akademik.ms_mahasiswa m
					left join akademik.ak_perwalian w on w.nim = m.nim and w.periode = $eperiode
					where m.statusmhs in ('A','C','T') and m.periodemasuk <> $eperiode and w.nim is null";
			
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and m.jalurpenerimaan = ".Query::escape($data['jalurpenerimaan']);
			if($data['kodeunit'] <> '')
				$sql .= " and m.kodeunit = ".Query::escape($data['kodeunit']);
			if($data['gelombang'] <> '')
				$sql .= " and m.gelombang = ".Query::escape($data['gelombang']);
			if($data['sistemkuliah'] <> '')
				$sql .= " and m.sistemkuliah = ".Query::escape($data['sistemkuliah']);
			if($data['nim'] <> '')
				$sql .= " and m.nim = ".Query::escape($data['nim']);
			
			return $conn->Execute($sql);
		}
		
		function updateRecordPerwalian($conn,$record,$key,$status=false) {
			$err = Query::recUpdate($conn,$record,'akademik.ak_perwalian',$key);
		
				return $err;
		}
		function updateStatusMhs($conn,$record,$key) {
			$err = Query::recUpdate($conn,$record,'akademik.ms_mahasiswa',"nim = '$key' ");
		
				return $err;
		}
		
		function insertRecordPerwalian($conn,$record,$key,$status=false) {
			$err = Query::recInsert($conn,$record,'akademik.ak_perwalian',$key);
		
				return $err;
		}
		
		function isExistPerwalian($conn, $nim, $periode){
				return $conn->getOne("select 1 from akademik.ak_perwalian where periode = '$periode' and nim = '$nim'");
			}
			
		function getDatamhs($conn,$mhs){
				$sql = "select m.nim,m.nama,m.kodeunit,u.namaunit, coalesce(s.namasistem,'')||' '||coalesce(s.tipeprogram,'') as namasistem,
						m.jalurpenerimaan, m.gelombang,
						m.jalurpenerimaan||' '||coalesce(j.keterangan,'') as namajalur,m.periodemasuk,m.sistemkuliah, m.mhstransfer
						from akademik.ms_mahasiswa m 
						left join gate.ms_unit u on  m.kodeunit = u.kodeunit
						left join akademik.ak_sistem s on s.sistemkuliah = m.sistemkuliah
						left join akademik.lv_jalurpenerimaan j on j.jalurpenerimaan = m.jalurpenerimaan
						where nim = '$mhs'";
				return $conn->getRow($sql);
			}
		
		function getDatapendaftar($conn,$mhs){
				$sql = "select m.nopendaftar,m.nama,m.pilihanditerima,m.idgelombang as gelombang,m.pilihanditerima as kodeunit,u.namaunit,m.jalurpenerimaan, 
						coalesce(s.namasistem,'')||' '||coalesce(s.tipeprogram,'') as namasistem,m.sistemkuliah,
						m.jalurpenerimaan||' '||coalesce(j.keterangan,'') as namajalur,m.periodedaftar,m.mhstransfer
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
			
			if($unit <> ''){
				$a_unit=$conn->GetRow("select infoleft,inforight from gate.ms_unit where kodeunit='$unit'");
				$sql .= " and (u.infoleft >= '".$a_unit['infoleft']."' and u.inforight <= '".$a_unit['inforight']."')";
				//$sql .= " and m.kodeunit = '$unit'";
			}
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
					m.idgelombang as gelombang,
					m.periodedaftar,
					m.pilihanditerima
			 		from pendaftaran.pd_pendaftar m 
					where (1=1) and lulusujian = '-1'";
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and jalurpenerimaan = ".Query::escape($data['jalurpenerimaan']);
			if($data['kodeunit'] <> '')
				$sql .= " and pilihanditerima = ".Query::escape($data['kodeunit']);
			if($data['gelombang'] <> '')
				$sql .= " and idgelombang = ".Query::escape($data['gelombang']);
			if($data['periodetagihan'] <> '')
				$sql .= " and periodedaftar = ".Query::escape($data['periodetagihan']);
			if($data['sistemkuliah'] <> '')
				$sql .= " and sistemkuliah = ".Query::escape($data['sistemkuliah']);
				
			return $conn->getArray($sql);
			}
		
		function getListMahasiswaBaru($conn,$data) {
			$sql = "select 
					nim,
					jalurpenerimaan,
					sistemkuliah,
					gelombang,
					periodemasuk,
					kodeunit,
					jenisdata,
					mhstransfer
			 		from h2h.v_mhspendaftar
					where periodemasuk = ".Query::escape($data['periodetagihan']);
			if($data['jalurpenerimaan'] <> '')
				$sql .= " and jalurpenerimaan = ".Query::escape($data['jalurpenerimaan']);
			if($data['kodeunit'] <> '')
				$sql .= " and kodeunit = ".Query::escape($data['kodeunit']);
			if($data['gelombang'] <> '')
				$sql .= " and gelombang = ".Query::escape($data['gelombang']);
			if($data['sistemkuliah'] <> '')
				$sql .= " and sistemkuliah = ".Query::escape($data['sistemkuliah']);
			if(!empty($data['ispendaftar']))
				$sql .= " and jenisdata = 'pendaftar'";
			if($data['nim'] <> '')
				$sql .= " and nim = ".Query::escape($data['nim']);
				
			return $conn->getArray($sql);
			}
			
		function getDatakrs($conn,$r_nim,$r_periode){
			$sql = "select sum(m.sks) as sks,coalesce(m.tipekuliah,'A') as tipekuliah from akademik.ak_krs k
					left join akademik.ak_matakuliah m on m.thnkurikulum = k.thnkurikulum and m.kodemk = k.kodemk
					where k.periode = '$r_periode' and k.nim = '$r_nim' group by coalesce(m.tipekuliah,'A') 
					";
					
			$rs = $conn->Execute($sql);
			while($row = $rs->fetchRow()){
					$data[$row['tipekuliah']] = $row['sks'];
				}
				
			return $data;
			}
			
		function getDatakrsall($conn,$r_periode,$nim=null){
			$sql = " select sum(m.sks) as sks,nim from akademik.ak_krs k
					left join akademik.ak_matakuliah m on m.thnkurikulum = k.thnkurikulum and m.kodemk = k.kodemk
					where k.periode = '$r_periode'
					".(empty($nim) ? '' : " and k.nim = ".Query::escape($nim))."
					group by k.nim 
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
					$batas = 6;
					$bulan = array();
						$thn = substr($periode,0,4);

					for ($a=0; $a<=5; $a++){
						
						
						$bln = $row['bulanawal']++;
						$bln = str_pad($bln,2,'0',STR_PAD_LEFT);
						
						$bulan[$thn.$bln] = $thn.$bln;
						if ($row['bulanawal'] > '12'){
							$row['bulanawal'] = 1;
						$thn = substr($periode,0,4)+1;
						}
						$row['bulanawal'] = str_pad($row['bulanawal'],2,'0',STR_PAD_LEFT);
						
						}
					}
				return $bulan;
			}
		function getNama($conn, $nomor){
			$sql="select m.nama, m.semestermhs, m.kodeunit, u.namaunit, u2.namaunit as fakultas, r.kode_jenjang_studi as strata from akademik.ms_mahasiswa m 
			join gate.ms_unit u on m.kodeunit = u.kodeunit 
			join gate.ms_unit u2 on u2.kodeunit = u.kodeunitparent
			left join akademik.ak_prodi r on r.kodeunit = m.kodeunit
			where m.nim='$nomor'";
			$rs=$conn->getRow($sql);
			if (empty ($rs['nama']))
				{
					$pindahprodi = $conn->getOne("select pindahprodi from pendaftaran.pd_pendaftar where nopendaftar = '$nomor'");
					
						$sql=" select p.nama, m.semestermhs, u.kodeunit, u.namaunit, u2.namaunit as fakultas, r.kode_jenjang_studi as strata from pendaftaran.pd_pendaftar p 
							   left join akademik.ms_mahasiswa m on m.nim = p.nimpendaftar ";
						if ($pindahprodi)
							$sql.=" left join gate.ms_unit u on u.kodeunit = p.pindahprodi ";
						else
							$sql.=" left join gate.ms_unit u on u.kodeunit = p.pilihanditerima ";

						$sql.= " left join gate.ms_unit u2 on u2.kodeunit = u.kodeunitparent
								left join akademik.ak_prodi r on r.kodeunit = u.kodeunit
						where p.nopendaftar = '$nomor'";
							
							
					$rs = $conn->getRow($sql);
					}
			return $rs;
			}
			
			function findMahasiswa($conn,$str,$col='',$key='',$unit='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = 'nim';
			if(empty($key))
				$key = 'nim';
			
			
			$sql = "select $key, $col as label from akademik.ms_mahasiswa
					where lower($col::varchar) like '%$str%' ";
					
			if (!empty ($unit)){
				$sql_u = "select * from gate.ms_unit where kodeunit = '$unit'";
				$rs_u = $conn->Execute($sql_u)->FetchRow();
				$infoleft = $rs_u ['infoleft'];
				$inforight = $rs_u ['inforight'];

				$sql1 = "select kodeunit from gate.ms_unit where infoleft >=$infoleft and inforight<=$inforight";
				
			$sql.=" and kodeunit in ($sql1) ";

			}
			
			$sql.="order by nama";
			
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == 'nim')
					$t_key = $row['nim'];
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			return $data;
		}			
		function findPendaftar($conn,$str,$col='',$key='',$unit='') {
			global $conf;
			
			$str = strtolower($str);
			if(empty($col))
				$col = 'nopendaftar';
			if(empty($key))
				$key = 'nopendaftar';
			
			
			$sql = "select $key, $col as label from pendaftaran.pd_pendaftar
					where lower($col::varchar) like '%$str%' ";
					
			if (!empty ($unit)){
				$sql_u = "select * from gate.ms_unit where kodeunit = '$unit'";
				$rs_u = $conn->Execute($sql_u)->FetchRow();
				$infoleft = $rs_u ['infoleft'];
				$inforight = $rs_u ['inforight'];

				$sql1 = "select kodeunit from gate.ms_unit where infoleft >=$infoleft and inforight<=$inforight";
				
			$sql.=" and pilihanditerima in ($sql1) or pindahprodi in ($sql1) ";

			}
			
			$sql.="order by nama";
			
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow()) {
				if($key == 'nopendaftar')
					$t_key = $row['nopendaftar'];
				else
					$t_key = $row[$key];
				
				$data[] = array('key' => $t_key, 'label' => $row['label']);
			}
			return $data;
		}
		
		function getDataMhsPendaftar($conn,$nim) {
			$sql = "select * from h2h.".(self::isRolePMB() ? 'v_pendaftar' : 'v_mhspendaftarall')." where nim = ".Query::escape($nim);
			
			return $conn->GetRow($sql);
		}
		
		function findMhsPendaftarUnit($conn,$str,$col,$key,$jenis=null) {
			global $conf;
			
			$lr = Modul::getLeftRight();
			
			$sql = "select case when m.jenisdata = 'pendaftar' then 'p' else 'm' end||':'||m.nim as key,
					m.nim||' - '||m.nama as label from h2h.".(self::isRolePMB() ? 'v_pendaftar' : 'v_mhspendaftarall')." m
					join gate.ms_unit u on m.kodeunit = u.kodeunit and u.infoleft >= ".$lr['LEFT']." and u.inforight <= ".$lr['RIGHT']."
					where lower(m.nim||' - '||m.nama) like ".Query::escape('%'.$str.'%')."
					order by m.nama";
			$rs = $conn->SelectLimit($sql,$conf['row_autocomplete']);
			
			$data = array();
			while($row = $rs->FetchRow())
				$data[] = $row;
			
			return $data;
		}
		
		function sqlMhsPendaftar($filter) {
			$sql = "select * from h2h.v_mhspendaftar where (1=1)";
			if(!empty($filter['kodeunit']))
				$sql .= " and kodeunit = ".Query::escape($filter['kodeunit']);
			if(!empty($filter['jalurpenerimaan']))
				$sql .= " and jalurpenerimaan = ".Query::escape($filter['jalurpenerimaan']);
      if(!empty($filter['sistemkuliah']))
        $sql .= " and sistemkuliah = ".Query::escape($filter['sistemkuliah']);
			if(!empty($filter['gelombang']))
				$sql .= " and gelombang = ".Query::escape($filter['gelombang']);
			if(isset($filter['ispendaftar'])) {
				if(empty($filter['ispendaftar']))
					$sql .= " and jenisdata = 'mahasiswa'";
				else
					$sql .= " and jenisdata = 'pendaftar'";
			}
			if(!empty($filter['nim']))
				$sql .= " and nim = ".Query::escape($filter['nim']);
			
			// tambah join
			if(!empty($filter['periode']) and !empty($filter['isnonaktif'])) {
				$sql = "select x.* from (".$sql.") x
						join akademik.ak_perwalian w on w.nim = x.nim
						and w.periode = ".Query::escape($filter['periode'])."
						and w.statusmhs <> 'A'";
			}
			
			return $sql;
		}
		
		function infoPendaftaran($conn,$periode,$jalurpenerimaan,$gelombang){
			$sql ="select * from pendaftaran.pd_gelombangdaftar where periodedaftar = '$periode' and idgelombang = '$gelombang' and jalurpenerimaan = '$jalurpenerimaan' and isaktif = 't'";
			return $conn->getRow($sql);
		}
		
		function getPeriodeSekarang($conn) {
			$sql = "select periodesekarang from akademik.ms_setting where idsetting = 1";
			
			return $conn->GetOne($sql);
		}
		
		function isRolePMB($role=null) {
			if(empty($role))
				$role = Modul::getRole();
			
			return ($role == 'PMB' ? true : false);
		}
	}
?>
