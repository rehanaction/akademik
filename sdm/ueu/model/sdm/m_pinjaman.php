<?php
	// model agama
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('model'));
	
	class mPinjaman extends mModel {
		const schema = 'sdm';
		
		
		/**************************************************** PINJAMAN ******************************************************/
		// mendapatkan kueri list untuk setting kehadiran
		function listQueryPerjanjian($r_tahun) {
			$sql = "select p.*, m.nip, j.jnspinjaman,a.angsuran,
					sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang)||' - '||u.namaunit as namalengkap 
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('lv_jnspinjaman')." j on j.kodejnspinjaman=p.kodejnspinjaman
					left join (select sum(aa.jmlangsuran) as angsuran,idpinjaman from sdm.pe_angsuran aa where isdibayar='Y' group by idpinjaman) a on a.idpinjaman = p.idpinjaman
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit";
					if(!empty($r_tahun) and $r_tahun != 'all')
						$sql .= " where DATE_PART('year', p.tglperjanjian) = '$r_tahun'";
					else
						$sql .= " where (1=1)";
			
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
						return "DATE_PART('year', tglperjanjian) = '$key'";
					else
						return "(1=1)";
					
					break;
				
				case 'jnspinjaman':
					if($key != 'all')
						return "p.kodejnspinjaman = '$key'";
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
			$sql = "select  DATE_PART('year', tglperjanjian) as tahun 
					from ".static::table('pe_pinjaman')."
					group by DATE_PART('year', tglperjanjian) order by DATE_PART('year', tglperjanjian) desc";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Tahun --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['tahun']] = $row['tahun'];
			}
						
			return $a_data;
		}
		
		function getJnsPinjaman($conn){
			$sql = "select kodejnspinjaman, jnspinjaman from ".static::table('lv_jnspinjaman')." where isaktif='Y'";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			$a_add = array('all' => '-- Semua Jenis --');
			$a_data = array_merge($a_data,$a_add);
			
			while($row = $rs->FetchRow()){
				$a_data[$row['kodejnspinjaman']] = $row['jnspinjaman'];
			}
						
			return $a_data;
		}
		
		function getLastTahun($conn){
			$sql = "select top 1 DATE_PART('year', tglperjanjian) as tahun 
					from ".static::table('pe_pinjaman')."
					group by DATE_PART('year', tglperjanjian) order by DATE_PART('year', tglperjanjian) desc";
			$tahun = $conn->GetOne($sql);
			
			return $tahun;
		}
		
		function getCJenisPinjaman($conn){
			$sql = "select kodejnspinjaman, jnspinjaman from ".static::table('lv_jnspinjaman')." where isaktif='Y'";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function filterJenisPinjaman($conn,$where=''){
			$sql = "select kodejnspinjaman, jnspinjaman from ".static::table('lv_jnspinjaman')." 
					{$where}
					order by kodejnspinjaman";
			
			return Query::arrQuery($conn, $sql);
		}
		
		function getDataEditPerjanjian($r_key){
			$sql = "select p.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang)||' - '||u.namaunit as namalengkap,
					substring(p.periodeawal,1,4) as tahun, cast(substring(p.periodeawal,5,2) as int) as bulan
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					where idpinjaman=$r_key";
			
			return $sql;
		}
		
		function getDataEditPembayaran($r_key){
			$sql = "select p.*, j.jnspinjaman,
					sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang)||' - '||u.namaunit as namalengkap 
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('ms_pegawai')." m on m.idpegawai=p.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=m.idunit
					left join ".static::table('lv_jnspinjaman')." j on j.kodejnspinjaman=p.kodejnspinjaman
					where idpinjaman=$r_key";
			
			return $sql;
		}
		
		function getProsesBayar($conn,$key,$label='',$post='') {
			$sql = "select * from ".static::table('pe_bayarpinjaman')." where idpinjaman = '$key' and bayarvia = 'K' order by tglbayar";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['idbayarpinjaman'] = $row['idbayarpinjaman'];
				$t_data['nobkm'] = $row['nobkm'];
				$t_data['tglbayar'] = $row['tglbayar'];
				$t_data['jmlbayar'] = $row['jmlbayar'];
				$t_data['keterangan'] = $row['keterangan'];
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function saveAngsuran($conn,$record,$r_key){
			$conn->Execute("delete from ".static::table('pe_angsuran')." where idpinjaman = $r_key");
			
			//insert
			if($record['totalpinjaman'] > 0 and $record['jmlcicilandisetujui'] > 0){
				$bsr = $record['totalpinjaman']/$record['jmlcicilandisetujui'];
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
			$sql = "select a.*,substring(a.periodegajitunda,1,4) as tahun,cast(substring(a.periodegajitunda,5,2) as int) as bulan,b.nobkm
					from ".static::table('pe_angsuran')." a
					left join ".static::table('pe_bayarpinjaman')." b on b.idbayarpinjaman=a.idbayarpinjaman
					where a.idpinjaman = $r_key order by a.noangsuran";
			$rs = $conn->Execute($sql);
			
			return $rs;
		}
		
		function statusBayar(){
			return array('Y' => 'Sudah dibayar', 'N' => 'Belum dibayar', 'T' => 'Ditunda');
		}	
		
		//menentukan angsurannya
		function getAngsuranPerjanjian($conn,$r_key){
			$sql = "select min(noangsuran) as min,max(noangsuran) as max,jmlangsuran
					from ".static::table('pe_angsuran')." 
					where idpinjaman = $r_key group by jmlangsuran order by min(noangsuran)";
			$rs = $conn->Execute($sql);
			
			while($row = $rs->FetchRow()){
				$t_data['min'] = $row['min'];
				$t_data['max'] = $row['max'];
				$t_data['jmlangsuran'] = $row['jmlangsuran'];
				
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
		function repLapPiutangPeg($conn,$r_kodeunit,$sqljenis,$r_idpegawai){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_kodeunit);
			$jenispinjaman = $conn->GetOne("select jnspinjaman from ".static::table('lv_jnspinjaman')." pj where (1=1) {$sqljenis}");
			
			$sql = "select pj.idpeminjam,".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit
					from ".static::table('pe_pinjaman')." pj
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pj.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					where (pj.islunas <> 'Y' or pj.islunas is null) {$sqljenis}";
			
			if(!empty($r_idpegawai))
				$sql .= " and pj.idpeminjam = $r_idpegawai";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql.=" group by p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang,u.namaunit,pj.idpeminjam,u.infoleft order by u.infoleft";
			
			$rs = $conn->Execute($sql);
			
			// Data angsuran
			$sql = "select pj.*,jp.jnspinjaman,pj.saldo,b.tglbayar, b.nobkm,a.jmlangsuran,a.isdibayar,a.keterangan 
					from ".static::table('pe_pinjaman')." pj
					left join ".static::table('ms_pegawai')." p on p.idpegawai=pj.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit
					left join ".static::table('pe_angsuran')." a on a.idpinjaman=pj.idpinjaman
					left join ".static::table('pe_bayarpinjaman')." b on b.idbayarpinjaman=a.idbayarpinjaman and b.idpinjaman=pj.idpinjaman
					left join ".static::table('lv_jnspinjaman ')." jp on jp.kodejnspinjaman=pj.kodejnspinjaman
					where (pj.islunas <> 'Y' or pj.islunas is null) {$sqljenis}";
			
			if(!empty($r_idpegawai))
				$sql .= " and p.idpegawai = $r_idpegawai ";
			if(!empty($r_kodeunit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .="order by a.noangsuran";
			
			$rsa = $conn->Execute($sql);
			
			while($rowa = $rsa->FetchRow()){
				$a_angsuran[$rowa['idpeminjam']][$rowa['idpinjaman']][] = $rowa;
			}
			
			$a_data = array('list' => $rs, 'namaunit' => $col['namaunit'], 'angsuran' => $a_angsuran, 'jenispinjaman' => $jenispinjaman);
			
			return $a_data;
		}
		
		//laporan angsuran pinjaman pegawai sebelum di potong
		function repLapAngsuranPinjaman($conn,$r_periode, $r_unit,$sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//kepada
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural ='22200'";
			$rs = $conn->Execute($sql);
			
			$kepada = array();
			while ($row = $rs->FetchRow()){
				$kepada['kepegawaian'] = $row['namalengkap'];
				$kepada['jabkepegawaian'] = $row['jabatanstruktural'];
			}
			
			//Daftar peminjam
			$sql = "select u.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang) as namalengkap
					from ".static::table('ms_unit')." u
					left join ".static::table('ga_historydatagaji')." hi on hi.idunit=u.idunit
					left join ".static::table('pe_pinjaman')." p on p.idpeminjam=hi.idpeg
					left join ".static::table('ms_pegawai')." pe on pe.idpegawai=p.idpeminjam
					left join ".static::table('ms_struktural')." s on s.idjstruktural=pe.idjstruktural
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
						(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar <> 'Y'
						order by ap.noangsuran)
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') and p.periodeawal <= '$r_periode'";
			
			if(!empty($r_unit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .=" group by s.idjstruktural,s.jabatanstruktural,u.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang)";
			
			$rss = $conn->Execute($sql);
			
			$a_data = array();
			while ($rows = $rss->FetchRow()){
				$a_data[] = $rows;
				$a_peminjam[$rows['idpeminjam']] = $rows['idpeminjam'];
			}
			
			if(count($a_peminjam) > 0)
				$i_peminjam = implode(",",$a_peminjam);
				
			//mendapatkan total per unit
			$sql = "select u.idunit,p.kodejnspinjaman,sum(jmlangsuran) as totalunit
					from ".static::table('ms_unit')." u
					left join ".static::table('ms_pegawai')." pg on pg.idunit = u.idunit
					left join ".static::table('pe_pinjaman')." p on p.idpeminjam = pg.idpegawai
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar <> 'Y' order by ap.noangsuran) 
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') 
					and p.periodeawal <= '$r_periode' {$sqljenis}
					group by u.idunit,p.kodejnspinjaman";
			$rst = $conn->Execute($sql);
			
			$a_totalunit = array();
			while ($rowt = $rst->FetchRow())
				$a_totalunit[$rowt['idunit']][$rowt['kodejnspinjaman']] = $rowt['totalunit'];
				
			//mendapatkan total
			$sql = "select p.kodejnspinjaman,sum(jmlangsuran) as total
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar <> 'Y' order by ap.noangsuran) 
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') 
					and p.periodeawal <= '$r_periode' {$sqljenis}
					group by p.kodejnspinjaman";
			$rstt = $conn->Execute($sql);
			
			$a_total = array();
			while ($rowtt = $rstt->FetchRow())
				$a_total[$rowtt['kodejnspinjaman']] = $rowtt['total'];
			
			return array("kepada" => $kepada, "data" => $a_data, "namaperiode" => $namaperiode, "namaunit" => $col['namaunit'], "i_peminjam" => $i_peminjam, "totalunit" => $a_totalunit, "total" => $a_total);
		}
		
		//detail angsuran pinjaman pegawai sebelum di potong
		function getDetailAngsuranPinjaman($conn,$r_unit,$r_periode,$i_peminjam,$sqljenis){
			
			//data pinjaman
			$sql = "select s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang) as namalengkap
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('ms_pegawai')." pe on pe.idpegawai=p.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=pe.idunit
					left join ".static::table('ms_struktural')." s on s.idunit=u.idunit
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
						(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar <> 'Y'
						order by ap.noangsuran)
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') and p.periodeawal <= '$r_periode'
					and u.parentunit = $r_unit {$sqljenis}";
			if(!empty($i_peminjam))
				$sql .= " and p.idpeminjam not in ($i_peminjam)";
				
			$sql .=" group by s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang)";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getNominalAngsuran($conn,$r_periode,$sqljenis){
			
			//data nominal angsuran
			$sql = "select p.idpeminjam,p.kodejnspinjaman,sum(a.jmlangsuran) as jmlangsuran
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
						(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar <> 'Y'
						order by ap.noangsuran)
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') and p.periodeawal <= '$r_periode' {$sqljenis}
					group by p.idpeminjam,p.kodejnspinjaman";
			$rs = $conn->Execute($sql);
			
			$a_angsuran = array();
			while ($row = $rs->FetchRow())
				$a_angsuran[$row['idpeminjam']][$row['kodejnspinjaman']] = $row['jmlangsuran'];
			
			return $a_angsuran;
		}
		
		//laporan rekap piutang pegawai setelah dipotong gaji
		function repLapRekapPiutang($conn,$r_periode, $r_unit,$sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
			
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//kepada
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural ='22200'";
			$rs = $conn->Execute($sql);
			
			$kepada = array();
			while ($row = $rs->FetchRow()){
				$kepada['kepegawaian'] = $row['namalengkap'];
				$kepada['jabkepegawaian'] = $row['jabatanstruktural'];
			}
			
			//Daftar peminjam
			$sql = "select u.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang) as namalengkap
					from ".static::table('ms_unit')." u
					left join ".static::table('ga_historydatagaji')." hi on hi.idunit=u.idunit
					left join ".static::table('pe_pinjaman')." p on p.idpeminjam=hi.idpeg
					left join ".static::table('ms_pegawai')." pe on pe.idpegawai=p.idpeminjam
					left join ".static::table('ms_struktural')." s on s.idjstruktural=pe.idjstruktural
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
						(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar = 'Y'
						order by ap.noangsuran desc)
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') and p.periodeawal <= '$r_periode'";
			
			if(!empty($r_unit))
				$sql .= " and u.infoleft >= ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight'];
			
			$sql .=" group by s.idjstruktural,s.jabatanstruktural,u.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang)";
			
			$rss = $conn->Execute($sql);
			
			$a_data = array();
			while ($rows = $rss->FetchRow()){
				$a_data[] = $rows;
				$a_peminjam[$rows['idpeminjam']] = $rows['idpeminjam'];
			}
			
			if(count($a_peminjam) > 0)
				$i_peminjam = implode(",",$a_peminjam);
				
			//mendapatkan total per unit
			$sql = "select u.idunit,p.kodejnspinjaman,sum(jmlangsuran) as totalunit
					from ".static::table('ms_unit')." u
					left join ".static::table('ms_pegawai')." pg on pg.idunit = u.idunit
					left join ".static::table('pe_pinjaman')." p on p.idpeminjam = pg.idpegawai
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar = 'Y' order by ap.noangsuran desc) 
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') 
					and p.periodeawal <= '$r_periode' {$sqljenis}
					group by u.idunit,p.kodejnspinjaman";
			$rst = $conn->Execute($sql);
			
			$a_totalunit = array();
			while ($rowt = $rst->FetchRow())
				$a_totalunit[$rowt['idunit']][$rowt['kodejnspinjaman']] = $rowt['totalunit'];
				
			//mendapatkan total
			$sql = "select p.kodejnspinjaman,sum(jmlangsuran) as total
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar = 'Y' order by ap.noangsuran desc) 
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') 
					and p.periodeawal <= '$r_periode' {$sqljenis}
					group by p.kodejnspinjaman";
			$rstt = $conn->Execute($sql);
			
			$a_total = array();
			while ($rowtt = $rstt->FetchRow())
				$a_total[$rowtt['kodejnspinjaman']] = $rowtt['total'];
			
			return array("kepada" => $kepada, "data" => $a_data, "namaperiode" => $namaperiode, "namaunit" => $col['namaunit'], "i_peminjam" => $i_peminjam, "totalunit" => $a_totalunit, "total" => $a_total);
		}
		
		//detail arekap piutang pegawai setelah dipotong gaji
		function getDetailRekapPiutang($conn,$r_unit,$r_periode,$i_peminjam,$sqljenis){
			
			//data pinjaman
			$sql = "select s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang) as namalengkap
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('ms_pegawai')." pe on pe.idpegawai=p.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=pe.idunit
					left join ".static::table('ms_struktural')." s on s.idunit=u.idunit
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
						(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar = 'Y'
						order by ap.noangsuran desc)
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') and p.periodeawal <= '$r_periode'
					and u.parentunit = $r_unit {$sqljenis}";
			if(!empty($i_peminjam))
				$sql .= " and p.idpeminjam not in ($i_peminjam)";
				
			$sql .=" group by s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,p.idpeminjam,
					".static::schema.".f_namalengkap(pe.gelardepan,pe.namadepan,pe.namatengah,pe.namabelakang,pe.gelarbelakang)";
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[] = $row;
			
			return $a_data;
		}
		
		function getNominalPiutang($conn,$r_periode,$sqljenis){
			
			//data nominal angsuran
			$sql = "select p.idpeminjam,p.kodejnspinjaman,a.jmlangsuran
					from ".static::table('pe_pinjaman')." p
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
						(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = p.idpinjaman and ap.isdibayar = 'Y'
						order by ap.noangsuran desc)
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') and p.periodeawal <= '$r_periode' {$sqljenis}";
			$rs = $conn->Execute($sql);
			
			$a_angsuran = array();
			while ($row = $rs->FetchRow())
				$a_angsuran[$row['idpeminjam']][$row['kodejnspinjaman']] = $row['jmlangsuran'];
			
			return $a_angsuran;
		}
		
		function rekapPermohonanPot($conn, $r_periode, $r_unit, $sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//kepada
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural ='22200'";
			$rs = $conn->Execute($sql);
			
			$kepada = array();
			while ($row = $rs->FetchRow()){
				$kepada['kepegawaian'] = $row['namalengkap'];
				$kepada['jabkepegawaian'] = $row['jabatanstruktural'];
			}
			
			//pegawai di dalam unit anggaran
			$sql = "select u.idunitanggaran,g.idpeminjam 
					from ".static::table('pe_pinjaman')." g 
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeminjam
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit 
					left join ".static::table('ms_unit')." un on un.idunit=u.idunitanggaran 
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = g.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = g.idpinjaman and ap.isdibayar <> 'Y' order by ap.noangsuran) 
					where g.isfixpinjam = 'Y' and (g.islunas is null or g.islunas = 'N') and g.periodeawal <= '$r_periode' and u.idunitanggaran is not null {$sqljenis}
					and u.infoleft > ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					group by u.idunitanggaran,g.idpeminjam,p.idpegawai,un.infoleft,s.infoleft 
					order by coalesce(un.infoleft,p.idpegawai),coalesce(s.infoleft,p.idpegawai)";
			$rsu = $conn->Execute($sql);
			
			$a_unit = array();
			while ($rowu = $rsu->FetchRow()){
				$a_unit[$rowu['idunitanggaran']][$rowu['idpeminjam']] = $rowu['idpeminjam'];
			}
			
			
			//data gaji pegawai
			$sql = "select u.idunitanggaran,un.namaunit,g.idpeminjam,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".static::table('pe_pinjaman')." g 
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpeminjam 
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit 
					left join ".static::table('ms_unit')." un on un.idunit=u.idunitanggaran 
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = g.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = g.idpinjaman and ap.isdibayar <> 'Y' order by ap.noangsuran) 
					where g.isfixpinjam = 'Y' and (g.islunas is null or g.islunas = 'N') and g.periodeawal <= '$r_periode' and u.idunitanggaran is not null {$sqljenis}
					and u.infoleft > ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					group by u.idunitanggaran,un.namaunit,g.idpeminjam,p.idpegawai,p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang,un.infoleft,s.infoleft 
					order by coalesce(un.infoleft,p.idpegawai),coalesce(s.infoleft,p.idpegawai)";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data[$row['idpeminjam']][]= $row;
			}
				
			return array("kepada" => $kepada, "data" => $a_data, "namaperiode" => $namaperiode,"unit"=> $a_unit);
		}

		function rekapRealisasiPot($conn, $r_periode, $r_unit, $sqljenis){
			global $conf;
			require_once($conf['gate_dir'].'model/m_unit.php');
					
			$col = mUnit::getData($conn,$r_unit);
			
			$namaperiode = $conn->GetOne("select namaperiode from ".static::table('ga_periodegaji')." where periodegaji='$r_periode'");
			
			//kepada
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from ".static::table('ms_pegawai')." p
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural ='22200'";
			$rs = $conn->Execute($sql);
			
			$kepada = array();
			while ($row = $rs->FetchRow()){
				$kepada['kepegawaian'] = $row['namalengkap'];
				$kepada['jabkepegawaian'] = $row['jabatanstruktural'];
			}
			
			//pegawai di dalam unit anggaran
			$sql = "select u.idunitanggaran,g.idpeminjam 
					from ".static::table('pe_pinjaman')." g 
					left join ".static::table('ms_pegawai')." p on p.idpegawai = g.idpeminjam
					left join ".static::table('ga_gajipeg')." gj on gj.idpegawai = p.idpegawai and periodegaji = '$r_periode'
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit 
					left join ".static::table('ms_unit')." un on un.idunit=u.idunitanggaran 
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = g.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = g.idpinjaman and ap.isdibayar = 'Y'
					order by ap.noangsuran desc)
					where g.isfixpinjam = 'Y' and (g.islunas is null or g.islunas = 'N') and g.periodeawal <= '$r_periode' and u.idunitanggaran is not null {$sqljenis}
					and u.infoleft > ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']." and gj.isfinish = 'Y'
					group by u.idunitanggaran,g.idpeminjam,p.idpegawai,un.infoleft,s.infoleft 
					order by coalesce(un.infoleft,p.idpegawai),coalesce(s.infoleft,p.idpegawai)";
			$rsu = $conn->Execute($sql);
			
			$a_unit = array();
			while ($rowu = $rsu->FetchRow()){
				$a_unit[$rowu['idunitanggaran']][$rowu['idpeminjam']] = $rowu['idpeminjam'];
			}
			
			
			//data gaji pegawai
			$sql = "select u.idunitanggaran,un.namaunit,g.idpeminjam,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from ".static::table('pe_pinjaman')." g 
					left join ".static::table('ms_pegawai')." p on p.idpegawai=g.idpeminjam 
					left join ".static::table('ms_unit')." u on u.idunit=p.idunit 
					left join ".static::table('ms_unit')." un on un.idunit=u.idunitanggaran 
					left join ".static::table('ms_struktural')." s on s.idjstruktural=p.idjstruktural
					left join ".static::table('pe_angsuran')." a on a.idpinjaman = g.idpinjaman and a.noangsuran = 
					(select top 1 ap.noangsuran from ".static::table('pe_angsuran')." ap where ap.idpinjaman = g.idpinjaman and ap.isdibayar = 'Y'
					order by ap.noangsuran desc)
					where g.isfixpinjam = 'Y' and (g.islunas is null or g.islunas = 'N') and g.periodeawal <= '$r_periode' and u.idunitanggaran is not null {$sqljenis}
					and u.infoleft > ".(int)$col['infoleft']." and u.inforight <= ".(int)$col['inforight']."
					group by u.idunitanggaran,un.namaunit,g.idpeminjam,p.idpegawai,p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang,un.infoleft,s.infoleft 
					order by coalesce(un.infoleft,p.idpegawai),coalesce(s.infoleft,p.idpegawai)";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data[$row['idpeminjam']][]= $row;
			}
				
			return array("kepada" => $kepada, "data" => $a_data, "namaperiode" => $namaperiode,"unit"=> $a_unit);
		}

		/**************************************************** E N D OF L A P O R A N ******************************************************/
	
	}
?>
