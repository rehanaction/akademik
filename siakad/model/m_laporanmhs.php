<?php
	// model laporan kelas
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mLaporanMhs {
		function getKHS($conn,$periode,$nim) {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.semestermhs, m.kodeunit, u.namaunit, m.ipk, m.skslulus, r.namaunit as fakultas,
					m.periodemasuk, pr.kode_jenjang_studi as programpend, m.nipdosenwali, akademik.f_namalengkap(w.gelardepan,w.namadepan, w.namatengah, w.namabelakang,w.gelarbelakang) as dosenwali,
					u.ketua as nipketua, akademik.f_namalengkap(k.gelardepan,k.namadepan,k.namatengah,k.namabelakang,k.gelarbelakang) as ketua,
					coalesce(p.ips,0) as ips, coalesce(p.skssem,0) as skssem, coalesce(p.jumlahsks,0) as jumlahsks,
					m.batassks
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai w on m.nipdosenwali = w.nik or m.nipdosenwali = w.idpegawai::text
					left join sdm.ms_pegawai k on k.nik = u.ketua or k.idpegawai::text = u.ketua
					left join akademik.ak_perwalian p on p.nim = m.nim and p.periode = '$periode'
					where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);
			
			// data khs
			$sql = "select k.kodemk, m.namamk, m.kodejenis, k.kelasmk, m.sks, k.nhuruf, k.nangka, k.lulus, k.nilaimasuk,k.dipakai
					from akademik.ak_krs k
					left join akademik.ak_matakuliah m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk
					where k.nim = '$nim' and k.periode = '$periode' order by k.kodemk";
			$rs = $conn->Execute($sql);
			
			$a_khs = array();
			while($row = $rs->FetchRow())
				$a_khs[] = $row;
			
			$a_data['khs'] = $a_khs;
			
			//data parameter nilai
			$sql ="select k.kodemk, m.namamk,k.kelasmk,u.idunsurnilai,u.namaunsurnilai,u.prosentasenilai,un.nilaiunsur,k.nnumerik
					from akademik.ak_unsurpenilaian u 
					join akademik.ak_krs k using(periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					join akademik.ak_unsurnilaikelas un using (periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, idunsurnilai)
					left join akademik.ak_kurikulum m using (thnkurikulum, kodeunit, kodemk)
					where k.nim = '$nim' and k.periode = '$periode' order by k.kodemk";
			$rsp=$conn->Execute($sql);
			$a_unsur = array();
			while($row = $rsp->FetchRow()){
				$idx=$row['namamk'].'('.$row['kodemk'].')';
				$a_unsur[$idx][$row['idunsurnilai']] = $row;
			}
			
			$a_data['unsur'] = $a_unsur;
			
			return array($a_data);
		}

		function cekInputanNilai($conn,$periode,$kodemk,$nim)
		{
			$sql="select count(nilaiunsur) as nilai FROM akademik.ak_unsurnilaikelas WHERE periode='$periode' AND kodemk='$kodemk' AND nim='$nim' and nilaiunsur=0";
			return $conn->GetOne($sql);
		}
		function cekInputanNilaiLengkap($conn,$periode,$kodemk,$nim)
		{
			$sql="select count(nilaiunsur) as nilai FROM akademik.ak_unsurnilaikelas WHERE periode='$periode' AND kodemk='$kodemk' AND nim='$nim'";
			return $conn->GetOne($sql);
		}

		function getValidateInputNilai($conn,$nim,$periode){
			$sql = "select count(kodemk) AS jmlmk,(select count(dipakai) 
			from akademik.ak_krs sk where sk.nim=k.nim and sk.periode=k.periode and sk.dipakai=-1) as dipakai,
			(select count(dipakai) from akademik.ak_krs sk 
			where sk.nim=k.nim and sk.periode=k.periode and sk.nilaimasuk=-1) as nmasuk  
			FROM akademik.ak_krs k WHERE periode = '$periode' AND nim = '$nim' group by k.nim,k.periode";
	
			return $conn->GetRow($sql);
		}
		
		function getKHSUnit($conn,$periode,$kodeunit='',$angkatan='') {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.semestermhs, m.kodeunit, u.namaunit, m.ipk, m.skslulus, r.namaunit as fakultas,
					m.periodemasuk, pr.kode_jenjang_studi as programpend, w.nik as nikdosen, akademik.f_namalengkap(w.gelardepan,w.namadepan, w.namatengah, w.namabelakang,w.gelarbelakang) as dosenwali,
					u.ketua as nipketua, akademik.f_namalengkap(k.gelardepan,k.namadepan , k.namatengah, k.namabelakang,k.gelarbelakang) as ketua,
					coalesce(p.ips,0) as ips, coalesce(p.skssem,0) as skssem, coalesce(p.jumlahsks,0) as jumlahsks,
					p.batassks
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai w on m.nipdosenwali = w.nik or m.nipdosenwali = w.idpegawai::text
					left join sdm.ms_pegawai k on k.nik = u.ketua or k.idpegawai::text = u.ketua
					left join akademik.ak_perwalian p on p.nim = m.nim and p.periode = '$periode'
					where m.kodeunit = '$kodeunit' and substr(m.periodemasuk,1,4) = '$angkatan'
					order by m.nim";
			$a_data = $conn->GetArray($sql);
			
			// data khs
			$sql = "select k.nim, k.kodemk, m.namamk, m.kodejenis, k.kelasmk, m.sks, k.nhuruf, k.nangka, k.lulus, k.nilaimasuk
					from akademik.ak_krs k
					join akademik.ms_mahasiswa h on k.nim = h.nim
					left join akademik.ak_matakuliah m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk
					where h.kodeunit = '$kodeunit' and substr(h.periodemasuk,1,4) = '$angkatan' and k.periode = '$periode'
					order by k.nim, k.kodemk";
			$rs = $conn->Execute($sql);
			
			$i = 0;
			$t_data = $a_data[$i];
			while($row = $rs->FetchRow()) {
				if($row['nim'] != $t_nim) {
					$a_khs = array();
					$t_nim = $row['nim'];
					
					while(!empty($t_data) and $t_data['nim'] != $t_nim)
						$t_data = $a_data[++$i];
				}
				
				$a_khs[] = $row;
				
				if($rs->EOF or $rs->fields['nim'] != $row['nim'])
					$a_data[$i]['khs'] = $a_khs;
			}
			
			//data parameter nilai
			$sql ="select k.nim,k.kodemk, m.namamk,k.kelasmk,u.idunsurnilai,u.namaunsurnilai,u.prosentasenilai,un.nilaiunsur,k.nnumerik
					from akademik.ak_unsurpenilaian u 
					join akademik.ak_krs k using(periode, thnkurikulum, kodeunit, kodemk, kelasmk)
					join akademik.ak_unsurnilaikelas un using (periode, thnkurikulum, kodeunit, kodemk, kelasmk, nim, idunsurnilai)
					left join akademik.ak_kurikulum m using (thnkurikulum, kodeunit, kodemk)
					join akademik.ms_mahasiswa h on k.nim = h.nim
					where h.kodeunit = '$kodeunit' and substr(h.periodemasuk,1,4) = '$angkatan' and k.periode = '$periode'
					order by k.nim, k.kodemk";
			$rsp=$conn->Execute($sql);
			
			$a_unsur = array();
			$i = 0;
			$t_data = $a_data[$i];
			while($row = $rsp->FetchRow()) {
				if($row['nim'] != $t_nim) {
					$a_khs = array();
					$t_nim = $row['nim'];
					
					while(!empty($t_data) and $t_data['nim'] != $t_nim)
						$t_data = $a_data[++$i];
				}
				
				$idx=$row['namamk'].'('.$row['kodemk'].')';
				$a_unsur[$idx][$row['idunsurnilai']] = $row;
				
				if($rs->EOF or $rs->fields['nim'] != $row['nim'])
					$a_data[$i]['unsur'] = $a_unsur;
			}
			
			
			
			return $a_data;
		}
		
		function getNilai($conn,$nim) {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.sex, m.periodemasuk, m.semestermhs, u.namaunit, p.namaunit as fakultas
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);
			
			// data nilai
			$sql = "select t.kodemk, t.namamk, t.sks, t.nhuruf, t.nangka
					from akademik.ak_transkrip t
					where t.nim = '$nim' order by t.namamk";
			$rs = $conn->Execute($sql);
			
			$a_nilai = array();
			while($row = $rs->FetchRow())
				$a_nilai[] = $row;
			
			$a_data['nilai'] = $a_nilai;
			
			return array($a_data);
		}
		
		function getNilaiUnit($conn,$kodeunit='',$angkatan='') {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.sex, m.periodemasuk, m.semestermhs, u.namaunit, p.namaunit as fakultas
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					where m.kodeunit = '$kodeunit' and substr(m.periodemasuk,1,4) = '$angkatan'
					order by m.nim";
			$a_data = $conn->GetRow($sql);
			
			// data nilai
			$sql = "select t.nim, t.kodemk, t.namamk, t.sks, t.nhuruf, t.nangka
					from akademik.ak_transkrip t
					join akademik.ms_mahasiswa m on t.nim = m.nim
					where m.kodeunit = '$kodeunit' and substr(m.periodemasuk,1,4) = '$angkatan'
					order by t.nim, t.namamk";
			$rs = $conn->Execute($sql);
			
			$i = 0;
			$t_data = $a_data[$i];
			while($row = $rs->FetchRow()) {
				if($row['nim'] != $t_nim) {
					$a_nilai = array();
					$t_nim = $row['nim'];
					
					while(!empty($t_data) and $t_data['nim'] != $t_nim)
						$t_data = $a_data[++$i];
				}
				
				$a_nilai[] = $row;
				
				if($rs->EOF or $rs->fields['nim'] != $row['nim'])
					$a_data[$i]['nilai'] = $a_nilai;
			}
			
			return $a_data;
		}
		
		function getTranskripSementara($conn,$periode,$nim) {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, m.tmplahir, m.tgllahir, p.namaunit as fakultas
					, coalesce(m.alamat,'') ||
						coalesce(' '||'RT '||lpad(m.rt::varchar,3,'0'),'')||
						coalesce(' '||'RW '||lpad(m.rw::varchar,3,'0'),'')||
						coalesce(' '||'Kode Pos '||m.kodepos,'')||
						coalesce(' '||'Kelurahan '||m.kelurahan,'')||
						coalesce(' '||'Kecamatan '||m.kecamatan,'')||
						coalesce(' '||kota.namakota,'')||
						coalesce(' '||'Propinsi '||prop.namapropinsi,'') AS alamat,
					u.ketua as nipketua, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as ketua,
					p.pembantu1 as nippdakad, akademik.f_namalengkap(d.gelardepan,d.namadepan, d.namatengah, d.namabelakang,d.gelarbelakang) as pdakad, k.nidn as nidnketua,
					p.pembantu1 as nippdakad, akademik.f_namalengkap(d.gelardepan,d.namadepan, d.namatengah, d.namabelakang,d.gelarbelakang) as pdakad,
					substring(m.periodemasuk,1,4) AS tahunmasuk, semestermhs, skslulus, ipk, e.namapredikat,
					(select max(periode) AS periode from akademik.ak_krs where nim='$nim') AS semesterakhir
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai k on k.nik = u.ketua or k.idpegawai::text = u.ketua
					left join sdm.ms_pegawai d on d.nik = p.pembantu1 or d.idpegawai::text = p.pembantu1
					left join akademik.ak_predikat e on e.programpend = pr.kode_jenjang_studi and e.thnkurikulum = akademik.f_kurikulumperiode(m.periodemasuk)
						and m.ipk <= e.ipkatas and m.ipk >= e.ipkbawah and m.semestermhs <= e.bataswaktu
					left join akademik.ms_kota kota on m.kodekota = kota.kodekota
					left join akademik.ms_propinsi prop on kota.kodepropinsi = prop.kodepropinsi
					where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);
			

			// data transkrip
			$sql = "select t.thnkurikulum, t.periode, t.kodemk, t.namamk, t.kodejenis, t.nhuruf, t.nangka, t.sks, kr.semmk as semester
					from akademik.ak_transkrip t 
					left join akademik.ak_kurikulum kr on t.kodeunit=kr.kodeunit and t.kodemk=kr.kodemk and t.thnkurikulum=kr.thnkurikulum 
					where t.nim = '$nim' order by semester asc";
			$rs = $conn->Execute($sql);
			
			$a_alltrans = array();
			while($row = $rs->FetchRow()) {
				if(!isset($t_kodejenis) or $t_kodejenis != $row['kodejenis']) {
					//$a_alltrans[] = $row['namajenis'];
					//$t_kodejenis = $row['kodejenis'];
				}
				
				$a_alltrans[] = $row;
			}
			
			$n = count($a_alltrans);
			$m = ceil($n/2);
			
			$a_transkrip = array(array(),array());
			for($i=0;$i<$n;$i++) {
				if($i < $m)
					$j = 0;
				else
					$j = 1;
				
				$a_transkrip[$j][] = $a_alltrans[$i];
			}
			
			$a_data['transkrip'] = $a_transkrip;
			
			return array($a_data);
		}

		function cekPeminatan($conn, $nim){
			$sql = "select kodemk from akademik.ak_transkrip where nim = '$nim'";
			$rsj = $conn->Execute($sql);

			return array($rsj);
		}

		function getDataFeeder($conn,$unit,$periodemasuk){
			$sql = "select * from akademik.v_feedermhs where kodeunit='$unit' and periodemasuk='$periodemasuk'";
			return $conn->GetArray($sql);
		}
		function getDataKelasMkFeeder($conn,$unit,$periodemasuk){
			$sql = "select * from akademik.v_feederkelasmk where kodeunit='$unit' and periode='$periodemasuk'";
			return $conn->GetArray($sql);
		}
		function getDataAjarDosenFeeder($conn,$unit,$periode){
			$sql = "select * from akademik.v_feederajardosen where kodeunit='$unit' and periode='$periode'";
			return $conn->GetArray($sql);
		}
		function getDataKrsFeeder($conn,$unit,$periode){
			$sql = "select * from akademik.v_feederkrs where kodeunit='$unit' and periode='$periode'";
			return $conn->GetArray($sql);
		}
		function getDataAktivitasMhsFeeder($conn,$periode,$kodeunit){
			$sql = "select * from akademik.v_feederaktivitasmhs where periode='$periode' and kodeunit='$kodeunit' order by statusmhs asc";
			return $conn->GetArray($sql);
		}
		function getDataKonversi($conn,$periode,$kodeunit){
			$sql = "select * from akademik.v_konversifeeder where periodemasuk='$periode' and kodeunit='$kodeunit' order by nim asc";
			return $conn->GetArray($sql);
		}
		
		function getDataMatakuliahFeeder($conn,$unit,$thn){
			$sql = "select * from akademik.v_feedermatakuliah where kodeunit='$unit' and thnkurikulum='$thn'";
			return $conn->GetArray($sql);
		}




		function getTranskripSementaraAdji($conn,$periode,$nim) {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, m.tmplahir, m.tgllahir, p.namaunit as fakultas,
					u.ketua as nipketua, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as ketua,
					p.pembantu1 as nippdakad, akademik.f_namalengkap(d.gelardepan,d.namadepan, d.namatengah, d.namabelakang,d.gelarbelakang) as pdakad
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai k on k.nik = u.ketua or k.idpegawai::text = u.ketua
					left join sdm.ms_pegawai d on d.nik = p.pembantu1 or d.idpegawai::text = p.pembantu1
					where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);
			
			// data transkrip
			$sql = "select t.thnkurikulum, t.periode, t.kodemk, t.namamk, t.kodejenis, t.nhuruf, t.nangka, t.sks, kr.semmk as semester
from akademik.ak_transkrip t left join akademik.ak_kurikulum kr on t.kodeunit=kr.kodeunit and t.kodemk=kr.kodemk and t.thnkurikulum=kr.thnkurikulum 
					where t.nim = '$nim' order by semester asc";
			$rs = $conn->Execute($sql);
			
			$a_alltrans = array();
			while($row = $rs->FetchRow()) {
				if(!isset($t_kodejenis) or $t_kodejenis != $row['kodejenis']) {
					$a_alltrans[] = $row['namajenis'];
					$t_kodejenis = $row['kodejenis'];
				}
				
				$a_alltrans[] = $row;
			}
			
			$n = count($a_alltrans);
			$m = ceil($n/2);
			
			$a_transkrip = array(array(),array());
			for($i=0;$i<$n;$i++) {
				if($i < $m)
					$j = 0;
				else
					$j = 1;
				
				$a_transkrip[$j][] = $a_alltrans[$i];
			}
			
			$a_data['transkrip'] = $a_transkrip;
			
			return array($a_data);
		}

		
		function getResumeSementara($conn,$periode,$nim) {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, m.tmplahir, m.tgllahir, p.namaunit as fakultas
					, coalesce(m.alamat,'') ||
						coalesce(' '||'RT '||lpad(m.rt::varchar,3,'0'),'')||
						coalesce(' '||'RW '||lpad(m.rw::varchar,3,'0'),'')||
						coalesce(' '||'Kode Pos '||m.kodepos,'')||
						coalesce(' '||'Kelurahan '||m.kelurahan,'')||
						coalesce(' '||'Kecamatan '||m.kecamatan,'')||
						coalesce(' '||kota.namakota,'')||
						coalesce(' '||'Propinsi '||prop.namapropinsi,'') AS alamat,
					u.ketua as nipketua, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as ketua,
					p.pembantu1 as nippdakad, akademik.f_namalengkap(d.gelardepan,d.namadepan, d.namatengah, d.namabelakang,d.gelarbelakang) as pdakad, k.nidn as nidnketua,
					substring(m.periodemasuk,1,4) AS tahunmasuk, semestermhs, skslulus, ipk,
					(select max(periode) AS periode from akademik.ak_krs where nim='$nim') AS semesterakhir
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai k on k.nik = u.ketua or k.idpegawai::text = u.ketua
					left join sdm.ms_pegawai d on d.nik = p.pembantu1 or d.idpegawai::text = p.pembantu1
					
					left join akademik.ms_kota kota on m.kodekota = kota.kodekota
					left join akademik.ms_propinsi prop on kota.kodepropinsi = prop.kodepropinsi
					where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);
			
			// data transkrip
			$sql = "select t.thnkurikulum, t.periode, t.kodemk, t.namamk, t.kodejenis, t.nhuruf, t.nangka, t.sks
					from akademik.ak_transkrip t
					where t.nim = '$nim' order by t.thnkurikulum, t.periode desc, t.kodemk";
			$rs = $conn->Execute($sql);
			
			$a_alltrans = array();
			while($row = $rs->FetchRow()) {
				if(!isset($t_kodejenis) or $t_kodejenis != $row['kodejenis']) {
					$a_alltrans[] = $row['namajenis'];
					$t_kodejenis = $row['kodejenis'];
				}
				
				$a_alltrans[] = $row;
			}
			
			$n = count($a_alltrans);
			$m = ceil($n/2);
			
			$a_transkrip = array(array(),array());
			for($i=0;$i<$n;$i++) {
				if($i < $m)
					$j = 0;
				else
					$j = 1;
				
				$a_transkrip[$j][] = $a_alltrans[$i];
			}
			
			$a_data['transkrip'] = $a_transkrip;
			
			return array($a_data);
		}

		
		function getTranskripSementaraUnit($conn,$periode,$kodeunit='',$angkatan='') {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, m.tmplahir, m.tgllahir, p.namaunit as fakultas
					, coalesce(m.alamat,'') ||
						coalesce(' '||'RT '||lpad(m.rt::varchar,3,'0'),'')||
						coalesce(' '||'RW '||lpad(m.rw::varchar,3,'0'),'')||
						coalesce(' '||'Kode Pos '||m.kodepos,'')||
						coalesce(' '||'Kelurahan '||m.kelurahan,'')||
						coalesce(' '||'Kecamatan '||m.kecamatan,'')||
						coalesce(' '||kota.namakota,'')||
						coalesce(' '||'Propinsi '||prop.namapropinsi,'') AS alamat,
					u.ketua as nipketua, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as ketua,
					p.pembantu1 as nippdakad, akademik.f_namalengkap(d.gelardepan,d.namadepan, d.namatengah, d.namabelakang,d.gelarbelakang) as pdakad,
					substring(m.periodemasuk,1,4) AS tahunmasuk, semestermhs, skslulus, ipk, e.namapredikat,
					(select max(periode) AS periode from akademik.ak_krs where nim='$nim') AS semesterakhir
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai k on k.nik = u.ketua or k.idpegawai::text = u.ketua
					left join sdm.ms_pegawai d on d.nik = p.pembantu1 or d.idpegawai::text = p.pembantu1
					left join akademik.ak_predikat e on e.programpend = pr.kode_jenjang_studi and e.thnkurikulum = akademik.f_kurikulumperiode(m.periodemasuk)
						and m.ipk <= e.ipkatas and m.ipk >= e.ipkbawah and m.semestermhs <= e.bataswaktu
					left join akademik.ms_kota kota on m.kodekota = kota.kodekota
					left join akademik.ms_propinsi prop on kota.kodepropinsi = prop.kodepropinsi
					where m.nim = '$nim'";
			$a_data = $conn->GetArray($sql);
			
			// data transkrip
			$sql = "select t.thnkurikulum, t.periode, t.kodemk, t.namamk, t.kodejenis, t.nhuruf, t.nangka, t.sks, kr.semmk as semester
from akademik.ak_transkrip t join akademik.ak_kurikulum kr on t.kodeunit=kr.kodeunit and t.kodemk=kr.kodemk and t.thnkurikulum=kr.thnkurikulum 
					where t.nim = '$nim' order by semester asc";
			$rs = $conn->Execute($sql);
			
			$i = 0;
			$t_data = $a_data[$i];
			while($row = $rs->FetchRow()) {
				if($row['nim'] != $t_nim) {
					$a_alltrans = array();
					$t_nim = $row['nim'];
					unset($t_kodejenis);
					
					while(!empty($t_data) and $t_data['nim'] != $t_nim)
						$t_data = $a_data[++$i];
				}
				
				if(!isset($t_kodejenis) or $t_kodejenis != $row['kodejenis']) {
					$a_alltrans[] = $row['namajenis'];
					$t_kodejenis = $row['kodejenis'];
				}
				
				$a_alltrans[] = $row;
				
				if($rs->EOF or $rs->fields['nim'] != $row['nim']) {
					$n = count($a_alltrans);
					$m = ceil($n/2);
					
					$a_transkrip = array(array(),array());
					for($i=0;$i<$n;$i++) {
						if($i < $m)
							$j = 0;
						else
							$j = 1;
						
						$a_transkrip[$j][] = $a_alltrans[$i];
					}
					
					$a_data[$i]['transkrip'] = $a_transkrip;
				}
			}
			
			return $a_data;
		}
		
		function getTranskrip($conn,$periode,$nim) {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, r.namaprogram, p.namaunit as fakultas,
					u.keterangan, y.noijasah, y.notranskrip, coalesce(y.tgltranskrip,i.tglyudisium) as tgltranskrip,
					t.judulta, m.ipk, m.skslulus,
					p.ketua as nipdekan, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as dekan,
					p.pembantu1 as nippdakad, akademik.f_namalengkap(d.gelardepan,d.namadepan, d.namatengah, d.namabelakang,d.gelarbelakang) as pdakad,
					e.namapredikat
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join akademik.ms_programpend r on pr.kode_jenjang_studi = r.programpend
					left join sdm.ms_pegawai k on k.nik = p.ketua or k.idpegawai::text = p.ketua
					left join sdm.ms_pegawai d on d.nik = p.pembantu1 or d.idpegawai::text = p.pembantu1
					left join akademik.ak_yudisium y on m.nim = y.nim and y.noijasah is not null
					left join akademik.ak_periodeyudisium i on y.idyudisium = i.idyudisium
					left join akademik.ak_ta t on m.nim = t.nim and t.statusta <> 'T'
					left join akademik.ak_predikat e on e.programpend = pr.kode_jenjang_studi and e.thnkurikulum = akademik.f_kurikulumperiode(m.periodemasuk)
						and m.ipk <= e.ipkatas and m.ipk >= e.ipkbawah and m.semestermhs <= e.bataswaktu
					where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);
			
			// data transkrip
			$sql = "select t.kodemk, t.namamk, t.kodejenis, j.namajenis, t.nhuruf, t.nangka, t.sks
					from akademik.ak_transkrip t
					left join akademik.lv_jenismk j on t.kodejenis = j.kodejenis
					where t.nim = '$nim' order by t.kodejenis, t.kodemk";
			$rs = $conn->Execute($sql);
			
			$a_alltrans = array();
			while($row = $rs->FetchRow()) {
				if(!isset($t_kodejenis) or $t_kodejenis != $row['kodejenis']) {
					$a_alltrans[] = $row['namajenis'];
					$t_kodejenis = $row['kodejenis'];
				}
				
				$a_alltrans[] = $row;
			}
			
			$n = count($a_alltrans);
			$m = ceil($n/2);
			
			$a_transkrip = array(array(),array());
			for($i=0;$i<$n;$i++) {
				if($i < $m)
					$j = 0;
				else
					$j = 1;
				
				$a_transkrip[$j][] = $a_alltrans[$i];
			}
			
			$a_data['transkrip'] = $a_transkrip;
			
			return array($a_data);
		}
	//Get Data prodi by mata kuliah seminar & jurusan
	function getProdi($conn,$nim)
	{
		$sql = "select t.namamk	from akademik.ak_transkrip t where 
				t.nim = '$nim' and 
				substring(t.namamk from 1 for 7)='SEMINAR' and 
				t.namamk!='SEMINAR USULAN PENELITIAN' 
				order by t.periode asc nulls first, t.kodemk";
		
		$prodi = $conn->GetOne($sql);
		$a_prodi = explode(' ',$prodi);
		$cekak = array_search("AKUNTANSI",$a_prodi);
		if(empty($cekak)){
			if(count($a_prodi)==2){
				if($a_prodi[1]=='MSDM'){
					$a_prodi[1]='SDM';
				}
				$sql1 = "select ab.kodeunit,kode_jenjang_studi as jenjang,nama_program_studi as program,namabs as bidang 
						from akademik.ak_bidangstudi ab join akademik.ak_prodi ap on ap.kodeunit=ab.kodeunit 
						where lower(namabs) LIKE lower('%$a_prodi[1]%')";
			}else{
				$sql1 = "select ab.kodeunit,kode_jenjang_studi as jenjang,nama_program_studi as program,namabs as bidang 
						from akademik.ak_bidangstudi ab join akademik.ak_prodi ap on ap.kodeunit=ab.kodeunit 
						where lower(namabs) LIKE lower('%$a_prodi[2]%')";
			}
		}elseif(!empty($cekak)){

				$sql1 = "select ap.kodeunit,kode_jenjang_studi as jenjang,nama_program_studi as program,coalesce(namabs,'-') as bidang 
				from akademik.ak_prodi ap left join akademik.ak_bidangstudi ab on ab.kodeunit=ap.kodeunit where ap.kodeunit='4302162201' and lower(namabs) LIKE lower('%$a_prodi[2]%')";
		}else{
			$sql1 = "select ap.kodeunit,kode_jenjang_studi as jenjang,nama_program_studi as program,coalesce(namabs,'-') as bidang 
			from akademik.ak_prodi ap left join akademik.ak_bidangstudi ab on ab.kodeunit=ap.kodeunit where ap.kodeunit='4302161101'";
		}
			$a_data=$conn->GetRow($sql1);
			return $a_data;


	}
    
    
       
   	//transkrip untuk alumni yang bentuknya berbeda	
		function getTranskripLulus($conn,$periode,$nim) {
			// data mahasiswa
			// $sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, r.namaprogram, p.namaunit as fakultas,
			//		u.keterangan, y.noijasah, y.notranskrip, coalesce(y.tgltranskrip,i.tglyudisium) as tgltranskrip,
			//		t.judulta, m.ipk, m.skslulus, m.ptasal,
			//		p.namaketuasementara AS dekan, p.nipketuasementara AS nipdekan,
			//		e.namapredikat,
			//		COALESCE(m.tmplahir,'') AS tmplahir, m.tgllahir, m.tgllulus, m.thnkurikulum
			//	from akademik.ms_mahasiswa m
			//		left join gate.ms_unit u on m.kodeunit = u.kodeunit
			//		left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
			//		left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
			//		left join akademik.ms_programpend r on pr.kode_jenjang_studi = r.programpend
			//		left join akademik.ak_yudisium y on m.nim = y.nim and y.noijasah is not null
			//		left join akademik.ak_periodeyudisium i on y.idyudisium = i.idyudisium
			//		left join akademik.ak_ta t on m.nim = t.nim and t.statusta <> 'T'
			//		left join akademik.ak_predikat e on e.programpend = pr.kode_jenjang_studi and e.thnkurikulum = (select max(thnkurikulum) from akademik.ms_thnkurikulum) ". /* akademik.f_kurikulumperiode(m.periodemasuk) */ "
			//			and m.ipk <= e.ipkatas and m.ipk >= e.ipkbawah and m.semestermhs <= e.bataswaktu
			//		where m.nim = '$nim'";
			$sql = "select m.nim, m.nama, COALESCE(m.tmplahir,'') AS tmplahir, m.tgllahir, m.tgllulus, m.thnkurikulum, m.ipk,pw.ipklulus, m.skslulus, m.ptasal
				, m.kodeunit AS kodeunit, y.prodi_nama AS namaunit, y.programpend_kode AS programpend, y.programpend_nama AS namaprogram, y.fakultas_nama as fakultas
				, y.ijazah_no, y.skrektor_no, y.tgl_lulus as tgltranskrip
				,u.namaunit, m.periodemasuk
				,k.nidn as nidnketua
				, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as ketua
				,p.namaketuasementara AS dekan, p.nipketuasementara AS nipdekan
				, y.fakultas_ketua AS dekan, y.fakultas_nip AS nipdekan
				, t.judulta
				, e.namapredikat
				, ap.kode_jenjang_studi as program
			from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_perwalian pw on m.nim = pw.nim and pw.periode='$periode'
					left join akademik.ak_yudisium y on m.nim = y.nim and y.ijazah_no is not null
					left join akademik.ak_ta t on m.nim = t.nim and t.statusta <> 'T'
					left join akademik.ak_prodi ap on ap.kodeunit=m.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai k on k.nik = u.ketua or k.idpegawai::text = u.ketua
					left join akademik.ak_predikat e on e.programpend = coalesce(y.programpend_kode,ap.kode_jenjang_studi) and e.thnkurikulum = (select max(thnkurikulum) from akademik.ms_thnkurikulum)
						and coalesce(m.ipk,pw.ipklulus) <= e.ipkatas and coalesce(m.ipk,pw.ipklulus) >= e.ipkbawah
			where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);
			
			$sql = "select t.kodemk, t.namamk, t.kodejenis, t.nhuruf, t.nangka, t.sks, t.periode
					from akademik.ak_transkrip t
			  	where t.nim = '$nim' order by t.periode asc nulls first, t.kodemk ";     
          
			$rs = $conn->Execute($sql);
			
			$a_alltrans = array();
			while($row = $rs->FetchRow()) {
				if(!isset($t_kodejenis) or $t_kodejenis != $row['kodejenis']) {
					//$a_alltrans[] = $row['namajenis'];
					//$t_kodejenis = $row['kodejenis'];
				}
				
				$a_alltrans[] = $row;
			}
			
			$n = count($a_alltrans);
			$m = 40;// ceil($n/2);
			
			$a_transkrip = array(array(),array());
			$cetakptasal=true;$ptesa=true;
			for($i=0;$i<$n;$i++) {
				if($i < $m)
					$j = 0;
				else
					$j = 1;
				
				if ( ( empty($a_alltrans[$i]['periode']) || $a_alltrans[$i]['periode']=="00000" ) && $cetakptasal && !empty($a_data['ptasal']) ){
					$cetakptasal=false;
					$a_transkrip[$j][]=array("kodemk"=>"", "namamk"=>"Nilai Pindahan dari ".strtoupper($a_data['ptasal']), "kodejenis"=>"", "nhuruf"=>"", "nangka"=>"", "sks"=>"", "periode"=>"");
				}

				if ( ( !empty($a_alltrans[$i]['periode']) && $a_alltrans[$i]['periode']!="00000" ) && $ptesa && !empty($a_data['ptasal']) ){
					$ptesa=false;
					// $a_transkrip[$j][]=array("kodemk"=>"", "namamk"=>"", "kodejenis"=>"", "nhuruf"=>"", "nangka"=>"", "sks"=>"", "periode"=>"");
					$a_transkrip[$j][]=array("kodemk"=>"", "namamk"=>"Nilai dari STIE INABA", "kodejenis"=>"", "nhuruf"=>"", "nangka"=>"", "sks"=>"", "periode"=>"");
				}
				$a_transkrip[$j][] = $a_alltrans[$i];
			}
			$a_data['transkrip'] = $a_transkrip;
			
			return array($a_data);
		}
    
     
    
		function getTranskripUnit($conn,$periode,$kodeunit='',$angkatan='') {
			// data mahasiswa
			$sql = "select m.nim, m.nama, m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, r.namaprogram, p.namaunit as fakultas,
					u.keterangan, y.noijasah, y.notranskrip, coalesce(y.tgltranskrip,i.tglyudisium) as tgltranskrip,
					t.judulta, m.ipk,pw.ipklulus, m.skslulus,
					p.ketua as nipdekan, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as dekan,
					p.pembantu1 as nippdakad, akademik.f_namalengkap(d.gelardepan,d.namadepan, d.namatengah, d.namabelakang,d.gelarbelakang) as pdakad,
					e.namapredikat, m.periodemasuk
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_perwalian pw on m.nim = pw.nim and pw.periode='$periode'
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join akademik.ms_programpend r on pr.kode_jenjang_studi = r.programpend
					left join sdm.ms_pegawai k on k.nik = p.ketua or k.idpegawai::text = p.ketua
					left join sdm.ms_pegawai d on d.nik = p.pembantu1 or d.idpegawai::text = p.pembantu1
					left join akademik.ak_yudisium y on m.nim = y.nim and y.noijasah is not null
					left join akademik.ak_periodeyudisium i on y.idyudisium = i.idyudisium
					left join akademik.ak_ta t on m.nim = t.nim and t.statusta <> 'T'
					left join akademik.ak_predikat e on e.programpend = pr.kode_jenjang_studi and e.thnkurikulum = akademik.f_kurikulumperiode(m.periodemasuk)
						and coalesce(m.ipk,pw.ipklulus) <= e.ipkatas and coalesce(m.ipk,pw.ipklulus) >= e.ipkbawah and m.semestermhs <= e.bataswaktu
					where m.kodeunit = '$kodeunit' and substr(m.periodemasuk,1,4) = '$angkatan' and m.statusmhs = 'L'
					order by m.nim";
			$a_data = $conn->GetArray($sql);
			
			// data transkrip
			$sql = "select t.nim, t.kodemk, t.namamk, t.kodejenis, j.namajenis, t.nhuruf, t.nangka, t.sks
					from akademik.ak_transkrip t
					join akademik.ms_mahasiswa h on t.nim = h.nim
					left join akademik.lv_jenismk j on t.kodejenis = j.kodejenis
					where h.kodeunit = '$kodeunit' and substr(h.periodemasuk,1,4) = '$angkatan' and h.statusmhs = 'L'
					order by t.nim, t.kodejenis, t.kodemk";
			$rs = $conn->Execute($sql);
			
			$i = 0;
			$t_data = $a_data[$i];
			while($row = $rs->FetchRow()) {
				if($row['nim'] != $t_nim) {
					$a_alltrans = array();
					$t_nim = $row['nim'];
					unset($t_kodejenis);
					
					while(!empty($t_data) and $t_data['nim'] != $t_nim)
						$t_data = $a_data[++$i];
				}
				
				if(!isset($t_kodejenis) or $t_kodejenis != $row['kodejenis']) {
					$a_alltrans[] = $row['namajenis'];
					$t_kodejenis = $row['kodejenis'];
				}
				
				$a_alltrans[] = $row;
				
				if($rs->EOF or $rs->fields['nim'] != $row['nim']) {
					$n = count($a_alltrans);
					$m = ceil($n/2);
					
					$a_transkrip = array(array(),array());
					for($k=0;$k<$n;$k++) {
						if($k < $m)
							$j = 0;
						else
							$j = 1;
						
						$a_transkrip[$j][] = $a_alltrans[$k];
					}
					
					$a_data[$i]['transkrip'] = $a_transkrip;
				}
			}
			
			return $a_data;
		}
		function getDataMhs($conn,$nim){
			$sql="select m.nim,m.nama,m.tmplahir,m.tgllahir,m.rt,m.rw,m.kelurahan,m.kecamatan,m.alamat,m.kodekota,m.ipk,coalesce(m.telp,m.telp2) as telephon,coalesce(m.hp,m.hp2) as hp,m.sex,coalesce(m.namaayah,m.namaibu) as namaortu,m.semestermhs,m.namaperusahaan,m.alamatperusahaan,m.keterangan,
			j.namaunit as jurusan,f.namaunit as fakultas,
			a.namaagama,
			k.namakota,
			y.tglskyudisium,
			pr.kode_jenjang_studi as jenjang,
			akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as kaprodi,
			ta.judulta,
			py.idyudisium,py.tglyudisium
			from akademik.ms_mahasiswa m
			join gate.ms_unit j using (kodeunit)
			left join sdm.ms_pegawai p on p.nik=j.ketua or p.idpegawai::text=j.ketua
			join gate.ms_unit f on j.kodeunitparent=f.kodeunit
			left join akademik.ak_prodi pr on m.kodeunit=pr.kodeunit
			left join akademik.lv_agama a using (kodeagama)
			left join akademik.ms_kota k on m.kodekota=k.kodekota
			left join akademik.ak_yudisium y using (nim) 
			left join akademik.ak_periodeyudisium py on y.idyudisium=py.idyudisium
			left join akademik.ak_ta ta on m.nim=ta.nim
			where m.nim='$nim'";
			return $conn->GetRow($sql);
		}

		function getRekapMhsStatus($conn,$periode,$statusmhs,$fakultas){
			 $sql="select uf.kodeunit as fak,uf.namaunit as fakultas,uj.kodeunit as jur,uj.namaunit as jurusan,substr(m.periodemasuk,1,4) as angkatan,
				m.sistemkuliah,s.namasistem||' - '||s.tipeprogram as sistem,sum(1) as jumlah
				from akademik.ak_perwalian p 
				join akademik.ms_mahasiswa m on m.nim = p.nim 
				join gate.ms_unit uj on m.kodeunit=uj.kodeunit
				join gate.ms_unit uf on uf.kodeunit=uj.kodeunitparent
				join akademik.ak_sistem s on s.sistemkuliah=m.sistemkuliah
				where p.periode = '$periode' 
				and p.statusmhs='$statusmhs'";
			//if($fakultas!=0){
			//	$sql.=" and uj.kodeunitparent='$fakultas'";
			//}
			//if($statusmhs=='A'){
			//	$sql.=" and p.frsterisi<>0";
			//}
			$sql.=" group by uf.kodeunit,uf.namaunit,uj.kodeunit,uj.namaunit,angkatan,m.sistemkuliah,sistem
					order by uf.kodeunit,uj.kodeunit,angkatan,m.sistemkuliah";

			return $conn->GetArray($sql);
		}

		function getIjazahMhs($conn,$statusmhs,$kodeunit,$nim,$periode){
			// $sql="select uf.kodeunit as fak,uf.namaunit as fakultas,uj.kodeunit as jur,uj.namaunit as jurusan,substr(m.periodemasuk,1,4) as angkatan,
			//		m.sistemkuliah,s.namasistem||' - '||s.tipeprogram as sistem,m.nama,m.nim,m.tgllahir,uj.namaunit,y.tglskyudisium as tgllolos,
			//		m.tgllulus,m.tmplahir,m.notranskrip,m.noijasah,pr.gelar,pr.deskgelar
			//	from akademik.ak_perwalian p 
			//	join akademik.ms_mahasiswa m on m.nim = p.nim 
			//	join gate.ms_unit uj on m.kodeunit=uj.kodeunit
			//	join gate.ms_unit uf on uf.kodeunit=uj.kodeunitparent
			//	join akademik.ak_prodi pr on pr.kodeunit = uj.kodeunit
			//	join akademik.ak_sistem s on s.sistemkuliah=m.sistemkuliah
			//	join akademik.ak_yudisium y on y.nim = m.nim
			//	and  m.kodeunit = '$kodeunit' ";
			
			$sql="SELECT yd.fakultas_kode AS fak, yd.fakultas_nama AS fakultas, yd.fakultas_ketua AS namadekan, yd.prodi_kode AS jur, yd.prodi_nama AS jurusan
						, yd.prodi_nama as namaunit
						, substr(m.periodemasuk,1,4) AS angkatan
						, m.sistemkuliah, s.namasistem ||' - '|| s.tipeprogram AS sistem
						, m.nim, lower(m.nama) AS nama, lower(m.tmplahir) AS tmplahir, m.tgllahir
						, yd.tgl_lulus
						, yd.skrektor_no
						, yd.ijazah_no, yd.ijazah_tgl, yd.ijazah_nopin
						, yd.gelar_kode AS gelar, yd.gelar_nama AS deskgelar
						, yd.skbanpt_no
					FROM akademik.ak_yudisium yd
						JOIN akademik.ms_mahasiswa m ON yd.nim=m.nim
						JOIN akademik.ak_sistem s ON s.sistemkuliah=m.sistemkuliah
					WHERE 1=1
					";
			// if( !empty($kodeunit) ){
			//	$sql .= " and m.kodeunit= '$kodeunit' ";
			// }
			
			if(!empty($nim) ){
				$sql .= " and m.nim = '$nim' ";
			}
			// $sql.="order by yd.fakultas_kode, yd.prodi_kode, angkatan, m.sistemkuliah limit 1 ";
			return $conn->GetArray($sql);
		}
		function getNilaiMhs($conn,$r_periode,$kodeunit){
			$unit=$conn->GetRow("select infoleft,inforight from gate.ms_unit where kodeunit='$kodeunit'");
			$sql="select k.kodemk, kr.semmk, kr.namamk,kr.sks, k.kelasmk, k.nim, m.nama, k.nnumerik, k.nangka, k.nhuruf, k.nilaimasuk, k.lulus, k.dipakai from akademik.ak_krs k 
					join akademik.ak_kurikulum kr on k.kodeunit=kr.kodeunit and k.kodemk=kr.kodemk and k.thnkurikulum=kr.thnkurikulum 
					join akademik.ms_mahasiswa m on k.nim = m.nim 
					join gate.ms_unit u on u.kodeunit=m.kodeunit 
					where 
					u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']."
					and k.periode = '$r_periode' 
					order by m.nama,kr.namamk,k.kelasmk";
			return $conn->GetArray($sql);
		}
		function getRekapKrs($conn,$kodeunit,$periode,$angkatan){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			
			$sql="select j.namaunit as jurusan,count(p.nim) as jum_mhs,count(case when frsterisi<>0 then p.nim end) as jum_krs,
					count(case when frsterisi=0 then p.nim end) as jum_blmkrs
				from akademik.ms_mahasiswa m 
				join akademik.ak_perwalian p on p.nim=m.nim and p.periode='$periode'
				join gate.ms_unit j on j.kodeunit=m.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight']."
				where substr(m.periodemasuk,1,4)='$angkatan'
				group by j.namaunit";
				
			return $conn->Execute($sql);
		}

		function ceklis($conn,$nim){
			
			
			$sql="update akademik.ms_mahasiswa set iscetakktm='1' where nim='".$nim."'";
				
			return $conn->Execute($sql);
		}

		function statusceklis($conn,$nim){
					
			$sql="select iscetakktm from akademik.ms_mahasiswa where nim='".$nim."'";
				
			return $conn->GetOne($sql);
		}
		
		function getKtm($conn,$kodeunit,$angkatan,$nim){
		$sql="select m.nim,m.nama,j.namaunit as jurusan,p.kode_jenjang_studi as jenjang,m.tglregistrasi,m.alamat,k.namakota from akademik.ms_mahasiswa m
			join akademik.ak_prodi p on m.kodeunit=p.kodeunit
			join gate.ms_unit j on m.kodeunit=j.kodeunit
			left join akademik.ms_kota k on k.kodekota = m.kodekota
			 where 1=1";
		if(!empty($nim))
			$sql.=" and m.nim='$nim'";
		else{
			require_once(Route::getModelPath('unit'));		
			$row = mUnit::getData($conn,$kodeunit);
			$sql.=" and j.infoleft >= ".(int)$row['infoleft']." and j.inforight <= ".(int)$row['inforight'].
				" and substr(m.periodemasuk,1,4)='$angkatan'";
		}
		$sql.=" order by m.nim";
		return $conn->GetArray($sql); 
	}
	//function getPredikatByIPK($conn, $arg1, $arg2, $arg3){
	//	$sql = "SELECT namapredikat FROM akademik.ak_predikat WHERE programpend='$arg1' AND thnkurikulum='$arg2' AND $arg3 BETWEEN ipkbawah AND ipkatas";
	//	$a_data = $conn->GetRow($sql);
	//	return $a_data; 
	//}
}
?>
