<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mAngkaKredit extends mModel {
		const schema = 'sdm';
			
		//master penilaian
		function getDataEditPenilaian($r_key) {
			$sql = "select m.*,case when m.parentidkegiatan is not null then p.kodekegiatan+' - '+p.namakegiatan end as parentkegiatan
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
		
		function aFungsional($conn) {
			$sql = "select idjfungsional, jabatanfungsional from ".self::table('ms_fungsional')." order by idjfungsional";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function aPendidikan($conn) {
			$sql = "select idpendidikan, namapendidikan from ".self::table('lv_jenjangpendidikan')." order by urutan";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function aoperator(){
			return array('<=' => '<=','>=' => '>=');
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
			$sql = "select bidangkegiatan,kodekegiatan+' - '+namakegiatan as parentkegiatan,isaktif from ".self::table('ms_penilaian')." where idkegiatan = '$p_key'";
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
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,j.namapendidikan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang1a')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('lv_jenjangpendidikan')." j on j.idpendidikan = r.jenjang
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidangIA($r_subkey) {
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,j.namapendidikan,coalesce(r.nilaikreditman,r.nilaikredit) as kreditmax,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasi
					from ".self::table('ak_bidang1a')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('lv_jenjangpendidikan')." j on j.idpendidikan = r.jenjang
					where nobidangia='$r_subkey'";
			
			return $sql;
		}
		
		//pop pendidikan
		function getPendidikan($conn,$r_key){			
			$sql = "select r.*, p.namapendidikan,f.namafakultas,j.namajurusan,b.namabidang,
					case when r.kodept is not null then t.namapt else namainstitusi end as namainstitusipend
					from ".self::table('pe_rwtpendidikan')." r 
					left join ".self::table('lv_jenjangpendidikan')." p on p.idpendidikan=r.idpendidikan
					left join ".self::table('ms_pt')." t on t.kodept=r.kodept
					left join ".self::table('ms_fakultas')." f on f.kodefakultas=r.kodefakultas
					left join ".self::table('ms_jurusan')." j on j.kodejurusan=r.kodejurusan
					left join ".self::table('ms_bidang')." b on b.kodebidang=r.kodebidang
					where r.idpegawai='$r_key' and r.isvalid='Y'
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
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,coalesce(r.nilaikreditman,r.nilaikredit) as kreditmax,
					substring(r.thnakademik,1,4)+'/'+substring(r.thnakademik,5,4)+' '+case when semester = '01' then 'Ganjil' else 'Genap' end as namaperiodeakad,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang1b')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('ms_mapperiodeakad')." p on p.kodeperiodeakad = r.thnakademik
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidangIB($r_subkey) {
			$sql = "select r.*,substring(r.thnakademik,1,4) as tahun1,substring(r.thnakademik,5,4) as tahun2,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,coalesce(r.nilaikreditman,r.nilaikredit) as kreditmax,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasi,
					r2.idkegiatan as idkegiatan2, r2.sksdiakui as sksdiakui2
					from ".self::table('ak_bidang1b')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('ak_bidang1b')." r2 on r2.refnobidangib = r.idkegiatan
					where r.nobidangib='$r_subkey'";
			
			return $sql;
		}
		
		function listQueryBidangIBTemp(){
			$sql = "select r.*,p.nik,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nodosen
					from ".self::table('ak_bidang1btemp')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit";
					
			return $sql;
		}
				
		//penarikan data pengajaran dari akademik
		function tarikAkademik($conn,$connsia,$r_periode){
			//hapus dulu
			$conn->Execute("delete from ".self::table('ak_bidang1btemp')." where periode = '$r_periode' and isinput is null");
			
			//komper dengan idpegawai
			$a_peg = array();
			$a_peg = self::getCompDosen($conn);
			
			$sql = "select m.periode,m.kodeunit,m.nipdosen,m.kelasmk,mk.kodemk,mk.namamk,mk.sks,u.namaunit
					from akademik.ak_mengajar m
					left join akademik.ak_kurikulum mk using (thnkurikulum, kodeunit, kodemk)
					left join gate.ms_unit u on u.kodeunit = m.kodeunit 
					where m.periode = '$r_periode' and m.nipdosen is not null and m.nipdosen <>''
					group by m.periode,m.kodeunit,m.nipdosen,m.kelasmk,mk.kodemk,mk.namamk,mk.sks,u.namaunit";					
					
			// $sql = "select a.periode,a.nipdosen,m.kodemk,m.namamk,u.kodeunit,u.namaunit,a.thnkurikulum,r.koderuang,r.lantai,r.lokasi,m.sks,a.tglkuliah,a.kelasmk
					// from akademik.ak_kuliah a 
					// left join akademik.ak_matakuliah m on m.kodemk = a.kodemk and m.thnkurikulum = a.thnkurikulum
					// left join akademik.ms_ruang r on r.koderuang = a.koderuang 
					// left join gate.ms_unit u on u.kodeunit = a.kodeunit 
					// where a.periode = '$r_periode' and statusperkuliahan = 'S' and a.nipdosen is not null and a.nipdosen <>'' 
					// group by a.nipdosen,a.periode,m.kodemk,m.namamk,u.kodeunit,u.namaunit,a.thnkurikulum,r.koderuang,r.lantai,r.lokasi,m.sks,a.tglkuliah,a.kelasmk
					// order by a.tglkuliah desc";
			
			$rsa = $connsia->Execute($sql);					
			
			$it=0;
			$nobidang = self::getNoBidangIBTemp($conn,$r_periode);
			$nobidang = empty($nobidang) ? 0 : $nobidang;
			
			while($rowa = $rsa->FetchRow()){
				if ($a_peg['nodosen'][$rowa['nipdosen']] == $rowa['nipdosen'] and !empty($a_peg['idpegawai'][$rowa['nipdosen']])){
					$colsks = '';
					$sks = '';
					$record = array();
					$record['idpegawai'] = $a_peg['idpegawai'][$rowa['nipdosen']];
					$record['nobidangibtemp'] = ++$nobidang;
					$record['periode'] = $rowa['periode'];
					$record['semester'] = '0'.substr($rowa['periode'],4,1);
					// $record['tglawal'] = $rowa['tglkuliah'];
					// $record['tglakhir'] = $rowa['tglkuliah'];
					$record['thnakademik'] = substr($record['periode'],0,4).((int) substr($record['periode'],0,4) + 1);
					$record['namakegiatan'] = $rowa['kodemk'].' - '.$rowa['namamk'];
					// $record['pada'] = $rowa['koderuang'].' - '.$rowa['lantai'].' '.$rowa['lokasi'];
					$record['tempat'] = $rowa['kodeunit'].' - '.$rowa['namaunit'];
					$record['kodeunitsia'] = $rowa['kodeunit'];
					$record['kelasmk'] = $rowa['kelasmk'];
					$record['sks'] = $rowa['sks'];
					$record['keterangan'] = 'tarik dari akademik';

					if(!empty($record['sks'])){
						$sks = ','.$record['sks'];
						$colsks=',sks';
					}
					$ssql .= "insert into ".self::table('ak_bidang1btemp')."
							(nobidangibtemp,idpegawai,periode,semester,tglawal,tglakhir,thnakademik,namakegiatan,pada,tempat,kodeunitsia,keterangan".$colsks.",kelasmk)
							values
							(".$record['nobidangibtemp'].",".$record['idpegawai'].",'".$record['periode']."','".$record['semester']."',
							'".$record['tglawal']."','".$record['tglakhir']."','".$record['thnakademik']."','".$record['namakegiatan']."',
							'".$record['pada']."','".$record['tempat']."','".$record['kodeunitsia']."','".$record['keterangan']."'".$sks.",'".$record['kelasmk']."');";
					$it++;
					
					if($it>20){
						$conn->Execute($ssql);
						$ssql = '';
						$it=0;
					}
				}
			}
				
			if(!empty($sql))
				$conn->Execute($ssql);
			
			return self::insertStatus($conn);
		}
		
		//mendapatkan idpegawai dari nodosen
		function getCompDosen($conn){
			$sql = "select nodosen,idpegawai from ".self::table('ms_pegawai')." where nodosen is not null";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data['nodosen'][$row['nodosen']] = $row['nodosen'];
				$a_data['idpegawai'][$row['nodosen']] = $row['idpegawai'];
			}
			
			return $a_data;
		}
		
		function getCInput(){
			$a_bayar = array('' => '-- Semua --', 'Y' => 'Sudah Diinputkan', 'T' => 'Belum Diinputkan');
			
			return $a_bayar;	
		}
		
		function saveToBidangIB($conn,$r_key){
			list($id,$periode,$idpegawai) = explode('|',$r_key);
			$sql = "select * from ".self::table('ak_bidang1btemp')." where nobidangibtemp = $id and periode = '$periode'";
			$row = $conn->GetRow($sql);
			
			$post = $idpegawai.'|'.$row['sks'].'|'.$row['thnakademik'];
			$data = self::cekSKSMengajar($conn,$post);
			
			$a_post = array();
			$a_post = explode('|',$data);
			
			if($a_post[0] == '0'){
				$record = array();
				$record['idpegawai'] = $row['idpegawai'];
				$record['semester'] = $row['semester'];
				$record['thnakademik'] = $row['thnakademik'];
				$record['tglawal'] = $row['tglawal'];
				$record['tglakhir'] = $row['tglakhir'];
				$record['namakegiatan'] = $row['namakegiatan'];
				$record['pada'] = $row['pada'];
				$record['tempat'] = $row['tempat'];
				$record['sks'] = $row['sks'];
				$record['keterangan'] = $row['keterangan'];
				$record['sksdiakui'] = $a_post[1];
				$record['idkegiatan'] = $a_post[2];
				$record['stdkredit'] = self::hitungKredit($conn,$record['idkegiatan'],$record['sksdiakui']);
				$record['nilaikredit'] = $record['stdkredit'];
				$record['isvalid'] = 'Y';
				$record['ismengajar'] = 'Y';
				$record['refnobidangibtemp'] = $row['nobidangibtemp'];
				$record['kodeunitsia'] = $row['kodeunitsia'];
				$record['periode'] = $row['periode'];
				
				list($p_posterr,$p_postmsg) = self::insertRecord($conn,$record,true,'ak_bidang1b','','',true,$r_subkey);
				
				if(!$p_posterr){
					$record['sksdiakui2'] = $a_post[3];
					$record['idkegiatan2'] = $a_post[4];
					self::savePecahan($conn,$r_subkey,$record['idkegiatan2'],$record['sksdiakui2']);
				}
				
				//update bidang IB temp
				if(!$p_posterr){
					$rec = array();
					$rec['isinput'] = 'Y';
					$err = self::updateRecord($conn,$rec,$id.'|'.$periode,false,'ak_bidang1btemp','nobidangibtemp,periode');
				}
			}else{
				$p_posterr = 1;
				$p_postmsg = "Ma'af, sks yang anda inputkan sudah melebihi 12 sks";
			}
			
			return array($p_posterr,$p_postmsg);
		}
		
		//pengecekan sks mengajar
		function cekSKSMengajar($conn,$r_key){
			list($r_key,$sks,$thn) = explode('|',$r_key);	
			$err = 0;
		
			$sql = "select top 1 idjfungsional,tmtmulai from sdm.pe_rwtfungsional where idpegawai = $r_key and isvalid = 'Y' order by tmtmulai desc";
			$jab = $conn->GetRow($sql);
			
			$sql = "select coalesce(sum(sksdiakui),0) as jmlsks from sdm.ak_bidang1b 
					where idpegawai = $r_key and tglawal > '".$jab['tmtmulai']."' and (statusvalidasi = '' or statusvalidasi is null)
					and ismengajar = 'Y' and thnakademik = '$thn'";
					
			$jmlsks = $conn->GetOne($sql);
			$totsks = (int)$jmlsks + (int)$sks;
			
			$aa = array('31','32'); //asisten ahli
			$bb = array('33','34','41','42','43','44','45'); //lektor ke atas		
			
			//asisten ahli
			if(in_array($jab['idjfungsional'],$aa)){
				if($jmlsks >= 12){
					$err = 1;
				}else{
					if($totsks <= 10 and $totsks > 0){
						$mk1 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 21");
						$sksdiakui1 = $sks;
					}else{
						if($jmlsks >= 10){
							$mk1 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 22");
							$sksdiakui1 = 12 - $jmlsks; //sisa sks yang diakui
						}else if($jmlsks < 10 and $jmlsks >= 0){
							$mk1 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 21");
							$sksdiakui1 = 10 - $jmlsks; //sisa sks yang diakui
							
							$mk2 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 22");
							if($totsks < 12){
								$sksdiakui2 = 12 - $totsks;
							}else{
								$skss = (int)$sks - (int)$sksdiakui1;
								$sksdiakui2 = $skss - ($totsks-12); //sisa sks yang diakui
							}
						}
					}
				}
			}
			
			//lektor ke atas
			else if(in_array($jab['idjfungsional'],$bb)){
				if($jmlsks >= 12){
					$err = 1;
				}else{
					if($totsks <= 10 and $totsks > 0){
						$mk1 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 23");
						$sksdiakui1 = $sks;
					}else{
						if($jmlsks >= 10){
							$mk1 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 24");
							$sksdiakui1 = 12 - $jmlsks; //sisa sks yang diakui
						}else if($jmlsks < 10 and $jmlsks >= 0){
							$mk1 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 23");
							$sksdiakui1 = 10 - $jmlsks; //sisa sks yang diakui
							
							$mk2 = $conn->GetRow("select idkegiatan from sdm.ms_penilaian where idkegiatan = 24");
							if($totsks < 12){
								$sksdiakui2 = 12 - $totsks;
							}else{
								$skss = (int)$sks - (int)$sksdiakui1;
								$sksdiakui2 = $skss - ($totsks-12); //sisa sks yang diakui
							}
						}
					}
				}			
			}
			
			//pengajar atau tidak punya jab. akademik
			else{
				$mk1 = $conn->GetRow("select idkegiatan,stdkredit from sdm.ms_penilaian where idkegiatan = 21");
				$sksdiakui1 = $sks;
			}		
		
			return $err.'|'.$sksdiakui1.'|'.$mk1['idkegiatan'].'|'.$sksdiakui2.'|'.$mk2['idkegiatan'];	
		}
		
		//mendapatkan nobidang maks
		function getNoBidangIBTemp($conn,$r_periode){				
			$conn->Execute("delete from ".self::table('ak_bidang1btemp')." where periode = '$r_periode' and isinput is null");
			
			$nobidang = $conn->GetOne("select max(nobidangibtemp) from ".self::table('ak_bidang1btemp')." where periode = '$r_periode'");
			
			return $nobidang;
		}
		
		//mengembalikan is input = null
		function backIsInput($conn,$r_subkey){
			$row = $conn->GetRow("select refnobidangibtemp,periode from ".self::table('ak_bidang1b')." where nobidangib = $r_subkey");
			
			$rec = array();
			$rec['isinput'] = 'null';
			$err = self::updateRecord($conn,$rec,$row['refnobidangibtemp'].'|'.$row['periode'],false,'ak_bidang1btemp','nobidangibtemp,periode');
			
			return self::deleteStatus($conn);
		}
		
		function PeriodeSemester() {
			$data = array('01' => 'Ganjil', '02' => 'Genap', '03' => 'Pendek', '00' => 'Pendek Awal');
			
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

				if((!empty($idkegiatan2) and $idkegiatan2 != 'null') and (!empty($sks2) and $sks2 != 'null')){
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
		
		//list bidang penelitian
		function listQueryBidang2($r_key) {
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,p.judulpenelitian,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang2')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					left join ".self::table('pe_penelitian')." p on p.idpenelitian = r.idpenelitian
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidang2($r_subkey) {
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,p.judulpenelitian,coalesce(r.nilaikreditman,r.stdkredit) as stdkredit,
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
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang3')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidang3($r_subkey) {
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,coalesce(r.nilaikreditman,r.stdkredit) as stdkredit,
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
			$sql = "select r.*,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,
					substring(r.thnakademik,1,4)+'/'+substring(r.thnakademik,5,4)+' '+case when semester = '01' then 'Ganjil' else 'Genap' end as namaperiodeakad,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasiak
					from ".self::table('ak_bidang4')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditBidang4($r_subkey) {
			$sql = "select r.*,substring(thnakademik,1,4) as tahun1,substring(thnakademik,5,4) as tahun2,m.kodekegiatan+' - '+m.namakegiatan as kegiatan,coalesce(r.nilaikreditman,r.stdkredit) as stdkredit,
					case when r.statusvalidasi = 'Y' then 'Disetujui' when r.statusvalidasi = 'N' then 'Ditolak' when r.statusvalidasi = 'A' then 'Diajukan' else 'Belum Diajukan' end as statusvalidasi
					from ".self::table('ak_bidang4')." r 
					left join ".self::table('ms_penilaian')." m on m.idkegiatan = r.idkegiatan
					where nobidangiv='$r_subkey'";
			
			return $sql;
		}
				
		//pop sertifikasi
		function getSertifikasi($conn,$r_key){	
			//select bidang4 sertifikasi pegawai
			$rsr = $conn->Execute("select ak.idsertifikasi from ".self::table('ak_bidang4')." ak
					left join ".self::table('pe_sertifikasi')." r on r.idsertifikasi = ak.idsertifikasi
					where ak.idpegawai='$r_key'");
			while($rowr = $rsr->FetchRow()){
				$aid[] = $rowr['idsertifikasi'];
			}
			
			if(count($aid)>0)
				$inid = implode("','",$aid);
			
			//select sertifikasi yang masih belum dipake buat kum bidang4
			$sql = "select r.*,s.jenissertifikasi from ".self::table('pe_sertifikasi')." r 
					left join ".self::table('lv_jenissertifikasi')." s on s.kodesertifikasi = r.kodesertifikasi					
					where r.idpegawai='$r_key' and r.isvalid='Y' and r.idsertifikasi not in ('$inid')
					order by r.tglsertifikasi desc";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idsertifikasi'] = $row['idsertifikasi'];
				$t_data['tglmulai'] = $row['tglsertifikasi'];
				$t_data['namakegiatan'] = $row['jenissertifikasi'];
				$t_data['nolegalitas'] = $row['nosertifikasi'];
				$t_data['keterangan'] = $row['keterangan'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		//cek apakah jabatan ada
 		function cekFungsional($conn,$r_key){
			$sql = "select top 1 tmtmulai from ".static::schema()."pe_rwtfungsional 
					where idpegawai = $r_key and isvalid = 'Y' 
					order by tmtmulai desc";
			$tmt = $conn->GetOne($sql);
			
			if (empty($tmt))
				return 0;
			else
				return 1;
		}	

	    function cekPendidikan($conn,$idPeg){
	        $sql = "select p.idpendidikan,d.urutan from " . self::table('ms_pegawai') . " p
	                left join " . self::table('lv_jenjangpendidikan') . " d on d.idpendidikan = p.idpendidikan
	                where p.idpegawai = $idPeg";
	        $peg = $conn->GetRow($sql);

	        //prosentase
	        $sql = "select top 1 d.urutan from " . self::table('ms_prosentaseak') . " p
	                left join " . self::table('lv_jenjangpendidikan') . " d on d.idpendidikan = p.idpendidikan
	                order by d.urutan";
	        $proc = $conn->GetRow($sql);

	        if($peg['urutan'] < $proc['urutan'])
	            return 0;
	        else
	            return 1;
	    }

	    function lastPendidikan($conn,$idPeg){
	        $sql = "select idpendidikan from " . self::table('ms_pegawai') . " where idpegawai = $idPeg";

	        return $conn->GetOne($sql);
	    }
		
		//list simulasi angka kredit semester
		function listQuerySimulasiAKSMTR($r_key) {
			$sql = "select r.*,substring(r.periodeakreditasi,1,4) as tahun, 
					case
						when substring(r.periodeakreditasi,5,2) = '01' then 'Ganjil'
						when substring(r.periodeakreditasi,5,2) = '02' then 'Genap'
						when substring(r.periodeakreditasi,5,2) = '03' then 'Pendek'
						when substring(r.periodeakreditasi,5,2) = '00' then 'Pendek Awal'
					end
					as semester 
					from ".self::table('ak_skdosensmtr')." r where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditSimulasiAKSMTR($r_subkey) {
			list($periodeakreditasi,$idpegawai) = explode('|', $r_subkey);

			$sql = "select r.*,substring(r.periodeakreditasi,1,4) as tahun,substring(r.periodeakreditasi,5,2) as semester	
					from ".self::table('ak_skdosensmtr')." r 
					where r.periodeakreditasi='$periodeakreditasi' and r.idpegawai = $idpegawai";
			
			return $sql;
		}

		function getNilaiKredit($conn,$r_key){
			$sql = "select periodeakreditasi,sum(nilaikredit) as nilai from ".self::table('ak_bidang1a')." 
					where idpegawai = $r_key and statusvalidasi in ('A','Y') group by periodeakreditasi";
			$rs = $conn->Execute($sql);

			while($row = $rs->FetchRow())
				$a_nilai['IA'][$row['periodeakreditasi']] = $row['nilai'];

			$sql = "select periodeakreditasi,sum(nilaikredit) as nilai from ".self::table('ak_bidang1b')." 
					where idpegawai = $r_key and statusvalidasi in ('A','Y') group by periodeakreditasi";
			$rs = $conn->Execute($sql);

			while($row = $rs->FetchRow())
				$a_nilai['IB'][$row['periodeakreditasi']] = $row['nilai'];


			$sql = "select periodeakreditasi,sum(nilaikredit) as nilai from ".self::table('ak_bidang2')." 
					where idpegawai = $r_key and statusvalidasi in ('A','Y') group by periodeakreditasi";
			$rs = $conn->Execute($sql);

			while($row = $rs->FetchRow())
				$a_nilai['II'][$row['periodeakreditasi']] = $row['nilai'];


			$sql = "select periodeakreditasi,sum(nilaikredit) as nilai from ".self::table('ak_bidang3')." 
					where idpegawai = $r_key and statusvalidasi in ('A','Y') group by periodeakreditasi";
			$rs = $conn->Execute($sql);

			while($row = $rs->FetchRow())
				$a_nilai['III'][$row['periodeakreditasi']] = $row['nilai'];


			$sql = "select periodeakreditasi,sum(nilaikredit) as nilai from ".self::table('ak_bidang3')." 
					where idpegawai = $r_key and statusvalidasi in ('A','Y') group by periodeakreditasi";
			$rs = $conn->Execute($sql);

			while($row = $rs->FetchRow())
				$a_nilai['IV'][$row['periodeakreditasi']] = $row['nilai'];

			return $a_nilai;
		}
		
		//list simulasi angka kredit
		function listQuerySimulasiAK($r_key) {
			$sql = "select r.*,j1.jabatanfungsional as jabatanasal,j2.jabatanfungsional as jabatantujuan,
					case when r.statususulan = 'Y' then 'Sudah Divalidasi' when r.statususulan = 'A' then 'Diajukan' else 'Belum Diajukan' end as statususul,u.kodeunit as unit,substring(cast(cast(r.tglusulan as date) as varchar),1,4) as tahun
					from ".self::table('ak_skdosen')." r 
					left join ".self::table('ms_fungsional')." j1 on j1.idjfungsional = r.fungsionalasal
					left join ".self::table('ms_fungsional')." j2 on j2.idjfungsional = r.fungsionaltujuan
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('ms_unit')." u on u.idunit = p.idunit
					where r.idpegawai='$r_key'";
			
			return $sql;
		}
		
		function getDataEditSimulasiAK($r_subkey) {
			$sql = "select r.*,u.kodeunit as unit,
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
			$jab = $conn->GetRow("select top 1 r.*,
					cast(r.nilaibidang1a as varchar) as nilaibidang1a,cast(r.nilaibidang1b as varchar) as nilaibidang1b,
					cast(r.nilaibidang2 as varchar) as nilaibidang2,cast(r.nilaibidang3 as varchar) as nilaibidang3,
					cast(r.nilaibidang4 as varchar) as nilaibidang4,
					cast(r.sisabidang1a as varchar) as sisabidang1a,cast(r.sisabidang1b as varchar) as sisabidang1b,
					cast(r.sisabidang2 as varchar) as sisabidang2,cast(r.sisabidang3 as varchar) as sisabidang3,
					cast(r.sisabidang4 as varchar) as sisabidang4,f.jabatanfungsional,cast(f.angkakredit as varchar) as angkakredit
					from ".self::table('pe_rwtfungsional')." r
					left join ".self::table('ms_fungsional')." f on f.idjfungsional = r.idjfungsional
					where r.idpegawai = '$r_key' and r.isvalid = 'Y'
					order by r.tmtmulai desc");
					
			return $jab;
		}

		function getKreditSmtr($conn,$r_subkey){
			$sql = "select periodeakreditasi,periodeakreditasi as periode 
					from ".self::table('ak_skdosensmtr')." where nourutakd = $r_subkey group by periodeakreditasi";

			return Query::arrQuery($conn,$sql);
		}

		function getSemester($conn,$r_subkey,$r_key){
			$sql = "select periodeakreditasi,periodeakreditasi as periode 
					from ".self::table('ak_skdosensmtr')." where idpegawai= $r_key and nourutakd is null";
			if(!empty($r_subkey))
				$sql .= " or nourutakd = $r_subkey";

			$sql .= " group by periodeakreditasi";

			return Query::arrQuery($conn,$sql);
		}
				
		function jabatanFungsional($conn,$id) {
			$sid = !empty($id) ? 'where idjfungsional > '.$id.'' : '';
			$sql = "select idjfungsional, jabatanfungsional from ".static::schema()."ms_fungsional {$sid} order by angkakredit";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function getPeriode($conn,$r_key) {
			$periode = $conn->GetOne("select top 1 periodeakreditasi from ".self::table('ak_skdosensmtr')." where nourutakd = $r_key");
			
			return $periode;
		}
		
		function getBidangIA($conn,$r_key,$periode){
			$where = !empty($periode) ? " and periodeakreditasi = '$periode'" : " and (statusvalidasi = '' or statusvalidasi is null) ";
			
			$sql = "select k.*,k.stdkredit,coalesce(k.nilaikreditman,k.nilaikredit) as nilaikredit,p.namapendidikan as namakegiatan,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,s1.stdkredit as kreditmax,s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
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
			
			$sql = "select k.*,k.stdkredit,coalesce(k.nilaikreditman,k.nilaikredit) as nilaikredit,
					substring(k.thnakademik,1,4)+'/'+substring(k.thnakademik,5,4)+' '+case when k.semester = '01' then 'Ganjil' else 'Genap' end as periodekuliah,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,s1.stdkredit as kreditmax,
					s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
					from ".self::table('ak_bidang1b')." k 
					left join ".self::table('ms_mapperiodeakad')." p on p.kodeperiodeakad = k.thnakademik 
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
			
			$sql = "select k.*,k.stdkredit,coalesce(k.nilaikreditman,k.nilaikredit) as nilaikredit,p.judulpenelitian as namakegiatan,substring(cast(tgl as varchar),1,4) as periode,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,s1.stdkredit as kreditmax,s2.kodekegiatan as kodeparent,s2.namakegiatan as namaparent
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
			
			$sql = "select k.*,k.stdkredit,coalesce(k.nilaikreditman,k.nilaikredit) as nilaikredit,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,s1.stdkredit as kreditmax,
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
			
			$sql = "select k.*,k.stdkredit,coalesce(k.nilaikreditman,k.nilaikredit) as nilaikredit,
					s1.kodekegiatan as indeks,s1.namakegiatan as namaindeks,s1.stdkredit as kreditmax,
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
		
		function getNilaiIA($conn,$r_key,$r_subkey){
			$a_periode = self::getKreditSmtr($conn,$r_subkey);
			$i_periode = implode("','", $a_periode);

			$where = " and periodeakreditasi in ('$i_periode') and statusvalidasi in ('A','Y') ";
			
			$sql = "select coalesce(nilaikredit,0) as nilaikredit from ".self::table('ak_bidang1a')." where idpegawai = '$r_key' and isvalid = 'Y' {$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang1a += $row['nilaikredit'];
			}
			
			return $nbidang1a;
		}
		
		function getNilaiIB($conn,$r_key,$r_subkey){
			$a_periode = self::getKreditSmtr($conn,$r_subkey);
			$i_periode = implode("','", $a_periode);

			$where = " and periodeakreditasi in ('$i_periode') and statusvalidasi in ('A','Y') ";
			
			$sql = "select coalesce(nilaikredit,0) as nilaikredit from ".self::table('ak_bidang1b')." where idpegawai = '$r_key' and isvalid = 'Y' {$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang1b += $row['nilaikredit'];
			}
			
			return $nbidang1b;
		}
		
		function getNilaiII($conn,$r_key,$r_subkey){
			$a_periode = self::getKreditSmtr($conn,$r_subkey);
			$i_periode = implode("','", $a_periode);

			$where = " and periodeakreditasi in ('$i_periode') and statusvalidasi in ('A','Y') ";
			
			$sql = "select coalesce(nilaikredit,0) as nilaikredit from ".self::table('ak_bidang2')." where idpegawai = '$r_key' and isvalid = 'Y' {$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang2 += $row['nilaikredit'];
			}
			
			return $nbidang2;
		}
		
		function getNilaiIII($conn,$r_key,$r_subkey){
			$a_periode = self::getKreditSmtr($conn,$r_subkey);
			$i_periode = implode("','", $a_periode);

			$where = " and periodeakreditasi in ('$i_periode') and statusvalidasi in ('A','Y') ";
			
			$sql = "select coalesce(nilaikredit,0) as nilaikredit from ".self::table('ak_bidang3')." where idpegawai = '$r_key' and isvalid = 'Y' {$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang3 += $row['nilaikredit'];
			}
			
			return $nbidang3;
		}
		
		function getNilaiIV($conn,$r_key,$r_subkey){
			$a_periode = self::getKreditSmtr($conn,$r_subkey);
			$i_periode = implode("','", $a_periode);

			$where = " and periodeakreditasi in ('$i_periode') and statusvalidasi in ('A','Y') ";
			
			$sql = "select coalesce(nilaikredit,0) as nilaikredit from ".self::table('ak_bidang4')." where idpegawai = '$r_key' and isvalid = 'Y' {$where}";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$nbidang4 += $row['nilaikredit'];
			}
			
			return $nbidang4;
		}
		
		function updateRWTAkreditasi($conn,$r_subkey){
			list($periodeakreditasi,$idpegawai) = explode('|', $r_subkey);
			
			//update kegiatan bidang yang tidak jadi diajukan
			$ok = $conn->Execute("update ".self::table('ak_bidang1a')." set periodeakreditasi=null,nilaikreditman=null,statusvalidasi=null where periodeakreditasi='$periodeakreditasi' and idpegawai='$idpegawai'");
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang1b')." set periodeakreditasi=null,nilaikreditman=null,statusvalidasi=null where periodeakreditasi='$periodeakreditasi' and idpegawai='$idpegawai'");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang2')." set periodeakreditasi=null,nilaikreditman=null,statusvalidasi=null where periodeakreditasi='$periodeakreditasi' and idpegawai='$idpegawai'");
			else
				break;
			
			if($ok)	
				$ok = $conn->Execute("update ".self::table('ak_bidang3')." set periodeakreditasi=null,nilaikreditman=null,statusvalidasi=null where periodeakreditasi='$periodeakreditasi' and idpegawai='$idpegawai'");
			else
				break;
			
			if($ok)	
				$ok = $conn->Execute("update ".self::table('ak_bidang4')." set periodeakreditasi=null,nilaikreditman=null,statusvalidasi=null where periodeakreditasi='$periodeakreditasi' and idpegawai='$idpegawai'");
			else
				break;
			
			return $conn->ErrorNo();
		}
		
		function updateRWTAkreditasiFinal($conn,$r_subkey,$r_key){			
			$sql = "select periodeakreditasi,periodeakreditasi as periode from ".self::table('ak_skdosensmtr')." where nourutakd = $r_subkey group by periodeakreditasi";
			$a_periode = Query::arrQuery($conn,$sql);
			$i_periode = implode("','", $a_periode);
			
			//update kegiatan bidang yang tidak jadi diajukan
			$ok = $conn->Execute("update ".self::table('ak_bidang1a')." set statusvalidasi='A',tglvalidasi=null,isfinal=null where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang1b')." set statusvalidasi='A',tglvalidasi=null,isfinal=null where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang2')." set statusvalidasi='A',tglvalidasi=null,isfinal=null where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
			
			if($ok)	
				$ok = $conn->Execute("update ".self::table('ak_bidang3')." set statusvalidasi='A',tglvalidasi=null,isfinal=null where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
			
			if($ok)	
				$ok = $conn->Execute("update ".self::table('ak_bidang4')." set statusvalidasi='A',tglvalidasi=null,isfinal=null where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
			
			return $conn->ErrorNo();
		}

		function updateHitungSmtr($conn,$r_key,$r_subkey,$smtr){
			$i_smstr = implode("','", $smtr);

			$sql = "update ".self::table('ak_skdosensmtr')." set nourutakd = $r_subkey 
					where periodeakreditasi in ('$i_smstr') and idpegawai='$r_key'";
			$conn->Execute($sql);

			return $conn->ErrorNo();
		}

		function updateValidasiSmtr($conn,$r_subkey){
			$sql = "update ".self::table('ak_skdosensmtr')." set tglvalidasi = '".date('Y-m-d')."', isvalid = 'Y' where nourutakd = $r_subkey";
			$conn->Execute($sql);

			return $conn->ErrorNo();
		}
		
		function updateSimulasiSmtr($conn,$r_subkey){
			$sql = "update ".self::table('ak_skdosensmtr')." set nourutakd=null where nourutakd=$r_subkey";
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		function validasiRWT($conn,$r_subkey,$r_key){		
			$sql = "select periodeakreditasi,periodeakreditasi as periode from ".self::table('ak_skdosensmtr')." where nourutakd = $r_subkey group by periodeakreditasi";
			$a_periode = Query::arrQuery($conn,$sql);
			$i_periode = implode("','", $a_periode);
			
			//mengisikan tanggal validasi dan isfinal
			$ok = $conn->Execute("update ".self::table('ak_bidang1a')." set statusvalidasi = 'Y', tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang1b')." set statusvalidasi = 'Y', tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang2')." set statusvalidasi = 'Y', tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang3')." set statusvalidasi = 'Y', tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang4')." set statusvalidasi = 'Y', tglvalidasi='".date('Y-m-d')."',isfinal='Y' where periodeakreditasi in ('$i_periode') and idpegawai='$r_key'");
			else
				break;
				
			//update null kegiatan bidang yang tidak jadi diajukan
			$ok = $conn->Execute("update ".self::table('ak_bidang1a')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi in ('$i_periode') and idpegawai='$r_key' and statusvalidasi is null");			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang1b')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi in ('$i_periode') and idpegawai='$r_key' and statusvalidasi is null");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang2')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi in ('$i_periode') and idpegawai='$r_key' and statusvalidasi is null");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang3')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi in ('$i_periode') and idpegawai='$r_key' and statusvalidasi is null");
			else
				break;
			
			if($ok)
				$ok = $conn->Execute("update ".self::table('ak_bidang4')." set periodeakreditasi=null,nilaikredit=stdkredit where periodeakreditasi in ('$i_periode') and idpegawai='$r_key' and statusvalidasi is null");
			else
				break;
						
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
	            $t_data['I'][$row['idjfungsional']][$row['idpendidikan']]['operator'] = $row['oper1'];
	            $t_data['I'][$row['idjfungsional']][$row['idpendidikan']]['prosentase'] = $row['proc1'];
	            $t_data['II'][$row['idjfungsional']][$row['idpendidikan']]['operator'] = $row['oper2'];
	            $t_data['II'][$row['idjfungsional']][$row['idpendidikan']]['prosentase'] = $row['proc2'];
	            $t_data['III'][$row['idjfungsional']][$row['idpendidikan']]['operator'] = $row['oper3'];
	            $t_data['III'][$row['idjfungsional']][$row['idpendidikan']]['prosentase'] = $row['proc3'];
	            $t_data['IV'][$row['idjfungsional']][$row['idpendidikan']]['operator'] = $row['oper4'];
	            $t_data['IV'][$row['idjfungsional']][$row['idpendidikan']]['prosentase'] = $row['proc4'];
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
			if($col == 'tahun')
				return "substring(cast(cast(r.tglusulan as date) as varchar),1,4) = '$key'";
			if($col == 'input'){
				if(!empty($key)){
					if($key == 'Y')
						return "r.isinput = 'Y'";
					else
						return "(r.isinput = 'T' or r.isinput is null)";
				}
			}
		}
		
		//*********************************************L A P O R A N****************************************************
		//list dupak
		function listQueryRepDupak(){
			$sql = "select r.nourutakd,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,
					u.kodeunit+' - '+u.namaunit as unit,t.tipepeg+' - '+j.jenispegawai as jenispegawai
					from ".self::table('ak_skdosen')." r 
					left join ".self::table('ms_pegawai')." p on p.idpegawai = r.idpegawai
					left join ".self::table('ms_jenispeg')." j on j.idjenispegawai = p.idjenispegawai
					left join ".self::table('ms_tipepeg')." t on t.idtipepeg = p.idtipepeg
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
		
		//laporan dupak
		function getListDupak($conn,$r_kode,$r_kodeunit){
			if(!empty($r_kodeunit))
				$unit = $conn->GetOne("select namaunit from ".static::schema()."ms_unit where kodeunit = '$r_kodeunit'");
			
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,p.nik,
					p.tmplahir,p.tgllahir,p.jeniskelamin,pd.namapendidikan,p.tmtpangkat,'('+pk.golongan+') '+pk.namapangkat as namagolongan,
					rf.tmtmulai,f.jabatanfungsional,u.namaunit,up.namaunit as parentunit,p.masakerjathngol,p.masakerjablngol,r.*
					from ".static::schema()."ak_skdosen r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_unit up on up.idunit=u.parentunit
					left join ".static::schema()."lv_jenjangpendidikan pd on pd.idpendidikan=p.idpendidikan
					left join ".static::schema()."ms_pangkat pk on pk.idpangkat=p.idpangkat
					left join ".static::schema()."pe_rwtfungsional rf on rf.nourutjf=(select top 1 rrf.nourutjf from ".static::schema()."pe_rwtfungsional rrf
						where rrf.idpegawai = p.idpegawai and rrf.isvalid = 'Y' and rrf.jenisjabatan = 'L' order by rrf.tmtmulai desc)
					left join ".static::schema()."ms_fungsional f on f.idjfungsional=rf.idjfungsional										
					where r.nourutakd in ('$r_kode')";
			$rs = $conn->Execute($sql);
			
			$a_data = array('list' => $rs, 'namaunit' => $unit);
			
			return $a_data;			
		}

		function getNoAkd($conn,$r_tahun,$r_sem,$kodeunit){
			global $conn,$conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			$row = mUnit::getData($conn,$kodeunit);
			
			$unit = " and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			$sem = !empty($r_sem) ? " and substring(r.periodeakreditasi,5,2) = '$r_sem'" : "";

			$sql = "select r.nourutakd,r.nourutakd as nourutakdsmtr 
					from ".static::schema()."ak_skdosensmtr r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where substring(r.periodeakreditasi,1,4) = '$r_tahun' {$sem} {$unit} and nourutakd is not null
					group by r.nourutakd";

			$a_nourutakd = Query::arrQuery($conn,$sql);

			return implode("','", $a_nourutakd);
		}
		
		function getRWTKredit($conn,$p_dbtable,$kodeunit,$r_subkey){			
			$sql = "select periodeakreditasi,periodeakreditasi as periode from ".self::table('ak_skdosensmtr')." 
					where nourutakd in ('$r_subkey') group by periodeakreditasi";
			$a_periode = Query::arrQuery($conn,$sql);
			$i_periode = implode("','", $a_periode);

			global $conn,$conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			if(!empty($kodeunit)){
				$row = mUnit::getData($conn,$kodeunit);
				$unit = " and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];				
			}
			
			$periodeakreditasi = !empty($i_periode) ? " and r.periodeakreditasi in ('$i_periode')" : "";				
			$status = " and statusvalidasi = 'Y' and isvalid = 'Y'";
			
			$sql = "select cast(r.nilaikredit as varchar) as nilaikredit,r.idpegawai,m.parentidkegiatan 
					from ".self::table($p_dbtable)." r
					left join ".static::schema()."ms_penilaian m on m.idkegiatan=r.idkegiatan
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.idpegawai
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where 1=1 {$periodeakreditasi} {$status} {$unit}
					order by r.idpegawai";		
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$kredit[$row['idpegawai']][$row['parentidkegiatan']] += $row['nilaikredit'];
			}
			
			return $kredit;
		}
		
		function repRekapitulasiKUM($conn,$r_kodeunit){
			global $conn,$conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			$row = mUnit::getData($conn,$r_kodeunit);
			$namaunit = $conn->GetOne("select namaunit from ".static::schema()."ms_unit where kodeunit = '$r_kodeunit'");
			
			$unit = " and u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			
			//unit homebase awal pegawai
			$sql = "select p.idpegawai,".static::schema()."f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,u.namaunit,
					p.idunitbase,p.nodosen,p.nidn,f.kodefungsional,f.angkakredit
					from ".static::schema()."ms_pegawai p
					left join ".static::schema()."ms_unit u on u.idunit = p.idunitbase
					left join ".static::schema()."ms_fungsional f on f.idjfungsional = p.idjfungsional
					left join ".static::schema()."lv_statusaktif k on k.idstatusaktif = p.idstatusaktif
					where p.idjenispegawai in ('PLB','PT','PTD') and k.iskeluar = 'T' {$unit}
					order by u.infoleft";
			$rs = $conn->Execute($sql);
			
			$i=0;
			while($row = $rs->FetchRow()){
				$a_pegawai[$row['idunitbase']][$row['idpegawai']] = $row;
				$a_idunit[$i] = $row['idunitbase'];
				$i++;
			}
			
			$sqlu = "select idunit,namaunit from ".static::schema()."ms_unit order by infoleft";
			$a_unit = Query::arrQuery($conn,$sqlu);
			
			return array('a_pegawai' => $a_pegawai, 'namaunit' => $namaunit, 'a_idunit' => $a_idunit, 'a_unit' => $a_unit);
		}
	}
?>
