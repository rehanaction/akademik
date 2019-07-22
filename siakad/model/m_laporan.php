<?php
	// model laporan
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );

	class mLaporan {
		function getIPKProdi($conn,$jurusan,$semester,$tahun) {
			$sql = "SELECT *
			   FROM akademik.v_lapipk_per_prodiangkatansemester m
			   WHERE m.kodeunit = '$jurusan'
			   AND m.periode = '$tahun"."$semester'
			   Order by m.periode asc";

			return $conn->Execute($sql);
		}
		function getIPKTertinggi($conn,$jurusan,$angkatan,$semester,$tahun) {
			$sql = "SELECT *
			   FROM akademik.v_lapipk_per_mhs_prodiangkatansemester p
			   WHERE  p.kodeunit = '$jurusan'
			   AND p.angkatan = '$angkatan'
			   AND p.periode = '$tahun"."$semester'
			   Order by p.ipk desc";

			return $conn->SelectLimit($sql,10);
		}
		function getIPKRatarata($conn,$jurusan,$angkatan,$semester,$tahun) {
			$sql = "SELECT *
			   FROM akademik.v_lapipk_per_mhs_prodiangkatansemester a
			   WHERE  a.kodeunit = '$jurusan'
			   AND a.angkatan = '$angkatan'
			   AND a.periode = '$tahun"."$semester'
			   AND a.ipk >=3.00
			   Order by a.ipk desc";

			return $conn->Execute($sql);
		}
		function getIPKAngkatan($conn,$semester,$tahun) {
			$sql ="SELECT *
			   FROM akademik.v_lapipk_per_prodiangkatansemester m
			   WHERE m.periode = '$tahun"."$semester'
			   Order by m.kodeunit,m.angkatan asc";

			return $conn->Execute($sql);
		}
		function getIPKjalurPenerimaan($conn,$jurusan,$semester,$tahun) {
			$sql ="SELECT *
             FROM akademik.v_lapipk_per_jalur_prodiangkatansemester j
			 WHERE j.kodeunit = '$jurusan'
			 AND j.periode = '$tahun"."$semester'
			 Order by j.angkatan desc";

			return $conn->Execute($sql);
		}
		function getIPKjalurPenerimaansem($conn,$fakultas,$semester,$tahun) {
		$sql = "select * from gate.ms_unit where kodeunit = '$fakultas'";
		$ok = $conn->Execute($sql)->FetchRow();
		$infoleft = $ok ['infoleft'];
		$inforight = $ok ['inforight'];
        $sql1 = "select kodeunit from gate.ms_unit where infoleft >=$infoleft and inforight<=$inforight";
			$sql ="SELECT *
             FROM akademik.v_lapipk_per_jalur_prodiangkatansemester j
			 WHERE j.kodeunit in ($sql1)
			 AND j.periode = '$tahun"."$semester'
			 Order by j.kodeunit asc";

			return $conn->Execute($sql);
		}

		function getStatusmhs($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT *
		   FROM akademik.v_lapstatus_per_prodiangkatansemester s
		   WHERE s.kodeunit = '$jurusan'
		   AND s.periode = '$tahun"."$semester'
		   Order by s.namaunit asc";

        return $conn->Execute($sql);
	    }
		function getStatusmhs5thn($conn,$jurusan,$tahun1,$tahun2) {
		$sql = "SELECT *
		   FROM akademik.v_lapstatus_per_prodiangkatansemester s
		   WHERE s.kodeunit = '$jurusan'
		   AND s.periode between '$tahun1' AND '$tahun2'";



        return $conn->Execute($sql);
	    }
    	function getLamastudi($conn,$jurusan,$programPendidikan) {
		$sql = "SELECT *
		   FROM akademik.v_laplamastudi_per_periodelulus l
		   WHERE l.kodeunit = '$jurusan'
		   AND l.programpend = '$programPendidikan'
		   Order by l.idlulus asc";

        return $conn->Execute($sql);
	}
	function getIPKlulusprodi($conn,$jurusan,$programPendidikan) {
		$sql = "SELECT *
		   FROM akademik.v_lapipklulus_per_periodelulus p
		   WHERE p.kodeunit = '$jurusan'
		   AND p.programpend = '$programPendidikan'
		   Order by p.idlulus asc";

        return $conn->Execute($sql);
	}
	function getIPKlulus($conn,$fakultas,$programPendidikan,$semester,$tahun) {
	$sql = "select * from gate.ms_unit where kodeunit = '$fakultas'";
		$ok = $conn->Execute($sql)->FetchRow();

		$infoleft = $ok ['infoleft'];
		$inforight = $ok ['inforight'];
        $sql1 = "select kodeunit from gate.ms_unit where infoleft >=$infoleft and inforight<=$inforight";
		$sql = "SELECT *
		   FROM akademik.v_lapipklulus_per_periodelulus k
		   WHERE k.kodeunit in ($sql1)
		   AND k.programpend = '$programPendidikan'
		   AND k.periodelulus = '$tahun"."$semester'
		   Order by k.idlulus asc";

        return $conn->Execute($sql);
	}
	function getRekapKuliahProdi($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT *
		   FROM akademik.v_laprekapkuliah_per_semester r
		   Where r.kodeunit='$jurusan'
		   AND r.periode='$tahun"."$semester'
		   Order by r.nipdosen asc";

        return $conn->Execute($sql);
	}
	function getRekapKuliahSem($conn,$fakultas,$semester,$tahun) {
	//$conn->debug=true;
		$sql = "select * from gate.ms_unit where kodeunit = '$fakultas'";
		$ok = $conn->Execute($sql)->FetchRow();
		$infoleft = $ok ['infoleft'];
		$inforight = $ok ['inforight'];
        $sql = "select kodeunit from gate.ms_unit where infoleft >=$infoleft and inforight<=$inforight";
		$sql = "SELECT r.*,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadosen
		   FROM akademik.v_laprekapkuliah_per_semester r
		   left join sdm.ms_pegawai p on r.nipdosen = p.idpegawai::text
		   Where r.kodeunit in ($sql)
		   AND r.periode ='$tahun"."$semester'
		   Order by r.namaunit asc";

        return $conn->Execute($sql);
	}
	function getRekapAjarDosen($conn,$periode){

		$sql = "select * from akademik.v_reportajardosen where periode='$periode'";
		return $conn->GetArray($sql);
	}
	function getBlmkrs($conn,$jurusan,$semester,$tahun,$angkatan) {
		$sql = "SELECT *
		   FROM akademik.v_lapmhs_blmkrs_per_semester
		   Where periode='$tahun"."$semester' and kodeunit='$jurusan' and substr(periodemasuk,1,4)='$angkatan'
		   Order by nim asc";

        return $conn->Execute($sql);
	}
	function getRasiodosprodi($conn,$jurusan) {
		$sql = "SELECT u.namaunit,r.periode,r.jmldosen,r.jmlmhs,r.prosendosenmhs,r.rasiodosenmhs
		   FROM akademik.v_laprasio_dosen_mhs r
		   JOIN gate.ms_unit u ON u.kodeunit = r.kodeunit
		   Where r.kodeunit ='$jurusan'
		   Order By r.periode asc";

        return $conn->Execute($sql);
	}
	function getRasiodosfakultas($conn,$fakultas,$semester,$tahun) {

		//$conn->debug=true;
		$sql = "select * from gate.ms_unit where kodeunit = '$fakultas'";
		$ok = $conn->Execute($sql)->FetchRow();
		$infoleft = $ok ['infoleft'];
		$inforight = $ok ['inforight'];
        $sql = "select kodeunit from gate.ms_unit where infoleft >=$infoleft and inforight<=$inforight";

        $sql =  "SELECT u.namaunit,r.periode,r.jmldosen,r.jmlmhs,r.prosendosenmhs,r.rasiodosenmhs
                FROM akademik.v_laprasio_dosen_mhs r
                JOIN gate.ms_unit u ON u.kodeunit = r.kodeunit
                Where r.kodeunit in ($sql)
				AND r.periode ='$tahun"."$semester'";
				return $conn->Execute($sql);
		}


	function getRasiodosenins($conn,$semester,$tahun) {
		$sql = "SELECT u.namaunit,r.jmldosen,r.jmlmhs,r.prosendosenmhs,r.rasiodosenmhs
		   FROM akademik.v_laprasio_dosen_mhs r
		   JOIN gate.ms_unit u ON u.kodeunit = r.kodeunit
		   Where r.periode ='$tahun"."$semester'
		   Order by u.namaunit asc";

        return $conn->Execute($sql);
	}

	function getProgrammtkul($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT *
		   FROM akademik.v_lapmhsulangmk_per_periode m
		   Where m.kodeunit ='$jurusan'
		   AND m.periode ='$tahun"."$semester'
		   Order By m.nim asc";

        return $conn->Execute($sql);
	}
	function getJmlmhsberhakSidang($conn,$fakultas,$semester,$tahun) {

	$sql = "SELECT up.namaunit as fakultas,s.kodeunit,s.namaunit,s.jmlmhs
            FROM akademik.v_laprekap_mhsberhaksidang_per_semester s
            JOIN gate.ms_unit u ON s.kodeunit=u.kodeunit
            JOIN gate.ms_unit up ON up.kodeunit=u.kodeunitparent
            Where s.periode ='$tahun"."$semester'
            Order By u.kodeunitparent asc";
        return $conn->Execute($sql);
	}
	function getDaftarMhsBerhakSidang($conn,$kodeunit,$semester,$tahun) {
		$sql = "SELECT *
		   FROM akademik.v_lapdaftar_mhsberhaksidang_per_semester d
		   Where d.kodeunit = '$kodeunit'
		   AND d.periode ='$tahun"."$semester'
		   Order By d.nim asc";

        return $conn->Execute($sql);
	}
	function getPenerimaBeasiswa($conn,$beasiswa,$namabeasiswa,$tahun,$semester,$periodeawal,$periodeakhir) {
		$sql = "SELECT u.namaunit,s.namasumber,b.periodeawal,b.periodeakhir,p.nim,m.nama,b.namabeasiswa
                FROM akademik.ak_penerimabeasiswa p
                JOIN akademik.ak_beasiswa b ON p.idbeasiswa= b.idbeasiswa
                JOIN akademik.ms_mahasiswa m ON p.nim = m.nim
				JOIN akademik.ms_sumberbeasiswa s ON b.kodesumber = s.kodesumber
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
				Where s.namasumber = '$beasiswa'
				and ((b.periodeawal <= '$periodeawal' and b.periodeawal >= '$periodeakhir') or (b.periodeakhir <= '$periodeawal' and b.periodeakhir >= '$periodeakhir'))
				AND b.namabeasiswa ='$namabeasiswa'
                GROUP BY u.namaunit,s.namasumber,b.periodeawal,b.periodeakhir,p.nim,m.nama,b.namabeasiswa";

        return $conn->Execute($sql);
	}
	function getNilaiKKN($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT Distinct p.nim,m.nama,k.judulkp,substr(p.nilaipembimbing::text, 1, 2) AS nilaipembimbing,substr(p.nilaipenguji::text, 1, 3) AS nilaipenguji,substr(p.nilaiperusahaan::text, 1, 2) AS nilaiperusahaan,substr(n.nnumerik::text, 1, 2) AS numerik,n.nhuruf
                FROM akademik.ak_pesertakp p
		        JOIN akademik.ak_kp k ON p.idkp = k.idkp
                JOIN akademik.ms_mahasiswa m ON p.nim = m.nim
                JOIN akademik.ak_krs n ON m.nim = n.nim and k.periode = n.periode
                join akademik.ak_matakuliah l on n.kodemk = l.kodemk and n.thnkurikulum = l.thnkurikulum and l.tipekuliah = 'K'
		        JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
				Where u.kodeunit = '$jurusan'
				AND k.periode ='$tahun"."$semester'";


        return $conn->Execute($sql);
	}
	function getPrestasiMhs($conn,$jurusan) {
		$sql = "SELECT u.kodeunit,u.namaunit,p.nim,m.nama,p.tglpenghargaan,p.namapenghargaan
                FROM akademik.ak_penghargaan p
                JOIN akademik.ms_mahasiswa m ON p.nim = m.nim
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
				Where u.kodeunit = '$jurusan'
                GROUP BY u.kodeunit,u.namaunit,p.nim,m.nama,p.tglpenghargaan,p.namapenghargaan";


        return $conn->Execute($sql);
	}
	function getJurnalPerwalian($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT u.kodeunit,u.namaunit ,k.nim,m.nama,k.nip,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namadosen,k.periode,k.tglkonsultasi,k.isikonsultasi
                FROM akademik.ak_konsultasi k
                JOIN akademik.ms_mahasiswa m ON k.nim = m.nim
                JOIN sdm.ms_pegawai p ON k.nip=p.idpegawai::text
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
				Where u.kodeunit = '$jurusan'
				AND k.periode = '$tahun"."$semester'
                GROUP BY u.kodeunit,u.namaunit,k.nim,m.nama,k.nip,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),k.periode,k.tglkonsultasi,k.isikonsultasi";


        return $conn->Execute($sql);
	}
	function getDistribusidosenwali($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT w.nipdosenwali,count(m.nama) as jumlah,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) AS namadosenwali
                FROM akademik.ms_mahasiswa m
                LEFT JOIN sdm.ms_pegawai p on m.nipdosenwali=p.idpegawai::text
                JOIN akademik.ak_perwalian w ON w.nim = m.nim
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
				Where u.kodeunit = '$jurusan'
				AND m.statusmhs not in ('L','U','W','O','K')
				AND w.periode = '$tahun"."$semester' and w.nipdosenwali <> ''
				group by w.nipdosenwali,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)";

        return $conn->Execute($sql);
	}
	function getDaftarYudisium($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT y.idyudisium,p.periode,p.tglyudisium,y.nim,y.nama
                FROM akademik.ak_yudisium y
                JOIN akademik.ak_periodeyudisium p ON y.idyudisium = p.idyudisium
                JOIN akademik.ms_mahasiswa m ON y.nim = m.nim
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
                Where u.kodeunit ='$jurusan'
                and p.periode = '$tahun"."$semester'
                order by y.idyudisium,y.nama asc";

        return $conn->Execute($sql);
	}
	function rekapAlumni($conn,$jurusan,$periode,$periode2) {
		$rs_leftright = $conn->GetRow("select infoleft, inforight from gate.ms_unit where kodeunit='$jurusan'");
		$rs_periode = $conn->Execute("select * from akademik.ak_periodeyudisium where idyudisium between $periode and $periode2");
		$arr_periode=array();
		while($row = $rs_periode->FetchRow()){
			$arr_periode[] = $row['idyudisium'];
		}
		$sql = "SELECT y.idyudisium,p.periode,p.tglyudisium,y.nim,y.nama,m.sex,m.kodeunit
                FROM akademik.ak_yudisium y
                JOIN akademik.ak_periodeyudisium p ON y.idyudisium = p.idyudisium
                JOIN akademik.ms_mahasiswa m ON y.nim = m.nim
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
                Where u.infoleft>='".$rs_leftright['infoleft']."' and inforight<= '".$rs_leftright['inforight']."'
				and y.idyudisium in ('".implode("','",$arr_periode)."')
				";
        $sql .= "order by y.idyudisium,y.nama asc";

        return $conn->Execute($sql);
	}

	function getKaryaIlmiahDosen($conn,$fakultas) {
		$sql = "SELECT p.tahunkegiatan,p.nip,akademik.f_namalengkap(p2.gelardepan,p2.namadepan,p2.namatengah,p2.namabelakang,p2.gelarbelakang) as nama,p.idtipeppm,p.judul,p.tempat
                FROM sdm.ak_ppmdosen p
                JOIN sdm.ms_pegawai p2 ON p.nip = p2.idpegawai::text
                JOIN gate.ms_unit u ON p2.kodeunit = u.kodeunit
				Where u.kodeunit = '$fakultas'

                order by p.tahunkegiatan asc";

        return $conn->Execute($sql);
	}
	function getKapasitasKelas($conn,$jurusan,$semester,$tahun) {
		$sql = "SELECT k.thnkurikulum,k.kodemk,m.namamk,k.kelasmk,k.koderuang,k.dayatampung,k.jumlahpeserta
                FROM akademik.ak_kelas k
                JOIN gate.ms_unit u ON k.kodeunit = u.kodeunit
                JOIN akademik.ak_matakuliah m ON k.kodemk = m.kodemk
                Where u.kodeunit ='$jurusan'
                and k.periode = '$tahun"."$semester'
                group by k.thnkurikulum,k.kodemk,m.namamk,k.kelasmk,k.koderuang,k.dayatampung,k.jumlahpeserta
				order by k.kodemk asc";

        return $conn->Execute($sql);
	}
	function getJadwalSkripsi($conn,$jurusan,$tglawal,$tglakhir) {
		$sql = "SELECT t.nim,m.nama,t.judulta,uj.koderuang,uj.waktumulai,uj.waktuselesai,uj.tglujian
                FROM akademik.ak_ta t
                JOIN akademik.ak_ujianta uj ON uj.idta = t.idta and uj.idujianta = (select max(x.idujianta) as idujianta from akademik.ak_ujianta x where x.idta = t.idta)
                JOIN akademik.ms_mahasiswa m ON t.nim = m.nim
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
				Where u.kodeunit = '$jurusan'
				and uj.tglujian between '".CStr::formatDate($tglawal)."' and '".CStr::formatDate($tglakhir)."'
				order by t.nim asc
				";

        return $conn->Execute($sql);
	}
	function getNilaiUjiSkripsi($conn,$jurusan,$tglawal,$tglakhir) {
		$sql = "SELECT t.nim,m.nama,t.judulta,uj.tglujian,uj.nilaiujian
                FROM akademik.ak_ujianta uj
                JOIN akademik.ak_ta t ON uj.idta = t.idta
                JOIN akademik.ms_mahasiswa m ON t.nim = m.nim
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
                Where u.kodeunit = '$jurusan'
				and uj.tglujian between '".CStr::formatDate($tglawal)."' and '".CStr::formatDate($tglakhir)."'
				order by t.nim asc";

        return $conn->Execute($sql);
	}
	function getPerwalian($conn,$jurusan,$dosen,$semester,$tahun) {
		$row = mUnit::getData($conn,$jurusan);
		$sql = "SELECT p.nim,m.nama,p.semmhs,p.frsterisi,p.frsdisetujui,p.prasyaratspp,p.statusmhs
                FROM akademik.ak_perwalian p
                JOIN akademik.ms_mahasiswa m ON p.nim = m.nim
                JOIN sdm.ms_pegawai p2 ON p.nipdosenwali = p2.idpegawai::text
                JOIN gate.ms_unit u ON m.kodeunit = u.kodeunit
                Where u.infoleft >= ".(int)$row['infoleft']." and u.inforight <= ".(int)$row['inforight']."
                and p.periode = '$tahun"."$semester'
                and p.nipdosenwali ='$dosen'";

        return $conn->Execute($sql);
	}

	function getStatistikKRS($conn,$kodeunit,$r_periode) {
		require_once(Route::getModelPath('unit'));
		$unit = mUnit::getData($conn,$kodeunit);
		$sql="select j.kodeunit,j.namaunit,count(p.nim) as jumlahmhs,count(case when p.prasyaratspp!='0' and p.statusmhs='A' then p.nim end) as mhsbayar,
				count(case when p.statusmhs='A' and p.frsterisi!=0 then p.nim end) as mhsaktifkrs
				from akademik.ak_perwalian p
				join akademik.ms_mahasiswa m on p.nim=m.nim
				join gate.ms_unit j on j.kodeunit=m.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight']."
				where p.periode='$r_periode'
				group by j.kodeunit,j.namaunit order by j.infoleft";
		/*
		$sql="select tabel2.kodeunit, tabel2.namaunit, tabel2.jml as mahasiswaaktif, tabel1.jml as sudahkrs from
				(select u.kodeunit, u.namaunit, count(k.nim) as jml from akademik.ak_perwalian k
				JOIN akademik.ms_mahasiswa m on m.nim=k.nim
				JOIN gate.ms_unit u
				ON u.kodeunit = m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']."
				where k.statusmhs='A' and k.periode='$r_periode' group by u.kodeunit,u.namaunit ) as tabel2
				left join
				(select u.kodeunit, u.namaunit, count(distinct(k.nim)) as jml from akademik.ak_krs k
				JOIN akademik.ms_mahasiswa m on m.nim=k.nim
				JOIN gate.ms_unit u
				ON u.kodeunit = m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']."
				where m.statusmhs='A' and k.periode='$r_periode' group by u.kodeunit,u.namaunit  ) as tabel1
				on tabel1.kodeunit=tabel2.kodeunit";*/
        return $conn->Execute($sql);

	}

	function getMahasiswaSkripsi($conn,$kodeunit,$r_periode) {

		$sql="select a.nim, b.nama, a.kodemk, c.namamk, b.kodeunit, j.namaunit from akademik.ak_krs a
		 left join akademik.ms_mahasiswa b on a.nim=b.nim
		 left join akademik.ak_matakuliah c on a.kodemk=c.kodemk
		 left join gate.ms_unit j on j.kodeunit=b.kodeunit
		 where a.periode='$r_periode' and b.kodeunit='$kodeunit' and a.kodemk in ('INA029', 'INA0292', 'INA0293', 'INA0294')
		 ";

        return $conn->Execute($sql);

	}

	function getMahasiswaThesis($conn,$kodeunit,$r_periode) {

		$sql="select a.nim, b.nama, a.kodemk, c.namamk, b.kodeunit, j.namaunit from akademik.ak_krs a
		 left join akademik.ms_mahasiswa b on a.nim=b.nim
		 left join akademik.ak_matakuliah c on a.kodemk=c.kodemk
		 left join gate.ms_unit j on j.kodeunit=b.kodeunit
		 where a.periode='$r_periode' and b.kodeunit='4302161101' and a.kodemk='INA206'
		 ";

        return $conn->Execute($sql);

	}

	function getRekapMengajar($conn,$kodeunit,$periode,$periodegaji,$sistemkuliah='',$nopengajuan='',$nip=''){
		require_once(Route::getModelPath('unit'));
		$unit = mUnit::getData($conn,$kodeunit);
		//$unit = mUnit::getData($conn,$kodeunit);
		/*$sql="select k.isonline,k.perkuliahanke,s.namasistem||'-'||s.tipeprogram as basis,kl.sistemkuliah,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen,k.nipdosen,k.nipdosenrealisasi,
				j.namaunit as jurusan,k.kodemk,k.kelasmk,mk.namamk,mk.sks,mk.skspraktikum,k.jeniskuliah,k.topikkuliah,k.tglkuliahrealisasi,k.waktumulairealisasi,k.waktuselesairealisasi
				from akademik.ak_kuliah k
				join akademik.ak_matakuliah mk using (thnkurikulum , kodemk )
				join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
				left join akademik.ak_sistem s on s.sistemkuliah=kl.sistemkuliah
				join sdm.ms_pegawai p on p.idpegawai::text=k.nipdosenrealisasi
				join gate.ms_unit j on j.kodeunit=k.kodeunit and j.kodeunit='$kodeunit'
				where k.periode='$periode' and statusperkuliahan='S' and extract(MONTH from tglkuliahrealisasi) = '$bulan' and kl.sistemkuliah='$sistemkuliah'";*/
		$sql="select k.tglkuliah , k.perkuliahanke , k.periode , k.thnkurikulum , k.kodeunit , k.kodemk , k.kelasmk , k.jeniskuliah , k.kelompok,
				k.waktumulai,k.waktumulairealisasi,k.waktuselesai,k.waktuselesairealisasi,
				s.namasistem||'-'||s.tipeprogram as basis,kl.sistemkuliah,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen,k.nipdosen,g.nipdosenrealisasi,
				j.namaunit as jurusan,mk.namamk,mk.sks,g.skshonor,mk.skspraktikum,mk.skstatapmuka,mk.sksprakteklapangan,k.jeniskuliah,k.topikkuliah,k.tglkuliahrealisasi,
				k.waktumulairealisasi,k.waktuselesairealisasi,g.honordosen,g.nopengajuan,g.nopembayaran,k.validhonorkuliah,g.validhonor,k.isonline,mj.tugasmengajar, g.keterangan
				from akademik.ak_honordosen g
				join akademik.ak_kuliah k using (tglkuliah, perkuliahanke, periode, thnkurikulum, kodeunit, kodemk, kelasmk, jeniskuliah, kelompok)
				join akademik.ak_matakuliah mk using (thnkurikulum , kodemk )
				join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
				left join akademik.ak_sistem s on s.sistemkuliah=kl.sistemkuliah
				join sdm.ms_pegawai p on p.idpegawai::text=g.nipdosenrealisasi
				join gate.ms_unit j on j.kodeunit=k.kodeunit and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight']."
				join akademik.ak_mengajar mj on mj.periode=k.periode and mj.thnkurikulum=k.thnkurikulum and mj.kodeunit=k.kodeunit and
				mj.kodemk=k.kodemk and mj.kelasmk=k.kelasmk and mj.nipdosen=k.nipdosenrealisasi and mj.jeniskul=k.jeniskuliah and mj.kelompok=k.kelompok
				where g.validhonor=-1 and k.periode='$periode' and g.periodegaji='$periodegaji' ";
		if(!empty($sistemkuliah))
			$sql.=" and kl.sistemkuliah='$sistemkuliah'";
		if(!empty($nopengajuan))
			$sql.=" and g.nopengajuan='$nopengajuan'";
		if(!empty($nip))
			$sql.=" and k.nipdosenrealisasi='$nip'";

		$sql.=" order by k.nipdosen,k.kelasmk,k.perkuliahanke";

		return $conn->GetArray($sql);
	}

	function getPemindahBukuan($conn,$kodeunit,$periode,$periodegaji,$nopembayaran){
		require_once(Route::getModelPath('unit'));

		$unit = mUnit::getData($conn,$kodeunit);
		$sqlinv="select g.nopengajuan,j.namaunit,j.kodeurutan,kl.sistemkuliah
				from akademik.ak_honordosen g
				join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk)
				join gate.ms_unit j on j.kodeunit=g.kodeunit
				where g.validhonor=-1 and g.periode='$periode' and g.periodegaji = '$periodegaji'";
		if(!empty($nopembayaran))
			$sqlinv.=" and g.nopembayaran ='$nopembayaran'";

		$sqlinv.=" group by g.nopengajuan,j.namaunit,j.kodeurutan,kl.sistemkuliah order by j.kodeurutan";

		$sql="select k.nipdosenrealisasi,akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) As namadosen,
					kl.sistemkuliah,
				sum(g.honordosen) as jum_honor
				from akademik.ak_honordosen g
				join akademik.ak_kuliah k using (tglkuliah , perkuliahanke , periode , thnkurikulum , kodeunit , kodemk , kelasmk , jeniskuliah , kelompok )
				join akademik.ak_kelas kl using (periode , thnkurikulum , kodeunit , kodemk , kelasmk)
				join sdm.ms_pegawai p on p.idpegawai::text=k.nipdosenrealisasi
				join gate.ms_unit j on j.kodeunit=k.kodeunit
				join akademik.ak_mengajar mj on mj.periode=k.periode and mj.thnkurikulum=k.thnkurikulum and mj.kodeunit=k.kodeunit and
				mj.kodemk=k.kodemk and mj.kelasmk=k.kelasmk and mj.nipdosen=k.nipdosenrealisasi and mj.jeniskul=k.jeniskuliah and mj.kelompok=k.kelompok and mj.tugasmengajar=0
				where g.validhonor=-1 and k.periode='$periode' and g.periodegaji = '$periodegaji'";
		if(!empty($nopembayaran))
			$sql.=" and g.nopembayaran ='$nopembayaran'";
		$sql.=" group by k.nipdosenrealisasi,namadosen,kl.sistemkuliah order by k.nipdosenrealisasi";

		$inv=$conn->GetArray($sqlinv);
		$data=$conn->GetArray($sql);
		$a_data=array('inv'=>$inv,'data'=>$data);

		return $a_data;
	}

	function getSdhkrs($conn,$jurusan,$periode,$frsdisetujui,$nip=null) {
		require_once(Route::getModelPath('unit'));
		$unit = mUnit::getData($conn,$jurusan);
		/*$sql = "SELECT *
		   FROM akademik.v_lapmhs_sdhkrs_per_semester krs
		   Where krs.periode='$tahun"."$semester' and krs.kodeunit='$jurusan' and substr(periodemasuk,1,4)='$angkatan'
		   Order by krs.nim asc";*/
		$sql="SELECT w.periode, m.kodeunit,m.nipdosenwali,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as dosenwali,
				akademik.f_namalengkap(pv.gelardepan,pv.namadepan,pv.namatengah,pv.namabelakang,pv.gelarbelakang) as validator,
				w.t_updateuser,m.nim, m.nama, m.periodemasuk, s.namastatus AS status, w.jumlahsks AS sks,
				akademik.f_getipslalu(m.nim,w.periode) as ipslalu,max(k.t_updatetime) AS waktukrs
			   FROM akademik.ms_mahasiswa m
			   JOIN akademik.lv_statusmhs s ON m.statusmhs::text = s.statusmhs::text
			   JOIN akademik.ak_perwalian w ON m.nim::text = w.nim::text and w.periode='$periode' and w.frsterisi<>0 and w.frsdisetujui='$frsdisetujui'
			   JOIN akademik.ak_krs k ON w.nim::text = k.nim::text AND w.periode::text = k.periode::text
			   join akademik.ak_matakuliah mk on mk.kodemk=k.kodemk and mk.thnkurikulum=k.thnkurikulum
			   join gate.ms_unit j on j.kodeunit=m.kodeunit
			   left join sdm.ms_pegawai p ON m.nipdosenwali=p.idpegawai::text
			   left join sdm.ms_pegawai pv ON w.t_updateuser=pv.idpegawai::text
			   where 1=1 and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight'];
		if(!empty($nip))
			$sql.=" and m.nipdosenwali='$nip'";

		$sql.=" GROUP BY w.periode, m.kodeunit,m.nipdosenwali,
				akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),
				akademik.f_namalengkap(pv.gelardepan,pv.namadepan,pv.namatengah,pv.namabelakang,pv.gelarbelakang),
				w.t_updateuser,m.nim, m.nama, m.periodemasuk, s.namastatus, w.jumlahsks order by m.nim";
		//print_r($sql);
		//die();
        $rs = $conn->Execute($sql);

        $a_data = array();
        while($row = $rs->fetchRow()){
			$a_data[$row['nipdosenwali']][]=$row;
		}

		return $a_data;
	}
	
	function getPelanggaran($conn,$jurusan,$periode,$nim=null) {
		require_once(Route::getModelPath('unit'));
		$unit = mUnit::getData($conn,$jurusan);

		$sql=  "select    w.periode,
				          m.kodeunit,
				          m.nipdosenwali,
				          namaunit,
				          m.nim,
				          m.nama,
				          m.periodemasuk,
				          s.namastatus                           as status,
				          w.jumlahsks                            as sks,
				          akademik.F_getipslalu(m.nim,w.periode) as ipslalu,
				          count(idpelanggaran)                   as jumlahpelanggaran,
				          sum(poinpelanggaran) as poinpelanggaran
				from      akademik.ms_mahasiswa m
				join      akademik.lv_statusmhs s on m.statusmhs::text = s.statusmhs::text
				left join akademik.ak_perwalian w on m.nim::text = w.nim::text and w.periode='$periode' 
				join      gate.ms_unit j on j.kodeunit=m.kodeunit
				left join kemahasiswaan.mw_pelanggaranmhs pl on m.nim = pl.nim and pl.periode='$periode' and pl.isvalid <> 0
				where     1=1 and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight']; 
				//and pl.periode='".$periode."'";

		if(!empty($nim))
			$sql.=" and m.nim='$nim'";

		$sql.=" GROUP BY w.periode, m.kodeunit,m.nipdosenwali,namaunit,
				m.nim, m.nama, m.periodemasuk, s.namastatus, w.jumlahsks order by m.nim";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while($row = $rs->fetchRow()){
			$a_data[]=$row;
		}

		return $a_data;
	}

	function getDetailPelanggaran($conn,$nim,$periode){
		$sql = "select j.namajenispelanggaran, 
				       j.poinpelanggaran 
				from   kemahasiswaan.mw_pelanggaranmhs p 
				       left join kemahasiswaan.lv_jenispelanggaran j 
				              on j.idjenispelanggaran = p.idjenispelanggaran 
				where  nim = '$nim' 
				       and periode = '$periode' and p.isvalid <> 0 " ;

		return  $conn->getArray($sql);			       
	}
	
	
	function getPrestasiByMhs($conn,$nim){

		$sql="	select p.*,namajenisprestasi , namatingkatprestasi , namakategoriprestasi
				from kemahasiswaan.ms_prestasimhs p
				join kemahasiswaan.lv_jenisprestasi jp on p.kodejenisprestasi = jp.kodejenisprestasi
				left join kemahasiswaan.lv_tingkatprestasi t on p.kodetingkatprestasi = t.kodetingkatprestasi
				left join kemahasiswaan.lv_kategoriprestasi k on p.kodekategoriprestasi = k.kodekategoriprestasi 
				where nim = '$nim' ";

		return  $conn->getArray($sql);
	}
	/*
	function getBebasPelanggaran($conn,$periode,$nim){
		$sql = "select idpelanggaran, 
				       p.periode, 
				       p.nim, 
				       m.nama, 
				       namajenispelanggaran, 
				       p.poinpelanggaran as poin,
				       m.kodeunit,
				       u.namaunit
				from   kemahasiswaan.mw_pelanggaranmhs p 
				       join akademik.ms_mahasiswa m 
				         on p.nim = m.nim 
				       left join gate.ms_unit u on u.kodeunit = m.kodeunit
				       join kemahasiswaan.lv_jenispelanggaran jp 
				         on p.idjenispelanggaran = jp.idjenispelanggaran 
				where p.nim = '$nim' and p.periode = '$periode'" ;


		return  $conn->getArray($sql);
	}
	*/

	function getRekapKlaim($conn,$idunit,$tglawal,$tglakhir){
		require_once(Route::getModelPath('unit'));
		$unit = mUnit::getData($conn,$idunit);

		$sql = "select count(ma.nim) as jumlahaju, sum(case when isvalid = -1 then 1 else 0 end) as diterima,j.idunit,namaunit
				from kemahasiswaan.mw_klaimasuransi a
				join kemahasiswaan.ms_asuransimhs ma on a.idasuransimhs = ma.idasuransimhs ";
		if( (!empty($tglawal)) and (!empty($tglakhir)) )
			$sql .= " and tglpengajuan between '$tglawal'::date and '$tglakhir'::date ";
		$sql .=" join akademik.ms_mahasiswa m using (nim)
				join kemahasiswaan.ms_asuransi asu on ma.idasuransi = asu.idasuransi
				join kemahasiswaan.ms_perusahaanasuransi p on asu.kodeprsasuransi = p.kodeprsasuransi
				right join gate.ms_unit j on j.kodeunit=m.kodeunit
				where 1=1 and j.infoleft >= ".(int)$unit['infoleft']." and j.inforight <= ".(int)$unit['inforight']." and level = 2
				GROUP BY j.idunit,namaunit,infoleft
				order by j.infoleft ";
		/*
		$data = array();
		$rs = $conn->Execute($sql);
		while($row = $rs->fetchRow()){
			$a_data[$row['idunit']]=$row;
		}
		return  $data;
		*/
		return  $conn->getArray($sql);
	}

	function getPolismhs($conn,$nim){
		$sql = "select namajenisasuransi, namaasuransi, nopolis, case when s.isaktif = -1 then 'Aktif' else 'Nonaktif' end as isaktif, waktudaftar
				from kemahasiswaan.ms_asuransimhs s
				join kemahasiswaan.ms_asuransi a using (idasuransi)
				join kemahasiswaan.lv_jenisasuransi j using (idjenisasuransi)
				where nim = '$nim' ";
		return $conn->getArray($sql);
	}

	function getSKPI($conn,$nim) {
		//$conn->debug = true;
			// data mahasiswa
			/*
			$sql = "select m.nim, m.nama, m.tmplahir, m.tgllahir,m.tglregistrasi,m.mhstransfer,m.tgllulus,m.tglmasuk,
					m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, r.namaprogram,
					p.namaunit as fakultas,
					u.keterangan,coalesce(m.noijasah,y.noijasah) as noijasah, y.notranskrip, coalesce(y.tgltranskrip,i.tglyudisium) as tgltranskrip,
					t.judulta, m.ipk, m.skslulus,skp.gelar,skp.gelaren,skp.lamastudi,univ.no_sk_dikti,univ.tgl_sk_dikti,
					syaratpenerimaan,syaratpenerimaanen,namabasis,jenispendidikan,jenjangpendidikan,
					jenispendidikanen,jenjangpendidikanen,jenjangpendidikanlanjut,jenjangpendidikanlanjuten,
					jenjangkkni,gelarsingkat,bahasapengantar,bahasapengantaren,skala,kemampuankerja,kemampuankerjaen,
					p.ketua as nipdekan, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as dekan,
					p.namaunit as namafakultas,p.namauniten as namafakultasen, penguasaanpengetahuan,penguasaanpengetahuanen,sikapkhusus,sikapkhususen,
					formatnomor,tglskpi,skp.namauniten,p.namaketuasementara,p.nipketuasementara
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join akademik.ak_prodi univ on univ.kodeunit = '20000000'
					left join akademik.ms_programpend r on pr.kode_jenjang_studi = r.programpend
					left join akademik.ak_yudisium y on m.nim = y.nim and y.noijasah is not null
					left join akademik.ak_periodeyudisium i on y.idyudisium = i.idyudisium
					left join akademik.ak_ta t on m.nim = t.nim and t.statusta <> 'T'
					left join akademik.ak_predikat e on e.programpend = pr.kode_jenjang_studi
								and e.thnkurikulum = akademik.f_kurikulumperiode(m.periodemasuk)
								and m.ipk <= e.ipkatas and m.ipk >= e.ipkbawah and m.semestermhs <= e.bataswaktu
					left join kemahasiswaan.mw_settingskpiprodi skp on m.kodeunit = skp.kodeunit
					left join akademik.ak_sistem sis on m.sistemkuliah = sis.sistemkuliah
					left join akademik.lv_basis bas on sis.kodebasis = bas.kodebasis
					left join (
								select thnkurikulum,programpend,xmlagg((nangkasn||'='||nhuruf||', ')::xml) as skala
								from (select thnkurikulum,programpend,nangkasn,nhuruf
										from akademik.ak_skalanilai group by thnkurikulum,programpend,
										nangkasn,nhuruf order by nangkasn desc
									 ) skn
								group by thnkurikulum,programpend ) sn
					on m.thnkurikulum::int=sn.thnkurikulum and  sn.programpend=pr.kode_jenjang_studi
					left join sdm.ms_pegawai k on k.nik = p.ketua or k.idpegawai::text = p.ketua
					where m.nim = '$nim' ";
			*/
			$sql = "select m.nim, m.nama, m.tmplahir, m.tgllahir,m.tglregistrasi,m.mhstransfer,m.tgllulus,m.tglmasuk,
					m.kodeunit, u.namaunit, pr.kode_jenjang_studi as programpend, r.namaprogram,
					p.namaunit as fakultas,
					u.keterangan,coalesce(m.noijasah,y.ijazah_no) as noijasah, y.skrektor_no AS notranskrip, y.skrektor_tgl as tgltranskrip,
					t.judulta, m.ipk, m.skslulus,skp.gelar,skp.gelaren,skp.lamastudi,univ.no_sk_dikti,univ.tgl_sk_dikti,
					syaratpenerimaan,syaratpenerimaanen,namabasis,jenispendidikan,jenjangpendidikan,
					jenispendidikanen,jenjangpendidikanen,jenjangpendidikanlanjut,jenjangpendidikanlanjuten,
					jenjangkkni,gelarsingkat,bahasapengantar,bahasapengantaren,skala,kemampuankerja,kemampuankerjaen,
					p.ketua as nipdekan, akademik.f_namalengkap(k.gelardepan,k.namadepan, k.namatengah, k.namabelakang,k.gelarbelakang) as dekan,
					p.namaunit as namafakultas,p.namauniten as namafakultasen, penguasaanpengetahuan,penguasaanpengetahuanen,sikapkhusus,sikapkhususen,
					formatnomor,tglskpi,skp.namauniten,p.namaketuasementara,p.nipketuasementara
					from akademik.ms_mahasiswa m
					left join gate.ms_unit u on m.kodeunit = u.kodeunit
					left join akademik.ak_prodi pr on m.kodeunit = pr.kodeunit
					left join gate.ms_unit p on p.kodeunit = u.kodeunitparent
					left join akademik.ak_prodi univ on univ.kodeunit = '20000000'
					left join akademik.ms_programpend r on pr.kode_jenjang_studi = r.programpend
					left join akademik.ak_yudisium y on m.nim = y.nim and y.ijazah_no is not null
					left join akademik.ak_ta t on m.nim = t.nim and t.statusta <> 'T'
					left join akademik.ak_predikat e on e.programpend = pr.kode_jenjang_studi
								and e.thnkurikulum = akademik.f_kurikulumperiode(m.periodemasuk)
								and m.ipk <= e.ipkatas and m.ipk >= e.ipkbawah and m.semestermhs <= e.bataswaktu
					left join kemahasiswaan.mw_settingskpiprodi skp on m.kodeunit = skp.kodeunit
					left join akademik.ak_sistem sis on m.sistemkuliah = sis.sistemkuliah
					left join akademik.lv_basis bas on sis.kodebasis = bas.kodebasis
					left join (
								select thnkurikulum,programpend,xmlagg((nangkasn||'='||nhuruf||', ')::xml) as skala
								from (select thnkurikulum,programpend,nangkasn,nhuruf
										from akademik.ak_skalanilai group by thnkurikulum,programpend,
										nangkasn,nhuruf order by nangkasn desc
									 ) skn
								group by thnkurikulum,programpend ) sn
					on m.thnkurikulum::int=sn.thnkurikulum and  sn.programpend=pr.kode_jenjang_studi
					left join sdm.ms_pegawai k on k.nik = p.ketua or k.idpegawai::text = p.ketua
					where m.nim = '$nim'";
			$a_data = $conn->GetRow($sql);

			$a_data['prestasi'] = self::getPrestasiByMhs($conn,$nim);

			//get pengalaman mhs, sementara diambilkan dari akademik pengalaman. karena mau dicetak akhir maret :D
			$a_data['penghargaan'] = self::getPenghargaan($conn,$nim);

			return array($a_data);
		}

		function getNoSKPI($conn,$nim){
			$sql = "select nomorskpi,statusmhs,counternomor,formatnomor,m.kodeunit,tglskpi,countertahun
					from akademik.ms_mahasiswa m
					left join kemahasiswaan.mw_settingskpiprodi skp on m.kodeunit = skp.kodeunit
					where nim = '".CStr::removeSpecial($nim)."'";
			return  $conn->GetRow($sql);
		}

		function getBioMhs($conn,$periode,$nim){
			$sql = "select m.nim , m.nama , m.kodeunit , u.namaunit from akademik.ms_mahasiswa m
					left join gate.ms_unit u 
						on u.kodeunit = m.kodeunit 
					where nim = '".CStr::removeSpecial($nim)."'";

			return  $conn->GetArray($sql);
		}

		// penghargaan dari skema akademik
		function getPenghargaan($conn,$key,$label='',$post='') {
			$sql = "select idpenghargaan, tglpenghargaan, namapenghargaan,namapenghargaanenglish, isvalid,idjenispenghargaan
					from akademik.ak_penghargaan
					where nim = '$key' and isvalid=-1 order by tglpenghargaan asc";
			$rs = $conn->execute($sql);
			$a_data = array();
			while($row = $rs->fetchRow()){
				if(empty($row['idjenispenghargaan']))
					$data['else'] = $row;
				else
					$a_data[$row['idjenispenghargaan']][] = $row;
			}

			return $a_data;
		}
	
		function getBeasiswaPendaftar($conn,$idunit='',$periode='',$propinsi=''){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$idunit);

			$sql = "select a.*, b.periode||'/'||namajenisbeasiswa as beasiswa,nama,namabeasiswa
					,u1.namaunit as pilihan1,u2.namaunit as pilihan2,namapropinsi
					from kemahasiswaan.mw_pengajuanbeasiswapendaftar a 	
					join pendaftaran.pd_pendaftar m  using (nopendaftar)
					join kemahasiswaan.ms_beasiswa b  using (idbeasiswa)
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa) 
					left join gate.ms_unit u1 on u1.kodeunit=m.pilihan1
					left join gate.ms_unit u2 on u2.kodeunit=m.pilihan2
					left join akademik.ms_propinsi p on m.kodepropinsi = p.kodepropinsi
					where 1=1 ";
			if(!empty($idunit))
				$sql .=" and (u1.infoleft >= ".(int)$unit['infoleft']." and u1.inforight <= ".(int)$unit['inforight']." 
						or u2.infoleft >= ".(int)$unit['infoleft']." and u2.inforight <= ".(int)$unit['inforight'].") "  ;
			if(!empty($periode))
				$sql .=" and periodedaftar = '$periode' ";
			if(!empty($propinsi))
				$sql .=" and m.kodepropinsi = '$propinsi' ";
			return $conn->GetArray($sql);
		}

		function getBeasiswaPresMaba($conn,$idunit='',$periode='',$propinsi='',$prestasi=''){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$idunit);

			$sql = "select a.*, b.periode||'/'||namajenisbeasiswa as beasiswa,nama,namabeasiswa
					,u1.namaunit as pilihan1,u2.namaunit as pilihan2,namapropinsi
					from kemahasiswaan.mw_pengajuanbeasiswapendaftar a 	
					join pendaftaran.pd_pendaftar m  using (nopendaftar)
					join kemahasiswaan.ms_beasiswa b  using (idbeasiswa)
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa) 
					left join gate.ms_unit u1 on u1.kodeunit=m.pilihan1
					left join gate.ms_unit u2 on u2.kodeunit=m.pilihan2
					left join akademik.ms_propinsi p on m.kodepropinsi = p.kodepropinsi
					join (
						   select idpengajuanbeasiswa, string_agg(namatingkatprestasi||'-'||namaprestasi,'<br>') as prestasi
							from kemahasiswaan.mw_prestasibeasiswamaba
							join kemahasiswaan.lv_jenisprestasi using(kodejenisprestasi)
							join kemahasiswaan.lv_kategoriprestasi using(kodekategoriprestasi)
							join kemahasiswaan.lv_tingkatprestasi using(kodetingkatprestasi) ";
			if(!empty($prestasi))
					$sql .= " where kodetingkatprestasi = '$prestasi' ";
			$sql .= " group by idpengajuanbeasiswa
						) pres on a.idpengajuanbeasiswa = pres.idpengajuanbeasiswa
					where 1=1 ";
			if(!empty($idunit))
				$sql .=" and (u1.infoleft >= ".(int)$unit['infoleft']." and u1.inforight <= ".(int)$unit['inforight']." 
						or u2.infoleft >= ".(int)$unit['infoleft']." and u2.inforight <= ".(int)$unit['inforight'].") "  ;
			if(!empty($periode))
				$sql .=" and periodedaftar = '$periode' ";
			if(!empty($propinsi))
				$sql .=" and m.kodepropinsi = '$propinsi' ";
			return $conn->GetArray($sql);
		}
		
		function getBeasiswaMahasiswa($conn,$idunit='',$periode='',$propinsi=''){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$idunit);

			$sql = "select a.*, b.periode||'/'||namajenisbeasiswa as beasiswa,nama,namabeasiswa
					,u1.namaunit
					from kemahasiswaan.mw_pengajuanbeasiswa a 	
					join akademik.ms_mahasiswa m  using (nim)
					join kemahasiswaan.ms_beasiswa b  using (idbeasiswa)
					join kemahasiswaan.lv_jenisbeasiswa j  using (idjenisbeasiswa) 
					left join gate.ms_unit u1 on u1.kodeunit=m.kodeunit
					where 1=1 ";
			if(!empty($idunit))
				$sql .=" and (u1.infoleft >= ".(int)$unit['infoleft']." and u1.inforight <= ".(int)$unit['inforight']." ) "  ;
			if(!empty($periode))
				$sql .=" and b.periode = '$periode' ";
			if(!empty($propinsi))
				$sql .=" and m.kodepropinsi = '$propinsi' ";
			return $conn->GetArray($sql);
		}

		function getQuiz($conn_moodle,$key,$unit) {
			if($unit=='4302100000'){
				$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from moodle.v_reportquiz where namadosen is not null and periode='$key'";
			}else{
				if((int)$key>=20182){
					$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from moodle.v_reportquiz where namadosen is not null and periode='$key' and kodeunit='$unit|$key'";
					
				}else{
					$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from moodle.v_reportquiz where namadosen is not null and periode='$key' and kodeunit='$unit'";
					
				}
			}
			return $conn_moodle->getArray($sql);
			

		}
		function getReportActByDate($conn_moodle,$kodeunit,$periode,$tanggalawal,$tanggalakhir){
			if($kodeunit=='4302100000'){
				$sql = "select kodeunit,periode,kodemk,namakelas,namadosen,seksi as kelasmk,
				COUNT(pertemuanke) filter (where lower(realisasi) like '%quiz%' or lower(realisasi) like '%kuis%') as quiz, 
				count(pertemuanke) filter (where lower(realisasi) like '%tugas%' or lower(realisasi) like '%latihan%') as tugas
				from moodle.report_quiz_dosen where DATE(tglmulaiperkuliahan)>='$tanggalawal' and DATE(tglmulaiperkuliahan)<='$tanggalakhir' group by kodeunit,periode,kodemk,namakelas,namadosen,seksi order by kodeunit asc";
			}else{
				$sql = "select kodeunit,periode,kodemk,namakelas,namadosen,seksi as kelasmk,
				COUNT(pertemuanke) filter (where lower(realisasi) like '%quiz%' or lower(realisasi) like '%kuis%') as quiz, 
				count(pertemuanke) filter (where lower(realisasi) like '%tugas%' or lower(realisasi) like '%latihan%') as tugas
				from moodle.report_quiz_dosen where DATE(tglmulaiperkuliahan)>='$tanggalawal' and DATE(tglmulaiperkuliahan)<='$tanggalakhir' and periode='$periode' and kodeunit='$kodeunit|$periode' group by kodeunit,periode,kodemk,namakelas,namadosen,seksi order by kodeunit asc";
			}
			return $conn_moodle->getArray($sql);
		}
		function getTugas($conn_moodle,$key,$unit) {
			if($unit=='4302100000'){
				$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from moodle.v_reporttugas where namadosen is not null and periode='$key'";
			}else{
				if((int)$key>=20182){
					$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from moodle.v_reportquiz where namadosen is not null and periode='$key' and kodeunit='$unit|$key'";
					
				}else{
					$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from moodle.v_reporttugas where namadosen is not null and periode='$key' and kodeunit='$unit'";
					
				}
			}
			return $conn_moodle->getArray($sql);
			

		}
		function getVideo($conn_moodle,$key,$unit) {
			if($unit=='4302100000'){
				$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from public.v_reportvideo where namadosen is not null and periode='$key'";
			}else{
				if((int)$key>=20182){
					$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from moodle.v_reportquiz where namadosen is not null and periode='$key' and kodeunit='$unit|$key'";
					
				}else{
					$sql = "select kodeunit,periode,namakelas,namadosen,jumlah from public.v_reportvideo where namadosen is not null and periode='$key' and kodeunit='$unit'";
					
				}
			}
			return $conn_moodle->getArray($sql);
			

		}
}
?>
