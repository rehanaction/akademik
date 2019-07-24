<?php

// model agama
defined('__VALID_ENTRANCE') or die('Akses terbatas');

require_once(Route::getModelPath('model'));

class mGaji extends mModel {

    const schema = 'sdm';

    /*     * ************************************************** GAJI ***************************************************** */

    // mendapatkan kueri list untuk periode gaji
    function listQueryPeriodeGA() {
        $sql = "select * from " . static::table('ga_periodegaji');

        return $sql;
    }

    function getCPeriodeGaji($conn) {
        $sql = "select periodegaji, namaperiode from " . static::table('ga_periodegaji') . " where refperiodegaji is null order by tglakhirhit desc";

        return Query::arrQuery($conn, $sql);
    }

    function getCPeriodeTHR($conn) {
        $sql = "select periodegaji, namaperiode from " . static::table('ga_periodegaji') . " where refperiodegaji is not null order by tglakhirhit desc";

        return Query::arrQuery($conn, $sql);
    }

    function getLastPeriodeGaji($conn) {
        $r_periodegaji = $conn->GetOne("select periodegaji from " . static::table('ga_periodegaji') . " where refperiodegaji is null order by tglakhirhit desc limit 1");

        return $r_periodegaji;
    }

    function getLastDataPeriodeGaji($conn, $r_periode = '') {
        if (empty($r_periode))
            $r_periode = self::getLastPeriodeGaji($conn);

        $row = $conn->GetRow("select * from " . static::table('ga_periodegaji') . " where periodegaji = '$r_periode'");
        
        return $row;
    }

    function listQueryPeriodeTarif() {
        $sql = "select * from " . static::table('ms_periodetarif') . "";

        return $sql;
    }

    function getCPeriodeTarif($conn) {
        $sql = "select periodetarif, namaperiode from " . static::table('ms_periodetarif') . " order by tglmulai desc";

        return Query::arrQuery($conn, $sql);
    }

    function getLastPeriodeTarif($conn) {
        $r_periodetarif = $conn->GetOne("select periodetarif from " . static::table('ms_periodetarif') . " order by tglmulai desc limit 1");

        return $r_periodetarif;
    }

    function periodeTarifSalin($conn, $r_key = '') {
        $sql = "select periodetarif, namaperiode from " . static::table('ms_periodetarif') . " where periodetarif <> '$r_key' order by tglmulai desc";

        return Query::arrQuery($conn, $sql);
    }

    function saveSalinTarif($conn, $r_key, $p_dbtable, $f_key) {
        //bersihkan dulu
        list($err, $msg) = self::delete($conn, $r_key, 'ms_tarifgapok', 'periodetarif');
        if (!$err) {
            $sql = "insert into " . static::table('ms_tarifgapok') . " 
						select '$r_key',idpangkat,masakerja,tarifgapok,'" . Modul::getUserName() . "','" . date('Y-m-d H:i:s') . "','" . $_SERVER['REMOTE_ADDR'] . "' 
						from " . static::table('ms_tarifgapok') . "
						where periodetarif = '$f_key'";

            $conn->Execute($sql);
        }

        list($err, $msg) = self::delete($conn, $r_key, 'ms_tariftunjangan', 'periodetarif');
        if (!$err) {
            $sql = "insert into " . static::table('ms_tariftunjangan') . " 
						select '$r_key',kodetunjangan,variabel1,variabel2,nominal,'" . Modul::getUserName() . "','" . date('Y-m-d H:i:s') . "','" . $_SERVER['REMOTE_ADDR'] . "' 
						from " . static::table('ms_tariftunjangan') . "
						where periodetarif = '$f_key'";

            $conn->Execute($sql);
        }

        return $err;
    }

    function getCPendidikan($conn) {
        $sql = "select idpendidikan, namapendidikan from " . static::table('lv_jenjangpendidikan') . " order by urutan desc";

        return Query::arrQuery($conn, $sql);
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

    //********************************G A J I   P O K O K*************************************
    function listQueryGajiHonorer() {
        $sql = "select p.idpegawai," . static::schema() . "f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap,
					namapendidikan,namaunit,th.nominal as tarif,g.isfinish,g.gajiditerima
					from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('lv_jenjangpendidikan') . " j on j.idpendidikan=p.idpendidikan
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					left join " . static::table('ga_tarifhonorer') . " th on th.idpendidikan=p.idpendidikan
					where p.idstatusaktif in (select idstatusaktif from " . static::table('lv_statusaktif') . " where isdigaji='Y')
					and idhubkerja in ('HP')";
        return $sql;
    }

    function getJumlahHadir($conn, $r_periode) {
        $last = self::getLastDataPeriodeGaji($conn, $r_periode);
        if(!empty($last['tglawalhonorer'])){
        $sql = "select count(tglpresensi) as jum,p.idpegawai from " . static::table('ms_pegawai') . " p
					left join " . static::table('pe_presensidet') . " t on p.idpegawai=t.idpegawai and (t.tglpresensi between '" . $last['tglawalhonorer'] . "' and '" . $last['tglakhirhonorer'] . "') and (jamdatang is not null or jampulang is not null)
					group by p.idpegawai";
        $rs = $conn->Execute($sql);

        $a_jumkehadiran = array();
        while ($row = $rs->FetchRow()) {
            $a_jumkehadiran[$row['idpegawai']] = $row['jum'];
        }

        return $a_jumkehadiran;
        }
    }

    function hitGajiHonorer($conn, $r_periode, $r_sql = '') {
        $last = self::getLastDataPeriodeGaji($conn, $r_periode);
        if(!empty($last['tglawalhonorer'])){
        $sql = "select idpendidikan,nominal from " . static::table('ga_tarifhonorer');
        $a_tarif = Query::arrQuery($conn, $sql);

        $sql = "select count(tglpresensi) as jum,p.idpegawai,p.idpendidikan,g.idpegawai as idpeg,g.isfinish 
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('pe_presensidet') . " t on p.idpegawai=t.idpegawai and (t.tglpresensi between '" . $last['tglawalhonorer'] . "' and '" . $last['tglakhirhonorer'] . "') and (jamdatang is not null or jampulang is not null)
					left join " . static::table('ga_gajipeg') . " g on g.idpegawai=p.idpegawai and periodegaji='$r_periode'
					where p.idstatusaktif in (select idstatusaktif from " . static::table('lv_statusaktif') . " where isdigaji='Y') and idhubkerja in ('HP')
					group by p.idpegawai,p.idpendidikan,g.idpegawai,g.isfinish";
        $rs = $conn->Execute($sql);
        $a_pegawai = array();
        while ($row = $rs->FetchRow()) {
            $record = array();
            $record['periodegaji'] = $r_periode;
            $record['idpegawai'] = $row['idpegawai'];
            $record['gapok'] = $row['jum'] * $a_tarif[$row['idpendidikan']];

            if ($row['idpeg'] <> '') {
                if ($row['isfinish'] <> 'Y') {
                    $key = $r_periode . '|' . $record['idpegawai'];
                    $colkey = 'periodegaji,idpegawai';
                    $err = self::updateRecord($conn, $record, $key, false, 'ga_gajipeg', $colkey);
                }
            } else
                $err = self::insertRecord($conn, $record, false, 'ga_gajipeg');
        }

        return array($err, $msg);
        }
    }

    function bayarGajiHonorer($conn, $r_periode, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select g.idpegawai,g.periodegaji from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " m on m.idpegawai=g.idpegawai 
					where g.periodegaji = '$r_periode' and (g.istunda = 'T' or g.istunda is null) and idhubkerja='HP'
					and (g.isfinish = 'T' or g.isfinish is null)";
        if (!empty($a_peg))
            $sql .= " and g.idpegawai in ($a_peg)";

        $rs = $conn->Execute($sql);

        $record = array();
        $record['isfinish'] = 'Y';
        $record['tgldibayarkan'] = date('Y-m-d');

        while ($row = $rs->FetchRow()) {
            if (!empty($row['norekening']))
                $record['istransfer'] = 'Y';
            else
                $record['istransfer'] = 'T';

            $key = $row['idpegawai'] . '|' . $row['periodegaji'];
            $colkey = 'idpegawai,periodegaji';
            list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_gajipeg', $colkey);
        }

        list($err, $msg) = self::updateStatus($conn);

        return array($err, $msg);
    }

    function listQueryGapok() {
        $sql = "select g.*,p.golongan from " . static::table('ms_tarifgapok') . " g
					left join " . static::table('ms_pangkat') . " p on p.idpangkat = g.idpangkat";

        return $sql;
    }

    function getCPangkat($conn) {
        $sql = "select idpangkat, golongan from " . static::table('ms_pangkat') . " order by idpangkat";

        return Query::arrQuery($conn, $sql);
    }

    function listQueryPeriodeUMR() {
        $sql = "select * from " . static::table('ga_periodegapokumr') . "";

        return $sql;
    }

    function listQueryTarifUMR() {
        $sql = "select t.*," . static::schema() . "f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap 
				from " . static::table('ms_tarifumr') . " t
				left join " . static::table('ms_pegawai') . " p on p.idpegawai = t.idpegawai";

        return $sql;
    }

    function getDataEditTarifUMR($r_key) {
        $sql = "select t.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
				from " . static::table('ms_tarifumr') . " t
				left join " . static::table('ms_pegawai') . " m on m.idpegawai=t.idpegawai
				where t.notarifumr = $r_key";

        return $sql;
    }

    function getGapokNonAdmin($conn) {
        $n_umr = self::getLastUMR($conn);

        //periode tarif sekarang
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        //jumlah hari kerja
        $jmlharikerja = mPresensi::getHariKerja($conn);

        //tunjangan kehadiran
        $t_hadir = $conn->GetOne("select nominal from " . static::table('ms_tariftunjangan') . " where periodetarif = '$r_periodetarif' and kodetunjangan = 'T00015' limit 1");
        $t_hadir = empty($t_hadir) ? 0 : $t_hadir * $jmlharikerja;

        //tunjangan transport
        $t_transport = $conn->GetOne("select nominal from " . static::table('ms_tariftunjangan') . " where periodetarif = '$r_periodetarif' and kodetunjangan = 'T00016' limit 1");
        $t_transport = empty($t_transport) ? 0 : $t_transport * $jmlharikerja;

        //umr - (tunjangan kehadiran dan transport)
        $r_umr = $n_umr - ($t_hadir + $t_transport);

        return $r_umr;
    }

    function getLastUMR($conn) {
        $n_umr = $conn->GetOne("select umr from " . static::table('ga_periodegapokumr') . " order by tglmulai desc limit 1");

        return $n_umr;
    }

    function saveSalinGapok($conn, $r_periode, $keyPeriode, $prosentase) {
        $err = 0;
        list($err, $msg) = self::delete($conn, $r_periode, 'ms_tarifgapok', 'periodetarif');

        if (!$err) {
            $sql = "insert into " . static::table('ms_tarifgapok') . " 
						select $r_periode,idpangkat,masakerja,tarifgapok + (tarifgapok * $prosentase) as tarifgapok,'" . Modul::getUserName() . "','" . date('Y-m-d H:i:s') . "','" . $_SERVER['REMOTE_ADDR'] . "' 
						from " . static::table('ms_tarifgapok') . "
						where periodetarif='$keyPeriode' ";
            $conn->Execute($sql);
        }
        if ($err)
            $msg = 'Salin tarif gaji pokok gagal, data masih digunakan';
        else
            $msg = 'Salin tarif gaji pokok berhasil';

        return array($err, $msg);
    }

    function listQueryTarifGapokAdmin() {
        $sql = "select g.* from " . static::table('ms_tarifgapokadmin') . " g";

        return $sql;
    }

    function getTarifGapokAdmin($conn, $r_periode) {
        $sql = "select * from " . static::table('ms_tarifgapokadmin') . " where periodetarif = '$r_periode'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpendidikan']] = $row['tarifgapok'];
        }

        return $a_data;
    }

    function listQueryTarifGapokDosen() {
        $sql = "select g.* from " . static::table('ms_tarifgapokdosen') . " g";

        return $sql;
    }

    function getTarifGapokDosen($conn, $r_periode) {
        $sql = "select * from " . static::table('ms_tarifgapokdosen') . " where periodetarif = '$r_periode'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpendidikan']] = $row['tarifgapok'];
        }

        return $a_data;
    }

    function isDosenLB($conn, $idpegawai) {
        $cekDosenLB = $conn->GetOne("select 1 from " . static::table('ms_pegawai') . " where idpegawai=$idpegawai and idjenispegawai='PLB'");

        return $cekDosenLB;
    }

    //*************************************T U N J A N G A N************************************

    function listQueryTunjangan() {
        $sql = "select * from " . static::table('ms_tunjangan');

        return $sql;
    }

    function getCTunjTarif($conn) {
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where carahitung in ('T','P') and isaktif = 'Y' order by kodetunjangan";

        return Query::arrQuery($conn, $sql);
    }

    function saveSalinTunjangan($conn, $r_periode, $r_tunjangan, $keyPeriode, $prosentase) {
        $err = 0;

        $key = $r_periode . '|' . $r_tunjangan;
        list($err, $msg) = self::delete($conn, $key, 'ms_tariftunjangan', 'periodetarif,kodetunjangan');

        if (!$err) {
            $sql = "insert into " . static::table('ms_tariftunjangan') . " 
						select $r_periode,kodetunjangan,variabel1,variabel2,nominal + (nominal * $prosentase) as nominal,'" . Modul::getUserName() . "','" . date('Y-m-d H:i:s') . "','" . $_SERVER['REMOTE_ADDR'] . "' 
						from " . static::table('ms_tariftunjangan') . "
						where periodetarif='$keyPeriode' and kodetunjangan='$r_tunjangan'";
            $conn->Execute($sql);
        }
        if ($err)
            $msg = 'Salin tarif tunjangan gagal, data masih digunakan';
        else
            $msg = 'Salin tarif tunjangan berhasil';

        return array($err, $msg);
    }

    function getCTunjBayarAwal($conn) {
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where isbayargaji = 'T' and isaktif = 'Y' order by kodetunjangan";

        return Query::arrQuery($conn, $sql);
    }

    function getTunjBayarAwal($conn) {
        $sql = "select  kodetunjangan from " . static::table('ms_tunjangan') . " where isbayargaji = 'T' and isaktif = 'Y' order by kodetunjangan limit 1";
        $kode = $conn->GetOne($sql);

        return $kode;
    }

    function getNamaTunj($conn, $r_tunjangan) {
        $sql = "select namatunjangan from " . static::table('ms_tunjangan') . " where kodetunjangan = '$r_tunjangan'";
        $nama = $conn->GetOne($sql);

        return $nama;
    }

    function getCTunjParameter($conn, $empty = false) {
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where carahitung in ('P','M') and isbayargaji = 'T' and isaktif = 'Y' order by kodetunjangan";

        $rs = $conn->Execute($sql);

        if ($empty) {
            $a_data = array();
            $a_add = array('all' => '-- Semua Tunjangan Tarif Param --');
            $a_data = array_merge($a_data, $a_add);
        }

        while ($row = $rs->FetchRow()) {
            $a_data[$row['kodetunjangan']] = $row['namatunjangan'];
        }

        return $a_data;
    }

    function getCTunjManual($conn, $empty = false) {
        $sql = "select kodetunjangan, namatunjangan from " . static::table('ms_tunjangan') . " where carahitung = 'M' and isbayargaji = 'Y' and isaktif = 'Y' order by kodetunjangan";

        $rs = $conn->Execute($sql);

        if ($empty) {
            $a_data = array();
            $a_add = array('all' => '-- Semua Tunjangan Lain --');
            $a_data = array_merge($a_data, $a_add);
        }

        while ($row = $rs->FetchRow()) {
            $a_data[$row['kodetunjangan']] = $row['namatunjangan'];
        }

        return $a_data;
    }

    function listQueryProcMasaKerja() {
        $sql = "select g.* from " . static::table('ms_procmasakerja') . " g";

        return $sql;
    }

    function listQueryProcMasaKerjaAdm() {
        $sql = "select g.* from " . static::table('ms_procmasakerjaadm') . " g";

        return $sql;
    }

    //prosentase tunjangan masa kerja untuk admin
    function getTarifMasaKerjaAdm($conn, $r_periodetarif) {
        //mendapatkan acuan tunjangan masa kerja admin
        $sql = "select cast(variabel1 as int) as masakerja,nominal from " . static::table('ms_tariftunjangan') . " 
				where periodetarif = '$r_periodetarif' and kodetunjangan = 'T00020'";
        $rowa = $conn->GetRow($sql);

        $sql = "select * from " . static::table('ms_procmasakerjaadm') . " where periodetarif = '$r_periodetarif' order by masakerja";
        $rs = $conn->Execute($sql);

        $a_tarifproc = array();
        while ($row = $rs->FetchRow()) {
			$a_tarifproc[$row['masakerja']] = $rowa['nominal'] * ($row['prosentase'] / 100);
        }

        return $a_tarifproc;
    }

    //prosentase tunjangan masa kerja untuk non admin
    function getPRocMasaKerja($conn, $r_periodetarif) {
        $sql = "select * from " . static::table('ms_procmasakerja') . " where periodetarif = '$r_periodetarif' order by masakerja";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_proc[$row['masakerja']] = $row['prosentase'];
        }

        return $a_proc;
    }

    function listQueryTarifTunjangan($r_tunjangan) {
        if ($r_tunjangan == 'T00001' or $r_tunjangan == 'T00017') {//T. Struktural
            $select = ",jj.namajabatan as namavariabel,jj.level,jj.idjabatan";
            $leftjoin = "left join " . static::table('ms_jabatan') . " jj on jj.idjabatan = g.variabel1";
        } else if ($r_tunjangan == 'T00018') {//T. Fasilitas
            $select = ",s.jabatanstruktural as namavariabel,s.level,s.infoleft";
            $leftjoin = "left join " . static::table('ms_struktural') . " s on s.idjstruktural = g.variabel1";
        } else if ($r_tunjangan == 'T00013') {//T. Homebase
            $select = ",f.jabatanfungsional as namavariabel";
            $leftjoin = "left join " . static::table('ms_fungsional') . " f on f.idjfungsional = g.variabel1";
        } else if ($r_tunjangan == 'T00020') {//T. Masa Kerja
            $select = ",g.variabel1+' tahun' as namavariabel";
        } else {//Selain di atas
            $select = ",t.tipepeg||' - '||j.jenispegawai as namavariabel";
            $leftjoin = "left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = g.variabel1
						left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = j.idtipepeg";
        }

        $sql = "select g.*{$select} from " . static::table('ms_tariftunjangan') . " g {$leftjoin}";

        return $sql;
    }

    function getTunjangan($conn, $r_key) {
        $tunj = $conn->GetOne("select kodetunjangan from " . static::table('ms_tariftunjangan') . " where idtarif = $r_key");

        return $tunj;
    }

    function infoTunjangan($conn, $r_tunjangan) {
        if ($r_tunjangan == '')
            $r_tunjangan = $conn->GetOne("select kodetunjangan from " . static::table('ms_tunjangan') . " order by kodetunjangan limit 1");

        $tunj = $conn->GetOne("select namatunjangan from " . static::table('ms_tunjangan') . " where kodetunjangan = '$r_tunjangan'");

        if ($r_tunjangan == 'T00001' or $r_tunjangan == 'T00017') {//T. Struktural
            $info = 'Jabatan';
            $filter = 'jj.namajabatan';
        } else if ($r_tunjangan == 'T00018') {//T. Fasilitas
            $info = 'Struktural';
            $filter = 's.jabatanstruktural';
            $filter = 'gl.golongan';
        } else if ($r_tunjangan == 'T00013') {//T. Homebase
            $info = 'Fungsional';
            $filter = 'f.jabatanfungsional';
        } else if ($r_tunjangan == 'T00020') {//T. Masa Kerja
            $info = 'Masa Kerja Acuan (tahun)';
            $filter = "g.variabel1+' tahun'";
        } else {//Selain di atas
            $info = 'Jenis Pegawai';
            $filter = 'j.jenispegawai';
        }

        $rtunj['namatunjangan'] = $tunj;
        $rtunj['info'] = $info;
        $rtunj['filter'] = $filter;

        return $rtunj;
    }

    function aCaraHitungTunj() {
        return array("M" => "Manual", "O" => "Otomatis", "T" => "Tarif", "P" => "Tarif Parameter");
    }

    function getCJenisPegawai($conn) {
        $sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai as jenispegawai 
					from " . static::table('ms_jenispeg') . " j
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg=j.idtipepeg 
					order by tipepeg";

        return Query::arrQuery($conn, $sql);
    }

    function getCAllHubKerja($conn) {
        $sql = "select idhubkerja, hubkerja from " . static::table('ms_hubkerja') . " order by idhubkerja";
        $rs = $conn->Execute($sql);

        $a_data = array();
        $a_add = array('all' => '-- Semua Hubungan Kerja --');
        $a_data = array_merge($a_data, $a_add);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['idhubkerja']] = $row['hubkerja'];
        }

        return $a_data;
    }

    function getCAllJenisPegawai($conn) {
        $sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai as jenispegawai 
					from " . static::table('ms_jenispeg') . " j
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg=j.idtipepeg 
					order by tipepeg";
        $rs = $conn->Execute($sql);

        $a_data = array();
        $a_add = array('all' => '-- Semua Jenis Pegawai --');
        $a_data = array_merge($a_data, $a_add);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['idjenispegawai']] = $row['jenispegawai'];
        }

        return $a_data;
    }

    function aTunjHak($conn, $r_key) {
        $sql = "select j.idjenispegawai, tipepeg || ' - ' || jenispegawai as jenispegawai, kodetunjangan
					from " . static::table('ms_jenispeg') . " j
					left join " . static::table('ms_tunjangandet') . " t on j.idjenispegawai=t.idjenispegawai and  kodetunjangan='$r_key'
					left join " . static::table('ms_tipepeg') . " tp on tp.idtipepeg=j.idtipepeg
					order by tipepeg";
        $rs = $conn->Execute($sql);
        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return $a_data;
    }

    function saveTunjHak($conn, $r_key, $a_jenis) {
        $sql = "delete from " . static::table('ms_tunjangandet') . " where kodetunjangan='$r_key'";
        $conn->Execute($sql);

        $recdetail = array();
        $recdetail['kodetunjangan'] = $r_key;
        if (count($a_jenis) > 0) {
            foreach ($a_jenis as $col) {
                unset($recdetail['idjenispegawai']);
                $recdetail['idjenispegawai'] = $col;

                mGaji::insertRecord($conn, $recdetail, false, 'ms_tunjangandet');
            }
        }

        return static::updateStatus($conn);
    }

    //Jenis tunjangan tidak tetap
    function tunjanganTdkTetap($conn) {
        $sql = "select kodetunjangan from " . static::table('ms_tunjangan') . "	where (isgajitetap = 'N' or isgajitetap is null) and isbayargaji = 'Y' and isaktif = 'Y'";
        $rst = $conn->Execute($sql);

        while ($rowt = $rst->FetchRow()) {
            $a_tunjt[] = $rowt['kodetunjangan'];
        }

        return $a_tunjt;
    }

    //Jenis tunjangan yang merupakan gaji tetap
    function tunjanganTetap($conn) {
        $sql = "select kodetunjangan from " . static::table('ms_tunjangan') . "	where isgajitetap = 'Y' and isbayargaji = 'Y' and isaktif = 'Y'";
        $rst = $conn->Execute($sql);

        while ($rowt = $rst->FetchRow()) {
            $a_tunj[] = $rowt['kodetunjangan'];
        }

        return $a_tunj;
    }

    //Tunjangan yang dikalikan jam kerja
    function isTunjJamKerja($conn) {
        $sql = "select kodetunjangan from " . static::table('ms_tunjangan') . "	where iskeldosen = 'Y'";
        $rst = $conn->Execute($sql);

        while ($rowt = $rst->FetchRow()) {
            $a_tunjjk[] = $rowt['kodetunjangan'];
        }

        return $a_tunjjk;
    }

    //Tunjangan yang dikalikan jumlah hari kerja
    function isKaliHariKerja($conn) {
        $sql = "select kodetunjangan from " . static::table('ms_tunjangan') . "	where iskaliharikerja = 'Y'";
        $rst = $conn->Execute($sql);

        while ($rowt = $rst->FetchRow()) {
            $a_tunjhk[] = $rowt['kodetunjangan'];
        }

        return $a_tunjhk;
    }

    //Jenis tunjangan tidak tetap dengan jenis pegawai
    function tunjanganTdkTetapDet($conn) {
        $sql = "select d.* from " . static::table('ms_tunjangandet') . " d
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan = d.kodetunjangan
					where (t.isgajitetap = 'N' or t.isgajitetap is null) and t.isbayargaji = 'Y' and t.isaktif = 'Y'";
        $rsd = $conn->Execute($sql);

        while ($rowd = $rsd->FetchRow()) {
            $a_tunjtdet[$rowd['kodetunjangan']][$rowd['idjenispegawai']] = $rowd['idjenispegawai'];
        }

        return $a_tunjtdet;
    }

    //Jenis tunjangan yang merupakan gaji awal
    function tunjanganAwal($conn) {
        $sql = "select kodetunjangan from " . static::table('ms_tunjangan') . "	where isbayargaji = 'T' and isaktif = 'Y'";
        $rst = $conn->Execute($sql);

        while ($rowt = $rst->FetchRow()) {
            $a_tunj[] = $rowt['kodetunjangan'];
        }

        return $a_tunj;
    }

    //Jenis tunjangan dibayar awal
    function tunjanganAwalDet($conn) {
        $sql = "select d.* from " . static::table('ms_tunjangandet') . " d
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan = d.kodetunjangan
					where t.isbayargaji = 'T' and t.isaktif = 'Y'";
        $rsd = $conn->Execute($sql);

        while ($rowd = $rsd->FetchRow()) {
            $a_tunjadet[$rowd['kodetunjangan']][$rowd['idjenispegawai']] = $rowd['idjenispegawai'];
        }

        return $a_tunjadet;
    }

    //Jenis tunjangan dengan jenis pegawai
    function tunjanganTetapDet($conn) {
        $sql = "select d.* from " . static::table('ms_tunjangandet') . " d
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan = d.kodetunjangan
					where t.isgajitetap = 'Y' and t.isbayargaji = 'Y' and t.isaktif = 'Y'";
        $rsd = $conn->Execute($sql);

        while ($rowd = $rsd->FetchRow()) {
            $a_tunjdet[$rowd['kodetunjangan']][$rowd['idjenispegawai']] = $rowd['idjenispegawai'];
        }

        return $a_tunjdet;
    }

    function getTarifTunj($conn) {
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        $sql = "select * from " . static::table('ms_tariftunjangan') . " where periodetarif = '$r_periodetarif'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            if (!empty($row['variabel2']))
                $a_tariftunj[$row['kodetunjangan']][$row['variabel1']][$row['variabel2']] = $row['nominal'];
            else
                $a_tariftunj[$row['kodetunjangan']][$row['variabel1']] = $row['nominal'];
        }

        return $a_tariftunj;
    }

    //Simpan gaji tunjangan
    function saveTunjangan($conn, $record) {
        $err = 0;
        $idpegawai = $record['idpegawai'];
        $a_hubkerja = self::getHubKerjaPegProsentase($conn);
        $isDosenLB = self::isDosenLB($conn, $idpegawai);

        if ($isDosenLB and $record['kodetunjangan'] <> "T00013")
            $record['nominal'] = 0;
        else
            $record['nominal'] = $record['nominal'] * ($a_hubkerja[$record['idpegawai']] / 100);

        if (!empty($record['nominal']))
            $err = self::insertRecord($conn, $record, false, 'ga_tunjanganpeg');

        return $err;
    }

    //Simpan gaji tunjangan struktural
    function saveTunjanganStruktural($conn, $record, $nominal, $struktural, $struktural1, $struktural2) {
        $err = 0;
        $idpegawai = $record['idpegawai'];
        $a_hubkerja = self::getHubKerjaPegProsentase($conn);
        $isDosenLB = self::isDosenLB($conn, $idpegawai);

        if (!empty($nominal[$struktural])) {
            if ($isDosenLB)
                $nominal[$struktural] = 0;
            else
                $nominal[$struktural] = $nominal[$struktural] * ($a_hubkerja[$record['idpegawai']] / 100);

            $record['nominal'] = $nominal[$struktural];
            $record['idjstruktural'] = $struktural;

            if (!empty($record['nominal']))
                $err = self::insertRecord($conn, $record, false, 'ga_tunjanganpeg');
        }

        $conn->Execute("delete from " . static::table('ga_tunjanganstrukturallain') . " 
						where idpegawai =" . $record['idpegawai'] . " and periodegaji ='" . $record['periodegaji'] . "' and kodetunjangan='" . $record['kodetunjangan'] . "'");
        

        if (!empty($nominal[$struktural1])) {
            if ($isDosenLB)
                $nominal[$struktural1] = 0;
            else
                $nominal[$struktural1] = $nominal[$struktural1] * ($a_hubkerja[$record['idpegawai']] / 100);

            $record['nominal'] = $nominal[$struktural1];
            $record['idjstruktural'] = $struktural1;

            if (!empty($record['nominal']))
                $err = self::insertRecord($conn, $record, false, 'ga_tunjanganstrukturallain');
        }

        if (!empty($nominal[$struktural2])) {
            if ($isDosenLB)
                $nominal[$struktural2] = 0;
            else
                $nominal[$struktural2] = $nominal[$struktural2] * ($a_hubkerja[$record['idpegawai']] / 100);

            $record['nominal'] = $nominal[$struktural2];
            $record['idjstruktural'] = $struktural2;

            if (!empty($record['nominal']))
                $err = self::insertRecord($conn, $record, false, 'ga_tunjanganstrukturallain');
        }

        return $err;
    }

    function isBayarTunj($conn, $r_periode, $r_periodetarif) {
        $sql = "select idpegawai,kodetunjangan 
					from " . static::table('ga_tunjanganpeg') . " 
					where periodegaji = '$r_periode' and periodetarif = '$r_periodetarif' and isdibayar = 'Y'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_byr[$row['idpegawai']][$row['kodetunjangan']] = $row['kodetunjangan'];
        }

        return $a_byr;
    }

    function listQueryTunjLain() {
        $sql = "select g.*, p.nip, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					t.namatunjangan
					from " . static::table('ga_pegawaitunjangan') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where t.carahitung = 'M' and t.isbayargaji = 'Y' and t.isaktif = 'Y'";

        return $sql;
    }

    function getDataEditTunjLain($r_key) {
        $sql = "select g.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
					from " . static::table('ga_pegawaitunjangan') . " g
					left join " . static::table('ms_pegawai') . " m on m.idpegawai=g.idpegawai
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where t.carahitung = 'M' and t.isbayargaji = 'Y' and t.isaktif = 'Y' and g.notunjangan=$r_key";

        return $sql;
    }

    function listQueryTunjTarifParam() {
        $sql = "select g.*, p.nip, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					t.namatunjangan
					from " . static::table('ga_pegawaitunjangan') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where t.carahitung in ('P','M') and t.isbayargaji = 'T' and t.isaktif = 'Y'";

        return $sql;
    }

    function getDataEditTunjTarifParam($r_key) {
        $sql = "select g.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
					from " . static::table('ga_pegawaitunjangan') . " g
					left join " . static::table('ms_pegawai') . " m on m.idpegawai=g.idpegawai
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where t.carahitung in ('P','M') and t.isbayargaji = 'T' and t.isaktif = 'Y' and g.notunjangan = $r_key";

        return $sql;
    }

    function getTahunTunjLain($conn) {
        $sql = "select date_part('year', t.tmtmulai) as tahun 
					from " . static::table('ga_pegawaitunjangan') . " t
					left join " . static::table('ms_tunjangan') . " m on m.kodetunjangan = t.kodetunjangan
					where m.carahitung = 'M' and m.isbayargaji = 'Y' and m.isaktif = 'Y'
					group by date_part('year', tmtmulai) 
					order by date_part('year', tmtmulai) desc";
        $rs = $conn->Execute($sql);

        $a_data = array();
        $a_add = array('all' => '-- Semua Tahun --');
        $a_data = array_merge($a_data, $a_add);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['tahun']] = $row['tahun'];
        }


        return $a_data;
    }

    function getLastTahunTunjLain($conn) {
        $sql = "select  date_part('year', tmtmulai) as tahun 
					from " . static::table('ga_pegawaitunjangan') . " t
					left join " . static::table('ms_tunjangan') . " m on m.kodetunjangan = t.kodetunjangan
					where m.carahitung = 'M' and m.isbayargaji = 'Y' and m.isaktif = 'Y'
					group by date_part('year', tmtmulai) 
					order by date_part('year', tmtmulai) desc limit 1";
        $tahun = $conn->GetOne($sql);

        return $tahun;
    }

    function getTahunTunjTarifParam($conn) {
        $sql = "select date_part('year', t.tmtmulai) as tahun 
					from " . static::table('ga_pegawaitunjangan') . " t
					left join " . static::table('ms_tunjangan') . " m on m.kodetunjangan = t.kodetunjangan
					where m.carahitung in ('P','M') and m.isbayargaji = 'T' and m.isaktif = 'Y'
					group by date_part('year', tmtmulai) 
					order by date_part('year', tmtmulai) desc";
        $rs = $conn->Execute($sql);

        $a_data = array();
        $a_add = array('all' => '-- Semua Tahun --');
        $a_data = array_merge($a_data, $a_add);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['tahun']] = $row['tahun'];
        }


        return $a_data;
    }

    function getLastTahunTunjTarifParam($conn) {
        $sql = "select date_part('year', t.tmtmulai) as tahun 
					from " . static::table('ga_pegawaitunjangan') . " t
					left join " . static::table('ms_tunjangan') . " m on m.kodetunjangan = t.kodetunjangan
					where m.carahitung = 'P' and m.isbayargaji = 'T' and m.isaktif = 'Y'
					group by date_part('year', tmtmulai) 
					order by date_part('year', tmtmulai) desc limit 1";
        $tahun = $conn->GetOne($sql);

        return $tahun;
    }

    function getTunjTetapSlip($conn, $key) {
        list($periode, $idpegawai) = explode('|', $key);

        $sql = "select g.* from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$periode' and t.isgajitetap = 'Y' and t.isbayargaji = 'Y' and t.isaktif = 'Y'";
        if (!empty($idpegawai))
            $sql .= " and g.idpegawai = $idpegawai";

        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunj[$row['idpegawai']][$row['kodetunjangan']] = $row['nominal'];
        }

        return $a_tunj;
    }

    function getTunjTetapStrukLain($conn, $key) {
        list($periode, $idpegawai) = explode('|', $key);

        $sql = "select g.* from " . static::table('ga_tunjanganstrukturallain') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$periode' and t.isgajitetap = 'Y' and t.isbayargaji = 'Y' and t.isaktif = 'Y'";
        if (!empty($idpegawai))
            $sql .= " and g.idpegawai = $idpegawai";

        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunjstruklain[$row['idpegawai']][$row['kodetunjangan']][$row['idjstruktural']] = $row['nominal'];
        }

        return $a_tunjstruklain;
    }

    function getTunjAwalSlip($conn, $key) {
        list($periode, $idpegawai) = explode('|', $key);

        $sql = "select g.* from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$periode' and t.isbayargaji = 'T' and t.isaktif = 'Y'";
        if (!empty($idpegawai))
            $sql .= " and g.idpegawai = $idpegawai";

        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunja[$row['idpegawai']][$row['kodetunjangan']] = $row['nominal'];
        }

        return $a_tunja;
    }

    function getTunjPenyesuaianSlip($conn, $key) {
        list($periode, $idpegawai) = explode('|', $key);

        $sql = "select g.* from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$periode' and t.isbayargaji = 'Y' and (t.isgajitetap = 'N' or t.isgajitetap is null) and t.isaktif = 'Y'";
        if (!empty($idpegawai))
            $sql .= " and g.idpegawai = $idpegawai";

        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunj[$row['idpegawai']][$row['kodetunjangan']] = $row['nominal'];
        }

        return $a_tunj;
    }

    function getProsentasePejabat($conn) {
        $sql = "select idjnspejabat,proctunjangan from " . static::table('ms_jenispejabat') . " order by idjnspejabat";

        return Query::arrQuery($conn, $sql);
    }

    function getInfoStruktural($conn) {
        $sql = "select idjstruktural,jabatanstruktural from " . static::table('ms_struktural') . " order by idjstruktural";

        return Query::arrQuery($conn, $sql);
    }

    //********************************************P A J A K****************************************************

    function listQueryPajak() {
        $sql = "select * from " . static::table('ms_pajak');

        return $sql;
    }

    function getListPajakDet($conn, $r_key) {
        $sql = "select * from " . static::table('ms_pajakdet') . " where idpajak = '$r_key'";
        $rs = $conn->Execute($sql);

        return $rs;
    }

    function getPajak($conn) {
        $sql = "select * from " . static::table('ms_pajak') . " where isaktif = 'Y'";
        $row = $conn->GetRow($sql);

        if (!empty($row))
            return array(true, $row);
        else
            return array(false, $row);
    }

    function getPajakDet($conn, $r_key) {
        $sql = "select * from " . static::table('ms_pajakdet') . " where idpajak = '$r_key' order by batasbawah";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $b_atas = empty($row['batasatas']) ? '-' : $row['batasatas'];
            $a_bts[$b_atas] = $row['prosentase'] / 100;
        }

        if (!empty($a_bts))
            return array(true, $a_bts);
        else
            return array(false, $a_bts);
    }

    function hitPajak($conn, $r_periode, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }
		
		//tarik data
		self::tarikData($conn, $r_periode,'', $a_peg);

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select g.*,gh.*
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ga_historydatagaji') . " gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					where (g.istunda = 'T' or g.istunda is null) and g.periodegaji = '$r_periode'";
        if (!empty($a_peg))
            $sql .= " and g.idpegawai in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and g.idpegawai not in ($b_peg)";

        $rs = $conn->Execute($sql);

        list($isset, $pjk) = self::getPajak($conn);
        if ($isset)
            list($isset, $pjkd) = self::getPajakDet($conn, $pjk['idpajak']);

        //potongan pajak
        $potpajak = self::getPotPajak($conn, $r_periode);

        if ($isset) {
            while ($row = $rs->FetchRow()) {
                $g_bruto = $row['gajibruto'];

                //dikurangi dengan potongan pajak
                if (!empty($potpajak[$row['idpegawai']]))
                    $g_bruto = $g_bruto - $potpajak[$row['idpegawai']];

                //mendapatkan biaya jabatan
                $biaya_jbt = $pjk['prosentasepotongan'] / 100 * $g_bruto;
                if ($biaya_jbt > $pjk['maxpotongan'])
                    $biaya_jbt = $pjk['maxpotongan'];

                //mendapatkan premi pensiun
                $p_pensiun = 0;
                /* $p_pensiun = $row['premipensiun'];
                  if(!empty($pen[$row['nip']]))
                  $p_pensiun = $p_pensiun + $pen[$row['nip']]; */

                $pengurangan = $biaya_jbt + $p_pensiun;

                $g_netto = $g_bruto - $pengurangan;

                $pengali = 12;
                $g_netto_th = $g_netto * $pengali; //gaji netto disetahunkan jika bukan akhir dan masa kerja pegawai sudah lebih dari setahun
                //perhitungan penghasilan tidak kena pajak (ptkp)
                $sendiri = $pjk['ptkppribadi'];
                if ($row['statusnikah'] == 'N')
                    $menipah = $pjk['ptkpkawin'];
                else
                    $menipah = 0;

                if ($row['jmlanak'] > $pjk['maxanak'])
                    $anak = $pjk['maxanak'] * $pjk['ptkpanak'];
                else
                    $anak = $row['jmlanak'] * $pjk['ptkpanak'];

                if ($row['jeniskelamin'] == 'P' and $row['statusnikah'] == 'N' and $row['ispasangankerja'] == 'Y')//wanita, yang punya suami bekerja dihitung seperti sendiri
                    $ptkp = $sendiri + $anak;
                else
                    $ptkp = $sendiri + $menipah + $anak;

                //pengurangan netto setahun dengan total PTKP
                $pkp_th = $g_netto_th - $ptkp;

                $pph_th = 0;
                $tpkp_th = $pkp_th; // menyimpan pkp pada proses perpajakan
				
                //menghitung pph
				$ip = 0;
                foreach ($pjkd as $t_limit => $t_persen) {
                    if ($t_limit != '-' and $tpkp_th > $t_limit) {
                        $pph_th += ($t_persen * $t_limit);
                        $tpkp_th -= $t_limit;
                    } else {
                        $pph_th += ($t_persen * $tpkp_th);
                        break;
                    }
					
					if($ip == 0)
						$record['procpph'] = $t_persen;
					$ip++;
                }

                $pph = $pph_th / $pengali;
                if ($pph <= 0)
                    $pph = 0;

                $record = array();
                $record['pph'] = $pph;

                list($err, $msg) = self::saveGaji($conn, $record, $r_periode, $row['idpegawai']);
                if ($err)
                    $msg = 'Perhitungan pajak gagal';
                else
                    $msg = 'Perhitungan pajak berhasil';
            }
        }else {
            $err = true;
            $msg = 'Silahkan setting Pajak terlebih dahulu';
        }

        return array($err, $msg);
    }

    //Bayar gaji
    function bayarGaji($conn, $r_periode, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select g.idpegawai,g.periodegaji,gh.norekening from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ga_historydatagaji') . " gh on gh.gajiperiode = g.periodegaji and gh.idpeg = g.idpegawai
					where g.periodegaji = '$r_periode' and (g.istunda = 'T' or g.istunda is null)";
        if (!empty($a_peg))
            $sql .= " and g.idpegawai in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and g.idpegawai not in ($b_peg)";

        $rs = $conn->Execute($sql);

        $record = array();
        $record['isfinish'] = 'Y';
        $record['tgldibayarkan'] = date('Y-m-d');

        while ($row = $rs->FetchRow()) {
            if (!empty($row['norekening']))
                $record['istransfer'] = 'Y';
            else
                $record['istransfer'] = 'T';

            $key = $row['idpegawai'] . '|' . $row['periodegaji'];
            $colkey = 'idpegawai,periodegaji';
            list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_gajipeg', $colkey);
        }

        list($err, $msg) = self::updateStatus($conn);

        if (!$err) {
            self::bayarPinjaman($conn, $r_periode);
        }

        return array($err, $msg);
    }

    //Tunda Bayar gaji
    function tundaBayarGaji($conn, $r_periode, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        $sql = "select g.idpegawai,g.periodegaji,gh.norekening from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ga_historydatagaji') . " gh on gh.gajiperiode = g.periodegaji and gh.idpeg = g.idpegawai
					where g.periodegaji = '$r_periode' and g.isfinish = 'Y'";
        if (!empty($a_peg))
            $sql .= " and g.idpegawai in ($a_peg)";

        $rs = $conn->Execute($sql);

        $record = array();
        $record['isfinish'] = 'T';
        $record['istransfer'] = 'null';
        $record['tgldibayarkan'] = 'null';

        while ($row = $rs->FetchRow()) {
            $key = $row['idpegawai'] . '|' . $row['periodegaji'];
            $colkey = 'idpegawai,periodegaji';
            list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_gajipeg', $colkey);
        }

        list($err, $msg) = self::updateStatus($conn);

        if (!$err) {
            list($err, $msg) = self::tundaBayarPinjaman($conn, $r_periode);
        }

        return array($err, $msg);
    }

    //Bayar tunjangan awal terlebih dahulu
    function bayarTunjangan($conn, $r_periode, $r_sql = '') {
        //periode tarif sekarang
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        //nip yang diselect		
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select g.idpegawai,g.kodetunjangan from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan = g.kodetunjangan
					where g.periodegaji = '$r_periode' and g.periodetarif = '$r_periodetarif' and t.isbayargaji = 'T' and t.isaktif = 'Y'
					and (g.isdibayar = 'T' or g.isdibayar is null)";
        if (!empty($a_peg))
            $sql .= " and g.idpegawai in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and g.idpegawai not in ($b_peg)";

        $rs = $conn->Execute($sql);

        $record = array();
        $record['isdibayar'] = 'Y';
        $record['tglbayar'] = date('Y-m-d');

        $recgaji = array();
        $recgaji['isbayarawal'] = 'Y';

        while ($row = $rs->FetchRow()) {
            $key = $row['idpegawai'] . '|' . $r_periode . '|' . $r_periodetarif . '|' . $row['kodetunjangan'];
            $colkey = 'idpegawai,periodegaji,periodetarif,kodetunjangan';

            list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_tunjanganpeg', $colkey);

            if (!$err) {
                $keygaji = $row['idpegawai'] . '|' . $r_periode;
                $colkeygaji = 'idpegawai,periodegaji';
                list($err, $msg) = self::updateRecord($conn, $recgaji, $keygaji, true, 'ga_gajipeg', $colkeygaji);
            }
        }

        list($err, $msg) = self::updateStatus($conn);

        return array($err, $msg);
    }

    //Tunda bayar tunjangan awal terlebih dahulu
    function tundaBayarTunjangan($conn, $r_periode, $r_sql = '') {
        //periode tarif sekarang
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        //nip yang diselect		
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        $sql = "select g.idpegawai,g.kodetunjangan from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan = g.kodetunjangan
					where g.periodegaji = '$r_periode' and g.periodetarif = '$r_periodetarif' and t.isbayargaji = 'T' and t.isaktif = 'Y' and g.isdibayar = 'Y'";
        if (!empty($a_peg))
            $sql .= " and g.idpegawai in ($a_peg)";

        $rs = $conn->Execute($sql);

        $record = array();
        $record['isdibayar'] = 'T';
        $record['tglbayar'] = 'null';

        $recgaji = array();
        $recgaji['isbayarawal'] = 'T';
        while ($row = $rs->FetchRow()) {
            $key = $row['idpegawai'] . '|' . $r_periode . '|' . $r_periodetarif . '|' . $row['kodetunjangan'];
            $colkey = 'idpegawai,periodegaji,periodetarif,kodetunjangan';

            list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_tunjanganpeg', $colkey);

            if (!$err) {
                $keygaji = $row['idpegawai'] . '|' . $r_periode;
                $colkeygaji = 'idpegawai,periodegaji';
                list($err, $msg) = self::updateRecord($conn, $recgaji, $keygaji, true, 'ga_gajipeg', $colkeygaji);
            }
        }

        list($err, $msg) = self::updateStatus($conn);

        return array($err, $msg);
    }

    // mendapatkan potongan kueri filter list
    function getListFilter($col, $key) {
        switch ($col) {
            case 'unit':
                global $conn, $conf;
                require_once($conf['gate_dir'] . 'model/m_unit.php');

                $row = mUnit::getData($conn, $key);

                return "u.infoleft >= " . (int) $row['infoleft'] . " and u.inforight <= " . (int) $row['inforight'];
                break;
            case 'tahun':
                if ($key != 'all')
                    return "date_part('year', tmtmulai) = '$key'";
                else
                    return "(1=1)";

                break;
            case 'tunjangan':
                if ($key != 'all')
                    return "g.kodetunjangan = '$key'";
                else
                    return "(1=1)";

                break;
            case 'tahunpot':
                if ($key != 'all')
                    return "date_part('year', g.tglmulai) = '$key'";
                else
                    return "(1=1)";

                break;
            case 'potonganparam':
                if ($key != 'all')
                    return "g.kodepotongan = '$key'";
                else
                    return "(1=1)";

                break;
            case 'jenispegawai':
                if ($key != 'all')
                    return "p.idjenispegawai = '$key'";
                else
                    return "(1=1)";

                break;
            case 'periodegaji':
                return "g.periodegaji = '$key'";
                break;
            case 'periodetarif':
                return "g.periodetarif = '$key'";
                break;
            case 'golongan':
                return "g.idpangkat = '$key'";
                break;
            case 'jnstunjangan':
                return "g.kodetunjangan = '$key'";
                break;
            case 'periodehist':
                return "g.gajiperiode = '$key'";
                break;
            case 'hubungankerja':
                if ($key != 'all')
                    return "p.idhubkerja = '$key'";
                else
                    return "(1=1)";
                
                break;
            case 'tunda':
                if (!empty($key)) {
                    if ($key == 'Y')
                        return "g.istunda = 'Y'";
                    else
                        return "(g.istunda = 'T' or g.istunda is null)";
                }
                break;
            case 'bayar':
                if (!empty($key)) {
                    if ($key == 'Y')
                        return "g.isfinish = 'Y'";
                    else
                        return "(g.isfinish = 'T' or g.isfinish is null)";
                }
                break;
            case 'bayartunj':
                if (!empty($key)) {
                    if ($key == 'Y')
                        return "g.isdibayar = 'Y'";
                    else
                        return "(g.isdibayar = 'T' or g.isdibayar is null)";
                }
                break;
            case 'bayarlembur':
                if (!empty($key)) {
                    if ($key == 'Y')
                        return "g.isbayar = 'Y'";
                    else
                        return "(g.isbayar = 'T' or g.isbayar is null)";
                }
                break;
            case 'periodepenilaian':
                return "kodeperiode='$key'";
                break;
            case 'pendidikan':
                return "idpendidikan='$key'";
                break;
            case 'tipepegawai':
                if ($key != 'all')
                    return "p.idtipepeg = '$key'";
                else
                    return "(1=1)";

                break;
        }
    }

    function filterJenis($conn) {
        $sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from " . static::table('ms_jenispeg') . " j
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";

        return Query::arrQuery($conn, $sql);
    }

    function filterJenisPeg($conn, $jenis) {
        $sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from " . static::table('ms_jenispeg') . " j
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg=j.idtipepeg
					where t.idtipepeg in ('$jenis')
					order by j.idtipepeg";

        return Query::arrQuery($conn, $sql);
    }

    function filterJenisDosen($conn) {
        $sql = "select idjenispegawai, tipepeg || ' - ' || jenispegawai from " . static::table('ms_jenispeg') . " j
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg=j.idtipepeg
					order by j.idtipepeg";

        return Query::arrQuery($conn, $sql);
    }

    //Daftar penarikan data
    function listQueryHistoryGaji() {
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,s.jabatanstruktural,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,
					substring(g.masakerja,1,2)||' tahun ' || substring(g.masakerja,3,2)||' bulan' as mkgaji
					from " . static::table('ga_historydatagaji') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpeg
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = g.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = g.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = g.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = g.pendidikan
					left join " . static::table('ms_struktural') . " s on s.idjstruktural = g.struktural";

        return $sql;
    }

    function listQueryHistoryGajiPerPegawai() {
        $a_peg = self::getPegawaiGaji('TARIKDATA');

        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,s.jabatanstruktural,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,
					substring(g.masakerja,1,2)||' tahun ' || substring(g.masakerja,3,2)||' bulan' as mkgaji
					from " . static::table('ga_historydatagaji') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpeg
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = g.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = g.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = g.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = g.pendidikan
					left join " . static::table('ms_struktural') . " s on s.idjstruktural = g.struktural";

        if (!empty($a_peg))
            $sql .= " where g.idpeg in ($a_peg)";
        else
            $sql .= " where 1=0";

        return $sql;
    }

    function getDataHistoryGaji($conn, $r_key) {
        list($idpegawai, $periodegaji) = explode('|', $r_key);

        $sql = "select g.*,pg.namaperiode as namaperiodegaji,pt.namaperiode as namaperiodetarif,
					nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pk.golongan,js.jabatanstruktural,ms.jenispejabat,pd.namapendidikan,f.jabatanfungsional,t.tipepeg||' - '||j.jenispegawai as namajenispegawai,
					h.hubkerja,s.namastatusaktif,case when g.statusnikah = 'S' then 'Single' when g.statusnikah = 'N' then 'nipah' 
					when g.statusnikah = 'D' then 'Duda' when g.statusnikah = 'J' then 'Janda' end as statusnipah,
					case when g.ispasangankerja = 'Y' then 'Ya' else 'Tidak' end as pasangankerja,
					substring(g.masakerja,1,2)||' tahun ' || substring(g.masakerja,3,2)||' bulan' as masakerja,
					case when g.jeniskelamin = 'L' then 'Laki-laki' when g.jeniskelamin = 'P' then 'Perempuan' else '' end  as jnskelamin
					from " . static::table('ga_historydatagaji') . " g
					left join " . static::table('ga_periodegaji') . " pg on pg.periodegaji = g.gajiperiode
					left join " . static::table('ms_periodetarif') . " pt on pt.periodetarif = g.tarifperiode
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpeg
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = g.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = g.idjenispegawai
					left join " . static::table('ms_hubkerja') . " h on h.idhubkerja = g.idhubkerja
					left join " . static::table('lv_statusaktif') . " s on s.idstatusaktif = g.idstatusaktif
					left join " . static::table('ms_unit') . " u on u.idunit = g.idunit
					left join " . static::table('ms_pangkat') . " pk on pk.idpangkat = g.pangkatpeg
					left join " . static::table('ms_fungsional') . " f on f.idjfungsional = g.fungsional
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = g.pendidikan
					left join " . static::table('ms_struktural') . " js on js.idjstruktural = g.struktural
					left join " . static::table('ms_jenispejabat') . " ms on ms.idjnspejabat = g.jnspejabat
					where idpeg = $idpegawai and gajiperiode = '$periodegaji'";

        $row = $conn->GetRow($sql);

        return $row;
    }

    function saveStrukturalGaji($conn, $a_strukturalgaji, $key, $colkey) {
        $u_strukturalgaji = array_unique($a_strukturalgaji);

        $record = array();
        foreach ($u_strukturalgaji as $no => $idjstruktural) {
            $record['strukturalgaji' . $no] = $idjstruktural;
        }

        $sql = "update sdm.ga_historydatagaji set strukturalgaji1=null, strukturalgaji2=null where " . self::getCondition($key, $colkey) . "";
        $conn->Execute($sql);

        list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_historydatagaji', $colkey);

        return array($err, $msg);
    }

    function getStrukturalPegawai($conn) {
        $sql = "select idpegawai,idjstruktural from " . static::table('pe_rwtstruktural') . "
						where isvalid = 'Y' and isaktif = 'Y' and isgaji = 'Y'";
        $rs = $conn->Execute($sql);


        $a_strukturalpeg = array();
        while ($row = $rs->FetchRow()) {
            $a_strukturalpeg[$row['idpegawai']][] = $row['idjstruktural'];
        }

        return $a_strukturalpeg;
    }

    function tarikData($conn, $r_periode, $r_unit = '', $r_idpegawai = '') {
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        if (!empty($r_unit)) {
            global $conn, $conf;
            require_once($conf['gate_dir'] . 'model/m_unit.php');

            $row = mUnit::getData($conn, $r_unit);

            $sqladd = " and u.infoleft >= " . (int) $row['infoleft'] . " and u.inforight <= " . (int) $row['inforight'];
        } else if (!empty($r_idpegawai))
            $sqladd = " and p.idpegawai in ($r_idpegawai)";

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::isBayarGajiTunj($conn, $r_periode);
        if (count($b_peg) > 0) {
            $i_peg = implode(',', $b_peg);
            $sqladd .= " and p.idpegawai not in ($i_peg)";
        }

        $a_strukturalpeg = self::getStrukturalPegawai($conn);

        $sql = "select p.idpegawai,p.jeniskelamin,p.statusnikah,p.jmlanak,p.npwp,p.idjstruktural,js.idjnspejabat,p.idpendidikan,p.idpangkat,p.idjfungsional,
					p.idtipepeg,p.idjenispegawai,p.idhubkerja,jk.jamkerja,p.ispasangankerja,p.idstatusaktif,p.idunitbase,p.nodosen,
					" . static::schema . ".get_mkpengabdiangaji(p.idpegawai,'$r_periode') as masakerja,p.idunit,p.norekening,p.anrekening,p.isoffmengajar
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_unit') . " u on u.idunit = p.idunit
					left join " . static::table('lv_statusaktif') . " a on a.idstatusaktif = p.idstatusaktif
					left join " . static::table('ms_hubkerja') . " h on h.idhubkerja = p.idhubkerja
					left join " . static::table('ms_kelompokdosen') . " jk on jk.kodekeldosen = p.kodekeldosen
					left join " . static::table('pe_rwtstruktural') . " js on js.nourutjs = (select jss.nourutjs from " . static::table('pe_rwtstruktural') . " jss
						where jss.idpegawai = p.idpegawai and jss.isvalid = 'Y' and jss.isaktif = 'Y' and isgaji = 'Y' order by coalesce(isutama,'T') desc,tmtmulai desc limit 1)
					where a.isdigaji = 'Y' and h.istarikgaji = 'Y' {$sqladd}";

        $rs = $conn->Execute($sql);

        $i = 0;
        while ($row = $rs->FetchRow()) {
            $i++;

            $record = array();
            $record['gajiperiode'] = $r_periode;
            $record['tarifperiode'] = $r_periodetarif;
            $record['idpeg'] = $row['idpegawai'];
            $record['jeniskelamin'] = $row['jeniskelamin'];
            $record['statusnikah'] = $row['statusnikah'];
            $record['ispasangankerja'] = $row['ispasangankerja'];
            $record['jmlanak'] = $row['jmlanak'];
            $record['npwp'] = $row['npwp'];
            $record['idtipepeg'] = $row['idtipepeg'];
            $record['idjenispegawai'] = $row['idjenispegawai'];
            $record['idhubkerja'] = $row['idhubkerja'];
            $record['idstatusaktif'] = $row['idstatusaktif'];
            $record['jamkerja'] = $row['jamkerja'];
            $record['struktural'] = $row['idjstruktural'];
            $record['jnspejabat'] = $row['idjnspejabat'];
            $record['pendidikan'] = $row['idpendidikan'];
            $record['pangkatpeg'] = $row['idpangkat'];
            $record['fungsional'] = $row['idjfungsional'];
            $record['idunit'] = $row['idunit'];
            $record['idunitbase'] = $row['idunitbase'];
            $record['masakerja'] = $row['masakerja'];
            $record['norekening'] = $row['norekening'];
            $record['anrekening'] = $row['anrekening'];
            $record['tgltarik'] = date('Y-m-d');
            $record['isoffmengajar'] = $row['isoffmengajar'];
            $record['nodosen'] = $row['nodosen'];

            $a_strukturalgaji = array();
            $no = 0;
            if (!empty($a_strukturalpeg[$row['idpegawai']])) {
                foreach ($a_strukturalpeg[$row['idpegawai']] as $strukturalpeg) {
                    if ($strukturalpeg != $row['idjstruktural']) {
                        $no++;
                        $a_strukturalgaji[$no] = $strukturalpeg;
                    }
                }
            }

            $isexist = $conn->GetOne("select 1 from " . static::table('ga_historydatagaji') . " where idpeg = " . $record['idpeg'] . " and gajiperiode = '" . $record['gajiperiode'] . "'");
            $key = $record['idpeg'] . '|' . $record['gajiperiode'];
            $colkey = 'idpeg,gajiperiode';

            if (empty($isexist))
                list($err, $msg) = self::insertRecord($conn, $record, true, 'ga_historydatagaji');
            else
                list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_historydatagaji', $colkey);

            if (!$err and ! empty($a_strukturalgaji))
                list($err, $msg) = self::saveStrukturalGaji($conn, $a_strukturalgaji, $key, $colkey);
        }

        if ($i == 0) {
            $err = 1;
            $msg = 'Gaji pegawai tersebut sudah dibayar';
        }

        return array($err, $msg);
    }

    //cek apakah sudah ditarik data pegawai
    function isTarikData($conn, $r_unit, $r_periode) {
        global $conn, $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $row = mUnit::getData($conn, $r_unit);

        $sql = "select count(*) from " . static::table('ga_historydatagaji') . " g
					left join " . static::table('ms_unit') . " u on u.idunit = g.idunit
					where gajiperiode = '$r_periode' and u.infoleft >= " . (int) $row['infoleft'] . " and u.inforight <= " . (int) $row['inforight'];
        $ntarik = $conn->GetOne($sql);

        $istarik = !empty($ntarik) ? true : false;
        return $istarik;
    }

    function deleteTarikData($conn, $r_key) {
        list($r_pegawai, $r_periode) = explode('|', $r_key);
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        $conn->Execute("delete from " . static::table('ga_tunjanganpeg') . " where idpegawai = $r_pegawai and periodegaji = '$r_periode' and periodetarif = '$r_periodetarif'");
        $conn->Execute("delete from " . static::table('ga_potongan') . " where idpegawai = $r_pegawai and periodegaji = '$r_periode'");
        $conn->Execute("delete from " . static::table('ga_gajipeg') . " where idpegawai = $r_pegawai and periodegaji = '$r_periode'");

        $conn->Execute("delete from " . static::table('ga_historydatagaji') . " where idpeg = $r_pegawai and gajiperiode = '$r_periode'");

        return self::deleteStatus($conn);
    }

    /* ---------------------------- DOSEN TIDAK BOLEH MENGAJAR ------------------------- */

    //Daftar dosen tidak boleh mengajar
    function listQueryDosenTdkMengajar() {
        $sql = "select p.nip,p.nodosen," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,j.namapendidikan,p.idpegawai,p.idpendidikan,p.idjfungsional,f.jabatanfungsional,p.isoffmengajar
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('lv_jenjangpendidikan') . " j on j.idpendidikan=p.idpendidikan
					left join " . static::table('ms_fungsional') . " f on f.idjfungsional=p.idjfungsional
					left join " . static::table('ms_unit') . " u on p.idunitbase=u.idunit
					where (p.idtipepeg = 'D' or p.nodosen is not null)";

        return $sql;
    }

    /* ------------------------------------------------------------------------------------ */

    function listQueryGajiTetap() {
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(gh.masakerja,1,2)||' tahun ' || substring(gh.masakerja,3,2)||' bulan' as mkgaji,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpegawai
					left join " . static::table('ga_historydatagaji') . " gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = gh.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = gh.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = gh.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = gh.pendidikan";

        return $sql;
    }

    function listQueryGajiTetapPerPegawai() {
        $a_peg = self::getPegawaiGaji('GAJITETAP');

        $sql = "select gp.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(g.masakerja,1,2)||' tahun ' || substring(g.masakerja,3,2)||' bulan' as mkgaji,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from " . static::table('ga_historydatagaji') . " g
					left join " . static::table('ga_gajipeg') . " gp on gp.idpegawai = g.idpeg and gp.periodegaji = g.gajiperiode
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpeg
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = g.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = g.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = g.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = g.pendidikan";

        if (!empty($a_peg))
            $sql .= " where g.idpeg in ($a_peg)";
        else
            $sql .= " where 1=0";

        return $sql;
    }

    function cekGajiPegawai($conn, $r_periode, $r_pegawai) {
        $sql = "select idpeg from " . static::table('ga_historydatagaji') . " where idpeg = $r_pegawai and gajiperiode = '$r_periode'";
        $idpegawai = $conn->GetOne($sql);

        return $idpegawai;
    }

    function cekTarikPegawai($conn, $r_pegawai) {
        list($err, $msg) = self::delete($conn, $r_pegawai, 'ga_historydatagaji', 'idpeg');

        $sql = "select p.idpegawai from " . static::table('ms_pegawai') . " p
					left join " . static::table('lv_statusaktif') . " a on a.idstatusaktif = p.idstatusaktif
					left join " . static::table('ms_hubkerja') . " h on h.idhubkerja = p.idhubkerja  
					where a.isdigaji = 'Y' and h.istarikgaji='Y' and idpegawai = $r_pegawai";

        $idpegawai = $conn->GetOne($sql);

        return $idpegawai;
    }

    function setPegawaiGajiTetap($idpegawai, $key) {
        $isEx = $_SESSION[SITE_ID]['VAR'][$key];
        $isEx = $_SESSION[SITE_ID]['VAR'][$key];
        if (!empty($isEx)) {
            $a_id = array();
            $a_id = explode(',', $_SESSION[SITE_ID]['VAR'][$key]);
            if (!in_array($idpegawai, $a_id))
                $_SESSION[SITE_ID]['VAR'][$key] .= ',' . $idpegawai;
        } else
            $_SESSION[SITE_ID]['VAR'][$key] = $idpegawai;
    }

    function getPegawaiGaji($key) {
        return $_SESSION[SITE_ID]['VAR'][$key];
    }

    function unsetPegawaiGaji($key, $idpegawai) {
        $isEx = $_SESSION[SITE_ID]['VAR'][$key];
        if (!empty($isEx)) {
            $a_id = array();
            $a_id = explode(',', $isEx);
            array_splice($a_id, array_search($idpegawai, $a_id), 1);
            $_SESSION[SITE_ID]['VAR'][$key] = implode(',', $a_id);
        }
    }

    function hitGajiTdkTetap($conn, $r_periode, $r_sql = '', $isall = false) {
        if (!$isall)
            $a_peg = self::getPegawaiGaji('GAJITETAP');
        else {
            if (!empty($r_sql)) {
                $a_peg = self::pegFilter($conn, $r_sql);
            }
        }
		
		//tarik data
		self::tarikData($conn, $r_periode,'', $a_peg);

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select * from " . static::table('ga_historydatagaji') . "
					where gajiperiode = '$r_periode'";
        if (!empty($a_peg))
            $sql .= " and idpeg in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and idpeg not in ($b_peg)";

        $rs = $conn->Execute($sql);

        //periode tarif sekarang
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        //Jenis tunjangan tidak tetap			
        $a_tunjt = self::tunjanganTdkTetap($conn);

        //detail tunjangan tidak tetap
        $a_tunjtdet = self::tunjanganTdkTetapDet($conn);

        //tarif tunjangan lain
        $a_tunjlain = self::getTunjLain($conn);

        //Komponen T. Sabtu Minggu
        list($a_shiftsab, $a_shiftming) = self::getShiftSabtuMinggu($conn);
        list($a_realsab, $a_realming) = self::getRealSabtuMinggu($conn);

        //tunjangan yang dikalikan jam kerja
        $a_tunjjamkerja = self::isTunjJamKerja($conn);

        //cek apakah tunjangan sudah dibayar
        $a_byr = self::isBayarTunj($conn, $r_periode, $r_periodetarif);

        //tunjangan dikalikan hari kerja
        $a_tunjharikerja = self::isKaliHariKerja($conn);

        //mendapatkan jumlah hari kerja
        require_once(Route::getModelPath('presensi'));
        $jmlharikerja = mPresensi::getHariKerja($conn);

        while ($row = $rs->FetchRow()) {
            //Dosen dan Administrasi dosen yang punya jam kerja
            $jamkerja = 1;
            if (($row['idjenispegawai'] == 'D' or $row['idjenispegawai'] == 'AD') and ! empty($row['jamkerja']))
                $jamkerja = $row['jamkerja'] / 40;

            for ($i = 0; $i < count($a_tunjt); $i++) {

                if ($a_byr[$row['idpeg']][$a_tunjt[$i]] != $a_tunjt[$i]) {
                    $key = $r_periode . '|' . $r_periodetarif . '|' . $row['idpeg'] . '|' . $a_tunjt[$i];
                    $colkey = 'periodegaji,periodetarif,idpegawai,kodetunjangan';

                    list($err, $msg) = self::delete($conn, $key, 'ga_tunjanganpeg', $colkey);

                    //Tunjangan Penyesuaiaan
                    if (!$err and $a_tunjt[$i] == 'T00007' and in_array($row['idjenispegawai'], $a_tunjtdet[$a_tunjt[$i]]) and ! empty($a_tunjlain[$a_tunjt[$i]][$row['idpeg']])) {
                        $tarif = $a_tunjlain[$a_tunjt[$i]][$row['idpeg']];
                        $nominal = $tarif;

                        $rectunj = array();
                        $rectunj['periodegaji'] = $r_periode;
                        $rectunj['periodetarif'] = $r_periodetarif;
                        $rectunj['idpegawai'] = $row['idpeg'];
                        $rectunj['kodetunjangan'] = $a_tunjt[$i];
                        $rectunj['nominal'] = $nominal;

                        if (in_array($a_tunjt[$i], $a_tunjjamkerja))
                            $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                        if (in_array($a_tunj[$i], $a_tunjharikerja))
                            $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                        list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                    }

                    //Tunjangan Khusus
                    else if (!$err and $a_tunjt[$i] == 'T00014' and in_array($row['idjenispegawai'], $a_tunjtdet[$a_tunjt[$i]]) and ! empty($a_tunjlain[$a_tunjt[$i]][$row['idpeg']])) {
                        $tarif = $a_tunjlain[$a_tunjt[$i]][$row['idpeg']];
                        $nominal = $tarif;

                        $rectunj = array();
                        $rectunj['periodegaji'] = $r_periode;
                        $rectunj['periodetarif'] = $r_periodetarif;
                        $rectunj['idpegawai'] = $row['idpeg'];
                        $rectunj['kodetunjangan'] = $a_tunjt[$i];
                        $rectunj['nominal'] = $nominal;

                        if (in_array($a_tunjt[$i], $a_tunjjamkerja))
                            $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                        if (in_array($a_tunjt[$i], $a_tunjharikerja))
                            $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                        list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                    }

                    //Tunjangan Sabtu/ Ahad
                    else if (!$err and $a_tunjt[$i] == 'T00006' and in_array($row['idjenispegawai'], $a_tunjtdet[$a_tunjt[$i]]) and (in_array($row['idpeg'],$a_shiftsab) or in_array($row['idpeg'],$a_shiftming))) {
                        //tunjangan kehadiaran dan struktural
                        $jmlsh = self::jmlTunjSabtuAhad($conn, $r_periode, $r_periodetarif, $row['idpeg']);
						
						//gapok
                        $gapok = self::getGapokPeg($conn,$r_periode,$row['idpeg']);

                        /**
                         * @param decimal $prosentasesab prosentase sabtu 
                         */
                        $prosentasesab = 0;
                        if (in_array($row['idpeg'],$a_shiftsab))
                            $prosentasesab = 10 / 100;

                        /**
                         * @param decimal $prosentaseming prosentase minggu 
                         */
                        $prosentaseming = 0;
                        if (in_array($row['idpeg'],$a_shiftming))
                            $prosentaseming = 20 / 100;

                        $nominalsabtu = $prosentasesab * ($jmlsh + $gapok);
                        if (empty($nominalsabtu))
                            $nominalsabtu = 0;

                        $nominalminggu = $prosentaseming * ($jmlsh + $gapok);
                        if (empty($nominalminggu))
                            $nominalminggu = 0;

                        $nominal = $nominalsabtu + $nominalminggu;
						
                        $rectunj = array();
                        $rectunj['periodegaji'] = $r_periode;
                        $rectunj['periodetarif'] = $r_periodetarif;
                        $rectunj['idpegawai'] = $row['idpeg'];
                        $rectunj['kodetunjangan'] = $a_tunjt[$i];
                        $rectunj['nominal'] = $nominal;

                        if (in_array($a_tunjt[$i], $a_tunjjamkerja))
                            $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                        if (in_array($a_tunjt[$i], $a_tunjharikerja))
                            $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                        list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                    }
                }
            }
        }

        if ($err)
            $msg = 'Penyimpanan gaji gagal';
        else
            $msg = 'Penyimpanan gaji berhasil';

        return array($err, $msg);
    }

    /**
     * 
     * @param type $conn
     * @param type $r_periode
     * @param type $r_sql
     * @return type
     */
    function hitGajiAwal($conn, $r_periode, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }
		
		//tarik data
		self::tarikData($conn, $r_periode,'', $a_peg);
        
        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select * from " . static::table('ga_historydatagaji') . "
					where gajiperiode = '$r_periode'";
        if (!empty($a_peg))
            $sql .= " and idpeg in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and idpeg not in ($b_peg)";

        $rs = $conn->Execute($sql);

        //Jenis tunjangan awal
        $a_tunja = self::tunjanganAwal($conn);
        $a_tunjadet = self::tunjanganAwalDet($conn);

        //Komponen T. Prestasi
        $a_hpa = self::getHasilPA($conn);
        $a_prp = self::getProcPrestasi($conn);
        $a_prs = self::getProcSanksi($conn);
        $a_npn = self::getNonPenyesuaianPA($conn);

        //tarif tunjangan
        $a_tarifTunj = self::getTarifTunj($conn);

        //periode tarif sekarang
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        //tarif gaji pokok admin
        $a_gapokadmin = self::getTarifGapokAdmin($conn, $r_periodetarif);

        //tarif gaji pokok dosen
        $a_gapokdosen = self::getTarifGapokDosen($conn, $r_periodetarif);

        //pegawai yang dapat tarif tunjangan tarif parameter
        $a_tunjtarifparam = self::getTunjTarifParam($conn);

        //tunjangan yang dikalikan jam kerja
        $a_tunjjamkerja = self::isTunjJamKerja($conn);

        //cek apakah tunjangan sudah dibayar
        $a_byr = self::isBayarTunj($conn, $r_periode, $r_periodetarif);

        //tunjangan dikalikan hari kerja
        $a_tunjharikerja = self::isKaliHariKerja($conn);

        //tunjangan prestasi
        $a_tunjpres = self::getPrestasi($conn);

        //mendapatkan jumlah hari kerja
        require_once(Route::getModelPath('presensi'));
        $jmlharikerja = mPresensi::getHariKerja($conn);

        while ($row = $rs->FetchRow()) {
            //Dosen dan Administrasi dosen yang punya jam kerja
            $jamkerja = 1;
            if (($row['idtipepeg'] == 'D' or $row['idtipepeg'] == 'AD') and ! empty($row['jamkerja']))
                $jamkerja = $row['jamkerja'] / 40;

            //Non Admin menggunakan UMR
            if ($row['idtipepeg'] == 'N')
                $gapok = self::getGapokNonAdmin($conn); //Tarif UMR
            else if ($row['idtipepeg'] == 'D')
                $gapok = $jamkerja * $a_gapokdosen[$row['pendidikan']];
            else
                $gapok = $a_gapokadmin[$row['pendidikan']]; //gajipokok

            $record = array();
            $record['periodegaji'] = $r_periode;
            $record['idpegawai'] = $row['idpeg'];
            $record['gapok'] = $gapok;

            list($err, $msg) = self::saveGaji($conn, $record, $r_periode, $row['idpeg']);
            if (!$err) {
                for ($i = 0; $i < count($a_tunja); $i++) {

                    if ($a_byr[$row['idpeg']][$a_tunja[$i]] != $a_tunja[$i]) {
                        $key = $r_periode . '|' . $r_periodetarif . '|' . $row['idpeg'] . '|' . $a_tunja[$i];
                        $colkey = 'periodegaji,periodetarif,idpegawai,kodetunjangan';

                        list($err, $msg) = self::delete($conn, $key, 'ga_tunjanganpeg', $colkey);

                        //Tunjangan Prestasi manual
                        if (!$err and $a_tunja[$i] == 'T00022' and in_array($row['idjenispegawai'], $a_tunjadet[$a_tunja[$i]]) and ! empty($a_tunjpres[$row['idpeg']])) {
                            $tarif = $a_tunjpres[$row['idpeg']];
                            $nominal = $tarif;

                            $rectunj = array();
                            $rectunj['periodegaji'] = $r_periode;
                            $rectunj['periodetarif'] = $r_periodetarif;
                            $rectunj['idpegawai'] = $row['idpeg'];
                            $rectunj['kodetunjangan'] = $a_tunja[$i];
                            $rectunj['nominal'] = $nominal;

                            if (in_array($a_tunja[$i], $a_tunjjamkerja))
                                $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                            if (in_array($a_tunja[$i], $a_tunjharikerja))
                                $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                            list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                        }

                        //Tunjangan Prestasi
                        else if (!$err and $a_tunja[$i] == 'T00011' and in_array($row['idjenispegawai'], $a_tunjadet[$a_tunja[$i]]) and ! empty($a_hpa[$row['idpeg']]) and ! empty($a_npn[$row['idpeg']])) {
                            $hslpa = $a_hpa[$row['idpeg']];
                            $nominal = ($a_prp[$hslpa] / 100) * $a_npn[$row['idpeg']];

                            //dikalikan prosentase sanksi
                            if (!empty($a_prs[$row['idpeg']]))
                                $nominal = ($a_prs[$row['idpeg']] / 100) * $nominal;

                            $rectunj = array();
                            $rectunj['periodegaji'] = $r_periode;
                            $rectunj['periodetarif'] = $r_periodetarif;
                            $rectunj['idpegawai'] = $row['idpeg'];
                            $rectunj['kodetunjangan'] = $a_tunja[$i];
                            $rectunj['nominal'] = $nominal;

                            if (in_array($a_tunja[$i], $a_tunjjamkerja))
                                $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                            if (in_array($a_tunja[$i], $a_tunjharikerja))
                                $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                            list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                        }else {
                            if (!$err and in_array($row['idjenispegawai'], $a_tunjadet[$a_tunja[$i]]) and $a_tunjtarifparam[$a_tunja[$i]][$row['idpeg']] == $row['idpeg']) {
                                $nominal = $a_tarifTunj[$a_tunja[$i]][$row['idjenispegawai']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunja[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunja[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunja[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }
                        }
                    }
                }
            }
        }

        if ($err)
            $msg = 'Penyimpanan gaji gagal';
        else
            $msg = 'Penyimpanan gaji berhasil';

        return array($err, $msg);
    }

    function hitGajiTetap($conn, $r_periode, $r_sql = '', $isall = false) {
        if (!$isall)
            $a_peg = self::getPegawaiGaji('GAJITETAP');
        else {
            if (!empty($r_sql)) {
                $a_peg = self::pegFilter($conn, $r_sql);
            }
        }
		
		//tarik data
		self::tarikData($conn, $r_periode,'', $a_peg);

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select h.*,jj.idjabatan as jabatan,jj1.idjabatan as jabatan1, jj2.idjabatan as jabatan2 
					from " . static::table('ga_historydatagaji') . " h
					left join " . static::table('ms_struktural') . " s on s.idjstruktural = h.struktural
					left join " . static::table('ms_jabatan') . " jj on jj.idjabatan = s.idjabatan
					left join " . static::table('ms_struktural') . " s1 on s1.idjstruktural = h.strukturalgaji1
					left join " . static::table('ms_jabatan') . " jj1 on jj1.idjabatan = s1.idjabatan
					left join " . static::table('ms_struktural') . " s2 on s2.idjstruktural = h.strukturalgaji2
					left join " . static::table('ms_jabatan') . " jj2 on jj2.idjabatan = s2.idjabatan
					where gajiperiode = '$r_periode'";
        if (!empty($a_peg))
            $sql .= " and h.idpeg in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and h.idpeg not in ($b_peg)";

        $rs = $conn->Execute($sql);

        //Jenis tunjangan tetap
        $a_tunj = self::tunjanganTetap($conn);
        $a_tunjdet = self::tunjanganTetapDet($conn);

        //tarif tunjangan
        $a_tarifTunj = self::getTarifTunj($conn);

        //periode tarif sekarang
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        //tarif gaji pokok admin
        $a_gapokadmin = self::getTarifGapokAdmin($conn, $r_periodetarif);

        //tarif gaji pokok dosen
        $a_gapokdosen = self::getTarifGapokDosen($conn, $r_periodetarif);

        //prosentase tunjangan masakerja non admin
        $a_procmasakerja = self::getPRocMasaKerja($conn, $r_periodetarif);

        //tunjangan masa kerja admin
        $a_tunjmasakerja = self::getTarifMasaKerjaAdm($conn, $r_periodetarif);

        //tunjangan yang dikalikan jam kerja
        $a_tunjjamkerja = self::isTunjJamKerja($conn);

        //cek apakah tunjangan sudah dibayar
        $a_byr = self::isBayarTunj($conn, $r_periode, $r_periodetarif);

        //prosentase status pejabat struktural
        $a_prosentase = self::getProsentasePejabat($conn);

        //tunjangan dikalikan hari kerja
        $a_tunjharikerja = self::isKaliHariKerja($conn);

        //mendapatkan jumlah hari kerja
        require_once(Route::getModelPath('presensi'));
        $jmlharikerja = mPresensi::getHariKerja($conn);

        while ($row = $rs->FetchRow()) {
            //Dosen dan Administrasi dosen yang punya jam kerja
            $jamkerja = 1;
            if (($row['idtipepeg'] == 'D' or $row['idtipepeg'] == 'AD') and !empty($row['jamkerja']))
                $jamkerja = $row['jamkerja'] / 40;

            //Non Admin menggunakan UMR
            if ($row['idtipepeg'] == 'N')
                $gapok = self::getGapokNonAdmin($conn); //Tarif gapok non admin
            else if ($row['idtipepeg'] == 'D')
                $gapok = $jamkerja * $a_gapokdosen[$row['pendidikan']];
            else
                $gapok = $a_gapokadmin[$row['pendidikan']]; //gajipokok

            $record = array();
            $record['periodegaji'] = $r_periode;
            $record['idpegawai'] = $row['idpeg'];
            $record['gapok'] = $gapok;

            list($err, $msg) = self::saveGaji($conn, $record, $r_periode, $row['idpeg']);

            if (!$err) {
                for ($i = 0; $i < count($a_tunj); $i++) {

                    if ($a_byr[$row['idpeg']][$a_tunj[$i]] != $a_tunj[$i]) {
                        $key = $r_periode . '|' . $r_periodetarif . '|' . $row['idpeg'] . '|' . $a_tunj[$i];
                        $colkey = 'periodegaji,idpegawai,kodetunjangan';

                        list($err, $msg) = self::delete($conn, $key, 'ga_tunjanganpeg', $colkey);

                        if (count($a_tunjdet[$a_tunj[$i]]) > 0) {
                            //Tunjangan Struktural Admin
                            if (!$err and $a_tunj[$i] == 'T00001' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]]) and ( !empty($row['jabatan']) or ! empty($row['jabatan1']) or ! empty($row['jabatan2']))) {
                                if (empty($a_prosentase[$row['jnspejabat']]))
                                    $prosentase = 1;
                                else
                                    $prosentase = $a_prosentase[$row['jnspejabat']] / 100;

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];

                                $nominal = array();
                                if (!empty($row['jabatan'])) {
                                    $nominal[$row['struktural']] = $prosentase * $a_tarifTunj[$a_tunj[$i]][$row['jabatan']];

                                    if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                        $nominal[$row['struktural']] = $nominal[$row['struktural']] * $jamkerja;
                                    if (in_array($a_tunj[$i], $a_tunjharikerja))
                                        $nominal[$row['struktural']] = $nominal[$row['struktural']] * $jmlharikerja;
                                }
                                if (!empty($row['jabatan1'])) {
                                    $nominal[$row['strukturalgaji1']] = $prosentase * $a_tarifTunj[$a_tunj[$i]][$row['jabatan1']];

                                    if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                        $nominal[$row['strukturalgaji1']] = $nominal[$row['strukturalgaji1']] * $jamkerja;
                                    if (in_array($a_tunj[$i], $a_tunjharikerja))
                                        $nominal[$row['strukturalgaji1']] = $nominal[$row['strukturalgaji1']] * $jmlharikerja;
                                }
                                if (!empty($row['jabatan2'])) {
                                    $nominal[$row['strukturalgaji2']] = $prosentase * $a_tarifTunj[$a_tunj[$i]][$row['jabatan2']];

                                    if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                        $nominal[$row['strukturalgaji2']] = $nominal[$row['strukturalgaji2']] * $jamkerja;
                                    if (in_array($a_tunj[$i], $a_tunjharikerja))
                                        $nominal[$row['strukturalgaji2']] = $nominal[$row['strukturalgaji2']] * $jmlharikerja;
                                }

                                list($err, $msg) = self::saveTunjanganStruktural($conn, $rectunj, $nominal, $row['struktural'], $row['strukturalgaji1'], $row['strukturalgaji2']);
                            }

                            //Tunjangan Kehadiran Administrasi
                            else if (!$err and $a_tunj[$i] == 'T00004' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]])) {
                                $nominal = $a_tarifTunj[$a_tunj[$i]][$row['idjenispegawai']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Keahlian
                            else if (!$err and $a_tunj[$i] == 'T00012' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]])) {
                                $nominal = $a_tarifTunj[$a_tunj[$i]][$row['idjenispegawai']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Homebase
                            else if (!$err and $a_tunj[$i] == 'T00013' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]]) and ! empty($row['fungsional'])) {
                                $nominal = $a_tarifTunj[$a_tunj[$i]][$row['fungsional']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Kehadiran Non administrasi
                            else if (!$err and $a_tunj[$i] == 'T00015' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]])) {
                                $nominal = $a_tarifTunj[$a_tunj[$i]][$row['idjenispegawai']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Transport Penunjang
                            else if (!$err and $a_tunj[$i] == 'T00016' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]])) {
                                $nominal = $a_tarifTunj[$a_tunj[$i]][$row['idjenispegawai']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Struktural Akademik
                            else if (!$err and $a_tunj[$i] == 'T00017' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]]) and ( !empty($row['jabatan']) or !empty($row['jabatan1']) or !empty($row['jabatan2']))) {

                                if (empty($a_prosentase[$row['jnspejabat']]))
                                    $prosentase = 1;
                                else
                                    $prosentase = $a_prosentase[$row['jnspejabat']] / 100;

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];

                                $nominal = array();
                                if (!empty($row['jabatan'])) {
                                    $nominal[$row['struktural']] = $prosentase * $a_tarifTunj[$a_tunj[$i]][$row['jabatan']];

                                    if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                        $nominal[$row['struktural']] = $nominal[$row['struktural']] * $jamkerja;
                                    if (in_array($a_tunj[$i], $a_tunjharikerja))
                                        $nominal[$row['struktural']] = $nominal[$row['struktural']] * $jmlharikerja;
                                }
                                if (!empty($row['jabatan1'])) {
                                    $nominal[$row['strukturalgaji1']] = $prosentase * $a_tarifTunj[$a_tunj[$i]][$row['jabatan1']];

                                    if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                        $nominal[$row['strukturalgaji1']] = $nominal[$row['strukturalgaji1']] * $jamkerja;
                                    if (in_array($a_tunj[$i], $a_tunjharikerja))
                                        $nominal[$row['strukturalgaji1']] = $nominal[$row['strukturalgaji1']] * $jmlharikerja;
                                }
                                if (!empty($row['jabatan2'])) {
                                    $nominal[$row['strukturalgaji2']] = $prosentase * $a_tarifTunj[$a_tunj[$i]][$row['jabatan2']];

                                    if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                        $nominal[$row['strukturalgaji2']] = $nominal[$row['strukturalgaji2']] * $jamkerja;
                                    if (in_array($a_tunj[$i], $a_tunjharikerja))
                                        $nominal[$row['strukturalgaji2']] = $nominal[$row['strukturalgaji2']] * $jmlharikerja;
                                }

                                list($err, $msg) = self::saveTunjanganStruktural($conn, $rectunj, $nominal, $row['struktural'], $row['strukturalgaji1'], $row['strukturalgaji2']);
                            }

                            //Tunjangan Struktural Non Admin
                            else if (!$err and $a_tunj[$i] == 'T00018' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]]) and ! empty($row['struktural'])) {
                                if (empty($a_prosentase[$row['jnspejabat']]))
                                    $prosentase = 1;
                                else
                                    $prosentase = $a_prosentase[$row['jnspejabat']] / 100;

                                $nominal = $prosentase * $a_tarifTunj[$a_tunj[$i]][$row['struktural']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Kehadiran Akademik
                            else if (!$err and $a_tunj[$i] == 'T00019' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]])) {
                                $nominal = $a_tarifTunj[$a_tunj[$i]][$row['idjenispegawai']];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Masa Kerja Admin
                            else if (!$err and $a_tunj[$i] == 'T00020' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]]) and ! empty($row['masakerja'])) {
                                $masakerja = (int) substr($row['masakerja'], 0, 2);
                                $nominal = $a_tunjmasakerja[$masakerja];

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }

                            //Tunjangan Masa Kerja Non Admin
                            else if (!$err and $a_tunj[$i] == 'T00021' and in_array($row['idjenispegawai'], $a_tunjdet[$a_tunj[$i]]) and ! empty($row['masakerja'])) {
                                $masakerja = (int) substr($row['masakerja'], 0, 2);
                                $proc = $a_procmasakerja[$masakerja];
                                $umr = self::getLastUMR($conn);
                                $nominal = ($proc / 100) * $umr;

                                $rectunj = array();
                                $rectunj['periodegaji'] = $r_periode;
                                $rectunj['periodetarif'] = $r_periodetarif;
                                $rectunj['idpegawai'] = $row['idpeg'];
                                $rectunj['kodetunjangan'] = $a_tunj[$i];
                                $rectunj['nominal'] = $nominal;

                                if (in_array($a_tunj[$i], $a_tunjjamkerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jamkerja;
                                if (in_array($a_tunj[$i], $a_tunjharikerja))
                                    $rectunj['nominal'] = $rectunj['nominal'] * $jmlharikerja;

                                list($err, $msg) = self::saveTunjangan($conn, $rectunj);
                            }
                        }
                    }
                }
            }
        }

        if ($err)
            $msg = 'Penyimpanan gaji gagal';
        else
            $msg = 'Penyimpanan gaji berhasil';

        return array($err, $msg);
    }

    function pegFilter($conn, $r_sql) {
        $rs = $conn->Execute($r_sql);

        $a_pegawai = array();
        while ($row = $rs->FetchRow()) {
            $a_pegawai[$row['idpegawai']] = $row['idpegawai'];
        }

        if (count($a_pegawai) > 0) {
            $i_peg = implode(',', $a_pegawai);
        }

        return $i_peg;
    }

    function jmlTunjSabtuAhad($conn, $r_periode, $r_periodetarif, $r_pegawai) {
		//tunjangan struktural dan kehadiran
		$i_tunj = "'T00004','T00015','T00019','T00001','T00017','T00018','T00020','T00021'";
        $sql = "select coalesce(sum(g.nominal),0) from " . static::table('ga_tunjanganpeg') . " g
				where g.periodegaji = '$r_periode' and g.periodetarif = '$r_periodetarif' and g.idpegawai = $r_pegawai and g.kodetunjangan in ($i_tunj)";

        $jml = $conn->GetOne($sql);

        return $jml;
    }
	
	function getGapokPeg($conn,$r_periode,$r_pegawai){
		$sql = "select coalesce(gapok,0) from " . static::table('ga_gajipeg') . " where periodegaji = '$r_periode' and idpegawai = $r_pegawai";
		
		return $conn->GetOne($sql);
	}

    function getPotonganTransport($conn,$r_sql='') {
		if (!empty($r_sql)) {
			$a_peg = self::pegFilter($conn, $r_sql);
		}
		
        $tarif = self::getTarifPotTransport($conn);
        $last = self::getLastDataPeriodeGaji($conn);

        $procpottransport = self::getProcPotTransport($conn, $last['tglawalpotongan'], $last['tglakhirpotongan'], $r_sql);
        $totpottransport = $procpottransport['totproc'];

        $sql = "select p.idpegawai,m.idjenispegawai from " . static::table('pe_presensidet') . " p
					left join " . static::table('ms_pegawai') . " m on m.idpegawai = p.idpegawai
					where p.tglpresensi between '" . $last['tglawalpotongan'] . "' and '" . $last['tglakhirpotongan'] . "'";
		
		 if (!empty($a_peg))
            $sql .= " and p.idpegawai in ($a_peg)";
			
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpegawai']] = $tarif[$row['idjenispegawai']] * ($totpottransport[$row['idpegawai']] / 100);
        }

        return $a_data;
    }

    function getPotonganKehadiran($conn,$r_sql='') {
		if (!empty($r_sql)) {
			$a_peg = self::pegFilter($conn, $r_sql);
		}
			
        $tarif = self::getTarifPotKehadiran($conn);
        $last = self::getLastDataPeriodeGaji($conn);

        $procpotkehadiran = self::getProcPotKehadiran($conn, $last['tglawalpotongan'], $last['tglakhirpotongan'], $r_sql);
        $totpotkehadiran = $procpotkehadiran['totproc'];

        $sql = "select p.idpegawai,m.idjenispegawai from " . static::table('pe_presensidet') . " p
					left join " . static::table('ms_pegawai') . " m on m.idpegawai = p.idpegawai
					where p.tglpresensi between '" . $last['tglawalpotongan'] . "' and '" . $last['tglakhirpotongan'] . "'";
		
		 if (!empty($a_peg))
            $sql .= " and p.idpegawai in ($a_peg)";
			
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpegawai']] = $tarif[$row['idjenispegawai']] * ($totpotkehadiran[$row['idpegawai']] / 100);
        }

        return $a_data;
    }

    //mendapatkan potongan jamsostek berdasarkan prosentase * THP
    function getJamsosProc($conn) {
        $r_periode = self::getLastPeriodeGaji($conn);

        //mendapatkan prosentase jamsostek
        $procjamsos = $conn->GetOne("select coalesce(prosentase,0) from " . static::table('ms_procjamsostek') . " where isaktif = 'Y' order by tglberlaku desc limit 1");

        //mendapatkan THP
        $sql = "select idpegawai,gajibruto from " . static::table('ga_gajipeg') . " where periodegaji = '$r_periode'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_jamsosproc[$row['idpegawai']] = ($procjamsos / 100) * $row['gajibruto'];
        }

        return $a_jamsosproc;
    }

    //mendapatkan jamsostek pegawai
    function getPotPajak($conn, $r_periode) {
        $sql = "select * from " . static::table('ga_potongan') . " where periodegaji = '$r_periode' and kodepotongan in ('P00001','P00002','P00004','P00006')";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_jamsos[$row['idpegawai']] += $row['nominal'];
        }

        return $a_jamsos;
    }

    function getAlpa($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        //select dari presensi yang alpa
        $sql = "select coalesce(count(*),0) as alpa,idpegawai from " . static::table('pe_presensidet') . "
					where tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "' and kodeabsensi = 'A'
					group by idpegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_alpa[$row['idpegawai']] = $row['alpa'];
        }

        return $a_alpa;
    }

    function getTerlambat($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        //select dari presensi yang terlambat atau pulang duluan
        $sql = "select menitdatang,menitpulang,idpegawai from " . static::table('pe_presensidet') . "
					where tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "' and kodeabsensi in ('T','PD')";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $telat = 0;
            $pulangduluan = 0;
            if ($row['menitdatang'] > 0) {
                $telat = (int) $row['menitdatang'] / 60;
                if ($row['menitdatang'] % 60 > 30)
                    $telat += 1;
            }

            if ($row['menitpulang'] < 0) {
                $pulangduluan = (int) abs($row['menitpulang']) / 60;
                if (abs($row['menitpulang']) / 60 > 30)
                    $pulangduluan += 1;
            }

            $jam = ($telat + $pulangduluan);
            $a_terlambat[$row['idpegawai']] += $row['terlambat'];
        }

        return $a_terlambat;
    }

    function getCLTPegawai($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        $sql = "select coalesce(count(p.tglpresensi),0) as jmlclt,p.idpegawai 
					from " . static::table('pe_presensidet') . " p
					left join " . static::table('pe_rwtcuti') . " c on c.nourutcuti = p.nourutcuti
					where c.idjeniscuti = 'CLT' and p.tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "'
					group by p.idpegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_clt[$row['idpegawai']] = $row['jmlclt'];
        }

        return $a_clt;
    }

    function getWajibHadir($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        $sql = "select coalesce(count(*),0) as wajibhadir,idpegawai from " . static::table('pe_presensidet') . "
					where tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "' and (sjamdatang is not null or sjampulang is not null)
					group by idpegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_wajibhadir[$row['idpegawai']] = $row['wajibhadir'];
        }

        return $a_wajibhadir;
    }

    //mendapatkan jumlah shift sabtu/ ahad
    function getShiftSabtuMinggu($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        //shift sabtu
        $sql = "select p.idpegawai from " . static::table('ms_pegawai') . " p
					left join " . static::table('pe_rwtharikerja') . " r on cast(r.idpegawai as varchar)+r.kodekelkerja+cast(r.tglberlaku as varchar) =(select  
                        cast(rr.idpegawai as varchar)+rr.kodekelkerja+cast(rr.tglberlaku as varchar) from " . static::table('pe_rwtharikerja') . " rr where p.idpegawai=rr.idpegawai and rr.isaktif='Y' order by rr.tglberlaku desc limit 1) 
					left join " . static::table('ms_kelkerja') . " k on k.kodekelkerja = r.kodekelkerja
					where k.sabtu is not null 
					order by p.idpegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_shiftsabtu[] = $row['idpegawai'];
        }

        //shift ahad
        $sql = "select p.idpegawai from " . static::table('ms_pegawai') . " p
                    left join " . static::table('pe_rwtharikerja') . " r on cast(r.idpegawai as varchar)+r.kodekelkerja+cast(r.tglberlaku as varchar) =(select 
                        cast(rr.idpegawai as varchar)+rr.kodekelkerja+cast(rr.tglberlaku as varchar) from " . static::table('pe_rwtharikerja') . " rr where p.idpegawai=rr.idpegawai and rr.isaktif='Y' order by rr.tglberlaku desc limit 1) 
                    left join " . static::table('ms_kelkerja') . " k on k.kodekelkerja = r.kodekelkerja
                    where k.minggu is not null 
                    order by p.idpegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_shiftahad[] = $row['idpegawai'];
        }

        return array($a_shiftsabtu, $a_shiftahad);
    }

    //mendapatkan jumlah hadir sabtu/ ahad
    function getRealSabtuMinggu($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        //shift sabtu
        $sql = "select coalesce(count(*),0) as hadirsabtu,idpegawai 
					from " . static::table('pe_presensidet') . "
					where tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "' and
					date_part('dw',tglpresensi) = 7 and sjamdatang is not null and sjampulang is not null and kodeabsensi in ('H','T','PD','DK','PK')
					group by idpegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_hadirsabtu[$row['idpegawai']] = $row['hadirsabtu'];
        }

        //shift ahad
        $sql = "select coalesce(count(*),0) as hadirahad,idpegawai 
					from " . static::table('pe_presensidet') . "
					where tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "' and
					date_part('dw',tglpresensi) = 1 and sjamdatang is not null and sjampulang is not null and kodeabsensi in ('H','T','PD','DK','PK')
					group by idpegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_hadirahad[$row['idpegawai']] = $row['hadirahad'];
        }

        return array($a_hadirsabtu, $a_hadirahad);
    }

    //hasil PA
    function getHasilPA($conn) {
        //periode gaji terakhir
        $periodegaji = self::getLastPeriodeGaji($conn);
        $tahunperiode = substr($periodegaji, 0, 4);

        //periode penilaian tahun sebelumnya
        $periodepa = $conn->GetOne("select kodeperiode from " . static::table('pa_periode') . " 
						where cast(substring(kodeperiode,1,4) as int) < $tahunperiode order by kodeperiode desc limit 1");

        //hasil penilaian
        if (!empty($periodepa)) {
            $sql = "select idpegawai,kategorinilai from " . static::table('pa_nilaiakhir') . "
						where kodeperiode = '$periodepa'";
            $rsh = $conn->Execute($sql);

            while ($rowh = $rsh->FetchRow()) {
                $a_hasil[$rowh['idpegawai']] = $rowh['kategorinilai'];
            }
        }
        return $a_hasil;
    }

    //prosentase sanksi
    function getProcSanksi($conn) {
        //periode gaji terakhir
        $periodegaji = self::getLastPeriodeGaji($conn);
        $tahunperiode = substr($periodegaji, 0, 4);

        //periode penilaian tahun sebelumnya
        $periodepa = $conn->GetOne("select  kodeperiode from " . static::table('pa_periode') . " 
						where cast(substring(kodeperiode,1,4) as int) < $tahunperiode order by kodeperiode desc limit 1");

        //hasil penilaian
        if (!empty($periodepa)) {
            $sql = "select idpegawai,pctsanksi from " . static::table('pa_nilaiakhir') . "
						where kodeperiode = '$periodepa'";
            $rsh = $conn->Execute($sql);

            while ($rowh = $rsh->FetchRow()) {
                $a_sanksi[$rowh['idpegawai']] = $rowh['pctsanksi'];
            }
        }
        return $a_sanksi;
    }

    //prosentase tunjangan prestasi
    function getProcPrestasi($conn) {
        //periode gaji terakhir
        $periodegaji = self::getLastPeriodeGaji($conn);
        $tahunperiode = substr($periodegaji, 0, 4);

        //periode penilaian tahun sebelumnya
        $periodepa = $conn->GetOne("select kodeperiode from " . static::table('pa_periode') . " 
						where cast(substring(kodeperiode,1,4) as int) < $tahunperiode order by kodeperiode desc limit 1");

        //acuan periodegaji
        if (!empty($periodepa)) {
            $periodeacuan = $conn->GetOne("select  periodegaji from " . static::table('ga_patunjprestasi') . " 
							where kodeperiode = '$periodepa' order by kodeperiode desc limit 1");

            //select prosentase tunjangan prestasi
            if (!empty($periodeacuan)) {
                $sql = "select kategorinilai,procnilai from " . static::table('ga_patunjprestasi') . "
							where kodeperiode = '$periodepa' and periodegaji = '$periodeacuan'";
                $rsp = $conn->Execute($sql);

                while ($rowp = $rsp->FetchRow()) {
                    $a_proc[$rowp['kategorinilai']] = $rowp['procnilai'];
                }
            }
        }
        return $a_proc;
    }

    //mendapatkan gaji non penyesuaian
    function getNonPenyesuaian($conn, $r_periode) {
        $sql = "select idpegawai,gajinonpenyesuaian from " . static::table('ga_gajipeg') . "
					where periodegaji = '$r_periode'";
        $rsg = $conn->Execute($sql);

        while ($rowg = $rsg->FetchRow()) {
            $a_gnp[$rowg['idpegawai']] = $rowg['gajinonpenyesuaian'];
        }

        return $a_gnp;
    }

    //gaji non penyesuaian PA
    function getNonPenyesuaianPA($conn) {
        //periode gaji terakhir
        $periodegaji = self::getLastPeriodeGaji($conn);
        $tahunperiode = substr($periodegaji, 0, 4);

        //periode penilaian tahun sebelumnya
        $periodepa = $conn->GetOne("select kodeperiode from " . static::table('pa_periode') . " 
						where substring(kodeperiode,1,4) < $tahunperiode order by kodeperiode desc limit 1");

        //acuan periodegaji
        if (!empty($periodepa)) {
            $periodeacuan = $conn->GetOne("select periodegaji from " . static::table('ga_patunjprestasi') . " 
							where kodeperiode = '$periodepa' order by kodeperiode desc limit 1");

            if (!empty($periodeacuan))
                $a_gnpPA = self::getNonPenyesuaian($conn, $periodeacuan);
        }
        return $a_gnpPA;
    }

    //mendapatkan tarif tunjangan lain
    function getTunjLain($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        $sql = "select g.nominal as tunjlain,g.idpegawai,g.kodetunjangan
				from " . static::table('ga_pegawaitunjangan') . " g
				left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
				where g.tmtmulai <= '" . $last['tglawalhit'] . "' and t.carahitung = 'M' and t.isbayargaji = 'Y' and t.isaktif = 'Y' and g.nominal is not null and g.isaktif = 'Y'
                order by g.tmtmulai asc";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunjlain[$row['kodetunjangan']][$row['idpegawai']] = $row['tunjlain'];
        }

        return $a_tunjlain;
    }

    function getPrestasi($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        $sql = "select g.nominal,p.idpegawai
				from " . static::table('ms_pegawai') . " p
				left join " . static::table('ga_pegawaitunjangan') . " g on g.notunjangan = (select gg.notunjangan
					from " . static::table('ga_pegawaitunjangan') . " gg where gg.idpegawai = p.idpegawai and gg.tmtmulai <= '" . $last['tglawalhit'] . "' and gg.isaktif='Y'
				order by gg.tmtmulai desc limit 1)
				where g.kodetunjangan = 'T00022' and g.nominal is not null";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunjpres[$row['idpegawai']] = $row['nominal'];
        }

        return $a_tunjpres;
    }

    //mendapatkan tarif tunjangan tarif parameter
    function getTunjTarifParam($conn) {
        $last = self::getLastDataPeriodeGaji($conn);

        $sql = "select g.idpegawai,g.kodetunjangan
                from " . static::table('ga_pegawaitunjangan') . " g
                left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
                where g.tmtmulai <= '" . $last['tglawalhit'] . "' and t.carahitung in ('P','M') and t.isbayargaji = 'T' and t.isaktif = 'Y' and g.idpegawai is not null and g.isaktif = 'Y'
                order by g.tmtmulai asc";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunjtarifparam[$row['kodetunjangan']][$row['idpegawai']] = $row['idpegawai'];
        }

        return $a_tunjtarifparam;
    }

    //pegawai yang sudah dibayarkan gajinya
    function sudahBayar($conn, $r_periode) {
        $sql = "select idpegawai from " . static::table('ga_gajipeg') . " where periodegaji = '$r_periode' and isfinish = 'Y'";
        $rs = $conn->Execute($sql);

        $a_peg = array();
        while ($row = $rs->FetchRow()) {
            $a_peg[$row['idpegawai']] = $row['idpegawai'];
        }

        if (count($a_peg) > 0)
            $i_peg = implode(",", $a_peg);

        return $i_peg;
    }

    function isBayarGajiTunj($conn, $r_periode) {
        $sql = "select idpegawai from " . static::table('ga_gajipeg') . " where periodegaji = '$r_periode' and isfinish = 'Y'";
        $rs = $conn->Execute($sql);

        $a_peg = array();
        while ($row = $rs->FetchRow()) {
            $a_peg[$row['idpegawai']] = $row['idpegawai'];
        }

        //cek tunjangan yang sudah dibayarkan			
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        $sql = "select idpegawai from " . static::table('ga_tunjanganpeg') . " 
					where periodegaji = '$r_periode' and periodetarif = '$r_periodetarif' and isdibayar = 'Y'
					group by idpegawai,periodegaji,periodetarif";
        $rs = $conn->Execute($sql);

        $t_peg = array();
        while ($row = $rs->FetchRow()) {
            $t_peg[$row['idpegawai']] = $row['idpegawai'];
        }

        if (count($a_peg) > 0 or count($t_peg) > 0) {
            $a_peg = array_merge($a_peg, $t_peg);
            $a_peg = array_unique($a_peg);
        }

        return $a_peg;
    }

    function getHubKerjaPegProsentase($conn) {
        $periodegaji = self::getLastPeriodeGaji($conn);

        $sql = "select g.idpeg, h.prosentasegaji from " . static::table('ga_historydatagaji') . " g
					left join " . static::table('ms_hubkerja') . " h on h.idhubkerja=g.idhubkerja
					where g.gajiperiode = '$periodegaji'";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpeg']] = $row['prosentasegaji'];
        }

        return $a_data;
    }

    //Simpan gaji
    function saveGaji($conn, $record, $r_periode, $idpegawai) {
        $a_hubkerja = self::getHubKerjaPegProsentase($conn);
        $isDosenLB = self::isDosenLB($conn, $idpegawai);

        if (!empty($record['gapok'])) {
            if ($isDosenLB)
                $record['gapok'] = 0;
            else
                $record['gapok'] = $record['gapok'] * ($a_hubkerja[$idpegawai] / 100);
        }

        $isexist = $conn->GetOne("select 1 from " . static::table('ga_gajipeg') . " where periodegaji = '$r_periode' and idpegawai = $idpegawai");


        if (empty($isexist))
            $err = self::insertRecord($conn, $record, false, 'ga_gajipeg');
        else {
            $key = $r_periode . '|' . $idpegawai;
            $colkey = 'periodegaji,idpegawai';
            $err = self::updateRecord($conn, $record, $key, false, 'ga_gajipeg', $colkey);
        }

        if ($err)
            $msg = 'Penyimpanan gaji gagal';
        else
            $msg = 'Penyimpanan gaji berhasil';

        return array($err, $msg);
    }

    //Jenis Tunjangan Tetap bersama gaji
    function getTunjTetapGaji($conn) {
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where isgajitetap = 'Y' and isbayargaji = 'Y' and isaktif = 'Y' 
					order by kodetunjangan";
        $a_jtunj = Query::arrQuery($conn, $sql);

        return $a_jtunj;
    }

    //Jenis Tunjangan Tetap detail bersama gaji
    function getTunjTetapGajiDet($conn) {
        $sql = "select d.kodetunjangan,d.idjenispegawai from " . static::table('ms_tunjangandet') . " d
					left join " . static::table('ms_tunjangan') . " m on m.kodetunjangan=d.kodetunjangan
					where m.isgajitetap = 'Y' and m.isbayargaji = 'Y' and m.isaktif = 'Y'
					order by d.kodetunjangan";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['idjenispegawai']][$row['kodetunjangan']] = $row['kodetunjangan'];
        }

        return $a_data;
    }

    //Jenis Tunjangan Tetap awal
    function getTunjTetapAwal($conn) {
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where isbayargaji = 'T' and isaktif = 'Y' order by kodetunjangan";
        $a_jtunjawal = Query::arrQuery($conn, $sql);

        return $a_jtunjawal;
    }

    //Jenis Tunjangan Tetap awal gaji
    function getTunjTetapAwalDet($conn) {
        $sql = "select idjenispegawai from " . static::table('ms_tunjangan') . " t
					left join " . static::table('ms_tunjangandet') . " d on d.kodetunjangan=t.kodetunjangan
					where t.isbayargaji='T' and t.isaktif = 'Y'
					group by idjenispegawai";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_data[] = $row['idjenispegawai'];
        }

        return $a_data;
    }

    //Jenis Tunjangan Penyesuaian
    function getTunjPenyesuaian($conn) {
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where isbayargaji = 'Y' and (isgajitetap = 'N' or isgajitetap is null) and isaktif = 'Y' 
					order by kodetunjangan";
        $a_jttunj = Query::arrQuery($conn, $sql);

        return $a_jttunj;
    }

    //Jenis Potongan
    function getJnsPotongan($conn) {
        $sql = "select kodepotongan,namapotongan from " . static::table('ms_potongan') . " where isaktif = 'Y' order by kodepotongan";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_jns[$row['kodepotongan']] = $row['namapotongan'];
        }

        $sql = "select kodejnspinjaman,jnspinjaman from " . static::table('lv_jnspinjaman') . " where isaktif = 'Y' order by kodejnspinjaman";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_jns[$row['kodejnspinjaman']] = $row['jnspinjaman'];
        }

        return $a_jns;
    }

    //Informasi gaji
    function getInfoGaji($conn, $key) {
        list($periode, $idpegawai) = explode('|', $key);

        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					js.jabatanstruktural,pd.namapendidikan,	substring(gh.masakerja,1,2)+' thn. ' + substring(gh.masakerja,3,2)+' bln.' as mkgaji,pk.golongan,
					gp.namaperiode,gh.idtipepeg,gh.idjenispegawai,f.jabatanfungsional as fungsional,l.upahlembur
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ga_historydatagaji') . " gh on gh.gajiperiode=g.periodegaji and gh.idpeg = g.idpegawai
					left join " . static::table('ga_upahlembur') . " l on l.idpegawai=gh.idpeg and l.periodegaji=gh.gajiperiode
					left join " . static::table('ms_struktural') . " js on js.idjstruktural=gh.struktural
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan=gh.pendidikan
					left join " . static::table('ms_pangkat') . " pk on pk.idpangkat = gh.pangkatpeg
					left join " . static::table('ms_fungsional') . " f on f.idjfungsional = gh.fungsional
					left join " . static::table('ga_periodegaji') . " gp on gp.periodegaji = g.periodegaji
					where g.periodegaji = '$periode' and g.idpegawai = $idpegawai";
        $row = $conn->GetRow($sql);

        return $row;
    }

    function iyaTidak() {
        $a_konf = array('Y' => 'Iya', 'T' => 'Tidak');

        return $a_konf;
    }

    function getCCekal() {
        $a_cekal = array('' => '-- Semua --', 'Y' => 'Ditunda', 'T' => 'Tidak Ditunda');

        return $a_cekal;
    }

    function getCBayar() {
        $a_bayar = array('' => '-- Semua --', 'Y' => 'Sudah Dibayar', 'T' => 'Belum Dibayar');

        return $a_bayar;
    }

    function listQueryGajiBayar() {
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(gh.masakerja,1,2)||' tahun ' || substring(gh.masakerja,3,2)||' bulan' as mkgaji,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai,p.email
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpegawai
					left join " . static::table('ga_historydatagaji') . " gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = gh.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = gh.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = gh.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = gh.pendidikan
					where (g.istunda = 'T' or g.istunda is null)";

        return $sql;
    }

    function listQueryGajiAwal() {
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(gh.masakerja,1,2)||' tahun ' || substring(gh.masakerja,3,2)||' bulan' as mkgaji,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpegawai
					left join " . static::table('ga_historydatagaji') . " gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = gh.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = gh.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = gh.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = gh.pendidikan";

        return $sql;
    }

    function listQueryGajiBayarAwal() {
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(gh.masakerja,1,2)||' tahun ' || substring(gh.masakerja,3,2)||' bulan' as mkgaji,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpegawai
					left join " . static::table('ga_historydatagaji') . " gh on gh.idpeg = g.idpegawai and gh.gajiperiode = g.periodegaji
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = gh.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = gh.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = gh.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = gh.pendidikan";

        return $sql;
    }

	function getCountEmail($conn,$r_key){
		list($r_periode,$idpegawai) = explode('|', $r_key);

		$sql = "select coalesce(issent,'0') from " . static::table('ga_gajipeg') . " where periodegaji = '$r_periode' and idpegawai = '$idpegawai'";
		$count = $conn->GetOne($sql);

		return $count+1;
	}

    /*     * ************************************************** POTONGAN ***************************************************** */

    function listQueryPotongan() {
        $sql = "select * from " . static::table('ms_potongan');

        return $sql;
    }

    function getLastPotongan($conn) {
        $sql = "select kodepotongan || '|' || ismanual as kode from " . static::table('ms_potongan') . " where isaktif='Y' order by kodepotongan";

        return $conn->GetOne($sql);
    }

    //kode absensi potongan hadir dan transport
    function aKodeabsensiPot($conn) {
        $sql = "select kodeabsensi,absensi from " . static::table('ms_absensi') . " where kodeabsensi in ('T','PD')";
        $a_kode = Query::arrQuery($conn, $sql);

        return $a_kode;
    }

    //kode absensi potongan
    function absensiPotongan($conn) {
        $sql = "select kodeabsensi,absensi from " . static::table('ms_absensi') . " where kodeabsensi in ('A','DK','PK','I','ST')";
        $a_kode = Query::arrQuery($conn, $sql);

        return $a_kode;
    }

    function getCPotongan($conn) {
        $sql = "select kodepotongan || '|' || coalesce(ismanual,'') as kodepotongan, namapotongan from " . static::table('ms_potongan') . " where isaktif='Y'";

        $rs = $conn->Execute($sql);
        while ($row = $rs->FetchRow()) {
            $a_pot[$row['kodepotongan']] = $row['namapotongan'];
        }

        //pinjaman
        $sql = "select kodejnspinjaman, jnspinjaman 
				from " . static::table('lv_jnspinjaman') . " where isaktif='Y'";

        $rs = $conn->Execute($sql);
        while ($row = $rs->FetchRow()) {
            $a_pot[$row['kodejnspinjaman']] = $row['jnspinjaman'];
        }

        return $a_pot;
    }

    function getCPotParam($conn, $empty = false) {
        $sql = "select kodepotongan, namapotongan from " . static::table('ms_potongan') . " 
					where ismanual = 'P' and isaktif='Y'";

        $rs = $conn->Execute($sql);

        if ($empty) {
            $a_data = array();
            $a_add = array('all' => '-- Semua Potongan Param --');
            $a_data = array_merge($a_data, $a_add);
        }

        while ($row = $rs->FetchRow()) {
            $a_data[$row['kodepotongan']] = $row['namapotongan'];
        }

        return $a_data;
    }

    function listQueryPotParam() {
        $sql = "select g.*, p.nip, sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					pt.namapotongan
					from " . static::table('ga_potonganparam') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					left join " . static::table('ms_potongan') . " pt on pt.kodepotongan=g.kodepotongan
					where pt.ismanual = 'P'";

        return $sql;
    }

    function getDataEditPotParam($r_key) {
        $sql = "select g.*, sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap 
					from " . static::table('ga_potonganparam') . " g
					left join " . static::table('ms_pegawai') . " m on m.idpegawai=g.idpegawai
					where g.nopotongan = $r_key";

        return $sql;
    }

    function isOverlapPotParam($conn, $record, $r_key = '') {
        $sql = "select idpegawai from " . static::table('ga_potonganparam') . "
					where idpegawai = " . $record['idpegawai'] . " and kodepotongan = '" . $record['kodepotongan'] . "' and tglmulai = '" . $record['tglmulai'] . "'";

        if (!empty($r_key))
            $sql .= " and nopotongan <> $r_key limit 1";

        $idpegawai = $conn->GetOne($sql);

        return !empty($idpegawai) ? true : false;
    }

    function caraHitungPot() {
        $a_cara = array('Y' => 'Manual', 'T' => 'Otomatis', 'P' => 'Parameter');

        return $a_cara;
    }

    function getTahunPotonganParam($conn) {			
        $sql = "select date_part('year', tglmulai) as tahun from " . static::table('ga_potonganparam') . "
				group by date_part('year', tglmulai) order by date_part('year', tglmulai) desc";
        $rs = $conn->Execute($sql);

        $a_data = array();
        $a_add = array('all' => '-- Semua Tahun --');
        $a_data = array_merge($a_data, $a_add);

        while ($row = $rs->FetchRow()) {
            $a_data[$row['tahun']] = $row['tahun'];
        }


        return $a_data;
    }

    function getLastTahunPotonganParam($conn) {		
        $sql = "select date_part('year', tglmulai) as tahun 
				from " . static::table('ga_potonganparam') . "
				group by date_part('year', tglmulai) order by date_part('year', tglmulai) desc limit 1";
        $tahun = $conn->GetOne($sql);

        return $tahun;
    }

    function listQueryHitPotongan($r_periode, $r_potongan) {
        $sqladd = "";
        if (!empty($r_periode))
            $sqladd = " and pt.periodegaji='$r_periode'";

        if (!empty($r_potongan))
            $sqladd .= " and pt.kodepotongan='$r_potongan'";

        $sql = "select g.*,p.idpegawai,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(g.masakerja,1,2)||' tahun ' || substring(g.masakerja,3,2)||' bulan' as mkgaji,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai,pt.nominal
					from " . static::table('ga_historydatagaji') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = g.idpeg
					left join " . static::table('ga_potongan') . " pt on pt.idpegawai = g.idpeg {$sqladd}
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = g.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = g.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = g.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = g.pendidikan";

        return $sql;
    }

    function savePotongan($conn, $r_periode, $r_potongan, $a_post) {
        $a_pegawai = $a_post['id'];

        if (count($a_pegawai) > 0) {
            foreach ($a_pegawai as $idpegawai) {
                $key = $r_periode . '|' . $r_potongan . '|' . $idpegawai;
                $colkey = 'periodegaji,kodepotongan,idpegawai';

                list($err, $msg) = self::delete($conn, $key, 'ga_potongan', $colkey);

                if (!$err and ! empty($a_post['nominal_' . $idpegawai])) {
                    $record = array();
                    $record['periodegaji'] = $r_periode;
                    $record['kodepotongan'] = $r_potongan;
                    $record['idpegawai'] = $idpegawai;
                    $record['nominal'] = Cstr::cStrDec($a_post['nominal_' . $idpegawai]);

                    $err = self::insertRecord($conn, $record, false, 'ga_potongan');
                }
            }
        }

        if ($err)
            $msg = 'Penyimpanan potongan gagal';
        else
            $msg = 'Penyimpanan potongan berhasil';

        return array($err, $msg);
    }

    //sudah bayar pinjaman utk periode hitung gaji
    function isbyrpjm($conn, $r_periode) {
        $sql = "select p.idpinjaman,a.noangsuran
					from " . static::table('pe_angsuran') . " a
					left join " . static::table('pe_pinjaman') . " p on p.idpinjaman = a.idpinjaman
					where a.periodegaji = '$r_periode' and a.isdibayar = 'Y' and p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N')";
        $rs = $conn->Execute($sql);
		
		$a_sdhbyr = array();
        while ($row = $rs->FetchRow()) {
            $a_sdhbyr[$row['idpinjaman']][$row['noangsuran']] = $row['idpeminjam'];
        }

        return $a_sdhbyr;
    }

    function bayarPinjaman($conn, $r_periode) {
        $sql = "select * from " . static::table('pe_angsuran') . " where periodegaji = '$r_periode' and (isdibayar is null or isdibayar = 'N')";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            //insert ke bayar angsuran
            $recbyr = array();
            $recbyr['idpinjaman'] = $row['idpinjaman'];
            $recbyr['jmlbayar'] = $row['jmlangsuran'];
            $recbyr['bayarvia'] = 'G';
            $recbyr['tglbayar'] = date('Y-m-d');

            $err = self::insertRecord($conn, $recbyr, false, 'pe_bayarpinjaman', '', '', true, $idbayarpinjaman);

            if (!$err) {
                $recangs = array();
                $recangs['idbayarpinjaman'] = $idbayarpinjaman;
                $recangs['isdibayar'] = 'Y';

                $akey = $row['idpinjaman'] . '|' . $row['noangsuran'];
                $acolkey = 'idpinjaman,noangsuran';
                $err = self::updateRecord($conn, $recangs, $akey, false, 'pe_angsuran', $acolkey);
            }
        }
    }

    function tundaBayarPinjaman($conn, $r_periode) {
        $sql = "select * from " . static::table('pe_angsuran') . " where periodegaji = '$r_periode' and isdibayar = 'Y'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            //dinullkan dulu
            $recangs = array();
            $recangs['idbayarpinjaman'] = 'null';
            $recangs['isdibayar'] = 'null';

            $akey = $row['idpinjaman'] . '|' . $row['noangsuran'];
            $acolkey = 'idpinjaman,noangsuran';
            list($err, $msg) = self::updateRecord($conn, $recangs, $akey, true, 'pe_angsuran', $acolkey);

            //hapus bayar angsuran
            if (!$err) {
                $key = $row['idbayarpinjaman'].'|'.$row['idpinjaman'] . '|G';
                $colkey = 'idbayarpinjaman,idpinjaman,bayarvia';

                list($err, $msg) = self::delete($conn, $key, 'pe_bayarpinjaman', $colkey);
            }

            if ($err)
                break;
        }

        return array($err, $msg);
    }

    function pinjamanPeg($conn, $r_periode) {
        $sql = "select p.idpinjaman,p.idpeminjam,p.kodejnspinjaman,a.noangsuran,a.jmlangsuran
					from " . static::table('pe_pinjaman') . " p
					left join " . static::table('pe_angsuran') . " a on a.idpinjaman = p.idpinjaman and a.noangsuran = 
						(select ap.noangsuran from " . static::table('pe_angsuran') . " ap where ap.idpinjaman = p.idpinjaman and (ap.isdibayar <> 'Y' or ap.isdibayar is null)
						order by ap.noangsuran limit 1)
					where p.isfixpinjam = 'Y' and (p.islunas is null or p.islunas = 'N') and p.periodeawal <= '$r_periode'
					and (a.periodegajitunda <> '$r_periode' or a.periodegajitunda is null)";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_pinjam[$row['idpeminjam']][$row['kodejnspinjaman']][$row['idpinjaman']]['idpinjaman'] = $row['idpinjaman'];
            $a_pinjam[$row['idpeminjam']][$row['kodejnspinjaman']][$row['idpinjaman']]['noangsuran'] = $row['noangsuran'];
            $a_pinjam[$row['idpeminjam']][$row['kodejnspinjaman']][$row['idpinjaman']]['jmlangsuran'] = $row['jmlangsuran'];
        }

        return $a_pinjam;
    }

    function hitungHistoryPotongan($conn, $r_periode, $r_sql) {
        $last = self::getLastDataPeriodeGaji($conn);

        $procpotkehadiran = self::getProcPotKehadiran($conn, $last['tglawalpotongan'], $last['tglakhirpotongan'], $r_sql);
        $a_prockehadiran = $procpotkehadiran['data'];
        $a_tarifpotkehadiran = self::getTarifPotKehadiran($conn);

        $procpottransport = self::getProcPotTransport($conn, $last['tglawalpotongan'], $last['tglakhirpotongan'], $r_sql);
        $a_proctransport = $procpottransport['data'];
        $a_tarifpottransport = self::getTarifPotTransport($conn);

        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select * from " . static::table('ga_historydatagaji') . "
					where gajiperiode = '$r_periode'";
        if (!empty($a_peg))
            $sql .= " and idpeg in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and idpeg not in ($b_peg)";

        $rs = $conn->Execute($sql);
        while ($row = $rs->FetchRow()) {
            $tarifpotkehadiran = $a_tarifpotkehadiran[$row['idjenispegawai']];
            $tarifpottransport = $a_tarifpottransport[$row['idjenispegawai']];

            foreach ($a_prockehadiran[$row['idpeg']] as $tglpresensi => $a_kehadiran) {

                $rec_historypot = array();
                $rec_historypot['idpegawai'] = $row['idpeg'];
                $rec_historypot['tglpresensi'] = $tglpresensi;
                $rec_historypot['tarifpotkehadiran'] = $tarifpotkehadiran;
                $rec_historypot['tarifpottransport'] = $tarifpottransport;
                $rec_historypot['procpotkehadirantelat'] = $a_kehadiran['procpotdt'];
                $rec_historypot['procpottransporttelat'] = $a_proctransport[$row['idpeg']][$tglpresensi]['procpotdt'];
                $rec_historypot['procpotkehadiranpd'] = $a_kehadiran['procpotpd'];
                $rec_historypot['procpottransportpd'] = $a_proctransport[$row['idpeg']][$tglpresensi]['procpotpd'];
                $rec_historypot['potkehadiran'] = (($a_kehadiran['procpotdt'] + $a_kehadiran['procpotpd']) / 100) * $tarifpotkehadiran;
                $rec_historypot['pottransport'] = (($a_proctransport[$row['idpeg']][$tglpresensi]['procpotdt'] + $a_proctransport[$row['idpeg']][$tglpresensi]['procpotpd']) / 100) * $tarifpottransport;

                $cek = $conn->GetOne("select 1 from " . static::table('ga_historypotongan') . " where idpegawai=" . $row['idpeg'] . " and tglpresensi='$tglpresensi'");
                if (empty($cek))
                    $err = self::insertRecord($conn, $rec_historypot, false, 'ga_historypotongan');
                else {
                    $akey = $row['idpeg'] . '|' . $tglpresensi;
                    $acolkey = 'idpegawai,tglpresensi';
                    $err = self::updateRecord($conn, $rec_historypot, $akey, false, 'ga_historypotongan', $acolkey);
                }
            }
        }
    }

    function hitungPotongan($conn, $r_periode, $r_potongan, $r_sql) {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }
		
		//pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        //Komponen pinjaman
		if ($r_potongan == 'PJ001' or $r_potongan == 'PJ002' or $r_potongan == 'PJ003'){
			$a_sdhbyr = self::isbyrpjm($conn, $r_periode); //cek apakah sudah membayar pinjaman periode gaji hitung
			$a_pinjam = self::pinjamanPeg($conn, $r_periode); //angsuran pinjaman
        }

        //potongan transport
		if($r_potongan == 'P00001')
			$a_PotTransport = self::getPotonganTransport($conn, $r_sql);

        //potongan kehadiran
		if ($r_potongan == 'P00002')
			$a_PotKehadiran = self::getPotonganKehadiran($conn, $r_sql);

        $sql = "select * from " . static::table('ga_historydatagaji') . "
					where gajiperiode = '$r_periode'";
        if (!empty($a_peg))
            $sql .= " and idpeg in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and idpeg not in ($b_peg)";

        $rs = $conn->Execute($sql);

        //cek apakah potongan termasuk potongan parameter
        $isparam = $conn->GetOne("select ismanual from " . static::table('ms_potongan') . " where kodepotongan = '$r_potongan'");

        //mendapatkan tarif parameter
        if ($isparam == 'P')
            $a_tarifParam = self::getTarifPotParam($conn, $r_potongan);

        while ($row = $rs->FetchRow()) {
            $key = $r_periode . '|' . $r_potongan . '|' . $row['idpeg'];
            $colkey = 'periodegaji,kodepotongan,idpegawai';

            //mengecek tabel ga_gajipeg ada/tidak -> harus ada agar muncul di slip gaji
            $cek_gajipeg = $conn->GetOne("select 1 from " . static::table('ga_gajipeg') . " where periodegaji='$r_periode' and idpegawai=" . $row['idpeg'] . "");
            if (empty($cek_gajipeg)) {
                $rec_gagipeg = array();
                $rec_gagipeg['periodegaji'] = $r_periode;
                $rec_gagipeg['idpegawai'] = $row['idpeg'];

                self::insertRecord($conn, $rec_gagipeg, false, 'ga_gajipeg');
            }

            list($err, $msg) = self::delete($conn, $key, 'ga_potongan', $colkey);

            if ($isparam == 'P') {
                $record = array();
                $record['periodegaji'] = $r_periode;
                $record['kodepotongan'] = $r_potongan;
                $record['idpegawai'] = $row['idpeg'];

                if ($a_tarifParam[$row['idpeg']] != '')
                    $nominal = $a_tarifParam[$row['idpeg']];
                else
                    $nominal = 0;

                $record['nominal'] = $nominal;

                $err = self::simpanPotongan($conn, $record);
            }
            else {
                if ($r_potongan == 'P00001') { //potongan transport
                    $record = array();
                    $record['periodegaji'] = $r_periode;
                    $record['kodepotongan'] = $r_potongan;
                    $record['idpegawai'] = $row['idpeg'];

                    $nominal = $a_PotTransport[$row['idpeg']];
                    $record['nominal'] = $nominal;

                    $err = self::simpanPotongan($conn, $record);
                } else if ($r_potongan == 'P00002') { //potongan kehadiran
                    $record = array();
                    $record['periodegaji'] = $r_periode;
                    $record['kodepotongan'] = $r_potongan;
                    $record['idpegawai'] = $row['idpeg'];

                    $nominal = $a_PotKehadiran[$row['idpeg']];
                    $record['nominal'] = $nominal;

                    $err = self::simpanPotongan($conn, $record);
                } else if ($r_potongan == 'PJ001') { //potongan pinjaman keuangan
                    $record = array();
                    $record['periodegaji'] = $r_periode;
                    $record['kodepotongan'] = $r_potongan;
                    $record['idpegawai'] = $row['idpeg'];
					
					if(count($a_pinjam[$row['idpeg']][$r_potongan])>0){
						foreach($a_pinjam[$row['idpeg']][$r_potongan] as $k_idpinjaman => $datap){
							if($row['idpeg'] != $a_sdhbyr[$datap['idpinjaman']][$datap['noangsuran']]){
								$record['nominal'] += $datap['jmlangsuran'];

								//update periode gaji di pe_angsuran
								$recangs = array();
								$recangs['periodegaji'] = $r_periode;

								$akey = $datap['idpinjaman'] . '|' . $datap['noangsuran'];
								$acolkey = 'idpinjaman,noangsuran';
								$err = self::updateRecord($conn, $recangs, $akey, false, 'pe_angsuran', $acolkey);
							}
						}
						if (!$err)
							$err = self::simpanPotongan($conn, $record);
					}                    
                } else if ($r_potongan == 'PJ002') { //potongan pinjaman excess
                    $record = array();
                    $record['periodegaji'] = $r_periode;
                    $record['kodepotongan'] = $r_potongan;
                    $record['idpegawai'] = $row['idpeg'];

                    if(count($a_pinjam[$row['idpeg']][$r_potongan])>0){
						foreach($a_pinjam[$row['idpeg']][$r_potongan] as $k_idpinjaman => $datap){
							if($row['idpeg'] != $a_sdhbyr[$datap['idpinjaman']][$datap['noangsuran']]){
								$record['nominal'] += $datap['jmlangsuran'];

								//update periode gaji di pe_angsuran
								$recangs = array();
								$recangs['periodegaji'] = $r_periode;

								$akey = $datap['idpinjaman'] . '|' . $datap['noangsuran'];
								$acolkey = 'idpinjaman,noangsuran';
								$err = self::updateRecord($conn, $recangs, $akey, false, 'pe_angsuran', $acolkey);
							}
						}
						if (!$err)
							$err = self::simpanPotongan($conn, $record);
					}
                } else if ($r_potongan == 'PJ003') { //potongan pinjaman extra premi
                    $record = array();
                    $record['periodegaji'] = $r_periode;
                    $record['kodepotongan'] = $r_potongan;
                    $record['idpegawai'] = $row['idpeg'];

                    if(count($a_pinjam[$row['idpeg']][$r_potongan])>0){
						foreach($a_pinjam[$row['idpeg']][$r_potongan] as $k_idpinjaman => $datap){
							if($row['idpeg'] != $a_sdhbyr[$datap['idpinjaman']][$datap['noangsuran']]){
								$record['nominal'] += $datap['jmlangsuran'];

								//update periode gaji di pe_angsuran
								$recangs = array();
								$recangs['periodegaji'] = $r_periode;

								$akey = $datap['idpinjaman'] . '|' . $datap['noangsuran'];
								$acolkey = 'idpinjaman,noangsuran';
								$err = self::updateRecord($conn, $recangs, $akey, false, 'pe_angsuran', $acolkey);
							}
						}
						if (!$err)
							$err = self::simpanPotongan($conn, $record);
					}
                }
            }
        }

        if ($err)
            $msg = 'Penyimpanan potongan gagal';
        else
            $msg = 'Penyimpanan potongan berhasil';

        return array($err, $msg);
    }

    //Simpan potongan
    function simpanPotongan($conn, $record) {
        $err = 0;
        $isDosenLB = self::isDosenLB($conn, $record['idpegawai']);

        if ($isDosenLB)
            $record['nominal'] = 0;

        if (!empty($record['nominal']))
            $err = self::insertRecord($conn, $record, false, 'ga_potongan');

        return $err;
    }

    function getPotonganSlip($conn, $key) {
        list($periode, $idpegawai) = explode('|', $key);

        $sql = "select g.* from " . static::table('ga_potongan') . " g
					where g.periodegaji = '$periode'";
        if (!empty($idpegawai))
            $sql .= " and g.idpegawai = $idpegawai";

        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_pot[$row['idpegawai']][$row['kodepotongan']] = $row['nominal'];
        }

        return $a_pot;
    }

    function getTarifPotParam($conn, $r_potongan) {
        $sql = "select coalesce(t.nominal,0) as potparam,p.idpegawai
				from " . static::table('ms_pegawai') . " p
				left join " . static::table('ga_potonganparam') . " t on t.nopotongan = (select tt.nopotongan from " . static::table('ga_potonganparam') . " tt 
				where tt.idpegawai = p.idpegawai and tt.kodepotongan = '$r_potongan' and tt.isaktif = 'Y' order by tt.tglmulai desc limit 1)
				where kodepotongan = '$r_potongan'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_potparam[$row['idpegawai']] = $row['potparam'];
        }

        return $a_potparam;
    }

    /*     * ************************************************** END OF POTONGAN ***************************************************** */

    /*     * ************************************************** T H R ********************************************************** */

    function getCPeriodeGajiTHR($conn) {
        $sql = "select periodegaji, namaperiode from " . static::table('ga_periodegaji') . " where refperiodegaji is not null order by tglakhirhit desc";

        return Query::arrQuery($conn, $sql);
    }

    function getLastPeriodeGajiTHR($conn) {
        $r_periodegaji = $conn->GetOne("select periodegaji from " . static::table('ga_periodegaji') . " where refperiodegaji is not null order by tglakhirhit desc limit 1");

        return $r_periodegaji;
    }

    function getLastDataPeriodeGajiTHR($conn) {
        $periode = self::getLastPeriodeGaji($conn);
        $row = $conn->GetRow("select * from " . static::table('ga_periodegaji') . " where periodegaji = '$periode'");
       
        return $row;
    }

    function listQueryKomposisiTHR() {
        $sql = "select g.*,h.hubkerja,cast(mkmin as varchar)||' - '||cast(mkmax as varchar)||' bulan' as masakerja
					from " . static::table('ga_komposisithr') . " g
					left join " . static::table('ms_hubkerja') . " h on h.idhubkerja = g.idhubkerja";

        return $sql;
    }

    function getDataEditKomposisiTHR($r_key) {
        list($r_periode, $r_hubkerja) = explode('|', $r_key);
        $sql = "select * from " . static::table('ga_komposisithr') . " 
					where periodegaji = '$r_periode' and idhubkerja = '$r_hubkerja'";

        return $sql;
    }

    function getTHRDetail($conn, $key, $label = '', $post = '') {
        list($r_periode, $r_jenis, $r_aktif) = explode('|', $key);
        $sql = "select * from " . static::table('ga_komposisithrdet') . " 
					where periodegaji = '$r_periode' and idjenispegawai = '$r_jenis' and idstatusaktif = '$r_aktif' 
					order by kodetunjangan";

        return static::getDetail($conn, $sql, $label, $post);
    }

    function getDetailInfo($detail, $kolom = '') {
        $info = array();

        switch ($detail) {
            case 'detailthr':
                $info['table'] = 'ga_komposisithrdet';
                $info['key'] = 'periodegaji,idjenispegawai,idstatusaktif,kodetunjangan';
                $info['label'] = 'Komposisi THR Tunjangan';
                break;
        }

        if (empty($kolom))
            return $info;
        else
            return $info[$kolom];
    }

    function getDataPeriodeGajiTHR($conn, $r_periode) {
        $row = $conn->GetRow("select * from " . static::table('ga_periodegaji') . " where periodegaji = '$r_periode'");

        return $row;
    }

    function getCHubkerja($conn) {
        $sql = "select idhubkerja, hubkerja from " . static::table('ms_hubkerja') . " order by idhubkerja";

        return Query::arrQuery($conn, $sql);
    }

    function getCSettingTHR() {
        $a_setting = array('O' => 'Otomatis', 'M' => 'Manual');

        return $a_setting;
    }

    function listQueryHitungTHR($r_periode, $r_refperiode, $r_hubkerja, $isExist) {
        $sql = "select g.gajiditerima,g.pph,g.isfinish,g.idpegawai,g.periodegaji,gh.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,substring(gh.masakerja,1,2)||' tahun ' || substring(gh.masakerja,3,2)||' bulan' as mkgaji,
					t.tipepeg||' - '||j.jenispegawai as namajenispegawai
					from " . static::table('ga_historydatagaji') . " gh
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = gh.idpeg
					left join " . static::table('ga_gajipeg') . " g on g.idpegawai = gh.idpeg and g.periodegaji = '$r_periode'
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = gh.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = gh.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = gh.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = gh.pendidikan
					where gh.gajiperiode = '$r_refperiode' and g.idpegawai is not null";

        if (count($r_hubkerja) == 0 and ! $isExist)
            $sql .= ' and 1=0';
        if (count($r_hubkerja) > 0) {
            $i_hubkerja = implode("','", $r_hubkerja);
            $sql .= " and p.idhubkerja in ('$i_hubkerja')";
        }

        return $sql;
    }

    function hitGajiTHR($conn, $r_periode, $r_sql = '', $r_perioderef) {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        //gaji periode referensi
        $g_ref = self::getGajiRef($conn, $r_perioderef);

        $sql = "select * from " . static::table('ga_historydatagaji') . "
					where gajiperiode = '$r_perioderef'";
        if (!empty($a_peg))
            $sql .= " and idpeg in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and idpeg not in ($b_peg)";

        $rs = $conn->Execute($sql);

        //masa kerja proporsional
        $prop = self::getProporsional($conn, $r_perioderef);

        while ($row = $rs->FetchRow()) {
            $record = array();
            $record['periodegaji'] = $r_periode;

            if (!empty($g_ref[$row['idpeg']]) and $prop[$row['idpeg']] > 0) {
                $record['idpegawai'] = $row['idpeg'];
                $record['gapok'] = $prop[$row['idpeg']] * $g_ref[$row['idpeg']];

                list($err, $msg) = self::saveGaji($conn, $record, $r_periode, $row['idpeg']);
            }
        }

        if ($err)
            $msg = 'Penyimpanan gaji THR gagal';
        else
            $msg = 'Penyimpanan gaji THR berhasil';

        return array($err, $msg);
    }

    function getGajiRef($conn, $r_perioderef) {
        $sql = "select idpegawai,cast(gajiditerima as varchar) as gajiditerima from " . static::table('ga_gajipeg') . "
					where periodegaji = '$r_perioderef'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_gaji[$row['idpegawai']] = $row['gajiditerima'];
        }

        return $a_gaji;
    }

    function saveGajiTHR($conn, $r_periode, $r_perioderef, $a_post) {
        $a_pegawai = $a_post['id'];

        if (count($a_pegawai) > 0) {
            //masa kerja proporsional
            $prop = self::getProporsional($conn, $r_perioderef);

            foreach ($a_pegawai as $idpegawai) {
                if (!empty($a_post['gajiditerima_' . $idpegawai])) {
                    $record = array();
                    $record['periodegaji'] = $r_periode;
                    $record['idpegawai'] = $idpegawai;
                    $record['gapok'] = Cstr::cStrDec($a_post['gajiditerima_' . $idpegawai]);

                    list($err, $msg) = self::saveGaji($conn, $record, $r_periode, $idpegawai);
                }
            }
        }

        if ($err)
            $msg = 'Penyimpanan gaji THR gagal';
        else
            $msg = 'Penyimpanan gaji THR berhasil';

        return array($err, $msg);
    }

    function isDihitungTHR($conn, $r_periode) {
        $isExist = $conn->GetOne("select idpegawai from " . static::table('ga_gajipeg') . " where periodegaji = '$r_periode' limit 1");

        return $isExist;
    }

    function getProporsional($conn, $r_periode) {
        $sql = "select idpeg," . static::schema . ".get_mkpengabdian(idpeg) as masakerja 
					from " . static::table('ga_historydatagaji') . " 
					where gajiperiode = '$r_periode'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            //masa kerja
            $prop = 12 / 12;
            if ((int) substr($row['masakerja'], 0, 2) <= 0) {
                $prop = (int) substr($row['masakerja'], 2, 2) / 12;
            }

            if ($prop > 0)
                $a_prop[$row['idpeg']] = $prop;
        }

        return $a_prop;
    }

    /*     * ******************************************** E N D   O F   T H R ************************************************** */
    /*     * ************************************************** END OF GAJI ***************************************************** */

    /*     * ******************************************** START OF LEMBUR ************************************************** */

    function listQueryHitLembur($r_periode) {
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					u.namaunit,pd.namapendidikan,t.tipepeg||' - '||j.jenispegawai as namajenispegawai 
					from " . static::table('ga_upahlembur') . " g
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai = h.idpeg
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg = h.idtipepeg
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = h.idjenispegawai
					left join " . static::table('ms_unit') . " u on u.idunit = h.idunit
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan = h.pendidikan";

        return $sql;
    }

    function saveLembur($conn, $r_periode, $a_post) {
        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);
        $ib_peg = explode(',', $b_peg);

        //lembur yang sudah dibayarkan
        $l_peg = self::isLemburBayar($conn, $r_periode);
        $il_peg = explode(',', $l_peg);

        $a_pegawai = $a_post['id'];
        if (count($a_pegawai) > 0) {
            $conn->StartTrans();

            foreach ($a_pegawai as $idpegawai) {
                if (!in_array($idpegawai, $ib_peg) and ! in_array($idpegawai, $il_peg)) {
                    $kasbon = str_replace('.', '', $a_post['kasbon_' . $idpegawai]);

                    $record = array();
                    $record['kasbon'] = $kasbon;

                    $key = $r_periode . '|' . $idpegawai;
                    $colkey = 'periodegaji,idpegawai';

                    list($err, $msg) = mGaji::updateRecord($conn, $record, $key, true, 'ga_upahlembur', $colkey);
                }
            }
            $conn->CompleteTrans();
        }

        return array($err, $msg);
    }

    function getUMRPegawai($conn) {
        $sql = "select p.idpegawai,t.tarifumr
				from " . static::table('ms_pegawai') . " p
				left join " . static::table('ms_tarifumr') . " t on t.notarifumr=(select tt.notarifumr from " . static::table('ms_tarifumr') . " tt
					where tt.idpegawai = p.idpegawai and tt.isaktif = 'Y' order by tt.tglberlaku desc limit 1)
				where t.tarifumr is not null";

        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tarifumr[$row['idpegawai']] = $row['tarifumr'];
        }

        return $a_tarifumr;
    }

    function hitungLembur($conn, $r_periode, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        //lembur yang sudah dibayarkan
        $l_peg = self::isLemburBayar($conn, $r_periode);

        //pegawai umr tertentu
        $a_pegumr = self::getUMRPegawai($conn);

        //unit IT
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');
        $col = mUnit::getData($conn, '0211000');
            
        //Cek apakah hari cuti dalam hari liburan
        $libur = $conn->Execute("select tgllibur from ".self::table('ms_liburdetail')." 
                    where tgllibur between '".$periode['tglawallembur']."' and '".$periode['tglakhirlembur']."' and 
                    date_part('dw',tgllibur) <> 1 and date_part('dw',tgllibur) <> 7");

        while($rowl = $libur->FetchRow()){
            $a_libur[$rowl['tgllibur']] = $rowl['tgllibur'];
        }

        $periode = $conn->GetRow("select tglawallembur, tglakhirlembur from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
        $sql = "select p.totlembur,p.idpegawai,p.tglpresensi,g.gapok,p.sjamdatang,p.kodeabsensi,u.infoleft,u.inforight 
					from " . static::table('pe_presensidet') . " p
					left join " . static::table('ga_gajipeg') . " g on g.idpegawai=p.idpegawai and g.periodegaji='$r_periode'
					left join " . static::table('ms_pegawai') . " m on m.idpegawai=p.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=m.idunit
					where totlembur is not null and issetujuatasan='Y' and isvalid='Y'
					and tglpresensi between '".$periode['tglawallembur']."' and '".$periode['tglakhirlembur']."'";

        if (!empty($a_peg))
            $sql .= " and p.idpegawai in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and p.idpegawai not in ($b_peg)";
        if (!empty($l_peg))
            $sql .= " and p.idpegawai not in ($l_peg)";
        $rs = $conn->Execute($sql);

        $a_pegawai = array();
        $a_lembur = array();

        //perhitungan lembur 8 jam/hari dan 5 hari kerja/minggu
        while ($row = $rs->FetchRow()) {
            $reclembur = array();
            $jumjam = $jumjamlibur = $l = 0;

            //karyawan yang lembur
            $a_pegawai[$row['idpegawai']] = $row['idpegawai'];

            if($row['infoleft'] >= $col['infoleft'] and $row['inforight'] <= $col['inforight']){// untuk unit IT 
                $reclembur['lemburjam'] = 25000;
                
                if ($row['kodeabsensi'] == 'H' or $row['kodeabsensi'] == 'T' or $row['kodeabsensi'] == 'PD' or $row['kodeabsensi'] == 'PK' or $row['kodeabsensi'] == 'DK') {
                    $reclembur['jmljam'] = $reclembur['totaljam'] = $jumjam = $row['totlembur'];
                    $reclembur['lembur'] = $l = 25000 * $jumjam;
                    $status = 'A';
                }else if ($row['kodeabsensi'] == 'HL') {
                    $reclembur['jmljam'] = $reclembur['totaljam'] = $jumjamlibur = $row['totlembur'];
                    $reclembur['lembur'] = $l = 25000 * $jumjamlibur;

                    $status = in_array($row['tglpresensi'], $a_libur) ? 'C' : 'B';
                }
            }else{
                if ($row['kodeabsensi'] == 'H' or $row['kodeabsensi'] == 'T' or $row['kodeabsensi'] == 'PD' or $row['kodeabsensi'] == 'PK' or $row['kodeabsensi'] == 'DK') {
                    $reclembur['jmljam'] = $jumjam = $row['totlembur'];
                    if ($jumjam == 1)
                        $l = 1 * 1.5;
                    else if ($jumjam > 1)
                        $l = (1 * 1.5) + (($jumjam - 1) * 2);

                    $reclembur['totaljam'] = $l;
                    $status = 'A';
                }else if ($row['kodeabsensi'] == 'HL') {
                    $reclembur['jmljam'] = $jumjamlibur = $row['totlembur'];
                    if ($jumjamlibur <= 8)
                        $l = $jumjamlibur * 2;
                    else if ($jumjamlibur == 9)
                        $l = (8 * 2) + (1 * 3);
                    else if ($jumjamlibur > 9)
                        $l = (8 * 2) + (1 * 3) + (($jumjamlibur - 9) * 4);

                    $reclembur['totaljam'] = $l;
                    $status = in_array($row['tglpresensi'], $a_libur) ? 'C' : 'B';
                }

                if (!empty($a_pegumr[$row['idpegawai']])){
                    $reclembur['gajipokok'] = $a_pegumr[$row['idpegawai']];
                    $reclembur['lemburjam'] = (1 / 173 * $a_pegumr[$row['idpegawai']]);
                    $l = $l * (1 / 173 * $a_pegumr[$row['idpegawai']]);
                }
                else{
                    $reclembur['gajipokok'] = $row['gapok'];
                    $reclembur['lemburjam'] = (1 / 173 * $row['gapok']);
                    $l = $l * (1 / 173 * $row['gapok']);
                }
            }
            
            $reclembur['lembur'] = $l;
            $reclembur['jenislembur'] = $status;

            $a_lembur[$row['idpegawai']] += $l;
            $a_jamkerja[$row['idpegawai']] += $jumjam;
            $a_jumjamlibur[$row['idpegawai']] += $jumjamlibur;

            $key = $row['tglpresensi'] . '|' . $row['idpegawai'];
            $colkey = 'tgllembur,idpegawai';
            list($err, $msg) = mGaji::updateRecord($conn, $reclembur, $key, true, 'pe_suratlembur', $colkey);
        }

        if (count($a_pegawai) > 0) {
            $conn->StartTrans();
            foreach ($a_pegawai as $id) {
                $record = array();
                $record['periodegaji'] = $r_periode;
                $record['idpegawai'] = $id;
                $record['upahlembur'] = $a_lembur[$id];
                $record['jamkerja'] = $a_jamkerja[$id];
                $record['jamkerjalibur'] = $a_jumjamlibur[$id];

                $isExist = false;
                $key = $r_periode . '|' . $id;
                $colkey = 'periodegaji,idpegawai';
                $isExist = mGaji::isDataExist($conn, $key, 'ga_upahlembur', $colkey);

                if ($isExist)
                    list($err, $msg) = mGaji::updateRecord($conn, $record, $key, true, 'ga_upahlembur', $colkey);
                else
                    list($err, $msg) = mGaji::insertRecord($conn, $record, true, 'ga_upahlembur');

                if ($err)
                    $msg = 'Perhitungan lembur gagal';
                else
                    $msg = 'Perhitungan lembur berhasil';
            }

            $conn->CompleteTrans();
        }

        return array($err, $msg);
    }

    function bayarLembur($conn, $r_periode, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        //pegawai yang gajinya sudah dibayar
        $b_peg = self::sudahBayar($conn, $r_periode);

        $sql = "select idpegawai from " . static::table('ga_upahlembur') . "
					where periodegaji = '$r_periode' and (isbayar = 'T' or isbayar is null)";
        if (!empty($a_peg))
            $sql .= " and idpegawai in ($a_peg)";
        if (!empty($b_peg))
            $sql .= " and idpegawai not in ($b_peg)";

        $rs = $conn->Execute($sql);

        $record = array();
        $record['isbayar'] = 'Y';
        $record['tglbayar'] = date('Y-m-d');

        while ($row = $rs->FetchRow()) {
            $key = $row['idpegawai'] . '|' . $r_periode;
            $colkey = 'idpegawai,periodegaji';

            list($err, $msg) = self::updateRecord($conn, $record, $key, true, 'ga_upahlembur', $colkey);
        }

        list($err, $msg) = self::updateStatus($conn);

        return array($err, $msg);
    }

    function isLemburBayar($conn, $r_periode) {
        $sql = "select idpegawai from " . static::table('ga_upahlembur') . "
					where periodegaji = '$r_periode' and isbayar = 'Y'";
        $rs = $conn->Execute($sql);

        $a_peg = array();
        while ($row = $rs->FetchRow()) {
            $a_peg[$row['idpegawai']] = $row['idpegawai'];
        }

        if (count($a_peg) > 0)
            $i_peg = implode(',', $a_peg);

        return $i_peg;
    }

    /*     * ******************************************** E N D   O F   LEMBUR ************************************************** */

    /*     * *********************** SLIP EMAIL ************************** */

    function getTunjTetapSlipEmail($conn, $r_periode) {
        $sql = "select g.* from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$r_periode' and t.isgajitetap = 'Y' and t.isbayargaji = 'Y' and t.isaktif = 'Y'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunj[$row['kodetunjangan']][$row['idpegawai']] = $row['nominal'];
        }

        return $a_tunj;
    }

    function getTunjTetapStrukLainEmail($conn, $r_periode) {
        $sql = "select g.* from " . static::table('ga_tunjanganstrukturallain') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$r_periode' and t.isgajitetap = 'Y' and t.isbayargaji = 'Y' and t.isaktif = 'Y'";

        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunjstruklain[$row['kodetunjangan']][$row['idpegawai']][$row['idjstruktural']] = $row['nominal'];
        }

        return $a_tunjstruklain;
    }

    function getTunjAwalSlipEmail($conn, $r_periode) {

        $sql = "select g.* from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$r_periode' and t.isbayargaji = 'T' and t.isaktif = 'Y'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunja[$row['kodetunjangan']][$row['idpegawai']] = $row['nominal'];
        }

        return $a_tunja;
    }

    function getTunjPenyesuaianSlipEmail($conn, $r_periode) {

        $sql = "select g.* from " . static::table('ga_tunjanganpeg') . " g
					left join " . static::table('ms_tunjangan') . " t on t.kodetunjangan=g.kodetunjangan
					where g.periodegaji = '$r_periode' and t.isbayargaji = 'Y' and (t.isgajitetap = 'N' or t.isgajitetap is null) and t.isaktif = 'Y'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_tunj[$row['kodetunjangan']][$row['idpegawai']] = $row['nominal'];
        }

        return $a_tunj;
    }

    function getPotonganSlipEmail($conn, $r_periode) {

        $sql = "select g.* from " . static::table('ga_potongan') . " g
					where g.periodegaji = '$r_periode'";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            $a_pot[$row['kodepotongan']][$row['idpegawai']] = $row['nominal'];
        }

        return $a_pot;
    }

    /*     * ************************** END OF SLIP EMAIL ********************************* */

    /*     * ************************************************** SETTING RATE MENGAJAR ***************************************************** */

    function listQueryRateHonor() {
        $sql = "select r.*,j.idtipepeg from " . self::table('ms_ratehonor') . " r
					left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai=r.idjenispegawai";

        return $sql;
    }

    function getRateJenisPegawai($conn) {
        $sql = "select j.idjenispegawai, t.tipepeg||' - '||j.jenispegawai 
					from " . self::table('ms_jenispeg') . " j
					left join " . static::table('ms_tipepeg') . " t on t.idtipepeg=j.idtipepeg
					where j.idtipepeg in ('AD','D') order by j.idtipepeg,j.idjenispegawai";

        return Query::arrQuery($conn, $sql);
    }

    function getRateJenisRate($conn) {
        $sql = "select kodejnsrate, namajnsrate from " . static::table('ms_jnsrate') . " where ismanual='T' order by kodejnsrate";

        return Query::arrQuery($conn, $sql);
    }

    function setRateHonor($conn, $idpegawai = '', $idjenispegawai = '', $kodejnsrate = '', $isaktif = 'Y', $idpendidikan = '') {
        //prosentase honor
        require_once(Route::getModelPath('honor'));
        $prochonor = mHonor::getLastProcHonor($conn);

        if (empty($prochonor))
            $prochonor = 100;

        if (!empty($kodejnsrate))
            $wkodejnsrate = "and kodejnsrate = '$kodejnsrate'";

        if (!empty($idjenispegawai))
            $widjenispegawai = "and idjenispegawai='$idjenispegawai'";

        if (!empty($idpendidikan))
            $widpendidikan = "and idpendidikan='$idpendidikan'";

        if (!empty($idpegawai))
            $widpegawai = "and idpegawai = '$idpegawai'";

        //gapok dan kehadiran rate
        $sqlt = "select * from " . static::table('ms_tarifrate') . " where 1=1 {$widpendidikan} order by idpendidikan";
        $rst = $conn->Execute($sqlt);

        while ($rowt = $rst->FetchRow()) {
            $a_rate[$rowt['idpendidikan']]['gapok'] = $rowt['tarifgapok'];
            $a_rate[$rowt['idpendidikan']]['kehadiran'] = $rowt['tarifkehadiran'];
        }

        //rumus
        $sqlr = "select * from " . static::table('ms_ratehonor') . "  where 1=1 {$wkodejnsrate} {$widjenispegawai} order by idjenispegawai";
        $rsr = $conn->Execute($sqlr);

        while ($rowr = $rsr->FetchRow()) {
            $a_rumus[$rowr['idjenispegawai']][$rowr['kodejnsrate']] = $rowr['rumus'];
        }

        $sql = "select idpegawai,idjenispegawai,idpendidikan from " . static::table('ms_pegawai') . " 
					where (idtipepeg = 'D' or idtipepeg = 'AD' or nodosen is not null) {$widjenispegawai} {$widpegawai} {$widpendidikan}";
        $rs = $conn->Execute($sql);

        while ($row = $rs->FetchRow()) {
            if (count($a_rumus[$row['idjenispegawai']]) > 0) {
                foreach ($a_rumus[$row['idjenispegawai']] as $kkode => $vkode) {
                    $rumus = str_replace('GP', ($a_rate[$row['idpendidikan']]['gapok'] == '' ? 0 : $a_rate[$row['idpendidikan']]['gapok']), $vkode);
                    $rumus = str_replace('TK', ($a_rate[$row['idpendidikan']]['kehadiran'] == '' ? 0 : $a_rate[$row['idpendidikan']]['kehadiran']), $rumus);

                    eval('$rum = (' . $rumus . ');');

                    $result = array();
                    $result['nominal'] = $rum * ($prochonor / 100);
                    $result['idpegawai'] = $row['idpegawai'];
                    $result['kodejnsrate'] = $kkode;
                    $result['isvalid'] = $isaktif;

                    $colkey = 'idpegawai,kodejnsrate';
                    $key = $row['idpegawai'] . '|' . $kkode;
                    list($err, $msg) = self::delete($conn, $key, 'ga_ratehonor', $colkey);

                    if (!$err and $result['nominal'] > 0) {
                        list($err, $msg) = self::insertRecord($conn, $result, true, 'ga_ratehonor');
                        if (!$err) {
                            $isExist = $conn->GetOne("select 1 from " . static::table('ga_ajardosen') . " where idpegawai=" . $row['idpegawai'] . "");
                            $record = array();
                            $record['idpegawai'] = $row['idpegawai'];
                            if (!$isExist)
                                list($p_posterr, $p_postmsg) = mHonor::insertRecord($conn, $record, true, 'ga_ajardosen');
                        }
                    }
                }
            }
        }

        return array($p_posterr, $p_postmsg);
    }

    function unValidRateHonor($conn, $r_key) {
        $conn->Execute("update " . self::table('ga_ajardosen') . " set isvalid = null where idpegawai = $r_key");

        return $conn->ErrorNo();
    }

    function getProcPot($conn, $jam, $kodeabsensi) {
        $procpotongan = $conn->GetOne("SELECT  COALESCE(prosentase,0) as procpotongan FROM " . static::table('ms_potonganhadir') . "
											WHERE kodeabsensi = '$kodeabsensi' AND $jam > CAST(rangebawah AS NUMERIC(5,2)) ORDER BY CAST(rangebawah AS NUMERIC(5,2)) DESC limit 1");
        return $procpotongan;
    }

    function getProcPotTrans($conn, $jam, $kodeabsensi) {
        $procpotongan = $conn->GetOne("SELECT COALESCE(prosentase,0) as procpotongan FROM " . static::table('ms_potongantransport') . "
											WHERE kodeabsensi = '$kodeabsensi' AND $jam > CAST(rangebawah AS NUMERIC(5,2)) ORDER BY CAST(rangebawah AS NUMERIC(5,2)) DESC limit 1");
        return $procpotongan;
    }

    function getProcAbsen($conn, $kodeabsensi) {
        $prochadir = $conn->GetOne("SELECT COALESCE(prosentasehadir,0) as prochadir FROM " . static::table('ms_potonganabsensi') . "
											WHERE kodeabsensi = '$kodeabsensi'");
        return $prochadir;
    }

    function getProcTrans($conn, $kodeabsensi) {
        $proctrans = $conn->GetOne("SELECT COALESCE(prosentasetransport,0) as proctrans FROM " . static::table('ms_potonganabsensi') . "
											WHERE kodeabsensi = '$kodeabsensi'");
        return $proctrans;
    }

    function getProcPotKehadiran($conn, $r_tglmulai, $r_tglselesai, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        $sqld = "select pegditunjuk,tglpergi,tglpulang from " . static::table('pe_rwtdinas') . " 
					where tglpergi between '$r_tglmulai' and '$r_tglselesai' and tglpulang between '$r_tglmulai' and '$r_tglselesai'";
		 if (!empty($a_peg))
            $sqld.= "pegditunjuk in (" . $a_peg . ")";
			
        $rsd = $conn->Execute($sqld);
        while ($rowd = $rsd->FetchRow()) {
            $mulai = strtotime($rowd['tglpergi']);
            $selesai = strtotime($rowd['tglpulang']);

            $artgl = array();
            $i = 0;
            while ($mulai <= $selesai) {
                $nefektif = date("w", $mulai);
                if ($nefektif != 0 and $nefektif != 6)
                    $a_dinas[$rowd['pegditunjuk']][date('Y-m-d', $mulai)] = 'D';
                $mulai+=86400;
            }
        }


        $sql = "select idpegawai,tglpresensi,kodeabsensi,menitdatang,menitpulang from " . static::table('pe_presensidet') . " 
					where tglpresensi between '$r_tglmulai' and '$r_tglselesai'";
        $rs = $conn->Execute($sql);

        if (!empty($a_peg))
            $sql.= "idpegawai in (" . $a_peg . ")";

        $a_data = array();
        $totprochari = array();
        $a_totproc = array();
        while ($row = $rs->FetchRow()) {
            $menitdatang = $row['menitdatang'] / 60;
            $menitpulang = $row['menitpulang'] / 60;

            if ($menitdatang > 0)
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] = self::getProcPot($conn, $menitdatang, 'T');
            if ($menitpulang < 0)
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotpd'] = self::getProcPot($conn, abs($menitpulang), 'PD');
            if ($row['kodeabsensi'] == 'I' or $row['kodeabsensi'] == 'A' or $row['kodeabsensi'] == 'DK' or $row['kodeabsensi'] == 'PK' or $row['kodeabsensi'] == 'ST')
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] = self::getProcAbsen($conn, $row['kodeabsensi']);

            if ($a_dinas[$row['idpegawai']][$row['tglpresensi']] == 'D') {
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] = 0;
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotpd'] = 0;
            }

            //proc kehadiran total
            $totprochari[$row['idpegawai']][$row['tglpresensi']] = $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] + $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotpd'];
            $a_totproc[$row['idpegawai']] += $totprochari[$row['idpegawai']][$row['tglpresensi']];
        }

        return array("totproc" => $a_totproc, "totprochari" => $totprochari, "data" => $a_data, "dinas" => $a_dinas);
    }

    function getProcPotTransport($conn, $r_tglmulai, $r_tglselesai, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        $sqld = "select pegditunjuk,tglpergi,tglpulang from " . static::table('pe_rwtdinas') . " 
					where tglpergi between '$r_tglmulai' and '$r_tglselesai' and tglpulang between '$r_tglmulai' and '$r_tglselesai'";
		 if (!empty($a_peg))
            $sqld.= "pegditunjuk in (" . $a_peg . ")";
			
        $rsd = $conn->Execute($sqld);
        while ($rowd = $rsd->FetchRow()) {
            $mulai = strtotime($rowd['tglpergi']);
            $selesai = strtotime($rowd['tglpulang']);

            $artgl = array();
            $i = 0;
            while ($mulai <= $selesai) {
                $nefektif = date("w", $mulai);
                if ($nefektif != 0 and $nefektif != 6)
                    $a_dinas[$rowd['pegditunjuk']][date('Y-m-d', $mulai)] = 'D';
                $mulai+=86400;
            }
        }

        $sql = "select idpegawai,tglpresensi,kodeabsensi,menitdatang,menitpulang from " . static::table('pe_presensidet') . " 
					where tglpresensi between '$r_tglmulai' and '$r_tglselesai'";

        if (!empty($a_peg))
            $sql.= "idpegawai in (" . $a_peg . ")";
        $rs = $conn->Execute($sql);

        $a_data = array();
        $totprochari = array();
        $a_totproc = array();
        while ($row = $rs->FetchRow()) {
            $menitdatang = $row['menitdatang'] / 60;
            $menitpulang = $row['menitpulang'] / 60;

            if ($menitdatang > 0)
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] = self::getProcPotTrans($conn, $menitdatang, 'T');
            if ($menitpulang < 0)
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotpd'] = self::getProcPotTrans($conn, abs($menitpulang), 'PD');
            if ($row['kodeabsensi'] == 'I' or $row['kodeabsensi'] == 'A' or $row['kodeabsensi'] == 'DK' or $row['kodeabsensi'] == 'PK' or $row['kodeabsensi'] == 'ST')
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] = self::getProcTrans($conn, $row['kodeabsensi']);

            if ($a_dinas[$row['idpegawai']][$row['tglpresensi']] == 'D') {
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] = 0;
                $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotpd'] = 0;
            }

            //proc transport total
            $totprochari[$row['idpegawai']][$row['tglpresensi']] = $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotdt'] + $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotpd'];
            $a_totproc[$row['idpegawai']] += $totprochari[$row['idpegawai']][$row['tglpresensi']];
        }

        return array("totproc" => $a_totproc, "totprochari" => $totprochari, "data" => $a_data);
    }

    function getPotKehadiranTransport($conn, $r_tglmulai, $r_tglselesai, $r_sql = '') {
        if (!empty($r_sql)) {
            $a_peg = self::pegFilter($conn, $r_sql);
        }

        $sql = "select * from " . static::table('ga_historypotongan') . " where tglpresensi between '$r_tglmulai' and '$r_tglselesai'";
        if (!empty($a_peg))
            $a_peg .= " idpegawai in ($a_peg)";

        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpegawai']][$row['tglpresensi']]['procpottransporttelat'] = $row['procpottransporttelat'];
            $a_data[$row['idpegawai']][$row['tglpresensi']]['procpottransportpd'] = $row['procpottransportpd'];
            $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotkehadirantelat'] = $row['procpotkehadirantelat'];
            $a_data[$row['idpegawai']][$row['tglpresensi']]['procpotkehadiranpd'] = $row['procpotkehadiranpd'];
            $a_data[$row['idpegawai']][$row['tglpresensi']]['pottransport'] = $row['pottransport'];
            $a_data[$row['idpegawai']][$row['tglpresensi']]['potkehadiran'] = $row['potkehadiran'];
            $a_data[$row['idpegawai']][$row['tglpresensi']]['tarifpottransport'] = $row['tarifpottransport'];
            $a_data[$row['idpegawai']][$row['tglpresensi']]['tarifpotkehadiran'] = $row['tarifpotkehadiran'];
        }

        return $a_data;
    }

    function getTarifPotKehadiran($conn) {
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        $sql = "select * from " . static::table('ms_tariftunjangan') . "
					where periodetarif='$r_periodetarif' and (kodetunjangan='T00004' or kodetunjangan='T00015' or kodetunjangan='T00019')";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['variabel1']] = $row['nominal'];
        }

        return $a_data;
    }

    function getTarifPotTransport($conn) {
        $r_periodetarif = self::getLastPeriodeTarif($conn);

        $sql = "select * from " . static::table('ms_tariftunjangan') . "
					where periodetarif='$r_periodetarif' and (kodetunjangan='T00016')";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['variabel1']] = $row['nominal'];
        }

        return $a_data;
    }

    /*     * ************************************************** L A P O R A N ***************************************************** */

	function getNamaUnit($conn,$r_kodeunit){
		$sql = "select namaunit from " . static::table('ms_unit') . " where kodeunit = '$r_kodeunit'";
		
		return $conn->GetOne($sql);
	}
	
    function getInfoLembur($conn, $key) {
        list($r_periode, $r_pegawai) = explode("|", $key);
        $periode = $conn->GetRow("select tglawallembur, tglakhirlembur from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
        $sql = "select p.totlembur,p.idpegawai,p.tglpresensi,p.jamdatang,p.jampulang,p.kodeabsensi,g.*,
					sdm.f_namalengkap(m.gelardepan,m.namadepan,m.namatengah,m.namabelakang,m.gelarbelakang) as namalengkap, u.namaunit
					from " . static::table('pe_presensidet') . " p
					left join " . static::table('ga_upahlembur') . " g on g.idpegawai=p.idpegawai and g.periodegaji='$r_periode'
					left join " . static::table('ms_pegawai') . " m on m.idpegawai=p.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=m.idunit
					where totlembur is not null and issetujuatasan='Y' and isvalid='Y'
					and tglpresensi between '$periode[tglawallembur]' and '$periode[tglakhirlembur]'
					and p.idpegawai='$r_pegawai'";
        $rs = $conn->Execute($sql);
        $a_data = array();
        $a_info = array();
        while ($row = $rs->FetchRow()) {
            $a_info['namalengkap'] = $row['namalengkap'];
            $a_info['namaunit'] = $row['namaunit'];
            if ($row['kodeabsensi'] == 'H') {
                $a_data['H']['totlembur'][] = $row['totlembur'];
                $a_data['H']['jamdatang'][] = $row['jamdatang'];
                $a_data['H']['jampulang'][] = $row['jampulang'];
                $a_data['H']['tanggal'][] = $row['tglpresensi'];
            } else if ($row['kodeabsensi'] == 'HL') {
                $a_data['HL']['totlembur'][] = $row['totlembur'];
                $a_data['HL']['jamdatang'][] = $row['jamdatang'];
                $a_data['HL']['jampulang'][] = $row['jampulang'];
                $a_data['HL']['tanggal'][] = $row['tglpresensi'];
            }

            $a_info['upahlembur'] = $row['upahlembur'];
        }

        return array("info" => $a_info, "data" => $a_data);
    }

    function repSlipGaji($conn, $r_periode, $r_kodeunit, $r_idpegawai) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_kodeunit);

        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,
					js.jabatanstruktural,pd.namapendidikan,	substring(gh.masakerja,1,2)||' thn. ' || substring(gh.masakerja,3,2)||' bln.' as mkgaji,pk.golongan,
					gh.idtipepeg,gh.idjenispegawai,f.jabatanfungsional as fungsional, l.upahlembur
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ga_historydatagaji') . " gh on gh.gajiperiode=g.periodegaji and gh.idpeg = g.idpegawai
					left join " . static::table('ga_upahlembur') . " l on l.idpegawai=gh.idpeg and l.periodegaji=gh.gajiperiode
					left join " . static::table('ms_struktural') . " js on js.idjstruktural=gh.struktural
					left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan=gh.pendidikan
					left join " . static::table('ms_unit') . " u on u.idunit=gh.idunit
					left join " . static::table('ms_pangkat') . " pk on pk.idpangkat = gh.pangkatpeg
					left join " . static::table('ms_fungsional') . " f on f.idjfungsional = gh.fungsional
					where g.periodegaji = '$r_periode'";

        if (!empty($r_idpegawai))
            $sql .= " and g.idpegawai = $r_idpegawai";
        if (!empty($r_kodeunit))
            $sql .= " and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'];

        $rs = $conn->Execute($sql);

        //nama periode gaji
        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji = '$r_periode'");

        $a_data = array('list' => $rs, 'namaunit' => $col['namaunit'], 'namaperiode' => $namaperiode);

        return $a_data;
    }

    function repLapGapok($conn, $r_periode) {
        $sql = "select idpangkat,cast(masakerja as varchar) as mkerja,tarifgapok from " . static::table('ms_tarifgapok') . " 
					where periodetarif = '$r_periode' order by masakerja,cast(idpangkat as int)";
        $rs = $conn->Execute($sql);

        $a_data = array();
        $a_mk = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpangkat']][$row['mkerja']] = $row['tarifgapok'];
            $a_mk[$row['mkerja']] = $row['mkerja'];
        }

        $a_mk = array_unique($a_mk);

        //nama periode gaji
        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ms_periodetarif') . " where periodetarif = '$r_periode'");

        //jenis pangkat
        $sql = "select idpangkat,golongan from " . static::table('ms_pangkat') . " order by urutan";
        $rsp = $conn->Execute($sql);

        $a_pkt = array();
        $a_gol = array();
        $a_ngol = array();
        while ($rowp = $rsp->FetchRow()) {
            $a_pkt[$rowp['idpangkat']] = $rowp['golongan'];
            $a_gol[substr($rowp['idpangkat'], 0, 1)] = substr($rowp['idpangkat'], 0, 1);
            $a_ngol[substr($rowp['idpangkat'], 0, 1)] ++;
        }

        $a_gol = array_unique($a_gol);

        return array('data' => $a_data, 'pangkat' => $a_pkt, 'gol' => $a_gol, 'jmlgol' => $a_ngol, 'mk' => $a_mk, 'namaperiode' => $namaperiode);
    }

    function repLapPindahBuku($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
		
		//untuk ttd universitas
		$infoleft = $conn->GetOne("select infoleft from ".static::table('ms_unit')." where lower(namaunit) like '%universitas%' order by infoleft limit 1");

        //pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','12000','12400','22100','22200')";
        $rs = $conn->Execute($sql);

        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000') {
                $a_ttd['yayasan'] = $row['namalengkap'];
                $a_ttd['jabyayasan'] = $row['jabatanstruktural'];
            }
			
			if($infoleft >= $col['infoleft']){
				if ($row['idjstruktural'] == '12000') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '12400') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}else{
				if ($row['idjstruktural'] == '22100') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '22200') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
        }

        $sql = "select kodepotongan,namapotongan from ".static::table('ms_potongan')." where isaktif='Y' order by kodepotongan";
        $rsrpot = $conn->Execute($sql);
        $a_jnspot = array();
        while ($rowrpot = $rsrpot->FetchRow()) {
            $a_jnspot[$rowrpot['kodepotongan']] = $rowrpot['namapotongan'];
        }

        $sql = "select kodejnspinjaman as kodepotongan from ".static::table('lv_jnspinjaman')." where isaktif='Y' order by kodejnspinjaman";
        $rsrping = $conn->Execute($sql);
        $a_jnspinj = array();
        while ($rowrping = $rsrping->FetchRow()) {
            $a_jnspinj[$rowrping['kodepotongan']] = $rowrping['kodepotongan'];
        }
		
		$a_pinj = array('pinjaman' => 'Total Pinjaman');
        $jns = array();
        $jns = array_merge($a_pinj, $a_jnspot);

        //data potongan
        $sql = "select * from " . static::table('ga_potongan') . " where periodegaji = '$r_periode' order by idpegawai";
        $rsp = $conn->Execute($sql);

        while ($rowp = $rsp->FetchRow()) {
			if(in_array($rowp['kodepotongan'],$a_jnspinj))
				$a_pot[$rowp['idpegawai']]['pinjaman'] += $rowp['nominal'];
			else
				$a_pot[$rowp['idpegawai']][$rowp['kodepotongan']] = $rowp['nominal'];
        }

        //data gaji
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					case when h.struktural is not null then s.jabatanstruktural else 'Staff' end as jabatanstruktural,j.jenispegawai,h.idjenispegawai,h.norekening
					from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg=g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=h.idpeg
					left join " . static::table('ms_unit') . " u on u.idunit=h.idunit
					left join " . static::table('ms_struktural ') . " s on s.idjstruktural=h.struktural
					left join " . static::table('ms_jenispeg  ') . " j on j.idjenispegawai=h.idjenispegawai
					where g.periodegaji='$r_periode' {$sqljenis} and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by h.idjenispegawai";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "potongan" => $a_pot);
    }

    function repLapPindahBukuStruk($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
		
		//untuk ttd universitas
		$infoleft = $conn->GetOne("select infoleft from ".static::table('ms_unit')." where lower(namaunit) like '%universitas%' order by infoleft limit 1");

        //pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','12000','12400','22100','22200')";
        $rs = $conn->Execute($sql);
		
        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000') {
                $a_ttd['yayasan'] = $row['namalengkap'];
                $a_ttd['jabyayasan'] = $row['jabatanstruktural'];
            }
			
			if($infoleft >= $col['infoleft']){
				if ($row['idjstruktural'] == '12000') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '12400') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}else{
				if ($row['idjstruktural'] == '22100') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '22200') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
        }

        $sql = "select kodepotongan,namapotongan from " . static::table('ms_potongan') . " where isaktif='Y' order by kodepotongan";
        $rsrpot = $conn->Execute($sql);
        $a_jnspot = array();
        while ($rowrpot = $rsrpot->FetchRow()) {
            $a_jnspot[$rowrpot['kodepotongan']] = $rowrpot['namapotongan'];
        }

        $sql = "select kodejnspinjaman as kodepotongan from ".static::table('lv_jnspinjaman')." where isaktif='Y' order by kodejnspinjaman";
        $rsrping = $conn->Execute($sql);
        $a_jnspinj = array();
        while ($rowrping = $rsrping->FetchRow()) {
            $a_jnspinj[$rowrping['kodepotongan']] = $rowrping['kodepotongan'];
        }
		
		$a_pinj = array('pinjaman' => 'Total Pinjaman');
        $jns = array();
        $jns = array_merge($a_pinj, $a_jnspot);

        //data potongan
        $sql = "select g.* from " . static::table('ga_potongan') . " g
                left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
                where g.periodegaji = '$r_periode' {$sqljenis}
                order by idpegawai";
        $rsp = $conn->Execute($sql);

        while ($rowp = $rsp->FetchRow()) {
			if(in_array($rowp['kodepotongan'],$a_jnspinj))
				$a_pot[$rowp['idpegawai']]['pinjaman'] += $rowp['nominal'];
			else
				$a_pot[$rowp['idpegawai']][$rowp['kodepotongan']] += $rowp['nominal'];
        }

        //pegawai di dalam unit anggaran
        $sql = "select u.idunitanggaran,g.idpegawai from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_unit') . " u on u.idunit = h.idunit
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural
					where g.periodegaji='$r_periode' and u.idunitanggaran is not null and h.idpeg is not null {$sqljenis}
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by coalesce(un.infoleft,g.idpegawai),coalesce(s.infoleft,g.idpegawai)";
        $rsu = $conn->Execute($sql);

        $a_unit = array();
        $a_level = array();
        while ($rowu = $rsu->FetchRow()) {
            $a_unit[$rowu['idunitanggaran']][$rowu['idpegawai']] = $rowu['idpegawai'];
        }

        //data gaji pegawai
        $sql = "select g.*,u.idunitanggaran,un.namaunit,p.idpegawai,p.nip,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					case when h.struktural is not null then s.jabatanstruktural 
					else 
						case  
							when h.idtipepeg='D' then 'Dosen' 
							else 'Staff'
						end
					end as jabatanstruktural,p.norekening
					from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=h.idpeg 
					left join " . static::table('lv_statusaktif') . " a ON a.idstatusaktif = p.idstatusaktif
					left join " . static::table('ms_unit') . " u on u.idunit=h.idunit 
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural
					where g.periodegaji='$r_periode' and p.idunit is not null and h.idpeg is not null {$sqljenis}
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by coalesce(un.infoleft,g.idpegawai),coalesce(s.infoleft,g.idpegawai)";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpegawai']][] = $row;
        }


        return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "potongan" => $a_pot, "unit" => $a_unit, "level" => $a_level);
    }

    function repLapSerahBank($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");

        //pendatanganan
        $sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap, idjstruktural 
					from " . static::table('ms_pegawai') . " where idjstruktural in ('10000','20000')";
        $rs = $conn->Execute($sql);
        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000')
                $a_ttd['yayasan'] = $row['namalengkap'];
            else if ($row['idjstruktural'] == '20000')
                $a_ttd['rektor'] = $row['namalengkap'];
        }

        //ambil dosen
        $sqld = "select g.*,anrekening,p.norekening,p.nip,p.alamat
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai 
					left join sdm.ms_struktural s on s.idjstruktural=p.idjstruktural
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran
					where g.periodegaji='$r_periode' and (p.norekening is not null and p.anrekening is not null) {$sqljenis} 
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by coalesce(un.infoleft,g.idpegawai),coalesce(s.infoleft,g.idpegawai)";
        $rs = $conn->Execute($sqld);

        $a_pegawai = array();
        while ($row = $rs->FetchRow()) {
            $a_pegawai[] = $row;
        }

        //ambil potongan rekening
        $sql = "select kodepotongan,norekening,anrekening from " . static::table('ms_potongan') . " 
					where isaktif='Y' and norekening is not null or anrekening is not null order by kodepotongan";
        $rsrpot = $conn->Execute($sql);
        $a_pot = array();
        while ($rowrpot = $rsrpot->FetchRow()) {
            $a_pot[] = $rowrpot;
        }

        $sql = "select kodejnspinjaman as kodepotongan,norekening,anrekening from " . static::table('lv_jnspinjaman') . " 
					where isaktif='Y' and norekening is not null or anrekening is not null order by kodejnspinjaman";
        $rsrping = $conn->Execute($sql);
        $a_pinj = array();
        while ($rowrping = $rsrping->FetchRow()) {
            $a_pinj[] = $rowrping;
        }

        $a_rekening = array();
        $a_rekening = array_merge($a_pinj, $a_pot);

        $sqlp = "select kodepotongan,sum(nominal) as total 
                    from " . static::table('ga_potongan') . " g
                    left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai 
                    left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_unit') . " u on u.idunit=h.idunit
					where g.periodegaji='$r_periode' and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . " {$sqljenis}
					group by kodepotongan order by kodepotongan";
        $rsp = $conn->Execute($sqlp);

        //pinjaman selain keuangan, juga dimasukkan ke keuangan
        $a_pinjkeu = array('PJ001','PJ002','PJ003');
        $a_potrekening = array();
        while ($rowpr = $rsp->FetchRow()) {
            if(in_array($rowpr['kodepotongan'], $a_pinjkeu))
                $a_potrekening['PJ001'] += $rowpr['total'];
            else
                $a_potrekening[$rowpr['kodepotongan']] += $rowpr['total'];
        }

        return array("ttd" => $a_ttd, "datapegawai" => $a_pegawai, "datapotrekening" => $a_potrekening, "namaperiode" => $namaperiode, "rekening" => $a_rekening);
    }

    //detail gaji pemindahan buku
    function getDetailPindahBukuStruk($conn, $r_unit, $i_pegawai, $r_periode, $sqljenis) {

        //data gaji
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					case when h.struktural is not null then s.jabatanstruktural else 'Staff' end as jabatanstruktural
					from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg=g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=h.idunit
					left join " . static::table('ms_struktural ') . " s on s.idjstruktural=h.struktural
					where g.periodegaji='$r_periode' {$sqljenis} and u.parentunit = $r_unit";
        if (!empty($i_pegawai))
            $sql .= " and g.idpegawai not in ($i_pegawai)";

        $sql .=" order by s.infoleft";

        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return $a_data;
    }

    function getLapPotonganPresensi($conn, $r_kodeunit, $r_tglmulai, $r_tglselesai, $r_idpegawai) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_kodeunit);

        $sql = "select t.idpegawai, p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, u.namaunit,s.jabatanstruktural,p.idjenispegawai 
					from " . static::table('pe_presensidet') . " t
					left join " . static::schema() . "ms_pegawai p on p.idpegawai=t.idpegawai
					left join " . static::schema() . "lv_statusaktif a on a.idstatusaktif=p.idstatusaktif
					left join " . static::schema() . "ms_unit u on u.idunit=p.idunit
					left join " . static::schema() . "ms_struktural s on s.idjstruktural=p.idjstruktural
					where t.tglpresensi between '$r_tglmulai' and '$r_tglselesai' and a.iskeluar = 'T'";

        if (!empty($r_kodeunit))
            $sql .= " and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "";
        if (!empty($r_idpegawai))
            $sql .= " and t.idpegawai = $r_idpegawai";

        $sql .= " group by t.idpegawai, p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang), u.namaunit,s.jabatanstruktural,p.idjenispegawai";
        $rs = $conn->Execute($sql);

        $a_row = array();
        while ($row = $rs->FetchRow())
            $a_row[] = $row;

        $sql = "select r.*,a.absensi,
					case when jamdatang is not null then substring(jamdatang,1,2) || ':' || substring(jamdatang,3,2) end as jamdatang2,
					case when jampulang is not null then substring(jampulang,1,2) || ':' || substring(jampulang,3,2) end as jampulang2,
					case when sjamdatang is not null then substring(sjamdatang,1,2) || ':' || substring(sjamdatang,3,2) end as sjamdatang2,
					case when sjampulang is not null then substring(sjampulang,1,2) || ':' || substring(sjampulang,3,2) end as sjampulang2,
					menitdatang, menitpulang, totlembur, round(sdm.f_diffmenit(jamdatang, jampulang)/60,2) as jamkerja
					from " . static::schema() . "pe_presensidet r
					left join " . static::schema() . "ms_pegawai k on k.idpegawai=r.idpegawai
					left join " . static::schema() . "ms_unit u on u.idunit=k.idunit
					left join " . static::schema() . "ms_jenispeg v on v.idjenispegawai=k.idtipepeg
					left join " . static::schema() . "ms_tipepeg z on z.idtipepeg=k.idtipepeg
					left join " . static::schema() . "ms_absensi a on a.kodeabsensi=r.kodeabsensi
					where tglpresensi between '$r_tglmulai' and '$r_tglselesai'";

        if (!empty($r_kodeunit))
            $sql .= " and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'];
        if (!empty($r_idpegawai))
            $sql .= " and k.idpegawai = $r_idpegawai";
        $sql .= " order by tglpresensi";

        $rs = $conn->Execute($sql);

        $a_rows = array();
        while ($row = $rs->FetchRow())
            $a_rows[$row['idpegawai']][] = $row;

        $a_data = array('pegawai' => $a_rows, 'terima' => $a_row, 'namaunit' => $col['namaunit']);

        return $a_data;
    }

    function repLapRekapPotKehadiran($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $last = self::getLastDataPeriodeGaji($conn);
        $col = mUnit::getData($conn, $r_unit);
        $arrange_info = (int) $col['inforight'] - (int) $col['infoleft'];
        if ($arrange_info == 1)
            $sql_info = "u.inforight - u.infoleft >= 1";
        else
            $sql_info = "u.inforight - u.infoleft > 1";

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
		
		//untuk ttd universitas
		$infoleft = $conn->GetOne("select infoleft from ".static::table('ms_unit')." where lower(namaunit) like '%universitas%' order by infoleft limit 1");

        //pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','12000','12400','22100','22200')";
        $rs = $conn->Execute($sql);

        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000') {
                $a_ttd['yayasan'] = $row['namalengkap'];
                $a_ttd['jabyayasan'] = $row['jabatanstruktural'];
            }
			
			if($infoleft >= $col['infoleft']){
				if ($row['idjstruktural'] == '12000') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '12400') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}else{
				if ($row['idjstruktural'] == '22100') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '22200') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
        }

        //Jabatan struktural
        $sql = "select s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,h.idpegawai,
					sum(potkehadiran) as potkehadiran,sum(pottransport) as pottransport, sum(potkehadiran) + sum(pottransport) as totalpotkehadiran,
					" . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from " . static::table('ms_struktural') . " s
					left join " . static::table('ms_unit') . " u on u.idunit=s.idunit
					left join " . static::table('ga_historydatagaji') . " hi on hi.struktural=s.idjstruktural and hi.gajiperiode = '$r_periode'
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=hi.idpeg
					left join " . static::table('pe_presensidet') . " h on h.idpegawai=p.idpegawai and h.tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "'
					where {$sql_info} and h.idpegawai is not null {$sqljenis} 
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					group by h.idpegawai,s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,u.infoleft,s.infoleft,
					" . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					order by u.infoleft,s.infoleft";
        $rss = $conn->Execute($sql);

        $a_data = array();
        while ($rows = $rss->FetchRow()) {
            $a_data[] = $rows;
            $a_pegawai[$rows['idpegawai']] = $rows['idpegawai'];
        }

        if (count($a_pegawai) > 0)
            $i_pegawai = implode(",", $a_pegawai);

        return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "namaunit" => $col['namaunit'], "i_pegawai" => $i_pegawai);
    }

    //detail rekap potongan kehadiran dan transport
    function getDetailRekapPotKehadiran($conn, $r_unit, $r_periode, $sqljenis, $i_pegawai) {
        $last = self::getLastDataPeriodeGaji($conn);

        //data gaji
        $sql = "select pr.idpegawai,sum(potkehadiran) as potkehadiran,sum(pottransport) as pottransport, sum(potkehadiran) + sum(pottransport) as totalpotkehadiran, 
					" . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					case when p.idjstruktural is not null then s.jabatanstruktural else 'Staff' end as jabatanstruktural,j.jenispegawai
					from " . static::table('pe_presensidet') . " pr 
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=pr.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					left join " . static::table('ms_struktural ') . " s on s.idjstruktural=p.idjstruktural
					left join " . static::table('ms_jenispeg  ') . " j on j.idjenispegawai=p.idjenispegawai
					where pr.tglpresensi between '" . $last['tglawalhit'] . "' and '" . $last['tglakhirhit'] . "' {$sqljenis} and u.parentunit = $r_unit";
        if (!empty($i_pegawai))
            $sql .= " and p.idpegawai not in ($i_pegawai)";

        $sql .=" group by pr.idpegawai, " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang), p.idjstruktural, jabatanstruktural,p.idjenispegawai,j.jenispegawai
					order by p.idjenispegawai";

        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return $a_data;
    }

    function repSlipLembur($conn, $r_periode, $r_kodeunit, $r_idpegawai) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_kodeunit);

        $periode = $conn->GetRow("select tglawallembur, tglakhirlembur from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");

        $sql = "select p.totlembur,p.idpegawai,p.tglpresensi,p.jamdatang,p.jampulang,p.kodeabsensi,g.*
					from " . static::table('pe_presensidet') . " p
					left join " . static::table('ga_upahlembur') . " g on g.idpegawai=p.idpegawai and g.periodegaji='$r_periode'
					left join " . static::table('ms_pegawai') . " m on m.idpegawai=p.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=m.idunit
					where totlembur is not null and issetujuatasan='Y' and isvalid='Y'
					and tglpresensi between '$periode[tglawallembur]' and '$periode[tglakhirlembur]'";

        if (!empty($r_idpegawai))
            $sql .= " and p.idpegawai = $r_idpegawai";
        if (!empty($r_kodeunit))
            $sql .= " and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'];

        $sql .= " order by tglpresensi";
        $rsl = $conn->Execute($sql);

        $a_data = array();
        $a_pegawai = array();
        while ($row = $rsl->FetchRow()) {
            if ($row['kodeabsensi'] == 'H') {
                $a_data[$row['idpegawai']]['H']['totlembur'][] = $row['totlembur'];
                $a_data[$row['idpegawai']]['H']['jamdatang'][] = $row['jamdatang'];
                $a_data[$row['idpegawai']]['H']['jampulang'][] = $row['jampulang'];
                $a_data[$row['idpegawai']]['H']['tanggal'][] = $row['tglpresensi'];
            } else if ($row['kodeabsensi'] == 'HL') {
                $a_data[$row['idpegawai']]['HL']['totlembur'][] = $row['totlembur'];
                $a_data[$row['idpegawai']]['HL']['jamdatang'][] = $row['jamdatang'];
                $a_data[$row['idpegawai']]['HL']['jampulang'][] = $row['jampulang'];
                $a_data[$row['idpegawai']]['HL']['tanggal'][] = $row['tglpresensi'];
            }
            $a_pegawai[] = $row['idpegawai'];
            $a_data[$row['idpegawai']]['upahlembur'] = $row['upahlembur'];
        }

        $a_pegawai = array_unique($a_pegawai);

        if (!empty($a_pegawai))
            $i_pegawai = implode(",", $a_pegawai);

        //data header
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,u.namaunit
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					where g.periodegaji = '$r_periode'";

        if (!empty($r_idpegawai))
            $sql .= " and g.idpegawai = $r_idpegawai";
        if (!empty($i_pegawai))
            $sql .= " and g.idpegawai in ($i_pegawai)";
        if (!empty($r_kodeunit))
            $sql .= " and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'];

        $rs = $conn->Execute($sql);

        //nama periode gaji
        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji = '$r_periode'");

        return array("list" => $rs, "namaunit" => $col['namaunit'], "namaperiode" => $namaperiode, "data" => $a_data);
    }


    function repLapRekapGaji($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
		
		//untuk ttd universitas
		$infoleft = $conn->GetOne("select infoleft from ".static::table('ms_unit')." where lower(namaunit) like '%universitas%' order by infoleft limit 1");
        
		//pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
                    p.idjstruktural,s.jabatanstruktural,f.jabatanfungsional
                    from " . static::table('ms_pegawai') . " p
                    left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
                    left join " . static::table('ms_fungsional') . " f on f.idjfungsional=p.idjfungsional
                    where p.idjstruktural in ('10000','12000','12400','22100','22200')";
        $rs = $conn->Execute($sql);

        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000') {
                $a_ttd['yayasan'] = $row['namalengkap'];
                $a_ttd['jabyayasan'] = $row['jabatanstruktural'];
            }
			
			if($infoleft >= $col['infoleft']){
				if ($row['idjstruktural'] == '12000') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '12400') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}else{
				if ($row['idjstruktural'] == '22100') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '22200') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
        }

         //jenis tunjangan
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where isaktif = 'Y' order by urutan";
        $jns = Query::arrQuery($conn, $sql);

        //data tunjangan
        $sql = "select kodetunjangan,nominal,idpegawai from " . static::table('ga_tunjanganpeg') . " where periodegaji = '$r_periode' order by idpegawai";
        $rsp = $conn->Execute($sql);

        while ($rowp = $rsp->FetchRow()) {
            $a_tunj[$rowp['idpegawai']][$rowp['kodetunjangan']] = $rowp['nominal'];
        }

        //pegawai di dalam unit anggaran
        $sql = "select u.idunitanggaran,g.idpegawai from " . static::table('ga_gajipeg') . " g 
                    left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
                    left join " . static::table('ms_unit') . " u on u.idunit = h.idunit
                    left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
                    left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural
                    where g.periodegaji='$r_periode' and u.idunitanggaran is not null and h.idpeg is not null {$sqljenis}
                    and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
                    order by coalesce(un.infoleft,g.idpegawai),coalesce(s.infoleft,g.idpegawai)";
        $rsu = $conn->Execute($sql);

        $a_unit = array();
        $a_level = array();
        while ($rowu = $rsu->FetchRow()) {
            $a_unit[$rowu['idunitanggaran']][$rowu['idpegawai']] = $rowu['idpegawai'];
        }


        //data gaji pegawai
        $sql = "select g.*,u.idunitanggaran,un.namaunit,p.idpegawai,p.nip,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
                    case when h.struktural is not null then s.jabatanstruktural 
                    else 
                        case  
                            when h.idtipepeg='D' then 'Dosen' 
                            else 'Staff'
                        end
                    end as jabatanstruktural,pd.namapendidikan,p.tglmasuk,f.jabatanfungsional
                    from " . static::table('ga_gajipeg') . " g 
                    left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
                    left join " . static::table('ms_pegawai') . " p on p.idpegawai=h.idpeg 
                    left join " . static::table('ms_unit') . " u on u.idunit=h.idunit 
                    left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
                    left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural 
                    left join " . static::table('lv_jenjangpendidikan') . " pd on pd.idpendidikan=h.pendidikan
                    left join " . static::table('ms_fungsional') . " f on f.idjfungsional=h.fungsional
                    where g.periodegaji='$r_periode' and p.idunit is not null and h.idpeg is not null {$sqljenis}
                    and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
                    order by coalesce(un.infoleft,g.idpegawai),coalesce(s.infoleft,g.idpegawai)";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpegawai']][] = $row;
        }


        return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "tunjangan" => $a_tunj, "unit" => $a_unit, "level" => $a_level);
    }

    function repLapRekapGajiLama($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
		
		//untuk ttd universitas
		$infoleft = $conn->GetOne("select infoleft from ".static::table('ms_unit')." where lower(namaunit) like '%universitas%' order by infoleft limit 1");

        //pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','12000','12400','22100','22200')";
        $rs = $conn->Execute($sql);

        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000') {
                $a_ttd['yayasan'] = $row['namalengkap'];
                $a_ttd['jabyayasan'] = $row['jabatanstruktural'];
            }
			
			if($infoleft >= $col['infoleft']){
				if ($row['idjstruktural'] == '12000') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '12400') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}else{
				if ($row['idjstruktural'] == '22100') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '22200') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
        }

        //jenis potongan
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " where isaktif = 'Y' order by urutan";
        $jns = Query::arrQuery($conn, $sql);

        //data potongan
        $sql = "select kodetunjangan,nominal,idpegawai from " . static::table('ga_tunjanganpeg') . " where periodegaji = '$r_periode' order by idpegawai";
        $rsp = $conn->Execute($sql);

        while ($rowp = $rsp->FetchRow()) {
            $a_tunj[$rowp['idpegawai']][$rowp['kodetunjangan']] = $rowp['nominal'];
        }

        $sql = "select s.idjstruktural,s.jabatanstruktural,s.idunit,u.namaunit,h.idpeg,g.*,
					p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap
					from " . static::table('ms_struktural') . " s
					left join " . static::table('ga_historydatagaji') . " h on h.struktural=s.idjstruktural and h.gajiperiode = '$r_periode'
					left join " . static::table('ga_gajipeg') . " g on g.idpegawai=h.idpeg and h.gajiperiode=g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=h.idpeg
					left join " . static::table('ms_unit') . " u on u.idunit=h.idunit
					where h.gajiperiode='$r_periode' {$sqljenis} 
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by u.infoleft,s.infoleft";
        $rs = $conn->Execute($sql);
        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "tunjangan" => $a_tunj);
    }

    //detail rekap gaji
    function getDetailRekapgaji($conn, $r_unit, $i_pegawai, $r_periode, $sqljenis) {

        //data gaji
        $sql = "select g.*,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					case when h.struktural is not null then s.jabatanstruktural else 'Staff' end as jabatanstruktural
					from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg=g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=h.idunit
					left join " . static::table('ms_struktural ') . " s on s.idjstruktural=h.struktural
					where g.periodegaji='$r_periode' {$sqljenis} and u.parentunit = $r_unit";
        if (!empty($i_pegawai))
            $sql .= " and g.idpegawai not in ($i_pegawai)";

        $sql .=" order by s.infoleft";

        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return $a_data;
    }

    function repLapRekapGajiHonorer($conn, $r_periode, $r_unit) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
		
		//untuk ttd universitas
		$infoleft = $conn->GetOne("select infoleft from ".static::table('ms_unit')." where lower(namaunit) like '%universitas%' order by infoleft limit 1");

        //pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','12000','12400','22100','22200')";
        $rs = $conn->Execute($sql);

        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000') {
                $a_ttd['yayasan'] = $row['namalengkap'];
                $a_ttd['jabyayasan'] = $row['jabatanstruktural'];
            }
			
			if($infoleft >= $col['infoleft']){
				if ($row['idjstruktural'] == '12000') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '12400') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}else{
				if ($row['idjstruktural'] == '22100') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '22200') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
        }

        $sql = "select g.idpegawai," . static::schema() . "f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					coalesce(g.gajiditerima,0) as gajiditerima, j.namapendidikan, count(tglpresensi) as jum,th.nominal as tarif,u.namaunit
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on g.idpegawai=p.idpegawai
					left join " . static::table('ga_tarifhonorer') . " th on th.idpendidikan=p.idpendidikan
					left JOIN " . static::table('pe_rwtpendidikan') . " r on r.nourutrpen=(select  " . static::table('pe_rwtpendidikan') . ".nourutrpen
					from " . static::table('pe_rwtpendidikan') . " where " . static::table('pe_rwtpendidikan') . ".idpegawai=p.idpegawai and isvalid='Y' 
					and isdiakuiuniv='Y' order by " . static::table('pe_rwtpendidikan') . ".tglijazah desc limit 1)
					left join " . static::table('lv_jenjangpendidikan') . " j on j.idpendidikan=r.idpendidikan
					left join " . static::table('pe_presensidet') . " t on g.idpegawai=t.idpegawai and 
					date_part('m',tglpresensi)=(select date_part('m',tglawalhit) from " . static::table('ga_periodegaji') . " 
					where periodegaji='$r_periode') and (jamdatang is not null or jampulang is not null)
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					where periodegaji='$r_periode' and idhubkerja='HP'
					group by g.idpegawai," . static::schema() . "f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),gajiditerima,namapendidikan,th.nominal,u.namaunit,u.infoleft
					order by u.infoleft";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return array('list' => $a_data, 'namaperiode' => $namaperiode, "ttd" => $a_ttd);
    }

    function repLapRekapLembur($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $periode = $conn->GetRow("select tglawallembur, tglakhirlembur,namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");

        //pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('22110','12400')";
        $rs = $conn->Execute($sql);

        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '22110') {
                $a_ttd['keuangan'] = $row['namalengkap'];
                $a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
            } else if ($row['idjstruktural'] == '12400') {
                $a_ttd['kepegawaian'] = $row['namalengkap'];
                $a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
            }
        }

        $sql = "select sl.idsuratlembur,sl.tgllembur,sl.jenislembur,sl.jmljam,sl.totaljam,sl.gajipokok,sl.lemburjam,sl.lembur,
                u.idunit,u.namaunit,p.idpegawai,
                " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai
				from " . static::table('ms_unit') . " u
				left join " . static::table('ms_pegawai') . " p on p.idunit = u.idunit
				join " . static::table('ga_upahlembur') . " g on g.idpegawai=p.idpegawai
				left join " . static::table('pe_suratlembur') . " sl on sl.idpegawai = g.idpegawai and sl.tgllembur between '$periode[tglawallembur]' and '$periode[tglakhirlembur]'
				left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
				left join " . static::table('ms_jenispeg') . " j on j.idjenispegawai = p.idjenispegawai
				where g.periodegaji='$r_periode' {$sqljenis} and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . " and sl.jmljam is not null
                order by u.infoleft,sl.idpegawai,sl.tgllembur,sl.jamawal";
        $rs = $conn->Execute($sql);
        
        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_rowspan[$row['idpegawai']]++;
            $a_rowspanunit[$row['idunit']]++;

            $a_data[$row['idsuratlembur']]['idpegawai'] = $row['idpegawai'];
            $a_data[$row['idsuratlembur']]['namapegawai'] = $row['namapegawai'];
            $a_data[$row['idsuratlembur']]['idunit'] = $row['idunit'];
            $a_data[$row['idsuratlembur']]['namaunit'] = $row['namaunit'];
            $a_data[$row['idsuratlembur']]['jenislembur'] = $row['jenislembur'];
            $a_data[$row['idsuratlembur']]['tgllembur'] = $row['tgllembur'];
            $a_data[$row['idsuratlembur']]['jmljam'] = $row['jmljam'];
            $a_data[$row['idsuratlembur']]['totaljam'] = $row['totaljam'];
            $a_data[$row['idsuratlembur']]['lemburjam'] = $row['lemburjam'];
            $a_data[$row['idsuratlembur']]['lembur'] = $row['lembur'];

            $a_total[$row['idpegawai']] += $row['lembur'];
            $a_totalunit[$row['idunit']] += $row['lembur'];
        }

        return array("ttd" => $a_ttd, "data" => $a_data, "rowspan" => $a_rowspan, "rowspanunit" => $a_rowspanunit, "total" => $a_total, "totalunit" => $a_totalunit, "namaperiode" => $periode['namaperiode']);
    }

    function repLapPindahLembur($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");

        //pendatanganan
        $sql = "select sdm.f_namalengkap(gelardepan,namadepan,namatengah,namabelakang,gelarbelakang) as namalengkap, idjstruktural 
					from " . static::table('ms_pegawai') . " where idjstruktural in ('10000','20000')";
        $rs = $conn->Execute($sql);
        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000')
                $a_ttd['yayasan'] = $row['namalengkap'];
            else if ($row['idjstruktural'] == '20000')
                $a_ttd['rektor'] = $row['namalengkap'];
        }

        $sql = "select g.*, anrekening, p.norekening,p.alamat,p.nip,p.namadepan,p.namatengah,p.namabelakang
					from " . static::table('ga_upahlembur') . " g 
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					where g.periodegaji='$r_periode' and (p.norekening is not null or p.anrekening is not null) {$sqljenis} and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by u.infoleft";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode);
    }

    //rekap pajak tahunan
    function repLapPajakTahunan($conn, $r_tahun, $r_kodeunit) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_kodeunit);

        $sql = "select g.idpegawai,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai
					from " . static::table('ga_gajipeg') . " g
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					where substring(g.periodegaji,1,4)='$r_tahun' and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					group by g.idpegawai,p.nip,p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang,u.infoleft
					order by u.infoleft";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        $sql = "select substring(cast(pe.tglawalhit as varchar),6,2) as bulan,g.idpegawai,g.gajidibulatkan,g.pph
					from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ga_periodegaji') . " pe on pe.periodegaji=g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=g.idpegawai
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					where substring(g.periodegaji,1,4)='$r_tahun' and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by u.infoleft";
        $rsgp = $conn->Execute($sql);

        $a_bulangaji = array();
        $a_bulanpph = array();
        while ($rowgp = $rsgp->FetchRow()) {
            $a_bulangaji[$rowgp['bulan']][$rowgp['idpegawai']] = $rowgp['gajidibulatkan'];
            $a_bulanpph[$rowgp['bulan']][$rowgp['idpegawai']] = $rowgp['pph'];
        }

        return array("data" => $a_data, "gaji" => $a_bulangaji, "pph" => $a_bulanpph);
    }

    function repRekapGajiTHR($conn, $r_periodethr, $r_unit) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiodethr = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periodethr'");

        $refperiodethr = $conn->GetOne("select refperiodegaji from " . static::table('ga_periodegaji') . " where periodegaji='$r_periodethr'");
        $prop = self::getProporsional($conn, $refperiodethr);

        $sql = "select g.idpegawai,p.nip," . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namapegawai,COALESCE(tglmasuk,tglcalon) as tmk,
					substring(h.masakerja,1,2)||' tahun ' || substring(h.masakerja,3,2)||' bulan' as masakerja,
					gajidibulatkan,pph
					from " . static::table('ga_gajipeg') . " g 
					left join " . static::table('ga_periodegaji') . " pg on pg.periodegaji=g.periodegaji
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg=g.idpegawai and h.gajiperiode=pg.refperiodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=h.idpeg
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran
					where g.periodegaji='$r_periodethr' and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by coalesce(un.infoleft,p.idpegawai),coalesce(s.infoleft,nip)";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow())
            $a_data[] = $row;

        return array("data" => $a_data, "namaperiodethr" => $namaperiodethr, "prorata" => $prop);
    }

    //rekapitulasi insentif
    function repRekapInsentif($conn, $r_periode, $r_unit) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $namaperiode = $conn->GetOne("select namaperiode from " . static::table('ga_periodegaji') . " where periodegaji='$r_periode'");
		
		//untuk ttd universitas
		$infoleft = $conn->GetOne("select infoleft from ".static::table('ms_unit')." where lower(namaunit) like '%universitas%' order by infoleft limit 1");

        //pendatanganan
        $sql = "select " . static::schema . ".f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap, 
					p.idjstruktural,s.jabatanstruktural
					from " . static::table('ms_pegawai') . " p
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=p.idjstruktural
					where p.idjstruktural in ('10000','12000','12400','22100','22200')";
        $rs = $conn->Execute($sql);

        $a_ttd = array();
        while ($row = $rs->FetchRow()) {
            if ($row['idjstruktural'] == '10000') {
                $a_ttd['yayasan'] = $row['namalengkap'];
                $a_ttd['jabyayasan'] = $row['jabatanstruktural'];
            }
			
			if($infoleft >= $col['infoleft']){
				if ($row['idjstruktural'] == '12000') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '12400') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}else{
				if ($row['idjstruktural'] == '22100') {
					$a_ttd['keuangan'] = $row['namalengkap'];
					$a_ttd['jabkeuangan'] = $row['jabatanstruktural'];
				} else if ($row['idjstruktural'] == '22200') {
					$a_ttd['kepegawaian'] = $row['namalengkap'];
					$a_ttd['jabkepegawaian'] = $row['jabatanstruktural'];
				}
			}
        }

        //ms_tunjangan det 
        $sql = "select kodetunjangan,idjenispegawai 
					from " . static::table('ms_tunjangandet') . "";
        $rs = $conn->Execute($sql);

        $a_tunjdet = array();
        while ($row = $rs->FetchRow()) {
            $a_tunjdet[$row['kodetunjangan']] = $row['idjenispegawai'];
        }

        //jenis insentif
        $sql = "select kodetunjangan,namatunjangan from " . static::table('ms_tunjangan') . " 
					where kodetunjangan='T00008' or kodetunjangan='T00009' or kodetunjangan='T00010'  and carahitung in ('P','M') and isbayargaji = 'T' and isaktif = 'Y' order by kodetunjangan";
        $rst = $conn->Execute($sql);
        $jns = Query::arrQuery($conn, $sql);
        while ($rowt = $rst->FetchRow()) {
            $a_tunjangan[] = $rowt['kodetunjangan'];
        }

        $i_tunjangan = array();
        if (!empty($a_tunjangan))
            $i_tunjangan = implode("','", $a_tunjangan);


        //data tunjangan insentif
        $sql = "select * from " . static::table('ga_tunjanganpeg') . " where periodegaji = '$r_periode' order by idpegawai";
        $rsp = $conn->Execute($sql);

        while ($rowp = $rsp->FetchRow()) {
            $a_insentif[$rowp['idpegawai']][$rowp['kodetunjangan']] = $rowp['nominal'];
        }

        //pegawai di dalam unit anggaran
        $sql = "select u.idunitanggaran,g.idpegawai from " . static::table('ga_tunjanganpeg') . " g 
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_unit') . " u on u.idunit = h.idunit
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural
					where g.periodegaji='$r_periode' and u.idunitanggaran is not null and h.idpeg is not null {$sqljenis} and g.kodetunjangan in ('$i_tunjangan')
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					group by u.idunitanggaran,g.idpegawai,un.infoleft,s.infoleft
					order by coalesce(un.infoleft,g.idpegawai),coalesce(s.infoleft,g.idpegawai)";
        $rsu = $conn->Execute($sql);

        $a_unit = array();
        $a_level = array();
        while ($rowu = $rsu->FetchRow()) {
            $a_unit[$rowu['idunitanggaran']][$rowu['idpegawai']] = $rowu['idpegawai'];
        }


        //data pegawai
        $sql = "select g.nominal,u.idunitanggaran,un.namaunit,p.idpegawai,p.nip,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					p.norekening
					from " . static::table('ga_tunjanganpeg') . " g 
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = g.periodegaji
					left join " . static::table('ms_pegawai') . " p on p.idpegawai=h.idpeg 
					left join " . static::table('ms_unit') . " u on u.idunit=h.idunit 
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural
					where g.periodegaji='$r_periode' and p.idunit is not null and h.idpeg is not null {$sqljenis} and g.kodetunjangan in ('$i_tunjangan')
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					group by g.nominal,u.idunitanggaran,un.namaunit,p.idpegawai,p.nip,p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang,p.norekening,un.infoleft,s.infoleft
					order by coalesce(un.infoleft,p.idpegawai),coalesce(s.infoleft,p.idpegawai)";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpegawai']][] = $row;
        }

        return array("ttd" => $a_ttd, "data" => $a_data, "namaperiode" => $namaperiode, "jenis" => $jns, "insentif" => $a_insentif, "unit" => $a_unit);
    }

    function rekapLapPotKehadiran($conn, $r_periode, $r_unit, $sqljenis) {
        global $conf;
        require_once($conf['gate_dir'] . 'model/m_unit.php');

        $col = mUnit::getData($conn, $r_unit);

        $sql = "select u.idunitanggaran,g.idpegawai from " . static::table('ms_pegawai') . " g
					left join " . static::table('ga_potongan') . " t on t.idpegawai=g.idpegawai and t.periodegaji='$r_periode' and t.kodepotongan='P00001'
					left join " . static::table('ga_potongan') . " k on k.idpegawai=g.idpegawai and k.periodegaji='$r_periode' and k.kodepotongan='P00002'
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg = g.idpegawai and h.gajiperiode = '$r_periode'
					left join " . static::table('ms_unit') . " u on u.idunit = h.idunit
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural
					where t.nominal is not null or k.nominal is not null
					and u.idunitanggaran is not null and h.idpeg is not null {$sqljenis}
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by coalesce(un.infoleft,g.idpegawai),coalesce(s.infoleft,g.idpegawai)";
        $rsu = $conn->Execute($sql);

        $a_unit = array();
        $a_level = array();
        while ($rowu = $rsu->FetchRow()) {
            $a_unit[$rowu['idunitanggaran']][$rowu['idpegawai']] = $rowu['idpegawai'];
        }


        //data gaji pegawai
        $sql = "select k.nominal as potkehadiran,t.nominal as pottransport,u.idunitanggaran,un.namaunit,p.idpegawai,p.nip,sdm.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as namalengkap,
					case when h.struktural is not null then s.jabatanstruktural else 'Staff' end as jabatanstruktural
					from " . static::table('ms_pegawai') . " p 
					left join " . static::table('ga_potongan') . " t on t.idpegawai=p.idpegawai and t.periodegaji='$r_periode' and t.kodepotongan='P00001'
					left join " . static::table('ga_potongan') . " k on k.idpegawai=p.idpegawai and k.periodegaji='$r_periode' and k.kodepotongan='P00002'
					left join " . static::table('ga_historydatagaji') . " h on h.idpeg = p.idpegawai and h.gajiperiode = '$r_periode'
					left join " . static::table('ms_unit') . " u on u.idunit=p.idunit 
					left join " . static::table('ms_unit') . " un on un.idunit=u.idunitanggaran 
					left join " . static::table('ms_struktural') . " s on s.idjstruktural=h.struktural
					where p.idunit is not null and h.idpeg is not null {$sqljenis}
					and u.infoleft >= " . (int) $col['infoleft'] . " and u.inforight <= " . (int) $col['inforight'] . "
					order by coalesce(un.infoleft,p.idpegawai),coalesce(s.infoleft,p.idpegawai)";
        $rs = $conn->Execute($sql);

        $a_data = array();
        while ($row = $rs->FetchRow()) {
            $a_data[$row['idpegawai']][] = $row;
        }


        return array("unit" => $a_unit, "data" => $a_data, "namaperiode" => $periode['namaperiode'], "namaunit" => $col['namaunit']);
    }

    /*     * ************************************************** E N D OF L A P O R A N ***************************************************** */
}

?>
