<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAngkaKredit extends mModel {
		const schema = 'sdm';
			
		//master penilaian
		function getDataEditPenilaian($r_key) {
			$sql = "select m.*,case when m.parentidkegiatan is not null then p.kodekegiatan||' - '||p.namakegiatan end as parentkegiatan
					from ".self::table('ms_penilaian')." m 
					left join ".self::table('ms_penilaian')." p on p.idkegiatan = m.parentidkegiatan
					where m.idkegiatan='$r_key'";
			
			return $sql;
		}
		
		//jenis bidang
		function jenisBidang() {
			$data = array('IA' => 'IA', 'IB' => 'IB', 'II' => 'II', 'III' => 'III', 'IV' => 'IV');
			
			return $data;
		}
		
		//passing level dan kode urutan penilaian
		function levelPenilaian($conn,$parent,$r_key=''){
			if($parent == 'null') {
				$parent = '';
				$record['level'] = 0;
				
				$g_max = $conn->GetOne("select max(cast(kodeurutan as int)) from ".self::table('ms_penilaian')." where level = 0 or level is null");
				if($g_max == '')
					$kodeurutan = '1';
				else
					$kodeurutan = $g_max+1;
			}
			else {
				$g_parent = $conn->GetRow("select coalesce(level,0)+1 as nlevel, kodeurutan from ".self::table('ms_penilaian')." where idkegiatan = '".$parent."'");
				$record['level'] = $g_parent['nlevel'];
				
				$g_max = $conn->GetOne("select max(cast(kodeurutan as int)) from ".self::table('ms_penilaian')." where parentidkegiatan = '".$parent."'");
				if($g_max == '')
					$kodeurutan = $g_parent['kodeurutan'].'01';
				else {
					$lnum = substr($g_max,-2);
					$lnum++;
					if(strlen($lnum) == 1)
						$lnum = str_pad($lnum,2,'0',STR_PAD_LEFT);
					$kodeurutan = substr($g_max,0,strlen($g_max)-2).$lnum;
				}
			}
								
			if(!empty($r_key)){
				$g_akreditasi = $conn->GetRow("select kodeurutan,parentidkegiatan from ".self::table('ms_penilaian')." where idkegiatan = '$r_key'");
				if(empty($g_akreditasi['kodeurutan']) or $parent != $g_akreditasi['parentidkegiatan'])
					$record['kodeurutan'] = $kodeurutan;
			}else
				$record['kodeurutan'] = $kodeurutan;
					
			return $record;
		}	
		
		//level dan kategori parent
		function pPenilaian($conn,$p_key) {
			$sql = "select bidangkegiatan,kodekegiatan||' - '||namakegiatan as parentkegiatan,isaktif from ".self::table('ms_penilaian')." where idkegiatan = '$p_key'";
			$row = $conn->GetRow($sql);
			
			return $row;
		}
		
		//penilaian
		function getPenilaian($conn,$bdg=''){
			if(!empty($bdg))
				$bidang = "and bidangkegiatan = '$bdg'";
			
			$sql = "select *,cast(stdkredit as varchar) as stdkredit,cast(level as varchar) as level from ".self::table('ms_penilaian')." where isaktif = 'Y' {$bidang} order by kodeurutan,kodekegiatan";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idkegiatan'] = $row['idkegiatan'];
				$t_data['kodekegiatan'] = $row['kodekegiatan'];
				$t_data['namakegiatan'] = $row['namakegiatan'];
				$t_data['level'] = $row['level'];
				$t_data['stdkredit'] = $row['stdkredit'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function isFinal() {
			$data = array('T' => 'Tidak', 'Y' => 'Ya');
			
			return $data;
		}
		
		
		function statusUsulan() {
			$data = array('' => 'Belum Diajukan', 'A' => 'Diajukan', 'Y' => 'Sudah Divalidasi');
			
			return $data;
		}
		
		//list bidang 1A
		function listQueryBidangIA($r_key) {
			$sql = "select r.*,m.kodekegiatan ||' - '|| m.namakegiatan as kegiatan,j.namapendidikan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang1a')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('lv_jenjangpendidikan')." j on j.idpendidikan = r.jenjang
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidangIA($r_subkey) {
			$sql = "select r.*,m.kodekegiatan ||' - '|| m.namakegiatan as kegiatan,j.namapendidikan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasi
					from ".self::table('ak_bidang1a')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('lv_jenjangpendidikan')." j on j.idpendidikan = r.jenjang
					where nobidangia='$r_subkey'";
			
			return $sql;
		}
		
		//pop pendidikan
		function getPendidikan($conn,$idpegawai){			
			$sql = "select r.*, p.namapendidikan,f.namafakultas,j.namajurusan,b.namabidang,
					case when r.kodept is not null then t.namapt else namainstitusi end as namainstitusipend
					from ".self::table('pe_rwtpendidikan')." r 
					left join ".self::table('lv_jenjangpendidikan')." p on p.idpendidikan=r.idpendidikan
					left join ".self::table('ms_pt')." t on t.kodept=r.kodept
					left join ".self::table('ms_fakultas')." f on f.kodefakultas=r.kodefakultas
					left join ".self::table('ms_jurusan')." j on j.kodejurusan=r.kodejurusan
					left join ".self::table('ms_bidang')." b on b.kodebidang=r.kodebidang
					where r.idpegawai='$idpegawai' and r.isvalid='Y'
					order by p.urutan desc,r.tglijazah desc";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['nourutrpen'] = $row['nourutrpen'];
				$t_data['idpendidikan'] = $row['idpendidikan'];
				$t_data['namapendidikan'] = $row['namapendidikan'];
				$t_data['namainstitusipend'] = $row['namainstitusipend'];
				$t_data['namafakultas'] = $row['namafakultas'];
				$t_data['namajurusan'] = $row['namajurusan'];
				$t_data['namabidang'] = $row['namabidang'];
				$t_data['noijazah'] = $row['noijazah'];
				$t_data['tglijazah'] = $row['tglijazah'];
				$t_data['noijazahnegara'] = $row['noijazahnegara'];
				$t_data['tglijazahnegara'] = $row['tglijazahnegara'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		//list bidang 1B
		function listQueryBidangIB($r_key) {
			$sql = "select r.*,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,
					substring(thnakademik,1,4)||'/'||substring(thnakademik,5,4)||' '||case when semester = '01' then 'Ganjil' when semester = '02' then 'Genap' end as periodekuliah,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang1b')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidangIB($r_subkey) {
			$sql = "select r.*,substring(r.thnakademik,1,4) as tahun1,substring(r.thnakademik,5,4) as tahun2,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasi,
					r2.idkegiatan as idkegiatan2, r2.sksdiakui as sksdiakui2
					from ".self::table('ak_bidang1b')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('ak_bidang1b')." r2 on r2.refnobidangib = r.idkegiatan
					where r.nobidangib='$r_subkey'";
			
			return $sql;
		}
				
		function PeriodeSemester() {
			$data = array('01' => 'Ganjil', '02' => 'Genap');
			
			return $data;
		}
		
		function hitungKredit($conn,$idkegiatan,$sks){
			$kredit = $conn->GetOne("select stdkredit from ".self::table('ms_penilaian')." where idkegiatan = $idkegiatan");
			$nilai = (float)$kredit*(float)$sks;
			
			return $nilai;
		}
		
		//simpan pecahan sks
		function savePecahan($conn,$r_subkey,$idkegiatan2,$sks2){			
			//select dari referensi pecahan sksnya
			$record = $conn->GetRow("select * from ".self::table('ak_bidang1b')." where nobidangib = $r_subkey");
			
			unset($record['nobidangib'],$record['refnobidangib'],$record['stdkredit'],$record['nilaikredit'],$record['idkegiatan'],$record['sksdiakui']);
			$isexists = $conn->GetOne("select 1 from ".self::table('ak_bidang1b')." where refnobidangib = $r_subkey");
			if(empty($isexists)){
				$record['refnobidangib'] = $r_subkey;

				if($idkegiatan2 != 'null' and $sks2 != 'null'){
					$kredit = $conn->GetOne("select stdkredit from ".self::table('ms_penilaian')." where idkegiatan = $idkegiatan2");
					$record['stdkredit'] = (float)$kredit * (float)$sks2;
					$record['nilaikredit'] = $record['stdkredit'];
					$record['idkegiatan'] = $idkegiatan2;
					$record['sksdiakui'] = $sks2;
					
					$err = self::insertRecord($conn,$record,false,'ak_bidang1b');
				}
			}else
				$err = self::updateRecord($conn,$record,$r_subkey,false,'ak_bidang1b','refnobidangib');
			
			return self::saveStatus($conn);
		}
		
		//hapus pecahan sks
		function deletePecahan($conn,$r_subkey){
			$err = self::delete($conn,$r_subkey,'ak_bidang1b','refnobidangib');
			
			return self::deleteStatus($conn);
		}
		
		//cek apakah pecahan
		function isPecahan($conn,$r_subkey){
			$ispecahan = $conn->GetOne("select 1 from ".self::table('ak_bidang1b')." where nobidangib = $r_subkey and refnobidangib is not null");
			
			$istrue = $ispecahan == '1' ? true : false;
			
			return $istrue;
		}
		
		//cek apakah form mengajar
		function isMengajar($conn,$r_subkey){
			$ismengajar = $conn->GetOne("select 1 from ".self::table('ak_bidang1b')." where nobidangib = $r_subkey and ismengajar = 'Y'");
			
			$istrue = $ismengajar == '1' ? true : false;
			
			return $ismengajar;
		}
		
		//list bidang penelitian
		function listQueryBidang2($r_key) {
			$sql = "select r.*,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,p.judulpenelitian,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang2')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('pe_penelitian')." p on p.idpenelitian = r.idpenelitian
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidang2($r_subkey) {
			$sql = "select r.*,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,p.judulpenelitian,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang2')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('pe_penelitian')." p on p.idpenelitian = r.idpenelitian
					where nobidangii='$r_subkey'";
			
			return $sql;
		}
		
		//pop penelitian
		function getPenelitian($conn,$r_key){	
			//select pengabdian pegawai
			$rsr = $conn->Execute("select ak.idpenelitian from ".self::table('ak_bidang2')." ak
					left join ".self::table('pe_penelitian')." r on r.idpenelitian = ak.idpenelitian
					where ak.idpegawai='$r_key'");
			while($rowr = $rsr->FetchRow()){
				$aid[] = $rowr['idpenelitian'];
			}
			
			if(count($aid)>0)
				$inid = implode("','",$aid);
					
			$sql = "select r.*, o.outputpenelitian,p.idkegiatan,p.kodekegiatan,p.namakegiatan,cast(p.stdkredit as varchar) as stdkredit
					from ".self::table('pe_penelitian')." r 
					left join ".self::table('lv_outputpenelitian')." o on o.kodeoutput=r.kodeoutput
					left join ".self::table('ms_penilaian')." p on p.idkegiatan=o.idkegiatan
					where r.idpegawai='$r_key' and r.isvalid = 'Y'";
			$sql .= !empty($inid) ? " and r.idpenelitian not in ('$inid')" : "";
			$sql .= " order by r.tglmulai desc";
					
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idpenelitian'] = $row['idpenelitian'];
				$t_data['judulpenelitian'] = $row['judulpenelitian'];
				$t_data['outputpenelitian'] = $row['outputpenelitian'];
				$t_data['lokasipenelitian'] = $row['lokasipenelitian'];
				$t_data['tglmulai'] = $row['tglmulai'];
				$t_data['tglselesai'] = $row['tglselesai'];
				$t_data['idkegiatan'] = $row['idkegiatan'];
				$t_data['kodekegiatan'] = $row['kodekegiatan'];
				$t_data['namakegiatan'] = preg_replace("/[^a-z0-9_\-\.]/i"," ",$row['namakegiatan']);
				$t_data['stdkredit'] = $row['stdkredit'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		//mendapatkan nilai angka kredit berdasarkan kontributor ke
		function getAKPenelitian($conn,$idpenelitian,$idkegiatan){
			//select nilai kredit
			$kredit = $conn->GetOne("select stdkredit from ".self::table('ms_penilaian')." where idkegiatan = $idkegiatan");
			
			//select kontributor
			$rwt = $conn->GetRow("select mandiriteam,kontributorke,jmlteambagi from ".self::table('pe_penelitian')." where idpenelitian = $idpenelitian");
			if($rwt['mandiriteam'] == 'T'){
				if($rwt['kontributorke'] == '1')
					$nilaikredit = 60/100 * $kredit;
				else{						
					$jml = empty($rwt['jmlteambagi']) ? '1' : $rwt['jmlteambagi'];
						
					$nilaikredit = (40/100 * $kredit)/$jml;
				}
			}else
				$nilaikredit = $kredit;
				
			return number_format($nilaikredit,2);
		}
		
		//list bidang pengabdian
		function listQueryBidang3($r_key) {
			$sql = "select r.*,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang3')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidang3($r_subkey) {
			$sql = "select r.*,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang3')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where nobidangiii='$r_subkey'";
			
			return $sql;
		}
		
		//pop pengabdian
		function getPengabdian($conn,$r_key){
			//select pengabdian pegawai
			$rsr = $conn->Execute("select ak.idpkm from ".self::table('ak_bidang3')." ak
					left join ".self::table('pe_pkm')." r on r.idpkm = ak.idpkm
					where ak.idpegawai='$r_key'");
			while($rowr = $rsr->FetchRow()){
				$aid[] = $rowr['idpkm'];
			}
			
			if(count($aid)>0)
				$inid = implode("','",$aid);
			
			$sql = "select r.*, j.namapkm,p.idkegiatan,p.kodekegiatan,p.namakegiatan as kegiatan,cast(p.stdkredit as varchar) as stdkredit
					from ".self::table('pe_pkm')." r 
					left join ".self::table('lv_jenispkm')." j on j.kodepkm=r.kodepkm
					left join ".self::table('ms_penilaian')." p on p.idkegiatan=j.idkegiatan
					where r.idpegawai='$r_key' and r.isvalid = 'Y'";
			$sql .= !empty($inid) ? " and r.idpkm not in ('$inid')" : "";
			$sql .= " order by r.tglawal desc";
			
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idpkm'] = $row['idpkm'];
				$t_data['namakegiatan'] = $row['namakegiatan'];
				$t_data['namapkm'] = $row['namapkm'];
				$t_data['tempatkegiatan'] = $row['tempatkegiatan'];
				$t_data['tglawal'] = $row['tglawal'];
				$t_data['tglakhir'] = $row['tglakhir'];
				$t_data['idkegiatan'] = $row['idkegiatan'];
				$t_data['kodekegiatan'] = $row['kodekegiatan'];
				$t_data['kegiatan'] = preg_replace("/[^a-z0-9_\-\.]/i"," ",$row['kegiatan']);
				$t_data['stdkredit'] = $row['stdkredit'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
				
		//mendapatkan nilai angka kredit berdasarkan kontributor ke
		function getAKPKM($conn,$idpkm,$idkegiatan){
			//select nilai kredit
			$kredit = $conn->GetOne("select stdkredit from ".self::table('ms_penilaian')." where idkegiatan = $idkegiatan");
			
			//select kontributor
			$rwt = $conn->GetRow("select mandiriteam,kontributorke,jmlteambagi from ".self::table('pe_pkm')." where idpkm = $idpkm");
			if($rwt['mandiriteam'] == 'T'){
				if($rwt['kontributorke'] == '1')
					$nilaikredit = 60/100 * $kredit;
				else{						
					$jml = empty($rwt['jmlteambagi']) ? '1' : $rwt['jmlteambagi'];
						
					$nilaikredit = (40/100 * $kredit)/$jml;
				}
			}else
				$nilaikredit = $kredit;
				
			return number_format($nilaikredit,2);
		}
		
		//list bidang 4
		function listQueryBidang4($r_key) {
			$sql = "select r.*,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang4')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidang4($r_subkey) {
			$sql = "select r.*,substring(thnakademik,1,4) as tahun1,substring(thnakademik,5,4) as tahun2,m.kodekegiatan||' - '||m.namakegiatan as kegiatan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasi
					from ".self::table('ak_bidang4')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where nobidangiv='$r_subkey'";
			
			return $sql;
		}
		
		//cek apakah jabatan ada
 		function cekFungsional($conn,$r_key){
			$sql = "select tmtmulai from ".static::schema()."pe_rwtfungsional 
					where idpegawai = $r_key and jenisjabatan = 'L' and isvalid = 'Y' 
					order by tmtmulai desc limit 1";
			$tmt = $conn->GetOne($sql);
			
			if (empty($tmt))
				return 0;
			else
				return 1;
		}	
		
		//list simulasi angka kredit
		function listQuerySimulasiAK($r_key) {
			$sql = "select r.*,j1.jabatanfungsional as jabatanasal,j2.jabatanfungsional as jabatantujuan,
					case when r.statususulan = 'Y' then 'Sudah Divalidasi' when r.statususulan = 'A' then 'Diajukan' else 'Belum Diajukan' end as statususul,
					substring(r.periodeakreditasi,1,4) as tahun, substring(r.periodeakreditasi,5,2) as semester,u.kodeunit as unit
					from ".self::table('ak_skdosen')." r 
					left join ".self::table('ms_fungsional')." j1 on j1.idjfungsional = r.fungsionalasal
					left join ".self::table('ms_fungsional')." j2 on j2.idjfungsional = r.fungsionaltujuan
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditSimulasiAK($r_subkey) {
			$sql = "select r.*,substring(r.periodeakreditasi,1,4) as tahun,substring(r.periodeakreditasi,5,2) as semester,u.kodeunit as unit,
					j1.jabatanfungsional as jabatanasal,j2.jabatanfungsional as jabatantujuan,j2.angkakredit,
					case when r.statususulan = 'Y' then 'Disetujui' when r.statususulan = 'A' then 'Diajukan' end as statususul
					from ".self::table('ak_skdosen')." r 
					left join ".self::table('ms_fungsional')." j1 on j1.idjfungsional = r.fungsionalasal
					left join ".self::table('ms_fungsional')." j2 on j2.idjfungsional = r.fungsionaltujuan
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					where r.nourutakd='$r_subkey'";
			
			return $sql;
		}
		
		//mendapatkan data jabatan fungsional terakhir pegawai
		function getFungsional($conn,$r_key){
			$jab = $conn->GetRow("select r.*,
					cast(r.nilaibidang1a as varchar) as nilaibidang1a,cast(r.nilaibidang1b as varchar) as nilaibidang1b,
					cast(r.nilaibidang2 as varchar) as nilaibidang2,cast(r.nilaibidang3 as varchar) as nilaibidang3,
					cast(r.nilaibidang4 as varchar) as nilaibidang4,
					cast(r.sisabidang1a as varchar) as sisabidang1a,cast(r.sisabidang1b as varchar) as sisabidang1b,
					cast(r.sisabidang2 as varchar) as sisabidang2,cast(r.sisabidang3 as varchar) as sisabidang3,
					cast(r.sisabidang4 as varchar) as sisabidang4,f.jabatanfungsional,cast(f.angkakredit as varchar) as angkakredit
					from ".self::table('pe_rwtfungsional')." r
					left join ".self::table('ms_fungsional')." f on f.idjfungsional = r.idjfungsional
					where r.idpegawai = '$r_key' and r.isvalid = 'Y'
					order by r.tmtmulai desc
					limit 1");
					
			return $jab;
		}
				
		function jabatanFungsional($conn,$id) {
			$sid = !empty($id) ? 'where idjfungsional::integer > '.$id.'' : '';
			$sql = "select idjfungsional, jabatanfungsional from ".static::schema()."ms_fungsional {$sid} order by angkakredit";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getPeriode($conn,$r_key) {
			$periode = $conn->GetOne("select periodeakreditasi from ".self::table('ak_skdosen')." where nourutakd = $r_key");
			
			return $periode;
		}
		
		function getBidangIA($conn,$r_key,$periode){
			$where = !empty($periode) ? " and periodeakreditasi = '$periode'" : " and (statusvalidasi = '' or statusvalidasi is null) ";
			
			$sql = "select k.*,cast(k.stdkredit as varchar) as stdkredit,cast(k.nilaikredit as varchar) as nilaikredit,p.namapendidikan as namakegiatan,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,cast(s1.stdkredit as varchar) as kreditmax,s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
					from ".self::table('ak_bidang1a')." k 
					left join ".self::table('lv_jenjangpendidikan')." p on p.idpendidikan = k.jenjang
					left join ".self::table('ms_penilaian')." s1 on s1.idkegiatan = k.idkegiatan 
					left join ".self::table('ms_penilaian')." s2 on s2.idkegiatan = s1.parentidkegiatan 
					where k.idpegawai = $r_key and k.isvalid = 'Y' {$where}
					order by kodeparent";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data[$row['nobidangia']]['data'] = $row;
			}
			
			return $t_data;
		}
				
		function getBidangIB($conn,$r_key,$periode){
			$where = !empty($periode) ? " and periodeakreditasi = '$periode'" : " and (statusvalidasi = '' or statusvalidasi is null) ";
			$jab = self::getFungsional($conn,$r_key);
			
			$sql = "select k.*,cast(k.stdkredit as varchar) as stdkredit,cast(k.nilaikredit as varchar) as nilaikredit,
					substring(k.thnakademik,1,4)||'/'||substring(k.thnakademik,5,4)||' '||case when k.semester = '01' then 'Ganjil' when k.semester = '02' then 'Genap' end as periodekuliah,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,cast(s1.stdkredit as varchar) as kreditmax,
					s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
					from ".self::table('ak_bidang1b')." k 
					left join ".self::table('ms_penilaian')." s1 on s1.idkegiatan = k.idkegiatan 
					left join ".self::table('ms_penilaian')." s2 on s2.idkegiatan = s1.parentidkegiatan 
					where k.idpegawai = $r_key and k.isvalid = 'Y' and k.tglawal > '".$jab['tmtmulai']."' {$where}
					order by kodeparent";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data[$row['nobidangib']]['data'] = $row;
			}
			
			return $t_data;
		}
		
		function getBidangII($conn,$r_key,$periode){
			$where = !empty($periode) ? " and periodeakreditasi = '$periode'" : " and (statusvalidasi = '' or statusvalidasi is null) ";
			
			$sql = "select k.*,cast(k.stdkredit as varchar) as stdkredit,cast(k.nilaikredit as varchar) as nilaikredit,p.judulpenelitian as namakegiatan,substring(cast(tgl as varchar),1,4) as periode,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,cast(s1.stdkredit as varchar) as kreditmax,s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
					from ".self::table('ak_bidang2')." k 
					left join ".self::table('pe_penelitian')." p on p.idpenelitian = k.idpenelitian
					left join ".self::table('ms_penilaian')." s1 on s1.idkegiatan = k.idkegiatan 
					left join ".self::table('ms_penilaian')." s2 on s2.idkegiatan = s1.parentidkegiatan 
					where k.idpegawai = $r_key and k.isvalid = 'Y' {$where}
					order by kodeparent";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data[$row['nobidangii']]['data'] = $row;
			}
			
			return $t_data;
		}
		
		function getBidangIII($conn,$r_key,$periode){
			$where = !empty($periode) ? " and periodeakreditasi = '$periode'" : " and (statusvalidasi = '' or statusvalidasi is null) ";
			$jab = self::getFungsional($conn,$r_key);
			
			$sql = "select k.*,cast(k.stdkredit as varchar) as stdkredit,cast(k.nilaikredit as varchar) as nilaikredit,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,cast(s1.stdkredit as varchar) as kreditmax,
					s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
					from ".self::table('ak_bidang3')." k 
					left join ".self::table('ms_penilaian')." s1 on s1.idkegiatan = k.idkegiatan 
					left join ".self::table('ms_penilaian')." s2 on s2.idkegiatan = s1.parentidkegiatan 
					where k.idpegawai = $r_key and k.isvalid = 'Y' and k.tgl > '".$jab['tmtmulai']."' {$where}
					order by kodeparent";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data[$row['nobidangiii']]['data'] = $row;
			}
			
			return $t_data;
		}
		
		function getBidangIV($conn,$r_key,$periode){
			$where = !empty($periode) ? " and periodeakreditasi = '$periode'" : " and (statusvalidasi = '' or statusvalidasi is null) ";
			$jab = self::getFungsional($conn,$r_key);
			
			$sql = "select k.*,cast(k.stdkredit as varchar) as stdkredit,cast(k.nilaikredit as varchar) as nilaikredit,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,cast(s1.stdkredit as varchar) as kreditmax,
					s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
					from ".self::table('ak_bidang4')." k 
					left join ".self::table('ms_penilaian')." s1 on s1.idkegiatan = k.idkegiatan 
					left join ".self::table('ms_penilaian')." s2 on s2.idkegiatan = s1.parentidkegiatan 
					where k.idpegawai = $r_key and k.isvalid = 'Y' and k.tglmulai > '".$jab['tmtmulai']."' {$where}
					order by kodeparent";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data[$row['nobidangiv']]['data'] = $row;
			}
			
			return $t_data;
		}
		
		function getNilaiIA($conn,$r_key,$periode){
			$where = " and periodeakreditasi = '$periode' and statusvalidasi in ('A','Y') ";
			
			$sql = "select cast(nilaikredit as varchar) as nilaikredit from ".self::table('ak_bidang1a')." where idpegawai = '$r_key' and isvalid = 'Y'{$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang1a += $row['nilaikredit'];
			}
			
			return $nbidang1a;
		}
		
		function getNilaiIB($conn,$r_key,$periode){
			$where = " and periodeakreditasi = '$periode' and statusvalidasi in ('A','Y') ";
			$jab = self::getFungsional($conn,$r_key);
			
			$sql = "select cast(nilaikredit as varchar) as nilaikredit from ".self::table('ak_bidang1b')." where idpegawai = '$r_key' and isvalid = 'Y' and tglawal > '".$jab['tmtmulai']."'{$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang1b += $row['nilaikredit'];
			}
			
			return $nbidang1b;
		}
		
		function getNilaiII($conn,$r_key,$periode){
			$where = " and periodeakreditasi = '$periode' and statusvalidasi in ('A','Y') ";
			
			$sql = "select cast(nilaikredit as varchar) as nilaikredit from ".self::table('ak_bidang2')." where idpegawai = '$r_key' and isvalid = 'Y'{$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang2 += $row['nilaikredit'];
			}
			
			return $nbidang2;
		}
		
		function getNilaiIII($conn,$r_key,$periode){
			$where = " and periodeakreditasi = '$periode' and statusvalidasi in ('A','Y') ";
			$jab = self::getFungsional($conn,$r_key);
			
			$sql = "select cast(nilaikredit as varchar) as nilaikredit from ".self::table('ak_bidang3')." where idpegawai = '$r_key' and isvalid = 'Y' and tgl > '".$jab['tmtmulai']."'{$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang3 += $row['nilaikredit'];
			}
			
			return $nbidang3;
		}
		
		function getNilaiIV($conn,$r_key,$periode){
			$where = " and periodeakreditasi = '$periode' and statusvalidasi in ('A','Y') ";
			$jab = self::getFungsional($conn,$r_key);
			
			$sql = "select cast(nilaikredit as varchar) as nilaikredit from ".self::table('ak_bidang4')." where idpegawai = '$r_key' and isvalid = 'Y' and tglmulai > '".$jab['tmtmulai']."'{$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang4 += $row['nilaikredit'];
			}
			
			return $nbidang4;
		}
		
		function updateRWTAkreditasi($conn,$r_subkey){
			$row = $conn->GetRow("select periodeakreditasi,idpegawai from ".self::table('ak_skdosen')." where nourutakd = '$r_subkey'");
			
			//update kegiatan bidang yang tidak jadi diajukan
			$ok = $conn->Execute("update ".self::table('ak_bidang1a')." set periodeakreditasi=null,nilaikredit=stdkredit,statusvalidasi=null where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."'");
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang1b')." set periodeakreditasi=null,nilaikredit=stdkredit,statusvalidasi=null where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."'");
			else
				$conn->RollbackTrans();
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang2')." set periodeakreditasi=null,nilaikredit=stdkredit,statusvalidasi=null where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."'");
			else
				$conn->RollbackTrans();
			
			if($ok)	
				$ok = $conn->Execute("update ".self::table('ak_bidang3')." set periodeakreditasi=null,nilaikredit=stdkredit,statusvalidasi=null where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."'");
			else
				$conn->RollbackTrans();
			
			if($ok)	
				$ok = $conn->Execute("update ".self::table('ak_bidang4')." set periodeakreditasi=null,nilaikredit=stdkredit,statusvalidasi=null where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."'");
			else
				$conn->RollbackTrans();
			
			return $conn->ErrorNo();
		}
		
		function validasiRWT($conn,$r_subkey){
			$row = $conn->GetRow("select periodeakreditasi,idpegawai from ".self::table('ak_skdosen')." where nourutakd = '$r_subkey'");
			
			//mengisikan tanggal validasi dan isfinal
			$ok = $conn->Execute("update ".self::table('ak_bidang1a')." set tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi = 'Y'");
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang1b')." set tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi = 'Y'");
			else
				$conn->RollbackTrans();
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang2')." set tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi = 'Y'");
			else
				$conn->RollbackTrans();
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang3')." set tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi = 'Y'");
			else
				$conn->RollbackTrans();
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang4')." set tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi = 'Y'");
			else
				$conn->RollbackTrans();
				
			//update null kegiatan bidang yang tidak jadi diajukan
			$ok = $conn->Execute("update ".self::table('ak_bidang1a')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi is null");			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang1b')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi is null");
			else
				$conn->RollbackTrans();
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang2')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi is null");
			else
				$conn->RollbackTrans();
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang3')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi is null");
			else
				$conn->RollbackTrans();
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang4')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi='".$row['periodeakreditasi']."' and idpegawai='".$row['idpegawai']."' and statusvalidasi is null");
			else
				$conn->RollbackTrans();
						
			return $conn->ErrorNo();
		}
		
		//jenis bidang utk prosentase
		function jenisBidangProsentase() {
			$data = array('I' => 'Bidang I', 'II' => 'Bidang II', 'III' => 'Bidang III', 'IV' => 'Bidang IV');
			
			return $data;
		}
		
		//prosentase angka kredit
		function getProsentase($conn){			
			$sql = "select * from ".self::table('ms_prosentaseak')." where isaktif = 'Y'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data[$row['bidangak']] = $row['prosentase'];
			}
			
			return $t_data;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			if($col == 'unit') {
				global $conn, $conf;
				require_once($conf['gate_dir'].'model/m_unit.php');
				
				$row = mUnit::getData($conn,$key);
				
				return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
			if($col == 'semester')
				return "substring(r.periodeakreditasi,5,2) = '$key'";
			if($col == 'tahun')
				return "substring(r.periodeakreditasi,1,4) = '$key'";
		}
		
		//*********************************************L A P O R A N****************************************************
		//list ak_bidang1a
		function listQueryRepAngkaKredit1A($r_periode){
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,p.idpegawai,
					u.kodeunit||' - '||u.namaunit as unit,j.jenispegawai
					from ".self::table('ms_pegawai')." p
					join ".self::table('ak_bidang1a')." r on r.idpegawai = p.idpegawai and r.periodeakreditasi = '$r_periode'
					left join ".self::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					group by ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),p.nik,p.idpegawai,u.kodeunit||' - '||u.namaunit,j.jenispegawai";
			
			return $sql;
		}
		
		//list ak_bidang1b
		function listQueryRepAngkaKredit1B($r_periode){
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,p.idpegawai,
					u.kodeunit||' - '||u.namaunit as unit,j.jenispegawai
					from ".self::table('ms_pegawai')." p
					join ".self::table('ak_bidang1b')." r on r.idpegawai = p.idpegawai and r.periodeakreditasi = '$r_periode'
					left join ".self::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					group by ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),p.nik,p.idpegawai,u.kodeunit||' - '||u.namaunit,j.jenispegawai";
			
			return $sql;
		}
		
		//list ak_bidang2
		function listQueryRepAngkaKredit2($r_periode){
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,p.idpegawai,
					u.kodeunit||' - '||u.namaunit as unit,j.jenispegawai
					from ".self::table('ms_pegawai')." p
					join ".self::table('ak_bidang2')." r on r.idpegawai = p.idpegawai and r.periodeakreditasi = '$r_periode'
					left join ".self::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					group by ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),p.nik,p.idpegawai,u.kodeunit||' - '||u.namaunit,j.jenispegawai";
			
			return $sql;
		}
		
		//list ak_bidang3
		function listQueryRepAngkaKredit3($r_periode){
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,p.idpegawai,
					u.kodeunit||' - '||u.namaunit as unit,j.jenispegawai
					from ".self::table('ms_pegawai')." p
					join ".self::table('ak_bidang3')." r on r.idpegawai = p.idpegawai and r.periodeakreditasi = '$r_periode'
					left join ".self::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					group by ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),p.nik,p.idpegawai,u.kodeunit||' - '||u.namaunit,j.jenispegawai";
			
			return $sql;
		}
		
		//list ak_bidang4
		function listQueryRepAngkaKredit4($r_periode){
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,p.idpegawai,
					u.kodeunit||' - '||u.namaunit as unit,j.jenispegawai
					from ".self::table('ms_pegawai')." p
					join ".self::table('ak_bidang4')." r on r.idpegawai = p.idpegawai and r.periodeakreditasi = '$r_periode'
					left join ".self::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					group by ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),p.nik,p.idpegawai,u.kodeunit||' - '||u.namaunit,j.jenispegawai";
			
			return $sql;
		}
		
		//list dupak
		function listQueryRepDupak(){
			$sql = "select r.nourutakd,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,
					u.kodeunit||' - '||u.namaunit as unit,j.jenispegawai
					from ".self::table('ak_skdosen')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit";
			
			return $sql;
		}
		
		//master penilaian
		function getMasterPenilaian($conn){
			$sql = "select idkegiatan,kodekegiatan,kodeurutan,namakegiatan,bidangkegiatan
					from ".static::schema()."ms_penilaian
					order by kodeurutan";				
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		//cari atasan
		function getAtasanPegawai($conn,$r_idpegawai){
			$jabatanatasan = $conn->GetOne("select idjstrukturalatasan from ".static::schema()."ms_pegawai where idpegawai = '$r_idpegawai'");
			
			if(empty($jabatanatasan)){
				$unitdosen = $conn->GetOne("select idunit from ".static::schema()."ms_pegawai where idpegawai = '$r_idpegawai'");
				$jabatanatasan = $conn->GetOne("select idjstruktural from ".static::schema()."ms_struktural where idunit = '$unitdosen' and level=3 ");	
			}
			
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik as npp,p.idpegawai,
					pk.namapangkat ||'/ '||pk.golongan as namagolongan,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=p.idjfungsional
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					where p.idjstruktural='$jabatanatasan'";
			$a_data = $conn->GetRow($sql);
			
			return $a_data;
		}
		
		//laporan ak bidang 1a
		function getListBidang1A($conn,$r_kode,$r_periode){			
			//select data pegawai
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik as npp,p.idpegawai,
					pk.namapangkat ||'/ '||pk.golongan as namagolongan,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=p.idjfungsional
					where p.idpegawai in ('$r_kode')";
			$rs = $conn->Execute($sql);
			
			//select riwayat bidang
			$sql = "select r.*, m1.namakegiatan as indexakrediasi, m2.namakegiatan as indexakreditasiparent
					from ".static::schema()."ak_bidang1a r
					left join ".static::schema()."ms_penilaian m1 on r.idkegiatan = m1.idkegiatan 
					left join ".static::schema()."ms_penilaian m2 on m1.parentidkegiatan = m2.idkegiatan
					where r.idpegawai in ('$r_kode') and r.periodeakreditasi='$r_periode'
					order by r.idpegawai,indexakreditasiparent";
			$rsd = $conn->Execute($sql);
			
			while($rowd = $rsd->FetchRow()){
				$a_det['indexakreditasiparent'] = $rowd['indexakreditasiparent'];
				$a_det['indexakrediasi'] = $rowd['indexakrediasi'];
				$a_det['namainstitusi'] = $rowd['namainstitusi'];
				$a_det['tglijazah'] = $rowd['tglijazah'];
				$a_det['noijazah'] = $rowd['noijazah'];
				$a_det['nilaikredit'] = $rowd['nilaikredit'];				
				
				$a_col[$rowd['idpegawai']][$rowd['indexakreditasiparent']]++;				
				$a_detail[$rowd['idpegawai']][] = $a_det;
			}
			
			$a_data = array('list' => $rs, 'detail' => $a_detail, 'colspan' => $a_col);
			
			return $a_data;			
		}
		
		//laporan ak bidang 1b
		function getListBidang1B($conn,$r_kode,$r_periode){			
			//select data pegawai
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik as npp,p.idpegawai,
					pk.namapangkat ||'/ '||pk.golongan as namagolongan,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=p.idjfungsional
					where p.idpegawai in ('$r_kode')";
			$rs = $conn->Execute($sql);
			
			//select riwayat bidang
			$sql = "select r.*, m1.namakegiatan as indexakrediasi, m2.namakegiatan as indexakreditasiparent
					from ".static::schema()."ak_bidang1b r
					left join ".static::schema()."ms_penilaian m1 on r.idkegiatan = m1.idkegiatan 
					left join ".static::schema()."ms_penilaian m2 on m1.parentidkegiatan = m2.idkegiatan
					where r.idpegawai in ('$r_kode') and r.periodeakreditasi='$r_periode'
					order by r.idpegawai,indexakreditasiparent";
			$rsd = $conn->Execute($sql);
			
			while($rowd = $rsd->FetchRow()){
				$a_det['indexakreditasiparent'] = $rowd['indexakreditasiparent'];
				$a_det['indexakrediasi'] = $rowd['indexakrediasi'];
				$a_det['sks'] = $rowd['sks'];
				$a_det['namainstitusi'] = $rowd['namainstitusi'];
				$a_det['tglijazah'] = $rowd['tglijazah'];
				$a_det['noijazah'] = $rowd['noijazah'];
				$a_det['nilaikredit'] = $rowd['nilaikredit'];				
				
				$a_col[$rowd['idpegawai']][$rowd['indexakreditasiparent']]++;				
				$a_detail[$rowd['idpegawai']][] = $a_det;
			}
			
			$a_data = array('list' => $rs, 'detail' => $a_detail, 'colspan' => $a_col);
			
			return $a_data;			
		}
		
		//laporan ak bidang 2
		function getListBidang2($conn,$r_kode,$r_periode){			
			//select data pegawai
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik as npp,p.idpegawai,
					".static::schema.".f_namalengkap(null,p.namadepan,p.namatengah,p.namabelakang,null) as nama,pk.namapangkat ||'/ '||pk.golongan as namagolongan,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=p.idjfungsional
					where p.idpegawai in ('$r_kode')";
			$rs = $conn->Execute($sql);
			
			//select riwayat bidang
			$sql = "select r.*, m1.namakegiatan as indexakrediasi, m2.namakegiatan as indexakreditasiparent, pe.judulpenelitian,substr(pe.tglmulai::text,1,4) as tahun,r.idpenelitian
					from ".static::schema()."ak_bidang2 r
					left join ".static::schema()."ms_penilaian m1 on r.idkegiatan = m1.idkegiatan 
					left join ".static::schema()."ms_penilaian m2 on m1.parentidkegiatan = m2.idkegiatan
					left join ".static::schema()."pe_penelitian pe on pe.idpenelitian = r.idpenelitian
					where r.idpegawai in ('$r_kode') and r.periodeakreditasi='$r_periode'
					order by r.idpegawai,indexakreditasiparent";
			$rsd = $conn->Execute($sql);
			
			while($rowd = $rsd->FetchRow()){
				$a_det['indexakreditasiparent'] = $rowd['indexakreditasiparent'];
				$a_det['indexakrediasi'] = $rowd['indexakrediasi'];
				$a_det['idpenelitian'] = $rowd['idpenelitian'];
				$a_det['judulpenelitian'] = $rowd['judulpenelitian'];
				$a_det['tahun'] = $rowd['tahun'];
				$a_det['stdkredit'] = $rowd['stdkredit'];
				$a_det['nilaikredit'] = $rowd['nilaikredit'];
				$a_det['keterangan'] = $rowd['keterangan'];				
				
				$a_col[$rowd['idpegawai']][$rowd['indexakreditasiparent']]++;				
				$a_detail[$rowd['idpegawai']][] = $a_det;
			}	
			
			$a_data = array('list' => $rs, 'detail' => $a_detail, 'colspan' => $a_col);
			
			return $a_data;			
		}
		
		//select tim penelitian
		function getTimPenelitian($conn,$r_idpenelitian){
			$sql = "select ".static::schema.".f_namalengkap(null,p.namadepan,p.namatengah,p.namabelakang,null) as nama
					from ".static::schema()."pe_timpenelitian pt
					left join ".static::schema()."ms_pegawai p on p.idpegawai=pt.idpegawai
					where pt.idpenelitian = '$r_idpenelitian'";
			
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nama[] = $row['nama'];
			}
			$a_nama = implode(", ",$nama);
			
			return $a_nama;
		}
		
		//laporan ak bidang 3
		function getListBidang3($conn,$r_kode,$r_periode){			
			//select data pegawai
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik as npp,p.idpegawai,
					".static::schema.".f_namalengkap(null,p.namadepan,p.namatengah,p.namabelakang,null) as nama,pk.namapangkat ||'/ '||pk.golongan as namagolongan,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=p.idjfungsional
					where p.idpegawai in ('$r_kode')";
			$rs = $conn->Execute($sql);
			
			//select riwayat bidang
			$sql = "select r.*, m1.namakegiatan as indexakrediasi, m2.namakegiatan as indexakreditasiparent,
					jn.namapkm as bentuk, pk.tempatkegiatan,pk.tglawal
					from ".static::schema()."ak_bidang3 r
					left join ".static::schema()."ms_penilaian m1 on r.idkegiatan = m1.idkegiatan 
					left join ".static::schema()."ms_penilaian m2 on m1.parentidkegiatan = m2.idkegiatan
					left join ".static::schema()."pe_pkm pk on pk.idpkm = r.idpkm
					left join ".static::schema()."lv_jenispkm jn on jn.kodepkm = pk.kodepkm
					where r.idpegawai in ('$r_kode') and r.periodeakreditasi='$r_periode'
					order by r.idpegawai,indexakreditasiparent";
			$rsd = $conn->Execute($sql);
			
			while($rowd = $rsd->FetchRow()){
				$a_det['indexakreditasiparent'] = $rowd['indexakreditasiparent'];
				$a_det['indexakrediasi'] = $rowd['indexakrediasi'];
				$a_det['bentuk'] = $rowd['bentuk'];
				$a_det['tempatkegiatan'] = $rowd['tempatkegiatan'];
				$a_det['tglawal'] = $rowd['tglawal'];				
				$a_det['nilaikredit'] = $rowd['nilaikredit'];				
				$a_det['keterangan'] = $rowd['keterangan'];				
				
				$a_col[$rowd['idpegawai']][$rowd['indexakreditasiparent']]++;				
				$a_detail[$rowd['idpegawai']][] = $a_det;
			}
			
			$a_data = array('list' => $rs, 'detail' => $a_detail, 'colspan' => $a_col);
			
			return $a_data;
		}
		
		//laporan ak bidang 4
		function getListBidang4($conn,$r_kode,$r_periode){			
			//select data pegawai
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik as npp,p.idpegawai,
					".static::schema.".f_namalengkap(null,p.namadepan,p.namatengah,p.namabelakang,null) as nama,pk.namapangkat ||'/ '||pk.golongan as namagolongan,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=p.idjfungsional
					where p.idpegawai in ('$r_kode')";
			$rs = $conn->Execute($sql);
			
			//select riwayat bidang
			$sql = "select r.*, m1.namakegiatan as indexakrediasi, m2.namakegiatan as indexakreditasiparent
					from ".static::schema()."ak_bidang4 r
					left join ".static::schema()."ms_penilaian m1 on r.idkegiatan = m1.idkegiatan 
					left join ".static::schema()."ms_penilaian m2 on m1.parentidkegiatan = m2.idkegiatan
					where r.idpegawai in ('$r_kode') and r.periodeakreditasi='$r_periode'
					order by r.idpegawai,indexakreditasiparent";
			$rsd = $conn->Execute($sql);
			
			while($rowd = $rsd->FetchRow()){
				$a_det['indexakreditasiparent'] = $rowd['indexakreditasiparent'];
				$a_det['indexakrediasi'] = $rowd['indexakrediasi'];
				$a_det['kedudukan'] = $rowd['kedudukan'];
				$a_det['lokasi'] = $rowd['lokasi'];
				$a_det['tglmulai'] = $rowd['tglmulai'];				
				$a_det['nilaikredit'] = $rowd['nilaikredit'];				
				$a_det['keterangan'] = $rowd['keterangan'];				
				
				$a_col[$rowd['idpegawai']][$rowd['indexakreditasiparent']]++;				
				$a_detail[$rowd['idpegawai']][] = $a_det;
			}
			
			$a_data = array('list' => $rs, 'detail' => $a_detail, 'colspan' => $a_col);
			
			return $a_data;
		}
		
		//laporan dupak
		function getListDupak($conn,$r_kode,$r_kodeunit){
			$unit = $conn->GetOne("select namaunit from ".static::schema()."ms_unit where kodeunit = '$r_kodeunit'");
			
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,p.idpegawai,
					p.tmplahir,p.tgllahir,p.jeniskelamin,pd.namapendidikan,'('||pk.golongan||') '||pk.namapangkat as namagolongan,
					rf.tmtmulai,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit, ".static::schema()."get_mkgolnow(p.idpegawai),r.*
					from ".static::schema()."ak_skdosen r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					left join ".static::schema()."lv_jenjangpendidikan pd on pd.idpendidikan=p.idpendidikan
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."pe_rwtfungsional rf on rf.nourutjf=(select rrf.nourutjf from ".static::schema()."pe_rwtfungsional rrf
						where rrf.idpegawai = p.idpegawai and rrf.isvalid = 'Y' and rrf.jenisjabatan = 'L' order by rrf.tmtmulai desc limit 1)
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=rf.idjfungsional										
					where r.nourutakd in ('$r_kode')";
					
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $unit);
			
			return $a_data;			
		}
		
		function getRWTKredit($conn,$p_dbtable,$kodeunit,$periode){
			global $conn,$conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			$row = mUnit::getData($conn,$kodeunit);
			
			$unit = " and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
				
			$where = " and periodeakreditasi = '$periode' and statusvalidasi in ('A','Y') ";
			
			$sql = "select cast(r.nilaikredit as varchar) as nilaikredit,r.idpegawai,m.parentidkegiatan 
					from ".self::table($p_dbtable)." r
					left join ".static::schema()."ms_penilaian m on m.idkegiatan=r.idkegiatan
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where isvalid = 'Y' {$where} {$unit}
					order by r.idpegawai";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$kredit[$row['idpegawai']][$row['parentidkegiatan']] += $row['nilaikredit'];
			}
			
			return $kredit;
		}
	}
?>
