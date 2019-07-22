<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mYudisium extends mModel {
		const schema = 'akademik';
		const table = 'ak_yudisium';
		const order = 'y.nim';
		const key = 'nim,idyudisium';
		const label = 'yudisium';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select y.idyudisium, y.nim, m.kodeunit, m.nama, m.notranskrip, m.noijasah from ".static::table()." y
					join ".static::table('ms_mahasiswa')." m on m.nim = y.nim
					left join gate.ms_unit u on m.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periodewisuda': return "y.idyudisium = '$key'";
				case 'unit':
					global $conn;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		
		// mendapatkan kueri data
		function dataQuery($key) {
			list($nim,$periodewisuda) = explode('|',$key);
			
			$sql = "select m.nim, y.idyudisium, m.nama, m.kodeunit, m.noijasah, m.notranskrip
					from ".static::table('ms_mahasiswa')." m left join ".static::table()." y
					on m.nim = y.nim and y.nim = '$nim' and y.idyudisium = '$periodewisuda'
					where m.nim = '$nim'";
			
			return $sql;
		}
		
		// insert record
		function insertRecord($conn,$record,$status=false) {
			$t_nim = $record['nim'];
			
			// cek status mahasiswa
			$sql = "select nama, statusmhs from ".static::table('ms_mahasiswa')." where nim = '$t_nim'";
			$a_mhs = $conn->GetRow($sql);
			
			if($a_mhs['statusmhs'] != 'A') {
				$err = -1;
				if($status)
					return array($err,'Status <strong>'.$t_nim.' - '.$a_mhs['nama'].'</strong> tidak aktif');
				else
					return $err;
			}
			
			// cek syarat yudisium mahasiswa
			$sql = "select 1 from ".static::table('ak_syaratyudisium')." s
					left join ".static::table('ak_ceksyaratyudisium')." m on
						m.idsyaratyudisium = s.idsyaratyudisium and m.nim = '$t_nim'
					where s.idyudisium = '".$record['idyudisium']."' and m.nim is null";
			$iskurang = $conn->GetOne($sql);
			
			if(!empty($iskurang)) {
				$err = -1;
				if($status)
					return array($err,'Peserta yudisium belum memenuhi syarat, untuk mengeceknya klik <u class="ULink" onclick="goOpen(\'set_syaratyudisiummhs&key='.$record['nim'].'\')">di sini</u>');
				else
					return $err;
			}
			
			$err = Query::recInsert($conn,$record,static::table());
				
			if($status)
				return static::insertStatus($conn);
			else
				return $err;
		}
		
		// update record
		function updateCRecord($conn,$kolom,$record,$key) {
			$conn->BeginTrans();
			
			// unset record
			if(!empty($kolom))
				foreach($kolom as $datakolom)
					if($datakolom['readonly'])
						unset($record[$datakolom['kolom']]);
			
			list($periodewisuda,$npm) = explode('|',$key);
			
			$err = Query::recUpdate($conn,$record,static::table(),static::getCondition($key));
			if(!$err)
				$err = Query::recUpdate($conn,$record,static::table('ms_mahasiswa'),"nim = '$npm'");
			
			if(!$err)
				$key = static::getRecordKey($key,$record);
			
			$ok = Query::isOK($err);
			$conn->CommitTrans($ok);
			
			return static::updateStatus($conn,$kolom);
		}
		
		// dapatkan data terakhir mahasiswa
		function getDataAkhirMahasiswa($conn,$nim) {
			$sql = "select m.nama, m.alamat, m.datavalid, t.idta, t.judulta from ".static::table('ms_mahasiswa')." m
					left join ".static::table('ak_ta')." t on t.nim = m.nim
					where m.nim = '$nim' order by (case when t.statusta = 'L' then 0 else 1 end), tglmulai desc";
			
			return $conn->GetRow($sql);
		}
		
		// simpan data terakhir mahasiswa
		function saveDataAkhirMahasiswa($conn,$record,$nim) {
			// data mahasiswa
			$recmhs = array();
			$recmhs['nama'] = $record['nama'];
			$recmhs['alamat'] = $record['alamat'];
			if(isset($record['datavalid']))
				$recmhs['datavalid'] = $record['datavalid'];
			
			$err = Query::recUpdate($conn,$recmhs,static::table('ms_mahasiswa'),"nim = '$nim'");
			
			// data ta
			if(empty($err)) {
				$recta = array();
				$recta['judulta'] = $record['judulta'];
				
				$err = Query::recUpdate($conn,$recta,static::table('ak_ta'),"idta = '".$record['idta']."'");
			}
			
			return $err;
		}
		
		// set nomor ijazah
		function setNoIjazah($conn,$periodewisuda,$kodeunit,$noijazah='',$nofakultas='') {
			$ok = true;
			$conn->BeginTrans();
			
			$sql = "select a.nim, pr.kode_jenjang_studi as programpend, u.kodeunitparent from ".static::table()." a
					join ".static::table('ms_mahasiswa')." b on b.nim = a.nim
					join gate.ms_unit u on b.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on b.kodeunit = pr.kodeunit
					join gate.ms_unit up on u.infoleft >= up.infoleft and u.inforight <= up.inforight and up.kodeunit = '$kodeunit'
					where a.idyudisium = '$periodewisuda'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()) {
				$record = array();
				
				if(!empty($noijazah)) {
					$t_strno = str_pad($noijazah,6,'0',STR_PAD_LEFT);
					
					if($row['programpend'] == 'D2')
						$t_strno = 'D2/'.$t_strno;
					else
						$t_strno = 'S1/'.$t_strno;
					
					$record['noijasah'] = 'IN.'.$t_strno;
				}
				if(!empty($nofakultas)) {
					$t_strno = str_pad($nofakultas,4,'0',STR_PAD_LEFT);
					
					$record['notranskrip'] = $t_strno.'/'.$row['kodeunitparent'].'/'.date('Y');
				}
				if (!empty ($noijazah))
				$noijazah++;
				
				if (!empty ($nofakultas))
				$nofakultas++;
				 
				Query::recUpdate($conn,$record,static::table('ms_mahasiswa'),"nim = '".$row['nim']."'");
				list($err,$msg) = static::updateStatus($conn,null,'no ijazah','ms_mahasiswa');
				
				if (!$err)
				list($err,$msg) = mYudisium::updateRecord($conn,$record,$row['nim'].'|'.$periodewisuda);
				
				if($err) {
					$ok = false;
					break;
				}
			}
			
			$conn->CommitTrans($ok);
			
			if(empty($msg)) {
				$err = 0;
				$msg = 'Pengubahan no ijazah berhasil';
			}
			
			return array($err,$msg);
		}
	}
?>
