<?php
	// model laporan kelas
	defined( '__VALID_ENTRANCE' ) or die( 'Akses terbatas' );
	
	class mLaporanKelas {
		// fungsi pembantu
		function getPeserta($conn,$kodeunit,$periode,$thnkurikulum='',$kodemk='',$kelasmk='',$uts=false,$uas=false) {
			// mendapatkan mahasiswa terkena skors
			$sql = "select distinct nim from akademik.ak_skors where '$periode' between
					coalesce(periodeawal,periodeakhir) and coalesce(periodeakhir,periodeawal)";
			$rs = $conn->Execute($sql);
			
			$a_mhs = array();
			while($row = $rs->FetchRow())
				$a_mhs[] = $row['nim'];
			
			// mendapatkan data peserta
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, k.nim, m.nama, k.nnumerik, k.nangka, k.nhuruf
					from akademik.ak_krs k
					join akademik.ms_mahasiswa m on m.nim = k.nim
					join akademik.ak_perwalian ap on m.nim = ap.nim and ap.periode='$periode'
					where k.kodeunit = '$kodeunit' and k.periode = '$periode' and ap.frsdisetujui=-2";
			
			if(!empty($thnkurikulum))
				$sql .= " and k.thnkurikulum = '$thnkurikulum' and k.kodemk = '$kodemk' and k.kelasmk = '$kelasmk'";
			/*if($uts)
				$sql .= " and k.isikututs = -1";
			if($uas)
				$sql .= " and k.isikutuas = -1";*/
				
			$sql .= " and k.nim not in ('".implode("','",$a_mhs)."')
					order by k.kodemk, k.kelasmk, k.thnkurikulum, k.nim";
			
			return $conn->Execute($sql);			
		}
		function getPesertaUjian($conn,$keyujian) {
			
			
			// mendapatkan data peserta
			/*$sql = "select m.nim, m.nama
					from akademik.ak_pesertaujian p
					join akademik.ak_jadwalujian j on p.idjadwalujian=j.idjadwalujian
					join akademik.ak_krs k on k.periode=j.periode and k.thnkurikulum=j.thnkurikulum and k.kodeunit=j.kodeunit and k.kodemk=j.kodemk and k.kelasmk=j.kelasmk and k.nim=p.nim
					join akademik.ms_mahasiswa m on m.nim = p.nim
					join akademik.ak_perwalian pw on k.nim=pw.nim and k.periode=pw.periode and pw.cekalakad=0
					where p.idjadwalujian='$keyujian' and case when j.jenisujian='T' then k.isikututs = -1 else k.isikutuas = -1 end
					and case when j.jenisujian='T' then pw.isuts = -1 else pw.isuas = -1 end
					order by m.nim";*/
					$sql = "select m.nim, m.nama
					from akademik.ak_pesertaujian p
					join akademik.ak_jadwalujian j on p.idjadwalujian=j.idjadwalujian
					join akademik.ak_krs k on k.periode=j.periode and k.thnkurikulum=j.thnkurikulum and k.kodeunit=j.kodeunit and k.kodemk=j.kodemk and k.kelasmk=j.kelasmk and k.nim=p.nim
					join akademik.ms_mahasiswa m on m.nim = p.nim
					join akademik.ak_perwalian pw on k.nim=pw.nim and k.periode=pw.periode and pw.cekalakad=0
					where p.idjadwalujian='$keyujian' and case when j.jenisujian='T' then pw.isuts = -1 else pw.isuas = -1 end
					order by m.nim";
				
		
			
			return $conn->Execute($sql);			
		}
		function getNilaiPeserta($conn,$kodeunit,$periode,$thnkurikulum='',$kodemk='',$kelasmk='') {
			$sql = "select * from akademik.ak_unsurnilaikelas
					where kodeunit = '$kodeunit' and periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and thnkurikulum = '$thnkurikulum' and kodemk = '$kodemk' and kelasmk = '$kelasmk'";
			
			$rs = $conn->Execute($sql);
			
			// model krs untuk membentuk key
			require_once(Route::getModelPath('krs'));
			
			$data = array();
			while($row = $rs->FetchRow()) {
				$t_key = mKRS::getkeyRow($row);
				
				$data[$t_key][$row['idunsurnilai']] = $row['nilaiunsur'];
			}
			
			return $data;
		}
		
		// fungsi laporan
		function getJurnal($conn,$kodeunit,$periode,$thnkurikulum='',$kodemk='',$kelasmk='',$jeniskuliah='',$kelompok='',$cekabsen=false) {
			// mendapatkan data kelas
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks, m.semmk, u.namaunit, r.namaunit as fakultas, k.koderuang, k.koderuang2,
					akademik.f_namahari(k.nohari) as namahari, akademik.f_namahari(k.nohari2) as namahari2, k.jammulai, k.jamselesai, k.jammulai2, k.jamselesai2,
					xmlagg(('<div>'::text || akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) || '</div>'::text)::xml)::character varying as pengajar
					from akademik.ak_kelas k
					join akademik.ak_kurikulum m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk and k.kodeunit = m.kodeunit
					join gate.ms_unit u on k.kodeunit = u.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join akademik.ak_mengajar a on k.thnkurikulum = a.thnkurikulum and k.kodemk = a.kodemk and
						k.kodeunit = a.kodeunit and k.periode = a.periode and k.kelasmk = a.kelasmk
					left join sdm.ms_pegawai d on d.nik = a.nipdosen or d.idpegawai::text = a.nipdosen
					where k.kodeunit = '$kodeunit' and k.periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and k.thnkurikulum = '$thnkurikulum' and k.kodemk = '$kodemk' and k.kelasmk = '$kelasmk' ";
					
			$sql .= " group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks, m.semmk, u.namaunit,
						r.namaunit, k.koderuang, k.koderuang2, akademik.f_namahari(k.nohari), akademik.f_namahari(k.nohari2),
						k.jammulai, k.jamselesai, k.jammulai2, k.jamselesai2
					order by k.kodemk, k.kelasmk, k.thnkurikulum";
		$rs = $conn->Execute($sql);
			
			// mendapatkan data jurnal
			$sql = "select thnkurikulum, kodemk, kodeunit, periode, kelasmk,jeniskuliah,koderuang, perkuliahanke, topikkuliah,
					nohari, tglkuliah, waktumulai,waktuselesai,tglkuliahrealisasi,noharirealisasi,
					waktumulairealisasi,waktuselesairealisasi, k.keterangan, jumlahpeserta, jumlahhadir, kesandosen,nipdosenrealisasi,
					akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) as namadosen
					from akademik.ak_kuliah k
					left join sdm.ms_pegawai d on d.nik = k.nipdosenrealisasi or d.idpegawai::text = k.nipdosenrealisasi
					where kodeunit = '$kodeunit' and periode = '$periode' and jeniskuliah='K' ";
			if($cekabsen)
				$sql.=" and statusperkuliahan='S' and coalesce(jumlahpeserta,0)<=0";
			if(!empty($thnkurikulum))
				$sql .= " and thnkurikulum = '$thnkurikulum' and kodemk = '$kodemk' and kelasmk = '$kelasmk' and jeniskuliah='$jeniskuliah' and kelompok='$kelompok'";
			
			$sql .= " order by kodemk, kelasmk, thnkurikulum, perkuliahanke";
			$rsj = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_data = $row;
				
				// ambil jurnal
				$a_jurnal = array();
				while(!$rsj->EOF) {
					$rowj = $rsj->fields;
					
					if($rowj['thnkurikulum'] == $row['thnkurikulum'] and $rowj['kodemk'] == $row['kodemk'] and
						$rowj['kodeunit'] == $row['kodeunit'] and $rowj['periode'] == $row['periode'] and
						$rowj['kelasmk'] == $row['kelasmk'])
					{
						$t_time = Date::dateToTime($rowj['tglkuliah']);
						$t_time2 = Date::dateToTime($rowj['tglkuliahrealisasi']);
						
						$t_jurnal = array();
						$t_jurnal['perkuliahanke'] = $rowj['perkuliahanke'];
						$t_jurnal['jeniskuliah'] = $rowj['jeniskuliah'];
						$t_jurnal['koderuang'] = $rowj['koderuang'];
						$t_jurnal['nipdosenrealisasi'] = $rowj['nipdosenrealisasi'];
						$t_jurnal['namadosen'] = $rowj['namadosen'];
						$t_jurnal['topik'] = $rowj['topikkuliah'];
						$t_jurnal['hari'] = Date::indoDay($rowj['nohari']);
						$t_jurnal['tanggal'] = date('j-n-y',$t_time);
						$t_jurnal['jam'] = CStr::formatJam($rowj['waktumulai']).'-'.CStr::formatJam($rowj['waktuselesai']);
						
						$t_jurnal['topik'] = $rowj['topikkuliah'];
						$t_jurnal['noharirealisasi'] = Date::indoDay($rowj['noharirealisasi']);
						$t_jurnal['tanggalrealisasi'] = date('j-n-y',$t_time2);
						$t_jurnal['jamrealisasi'] = CStr::formatJam($rowj['waktumulairealisasi']).'-'.CStr::formatJam($rowj['waktuselesairealisasi']);
						$t_jurnal['keterangan'] = $rowj['keterangan'];
						$t_jurnal['kesandosen'] = $rowj['kesandosen'];
						$t_jurnal['jumlahpeserta'] = (int)$rowj['jumlahpeserta'];
						
						$a_jurnal[] = $t_jurnal;
						$rsj->MoveNext();
					}
					else
						break;
				}
				$t_data['jurnal'] = $a_jurnal;
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function getJurnalBlmAbsen($conn,$kodeunit,$periode){
			$sql = "select thnkurikulum, kodemk, kodeunit, periode, kelasmk,jeniskuliah,koderuang, perkuliahanke, topikkuliah,
					nohari, tglkuliah, waktumulai,waktuselesai,tglkuliahrealisasi,noharirealisasi,
					waktumulairealisasi,waktuselesairealisasi, k.keterangan, jumlahpeserta, jumlahhadir, kesandosen,nipdosenrealisasi,
					akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) as namadosen,
					mk.namamk
					from akademik.ak_kuliah k
					join akademik.ak_kurikulum mk using (kodeunit,thnkurikulum,kodemk)
					join sdm.ms_pegawai d on d.nik = k.nipdosenrealisasi or d.idpegawai::text = k.nipdosenrealisasi
					where kodeunit = '$kodeunit' and periode = '$periode'
					and statusperkuliahan='S' and coalesce(jumlahpeserta,0)<=0
					order by nipdosenrealisasi,kodemk,kelasmk,tglkuliahrealisasi";
			
			$rs = $conn->Execute($sql);
			$a_data = array();
			while($row = $rs->FetchRow())
				$a_data[] = $row;
				
			return $a_data;
		}
		
		function getAbsensi($conn,$kodeunit,$periode,$thnkurikulum='',$kodemk='',$kelasmk='') {
			// mendapatkan data kelas
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks, m.semmk, u.namaunit, r.namaunit as fakultas, k.koderuang, k.koderuang2,
					akademik.f_namahari(k.nohari) as namahari, akademik.f_namahari(k.nohari2) as namahari2, k.jammulai, k.jamselesai, k.jammulai2, k.jamselesai2,
					xmlagg(('<div>'::text || akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) || '</div>'::text)::xml)::character varying as pengajar
					from akademik.ak_kelas k
					join akademik.ak_kurikulum m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk and k.kodeunit = m.kodeunit
					join gate.ms_unit u on k.kodeunit = u.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join akademik.ak_mengajar a on k.thnkurikulum = a.thnkurikulum and k.kodemk = a.kodemk and
						k.kodeunit = a.kodeunit and k.periode = a.periode and k.kelasmk = a.kelasmk
					left join sdm.ms_pegawai d on d.nik = a.nipdosen or d.idpegawai::text = a.nipdosen
					where k.kodeunit = '$kodeunit' and k.periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and k.thnkurikulum = '$thnkurikulum' and k.kodemk = '$kodemk' and k.kelasmk = '$kelasmk'";
					
			$sql .= " group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks, m.semmk, u.namaunit,
						r.namaunit, k.koderuang, k.koderuang2, akademik.f_namahari(k.nohari), akademik.f_namahari(k.nohari2),
						k.jammulai, k.jamselesai, k.jammulai2, k.jamselesai2
					order by k.kodemk, k.kelasmk, k.thnkurikulum";
			$rs = $conn->Execute($sql);
			
			// mendapatkan data peserta
			$rsp = self::getPeserta($conn,$kodeunit,$periode,$thnkurikulum,$kodemk,$kelasmk);
			
			$a_data = array();
			while($row = $rs->FetchRow()) { 
				$t_data = $row;
				
				// ambil peserta
				$a_peserta = array();
				while(!$rsp->EOF) {
					$rowp = $rsp->fields;
					
					if($rowp['thnkurikulum'] == $row['thnkurikulum'] and $rowp['kodemk'] == $row['kodemk'] and
						$rowp['kodeunit'] == $row['kodeunit'] and $rowp['periode'] == $row['periode'] and
						$rowp['kelasmk'] == $row['kelasmk'])
					{
						$t_peserta = array();
						$t_peserta['nim'] = $rowp['nim'];
						$t_peserta['nama'] = $rowp['nama'];
						
						$a_peserta[] = $t_peserta;
						$rsp->MoveNext();
					}
					else
						break;
				}
				$t_data['peserta'] = $a_peserta;
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function getAbsensiUAS($conn,$kodeunit,$periode,$thnkurikulum='',$kodemk='',$kelasmk='',$r_uts,$r_uas) {
			// mendapatkan data kelas
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, u.namaunit, u.ketua as nipketua,
					r.namaunit as fakultas, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as ketua,
					xmlagg(('<div>'::text || akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) || '</div>'::text)::xml)::character varying as pengajar
					from akademik.ak_kelas k
					join akademik.ak_matakuliah m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk
					join gate.ms_unit u on k.kodeunit = u.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai p on p.nik = u.ketua or p.idpegawai::text = u.ketua
					left join akademik.ak_mengajar a on k.thnkurikulum = a.thnkurikulum and k.kodemk = a.kodemk and
						k.kodeunit = a.kodeunit and k.periode = a.periode and k.kelasmk = a.kelasmk
					left join sdm.ms_pegawai d on d.nik = a.nipdosen or d.idpegawai::text = a.nipdosen
					where k.kodeunit = '$kodeunit' and k.periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and k.thnkurikulum = '$thnkurikulum' and k.kodemk = '$kodemk' and k.kelasmk = '$kelasmk'";
					
			$sql .= " group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, u.namaunit, u.ketua,
						r.namaunit, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					order by k.kodemk, k.kelasmk, k.thnkurikulum";
			$rs = $conn->Execute($sql);
			
			// mendapatkan data unsur kelas
			$sql = "select thnkurikulum, kodemk, kodeunit, periode, kelasmk, namaunsurnilai, prosentasenilai
					from akademik.ak_unsurpenilaian
					where kodeunit = '$kodeunit' and periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and thnkurikulum = '$thnkurikulum' and kodemk = '$kodemk' and kelasmk = '$kelasmk'";
			
			$sql .= " order by kodemk, kelasmk, thnkurikulum, idunsurnilai";
			$rsu = $conn->Execute($sql);
			
			// mendapatkan data peserta
			$rsp = self::getPeserta($conn,$kodeunit,$periode,$thnkurikulum,$kodemk,$kelasmk,$r_uts,$r_uas);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_data = $row;
				
				// ambil unsur
				$a_unsur = array();
				while(!$rsu->EOF) {
					$rowu = $rsu->fields;
					
					if($rowu['thnkurikulum'] == $row['thnkurikulum'] and $rowu['kodemk'] == $row['kodemk'] and
						$rowu['kodeunit'] == $row['kodeunit'] and $rowu['periode'] == $row['periode'] and
						$rowu['kelasmk'] == $row['kelasmk'])
					{
						$t_unsur = array();
						$t_unsur['nama'] = $rowu['namaunsurnilai'];
						$t_unsur['prosentase'] = $rowu['prosentasenilai'];
						
						$a_unsur[] = $t_unsur;
						$rsu->MoveNext();
					}
					else
						break;
				}
				$t_data['unsur'] = $a_unsur;
				
				// ambil peserta
				$a_peserta = array();
				while(!$rsp->EOF) {
					$rowp = $rsp->fields;
					
					if($rowp['thnkurikulum'] == $row['thnkurikulum'] and $rowp['kodemk'] == $row['kodemk'] and
						$rowp['kodeunit'] == $row['kodeunit'] and $rowp['periode'] == $row['periode'] and
						$rowp['kelasmk'] == $row['kelasmk'])
					{
						$t_peserta = array();
						$t_peserta['nim'] = $rowp['nim'];
						$t_peserta['nama'] = $rowp['nama'];
						
						$a_peserta[] = $t_peserta;
						$rsp->MoveNext();
					}
					else
						break;
				}
				$t_data['peserta'] = $a_peserta;
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		function getAbsensiUjian($conn,$keyujian) {
			//$key_kelas=$conn->GetRow("select thnkurikulum,kodeunit,periode,kodemk,kelasmk from akademik.ak_jadwalujian where idjadwalujian='$keyujian'");
			// mendapatkan data kelas
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, u.namaunit, u.ketua as nipketua,
					r.namaunit as fakultas, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as ketua,
					xmlagg(('<div>'::text || akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) || '</div>'::text)::xml)::character varying as pengajar,
					j.kelompok,j.jenisujian,j.koderuang,j.tglujian,j.waktumulai,j.waktuselesai
					from akademik.ak_kelas k
					join akademik.ak_matakuliah m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk
					join gate.ms_unit u on k.kodeunit = u.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai p on p.nik = u.ketua or p.idpegawai::text = u.ketua
					left join akademik.ak_mengajar a on k.thnkurikulum = a.thnkurikulum and k.kodemk = a.kodemk and
						k.kodeunit = a.kodeunit and k.periode = a.periode and k.kelasmk = a.kelasmk
					left join sdm.ms_pegawai d on d.nik = a.nipdosen or d.idpegawai::text = a.nipdosen
					join akademik.ak_jadwalujian j on k.periode=j.periode and k.thnkurikulum=j.thnkurikulum and k.kodeunit=j.kodeunit and k.kodemk=j.kodemk and k.kelasmk=j.kelasmk and j.idjadwalujian='$keyujian'";
			
			/*if(!empty($key_kelas['thnkurikulum']))
				$sql .= " and k.thnkurikulum = '".$key_kelas['thnkurikulum']."' and k.kodemk = '".$key_kelas['kodemk']."' and k.kelasmk = '".$key_kelas['kelasmk']."'";*/
					
			$sql .= " group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, u.namaunit, u.ketua,
						r.namaunit, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang),
						j.kelompok,j.jenisujian,j.koderuang,j.tglujian,j.waktumulai,j.waktuselesai
					order by k.kodemk, k.kelasmk, k.thnkurikulum";
			$rs = $conn->Execute($sql);
			
			
			
			// mendapatkan data peserta
			$rsp = self::getPesertaUjian($conn,$keyujian);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_data = $row;
				
				
				
				// ambil peserta
				$a_peserta = array();
				while(!$rsp->EOF) {
					$rowp = $rsp->fields;
					
					/*if($rowp['thnkurikulum'] == $row['thnkurikulum'] and $rowp['kodemk'] == $row['kodemk'] and
						$rowp['kodeunit'] == $row['kodeunit'] and $rowp['periode'] == $row['periode'] and
						$rowp['kelasmk'] == $row['kelasmk'])
					{*/
						$t_peserta = array();
						$t_peserta['nim'] = $rowp['nim'];
						$t_peserta['nama'] = $rowp['nama'];
						
						$a_peserta[] = $t_peserta;
						$rsp->MoveNext();
					/*}
					else
						break;*/
				}
				$t_data['peserta'] = $a_peserta;
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		function getNilaiAkhir($conn,$kodeunit,$periode,$thnkurikulum='',$kodemk='',$kelasmk='') {
			// mendapatkan data kelas
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, u.namaunit, u.ketua as nipketua,
					r.namaunit as fakultas, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as ketua,
					min(case when a.tipepengajar = 'U' then a.nipdosen else null end) as nippengajar,
					xmlagg(('<div>'::text || akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) || '</div>'::text)::xml)::character varying as pengajar
					from akademik.ak_kelas k
					join akademik.ak_matakuliah m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk
					join gate.ms_unit u on k.kodeunit = u.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai p on p.nik = u.ketua or p.idpegawai::text = u.ketua
					left join akademik.ak_mengajar a on k.thnkurikulum = a.thnkurikulum and k.kodemk = a.kodemk and
						k.kodeunit = a.kodeunit and k.periode = a.periode and k.kelasmk = a.kelasmk
					left join sdm.ms_pegawai d on d.nik = a.nipdosen or d.idpegawai::text = a.nipdosen
					where k.kodeunit = '$kodeunit' and k.periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and k.thnkurikulum = '$thnkurikulum' and k.kodemk = '$kodemk' and k.kelasmk = '$kelasmk'";
					
			$sql .= " group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, u.namaunit, u.ketua,
						r.namaunit, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					order by k.kodemk, k.kelasmk, k.thnkurikulum";
			$rs = $conn->Execute($sql);
			
			// mendapatkan data unsur kelas
			$sql = "select thnkurikulum, kodemk, kodeunit, periode, kelasmk, idunsurnilai, namaunsurnilai, prosentasenilai
					from akademik.ak_unsurpenilaian
					where kodeunit = '$kodeunit' and periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and thnkurikulum = '$thnkurikulum' and kodemk = '$kodemk' and kelasmk = '$kelasmk'";
			
			$sql .= " order by kodemk, kelasmk, thnkurikulum, idunsurnilai";
			$rsu = $conn->Execute($sql);
			
			// mendapatkan data peserta
			$rsp = self::getPeserta($conn,$kodeunit,$periode,$thnkurikulum,$kodemk,$kelasmk);
			
			// mendapatkan nilai peserta
			$a_unsurnilaimhs = self::getNilaiPeserta($conn,$kodeunit,$periode,$thnkurikulum,$kodemk,$kelasmk);
			
			// model krs untuk membentuk key
			require_once(Route::getModelPath('krs'));
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_data = $row;
				
				// ambil unsur
				$a_unsur = array();
				while(!$rsu->EOF) {
					$rowu = $rsu->fields;
					
					if($rowu['thnkurikulum'] == $row['thnkurikulum'] and $rowu['kodemk'] == $row['kodemk'] and
						$rowu['kodeunit'] == $row['kodeunit'] and $rowu['periode'] == $row['periode'] and
						$rowu['kelasmk'] == $row['kelasmk'])
					{
						$t_unsur = array();
						$t_unsur['id'] = $rowu['idunsurnilai'];
						$t_unsur['nama'] = $rowu['namaunsurnilai'];
						$t_unsur['prosentase'] = $rowu['prosentasenilai'];
						
						$a_unsur[] = $t_unsur;
						$rsu->MoveNext();
					}
					else
						break;
				}
				$t_data['unsur'] = $a_unsur;
				
				// ambil peserta
				$a_peserta = array();
				while(!$rsp->EOF) {
					$rowp = $rsp->fields;
					
					if($rowp['thnkurikulum'] == $row['thnkurikulum'] and $rowp['kodemk'] == $row['kodemk'] and
						$rowp['kodeunit'] == $row['kodeunit'] and $rowp['periode'] == $row['periode'] and
						$rowp['kelasmk'] == $row['kelasmk'])
					{
						$t_peserta = array();
						$t_peserta['nim'] = $rowp['nim'];
						$t_peserta['nama'] = $rowp['nama'];
						$t_peserta['nnumerik'] = $rowp['nnumerik'];
						$t_peserta['nangka'] = $rowp['nangka'];
						$t_peserta['nhuruf'] = $rowp['nhuruf'];
						
						// key krs untuk nilai
						$t_key = mKRS::getkeyRow($rowp);
						$t_nilai = $a_unsurnilaimhs[$t_key];
						
						$t_peserta['nilai'] = $t_nilai;
						
						$a_peserta[] = $t_peserta;
						$rsp->MoveNext();
					}
					else
						break;
				}
				$t_data['peserta'] = $a_peserta;
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		
		function getPFTgsUTS($conn,$kodeunit,$periode,$thnkurikulum='',$kodemk='',$kelasmk='') {
			// mendapatkan data kelas
			$sql = "select k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks, u.namaunit, u.ketua as nipketua,
					r.namaunit as fakultas, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as ketua,
					xmlagg(('<div>'::text || akademik.f_namalengkap(d.gelardepan,d.namadepan,d.namatengah,d.namabelakang,d.gelarbelakang) || '</div>'::text)::xml)::character varying as pengajar,
					nohari, jammulai, jamselesai, koderuang, nohari2, jammulai2, jamselesai2, koderuang2
					from akademik.ak_kelas k
					join akademik.ak_matakuliah m on k.thnkurikulum = m.thnkurikulum and k.kodemk = m.kodemk
					join gate.ms_unit u on k.kodeunit = u.kodeunit
					left join gate.ms_unit r on r.kodeunit = u.kodeunitparent
					left join sdm.ms_pegawai p on p.nik = u.ketua or p.idpegawai::text = u.ketua
					left join akademik.ak_mengajar a on k.thnkurikulum = a.thnkurikulum and k.kodemk = a.kodemk and
						k.kodeunit = a.kodeunit and k.periode = a.periode and k.kelasmk = a.kelasmk
					left join sdm.ms_pegawai d on d.nik = a.nipdosen or d.idpegawai::text = a.nipdosen
					where k.kodeunit = '$kodeunit' and k.periode = '$periode'";
			
			if(!empty($thnkurikulum))
				$sql .= " and k.thnkurikulum = '$thnkurikulum' and k.kodemk = '$kodemk' and k.kelasmk = '$kelasmk'";
			
			$sql .= " group by k.thnkurikulum, k.kodemk, k.kodeunit, k.periode, k.kelasmk, m.namamk, m.sks, u.namaunit, u.ketua,
						r.namaunit, akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang)
					order by k.kodemk, k.kelasmk, k.thnkurikulum";
			$rs = $conn->Execute($sql);
			
			// mendapatkan data peserta
			$rsp = self::getPeserta($conn,$kodeunit,$periode,$thnkurikulum,$kodemk,$kelasmk);
			
			$a_data = array();
			while($row = $rs->FetchRow()) {
				$t_data = $row;
				
				// ambil peserta
				$a_peserta = array();
				while(!$rsp->EOF) {
					$rowp = $rsp->fields;
					
					if($rowp['thnkurikulum'] == $row['thnkurikulum'] and $rowp['kodemk'] == $row['kodemk'] and
						$rowp['kodeunit'] == $row['kodeunit'] and $rowp['periode'] == $row['periode'] and
						$rowp['kelasmk'] == $row['kelasmk'])
					{
						$t_peserta = array();
						$t_peserta['nim'] = $rowp['nim'];
						$t_peserta['nama'] = $rowp['nama'];
						
						$a_peserta[] = $t_peserta;
						$rsp->MoveNext();
					}
					else
						break;
				}
				$t_data['peserta'] = $a_peserta;
				
				$a_data[] = $t_data;
			}
			
			return $a_data;
		}
		function getPjmk($conn,$key){
			require_once(Route::getModelPath('kelas'));
			$cond=mKelas::getCondition($key,'thnkurikulum, kodemk, kodeunit, periode, kelasmk');
			$sql="select akademik.f_namalengkap(p.gelardepan,p.namadepan,p.namatengah,p.namabelakang,p.gelarbelakang) as pjmk,coalesce(p.nik,p.idpegawai::text) as nik
			from akademik.ak_mengajar m 
			join sdm.ms_pegawai p on p.nik=m.nipdosen or p.idpegawai::text=m.nipdosen
			where $cond and ispjmk=1";
			
			return $conn->GetRow($sql);
		}
		function getJadwalUjian($conn,$kodeunit,$periode,$tglawalujian,$tglakhirujian,$r_jenis){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$arr_jenis=array('uts'=>'T','uas'=>'A');
			$sql="select j.tglujian,j.waktumulai,j.waktuselesai,j.koderuang,j.kelompok,kr.kodemk,kr.namamk,k.kelasmk,k.namapengajar,k.namakoordinator,
				u.namaunit as jurusan,k.sistemkuliah,sum(1) as jum_peserta
				from akademik.ak_jadwalujian j
				join akademik.v_kelas3 k using (periode,thnkurikulum,kodeunit,kodemk,kelasmk)
				join akademik.ak_kurikulum kr using (kodeunit,thnkurikulum,kodemk)
				join gate.ms_unit u on u.kodeunit=k.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']."
				join akademik.ak_pesertaujian pj on pj.idjadwalujian=j.idjadwalujian
				where j.periode='$periode' and jenisujian='".$arr_jenis[$r_jenis]."'";
			if(!empty($tglawalujian) and !empty($tglakhirujian))
				$sql.=" and (j.tglujian between '$tglawalujian' and '$tglakhirujian')";
			else if (!empty($tglawalujian))
				$sql.=" and j.tglujian='$tglawalujian'";
			else if (!empty($tglakhirujian))
				$sql.=" and j.tglujian='$tglakhirujian'";
				
			$sql.=" group by u.namaunit,k.sistemkuliah,j.tglujian,j.waktumulai,j.waktuselesai,j.koderuang,j.kelompok,kr.kodemk,kr.namamk,k.kelasmk,k.namapengajar,k.namakoordinator
					order by j.tglujian";
			
			$a_data=$conn->GetArray($sql);
			$a_jadwal=array();
			foreach($a_data as $row)
				$a_jadwal[$row['tglujian']][]=$row;
				
			return $a_jadwal;
		}
		function getAbsensiDosen($conn,$kodeunit,$periode,$jeniskuliah,$sistemkuliah,$start,$end,$nip=''){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			
			/*$sql="select k.periode , k.thnkurikulum , k.kodeunit , k.kodemk , k.kelasmk,
				k.koderuang, akademik.f_namahari(k.nohari) as namahari,k.jammulai,k.jamselesai,
				kr.namamk,kr.sks,kl.nipdosenrealisasi,
				akademik.f_namalengkap(p.gelardepan,p.namadepan, p.namatengah, p.namabelakang,p.gelarbelakang) as namadosen,u.namaunit,
				sum(1) as jumlah from akademik.ak_kuliah kl
				join akademik.ak_kelas k on k.periode=kl.periode and k.thnkurikulum=kl.thnkurikulum and k.kodeunit=kl.kodeunit 
				and k.kodemk=kl.kodemk and k.kelasmk=kl.kelasmk and kl.jeniskuliah='$jeniskuliah'
				join akademik.ak_kurikulum kr on k.thnkurikulum=kr.thnkurikulum and k.kodemk=kr.kodemk and k.kodeunit=kr.kodeunit
				join sdm.ms_pegawai p on kl.nipdosenrealisasi = p.idpegawai::text
				join gate.ms_unit u on u.kodeunit=kl.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']."
				where kl.periode='$periode' and kl.statusperkuliahan='S' and kl.tglkuliahrealisasi between '$start' and '$end'
				group by k.periode , k.thnkurikulum , k.kodeunit , k.kodemk , k.kelasmk,
				k.koderuang,k.nohari,k.jammulai,k.jamselesai,kr.namamk,kr.sks,kl.nipdosenrealisasi,namadosen,u.namaunit";*/
			
			$sql="select k.periode , k.thnkurikulum , k.kodeunit , k.kodemk , k.kelasmk, k.koderuang, akademik.f_namahari(k.nohari) as namahari,k.jammulai,k.jamselesai,
				kr.namamk,kr.sks,m.nipdosen, akademik.f_namalengkap(p.gelardepan,p.namadepan, p.namatengah, p.namabelakang,p.gelarbelakang) as namadosen,
				u.namaunit, count( case when m.nipdosen=kl.nipdosen then kl.perkuliahanke end) as jumlahrencana,
				count( case when m.nipdosen=kl.nipdosenrealisasi and kl.statusperkuliahan='S' then kl.perkuliahanke end) as jumlahrealisasi
				from akademik.ak_mengajar m
				join akademik.ak_kelas k on k.periode=m.periode and k.thnkurikulum=m.thnkurikulum and k.kodeunit=m.kodeunit and k.kodemk=m.kodemk and k.kelasmk=m.kelasmk and m.jeniskul='$jeniskuliah' 
				left join akademik.ak_kuliah kl on kl.periode=m.periode and kl.thnkurikulum=m.thnkurikulum and kl.kodeunit=m.kodeunit and 
				kl.kodemk=m.kodemk and kl.kelasmk=m.kelasmk and (kl.nipdosen=m.nipdosen or kl.nipdosenrealisasi=m.nipdosen) and kl.jeniskuliah=m.jeniskul and kl.kelompok=m.kelompok
				and case when kl.tglkuliahrealisasi is not null then kl.tglkuliahrealisasi else kl.tglkuliah end between '$start' and '$end'
				join akademik.ak_kurikulum kr on m.thnkurikulum=kr.thnkurikulum and m.kodemk=kr.kodemk and m.kodeunit=kr.kodeunit 
				join sdm.ms_pegawai p on m.nipdosen = p.idpegawai::text 
				join gate.ms_unit u on u.kodeunit=m.kodeunit and u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']."
				where m.periode='$periode' and k.sistemkuliah='$sistemkuliah'";
			if(!empty($nip))
				$sql.=" and m.nipdosen='$nip'";
			$sql.=" group by k.periode , k.thnkurikulum , k.kodeunit , k.kodemk , k.kelasmk, k.koderuang,k.nohari,k.jammulai,k.jamselesai,kr.namamk,kr.sks,
				m.nipdosen,namadosen,u.namaunit   
				order by m.nipdosen";
			return $conn->GetArray($sql);
		}
		function getAbsensiMhs($conn,$kodeunit,$periode,$jeniskuliah,$sistemkuliah,$start,$end,$nim=''){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			
			$sql="select k.periode,k.thnkurikulum , g.kodeunit , g.namaunit , k.kodemk, ma.namamk, k.kelasmk,k.nim, m.nama,  count(ak.absen) as jumlah
				from akademik.ak_krs k
				join akademik.ak_kelas kl using (periode, thnkurikulum, kodeunit, kodemk, kelasmk)
				join akademik.ms_mahasiswa m on (k.nim=m.nim)
				join akademik.ak_matakuliah ma on k.kodemk=ma.kodemk and k.thnkurikulum=ma.thnkurikulum
				join gate.ms_unit g on g.kodeunit=kl.kodeunit and g.infoleft >= ".(int)$unit['infoleft']." and g.inforight <= ".(int)$unit['inforight']."
				left join akademik.ak_absensikuliah ak on k.periode=ak.periode and k.thnkurikulum=ak.thnkurikulum and k.kodeunit=ak.kodeunit and k.kodemk=ak.kodemk 
				and k.kelasmk=ak.kelasmk and k.nim=ak.nim and ak.jeniskuliah='$jeniskuliah' and (ak.tglkuliah between '$start' and '$end')
				where k.periode='$periode' and kl.sistemkuliah='$sistemkuliah'";
			if(!empty($nim))
				$sql.=" and m.nim='$nim'";
			$sql.=" group by k.periode,k.thnkurikulum , g.kodeunit , g.namaunit , k.kodemk, ma.namamk, k.kelasmk,k.nim, m.nama
				order by k.nim";
				
			return $conn->GetArray($sql);
		}
		function statusPenilaian($conn,$kodeunit,$periode){
			require_once(Route::getModelPath('unit'));
			$unit = mUnit::getData($conn,$kodeunit);
			$sql="select r.periode,r.kodeunit,r.thnkurikulum,r.kodemk,r.namamk,r.sks,r.kelasmk,r.namapengajar,r.nilaimasuk,r.kuncinilai,
					sum(1) as jum_mhs,sum (case when k.nangka is not null then 1 end) as dinilai,sum (case when k.dipakai=-1 then 1 end) as dipakai
					from akademik.v_kelas3 r
					left join akademik.ak_krs k using (periode , thnkurikulum , kodeunit , kodemk , kelasmk )
					join gate.ms_unit u on r.kodeunit = u.kodeunit 
					where u.infoleft >= ".(int)$unit['infoleft']." and u.inforight <= ".(int)$unit['inforight']." and periode = '$periode'
					group by r.periode,r.kodeunit,r.thnkurikulum,r.kodemk,r.namamk,r.sks,r.kelasmk,r.namapengajar,r.nilaimasuk,r.kuncinilai
					order by periode,namamk,kelasmk  ";
					
			return $conn->GetArray($sql);
		}
		
		function getRekapKuliah($conn,$r_periode,$tglawalkuliah,$tglakhirkuliah){
			$sql="select u.kodeunitparent,k.perkuliahanke,sum(1) as jumlah from gate.ms_unit u
					join akademik.ak_kuliah k on u.kodeunit=k.kodeunit 
					where periode='$r_periode'";
			if(!empty($tglakhirkuliah) and !empty($tglakhirkuliah))
				$sql.=" and (k.tglkuliahrealisasi between '$tglawalkuliah' and '$tglakhirkuliah')";
				
			$sql.=" group by u.kodeunitparent,k.perkuliahanke
					order by u.kodeunitparent,k.perkuliahanke";
			return $conn->Execute($sql);
			
		}
		
		function rekapralisasifak($conn,$r_periode,$tglawalkuliah,$tglakhirkuliah){
			require_once(Route::getModelPath('kelas'));
			$sql = "select f.kodeunit as kodefakultas,k.thnkurikulum,k.kodemk,k.kodeunit,k.periode,k.kelasmk,count(j.perkuliahanke) as jmljurnal
					from akademik.ak_kelas k
					left join akademik.ak_kuliah j using(thnkurikulum,kodemk,kodeunit,periode,kelasmk)
					join gate.ms_unit u using (kodeunit)
					join gate.ms_unit f on f.kodeunit=u.kodeunitparent
					where k.periode='$r_periode'";
			if(!empty($tglakhirkuliah) and !empty($tglakhirkuliah))
				$sql.=" and (j.tglkuliahrealisasi between '$tglawalkuliah' and '$tglakhirkuliah')";
			$sql.=" group by f.kodeunit,k.thnkurikulum,k.kodemk,k.kodeunit,k.periode,k.kelasmk
					order by f.kodeunit,jmljurnal";
			$rs = $conn->Execute($sql);
			
			$a_data = array();
			while($row = $rs->fetchRow()){
				$keykelas = mKelas::getKeyRow($row);
				$a_data[$row['kodefakultas']][$keykelas] = $row['jmljurnal'];
			}
			
			$a_jurnal = array();
			foreach($a_data as $fakultas=>$a_kelas){
				foreach($a_kelas as $idkelas=>$jmljurnal){
					$a_jurnal[$fakultas][$jmljurnal]++;
				}
			}
			
			return $a_jurnal;
		}
	}
?>
