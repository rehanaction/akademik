<?php
	// model semua yang berhubungan riwayat
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPengembangan extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list organisasi
		function listQueryOrganisasi($r_key) {
			$sql = "select r.*, 
					case when r.lingkuporganisasi = 'L' then 'Lokal' when r.lingkuporganisasi = 'N' then 'Nasional' when r.lingkuporganisasi = 'I' then 'Internasional' end as lingkup
					from ".self::table('pe_organisasi')." r 
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat data organisasi
		function getDataEditOrganisasi($r_subkey) {
			$sql = "select r.*, 
					case when r.lingkuporganisasi = 'L' then 'Lokal' when r.lingkuporganisasi = 'N' then 'Nasional' when r.lingkuporganisasi = 'I' then 'Internasional' end as lingkup
					from ".self::table('pe_organisasi')." r
					where nourutpo='$r_subkey'";
			
			return $sql;
		}
		
		function lingkup() {
			$data = array('L' => 'Lokal', 'N' => 'Nasional', 'I' => 'Internasional');
			
			return $data;
		}
		
		// mendapatkan kueri list studi lanjut
		function listQueryStudiLanjut($r_key) {
			$sql = "select r.*, p.namapendidikan, u.namapt
					from ".self::table('pe_tugasbelajar')." r 
					left join ".self::table('lv_jenjangpendidikan')." p on p.idpendidikan = r.idpendidikan
					left join ".self::table('ms_pt')." u on u.kodept = r.kodept
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat data studi lanjut
		function getDataEditStudiLanjut($r_subkey) {
			$sql = "select r.*, p.namapendidikan, u.namapt as pt,j.namajurusan as jurusan, f.namafakultas as fakultas, b.namabidang as bidang,
					coalesce(cast(r.durasitugas as varchar),'0') as durasitugas
					from ".self::table('pe_tugasbelajar')." r 
					left join ".self::table('lv_jenjangpendidikan')." p on p.idpendidikan = r.idpendidikan
					left join ".self::table('ms_pt')." u on u.kodept = r.kodept
					left join ".self::table('ms_fakultas')." f on f.kodefakultas=r.kodefakultas
					left join ".self::table('ms_jurusan')." j on j.kodejurusan=r.kodejurusan
					left join ".self::table('ms_bidang')." b on b.kodebidang=r.kodebidang
					where nouruttugas='$r_subkey'";
			
			return $sql;
		}
		
		function jenisStudi(){
			$data = array('L' => 'Lokal','N' => 'Nasional','I' => 'International');
			
			return $data;
		}
		
		function jenisPembiayaan($conn){
			$sql = "select idbiaya, namabiaya from ".static::schema()."ms_biayatugasbelajar order by idbiaya";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function statusUsulan(){
			$data = array('S' => 'Disetujui', 'T' => 'Ditolak');
			
			return $data;
		}
		
		function cekList($conn,$r_subkey) {
			if(empty($r_subkey))
				$sql = "select noceklist as nocek, ceklist from ".static::schema()."ms_cekliststudy where isaktif = 'Y' order by ceklist";
			else{
				$sql = "select p.*,c.noceklist as nocek,c.ceklist from ".static::schema()."ms_cekliststudy c
						left join ".self::table('pe_ceklist')." p on c.noceklist = p.noceklist and p.nouruttugas = '$r_subkey'
						order by ceklist";
			}
			
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['noceklist'] = $row['nocek'];
				$t_data['ceklist'] = $row['ceklist'];
				$t_data['status'] = $row['status'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function saveCeklist($conn,$post,$r_subkey){
			if(count($post['ceklist']) > 0){
				//hapus dulu di pe_ceklist
				$conn->Execute("delete from ".static::schema()."pe_ceklist where nouruttugas = $r_subkey");
				
				foreach($post['ceklist'] as $val){
					if(!empty($post['ceklist_'.$val]))
						$status = 'S';
					else
						$status = 'B';
					
					$record['nouruttugas'] = $r_subkey;
					$record['noceklist'] = $val;
					$record['status'] = $status;
					
					self::insertCRecord($conn,'',$record,$r_subkey,'pe_ceklist');
				}
			}
			
			return self::saveStatus($conn);
		}
		
		function deleteCeklist($conn,$r_subkey){
			$conn->Execute("delete from ".static::schema()."pe_ceklist where nouruttugas = $r_subkey");
			
			return self::deleteStatus($conn);
		}
		
		// mendapatkan kueri list sertifikasi
		function listQuerySertifikasi($r_key) {
			$sql = "select r.*, s.jenissertifikasi
					from ".self::table('pe_sertifikasi')." r 
					left join ".self::table('lv_jenissertifikasi')." s on s.kodesertifikasi = r.kodesertifikasi
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat data sertifikasi
		function getDataEditSertifikasi($r_subkey) {
			$sql = "select r.*, substring(r.periodesertifikasi,1,4) as tahun, cast(substring(r.periodesertifikasi,5,2) as int) as bulan,s.jenissertifikasi
					from ".self::table('pe_sertifikasi')." r 
					left join ".self::table('lv_jenissertifikasi')." s on s.kodesertifikasi = r.kodesertifikasi
					where idsertifikasi='$r_subkey'";
			
			return $sql;
		}		
		
		function jenisSertifikasi($conn) {
			$sql = "select kodesertifikasi, jenissertifikasi from ".static::schema()."lv_jenissertifikasi order by jenissertifikasi";
			
			return Query::arrQuery($conn,$sql);
		}
		
		// mendapatkan kueri list penelitian
		function listQueryPenelitian($r_key) {
			$sql = "select r.*, o.outputpenelitian,coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) ||' (Mandiri)' as mandiri
					from ".self::table('pe_penelitian')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('lv_outputpenelitian')." o on o.kodeoutput = r.kodeoutput
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat data penelitian
		function getDataEditPenelitian($r_subkey) {
			$sql = "select r.*, o.outputpenelitian,coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as insertuser
					from ".self::table('pe_penelitian')." r 
					left join ".self::table('lv_outputpenelitian')." o on o.kodeoutput = r.kodeoutput
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.t_insertuser::integer
					where r.idpenelitian='$r_subkey'";
			
			return $sql;
		}	
		
		//tim penelitian berdasarkan pegawai
		function listTimPenelitian($conn,$r_key){
			$sql = "select case when t.statustim = 'P' then coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					when t.statustim = 'L' then t.namatim end as namatim,cast(t.kontributorke as int) as kontributortim,
					r.idpenelitian,coalesce(m.nik,m.nippns,'')||' - '||".static::schema.".f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namapeg,cast(r.kontributorke as int) as kontributorke
					from ".self::table('pe_timpenelitian')." t
					left join ".self::table('ms_pegawai')." p on p.idpegawai = t.idpegawai
					left join ".self::table('pe_penelitian')." r on r.idpenelitian = t.idpenelitian
					left join ".self::table('ms_pegawai')." m on m.idpegawai = r.idpegawai
					where r.idpegawai = '$r_key'
					order by t.idpenelitian,t.kontributorke";
			$rs = $conn->Execute($sql);
			
			$a_team = array();
			while($row = $rs->FetchRow()){
				$a_team[$row['idpenelitian']][$row['kontributortim']] = $row['namatim'];
				$a_team[$row['idpenelitian']][$row['kontributorke']] = $row['namapeg'];
			}
			
			return $a_team;
		}
		
		//tim penelitian semua pegawai
		function listTimPenelitianAll($conn){
			$sql = "select case when t.statustim = 'P' then coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					when t.statustim = 'L' then t.namatim end as namatim,cast(t.kontributorke as int) as kontributortim,
					r.idpenelitian,coalesce(m.nik,m.nippns,'')||' - '||".static::schema.".f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namapeg,cast(r.kontributorke as int) as kontributorke
					from ".self::table('pe_timpenelitian')." t
					left join ".self::table('ms_pegawai')." p on p.idpegawai = t.idpegawai
					left join ".self::table('pe_penelitian')." r on r.idpenelitian = t.idpenelitian
					left join ".self::table('ms_pegawai')." m on m.idpegawai = r.idpegawai
					order by r.idpegawai,t.idpenelitian,t.kontributorke";
			$rs = $conn->Execute($sql);
			
			$a_team = array();
			while($row = $rs->FetchRow()){
				$a_team[$row['idpenelitian']][$row['kontributortim']] = $row['namatim'];
				$a_team[$row['idpenelitian']][$row['kontributorke']] = $row['namapeg'];
			}
			
			return $a_team;
		}
		
		function OutputPenelitian($conn) {
			$sql = "select kodeoutput, outputpenelitian from ".static::schema()."lv_outputpenelitian order by outputpenelitian";
			
			return Query::arrQuery($conn,$sql);
		}
				
		//cek apakah sudah diinputkan ke KUM
		function isPenelitianKUM($conn,$r_key){
			$rs = $conn->Execute("select idpenelitian from ".self::table('ak_bidang2')." where idpegawai = $r_key");
			while($row = $rs->FetchRow()){
				$a_kum[$row['idpenelitian']] = $row['idpenelitian'];
			}
			
			return $a_kum;
		}
		
		function jmlTeam($post){
			$jml=1;
			if(count($post['P'])>0){	
				foreach($post['P'] as $data){
					$jml++;
				}
			}
			
			if(count($post['L'])>0){	
				foreach($post['L'] as $data){
					$jml++;				
				}
			}
			
			return $jml;
		}
				
		function jmlTeamBagi($post,$kont){
			$jml = $kont>1 ? 1 : 0;
			if(count($post['P'])>0){	
				foreach($post['P'] as $data){
					$tim = explode('::',$data);
					if($tim[0]>1)
						$jml++;
				}
			}
			
			if(count($post['L'])>0){	
				foreach($post['L'] as $data){
					$tim = explode('::',$data);
					if($tim[0]>1)
						$jml++;
				}
			}
			
			return $jml;
		}
		
		// mendapatkan kueri list pkm
		function listQueryPKM($r_key) {
			$sql = "select r.*, k.namapkm,coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)||' (Mandiri)' as mandiri
					from ".self::table('pe_pkm')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('lv_jenispkm')." k on k.kodepkm = r.kodepkm
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		//untuk mendapat data penelitian
		function getDataEditPKM($r_subkey) {
			$sql = "select r.*, k.namapkm,coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as insertuser
					from ".self::table('pe_pkm')." r 
					left join ".self::table('lv_jenispkm')." k on k.kodepkm = r.kodepkm
					left join ".self::table('ms_pegawai')." p on p.idpegawai = CAST(r.t_insertuser as integer)
					where r.idpkm='$r_subkey'";
			
			return $sql;
		}	
		
		function jenisPKM($conn){
			$sql = "select kodepkm, namapkm from ".static::schema()."lv_jenispkm order by namapkm";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function listTimPKM($conn,$r_key){
			$sql = "select case when t.statustim = 'P' then coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					when t.statustim = 'L' then t.namatim end as namatim,cast(t.kontributorke as int) as kontributortim,
					r.idpkm,coalesce(m.nik,m.nippns,'')||' - '||".static::schema.".f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namapeg,cast(r.kontributorke as int) as kontributorke
					from ".self::table('pe_timpkm')." t
					left join ".self::table('ms_pegawai')." p on p.idpegawai = t.idpegawai
					left join ".self::table('pe_pkm')." r on r.idpkm = t.idpkm
					left join ".self::table('ms_pegawai')." m on m.idpegawai = r.idpegawai
					where r.idpegawai = '$r_key'
					order by t.idpkm,t.kontributorke";
			$rs = $conn->Execute($sql);
			
			$a_team = array();
			while($row = $rs->FetchRow()){
				$a_team[$row['idpkm']][$row['kontributortim']] = $row['namatim'];
				$a_team[$row['idpkm']][$row['kontributorke']] = $row['namapeg'];
			}
			
			return $a_team;
		}
		
		//tim pkm semua pegawai
		function listTimPKMAll($conn){
			$sql = "select case when t.statustim = 'P' then coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					when t.statustim = 'L' then t.namatim end as namatim,cast(t.kontributorke as int) as kontributortim,
					r.idpkm,coalesce(m.nik,m.nippns,'')||' - '||".static::schema.".f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namapeg,cast(r.kontributorke as int) as kontributorke
					from ".self::table('pe_timpkm')." t
					left join ".self::table('ms_pegawai')." p on p.idpegawai = t.idpegawai
					left join ".self::table('pe_pkm')." r on r.idpkm = t.idpkm
					left join ".self::table('ms_pegawai')." m on m.idpegawai = r.idpegawai
					order by r.idpegawai,t.idpkm,t.kontributorke";
			$rs = $conn->Execute($sql);
			
			$a_team = array();
			while($row = $rs->FetchRow()){
				$a_team[$row['idpkm']][$row['kontributortim']] = $row['namatim'];
				$a_team[$row['idpkm']][$row['kontributorke']] = $row['namapeg'];
			}
			
			return $a_team;
		}
				
		//cek apakah sudah diinputkan ke KUM
		function isPKMKUM($conn,$r_key){
			$rs = $conn->Execute("select idpkm from ".self::table('ak_bidang3')." where idpegawai = $r_key");
			while($row = $rs->FetchRow()){
				$a_kum[$row['idpkm']] = $row['idpkm'];
			}
			
			return $a_kum;
		}
		
		function mandiriTeam(){
			return array('T' => 'Tim', 'M' => 'Mandiri');
		}
		
		function kontributor(){
			return array('1' => 'Kontributor I', '2' => 'Kontributor II', '3' => 'Kontributor III', '4' => 'Kontributor IV', '5' => 'Kontributor V');
		}
		
		function isTugasKhusus(){
			return array('Y' => 'Ya', 'T' => 'Tidak');
		}
		
		function status(){
			return array('S' => 'Disetujui', 'T' => 'Ditolak');
		}
		
		function statusTim(){
			$data = array('P' => 'Pegawai', 'L' => 'Personil Luar');
			
			return $data;
		}
		
		function insertTim($conn,$p_dbtabletim,$a_id,$r_subkey,$post){
			list($id,$idtim) = explode('|',$a_id);
			
			//hapus dulu
			$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getCondition($r_subkey,$id));
			
			if(!$err){
				$i = $conn->GetOne("select cast(coalesce(max($idtim),0) as int) from ".self::table($p_dbtabletim)." where $id = '$r_subkey'");
				//insert tim pegawai
				if(count($post['P'])>0){	
					foreach($post['P'] as $data){
						$record[$id] = $r_subkey;
						$record[$idtim] = ++$i;
						$ardata = explode('::',$data);
						$record['kontributorke'] = $ardata[0];
						$record['statustim'] = $ardata[2];
						$record['idpegawai'] = $ardata[3];
						
						$err = Query::recInsert($conn,$record,self::table($p_dbtabletim));
						unset($record['idpegawai']);
						
						if($err)
							break;
					}
				}
				
				//insert personil luar
				if(count($post['L'])>0){	
					foreach($post['L'] as $data){
						$record[$id] = $r_subkey;
						$record[$idtim] = ++$i;
						$ardata = explode('::',$data);
						$record['kontributorke'] = $ardata[0];
						$record['statustim'] = $ardata[2];
						$record['namatim'] = $ardata[1];
						
						$err = Query::recInsert($conn,$record,self::table($p_dbtabletim));
						unset($record['namatim']);
						
						if($err)
							break;
					}
				}
			}else
				break;
			
			return self::saveStatus($conn);
		}
		
		function insertTimLain($conn,$a_table,$a_id,$r_subkey,$post){
			list($id,$refid,$idtim) = explode('|',$a_id);
			list($p_dbtable,$p_dbtabletim) = explode('|',$a_table);
			
			//insert juga utk tim yang lain
			$rec = $conn->GetRow("select * from ".self::table($p_dbtable)." where $id = '$r_subkey'");
			$kont = $rec['kontributorke'];
			$idpeg = $rec['idpegawai'];
			unset($rec[$id]);
			
			//cek utk pegawai insert atau update penelitian
			$rs = $conn->Execute("select idpegawai from ".self::table($p_dbtable)." where $refid = '$r_subkey'");
			$arnik = array();
			while($rown = $rs->FetchRow()){
				$arnik[] = $rown['idpegawai'];
			}
			
			if(count($post['P'])>0){
				$postpeg = $post['P'];
				foreach($post['P'] as $data){
					$other = array();
					$other = $postpeg;
					unset($other[array_search($data,$other)]);
					array_push($other,$kont.'::::P::'.$idpeg);
					$post['P'] = $other;
					
					$ardata = explode('::',$data);
					$rec['kontributorke'] = $ardata[0];
					$rec[$refid] = $r_subkey;
					$rec['idpegawai'] = $ardata[3];
					
					if(!in_array($ardata[3],$arnik)){
						$err = Query::recInsert($conn,$rec,self::table($p_dbtable));	
						
						$r_keyelse = self::getLastValue($conn,self::table($p_dbtable).'_'.$id.'_seq');
					}else{
						$r_keyelse = $conn->GetOne("select $id from ".self::table($p_dbtable)." where idpegawai = '".$ardata[3]."' and $refid = '$r_subkey'");
						$err = Query::recUpdate($conn,$rec,self::table($p_dbtable),static::getCondition($r_keyelse,$id));
					}
					
					if(!$err){
						$a_id = $id.'|'.$idtim;
						mPengembangan::insertTim($conn,$p_dbtabletim,$a_id,$r_keyelse,$post);
					}else
						break;
				}
			}
			
			return self::saveStatus($conn);
		}
		
		//mendapatkan tim di detail
		function getTim($conn,$p_dbtable,$a_id,$r_subkey){
			list($idpen,$idtim) = explode('|',$a_id);
			
			$sql = "select t.*,case when t.idpegawai is not null then coalesce(p.nik,p.nippns,'')||' - '||".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) else '' end as namalengkap 
					from ".self::table($p_dbtable)." t
					left join ".self::table('ms_pegawai')." p on p.idpegawai = t.idpegawai
					where $idpen = '$r_subkey'
					order by t.kontributorke";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				if($row['statustim'] == 'L')
					$arrow[] = $row['kontributorke'].'::'.$row['namatim'].'::'.$row['statustim'].'::::'.$row[$idtim];
				else
					$arrow[] = $row['kontributorke'].'::'.$row['namalengkap'].'::'.$row['statustim'].'::'.$row['idpegawai'].'::'.$row[$idtim];
			}
			
			return $arrow;
		}
		
		//mendapatkan data edit tim
		function getDataEditTimPenelitian($conn,$r_keydet) {
			$sql = "select t.*, t.kontributorke as timkontributorke,
					case when t.idpegawai is not null then ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) else '' end as pegawai 
					from ".self::table('pe_timpenelitian')." t 
					left join ".self::table('ms_pegawai')." p on p.idpegawai = t.idpegawai
					where t.notimpenelitian = '$r_keydet'";
			
			return $sql;
		}
		
		
		//mendapatkan data edit tim
		function getDataEditTimPKM($conn,$r_keydet) {
			$sql = "select t.*, t.kontributorke as timkontributorke,
					case when t.idpegawai is not null then ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) else '' end as pegawai 
					from ".self::table('pe_timpkm')." t 
					left join ".self::table('ms_pegawai')." p on p.idpegawai = t.idpegawai
					where t.notimpkm = '$r_keydet'";
			
			return $sql;
		}
		
		//menghapus tim satu persatu
		function deleteDetailTim($conn,$a_table,$a_id,$r_subkey,$r_keydet){
			list($kont,$nama,$status,$id,$no) = explode('::',$r_keydet);
			list($idpen,$refid) = explode('|',$a_id);
			list($p_dbtable,$p_dbtabletim) = explode('|',$a_table);
			
			//select idpenelitian utk hapus di riwayat penelitian lain
			$rs = $conn->Execute("select $idpen,idpegawai from ".self::table($p_dbtable)." where $refid = '$r_subkey'");			
			while($rown = $rs->FetchRow()){				
				if($status == 'P'){
					if($id == $rown['idpegawai']){
						$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getCondition($rown[$idpen],$idpen));
						if(!$err)
							$err = Query::qDelete($conn,self::table($p_dbtable),static::getCondition($rown[$idpen],$idpen));
						else
							break;
					}else
						$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getCondition($rown[$idpen].'|'.$id.'|'.$kont,$idpen.',idpegawai,kontributorke'));
					
					if($err)
						break;
				}else if($status == 'L'){
					$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getCondition($rown[$idpen].'|'.$nama.'|'.$kont,$idpen.',namatim,kontributorke'));
					if($err)
						break;
				}
			}
			
			if($status == 'P')
				$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getCondition($r_subkey.'|'.$id.'|'.$kont,$idpen.',idpegawai,kontributorke'));
			else if($status == 'L')
				$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getCondition($r_subkey.'|'.$nama.'|'.$kont,$idpen.',namatim,kontributorke'));
			
			if($err)
				break;
			
			return self::deleteStatus($conn);
		}
		
		function deleteRef($conn,$r_subkey,$a_table,$a_id){
			list($idpen,$refid) = explode('|',$a_id);
			list($p_dbtable,$p_dbtabletim) = explode('|',$a_table);
			
			$rs = $conn->Execute("select $idpen from ".self::table($p_dbtable)." where $refid = '$r_subkey'");
			while($row = $rs->FetchRow()){
				$arid[] = $row[$idpen];
			}
			
			if(count($arid) > 0){
				$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getInCondition($arid,$idpen));
				if(!$err)
					$err = Query::qDelete($conn,self::table($p_dbtable),static::getInCondition($arid,$idpen));
				else
					break;
			}
			
			if(!$err)
				$err = Query::qDelete($conn,self::table($p_dbtabletim),static::getCondition($r_subkey,$idpen));
			else
				break;
			
			return self::deleteStatus($conn);
		}
		
		//mendapatkan siapa yang pertama kali insert
		function insertUser($conn,$p_dbtable,$where,$r_subkey){
			$whoinsert = $conn->GetOne("select t_insertuser from ".self::table($p_dbtable)." where $where = $r_subkey");
			
			return $whoinsert;
		}
		
		// mendapatkan kueri list kemampuan bahasa
		function listQueryKemampuanBhs($r_key) {
			$sql = "select r.*
					from ".self::table('pe_kemampuanbhs')." r 
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		//********************************************L A P O R A N*************************************************
								
		function repRiwayatOrganisasi($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg
					from ".static::schema()."pe_organisasi r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg										
					where r.isvalid = 'Y' and r.tglmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." 
					order by namapegawai,r.tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}			
		
		function repRiwayatStudiLanjut($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenisbiaya){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg,j.namabiaya,d.namapendidikan,pt.namapt
					from ".static::schema()."pe_tugasbelajar r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_biayatugasbelajar j on j.idbiaya=r.idbiaya										
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg										
					left join ".static::schema()."ms_pt pt on pt.kodept=r.kodept										
					left join ".static::schema()."lv_jenjangpendidikan d on d.idpendidikan=r.idpendidikan
					where r.isvalid = 'Y' and r.tglmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .= !empty($r_jenisbiaya) ? " and r.idbiaya = '$r_jenisbiaya'" : "";
			$sql .= " order by namapegawai,r.tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}			
		
		function repRiwayatSertifikasi($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenissertifikasi){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg,j.jenissertifikasi
					from ".static::schema()."pe_sertifikasi r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."lv_jenissertifikasi j on j.kodesertifikasi=r.kodesertifikasi										
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg	
					where r.isvalid = 'Y' and r.tglsertifikasi between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .= !empty($r_jenissertifikasi) ? " and r.kodesertifikasi = '$r_jenissertifikasi'" : "";
			$sql .= " order by namapegawai,r.tglsertifikasi desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}				
		
		function repRiwayatPenelitian($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_output){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg,j.outputpenelitian
					from ".static::schema()."pe_penelitian r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."lv_outputpenelitian j on j.kodeoutput=r.kodeoutput
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg	
					where r.isvalid = 'Y' and r.tglmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .= !empty($r_output) ? " and r.kodeoutput = '$r_output'" : "";
			$sql .= " order by namapegawai,r.tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}				
		
		//penelitian homebase
		function repRiwayatPenelitianHB($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_output){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg,j.outputpenelitian
					from ".static::schema()."pe_penelitian r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunitbase
					left join ".static::schema()."lv_outputpenelitian j on j.kodeoutput=r.kodeoutput
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg	
					where r.isvalid = 'Y' and r.tglmulai between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .= !empty($r_output) ? " and r.kodeoutput = '$r_output'" : "";
			$sql .= " order by namapegawai,r.tglmulai desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}			
		
		function repRiwayatPKM($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg,j.namapkm
					from ".static::schema()."pe_pkm r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."lv_jenispkm j on j.kodepkm=r.kodepkm
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg	
					where r.isvalid = 'Y' and r.tglawal between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .= !empty($r_jenis) ? " and r.kodepkm = '$r_jenis'" : "";
			$sql .= " order by namapegawai,r.tglawal desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}				
		
		//PKM homebase
		function repRiwayatPKMHB($conn,$r_kodeunit,$r_tglmulai,$r_tglselesai,$r_jenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg,j.namapkm
					from ".static::schema()."pe_pkm r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunitbase
					left join ".static::schema()."lv_jenispkm j on j.kodepkm=r.kodepkm
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg	
					where r.isvalid = 'Y' and r.tglawal between '$r_tglmulai' and '$r_tglselesai'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .= !empty($r_jenis) ? " and r.kodepkm = '$r_jenis'" : "";
			$sql .= " order by namapegawai,r.tglawal desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}			
		
		function repKemampuanBahasa($conn,$r_kodeunit,$r_tahun1,$r_tahun2){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
						
			$sql = "select r.*,p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit, t.tipepeg
					from ".static::schema()."pe_kemampuanbhs r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_tipepeg t on t.idtipepeg=p.idtipepeg	
					where r.isvalid = 'Y' and r.tahunkemampuan between '$r_tahun1' and '$r_tahun2'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					order by namapegawai,r.tahunkemampuan desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}	
	}
?>
