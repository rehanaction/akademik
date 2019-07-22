<?php
	// model barang
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mBarang extends mModel {
		const schema = 'aset';
		const table = 'ms_barang1';
		const order = 'idbarang1';
		const key = 'idbarang1';
		const label = 'Barang';

		//list
		function listQuery() {
			$sql = "select idbarang1,namabarang,level,idsatuan,jenispenyusutan 
					from ".static::schema.".ms_barang1 b
					left join ".static::schema.".ms_jenispenyusutan s on s.idjenispenyusutan = b.idjenispenyusutan";
		
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'levelbarang': 
				    return "level = '$key'"; 
				break;
				case 'jenispenyusutan': 
				    return "b.idjenispenyusutan = '$key'"; 
				break;
			}
		}
		
		function getArAktif(){
			return array("1" => "Aktif", "0" => "Non Aktif");
		}
		
		function getNamaBarang($conn, $idbarang1){
		    return $conn->GetOne("select namabarang from ".self::table()." where idbarang1 = '$idbarang1'");
		}
		
		// informasi detail
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'jadwalrawat':
					$info['table'] = 'ms_jadwalrawat';
					$info['key'] = 'idjadwalrawat';
					$info['label'] = 'jadwal perawatan';
				break;
				case 'stockkonversi':
					$info['table'] = 'ms_konversi';
					$info['key'] = 'idkonversi';
					$info['label'] = 'stock konversi';
				break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		// jadwal perawatan
		function getJadwalRawat($conn,$key,$label='',$post='') {
			$sql = "select idjadwalrawat,idjenisrawat,periode,satuanperiode 
					from ".static::table('ms_jadwalrawat')." 
					where idbarang1 = '$key' order by idjadwalrawat desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		// stock konversi
		function getStockKonversi($conn,$key,$label='',$post='') {
			$sql = "select idkonversi,idtujuan,nilai 
					from ".static::table('ms_konversi')." 
					where idbarang1 = '$key' order by idkonversi desc";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function getNKonversi($conn,$key){
		    return (int)$conn->GetOne("select count(*) from aset.ms_konversi where idbarang1 = '$key'");
		}
		
		function getNTransAset($conn,$key){
		    return (int)$conn->GetOne("select count(*) from aset.as_perolehandetail where idbarang1 = '$key'");
		}
		
		function getNTransHP($conn,$key){
		    return (int)$conn->GetOne("select count(*) from aset.as_transhpdetail where idbarang1 = '$key'");
		}
		
		function getSatuanByID($conn,$key){
		    return $conn->GetOne("select idsatuan from aset.ms_barang1 where idbarang1 = '$key'");
		}
		
		function getLast($conn,$r_pkey){
			return $conn->GetOne("select max(idbarang1) from aset.ms_barang1 where idparent = '$r_pkey' and idbarang1 < (select max(idbarang1) from aset.ms_barang1 where idparent = '$r_pkey') ");
		}
		
	}
?>
