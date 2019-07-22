<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mTarif extends mModel {
		const schema = 'h2h';
		const table = 'ke_tarif';
		const order = 'idtarif';
		const key = 'idtarif';
		const label = 'idtarif';
		
	// mendapatkan array data
		function getArraytarif($conn,$periode='',$jalur='',$kodeunit = '',$jenistagihan = '',$sistemkuliah='', $gelombang='') {
			$sql = "select t.* from ".static::table()." t
					join gate.ms_unit u on t.kodeunit=u.kodeunit
					where (1=1)";
			
			if($periode <> '')
				$sql .= " and t.periodetarif = '$periode'";
			if($jalur<>'') // sebelumnya dikomen
				$sql .= " and t.jalurpenerimaan = '$jalur'";
			if($kodeunit <> ''){
				$unit=$conn->GetRow("select infoleft,inforight from gate.ms_unit where kodeunit='$kodeunit'");
				$sql .= " and (u.infoleft >= '".$unit['infoleft']."' and u.inforight <= '".$unit['inforight']."')";
			}
			if($jenistagihan <> '')
				$sql .= " and t.jenistagihan = '".$jenistagihan."'";
			if($sistemkuliah <> '')
				$sql .= " and sistemkuliah = '".$sistemkuliah."'";
			if($gelombang <> '')
				$sql .= " and t.gelombang = '$gelombang'";
		
			return $conn->GetArray($sql);
		}
		//perlu dipertimbangkan lagi
		function getArraytarifUkt($conn,$periode='',$jalur='',$kodeunit = '',$jenistagihan = '',$nim) {
			$sql="select u.kodeunit, u.periode as periodetarif, u.kodekategoriukt, u.nilaitarif as nominaltarif, u.keterangan, m.nim, m.sistemkuliah, m.jalurpenerimaan from h2h.ke_tarifukt u 
					join akademik.ms_mahasiswa m on u.kodeunit=m.kodeunit and u.periode=m.periodemasuk and u.kodekategoriukt=m.kodekategoriukt
					where m.nim in($nim) and m.jalurpenerimaan='$jalur'";
			return $conn->GetArray($sql);
		}
		function getTarifuktpendaftar($conn,$nopendaftar) {
			$sql="select p.pilihanditerima, p.periodedaftar||1, p.kodekategoriukt, u.nilaitarif, u.nilaitarif as nominaltarif from pendaftaran.pd_pendaftar p left join
					h2h.ke_tarifukt u on p.pilihanditerima=u.kodeunit and p.periodedaftar||1 = u.periode and u.kodekategoriukt = p.kodekategoriukt
					where p.nopendaftar = '$nopendaftar'";
			return $conn->GetRow($sql);
		}
				
	// row tarif
	function getRowtarif($conn,$periode='',$jalur='', $kodeunit = '',$jenistagihan = '',$sistemkuliah='',$gelombang = '') {
			$sql = "select t.*, g.namajenistagihan from ".static::table()." t join ".static::table(lv_jenistagihan)." g on t.jenistagihan=g.jenistagihan where (1=1) ";
			if($periode <> '')
				$sql .= " and t.periodetarif = '$periode'";
		//	if($jalur<>'')
				$sql .= " and t.jalurpenerimaan = '$jalur'";
			if($kodeunit <> '')
				$sql .= " and t.kodeunit = '$kodeunit'";
			if($gelombang <> '')
				$sql .= " and t.gelombang = '$gelombang'";
			if($jenistagihan <> '')
				$sql .= " and t.jenistagihan = '".$jenistagihan."'";
			if($sistemkuliah <> '')
				$sql .= " and t.sistemkuliah = '".$sistemkuliah."'";
		
			return $conn->getArray($sql);
		}
	//get id tarif 
		function getIdtarif($conn,$data){
			$sql = " select idtarif from ".static::table()." where (1=1)";
			foreach($data as $i => $val)
				$sql .= " and ".$i." = '".$val."'";
			
			return $conn->GetOne($sql);
			
			}
	// delete
	function delete($conn,$data){
			$sql = " delete from ".static::table()." where (1=1)";
			foreach($data as $i => $val)
				$sql .= " and ".$i." = '".$val."'";
			
			 $conn->Execute($sql);
			return $err->errorNo;
			}
	//get tarif wisuda		
	function getTarifwisuda($conn,$periode){
			$sql = "select * from ".static::table()." where (1=1)";
			if($periode <> '')
				$sql .= " and periodetarif = '$periode'";
			return $conn->GetArray($sql);
		}
	function salinTagihan($conn, $jalur, $periode, $jalursalin, $periodesalin){
		echo $periode.'-'.$jalur.'-'.$periodesalin.'-'.$jalursalin;
		
		$conn->Execute("delete from h2h.ke_tarif where jalurpenerimaan = '$jalursalin' and periodetarif='$periodesalin'");
		
		$sql = "insert into h2h.ke_tarif (jenistagihan, periodetarif, jalurpenerimaan, sistemkuliah, kodeunit, nominaltarif, gelombang) 
				select jenistagihan, '".$periodesalin."'::text, '".$jalursalin."'::text, sistemkuliah, kodeunit, nominaltarif, gelombang from h2h.ke_tarif
				where periodetarif = '$periode' and jalurpenerimaan = '$jalur'";
		$ok = $conn->Execute($sql);
		
		if ($ok){
			return array(false, 'Salin Tarif Berhasil');
			}else{
			return array(true, 'Salin Tarif Gagal');				
			}
		
		}
		
	}
?>
