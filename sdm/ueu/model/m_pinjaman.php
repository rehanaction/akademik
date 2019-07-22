<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPinjaman extends mModel {
		const schema = 'sdm';
		
		
		/**************************************************** PINJAMAN ******************************************************/
		// mendapatkan kueri list untuk setting kehadiran
		function listQueryPerjanjian() {
			$sql = "select p.*, m.nik, j.jnspinjaman,
					sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('lv_jnspinjaman')." j on j.kodejnspinjaman=p.kodejnspinjaman
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit";
			
			return $sql;
		}
		
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
					break;
				
				case 'tahun':
					if($key != 'all')
						return "date_part('year', tglperjanjian) = '$key'";
					else
						return "(1=1)";
					
					break;
			}
		}
		
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'bayar':
					$info['table'] = 'pe_bayarpinjaman';
					$info['key'] = 'idbayarpinjaman';
					$info['label'] = 'Proses Pembayaran';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		function getTahun($conn){
			$sql = "select  date_part('year', tglperjanjian) as tahun 
					from ".static::table('pe_pinjaman')."
					group by date_part('year', tglperjanjian) order by date_part('year', tglperjanjian) desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Tahun --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['tahun']] = $row['tahun'];
			}
			
			
			return $a_data;
		}
		
		function getLastTahunPinjam($conn){
			$sql = "select date_part('year', tglperjanjian) as tahun from ".static::table('pe_pinjaman')."
					group by date_part('year', tglperjanjian) order by date_part('year', tglperjanjian) desc limit 1";
			
			return $conn->GetOne($sql);
		}
		
		function getCJenisPinjaman($conn){
			$sql = "select kodejnspinjaman, jnspinjaman from ".static::table('lv_jnspinjaman')." where isaktif='Y'";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getDataEditPerjanjian($r_key){
			$sql = "select p.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap,
					substring(p.periodeawal,1,4) as tahun, cast(substring(p.periodeawal,5,2) as int) as bulan
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpeminjam
					where idpinjaman=$r_key";
			
			return $sql;
		}
		
		function getDataEditPembayaran($r_key){
			$sql = "select p.*, j.jnspinjaman,
					sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpeminjam
					left join ".static::table('lv_jnspinjaman')." j on j.kodejnspinjaman=p.kodejnspinjaman
					where idpinjaman=$r_key";
			
			return $sql;
		}
		
		function getProsesBayar($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('pe_bayarpinjaman')." where idpinjaman = '$key' order by tglbayar";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function saveAngsuran($conn,$record,$r_key){
			$conn->Execute("delete from ".static::table('pe_angsuran')." where idpinjaman = $r_key");
			
			//insert
			if($record['isfixpinjam'] == 'Y' and $record['jmldisetujui'] > 0 and $record['jmlcicilandisetujui'] > 0){
				$bsr = $record['jmldisetujui']/$record['jmlcicilandisetujui'];
				for($i=0;$i<$record['jmlcicilandisetujui'];$i++){
					$rec = array();
					$rec['idpinjaman'] = $r_key;
					$rec['noangsuran'] = $i+1;
					$rec['jmlangsuran'] = $bsr;
					
					self::insertRecord($conn,$rec,false,'pe_angsuran');
				}
			}
			
			return $conn->ErrorNo();
		}
		
		function saveBayarAngsuran($conn,$record,$r_key){
			$conn->Execute("delete from ".static::table('pe_angsuran')." where idpinjaman = $r_key and noangsuran between ".$record['noawal']." and ".$record['noakhir']."");
			
			//insert
			if($record['jmlangsuran'] > 0 and $record['noawal'] > 0 and $record['noakhir'] > 0){
				for($i=$record['noawal'];$i <= $record['noakhir'];$i++){
					$rec = array();
					$rec['idpinjaman'] = $r_key;
					$rec['noangsuran'] = $i;
					$rec['jmlangsuran'] = $record['jmlangsuran'];
					
					list($err,$msg) = self::insertRecord($conn,$rec,true,'pe_angsuran');
				}
			}
			
			return array($err,$msg);
		}
		
		function deleteBayarAngsuran($conn,$r_key,$r_subkey){
			list($min,$max) = explode(':',$r_subkey);
			$conn->Execute("delete from ".static::table('pe_angsuran')." where idpinjaman = $r_key and noangsuran between $min and $max");
			
			return self::deleteStatus($conn);
		}
		
		function getAngsuranPinj($conn,$r_key){
			$sql = "select min(noangsuran) as min,max(noangsuran) as max,jmlangsuran,isdibayar
					from ".static::table('pe_angsuran')." 
					where idpinjaman = $r_key group by jmlangsuran,isdibayar";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['min'] = $row['min'];
				$t_data['max'] = $row['max'];
				$t_data['jmlangsuran'] = $row['jmlangsuran'];
				$t_data['isdibayar'] = $row['isdibayar'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function getNoAngsuran($conn,$r_key){
			$noangs = $conn->GetOne("select top 1 noangsuran from ".static::table('pe_angsuran')." 
					where idpinjaman = $r_key and isdibayar is null or isdibayar = 'N'
					order by noangsuran");
			
			return $noangs;
		}
		
		/**************************************************** END OF PINJAMAN ******************************************************/
		
		/**************************************************** L A P O R A N ******************************************************/
		
		/**************************************************** E N D OF L A P O R A N ******************************************************/
	
	}
?>
