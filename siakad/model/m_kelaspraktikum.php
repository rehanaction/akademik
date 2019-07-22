<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mKelasPraktikum extends mModel {
		const schema = 'akademik';
		const table = 'ak_kelaspraktikum';
		const order = 'periode,namamk,kelasmk';
		const key = 'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul,kelompok';
		const label = 'kelas';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select p.*,k.namamk,k.sistemkuliah from ".static::table()." p
					join ".static::table('v_kelas3')." k using (thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					join gate.ms_unit u on p.kodeunit = u.kodeunit";
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'periode': return "periode = '$key'";
				case 'kodemk': return "kodemk = '$key'";
				case 'kelasmk': return "kelasmk = '$key'";
				case 'sistemkuliah': return "sistemkuliah = '$key'";
				case 'unit':
					global $conn, $conf;
					require_once(Route::getModelPath('unit'));
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		// mendapatkan data tambahan
		function getExtraRow($row) {
			$row['semester'] = substr($row['periode'],-1);
			$row['tahun'] = substr($row['periode'],0,4);
			
			return $row;
		}
		function mkKelas($conn,$kurikulum,$periode,$kodeunit) {
			$sql = "select k.kodemk, kr.kodemk||' - '||kr.namamk from ".static::table('ak_kelas')." k
					join ".static::table('ak_kurikulum')." kr using (thnkurikulum , kodeunit , kodemk)
					join ".static::table('ak_matakuliah')." mk on mk.thnkurikulum=kr.thnkurikulum and mk.kodemk=kr.kodemk and mk.skspraktikum is not null
					where k.periode='$periode' and k.thnkurikulum = '$kurikulum' and k.kodeunit = '$kodeunit' order by k.kodemk";
			
			return Query::arrQuery($conn,$sql);
		}
		function kelas($conn,$kurikulum,$periode,$kodeunit,$kodemk){
			$sql = "select k.kelasmk, k.kelasmk from ".static::table('ak_kelas')." k
					where k.periode='$periode' and k.thnkurikulum = '$kurikulum' and k.kodeunit = '$kodeunit' and k.kodemk='$kodemk' order by k.kodemk";
			
			return Query::arrQuery($conn,$sql);
		}
		function getKelompok($conn){
			$data=array();
			for($i=1;$i<=10;$i++)
				$data[$i]=$i;
				
			return $data;
		}
		function getDosenPengajar($conn,$key) {
			$sql = "select m.ispjmk, p.idpegawai as nipdosen, p.idpegawai, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as nama from ".static::table('ak_mengajar')." m
					join sdm.ms_pegawai p on m.nipdosen::text = p.idpegawai::text
					where ".static::getCondition($key,null,'m')." order by nama";
			
			return $conn->GetArray($sql);
		}
		function insertRecordMengajar($conn,$record,$key) {
			$reckey = static::getKeyRecord($key);
			$record += $reckey;
			
			Query::recInsert($conn,$record,static::table('ak_mengajar'));
			
			return static::insertStatus($conn,$kosong,'dosen pengajar','ak_mengajar');
		}
		// udate record
		function updateRecordMengajar($conn,$record,$key) {
			
			Query::recUpdate($conn,$record,static::table('ak_mengajar'),static::getCondition($key)." and ispjmk=1");
			
			return static::updateStatus($conn,$kosong,'dosen pengajar','ak_mengajar');
		}
		// hapus data
		function deleteMengajar($conn,$key,$nip=false) {
			$cond = static::getCondition($key);
			if($nip !== false)
				$cond .= " and nipdosen = '$nip'";
			
			Query::qDelete($conn,static::table('ak_mengajar'),$cond);
			
			return static::deleteStatus($conn,'dosen pengajar');
		}
		function getJadwalPrak($conn,$thnkurikulum,$periode,$kodeunit){
			$sql="select p.kodemk,p.kelasmk,p.kelompok,p.nohari,p.jammulai,p.jamselesai,(case when p.peserta>=p.kapasitas then 1 else 0 end) as penuh from ".static::table()." p
				where p.thnkurikulum='$thnkurikulum' and p.kodeunit='$kodeunit' and p.periode='$periode'
				union
				select m.kodemk,m.kelasmk,pl.kelompok,pl.nohari,pl.jammulai,pl.jamselesai,(case when pl.peserta>=pl.kapasitas then 1 else 0 end) as penuh from ".static::table('ak_pesertamku')." m
				join ".static::table()." pl  using (kodeunit,periode,thnkurikulum,kodemk,kelasmk)
					where m.thnkurikulum='$thnkurikulum' and m.unitmku='$kodeunit' and m.periode='$periode'";
			$data=$conn->GetArray($sql);
			$a_data=array();
			foreach($data as $row){
				$key=$row['kodemk'].'|'.$row['kelasmk'];
				$a_data[$key][]=$row;
			}
			return $a_data;
		}
		function getAbsenPrak($conn,$sort,$unit,$periode){
			$filterunit='';
			if(!empty($unit)){
				require_once(Route::getModelPath('unit'));
				$row = mUnit::getData($conn,$unit);		
				$filterunit="u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			
			$sql="select p.* from akademik.r_absensipraktikum p join gate.ms_unit u on p.kodeunit=u.kodeunit 
			where periode = '$periode' and $filterunit";
			
			
			$data=$conn->GetArray($sql);
			$a_data=array();
			foreach($data as $row){
				$key=$row['kodemk'].'|'.$row['kelasmk'];
				$a_data[$key][]=$row;
			}
			return $a_data;
		}
		function getKelompokKelas($conn,$key,$kelompok_prak){
			$sql="select kelompok from ".static::table()." 
					where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul')." 
					and kelompok::int!=$kelompok_prak order by kelompok";
			
			return Query::arrQuery($conn,$sql);
		}
		function getPesertaKel($conn,$key){
			$sql="select kelompok,peserta from ".static::table()." where ".static::getCondition($key,'thnkurikulum,kodemk,kodeunit,periode,kelasmk,jeniskul')." order by kelompok";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function delete($conn,$key){
			require_once(Route::getModelPath('detailkelas'));
			$ok=true;
			$conn->BeginTrans();
			
			list($p_posterr,$p_postmsg)=mDetailKelas:: deleteBlock($conn,$key);
			if(!$p_posterr)
				list($p_posterr,$p_postmsg)=self::deleteMengajar($conn,$key);
			if(!$p_posterr)
				list($p_posterr,$p_postmsg) = parent::delete($conn,$key);
				
			if($p_posterr)
				$ok=false;
				
			$conn->CommitTrans($ok);
			
			return array($p_posterr,$p_postmsg);
		}
		
		function getReportKelas($conn,$kolom,$sort,$filter){
			return static::getListData($conn,$kolom,$sort,$filter);
		}
	}
?>
