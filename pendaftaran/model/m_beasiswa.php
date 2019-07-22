<?php
	// model beasiswa
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mSumberBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'lv_sumberbeasiswa';
		const order = 'kodesumberbeasiswa';
		const key = 'kodesumberbeasiswa';
		const label = 'sumber beasiswa';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select *
					from ".static::table()." s ";
			
			return $sql;
		}
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select s.*, k.kodepropinsi
					from ".static::table()." s
					left join akademik.ms_kota k on k.kodekota = s.kodekota
					where ".static::getCondition($key);
			
			return $sql;
		}
		
		// mendapatkan array data
		function getArray($conn) {
			$sql = "select * from ".static::table()." order by ".static::order;
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getArrayNama($conn) {
			$sql = "select namasumber from ".static::table()." order by namasumber";
			
			return Query::arrQuery($conn,$sql);
		}
	}
	
	class mBeasiswa extends mModel {
		const schema = 'kemahasiswaan';
		const table = 'ms_beasiswa';
		const sequence = 'beasiswa_idbeasiswa_seq';
		const order = 'periode desc,idbeasiswa desc';
		const key = 'idbeasiswa';
		const label = 'beasiswa';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select b.*,
					j.namajenisbeasiswa  as jenis, 
				    s.namasumberbeasiswa as sumber 
					from ".static::table()." b 
					left join kemahasiswaan.lv_sumberbeasiswa s 
						  on s.kodesumberbeasiswa = b.kodesumberbeasiswa 
				    left join kemahasiswaan.lv_jenisbeasiswa j 
						  on j.idjenisbeasiswa = b.idjenisbeasiswa ";
			
			return $sql;
		}
		
		// mendapatkan kueri detail
		function dataQuery($key) {
			$sql = "select b.*
					from ".static::table()." b
					where ".static::getCondition($key);
			
			return $sql;
		}

		// get list beasiswa
		function getListBea($conn,$filter) {
			$sql = "select *, 
					       j.namajenisbeasiswa  as jenis, 
					       s.namasumberbeasiswa as sumber 
					from ".static::table()." b  
					       left join kemahasiswaan.lv_sumberbeasiswa s 
					              on s.kodesumberbeasiswa = b.kodesumberbeasiswa 
					       left join kemahasiswaan.lv_jenisbeasiswa j 
					              on j.idjenisbeasiswa = b.idjenisbeasiswa 
					where  ( Lower(( j.namajenisbeasiswa ) :: varchar) like '%".$filter."%'  
					          or Lower(( s.namasumberbeasiswa ) :: varchar) like '%".$filter."%'  
					          or Lower(( periode ) :: varchar) like '%".$filter."%'  ) 
					order  by idbeasiswa ";
			
			return $conn->GetArray($sql);
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'syarat':
					$info['table'] = 'lv_syaratbeasiswa';
					$info['key'] = 'idbeasiswa,kodesyaratbeasiswa';
					$info['label'] = 'syarat beasiswa';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// penerima beasiswa
		function getSyarat($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('lv_syaratbeasiswa')." p
					where idbeasiswa = '$key' order by kodesyaratbeasiswa";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		

		// mendapatkan array data
		function getArrayNama($conn) {
			$sql = "select idbeasiswa,namabeasiswa from ".static::table()." 
					where pesertabeasiswa = 'mb'
					order by namabeasiswa";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan array data
		function getArrayByPeriode($conn,$periode) {
			$sql = "select b.idbeasiswa, b.periode||' - '||namajenisbeasiswa as beasiswa
					from ".static::table()." b
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa)
					order by idbeasiswa ";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// kategori beasiswa
		function getArrayKategori($conn) {
			$sql = "select kodekategori, namakategori from h2h.lv_kategoribeasiswa order by kodekategori";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getIdByPd($conn,$nopendaftar){
			$sql = "select idbeasiswa 
					from ".static::table('mw_pengajuanbeasiswapendaftar')." p 
					where nopendaftar = '$nopendaftar' ";
			return $conn->GetOne($sql);
		}
		
		function getPrestasi($conn,$key){
			$sql = "select pb.*,namajenisprestasi,namatingkatprestasi,namakategoriprestasi,namaprestasi,tempat,tahun
					from kemahasiswaan.mw_prestasibeasiswamaba pb 
					left join ".static::table('lv_jenisprestasi')." jp on pb.kodejenisprestasi = jp.kodejenisprestasi 
					left join ".static::table('lv_tingkatprestasi')." tp on pb.kodetingkatprestasi = tp.kodetingkatprestasi 
					left join ".static::table('lv_kategoriprestasi')." kp on pb.kodekategoriprestasi = kp.kodekategoriprestasi 
					where pb.idpengajuanbeasiswa = $key ";					 
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function getOrganisasi($conn,$key){
			$sql = "select pb.*
					from kemahasiswaan.mw_organisasibeasiswa pb 
					where pb.idpengajuanbeasiswa = $key ";					 
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function getPelatihan($conn,$key){
			$sql = "select pb.*
					from kemahasiswaan.mw_pelatihanbeasiswa pb 
					where pb.idpengajuanbeasiswa = $key ";					 
			return static::getDetail($conn,$sql,$label,$post);
		}
		function getKerja($conn,$key){
			$sql = "select pb.*
					from kemahasiswaan.mw_kerjabeasiswa pb 
					where pb.idpengajuanbeasiswa = $key ";					 
			return static::getDetail($conn,$sql,$label,$post);
		}
	}
?>
