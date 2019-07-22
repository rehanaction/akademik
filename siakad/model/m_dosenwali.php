<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('mahasiswa'));
	
	class mDosenwali extends mMahasiswa {
		const label = 'dosen wali';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select m.*, u.namaunit, s.namastatus,
					akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					from ".self::table()." m
					join gate.ms_unit u on m.kodeunit = u.kodeunit
					join ".self::table('lv_statusmhs')." s on m.statusmhs = s.statusmhs
					join sdm.ms_pegawai p on m.nipdosenwali::text=p.idpegawai::text";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'dosen': return "nipdosenwali = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		
		// hapus berarti update
		function delete($conn,$key) {
			$record = array();
			$record['nipdosenwali'] = 'null';
			
			return self::updateRecord($conn,$record,$key,true);
		}
		
		// insert juga update
		function insert($conn,$nip,$nim1,$nim2='',$periode) {
	 
			$record = array();
			$record['nipdosenwali'] = CStr::cStrNull($nip);
			
			$where = "periode='$periode'";
			if($nim2 == '')
				$where .= "and nim = '$nim1'";
			else
				$where .= "and nim between '$nim1' and '$nim2'";
				
				//$where.=" and kodeunit='$kodeunit' ";
			
			Query::recUpdate($conn,$record,static::table('ak_perwalian'),$where,true);
		
			return static::updateStatus($conn);
		}
		function insertMhs($conn,$nip,$nim1,$nim2='',$periode) {
	 
			$record = array();
			$record['nipdosenwali'] = CStr::cStrNull($nip);
			
			if($nim2 == '')
				$where .= "nim = '$nim1' and periode='$periode'";
			else
				$where .= "nim between '$nim1' and '$nim2'";
				
				//$where.=" and kodeunit='$kodeunit' ";
			Query::recUpdate($conn,$record,static::table('ak_perwalian'),$where,true);
			self::insertKemhs($conn,$nip,$nim1,$nim2='');
		
			return static::updateStatus($conn);
		}

		function insertKemhs($conn,$nip,$nim1,$nim2='') {
	 
			$record = array();
			$record['nipdosenwali'] = CStr::cStrNull($nip);
			
			if($nim2 == '')
				$where .= "nim = '$nim1'";
			else
				$where .= "nim between '$nim1' and '$nim2'";
				
				//$where.=" and kodeunit='$kodeunit' ";
			Query::recUpdate($conn,$record,static::table('ms_mahasiswa'),$where,true);
			//self::insertKemhs($conn,$nip,$nim1,$nim2='');
		
			return static::updateStatus($conn);
		}

		// insert juga update
		function insertPerKelas($conn,$nip,$kelas,$jurusan,$angkatan,$periode) {
			$record = array();
			$record['nipdosenwali'] = CStr::cStrNull($nip);
			$where="periode='$periode' and nim in (select nim from ".static::table()." where sistemkuliah = '$kelas' and substr(periodemasuk,1,4)='$angkatan' and kodeunit='$jurusan')";			
			$update=Query::recUpdate($conn,$record,static::table('ak_perwalian'),$where,true);
		
			return static::updateStatus($conn);
		}
		function setWaldosen($conn,$idpegawai)
		{
			$sql = "update sdm.ms_pegawai set isdosenwali=-1 where idpegawai=$idpegawai";
			$ok = $conn->Execute($sql);
			return $ok;
		}
		function generateWali($conn){
			$pegawai=$conn->GetArray("select idpegawai from sdm.ms_pegawai where isdosen=-1");
			$jum_mhs=$conn->GetOne("select sum(1) as jum from akademik.ms_mahasiswa where nipdosenwali is null or nipdosenwali=''");
			$jum_kel=$jum_mhs/count($pegawai);
			$jum_kel=ceil($jum_kel);
			
			foreach($pegawai as $key=>$row){
				$mhs=$conn->GetArray("select nim from akademik.ms_mahasiswa where nipdosenwali is null or nipdosenwali='' limit $jum_kel offset $key");
				$in_sql="";
				foreach($mhs as $key_mhs=>$row_mhs){
					if($key_mhs>=0 and $key_mhs!= (count($mhs)-1))
						$in_sql.="'".$row_mhs['nim']."',";
					else
						$in_sql.="'".$row_mhs['nim']."'";
				}
				echo $in_sql."<br>";
				$record = array();
				$record['nipdosenwali'] = CStr::cStrNull($row['idpegawai']);
				if($in_sql!='')
				Query::recUpdate($conn,$record,static::table(),"nim in ({$in_sql})",true);
			}
			return static::updateStatus($conn);
		}


		function getDataDosen($conn){
			$sql = "select mp.idpegawai,akademik.f_namalengkap(mp.gelardepan,mp.namadepan,mp.namatengah,mp.namabelakang,mp.gelarbelakang) as namadosen,mu.kodeunit,mp.isdosenwali from sdm.ms_pegawai mp join sdm.ms_unit mu on (mu.idunit=mp.idunit) where isdosen='-1' and idstatusaktif='AA'";
			return $conn->getArray($sql);
		}
		
	}
?>
