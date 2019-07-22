<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));
	
	class mPegawai extends mBiodata {
		const schema = 'sdm';
		const table = 'ms_pegawai';
		const order = 'namalengkap';
		const key = 'idpegawai';
		const sequence = 'ms_pegawai_idpegawai_seq';
		const label = 'pegawai';
		
		// mendapatkan kueri list
		function listQuery() {
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,u.namaunit,p.nip,
					p.nodosen,p.alamat,p.telepon,s.namastatusaktif,p.nohp,p.email,p.idpegawai,g.golongan,j.jenispegawai,p.tglmasuk,
					substring(".static::schema.".get_mkpengabdian(p.idpegawai),1,2)::int ||' Tahun ' || 
					substring(".static::schema.".get_mkpengabdian(p.idpegawai),3,2)::int ||' Bulan' as masakerja
					from ".self::table()." p 
					left join ".static::schema.".lv_statusaktif s on s.idstatusaktif=p.idstatusaktif
					left join ".static::schema.".ms_pangkat g on g.idpangkat=p.idpangkat
					left join ".static::schema.".ms_jenispeg j on j.idjenispegawai=p.idjenispegawai
					left join ".static::schema.".ms_unit u on u.idunit=p.idunit";
			
			return $sql;
		}
		
		//mendapatkan informasi detail pegawai
		function getInfoPegawai($conn,$key){
			$sql = "select * from ".static::schema.".v_pegawai where idpegawai='$key'";
			
			return $conn->GetRow($sql);
		}
		
		//mendapatkan informasi id pegawai
		function getIDPegawai($conn, $key){
			$sql = "select idpegawai from ".static::schema.".ms_pegawai where nip='$key'";
			
			return $conn->GetOne($sql);
		}		
		
		// ganti password
		function changePassword($conn,$username) {
			$sql = "update gate.sc_user set password = md5(coalesce(hints,'')),
					t_updateact = 'resetpass', ".Query::logUpdateGate()." where username = '$username'"; 
			$conn->Execute($sql);
			
			return $conn->ErrorNo();
		}
		
		function getNamaPegawai($conn, $key){
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap from ".static::schema.".ms_pegawai where idpegawai='$key'";
			
			return $conn->GetOne($sql);
		}		
		
		function getAIDPegawai($conn){
			$sql = "select idpegawai,nip from ".static::schema.".ms_pegawai where idstatusaktif in (select idstatusaktif from ".static::schema()."lv_statusaktif where iskeluar='T')";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[$row['nip']] = $row['idpegawai'];
				
			return $a_data;		
		}
		
		// mendapatkan kolom filter list
		function getArrayListFilterCol() {
			$data['unit'] = 'p.idunit';
			$data['tipepeg'] = 'p.idtipepeg';
			$data['jenispeg'] = 'p.idjenispegawai';
			$data['hubkerja'] = 'p.idhubkerja';
			
			return $data;
		}
		
		// mendapatkan potongan kueri filter list
		function getListFilter($col,$key) {
			switch($col) {
				case 'unit':
					global $conn, $conf;
					require_once($conf['gate_dir'].'model/m_unit.php');
					
					$row = mUnit::getData($conn,$key);
					
					return "u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight'];
			}
		}
		
		//cek email apakah sudah ada sebelumnya
		function getCekEmail($conn,$r_email,$r_key){
			$cek = $conn->GetOne("select 1 from ".static::schema.".ms_pegawai where email = '$r_email' and idpegawai <> $r_key limit 1");
			
			return $cek;
		}
		
		// mendapatkan nama pegawai
		function getSimplePegawai($conn,$idpegawai) {
			$sql = "select ".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,nip from ".static::table()." where idpegawai = '$idpegawai'";
			
			return $conn->GetRow($sql);
		}
		//create NIP
		function createNIP($conn){
				$thn = substr(date('Y'),2,2);
				$bln = date('m');
				$urut = $conn->GetOne("select coalesce(max(cast(substring(nip,6,4) as int)),0)+1 from ".static::schema.".ms_pegawai where length(nip) = 9 and substring(nip,2,2) = '$thn'");
				$urutnip = str_pad($urut,3,"0",STR_PAD_LEFT);

				$nik = $thn.$bln.$urutnip;
				return $nik;
}
		
		function getDataEditBiodata($r_key) {
			$sql = "select p.*, 
					case when p.idkelurahan is not null then coalesce(prop.namapropinsi,'')||', '|| coalesce(kab.namakabupaten,'') ||', '|| coalesce(kec.namakecamatan,'')|| ', '|| coalesce(kel.namakelurahan,'') end as kelurahan,
					case when p.idkelurahanktp is not null then coalesce(propktp.namapropinsi,'')|| ', '|| coalesce(kabktp.namakabupaten,'')|| ', '|| coalesce(kecktp.namakecamatan,'')|| ', '|| coalesce(kelktp.namakelurahan,'') end  as kelurahanktp
					from ".self::table('ms_pegawai')." p
					left join ".self::table('lv_propinsi')." prop on prop.idpropinsi=substring(p.idkelurahan,1,2)
					left join ".self::table('lv_kabupaten')." kab on kab.idkabupaten=substring(p.idkelurahan,1,4)
					left join ".self::table('lv_kecamatan')." kec on kec.idkecamatan=substring(p.idkelurahan,1,6)
					left join ".self::table('lv_kelurahan')." kel on kel.idkelurahan=p.idkelurahan
					left join ".self::table('lv_propinsi')." propktp on propktp.idpropinsi=substring(p.idkelurahanktp,1,2)
					left join ".self::table('lv_kabupaten')." kabktp on kabktp.idkabupaten=substring(p.idkelurahanktp,1,4)
					left join ".self::table('lv_kecamatan')." kecktp on kecktp.idkecamatan=substring(p.idkelurahanktp,1,6)
					left join ".self::table('lv_kelurahan')." kelktp on kelktp.idkelurahan=p.idkelurahanktp
					where idpegawai = $r_key";
			
			return $sql;
		}
		
		function getDataEditKepegawaian($r_key) {
			$sql = "select p.*, 
					case when p.idkelurahan is not null then coalesce(prop.namapropinsi,'')||', '||coalesce(kab.namakabupaten,'')||', '||coalesce(kec.namakecamatan,'')||', '||coalesce(kel.namakelurahan,'') end as kelurahan,
					case when p.idkelurahanktp is not null then coalesce(propktp.namapropinsi,'')||', '||coalesce(kabktp.namakabupaten,'')||', '||coalesce(kecktp.namakecamatan,'')||', '||coalesce(kelktp.namakelurahan,'') end  as kelurahanktp
					from ".self::table('ms_pegawai')." p
					left join ".self::table('lv_propinsi')." prop on prop.idpropinsi=substring(p.idkelurahan,1,2)
					left join ".self::table('lv_kabupaten')." kab on kab.idkabupaten=substring(p.idkelurahan,1,4)
					left join ".self::table('lv_kecamatan')." kec on kec.idkecamatan=substring(p.idkelurahan,1,6)
					left join ".self::table('lv_kelurahan')." kel on kel.idkelurahan=p.idkelurahan
					left join ".self::table('lv_propinsi')." propktp on propktp.idpropinsi=substring(p.idkelurahanktp,1,2)
					left join ".self::table('lv_kabupaten')." kabktp on kabktp.idkabupaten=substring(p.idkelurahanktp,1,4)
					left join ".self::table('lv_kecamatan')." kecktp on kecktp.idkecamatan=substring(p.idkelurahanktp,1,6)
					left join ".self::table('lv_kelurahan')." kelktp on kelktp.idkelurahan=p.idkelurahanktp
					where idpegawai = $r_key";
			
			return $sql;
		}
		
		function golDarah(){
			return array('A' => 'A','B' => 'B','AB' => 'AB','O' => 'O');
		}
		
		function ukuranBaju(){
			return array('S' => 'S','M' => 'M','L' => 'L','XL' => 'XL','XXL' => 'XXL');
		}
		
		function bank($conn) {
			$sql = "select idbank, namabank from ".static::schema()."ms_bank order by namabank";
			
			return Query::arrQuery($conn,$sql);
		}
		
		function kelDosen($conn) {
			$sql = "select kodekeldosen, namakeldosen ||' : '|| jamkerja ||' Jam' as namakeldosen from ".static::schema()."ms_kelompokdosen order by kodekeldosen";
			
			return Query::arrQuery($conn,$sql);
		}		
		
		function milikPeg($conn) {
			$sql = "select idmilikpeg, milikpeg from ".static::schema()."ms_kepemilikanpegawai order by milikpeg";
			
			return Query::arrQuery($conn,$sql);
		}
		
		//mendapatkan tipe pegawai
		function getTipePegawai($conn, $key){
			$sql = "select isdosen from ".static::schema.".ms_pegawai where idpegawai=$key";
			
			return $conn->GetOne($sql);
		}
		
		//mendapatkan tipe pegawai
		function getTipePeg($conn,$key){
			$sql = "select idtipepeg from ".static::schema.".ms_pegawai where idpegawai=$key";
			print_r($sql);
			die();
			return $conn->GetOne($sql);
		}
		
		//membuat tipe dan jenis pegawai
		function jenisTipePegawai($conn){
			$sql = "select j.idjenispegawai,t.tipepeg||' - '||j.jenispegawai
					from ".self::table('ms_jenispeg')." j
					left join ".self::table('ms_tipepeg')." t on t.idtipepeg = j.idtipepeg
					order by t.idtipepeg";
					
			return Query::arrQuery($conn,$sql);
		}
		
		//============================================= L A P O R A N ==============================================
		// combo kolom
		function kolom(){
			$a_kolom['jeniskelamin'] = 'L/P';
			$a_kolom['tmplahir'] = 'Tempat Lahir';
			$a_kolom['tgllahir'] = 'Tanggal Lahir';
			$a_kolom['usia'] = 'Usia';
			$a_kolom['agama'] = 'Agama';
			$a_kolom['alamat'] = 'Alamat';
			$a_kolom['kelurahan'] = 'Kelurahan';
			$a_kolom['kecamatan'] = 'Kecamatan';
			$a_kolom['kabupaten'] = 'Kabupaten';
			$a_kolom['kodepos'] = 'Kodepos';
			$a_kolom['email'] = 'Email';
			$a_kolom['telepon'] = 'Telp.';
			$a_kolom['nohp'] = 'Handphone';
			$a_kolom['unitkerja'] = 'Unit Kerja - Sub Unit';
			//$a_kolom['unithomebase'] = 'Unit Homebase';
			$a_kolom['statusaktif'] = 'Status Keaktifan';
			$a_kolom['statuspegawai'] = 'Status Pegawai';
			$a_kolom['tipepegawai'] = 'Tipe Pegawai';
			$a_kolom['jenispegawai'] = 'Jenis Pegawai';
			$a_kolom['jabatanfungsional'] = 'Jabatan Akademik';
			$a_kolom['jabatan'] = 'Jabatan Struktural';
			$a_kolom['jabatanatasan'] = 'Jabatan Atasan';
			$a_kolom['golongan'] = 'Pangkat/Golongan';
			$a_kolom['mkseluruh'] = 'Masa Kerja Pengabdian';
			$a_kolom['mkgolongan'] = 'Masa Kerja Golongan';
			$a_kolom['tmtcoba'] = 'Tanggal Percobaan';
			$a_kolom['tmttetap'] = 'Tanggal Pengangkatan ';
			$a_kolom['tglmasuk'] = 'Tanggal Masuk Kerja ';
			$a_kolom['pendidikan'] = 'Pendidikan Terakhir';
			$a_kolom['tmtpensiun'] = 'Tanggal Pensiun';	
			$a_kolom['nidn'] = 'NIDN';
			$a_kolom['nodosen'] = 'No. Dosen';
			$a_kolom['milikpeg'] = 'Kelompok';
			//$a_kolom['nosertifikat'] = 'No. Serdos';
			$a_kolom['statuskawin'] = 'Status Pernikahan';
			$a_kolom['suamiistri'] = 'Nama Suami/Istri';
			$a_kolom['jmlanak'] = 'Jumlah Anak';
			$a_kolom['nip'] = 'NPP';
			$a_kolom['nama'] = 'Nama';
			
			ksort($a_kolom);
			
			return $a_kolom;
		}
		
		//jenis urutan
		function jenisUrutan(){
			return array('A' => 'Asc', 'D' => 'Desc');
		}
		
		//kriteria
		function kriteria(){			
			$a_kriteria['unitkerja'] = 'Unit Kerja';
			//$a_kriteria['unithomebase'] = 'Unit Homebase';
			$a_kriteria['tipepegawai'] = 'Tipe Pegawai';	
			$a_kriteria['jenispegawai'] = 'Jenis Pegawai';	
			$a_kriteria['hubungankerja'] = 'Hubungan Kerja';	
			$a_kriteria['statusaktif'] = 'Status Keaktifan';
			$a_kriteria['tmtcoba'] = 'Tanggal Calon';
			$a_kriteria['tmttetap'] = 'Tanggal Pengangkatan';
			$a_kriteria['tglmasuk'] = 'Tanggal Masuk Kerja ';
			$a_kriteria['tmtpensiun'] = 'Tanggal Pensiun';
			$a_kriteria['mkseluruh'] = 'Masa Kerja Pengabdian';
			$a_kriteria['mkgolongan'] = 'Masa Kerja Golongan';
			$a_kriteria['usia'] = 'Usia';
			$a_kriteria['fungsional'] = 'Jabatan Fungsional';
			$a_kriteria['golongan'] = 'Pangkat/ Golongan';
			$a_kriteria['pendidikan'] = 'Pendidikan Terakhir';
			
			return $a_kriteria;
		}
		
		//daftar template
		function template($conn) {
			$sql = "select idtemplate, namatemplate from ".static::schema()."pe_templatereport order by idtemplate";
			
			return Query::arrQuery($conn,$sql);
		}
		
		//mendapatkan data template report
		function getTemplate($conn,$r_template){
			$row = $conn->GetRow("select * from ".static::schema.".pe_templatereport where idtemplate = '$r_template'");
			
			return $row;
		}
		
		//kolom laporan
		function kolomLaporan($a_kolom,$kolom){
			//mulai looping
			for($i=0;$i<count($a_kolom);$i++){
				if($i == 0)
					$nama_kolom = $kolom[$a_kolom[$i]];
				else
					$nama_kolom .= ','. $kolom[$a_kolom[$i]];
				
				//join tabel
				if($a_kolom[$i] == 'jabatanatasan'){
					$nama_tabel1 = ' left join '.static::schema().'ms_struktural js on v.idjstrukturalatasan = js.idjstruktural';
					$cek['jabatanatasan'] = true;
				}
				else if($a_kolom[$i] == 'tmtpensiun'){
					$nama_tabel1 = ' left join '.static::schema().'pe_pensiun pp on pp.idpegawai = v.idpegawai';			
					$cek['tmtpensiun'] = true;
				}
				else if($a_kolom[$i] == 'suamiistri'){
					$nama_tabel1 = " left join ".static::schema()."pe_istrisuami si on si.idpegawai = v.idpegawai and si.isvalid='Y'";			
					$cek['suamiistri'] = true;
				}
				else if($a_kolom[$i] == 'jmlanak'){
					$nama_tabel1 = " left join (select idpegawai,count(*) as jmlanak from ".static::schema()."pe_anak where isvalid='Y' group by idpegawai) ja on ja.idpegawai = v.idpegawai";			
					$cek['jmlanak'] = true;
				}
				else
					$nama_tabel1 = '';					
				$nama_tabel	= $nama_tabel .''.$nama_tabel1;
			}
			
			return array($nama_kolom,$nama_tabel);
		}
		
		//cek join
		function varJoin(){
			$cek = array();	
			$cek['unitkerja'] = false;			
			//$cek['unithomebase'] = false;			
			$cek['jabatanatasan'] = false;	
			$cek['tmtpensiun'] = false;
			$cek['namaistri'] = false;
			$cek['jmlanak'] = false;
			
			return $cek;
		}
		
		//select field
		function selectField(){
			$kolom = array();
			$kolom['jeniskelamin'] = 'v.jeniskelamin';
			$kolom['tmplahir'] = 'v.tmplahir';
			$kolom['tgllahir'] = 'v.tgllahir';
			$kolom['usia'] = "cast(v.umurth as varchar) || ' Tahun ' || cast(v.umurmonth as varchar) || ' Bulan' as usia";
			$kolom['agama'] = 'v.namaagama';
			$kolom['alamat'] = 'v.alamat';
			$kolom['kelurahan'] = 'v.kelurahan';
			$kolom['kecamatan'] = 'v.kecamatan';
			$kolom['kabupaten'] = 'v.kabupaten';
			$kolom['kodepos'] = 'v.kodepos';
			$kolom['email'] = 'v.email';
			$kolom['telepon'] = 'v.telepon';
			$kolom['nohp'] = 'v.nohp';
			$kolom['unitkerja'] = 'v.namaunit, v.namaparent as parent';
			//$kolom['unithomebase'] = 'v.unithomebase';
			$kolom['statusaktif'] = 'v.namastatusaktif';
			$kolom['statuspegawai'] = 'v.hubkerja';
			$kolom['tipepegawai'] = 'v.tipepeg';
			$kolom['jenispegawai'] = 'v.jenispegawai';
			$kolom['jabatanfungsional'] = 'v.jabatanfungsional';
			$kolom['jabatan'] = 'v.jabatanstruktural';
			$kolom['golongan'] = 'v.namagolongan';
			$kolom['jabatanatasan'] = 'js.jabatanstruktural as jabatanatasan';
			$kolom['mkseluruh'] = "cast(v.masakerjathn as varchar) || ' ' || cast(v.masakerjabln as varchar) as mkseluruh";
			$kolom['mkgolongan'] = "cast(v.masakerjathngol as varchar) || ' ' || cast(v.masakerjablngol as varchar) as mkgolongan";
			$kolom['tmtcoba'] = 'v.tglcalon';
			$kolom['tmttetap'] = 'v.tglpengangkatan';
			$kolom['tglmasuk'] = 'v.tglmasuk';
			$kolom['pendidikan'] = 'v.namapendidikan';
			$kolom['tmtpensiun'] = 'coalesce(pp.tmtpensiun,v.tglpensiun) as pensiun';	
			$kolom['nidn'] = 'v.nidn';
			$kolom['nodosen'] = 'v.nodosen';
			$kolom['milikpeg'] = 'v.milikpeg';
			//$kolom['nosertifikat'] = 'v.nosertifikasi';
			$kolom['statuskawin'] = 'v.statusnikah';
			$kolom['suamiistri'] = 'si.namapasangan as suamiistri';
			$kolom['jmlanak'] = 'ja.jmlanak';
			$kolom['nip'] = 'v.nip';
			$kolom['nama'] = 'v.namalengkap';
			
			return $kolom;
		}
		
		function terjemahUrutan(){
			$terjemah_urutan = array();
			$terjemah_urutan['jeniskelamin'] = 'v.jeniskelamin';
			$terjemah_urutan['tmplahir'] = 'v.tmplahir';
			$terjemah_urutan['tgllahir'] = 'v.tgllahir';
			$terjemah_urutan['usia'] = "cast(umurth as varchar) || ' ' || cast(umurmonth as varchar)";
			$terjemah_urutan['agama'] = 'v.namaagama';
			$terjemah_urutan['alamat'] = 'v.alamat';
			$terjemah_urutan['kelurahan'] = 'v.kelurahan';
			$terjemah_urutan['kecamatan'] = 'v.kecamatan';
			$terjemah_urutan['kabupaten'] = 'v.kabupaten';
			$terjemah_urutan['kodepos'] = 'v.kodepos';
			$terjemah_urutan['email'] = 'v.email';
			$terjemah_urutan['telepon'] = 'v.telepon';
			$terjemah_urutan['nohp'] = 'v.nohp';
			$terjemah_urutan['unitkerja'] = 'v.namaunit';
			//$terjemah_urutan['unithomebase'] = 'v.unithomebase';
			$terjemah_urutan['statusaktif'] = 'v.namastatusaktif';
			$terjemah_urutan['statuspegawai'] = 'v.hubkerja';
			$terjemah_urutan['tipepegawai'] = 'v.tipepeg';
			$terjemah_urutan['jenispegawai'] = 'v.jenispegawai';
			$terjemah_urutan['jabatanfungsional'] = 'v.jabatanfungsional';
			$terjemah_urutan['jabatan'] = 'v.jabatanstruktural';
			$terjemah_urutan['golongan'] = 'v.namagolongan';
			$terjemah_urutan['jabatanatasan'] = 'js.jabatanstruktural';
			$terjemah_urutan['mkseluruh'] = "cast(v.masakerjathn as varchar) || ' ' || cast(v.masakerjabln as varchar)";
			$terjemah_urutan['mkgolongan'] = "cast(v.masakerjathngol as varchar) || ' ' || cast(v.masakerjablngol as varchar)";
			$terjemah_urutan['tmtcoba'] = 'v.tglcalon';
			$terjemah_urutan['tmttetap'] = 'v.tglpengangkatan';
			$terjemah_urutan['tglmasuk'] = 'v.tglmasuk';
			$terjemah_urutan['pendidikan'] = 'v.namapendidikan';
			$terjemah_urutan['tmtpensiun'] = 'coalesce(pp.tmtpensiun,v.tglpensiun)';	
			$terjemah_urutan['nidn'] = 'v.nidn';
			$terjemah_urutan['nodosen'] = 'v.nodosen';
			$terjemah_urutan['milikpeg'] = 'v.milikpeg';
			//$terjemah_urutan['nosertifikat'] = 'v.nosertifikasi';
			$terjemah_urutan['statuskawin'] = 'v.statusnikah';
			$terjemah_urutan['suamiistri'] = 'si.nama';
			$terjemah_urutan['jmlanak'] = 'ja.jmlanak';
			$terjemah_urutan['nip'] = 'v.nip';
			$terjemah_urutan['nama'] = 'v.namalengkap';
			
			return $terjemah_urutan;
		}
		
		function lebarKolom(){
			$lebarkolom = array();
			$lebarkolom['jeniskelamin'] = 50;
			$lebarkolom['tmplahir'] = 100;
			$lebarkolom['tgllahir'] = 100;
			$lebarkolom['usia'] = 50;
			$lebarkolom['agama'] = 100;
			$lebarkolom['alamat'] = 250;
			$lebarkolom['kelurahan'] = 150;
			$lebarkolom['kecamatan'] = 150;
			$lebarkolom['kabupaten'] = 150;
			$lebarkolom['kodepos'] = 100;
			$lebarkolom['email'] = 150;
			$lebarkolom['telepon'] = 150;
			$lebarkolom['nohp'] = 150;
			$lebarkolom['unitkerja'] = 350;
			//$lebarkolom['unithomebase'] = 200;
			$lebarkolom['statusaktif'] = 150;
			$lebarkolom['statuspegawai'] = 150;
			$lebarkolom['tipepegawai'] = 75;
			$lebarkolom['jenispegawai'] = 75;
			$lebarkolom['jabatanfungsional'] = 150;
			$lebarkolom['jabatan'] = 150;
			$lebarkolom['golongan'] = 75;
			$lebarkolom['jabatanatasan'] = 200;
			$lebarkolom['mkseluruh'] = 150;
			$lebarkolom['mkgolongan'] = 150;
			$lebarkolom['tmtcoba'] = 100;
			$lebarkolom['tmttetap'] = 100;
			$lebarkolom['tglmasuk'] = 100;
			$lebarkolom['pendidikan'] = 150;
			$lebarkolom['tmtpensiun'] = 100;	
			$lebarkolom['nidn'] = 120;
			$lebarkolom['nodosen'] = 100;
			$lebarkolom['milikpeg'] = 150;
			$lebarkolom['nosertifikat'] = 150;
			$lebarkolom['statuskawin'] = 75;
			$lebarkolom['suamiistri'] = 200;
			$lebarkolom['jmlanak'] = 100;
			$lebarkolom['nip'] = 75;
			$lebarkolom['nama'] = 200;
			
			return $lebarkolom;
		}
		
		function namaKolom(){
			$namakolom = array();
			$namakolom['jeniskelamin'] = 'JENIS KELAMIN';
			$namakolom['tmplahir'] = 'TEMPAT LAHIR';
			$namakolom['tgllahir'] = 'TANGGAL LAHIR';
			$namakolom['usia'] = 'USIA';
			$namakolom['agama'] = 'AGAMA';
			$namakolom['alamat'] = 'ALAMAT';
			$namakolom['kelurahan'] = 'KELURAHAN';
			$namakolom['kecamatan'] = 'KECAMATAN';
			$namakolom['kabupaten'] = 'KABUPATEN';
			$namakolom['kodepos'] = 'KDOEPOS';
			$namakolom['email'] = 'EMAIL';
			$namakolom['telepon'] = 'TELEPON';
			$namakolom['nohp'] = 'NO. HP';
			$namakolom['unitkerja'] = 'UNIT KERJA';
			//$namakolom['unithomebase'] = 'UNIT HOMEBASE';
			$namakolom['statusaktif'] = 'STATUS AKTIF';
			$namakolom['statuspegawai'] = 'STATUS PEGAWAI';
			$namakolom['tipepegawai'] = 'TIPE PEGAWAI';
			$namakolom['jenispegawai'] = 'JENIS PEGAWAI';
			$namakolom['jabatanfungsional'] = 'JAB. FUNGSIONAL AKADEMIK';
			$namakolom['jabatan'] = 'JABATAN STRUKTURAL';
			$namakolom['golongan'] = 'GOLONGAN';
			$namakolom['jabatanatasan'] = 'JABATAN ATASAN';
			$namakolom['mkseluruh'] = 'MASA PENGABDIAN';
			$namakolom['mkgolongan'] = 'MASA KERJA GOLONGAN';
			$namakolom['tmtcoba'] = 'TANGGAL PERCOBAAN';
			$namakolom['tmttetap'] = 'TANGGAL PENGANGKATAN';
			$namakolom['tglmasuk'] = 'TANGGAL MASUK KERJA';
			$namakolom['pendidikan'] = 'PENDIDIKAN TERAKHIR';
			$namakolom['tmtpensiun'] = 'TANGGAL PENSIUN';	
			$namakolom['nidn'] = 'NIDN';
			$namakolom['nodosen'] = 'NO. DOSEN';
			$namakolom['milikpeg'] = 'KELOMPOK';
			$namakolom['nosertifikat'] = 'NO. SERDOS';
			$namakolom['statuskawin'] = 'STATUS NIKAH';
			$namakolom['suamiistri'] = 'NAMA ISTRI/ SUAMI';
			$namakolom['jmlanak'] = 'JML. ANAK';
			$namakolom['nip'] = 'NPP';
			$namakolom['nama'] = 'NAMA LENGKAP';
			
			return $namakolom;
		}
		
		function terjemahKolom(){
			$terjemah_kolom = array();
			$terjemah_kolom['jeniskelamin'] = 'jeniskelamin';
			$terjemah_kolom['tmplahir'] = 'tmplahir';
			$terjemah_kolom['tgllahir'] = 'tgllahir';
			$terjemah_kolom['usia'] = 'usia';
			$terjemah_kolom['agama'] = 'namaagama';
			$terjemah_kolom['alamat'] = 'alamat';
			$terjemah_kolom['kelurahan'] = 'kelurahan';
			$terjemah_kolom['kecamatan'] = 'kecamatan';
			$terjemah_kolom['kabupaten'] = 'kabupaten';
			$terjemah_kolom['kodepos'] = 'kodepos';
			$terjemah_kolom['email'] = 'email';
			$terjemah_kolom['telepon'] = 'telepon';
			$terjemah_kolom['nohp'] = 'nohp';
			$terjemah_kolom['unitkerja'] = 'namaunit';
			$terjemah_kolom['parent'] = 'parent';
			//$terjemah_kolom['unithomebase'] = 'unithomebase';
			$terjemah_kolom['statusaktif'] = 'namastatusaktif';
			$terjemah_kolom['statuspegawai'] = 'hubkerja';
			$terjemah_kolom['tipepegawai'] = 'tipepeg';
			$terjemah_kolom['jenispegawai'] = 'jenispegawai';
			$terjemah_kolom['jabatanfungsional'] = 'jabatanfungsional';
			$terjemah_kolom['jabatan'] = 'jabatanstruktural';
			$terjemah_kolom['golongan'] = 'namagolongan';
			$terjemah_kolom['jabatanatasan'] = 'jabatanatasan';
			$terjemah_kolom['mkseluruh'] = 'mkseluruh';
			$terjemah_kolom['mkgolongan'] = 'mkgolongan';
			$terjemah_kolom['tmtcoba'] = 'tglcalon';
			$terjemah_kolom['tmttetap'] = 'tglpengangkatan';
			$terjemah_kolom['tglmasuk'] = 'tglmasuk';
			$terjemah_kolom['pendidikan'] = 'namapendidikan';
			$terjemah_kolom['tmtpensiun'] = 'pensiun';	
			$terjemah_kolom['nidn'] = 'nidn';
			$terjemah_kolom['nodosen'] = 'nodosen';
			$terjemah_kolom['milikpeg'] = 'milikpeg';
			//$terjemah_kolom['nosertifikat'] = 'nosertifikasi';
			$terjemah_kolom['statuskawin'] = 'statusnikah';
			$terjemah_kolom['suamiistri'] = 'suamiistri';
			$terjemah_kolom['jmlanak'] = 'jmlanak';
			$terjemah_kolom['nip'] = 'nip';
			$terjemah_kolom['nama'] = 'namalengkap';
			
			return $terjemah_kolom;
		}
		
		function terjemahKriteria($conn, $a_kriteria,$a_paramkriteria){
			$cek = array();
			$cek = mPegawai::varJoin();
			
			$terjemah_kriteria = array();	
			$terjemah_kriteria['tipepegawai'] = 'v.idtipepeg';	
			$terjemah_kriteria['jenispegawai'] = 'v.idjenispegawai';	
			$terjemah_kriteria['hubungankerja'] = 'v.idhubkerja';
			$terjemah_kriteria['statusaktif'] = 'v.idstatusaktif';
			$terjemah_kriteria['tmtcoba'] = 'v.tglcalon';
			$terjemah_kriteria['tmttetap'] = 'v.tglpengangkatan';
			$terjemah_kriteria['tglmasuk'] = 'v.tglmasuk';
			$terjemah_kriteria['tmtpensiun'] = 'coalesce(pp.tmtpensiun,v.tglpensiun)';
			$terjemah_kriteria['mkseluruh'] = "cast(v.masakerjathn as int)";
			$terjemah_kriteria['mkgolongan'] = "cast(v.masakerjathngol as int)";
			$terjemah_kriteria['usia'] = "cast(umurth as int)";
			$terjemah_kriteria['fungsional'] = 'v.idjfungsional';
			$terjemah_kriteria['golongan'] = 'v.idpangkat';
			$terjemah_kriteria['pendidikan'] = 'v.idpendidikan'; 
												  
			//jenis kriteria yang di post		
			$a_kriteria_select = array('unitkerja','tipepegawai','jenispegawai','hubungankerja','statusaktif','fungsional','golongan','pendidikan');
			$a_kriteria_between = array('tmtcoba','tmttetap','tglmasuk','tmtpensiun','mkseluruh','mkgolongan','usia');
			
			for($i=0;$i<count($a_kriteria);$i++){	
				$where2 = "";
				if(in_array($a_kriteria[$i],$a_kriteria_select)){ //$where .= " $kriteria in (43,44,45)".				
					$paramkriteria=explode(':',$a_paramkriteria[$i]);	
					
					for($j=0;$j<count($paramkriteria);$j++){					
						if ($a_kriteria[$i] == 'unitkerja'){
							$nama_tabel	= $nama_tabel .''.mPegawai::cekjoin($cek['unitkerja'],$a_kriteria[$i]);	
							$cek['unitkerja'] = true;
							$rsInfo = $conn->GetRow("select infoleft, inforight from ".static::table('ms_unit')." where idunit='$paramkriteria[$j]'");
						}				
						/*else if ($a_kriteria[$i] == 'unithomebase'){
							$nama_tabel	= $nama_tabel .''.mPegawai::cekjoin($cek['unithomebase'],$a_kriteria[$i]);	
							$cek['unithomebase'] = true;
							$rsInfo = $conn->GetRow("select infoleft, inforight from ".static::table('ms_unit')." where idunit='$paramkriteria[$j]'");
						}
						*/	
						if(count($paramkriteria)==1){
							if ($a_kriteria[$i] == 'unitkerja')
								$where1 =" (u.infoleft >= '".$rsInfo['infoleft']."' and u.inforight <='".$rsInfo['inforight']."') ";
							//else if ($a_kriteria[$i] == 'unithomebase')
							//	$where1 =" (uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."') ";
							else
								$where1 =" in ('$paramkriteria[$j]')";
						}
						else{	
							if ($a_kriteria[$i] == 'unitkerja'){
								if($j==0){
									$where1 =" ((u.infoleft >= '".$rsInfo['infoleft']."' and u.inforight <='".$rsInfo['inforight']."') ";
								}
								elseif($j==count($paramkriteria)-1){
									$where1 =" or (u.infoleft >= '".$rsInfo['infoleft']."' and u.inforight <='".$rsInfo['inforight']."')) ";
								}
								else{
									$where1 =" or (u.infoleft >= '".$rsInfo['infoleft']."' and u.inforight <='".$rsInfo['inforight']."') ";
								}
							}	
							/*else if ($a_kriteria[$i] == 'unithomebase'){
								if($j==0){
									$where1 =" ((uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."') ";
								}
								elseif($j==count($paramkriteria)-1){
									$where1 =" or (uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."')) ";
								}
								else{
									$where1 =" or (uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."') ";
								}
							}*/
							else{			
								if($j==0){
									$where1 =" in ('$paramkriteria[$j]',";
								}
								elseif($j==count($paramkriteria)-1){
									$where1 ="'$paramkriteria[$j]')";
								}
								else{
									$where1 ="'$paramkriteria[$j]',";
								}						
							}						
						}
						
						$where2 = $where2 . $where1;			
					}
					
					if ($a_kriteria[$i] != 'unitkerja')
						$where2 = $terjemah_kriteria[$a_kriteria[$i]].' '.$where2;
				}
				elseif(in_array($a_kriteria[$i],$a_kriteria_between)){ //$where .= " $kriteria between 20 and 50".
					$paramkriteria=explode(':',$a_paramkriteria[$i]);
					
					for($j=0;$j<2;$j++){
						if($a_kriteria[$i] == 'mkseluruh' or $a_kriteria[$i] == 'mkgolongan' or $a_kriteria[$i] == 'usia'){
							$paramkriteria[$j]= $paramkriteria[$j];					
						}
						elseif($a_kriteria[$i] == 'tmtcoba' or $a_kriteria[$i] == 'tmttetap' or $a_kriteria[$i] == 'tglmasuk'){
							$paramkriteria[$j]= "'".Cstr::formatDate($paramkriteria[$j])."'";
						}	
						elseif($a_kriteria[$i] == 'tmtpensiun'){
							$nama_tabel	= $nama_tabel .''.mPegawai::cekjoin($cek['tmtpensiun'],$a_kriteria[$i]);	
							$cek['tmtpensiun'] = true;	
							$paramkriteria[$j]= "'".Cstr::formatDate($paramkriteria[$j])."'";
						}			
						
						if($j==0)
							$where1= "between $paramkriteria[$j]";		
						elseif($j==1)
							$where1= " and $paramkriteria[$j]";
						$where2 = $where2 . $where1;
					}			
					$where2 = $terjemah_kriteria[$a_kriteria[$i]].' '.$where2; // jfungsional_now in (...				
				}
				
				if($i==0)
					$where = $where . $where2;		
				else
					$where = $where ." and ". $where2;				
			}
			
			if (empty($where))
				$where = '';
			else
				$where = $nama_tabel . " where " . $where;
			
			return $where;
		}
		
		function urutanJoin($a_urutan){
			$cek = array();
			$cek = mPegawai::varJoin();
			
			$a_terjemahan = array();
			$a_terjemahan = mPegawai::terjemahUrutan();
			for($i=0;$i<count($a_urutan);$i++){
				$urutan1[$i]= explode(':',$a_urutan[$i]);
				for($j=0;$j<2;$j++){ //ada dua parameter dalam menentukan urutan, berdasarkan pilihan list dan bedasarkan asc/disc.
					$urutan2[$i][$j]=$urutan1[$i][$j];
					if($urutan2[$i][$j]=='A'){
						$urutan = $urutan . " ASC";
					}
					else if($urutan2[$i][$j]=='D'){
						$urutan = $urutan . " DESC";
					}			
					else if($i>0 && $j==0)	{				
						$urutan = $urutan . ",". $a_terjemahan[$urutan2[$i][$j]];
						
						$nama_tabel	= $nama_tabel .''.mPegawai::cekjoin($cek[$urutan2[$i][$j]],$urutan2[$i][$j]);
						
						if($urutan2[$i][$j] != '' and $urutan2[$i][$j] != 'A' and $urutan2[$i][$j] != 'D'){
							$cek[$urutan2[$i][$j]] = true;
						}
					}
					else{			
						$urutan = $urutan . "". $a_terjemahan[$urutan2[$i][$j]];
						
						$nama_tabel	= $nama_tabel .''.mPegawai::cekjoin($cek[$urutan2[$i][$j]],$urutan2[$i][$j]);

						if($urutan2[$i][$j] != '' and $urutan2[$i][$j] != 'A' and $urutan2[$i][$j] != 'D'){
								$cek[$urutan2[$i][$j]] = true;
						}			
					}	
				}	
			}	
			return $urutan;
		}
		
		function getLaporanPegawai($conn,$nama_tabel,$nama_kolom,$where,$urutan){
			$sql = "select $nama_kolom from ".static::schema()."v_pegawairep as v $nama_tabel";
				
			if($where!='')		
				$sql .=  $where;		
			if($urutan!='')
				$sql .= " order by ".$urutan;	
			
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow()){
				$a_data[] = $row;
			}
			
			return $a_data;
		}
		
		function cekjoin($cek,$urut){
			if($cek == false && $urut == 'unitkerja'){
				return " left join ".static::schema()."ms_unit u on u.idunit = v.idunit";	
			}
			/*else if($cek == false && $urut == 'unithomebase'){
				return " left join ".static::schema()."ms_unit uh on uh.idunit = v.idunitbase";	
			}*/
			else if($cek == false && $urut == 'jabatanatasan'){
				return ' left join '.static::schema().'ms_struktural js on v.kodejabatanatasan = js.idjstruktural';	
			}
			else if($cek == false && $urut == 'tmtpensiun'){
				return ' left join '.static::schema().'pe_pensiun pp on pp.idpegawai = v.idpegawai';
			}
			else if($cek == false && $urut == 'suamiistri'){
				return " left join ".static::schema()."pe_istrisuami si on si.idpegawai = v.idpegawai and si.isvalid='Y'";
			}
			else if($cek == false && $urut == 'jmlanak'){
				return ' left join (select idpegawai,count(*) as jmlanak from '.static::schema().'pe_anak where isvalid=1 group by idpegawai) ja on ja.idpegawai = v.idpegawai';
			}
			else
				return '';
		}
		
		function jenispegawai($conn){
			$sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}
	}
?>
