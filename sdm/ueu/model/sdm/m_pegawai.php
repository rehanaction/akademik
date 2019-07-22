<?php
	// model user
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	require_once(Route::getModelPath('biodata'));

	class mPegawai extends mBiodata {
		const schema = 'sdm';
		const table = 'ms_pegawai';
		const order = 'namalengkap';
		const key = 'idpegawai';
		const sequence = 'idpegawai';
		const label = 'pegawai';
		
		// mendapatkan kueri list

		
		function listQuery() {
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,u.namaunit,p.nip,
					p.nodosen,p.alamat,p.telepon,p.idstatusaktif,s.namastatusaktif,p.nohp,p.email,p.idpegawai,g.golongan,t.tipepeg
					from ".self::table()." p 
					left join ".static::schema.".lv_statusaktif s on s.idstatusaktif=p.idstatusaktif
					left join ".static::schema.".ms_pangkat g on g.idpangkat=p.idpangkat
					left join ".static::schema.".ms_tipepegbaru t on t.idtipepeg=p.idtipepegbaru
					left join ".static::schema.".ms_unit u on u.idunit=p.idunit";
			
			return $sql;
		}
		function getIdPegawaii($conn){
			$sql = "select idpegawai as idpegawai from " . static::table('ms_pegawai') . " order by idpegawai desc limit 1";
			return $conn->getOne($sql);
		}
		
		//mendapatkan informasi detail pegawai
		function getInfoPegawai($conn,$key){
			$sql = "select * from ".static::schema.".v_pegawai where idpegawai='$key'";
			
			return $conn->GetRow($sql);
		}
		
		//detail pegawai honor
		function getDetailPegawai($conn, $r_key){
			$sql = "select ".static::schema.".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,u.namaunit,
					p.nodosen,p.norekeninghonor,p.anrekeninghonor,p.cabangbankhonor,b.namabank
					from ".self::table()." p 
					left join ".static::schema.".ms_unit u on u.idunit=p.idunitbase
					left join ".static::schema.".ms_bank b on b.idbank=p.idbank
					where p.idpegawai=$r_key";
			
			return $conn->GetRow($sql);
		}
		
		//mendapatkan informasi id pegawai
		function getIDPegawai($conn, $key){
			$sql = "select idpegawai from ".static::schema.".ms_pegawai where nip='$key'";
			
			return $conn->GetOne($sql);
		}		
		
		function getNamaPegawai($conn, $key){
			$sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap from ".static::schema.".ms_pegawai where idpegawai='$key'";
			
			return $conn->GetOne($sql);
		}		
		
		function getAIDPegawai($conn){
			$sql = "select idpegawai,idfinger from ".static::schema.".ms_pegawai where idstatusaktif in (select idstatusaktif from ".static::schema()."lv_statusaktif where iskeluar='T')";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while ($row = $rs->FetchRow())
				$a_data[$row['idfinger']] = $row['idpegawai'];
				
			return $a_data;		
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
					
	            case 'idtipepeg':
	                if ($key != 'all')
	                    return "p.idtipepegbaru = '$key'";
	                else
	                    return "(1=1)";

	                break;
			}
		}
		
		// mendapatkan nama pegawai
		function getSimplePegawai($conn,$idpegawai) {
			$sql = "select ".static::schema.".f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,nip from ".static::table()." where idpegawai = '$idpegawai'";
			
			return $conn->GetRow($sql);
		}
		
		//membuat no dosen
		function createNoDosen($conn){
			$nodosen = $conn->GetOne("select coalesce(max(cast(nodosen as int)),0)+1 from ".static::schema.".ms_pegawai where nodosen is not null and nodosen::NUMERIC = 1");
			
			return $nodosen;
		}
		
		//membuat nip sementara, digunakan utk sistem sendiri
		function createNIP($conn,$kel){
			$thn = substr(date('Y'),2,2);
			$bln = date('m');
			$urut = $conn->GetOne("select coalesce(max(cast(substring(nip,6,4) as int)),0)+1 from ".static::schema.".ms_pegawai where length(nip) >= 9 and substring(nip,3,2) = '$thn'");
			$urutnip = str_pad($urut,4,"0",STR_PAD_LEFT);
			
			$nip = 'U'.$kel.$thn.$bln.$urutnip;
			return $nip;
		}
		
		function getDataEditBiodata($r_key) {
			$sql = "select p.*, 
					case when p.idkelurahan is not null then coalesce(prop.namapropinsi,'')||', '||coalesce(kab.namakabupaten,'')||', '||coalesce(kec.namakecamatan,'')||', '||coalesce(kel.namakelurahan,'') end as kelurahan,
					case when p.idkelurahanktp is not null then coalesce(propktp.namapropinsi,'')||', '||coalesce(kabktp.namakabupaten,'')||', '||coalesce(kecktp.namakecamatan,'')||', '||coalesce(kelktp.namakelurahan,'') end  as kelurahanktp,
					substring(p.mkmengajar,1,2) as mkthn,substring(p.mkmengajar,3,2) as mkbln
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
					case when p.idkelurahanktp is not null then coalesce(propktp.namapropinsi,'')||', '||coalesce(kabktp.namakabupaten,'')||', '||coalesce(kecktp.namakecamatan,'')||', '||coalesce(kelktp.namakelurahan,'') end  as kelurahanktp,
					substring(p.mkmengajar,1,2) as mkthn,substring(p.mkmengajar,3,2) as mkbln,
					case when p.kodept is not null then t.namapt else p.namainstitusi end as pt
					from ".self::table('ms_pegawai')." p
					left join ".self::table('lv_propinsi')." prop on prop.idpropinsi=substring(p.idkelurahan,1,2)
					left join ".self::table('lv_kabupaten')." kab on kab.idkabupaten=substring(p.idkelurahan,1,4)
					left join ".self::table('lv_kecamatan')." kec on kec.idkecamatan=substring(p.idkelurahan,1,6)
					left join ".self::table('lv_kelurahan')." kel on kel.idkelurahan=p.idkelurahan
					left join ".self::table('lv_propinsi')." propktp on propktp.idpropinsi=substring(p.idkelurahanktp,1,2)
					left join ".self::table('lv_kabupaten')." kabktp on kabktp.idkabupaten=substring(p.idkelurahanktp,1,4)
					left join ".self::table('lv_kecamatan')." kecktp on kecktp.idkecamatan=substring(p.idkelurahanktp,1,6)
					left join ".self::table('lv_kelurahan')." kelktp on kelktp.idkelurahan=p.idkelurahanktp
					left join ".self::table('ms_pt')." t on t.kodept=p.kodept
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
			$sql = "select kodekeldosen, namakeldosen||' : '||jamkerja||' Jam' as namakeldosen from ".static::schema()."ms_kelompokdosen order by kodekeldosen";
			
			return Query::arrQuery($conn,$sql);
		}		
		
		function milikPeg($conn) {
			$sql = "select idmilikpeg, milikpeg from ".static::schema()."ms_kepemilikanpegawai order by milikpeg";
			
			return Query::arrQuery($conn,$sql);
		}	
		
		function statusAktifHomebase($conn) {
			$sql = "select idstatusaktifhomebase, namastatusaktifhomebase from sdm.lv_statusaktifhomebase order by namastatusaktifhomebase";
			
			return Query::arrQuery($conn,$sql);
		}
		
		//mendapatkan tipe pegawai
		function getTipePegawai($conn,$key){
		
			$sql = "select idtipepeg from ".static::schema.".ms_pegawai where idpegawai=".$key;
		
			
			return $conn->GetOne($sql);
		}

	    function getCTipePegawai($conn) {
	        $sql = "select idtipepeg, tipepeg
						from " . static::table('ms_tipepeg') . "
						order by idtipepeg";
	        $rs = $conn->Execute($sql);

	        $a_data = array();
	        $a_add = array('all' => '-- Semua Tipe Pegawai --');
	        $a_data = array_merge($a_data, $a_add);

	        while ($row = $rs->FetchRow()) {
	            $a_data[$row['idtipepeg']] = $row['tipepeg'];
	        }


	        return $a_data;
	    }

	    function getCTipePegawaiBaru($conn) {
	        $sql = "select idtipepeg, tipepeg
						from " . static::table('ms_tipepegbaru') . "
						order by idtipepeg";
	        $rs = $conn->Execute($sql);

	        $a_data = array();
	        $a_add = array('all' => '-- Semua Tipe Pegawai --');
	        $a_data = array_merge($a_data, $a_add);

	        while ($row = $rs->FetchRow()) {
	            $a_data[$row['idtipepeg']] = $row['tipepeg'];
	        }


	        return $a_data;
	    }
		
		//mendapatkan nip pegawai
		function getNIP($conn,$r_key){
			$sql = "select nip from ".static::schema.".ms_pegawai where idpegawai='$r_key'";
			
			return $conn->GetOne($sql);
		}
		
		//============================================= L A P O R A N ==============================================
		// combo kolom
		function kolom($role){
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
			$a_kolom['email'] = 'Email Esa Unggul';
			$a_kolom['emailpribadi'] = 'Email Selain Esa Unggul';
			$a_kolom['telepon'] = 'Telp.';
			$a_kolom['nohp'] = 'Handphone';
			$a_kolom['unitkerja'] = 'Unit Kerja';
			$a_kolom['subunitkerja'] = 'Sub Unit Kerja';
			$a_kolom['unithomebase'] = 'Unit Homebase';
			$a_kolom['statusaktif'] = 'Status Keaktifan';
			$a_kolom['statusaktifhb'] = 'Status Aktif Homebase';
			$a_kolom['hubungankerja'] = 'Hubungan Kerja';
			$a_kolom['tipepegawai'] = 'Tipe Pegawai Lama';
			$a_kolom['tipepegawaibaru'] = 'Tipe Pegawai';
			$a_kolom['jenispegawai'] = 'Jenis Pegawai Lama';
			$a_kolom['jenispegawaibaru'] = 'Jenis Pegawai';
			$a_kolom['kelompokpeg'] = 'Kelompok Pegawai';
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
			$a_kolom['bidang'] = 'Bidang Dosen';
			$a_kolom['tmtpensiun'] = 'Tanggal Pensiun';	
			$a_kolom['nidn'] = 'NIDN';
			$a_kolom['pt'] = 'Perguruan Tinggi';
			$a_kolom['nodosen'] = 'No. Dosen';
			$a_kolom['milikpeg'] = 'Kelompok';
			$a_kolom['nosertifikat'] = 'No. Serdos';
			$a_kolom['statuskawin'] = 'Status Pernikahan';
			$a_kolom['suamiistri'] = 'Nama Suami/Istri';
			$a_kolom['jmlanak'] = 'Jumlah Anak';
			$a_kolom['nip'] = 'NIK';
			$a_kolom['nama'] = 'Nama';
			if($role=='A' or $role=='admga' or $role=='gajihrm'){
				$a_kolom['gajitotal'] = 'Gaji Total';
				$a_kolom['ratemengajar'] = 'Rate Mengajar';
				$a_kolom['procpph'] = 'Proc. PPh Honor';
				$a_kolom['tunjanganprestasi'] = 'Tunjangan Prestasi';
				$a_kolom['tunjanganhomebase'] = 'Tunjangan Homebase';
				$a_kolom['shift'] = 'Jadwal Kerja Terakhir';
				$a_kolom['jamsostek'] = 'Iuran Jamsostek';
			}
			$a_kolom['norekening'] = 'No. Rekening Gaji';
			$a_kolom['anrekening'] = 'Atas Nama Rekening Gaji';
			$a_kolom['cabangbank'] = 'Bank Rekening Gaji';
			$a_kolom['norekeninghonor'] = 'No. Rekening Honor';
			$a_kolom['anrekeninghonor'] = 'Atas Nama Rekening Honor';
			$a_kolom['cabangbankhonor'] = 'Bank Rekening Honor';
			$a_kolom['noktp'] = 'No. KTP';
			$a_kolom['nohandkey'] = 'No. Handkey';
			$a_kolom['niphkey'] = 'NIP. Handkey';
			$a_kolom['idfinger'] = 'ID. Finger';
			$a_kolom['npwp'] = 'NPWP';
			$a_kolom['kodeunit'] = 'Kode Unit';
			$a_kolom['namaanak'] = 'Nama Anak';
			$a_kolom['tanggallahiranak'] = 'Tanggal Lahir Anak';
			$a_kolom['tanggallahirpasangan'] = 'Tanggal Lahir Istri/Suami';
			$a_kolom['tmtmulai'] = 'TMT Jabatan Akademik';
			$a_kolom['tmtinpasing'] = 'TMT Pangkat Inpasing';
			$a_kolom['inpasing'] = 'Pangkat Inpasing';
			
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
			$a_kriteria['unithomebase'] = 'Unit Homebase';
			$a_kriteria['tipepegawai'] = 'Tipe Pegawai Lama';	
			$a_kriteria['tipepegawaibaru'] = 'Tipe Pegawai';	
			$a_kriteria['jenispegawai'] = 'Jenis Pegawai Lama';	
			$a_kriteria['jenispegawaibaru'] = 'Jenis Pegawai';	
			$a_kriteria['kelompokpeg'] = 'Kelompok Pegawai';	
			$a_kriteria['hubungankerja'] = 'Hubungan Kerja';	
			$a_kriteria['statusaktif'] = 'Status Keaktifan';
			$a_kriteria['statusaktifhb'] = 'Status Aktif Homebase';
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
			$a_kriteria['noserdos'] = 'No. Serdos';
			$a_kriteria['sesuaibidang'] = 'Kesesuaian Bidang';
			$a_kriteria['periodegaji'] = 'Periode Gaji';
			
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
		function kolomLaporan($a_kolom,$kolom,$a_kriteria,$a_paramkriteria){
			//mulai looping
			for($i=0;$i<count($a_kolom);$i++){
				if($i == 0)
					$nama_kolom = $kolom[$a_kolom[$i]];
				else
					$nama_kolom .= ','. $kolom[$a_kolom[$i]];
				
				//join tabel
				if($a_kolom[$i] == 'jabatanatasan'){
					$nama_tabel1 = ' left join '.static::schema().'ms_struktural js on v.kodejabatanatasan = js.idjstruktural';
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
				else if($a_kolom[$i] == 'ratemengajar'){
					$nama_tabel1 = " left join ".static::schema()."ga_ajardosen gad1 on gad1.idpegawai = v.idpegawai";		
					$cek['ratemengajar'] = true;
				}
				else if($a_kolom[$i] == 'procpph'){
					$nama_tabel1 = " left join ".static::schema()."ga_ajardosen gad2 on gad2.idpegawai = v.idpegawai";
					$cek['procpph'] = true;
				}
				else if($a_kolom[$i] == 'gajitotal'){
					for($ig=0;$ig<count($a_kriteria);$ig++){
						if($a_kriteria[$ig] == 'periodegaji')
							$leftadd = "gp.periodegaji = '$a_paramkriteria[$ig]'";
						else
							$leftadd = "gp.periodegaji=(select top 1 periodegaji from ".static::table('ga_periodegaji')." where refperiodegaji is null order by tglakhirhit desc)";
					}
					
					$nama_tabel1 = " left join ".static::schema()."ga_gajipeg gp on gp.idpegawai = v.idpegawai and {$leftadd}";		
					$cek['gajitotal'] = true;
				}
				else if($a_kolom[$i] == 'tunjanganprestasi'){
					for($ig=0;$ig<count($a_kriteria);$ig++){
						if($a_kriteria[$ig] == 'periodegaji')
							$leftadd = " and tp.periodegaji = '$a_paramkriteria[$ig]'";
						else
							$leftadd = " and tp.periodegaji=(select top 1 periodegaji from ".static::table('ga_periodegaji')." where refperiodegaji is null order by tglakhirhit desc)";
					}
					
					$nama_tabel1 = " left join ".static::schema()."ga_tunjanganpeg tp on tp.idpegawai = v.idpegawai and tp.kodetunjangan='T00022' {$leftadd}";		
					$cek['tunjanganprestasi'] = true;
				}
				else if($a_kolom[$i] == 'tunjanganhomebase'){
					for($ig=0;$ig<count($a_kriteria);$ig++){
						if($a_kriteria[$ig] == 'periodegaji')
							$leftadd = " and th.periodegaji = '$a_paramkriteria[$ig]'";
						else
							$leftadd = " and th.periodegaji=(select top 1 periodegaji from ".static::table('ga_periodegaji')." where refperiodegaji is null order by tglakhirhit desc)";
					}
					
					$nama_tabel1 = " left join ".static::schema()."ga_tunjanganpeg th on th.idpegawai = v.idpegawai and th.kodetunjangan='T00013' {$leftadd}";		
					$cek['tunjanganhomebase'] = true;
				}
				else if($a_kolom[$i] == 'jamsostek'){					
					$nama_tabel1 = " left join ".static::schema()."ga_potonganparam jt on jt.idpegawai = v.idpegawai and jt.kodepotongan='P00004' and jt.tglmulai=(select top 1 jtt.tglmulai from ".static::table('ga_potonganparam')." jtt where jtt.kodepotongan='P00004' and jtt.idpegawai = v.idpegawai order by tglmulai desc)";		
					$cek['tunjanganhomebase'] = true;
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
			$cek['parentunit'] = false;			
			$cek['unithomebase'] = false;			
			$cek['jabatanatasan'] = false;	
			$cek['tmtpensiun'] = false;
			$cek['namaistri'] = false;
			$cek['jmlanak'] = false;
			$cek['gajitotal'] = false;
			$cek['ratemengajar'] = false;
			$cek['procpph'] = false;
			$cek['tunjanganprestasi'] = false;
			$cek['tunjanganhomebase'] = false;
			$cek['jamsostek'] = false;
			
			return $cek;
		}
		
		//select field
		function selectField(){
			$kolom = array();
			$kolom['jeniskelamin'] = 'v.jeniskelamin';
			$kolom['tmplahir'] = 'v.tmplahir';
			$kolom['tgllahir'] = 'v.tgllahir';
			$kolom['usia'] = "cast(v.umurth as varchar) + ' Tahun ' + cast(v.umurmonth as varchar) + ' Bulan' as usia";
			$kolom['agama'] = 'v.namaagama';
			$kolom['alamat'] = 'v.alamat';
			$kolom['kelurahan'] = 'v.kelurahan';
			$kolom['kecamatan'] = 'v.kecamatan';
			$kolom['kabupaten'] = 'v.kabupaten';
			$kolom['kodepos'] = 'v.kodepos';
			$kolom['email'] = 'v.email';
			$kolom['emailpribadi'] = 'v.emailpribadi';
			$kolom['telepon'] = 'v.telepon';
			$kolom['nohp'] = 'v.nohp';
			$kolom['parentunitparentunit'] = 'v.namaparent as parent';
			$kolom['unitkerja'] = 'v.namaunit';
			$kolom['unithomebase'] = 'v.unithomebase';
			$kolom['statusaktif'] = 'v.namastatusaktif';
			$kolom['statusaktifhb'] = 'v.namastatusaktifhomebase';
			$kolom['hubungankerja'] = 'v.hubkerja';
			$kolom['tipepegawai'] = 'v.tipepeg';
			$kolom['tipepegawaibaru'] = 'v.tipepegbaru';
			$kolom['jenispegawai'] = 'v.jenispegawai';
			$kolom['jenispegawaibaru'] = 'v.jenispegawaibaru';
			$kolom['kelompokpeg'] = 'v.namakelompok';
			$kolom['jabatanfungsional'] = 'v.jabatanfungsional';
			$kolom['jabatan'] = 'v.jabatanstruktural';
			$kolom['golongan'] = 'v.namagolongan';
			$kolom['jabatanatasan'] = 'js.jabatanstruktural as jabatanatasan';
			$kolom['mkseluruh'] = "cast(v.masakerjathn as varchar) + ' ' + cast(v.masakerjabln as varchar) as mkseluruh";
			$kolom['mkgolongan'] = "cast(v.masakerjathngol as varchar) + ' ' + cast(v.masakerjablngol as varchar) as mkgolongan";
			$kolom['tmtcoba'] = 'v.tglcalon';
			$kolom['tmttetap'] = 'v.tglpengangkatan';
			$kolom['tglmasuk'] = 'v.tglmasuk';
			$kolom['pendidikan'] = 'v.namapendidikan';
			$kolom['bidang'] = 'v.namabidang';
			$kolom['tmtpensiun'] = 'coalesce(pp.tmtpensiun,v.tglpensiun) as pensiun';	
			$kolom['nidn'] = 'v.nidn';
			$kolom['pt'] = 'v.pt';
			$kolom['nodosen'] = 'v.nodosen';
			$kolom['milikpeg'] = 'v.milikpeg';
			$kolom['nosertifikat'] = 'v.nosertifikasi';
			$kolom['statuskawin'] = 'v.statusnikah';
			$kolom['suamiistri'] = 'si.namapasangan as suamiistri';
			$kolom['jmlanak'] = 'ja.jmlanak';
			$kolom['nip'] = 'v.nip';
			$kolom['nama'] = 'v.namalengkap';
			$kolom['gajitotal'] = 'gp.gajitotal';
			$kolom['ratemengajar'] = 'gad1.ratereguler as ratemengajar';
			$kolom['procpph'] = 'coalesce(gad2.procpphmanual,gad2.procpph) as procpph';
			$kolom['noktp'] = 'v.noktp';
			$kolom['nohandkey'] = 'v.nohandkey';
			$kolom['niphkey'] = 'v.niphkey';
			$kolom['idfinger'] = 'v.idfinger';
			$kolom['npwp'] = 'v.npwp';
			$kolom['tunjanganprestasi'] = 'tp.nominal as tunjanganprestasi';
			$kolom['tunjanganhomebase'] = 'th.nominal as tunjanganhomebase';
			$kolom['jamsostek'] = 'jt.nominal as jamsostek';
			$kolom['kodeunit'] = 'v.kodeunit';
			$kolom['norekening'] = 'v.norekening';
			$kolom['anrekening'] = 'v.anrekening';
			$kolom['cabangbank'] = 'v.cabangbank';
			$kolom['norekeninghonor'] = 'v.norekeninghonor';
			$kolom['anrekeninghonor'] = 'v.anrekeninghonor';
			$kolom['cabangbankhonor'] = 'v.cabangbankhonor';
			$kolom['namaanak'] = 'v.idpegawai';
			$kolom['tanggallahiranak'] = 'v.idpegawai';
			$kolom['tanggallahirpasangan'] = 'v.tgllahirpasangan';
			$kolom['tmtmulai'] = 'v.tmtmulai';
			$kolom['tmtinpasing'] = 'v.tmtinpasing';
			$kolom['inpasing'] = 'v.inpasing';
			
			return $kolom;
		}
		
		function terjemahUrutan(){
			$terjemah_urutan = array();
			$terjemah_urutan['jeniskelamin'] = 'v.jeniskelamin';
			$terjemah_urutan['tmplahir'] = 'v.tmplahir';
			$terjemah_urutan['tgllahir'] = 'v.tgllahir';
			$terjemah_urutan['usia'] = "cast(umurth as varchar) + ' ' + cast(umurmonth as varchar)";
			$terjemah_urutan['agama'] = 'v.namaagama';
			$terjemah_urutan['alamat'] = 'v.alamat';
			$terjemah_urutan['kelurahan'] = 'v.kelurahan';
			$terjemah_urutan['kecamatan'] = 'v.kecamatan';
			$terjemah_urutan['kabupaten'] = 'v.kabupaten';
			$terjemah_urutan['kodepos'] = 'v.kodepos';
			$terjemah_urutan['email'] = 'v.email';
			$terjemah_urutan['emailpribadi'] = 'v.emailpribadi';
			$terjemah_urutan['telepon'] = 'v.telepon';
			$terjemah_urutan['nohp'] = 'v.nohp';
			$terjemah_urutan['parentunit'] = 'v.namaparent';
			$terjemah_urutan['unitkerja'] = 'v.infoleft';
			$terjemah_urutan['unithomebase'] = 'v.unithomebase';
			$terjemah_urutan['statusaktif'] = 'v.namastatusaktif';
			$terjemah_urutan['statusaktifhb'] = 'v.namastatusaktifhomebase';
			$terjemah_urutan['hubungankerja'] = 'v.hubkerja';
			$terjemah_urutan['tipepegawai'] = 'v.tipepeg';
			$terjemah_urutan['tipepegawaibaru'] = 'v.tipepegbaru';
			$terjemah_urutan['jenispegawai'] = 'v.jenispegawai';
			$terjemah_urutan['jenispegawaibaru'] = 'v.jenispegawaibaru';
			$terjemah_urutan['kelompokpeg'] = 'v.namakelompok';
			$terjemah_urutan['jabatanfungsional'] = 'v.jabatanfungsional';
			$terjemah_urutan['jabatan'] = 'v.jabatanstruktural';
			$terjemah_urutan['golongan'] = 'v.namagolongan';
			$terjemah_urutan['jabatanatasan'] = 'js.jabatanstruktural';
			$terjemah_urutan['mkseluruh'] = "cast(v.masakerjathn as varchar) + ' ' + cast(v.masakerjabln as varchar)";
			$terjemah_urutan['mkgolongan'] = "cast(v.masakerjathngol as varchar) + ' ' + cast(v.masakerjablngol as varchar)";
			$terjemah_urutan['tmtcoba'] = 'v.tglcalon';
			$terjemah_urutan['tmttetap'] = 'v.tglpengangkatan';
			$terjemah_urutan['tglmasuk'] = 'v.tglmasuk';
			$terjemah_urutan['pendidikan'] = 'v.namapendidikan';
			$terjemah_urutan['bidang'] = 'v.namabidang';
			$terjemah_urutan['tmtpensiun'] = 'coalesce(pp.tmtpensiun,v.tglpensiun)';	
			$terjemah_urutan['nidn'] = 'v.nidn';
			$terjemah_urutan['pt'] = 'v.pt';
			$terjemah_urutan['nodosen'] = 'v.nodosen';
			$terjemah_urutan['milikpeg'] = 'v.milikpeg';
			$terjemah_urutan['nosertifikat'] = 'v.nosertifikasi';
			$terjemah_urutan['statuskawin'] = 'v.statusnikah';
			$terjemah_urutan['suamiistri'] = 'si.nama';
			$terjemah_urutan['jmlanak'] = 'ja.jmlanak';
			$terjemah_urutan['nip'] = 'v.nip';
			$terjemah_urutan['nama'] = 'v.namalengkap';
			$terjemah_urutan['gajitotal'] = 'gp.gajitotal';
			$terjemah_urutan['ratemengajar'] = 'gad1.ratereguler';
			$terjemah_urutan['procpph'] = 'coalesce(gad2.procpphmanual,gad2.procpph) as procpph';
			$terjemah_urutan['noktp'] = 'v.noktp';
			$terjemah_urutan['nohandkey'] = 'v.nohandkey';
			$terjemah_urutan['niphkey'] = 'v.niphkey';
			$terjemah_urutan['idfinger'] = 'v.idfinger';
			$terjemah_urutan['npwp'] = 'v.npwp';
			$terjemah_urutan['tunjanganprestasi'] = 'tp.nominal';
			$terjemah_urutan['tunjanganhomebase'] = 'th.nominal';
			$terjemah_urutan['jamsostek'] = 'jt.nominal';
			$terjemah_urutan['kodeunit'] = 'v.kodeunit';
			$terjemah_urutan['norekening'] = 'v.norekening';
			$terjemah_urutan['anrekening'] = 'v.anrekening';
			$terjemah_urutan['cabangbank'] = 'v.cabangbank';
			$terjemah_urutan['norekeninghonor'] = 'v.norekeninghonor';
			$terjemah_urutan['anrekeninghonor'] = 'v.anrekeninghonor';
			$terjemah_urutan['cabangbankhonor'] = 'v.cabangbankhonor';
			$terjemah_urutan['namaanak'] = 'v.idpegawai';
			$terjemah_urutan['tanggallahiranak'] = 'v.idpegawai';
			$terjemah_urutan['tanggallahirpasangan'] = 'v.tgllahirpasangan';
			$terjemah_urutan['tmtmulai'] = 'v.tmtmulai';
			$terjemah_urutan['tmtinpasing'] = 'v.tmtinpasing';
			$terjemah_urutan['inpasing'] = 'v.inpasing';
			
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
			$lebarkolom['emailpribadi'] = 200;
			$lebarkolom['telepon'] = 150;
			$lebarkolom['nohp'] = 150;
			$lebarkolom['parentunit'] = 350;
			$lebarkolom['unitkerja'] = 350;
			$lebarkolom['unithomebase'] = 200;
			$lebarkolom['statusaktif'] = 150;
			$lebarkolom['statusaktifhb'] = 150;
			$lebarkolom['hubungankerja'] = 150;
			$lebarkolom['tipepegawai'] = 75;
			$lebarkolom['tipepegawaibaru'] = 75;
			$lebarkolom['jenispegawai'] = 75;
			$lebarkolom['jenispegawaibaru'] = 75;
			$lebarkolom['kelompokpeg'] = 75;
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
			$lebarkolom['bidang'] = 150;
			$lebarkolom['tmtpensiun'] = 100;	
			$lebarkolom['nidn'] = 120;
			$lebarkolom['pt'] = 200;
			$lebarkolom['nodosen'] = 100;
			$lebarkolom['milikpeg'] = 150;
			$lebarkolom['nosertifikat'] = 150;
			$lebarkolom['statuskawin'] = 75;
			$lebarkolom['suamiistri'] = 200;
			$lebarkolom['jmlanak'] = 100;
			$lebarkolom['nip'] = 75;
			$lebarkolom['nama'] = 200;
			$lebarkolom['gajitotal'] = 100;
			$lebarkolom['ratemengajar'] = 100;
			$lebarkolom['procpph'] = 80;
			$lebarkolom['noktp'] = 200;
			$lebarkolom['nohandkey'] = 100;
			$lebarkolom['niphkey'] = 100;
			$lebarkolom['idfinger'] = 100;
			$lebarkolom['npwp'] = 200;
			$lebarkolom['tunjanganprestasi'] = 150;
			$lebarkolom['tunjanganhomebase'] = 150;
			$lebarkolom['jamsostek'] = 150;
			$lebarkolom['kodeunit'] = 100;
			$lebarkolom['norekening'] = 100;
			$lebarkolom['anrekening'] = 150;
			$lebarkolom['cabangbank'] = 150;
			$lebarkolom['norekeninghonor'] = 100;
			$lebarkolom['anrekeninghonor'] = 150;
			$lebarkolom['cabangbankhonor'] = 150;
			$lebarkolom['namaanak'] = 250;
			$lebarkolom['tanggallahiranak'] = 110;
			$lebarkolom['tanggallahirpasangan'] = 110;
			$lebarkolom['tmtmulai'] = 100;
			$lebarkolom['tmtinpasing'] = 100;
			$lebarkolom['inpasing'] = 150;
			
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
			$namakolom['email'] = 'EMAIL ESA UNGGUL';
			$namakolom['emailpribadi'] = 'EMAIL SELAIN ESA UNGGUL';
			$namakolom['telepon'] = 'TELEPON';
			$namakolom['nohp'] = 'NO. HP';
			$namakolom['parentunit'] = 'PARENT UNIT KERJA';
			$namakolom['unitkerja'] = 'UNIT KERJA';
			$namakolom['unithomebase'] = 'UNIT HOMEBASE';
			$namakolom['statusaktif'] = 'STATUS AKTIF';
			$namakolom['statusaktifhb'] = 'STATUS AKTIF HOMEBASE';
			$namakolom['hubungankerja'] = 'HUBUNGAN KERJA';
			$namakolom['tipepegawai'] = 'TIPE PEGAWAI LAMA';
			$namakolom['tipepegawaibaru'] = 'TIPE PEGAWAI';
			$namakolom['jenispegawai'] = 'JENIS PEGAWAI LAMA';
			$namakolom['jenispegawaibaru'] = 'JENIS PEGAWAI';
			$namakolom['kelompokpeg'] = 'KELOMPOK PEGAWAI';
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
			$namakolom['bidang'] = 'BIDANG DOSEN';
			$namakolom['tmtpensiun'] = 'TANGGAL PENSIUN';	
			$namakolom['nidn'] = 'NIDN';
			$namakolom['pt'] = 'PERGURUAN TINGGI';
			$namakolom['nodosen'] = 'NO. DOSEN';
			$namakolom['milikpeg'] = 'KELOMPOK';
			$namakolom['nosertifikat'] = 'NO. SERDOS';
			$namakolom['statuskawin'] = 'STATUS NIKAH';
			$namakolom['suamiistri'] = 'NAMA ISTRI/ SUAMI';
			$namakolom['jmlanak'] = 'JML. ANAK';
			$namakolom['nip'] = 'NIK';
			$namakolom['nama'] = 'NAMA LENGKAP';
			$namakolom['gajitotal'] = 'GAJI TOTAL';
			$namakolom['ratemengajar'] = 'RATE MENGAJAR';
			$namakolom['procpph'] = 'PROC. PPh HONOR (%)';
			$namakolom['noktp'] = 'NO. KTP';
			$namakolom['nohandkey'] = 'NO. HANDKEY';
			$namakolom['niphkey'] = 'NIP HANDKEY';
			$namakolom['idfinger'] = 'ID FINGER';
			$namakolom['npwp'] = 'NPWP';
			$namakolom['tunjanganprestasi'] = 'TUNJANGAN PRESTASI';
			$namakolom['tunjanganhomebase'] = 'TUNJANGAN HOMEBASE';
			$namakolom['jamsostek'] = 'IURAN JAMSOSTEK';
			$namakolom['kodeunit'] = 'KODE UNIT';
			$namakolom['norekening'] = 'NO. REKENING GAJI';
			$namakolom['anrekening'] = 'AN. REKENING GAJI';
			$namakolom['cabangbank'] = 'BANK REKENING GAJI';
			$namakolom['norekeninghonor'] = 'NO. REKENING HONOR';
			$namakolom['anrekeninghonor'] = 'AN. REKENING HONOR';
			$namakolom['cabangbankhonor'] = 'BANK REKENING HONOR';
			$namakolom['namaanak'] = 'NAMA ANAK';
			$namakolom['tanggallahiranak'] = 'TGL. LAHIR ANAK';
			$namakolom['tanggallahirpasangan'] = 'TGL. LAHIR ISTRI/SUAMI';
			$namakolom['tmtmulai'] = 'TMT JABATAN AKADEMIK';
			$namakolom['tmtinpasing'] = 'TMT INPASING';
			$namakolom['inpasing'] = 'PANGKAT INPASING';
			
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
			$terjemah_kolom['emailpribadi'] = 'emailpribadi';
			$terjemah_kolom['telepon'] = 'telepon';
			$terjemah_kolom['nohp'] = 'nohp';
			$terjemah_kolom['parentunit'] = 'parent';
			$terjemah_kolom['unitkerja'] = 'namaunit';
			$terjemah_kolom['unithomebase'] = 'unithomebase';
			$terjemah_kolom['statusaktif'] = 'namastatusaktif';
			$terjemah_kolom['statusaktifhb'] = 'namastatusaktifhomebase';
			$terjemah_kolom['hubungankerja'] = 'hubkerja';
			$terjemah_kolom['tipepegawai'] = 'tipepeg';
			$terjemah_kolom['tipepegawaibaru'] = 'tipepegbaru';
			$terjemah_kolom['jenispegawai'] = 'jenispegawai';
			$terjemah_kolom['jenispegawaibaru'] = 'jenispegawaibaru';
			$terjemah_kolom['kelompokpeg'] = 'namakelompok';
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
			$terjemah_kolom['bidang'] = 'namabidang';
			$terjemah_kolom['tmtpensiun'] = 'pensiun';	
			$terjemah_kolom['nidn'] = 'nidn';
			$terjemah_kolom['pt'] = 'pt';
			$terjemah_kolom['nodosen'] = 'nodosen';
			$terjemah_kolom['milikpeg'] = 'milikpeg';
			$terjemah_kolom['nosertifikat'] = 'nosertifikasi';
			$terjemah_kolom['statuskawin'] = 'statusnikah';
			$terjemah_kolom['suamiistri'] = 'suamiistri';
			$terjemah_kolom['jmlanak'] = 'jmlanak';
			$terjemah_kolom['nip'] = 'nip';
			$terjemah_kolom['nama'] = 'namalengkap';
			$terjemah_kolom['gajitotal'] = 'gajitotal';
			$terjemah_kolom['ratemengajar'] = 'ratemengajar';
			$terjemah_kolom['procpph'] = 'procpph';
			$terjemah_kolom['noktp'] = 'noktp';
			$terjemah_kolom['nohandkey'] = 'nohandkey';
			$terjemah_kolom['niphkey'] = 'niphkey';
			$terjemah_kolom['idfinger'] = 'idfinger';
			$terjemah_kolom['npwp'] = 'npwp';
			$terjemah_kolom['tunjanganprestasi'] = 'tunjanganprestasi';
			$terjemah_kolom['tunjanganhomebase'] = 'tunjanganhomebase';
			$terjemah_kolom['jamsostek'] = 'jamsostek';
			$terjemah_kolom['kodeunit'] = 'kodeunit';
			$terjemah_kolom['norekening'] = 'norekening';
			$terjemah_kolom['anrekening'] = 'anrekening';
			$terjemah_kolom['cabangbank'] = 'cabangbank';
			$terjemah_kolom['norekeninghonor'] = 'norekeninghonor';
			$terjemah_kolom['anrekeninghonor'] = 'anrekeninghonor';
			$terjemah_kolom['cabangbankhonor'] = 'cabangbankhonor';
			$terjemah_kolom['namaanak'] = 'idpegawai';
			$terjemah_kolom['tanggallahiranak'] = 'idpegawai';
			$terjemah_kolom['tanggallahirpasangan'] = 'tgllahirpasangan';
			$terjemah_kolom['tmtmulai'] = 'tmtmulai';
			$terjemah_kolom['tmtinpasing'] = 'tmtinpasing';
			$terjemah_kolom['inpasing'] = 'inpasing';
			
			return $terjemah_kolom;
		}
		
		function terjemahKriteria($conn, $a_kriteria,$a_paramkriteria){
			//bila ada kriteria gaji dihapus
			$kgaji = array_search('periodegaji',$a_kriteria);
			if($kgaji != '')
				unset($a_kriteria[$kgaji]);
			
			$cek = mPegawai::varJoin();
			
			$terjemah_kriteria = array();	
			$terjemah_kriteria['tipepegawai'] = 'v.idtipepeg';	
			$terjemah_kriteria['tipepegawaibaru'] = 'v.idtipepegbaru';	
			$terjemah_kriteria['jenispegawai'] = 'v.idjenispegawai';	
			$terjemah_kriteria['jenispegawaibaru'] = 'v.idjenispegbaru';	
			$terjemah_kriteria['kelompokpeg'] = 'v.idkelompok';	
			$terjemah_kriteria['hubungankerja'] = 'v.idhubkerja';
			$terjemah_kriteria['statusaktif'] = 'v.idstatusaktif';
			$terjemah_kriteria['statusaktifhb'] = 'v.idstatusaktifhomebase';
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
			$terjemah_kriteria['noserdos'] = 'v.nosertifikasi'; 
			$terjemah_kriteria['sesuaibidang'] = 'v.issesuaibidang'; 
												  
			//jenis kriteria yang di post		
			$a_kriteria_select = array('unitkerja','unithomebase','tipepegawai','tipepegawaibaru','jenispegawai','jenispegawaibaru','kelompokpeg','hubungankerja','statusaktif','statusaktifhb','fungsional','golongan','pendidikan','noserdos','sesuaibidang');
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
						else if ($a_kriteria[$i] == 'unithomebase'){
							$nama_tabel	= $nama_tabel .''.mPegawai::cekjoin($cek['unithomebase'],$a_kriteria[$i]);	
							$cek['unithomebase'] = true;
							$rsInfo = $conn->GetRow("select infoleft, inforight from ".static::table('ms_unit')." where idunit='$paramkriteria[$j]'");
						}
							
						if(count($paramkriteria)==1){
							if ($a_kriteria[$i] == 'unitkerja')
								$where1 =" (u.infoleft >= '".$rsInfo['infoleft']."' and u.inforight <='".$rsInfo['inforight']."') ";
							else if ($a_kriteria[$i] == 'unithomebase')
								$where1 =" (uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."') ";
							else if ($a_kriteria[$i] == 'noserdos'){
								if($paramkriteria[$j] == 'Y')
									$where1 = " is null";
								else
									$where1 = " is not null";
							}else if ($a_kriteria[$i] == 'sesuaibidang'){
								if($paramkriteria[$j] == 'Y')
									$where1 = " = 'Y'";
								else
									$where1 = " is null";
							}else
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
							else if ($a_kriteria[$i] == 'unithomebase'){
								if($j==0){
									$where1 =" ((uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."') ";
								}
								elseif($j==count($paramkriteria)-1){
									$where1 =" or (uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."')) ";
								}
								else{
									$where1 =" or (uh.infoleft >= '".$rsInfo['infoleft']."' and uh.inforight <='".$rsInfo['inforight']."') ";
								}
							}else if ($a_kriteria[$i] == 'noserdos'){
								if($j>0){
									$where1 =" and ".$terjemah_kriteria[$a_kriteria[$i]];
								}
								
								if($paramkriteria[$j] == 'Y')
									$where1 .= " is null";
								else
									$where1 .= " is not null";								
							}else if ($a_kriteria[$i] == 'sesuaibidang'){
								if($j>0){
									$where1 =" and ".$terjemah_kriteria[$a_kriteria[$i]];
								}
								
								if($paramkriteria[$j] == 'Y')
									$where1 .= " = 'Y'";
								else
									$where1 .= " is null";								
							}else{			
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
					
					if ($a_kriteria[$i] != 'unitkerja' and $a_kriteria[$i] != 'unithomebase')
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
			$sql = "select $nama_kolom from ".static::schema()."v_pegawairep v $nama_tabel";
				
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
			else if($cek == false && $urut == 'unithomebase'){
				return " left join ".static::schema()."ms_unit uh on uh.idunit = v.idunitbase";	
			}
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
			$sql = "select idjenispegawai, tipepeg + ' - ' + jenispegawai from ".static::table('ms_jenispeg')." j
					left join ".static::table('ms_tipepeg')." t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";
			
			return Query::arrQuery($conn, $sql);
		}

		function namaAnak($conn){
			$sql = "select idpegawai,anakke,namaanak from ".static::table('pe_anak')." where isvalid='Y' order by idpegawai,anakke";
			$rs = $conn->Execute($sql);
			
			$a_anak = array();
			while ($row = $rs->FetchRow()){
				$a_anak[$row['idpegawai']][$row['anakke']] = $row['namaanak'];
			}

			$sqlp = "select idpegawai from ".static::table('pe_anak')." where isvalid='Y' group by idpegawai";
			$rsp = $conn->Execute($sqlp);
			
			$a_pegawai = array();
			while ($rowp = $rsp->FetchRow()){
				$a_pegawai[] = $rowp['idpegawai'];
			}

			$a_data = array();
			foreach ($a_pegawai as $valuepegawai) {
				foreach ($a_anak[$valuepegawai] as $keyanak => $valueanak) {
					if($keyanak > 1) $a_data[$valuepegawai] .="<br>";  
					
					$a_data[$valuepegawai] .= $keyanak.". ".$valueanak;
				}
			}
			
			return $a_data;
		}

		function tgllahirAnak($conn){
			$sql = "select idpegawai,anakke,tgllahir from ".static::table('pe_anak')." where isvalid='Y' order by idpegawai,anakke";
			$rs = $conn->Execute($sql);
			
			$a_anak = array();
			while ($row = $rs->FetchRow()){
				$a_anak[$row['idpegawai']][$row['anakke']] = $row['tgllahir'];
			}

			$sqlp = "select idpegawai from ".static::table('pe_anak')." where isvalid='Y' group by idpegawai";
			$rsp = $conn->Execute($sqlp);
			
			$a_pegawai = array();
			while ($rowp = $rsp->FetchRow()){
				$a_pegawai[] = $rowp['idpegawai'];
			}

			$a_data = array();
			foreach ($a_pegawai as $valuepegawai) {
				foreach ($a_anak[$valuepegawai] as $keyanak => $valueanak) {
					if($keyanak > 1) $a_data[$valuepegawai] .="<br>";  
					
					$a_data[$valuepegawai] .= $keyanak.". ".Cstr::formatDateInd($valueanak,false);
				}
			}
			
			return $a_data;
		}
		function insertPegawai($conn,$data){
			$kolom = implode(',',array_keys($data));
			$valuesArrays = array();
			$i = 0;
			foreach($data as $key=>$values)
			{
				if(is_int($values))
				{
					$valuesArrays[$i] = $values;
				}else{
					$valuesArrays[$i]= "'".$values."'";
				}
				$i++;
			}
			$values = implode(',',$valuesArrays);
			$sql = "insert into sdm.ms_pegawai ($kolom) values($values)";
			$ok = $conn->Execute($sql);
			if($ok){
				return true;
			}else{
				return false;
			}
		}
		
		
	}
?>
