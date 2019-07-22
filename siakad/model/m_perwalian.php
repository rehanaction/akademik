<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPerwalian extends mModel {
		const schema = 'akademik';
		const table = 'ak_perwalian';
		const order = 'periode desc';
		const key = 'nim,periode';
		const label = 'perwalian';
		
		// mendapatkan kueri list
		function listQuery() {
			global $r_key;
			
			$sql = "select p.nim, p.periode, ".static::schema.".f_namaperiode(p.periode) as namaperiode, s.namastatus,
					p.nipdosenwali, akademik.f_namalengkap(g.gelardepan,g.namadepan,g.namatengah,g.namabelakang,g.gelarbelakang)||coalesce(' ('||p.nipdosenwali||')','') as dosenwali, p.tglsk, p.nosk, p.alasancuti
					from ".static::table()." p
					left join ".static::table('lv_statusmhs')." s on p.statusmhs = s.statusmhs
					left join sdm.ms_pegawai g on  p.nipdosenwali = g.idpegawai::text
					where p.nim = '$r_key'";
			
			return $sql;
		}
		
		// mendapatkan data list sederhana
		function getList($conn) {
			global $r_key;
			
			$sql = "select * from ".static::table()." where nim = '$r_key' order by periode";
			
			return $conn->GetArray($sql);
		}
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				case 'periode':
					return " p.periode='$key'";
				default:
					return parent::getListFilter($col,$key);
			}
		}
		function getArrayListFilterCol() {
			$data['angkatan'] = 'substring(m.periodemasuk,1,4)';
			$data['unit'] = 'm.kodeunit';
			return $data;
		}
		// mendapatkan pembayaran spp mahasiswa
		function getDataSudahBayar($conn,$periode,$kodeunit,$periodedaftar='',$nim='') {
			$sql = "select m.nim, m.nama, m.semestermhs from ".static::table('ms_mahasiswa')." m
					join ".static::table('ak_perwalian')." p on p.nim = m.nim and p.periode = '$periode' and p.prasyaratspp = -1
					where m.kodeunit = '$kodeunit'";
			if(!empty($periodedaftar))
				$sql .= " and m.periodemasuk = '$periodedaftar'";
			if(!empty($nim))
				$sql .= " and m.nim = '$nim'";
			$sql .= " order by m.nim";
			$rs = $conn->Execute($sql);
			
			$a_bayar = array();
			while($row = $rs->FetchRow())
				$a_bayar[] = $row;
			
			return $a_bayar;
		}
		function getDataMhs($conn,$kodeunit,$periodedaftar='',$sistemkuliah='') {
			$sql = "select m.nim, m.nama, m.semestermhs from ".static::table('ms_mahasiswa')." m
					where m.kodeunit = '$kodeunit'";
			if(!empty($periodedaftar))
				$sql .= " and m.periodemasuk = '$periodedaftar'";
			if(!empty($sistemkuliah))
				$sql .= " and m.sistemkuliah = '$sistemkuliah'";
			$sql .= " order by m.nim";
			$rs = $conn->Execute($sql);
			
			$a_bayar = array();
			while($row = $rs->FetchRow())
				$a_bayar[] = $row;
			
			return $a_bayar;
		}
		
		function getDataSudahBayarNPM($conn,$periode,$nim) {
			$sql = "select m.nim, p.prasyaratspp from ".static::table('ms_mahasiswa')." m
					join ".static::table('ak_perwalian')." p on p.nim = m.nim and p.periode = '$periode' and p.prasyaratspp = -1
					where m.nim in ('".(is_array($nim) ? implode("','",$nim) : $nim)."')";
			$rs = $conn->Execute($sql);
			
			$a_bayar = array();
			while($row = $rs->FetchRow()) {
				if(!empty($row['prasyaratspp']))
					$a_bayar[trim($row['nim'])] = true;
			}
			
			return $a_bayar;
		}
		
		function getDataBayar($conn,$periode,$kodeunit='') {
			$sql = "select m.nim, m.nama, m.statusmhs, m.kodeunit, v.prasyaratspp as bayar from ".static::table('ms_mahasiswa')." m
					join gate.ms_unit u on m.kodeunit = u.kodeunit
					join gate.ms_unit up on u.infoleft >= up.infoleft and u.inforight <= up.inforight ".
					(strval($kodeunit) == '' ? '' : "and up.kodeunit = '$kodeunit'")."
					left join akademik.ak_perwalian v on v.nim = m.nim and v.periode = '$periode' and v.prasyaratspp = -1
					where m.periodemasuk <= '$periode' and (m.statusmhs not in ('L','O','T','W') or v.prasyaratspp = -1) order by m.nim";
			$rs = $conn->Execute($sql);
			
			$a_bayar = array();
			$a_belumbayar = array();
			while($row = $rs->FetchRow()) {
				if(empty($row['bayar']))
					$a_belumbayar[] = $row;
				else
					$a_bayar[] = $row;
			}
			
			return array($a_bayar,$a_belumbayar);
		}
		
		// mendapatkan data status spp
		function getStatusSPP($conn,$nim) {
			$sql = "select p.nim, p.periode, s.namastatus, p.nipdosenwali, akademik.f_namalengkap(g.gelardepan,g.namadepan,g.namatengah,g.namabelakang,g.gelarbelakang) as nama, p.frsterisi, p.prasyaratspp
					from ".static::table()." p
					left join ".static::table('lv_statusmhs')." s on p.statusmhs = s.statusmhs
					left join sdm.ms_pegawai g on p.nipdosenwali = g.idpegawai::text
					where p.nim = '$nim' order by p.periode desc";
			
			return $conn->GetArray($sql);
		}

		function getStatusUjian($conn,$nim) {
			$sql = "select isuts, isuas
					from ".static::table()."
					where nim = '$nim'";
			
			return $conn->GetArray($sql);
		}
		
		// mendapatkan data perwalian mahasiswa
		function getDataMahasiswa($conn,$nim) {
			$sql = "select periode, semmhs, ipk, ips, sks
					from ".static::table()."
					where nim = '$nim' order by periode";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['semmhs']] = $row;
			
			return $a_data;
		}
		
		function infoProgpend($conn,$nim){
			$jenjang=$conn->GetOne("select p.kode_jenjang_studi from ".static::table('ak_prodi')." p 
							join ".static::table('ms_mahasiswa')." m using (kodeunit) where m.nim='$nim'");
			$info=$conn->GetRow("select lamastudi,maxcuti 
							from ".static::table('ms_programpend')." where programpend='$jenjang'");
			
			return $info;
		}
		function infoMhs($conn,$nim){
			$info=$conn->GetRow("select count(1) as jum_smt,count(case when statusmhs='C' then 1 end) as jum_cuti 
							from ".static::table('ak_perwalian')."
							where nim='$nim'");
			return $info;
		}
		function genPerwalian($conn,$periode){
			$mhs=$conn->GetArray("select nim,nipdosenwali,ipk,ipslalu,periodemasuk from ".static::table('ms_mahasiswa')." 
								where sistemkuliah='R' and nim not in(select nim from ".static::table('ak_perwalian')." where periode='$periode') 
								and statusmhs not in ('L','W','O','U')");
			$i=0;
			$sukses=0;
			$nim='';
			$col = $conn->SelectLimit("select * from ".static::table(),1);
			$ok = true;
			$conn->BeginTrans();
			foreach($mhs as $row){
				$record=array();
				$record['nim']=$row['nim'];
				$record['periode']=$periode;
				$record['statusmhs']='T';
				$record['prasyaratspp']=0;
				
				$sql = $conn->GetInsertSQL($col,$record);
				$conn->Execute($sql);
				list($p_posterr,$p_postmsg)=static::insertStatus($conn);
				if($p_posterr){
					$nim=$record['nim'];
					$ok = false;
					break;
				}else
					$sukses++;
				
				$i++;	
			}
			$conn->CommitTrans($ok);
			
			if(!$ok)
				return array(true,'Terjadi Malsalah pada baris '.$i.' NIM '.$nim);
			else
				return array(false,'Berhasil membuat data perwalian mahasiswa periode '.Akademik::getNamaPeriode($periode).' Sebanyak '.$sukses.' Mahasiswa');
			
			
		}
		function listCekal($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter){
			$sql="select m.nim,m.nama,m.semestermhs,p.prasyaratspp,p.statusmhs,p.periode,p.cekalakad,p.keterangan,p.isuts,p.isuas
				from ".static::table('ms_mahasiswa')." m
				join ".static::table()." p using (nim)
				left join gate.ms_unit u on m.kodeunit = u.kodeunit
				left join gate.ms_unit up on u.kodeunitparent = up.kodeunit";
			$data=static::getPagerData($conn,$a_kolom,$r_row,$r_page,$r_sort,$a_filter,$sql);
			return $data;
		}
		function getMhsPaket($conn,$kodeunit,$periodedaftar='',$sistemkuliah='',$periodekrs=''){
			$sql = "select m.nim, m.nama, m.semestermhs from ".static::table('ms_mahasiswa')." m
					where 1=1";
			if(!empty($periodedaftar))
				$sql .= " and m.periodemasuk = '$periodedaftar'";
			if(!empty($sistemkuliah))
				$sql .= " and m.sistemkuliah = '$sistemkuliah'";
			if(!empty($periodekrs))
				$sql .= " and m.nim not in (select nim from ".static::table('ak_krs')." where periode='$periodekrs' and kodeunit = '$kodeunit')";
			$sql .= " order by m.nim asc";
			$rs = $conn->Execute($sql);
			
			$a_bayar = array();
			while($row = $rs->FetchRow())
				$a_bayar[] = $row;
			
			return $a_bayar;
		}
	}
?>
