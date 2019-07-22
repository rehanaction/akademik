<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mMastAktifitas extends mModel {
		const schema = 'sdm';
		
		function isSelesai() {
			$data = array('Y' => 'Ya', 'T' => 'Tidak');
			
			return $data;
		}
		
		function listQueryPelanggaran() {
			$sql = "select p.* from ".self::table('lv_pelanggaran')." p ";
			
			return $sql;
		}
		
		function getSanksi($conn,$r_key){
			$sql = "select sp.*,s.keterangan from ".self::table('lv_sanksipelanggaran')." sp 
					left join sdm.lv_sanksi s on sp.jenissanksi = s.jenissanksi
					where idjenispelanggaran = '$r_key'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['jenissanksi'] = $row['jenissanksi'];
				$t_data['keterangan'] = $row['keterangan'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		//tahun libur
		function getTahunLibur($conn){
			$sql = "select substring(cast(tglmulai as varchar),1,4) as thn, substring(cast(tglmulai as varchar),1,4) as tahun
					from ".self::table('ms_libur')."
					group by substring(cast(tglmulai as varchar),1,4)";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Tahun --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['thn']] = $row['tahun'];
			}
			
			
			return $a_data;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'tahun':
					if($key != 'all')
						return "substring(cast(tglmulai as varchar),1,4) = '$key'";
					
					break;
			}
		}
		
		//simpan detail libur
		function saveLiburDetail($conn,$post,$r_key,$is){
			$table = self::table('ms_liburdetail');
			
			//hapus dulu
			Query::qDelete($conn,$table,"idliburan = $r_key");
			
			$mulai=strtotime($post[$is.'_tglmulai']);
			$selesai=strtotime($post[$is.'_tglselesai']);
			
			if(empty($selesai))
				$selesai=$mulai;
			
			$record = array();
			while($mulai<=$selesai){
				$record['idliburan'] = $r_key;
				$record['isliburbersama'] = $post[$is.'_isliburbersama'];
				$record['tgllibur'] = date('Y-m-d',$mulai);
				
				Query::recInsert($conn,$record,$table);
				
				$mulai+=86400;
			}
			
			return $conn->ErrorNo();
		}
		
		//list master outputpenelitian
		function listQueryOutputPenelitian() {
			$sql = "select o.*,a.kodekegiatan ||' - '|| a.namakegiatan as namakegiatan from ".self::table('lv_outputpenelitian')." o
					left join ".self::table('ms_penilaian')." a on a.idkegiatan = o.idkegiatan";
			
			return $sql;
		}
		
		//list master pkm
		function listQueryJenisPKM() {
			$sql = "select p.*,a.kodekegiatan ||' - '|| a.namakegiatan as namakegiatan from ".self::table('lv_jenispkm')." p
					left join ".self::table('ms_penilaian')." a on a.idkegiatan = p.idkegiatan";
			
			return $sql;
		}
	}
?>
