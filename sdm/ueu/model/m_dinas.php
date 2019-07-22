<?php
	// model dinas
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mDinas extends mModel {
		const schema = 'sdm';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select thnanggaran,namaunit,anggaran,anggaranterpakai,a.idunit from ".static::table('ms_anggarandinas')." a
					left join ".static::schema()."ms_unit u on u.idunit=a.idunit ";
			
			return $sql;
		}
		
		function listQueryPeserta($conn,$key) {
			$sql = "select v.namalengkap,a.nosurat,a.nodinas,v.idpegawai,a.pejabatatasan,a.kasdm,issetuju,tglsetuju,
					kabagkeu,a.tgldicairkan,a.jmldicairkan,warek2
					from ".static::table('pe_rwtdinas')." a
					left join ".static::schema()."v_biodatapegawai v on v.idpegawai=a.pegditunjuk
					where a.refid=$key";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function listQueryRwtDinas($key) {
			$sql = "select r.*, namajenisdinas, 
					case when issetujuatasan='Y' then 'Disetujui' when issetujuatasan='T' then 'Ditolak' else 'Diajukan' end as statuspengajuan
					from ".static::table('pe_rwtdinas')." r
					left join ".static::table('ms_jenisdinas')." m on m.kodejenisdinas=r.kodejenisdinas
					where pegditunjuk=$key";
			
			return $sql;
		}
		
		// mendapatkan kueri list usulan tugas dinas
		function listQueryUsulanDinas() {
			$sql = "select d.* from ".static::table('v_tugasdinas')." d 
					left join ".static::schema()."ms_unit u on u.idunit=d.idunit";
					
			return $sql;
		}
		
		function listQueryPersetujuanDinas(){
			$sql = "select d.*, j.namajenisdinas, sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,nik
					from ".static::table('pe_rwtdinas')." d
					left join ".static::schema()."ms_pegawai p on p.idpegawai=d.pegditunjuk
					left join ".static::schema()."ms_jenisdinas j on j.kodejenisdinas=d.kodejenisdinas
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					where issetujuatasan is not null";
					
			if(Modul::getRole() == 'PS'){ //bila selain admin, mungkin atasan
				$sql .= " where (d.emailatasan = '".Modul::getUserEmail()."' or d.emailkasdm = '".Modul::getUserEmail()."' 
						or d.emailwarek2 = '".Modul::getUserEmail()."' or d.emailkabagkeu = '".Modul::getUserEmail()."')";
			}
			
			return $sql;
		}
		
		// mendapatkan potongan kueri filter list
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
						return "thnanggaran = '$key'";
					else
						return "(1=1)";
					
					break;
			}
		}
				
		// mendapatkan kueri data edit untuk riwayat
		function getDataEditRDinas($key) {
			$sql = "select d.*, 
					p.nik || ' - ' || sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					pj.nik || ' - ' || sdm.f_namalengkap(pj.gelardepan,pj.namadepan,pj.namatengah,pj.namabelakang,pj.gelarbelakang) as namapejabat,
					psdm.nik || ' - ' || sdm.f_namalengkap(psdm.gelardepan,psdm.namadepan,psdm.namatengah,psdm.namabelakang,psdm.gelarbelakang) as namasdm,
					pkeu.nik || ' - ' || sdm.f_namalengkap(pkeu.gelardepan,pkeu.namadepan,pkeu.namatengah,pkeu.namabelakang,pkeu.gelarbelakang) as namakeu,
					pwr.nik || ' - ' || sdm.f_namalengkap(pwr.gelardepan,pwr.namadepan,pwr.namatengah,pwr.namabelakang,pwr.gelarbelakang) as namawr,				
					pt.nik || ' - ' || sdm.f_namalengkap(pt.gelardepan,pt.namadepan,pt.namatengah,pt.namabelakang,pt.gelarbelakang) as pejabatpenugas,				
					u.namaunit
					from ".static::table('pe_rwtdinas')." d 
					left join ".static::table('ms_pegawai')." pj on pj.idpegawai=d.pejabatatasan
					left join ".static::table('ms_pegawai')." psdm on psdm.idpegawai=d.kasdm
					left join ".static::table('ms_pegawai')." pkeu on pkeu.idpegawai=d.kabagkeu
					left join ".static::table('ms_pegawai')." pwr on pwr.idpegawai=d.warek2
					left join ".static::table('ms_pegawai')." p on p.idpegawai=d.pegditunjuk
					left join ".static::table('ms_pegawai')." pt on pt.idpegawai=d.idpegawaitugas
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where nodinas=$key";
			
			return $sql;
		}
		function getEditDinasKol($key) {
			$sql = "select d.*, 
					pt.nik + ' - ' + sdm.f_namalengkap(pt.gelardepan,pt.namadepan,pt.namatengah,pt.namabelakang,pt.gelarbelakang) as pejabatpenugas
					from ".static::table('pe_rwtdinas')." d 
					left join ".static::table('ms_pegawai')." pt on pt.idpegawai=d.idpegawaitugas
					where refid=$key";
			
			return $sql;
		}
		
		function getTahun($conn){
			$sql = "select thnanggaran 
					from ".static::table('ms_anggarandinas')."
					group by thnanggaran order by thnanggaran desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Tahun --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['thnanggaran']] = $row['thnanggaran'];
			}
			
			
			return $a_data;
		}
		
		function getKodeKolektif($conn){
			$sql = "select coalesce(max(refid), 0)+1 from ".static::schema()."pe_rwtdinas";
			
			$max =  $conn->GetOne($sql);
			
			return $max;
		}
		
		function getNoSurat($conn,$date){
		
			$a_romawi = SDM::aRomawiSurat();
			
			$sql = "select max(cast(coalesce(substring(nosurat,1,3),'0') as int))+1 as maks,
					date_part('year',tglusulan) as tahun, date_part('month',tglusulan) as bulan
					from ".static::schema()."pe_rwtdinas where date_part('year',tglusulan) = date_part('year','$date'::timestamp)
					group by tglusulan order by maks desc limit 1";
			
			$col =  $conn->GetRow($sql);
			if (empty($col['maks']))
				$kode ='001/'.$a_romawi[$col['bulan']].'/'.$col['tahun'];
			else
				$kode = str_pad($col['maks'],'3','0', STR_PAD_LEFT).'/'.$a_romawi[$col['bulan']].'/'.$col['tahun'];
			
			return $kode;
		}
		
		function isExistDinas($conn, $key){
			$sql = "select 1 from ".static::table('pe_rwtdinas')." where refid=$key";
			
			return $conn->GetOne($sql);
		}
		
		function getValidasiDinas($conn, $key){
			$sql = "select issetujuatasan,issetujuwarek2,issetujukasdm,issetujukabagkeu,emailatasan,emailwarek2,emailkasdm,emailkabagkeu from ".static::table('pe_rwtdinas')." where nodinas=$key";
			
			return $conn->GetRow($sql);
		}
		
		function isValidDinas($conn, $r_key){
			$sql = "select 1 from ".static::table('pe_rwtdinas')." where issetujuatasan='S' and issetujukasdm='S' and issetujukabagkeu='S' and issetujuwarek2='S' and nodinas=$r_key";
			
			return $conn->GetOne($sql);
		}
		
		function getInformasi($conn, $key) {
			$sql = "select d.* from ".static::table('v_tugasdinas')." d 
					left join ".static::schema()."ms_unit u on u.idunit=d.idunit
					where refid=$key";
			
			return $conn->GetRow($sql);
		}
		
		function getRate($conn,$r_jenis){
			$sql = "select r.idrate,rateperjalanan,tarifrate from ".static::table('lv_rateperjalanan')." r
					left join ".static::schema()."ms_tarifperjalanan t on t.idrate=r.idrate and t.jnsrate='$r_jenis'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getBiayaDinasKol($conn, $r_refid){
			$sql = "select b.idrate,b.nominal,r.nodinas from ".static::table('pe_biayadinas')." b
					left join ".static::schema()."pe_rwtdinas r on r.nodinas=b.nodinas
					where r.refid=$r_refid";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['nodinas']][$row['idrate']] = $row['nominal'];
			
			return $a_data;
		}
		
		function getRtBiayaDinas($conn, $r_refid){
			$sql = "select idrate,nominal,nodinas from ".static::table('pe_biayadinas')."
					where nodinas=$r_refid";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[$row['idrate']] = $row['nominal'];
			
			return $a_data;
		}
		
		function getBiayaDinas($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('pe_biayadinas')." where nodinas = '$key' order by idrate";
			
			return static::getDetail($conn,$sql,$label,$post);
		}
		
		function jenisDinas($conn){
			$sql = "select kodejenisdinas, namajenisdinas from ".static::table('ms_jenisdinas')." order by namajenisdinas";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function jenisRatePerjalanan($conn){
			$sql = "select idrate, rateperjalanan from ".static::table('lv_rateperjalanan')." where isaktif='Y' and isManual='Y' order by rateperjalanan";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function aManual(){
			return array("Y" => "Manual", "T" => "Bukan Manual");
		}
		
		function jenisRate(){
			return array("DK" => "Dalam Kota", "LK" => "Luar Kota", "LN" => "Luar Negeri");
		}
		
		function getDetailInfo($detail,$kolom='') {
			$info = array();
			
			switch($detail) {
				case 'rateperjalanan':
					$info['table'] = 'pe_biayadinas';
					$info['key'] = 'nodinas,idrate';
					$info['label'] = 'Tarif Rate Perjalanan';
					break;
			}
			
			if(empty($kolom))
				return $info;
			else
				return $info[$kolom];
		}
		
		//mendapatkan detail pemakaian data per unit
		function detailDinasUnit($conn,$r_key){
		list($tahun,$idunit) = explode("|",$r_key);
		
		$sql = "select p.nik,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai, sum(nominal) as tarif, b.nodinas
				from ".static::schema()."pe_rwtdinas d
				left join ".static::schema()."ms_pegawai p on p.idpegawai=d.pegditunjuk
				left join ".static::schema()."pe_biayadinas b on b.nodinas=d.nodinas
				where issetujuatasan='S' and issetujukasdm='S' and issetujuwarek2='S' and issetujukabagkeu='S' and d.idunit=$idunit
				group by p.nik,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang), b.nodinas";
		$rs = $conn->Execute($sql);
		
		while($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		//mendapatkan info riwayat Dinas
		function infoDinas($conn,$r_key){
			$idjstruktural = $conn->GetRow("select d.*,p.idjstruktural from ".static::schema()."pe_rwtdinas d
											left join ".static::schema()."ms_pegawai p on p.idpegawai=d.pegditunjuk
											where nodinas=$r_key");
											
			return $idjstruktural;
		}
		
		//jenis rate perjalanan
		function cekRatePerjalanan($conn,$jnsrate,$idjstruktural,$r_key) {
			$sql = "select rp.idrate,rp.rateperjalanan,tarifrate,ismanual,nominal from ".static::schema()."lv_rateperjalanan rp
						left join ".static::schema()."ms_tarifperjalanan tp on tp.idrate=rp.idrate and tp.idjstruktural='$idjstruktural' 
						left join ".static::schema()."pe_biayadinas bd on bd.idrate=rp.idrate and bd.nodinas=$r_key
						where isaktif='Y' order by idrate";
			
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idrate'] = $row['idrate'];
				$t_data['rateperjalanan'] = $row['rateperjalanan'];
				$t_data['tarifrate'] = $row['tarifrate'];
				$t_data['ismanual'] = $row['ismanual'];
				$t_data['nominal'] = $row['nominal'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function cekStatusRate($conn,$r_key){
			$sql = "select * from ".static::schema()."pe_biayadinas where nodinas=$r_key";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow())
				$a_data[] = $row['idrate'];
				
			return $a_data;
		}
		
		//hitung anggaran unit
		function hitDinasUnit($conn,$idunit,$r_key){
			$sql = "select sum(nominal) as dinasunit from ".static::schema()."pe_biayadinas b 
					left join sdm.pe_rwtdinas r on r.nodinas=b.nodinas
					where r.idunit=$idunit and issetujuatasan='S' and issetujukasdm='S' and issetujuwarek2='S' and issetujukabagkeu='S'";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow())
				$a_data['anggaranterpakai'] = $row['dinasunit'];
				
			return $a_data;
		}
		
		/************************************************** L A P O R A N ************************************************/
		function repSuratDinas($conn, $r_key){
			$sql = "select r.*, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					sdm.f_namalengkap(a.gelardepan,a.namadepan,a.namatengah,a.namabelakang,a.gelarbelakang) as namapejabat, namaunit,
					sdm.f_namalengkap(w.gelardepan,w.namadepan,w.namatengah,w.namabelakang,w.gelarbelakang) as namawarek2,
					sdm.f_namalengkap(k.gelardepan,k.namadepan,k.namatengah,k.namabelakang,k.gelarbelakang) as namakabagkeu,
					sdm.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) as namakasdm,
					p.alamat as alamatpegawai, s.jabatanstruktural, date_part('day',age(tglpergi,tglpulang)) as lamahari
					from ".static::table('pe_rwtdinas')." r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.pegditunjuk
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_pegawai a on a.idpegawai=r.pejabatatasan
					left join ".static::schema()."ms_pegawai w on w.idpegawai=r.warek2
					left join ".static::schema()."ms_pegawai k on k.idpegawai=r.kabagkeu
					left join ".static::schema()."ms_pegawai d on d.idpegawai=r.kasdm
					left join ".static::schema()."ms_struktural s on s.idjstruktural=r.idjstruktural
					where nodinas=$r_key";
							
			return $conn->GetRow($sql);
		}
		
		function repBuktiKedinasan($conn,$r_kodeunit,$r_tahun,$r_bulan,$jenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select r.*, p.nik,".static::schema()."f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,j.namajenisdinas
					from ".static::table('pe_rwtdinas')." r
					left join ".static::schema()."ms_pegawai p on p.idpegawai=r.pegditunjuk
					left join ".static::schema()."ms_unit u on u.idunit=p.idunit
					left join ".static::schema()."ms_jenisdinas j on j.kodejenisdinas=r.kodejenisdinas
					where r.issetujuatasan = 'S' and r.issetujuwarek2 = 'S' and r.issetujukasdm = 'S' and r.issetujukabagkeu = 'S' and r.kodejenisdinas in ('$jenis')
					and date_part('year',r.tglpergi) = '$r_tahun' and date_part('month',r.tglpergi) = '".str_pad($r_bulan, 2, "0", STR_PAD_LEFT)."'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			$rs = $conn->Execute($sql);
			
			
			//rate perjalanan dinas
			$sql = "select sum(p.nominal) as jmlrate,p.nodinas 
					from ".static::table('pe_biayadinas')." p 
					left join ".static::schema()."pe_rwtdinas r on r.nodinas=p.nodinas
					left join ".static::schema()."ms_pegawai m on m.idpegawai=r.pegditunjuk
					left join ".static::schema()."ms_unit u on u.idunit=m.idunit
					where r.issetujuatasan = 'S' and r.issetujuwarek2 = 'S' and r.issetujukasdm = 'S' and r.issetujukabagkeu = 'S' and r.kodejenisdinas in ('$jenis')
					and date_part('year',r.tglpergi) = '$r_tahun' and date_part('month',r.tglpergi) = '".str_pad($r_bulan, 2, "0", STR_PAD_LEFT)."'
					and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					group by p.nodinas";
			$rsr = $conn->Execute($sql);
			
			while($rowr = $rsr->FetchRow()){
				$a_rate[$rowr['nodinas']] = $rowr['jmlrate'];
			}
		
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit'], 'rate' => $a_rate);
			
			return $a_data;	
		}
		
		function repAnggaranDinas($conn,$r_kodeunit,$r_tahun){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			
			$sql = "select a.*, u.namaunit
					from ".static::table('ms_anggarandinas')." a
					left join ".static::schema()."ms_unit u on u.idunit=a.idunit
					where thnanggaran='$r_tahun' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."";
			
			$rs1 = $conn->Execute($sql);
			
			$a_datalist = array();
			while ($row = $rs1->FetchRow())
				$a_datalist[] = $row;
					
			$sql = "select d.idunit,p.nik,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai, sum(nominal) as tarif, b.nodinas
					from ".static::schema()."pe_rwtdinas d
					left join ".static::schema()."ms_pegawai p on p.idpegawai=d.pegditunjuk
					left join ".static::schema()."pe_biayadinas b on b.nodinas=d.nodinas
					left join ".static::schema()."ms_unit u on u.idunit=d.idunit
					left join ".static::schema()."ms_anggarandinas a on a.idunit=d.idunit
					where issetujuatasan='S' and issetujukasdm='S' and issetujuwarek2='S' and issetujukabagkeu='S'
					and a. thnanggaran='$r_tahun' and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					group by d.idunit,p.nik,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang), b.nodinas";
		
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[$row['idunit']][] = $row;
			
			$a_data = array('anggaranunit' => $a_datalist, 'list' => $rs, 'detailpemakaian' => $a_data, 'namaunit' => $col['namaunit']);
			
			return $a_data;			
		}
		
		/************************************************** E N D  O F  L A P O R A N ************************************************/
	}
?>
