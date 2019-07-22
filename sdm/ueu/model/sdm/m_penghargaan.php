<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPenghargaan extends mModel {
		const schema = 'sdm';
		
		//mendapatkan data penghargaan		
		function listQueryPenghargaan($r_key) {
			$sql = "select r.*
					from ".self::table('pe_penghargaan')." r 
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		// mendapatkan kueri list sanksi
		function listQuerySanksi($r_key) {
			$sql = "select r.*, j.jenispelanggaran
					from ".self::table('pe_sanksi')." r 
					left join ".self::table('lv_pelanggaran')." j on j.idjenispelanggaran=r.idjenispelanggaran
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat
		function getDataEditSanksi($r_subkey) {
			$sql = "select r.*, j.jenispelanggaran
					from ".self::table('pe_sanksi')." r 
					left join ".self::table('lv_pelanggaran')." j on j.idjenispelanggaran=r.idjenispelanggaran
					where nourutsanksi='$r_subkey'";
			
			return $sql;
		}
		
		function getSanksi($conn) {
			$sql = "select p.*, s.keterangan 
					from ".static::schema()."lv_sanksipelanggaran p
					left join ".self::table('lv_sanksi')." s on p.jenissanksi=s.jenissanksi";
			
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$sanksi[$row['idjenispelanggaran']][$row['jenissanksi']] = $row['keterangan'];
			}
			
			return $sanksi;
		}
		
		function jenisPelanggaran($conn) {
			$sql = "select idjenispelanggaran, jenispelanggaran from ".static::schema()."lv_pelanggaran order by jenispelanggaran";
			
			return Query::arrQuery($conn,$sql);
		}
		
		//mendapatkan data penghargaan		
		function listQueryPiagam($r_key) {
			$sql = "select r.*
					from ".self::table('pe_piagam')." r 
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		//lingkup piagam
		function lingkupPiagam() {
			$data = array('L' => 'Lokal', 'N' => 'Nasional', 'I' => 'Internasional');
			
			return $data;
		}
		
		
		/******************************L A P O R A N***************************************/		
		
		function repSanksi($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$jenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,j.jenispelanggaran,t.tipepeg
					from ".static::table('pe_sanksi')." r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."lv_pelanggaran j on j.idjenispelanggaran=r.idjenispelanggaran
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg
					where r.isvalid = 'Y' and r.idjenispelanggaran in ('$jenis')
					and r.tgltmt between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tgltmt desc";
			$rs = $conn->Execute($sql);
			
			//sanksi
			$sql = "select p.*,s.keterangan
					from ".static::table('lv_sanksipelanggaran')." p
					left join ".static::schema()."lv_sanksi s on s.jenissanksi=p.jenissanksi
					order by p.idjenispelanggaran";
			$rsp = $conn->Execute($sql);
			
			while($rowp = $rsp->FetchRow()){
				$a_sanksi[$rowp['idjenispelanggaran']][$rowp['jenissanksi']] = $rowp['keterangan'];
			}
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit'], 'sanksi' => $a_sanksi);
			
			return $a_data;
		}
		
		function repPenghargaan($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,t.tipepeg
					from ".static::table('pe_penghargaan')." r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg
					where r.isvalid = 'Y' and r.tglpenghargaan between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglpenghargaan desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;
		}
		
		function repPiagam($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,t.tipepeg
					from ".static::table('pe_piagam')." r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg
					where r.isvalid = 'Y' and r.tglpiagam between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglpiagam desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;
		}
	}
?>
