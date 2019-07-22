<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mLokasi extends mModel {
		const schema = 'pendaftaran';
		const table = 'lv_lokasiujian';
		const order = 'kodelokasiujian';
		const key = 'kodelokasiujian';
		const label = 'lokasi ujian';
		
		function data($conn, $fperiode, $fjalur, $fgelombang){
			$sql="
				SELECT DISTINCT lokasiujian
				FROM pendaftaran.pd_pendaftar 
				WHERE periodedaftar='$fperiode'
				AND jalurpenerimaan='$fjalur'
				AND idgelombang='$fgelombang'
				";
			return $conn->SelectLimit($sql);
		}
		function getkapasitasRuang($conn, $kodelokasi){
			$sql="
				SELECT kapasitaslokasi
				FROM pendaftaran.lv_lokasiujian
				WHERE kodelokasiujian='$kodelokasi'
				";
			$result=$conn->SelectLimit($sql,1);
			return $result->FetchRow();
		}
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periodedaftar = '$key'";
				case 'jalur': return "jalurpenerimaan = '$key'";
				case 'gelombang':return "idgelombang = '$key'";
				
			}
		}
		function getWaktuUjian($conn, $jalurpenerimaan){
			$jalur=explode("-",$jalurpenerimaan);
			$sql="
				SELECT tglujian FROM pendaftaran.pd_gelombangdaftar WHERE jalurpenerimaan='$jalur[0]' AND idgelombang='$jalur[1]' AND periodedaftar='$jalur[2]'
				";
			$ok=$conn->Execute($sql);
			return $ok->FetchRow();
		}
		function getjumlahpeserta($conn, $jalurpenerimaan, $kodelokasi){
			$jalur=explode("-",$jalurpenerimaan);
			$sql="
				SELECT COUNT(nopendaftar) FROM pendaftaran.pd_pendaftar WHERE periodedaftar='$jalur[2]' AND idgelombang='$jalur[1]' AND jalurpenerimaan='$jalur[0]' AND lokasiujian='$kodelokasi'
				";
			$ok=$conn->Execute($sql);
			return $ok->FetchRow();
		}
		function getpesertaperruang($conn, $jalurpenerimaan, $kodelokasi){
			$jalur=explode("-",$jalurpenerimaan);
			$sql="
				SELECT nopendaftar, nama, pilihan1, pilihan2, pilihan3 FROM pendaftaran.pd_pendaftar WHERE periodedaftar='$jalur[2]' AND idgelombang='$jalur[1]' AND jalurpenerimaan='$jalur[0]' AND lokasiujian='$kodelokasi'
				";
			return $conn->Execute($sql);
		}
		function getRuang($conn){
			$sql="
				SELECT koderuang,koderuang||'-'||coalesce(lokasi,'') as ruang FROM akademik.ms_ruang ORDER BY koderuang
				";
			return Query::arrQuery($conn,$sql);
		}
		function getKota($conn){
			$sql="
				SELECT kodekota,namakota FROM akademik.ms_kota ORDER BY namakota
				";
			return Query::arrQuery($conn,$sql);
		}
		function getGedung($conn){
			$sql="
				SELECT DISTINCT keterangan as gedung FROM akademik.ms_ruang ORDER BY keterangan
				";
			return Query::arrQuery($conn,$sql);
		}
		function getLantai($conn){
			$sql="
				SELECT DISTINCT lantai FROM akademik.ms_ruang ORDER BY lantai
				";
			return Query::arrQuery($conn,$sql);
		}
			
		function getDataRuang($conn, $koderuang){
			$sql="
				SELECT * FROM akademik.ms_ruang WHERE koderuang='$koderuang'
				";
			return Query::arrQuery($conn,$sql);
		}
		function setLokasiUrut($conn, $periode, $jalur, $gelombang){
			
			//masih urut
			$sql="
				SELECT *
				FROM pendaftaran.pd_pendaftar 
				WHERE periodedaftar='$periode'
				AND jalurpenerimaan='$jalur'
				AND idgelombang='$gelombang'
				";
			$peserta= $conn->SelectLimit($sql);
			$jp=$jalur."-".$gelombang."-".$periode;
			$sql1="
				SELECT *
				FROM pendaftaran.lv_lokasiujian 
				WHERE aktif=true
				AND jalurpenerimaan='$jp'
				";
			$ruangan = $conn->SelectLimit($sql1);
			$tampung = $ruangan->RecordCount();
			$status=FALSE;
			while($data = $peserta -> FetchRow()){
				$i=0;
				
				while($i<$tampung){
					$ruangan = $conn->SelectLimit($sql1);
					for($j=0;$j<=$i;$j++){
						$ruang = $ruangan->FetchRow();
					}
					if(!self::isFull($conn, $ruang['kodelokasiujian'])){
						
						$nopendaftar=$data['nopendaftar'];
						$record=array();
						$record['lokasiujian']=$ruang['kodelokasiujian'];
						
						$table="pendaftaran.pd_pendaftar";
						
						$updateSQL = $conn->AutoExecute($table,$record,'UPDATE', "nopendaftar='$nopendaftar'");
						
						if($updateSQL){
							$status=TRUE;
						}else $status=FALSE;
						break;
					}
					$i++;
				}
				
			}
			if($status) self::setRandLog($conn, $periode,$jalur, $gelombang);
			
		}
		
		function setLokasiAcak($conn, $periode, $jalur, $gelombang){
			$sql="
				SELECT *
				FROM pendaftaran.pd_pendaftar 
				WHERE periodedaftar='$periode'
				AND jalurpenerimaan='$jalur'
				AND idgelombang='$gelombang'
				";
			$peserta= $conn->SelectLimit($sql);
			
			$jp=$jalur."-".$gelombang."-".$periode;
			$sql1="
				SELECT *
				FROM pendaftaran.lv_lokasiujian 
				WHERE aktif=true
				AND jalurpenerimaan='$jp'
				";
			$ruangan = $conn->SelectLimit($sql1);
			$status=FALSE;
			$tampung=$ruangan->RecordCount();
			
			while ($data = $peserta-> FetchRow()){
				$lokasiujian=rand(1,$tampung);
				$lokasiujian=str_pad($lokasiujian,2,'0',STR_PAD_LEFT);
				
				//echo "penuh? ".self::isFull($conn, $lokasiujian)."<br>";
				
				while(self::isFull($conn, $lokasiujian)){
					$lokasiujian=rand(1,$ruangan->RecordCount());
					$lokasiujian=str_pad($lokasiujian,2,'0',STR_PAD_LEFT);
				}
				if(!self::isFull($conn, $lokasiujian)){
					$nopendaftar=$data['nopendaftar'];
					$record=array();
					$record['lokasiujian']=$lokasiujian;
						
					$table="pendaftaran.pd_pendaftar";
						
					$updateSQL = $conn->AutoExecute($table,$record,'UPDATE', "nopendaftar='$nopendaftar'");
					
					if($updateSQL){
						$status=TRUE;
					}else $status=FALSE;
				}
			}
			if($status) self::setRandLog($conn, $periode,$jalur, $gelombang);
			
		}
		
		function isFull($conn,$kodelokasiujian){
			$sql="
				SELECT COUNT(nopendaftar) as nopendaftar FROM pendaftaran.pd_pendaftar WHERE lokasiujian='$kodelokasiujian'
				";
			$jumlah=$conn->Execute($sql);
			$jumlah=$jumlah->FetchRow();
			$jumlah=$jumlah['nopendaftar'];
			
			$sql2="
				SELECT kapasitaslokasi FROM pendaftaran.lv_lokasiujian WHERE kodelokasiujian='$kodelokasiujian'
				";
			$kapasitas=$conn->SelectLimit($sql2);
			$kapasitas=$kapasitas->fetchRow();
			$kapasitas=$kapasitas['kapasitaslokasi'];
			
			if($jumlah<$kapasitas){
				return false;
			}else{
				return true;
			}
		}
		
		function setRandLog($conn, $periode, $jalur, $gelombang){
			$record=array();
			$record['periodedaftar']	= $periode;
			$record['idgelombang']		= $gelombang;
			$record['jalurpenerimaan']	= $jalur;
			$record['t_updatetime']		= date('Y-m-d H:m:s');
			$record['t_updateuser']		= Modul::getUserID();
				
			$col = "select * from pendaftaran.pd_randlog where idrandlog='-1'";	     
			$col=$conn->Execute($col);
			$insertSQL = $conn->GetInsertSQL($col,$record);
			$sql = $conn->Execute($insertSQL);
				
			if($sql==true){
				return true;
				
			}else{
				return false;
				
			}
		}
		
		function isRand($conn, $periode, $gelombnag, $jalur){
			$sql="SELECT * FROM pendaftaran.pd_randlog WHERE periodedaftar='$periode' AND idgelombang='$gelombnag' AND jalurpenerimaan='$jalur'";
			
			$ok=$conn->SelectLimit($sql);
			if($ok->RecordCount()==0) return false;
			else return true;
		}
		
		function updatePeserta($conn,$idjadwal){
			$cekPeserta = $conn->GetOne("select jumlahpeserta from pendaftaran.pd_jadwal where idjadwal='$idjadwal'");
			$jmlAkhir = $cekPeserta+1;
			$rs_update = $conn->Execute("update pendaftaran.pd_jadwal set jumlahpeserta='$jmlAkhir' where idjadwal='$idjadwal'");
		}
		
		function cekKuota($conn,$idjadwal){
			$cekPeserta = $conn->GetRow("select kuota,jumlahpeserta from pendaftaran.pd_jadwal where idjadwal='$idjadwal'");
			if($cekPeserta['jumlahpeserta'] < $cekPeserta['kuota']){//masih bisa nambah peserta
				return true;
			}else
				return false;
		}
		
		function hitungPeserta($conn,$idjadwal){
			$jmlPeserta = $conn->GetOne("select count(*) from pendaftaran.pd_pendaftar where idjadwaldetail='$idjadwal'");
			$jmlkapasitas = $conn->GetRow("select j.*,r.kapasitaslokasi from pendaftaran.pd_jadwaldetail j left join pendaftaran.lv_lokasiujian r on r.namalokasi=j.koderuang
									where idjadwaldetail='$idjadwal' order by idjadwaldetail");
			if($jmlPeserta < $jmlkapasitas['kapasitaslokasi'])
				return true;
			else
				return false;
		}
	}
?>
